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

#ifndef CRC32_H
#define CRC32_H

#define CRC32_POLYNOMIAL 0x04c11db7

class CCRC32
{
public:
	void Initialize( );
	uint32_t FullCRC( unsigned char *sData, uint32_t ulLength );
	void PartialCRC( uint32_t *ulInCRC, unsigned char *sData, uint32_t ulLength );

private:
	uint32_t Reflect( uint32_t ulReflect, char cChar );
	uint32_t ulTable[256];
};

#endif
