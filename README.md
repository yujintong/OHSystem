# CHANNELCOMMNADS

### RCON COMMANDS
**rcon commands include always two types of players:**
* _targetplayer_ is the target/victim
* _usedplayer_ is the player who used the command

**un-/muting a player in a game, the mute is permanently and for the the complete chat**
* !rcon mute _usedplayer_ _targetplayer_
* !rcon unmute _usedplayer_ _targetplayer_

**kick a player, probably will add him to the banlist if he/she was not kicked within the last 5 minuts of the game**
* !rcon kick _usedplayer_ _targetplayer_

**saylobby allow to send something to the allchat of the current hosted lobby**
* !rcon saylobby _usedplayer_ _message_

**saygame allow to send a message to a specific game which is hosted, it is splited from the normal/default command, it allow us to use the hostcounter/gameid as target**
* !rcon saygame _usedplayer_ _hostcounter_ _message_

**lobbyteam allow to send a chatmessage to the current hosted lobby specidfied to a team**
**team should be 1 for sentinel and 2 for scourge**
* !rcon lobbyteam _usedplayer_ _team_ _message_

**gameteam allow to send a chatmessage to a current running game specified to a team**
**team should be 1 for sentinel and 2 for scourge**
**as on saygame we using the hostcounter/gameid, not the gamenumber**
* !rcon gameteam _usedplayer_ _hostcounter_ _team_ _message_

**from allow to display the current countriescodes of all players in the game**
* !rcon from

### OTHER CHANNEL COMMANDS
**vouch allow to vouch a registered&confirmed player, he is able to join then also if he plays with a banned counter or if the garena players are denied and he is a garena player etc.**
* !vouch _playername_

**setpermission is a very powerful command and should be used by rootadmins only. The command set the userlevel and is a general command overall connections from the bot**
**the realm is optional, without a realm the current realm where the usedplayer is online will be taken**
**this command works only if a user is registered&confirmed**
* !setpermission _playername_ _level_ _realm_
* !setp _playername_ _level_ _realm_
* !sep _playername_ _level_ _realm_

**update allow a manual activation of the update script on the bot side**
* !update

**permission allow to see a current level of a player, if playername isnt set it will display the usedplayer permission**
* !permission _playername_
* !perm _playername_

**checkpp will check the penality points of a player, if playername isnt set it will display the usedplayer penality points**
* !checkpp _playername_
* !pp _playername_

**ppadd/punish allow to punish a player in the way of adding him penality points. It is a very powerful commands because it can allow long ban ranges for a player.**
* !ppadd _playername_ _amount_ _reason_
* !punish _playername_ _amount_ _reason_

**removepp allow to remove specific penality points or all, head up to the usage**
**the amount does specifiy the maximum pp's which should be removed**
**in combination with the reason it will remove the first pp's with this reason until the amount has reached**
**by setting the amount to 0 it will remove all ppoints which are related to a reason**
**by setting the amount to 0 and dont leave the reason empty, all pp's will be removed**
* !removepp _playername_ _amount_ _reason_
* !rpp _playername_ _amount_ _reason_

**addban is a command to permanently ban a user from the bot**
* !addban _playername_ _reason_
* !ban _playername_ _reason_

**iprangeban allows to ban a iprange, if a user/admin/owner is not familair with ipranges he should avoid the usage of it.**
* !iprangeban _ip_
* !iprangeadd _ip_
* !irb _ip_

**tempban is a command to ban a player for a specific time, the usage is simple, the command get the time over an amount and suffix**
**amount should be related to the choosen suffix**
**suffix can be: h, hour, hours, d, day, days, w, week, weeks, m, month, months**
* !tempban _playername_ _amount_ _suffix_ _reason_
* !tban _playername_ _amount_ _suffix_ _reason_
