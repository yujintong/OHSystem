
#ifndef USERINTERFACE_H
#define USERINTERFACE_H

#include "gproxy.h"

#ifdef WIN32
 #include "curses.h"
#else
 #include <curses.h> // NOTE: Change this to xcurses.h if necessary
#endif

#define A_WHITE		( COLOR_PAIR(0) )
#define A_GREEN		( COLOR_PAIR(1) )
#define A_RED			( COLOR_PAIR(2) )
#define A_CYAN		( COLOR_PAIR(3) )
#define A_YELLOW	( COLOR_PAIR(4) )
#define A_BLUE		( COLOR_PAIR(5) )

#define A_BWHITE	( A_BOLD | COLOR_PAIR(0) )
#define A_BGREEN	( A_BOLD | COLOR_PAIR(1) )
#define A_BRED		( A_BOLD | COLOR_PAIR(2) )
#define A_BCYAN		( A_BOLD | COLOR_PAIR(3) )
#define A_BYELLOW	( A_BOLD | COLOR_PAIR(4) )
#define A_BBLUE		( A_BOLD | COLOR_PAIR(5) )

#define MAX_BUFFER_SIZE 512
#define SCROLL_VALUE 2

typedef vector<pair<string, int> > Buffer;

// Buffer type
enum BufferType
{
	B_ALL = 0,
	B_MAIN,
	B_FRIENDS,
	B_CLAN,
	B_REALM,
	B_CHANNEL = 15,
	B_INPUT,
	B_TAB,
	B_GAMES,
	B_GAME = 18
};

// Server
struct SRealmData
{
	string RealmAlias;
	string ChannelName;
	Buffer ChannelUsers;
	Buffer Messages;
	Buffer Friends;
	Buffer Clan;
	Buffer Games;
};

// Window type
enum WindowType
{
	W_TAB = 0,
	W_FULL,
	W_FULL2,
	W_UPPER,
	W_LOWER,
	W_CHANNEL,
	W_INPUT,
	W_HLINE,
	W_VLINE
};

// Window
struct SWindowData
{
	WINDOW *Window;
	string Title;
	bool IsWindowChanged;
	int Scroll;
};

// Tab type
enum TabType
{
	T_MAIN = 0,
	T_LIST,
	T_REALM,
	T_GAMES
};

// Tab
struct STabData
{
	string name;
	TabType type;
	BufferType bufferType;
	bool IsTabSelected;
	WindowType windowType;
};

//
// CCurses
//

class CCurses
{
private:
	// Pointer to CGProxy
	CGProxy *m_GProxy;

	// Buffers
	vector<Buffer *> m_Buffers;

	// RealmData
	SRealmData m_RealmData;

	// Windows
	vector<SWindowData> m_WindowData;

	// Tabs
	vector<STabData> m_TabData;

	// Input
	string m_InputBuffer;

	// Drawing
	void SetAttribute( SWindowData &data, string message, int flag, BufferType type, bool on );
			// on==true (before message), on==false (after message)

	void Draw( );													// draws everything
	void DrawTabs( WindowType type );								// draws tabs
	void DrawWindow( WindowType wType, BufferType bType );			// draws a window
	void DrawListWindow( WindowType wType, BufferType bType );		// draws a list (horizontal)
	void DrawListWindow2( WindowType wType, BufferType bType );		// draws a list (vertical)
	void DrawListWindow3( WindowType wType, BufferType bType );		// draws a list (for games)
	void DrawHorizontalLine( WindowType type );						// draws a horizontal line
	void DrawVerticalLine( WindowType type );						// draws a vertical line

	void Resize( int y, int x );									// resizes term
	void Resize( );													//
	
	// Tab
	void AddTab( string nName, TabType nType, uint32_t nId, BufferType nBufferType, WindowType nWindowType ); // adds tab
	void SelectTab( uint32_t n );	// unselects old tab and selects new tab

	// Window
	void UpdateWindow( WindowType type );											// updates window
	void ClearWindow( WindowType type );											// clears window
	void UpdateWindows( );															// updates windows

	void CompileList( BufferType type );											// compiles list
	void CompileLists( );															// compiles lists
	void CompileFriends( );															// compiles friends-list
	void CompileClan( );															// compiles clan-list
	void CompileGames( );															// compiles game-list

	// Mouse
	void UpdateMouse( int c );														// updates mouse clicks/scrolls

	// Misc
	uint32_t GetMessageFlag( string &message );										// returns message flag, e.g. "bnet" = 4 which makes it colored yellow
	void UpdateCustomLists( BufferType type );										// updates friends/clan list

	uint32_t m_SelectedTab;
	uint32_t m_SelectedInput;

	uint32_t m_ListUpdateTimer;														// timer for updating friends/clan list
	bool m_SplitView;																// is split view enabled for realm tab?
	int m_ListType;																	// list type (horizontal == 0 / vertical != 0)

	void ScrollDown( );
	void ScrollUp( );

	uint32_t NextTab( );
	uint32_t PreviousTab( );

	uint32_t m_InitialUpdate;														// fixes channel users not showing up
	int exY, exX;																	// fixes scrolling

public:
	CCurses( int nTermWidth, int nTermHeight, bool nSplitView, int nListType );
	~CCurses( );

	void SetGProxy( CGProxy * nGProxy );											// important for accessing gproxy
	void Print( string message, bool toMainBuffer );
	bool Update( );																	// updates user interface, returns true when quitting

	// Channel / Custom list
	void ChangeChannel( string channel );
	void AddChannelUser( string name, int flag );
	void UpdateChannelUser( string name, int flag );
	void RemoveChannelUser( string name );
	void RemoveChannelUsers( );
};

#endif