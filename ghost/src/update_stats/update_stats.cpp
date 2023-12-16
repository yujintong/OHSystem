/*

   Copyright [2013] []

   */

// STL

#include <fstream>
#include <iostream>
#include <sstream>
#include <iomanip>
#include <algorithm>
#include <map>
#include <queue>
#include <set>
#include <string>
#include <vector>

using namespace std;

#ifdef WIN32
#include "ms_stdint.h"
#else
#include <stdint.h>
#endif

#include "config.h"
#include <string.h>

#ifdef WIN32
#include <winsock.h>
#endif

#include <mysql/mysql.h>
#include <stdio.h>

#include <boost/thread.hpp>
#include <boost/filesystem.hpp>

using namespace boost::filesystem;

string gLogFile;
uint32_t gLogMethod;
ofstream *gLog = NULL;
boost::mutex PrintMutex;

void CONSOLE_Print( string message )
{

    boost::mutex::scoped_lock printLock(PrintMutex); 

    cout << message << endl;

    // logging

    if( !gLogFile.empty( ) )
    {
        if( gLogMethod == 1 )
        {
            ofstream Log;
            Log.open( gLogFile.c_str( ), ios :: app );

            if( !Log.fail( ) )
            {
                time_t Now = time( NULL );
                string Time = asctime( localtime( &Now ) );

                // erase the newline

                Time.erase( Time.size( ) - 1 );
                Log << "[" << Time << "] " << message << endl;
                Log.close( );
            }
        }
        else if( gLogMethod == 2 )
        {
            if( gLog && !gLog->fail( ) )
            {
                time_t Now = time( NULL );
                string Time = asctime( localtime( &Now ) );

                // erase the newline

                Time.erase( Time.size( ) - 1 );
                *gLog << "[" << Time << "] " << message << endl;
                gLog->flush( );
            }
        }
    }
    printLock.unlock();
}

string MySQLEscapeString(MYSQL *conn, string str)
{
	char *to = new char[str.size() * 2 + 1];
	unsigned long size = mysql_real_escape_string(conn, to, str.c_str(), str.size());
	string result(to, size);
	delete[] to;
	return result;
}

vector<string> MySQLFetchRow(MYSQL_RES *res)
{
	vector<string> Result;

	MYSQL_ROW Row = mysql_fetch_row(res);

	if (Row)
	{
		unsigned long *Lengths;
		Lengths = mysql_fetch_lengths(res);

		for (unsigned int i = 0; i < mysql_num_fields(res); i++)
		{
			if (Row[i])
				Result.push_back(string(Row[i], Lengths[i]));
			else
				Result.push_back(string());
		}
	}

	return Result;
}

string UTIL_ToString(uint32_t i)
{
	string result;
	stringstream SS;
	SS << i;
	SS >> result;
	return result;
}

string Int32_ToString(int32_t i)
{
	string result;
	stringstream SS;
	SS << i;
	SS >> result;
	return result;
}
string UTIL_ToString(float f, int digits)
{
	string result;
	stringstream SS;
	SS << std::fixed << std::setprecision(digits) << f;
	SS >> result;
	return result;
}

uint32_t UTIL_ToUInt32(string &s)
{
	uint32_t result;
	stringstream SS;
	SS << s;
	SS >> result;
	return result;
}

int32_t UTIL_ToInt32(string &s)
{
	int32_t result;
	stringstream SS;
	SS << s;
	SS >> result;
	return result;
}

float UTIL_ToFloat(string &s)
{
	float result;
	stringstream SS;
	SS << s;
	SS >> result;
	return result;
}

int main(int argc, char **argv)
{
	uint32_t ScoreStart = 100;
	uint32_t ScoreWin = 5;
	uint32_t ScoreLosse = 3;

	string CFGFile = "stats.cfg";

	if (argc > 1 && argv[1])
		CFGFile = argv[1];

	CConfig CFG;
	CFG.Read(CFGFile);
	string Server = CFG.GetString("db_mysql_server", string());
	string Database = CFG.GetString("db_mysql_database", "ghost");
	string User = CFG.GetString("db_mysql_user", string());
	string Password = CFG.GetString("db_mysql_password", string());
	int Port = CFG.GetInt("db_mysql_port", 0);

	ScoreStart = CFG.GetInt("statsupdate_scorestart", ScoreStart);
	ScoreWin = CFG.GetInt("statsupdate_scorewin", ScoreWin);
	ScoreLosse = CFG.GetInt("statsupdate_scoreloose", ScoreLosse);

	gLogFile = CFG.GetString("bot_log", "stats.log");
	gLogMethod = CFG.GetInt("bot_logmethod", 1);

	//日志文件信息
	if (!gLogFile.empty())
	{
		if (gLogMethod == 1)
		{
			// log method 1: open, append, and close the log for every message
			// this works well on Linux but poorly on Windows, particularly as the log file grows in size
			// the log file can be edited/moved/deleted while status is running
		}
		else if (gLogMethod == 2)
		{
			// log method 2: open the log on startup, flush the log for every message, close the log on shutdown
			// the log file CANNOT be edited/moved/deleted while status is running

			gLog = new ofstream();
			gLog->open(gLogFile.c_str(), ios::app);
		}
	}

    CONSOLE_Print("");
    CONSOLE_Print("***************************************************************************************");
    CONSOLE_Print("**                             INITIALIZE STATS MODULE                               **");
    CONSOLE_Print("***************************************************************************************");
    CONSOLE_Print("");
    CONSOLE_Print("[STATS] starting up");

	if (!gLogFile.empty())
	{
		if (gLogMethod == 1)
			CONSOLE_Print("[STATS] using log method 1, logging is enabled and [" + gLogFile + "] will not be locked");
		else if (gLogMethod == 2)
		{
			if (gLog->fail())
				CONSOLE_Print("[STATS] using log method 2 but unable to open [" + gLogFile + "] for appending, logging is disabled");
			else
				CONSOLE_Print("[STATS] using log method 2, logging is enabled and [" + gLogFile + "] is now locked");
		}
	}
	else
		CONSOLE_Print("[STATS] no log file specified, logging is disabled");

	CONSOLE_Print("[MYSQL] connecting to database server");

	MYSQL *Connection = NULL;

	if (!(Connection = mysql_init(NULL)))
	{
		CONSOLE_Print("[MYSQL] ERROR: " + string(mysql_error(Connection)));
		return 0;
	}

	my_bool Reconnect = true;
	mysql_options(Connection, MYSQL_OPT_RECONNECT, &Reconnect);

	if (!(mysql_real_connect(Connection, Server.c_str(), User.c_str(), Password.c_str(), Database.c_str(), Port, NULL, 0)))
	{
		CONSOLE_Print("[MYSQL] ERROR: " + string(mysql_error(Connection)));
		return 0;
	}

	CONSOLE_Print("[STATS] database server connected");
	CONSOLE_Print("[STATS] beginning transaction");

	string QBegin = "BEGIN";

	if (mysql_real_query(Connection, QBegin.c_str(), QBegin.size()) != 0)
	{
		CONSOLE_Print("[MYSQL] ERROR: " + string(mysql_error(Connection)));
		return 0;
	}

	CONSOLE_Print("[STATS] beginning get game types/aliases");

	//查询游戏类型 (dota, lod)
	queue<uint32_t> GameAliases;
	string QSelectAlias = "SELECT alias_id FROM oh_aliases ORDER BY alias_id;";

	//输出查询语句
	//CONSOLE_Print("[SQL] [" + QSelectAlias + "]");

	if (mysql_real_query(Connection, QSelectAlias.c_str(), QSelectAlias.size()) != 0)
	{
		CONSOLE_Print("[MYSQL] ERROR: " + string(mysql_error(Connection)));
		return 0;
	}
	else
	{
		MYSQL_RES *Result = mysql_store_result(Connection);
		if (Result)
		{
			vector<string> Row = MySQLFetchRow(Result);
			while (!Row.empty())
			{
				GameAliases.push(UTIL_ToUInt32(Row[0]));
				Row = MySQLFetchRow(Result);
			}
			mysql_free_result(Result);
		}
		else
		{
			CONSOLE_Print("[MYSQL] ERROR: " + string(mysql_error(Connection)));
			return 0;
		}
	}
	CONSOLE_Print("[STATS] found [" + UTIL_ToString(GameAliases.size()) + "] game types/aliases");

	while (!GameAliases.empty())
	{
		uint32_t Alias_ID = GameAliases.front();
		GameAliases.pop();

		CONSOLE_Print("[STATS] ***************************************************************************************");
		CONSOLE_Print("[STATS] Start processing all game data under game types/aliases [" + UTIL_ToString(Alias_ID) + "]");

		CONSOLE_Print("[STATS] getting unscored games");

		//查询未处理积分的游戏
		queue<uint32_t> UnscoredGames;
		string QSelectUnscored = "SELECT id FROM oh_games WHERE alias_id = " + UTIL_ToString(Alias_ID) + " AND stats = 0 ORDER BY id;";

		//输出查询语句
		//CONSOLE_Print("[SQL] [" + QSelectUnscored + "]");

		if (mysql_real_query(Connection, QSelectUnscored.c_str(), QSelectUnscored.size()) != 0)
		{
			CONSOLE_Print("[MYSQL] ERROR: " + string(mysql_error(Connection)));
			return 0;
		}
		else
		{
			MYSQL_RES *Result = mysql_store_result(Connection);
			if (Result)
			{
				vector<string> Row = MySQLFetchRow(Result);
				while (!Row.empty())
				{
					UnscoredGames.push(UTIL_ToUInt32(Row[0]));
					Row = MySQLFetchRow(Result);
				}
				mysql_free_result(Result);
			}
			else
			{
				CONSOLE_Print("[MYSQL] ERROR: " + string(mysql_error(Connection)));
				return 0;
			}
		}

		CONSOLE_Print("[STATS] found [" + UTIL_ToString(UnscoredGames.size()) + "] unscored games");

		//处理游戏下的用户数据

		while (!UnscoredGames.empty())
		{
			uint32_t GameID = UnscoredGames.front();
			UnscoredGames.pop();

			CONSOLE_Print("[STATS] gameid [" + UTIL_ToString(GameID) + "] found");
			CONSOLE_Print("[STATS] Processing Games data for [" + UTIL_ToString(GameID) + "] ");

			//查询游戏时间
			CONSOLE_Print("[STATS] Query game building time");

			queue<uint32_t> TGameTimes;
			uint32_t t_year, t_month, t_day, t_hour, t_minute, t_second;
			string QSelectTGameTimes = "SELECT datetime FROM oh_games WHERE id = " + UTIL_ToString(GameID) + ";";

			//CONSOLE_Print("[SQL] [" + QSelectTGameTimes + "]");

			if (mysql_real_query(Connection, QSelectTGameTimes.c_str(), QSelectTGameTimes.size()) != 0)
			{
				CONSOLE_Print("[MYSQL] ERROR: " + string(mysql_error(Connection)));
				return 0;
			}
			else
			{
				MYSQL_RES *Result = mysql_store_result(Connection);
				if (Result)
				{
					vector<string> Row = MySQLFetchRow(Result);
					if (Row.size() > 1)
					{
						CONSOLE_Print("[STATS] There are multiple data records in the database [" + Row[0] + "]");
						return 0;
					}
					else if (Row.size() == 1)
					{
						//分解时间
						CONSOLE_Print("[STATS] Decomposing game building time");
						sscanf(Row[0].c_str(), "%d-%d-%d %d:%d:%d", &t_year, &t_month, &t_day, &t_hour, &t_minute, &t_second);
						//CONSOLE_Print("[STATS] game building time [" + Row[0] + "]");
						//CONSOLE_Print("[STATS] game building time of Year [" + UTIL_ToString(t_year) + "]");
						//CONSOLE_Print("[STATS] game building time of Month [" + UTIL_ToString(t_month) + "]");
						//CONSOLE_Print("[STATS] game building time of Day [" + UTIL_ToString(t_day) + "]");
						//CONSOLE_Print("[STATS] game building time of Hour [" + UTIL_ToString(t_hour) + "]");
						//CONSOLE_Print("[STATS] game building time of Minute [" + UTIL_ToString(t_minute) + "]");
						//CONSOLE_Print("[STATS] game building time of Second [" + UTIL_ToString(t_second) + "]");
					}
					//mysql_free_result(Result);
				}
				else
				{
					CONSOLE_Print("[MYSQL] ERROR: " + string(mysql_error(Connection)));
					return 0;
				}
			}

			//查询游戏下所有玩家信息
			//string QSelectPlayers = "SELECT s.id, gp.name, dp.kills, dp.deaths, dp.assists, dp.creepkills, dp.creepdenies, dp.neutralkills, dp.towerkills, dp.raxkills, gp.spoofedrealm,gp.reserved, gp.left, gp.ip, g.duration, dg.winner, dp.newcolour, gp.team, s.streak, s.maxstreak, s.losingstreak, s.maxlosingstreak, s.alias_id AS s_alias_id, s.`month` AS s_month, s.`year` AS s_year, gp.player_id, g.alias_id AS g_alias_id, g.datetime FROM oh_gameplayers as gp LEFT JOIN oh_dotaplayers as dp ON gp.gameid=dp.gameid AND gp.colour=dp.newcolour LEFT JOIN oh_games as g on g.id=gp.gameid LEFT JOIN oh_stats as s ON gp.name = s.player_lower AND s.year = " + UTIL_ToString(t_year) + " AND s.`month` = " + UTIL_ToString(t_month) + " LEFT JOIN oh_dotagames as dg ON dg.gameid = gp.gameid WHERE gp.gameid = " + UTIL_ToString(GameID) + " AND g.alias_id = " + UTIL_ToString(Alias_ID);
			string QSelectPlayers = "SELECT gp.name, dp.kills, dp.deaths, dp.assists, dp.creepkills, dp.creepdenies, dp.neutralkills, dp.towerkills, dp.raxkills, gp.spoofedrealm,gp.reserved, gp.left, gp.ip, g.duration, dg.winner, dp.newcolour, gp.team, gp.player_id, g.alias_id, g.datetime FROM oh_gameplayers as gp LEFT JOIN oh_dotaplayers as dp ON gp.gameid=dp.gameid AND gp.colour=dp.newcolour LEFT JOIN oh_games as g on g.id=gp.gameid LEFT JOIN oh_dotagames as dg ON dg.gameid = gp.gameid WHERE gp.gameid = " + UTIL_ToString(GameID) + " AND g.alias_id = " + UTIL_ToString(Alias_ID) + ";";

			//输出语句
			//CONSOLE_Print("[SQL] [" + QSelectPlayers + "]");

			if (mysql_real_query(Connection, QSelectPlayers.c_str(), QSelectPlayers.size()) != 0)
			{
				CONSOLE_Print("[MYSQL] ERROR: " + string(mysql_error(Connection)));
				return 0;
			}
			else
			{
				MYSQL_RES *Result = mysql_store_result(Connection);
				if (Result)
				{
					//CONSOLE_Print("[STATS] gameid [" + UTIL_ToString(GameID) + "] found");
					//CONSOLE_Print("[STATS] ScoreWin [" + UTIL_ToString(ScoreWin) + "] found");
					//CONSOLE_Print("[STATS] ScoreLosse [" + UTIL_ToString(ScoreLosse) + "] found");

					bool ignore = false;			//是否忽略
					uint32_t pn_id[11];				//玩家ID
					uint32_t pn_pid[11];			//用户在oh_stats_players表中的ID
					uint32_t pn_alias_id[11];		//游戏类别的ID
					string   ps_names[30];			//玩家名称
					string   ps_lnames[30];			//玩家名字
					string   ps_last_seen[50];		//最后查看时间
					string   ps_country_code[4];	//国家代码
					string   ps_country[40];		//国家
					uint32_t pn_month[2];			//月
					uint32_t pn_year[4];			//年
					uint32_t pn_userlevel[2];		//用户在oh_users表中的等级，和oh_users中的user_level一样
					//uint32_t pn_fgproxy[1];			//
					int32_t  pn_score[11];			//积分
					uint32_t pn_games[11];			//游戏局数
					uint32_t pn_wins[11];			//胜利次数
					uint32_t pn_losses[11];			//失败次数
					int32_t  pn_draw[11];			//
					uint32_t pn_kills[11];			//杀人次数
					uint32_t pn_deaths[11];			//死亡次数
					uint32_t pn_assists[11];		//助攻次数
					uint32_t pn_creeps[11];			//补兵次数
					uint32_t pn_denies[11];			//反补兵次数
					uint32_t pn_neutrals[11];		//野怪次数
					uint32_t pn_towers[11];			//推塔次数
					uint32_t pn_rax[11];			//兵营
					uint32_t pn_banned[1];			//是否被封号
					string   ps_realm[100];			//玩家所在的服务器
					uint32_t pn_reserved[11];		//
					uint32_t pn_leaver[11];			//掉线或者早退
					string   ps_ip[16];				//IP地址
					uint32_t pn_streak[11];			//胜利次数
					uint32_t pn_maxstreak[11];		//最大胜利
					uint32_t pn_losingstreak[11];	//失败
					uint32_t pn_maxlosingstreak[11];//最大失败
					uint32_t pn_zerodeaths[11];		//零死亡次数
					//uint32_t pn_points[11];			//
					//uint32_t pn_points_bet[11];		//
					//uint32_t pn_hide[1];			//
					//uint32_t pn_updated[1];			//

					bool exists[10];      //玩家是否存在
					int num_players = 0;
					//int player_teams[10];
					int num_teams = 2;
					float team_ratings[2];
					float team_winners[2];
					int team_numplayers[2];
					team_ratings[0] = 0.0;
					team_ratings[1] = 0.0;
					team_numplayers[0] = 0;
					team_numplayers[1] = 0;

					vector<string> Row = MySQLFetchRow(Result);

					CONSOLE_Print("[STATS] Player data Found, Data Len [" + UTIL_ToString(Row.size()) + "]");

					while (Row.size() == 20)
					{
						CONSOLE_Print("[STATS] Processing user data for [" + UTIL_ToString(num_players) + "] ");

						if (num_players >= 10)
						{
							CONSOLE_Print("[STATS] gameid [" + UTIL_ToString(GameID) + "] has more than 10 players, ignoring");
							ignore = true;
							break;
						}

						string   tmp_name = Row[0];					//名字
						uint32_t tmp_kills = UTIL_ToUInt32(Row[1]);	//人头
						uint32_t tmp_deaths = UTIL_ToUInt32(Row[2]);	//死亡
						uint32_t tmp_assists = UTIL_ToUInt32(Row[3]);	//助攻
						uint32_t tmp_creepkills = UTIL_ToUInt32(Row[4]);	//补兵
						uint32_t tmp_creepdenies = UTIL_ToUInt32(Row[5]);	//反补
						uint32_t tmp_neutralkills = UTIL_ToUInt32(Row[6]);	//野怪
						uint32_t tmp_towerkills = UTIL_ToUInt32(Row[7]);	//推塔
						uint32_t tmp_raxkills = UTIL_ToUInt32(Row[8]);	//兵营
						string   tmp_realm = Row[9];					//服务器
						uint32_t tmp_reserved = UTIL_ToUInt32(Row[10]);	//保留
						uint32_t tmp_left = UTIL_ToUInt32(Row[11]);	//时长
						string   tmp_ip = Row[12];					//IP地址
						uint32_t tmp_duration = UTIL_ToUInt32(Row[13]);	//游戏时长
						uint32_t tmp_Winner = UTIL_ToUInt32(Row[14]);	//胜利标识
						uint32_t tmp_newcolour = UTIL_ToUInt32(Row[15]);	//颜色
						uint32_t tmp_team = UTIL_ToUInt32(Row[16]);	//军团
						uint32_t tmp_player_id = UTIL_ToUInt32(Row[17]);	//玩家ID
						uint32_t tmp_alias_id = UTIL_ToUInt32(Row[18]);	//游戏类别ID
						string   tmp_datetime = Row[19];					//游戏开始时间

						//CONSOLE_Print("[STATS] tmp_raxkills： " + UTIL_ToString(tmp_raxkills));
						
						//输出Winner信息
						//CONSOLE_Print("[STATS] Output Winner message： " + UTIL_ToString(tmp_Winner));

						if (tmp_Winner != 1 && tmp_Winner != 2 && tmp_Winner != 0)
						{
							CONSOLE_Print("[STATS] gameid [" + UTIL_ToString(GameID) + "] is not a two team map, ignoring");
							ignore = true;
							break;
						}
						else if (tmp_Winner == 1)	//近卫胜利
						{
							team_winners[0] = 1.0;
							team_winners[1] = 0.0;
						}
						else						//天灾胜利
						{
							team_winners[0] = 0.0;
							team_winners[1] = 1.0;
						}

						//CONSOLE_Print("[STATS] team_winners[0]=" + UTIL_ToString(team_winners[0]) + " ,team_winners[1]=" + UTIL_ToString(team_winners[1]));

						//获取玩家名称
						ps_names[num_players] = tmp_name;

						//输出玩家名称
						//CONSOLE_Print("[STATS] The Player Name:[" + ps_names[num_players] + "]");

						std::transform(tmp_name.begin(), tmp_name.end(), tmp_name.begin(), ::tolower);

						ps_lnames[num_players] = tmp_name;

						//获取玩家所在的服务器
						ps_realm[num_players] = tmp_realm;
						pn_banned[num_players] = 0;
						
						//查询玩家是否被封号
						string PlayerStatus = "SELECT name FROM oh_bans WHERE name = '" + ps_names[num_players] + "';";

						//输出查询语句
						//CONSOLE_Print("[SQL] [" + PlayerStatus + "]");

						if (mysql_real_query(Connection, PlayerStatus.c_str(), PlayerStatus.size()) != 0)
						{
							CONSOLE_Print("[MYSQL] ERROR: " + string(mysql_error(Connection)));
							return 0;
						}
						else
						{
							MYSQL_RES *Result = mysql_store_result(Connection);
							if (Result)
							{
								vector<string> Row = MySQLFetchRow(Result);
								if (Row.size() == 1)
								{
									pn_banned[num_players] = 1;
									CONSOLE_Print("[BAN] Player [" + ps_names[num_players] + "] is banned");
								}
								else
								{
									pn_banned[num_players] = 0;
									CONSOLE_Print("[BAN] Player [" + ps_names[num_players] + "] is not banned");
								}
								mysql_free_result(Result);
							}
						}

						//输出玩家所在的服务器
						//CONSOLE_Print("[STATS] Player Realm:[" + tmp_realm + "]");

						//输出玩家的颜色
						//CONSOLE_Print("[STATS] The Colour of the player " + ps_names[num_players] + " is [" + UTIL_ToString(tmp_newcolour) + "]");

						//查询用户是否存在
						string QExistStatus = "SELECT * FROM oh_stats WHERE player = '" + ps_names[num_players] + "' order by id desc limit 1;";

						//输出查询语句
						//CONSOLE_Print("[SQL] [" + QExistStatus + "]");

						if (mysql_real_query(Connection, QExistStatus.c_str(), QExistStatus.size()) != 0)
						{
							CONSOLE_Print("[MYSQL] ERROR: " + string(mysql_error(Connection)));
							return 0;
						}
						else
						{
							MYSQL_RES *Result = mysql_store_result(Connection);
							if (Result)
							{
								vector<string> Row = MySQLFetchRow(Result);

								//CONSOLE_Print("[STATS] Player oh_stats data Len [" + UTIL_ToString(Row.size()) + "]");

								//判断是否新用户，即用户是否在oh_stats表中存在,并获取已存在用户的信息
								if (!Row.empty())
								{
									exists[num_players] = true;
									pn_id[num_players] = UTIL_ToUInt32(Row[0]);
									pn_pid[num_players] = UTIL_ToUInt32(Row[1]);
									pn_alias_id[num_players] = UTIL_ToUInt32(Row[2]);
									//ps_names[num_players] = Row[3];
									//ps_lnames[num_players] = Row[4];
									ps_last_seen[num_players] = Row[5];
									ps_country_code[num_players] = Row[6];
									ps_country[num_players] = Row[7];
									pn_month[num_players] = UTIL_ToUInt32(Row[8]);
									pn_year[num_players] = UTIL_ToUInt32(Row[9]);
									pn_userlevel[num_players] = UTIL_ToUInt32(Row[10]);
									//pn_fgproxy[num_players] = UTIL_ToUInt32(Row[11]);
									pn_score[num_players] = UTIL_ToInt32(Row[12]);
									pn_games[num_players] = UTIL_ToUInt32(Row[13]);
									pn_wins[num_players] = UTIL_ToUInt32(Row[14]);
									pn_losses[num_players] = UTIL_ToUInt32(Row[15]);
									pn_draw[num_players] = UTIL_ToUInt32(Row[16]);
									pn_kills[num_players] = UTIL_ToUInt32(Row[17]);
									pn_deaths[num_players] = UTIL_ToUInt32(Row[18]);
									pn_assists[num_players] = UTIL_ToUInt32(Row[19]);
									pn_creeps[num_players] = UTIL_ToUInt32(Row[20]);
									pn_denies[num_players] = UTIL_ToUInt32(Row[21]);
									pn_neutrals[num_players] = UTIL_ToUInt32(Row[22]);
									pn_towers[num_players] = UTIL_ToUInt32(Row[23]);
									pn_rax[num_players] = UTIL_ToUInt32(Row[24]);
									//pn_banned[num_players] = UTIL_ToUInt32(Row[25]);
									//ps_realm[num_players] = Row[26];
									pn_reserved[num_players] = UTIL_ToUInt32(Row[27]);
									pn_leaver[num_players] = UTIL_ToUInt32(Row[28]);
									//ps_ip[num_players] = Row[29];
									pn_streak[num_players] = UTIL_ToUInt32(Row[30]);
									pn_maxstreak[num_players] = UTIL_ToUInt32(Row[31]);
									pn_losingstreak[num_players] = UTIL_ToUInt32(Row[32]);
									pn_maxlosingstreak[num_players] = UTIL_ToUInt32(Row[33]);
									pn_zerodeaths[num_players] = UTIL_ToUInt32(Row[34]);
									//pn_points[num_players] = UTIL_ToUInt32(Row[35]);
									//pn_points_bet[num_players] = UTIL_ToUInt32(Row[36]);
									//pn_hide[num_players] = UTIL_ToUInt32(Row[37]);
									//pn_updated[num_players] = UTIL_ToUInt32(Row[38]);
									CONSOLE_Print("[STATS] Exist Player Found, ID [" + UTIL_ToString(pn_id[num_players]) + "]");
								}
								//用户不存在，初始化用户相关数据变量
								else
								{
									CONSOLE_Print("[STATS] New Player Found");
									exists[num_players] = false;
									pn_id[num_players] = 0;
									pn_pid[num_players] = 0;
									pn_alias_id[num_players] = 1;
									//ps_names[num_players] = "";
									//ps_lnames[num_players] = "";
									//ps_last_seen[num_players] = "";
									//ps_country_code[num_players] = "";
									//ps_country[num_players] = "";
									pn_month[num_players] = t_month;
									pn_year[num_players] = t_year;
									//pn_userlevel[num_players] = 0;
									//pn_fgproxy[num_players] = 0;
									pn_score[num_players] = 0;
									pn_games[num_players] = 0;
									pn_wins[num_players] = 0;
									pn_losses[num_players] = 0;
									pn_draw[num_players] = 0;
									pn_kills[num_players] = 0;
									pn_deaths[num_players] = 0;
									pn_assists[num_players] = 0;
									pn_creeps[num_players] = 0;
									pn_denies[num_players] = 0;
									pn_neutrals[num_players] = 0;
									pn_towers[num_players] = 0;
									pn_rax[num_players] = 0;
									//pn_banned[num_players] = 0;
									//ps_realm[num_players] = "";
									pn_reserved[num_players] = 0;
									pn_leaver[num_players] = 0;
									//ps_ip[num_players] = "";
									pn_streak[num_players] = 0;
									pn_maxstreak[num_players] = 0;
									pn_losingstreak[num_players] = 0;
									pn_maxlosingstreak[num_players] = 0;
									pn_zerodeaths[num_players] = 0;
									//pn_points[num_players] = 0;
									//pn_points_bet[num_players] = 0;
									//pn_hide[num_players] = 0;
									//pn_updated[num_players] = 0;
								}

								//在oh_stats表中查到用户
								if (exists[num_players])
								{
									//近卫军团 数据处理
									if (tmp_newcolour >= 1 && tmp_newcolour <= 5)
									{
										team_numplayers[0]++;
										if (tmp_Winner == 1)
										{
											CONSOLE_Print("[STATS] Sentinel Winner");

											pn_score[num_players] = pn_score[num_players] + ScoreWin;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " score [ " + Int32_ToString(pn_score[num_players] - ScoreWin) + " -> " + Int32_ToString(pn_score[num_players]) + " ]");

											pn_games[num_players] = pn_games[num_players] + 1;
											pn_wins[num_players] = pn_wins[num_players] + 1;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " wins [ " + UTIL_ToString(pn_wins[num_players] - 1) + " -> " + UTIL_ToString(pn_wins[num_players]) + " ]");

											pn_streak[num_players] = pn_streak[num_players] + 1;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " streak [ " + UTIL_ToString(pn_streak[num_players] - 1) + " -> " + UTIL_ToString(pn_streak[num_players]) + " ]");

											if (pn_maxstreak[num_players] < pn_streak[num_players] + 1)
											{
												pn_maxstreak[num_players] = pn_maxstreak[num_players] + 1;
												CONSOLE_Print("[STATS] player " + ps_names[num_players] + " maxstreak [ " + UTIL_ToString(pn_maxstreak[num_players] - 1) + " -> " + UTIL_ToString(pn_maxstreak[num_players]) + " ]");
											}
										}
										else if (tmp_Winner == 2)
										{
											CONSOLE_Print("[STATS] Sentinel loser");

											pn_score[num_players] = pn_score[num_players] - ScoreLosse;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " score [ " + Int32_ToString(pn_score[num_players] + ScoreLosse) + " -> " + Int32_ToString(pn_score[num_players]) + " ]");

											pn_games[num_players] = pn_games[num_players] + 1;
											pn_losses[num_players] = pn_losses[num_players] + 1;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " losses [" + UTIL_ToString(pn_losses[num_players] - 1) + "->" + UTIL_ToString(pn_losses[num_players]) + "]");

											pn_losingstreak[num_players] = pn_losingstreak[num_players] + 1;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " losingstreak [ " + UTIL_ToString(pn_losingstreak[num_players] - 1) + " -> " + UTIL_ToString(pn_losingstreak[num_players]) + " ]");

											if (pn_maxlosingstreak[num_players] < pn_losingstreak[num_players] + 1)
											{
												pn_maxlosingstreak[num_players] = pn_maxlosingstreak[num_players] + 1;
												CONSOLE_Print("[STATS] player " + ps_names[num_players] + " maxlosingstreak [ " + UTIL_ToString(pn_maxlosingstreak[num_players] - 1) + " -> " + UTIL_ToString(pn_maxlosingstreak[num_players]) + " ]");
											}

										}
										else
										{
											pn_draw[num_players] = pn_draw[num_players] + 1;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " draw [ " + UTIL_ToString(pn_draw[num_players] - 1) + " -> " + UTIL_ToString(pn_draw[num_players]) + " ]");
										}
									}

									//天灾军团 数据处理
									else if (tmp_newcolour >= 7 && tmp_newcolour <= 11)
									{
										team_numplayers[1]++;
										if (tmp_Winner == 2)
										{
											CONSOLE_Print("[STATS] Scourge Winner");

											pn_score[num_players] = pn_score[num_players] + ScoreWin;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " score [ " + Int32_ToString(pn_score[num_players] - ScoreWin) + " -> " + Int32_ToString(pn_score[num_players]) + " ]");

											pn_games[num_players] = pn_games[num_players] + 1;
											pn_wins[num_players] = pn_wins[num_players] + 1;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " wins [ " + UTIL_ToString(pn_wins[num_players] - 1) + " -> " + UTIL_ToString(pn_wins[num_players]) + " ]");

											pn_streak[num_players] = pn_streak[num_players] + 1;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " streak [ " + UTIL_ToString(pn_streak[num_players] - 1) + " -> " + UTIL_ToString(pn_streak[num_players]) + " ]");

											if (pn_maxstreak[num_players] < pn_streak[num_players] + 1)
											{
												pn_maxstreak[num_players] = pn_maxstreak[num_players] + 1;
												CONSOLE_Print("[STATS] player " + ps_names[num_players] + " maxstreak [ " + UTIL_ToString(pn_maxstreak[num_players] - 1) + " -> " + UTIL_ToString(pn_maxstreak[num_players]) + " ]");
											}
										}
										else if (tmp_Winner == 1)
										{
											CONSOLE_Print("[STATS] Scourge loser");
											
											pn_score[num_players] = pn_score[num_players] - ScoreLosse;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " score [ " + Int32_ToString(pn_score[num_players] + ScoreLosse) + " -> " + Int32_ToString(pn_score[num_players]) + " ]");

											pn_games[num_players] = pn_games[num_players] + 1;
											pn_losses[num_players] = pn_losses[num_players] + 1;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " losses [ " + UTIL_ToString(pn_losses[num_players] - 1) + " -> " + UTIL_ToString(pn_losses[num_players]) + " ]");

											pn_losingstreak[num_players] = pn_losingstreak[num_players] + 1;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " losingstreak [ " + UTIL_ToString(pn_losingstreak[num_players] - 1) + " -> " + UTIL_ToString(pn_losingstreak[num_players]) + " ]");

											if (pn_maxlosingstreak[num_players] < pn_losingstreak[num_players] + 1)
											{
												pn_maxlosingstreak[num_players] = pn_maxlosingstreak[num_players] + 1;
												CONSOLE_Print("[STATS] player " + ps_names[num_players] + " maxlosingstreak [ " + UTIL_ToString(pn_maxlosingstreak[num_players] - 1) + " -> " + UTIL_ToString(pn_maxlosingstreak[num_players]) + " ]");
											}
										}
										else
										{
											pn_draw[num_players] = pn_draw[num_players] + 1;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " draw [ " + UTIL_ToString(pn_draw[num_players] - 1) + " -> " + UTIL_ToString(pn_draw[num_players]) + " ]");
										}
									}

									else
									{
										CONSOLE_Print("[STATS] gameid [" + UTIL_ToString(GameID) + "] has a player with an invalid newcolour, ignoring");
										ignore = true;
										break;
									}
								}

								//新用户
								else
								{
									//近卫军团 数据处理
									if (tmp_newcolour >= 1 && tmp_newcolour <= 5)
									{
										team_numplayers[0]++;
										if (tmp_Winner == 1)
										{
											CONSOLE_Print("[STATS] Sentinel Winner");
											pn_score[num_players] = ScoreStart + ScoreWin;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " score [ 0 -> " + Int32_ToString(pn_score[num_players]) + " ]");

											pn_games[num_players] = 1;
											pn_wins[num_players] = 1;
											pn_streak[num_players] = 1;
											pn_maxstreak[num_players] = 1;
											pn_maxlosingstreak[num_players] = 0;
										}
										else if (tmp_Winner == 2)
										{
											pn_score[num_players] = ScoreStart - ScoreLosse;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " score [ 0 -> " + Int32_ToString(pn_score[num_players]) + " ]");

											pn_games[num_players] = 1;
											pn_losses[num_players] = 1;
											pn_losingstreak[num_players] = 1;
											pn_maxlosingstreak[num_players] = 1;
											pn_maxstreak[num_players] = 0;
											CONSOLE_Print("[STATS] Sentinel loser");
										}
										else
										{
											pn_draw[num_players] = 1;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " draw [ 0 -> " + UTIL_ToString(pn_draw[num_players]) + " ]");
											pn_maxstreak[num_players] = 0;
											pn_maxlosingstreak[num_players] = 0;
										}
									}

									//天灾军团 数据处理
									else if (tmp_newcolour >= 7 && tmp_newcolour <= 11)
									{
										//CONSOLE_Print("[STATS] tmp_winner:" + UTIL_ToString(tmp_Winner));
										team_numplayers[1]++;
										if (tmp_Winner == 2)
										{
											CONSOLE_Print("[STATS] Scourge Winner");

											pn_score[num_players] = ScoreStart + ScoreWin;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " score [ 0 -> " + Int32_ToString(pn_score[num_players]) + " ]");

											pn_games[num_players] = 1;
											pn_wins[num_players] = 1;
											pn_streak[num_players] = 1;
											pn_maxstreak[num_players] = 1;
											pn_maxlosingstreak[num_players] = 0;
										}
										else if (tmp_Winner == 1)
										{
											CONSOLE_Print("[STATS] Scourge loser");

											pn_score[num_players] = pn_score[num_players] + ScoreStart - ScoreLosse;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " score [ 0 -> " + Int32_ToString(pn_score[num_players]) + " ]");

											pn_games[num_players] = 1;
											pn_losses[num_players] = 1;
											pn_maxstreak[num_players] = 0;
											pn_losingstreak[num_players] = 1;
											pn_maxlosingstreak[num_players] = 1;
										}
										else
										{
											pn_draw[num_players] = 1;
											CONSOLE_Print("[STATS] player " + ps_names[num_players] + " draw [ 0 -> " + UTIL_ToString(pn_draw[num_players]) + " ]");
											pn_maxstreak[num_players] = 0;
											pn_maxlosingstreak[num_players] = 0;
										}
									}

									else
									{
										CONSOLE_Print("[STATS] gameid [" + UTIL_ToString(GameID) + "] has a player with an invalid newcolour, ignoring");
										ignore = true;
										break;
									}
								}
								//mysql_free_result(Result);
							}
						}

						//IP地址
						ps_ip[num_players] = tmp_ip;

						//
						pn_reserved[num_players] = tmp_reserved;

						pn_kills[num_players] = pn_kills[num_players] + tmp_kills;

						pn_deaths[num_players] = pn_deaths[num_players] + tmp_deaths;

						if (tmp_deaths == 0)
							pn_zerodeaths[num_players] = pn_zerodeaths[num_players] + 1;
						else
							pn_zerodeaths[num_players] = 0;

						pn_assists[num_players] = pn_assists[num_players] + tmp_assists;
						pn_creeps[num_players] = pn_creeps[num_players] + tmp_creepkills;
						pn_denies[num_players] = pn_denies[num_players] + tmp_creepdenies;
						pn_neutrals[num_players] = pn_neutrals[num_players] + tmp_neutralkills;
						pn_towers[num_players] = pn_towers[num_players] + tmp_towerkills;
						pn_rax[num_players] = pn_rax[num_players] + tmp_raxkills;

						//CONSOLE_Print("[STATS] pn_rax： " + UTIL_ToString(pn_rax[num_players]));

						//CONSOLE_Print("[STATS] tmp_raxkills： " + UTIL_ToString(tmp_raxkills));

						// Leaver detect
						if (tmp_left < tmp_duration - 500)
							pn_leaver[num_players] = pn_leaver[num_players] + 1;
						else
							pn_leaver[num_players] = 0;

						pn_pid[num_players] = tmp_player_id;
						pn_alias_id[num_players] = tmp_alias_id;

						if ((pn_year[num_players] != t_year) && (pn_month[num_players] != t_month))
						{
							pn_year[num_players] = t_year;
							pn_month[num_players] = t_month;
							exists[num_players] = false;
						}

						num_players++;
						Row = MySQLFetchRow(Result);
					}

					mysql_free_result(Result);


					if (!ignore)
					{
						if (num_players == 0)
							CONSOLE_Print("[STATS] gameid [" + UTIL_ToString(GameID) + "] has no players, ignoring");
						else if (team_numplayers[0] == 0)
							CONSOLE_Print("[STATS] gameid [" + UTIL_ToString(GameID) + "] has no Sentinel players, ignoring");
						else if (team_numplayers[1] == 0)
							CONSOLE_Print("[STATS] gameid [" + UTIL_ToString(GameID) + "] has no Scourge players, ignoring");
						else
						{
							CONSOLE_Print("[STATS] gameid [" + UTIL_ToString(GameID) + "] is calculating");

							for (int i = 0; i < num_players; i++)
							{
								CONSOLE_Print("[STATS] player [" + ps_names[i] + "] score " + " -> " + Int32_ToString(pn_score[i]));

								//查询IP地址所属国家
								string c_code, c_country;
								string QIPArea = "SELECT code, country FROM oh_geoip WHERE INET_ATON('" + ps_ip[i] + "') BETWEEN ip_start_int AND ip_end_int;";

								//CONSOLE_Print("[SQL] [" + QIPArea + "]");

								if (mysql_real_query(Connection, QIPArea.c_str(), QIPArea.size()) != 0)
								{
									CONSOLE_Print("[MYSQL] ERROR: " + string(mysql_error(Connection)));
									return 0;
								}
								else
								{
									MYSQL_RES *Result = mysql_store_result(Connection);
									if (Result)
									{
										vector<string> Row = MySQLFetchRow(Result);
										if (!Row.empty()) {
											c_code = Row[0];
											c_country = Row[1];
											//CONSOLE_Print("[STATS] player " + ps_names[i] + " Country_Code: [" + c_code + "]");
											//CONSOLE_Print("[STATS] player " + ps_names[i] + " Country: [" + c_country + "]");
										}
										mysql_free_result(Result);
									}
								}

								//更新用户数据
								if (exists[i])
								{
									string QUpdateScore = "UPDATE `oh_stats` SET score = " + Int32_ToString(pn_score[i]) + ", games = " + UTIL_ToString(pn_games[i]) + ", wins = " + UTIL_ToString(pn_wins[i]) + ", losses = " + UTIL_ToString(pn_losses[i]) + ", draw = " + UTIL_ToString(pn_draw[i]) + ", kills = " + UTIL_ToString(pn_kills[i]) + ", deaths = " + UTIL_ToString(pn_deaths[i]) + ", assists = " + UTIL_ToString(pn_assists[i]) + ", creeps = " + UTIL_ToString(pn_creeps[i]) + ", denies = " + UTIL_ToString(pn_denies[i]) + ", neutrals = " + UTIL_ToString(pn_neutrals[i]) + ", towers = " + UTIL_ToString(pn_towers[i]) + ", rax = " + UTIL_ToString(pn_rax[i]) + ", banned = " + UTIL_ToString(pn_banned[i]) + ", leaver = " + UTIL_ToString(pn_leaver[i]) + ",  ip= '" + ps_ip[i] + "', streak = " + UTIL_ToString(pn_streak[i]) + ", maxstreak = " + UTIL_ToString(pn_maxstreak[i]) + ", losingstreak = " + UTIL_ToString(pn_losingstreak[i]) + ", maxlosingstreak = " + UTIL_ToString(pn_maxlosingstreak[i]) + ", zerodeaths = " + UTIL_ToString(pn_zerodeaths[i]) + " WHERE id = " + UTIL_ToString(pn_id[i]) + ";";

									//CONSOLE_Print("[SQL] [" + QUpdateScore + "]");

									if (mysql_real_query(Connection, QUpdateScore.c_str(), QUpdateScore.size()) != 0)
									{
										CONSOLE_Print("[MYSQL] ERROR: " + string(mysql_error(Connection)));
										return 0;
									}
								}
								else
								{
									string EscName = MySQLEscapeString(Connection, ps_names[i]);
									string EscLName = MySQLEscapeString(Connection, ps_lnames[i]);
									string EscServer = MySQLEscapeString(Connection, ps_realm[i]);
									string QInsertScore = "INSERT INTO `oh_stats` ( pid, alias_id, player, player_lower, country_code, country, month, year, score, games, wins, losses, draw, kills, deaths, assists, creeps, denies, neutrals, towers, rax, banned, realm, reserved, leaver, ip, streak, maxstreak, losingstreak, maxlosingstreak, zerodeaths ) VALUES ( " + UTIL_ToString(pn_pid[i]) + ", " + UTIL_ToString(pn_alias_id[i]) + ", '" + EscName + "', '" + EscLName + "', '" + c_code + "', '" + c_country + "', " + UTIL_ToString(pn_month[i]) + ", " + UTIL_ToString(pn_year[i]) + ", " + Int32_ToString(pn_score[i]) + ", " + UTIL_ToString(pn_games[i]) + ", " + UTIL_ToString(pn_wins[i]) + ", " + UTIL_ToString(pn_losses[i]) + ", " + UTIL_ToString(pn_draw[i]) + ", " + UTIL_ToString(pn_kills[i]) + ", " + UTIL_ToString(pn_deaths[i]) + ", " + UTIL_ToString(pn_assists[i]) + ", " + UTIL_ToString(pn_creeps[i]) + ", " + UTIL_ToString(pn_denies[i]) + ", " + UTIL_ToString(pn_neutrals[i]) + ", " + UTIL_ToString(pn_towers[i]) + ", " + UTIL_ToString(pn_rax[i]) + ", " + UTIL_ToString(pn_banned[i]) + ", '" + ps_realm[i] + "', " + UTIL_ToString(pn_reserved[i]) + ", " + UTIL_ToString(pn_leaver[i]) + ", '" + ps_ip[i] + "', " + UTIL_ToString(pn_streak[i]) + ", " + UTIL_ToString(pn_maxstreak[i]) + ", " + UTIL_ToString(pn_losingstreak[i]) + ", " + UTIL_ToString(pn_maxlosingstreak[i]) + ", " + UTIL_ToString(pn_zerodeaths[i]) + " );";

									//CONSOLE_Print("[SQL] [" + QInsertScore + "]");

									if (mysql_real_query(Connection, QInsertScore.c_str(), QInsertScore.size()) != 0)
									{
										CONSOLE_Print("[MYSQL] ERROR: " + string(mysql_error(Connection)));
										return 0;
									}
								}
							}
						}
					}
					//mysql_free_result(Result); 
				}
				else
				{
					CONSOLE_Print("[MYSQL] ERROR: " + string(mysql_error(Connection)));
					return 0;
				}
			}
			CONSOLE_Print("[STATS] Update Game [" + UTIL_ToString(GameID) + "] data");
			string QInsertScored1 = "UPDATE oh_games SET stats = 1 WHERE id = " + UTIL_ToString(GameID) + ";";
			//CONSOLE_Print("[SQL] [" + QInsertScored1 + "]");
			
			if (mysql_real_query(Connection, QInsertScored1.c_str(), QInsertScored1.size()) != 0)
			{
			CONSOLE_Print("[MYSQL] ERROR: " + string(mysql_error(Connection)));
			return 0;
			}
			
		}
		CONSOLE_Print("[STATS] ***************************************************************************************");
	}
	CONSOLE_Print("[STATS] committing transaction");

	string QCommit = "COMMIT";

	if (mysql_real_query(Connection, QCommit.c_str(), QCommit.size()) != 0)
	{
		CONSOLE_Print("[MYSQL] ERROR: " + string(mysql_error(Connection)));
		return 0;
	}

	CONSOLE_Print("[STATS] done");
	//CONSOLE_Print("");
	return 1;
}
