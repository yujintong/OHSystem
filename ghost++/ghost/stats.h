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

#ifndef STATS_H
#define STATS_H

//
// CStats
//

// the stats class is passed a copy of every player action in ProcessAction when it's received
// then when the game is over the Save function is called
// so the idea is that you parse the actions to gather data about the game, storing the results in any member variables you need in your subclass
// and in the Save function you write the results to the database
// e.g. for dota the number of kills/deaths/assists, etc...
// the base class is almost completely empty

class CIncomingAction;
class CGHostDB;

class CStats
{
protected:
	CBaseGame *m_Game;
	bool m_Locked;

public:
	CStats( CBaseGame *nGame );
	virtual ~CStats( );

	virtual bool ProcessAction( CIncomingAction *Action );
	virtual void Save( CGHost *GHost, CGHostDB *DB, uint32_t GameID );
	virtual void SetWinner( uint32_t nWinner ) {}
	virtual void LockStats( ) { m_Locked = true; }
};

#endif
