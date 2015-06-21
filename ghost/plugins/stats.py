import time
import host
import MySQLdb
from db import PluginDB

pdb = 0

players = 0
games = 0
bnets = {}

botid = 0
ip = ""
port = 0

lastUpdateTime = 0

def init():
        global pdb, botid, ip, port

	host.registerHandler('Update', onUpdate)
        host.registerHandler('BNetIPBanned', onBIPB)
        host.registerHandler('BNetTimeout', onBT)
        host.registerHandler('BNetLogged', onBL)
        host.registerHandler('PlayerJoined', onPlayerJoin)
        host.registerHandler('PlayerDeleted', onPlayerDeleted)
        host.registerHandler("GameCreated", onGameStarted)
        host.registerHandler("GameEnded", onGameEnded)

        pdb = PluginDB()
        pdb.dbconnect()

	config = host.config()
	botid = config.getInt("db_mysql_botid", botid)
	ip = config.getString("bot_bindaddress", ip)
	port = config.getInt("bot_hostport", port)

	query = "INSERT INTO oh_bot_status (botid, ip, hostport, last_update ) VALUES ('"+str(botid)+"','"+ip+"','"+str(port)+"', NOW()) ON DUPLICATE KEY UPDATE ip = '"+ip+"', hostport='"+str(port)+"', last_update= NOW()"
	pdb.execute(query)

def deinit():
	pdb.close()

def onUpdate( cGHost ):
        global lastUpdateTime, bnets, games, players, botid
        if gettime() - lastUpdateTime >= 10000:
	        query = "UPDATE oh_bot_status SET "

		for key, value in bnets.iteritems():
			query += "`" + key + "` = '"+str(value)+"', "

		query += "`games` = '"+str(games)+"', `players`='"+str(players)+"', last_update = NOW() WHERE botid = '"+str(botid)+"';"
	        pdb.execute(query)

		print("[BOTSTATS] GAMES: " + str(games) + " | PLAYERS: " + str(players) )

                lastUpdateTime = gettime()

def onBIPB( cBNet ):
	global bnets
	bnets[cBNet.serverAlias] = 2

def onBT( cBNet ):
        global bnets
        bnets[cBNet.serverAlias] = 3

def onBL( cBNet ):
        global bnets
        bnets[cBNet.serverAlias] = 1

def onPlayerJoin( cGame, cPlayerName):
	global players
	players += 1

def onPlayerDeleted( cGame, cPlayer ):
	global players
	players -= 1

def onGameStarted( cBaseGame ):
	global games
	games += 1

def onGameEnded( cGame ):
	global games
	games -= 1

def gettime():
        return int(round(time.time() * 1000))

