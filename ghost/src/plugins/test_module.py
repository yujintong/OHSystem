import host

def init( ):
    host.registerHandler( "StartUp", TestFunc1 )
    host.registerHandler( "StartUp", TestFunc2, True )

def TestFunc1( CFG ):
    print( "This is an example module text print!" )

def TestFunc2( CFG ):
    raise RuntimeError("Throw an error please")

