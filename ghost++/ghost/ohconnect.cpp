/*
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
OHConnect :: OHConnect( CGHost *nGHost, CGame *nGame, string nIP, uint32_t nPort ) : m_GHost( nGHost ), m_Connected( false ), IP( nIP ), Port( nPort ), m_FirstConnect(false), m_LastSendTime(0), m_Socket(new CTCPClient( )), m_Handshake(false), m_ClientID(0)
{

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
    m_Handshake = false;
  }

  if( !m_Socket->GetConnecting( ) && !m_Socket->GetConnected( ) )
  {
     CONSOLE_Print("[OHConnect] Socket is not connected, reseting socket");
     m_Socket->Reset( );
     m_Handshake = false;
  }

  if( m_Socket->GetConnected( ))
  {
    m_Socket->DoRecvPlain( (fd_set *)fd );
    ExtractPackets( );
    ProcessPackets( );
    m_Socket->DoSendPlain( (fd_set *)send_fd);
  }

  if( m_Socket->GetConnecting( ) ) {
    if( m_Socket->CheckConnect( ) ) {
      CONSOLE_Print("[OHConnect] Successfully connected to socket");
      CONSOLE_Print("[OHConnect] Sending handshake");
string toSend="GET / HTTP/1.1\r\nHost: 5.45.181.151:6973\r\nUpgrade: websocket\r\nConnection: Keep-Alive, Upgrade\r\nSec-WebSocket-Key: dGhlIHNhbXBsZSBub25jZQ==\r\nOrigin: http://ohconnect.net\r\nSec-Websocket-Protocol: chat, superchat\r\nSec-WebSocket-Version: 13\r\n\r\n";
      m_Socket->PutBytes(toSend);
      m_Socket->DoSendPlain( (fd_set *)send_fd);
    }
    else {
      CONSOLE_Print("[OHConnect] Connection check failed, reseting socket");
      m_Socket->Reset( );
      m_Handshake = false;
    }
  }

  if( !m_Socket->GetConnecting( ) && !m_Socket->GetConnected( ) ) {
    CONSOLE_Print("[OHConnect] Server isn't connected, init a new connection");
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

}

void OHConnect :: ExtractPackets( )
{
  string *RecvBuffer = m_Socket->GetBytes( );
  string Buffer = *RecvBuffer;
  // a packet is at least 4 bytes so loop as long as the buffer contains 4 bytes
  if( Buffer.size( ) > 0 )
  {
      uint32_t length = Buffer.size();
      if(length > 0 ) {
         string message = Buffer.substr(0, length);
         ProcessEvent(message);
         *RecvBuffer = RecvBuffer->substr(length);
      }
  }
}
bool invalidChar (char c) 
{  
    return !(c>=0 && c <128);   
} 

void OHConnect :: ProcessEvent( string msg ){

  msg.erase(remove_if(msg.begin(),msg.end(), invalidChar), msg.end());

  if(msg.find("Switching Protocols")!=std::string::npos) {
    CONSOLE_Print("[OHConnect] handshake complete, sending join message");
    m_Handshake = true;
    return;
  }

  size_t hasType = msg.find("type");
  size_t length = msg.size();
  /* has a type definition */
 
  size_t pop = msg.find("{");
  if(pop>length) {
    CONSOLE_Print("[OHConnect] Recieved invalid packet, ignoring: "+msg);
    return;
  }
  msg = msg.substr(pop);
  string fullmsg = msg;
  size_t systemMessage = msg.find("\"type\":\"system\"");
  if(msg.find("<img")!=std::string::npos && systemMessage != 1) {
    CONSOLE_Print("[OHConnect] Recieved a packet with image tag, ignoring: "+msg);
    return;
  }

  if(hasType!=std::string::npos) {
    /* type */
    size_t pos = msg.find("type");
    msg = msg.substr(pos+7);
    size_t pos2 = msg.find_first_of("\"");
    string type = msg.substr(0, pos2);
    msg = msg.substr(pos2);
    if(type != "id" ) {
      /* datetime */
      pos = msg.find("datetime");
      msg = msg.substr(pos+11);
      pos2 = msg.find_first_of("\"");
      string date = msg.substr(0, pos2);
      msg = msg.substr(pos2);

      /* name */
      pos = msg.find("name");
      msg = msg.substr(pos+7);
      pos2 = msg.find_first_of("\"");
      string name = msg.substr(0, pos2);
      msg = msg.substr(pos2);

      /* message */
      pos = msg.find("message");
      msg = msg.substr(pos+10);
      pos2 = msg.find_first_of("\"");
      string message = msg.substr(0, pos2);
      msg = msg.substr(pos2);
      
      /* color */
      msg = msg.substr(pos+8);
      pos2 = msg.find_first_of("\"");
      string color = msg.substr(0, pos2);
      msg = msg.substr(pos2);

      /* room */
      pos = msg.find("room");
      msg = msg.substr(pos+7);
      pos2 = msg.find_first_of("\"");
      string room = msg.substr(0, pos2);
      msg = msg.substr(pos2);

      /* roomname */
      pos = msg.find("roomname");
      msg = msg.substr(pos+11);
      pos2 = msg.find_first_of("\"");
      string roomname = msg.substr(0, pos2);
      msg = msg.substr(pos2);

      if(type=="usermsg") {
        CONSOLE_Print("[OHCONNECT: "+roomname+"] "+name+": "+message);
      }

      if(type=="system") {
        if(fullmsg.find("has joined") ) {
          CONSOLE_Print("[OHCONNECT: "+roomname+"] "+name+" has joined the room");
        }  

      }
    }
  }
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
  }
}
