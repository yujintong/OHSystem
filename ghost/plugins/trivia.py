import host

t_enabled = False
t_difficulty = 1

def init( ):
	print("*********************************")
	print("**   initalized trivia plugin  **")
	print("*********************************")

	host.registerHandler("GameCommand", onCommand)

def onCommand(cGame, player, command, payload):
	global t_enabled, t_difficulty
	if command in ["trivia"]:
		ppattern = payload.split(" ")

		if ppattern[0] == "on":
			if t_enabled:
				cGame.sendAllChat( "Trivia is already enabled" )
			else:
				cGame.sendAllChat( "[Trivia] Enabled Trivia game." )
				trivia_enabled = True
		elif ppattern[0] == "off":
			if t_enabled:
				cGame.sendAllChat( "[Trivia] Disable Trivia game." )
				trivia_enabled = False
			else:
				cGame.sendAllChat( "Trivia is already disabled" )
		elif ppattern[0] == "difficulty" and len(ppattern) == 2:
			t_difficulty = ppattern[1]
			diff = "Easy"
			if t_difficulty == 2:
				diff = "Hard"
			elif t_difficulty == 1:
				diff = "Medium"
			cGame.sendAllChat("[Trivia] Set difficulty to ["+diff+"]")
			
			
