/**
  * Copyright [2013-2014] [OHsystem]
  * 
  * OHSystem is free software: You can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation, either version 3 of the License, or
  * (at your option) any later version.
  * 
  * Please save the copyrights and notifications on the footer.
  * 
  * You can contact the developers on: ohsystem-public@googlegroups.com
  * or join us directly here: https://groups.google.com/d/forum/ohsystem-public
  * 
  * Visit us also on http://ohsystem.net/ and keep track always of the latest
  * features and changes.
  * 
  * 
  * This is modified from GHOST++: http://ghostplusplus.googlecode.com/
  * Official GhostPP-Forum: http://ghostpp.com/
 */

// seems like a solution for the compile process on windows, the problem is the linking of the libs.
#ifdef WIN32
 #pragma comment(lib, "winmm.lib")
#endif
 
#include <iomanip>
#include <iostream>
#include <map>
#include <queue>
#include <sstream>
#include <string>
#include <vector>
#include <algorithm>

using namespace std;

#ifdef WIN32
 #include "ms_stdint.h"
#else
 #include <stdint.h>
#endif

#include "config.h"
#include "includes.h"
#include "util.h"

#include <string.h>

#ifdef WIN32
 #include <winsock.h>
#endif

#include <mysql/mysql.h>

#include <stdio.h>

uint32_t GetTicks( )
{
#ifdef WIN32
	// don't use GetTickCount anymore because it's not accurate enough (~16ms resolution)
	// don't use QueryPerformanceCounter anymore because it isn't guaranteed to be strictly increasing on some systems and thus requires "smoothing" code
	// use timeGetTime instead, which typically has a high resolution (5ms or more) but we request a lower resolution on startup

	return timeGetTime( );
#elif __APPLE__
	uint64_t current = mach_absolute_time( );
	static mach_timebase_info_data_t info = { 0, 0 };
	// get timebase info
	if( info.denom == 0 )
		mach_timebase_info( &info );
	uint64_t elapsednano = current * ( info.numer / info.denom );
	// convert ns to ms
	return elapsednano / 1e6;
#else
	uint32_t ticks;
	struct timespec t;
	clock_gettime( CLOCK_MONOTONIC, &t );
	ticks = t.tv_sec * 1000;
	ticks += t.tv_nsec / 1000000;
	return ticks;
#endif
}

void CONSOLE_Print( string message )
{
	string outer = "[STATSUPDATE] " + message;
	cout << outer << endl;
}

string MySQLEscapeString( MYSQL *conn, string str )
{
	char *to = new char[str.size( ) * 2 + 1];
	unsigned long size = mysql_real_escape_string( conn, to, str.c_str( ), str.size( ) );
	string result( to, size );
	delete [] to;
	return result;
}

vector<string> MySQLFetchRow( MYSQL_RES *res )
{
	vector<string> Result;

	MYSQL_ROW Row = mysql_fetch_row( res );

	if( Row )
	{
		unsigned long *Lengths;
		Lengths = mysql_fetch_lengths( res );

		for( unsigned int i = 0; i < mysql_num_fields( res ); i++ )
		{
			if( Row[i] )
				Result.push_back( string( Row[i], Lengths[i] ) );
			else
				Result.push_back( string( ) );
		}
	}

	return Result;
}

string UTIL_ToString( uint32_t i )
{
	string result;
	stringstream SS;
	SS << i;
	SS >> result;
	return result;
}

string Int32_ToString( int32_t i )
{
	string result;
        stringstream SS;
        SS << i;
        SS >> result;
        return result;
}
string UTIL_ToString( float f, int digits )
{
	string result;
	stringstream SS;
	SS << std :: fixed << std :: setprecision( digits ) << f;
	SS >> result;
	return result;
}

string UTIL_ToString( double d, int digits )
{
        string result;
        stringstream SS;
        SS << std :: fixed << std :: setprecision( digits ) << d;
        SS >> result;
        return result;
}

uint32_t UTIL_ToUInt32( string &s )
{
	uint32_t result;
	stringstream SS;
	SS << s;
	SS >> result;
	return result;
}

int32_t UTIL_ToInt32( string &s )
{
        int32_t result;
        stringstream SS;
        SS << s;
        SS >> result;
        return result;
}

float UTIL_ToFloat( string &s )
{
	float result;
	stringstream SS;
	SS << s;
	SS >> result;
	return result;
}

void Print_Error( std::string error )
{
	CONSOLE_Print( "[Error]: "+error );
	return;
}

MYSQL_RES *QueryBuilder( MYSQL* Connection, string Query )
{
	if( mysql_real_query( Connection, Query.c_str( ), Query.size( ) ) != 0 )
	{
		Print_Error( mysql_error( Connection ) );
		return 0;
	}
	return mysql_store_result( Connection );
}

int main( int argc, char **argv )
{
    boost::mutex m_UpdateMutex;

    boost::mutex::scoped_lock updateLock( m_UpdateMutex );

    string CFGFile = "default.cfg";
    vector<string> ErrorLog;
    if( argc > 1 && argv[1] )
            CFGFile = argv[1];

    CConfig CFG;
    CFG.Read( CFGFile );
    string Server = CFG.GetString( "db_mysql_server", string( ) );
    string Database = CFG.GetString( "db_mysql_database", "ghost" );
    string User = CFG.GetString( "db_mysql_user", string( ) );
    string Password = CFG.GetString( "db_mysql_password", string( ) );
    int Port = CFG.GetInt( "db_mysql_port", 0 );
    uint32_t ScoreStart = CFG.GetInt( "statsupdate_scorestart", 0 );
    int32_t ScoreWin = CFG.GetInt( "statsupdate_scorewin", 5 );
    int32_t ScoreLoose = CFG.GetInt( "statsupdate_scoreloose", 3 );
    uint32_t StreakBonus = CFG.GetInt( "statsupdate_streakbonus", 1 );
    uint32_t StatsUpdateLimit = CFG.GetInt( "statsupdate_limit", 1 );
    
    CONSOLE_Print( "Connecting to database..." );
    MYSQL *Connection = NULL;

    if( !( Connection = mysql_init( NULL ) ) )
    {
            Print_Error( mysql_error( Connection ) );
            return 0;
    }

    my_bool Reconnect = true;
    mysql_options( Connection, MYSQL_OPT_RECONNECT, &Reconnect );

    if( !( mysql_real_connect( Connection, Server.c_str( ), User.c_str( ), Password.c_str( ), Database.c_str( ), Port, NULL, 0 ) ) )
    {
            Print_Error( mysql_error( Connection ) );
            return 0;
    }

    CONSOLE_Print( "Successfully connected to the database." );
    CONSOLE_Print( "Starting transaction..." );

    MYSQL_RES *BeginResult = QueryBuilder(Connection, "BEGIN" );

    queue<string> UnscoredGames;
    MYSQL_RES *GameResult = QueryBuilder(Connection, "SELECT `id`, MONTH(`datetime`), YEAR(`datetime`), alias_id FROM `oh_games` WHERE `stats` = '0' AND `gamestatus` = '1' ORDER BY id LIMIT "+UTIL_ToString( StatsUpdateLimit )+";" );
    if( GameResult )
    {
            vector<string> Row = MySQLFetchRow( GameResult );
            while( !Row.empty( ) )
            {
                    UnscoredGames.push( Row[0]+" "+Row[3]+" "+Row[1]+" "+Row[2] );
                    Row = MySQLFetchRow( GameResult );
            }
            mysql_free_result( GameResult );
    }
    else
    {
            Print_Error( mysql_error( Connection ) );
            return 1;
    }

    bool updatedstats = UnscoredGames.size( ) == 0 ? false : true;

    uint32_t GameAmount=UnscoredGames.size( );
    CONSOLE_Print( "Found ["+UTIL_ToString(GameAmount)+"] unscored games." );
    uint32_t StartTicks = GetTicks();
    uint32_t SkippedGames = 0;
    while( !UnscoredGames.empty( ) )
    {
        string Data = UnscoredGames.front( );
        UnscoredGames.pop( );
        string GameID;
        string Month;
        string Year;
        string Alias;
        stringstream SS;
        SS << Data;
        SS >> GameID;
        SS >> Alias;
        SS >> Month;
        SS >> Year;
        
        CONSOLE_Print( "Starting update for gameid ["+GameID+"]" );
        
        // BASIC INFORMATION UPDATE PROCESS
        MYSQL_RES *BasicResult = QueryBuilder(Connection, "SELECT s.id, gp.name, gp.spoofedrealm, gp.reserved, gp.left, gp.ip, g.duration, gp.team, gp.colour FROM oh_gameplayers as gp LEFT JOIN oh_games as g on g.id=gp.gameid LEFT JOIN oh_stats as s ON ( gp.name = s.player_lower AND s.month="+Month+" AND s.year="+Year+" AND s.alias_id="+Alias+") WHERE gp.gameid = " + GameID );
        if( BasicResult )
        {
            vector<string> Row = MySQLFetchRow( BasicResult );
            // basic values
            uint32_t i_playerId[12];
            string s_playerName[12];
            string s_lowerPlayerName[12];
            string s_spoofedRealm[12];
            bool b_reserved[12];
            bool b_bannedPlayer[12];
            bool b_newPlayer[12];
            bool b_leaver[12];
            uint32_t i_playerLeft[12];
            string s_playerIp[12];
            uint32_t i_playerTeam[12];
            uint32_t i_playerColour[12];
            string s_playerScore[12];
            int32_t i_newPlayerScore[12];
            uint32_t i_wins[12];
            uint32_t i_losses[12];
            uint32_t i_draws[12];
            string s_playerWinStreak[12];
            string s_playerLooseStreak[12];
            int32_t i_winPoints[12];
            uint32_t i_winStreak[12];
            uint32_t i_maxWinStreak[12];
            uint32_t i_looseStreak[12];
            uint32_t i_maxLooseStreak[12];
            uint32_t i_userLevel[12];
            
            // dota values
            uint32_t i_ingameKills[12];
            uint32_t i_ingameDeaths[12];
            uint32_t i_ingameAssists[12];
            uint32_t i_ingameCreeps[12];
            uint32_t i_ingameDenies[12];
            uint32_t i_ingameNeutrals[12];
            uint32_t i_ingameTower[12];
            uint32_t i_ingameRaxes[12];
            bool b_zerodeaths[12];
            
            // legion td values
            uint32_t legionTdValue[12];
            uint32_t legionTdSeconds[12];
            int32_t legionTdScore[12];
            uint32_t legionTdWood[12];
            uint32_t legionTdWoodTotal[12];
            uint32_t legionTdLevel[12];
            uint32_t legionTdLeaked[12];
            uint32_t legionTdIncome[12];
            uint32_t legionTdGoldTotal[12];
            uint32_t legionTdGoldIncome[12];
            string legionTdRace[12];
            string legionTdName[12];
            string legionTdGameMode[12];
            
            // game values
            uint32_t i_gameDuration=0;
            uint32_t i_gameAlias=UTIL_ToUInt32(Alias);
            string s_gameAliasName="";
            bool b_ignored = false;
            bool b_w3mmdfixedwinner = false;
            
            int i_playerCounter = 0;
            while( Row.size( ) == 9 ) {
                
                //init dota relating values
                i_ingameKills[i_playerCounter]=0;
                i_ingameDeaths[i_playerCounter]=0;
                i_ingameAssists[i_playerCounter]=0;
                i_ingameCreeps[i_playerCounter]=0;
                i_ingameDenies[i_playerCounter]=0;
                i_ingameNeutrals[i_playerCounter]=0;
                i_ingameTower[i_playerCounter]=0;
                i_ingameRaxes[i_playerCounter]=0;
                b_zerodeaths[i_playerCounter]=false;
                
                //init legiontd relating values
                legionTdValue[i_playerCounter]=0;
                legionTdSeconds[i_playerCounter]=0;
                legionTdScore[i_playerCounter]=0;
                legionTdWood[i_playerCounter]=0;
                legionTdWoodTotal[i_playerCounter]=0;
                legionTdLevel[i_playerCounter]=0;
                legionTdLeaked[i_playerCounter]=0;
                legionTdIncome[i_playerCounter]=0;
                legionTdGoldTotal[i_playerCounter]=0;
                legionTdGoldIncome[i_playerCounter]=0;
                legionTdRace[i_playerCounter]="";
                legionTdName[i_playerCounter]="";
                legionTdGameMode[i_playerCounter]="";
                
                //general datas
                i_playerId[i_playerCounter]=0;
                s_playerName[i_playerCounter]="";
                s_lowerPlayerName[i_playerCounter]="";
                s_spoofedRealm[i_playerCounter]="";
                i_playerLeft[i_playerCounter]=0;
                s_playerIp[i_playerCounter]="";
                i_playerTeam[i_playerCounter]=0;
                i_playerColour[i_playerCounter]=0;
                s_playerScore[i_playerCounter]="";
                i_newPlayerScore[i_playerCounter]=0;
                i_wins[i_playerCounter]=0;
                i_losses[i_playerCounter]=0;
                i_draws[i_playerCounter]=0;
                s_playerWinStreak[i_playerCounter]="";
                s_playerLooseStreak[i_playerCounter]="";
                i_winPoints[i_playerCounter]=0;
                i_winStreak[i_playerCounter]=0;
                i_maxWinStreak[i_playerCounter]=0;
                i_looseStreak[i_playerCounter]=0;
                i_maxLooseStreak[i_playerCounter]=0;
                i_userLevel[i_playerCounter]=0;
                
                //init
                b_newPlayer[i_playerCounter]=false;
                b_reserved[i_playerCounter]=false;
                b_bannedPlayer[i_playerCounter]=false;
                b_leaver[i_playerCounter]=false;
                
                //define values
                if(i_playerCounter==0 &&! Row[6].empty( ) ) {
                    i_gameDuration = UTIL_ToUInt32(Row[6]);
                }
                
                if( Row[0].empty( ) )
                    b_newPlayer[i_playerCounter] = true;
                else
                    i_playerId[i_playerCounter] = UTIL_ToUInt32(Row[0]);
                
                s_playerName[i_playerCounter] = Row[1];
                s_lowerPlayerName[i_playerCounter] = s_playerName[i_playerCounter];
                std::transform( s_lowerPlayerName[i_playerCounter].begin(), s_lowerPlayerName[i_playerCounter].end(), s_lowerPlayerName[i_playerCounter].begin(), ::tolower);
                s_spoofedRealm[i_playerCounter] = Row[2];
                b_reserved[i_playerCounter] = ( UTIL_ToUInt32(Row[3]) == 1 ) ? true : false;
                i_playerLeft[i_playerCounter] = UTIL_ToUInt32(Row[4]);
                s_playerIp[i_playerCounter] = Row[5];
                i_playerTeam[i_playerCounter] = UTIL_ToUInt32(Row[7]);
                i_playerColour[i_playerCounter] = UTIL_ToUInt32(Row[8]);
                
                // ban check
                MYSQL_RES *BanResult = QueryBuilder(Connection, "SELECT id FROM oh_bans WHERE name='"+s_lowerPlayerName[i_playerCounter]+"' OR ip='"+s_playerIp[i_playerCounter]+"';" );
                if( BanResult ) {
                    vector<string> Row = MySQLFetchRow( BanResult );
                    if(Row.size( ) != 0 ) {
                        b_bannedPlayer[i_playerCounter] = true;
                    }
                }
                
                // user level check
                MYSQL_RES *UserLevelResult = QueryBuilder(Connection, "SELECT user_level FROM oh_users WHERE bnet_username='"+s_lowerPlayerName[i_playerCounter]+"';" );
                if( UserLevelResult ) {
                    vector<string> Row = MySQLFetchRow( UserLevelResult );
                    if(Row.size( ) != 0 ) {
                        i_userLevel[i_playerCounter] = UTIL_ToUInt32(Row[0]);
                    }
                }
                
                //check for leaver
                if( ( i_gameDuration - i_playerLeft[i_playerCounter] > 300 && i_gameDuration >= 1000 ) || ( i_gameDuration<5 && i_gameDuration - i_playerLeft[i_playerCounter] > 5 ) ) {
                    b_leaver[i_playerCounter] = true;
                }
                
                //define the alias type
                MYSQL_RES *AliasResult = QueryBuilder(Connection, "SELECT alias_name FROM oh_aliases WHERE alias_id='"+Alias+"';" );
                if( AliasResult ) {
                    vector<string> Row = MySQLFetchRow( AliasResult );
                    if(Row.size( ) != 0 ) {
                         s_gameAliasName = Row[0];
                    }
                }
                std::transform( s_gameAliasName.begin(), s_gameAliasName.end(), s_gameAliasName.begin(), ::tolower);
                
                /**
                 * Dota Specific games should be filtered here
                 */
                if( ( i_gameAlias != 0 &&! s_gameAliasName.empty( ) ) && ( s_gameAliasName.find("dota")!=string::npos || s_gameAliasName.find("lod")!=string::npos || s_gameAliasName.find("imba")!=string::npos ) ) {
                    //Select Winner
                    uint32_t i_Winner = 0;
                    
                    MYSQL_RES *DotAGameResult = QueryBuilder(Connection, "SELECT winner FROM oh_dotagames WHERE gameid="+GameID);
                    if( DotAGameResult ) {
                        vector<string> Row = MySQLFetchRow( DotAGameResult );
                        if(Row.size( ) != 0 ) {
                             i_Winner = UTIL_ToUInt32(Row[0]);
                        }
                    }
                    
                    // avoid the update process of wrong games
                    if( i_playerCounter >= 11 )
                    {
                            CONSOLE_Print( "GameID ["+GameID+"] has more than 10 players and was marked as ["+s_gameAliasName+"]. Ignoring." );
                            ErrorLog.push_back( "["+GameID+"] has more than 10 players and was marked as ["+s_gameAliasName+"]. Ignoring.");
                            SkippedGames++;
                            b_ignored = true;
                            break;
                    }
                    if( i_Winner <= 0 && i_Winner >= 2 )
                    {
                            CONSOLE_Print( "GameID ["+GameID+"] has an invalid winner, it was marked as ["+s_gameAliasName+"]. Ignoring." );
                            ErrorLog.push_back( "["+GameID+"] has an invalid winner, it was marked as ["+s_gameAliasName+"]. Ignoring.");
                            SkippedGames++;
                            b_ignored = true;
                            break;
                    }
                    
                    // more information about the player
                    MYSQL_RES *DotAPlayerResult = QueryBuilder(Connection, "SELECT kills, deaths, assists, creepkills, creepdenies, neutralkills, towerkills, raxkills FROM oh_dotaplayers WHERE gameid='"+GameID+"' AND newcolour='"+UTIL_ToString(i_playerColour[i_playerCounter])+"';" );
                    if(DotAPlayerResult) {
                        vector<string> Row = MySQLFetchRow( DotAPlayerResult );
                        if( Row.size( ) == 8 ) {
                            i_ingameKills[i_playerCounter]=UTIL_ToUInt32(Row[0]);
                            i_ingameDeaths[i_playerCounter]=UTIL_ToUInt32(Row[1]);
                            i_ingameAssists[i_playerCounter]=UTIL_ToUInt32(Row[2]);
                            i_ingameCreeps[i_playerCounter]=UTIL_ToUInt32(Row[3]);
                            i_ingameDenies[i_playerCounter]=UTIL_ToUInt32(Row[4]);
                            i_ingameNeutrals[i_playerCounter]=UTIL_ToUInt32(Row[5]);
                            i_ingameTower[i_playerCounter]=UTIL_ToUInt32(Row[6]);
                            i_ingameRaxes[i_playerCounter]=UTIL_ToUInt32(Row[7]);
                        }
                    }
                    if(i_ingameDeaths[i_playerCounter]==0) {
                        b_zerodeaths[i_playerCounter]=true;
                    }
                    
                    if( ( i_Winner == 1 && i_playerColour[i_playerCounter] >= 1 && i_playerColour[i_playerCounter] <= 5 ) || ( i_Winner == 2 && i_playerColour[i_playerCounter] >= 7 && i_playerColour[i_playerCounter] <= 11 ) ) {
                        s_playerScore[i_playerCounter]="score = score+" + Int32_ToString( ScoreWin)  + ",";
                        i_newPlayerScore[i_playerCounter]=ScoreStart+ScoreWin;
                        i_wins[i_playerCounter]=1;
                    } 
                    
                    else if( ( i_Winner == 2 && i_playerColour[i_playerCounter] >= 1 && i_playerColour[i_playerCounter] <= 5 ) || ( i_Winner == 1 && i_playerColour[i_playerCounter] >= 7 && i_playerColour[i_playerCounter] <= 11 ) ) {
                        s_playerScore[i_playerCounter]="score = score-" + Int32_ToString( ScoreLoose) + ",";
                        i_newPlayerScore[i_playerCounter]=ScoreStart-ScoreLoose;
                        i_losses[i_playerCounter]=1;
                    } 
                    //Draw Game
                    else {
                        i_draws[i_playerCounter]=1;
                        i_newPlayerScore[i_playerCounter]=ScoreStart;
                    }
                } 
                
                /**
                 * LEGION TD GAMES
                 */
                else if( ( i_gameAlias != 0 &&! s_gameAliasName.empty( ) ) && ( s_gameAliasName.find("legion")!=string::npos ) ) {
                    //select winner & pid flag
                    uint32_t i_legionTDPID=0;
                    string s_Winner="";
                    // make sure the winner is set in any case, the autoend sometimes bugging here so the left players probably dont set the winner flag!
                    // normally this is good but it does fail on the statspage then.
                    if(! b_w3mmdfixedwinner ) {
                        MYSQL_RES *LegionTDFixWinner1 = QueryBuilder(Connection, "SELECT pid, flag FROM oh_w3mmdplayers WHERE gameid='"+GameID+"' AND flag != '' AND flag != 'NULL'" );
                        if( LegionTDFixWinner1 ) {
                            vector<string> Row = MySQLFetchRow( LegionTDPlayerResult );
                        if( Row.size( ) == 2 ) {
                            string Team1QueryCondition = UTIL_ToUInt32(Row[0]) < 4 ? " pid <= 4" : " pid > 4 ";
                            string Team2QueryCondition = UTIL_ToUInt32(Row[0]) < 4 ? " pid >= 4" : " pid < 4 ";
                            string Team1 = Row[1];
                            string Team2 = "drawer";
                            if( Team1 == "winner")
                                Team2="loser";
                            else if( Team1=="loser")
                                Team2="winner";
                            
                            MYSQL_RES *LegionTDFixTeam1 = QueryBuilder(Connection, "UPDATE oh_w3mmdplayers SET flag='"+Team1+"' WHERE gameid='"+GameID+"' AND "+Team1QueryCondition+";" );
                            MYSQL_RES *LegionTDFixTeam2 = QueryBuilder(Connection, "UPDATE oh_w3mmdplayers SET flag='"+Team2+"' WHERE gameid='"+GameID+"' AND "+Team2QueryCondition+";" );
                           b_w3mmdfixedwinner=true; 
                        }
                    }
                    MYSQL_RES *LegionTDPlayerResult= QueryBuilder(Connection, "SELECT pid, flag FROM oh_w3mmdplayers WHERE gameid='"+GameID+"' AND name='"+s_lowerPlayerName[i_playerCounter]+"';" );
                    if(LegionTDPlayerResult) {
                        vector<string> Row = MySQLFetchRow( LegionTDPlayerResult );
                        if( Row.size( ) == 2 ) {
                            i_legionTDPID = UTIL_ToUInt32(Row[0]);
                            s_Winner=Row[1];
                        }
                    }
                    
                    //select flags for LegionTD
                    MYSQL_RES *LegionTDPlayerFlagsResult = QueryBuilder(Connection, "SELECT `value_int`, varname FROM oh_w3mmdvars WHERE `pid` = '"+UTIL_ToString(i_legionTDPID)+"' AND value_int != 'NULL' AND `gameid`= '"+GameID+"' ORDER BY id DESC LIMIT 10;" );
                    if( LegionTDPlayerFlagsResult ) {
                        vector<string> Row = MySQLFetchRow( LegionTDPlayerFlagsResult );
                        
                        while( !Row.empty( ) )
                        {
                            if(Row[1]=="value")
                                legionTdValue[i_playerCounter]=UTIL_ToUInt32(Row[0]);
                            else if(Row[1]=="seconds")
                                legionTdSeconds[i_playerCounter]=UTIL_ToUInt32(Row[0]);
                            else if(Row[1]=="score")
                                legionTdScore[i_playerCounter]=UTIL_ToUInt32(Row[0]);
                            else if(Row[1]=="lumberjack")
                                legionTdWood[i_playerCounter]=UTIL_ToUInt32(Row[0]);
                            else if(Row[1]=="lumber_total")
                                legionTdWoodTotal[i_playerCounter]=UTIL_ToUInt32(Row[0]);
                            else if(Row[1]=="level")
                                legionTdLevel[i_playerCounter]=UTIL_ToUInt32(Row[0]);
                            else if(Row[1]=="leaked")
                                legionTdLeaked[i_playerCounter]=UTIL_ToUInt32(Row[0]);
                            else if(Row[1]=="income")
                                legionTdIncome[i_playerCounter]=UTIL_ToUInt32(Row[0]);
                            else if(Row[1]=="gold_total")
                                legionTdGoldTotal[i_playerCounter]=UTIL_ToUInt32(Row[0]);
                            else if(Row[1]=="gold_income")
                                legionTdGoldIncome[i_playerCounter]=UTIL_ToUInt32(Row[0]);
                                
                            Row = MySQLFetchRow( LegionTDPlayerFlagsResult );
                        }
                        mysql_free_result( LegionTDPlayerFlagsResult );
                    }
                    MYSQL_RES *LegionTDPlayerFlagsStringResult = QueryBuilder(Connection, "SELECT `value_string`, varname FROM oh_w3mmdvars WHERE  `pid` =  '"+UTIL_ToString(i_legionTDPID)+"' AND value_string != 'NULL' AND `gameid` = '"+GameID+"' ORDER BY id DESC LIMIT 3;" );
                    if( LegionTDPlayerFlagsResult ) {
                        vector<string> Row = MySQLFetchRow( LegionTDPlayerFlagsStringResult );
                        
                        while( !Row.empty( ) )
                        {
                            if(Row[1]=="race")
                                legionTdRace[i_playerCounter]=Row[0];
                            else if(Row[1]=="name")
                                legionTdName[i_playerCounter]=Row[0];
                            else if(Row[1]=="game_mode")
                                legionTdGameMode[i_playerCounter]=Row[0];
                            
                            Row = MySQLFetchRow( LegionTDPlayerFlagsStringResult );
                        }
                        mysql_free_result( LegionTDPlayerFlagsStringResult );
                    }

                    // Winner
                    if( s_Winner == "winner" ) {
                        s_playerScore[i_playerCounter]="score = score+" + Int32_ToString( ScoreWin) + ",";
                        i_newPlayerScore[i_playerCounter] = ScoreStart+ScoreWin;
                        i_wins[i_playerCounter]=1;
                    } 
                    // Looser
                    else if( s_Winner == "loser" ) {
                        s_playerScore[i_playerCounter]="score = score-" + Int32_ToString( ScoreLoose) + ",";
                        i_newPlayerScore[i_playerCounter]=ScoreStart-ScoreLoose;
                        i_losses[i_playerCounter]=1;
                    } 
                    //Draw Game
                    else if( s_Winner == "drawer" || s_Winner.empty() ) {
                        s_playerScore[i_playerCounter]="";
                        i_newPlayerScore[i_playerCounter]=ScoreStart;
                        i_draws[i_playerCounter]=1;
                    }
                        
                }
                
                //check if the player had bet for this game
                //check the current maxstreaks and determine if they should be set again
                int32_t i_currentPoints=0;
                uint32_t i_pointsBet=0;
                uint32_t i_currStreak=0;
                uint32_t i_maxStreak=0;
                uint32_t i_currLoosStreak=0;
                uint32_t i_maxLoosingStreak=0;
                if(! b_newPlayer[i_playerCounter] ) {
                    MYSQL_RES *DetailedStatsQuery = QueryBuilder(Connection, "SELECT points, points_bet, streak, maxstreak, losingstreak, maxlosingstreak FROM oh_stats WHERE id='"+UTIL_ToString(i_playerId[i_playerCounter])+"';" );
                    if( DetailedStatsQuery ) {
                        vector<string> Row = MySQLFetchRow( DetailedStatsQuery );
                        if( Row.size( ) == 6 ) {
                            i_currentPoints=UTIL_ToInt32(Row[0]);
                            i_pointsBet=UTIL_ToUInt32(Row[1]);
                            i_currStreak=UTIL_ToUInt32(Row[2]);
                            i_maxStreak=UTIL_ToUInt32(Row[3]);
                            i_currLoosStreak=UTIL_ToUInt32(Row[4]);
                            i_maxLoosingStreak=UTIL_ToUInt32(Row[5]);
                        }
                    }
                    
                }
                if( i_wins[i_playerCounter] == 1 ) {
                    s_playerWinStreak[i_playerCounter]="streak = streak+1, ";
                    s_playerLooseStreak[i_playerCounter]="losingstreak = 0, ";
                    i_winStreak[i_playerCounter]=i_currStreak+1;
                    i_looseStreak[i_playerCounter]=0;
                    if(i_currentPoints != 0 ) {
                        i_winPoints[i_playerCounter] = i_currentPoints+i_currentPoints*2;
                    }
                    if(i_winStreak[i_playerCounter] >= i_maxStreak ) {
                        i_maxWinStreak[i_playerCounter]=i_winStreak[i_playerCounter];
                    }
                } else if( i_losses[i_playerCounter] == 1 ) {
                    s_playerWinStreak[i_playerCounter]="streak = 0, ";
                    s_playerLooseStreak[i_playerCounter]="losingstreak = losingstreak+1, ";
                    i_looseStreak[i_playerCounter]=i_currLoosStreak+1;
                    i_winStreak[i_playerCounter]=0;
                    if(i_currentPoints != 0 ) {
                        i_winPoints[i_playerCounter] = i_currentPoints-i_currentPoints*4;
                    } else {
                        i_winPoints[i_playerCounter]=0;
                    }
                    if(i_looseStreak[i_playerCounter] >= i_maxLoosingStreak ) {
                        i_maxLooseStreak[i_playerCounter]=i_looseStreak[i_playerCounter];
                    }
                } else {
                    i_looseStreak[i_playerCounter]=i_currLoosStreak;
                    i_winStreak[i_playerCounter]=i_currStreak;
                    i_maxLooseStreak[i_playerCounter]=i_maxLoosingStreak;
                    i_maxWinStreak[i_playerCounter]=i_maxStreak;
                    if(i_currentPoints != 0 ) {
                        i_winPoints[i_playerCounter] = i_currentPoints;
                    } else {
                        i_winPoints[i_playerCounter]=5;
                    }
                }
                        
                i_playerCounter++;
                Row = MySQLFetchRow( BasicResult );
            }
        
             mysql_free_result( BasicResult );

            if( !b_ignored ) {
                    if( i_playerCounter == 0 ) {
                            CONSOLE_Print( "GameID ["+GameID+"] has no players. Ignoring this game." );
                            ErrorLog.push_back( "["+GameID+"] has no players. Ignoring this game." );
                            SkippedGames++;
                    }
                    else
                    {
                            for( int i = 0; i < i_playerCounter; i++ )
                            {
                                    if(! b_newPlayer[i] ) {
                                        string UpdateString = "UPDATE `oh_stats` SET last_seen=CURRENT_TIMESTAMP(), points_bet = '0', points=points"+( ( Int32_ToString( i_winPoints[i] ).substr( 0, 1 ) == "-" ) ?  Int32_ToString( i_winPoints[i] ) : "+"+Int32_ToString( i_winPoints[i] ) )+", leaver = leaver+'"+(b_leaver[i] ? "1" : "0" )+"', banned = '"+ ( b_bannedPlayer[i] ?  "1" : "0") +"', user_level = '"+UTIL_ToString(i_userLevel[i])+"', maxlosingstreak = '" + UTIL_ToString( i_maxLooseStreak[i] ) + "', maxstreak = '" + UTIL_ToString( i_maxWinStreak[i] ) + "', streak='"+ UTIL_ToString( i_looseStreak[i] ) +"', losingstreak='" + UTIL_ToString( i_winStreak[i] ) +"', wins = wins+" + UTIL_ToString( i_wins[i] ) + ", losses = losses+" + UTIL_ToString( i_losses[i] ) + ", draw = draw+" + UTIL_ToString( i_draws[i] ) + ", games= games+1, ip= '" + s_playerIp[i] + "' ";
                                        if( ( i_gameAlias != 0 &&! s_gameAliasName.empty( ) ) && ( s_gameAliasName.find("dota")!=string::npos || s_gameAliasName.find("lod")!=string::npos || s_gameAliasName.find("imba")!=string::npos ) )
                                            UpdateString +=  ", "+ s_playerScore[i] +" zerodeaths = zerodeaths+"+ ( b_zerodeaths[i] ? "1" : "0") +", kills=kills+"+UTIL_ToString(i_ingameKills[i])+", deaths=deaths+" + UTIL_ToString( i_ingameDeaths[i] ) + ", assists=assists+" + UTIL_ToString( i_ingameAssists[i] ) + ", creeps=creeps+" + UTIL_ToString( i_ingameCreeps[i] ) + ", denies=denies+" + UTIL_ToString( i_ingameDenies[i] ) + ", neutrals=neutrals+" + UTIL_ToString( i_ingameNeutrals[i] ) + ", towers=towers+" + UTIL_ToString( i_ingameTower[i] ) + ", rax=rax+" + UTIL_ToString( i_ingameRaxes[i] );
                                        else if( ( i_gameAlias != 0 &&! s_gameAliasName.empty( ) ) && ( s_gameAliasName.find("legion")!=string::npos ) )
                                            UpdateString += ", "+ s_playerScore[i] +" kills=kills+"+UTIL_ToString(legionTdValue[i])+", deaths=deaths+"+UTIL_ToString(legionTdSeconds[i])+", assists=assists+"+UTIL_ToString(legionTdWood[i])+", creeps=creeps+"+UTIL_ToString(legionTdWoodTotal[i])+", denies=denies+"+UTIL_ToString(legionTdLeaked[i])+", neutrals=neutrals+"+UTIL_ToString(legionTdIncome[i])+", towers=towers+"+UTIL_ToString(legionTdGoldTotal[i])+", rax=rax+"+UTIL_ToString(legionTdGoldIncome[i]);

                                        UpdateString += " WHERE id='" + UTIL_ToString( i_playerId[i] )+"';";
                                        MYSQL_RES *PlayerUpdateResult = QueryBuilder(Connection, UpdateString );
                                    }
                                    else
                                    {
                                            string EscName = MySQLEscapeString( Connection, s_playerName[i] );
                                            string EscLName = MySQLEscapeString( Connection, s_lowerPlayerName[i] );
                                            string EscServer = MySQLEscapeString( Connection, s_spoofedRealm[i] );
                                            string InsertQuery = "INSERT INTO `oh_stats` ( month, year, alias_id, last_seen, player, player_lower, banned, realm, ip, score, games, kills, deaths, assists, creeps, denies, neutrals, towers, rax, wins, losses, draw, streak, maxstreak, losingstreak, maxlosingstreak, leaver, points ) VALUES ("+Month+", "+Year+","+UTIL_ToString(i_gameAlias)+", CURRENT_TIMESTAMP(), '" + EscName + "', '" + EscLName + "', '" + ( b_bannedPlayer[i] ? "1" : "0") + "', '" + EscServer + "', '" + s_playerIp[i] + "', "+( Int32_ToString( i_newPlayerScore[i] ) )+", 1, ";
                                            if( ( i_gameAlias != 0 &&! s_gameAliasName.empty( ) ) && ( s_gameAliasName.find("dota")!=string::npos || s_gameAliasName.find("lod")!=string::npos || s_gameAliasName.find("imba")!=string::npos ) )
                                                InsertQuery += UTIL_ToString( i_ingameKills[i] )+", " + UTIL_ToString( i_ingameDeaths[i] ) + ", "+ UTIL_ToString( i_ingameAssists[i] ) + ", " + UTIL_ToString( i_ingameCreeps[i] ) + ", "+ UTIL_ToString( i_ingameDenies[i] ) + ", " + UTIL_ToString( i_ingameNeutrals[i] ) + ", " + UTIL_ToString( i_ingameTower[i] ) + ", " + UTIL_ToString( i_ingameRaxes[i] );
                                            else if( ( i_gameAlias != 0 &&! s_gameAliasName.empty( ) ) && ( s_gameAliasName.find("legion")!=string::npos ) )
                                                InsertQuery += UTIL_ToString(legionTdValue[i])+", "+UTIL_ToString(legionTdSeconds[i])+", "+UTIL_ToString(legionTdWood[i])+", "+UTIL_ToString(legionTdWoodTotal[i])+", +"+UTIL_ToString(legionTdLeaked[i])+", +"+UTIL_ToString(legionTdIncome[i])+", "+UTIL_ToString(legionTdGoldTotal[i])+", "+UTIL_ToString(legionTdGoldIncome[i]);
                                            else {
                                                InsertQuery += "0, 0, 0, 0, 0, 0, 0, 0";
                                            }
                                            InsertQuery +=  ", "+UTIL_ToString( i_wins[i]) + ", " + UTIL_ToString( i_losses[i]) + ", " + UTIL_ToString( i_draws[i]) + ", " + UTIL_ToString( i_winStreak[i]) + ", " + UTIL_ToString( i_maxWinStreak[i]) + ", " + UTIL_ToString( i_looseStreak[i]) + ", " + UTIL_ToString( i_maxLooseStreak[i]) + ", " + ( b_leaver[i] ?  "1" : "0") + ", " + Int32_ToString( i_winPoints[i]) + " )";

                                            MYSQL_RES *PlayerInsertResult = QueryBuilder(Connection, InsertQuery);
                                    }
                            }
                    }
            }
            else
            {
                    Print_Error(mysql_error( Connection ) );
                    return 1;
            }

            MYSQL_RES *UpdateResult = QueryBuilder(Connection, "UPDATE `oh_games` SET `stats` = '1' WHERE `id` = " + GameID + ";" );
            if( UpdateResult )
                    CONSOLE_Print( "Successfully updated players from GameID ["+GameID+"]" );
        }
    }
    
    if(updatedstats)
            CONSOLE_Print( "Committing transaction..." );

    MYSQL_RES *CommitResult = QueryBuilder(Connection, "COMMIT" );
    CONSOLE_Print( "Transaction done. Closing connection." );
    uint32_t EndTicks = GetTicks();
    CONSOLE_Print( "Statistic: Updated ["+UTIL_ToString(GameAmount)+"], skipped ["+UTIL_ToString(SkippedGames)+"] games, in ["+UTIL_ToString(EndTicks-StartTicks)+"] ms.");
    if(! ErrorLog.empty())
    {
        CONSOLE_Print( "Unupdated games, error log:");
        for( vector<string> :: iterator i = ErrorLog.begin( ); i != ErrorLog.end( ); ++i )
            CONSOLE_Print( "[ErrorLog]"+*i);
    }
 updateLock.unlock( );
    return 0;
}
