/*

   Copyright [2013] [OHsystem]

       OHSystem-Bot is free software: you can redistribute it and/or modify
       it under the terms of the GNU General Public License as published by
       the Free Software Foundation, either version 3 of the License, or
       (at your option) any later version.

   This is modified from GHOST++: http://ghostplusplus.googlecode.com/

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
    MYSQL_RES *GameResult = QueryBuilder(Connection, "SELECT `id`, MONTH(`datetime`), YEAR(`datetime`) FROM `oh_games` WHERE map LIKE `dota` AND `stats` = '0' AND `gamestatus` = '1' ORDER BY id LIMIT "+UTIL_ToString( StatsUpdateLimit )+";" );
    if( GameResult )
    {
            vector<string> Row = MySQLFetchRow( GameResult );
            while( !Row.empty( ) )
            {
                    UnscoredGames.push( Row[0]+" "+Row[1]+" "+Row[2] );
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
        stringstream SS;
        SS << Data;
        SS >> GameID;
        SS >> Month;
        SS >> Year;
        CONSOLE_Print( Data + " " + GameID + " " + Month + " "+ Year);
        MYSQL_RES *Result = QueryBuilder(Connection, "SELECT s.id, gp.name, dp.kills, dp.deaths, dp.assists, dp.creepkills, dp.creepdenies, dp.neutralkills, dp.towerkills, dp.raxkills, gp.spoofedrealm,gp.reserved, gp.left, gp.ip, g.duration, dg.winner, dp.newcolour, gp.team, s.streak, s.maxstreak, s.losingstreak, s.maxlosingstreak, s.points_bet, s.ingame_role, s.kills, s.deaths, s.assists, s.creeps, s.denies, s.neutrals, s.games FROM oh_gameplayers as gp LEFT JOIN oh_dotaplayers as dp ON gp.gameid=dp.gameid AND gp.colour=dp.newcolour LEFT JOIN oh_games as g on g.id=gp.gameid LEFT JOIN oh_stats as s ON gp.name = s.player_lower AND s.month="+Month+" AND s.year="+Year+" LEFT JOIN oh_dotagames as dg ON dg.gameid=gp.gameid WHERE gp.gameid = " + GameID );

        if( Result )
        {
                CONSOLE_Print( "Starting update for gameid ["+GameID+"]" );

                uint32_t id[11];
                bool ignore = false;
                string names[11];
                string servers[11];
                bool exists[11];
                int num_players = 0;
                string score[11];
                int64_t nscore[11];
                float team_ratings[2];
                float team_winners[2];
                int team_numplayers[2];
                team_ratings[0] = 0.0;
                team_ratings[1] = 0.0;
                team_numplayers[0] = 0;
                team_numplayers[1] = 0;
                string lnames[11];
                string ips[11];
                uint32_t reserved[11];
                uint32_t lt[11];
                uint32_t left[11];
                uint32_t team[11];
                uint32_t colour[11];
                uint32_t k[11];
                uint32_t d[11];
                uint32_t a[11];
                uint32_t c[11];
                uint32_t de[11];
                uint32_t n[11];
                uint32_t t[11];
                uint32_t r[11];
                string streak[11];
                uint32_t maxstreak[11];
                string lstreak[11];
                uint32_t maxlstreak[11];
                uint32_t zd[11];
                uint32_t bp[1];
                uint32_t win[11];
                uint32_t losses[11];
                uint32_t draw[11];
                uint32_t nstreak[11];
                uint32_t nlstreak[11];
                uint32_t banned[11];
                uint32_t leaver[11];
                uint32_t npoints[11];
                string points[11];
                uint32_t ingame_role[11];

                vector<string> Row = MySQLFetchRow( Result );

                while( Row.size( ) == 31 )
                {
                        if( num_players >= 11 )
                        {
                                CONSOLE_Print( "GameID ["+GameID+"] has more than 10 players. Ignoring this game." );
                                ErrorLog.push_back( "["+GameID+"] has more than 10 players. Ignoring this game.");
                                SkippedGames++;
                                ignore = true;
                                break;
                        }

                        uint32_t Winner = UTIL_ToUInt32( Row[15] );

                        if( Winner != 1 && Winner != 2 && Winner != 0)
                        {
                                CONSOLE_Print( "GameID ["+GameID+"] is not a two team map. Ignoring this game." );
                                ErrorLog.push_back( "["+GameID+"] is not a two team map. Ignoring this game.");
                                SkippedGames++;
                                ignore = true;
                                break;
                        }
                        else if( Winner == 1 )
                        {
                                team_winners[0] = 1.0;
                                team_winners[1] = 0.0;
                        }
                        else
                        {
                                team_winners[0] = 0.0;
                                team_winners[1] = 1.0;
                        }

                        names[num_players] = Row[1];
                        string name = Row[1];
                        std::transform( name.begin(), name.end(), name.begin(), ::tolower);
                        lnames[num_players] = name;
                        servers[num_players] = Row[10];
                        score[num_players] = "";
                        nscore[num_players] = 0;
                        banned[num_players] = 0;

                        MYSQL_RES *BanResult = QueryBuilder(Connection, "SELECT name FROM `oh_bans` WHERE name = '" + Row[1] + "';" );
                        if( BanResult )
                        {
                               vector<string> Row = MySQLFetchRow( BanResult );
                               if( Row.size( ) == 1 )
                                       banned[num_players] = 1;
                        }
                        if( !Row[0].empty( ) )
                                exists[num_players] = true;
                        else
                        {
                                CONSOLE_Print( "Unscored Player ["+Row[1]+"] found." );
                                exists[num_players] = false;
                        }
                        id[num_players] = UTIL_ToUInt32( Row[0] );

                        uint32_t Colour = UTIL_ToUInt32( Row[16] );
                        npoints[num_players] = 0;
                        points[num_players] = "";
                        if( Colour >= 1 && Colour <= 5 )
                        {
                                team_numplayers[0]++;
                                if( Winner == 1 )
                                {
                                        score[num_players] = "score = score+" + Int32_ToString( ScoreWin+(UTIL_ToUInt32(Row[18])*StreakBonus) ) + ",";
                                        nscore[num_players] = ScoreStart+ScoreWin;
                                        win[num_players] = 1;
                                        streak[num_players] = "streak = streak+1, ";
                                        lstreak[num_players] = "losingstreak = 0, ";
                                        nstreak[num_players] = 1;
                                        nlstreak[num_players] = 0;
                                        losses[num_players] = 0;
                                        draw[num_players] = 0;
                                        npoints[num_players] = 10;
                                        if( Row[22] != "0" )
                                                points[num_players] = "+" + UTIL_ToString( UTIL_ToInt32( Row[22] )*0.5, 2 );
                                        else
                                                points[num_players] = "+10";

                                        if( !Row[19].empty() && !Row[18].empty() && !Row[21].empty() )
                                        {
                                                if( UTIL_ToUInt32( Row[19] ) < UTIL_ToUInt32( Row[18] )+1 )
                                                        maxstreak[num_players] = UTIL_ToUInt32( Row[19] )+1;
                                                else
                                                        maxstreak[num_players] = UTIL_ToUInt32( Row[19] );

                                                maxlstreak[num_players] = UTIL_ToUInt32( Row[21] );
                                        }
                                        else
                                        {
                                                maxstreak[num_players] = 1;
                                                maxlstreak[num_players] = 0;
                                        }
                                }
                                else if( Winner == 2 )
                                {
                                        score[num_players] = "score = score-" + Int32_ToString( ScoreLoose+(UTIL_ToUInt32(Row[20])*StreakBonus) ) + ",";
                                        nscore[num_players] = ScoreStart-ScoreLoose;
                                        losses[num_players] = 1;
                                        lstreak[num_players] = "losingstreak = losingstreak+1, ";
                                        streak[num_players] = "streak = 0, ";
                                        nstreak[num_players] = 0;
                                        nlstreak[num_players] = 1;
                                        win[num_players] = 0;
                                        draw[num_players] = 0;
                                        if( Row[22] != "0" )
                                                 points[num_players] = "-" + Row[22];

                                        if( !Row[21].empty() && !Row[20].empty() && !Row[19].empty() )
                                        {
                                                if( UTIL_ToUInt32( Row[21] ) < UTIL_ToUInt32( Row[20] )+1 )
                                                        maxlstreak[num_players] = UTIL_ToUInt32( Row[21] )+1;
                                                else
                                                        maxlstreak[num_players] = UTIL_ToUInt32( Row[21] );

                                                maxstreak[num_players] = UTIL_ToUInt32( Row[19] );
                                        }
                                        else
                                        {
                                                maxlstreak[num_players] = 1;
                                                maxstreak[num_players] = 0;
                                        }
                                }
                                else
                                {
                                        draw[num_players] = 1;
                                        losses[num_players] = 0;
                                        win[num_players] = 0;
                                        nscore[num_players] = ScoreStart;
                                        nstreak[num_players] = 0;
                                        nlstreak[num_players] = 0;
                                        streak[num_players] = "";
                                        lstreak[num_players] = "";
                                        if( !Row[18].empty() && !Row[21].empty() )
                                        {
                                                maxstreak[num_players] = UTIL_ToUInt32( Row[19] );
                                                maxlstreak[num_players] = UTIL_ToUInt32( Row[21] );
                                        }
                                        else
                                        {
                                                maxstreak[num_players] = 0;
                                                maxlstreak[num_players] = 0;
                                        }
                                }
                        }
                        else if( Colour >= 7 && Colour <= 11 )
                        {
                                team_numplayers[1]++;
                                if( Winner == 2 )
                                {
                                        score[num_players] = "score = score+" + Int32_ToString( ScoreWin+(UTIL_ToUInt32(Row[18])*StreakBonus) ) + ",";
                                        nscore[num_players] = ScoreStart+ScoreWin;
                                        win[num_players] = 1;
                                        streak[num_players] = "streak = streak+1, ";
                                        lstreak[num_players] = "losingstreak = 0, ";
                                        losses[num_players] = 0;
                                        draw[num_players] = 0;
                                        nstreak[num_players] = 1;
                                        nlstreak[num_players] = 0;
                                        npoints[num_players] = 10;
                                        if( Row[22] != "0" )
                                                points[num_players] = "+" + UTIL_ToString( UTIL_ToInt32( Row[22] ) *0.5, 2 );
                                        else
                                                points[num_players] = "+10";

                                        if( !Row[19].empty() && !Row[18].empty() && !Row[21].empty() )
                                        {
                                                if( UTIL_ToUInt32( Row[19] ) < UTIL_ToUInt32( Row[18] )+1 )
                                                        maxstreak[num_players] = UTIL_ToUInt32( Row[19] )+1;
                                                else
                                                        maxstreak[num_players] = UTIL_ToUInt32( Row[19] );

                                                maxlstreak[num_players] = UTIL_ToUInt32( Row[21] );
                                        }
                                        else
                                        {
                                                maxstreak[num_players] = 1;
                                                maxlstreak[num_players] = 0;
                                        }
                                }
                                else if( Winner == 1 )
                                {
                                        score[num_players] = "score = score-" + Int32_ToString( ScoreLoose+(UTIL_ToUInt32(Row[20])*StreakBonus) ) + ",";
                                        nscore[num_players] = ScoreStart-ScoreLoose;
                                        losses[num_players] = 1;
                                        lstreak[num_players] = "losingstreak = losingstreak+1, ";
                                        streak[num_players] = "streak = 0, ";
                                        win[num_players] = 0;
                                        draw[num_players] = 0;
                                        nstreak[num_players] = 0;
                                        nlstreak[num_players] = 1;
                                        if( Row[22] != "0" )
                                               points[num_players] = "-" + Row[22];

                                        if( !Row[21].empty() && !Row[20].empty() && !Row[19].empty() )
                                        {
                                                if( UTIL_ToUInt32( Row[21] ) < UTIL_ToUInt32( Row[21] )+1 )
                                                        maxlstreak[num_players] = UTIL_ToUInt32( Row[21] )+1;
                                                else
                                                        maxlstreak[num_players] = UTIL_ToUInt32( Row[21] );

                                                maxstreak[num_players] = UTIL_ToUInt32( Row[19] );
                                        }
                                        else
                                        {
                                                maxstreak[num_players] = 0;
                                                maxlstreak[num_players] = 1;
                                        }
                                }
                                else
                                {
                                        draw[num_players] = 1;
                                        losses[num_players] = 0;
                                        win[num_players] = 0;
                                        nscore[num_players] = ScoreStart;
                                        nstreak[num_players] = 0;
                                        nlstreak[num_players] = 0;
                                        streak[num_players] = "";
                                        lstreak[num_players] = "";
                                        if( !Row[19].empty() && !Row[21].empty() )
                                        {
                                                maxstreak[num_players] = UTIL_ToUInt32( Row[19] );
                                                maxlstreak[num_players] = UTIL_ToUInt32( Row[21] );
                                        }
                                        else
                                        {
                                                maxstreak[num_players] = 0;
                                                maxlstreak[num_players] = 0;
                                        }
                                }
                        }
                        //if a player got a connection error his stats arent safed properly, there is an issue that his newcolour gets automatically set to 0
                        else if( !Row[16].empty( ) )  
                        {
                                CONSOLE_Print( "GameID ["+GameID+"] has a player with an invalid newcolour. Ignoring this Game." );
                                ErrorLog.push_back( "["+GameID+"] has a player with an invalid newcolour: ["+Row[16]+"]. Ignoring this Game.");
                                SkippedGames++;
                                ignore = true;
                                break;
                        }

                        ips[num_players] = Row[13];
                        reserved[num_players] = UTIL_ToUInt32( Row[11] );
                        left[num_players] = UTIL_ToUInt32( Row[12] );
                        team[num_players] = UTIL_ToUInt32( Row[17] );
                        colour[num_players] = UTIL_ToUInt32( Row[16] );
                        k[num_players] = UTIL_ToUInt32( Row[2] );
                        d[num_players] = UTIL_ToUInt32( Row[3] );
                        if( UTIL_ToUInt32( Row[3] ) == 0 )
                                zd[num_players] = 1;
                        else
                                zd[num_players] = 0;

                        a[num_players] = UTIL_ToUInt32( Row[4] );
                        c[num_players] = UTIL_ToUInt32( Row[5] );
                        de[num_players] = UTIL_ToUInt32( Row[6] );
                        n[num_players] = UTIL_ToUInt32( Row[7] );
                        t[num_players] = UTIL_ToUInt32( Row[8] );
                        r[num_players] = UTIL_ToUInt32( Row[9] );


                        // Leaver detect
                        if( UTIL_ToUInt32( Row[12] ) < ( UTIL_ToUInt32( Row[14] )-500 ) )
                                leaver[num_players] = 1;
                        else
                                leaver[num_players] = 0;
                        
                        /**
                         * calculation of the ingame role
                         * possible roles with their identify & calculation & ranking:
                         * 1: Assassin (  kills/games > 15 )
                         * 2: Jungler ( neutrals/games > 50 )
                         * 3: Supporter ( assists/games > 15 )
                         * 4: Observer ( observedgames/games > .5 )
                         * 5: Feeder ( deaths/games > 8 )
                         * 6: Enemies Assitant ( (kills/deaths) < 1 )
                         */
                        uint32_t currentRole = UTIL_ToUInt32( Row[23] );
                        uint32_t kills = UTIL_ToUInt32(Row[24])+k[num_players];
                        uint32_t deaths = UTIL_ToUInt32(Row[25])+d[num_players];
                        uint32_t assists = UTIL_ToUInt32(Row[26])+a[num_players];
                        uint32_t creeps = UTIL_ToUInt32(Row[27])+c[num_players];
                        uint32_t denies = UTIL_ToUInt32(Row[28])+de[num_players];
                        uint32_t neutrals = UTIL_ToUInt32(Row[29])+n[num_players];
                        uint32_t games = UTIL_ToUInt32(Row[30]);
                        uint32_t observedGames = 0;
                        
                        if( kills / games > 15 ) {
                            currentRole = 1; 
                        } else if( neutrals / games > 50 ) {
                            currentRole = 2;
                        } else if( assists / games > 15 ) {
                            currentRole = 3;
                        } else if( observedGames / games > .5 ) {
                            currentRole = 4;
                        } else if( deaths / games > 8 ) {
                            currentRole = 5;
                        } else if( kills / deaths < 1 ) {
                            currentRole = 6;
                        }
                        
                        ingame_role[num_players] = currentRole;
                        
                        num_players++;
                        Row = MySQLFetchRow( Result );
                }

                mysql_free_result( Result );

                if( !ignore )
                {
                        if( num_players == 0 ) {
                                CONSOLE_Print( "GameID ["+GameID+"] has no players. Ignoring this game." );
                                ErrorLog.push_back( "["+GameID+"] has no players. Ignoring this game." );
                                SkippedGames++;
                        }
                        else if( team_numplayers[0] == 0 ) {
                                CONSOLE_Print( "GameID ["+GameID+"] has no Sentinel players. Ignoring this game." );
                                ErrorLog.push_back( "["+GameID+"] has no Sentinel players. Ignoring this game." );
                                SkippedGames++;
                        }
                        else if( team_numplayers[1] == 0 ) {
                                CONSOLE_Print( "GameID ["+GameID+"] has no Scourge players. Ignoring this game." );
                                ErrorLog.push_back( "["+GameID+"] has no Scourge players. Ignoring this game." );
                                SkippedGames++;
                        }
                        else
                        {
                                //CONSOLE_Print( "GameID ["+GameID+"] is calculating..." );

                                for( int i = 0; i < num_players; i++ )
                                {
                                        //CONSOLE_Print( "Player ["+names[i]+"] New score: "+Int32_ToString( nscore[i] ) );

                                        if( exists[i] )
                                                MYSQL_RES *PlayerUpdateResult = QueryBuilder(Connection, "UPDATE `oh_stats` SET ingame_role='"+UTIL_ToString(ingame_role[i])+"', last_seen=CURRENT_TIMESTAMP(), points_bet = 0, points=points" + points[i] + ", leaver = leaver+"+UTIL_ToString(leaver[i])+", banned = "+ UTIL_ToString( banned[i] ) +", zerodeaths = zerodeaths+ "+ UTIL_ToString( zd[i] ) +", maxlosingstreak = " + UTIL_ToString( maxlstreak[i] ) + ", maxstreak = " + UTIL_ToString( maxstreak[i] ) + ", "+ lstreak[i] + streak[i] +" wins = wins+" + UTIL_ToString( win[i] ) + ", losses = losses+" + UTIL_ToString( losses[i] ) + ", draw = draw+" + UTIL_ToString( draw[i] ) + ", "+ score[i] +" games= games+1, kills=kills+" + UTIL_ToString( k[i] ) + ", deaths=deaths+" + UTIL_ToString( d[i] ) + ", assists=assists+" + UTIL_ToString( a[i] ) + ", creeps=creeps+" + UTIL_ToString( c[i] ) + ", denies=denies+" + UTIL_ToString( de[i] ) + ", neutrals=neutrals+" + UTIL_ToString( n[i] ) + ", towers=towers+" + UTIL_ToString( t[i] ) + ", rax=rax+" + UTIL_ToString( r[i] ) + ",  ip= '" + ips[i] + "' WHERE id=" + UTIL_ToString( id[i] ) );
                                        else
                                        {
                                                string EscName = MySQLEscapeString( Connection, names[i] );
                                                string EscLName = MySQLEscapeString( Connection, lnames[i] );
                                                string EscServer = MySQLEscapeString( Connection, servers[i] );
                                                MYSQL_RES *PlayrInsertResult = QueryBuilder(Connection, "INSERT INTO `oh_stats` ( month, year, last_seen, player, player_lower, banned, realm, ip, score, games, kills, deaths, assists, creeps, denies, neutrals, towers, rax, wins, losses, draw, streak, maxstreak, losingstreak, maxlosingstreak, zerodeaths, leaver, points, ingame_role ) VALUES ("+Month+", "+Year+", CURRENT_TIMESTAMP(), '" + EscName + "', '" + EscLName + "', '" + UTIL_ToString( banned[i] ) + "', '" + EscServer + "', '" + ips[i] + "', "+ Int32_ToString( nscore[i] ) +", 1, " + UTIL_ToString( k[i]) + ", " + UTIL_ToString( d[i]) + ", " + UTIL_ToString( a[i]) + ", " + UTIL_ToString( c[i]) + ", " + UTIL_ToString( de[i]) + ", " + UTIL_ToString( n[i]) + ", " + UTIL_ToString( t[i]) + ", " + UTIL_ToString( r[i]) + ", " + UTIL_ToString( win[i]) + ", " + UTIL_ToString( losses[i]) + ", " + UTIL_ToString( draw[i]) + ", " + UTIL_ToString( nstreak[i]) + ", " + UTIL_ToString( maxstreak[i]) + ", " + UTIL_ToString( nlstreak[i]) + ", " + UTIL_ToString( maxlstreak[i]) + ", " + UTIL_ToString( zd[i]) + ", " + UTIL_ToString( leaver[i]) + ", " + UTIL_ToString( npoints[i]) + ", '"+UTIL_ToString(ingame_role[i])+"' )" );
                                        }
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
