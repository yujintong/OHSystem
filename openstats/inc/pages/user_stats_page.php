<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

   	//GAME TYPES/ALIASES (dota, lod)
    $sth = $db->prepare("SELECT * FROM ".OSDB_ALIASES." ORDER BY alias_id ASC");
	$result = $sth->execute();
	$GameAliases = array();
	$c = 0;

    $uid = safeEscape( (int) $_GET["u"] );
	
	$year  = date("Y");
	$month = date("n");
	
	$sth = $db->prepare("SELECT * FROM ".OSDB_STATS."  WHERE id = :user_id LIMIT 1");
	$sth->bindValue(':user_id', (int)$uid, PDO::PARAM_INT);  
	$result = $sth->execute();
	
	if ( $sth->rowCount()<=0 ) { 
    require_once(OS_PLUGINS_DIR.'index.php');
    os_init();
	header('location: '.OS_HOME.'?404'); die; 
	
	}
	
	$c=0;
    $UserData = array();
	
	 if ( file_exists("inc/geoip/geoipcity.inc") ) {
	  include("inc/geoip/geoipcity.inc");
	  $gi = geoip_open("inc/geoip/GeoLiteCity.dat",GEOIP_STANDARD);
	  $GeoIP = 1;
	 }
	
	while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	
	//Check user ban
	$chk = $db->prepare("SELECT * FROM ".OSDB_BANS." WHERE name = '".$row["player"]."' ORDER BY id DESC LIMIT 1");
    $result = $chk->execute();
	$rowban = $chk->fetch(PDO::FETCH_ASSOC);

	if ( isset($GeoIP) AND $GeoIP == 1) {
	$IP = $row["ip"];
	
	//Try to find user IP from another games (if player)
	if ( $IP == "0.0.0.0" OR $IP == "" ) {
	 $sth2 = $db->prepare("SELECT * FROM ".OSDB_GP." WHERE name = '".$row["player"]."' AND ip!='' AND ip!='0.0.0.0' ORDER BY id DESC LIMIT 1");
	 $result2 = $sth2->execute();
	 $row2 = $sth2->fetch(PDO::FETCH_ASSOC);
	 $IP = $row2["ip"];
	}
	
	$record = geoip_record_by_addr($gi,$IP);
	if (isset($record->city) ) $city = mb_convert_encoding($record->city, "UTF-8"); else $city="";
	if ( $IP == "0.0.0.0" OR $IP = "" ) $city = "";
	if (isset($record->country_code) ) $UserData[$c]["letter"]   = $record->country_code; else $UserData[$c]["letter"] = "";
	
	if ( isset($record->country_name)  ) { 
	$UserData[$c]["country"] = $record->country_name; 
	
	    if ( !empty($city) ) { $UserData[$c]["country"]  = $record->country_name.", $city"; $UserData[$c]["zoom_map"] = 15; }
		else $UserData[$c]["zoom_map"] = 8;
		
	} else $UserData[$c]["country"] = "";
	
	if (isset($record->latitude) )       $UserData[$c]["latitude"]       = $record->latitude;  else $UserData[$c]["latitude"]  ="";
	if (isset($record->longitude) )      $UserData[$c]["longitude"]      = $record->longitude; else $UserData[$c]["longitude"]  ="";
	if (isset($record->continent_code) ) $UserData[$c]["continent_code"] = $record->continent_code; else $UserData[$c]["continent_code"]  ="";
	//$UserData[$c]["letter"]   = geoip_country_code_by_addr($GeoIPDatabase, $row["ip"]);
	//$UserData[$c]["country"]  = geoip_country_name_by_addr($GeoIPDatabase, $row["ip"]);
	}
	
	
	$UserData[$c]["id"]        = (int)($row["id"]);
	$UserData[$c]["player"]   = ($row["player"]);
	$UserData[$c]["alias_id"]  = ($row["alias_id"]);
	$UserAliasID = ($row["alias_id"]);
	
	$PlayerName = $UserData[$c]["player"];
	$UserData[$c]["banname"]  = ($rowban["name"]);
	$UserData[$c]["bandate"]  = date($DateFormat, strtotime($rowban["date"]));
	$UserData[$c]["bandate_raw"]  = ($rowban["date"]);
	$UserData[$c]["expiredate"]  = ($rowban["expiredate"]);
	$UserData[$c]["reason"]  = ($rowban["reason"]);
	$UserData[$c]["reason"] = str_replace(array("fuck", "FUCK", "Fuck"), array("f***", "F***", "F***"), $UserData[$c]["reason"]);
	$UserData[$c]["admin"]  = ($rowban["admin"]);
	
	$UserData[$c]["points"]  = number_format($row["points"], 0);
	$UserData[$c]["points_int"]  = ($row["points"]);
	
	$UserData[$c]["bannedby"]  = '<a href="'.OS_HOME.'?u='.strtolower($UserData[$c]["admin"]).'">'.$UserData[$c]["admin"].'</a>';
	
	if ( empty($rowban["admin"]) ) $UserData[$c]["bannedby"] = '<span class="banned">[system]</span>';
	
	$UserData[$c]["score"]  = number_format($row["score"],0);
	$UserData[$c]["games"]  = number_format($row["games"],0);
	$UserData[$c]["wins"]  = number_format($row["wins"],0);
    $UserData[$c]["win"]  = number_format($row["wins"],0);
	$UserData[$c]["losses"]  = number_format($row["losses"],0);
	$UserData[$c]["draw"]  = number_format($row["draw"],0);
	$UserData[$c]["kills"]  = number_format($row["kills"],0);
	$UserData[$c]["deaths"]  = number_format($row["deaths"],0);
	$UserData[$c]["assists"]  = number_format($row["assists"],0);
	$UserData[$c]["creeps"]  = number_format($row["creeps"],0);
	$UserData[$c]["denies"]  = number_format($row["denies"],0);
	$UserData[$c]["neutrals"]  = number_format($row["neutrals"],0);
	$UserData[$c]["towers"]  = ($row["towers"]);
	$UserData[$c]["rax"]  = ($row["rax"]);
	$UserData[$c]["banned"]  = ($row["banned"]);
	
	$UserData[$c]["month"]  = ($row["month"]);
	$UserData[$c]["year"]  = ($row["year"]);
	
	if ( $row["month"] != date("m") OR $row["year"]!= date("Y") ) {
	
	 $sth3 = $db->prepare("SELECT * FROM ".OSDB_STATS." 
	 WHERE player = '".$row["player"]."' AND alias_id = '".$UserAliasID."' 
	 ORDER BY id DESC LIMIT 1");
	 $result3 = $sth3->execute();
	 $row3 = $sth3->fetch(PDO::FETCH_ASSOC);
	 
	 $UserData[$c]["OtherStats"]  = ($row3["id"]);
	}
	
	$UserData[$c]["hide"]  = ($row["hide"]);
	if ( os_is_logged() AND $_SESSION["level"]>=9 AND $row["hide"] == 1 ) {
	$UserData[$c]["hide"] = 0;
	$UserData[$c]["admin_info"] = 1;
	}
	
	if ( strtotime($rowban["expiredate"]) <=time() ) $UserData[$c]["banned"]  = 0;
	
	$UserData[$c]["warn_expire"]  = ($row["warn_expire"]);
	$UserData[$c]["warn"]  = ($row["warn"]);
	$UserData[$c]["GameAdmin"]  = ($row["user_level"]);
	
	//Don't show ban on safelisted user, because they are not really banned!
	if ( $row["user_level"]>1 ) {
	$UserData[$c]["banned"] = 0;
	$UserData[$c]["banname"] ="";
	}
	
	$UserData[$c]["ip"]  = ($row["ip"]);
	$UserData[$c]["streak"]  = ($row["streak"]);
	$UserData[$c]["maxstreak"]  = ($row["maxstreak"]);
	$UserData[$c]["losingstreak"]  = ($row["losingstreak"]);
	$UserData[$c]["maxlosingstreak"]  = ($row["maxlosingstreak"]);
	$UserData[$c]["zerodeaths"]  = ($row["zerodeaths"]);
	
    if( strlen($row["realm"]) <= 2 ) {
		$UserData[$c]["realm"] = "Garena";
	} else {
		$UserData[$c]["realm"]  = ($row["realm"]);
	}
	if( !isset($UserData[$c]["realm"]) AND empty($UserData[$c]["realm"]) ) $UserData[$c]["realm"] = "Garena";
	//$UserData[$c]["avg_loading"]  = millisecondsToTime( $row["loading"]/$row["games"] );
	//$UserData[$c]["loading"]  = millisecondsToTime( $row["loading"]);
	$UserData[$c]["reserved"]  = ($row["reserved"]);
	
	$UserData[$c]["leaver"]  = ($row["leaver"]);
	if ($row["games"] >0 ) {
	$left = $row["games"] - $row["leaver"];
	$StayR = round(($left/$row["games"])*100, 1);
	//$UserData[$c]["stayratio"] = round($row["games"]/($row["games"]-$row["leaver"]), 3)*100;
	$UserData[$c]["stayratio"] = $StayR;
	}
	//$UserData[$c]["stayratio"] = round($row["games"]/$row["leaver"],2); 
	else $UserData[$c]["stayratio"] = 0;
	
	//SET META INFORMATION AND PAGE NAME
	 $HomeTitle = ($row["player"]);
	 $HomeDesc = ($row["player"]);
	 $HomeKeywords = strtolower($row["player"]).','.$HomeKeywords;
	
	if ($row["games"]>=1 AND $row["kills"]>=1) {
	$UserData[$c]["kpg"] = round($row["kills"]/$row["games"],2); 
	}
	else $UserData[$c]["kpg"] = 0;
	
	if ($row["games"]>=1 AND $row["deaths"]>=1) {
	$UserData[$c]["dpg"] = round($row["deaths"]/$row["games"],2); 
	}
	else $UserData[$c]["dpg"] = 0;
	
	if ($row["deaths"]>=1) $UserData[$c]["kd"]  = round($row["kills"] / $row["deaths"],2);
    else $UserData[$c]["kd"] = $row["kills"];
	
	if ($row["wins"] >0 )
	$UserData[$c]["winslosses"] = round($row["wins"]/($row["wins"]+$row["losses"]), 3)*100;
	else $UserData[$c]["winslosses"] = 0;
	
	//AVG assists
	if ($row["games"]>=1 AND $row["assists"]>=1) {
	$UserData[$c]["apg"] = round($row["assists"]/$row["games"],2); 
	}
	else $UserData[$c]["apg"] = 0;
	
	//AVG creeps per game
	if ($row["games"]>=1 AND $row["creeps"]>=1) {
	$UserData[$c]["ckpg"] = ROUND($row["creeps"]/$row["games"],2); 
	}
	else $UserData[$c]["ckpg"] = 0;

	//AVG denies per game
	if ($row["games"]>=1 AND $row["denies"]>=1) {
	$UserData[$c]["cdpg"] = ROUND($row["denies"]/$row["games"],2); 
	}
	else $UserData[$c]["cdpg"] = 0;

        $IP = $UserData[$c]["ip"];
	$c++;
	}
	if ( isset($GeoIP) AND $GeoIP == 1) geoip_close($gi);
	
	$sth = $db->prepare("SELECT 
	SUM(score) as totalscore,
	SUM(games) as totalgames,
	SUM(wins)  as totalwins,
	SUM(losses) as totallosses,
	SUM(draw) as totaldraw,
	SUM(kills) as totalkills,
	SUM(deaths) as totaldeaths,
	SUM(assists) as totalassists,
	SUM(creeps) as totalcreeps,
	SUM(denies) as totaldenies,
	SUM(neutrals) as totalneutrals,
	SUM(towers) as totaltowers,
	SUM(rax) as totalrax,
	SUM(leaver) as totalleaver,
	SUM(zerodeaths) as totalzerodeaths,
	SUM(maxstreak) as totalmaxstreak
	FROM ".OSDB_STATS." 
	WHERE LOWER(player) = '".strtolower($PlayerName)."'  ");
	
	$result = $sth->execute();
	$row = $sth->fetch(PDO::FETCH_ASSOC);
	$UserData[0]["totalscore"] = $row["totalscore"];
	$UserData[0]["totalgames"] = $row["totalgames"];
	$UserData[0]["totalwins"] = $row["totalwins"];
	$UserData[0]["totallosses"] = $row["totallosses"];
	$UserData[0]["totaldraw"] = $row["totaldraw"];
	$UserData[0]["totalkills"] = $row["totalkills"];
	$UserData[0]["totaldeaths"] = $row["totaldeaths"];
	$UserData[0]["totalassists"] = $row["totalassists"];
	$UserData[0]["totalcreeps"] = $row["totalcreeps"];
	$UserData[0]["totaldenies"] = $row["totaldenies"];
	$UserData[0]["totalneutrals"] = $row["totalneutrals"];
	$UserData[0]["totaltowers"] = $row["totaltowers"];
	$UserData[0]["totalrax"] = $row["totalrax"];
	$UserData[0]["totalleaver"] = $row["totalleaver"];
	$UserData[0]["totalzerodeaths"] = $row["totalzerodeaths"];
	$UserData[0]["totalmaxstreak"] = $row["totalmaxstreak"];
	
	if ($row["totalwins"] >0 )
	$UserData[0]["totalwinslosses"] = round($row["totalwins"]/($row["totalwins"]+$row["totallosses"]), 3)*100;
	else $UserData[0]["totalwinslosses"] = 0;
	
	if ($row["totaldeaths"]>=1) $UserData[0]["totalkd"]  = round($row["totalkills"] / $row["totaldeaths"],2);
    else $UserData[0]["totalkd"] = $row["totalkills"];
	
	if ($row["totalgames"]>=1 AND $row["totalkills"]>=1) {
	$UserData[0]["totalkpg"] = round($row["totalkills"]/$row["totalgames"],2); 
	}
	else $UserData[0]["totalkpg"] = 0;
	
	if ($row["totalgames"]>=1 AND $row["totaldeaths"]>=1) {
	$UserData[0]["totaldpg"] = round($row["totaldeaths"]/$row["totalgames"],2); 
	}
	else $UserData[0]["totaldpg"] = 0;
	
	//AVG assists
	if ($row["totalgames"]>=1 AND $row["totalassists"]>=1) {
	$UserData[0]["totalapg"] = round($row["totalassists"]/$row["totalgames"],2); 
	}
	else $UserData[0]["totalapg"] = 0;
	
	//AVG creeps per game
	if ($row["totalgames"]>=1 AND $row["totalcreeps"]>=1) {
	$UserData[0]["totalckpg"] = ROUND($row["totalcreeps"]/$row["totalgames"],2); 
	}
	else $UserData[0]["totalckpg"] = 0;

	//AVG denies per game
	if ($row["totalgames"]>=1 AND $row["totaldenies"]>=1) {
	$UserData[0]["totalcdpg"] = ROUND($row["totaldenies"]/$row["totalgames"],2); 
	}
	else $UserData[0]["totalcdpg"] = 0;
	
	if ($row["totalgames"] >0 ) {
	$left2 = $row["totalgames"] - $row["totalleaver"];
	$StayR2 = round(($left2/$row["totalgames"])*100, 1);
	$UserData[0]["totalstayratio"] = $StayR2;
	}
	else $UserData[0]["totalstayratio"] = 0;
	
	
	
	//CHECK BNET USERNAME
	
	$sth = $db->prepare("SELECT * FROM ".OSDB_USERS." as u 
	WHERE LOWER(bnet_username) = '".strtolower($PlayerName)."' AND user_bnet>=1 ");
	$result = $sth->execute();
    $row = $sth->fetch(PDO::FETCH_ASSOC);
    $UserData[0]["user_bnet"] = $row["user_bnet"];
	$UserData[0]["bnet_username"] = $row["bnet_username"];
	$UserData[0]["user_ppwd"] = $row["user_ppwd"];

    //LONGEST, FASTEST GAME WON
	if ( !empty($PlayerName) ) {
	
	   if ($ShowLongFastGameWon==1) {
	   $LongGame      = $db->prepare( longGameWon     ( strtolower($PlayerName) )    );
	   $result = $LongGame->execute();
	   $lg = $LongGame->fetch(PDO::FETCH_ASSOC);
	   
	   $FastGame      = $db->prepare( fastGameWon     ( strtolower($PlayerName) )    );
	   $result = $FastGame->execute();
	   $fg = $FastGame->fetch(PDO::FETCH_ASSOC);
	   
	   $GamesDuration = $db->prepare( GetGameDurations( strtolower($PlayerName) )    );
	   $result = $GamesDuration->execute();
	   $dg = $GamesDuration->fetch(PDO::FETCH_ASSOC);
	   
	   $LongestGame = array();
	   $FastestGame = array();
	   $Durations   = array();
	   
	   $LongestGame["gameid"] = ($lg["gameid"]);
	   $LongestGame["gamename"] = ($lg["gamename"]);
	   $LongestGame["duration"] = secondsToTime($lg["duration"]);
	   $LongestGame["kills"] = ($lg["kills"]);
	   $LongestGame["deaths"] = ($lg["deaths"]);
	   $LongestGame["assists"] = ($lg["assists"]);
	   $LongestGame["creepkills"] = ($lg["creepkills"]);
	   $LongestGame["creepdenies"] = ($lg["creepdenies"]);
	   $LongestGame["neutralkills"] = ($lg["neutralkills"]);
	   $LongestGame["newcolour"] = ($lg["newcolour"]);
	   
	   $FastestGame["gameid"] = ($fg["gameid"]);
	   $FastestGame["gamename"] = ($fg["gamename"]);
	   $FastestGame["duration"] = secondsToTime($fg["duration"]);
	   $FastestGame["kills"] = ($fg["kills"]);
	   $FastestGame["deaths"] = ($fg["deaths"]);
	   $FastestGame["assists"] = ($fg["assists"]);
	   $FastestGame["creepkills"] = ($fg["creepkills"]);
	   $FastestGame["creepdenies"] = ($fg["creepdenies"]);
	   $FastestGame["neutralkills"] = ($fg["neutralkills"]);
	   $FastestGame["newcolour"] = ($fg["newcolour"]);
	   	   
	   $Durations["min_loading"] = $dg["MIN(loadingtime)"];
	   $Durations["max_loading"] = $dg["MAX(loadingtime)"];
	   $Durations["avg_loading"] = $dg["AVG(loadingtime)"];
	   }
		$sth = $db->prepare( "SELECT SUM(`left`) FROM ".OSDB_GP." 
		WHERE (name)=? LIMIT 1" );
		
		$sth->bindValue(1, "".strtolower($PlayerName)."", PDO::PARAM_STR);
	    $result = $sth->execute();
		 
	    $res = $sth->fetch(PDO::FETCH_ASSOC);
		$TotalDuration=secondsToTime($res["SUM(`left`)"]);
		
		$TotalHours=ROUND($res["SUM(`left`)"]/ 3600,1);
		$TotalMinutes=ROUND($res["SUM(`left`)"]/ 3600*60,1);
		$TimePlayed["timeplayed"] = secondsToTime( $res["SUM(`left`)"] , $lang["h"], $lang["m"], $lang["s"]);
	   //$Durations["avg_loading"] = $dg["AVG(loadingtime)"];
	   
         //GET MOST PLAYED HERO BY USER
	     $getHero = $db->prepare( GetMostPlayedHero( $PlayerName ) );
		 $result = $getHero->execute();
		 
	     $row = $getHero->fetch(PDO::FETCH_ASSOC);
	     $FavoriteHero = array();
	     $TimePlayed2["timeplayed"] = secondsToTime($row["timeplayed"], $lang["h"], $lang["m"], $lang["s"]);
	     $FavoriteHero["original"] = $row["original"];
	     $FavoriteHero["description"] = $row["description"];
		 $FavoriteHero["played"] = $row["played"];
		 
         if ( isset($ShowUserHeroStats ) AND $ShowUserHeroStats ==1 ) {
		 //GET MOST KILLS HERO BY USER
	     $getHero = $db->prepare( GetMostKillsHero( $PlayerName ) );
		 
		 $result = $getHero->execute();
	     $row = $getHero->fetch(PDO::FETCH_ASSOC);
	     $MostKillsHero = array();
	     $MostKillsHero["kills"] = ($row["maxkills"]);
		 $MostKillsHero["gameid"] = ($row["gameid"]);
	     $MostKillsHero["original"] = $row["original"];
	     $MostKillsHero["description"] = $row["description"];

		 //GET MOST DEATHS HERO BY USER
	     $getHero = $db->prepare( GetMostDeathsHero( $PlayerName ) );
		 $result = $getHero->execute();
		 
	     $row = $getHero->fetch(PDO::FETCH_ASSOC);
	     $MostDeathsHero = array();
		 $MostDeathsHero["gameid"] = ($row["gameid"]);
	     $MostDeathsHero["deaths"] = ($row["maxdeaths"]);
	     $MostDeathsHero["original"] = $row["original"];
	     $MostDeathsHero["description"] = $row["description"];

		 //GET MOST Assists HERO BY USER
	     $getHero = $db->prepare( GetMostAssistsHero( $PlayerName ) );
		 $result = $getHero->execute();
		 
	     $row = $getHero->fetch(PDO::FETCH_ASSOC);
	     $MostAssistsHero = array();
		 $MostAssistsHero["gameid"] = ($row["gameid"]);
	     $MostAssistsHero["assists"] = ($row["maxassists"]);
	     $MostAssistsHero["original"] = $row["original"];
	     $MostAssistsHero["description"] = $row["description"];

		 //GET MOST WINS HERO BY USER
	     $getHero = $db->prepare( GetMostWinsHero( $PlayerName ) );
		 $result = $getHero->execute();
		 
	     $row = $getHero->fetch(PDO::FETCH_ASSOC);
	     $MostWinsHero = array();
	     $MostWinsHero["wins"] = ($row["wins"]);
	     $MostWinsHero["original"] = $row["original"];
	     $MostWinsHero["description"] = $row["description"];
		 
		 
	     }
	   }
	   
	   //PLAYER OFFENCES
	   /*
	   $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_GO." WHERE id>=1 AND player_name = '$PlayerName' ");
	   $result = $sth->execute();
	   $r = $sth->fetch(PDO::FETCH_NUM);
	   $numrows = $r[0];
	   $result_per_page = $TopPlayersPerPage;
	   $draw_pagination = 0;
	   include('inc/pagination.php');
	   $draw_pagination = 1;
	   */
	   
	   $sth = $db->prepare("SELECT SUM(pp) as totalpp, COUNT(*) as warned
	   FROM ".OSDB_GO." 
	   WHERE player_name = '$PlayerName'");
	   $result = $sth->execute();
	   $r = $sth->fetch(PDO::FETCH_ASSOC);
	   $TotalPP = $r["totalpp"];
	   $Warned = $r["warned"];
	   
	   $sth = $db->prepare("SELECT player_id,  player_name, reason, offence_time, offence_expire, pp, admin, id
	   FROM ".OSDB_GO." 
	   WHERE id>=1 AND player_name = '$PlayerName'
	   ORDER BY id DESC LIMIT 25");
	   $result = $sth->execute();
	   
	   $PenaltyData = array();
	   $c = 0;
	   if ( $sth->rowCount()>=1) {
	   while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	    $PenaltyData[$c]["id"] = $row["id"];
		$PenaltyData[$c]["player_id"] = $row["player_id"];
		$PenaltyData[$c]["player_name"] = $row["player_name"];
		$PenaltyData[$c]["reason"] = $row["reason"];
		$PenaltyData[$c]["offence_time"] = $row["offence_time"];
		$PenaltyData[$c]["date"] = date(OS_DATE_FORMAT, strtotime($row["offence_time"]));
		
		$ExpireDate = (strtotime($row["offence_time"])) + ($PPExpireDays*3600*24);
		$PenaltyData[$c]["expire_date_int"] = $ExpireDate;
		$PenaltyData[$c]["expire_date"] = date(OS_DATE_FORMAT, $ExpireDate);
		
		$PenaltyData[$c]["offence_expire"] = $row["offence_expire"];
		$PenaltyData[$c]["pp"] = $row["pp"];
		$PenaltyData[$c]["admin"] = $row["admin"];
		if (empty($row["admin"])) $PenaltyData[$c]["admin"] = "[system]";
		$PenaltyData[$c]["total"] = $TotalPP;
		$PenaltyData[$c]["warned"] = $Warned;
		
		//$PenaltyData[$c]["reason"] = str_replace(array("fuck", "FUCK", "Fuck"), array("f***", "F***", "F***"), $PenaltyData[$c]["reason"]);
		
		//Just fix for old version
		if ( strstr( $row["reason"], "left ") AND   strstr( $row["reason"], "/")) {
		 
		 $fixReason = explode("/", $row["reason"]);
		 if ( isset($fixReason[0]) ) $fixReason[0] = $fixReason[0]; else $fixReason[0] = "";
		 if ( isset($fixReason[1]) ) $fixReason[1] = $fixReason[1]; else $fixReason[1] = "";
		 
		 $fixReason[0] = filter_var( $fixReason[0], FILTER_SANITIZE_NUMBER_INT);
		 $fixReason[0] = ROUND( ($fixReason[0]/60) , 0);
		 $fixReason[1] = ROUND( ($fixReason[1]/60) , 0);
		 
		 $PenaltyData[$c]["reason"] = "left: ". ( $fixReason[0] )." min. / ".( $fixReason[1] )." min.";
		}
		
		$c++;
	   }
	} else {
	    $PenaltyData[$c]["id"] = "";
		$PenaltyData[$c]["player_id"] = "";
		$PenaltyData[$c]["player_name"] = "";
		$PenaltyData[$c]["reason"] = "";
		$PenaltyData[$c]["offence_time"] = "";
		$PenaltyData[$c]["date"] = "";
		$PenaltyData[$c]["offence_expire"] = "";
		$PenaltyData[$c]["pp"] = 0;
		$PenaltyData[$c]["admin"] = "";
		$PenaltyData[$c]["total"] = 0;
		$PenaltyData[$c]["warned"] = 0;
	}
	
	if ( $LiveGames == 1 ) {
	//LAST SEEN FROM GAME LOG
	$sth = $db->prepare("SELECT gameid, botid, log_time, log_data
	FROM ".OSDB_GAMELOG." 
	WHERE id>=1 AND log_data LIKE '%".$PlayerName."%'
	ORDER BY id DESC LIMIT 1");
	$result = $sth->execute();
	$row = $sth->fetch(PDO::FETCH_ASSOC);
	
	$LastSeen = array();
	$LastSeen["gameid"] = $row["gameid"];
	$LastSeen["botid"] = $row["botid"];
	$LastSeen["time"] = date( $DateFormat, strtotime($row["log_time"]));  
	$LastSeen["log_data"] = $row["log_data"]; 
	}
	
	//This will be added later
	include("inc/pages/player_role.php");
	
	
	//Check other game types
	$sth = $db->prepare("SELECT * FROM ".OSDB_STATS."  
	WHERE player = :player AND `month` = '".$month."' AND year = '".$year."' AND alias_id != '".$UserAliasID."' LIMIT 1");
	$sth->bindValue(':player', $PlayerName, PDO::PARAM_STR);  
	$result = $sth->execute();
	$row = $sth->fetch(PDO::FETCH_ASSOC);
	
	$UserOtherGames["id"] = $row["id"];
	
	//GAME TYPES/ALIASES (dota, lod)
	
    $sth = $db->prepare("SELECT * FROM ".OSDB_ALIASES." ORDER BY alias_id ASC");
	$result = $sth->execute();
	$GameAliases = array();
	$c = 0;
	while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	 $GameAliases[$c]["alias_id"] = $row["alias_id"];
	 $GameAliases[$c]["alias_name"] = $row["alias_name"];
	 
	 if ( isset($UserAliasID) AND $UserAliasID == $row["alias_id"] )
	 $GameAliases[$c]["selected"] = 'selected="selected"'; else $GameAliases[$c]["selected"] = '';
	 
	 if ( !isset($UserAliasID) AND $row["default_alias"] == 1) {
	 $GameAliases[$c]["selected"] = 'selected="selected"';
	 $DefaultGameType = $row["alias_id"];
	 }
	 
	 if ( isset($_GET["game_type"]) AND $_GET["game_type"] == $row["alias_id"] )
	 $GameAliases[$c]["selected"] = 'selected="selected"'; else $GameAliases[$c]["selected"] = ''; 
	 
	 $c++;
	}
	
	
	//Hook js
	AddEvent("os_js", "OS_UserMap");
  
  function OS_UserMap() {
?>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
  <?php } ?>
