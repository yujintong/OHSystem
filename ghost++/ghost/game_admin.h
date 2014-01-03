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

#ifndef GAME_ADMIN_H
#define GAME_ADMIN_H

//
// CAdminGame
//

class CCallableAdminCount;
class CCallableAdminAdd;
class CCallableAdminRemove;
class CCallableBanCount;
// class CCallableBanAdd;
class CCallableBanRemove;

typedef pair<string,CCallableAdminCount *> PairedAdminCount;
typedef pair<string,CCallableAdminAdd *> PairedAdminAdd;
typedef pair<string,CCallableAdminRemove *> PairedAdminRemove;
typedef pair<string,CCallableBanCount *> PairedBanCount;
// typedef pair<string,CCallableBanAdd *> PairedBanAdd;
typedef pair<string,CCallableBanRemove *> PairedBanRemove;

typedef pair<string,uint32_t> TempBan;

class CAdminGame : public CBaseGame
{
protected:
	string m_Password;
	vector<TempBan> m_TempBans;
	vector<PairedAdminCount> m_PairedAdminCounts;	// vector of paired threaded database admin counts in progress
	vector<PairedAdminAdd> m_PairedAdminAdds;		// vector of paired threaded database admin adds in progress
	vector<PairedAdminRemove> m_PairedAdminRemoves;	// vector of paired threaded database admin removes in progress
	vector<PairedBanCount> m_PairedBanCounts;		// vector of paired threaded database ban counts in progress
	// vector<PairedBanAdd> m_PairedBanAdds;		// vector of paired threaded database ban adds in progress
	vector<PairedBanRemove> m_PairedBanRemoves;		// vector of paired threaded database ban removes in progress

public:
	CAdminGame( CGHost *nGHost, CMap *nMap, CSaveGame *nSaveGame, uint16_t nHostPort, unsigned char nGameState, string nGameName, string nPassword );
	virtual ~CAdminGame( );

	virtual bool Update( void *fd, void *send_fd );
	virtual void SendAdminChat( string message );
	virtual void SendWelcomeMessage( CGamePlayer *player );
	virtual void EventPlayerJoined( CPotentialPlayer *potential, CIncomingJoinPlayer *joinPlayer );
	virtual bool EventPlayerBotCommand( CGamePlayer *player, string command, string payload );
};

#endif
