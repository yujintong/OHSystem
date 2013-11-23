<?php
  if ( !isset($website) ) {header('HTTP/1.1 404 Not Found'); die; }
  
   function OS_ParseGameLog($log_data) {
    /*
//WRONG !!!!
	1 - LobbyChatLog
	2 - BotLobbyLog
	3 - BotGameLog
	4 - GameInfo (k/d/a/t/r)
	5 - GameChatLog
	6 - Channel, not added yet O_o

	  0 - GameInfo (k/d/a/t/r)
      1 - GameChatLog
      2 - LobbyChatLog
      3 - BotGameLog
      4 - BotLobbyLog
	  5 - Channel
    */
	
	## k - KILL ##
	## s - SUICIDE ##
	## sek - SENT KILL ##
	## sck - SCOU KILL ##
	## a - ASSIST  ##
	## tok - TOWER KILL ##
	## setk - SENT TOWER KILL ##
	## sctk - SCOU TOWER KILL ##
	## rk - RAX KILL ##
	## serk - SENT RAX KILL ##
	## scrk - SCOU RAX KILL ##
	## lu - LEVEL UP ##
	## sw - SWAP ##
	## hp - HERO PICK ##
	## ftk - FROZEN THRONE GOT HURT ##
	## wtk - WORLD TREE GOT HURT ##
	## ally - ALLY CHAT ##
	## all - ALL CHAT ##
	## l - LOBBYLOG ##
	## bgm - BOTGAMELOGDATA ##
	## blm - BOTLOBBYLOGDATA ##
	## um - USER MESSAGE ##
	## uc - USER JOIN ##
	## left - USER LEFT ##
	## bm - BOT MESSAGE ##
	/* NEW FIELDS */
	## rosh - ROSHAN (Killer = Player1, Sentinel or Scourge ) ##
	## ap - AEGIS PICK ##
	## ad - AEGIS DROP ##
	## mode -  MODE? ##
	## rs - STORED A RUNE IN A BOTTLE ##
	## ru - USED A RUNE ##
	## inv - ITEM ## - too many infos (SPAM CHAT) - deprecated
	## fb - FIRST BLOOD
	## dk, tk, ul, rk - DOUBLE KILL, TRIPPLE KILL, ULTRA KILL, RAMPAGE
	
	global $lang;
	
	global $LiveShowAegis;
	global $LiveShowSwaps;
	global $LiveShowHeroPicks;
	global $LiveShowPlayerLevels ;
	global $LiveShowKills;
	global $LiveShowAssists;
	global $LiveShowSuicides;
	global $LiveShowRunes;
	global $LiveShowTowerKills;
	global $LiveShowRoshanKill;
	
	$DataArray = array_reverse ( explode("\n", $log_data) );
	$Final = "";
	$ASSISTS = "";
	$GameType = "";

	$OUTPUT = "";
	
	$W = 24; $H = 24; //Icon dimensions 24 X 24
	
	foreach ($DataArray as $Data){
	
	$HTML = "";
	$OUTPUT = "";
	
	$Log = explode("	", $Data);
	$type = $Log[0]; 
	$p1 = ""; $p2 = "";
//	if (isset($Log[1]) AND $Log[1]!= '-' ) $ChatID = $Log[1]; else $ChatID = "";
	if (isset($Log[1]) AND $Log[1]!= '-' ) $event = $Log[1]; else $event = "";
	if (isset($Log[2]) AND $Log[2]!= '-' )
	     if ( $Log[2] == "Sentinel") $p1 = "Sentinel";
	else if ( $Log[2] == "Scourge" ) $p1 = "Scourge";
	else $p1 = "<a target='_blank' href='".OS_HOME."?u=".$Log[2]."'>".$Log[2]."</a>"; 
	
	if (isset($Log[3]) AND $Log[3]!= '-' )
	     if ( $Log[3] == "Sentinel") $p2 = "Sentinel";
	else if ( $Log[3] == "Scourge" ) $p2 = "Scourge";
	else $p2 = "<a target='_blank' href='".OS_HOME."?u=".$Log[3]."'>".$Log[3]."</a>"; 
	
	if (isset($Log[4]) AND $Log[4]!= '-' ) $h1 = $Log[4]; else $h1 = "";
	if (isset($Log[5]) AND $Log[5]!= '-' ) $h2 = $Log[5]; else $h2 = "";
    if (isset($Log[6]) AND $Log[6]!= '-' ) $GameTime = "<span class='GameTimeLog'>".$Log[6]."</span>"; else $GameTime = "";
	if (isset($Log[7]) AND $Log[7]!= '-' ) $Message = OS_StrToUTF8(trim($Log[7])); else $Message = "";
	
	//SKIPPING playerlist AND type-6 (kda and hid)
	if ( $event!="pl" AND $type!=6 AND $event!="whisp") {
	//Get Hero ICONS
	if ( !empty($h1) AND $h1!='-' ) $h1 = '<img class="imgvalign" src="'.OS_HOME.'img/heroes/'.strtoupper($h1).'.gif" alt="" width="'.$W.'" />';
	else $h1 ="";
	if ( !empty($h2) AND $h2!='-' ) $h2 = '<img class="imgvalign" src="'.OS_HOME.'img/heroes/'.strtoupper($h2).'.gif" alt="" width="'.$H.'" />';
	else $h2 ="";

	//Get log type
	if ( $type == 4  AND $event!="k"  ) $GameType = ''.$lang["gl_system"].'';
	if ( $type == 1) $GameType = ''.$lang["gl_lobby"].'';
	if ( $type == 3 OR $type == 2 ) $GameType = $lang["gl_bot"];
	
	if ( $type == 5 ) $GameType = "[".strtoupper($event)."]";
	
	$DeadHero = '<span class="killX">X</span>';
	$DeadHero = '<img class="killX" src="'.OS_HOME.'img/loser.png" width="20" height="20" />';
	
	if( $event == "k" OR $event == "sek" OR $event == "sck") $KillCSS = " gl_killbackground "; else $KillCSS ="";
	
	if ( $type == 3 OR $type == 2 ) $KillCSS = " gl_botmessage";
	
	$s = "<div class='LiveGamesSingleMessage".$KillCSS."'>";
	$e = "</div>";
	
	if ( $type == 1 AND !empty($Message) ) {
	$OUTPUT = "$s $GameTime $GameType $h1 <b>$p1</b> $h2 <b>$p2</b> $Message $e";
	
	//Don't display user this command!!!
	// Grief: !pw wont be displayed, we don't send any datas from it, we breack it and leave it local. Reg isnt a gamecommand, only possible by whisp ;)
	// Neubivljiv: IT WAS LONG TIME AGO, WHEN COMMAND IS DISPLAYED TO ALL USERS...
	if ( substr($Message, 0, 3) == "!pw" ) $OUTPUT = "";
	if ( substr($Message, 0, 3) == "!reg" ) $OUTPUT = "";
	
	}
	
	if ( ($type == 4) /*AND !empty($p1)*/ ) {

	 if( $event == "mode") $OUTPUT = "$s<span class='gl_gamemode'>$GameTime $GameType ".$lang["gl_mode"]." $Message.</span>$e";
	  if( !empty($p1) ) {

	   //FIRST BLOOD
	   if ( $event == "fb" ) {
	   //Bugged heroe icons...
	    $OUTPUT = "$s<span class='gl_playerkill'>$GameTime $GameType <b>$p1</b> <!--$h1--> ".$lang["gl_killed"]." <!--$h2--> $DeadHero <b>$p2</b> for first blood</span>$e";
	   }
	   
	   //OK...HERE IS ITEM PICK - DEPRECATED . TOO MANY SPAMS
	   if ( $event == "inv" AND isset($SHOW_ITEMS) ) {
	   
	   //SHOULD BE LOADED ONLY ONCE --->> TO DO ->>> TOO LAZY NOW
	   $ItemFilePath = "inc/cache/ItemList.php";
	   if ( !file_exists("inc/cache/ItemList.php") ) $ItemFilePath = "../inc/cache/ItemList.php";
	   
	   if ( file_exists($ItemFilePath) ) {
	   include($ItemFilePath);
	   
	   foreach ( $ItemsList as $Item ) {
	     if ( strtolower($Item["itemid"]) == strtolower($Message)) $icon = $Item["icon"];
	   }
	   
	   if ( isset($icon) )
	   $OUTPUT = "$s<span class='gl_aegis'>$GameTime $GameType $h1 <b>$p1</b> ".$lang["gl_item"]." <img src='".OS_HOME."img/items/$icon' width='".$W."' height='".$H."' class='imgvalign' /></span>$e";
	   
	   }
	   
	   }

	   //AEGIS
       if( $event == "ap" AND $LiveShowAegis == 1)  $OUTPUT = "$s<span class='gl_aegis'>$GameTime $GameType $h1 <b>$p1</b> ".$lang["gl_picked"]." <img src='".OS_HOME."img/items/BTNArcaniteArmor.gif' width='".$W."' height='".$H."' class='imgvalign' /> Aegis.</span>$e";
       if( $event == "ad" AND $LiveShowAegis == 1)  $OUTPUT = "$s $GameTime $GameType $h1 <b>$p1</b> lost <img src='".OS_HOME."img/items/BTNArcaniteArmor.gif' width='".$W."' height='".$H."' class='imgvalign' /> Aegis.$e";
	   
	   //SWAP
       if( $event == "sw" AND $LiveShowSwaps == 1)  $OUTPUT = "$s<span class='gl_swap'>$GameTime $GameType $h1 <b>$p1</b> ".$lang["gl_swap"]." $h2 <b>$p2</b></span>$e";
	   
	   //GAME MODE

	   //HERO PICK AND PLAYER LEVEL
       if( $event == "hp" AND $LiveShowHeroPicks == 1)  $OUTPUT = "$s<span class='gl_heropick'>$GameTime $GameType <b>$p1</b> ".$lang["gl_heropick"]." $h1.$e";
       if( $event == "lu" AND $LiveShowPlayerLevels == 1 )  $OUTPUT = "$s<span class='gl_playerlevel'>$GameTime $GameType $h1 <b>$p1</b> ".$lang["gl_level"] ." $Message.</span>$e";
	   
	   
	   //KILLS + ASSISTS
	   if ( $LiveShowKills == 1 )
       if( $event == "k" OR $event == "sek" OR $event == "sck" OR $event == "a" ) {
  
	   if( $event == "k" OR $event == "sek" OR $event == "sck") {
	   
	   if ( !empty($ASSISTS) ) { 
	   $txt = " <div class='gl_assistWrap'><b>".$lang["gl_assists"]."</b> <span class='gl_assist'>"; 
	   $ASSISTS = substr($ASSISTS, 0, strlen($ASSISTS)-2 )."</span></div>";
	   }
	   else $txt = "";
	   
	   $OUTPUT.= "$s<span class='gl_playerkill'>$GameTime $GameType <b>$p1</b> $h1 ".$lang["gl_killed"]." $h2 $DeadHero <b>$p2</b></span>. $txt".$ASSISTS."$e";
	   $ASSISTS = "";
	   }
	   
	   if( $event == "a" AND $LiveShowAssists==1) $ASSISTS.= " $h1 <b>$p1</b>, ";

       }
        
	   //SUICIDE, RUNES
	   if( $event == "s" AND $LiveShowSuicides == 1)  $OUTPUT = "$s<span class='gl_suicide'>$GameTime $GameType $h1 <b>$p1</b> ".$lang["gl_suicide"]."</span>$e";
       if( $event == "ru" AND $LiveShowRunes == 1) $OUTPUT = "$s<span class='gl_rune'>$GameTime $GameType $h1 <b>$p1</b> ".$lang["gl_used"]." $Message ".$lang["gl_rune"]."</span>$e";
       if( $event == "rs" AND $LiveShowRunes == 1) $OUTPUT = "$s<span class='gl_rune'>$GameTime $GameType $h1 <b>$p1</b> ".$lang["gl_stored"]." $Message ".$lang["gl_rune"]."</span>$e";
	   
	   
	   //TOWER
	   if ( $LiveShowTowerKills == 1 )
       if( $event == "tok" OR $event == "setk" OR $event == "sctk" ) {
		$OUTPUT = ""; 
		$icon = "<img src='".OS_HOME."img/setower.gif' width='".$W."' height='".$H."' class='imgvalign' />";
		if ( $event == "sctk" ) $icon = "<img src='".OS_HOME."img/setower.gif' width='".$W."' height='".$H."' class='imgvalign' />";
		if ( $event == "setk" ) $icon = "<img src='".OS_HOME."img/sctower.gif' width='".$W."' height='".$H."' class='imgvalign' />";
		
		$OUTPUT = "$s<span class='gl_towerkill'>$GameTime $GameType $h1 <b>$p1</b> ".$lang["gl_killed_t"]." $icon $DeadHero <b>$p2</b> ".$lang["gl_tower"] ." </span>$e";
		}
		
        //RAX		
        if( $event == "rk" OR $event == "serk" OR $event == "scrk" ) {
		$OUTPUT = "";
		$icon = "<img src='".OS_HOME."img/semrax.gif' width='".$W."' height='".$H."' class='imgvalign' />";
		if ( $event == "serk" ) $icon = "<img src='".OS_HOME."img/semrax.gif' width='".$W."' height='".$H."' class='imgvalign' />";
		if ( $event == "scrk" ) $icon = "<img src='".OS_HOME."img/scmrax.gif' width='".$W."' height='".$H."' class='imgvalign' />";
		
        $OUTPUT = "$s<span class='gl_raxkill'>$GameTime $GameType $h1 <b>$p1</b> ".$lang["gl_killed_t"]." $icon $DeadHero <b>$p2</b> ".$lang["gl_rax"]."</span>$e";
		
		}
		//WORLD TREE
		if( $event == "ftk" OR $event == "wtk" )
		$OUTPUT = "$s<span class='gl_worldtree'>$GameTime $GameType <b>$p2</b> ".$lang["gl_attack"]." $Message%.</span>$e";
		//ROSHAN KILL
        if( $event == "rosh" AND $LiveShowRoshanKill == 1)
        $OUTPUT = "$s<span class='gl_roshan'>$GameTime $GameType <img src='".OS_HOME."img/roshan.gif' width='".$W."' height='".$H."' class='imgvalign' /> <b>Roshan</b> ".$lang["gl_killed_by"]." <b>$p1</b>.</span>$e";
	 }

	}
	if ( ($type == 2 OR $type == 3 ) AND !empty( $Message ) ) {
	  if ( $Message == "1. . ." )
	  $OUTPUT= "$s<img class='imgvalign' src='".OS_HOME."img/gamestarted.gif' alt='' width='160' height='24' /> <b>".$lang["gl_game_started"]."</b>  $e";
	  
	  $OUTPUT.= "$s $GameTime $GameType $Message $e";

	}
	
	//Scourge and Sentinel 
        if ( ($type == 5) AND !empty($Message) AND !empty($p1) ) {
		
		     if ( $event == "Scourge" )   $color="scourge"; 
		else if ( $event == "Sentinel" )  $color="sentinel";
		else if ( $event == "all" )       $color="all"; 
		
		
	    $OUTPUT = "$s $GameTime <span class='$color'>$GameType</span> $h1 <b>$p1</b>: $h2 $p2 $Message$e";
		
	}
	
	//FILTER - for non-logged users
	if ( !os_is_logged() ) {
	
	$s = "<div class='LiveGamesSingleMessage LoginToViewLog'>";
	$e = "</div>";
	
	if( $event == "ap" AND $LiveShowAegis == 2)          $OUTPUT = "$s".$lang["gl_login"]."$e";
	if( $event == "ad" AND $LiveShowAegis == 2)          $OUTPUT = "$s".$lang["gl_login"]."$e";
	if( $event == "sw" AND $LiveShowSwaps == 2)          $OUTPUT = "$s".$lang["gl_login"]."$e";
	if( $event == "hp" AND $LiveShowHeroPicks == 2)      $OUTPUT = "$s".$lang["gl_login"]."$e";  
	if( $event == "lu" AND $LiveShowPlayerLevels == 2 )  $OUTPUT = "$s".$lang["gl_login"]."$e";
	if ( $LiveShowKills == 2 AND ($event == "k" OR $event == "sek" OR $event == "sck" OR $event == "a") )
	$OUTPUT = "$s".$lang["gl_login"]."$e";
	if( $event == "s" AND $LiveShowSuicides == 2)        $OUTPUT = "$s".$lang["gl_login"]."$e";
	if( $event == "ru" AND $LiveShowRunes == 2)          $OUTPUT = "$s".$lang["gl_login"]."$e";
	if( $event == "rosh" AND $LiveShowRoshanKill == 2)   $OUTPUT = "$s".$lang["gl_login"]."$e";
	if ( $LiveShowTowerKills == 2 AND ( $event == "tok" OR $event == "setk" OR $event == "sctk" ) )
	$OUTPUT = "$s".$lang["gl_login"]."$e";
	
	}
	
	//Finally OUTPUT - one line
	if ( !empty( $OUTPUT ) ) $HTML = ''.$OUTPUT.'';
	
	//PLAYER LIST - SKIP
	if ( $event == "pl" ) $HTML = "";
	
	$Final.=$HTML;
	 }
	 
	 
	}
	return $Final;
   
 }  
 
   
   function OS_GetPlayerList( $DataArray, $chatID=0, $lastID=0, $click=0 ) {
	 global $lang;
	 $kills = ""; $deaths = ""; $assists = "";
	 //$HTML = "<h3>List of players #".$chatID."</h3>"; 
	 
	 $HTML="<table class='PlayerLogInfoTable'><tr><th width='230'>#".$chatID." &nbsp;&nbsp; ".$lang["gl_playerlist_sentinel_title"]."</th><th class='padLeft'>".$lang["gl_playerlist_kda"]."</th></tr>";
	 
	 $SentKills = 0; $ScourKills=0;
	 
	 //PLAYER INFO - HERO ICON and KDA
	 $DataArray2 = array_reverse ( $DataArray  );
	 
	 $c=1;
	 //Detect if playerdata exists (HID and KDA)
	 if ($click==1) $PlayerInfoData = 1; //Force to display player data on first load
	 else $PlayerInfoData = 0;
	 
	 $PlayerData = array();
     foreach ($DataArray2 as $Info){
	   $Log2 = explode("	", $Info);
		
	   if (isset($Log2[0]) ) $event  = $Log2[0]; else $event = "";
	   
	   if ( $event == 6 ) {
        $PlayerInfoData = 1;
		
		if (isset($Log2[2]) ) $PlayerData[$c]["hid"] = strtoupper($Log2[2]); else $PlayerData[$c]["hid"] = "";
		if (isset($Log2[2]) ) $PlayerData[$c]["level"] = strtoupper($Log2[4]); else $PlayerData[$c]["level"] ="";
		if (isset($Log2[2]) ) $PlayerData[$c]["kda"] = strtoupper($Log2[5]); else $PlayerData[$c]["kda"] = "";
		
		
		//Try to fix bug with player levels - currently disabled!
		if ($PlayerData[$c]["level"]>25) {
		// if (!empty($PlayerData[$c]["hid"]) ) { $PlayerData[$c]["level"] = $PlayerData[$c]["level"]; } ; else $PlayerData[$c]["level"] = "-";
		 //if (empty($PlayerData[$c]["hid"]) ) $PlayerData[$c]["level"] = "-";
		}
			$c++;
	   }
	  
	 }

	 //END -----> PLAYER INFO - HERO ICON and KDA
	 
	 foreach ($DataArray as $Data){
	 
     $Log = explode("	", $Data);
	 
	 if (isset($Log[0]) AND $Log[0]!= '-' ) $info  = $Log[0]; else $info = "";
     if (isset($Log[1]) AND $Log[1]!= '-' ) $event = $Log[1]; else $event = "";
	 
	 $EmptySlot = ' - ';
	 
	 if ( $info == 2 OR $info == 4 ) $PlayerInfoData = 1; //Always update for lobby

	 
	   if ( $event == "pl" AND !isset($PlayerData[$c]["hid"]) AND empty($PlayerData[$c]["hid"]) ) {
	 
	    $Logs = explode("	", $Data);
	    $slots = array();
		
		 for ($i = 1; $i <= 10; $i++) {
	     if (isset($Logs[$i+1]) AND $Logs[$i+1]!= '-' ) $slots[$i] = $Logs[$i+1]; else $slots[$i] = $EmptySlot;
	     }
		 
	   
	   $c = 1;
        
	   foreach ( $slots as $slot) {
	   
	   $link = OS_HOME."?live_games&c=".$slot."&id=".$chatID."&l=".$lastID."&hash=".md5($slot);
	   
	   $alert = 'onclick="alert(\'Chat with '.$slot.' is currently unavailable.\')"';
	   
	   if ( isset($PlayerData[$c]["hid"]) AND !empty($PlayerData[$c]["hid"]) ) $Hero = '<img src="'.OS_HOME.'img/heroes/'.$PlayerData[$c]["hid"].'.gif" width="24" height="24" class="imgvalign" alt="h" />'; else 
	   $Hero = '<img src="'.OS_HOME.'img/heroes/blank.gif" width="24" height="24" class="imgvalign" alt="h" />';
	   
	   if ( isset($PlayerData[$c]["level"]) ) $Level = '('.$PlayerData[$c]["level"].')'; else $Level = "";
	   if ( isset($PlayerData[$c]["kda"]) ) {  
	   $KDAData = explode("/", $PlayerData[$c]["kda"]);
	   if (isset($KDAData[0]) ) $kills   = '<span class="won">'.$KDAData[0].'<span> / ';  else $KDAData[0] = "";
	   if (isset($KDAData[1]) ) $deaths  = '<span class="lost">'.$KDAData[1].'<span> / '; else $KDAData[1] = "";
	   if (isset($KDAData[2]) ) $assists = '<span class="assists">'.$KDAData[2].'<span>'; else $KDAData[2] = "";
	   $kda   = '<span>'.$kills.$deaths.$assists."</span>"; 
	   if (isset($KDAData[0]) ) $killsData = $KDAData[0]; else $killsData = "";
	   }
	   else { $kda = ""; $killsData = ""; }
	   
	   $chatIcon = '<a onMouseout="hidetooltip()" onclick="hidetooltip()" onMouseover="tooltip(\'<b>'.$slot.'</b><br />'.$lang["gl_playerlist_send"].' \', \''.OS_HOME.'img/chat-icon.png\', 160, 32, 32)" href="'.$link.'" target="_blank" ><img class="PlayerListChat" src="'.OS_HOME.'img/chat-icon.png" alt="chat" width="16" height="16" class="imgvalign" /></a>';
	   
	   //$chatIcon ="";
	   if ( os_is_logged() AND $_SESSION["level"]>=9 ) {
	   $AdminCommands = '<a href="javascript:;" onclick="showhide(\'adm'.$c.'\')"><img style="float:right; padding-right: 5px;" src="'.OS_HOME.'img/cfg.png" class="imgvalign" width="16" height="16" /></a><div id="adm'.$c.'" style="display:none; padding-left:16px;"><a href="javascript:;" onclick="if (confirm(\'Mute player ['.$slot.'] ?\') ) { OS_AdminExec(\'mute\', \''.$slot.'\') } ">mute</a> | <a href="javascript:;" onclick="if (confirm(\'Unmute player ['.$slot.'] ?\') ) { OS_AdminExec(\'unmute\', \''.$slot.'\') } ">unmute</a> | <a href="javascript:;" onclick="if (confirm(\'KICK player ['.$slot.'] ?\') ) { OS_AdminExec(\'kick\', \''.$slot.'\') } ">kick</a> | <a href="javascript:;" onclick="if (confirm(\'Ban player ['.$slot.'] ?\') ) window.open(\''.OS_HOME.'adm/?bans&add='.$slot.'&gid='.$chatID.'\', \'_blank\')">ban</a></div>'; }
	   else $AdminCommands = "";
	   
	   if ($c>=6 AND !isset($scourgeTable) ) { 
	   $scourgeTable = 1;
	   $HTML.='</tr></table>';
	   $HTML.="<table class='PlayerLogInfoTable'><tr><th width='230'>#".$chatID." &nbsp;&nbsp; ".$lang["gl_playerlist_scourge_title"]."</th><th class='padLeft'>".$lang["gl_playerlist_kda"]."</th></tr>";
	   
	   }
	   
	   if ( !isset($scourgeTable) ) $SentKills+=$killsData; else $ScourKills+=$killsData;
       
	   if (trim($slot) == "-" ) $HTML.= '</tr><td width="230"><div class="PlayerListSingle slot'.$c.'"> &nbsp; '.$slot.' </div> </td><td></td></tr>';
	   else 
	   $HTML.= '<tr><td  width="230"><div class="PlayerListSingle slot'.$c.'"> &nbsp;'.$Hero.'&nbsp; <a target="_blank" href="'.OS_HOME.'?u='.strtolower($slot).'"><span class="col'.$c.'">'.$slot.'</span></a> '.$Level.' '.$chatIcon.'  '.$AdminCommands.'</td><td class="PlayerListSingle">'.$kda.' </div> <div class="PLClear">&nbsp;</div></td></tr>';
	   $c++;
	   }
	  }
	}
	 $HTML.="</table>";
	 
	 $TeamScores="<table class='TeamScores'><tr><td><span class='sentinelKills'><b>".$lang["gl_playerlist_sentinel"]."</b></span> $SentKills : $ScourKills <span class='scourgeKills'><b>".$lang["gl_playerlist_scourge"]."</b></span></td></tr></table>";
	 
	 $OUTPUT = $TeamScores.$HTML;
	 
	 if ( $PlayerInfoData == 0 ) $OUTPUT = "updating";
	 
	 return $OUTPUT;
   }
   
   //TO DO...
   function GameLogMessageFilter( $message = "" ) {
     $BlackList = array("","","","");
   }
   
 ?>
