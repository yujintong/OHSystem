# name = plugindb
# fullname = pychop/plugindb
# description = Used by other plguins to connect to the database.

import MySQLdb
import host
import operator

class PluginDB:
        '''Interface between other plugins and a MySQL database. Can either be used simply to get a connection or to write key/value pairs to the plugindb table. Also supports automatic score management.'''
        
        # these are not settings; they will be automatically retrieved!
        dbHost = "localhost"
        dbUser = "root"
        dbPassword = "password"
        dbName = "ghost"
        dbPort = 3306

        conn = 0
        cursor = 0

        readyCallback = 0
        
        # used in automatic score management
        scoreTuple = 0
        
        # used for interaction with plugindb table
        pluginName = "default"

        def __init__(self):
                config = host.config()
                self.dbHost = config.getString("db_mysql_server", self.dbHost)
                self.dbUser = config.getString("db_mysql_user", self.dbUser)
                self.dbPassword = config.getString("db_mysql_password", self.dbPassword)
                self.dbName = config.getString("db_mysql_database", self.dbName)
                self.dbPort = config.getInt("db_mysql_port", self.dbPort)

        def notifyReady(self, callbackFunction):
                # legacy function (not needed anymore since onStartup for configuration is not used)
                callbackFunction()

        def dbconnect(self):
                self.conn = MySQLdb.connect(host = self.dbHost, user = self.dbUser, passwd = self.dbPassword, db = self.dbName, port = self.dbPort)
                self.cursor = self.conn.cursor()
        
                # make sure the plugin db exists
                self.cursor.execute("CREATE TABLE IF NOT EXISTS plugindb (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, plugin VARCHAR(16), k VARCHAR(128), val VARCHAR(128))")
        
                return self.cursor
        
        def getCursor(self):
                return self.cursor

        def close(self):
                if self.cursor != 0:
                        self.cursor.close()
                if self.conn != 0:
                        self.conn.close()

        def setPluginName(self, name):
                self.pluginName = name

        def escape(self, nStr):
                return self.conn.escape_string(nStr)
        
        def execute(self, arg1, arg2 = ()):
                try:
                        self.cursor.execute(arg1, arg2)
                except (MySQLdb.OperationalError):
                        self.dbconnect()
                        self.cursor.execute(arg1, arg2) # hopefully it doesn't fail a second time, but definitely don't enter an infinite loop
                
                self.conn.commit()

        # from plugin table, retrieve (key's value (string), id) or return -1
        def dbGet(self, name, key):
                self.execute("SELECT val,id FROM plugindb WHERE plugin=%s AND k=%s", (name,key,))
                result = self.cursor.fetchone()
        
                if result == None:
                        return -1
                else:
                        return (str(result[0]), int(result[1]),)

        def dbSet(self, key, value):
                self.execute("UPDATE plugindb SET val=%s WHERE plugin=%s AND k=%s", (value,self.pluginName,key,))

        def dbFastSet(self, i, value):
                self.execute("UPDATE plugindb SET val=%s WHERE id=%s", (value,i,))

        def dbAdd(self, key, value):
                self.execute("INSERT INTO plugindb (plugin,k,val) VALUES(%s, %s, %s)", (self.pluginName,key,value,))
                return self.cursor.lastrowid

        def dbRemove(self, key):
                self.execute("DELETE FROM plugindb WHERE plugin=%s AND k=%s", (self.pluginName,key,))

        # returns array with tuples (key, value, dbID)
        def dbGetAll(self):
                self.execute("SELECT k,val,id FROM plugindb WHERE plugin=%s", (self.pluginName,))
                result_set = self.cursor.fetchall()
                result_list = []
        
                for row in result_set:
                        result_list.append((str(row[0]), str(row[1]), int(row[2]),))
        
                return result_list

