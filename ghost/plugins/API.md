## API for plugins

#### Description for the following content:
- this points are the events and when they are fired (this is the description of the event)
  - this is a value which is send on the event
  - this is another value


####Registered Handlers:
- ```StartUp``` (is executed when the bot is starting up)
  - CFG
- ```GHostStarted``` (is executed when the bot has been started)
  - cGHost
- ```RCONCommand``` (is executed when the bot recieves a rcon command)
  - Command + Payload
  - hasActiveBNetRealm

---

- ```BNetIPBanned``` (is executed when a realm is IP Banned from the bnet)
  - cBNet
- ```BNetTimeout``` (is executed when the connection to a realm is timing out)
  - cBNet
- ```BNetLogged``` (is executed when the ghost successfully logging into the bnet)
  - cBNet
- ```BNetCommand``` (is executed when a player using a bnet command)
  - cBNet
  - User
  - Command
  - Payload

---

- ```PlayerJoined``` (is executed when a player join the lobby)
  - cBaseGame
  - playername
- ```PlayerDeleted``` (is executed when a player leave the game)
  - cGame
  - player (ref)
- ```PlayerLoaded``` (is executed when a player finished loading)
  - cBaseGame
  - name
  - loading time
- ```PlayerChat``` (is executed when a player sending a message)
  - cBasegame
  - name
  - message
  - flag (42 if lobby)
- ```GameCommand``` (is executed when a player using a command)
  - cGame
  - player
  - Command
  - Payload
- ```GameStarted``` (is executed when a game is starting)
  - cBaseGame
- ```GameLoaded``` (is executed when a game is loaded)
  - cBaseGame
- ```GameEnded``` (is executed when a game ends)
  - cGame
---

- ```DotAData``` (is executed when the dota map sends data to the bot)
  - cStatsdota
  - cGame
  - DataString
  - KeyString
  - ValueInt

- ```DotAGlobal``` (is executed when the dota map sends global data to the bot)
  - cStatsDota
  - cGame
  - DataString
  - KeyString
  - ValueInt
