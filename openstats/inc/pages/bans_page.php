<?php

if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

   	 $HomeTitle = $lang["bans"] ;
	 $HomeDesc = $lang["bans"];
	 $HomeKeywords = strtolower( os_strip_quotes($lang["bans"])).','.$HomeKeywords;
	 $MenuClass["bans"] = "active";
	 
	 if ( !isset($search_bans) ) $search_bans=$lang["search_bans"];
   $start = "%";
     if ( isset($_GET["search_bans"]) AND strlen($_GET["search_bans"])>=2  ) {
	    $search_bans = safeEscape( trim($_GET["search_bans"]));
		$sql = "AND (name) LIKE ? ";
		$start = "%";
	 } else $sql = "";
	 
	 if ( isset($_GET["search_bans"]) AND strlen($_GET["search_bans"])==1  ) {
	 	$search_bans = safeEscape( trim($_GET["search_bans"]));
		$sql = "AND (name) LIKE ? ";
		$start = "";
	 }
	 
	//echo $sql; die;
	 
	 $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_BANS." WHERE id>=1 AND expiredate>=NOW() $sql LIMIT 1");
	 
	 if ( !empty($sql) ) $sth->bindValue(1, $start.strtolower($search_bans)."%", PDO::PARAM_STR);
	 
     $result = $sth->execute();
	  
     //$result = $db->query("SELECT COUNT(*) FROM ".OSDB_BANS." WHERE id>=1 $sql LIMIT 1");
	 $r = $sth->fetch(PDO::FETCH_NUM);
	 $numrows = $r[0];
	 $result_per_page = $TopPlayersPerPage;
	 $draw_pagination = 0;
	 include('inc/pagination.php');
	 $draw_pagination = 1;
	  
	 $sth = $db->prepare("SELECT * FROM ".OSDB_BANS." 
	 WHERE id>=1 AND expiredate>=NOW() $sql 
	 ORDER BY id DESC LIMIT $offset, $rowsperpage");
	 
	  if ( !empty($sql) ) $sth->bindValue(1, $start.strtolower($search_bans)."%", PDO::PARAM_STR);
	 
	 $result = $sth->execute();
	 $c=0;
    $BansData = array();
	if ( file_exists("inc/geoip/geoip.inc") ) {
	include("inc/geoip/geoip.inc");
	$GeoIPDatabase = geoip_open("inc/geoip/GeoIP.dat", GEOIP_STANDARD);
	$GeoIP = 1;
	}
	while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	if ( isset($GeoIP) AND $GeoIP == 1) {
	$BansData[$c]["letter"]   = geoip_country_code_by_addr($GeoIPDatabase, $row["ip"]);
	$BansData[$c]["country"]  = geoip_country_name_by_addr($GeoIPDatabase, $row["ip"]);
	}
	if ($GeoIP == 1 AND empty($BansData[$c]["letter"]) ) {
                if( strlen($row["server"]) <= 2) {
                        $BansData[$c]["letter"] = "GAR";
                        $BansData[$c]["country"] = "Garena";
                } else {
                        if( strtolower($row["server"]) == "europe.battle.net" ) {
                                $BansData[$c]["letter"] = "EU";
                                $BansData[$c]["country"] = "Europe";
                        }
                        else if( strtolower($row["server"]) == "uswest.battle.net" OR strtolower($row["server"]) == "useast.battle.net" ) {
                                $BansData[$c]["letter"] = "US";
                                $BansData[$c]["country"] = "USA";
                        }
                        else if( strtolower($row["server"]) == "asia.battle.net" ) {
                                $BansData[$c]["letter"] = "CN";
                                $BansData[$c]["country"] = "Asia";
                        } else {
                                $BansData[$c]["letter"] = "A1";
                                $BansData[$c]["country"] = "Unknown";
                        }
                }
				
	if ( $row["name"] == "iprange") {
        $BansData[$c]["letter"] = "iprange";
        $BansData[$c]["country"] = "iprange";
     }	
	}
	
	$BansData[$c]["id"]        = (int)($row["id"]);
	$BansData[$c]["server"]  = ($row["server"]);
	$BansData[$c]["name"]  = ($row["name"]);
	$BansData[$c]["ip"]  = ($row["ip"]);
	$BansData[$c]["date"]  = date($DateFormat, strtotime($row["date"]));
	$BansData[$c]["gamename"]  = ($row["gamename"]);
	$BansData[$c]["admin"]  = ($row["admin"]);
	if ( empty($row["admin"]) ) $BansData[$c]["admin"] = '<span class="banned">[system]</span>';
	$BansData[$c]["reason"]  = substr(stripslashes($row["reason"]),0, 30);
	$BansData[$c]["reason"] = str_replace("&amp;#039;", "'", $BansData[$c]["reason"]);
	$BansData[$c]["reason_full"]  = ($row["reason"]);
	$BansData[$c]["expiredate"]   = ($row["expiredate"]);
	$BansData[$c]["warn"]  = ($row["warn"]);
	if ( empty($BansData[$c]["reason"]) )   $BansData[$c]["reason"] = '&nbsp;';
	if ( empty($BansData[$c]["gamename"]) ) $BansData[$c]["gamename"] = '&nbsp;';
	//$BansData[$c]["expiredate"]  = ($row["expiredate"]);
	//$BansData[$c]["warn"]  = ($row["warn"]);
	$c++;
	}

	if ( isset($GeoIP) AND $GeoIP == 1) geoip_close($GeoIPDatabase);
	
	//$db->free($result);	
?>