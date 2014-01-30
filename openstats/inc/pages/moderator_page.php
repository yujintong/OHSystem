<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

if ( !OS_is_moderator() ) { header("location: ".OS_HOME.""); die(); }

$HomeTitle = ($lang["moderator_panel"]);
$SearchValue = "";
$Button = "Add Ban";
$sql = "";
$orderby = "id DESC, date DESC";

if ( file_exists("inc/geoip/geoip.inc") ) {
	 include("inc/geoip/geoip.inc");
	 $GeoIPDatabase = geoip_open("inc/geoip/GeoIP.dat", GEOIP_STANDARD);
	 $GeoIP = 1;
	 }
	 
if ( isset($_GET["page"]) AND is_numeric($_GET["page"]) ) {
    $Page = (int) $_GET["page"];
} else $Page = "";

	
//DELETE BAN	
if ( isset($_GET["delete"]) AND is_numeric($_GET["delete"]) AND isset($_GET["option"]) AND $_GET["option"] == "bans" ) {
    $del = (int) $_GET["delete"];
	$player = safeEscape($_GET["player"]);
    $sth = $db->prepare( "DELETE FROM ".OSDB_BANS." WHERE id = '".$del."' " );
	$result = $sth->execute();
	OS_AddLog($_SESSION["username"], "[os_moderator] Deleted Ban: $player ");
	if ( !empty($Page) ) $link = "&page=".$Page; else $link = "";
	header("location: ".OS_HOME."?moderator&option=bans".$link );
	die();
}

//DELETE PP
if ( isset($_GET["delete"]) AND is_numeric($_GET["delete"]) AND isset($_GET["option"]) AND $_GET["option"] == "pp" ) {
    $del = (int) $_GET["delete"];
	$player = safeEscape($_GET["player"]);
    $sth = $db->prepare( "DELETE FROM ".OSDB_GO." WHERE id = '".$del."' " );
	$result = $sth->execute();
	OS_AddLog($_SESSION["username"], "[os_moderator] Deleted PP: $player ");
	if ( !empty($Page) ) $link = "&page=".$Page; else $link = "";
	header("location: ".OS_HOME."?moderator&option=pp".$link );
	die();
}

if (!isset($_GET["option"])) {

   	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_GAMES." WHERE stats = 1" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalRankedGames = number_format($r[0]);
	
	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_BANS." WHERE id>=1" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalBans = number_format($r[0]);
	
	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_STATS." WHERE id>=1" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalRankedUsers = number_format($r[0]);
	
}

    if ( isset($_GET["option"]) AND $_GET["option"] == "addban" ) {
	
	  if ( isset($_GET["edit"]) AND is_numeric($_GET["edit"]) ) {
	    
		$id = (int)$_GET["edit"];
		$sth = $db->prepare("SELECT * FROM ".OSDB_BANS." WHERE id = '".$id."' ");
   
        $result = $sth->execute();
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		$BanPlayer = $row["name"];
		$BanAdmin = $row["admin"];
		$BanServer = $row["server"];
		$BanReason = $row["reason"];
		$BanGamename = $row["gamename"];
   		$BanExpire = $row["expiredate"];
		$BansDate = date( OS_DATE_FORMAT, strtotime($row["date"]));
		$Button = "Edit Ban";
		
	  } else {
		$BanPlayer = "";
		$BanAdmin = "";
		$BanServer = "";
		$BanReason = "";
		$BanGamename = "";
   		$BanExpire = "";
		$BansDate = "";
		$BanExpireDate = "";
	  }
	  
	  
	  if ( isset($_POST["add_ban"]) ) {
	    $name     = safeEscape( trim( strtolower($_POST["player"])));
	    $server   = safeEscape( trim($_POST["server"]));
	    $reason   = EscapeStr( convEnt2(trim($_POST["reason"])));
	    $admin    = $_SESSION["username"];
	    $gn       = safeEscape( trim($_POST["gamename"]));
		$expire   = EscapeStr( trim($_POST["expire"]));
		if ( isset($_POST["pp"]) AND is_numeric($_POST["pp"]) AND $_POST["pp"]<=15 ) {
		$pp = (int) $_POST["pp"];
		} else $pp = "";
		
		//Find player IP
	    $sth = $db->prepare("SELECT * FROM ".OSDB_GP." WHERE name = '".$name."' AND ip!='' ORDER BY id DESC LIMIT 1");
		$result = $sth->execute();
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		$ip = $row["ip"];
		
		if ( date("Y", strtotime($expire) )<=1990 ) $expire = "";
		
	  if ( !empty($ip) AND $GeoIP == 1) {
	    $Letter   = geoip_country_code_by_addr($GeoIPDatabase, $ip);
		$country = $Letter ;
	  } else $country = "";
	  
	  if ( substr($ip,0,1) == ":" ) $name  = 'iprange';
	  
	  if ( !empty($ip) AND $ip!='0.0.0.0') {
	    $ipv = explode(".", $ip);
		if ( count($ipv)>=2 ) $ip_part = $ipv[0].".".$ipv[1];
		$ip_part = str_replace(":", "", $ip_part);
	  } else $ip_part = "";
		
		
		//ADD BAN
		if ( !isset($_GET["edit"]) ) {
		
		$db->insert( OSDB_BANS, array(
		"name" => $name,
		"server" => $server,
		"reason" => $reason,
		"admin" => $admin,
		"gamename" => $gn,
		"expiredate" => $expire,
		"date" => date( "Y-m-d H:i:s", time() ),
		"ip" => $ip,
		"ip_part" => $ip_part
           ));				 					 
					 
		$uid = $db->lastInsertId(); 
		
		if (!empty($pp)) {
			$db->insert( OSDB_GO, array(
		   "player_name" => $name,
		   "reason" => $reason,
		   "offence_time" => date( "Y-m-d H:i:s", time() ),
		   "offence_expire" => '0000-00-00 00:00:00',
		   "pp" => $pp,
		   "admin" => $admin
            ));
		}
		
		OS_AddLog($_SESSION["username"], "[os_moderator] Banned: $name ");
		header("location: ".OS_HOME."?moderator&option=addban&edit=".$uid);
		die();
		
		} else {
		
		//EDIT BAN
		$id = (int)$_GET["edit"];
		$sth = $db->prepare("UPDATE ".OSDB_BANS." SET name=:name, server=:server, reason=:reason, gamename=:gamename, expiredate=:expiredate
		WHERE id=:id LIMIT 1");
		$sth->bindValue(':name', $name, PDO::PARAM_STR); 
		$sth->bindValue(':server', $server, PDO::PARAM_STR); 
		$sth->bindValue(':reason', $reason, PDO::PARAM_STR); 
		$sth->bindValue(':gamename', $gn, PDO::PARAM_STR); 
		$sth->bindValue(':expiredate', $expire, PDO::PARAM_STR); 
		$sth->bindValue(':id', $id, PDO::PARAM_STR); 
		$result = $sth->execute();
		OS_AddLog($_SESSION["username"], "[os_moderator] Edit Ban: $name ");
		header("location: ".OS_HOME."?moderator&option=addban&edit=".$id);
		die();
		}
		
	  }
	
	}
	
  //LIST BANS
  if ( isset($_GET["option"]) AND $_GET["option"] == "bans" ) {
  $HomeTitle ="BANS :: MODERATOR";
  if ( isset($_GET["sban"]) AND strlen($_GET["sban"])>=2 ) {
    $SearchValue = trim(safeEscape( strip_tags($_GET["sban"]) ) );
    $sql.=" AND name LIKE ('%".$SearchValue."%') ";
    $HomeTitle ="Search Bans :: MODERATOR | $SearchValue";
  }
	
  $sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_BANS." WHERE id>=1 $sql" );
  $result = $sth->execute();
  $r = $sth->fetch(PDO::FETCH_NUM);
  $numrows = $r[0];
  
  $result_per_page = 20;
  $draw_pagination = 0;
  $SHOW_TOTALS = 1;
  include('inc/pagination.php');
  $draw_pagination = 1;
  
   $sth = $db->prepare("SELECT * FROM ".OSDB_BANS." 
   WHERE id>=1 $sql 
   ORDER BY $orderby 
   LIMIT $offset, $rowsperpage");
   
   $result = $sth->execute();
   
   $c=0;
   $BansData = array();
   while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
    $Letter = "";
	$Country = "";
	
    if ($GeoIP == 1 ) {
	$Letter   = geoip_country_code_by_addr($GeoIPDatabase, $row["ip"]);
	$Country  = geoip_country_name_by_addr($GeoIPDatabase, $row["ip"]);
	}
	
	$BansData[$c]["id"] = $row["id"];
	$BansData[$c]["letter"]  = $Letter;
	$BansData[$c]["country"] = $Country;
	$BansData[$c]["botid"] = $row["botid"];
	$BansData[$c]["server"] = $row["server"];
	$BansData[$c]["name"] = $row["name"];
	$BansData[$c]["ip"] = $row["ip"];
	$BansData[$c]["ip_part"] = $row["ip_part"];
	$BansData[$c]["country"] = $Country;
	$BansData[$c]["date"] = date( OS_DATE_FORMAT, strtotime($row["date"]));
	$BansData[$c]["gamename"] = $row["gamename"];
	$BansData[$c]["admin"] = $row["admin"];
	$BansData[$c]["reason"] = $row["reason"];
	$BansData[$c]["expiredate"] = date( OS_DATE_FORMAT, strtotime($row["expiredate"]));
	$BansData[$c]["expiredate_db"] = $row["expiredate"];

    $c++;
   }
   }
   
   //LIST PP
   if ( isset($_GET["option"]) AND $_GET["option"] == "pp" ) {
    $HomeTitle ="PENALTY POINTS :: MODERATOR";
    $orderby = "id DESC, offence_time DESC";
	$Button = "Add PP";
	if ( isset($_GET["edit"]) AND is_numeric($_GET["edit"]) ) {
	$Button = "Edit PP";
	  $id = (int) $_GET["edit"];
      $sth = $db->prepare("SELECT * FROM ".OSDB_GO." WHERE id='".$id."' ");
      $result = $sth->execute();
	  $row = $sth->fetch(PDO::FETCH_ASSOC);
	  $PP_PlayerName = $row["player_name"];
	  $PP_Reason = $row["reason"];
	  $PP_Value = $row["pp"];
	} else { $PP_PlayerName = ""; $PP_Reason = ""; $PP_Value="1"; }
	
	//PP POST FORM
	if ( isset($_POST["add_pp"]) ) {
	
	    $player_name     = safeEscape( trim( strtolower($_POST["player_name"])));
	    $pp   = (int)( trim($_POST["pp"]));
	    $reason   = EscapeStr( (trim($_POST["reason"])));
	    $admin    = $_SESSION["username"];
		$time = date("Y-m-d H:i:s", time() );
		
		if ( !empty($player_name) AND $pp>=1 ) {
		  
		  //UPDATE PP
		  if ( isset($_GET["edit"]) ) {
		$id = (int)$_GET["edit"];
		$sth = $db->prepare("UPDATE ".OSDB_GO." SET player_name=:player_name, pp=:pp, reason=:reason
		WHERE id=:id LIMIT 1");
		$sth->bindValue(':player_name', $player_name, PDO::PARAM_STR); 
		$sth->bindValue(':pp', $pp, PDO::PARAM_INT); 
		$sth->bindValue(':reason', $reason, PDO::PARAM_STR); 
		$sth->bindValue(':id', $id, PDO::PARAM_STR); 
		$result = $sth->execute();
		OS_AddLog($_SESSION["username"], "[os_moderator] Edit PP: $player_name ");
		if ( isset($Page) AND $Page>=1 ) $link = "&page=".$Page; else $link = "";
		header("location: ".OS_HOME."?moderator&option=pp&edit=".$id.$link);
		die();
		  } else {
		  //INSERT PP
			$db->insert( OSDB_GO, array(
		   "player_name" => $player_name,
		   "reason" => $reason,
		   "offence_time" => date( "Y-m-d H:i:s", time() ),
		   "offence_expire" => '0000-00-00 00:00:00',
		   "pp" => $pp,
		   "admin" => $admin
            ));
		  
		OS_AddLog($_SESSION["username"], "[os_moderator] Added PP: $player_name ");
		if ( isset($Page) AND $Page>=1 ) $link = "&page=".$Page; else $link = "";
		header("location: ".OS_HOME."?moderator&option=pp");
		die();
		  }
		
		}
	
	}
	
	
  if ( isset($_GET["spp"]) AND strlen($_GET["spp"])>=2 ) {
    $SearchValue = trim(safeEscape( strip_tags($_GET["spp"]) ) );
    $sql.=" AND player_name LIKE ('%".$SearchValue."%') ";
    $HomeTitle ="Search PP :: MODERATOR | $SearchValue";
  }
   
    $sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_GO." WHERE id>=1 $sql" );
    $result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $numrows = $r[0];
  
    $result_per_page = 30;
    $draw_pagination = 0;
    $SHOW_TOTALS = 1;
    include('inc/pagination.php');
    $draw_pagination = 1;
	
   $sth = $db->prepare("SELECT * FROM ".OSDB_GO." 
   WHERE id>=1 $sql 
   ORDER BY $orderby 
   LIMIT $offset, $rowsperpage");
    $result = $sth->execute();
	$PPData = array();
	$c = 0;

    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
    $PPData[$c]["id"] = $row["id"];
	$PPData[$c]["player_name"] = $row["player_name"];
	$PPData[$c]["reason"] = $row["reason"];
	$PPData[$c]["offence_time"] = date( OS_DATE_FORMAT, strtotime($row["offence_time"]));
	$PPData[$c]["pp"] = $row["pp"];
	$PPData[$c]["admin"] = $row["admin"];
    $c++;	
    }
   
   }
   
   
   if ( isset($GeoIP) AND $GeoIP == 1) geoip_close($GeoIPDatabase);

?>