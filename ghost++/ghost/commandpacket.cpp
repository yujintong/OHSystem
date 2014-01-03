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

#include "ghost.h"
#include "commandpacket.h"

//
// CCommandPacket
//

CCommandPacket :: CCommandPacket( unsigned char nPacketType, int nID, BYTEARRAY nData ) : m_PacketType( nPacketType ), m_ID( nID ), m_Data( nData )
{

}

CCommandPacket :: ~CCommandPacket( )
{

}
