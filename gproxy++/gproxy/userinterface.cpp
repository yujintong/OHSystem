
#include "bnet.h"
#include "bnetprotocol.h"
#include "userinterface.h"
#include "util.h"
#include "ohsystem.h"

#include <math.h>

#ifdef WIN32
# include <windows.h>
#endif

// used for window resizing
int ceili( float f )
{
	return int( ceilf( f ) );
}

CCurses :: CCurses( int nTermWidth, int nTermHeight, bool nSplitView, int nListType )
{
#ifdef __PDCURSES__
    PDC_set_title("GProxy++ OHSystem Mod");
#endif

	// Initialize vectors
	SWindowData temp2;
	temp2.Scroll = 0;
	for ( uint32_t i = 0; i < 10; ++i )
		m_WindowData.push_back( temp2 );

	Buffer *temp3;
	for ( uint32_t i = 0; i < 30; ++i )
	{
		temp3 = new Buffer( );
		m_Buffers.push_back( temp3 );
	}

	// Initialize variables
	m_ListUpdateTimer = 0;
	m_SelectedTab = 0;
	m_SelectedInput = 0;
	m_GProxy = 0;
	m_SplitView = nSplitView;
	m_ListType = nListType;
	m_InitialUpdate = true;
	exY = 0; exX = 0;
	
	// Initialize curses and windows
	initscr( );
	clear( );
	noecho( );
	cbreak( );

	m_WindowData[W_TAB].Window = newwin( 1, COLS, 0, 0 );
	m_WindowData[W_FULL].Window = newwin( LINES - 3, COLS - 22, 1, 0 );
	m_WindowData[W_FULL2].Window = newwin( LINES - 3, COLS, 1, 0 );
	m_WindowData[W_UPPER].Window = newwin( LINES / 2 - 2, COLS - 22, 1, 0 );
	m_WindowData[W_LOWER].Window = newwin( LINES / 2 - 2, COLS - 22, LINES / 2, 0 );
	m_WindowData[W_CHANNEL].Window = newwin( LINES - 3, 21, 1, COLS - 21 );
	m_WindowData[W_INPUT].Window = newwin( 2, COLS, LINES - 2, 0 );
	m_WindowData[W_HLINE].Window = newwin( 1, COLS - 22, LINES / 2 - 1, 0 );
	m_WindowData[W_VLINE].Window = newwin( LINES - 3, 1, 1, COLS - 22 );

	scrollok( m_WindowData[W_FULL].Window, TRUE );
	scrollok( m_WindowData[W_FULL2].Window, TRUE );
	scrollok( m_WindowData[W_UPPER].Window, TRUE );
	scrollok( m_WindowData[W_LOWER].Window, TRUE );
	scrollok( m_WindowData[W_CHANNEL].Window, TRUE );
	keypad( m_WindowData[W_INPUT].Window, TRUE );
	scrollok( m_WindowData[W_INPUT].Window, TRUE );
	nodelay( m_WindowData[W_INPUT].Window, TRUE );

	// Initialize colors
	start_color();
	init_pair( 0, COLOR_WHITE, COLOR_BLACK );
	init_pair( 1, COLOR_GREEN, COLOR_BLACK );
	init_pair( 2, COLOR_RED, COLOR_BLACK );
	init_pair( 3, COLOR_CYAN, COLOR_BLACK );
	init_pair( 4, COLOR_YELLOW, COLOR_BLACK );
	init_pair( 5, COLOR_BLUE, COLOR_BLACK );
	init_pair( 6, COLOR_WHITE, COLOR_CYAN );

	// make this an option/config?
	wbkgdset( m_WindowData[W_INPUT].Window, ' ' | COLOR_PAIR(6)  );
	wbkgdset( m_WindowData[W_TAB].Window, ' ' | COLOR_PAIR(6)  );
	wattr_set( m_WindowData[W_INPUT].Window, A_NORMAL, 6, 0 );
	wattr_set( m_WindowData[W_TAB].Window, A_NORMAL, 6, 0 );
	
	// Change terminal size
	Resize( nTermHeight, nTermWidth );

	// Tabs
	AddTab( "MAIN", T_MAIN, 0, B_ALL, W_TAB );
	AddTab( "GAMES", T_GAMES, 0, B_GAMES, W_TAB );
	AddTab( "FRIENDS", T_LIST, 0, B_FRIENDS, W_TAB );
	AddTab( "CLAN", T_LIST, 0, B_CLAN, W_TAB );
	SelectTab( 0 );
	
	// Initialize Input-buffer
	m_Buffers[B_INPUT]->push_back( pair<string, int>("", 0) );

#ifdef __PDCURSES__
	// Mouse cursor
	mouse_on( ALL_MOUSE_EVENTS );
	mouseinterval( 30 );
	curs_set( 1 );	// 0 = nothing or 1 = underline or 2 = block
#endif
}

CCurses :: ~CCurses( )
{
	endwin( );
}

void CCurses :: AddTab( string nName, TabType nType, uint32_t nId, BufferType nBufferType, WindowType nWindowType )
{
	STabData data;
	data.name = nName;
	data.type = nType;
	data.bufferType  = nBufferType;
	data.IsTabSelected = false;
	data.windowType = nWindowType;

	for( vector<STabData> :: iterator i = m_TabData.begin( ); i != m_TabData.end( ); i++ )
	{
		if( (*i).type == nType )
		{
			m_TabData.insert( i + 1, data );
			m_WindowData[nWindowType].IsWindowChanged = true;
			return;
		}
	}

	m_WindowData[nWindowType].IsWindowChanged = true;
	m_TabData.push_back( data );
}

void CCurses :: SelectTab( uint32_t n )
{
	if( n >= 0 && n < m_TabData.size( ) )
	{
		if( m_SelectedTab < m_TabData.size( ) )
			m_TabData[m_SelectedTab].IsTabSelected = false;

		m_SelectedTab = n;
		m_TabData[m_SelectedTab].IsTabSelected = true;

		m_Buffers[B_REALM] = &m_RealmData.Messages;
		m_Buffers[B_CHANNEL] = &m_RealmData.ChannelUsers;

		UpdateCustomLists( m_TabData[m_SelectedTab].bufferType );
		CompileList( m_TabData[m_SelectedTab].bufferType );

		ClearWindow( W_FULL2 );

		UpdateWindows( );
	}
}

uint32_t CCurses :: NextTab( )
{
	if( m_SelectedTab == m_TabData.size( ) - 1 )
		return 0;
	return m_SelectedTab + 1;
}

uint32_t CCurses :: PreviousTab( )
{
	if( m_SelectedTab == 0 )
		return m_TabData.size( ) - 1;
	return m_SelectedTab - 1;
}


void CCurses :: UpdateWindow( WindowType type )
{
	int y1 = 0, x1 = 0;
	int y2 = 0, x2 = 0;

	// sizes for resizing and moving
	switch( type )
	{
	case W_TAB:		y1 = 0; x1 = 0; y2 = 1; x2 = COLS; break;
	case W_FULL:	y1 = 1; x1 = 0; y2 = LINES - 3; x2 = COLS - 22; break;
	case W_FULL2:	y1 = 1; x1 = 0; y2 = LINES - 3; x2 = COLS; break;
	case W_UPPER:	y1 = 1; x1 = 0; y2 = LINES / 2 - 2; x2 = COLS - 22; break;
	case W_LOWER:	y1 = LINES / 2; x1 = 0; y2 = ceili(LINES * 1.0f / 2) - 2; x2 = COLS - 22; break;
	case W_CHANNEL:	y1 = 1; x1 = COLS - 21; y2 = LINES - 3; x2 = 21; break;
	case W_INPUT:	y1 = LINES - 2; x1 = 0; y2 = 2; x2 = COLS; break;
	case W_HLINE:	y1 = LINES / 2 - 1; x1 = 0; y2 = 1; x2 = COLS - 22; break;
	case W_VLINE:	y1 = 1; x1 = COLS - 22; y2 = LINES - 3; x2 = 1; break;
	}

	wresize( m_WindowData[type].Window, y2, x2 );
	mvwin( m_WindowData[type].Window, y1, x1 );
	m_WindowData[type].IsWindowChanged = true;

	// halve scrolling in split windows
	if( !m_TabData.empty( ) && m_Buffers[m_TabData[m_SelectedTab].bufferType]->size( ) > (uint32_t)LINES / 2 )
	{
		if( ( m_WindowData[type].Scroll < LINES ) && ( type == W_FULL || type == W_FULL2 ) )
			m_WindowData[type].Scroll = LINES;
		else if( m_WindowData[type].Scroll < LINES / 2 )
			m_WindowData[type].Scroll = LINES / 2;
	}
}

void CCurses :: ClearWindow( WindowType type )
{
	wclear( m_WindowData[type].Window );
	wrefresh( m_WindowData[type].Window );
	m_WindowData[type].IsWindowChanged = true;
}

void CCurses :: UpdateWindows( )
{
	for( uint32_t i = 0; i < 9; ++i )
			UpdateWindow( WindowType( i ) );
}

void CCurses :: CompileList( BufferType type )
{
	switch( type )
	{
	case B_FRIENDS:	CompileFriends( );	break;
	case B_CLAN:	CompileClan( );		break;
	case B_GAMES:	CompileGames( );	break;
	}
}

void CCurses :: CompileLists( )
{
	CompileFriends( );
	CompileClan( );
	CompileGames( );
}

void CCurses :: CompileFriends( )
{
	m_Buffers[B_FRIENDS] = &m_RealmData.Friends;
}

void CCurses :: CompileClan( )
{
	m_Buffers[B_CLAN] = &m_RealmData.Clan;
}

void CCurses :: CompileGames( )
{
	m_Buffers[B_GAMES]->clear();

	int reliable = 0;
	
	for( uint32_t i = 0; i < m_GProxy->m_Games.size(); ++i )
	{	
		reliable = m_GProxy->m_Games[i]->GetMapWidth( ) == 1984 && m_GProxy->m_Games[i]->GetMapHeight( ) == 1984 ? 1 : 0;
		m_Buffers[B_GAMES]->push_back( pair<string, int>( m_GProxy->m_Games[i]->GetGameName( ), reliable ) );
		m_Buffers[B_GAMES]->push_back( pair<string, int>( m_GProxy->m_Games[i]->GetHostName( ), reliable ) );
		m_Buffers[B_GAMES]->push_back( pair<string, int>( m_GProxy->m_Games[i]->GetMapPath( ), reliable ) );
	}

	if( m_SelectedTab == 1)
		m_WindowData[W_FULL].IsWindowChanged = true;
}

void CCurses :: SetGProxy( CGProxy* nGProxy )
{
	m_GProxy = nGProxy;

	if(!m_GProxy->m_BNET)
		return;

	m_RealmData.RealmAlias = m_GProxy->m_BNET->GetServer();
	AddTab( m_GProxy->m_BNET->GetServer( ), T_REALM, 0, B_REALM, W_TAB );

	Print( "", true );
	Print( ">>> /commands", true );
	Print( "", true );
	Print( "  In the GProxy++ console:", true );
	Print( "   /<bnet-command> <...>    : Battle.net command", true );
	Print( "   /resize <width> <height> : Resizes console", true );
	Print( "   /split                   : Toggles split view in realm tabs", true );
	Print( "   /exit or /quit           : Close GProxy++", true );
	Print( "   /filter <f>              : Start filtering public game names for <f>", true );
	Print( "   /filteroff               : Stop filtering public game names", true );
	Print( "   /help                    : Show help text", true );
    Print( "   /public                  : Enable listing of games", true );
    Print( "   /publicoff               : Disable listing of games", true );
#ifdef WIN32
	Print( "   /start                   : Start warcraft 3", true );
#endif
	Print( "  In game:", true );
	Print( "   /re <message>            : Reply to the last received whisper", true );
	Print( "   /sc                      : Whispers \"spoofcheck\" to the game host", true );
	Print( "   /status                  : Show status information", true );
	Print( "   /w <user> <message>      : Whispers <message> to <user>", true );
	Print( "", false );

}

void CCurses :: SetAttribute( SWindowData &data, string message, int flag, BufferType type, bool on )
{
	attr_t attribute = A_WHITE;

	transform( message.begin(), message.end(), message.begin(), ::tolower );
	
	// determine color using buffer type, message and flag
	switch( type )
	{
	case B_TAB:
		if( flag == 1 )	attribute = COLOR_PAIR( 6 ) | A_BOLD;
		break;
	case B_ALL:
	case B_MAIN:
		flag = GetMessageFlag( message );
		if ( message[0] != '<' && message.find("error") != -1 )
		{
			attribute = A_RED;
			if( flag != 5 && flag != 1 ) break;
		}
	case B_REALM:
		flag = GetMessageFlag( message );
		switch( flag )
		{
		case 0: attribute = A_WHITE;	break;	// NORMAL, GAME, MAP, CONFIG, UTIL
		case 1: attribute = A_CYAN;		break;	// INFO, EMOTE
		case 2: attribute = A_CYAN;		break;	// GHOST
		case 3: attribute = A_RED;		break;	// ERROR
		case 4: attribute = A_YELLOW;	break;	// BNET, BNLSC
		case 5: attribute = A_GREEN;	break;	// WHISPER
		}
		break;
	case B_CHANNEL:
		switch( flag )
		{
		case 1: attribute = A_BBLUE;	break;	// BLIZZARD REP
		case 2: attribute = A_YELLOW;	break;	// CHANNEL OP
		case 4: attribute = A_CYAN;		break;	// SPEAKER
		case 8: attribute = A_BBLUE;	break;	// BNET ADMIN
		case 32: attribute = A_RED;		break;	// SQUELCHED
		}
		break;
	case B_FRIENDS:
		switch ( flag )
		{
		case 0: attribute = A_RED;		break;	// OFFLINE
		case 1: attribute = A_WHITE;	break;	// NOT IN CHAT
		case 2: attribute = A_BWHITE;	break;	// IN CHAT
		case 3: attribute = A_GREEN;	break;	// IN PUBLIC GAME
		case 4: attribute = A_YELLOW;	break;	// IN PRIVATE GAME; NOT MUTUAL
		case 5: attribute = A_CYAN;		break;	// IN PRIVATE GAME; MUTUAL
		}
		break;
	case B_CLAN:
		switch ( flag )
		{
		case 0: attribute = A_WHITE;	break;	// INITIATE
		case 1: attribute = A_WHITE;	break;	// PARTIAL MEMBER
		case 2: attribute = A_YELLOW;	break;	// MEMBER
		case 3: attribute = A_GREEN;	break;	// OFFICER
		case 4: attribute = A_BBLUE;	break;	// LEADER
		}
		break;
	case B_GAMES:
		switch ( flag )
		{
		case 0: attribute = A_WHITE;	break;	// NORMAL
		case 1: attribute = A_GREEN;	break;	// RELIABLE
		}
		break;
	}

	if( on )
		wattr_on( data.Window, attribute, 0 );
	else
		wattr_off( data.Window, attribute, 0 );
}

void CCurses :: Draw ( )
{
	// draw tabs always
	m_WindowData[W_TAB].Title = m_RealmData.ChannelName;
	DrawTabs( W_TAB );

	if( !m_TabData.empty( ) )
		switch( m_TabData[m_SelectedTab].type )
		{
		case T_MAIN:
			DrawWindow( W_FULL, m_TabData[m_SelectedTab].bufferType );
			break;
		case T_LIST:
			if( m_ListType == 0 )
			{
				DrawListWindow( W_FULL, m_TabData[m_SelectedTab].bufferType );
			}
			else
			{
				DrawListWindow2( W_FULL, m_TabData[m_SelectedTab].bufferType );
			}
			break;
		case T_REALM:
			if( m_SplitView )
			{
				DrawHorizontalLine( W_HLINE );
				DrawWindow( W_UPPER, B_MAIN );
				DrawWindow( W_LOWER, m_TabData[m_SelectedTab].bufferType );
			}
			else
			{
				DrawWindow( W_FULL, m_TabData[m_SelectedTab].bufferType );
			}
			break;
		case T_GAMES:
			DrawListWindow3( W_FULL, m_TabData[m_SelectedTab].bufferType );
			break;
		}

	DrawWindow( W_CHANNEL, B_CHANNEL );
	DrawWindow( W_INPUT, B_INPUT );
}

void CCurses :: DrawTabs( WindowType type )
{
	SWindowData &data = m_WindowData[type];

	if ( data.IsWindowChanged )
	{
		wclear( data.Window );
		wmove( data.Window, 0, 0 );

		for( vector<STabData> :: iterator i = m_TabData.begin( ); i != m_TabData.end( ); i++ )
		{
			if( (*i).windowType == type )
			{
				string &name = (*i).name;
				int flag = (*i).IsTabSelected ? 1 : 0;

				name = UTIL_UTF8ToLatin1( name );

				SetAttribute( data, name, flag, B_TAB, true );

				for( string :: iterator j = name.begin( ); j != name.end( ); j++ )
					waddch( data.Window, UTIL_ToULong( *j ) );

				SetAttribute( data, name, flag, B_TAB, false );

				waddch( data.Window, ' ' );
			}
		}

		if( !data.Title.empty() )
		{
			wmove( data.Window, 0, COLS - 21 );
			whline( data.Window, 0, 20 );
			mvwaddnstr( data.Window, 0, COLS - 21 + (data.Title.size( ) < 20 ? ( 20 - data.Title.size( ) ) / 2 : 0), data.Title.c_str( ), 20 );
		}

		wrefresh( data.Window );
		data.IsWindowChanged = false;
	}
}

void CCurses :: DrawWindow( WindowType wType, BufferType bType )
{
	SWindowData& data = m_WindowData[wType];

	if ( data.IsWindowChanged )
	{
		bool onlyLast = bType == B_INPUT && m_Buffers[bType]->size( ) > 1;

		wclear( data.Window );

		int k = 0;
		for( Buffer :: iterator i = onlyLast ? m_Buffers[bType]->end( ) - 1 : m_Buffers[bType]->begin( ); i != m_Buffers[bType]->end( ); i++ )
		{
			string message = (*i).first;
			int &flag = (*i).second;
			
			message = UTIL_UTF8ToLatin1( message );

			SetAttribute( data, message, flag, bType, true );

			for( string :: iterator j = message.begin( ); j != message.end( ); j++ )
				waddch( data.Window, UTIL_ToULong( *j ) );

			SetAttribute( data, message, flag, bType, false );

			if( k++ >= data.Scroll )
				break;

			if( i != m_Buffers[bType]->end( ) - 1 )
				waddch( data.Window, '\n' );
		}

		wrefresh( data.Window );
		data.IsWindowChanged = false;
	}
}

void CCurses :: DrawListWindow( WindowType wType, BufferType bType )
{
	SWindowData& data = m_WindowData[wType];

	if ( data.IsWindowChanged )
	{
		wclear( data.Window );

		for( Buffer :: iterator i = m_Buffers[bType]->begin( ); i != m_Buffers[bType]->end( ); i++ )
		{
			string message = (*i).first;
			int &flag = (*i).second;
			
			message = UTIL_UTF8ToLatin1( message );

			SetAttribute( data, message, flag, bType, true );

			for( string :: iterator j = message.begin( ); j != message.end( ); j++ )
				waddch( data.Window, UTIL_ToULong( *j ) );

			waddch( data.Window, ' ' );

			SetAttribute( data, message, flag, bType, false );
		}

		wrefresh( data.Window );
		data.IsWindowChanged = false;
	}
}

void CCurses :: DrawListWindow2( WindowType wType, BufferType bType )
{
	SWindowData& data = m_WindowData[wType];

	if ( data.IsWindowChanged )
	{
		wclear( data.Window );

		int y = 0;
		for( Buffer :: iterator i = m_Buffers[bType]->begin( ); i != m_Buffers[bType]->end( ); i++ )
		{
			string message = (*i).first;
			int &flag = (*i).second;
			
			message = UTIL_UTF8ToLatin1( message );

			if( y++ < LINES - 5 )
			{
				wmove( data.Window, y, 0 );
				refresh( );

				SetAttribute( data, message, flag, bType, true );

				for( uint32_t j = 0; j < message.size( ) && j < 30; j++ )
					waddch( data.Window, UTIL_ToULong( message[j] ) );

				i++;
				message = (*i).first;
				flag = (*i).second;
				message = UTIL_UTF8ToLatin1( message );

				wmove( data.Window, y, 32 );
				refresh( );

				for( uint32_t j = 0; j < message.size( ) && j < 20; j++ )
					waddch( data.Window, UTIL_ToULong( message[j] ) );

				SetAttribute( data, message, flag, bType, false );
			}
		}

		wrefresh( data.Window );
		data.IsWindowChanged = false;
	}
}

void CCurses :: DrawListWindow3( WindowType wType, BufferType bType )
{
	SWindowData& data = m_WindowData[wType];

	if ( data.IsWindowChanged )
	{
		wclear( data.Window );

		waddstr( data.Window, "Use command '/public' or '/game name' to populate the list. Note that it takes some time to query games.\n" );

		int y = 1;
		for( Buffer :: iterator i = m_Buffers[bType]->begin( ); i != m_Buffers[bType]->end( ); i++ )
		{
			string message = (*i).first;
			int &flag = (*i).second;
			
			message = UTIL_UTF8ToLatin1( message );

			if( y++ < LINES - 5 )
			{
				wmove( data.Window, y, 0 );
				refresh( );

				SetAttribute( data, message, flag, bType, true );

				for( uint32_t j = 0; j < message.size( ) && j < 30; j++ )
					waddch( data.Window, UTIL_ToULong( message[j] ) );

				i++;
				message = (*i).first;
				flag = (*i).second;
				message = UTIL_UTF8ToLatin1( message );

				wmove( data.Window, y, 32 );
				refresh( );

				for( uint32_t j = 0; j < message.size( ) && j < 20; j++ )
					waddch( data.Window, UTIL_ToULong( message[j] ) );

				i++;
				message = (*i).first;
				flag = (*i).second;
				message = UTIL_UTF8ToLatin1( message );

				wmove( data.Window, y, 54 );
				refresh( );

				for( uint32_t j = 0; j < message.size( ); j++ )
					waddch( data.Window, UTIL_ToULong( message[j] ) );

				SetAttribute( data, message, flag, bType, false );
			}
		}

		wrefresh( data.Window );
		data.IsWindowChanged = false;
	}
}

void CCurses :: DrawHorizontalLine( WindowType type )
{
	SWindowData& data = m_WindowData[type];

	if ( data.IsWindowChanged )
	{
		whline( data.Window, 0, COLS );
		wrefresh( data.Window );
		data.IsWindowChanged = false;
	}
}

void CCurses :: DrawVerticalLine( WindowType type )
{
	SWindowData& data = m_WindowData[type];

	if ( data.IsWindowChanged )
	{
		wvline( data.Window, 0, LINES );
		wrefresh( data.Window );
		data.IsWindowChanged = false;
	}
}

void CCurses :: Resize( int y, int x )
{
	if( y > 5 && x > 5 )
	{
		resize_term( y, x );
		
		Resize( );
	}
}

void CCurses :: Resize( )
{
	resize_term( 0, 0 );
	clear();
	refresh();

	UpdateWindows( );

	Draw( );
}

void CCurses :: Print( string message, bool toMainBuffer )
{
	message = UTIL_UTF8ToLatin1( message );
	pair<string, int> temp = pair<string, int>( message, 0 );
	if ( toMainBuffer )
	{
		m_Buffers[B_MAIN]->push_back( temp );

		if( m_Buffers[B_MAIN]->size( ) > MAX_BUFFER_SIZE )
			m_Buffers[B_MAIN]->erase( m_Buffers[B_MAIN]->begin( ) );

		m_WindowData[W_UPPER].IsWindowChanged = true;

		if(m_WindowData[W_UPPER].Scroll < 512)
			m_WindowData[W_UPPER].Scroll++;
	}
	else
	{
		m_RealmData.Messages.push_back( temp );

		if( m_RealmData.Messages.size( ) > MAX_BUFFER_SIZE )
			m_RealmData.Messages.erase( m_RealmData.Messages.begin( ) );

		m_WindowData[W_LOWER].IsWindowChanged = true;

		if(m_WindowData[W_LOWER].Scroll < 512)
			m_WindowData[W_LOWER].Scroll++;
	}

	m_Buffers[B_ALL]->push_back( temp );

	if( m_Buffers[B_ALL]->size( ) > MAX_BUFFER_SIZE )
		m_Buffers[B_ALL]->erase( m_Buffers[B_ALL]->begin( ) );

	m_WindowData[W_FULL].IsWindowChanged = true;
	m_WindowData[W_FULL2].IsWindowChanged = true;

	if(m_WindowData[W_FULL].Scroll < 512)
		m_WindowData[W_FULL].Scroll++;

	if(m_WindowData[W_FULL2].Scroll < 512)
		m_WindowData[W_FULL2].Scroll++;

	Draw( );
}

void CCurses :: UpdateMouse( int c )
{
#ifdef __PDCURSES__
	// Mouse position update
	request_mouse_pos( );
	move( MOUSE_Y_POS, MOUSE_X_POS );
	refresh( );
	if( c == KEY_MOUSE )
	{
		// Is cursor over tabs?
		if( MOUSE_Y_POS <= 1 )
		{
			// Is left button pressed?
			if( Mouse_status.button[0] == BUTTON_PRESSED )
			{
				// Where it is pressed?
				int x1 = 0, x2 = 0;
				for( uint32_t i = 0; i < m_TabData.size( ); ++i )
				{
					if( m_TabData[i].windowType == W_TAB )
					{
						x1 = x2;
						x2 = x1 + m_TabData[i].name.size( ) + 1;

						if ( MOUSE_X_POS >= x1 && MOUSE_X_POS < x2 - 1 && MOUSE_X_POS < COLS )
						{
							SelectTab( i );
							return;
						}
					}
				}
			}
		}
		
		if ( m_TabData[m_SelectedTab].type != T_LIST )
		{
			if( Mouse_status.changes == MOUSE_WHEEL_UP )
				ScrollUp( );
			else if( Mouse_status.changes == MOUSE_WHEEL_DOWN )
				ScrollDown( );
		}
	}
#endif
}

void CCurses :: ScrollDown( )
{
	if( exX > COLS - 23 )
	{
			m_WindowData[W_CHANNEL].Scroll = (uint32_t)m_WindowData[W_CHANNEL].Scroll < m_Buffers[B_CHANNEL]->size( ) ?
										   m_WindowData[W_CHANNEL].Scroll + SCROLL_VALUE :
										   m_WindowData[W_CHANNEL].Scroll;
			m_WindowData[W_CHANNEL].IsWindowChanged = true;
	}
	else
	{
		switch( m_TabData[m_SelectedTab].type )
		{
		case T_MAIN:
			m_WindowData[W_FULL].Scroll = (uint32_t)m_WindowData[W_FULL].Scroll < m_Buffers[m_TabData[m_SelectedTab].bufferType]->size( ) ?
										   m_WindowData[W_FULL].Scroll + SCROLL_VALUE :
										   m_WindowData[W_FULL].Scroll;
			m_WindowData[W_FULL].IsWindowChanged = true;
			break;
		case T_REALM:
			if( m_SplitView )
			{
				if( exY < LINES / 2 )
				{
					m_WindowData[W_UPPER].Scroll = (uint32_t)m_WindowData[W_UPPER].Scroll < m_Buffers[B_MAIN]->size( ) ?
												   m_WindowData[W_UPPER].Scroll + SCROLL_VALUE :
												   m_WindowData[W_UPPER].Scroll;
					m_WindowData[W_UPPER].IsWindowChanged = true;
				}
				else
				{
					m_WindowData[W_LOWER].Scroll = (uint32_t)m_WindowData[W_LOWER].Scroll < m_Buffers[m_TabData[m_SelectedTab].bufferType]->size( ) ?
												   m_WindowData[W_LOWER].Scroll + SCROLL_VALUE :
												   m_WindowData[W_LOWER].Scroll;
					m_WindowData[W_LOWER].IsWindowChanged = true;
				}
			}
			else
			{
				m_WindowData[W_FULL].Scroll = (uint32_t)m_WindowData[W_FULL].Scroll < m_Buffers[m_TabData[m_SelectedTab].bufferType]->size( ) ?
											  m_WindowData[W_FULL].Scroll + SCROLL_VALUE :
											  m_WindowData[W_FULL].Scroll;
				m_WindowData[W_FULL].IsWindowChanged = true;
			}
			break;
		}
	}
}

void CCurses :: ScrollUp( )
{
	if( exX > COLS - 23 )
	{
			m_WindowData[W_CHANNEL].Scroll = m_WindowData[W_CHANNEL].Scroll - SCROLL_VALUE > LINES ?
										   m_WindowData[W_CHANNEL].Scroll - SCROLL_VALUE :
										   LINES - 4;
			m_WindowData[W_CHANNEL].IsWindowChanged = true;
	}
	else
	{
		switch( m_TabData[m_SelectedTab].type )
		{
		case T_MAIN:
			m_WindowData[W_FULL].Scroll = m_WindowData[W_FULL].Scroll - SCROLL_VALUE > LINES ?
										   m_WindowData[W_FULL].Scroll - SCROLL_VALUE :
										   LINES - 4;
			m_WindowData[W_FULL].IsWindowChanged = true;
			break;
		case T_REALM:
			if( m_SplitView )
			{
				if( exY < LINES / 2 )
				{
					m_WindowData[W_UPPER].Scroll = m_WindowData[W_UPPER].Scroll - SCROLL_VALUE > LINES / 2 ?
												   m_WindowData[W_UPPER].Scroll - SCROLL_VALUE :
												   (LINES - 4) / 2;
					m_WindowData[W_UPPER].IsWindowChanged = true;
				}
				else
				{
					m_WindowData[W_LOWER].Scroll = m_WindowData[W_LOWER].Scroll - SCROLL_VALUE > LINES / 2 ?
												   m_WindowData[W_LOWER].Scroll - SCROLL_VALUE :
												   (LINES - 4) / 2;
					m_WindowData[W_LOWER].IsWindowChanged = true;
				}
			}
			else
			{
				m_WindowData[W_FULL].Scroll = m_WindowData[W_FULL].Scroll - SCROLL_VALUE > LINES ?
											  m_WindowData[W_FULL].Scroll - SCROLL_VALUE :
											  LINES - 4;
				m_WindowData[W_FULL].IsWindowChanged = true;
			}
			break;
		}
	}
}


bool CCurses :: Update( )
{
	bool Quit = false;

	bool Connected = m_GProxy->m_BNET->GetLoggedIn( );

	// update lists
	if( Connected && m_InitialUpdate )
	{
		m_InitialUpdate = false;

		m_GProxy->m_BNET->RequestListUpdates( );

		m_Buffers[B_REALM] = &m_RealmData.Messages;
		m_Buffers[B_CHANNEL] = &m_RealmData.ChannelUsers;
		UpdateWindows( );
	}
	
	if( GetTime( ) > m_ListUpdateTimer && m_TabData[m_SelectedTab].type == T_LIST )
	{
		m_ListUpdateTimer = GetTime( ) + 10;

		m_GProxy->m_BNET->RequestListUpdates( );

		UpdateCustomLists( m_TabData[m_SelectedTab].bufferType );
		CompileList( m_TabData[m_SelectedTab].bufferType );
	}

	CompileGames();

	int c = wgetch( m_WindowData[W_INPUT].Window );

#ifdef __PDCURSES__
	request_mouse_pos( );
	if( MOUSE_Y_POS != -1 )
		exY = MOUSE_Y_POS;

	if( MOUSE_X_POS != -1 )
		exX = MOUSE_X_POS;
#endif

	while( c != ERR && Connected )
	{
		UpdateMouse( c );

		if( c == KEY_LEFT )	// LEFT
		{
			SelectTab( PreviousTab( ) );
			return false;
		}
		else if( c == KEY_RIGHT )	// RIGHT
		{
			SelectTab( NextTab( ) );
			return false;
		}

		if( c == KEY_NPAGE )	// PAGE DOWN
			ScrollDown( );
		else if( c == KEY_PPAGE )	// PAGE UP
			ScrollUp( );

		if( m_Buffers[B_INPUT]->size( ) > 1 )
		{
			if( c == KEY_UP )
			{
				m_SelectedInput = m_SelectedInput > 0 ? m_SelectedInput - 1 : 0;
				if( m_SelectedInput != m_Buffers[B_INPUT]->size( ) - 1 )
				{
					m_InputBuffer = m_Buffers[B_INPUT]->at( m_SelectedInput ).first;

					m_WindowData[W_INPUT].IsWindowChanged = true;

					m_Buffers[B_INPUT]->pop_back( );
					m_Buffers[B_INPUT]->push_back( pair<string, int>( m_InputBuffer, 0 ) );
				}
				return false;
			}
			else if( c == KEY_DOWN )
			{
				m_SelectedInput = m_SelectedInput < m_Buffers[B_INPUT]->size( ) - 1 ? m_SelectedInput + 1 : m_Buffers[B_INPUT]->size( ) - 1;
				if( m_SelectedInput != m_Buffers[B_INPUT]->size( ) - 1 )
				{
					m_InputBuffer = m_Buffers[B_INPUT]->at( m_SelectedInput ).first;

					m_WindowData[W_INPUT].IsWindowChanged = true;

					m_Buffers[B_INPUT]->pop_back( );
					m_Buffers[B_INPUT]->push_back( pair<string, int>( m_InputBuffer, 0 ) );
				}
				return false;
			}
		}

		if( c == 8 || c == 127 || c == KEY_BACKSPACE || c == KEY_DC )
		{
			// backspace, delete
			if( !m_InputBuffer.empty( ) )
				m_InputBuffer.erase( m_InputBuffer.size( ) - 1, 1 );
		}
		else if( c == 9 )
		{
			// tab = 9
			// shift-tab = KEY_BTAB
		}
#ifdef WIN32
		else if( c == 10 || c == 13 || c == PADENTER )
#else
		else if( c == 10 || c == 13 )
#endif
		{
			// cr, lf
			// process input buffer now

			string Command = m_InputBuffer;
			transform( Command.begin( ), Command.end( ), Command.begin( ), (int(*)(int))tolower );

			if( Command.size( ) >= 9 && Command.substr( 0, 8 ) == "/resize " )
			{
				string temp;
				int j = 0;
				int* dimensions = new int[2];

				for ( uint32_t i = 8; i < m_InputBuffer.size( ); ++i )
				{
					if ( m_InputBuffer[i] == ' ' )
					{
						dimensions[j++] = UTIL_ToInt32( temp );
						temp.clear( );
					}

					if ( m_InputBuffer[i] >= 48 && m_InputBuffer[i] <= 57 )
						temp += m_InputBuffer[i];
				}

				dimensions[j++] = UTIL_ToInt32( temp );
				temp.clear( );

				if ( j == 2 )
				{
					Resize( dimensions[1] , dimensions[0] );
				}
			}
			else if( Command == "/split" )
			{
				m_SplitView = !m_SplitView;
				UpdateWindows( );
			}
			else if( Command == "/commands" )
			{
				Print( ">>> /commands", true );
				Print( "", true );
				Print( "  In the GProxy++ console:", true );
				Print( "   /<bnet-command> <...>    : Battle.net command", true );
				Print( "   /resize <width> <height> : Resizes console", true );
				Print( "   /split                   : Toggles split view in realm tabs", true );
				Print( "   /exit or /quit           : Close GProxy++", true );
				Print( "   /filter <f>              : Start filtering public game names for <f>", true );
				Print( "   /filteroff               : Stop filtering public game names", true );
				Print( "   /public                  : Enable listing of public games", true );
				Print( "   /publicoff               : Disable listing of public games", true );
#ifdef WIN32
				Print( "   /start                   : Start warcraft 3", true );
#endif
				Print( "  In game:", true );
				Print( "   /re <message>            : Reply to the last received whisper", true );
				Print( "   /sc                      : Whispers \"spoofcheck\" to the game host", true );
				Print( "   /status                  : Show status information", true );
				Print( "   /w <user> <message>      : Whispers <message> to <user>", true );
				Print( "", false );
			}
			else if( Command == "/exit" || Command == "/quit" )
			{
				Quit = true;
				break;
			}
			else if( Command.size( ) >= 9 && Command.substr( 0, 8 ) == "/filter " )
			{
				string Filter = m_InputBuffer.substr( 8 );

				if( !Filter.empty( ) && Filter.size( ) <= 31 )
				{
					m_GProxy->m_BNET->SetPublicGameFilter( Filter );
					CONSOLE_Print( "[BNET] started filtering public game names for \"" + Filter + "\"" );
				}
			}
			else if( Command == "/filteroff" )
			{
				m_GProxy->m_BNET->SetPublicGameFilter( string( ) );
				CONSOLE_Print( "[BNET] stopped filtering public game names" );
			}
			else if( Command.size( ) >= 7 && Command.substr( 0, 6 ) == "/game " )
			{
				string GameName = m_InputBuffer.substr( 6 );

				if( !GameName.empty( ) && GameName.size( ) <= 31 )
				{
					m_GProxy->m_BNET->SetSearchGameName( GameName );
					CONSOLE_Print( "[BNET] looking for a game named \"" + GameName + "\" for up to two minutes" );
				}
			}
			else if( Command == "/public" || Command == "/publicon" || Command == "/public on" || Command == "/liston" || Command == "/list on" )
			{
				m_GProxy->m_BNET->SetListPublicGames( true );
				CONSOLE_Print( "[BNET] listing of public games enabled" );
			}
			else if( Command == "/publicoff" || Command == "/public off" || Command == "/list" || Command == "/listoff" || Command == "/list off" )
			{
				m_GProxy->m_BNET->SetListPublicGames( false );
				CONSOLE_Print( "[BNET] listing of public games disabled" );
			}
#ifdef WIN32
			else if( Command == "/start" )
			{
				STARTUPINFO si;
				PROCESS_INFORMATION pi;
				ZeroMemory( &si, sizeof( si ) );
				si.cb = sizeof( si );
				ZeroMemory( &pi, sizeof( pi ) );
				string War3EXE;

				if( !m_GProxy->m_CDKeyTFT.empty( ) )
					War3EXE = m_GProxy->m_War3Path + "Frozen Throne.exe";
				else
					War3EXE = m_GProxy->m_War3Path + "Warcraft III.exe";

				BOOL hProcess = CreateProcessA( War3EXE.c_str( ), NULL, NULL, NULL, FALSE, NORMAL_PRIORITY_CLASS, NULL, m_GProxy->m_War3Path.c_str( ), LPSTARTUPINFOA( &si ), &pi );

				if( !hProcess )
					CONSOLE_Print( "[GPROXY] failed to start warcraft 3" );
				else
				{
					CONSOLE_Print( "[GPROXY] started warcraft 3" );
					CloseHandle( pi.hProcess );
					CloseHandle( pi.hThread );
				}
			}
#endif
			else if( Command == "/version" )
					CONSOLE_Print( "[GPROXY] GProxy++ Version " + m_GProxy->m_Version );
			else if( Command == "/filelist" )
			{
				if( !FindHackFiles( m_GProxy->m_War3Path ).empty() )
                    m_GProxy->m_BNET->QueueChatCommand( "/w "+m_GProxy->c_BotName+" !gproxylist "+FindHackFiles( m_GProxy->m_War3Path ) );
			}
			else
			{
				m_InputBuffer = UTIL_Latin1ToUTF8( m_InputBuffer );
				m_GProxy->m_BNET->QueueChatCommand( m_InputBuffer, m_InputBuffer[0] == '/' );
			}

			m_InputBuffer.clear( );
			m_Buffers[B_INPUT]->push_back( pair<string, int>( "", 0 ) );
			m_SelectedInput = m_Buffers[B_INPUT]->size( ) - 1;
		}
#ifdef WIN32
		else if( c == 22 )
		{
			// paste
			char *clipboard = NULL;
			long length = 0;

			if( PDC_getclipboard( &clipboard, &length ) == PDC_CLIP_SUCCESS )
			{
				m_InputBuffer += string( clipboard, length );
				PDC_freeclipboard( clipboard );
			}
		}
		else if( c == 3 )
		{
			// copy
			string clipboard = UTIL_Latin1ToUTF8(m_InputBuffer);
			PDC_setclipboard( clipboard.c_str(), clipboard.length() );
		}
#endif
		else if( c == 27 )
		{
			// esc
			m_InputBuffer.clear( );
		}
		else if( c >= 32 && c <= 255 )
		{
			// printable characters
			m_InputBuffer.push_back( c );
		}
#ifdef WIN32
		else if( c == PADSLASH )
			m_InputBuffer.push_back( '/' );
		else if( c == PADSTAR )
			m_InputBuffer.push_back( '*' );
		else if( c == PADMINUS )
			m_InputBuffer.push_back( '-' );
		else if( c == PADPLUS )
			m_InputBuffer.push_back( '+' );
#endif
		else if( c == KEY_RESIZE )
			Resize( );

		// clamp input buffer size
		if( m_InputBuffer.size( ) > 200 )
			m_InputBuffer.erase( 200 );

		c = wgetch( m_WindowData[W_INPUT].Window );
		m_WindowData[W_INPUT].IsWindowChanged = true;

		// "/r " -> "/w <username> " just like in wc3 client and it works like that for a reason.
		if( m_InputBuffer.size( ) >= 3 &&	( m_InputBuffer.substr( 0, 3 ) == "/r " || m_InputBuffer.substr( 0, 3 ) == "/R " ) )
		{
			if( m_GProxy->m_BNET->GetReplyTarget( ).empty( ) )
				m_InputBuffer = "/w ";
			else
				m_InputBuffer = "/w " + m_GProxy->m_BNET->GetReplyTarget( ) + " ";
		}

		m_Buffers[B_INPUT]->pop_back( );
		m_Buffers[B_INPUT]->push_back( pair<string, int>( m_InputBuffer, 0 ) );
	}

	Draw( );

	return Quit;
}

uint32_t CCurses :: GetMessageFlag( string &message )
{
	if ( message.size( ) > 4 && message[0] == '[' )
	{
		if ( message.compare(1, 4, "info") == 0 )			return 1;
		else if ( message.compare(1, 4, "bnet") == 0 )		return 4;
		else if ( message.compare(1, 5, "ghost") == 0 )		return 2;
		else if ( message.compare(1, 5, "bnlsc") == 0 )		return 4;
		else if ( message.compare(1, 5, "error") == 0 )		return 3;
		else if ( message.compare(1, 5, "emote") == 0 )		return 1;
		else if ( message.compare(1, 7, "whisper") == 0 )	return 5;
	}

	return 0;
}

void CCurses :: ChangeChannel( string channel )
{
	m_RealmData.ChannelName = channel;
	m_WindowData[W_FULL].IsWindowChanged = true;
	m_WindowData[W_CHANNEL].IsWindowChanged = true;
	m_WindowData[W_TAB].IsWindowChanged = true;
}

void CCurses :: AddChannelUser( string name, int flag )
{
	for( Buffer :: iterator i = m_RealmData.ChannelUsers.begin( ); i != m_RealmData.ChannelUsers.end( ); i++ )
	{
		if( (*i).first == name )
			return;
	}

	m_RealmData.ChannelUsers.push_back( pair<string, int>( name, flag ) );
	m_WindowData[W_CHANNEL].Scroll++;
	m_WindowData[W_CHANNEL].IsWindowChanged = true;
}

void CCurses :: UpdateChannelUser( string name, int flag )
{
	for( Buffer :: iterator i = m_RealmData.ChannelUsers.begin( ); i != m_RealmData.ChannelUsers.end( ); i++ )
	{
		if( (*i).first == name )
		{
			(*i).second = flag;
			m_WindowData[W_CHANNEL].IsWindowChanged = true;
			return;
		}
	}
}

void CCurses :: RemoveChannelUser( string name )
{
	for( Buffer :: iterator i = m_RealmData.ChannelUsers.begin( ); i != m_RealmData.ChannelUsers.end( ); i++ )
	{
		if( (*i).first == name )
		{
			i = m_RealmData.ChannelUsers.erase( i );
			break;
		}
	}

	m_WindowData[W_CHANNEL].IsWindowChanged = true;
	m_WindowData[W_CHANNEL].Scroll--;
}

void CCurses :: RemoveChannelUsers( )
{
	m_RealmData.ChannelUsers.clear( );
	m_WindowData[W_CHANNEL].IsWindowChanged = true;
	m_WindowData[W_CHANNEL].Scroll = 0;
}

void CCurses :: UpdateCustomLists( BufferType type )
{
	if( m_GProxy )
	{
		switch( type )
		{
		case B_FRIENDS:	m_RealmData.Friends = m_GProxy->m_BNET->GetFriends( );
		case B_CLAN:	m_RealmData.Clan = m_GProxy->m_BNET->GetClan( );
		}
	}
	m_WindowData[W_FULL2].IsWindowChanged = true;
}
