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
#include "util.h"
#include "commandpacket.h"

//
// OHConnect
//
OHConnect :: OHConnect( CGHost *nGHost, CGame *nGame, string nIP, uint32_t nPort ) : m_GHost( nGHost ), m_Connected( false ), IP( nIP ), Port( nPort )
{
  Connect( );
}

OHConnect :: ~OHConnect( )
{
  delete m_Socket;
}

bool OHConnect :: Update( void *fd, void *send_fd )
{
  if( m_Socket->HasError( ) )
  {
    CONSOLE_Print("[OHConnect] Socket error, reseting socket");
    m_Socket->Reset( );
  }

  if( !m_Socket->GetConnecting( ) && !m_Socket->GetConnected( ) )
  {
     m_Socket->Reset( );
  }

  if( m_Socket->GetConnected( ) )
  {
    m_Socket->DoRecvPlain( (fd_set *)fd );
    ExtractPackets( );
    ProcessPackets( );

    //m_Socket->PutBytes("{ name => 'Bot', message => 'Neuby sucks :)' }");
    //m_Socket->DoSendPlain( (fd_set *)send_fd );
  }

  if( m_Socket->GetConnecting( ) ) {
    if( m_Socket->CheckConnect( ) ) {
      CONSOLE_Print("[OHConnect] Successfully connected to socket");
    }
    else {
      CONSOLE_Print("[OHConnect] Connection check failed, reseting socket");
      m_Socket->Reset( );
    }
  }

  if( !m_Socket->GetConnecting( ) && !m_Socket->GetConnected( ) ) {
    Connect( );
  }
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

  while(!m_Packets.empty( ) ) {
    CCommandPacket *Packet = m_Packets.front( );
    m_Packets.pop( );

    if(Packet->GetPacketType( ) == 16 ) {
      CONSOLE_Print("ID: "+UTIL_ToString(Packet->GetID( ))+", Data: "+string(Packet->GetData( ).begin( ), Packet->GetData( ).end( ) ) );
    }
  }
}

void OHConnect :: ExtractPackets( )
{
  string *RecvBuffer = m_Socket->GetBytes( );
  string Buffer = *RecvBuffer;
  // a packet is at least 4 bytes so loop as long as the buffer contains 4 bytes
  if( Buffer.size( ) > 0 )
  {
      uint32_t length = Buffer.size();
      string message = Buffer.substr(3, length-3);
      ProcessEvent(message);
      *RecvBuffer = RecvBuffer->substr(length);
  }
}

void OHConnect :: ProcessEvent( string message ) {
CONSOLE_Print(message);

  size_t hasType = message.find("type");
  size_t length = message.size();
  /* has a type definition */
/*if(hasType!=std::string::npos) {
    size_t pos = message.find("type")!=std::string::npos;
    string message = message.substr(pos+9, length-(pos+7));
    size_t pos2 = message.find_first_of("\"");
    string type = message.substr(0, pos2);

CONSOLE_Print("Type: "+type);
  }
*/
}
void OHConnect :: Connect( )
{
  /* connect */
  m_Socket = new CTCPClient();
  m_Socket->Connect("", IP, Port);

  if(m_Socket->HasError( )) {
    //the socket has an error
    CONSOLE_Print("[OHConnect] disconnected from OHConnect due socket error");
    CONSOLE_Print("[OHConnect] "+m_Socket->GetErrorString( ));
  }
  else {
    m_Connected = true;
    CONSOLE_Print("[OHConnect] Starting connection to socket");
    /* handshake*/
    string key = "x3JJHMbDL1EzLkh9GBhXDw==";
    string toSend = " 'HTTP/1.1 101 Switching Protocols', 'Upgrade: websocket', 'Connection: Upgrade', 'Sec-WebSocket-Key: '"+key;
    // populate key with content of Sec-WebSocket-Key header
    key.append("258EAFA5-E914-47DA-95CA-C5AB0DC85B11");
    toSend += key;
    m_Socket->PutBytes(toSend);
  }
}
