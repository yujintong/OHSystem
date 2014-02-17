<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

if ( !empty($_GET["common_games"]) AND isset($_SESSION["bnet_username"]) ) {
  	$HomeTitle = "Common Games";
	$HomeDesc = os_strip_quotes($lang["game_archive"]);
	$HomeKeywords = strtolower( os_strip_quotes($lang["game_archive"])).','.$HomeKeywords;
	$MenuClass["games"] = "active";
	
	$User1 = strip_tags(trim($_SESSION["bnet_username"]));
	$User2 = safeEscape( trim( $_GET["common_games"] ) );
	
	$sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_GAMES." as g
	LEFT JOIN ".OSDB_GP." as gp ON gp.gameid = g.id AND gp.name=:user1 
	LEFT JOIN ".OSDB_GP." as gp2 ON gp2.gameid = gp.gameid AND gp2.name=:user2
	WHERE gp.name =:user1  AND gp2.name =:user2
	LIMIT 1");
	
	$sth->bindValue(':user1', $User1, PDO::PARAM_STR); 
	$sth->bindValue(':user2', $User2, PDO::PARAM_STR); 
	
	 $result = $sth->execute();
  
     $r = $sth->fetch(PDO::FETCH_NUM);
     $numrows = $r[0]; 
     $result_per_page = $GamesPerPage;
     $draw_pagination = 0;
     include('inc/pagination.php');
     $draw_pagination = 1;
	 
	$sth = $db->prepare("SELECT g.gamename, g.id, g.map, g.datetime, g.duration, g.gamestate, dg.winner
	FROM ".OSDB_GAMES." as g
	LEFT JOIN ".OSDB_GP." as gp ON gp.gameid = g.id AND gp.name=:user1 
	LEFT JOIN ".OSDB_GP." as gp2 ON gp2.gameid = gp.gameid AND gp2.name=:user2
	LEFT JOIN ".OSDB_DG." as dg ON dg.gameid = g.id
	WHERE gp.name =:user1  AND gp2.name =:user2
	ORDER BY g.id DESC
	LIMIT $offset, $rowsperpage");
	
	$sth->bindValue(':user1', $User1, PDO::PARAM_STR); 
	$sth->bindValue(':user2', $User2, PDO::PARAM_STR); 
	
	$result = $sth->execute();
	
	$CommonGames = array();
	$c=0;
	while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	$CommonGames[$c]["gamename"] = $row["gamename"];
	$CommonGames[$c]["id"] = $row["id"];
	$CommonGames[$c]["map"] = $row["map"];
	$CommonGames[$c]["datetime"] = $row["datetime"];
	$CommonGames[$c]["duration"] = $row["duration"];
	$CommonGames[$c]["gamestate"] = $row["gamestate"];
	$CommonGames[$c]["player1"] = $User1;
	$CommonGames[$c]["player2"] = $User2;
	
	$CommonGames[$c]["type"]  = OS_GetGameState($row["gamestate"], $lang["gamestate_pub"] , $lang["gamestate_priv"]);
	
	$CommonGames[$c]["winner"]  = ($row["winner"]);
	
	
	//REPLAY
	 $duration = secondsToTime($row["duration"]);
     $replayDate =  strtotime($row["datetime"]);  //3*3600 = +3 HOURS,   +0 minutes.
     $replayDate = date("Y-m-d H:i",$replayDate);
     $gametimenew = substr(str_ireplace(":","-",date("Y-m-d H:i",strtotime($replayDate))),0,16);
	 $gid =  (int)($row["id"]);
	 $gamename = $CommonGames[$c]["gamename"];
	 include('inc/get_replay.php');
	 
	 if ( file_exists($replayloc) ) $CommonGames[$c]["replay"] = $replayloc; else $CommonGames[$c]["replay"] = "";
	 //END REPLAY
	
	$c++;
	
	}
	
	}
?>