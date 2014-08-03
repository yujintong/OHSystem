/* Copyright [2013-2014] [OHsystem]
*
* We spent a lot of time writing this code, so show some respect:
* - Do not remove this copyright notice anywhere (bot, website etc.)
* - We do not provide support to those who removed copyright notice
*
* OHSystem is free software: You can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* You can contact the developers on: admin@ohsystem.net
* or join us directly here: http://ohsystem.net/forum/
*
* Visit us also on http://ohsystem.net/ and keep track always of the latest
* features and changes.
*
*
* This is modified from GHOST++: http://ghostplusplus.googlecode.com/
* Official GhostPP-Forum: http://ghostpp.com/
*/

#include "ghost.h"
#include "socket.h"
#include "ohconnect.h"

//
// OHConnect
//
OHConnect :: OHConnect( CGHost *nGHost, CGame *nGame, string IP, uint32_t port ) : m_GHost( nGHost ), m_Connected( false )
{

  /* connect */
  m_Socket = new CTCPClient();
  m_Socket->Connect("", IP, port);

  if(m_Socket->HasError( )) {
    //the socket has an error
    CONSOLE_Print("[OHConnect-Socket] disconnected from OHConnect due socket error");
    CONSOLE_Print("[OHConnect-Socket] "+m_Socket->GetErrorString( ));
  }
  else {
    m_Connected = true;
    CONSOLE_Print("[OHConnect-Socket] Successfully connected to OHConnect");
    m_Socket->PutBytes("");
  }
}

OHConnect :: ~OHConnect( )
{
  delete m_Socket;
}

bool OHConnect :: Update( void *fd, void *send_fd )
{


}

unsigned int OHConnect :: SetFD( void *fd, void *send_fd, int *nfds )
{
  unsigned int NumFDs = 0;

  if( !m_Socket->HasError( ) && m_Socket->GetConnected( ) ) {
    m_Socket->SetFD( (fd_set *)fd, (fd_set *)send_fd, nfds );
    ++NumFDs;
  }
  return NumFDs;
}
void OHConnect :: ProcessPackets( )
{

}

void OHConnect :: ExtractPackets( )
{

}
