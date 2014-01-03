  gameTimer = null;
  GameList = null;
  
  var FirstRefresh = 10000;
  var RefreshTime = 30000;
  
  GameList = setTimeout( function(){ ShowGameList() }, FirstRefresh );
  
/*
$( document ).ready(function() {
   GameList = setTimeout( function(){ ShowGameList() }, FirstRefresh );
});
*/

  function ShowGameList() {
  
   setTimeout( function(){ ShowGameList() }, RefreshTime );
   
   document.getElementById("gamerefresher").innerHTML = '<img class="imgvalign" width="16" height="16" src="img/loader-small.gif" alt="" />';
   
   currentGameID = document.getElementById("selectedGameID").value;
   
   var $jqry = jQuery;
   $jqry.ajax({
                    type: "POST",
                    url: "live.php",
                    data: "refresh=gamelist&gameid="+currentGameID,
                    success: function(msg){
					
                    document.getElementById("AllLiveGames").innerHTML = msg;
					
                    },
                    error: function(msg){
                    document.getElementById("AllLiveGames").innerHTML = msg;
                    }
                });
    
  }

  function show_live_game(botID, chatID, gamename, click) {
  jQuery.noConflict();

  document.getElementById("selectedBot").value = botID;

  AutoRefreshGames(botID, chatID, 5000, gamename, 0);
  
  document.getElementById("InfoLoader").innerHTML = '<img class="imgvalign" width="160" height="24" src="img/loader.gif" alt="" />';
  
  var html = document.getElementById("ShowData").innerHTML;
  
  var lastGameID = parseInt( document.getElementById("lastID").innerHTML );

   var $jq = jQuery;
   $jq.ajax({
                    type: "POST",
                    url: "live.php",
                    data: "botID="+botID+"&lastID="+lastGameID+"&chatID="+chatID+"&cl="+click,
                    success: function(msg){
					
                    if (msg!="") {
					
					var alldata = msg.split("|	|");
					
					var message = alldata[0];
					var lastID = alldata[1];
					var playerlist = $jq.trim(alldata[2]);

					output = message + html;
					document.getElementById("ShowData").innerHTML = output;
					if (playerlist!="updating") document.getElementById("PlayerList").innerHTML = playerlist;
					if (lastID!="undefined") document.getElementById("lastID").innerHTML = (lastID);
					}
					
					gn = gamename+'<input type="hidden" id="gninfo" value="'+gamename+'" /> <input onclick="ToClipboard(\'gninfo\')" type="button" value="Copy" class="menuButtons"  />';
					
					if (gamename!="undefined") document.getElementById("InfoGameName").innerHTML = gn;
					document.getElementById("InfoLoader").innerHTML = "";
					
                    },
                    error: function(msg){
                    document.getElementById("ShowData").innerHTML = msg + html;
					$("#ShowData").append(msg);
					document.getElementById("InfoLoader").innerHTML = "";
                    }
                });
  }
  
    
  function ToClipboard (id) {
  text = document.getElementById(id).value ;
  window.prompt ("Press Ctrl+C to copy on clipboard", text);
  }
  
  function AutoRefreshGames(botID, chatID, delayTime, gamename, click) {
    gameTimer = setTimeout(function(){ show_live_game(botID, chatID, gamename, click) }, delayTime);
	gn = gamename+'<input type="hidden" id="gninfo" value="'+gamename+'" /> <input onclick="ToClipboard(\'gninfo\')" type="button" value="Copy" class="menuButtons"  />';
	if (gamename!="undefined") document.getElementById("InfoGameName").innerHTML = gn;
	else document.getElementById("InfoGameName").innerHTML = "";
  }
  
    function clearLiveGamesTimer(botID, chatID, gamename) {
	
	PrevGameId = document.getElementById("selectedGameID").value;
	var $jq = jQuery;
	
	if (parseInt(PrevGameId)>=1) {
	$jq("#b"+PrevGameId).removeAttr("style");
	$jq("#b"+PrevGameId).css("color","");
	$jq("#b"+PrevGameId).css("font-weight","");
	
	//$jq("#b"+chatID).css("color","FFE0B8 !important");
	$jq('#b'+chatID).attr('style', 'color:#8D1D1D !important');
	$jq("#b"+chatID).css("font-weight","bold");
	}
	
	document.getElementById("ShowData").innerHTML = "";
	document.getElementById("PlayerList").innerHTML = "";
	document.getElementById("lastID").innerHTML = 0;
	document.getElementById("selectedGameID").value = chatID;
	document.getElementById("WinChanceWrapper").innerHTML = "";
	gn = gamename+'<input type="hidden" id="gninfo" value="'+gamename+'" /> <input onclick="ToClipboard(\'gninfo\')" type="button" value="Copy" class="menuButtons"  />';
	if (gamename!="undefined") document.getElementById("InfoGameName").innerHTML = gn;
	else document.getElementById("InfoGameName").innerHTML = "";
	document.getElementById('b'+chatID).disabled = true;
	setTimeout( function() { enableStartButton(chatID) }, 10000 );
	
    clearTimeout(gameTimer);
    AutoRefreshGames(botID, chatID, 1, gamename, 1);
   }
   
   function enableStartButton(chatID) {
   $jq('#b'+chatID).removeAttr('disabled');
   }
   
   function enableDisabledButton(buttonID) {
   $jq('#'+buttonID).removeAttr('disabled');
   }
   
   function OS_WinChance() {
    
	  document.getElementById("WinChanceLoader").innerHTML = '<img class="imgvalign" width="160" height="24" src="img/loader.gif" alt="" />';
	  gameID = document.getElementById("selectedGameID").value;
	  
	  document.getElementById('winchance').disabled = true;
	  setTimeout( function() { enableDisabledButton('winchance') }, 3000 );
	  
   var $js = jQuery;
   $js.ajax({
                    type: "POST",
                    url: "live.php",
                    data: "winchance="+gameID,
                    success: function(msg){
					
                    document.getElementById("WinChanceWrapper").innerHTML = msg;
					document.getElementById("WinChanceLoader").innerHTML = '';
                    },
                    error: function(msg){
                    document.getElementById("WinChanceWrapper").innerHTML = msg;
					document.getElementById("WinChanceLoader").innerHTML = '';
                    }
                });
	  
   }
   
   function OS_CheckBotOnline() {
	
   setTimeout( function() { OS_CheckBotOnline() }, 30000 );	
   var $js = jQuery;
   $js.ajax({
                    type: "POST",
                    url: "live.php",
                    data: "status=1",
                    success: function(msg){
					document.getElementById("BotStatus").innerHTML = msg;
                    },
                    error: function(msg){
                    document.getElementById("BotStatus").innerHTML = msg;
                    }
                });
	  
   }
   
   function OS_AdminExec(command, user) {
   
    var botID = document.getElementById("selectedBot").value;
   
	var $js = jQuery;
   $js.ajax({
                    type: "POST",
                    url: "live.php",
                    data: "command="+command+"&user="+user+"&botID="+botID,
                    success: function(msg){
					document.getElementById("debug_").innerHTML = msg;
                    },
                    error: function(msg){
					document.getElementById("debug_").innerHTML = msg;
                    }
                });
   }
   
   function OS_AdminRconExec(type) {
   
    var botID  = document.getElementById("selectedBot").value;
    var gameID = document.getElementById("selectedGameID").value;
    var com   = document.getElementById("botCommand").value;
	var type   = document.getElementById("command_type").value;
    
	//alert(botID+":"+gameID+":"+com+":"+type);

	var $js = jQuery;
   $js.ajax({
                    type: "POST",
                    url: "live.php",
                    data: "rcon="+type+"&com="+com+"&gameID="+gameID+"&botID="+botID,
                    success: function(msg){
					document.getElementById("debug_").innerHTML = msg;
					document.getElementById("botCommand").value = "";
                    },
                    error: function(msg){
					document.getElementById("debug_").innerHTML = msg;
					document.getElementById("botCommand").value = "";
                    }
                });
   }
   
   function OS_GetGameIDRcon(session){
     var gameID = document.getElementById("selectedGameID").value;
	 CommandHelp('!rcon saygame '+session+' '+gameID+' ');
	 
   }
   
   function CloseRcon() {
    document.getElementById("debug_").innerHTML = "";
   }
   
   function OS_Popup(url) {
	popupWindow = window.open(
		url,'popUpWindow','height=300,width=640,left=10,top=10,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes')
}

function OS_CurrentGames() {

  document.getElementById("CGRefresh").innerHTML = '<img class="imgvalign" width="16" height="16" src="img/loader-small.gif" alt="" />';
  
  setTimeout( function(){ OS_CurrentGames() }, 5000 );
  
  	var $js = jQuery;
   $js.ajax({
                    type: "POST",
                    url: "live.php",
                    data: "currentgames=1",
                    success: function(msg){
					document.getElementById("currentgames").innerHTML = msg;
					document.getElementById("CGRefresh").innerHTML = '';
                    },
                    error: function(msg){
					document.getElementById("currentgames").innerHTML = msg;
					document.getElementById("CGRefresh").innerHTML = '';
                    }
                });
				
}