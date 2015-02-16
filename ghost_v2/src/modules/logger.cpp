/*
 * Author:  Grief-Code
 * Project: OHSystem ghost_v2
 * Support: admin@gcoding.net
 * Date: 16.02.2014
 * Version: 0.9
 *
 * File: logger.cpp
 * Desc: A file to handle log entries over the whole project
 * - generate game logs
 * - generate lobby logs
 * - generate admin logs
 * - warper for statspage class
 * - warper for ohconnect class
 */

#include "logger.h"
#include "../ghost.h"

Logger :: Logger( )
{
	CONSOLE_Print("***************************************");
	CONSOLE_Print("** OHSYSTEM LOGGER MODULE INITALIZED **");
	CONSOLE_Print("***************************************");
}

Logger :: ~Logger( )
{

}


