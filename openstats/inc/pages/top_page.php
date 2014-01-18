<?php

if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

   	 //SET META INFORMATION AND PAGE NAME
	 $HomeTitle = $lang["top"];
	 $HomeDesc = $lang["top"];
	 //$HomeKeywords = strtolower($row["gamename"]).','.$HomeKeywords;
     $MenuClass["top"] = "active";
     $orderby = "`score` DESC";
	 
	//GAME TYPES/ALIASES (dota, lod)
	
    $sth = $db->prepare("SELECT * FROM ".OSDB_ALIASES." ORDER BY alias_id ASC");
	$result = $sth->execute();
	$GameAliases = array();
	$c = 0;
	while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	 $GameAliases[$c]["alias_id"] = $row["alias_id"];
	 $GameAliases[$c]["alias_name"] = $row["alias_name"];
	 
	 if ( isset($_GET["game_type"]) AND $_GET["game_type"] == $row["alias_id"] )
	 $GameAliases[$c]["selected"] = 'selected="selected"'; else $GameAliases[$c]["selected"] = '';
	 
	 if ( !isset($_GET["game_type"]) AND $row["default_alias"] == 1) {
	 $GameAliases[$c]["selected"] = 'selected="selected"';
	 $DefaultGameType = $row["alias_id"];
	 }
	 
	 $c++;
	}
	 
   
   if ( isset($_GET["sort"]) ) {
     if ( $_GET["sort"] == "score") $orderby = "`score` DESC";
	 if ( $_GET["sort"] == "country") $orderby = "`country_code` ASC";
	 if ( $_GET["sort"] == "player_name") $orderby = "(`player`) ASC";
	 if ( $_GET["sort"] == "games") $orderby = "(`games`) DESC";
	 if ( $_GET["sort"] == "wins") $orderby = "(`wins`) DESC";
	 if ( $_GET["sort"] == "losses") $orderby = "(`losses`) DESC";
	 if ( $_GET["sort"] == "draw") $orderby = "(`draw`) DESC";
	 if ( $_GET["sort"] == "kills") $orderby = "(`kills`/`games`) DESC";
	 if ( $_GET["sort"] == "deaths") $orderby = "(`deaths`/`games`) DESC";
	 if ( $_GET["sort"] == "assists") $orderby = "(`assists`/`games`) DESC";
	 if ( $_GET["sort"] == "ck") $orderby = "(`creeps`/`games`) DESC";
	 if ( $_GET["sort"] == "cd") $orderby = "(`denies`/`games`) DESC";
	 if ( $_GET["sort"] == "nk") $orderby = "(`neutrals`/`games`) DESC";
	 if ( $_GET["sort"] == "leaves") $orderby = "(`leaver`) DESC";
	 if ( $_GET["sort"] == "streak") $orderby = "(`maxstreak`) DESC";
   }
   

  $currentYear  = date("Y", time() );
  $currentMonth = date("n", time() );
  
  $sqlCurrentDate = " AND `month` = '".$currentMonth."' AND `year` = '".$currentYear."'";
  
  if ( isset($_GET["y"]) AND isset($_GET["m"]) AND is_numeric($_GET["y"]) AND is_numeric($_GET["m"]) ) {
  
    $sqlCurrentDate = " AND `year` = '".(int)$_GET["y"]."' ";
    $sqlCurrentDate.= " AND `month` = '".(int)$_GET["m"]."' ";
	
	$HomeTitle.=" | ".(int)$_GET["m"]."/".(int)$_GET["y"];
  }
  
   $sql = "";
	  
   if ( isset($_GET["L"]) AND strlen($_GET["L"]) == 1 ) {
     $sql = " AND player LIKE ('".strtolower($_GET["L"])."%') ";
	 
	 $HomeTitle.=" | ".strip_tags($_GET["L"])."";
   }
   
   
  if ( isset($_GET["country"]) AND strlen($_GET["country"]) == 2 ) {
    $country = strip_tags(substr($_GET["country"],0,2));
    $sql.= " AND country_code = '".$country."' ";
	
	$HomeTitle.=" | $country";
 } 
 
  if ( isset($_GET["game_type"]) AND is_numeric($_GET["game_type"]) ) {
    
	$sql.=" AND alias_id='". ( int ) $_GET["game_type"]."' ";
   
  } else {
    //DefaultGameType
  $sql.=" AND alias_id='". $DefaultGameType."' ";
  }
  

 
  $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_STATS." 
  WHERE id>=1 $sqlCurrentDate $sql AND hide=0 LIMIT 1");
  $result = $sth->execute();
  
  $r = $sth->fetch(PDO::FETCH_NUM);
  $numrows = $r[0];
  
  if ( $numrows<=0) { 
  require_once(OS_PLUGINS_DIR.'index.php');
  os_init();
  header('location: '.OS_HOME.'?404'); die; 
  }
  
  $result_per_page = $TopPlayersPerPage;
  $draw_pagination = 0;
  include('inc/pagination.php');
  $draw_pagination = 1;
  
  $sth = $db->prepare("SELECT * FROM ".OSDB_STATS." 
  WHERE id>=1  $sqlCurrentDate $sql AND hide=0 
  ORDER BY $orderby 
  LIMIT $offset, $rowsperpage");
  
  $result = $sth->execute();

  if ( isset($_GET["page"]) AND is_numeric($_GET["page"]) AND $sth->rowCount()>=1 ) {
     $HomeTitle.=" | Page ".(int) $_GET["page"];
  }
   
   	$c=0;
    $TopData = array();
	$counter = 0;
	
	if ( isset( $_GET["page"]) AND is_numeric($_GET["page"]) ) {
	  $counter = (($_GET["page"]-1) * $TopPlayersPerPage) ;
	}
	
	if ( file_exists("inc/geoip/geoip.inc") AND !class_exists("GeoIP")  ) {
	include_once("inc/geoip/geoip.inc");
	$GeoIPDatabase = geoip_open("inc/geoip/GeoIP.dat", GEOIP_STANDARD);
	$GeoIP = 1;
	}

	while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	$TopData[$c]["letter"]   = geoip_country_code_by_addr($GeoIPDatabase, $row["ip"]);
	$TopData[$c]["country"]  = geoip_country_name_by_addr($GeoIPDatabase, $row["ip"]);
	if ($GeoIP == 1 AND empty($TopData[$c]["letter"]) ) {
	$TopData[$c]["letter"] = "blank";
	$TopData[$c]["country"] = "Reserved";
	}
	
	$counter++;
	
	$TopData[$c]["counter"]        = $counter;
	$TopData[$c]["id"]        = (int)($row["id"]);
	$TopData[$c]["player"]  = ($row["player"]);
	
	$TopData[$c]["realm"] = ($row["realm"]);

	$TopData[$c]["score"]  = number_format($row["score"],0);
	$TopData[$c]["games"]  = number_format($row["games"],0);
	$TopData[$c]["wins"]  = number_format($row["wins"],0);
	$TopData[$c]["losses"]  = number_format($row["losses"],0);
	$TopData[$c]["draw"]  = number_format($row["draw"],0);
	$TopData[$c]["kills"]  = number_format($row["kills"],0);
	$TopData[$c]["deaths"]  = number_format($row["deaths"],0);
	$TopData[$c]["assists"]  = number_format($row["assists"],0);
	$TopData[$c]["creeps"]  = number_format($row["creeps"],0);
	$TopData[$c]["denies"]  = number_format($row["denies"],0);
	$TopData[$c]["neutrals"]  = number_format($row["neutrals"],0);
	$TopData[$c]["towers"]  = ($row["towers"]);
	$TopData[$c]["rax"]  = ($row["rax"]);
	$TopData[$c]["banned"]  = ($row["banned"]);
	$TopData[$c]["admin"]  = ($row["user_level"]);
	
	if ( $row["user_level"]>1 ) $TopData[$c]["banned"] = 0;
	//$TopData[$c]["safelist"]  = ($row["safelist"]);
	$TopData[$c]["ip"]  = ($row["ip"]);
	$TopData[$c]["leaver"]  = ($row["leaver"]);
	$TopData[$c]["streak"]  = ($row["streak"]);
	$TopData[$c]["maxstreak"]  = ($row["maxstreak"]);
	$TopData[$c]["losingstreak"]  = ($row["losingstreak"]);
	$TopData[$c]["maxlosingstreak"]  = ($row["maxlosingstreak"]);
	$TopData[$c]["zerodeaths"]  = ($row["zerodeaths"]);
	
	//AVERAGES
	if ($row["games"] >0 ) 
	$TopData[$c]["avg_kills"]  = round($row["kills"]/$row["games"],2); else $TopData[$c]["avg_kills"] = 0;
	
	if ($row["games"] >0 ) 
	$TopData[$c]["avg_deaths"]  = round($row["deaths"]/$row["games"],2); else $TopData[$c]["avg_deaths"] = 0;

	if ($row["games"] >0 ) 
	$TopData[$c]["avg_assists"]  = round($row["assists"]/$row["games"],2); else $TopData[$c]["avg_assists"] = 0;

	if ($row["games"] >0 ) 
	$TopData[$c]["avg_creeps"]  = round($row["creeps"]/$row["games"],2); else $TopData[$c]["avg_creeps"] = 0;

	if ($row["games"] >0 ) 
	$TopData[$c]["avg_denies"]  = round($row["denies"]/$row["games"],2); else $TopData[$c]["avg_denies"] = 0;

	if ($row["games"] >0 ) 
	$TopData[$c]["avg_neutrals"]  = round($row["neutrals"]/$row["games"],2); else $TopData[$c]["avg_neutrals"] = 0;

	if ($row["games"] >0 ) 
	$TopData[$c]["avg_towers"]  = round($row["towers"]/$row["games"],2); else $TopData[$c]["avg_towers"] = 0;
	
	if ($row["games"] >0 ) 
	$TopData[$c]["avg_rax"]  = round($row["rax"]/$row["games"],2); else $TopData[$c]["avg_rax"] = 0;
	
	//END  -   AVERAGES
	if ($row["games"] >0 )
	$TopData[$c]["stayratio"] = ROUND($row["games"]/($row["games"]+$row["leaver"]), 3)*100;
	else $TopData[$c]["stayratio"] = 0;
	
	if ($row["wins"] >0 )
	$TopData[$c]["winslosses"] = ROUND($row["wins"]/($row["wins"]+$row["losses"]), 3)*100;
	else $TopData[$c]["winslosses"] = 0;
		
	//Highlight - sort
	if ( (isset($_GET["sort"]) AND $_GET["sort"] == "score") OR !isset($_GET["sort"]) ) 
	$TopData[$c]["score"] = "<span class='highlight_top'>".$TopData[$c]["score"]."</span>";
	
	if ( isset($_GET["sort"]) AND $_GET["sort"] == "games") 
	$TopData[$c]["games"] = "<span class='highlight_top'>".$TopData[$c]["games"]."</span>";
	
	if ( isset($_GET["sort"]) AND $_GET["sort"] == "wins") 
	$TopData[$c]["wins"] = "<span class='highlight_top'>".$TopData[$c]["wins"]."</span>";
	
	if ( isset($_GET["sort"]) AND $_GET["sort"] == "losses") 
	$TopData[$c]["losses"] = "<span class='highlight_top'>".$TopData[$c]["losses"]."</span>";
	
	if ( isset($_GET["sort"]) AND $_GET["sort"] == "losses") 
	$TopData[$c]["losses"] = "<span class='highlight_top'>".$TopData[$c]["losses"]."</span>";
	
	if ( isset($_GET["sort"]) AND $_GET["sort"] == "draw") 
	$TopData[$c]["draw"] = "<span class='highlight_top'>".$TopData[$c]["draw"]."</span>";
	
	if ( isset($_GET["sort"]) AND $_GET["sort"] == "kills") 
	$TopData[$c]["avg_kills"] = "<span class='highlight_top'>".$TopData[$c]["avg_kills"]."</span>";
	
	if ( isset($_GET["sort"]) AND $_GET["sort"] == "deaths") 
	$TopData[$c]["avg_deaths"] = "<span class='highlight_top'>".$TopData[$c]["avg_deaths"]."</span>";
	
	if ( isset($_GET["sort"]) AND $_GET["sort"] == "assists") 
	$TopData[$c]["avg_assists"] = "<span class='highlight_top'>".$TopData[$c]["avg_assists"]."</span>";
	
	if ( isset($_GET["sort"]) AND $_GET["sort"] == "ck") 
	$TopData[$c]["avg_creeps"] = "<span class='highlight_top'>".$TopData[$c]["avg_creeps"]."</span>";
	
	if ( isset($_GET["sort"]) AND $_GET["sort"] == "cd") 
	$TopData[$c]["avg_denies"] = "<span class='highlight_top'>".$TopData[$c]["avg_denies"]."</span>";
	
	if ( isset($_GET["sort"]) AND $_GET["sort"] == "nk") 
	$TopData[$c]["avg_neutrals"] = "<span class='highlight_top'>".$TopData[$c]["avg_neutrals"]."</span>";
	
	if ( isset($_GET["sort"]) AND $_GET["sort"] == "streak") 
	$TopData[$c]["maxstreak"] = "<span class='highlight_top'>".$TopData[$c]["maxstreak"]."</span>";
	
	//Display online/offline players - extra queries in loop. Care!
	if ( $OnlineOfflineOnTopPage==1 ) {
         $sth2 = $db->prepare("SELECT gs.gamename, gl.log_time, gs.gameid, gs.botid
		 FROM oh_game_status as gs 
		 LEFT JOIN oh_game_log as gl ON gl.gameid=gs.gameid 
		 WHERE gs.gamestatus<3 
		 AND gl.log_data LIKE '%	".$TopData[$c]["player"]."	%' 
		 AND gl.log_time > NOW() - INTERVAL 5 minute
		 ORDER BY gl.log_time DESC 
		 LIMIT 1;");
		 
         $result2 = $sth2->execute();
         $row2 = $sth2->fetch(PDO::FETCH_ASSOC);
		 $TopData[$c]["gamename"] = $row2["gamename"];
		 $TopData[$c]["gametime"] = date(OS_DATE_FORMAT, strtotime($row2["log_time"]));
		 $TopData[$c]["gameid"] = $row2["gameid"];
		 $TopData[$c]["botid"] = $row2["botid"];
	}
	
	$c++;
	}	
	if ( isset($GeoIP) AND $GeoIP == 1) geoip_close($GeoIPDatabase);
	
?>