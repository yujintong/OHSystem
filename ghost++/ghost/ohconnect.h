/**
* Copyright [2013-2014] [OHsystem]
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

#ifndef OHConnect_H
#define OHConnect_H

//
// OHCONNECT
//

class CTCPClient;
class CGame;
class CCommandPacket;

class OHConnect
{
public:
    CGHost *m_GHost;

protected:
    bool m_Connected;
    string IP;
    uint32_t Port;
    queue<CCommandPacket *> m_Packets;
    bool m_FirstConnect;
    uint32_t m_LastSendTime;
    bool m_Handshake;
    uint32_t m_ClientID;
private:
    CTCPClient *m_Socket; // the connection to ohconnect

public:
    OHConnect( CGHost *nGHost, CGame *nGame, string ip, uint32_t port );

    virtual ~OHConnect( );

    /* processing functions */
    unsigned int SetFD( void *fd, void *send_fd, int *nfds );
    bool Update( void *fd, void *send_fd );
    void ExtractPackets( );
    void ProcessPackets( );
    void Connect( );
    void ProcessEvent( string message );
};

#endif
