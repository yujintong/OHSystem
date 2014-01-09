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

#ifndef GPSPROTOCOL_H
#define GPSPROTOCOL_H

//
// CGameProtocol
//

#define GPS_HEADER_CONSTANT			248

#define REJECTGPS_INVALID			1
#define REJECTGPS_NOTFOUND			2

class CGPSProtocol
{
public:
	enum Protocol {
		GPS_INIT				= 1,
		GPS_RECONNECT			= 2,
		GPS_ACK					= 3,
		GPS_REJECT				= 4
	};

	CGPSProtocol( );
	~CGPSProtocol( );

	// receive functions

	// send functions

	BYTEARRAY SEND_GPSC_INIT( uint32_t version );
	BYTEARRAY SEND_GPSC_RECONNECT( unsigned char PID, uint32_t reconnectKey, uint32_t lastPacket );
	BYTEARRAY SEND_GPSC_ACK( uint32_t lastPacket );

	BYTEARRAY SEND_GPSS_INIT( uint16_t reconnectPort, unsigned char PID, uint32_t reconnectKey, unsigned char numEmptyActions );
	BYTEARRAY SEND_GPSS_RECONNECT( uint32_t lastPacket );
	BYTEARRAY SEND_GPSS_ACK( uint32_t lastPacket );
	BYTEARRAY SEND_GPSS_REJECT( uint32_t reason );

	// other functions

private:
	bool AssignLength( BYTEARRAY &content );
	bool ValidateLength( BYTEARRAY &content );
};

#endif
