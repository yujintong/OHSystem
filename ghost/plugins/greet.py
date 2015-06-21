import host

def init( ):
	host.registerHandler( "PlayerJoined", join, True )
	print( "** Loaded greet module **" )
	
def join(baseGame, username):
	baseGame.sendAllChat( "Welcome user " + username + " to our game!" )
	print( username+" joined the game!" )
