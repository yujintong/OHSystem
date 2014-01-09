/**
  * Copyright [2013-2014] [OHsystem]
  * 
  * OHSystem is free software: You can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation, either version 3 of the License, or
  * (at your option) any later version.
  * 
  * Please save the copyrights and notifications on the footer.
  * We do NOT support or help people who removed this notice.
  * 
  * You can contact the developers on: admin@ohsystem.net
  * or join us directly here: https://ohsystem.net/forum
  * 
  * Visit us also on http://ohsystem.net/ and keep track always of the latest
  * features and changes.
  * 
  * This is modified from GHOST++: http://ghostplusplus.googlecode.com/
  * Official GhostPP-Forum: http://ghostpp.com/
 */

#ifndef COMMANDPACKET_H
#define COMMANDPACKET_H

//
// CCommandPacket
//

class CCommandPacket
{
private:
	unsigned char m_PacketType;
	int m_ID;
	BYTEARRAY m_Data;

public:
	CCommandPacket( unsigned char nPacketType, int nID, BYTEARRAY nData );
	~CCommandPacket( );

	unsigned char GetPacketType( )	{ return m_PacketType; }
	int GetID( )					{ return m_ID; }
	BYTEARRAY GetData( )			{ return m_Data; }
};

#endif
