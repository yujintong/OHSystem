<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
$errors = "";
$message = "";
$ip_part = "";

//THIS CODE BELOW WILL BAN ENTIRE COUNTRY FROM STATS TABLE BY GIVEN COUNTRY CODE (EG. IT)
if ( isset($_GET["code"]) AND strlen($_GET["code"]) == 2 ) {

    $code = strtoupper($_GET["code"]);
    $sth = $db->prepare("SELECT * FROM ".OSDB_STATS." WHERE country_code = '$code' AND banned = 0 ");
    $result = $sth->execute();
	
	$sql="INSERT INTO ".OSDB_BANS."(name, server, reason, ip, ip_part, admin, gamename, date, expiredate, country) VALUES";
	$time = date("Y-m-d H:i:s", time() );
	$Total = 0;
	$AllIP = array();
	while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	   	$ipv = explode(".", $row["ip"]);
		if ( count($ipv)>2 ) {
		$ip_part = $ipv[0].".".$ipv[1];
		if ( strlen($ipv[1])<=2) $ip_part.=".";
		$AllIP[] = $ip_part;
		//$sql.=" ('iprange [".strtoupper($code)."]', 'europe.battle.net', '".strtoupper($code)."',  ':$ip_part', '$ip_part', 'Server', 'All', '$time', '0000-00-00 00:00:00', '".strtoupper($code)."'),";
		
		
		}
	}
	
	$IPAddr = array_unique($AllIP);
	 foreach($IPAddr as $ip) {
	 $Total++;  
	 $sql.=" ('iprange [".strtoupper($code)."]', 'europe.battle.net', '".strtoupper($code)."',  ':$ip', '$ip', 'Server', 'All', '$time', '0000-00-00 00:00:00', '".strtoupper($code)."'),";
	 }
	 
	if ( isset($_GET["exec"]) ) {
		$upd = $db->prepare("UPDATE ".OSDB_STATS." SET banned = 1 WHERE country_code = '$code' AND banned = 0 ");
        $result = $upd->execute();
	}
	 
	if ($Total>=1) {
	$sql = substr($sql,0, -1);
	?><textarea style="margin: 2px; width: 892px; height: 134px;"><?=$sql?></textarea>
	<div><a href="<?=OS_HOME?>adm/?bans&amp;code=<?=$_GET["code"]?>&amp;exec">Execute SQL</a> (Total: <?=$Total?> records) </div>
	<?php
	if ( isset($_GET["exec"]) ) {
	$sth = $db->prepare($sql);
    $result = $sth->execute();
	?>
	<b>Query successfully executed!</b> 
	<a href="<?=OS_HOME?>adm/?bans&amp;code=<?=$_GET["code"]?>">REFRESH</a>
	<?php
	}
	} else {
	?>
	<div>No results.</div>
	<?php
	}
	
	
	}
	
	 
	 if ( file_exists("../inc/geoip/geoip.inc") ) {
	 include("../inc/geoip/geoip.inc");
	 $GeoIPDatabase = geoip_open("../inc/geoip/GeoIP.dat", GEOIP_STANDARD);
	 $GeoIP = 1;
	 }
	 
   if ( isset($_GET["page"]) AND is_numeric($_GET["page"]) ) $p = "&amp;page=".(int) $_GET["page"];
   else $p = "";
   
   if ( isset($_GET["ip_range"]) AND strlen($_GET["ip_range"]) >= 4 )
	  $ipr = trim( strip_tags( $_GET["ip_range"] ) ); else $ipr ="";
	 
   
   //UPDATE PP
   if ( isset( $_GET["updatePP"]) ) {
       $sth = $db->prepare("SELECT player_name,
       SUM(case when(pp >= 1) then pp else 0 end) as points	   
	   FROM ".OSDB_GO." 
	   GROUP BY player_name
	   LIMIT 1600"); 
       $result = $sth->execute();
	   
	    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
		 echo $row["player_name"]." - ".$row["points"]."<br />";
		 
		 if ( !empty($row["player_name"]) ) {
		 //$upd = $db->prepare("UPDATE ".OSDB_STATS." SET penalty = '".$row["points"]."' WHERE player = '".$row["player_name"]."' ");
		 //$result = $upd->execute();
		 }
		}
   }
   
   //UPDATE IP PART FIELD
   if ( isset($_GET["ip_part"]) ) {
   $sth = $db->prepare("SELECT * FROM ".OSDB_BANS." WHERE ip_part='' LIMIT 100"); 
   $result = $sth->execute();
   while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
    $ip = $row["ip"];
	
	 if ( !empty($ip) AND $ip!='0.0.0.0' AND !strstr($ip, ":") ) {
	    $ipv = explode(".", $ip);
		if ( count($ipv)>2 ) {
		$ip_part = $ipv[0].".".$ipv[1];
		$sth2 = $db->prepare("UPDATE ".OSDB_BANS." SET ip_part='$ip_part' WHERE id ='".$row["id"]."'"); 
		$result = $sth2->execute();
		}
		
	  } else $ip_part = "";
   
   }
}	 
	 
	//GET TOP BANS by Country code
	if ( isset($_GET["top_bans"]) ) {
	
     $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_BANS." WHERE id>=1 LIMIT 1");
	 $result = $sth->execute();
	 $r = $sth->fetch(PDO::FETCH_NUM);
	 $numrows = $r[0];
      
	$sth = $db->prepare("SELECT country, COUNT(country) AS top_bans FROM ".OSDB_BANS." 
	WHERE id>=1 AND country!='' AND ip!='0.0.0.0' GROUP BY country ORDER BY COUNT(country) DESC LIMIT 10");
	$result = $sth->execute();
	?>
	<h2>Top Bans By Country</h2>
	<table>
	<tr>
	  <th>Flag</th>
	  <th>Country</th>
	  <th>Total</th>
	  <th>%</th>
    </tr>
	<?php
	?><?php
	while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	$total = number_format( ($row["top_bans"] / $numrows)*100, 1);
	?>
	<tr>
	  <td width="32"><?=OS_ShowUserFlag( $row["country"], $row["country"], 175 )?></td>
	  <td width="64"><?=$row["country"]?></td>
	  <td width="120"><b><?=$row["top_bans"]?></b> / <?=$numrows?></td>
	  <td><?=$total?> %</td>
	</tr>
	<?php
	}
	?>
	</table>
	<?php
    }	

//UPDATE COUNTRY
if ( isset($_GET["update_countries"]) ) {

   $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_BANS." WHERE id>=1 AND country='' AND ip NOT LIKE (':%') AND ip!='' LIMIT 1");
   $result = $sth->execute();
   $r = $sth->fetch(PDO::FETCH_NUM);
   $numrows = $r[0];
   
   $sth = $db->prepare("SELECT * FROM ".OSDB_BANS." 
   WHERE id>=1 AND country='' AND ip NOT LIKE (':%') AND ip!='' 
   ORDER BY RAND() LIMIT 100");
   $result = $sth->execute();
   
   $total = $sth->rowCount();
   echo "Total: <b>$numrows</b> ";
   
   	$Letter = ""; $Country = "";
    $c = 0;
   while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
    if ($GeoIP == 1 AND !empty($row["ip"]) AND $row["ip"]!="0.0.0.0" ) {
	    if (!empty($row["ip"]) AND $row["ip"]!="0.0.0.0" AND !strstr($row["ip"], ":") ) {
	    $Letter   = geoip_country_code_by_addr($GeoIPDatabase, $row["ip"]);
	    $Country  = geoip_country_name_by_addr($GeoIPDatabase, $row["ip"]);
		//echo $Country."<br>";
        $upd = $db->prepare("UPDATE ".OSDB_BANS." SET country='$Letter' WHERE id = '".$row["id"]."' ");
	    $result = $upd->execute();
		$c++;
	    } 
		 else { $Letter = ""; $Country = ""; }
	}
   
   }
   echo " | Updated: $c <a href='?bans' class='menuButtons'>Refresh</a>";
   OS_AddLog($_SESSION["username"], "[os_updateips] Updated: $c IPs )");
   //if ( isset($GeoIP) AND $GeoIP == 1) geoip_close($GeoIPDatabase);
}

if ( isset($_GET["search_bans"]) ) $s = safeEscape($_GET["search_bans"]); else $s=""; 
?>
<div align="center" class="padBottom">
	 <form action="" method="get">
	 <table>
	   <tr>
	    <td width="320">
		
		  <input type="hidden" name="bans" />
		  <input style="width: 180px; height: 24px;" type="text" name="search_bans" value="<?=$s?>" />
		  <input class="menuButtons" type="submit" value="Search" />
		</td>
	    <td>
		
		  <input type="hidden" name="bans" />
		  <input style="width: 80px; height: 24px;" type="text" name="ip_range" value="<?=$ipr?>" />
		  <input class="menuButtons" type="submit" value="Search IP Range" />
<?php
  $sel=array();
  $sel[0] = ""; $sel[1] = ""; $sel[2] = ""; $sel[3] = ""; $sel[4] = ""; $sel[5] = ""; $sel[6] = "";
  if ( isset($_GET["sort"])) {
    if ($_GET["sort"] =="id" )       $sel[6] = 'selected="selected"';
    if ($_GET["sort"] =="name" )     $sel[0] = 'selected="selected"'; 
	if ($_GET["sort"] =="date" )     $sel[1] = 'selected="selected"'; 
	if ($_GET["sort"] =="expire" )   $sel[2] = 'selected="selected"'; 
	if ($_GET["sort"] =="warns" )    $sel[3] = 'selected="selected"'; 
	if ($_GET["sort"] =="bannedby" ) $sel[4] = 'selected="selected"'; 
	if ($_GET["sort"] =="country" )  $sel[5] = 'selected="selected"'; 
  }
?>
		Sort: 
		<select name="sort">
		 <option></option>
		 <option <?=$sel[6]?> value="id">by ID</option>
		 <option <?=$sel[0]?> value="name">by Name</option>
		 <option <?=$sel[1]?> value="date">by Ban Date</option>
		 <option <?=$sel[2]?> value="expire">by Expire Date</option>
		 <option <?=$sel[3]?> value="warns">by Warns</option>
		 <option <?=$sel[4]?> value="bannedby">by Banned by</option>
		 <option <?=$sel[5]?> value="country">by Country</option>
		</select>
		<input type="submit" value="Sort" class="menuButtons" />
		
		<a href="<?=OS_HOME?>adm/?bans&amp;autoban" class="menuButtons">Mass Ban Leavers</a>
		
		</td>
	   </tr>
	   <tr>
	    <td>
		<?php if ( !isset($_GET["duplicate"]) ) { ?>
	    <a class="menuButtons" href="<?=OS_HOME?>adm/?bans&amp;add">[+]Add Ban</a> 
		<a class="menuButtons" href="<?=OS_HOME?>adm/?bans&amp;duplicate">Duplicate bans</a>
		<?php } else { ?>
		<a class="menuButtons" href="<?=OS_HOME?>adm/?bans">Show All bans</a>
		<?php } ?>
		<a class="menuButtons" href="<?=OS_HOME?>adm/?bans&amp;addcountry">[+]Ban Country</a> 
		<div>
		<?php if (isset($_GET["check_ip_range"]) ) $check_ip_range = strip_tags($_GET["check_ip_range"]); else 
		$check_ip_range = "";
		?>
        <input type="text" name="check_ip_range" value="<?=$check_ip_range?>" size="10" /> <input type="submit" value="IP Range check" />
		</div>
		 </td>
		<td>
		 <a class="menuButtons" href="<?=OS_HOME?>adm/?bans&amp;update_ips<?=$p?>">Upd IPs on this page</a>
         <a class="menuButtons" href="<?=OS_HOME?>adm/?bans&amp;update_countries<?=$p?>">Upd Countries</a>
		 <!--<a class="menuButtons" href="<?=OS_HOME?>adm/?bans&amp;remove_expired">Remove expired bans</a>-->
		 <a class="menuButtons" href="<?=OS_HOME?>adm/?bans&amp;top_bans<?=$p?>">Bans By Country</a>
		 <?php if (!isset($_GET["showPP"]) ) { ?>
		 <a class="menuButtons" href="<?=OS_HOME?>adm/?bans&amp;showPP<?=$p?>">Show PP</a>
		 <?php } else { ?>
		 <a class="menuButtons" href="<?=OS_HOME?>adm/?bans<?=$p?>">Hide PP</a>
		 <?php } ?>
		 <div>
		 <?php if (isset($_GET["code"]) AND strlen($_GET["code"]) == 2 ) $code = $_GET["code"]; else $code =""; ?>
		 <input type="text" size="2" name="country_code" id="cc" value="<?=$code?>" /> 
		 <input type="button" value="Create qry" onclick="location.href='<?=OS_HOME?>adm/?bans&code='+cc.value" />
		 Create ban sql query by country code (from stats table).
		 </div>
		 </td>
	   </tr>
	 </table>
	 </form>
</div>
<?php

  if ( isset($_GET["autoban"]) ) include("admin_bans_auto.php"); else {
  
    //BAN COUNTRY
	if(isset($_GET["addcountry"])) {
	$err = "";
	$errc = "";
	 if ( isset($_POST["ban_country"]) ) {
	    $cd = strtoupper( trim($_POST["text"]));
		$all = explode(" ", $cd);
		
		foreach ($all as $e) {
		 $e = trim($e);
		 if ( strlen($e)>2 ) $errc.="<b>$e</b>,";
		}
		if (!empty($errc)) {
		$errc = substr($errc,0, strlen($errc)-1 );
		$err.="<div>The following codes are not valid: <div style='color:red;'>$errc</div></div>";
		} else {
		//INSERT CODES IN DB
		$sth = $db->prepare("UPDATE ".OSDB_CUSTOM_FIELDS." SET field_value = '$cd' 
	    WHERE field_id = 1 AND field_name = 'oh_country_ban'");
		$result = $sth->execute();
		$err.="<div style='color:green;'>Saved!</div>";
		OS_AddLog($_SESSION["username"], "[os_countryban] Country ban: ( $cd )");
		}
		
	 }
	
	 $sth = $db->prepare("SELECT * FROM ".OSDB_CUSTOM_FIELDS." 
	 WHERE field_id = 1 AND field_name = 'oh_country_ban'");
	 $result = $sth->execute();
	 
	 $row = $sth->fetch(PDO::FETCH_ASSOC);
	 $codes = $row["field_value"];
	 
	 ?>
	 <div>
	 <table>
	 <tr>
	 <td width="495">
	 <form action="" method="post">
	   <textarea style="width:480px;height:80px;" name="text"><?=$codes?></textarea>
	   <div><input class="menuButtons" type="submit" value="Click here to save" name="ban_country" /></div>
	 </form>  
	 <td>
	   <div>Separate each country code with a space.</div>
	   <div><a href="http://en.wikipedia.org/wiki/ISO_3166-1#Officially_assigned_code_elements" target="_blank">Two-letter country code</a>
	   </div>
	   <?php if (isset($err) AND !empty($err)) echo $err; ?>
	  </td>
	  </td>
	  </tr>
	  </table>
	 </div>
	 <?
	}

//remove expired
  if ( isset( $_GET["remove_expired"]) AND isset($ThisIsON)) {
     $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_BANS." 
	 WHERE expiredate<=NOW() AND expiredate != '' AND expiredate!='0000-00-00 00:00:00'");
	 $result = $sth->execute();
	 $r = $sth->fetch(PDO::FETCH_NUM);
	 $numrows = $r[0];
	 
	 //CREATING LIST FOR STATS TABLE (update banned to 0)
	 $sth = $db->prepare("SELECT * FROM ".OSDB_BANS." 
	 WHERE expiredate<=NOW() AND expiredate != '' AND expiredate!='0000-00-00 00:00:00' LIMIT 100");
	 $result = $sth->execute();
	 
	 $c=0;
	 $ExpiredBans = array();
	 while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	    $ExpiredBans[] = $row["name"];
		$c++;
	  }
	  //PREPARE QUERY
	  $sql = "UPDATE ".OSDB_STATS." SET banned = 0 WHERE  ";
	  foreach( $ExpiredBans as $Player) {
	    $sql.=" player = '".$Player."' OR ";
	  }
	  
	  if ( $c>=1 ) {
	  $sql = substr($sql,0, -3);
	  $sth = $db->prepare( $sql );
	  $result = $sth->execute();
	  }
	 ?>
	  <div align="center">
	  <h2>Expired bans: <?=$c?> / <?=$numrows ?> (deleted)</h2>
	  </div>
	 <?php
     $sth = $db->prepare("DELETE FROM ".OSDB_BANS." 
	 WHERE expiredate<=NOW() AND expiredate != '' AND expiredate!='0000-00-00 00:00:00' ");
	 $result = $sth->execute();
	 
	 OS_AddLog($_SESSION["username"], "[os_editban] Removed expired bans ($c / $numrows)");
  }

//delete
  if ( isset( $_GET["del"]) AND is_numeric($_GET["del"]) ) {
      $id = safeEscape( (int) $_GET["del"] );
	    $sth = $db->prepare("SELECT * FROM ".OSDB_BANS." WHERE id = '".$id."' LIMIT 1");
		$result = $sth->execute();
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		$name = $row["name"];
	  $sth = $db->prepare("DELETE FROM ".OSDB_BANS." WHERE id ='".(int)$id."' LIMIT 1 ");
	  $result = $sth->execute();
	  
	  $upd = $db->prepare("UPDATE ".OSDB_STATS." SET banned = 0 WHERE LOWER(player) = LOWER('".$name."') LIMIT 1");
	  $result  = $upd->execute();
	  OS_AddLog($_SESSION["username"], "[os_editban] Removed ban ($name)");
	 
	  ?>
	  <div align="center">
	  <h2>Ban successfully deleted. <a href="<?=OS_HOME?>adm/?bans">&laquo; Back</a></h2>
	  </div>
	  <?php 
  }
  
//eDIT
  if ( (isset( $_GET["edit"]) AND is_numeric($_GET["edit"]) ) OR isset($_GET["add"])  ) {
   $name = ""; $server = ""; $reason = ""; $ip = ""; $admin = ""; $gn=""; $date = date("Y-m-d H:i:00", time());
   $expire = ""; $warn="";
   if ( isset($_GET["edit"])  AND is_numeric($_GET["edit"])  ) $id = safeEscape( (int) $_GET["edit"] );
   //UPDATE
    if ( isset($_POST["edit_ban"]) ) {
	  $name     = safeEscape( trim( strtolower($_POST["name"])));
	  $server   = safeEscape( trim($_POST["server"]));
	  $reason   = EscapeStr( convEnt2(trim($_POST["reason"])));
	  $ip       = EscapeStr( trim($_POST["ip"]));
	  
	  $country       = EscapeStr( trim($_POST["country"]));
	  
	  //Force set name to 'iprange'
	  if ( substr($ip,0,1) == ":" ) $name  = 'iprange';
	  
	  if ( !empty($ip) AND $ip!='0.0.0.0') {
	    $ipv = explode(".", $ip);
		if ( count($ipv)>=2 ) $ip_part = $ipv[0].".".$ipv[1];
		$ip_part = str_replace(":", "", $ip_part);
	  } else $ip_part = "";
	  
	  $admin    = safeEscape( trim($_POST["admin"]));
	  $gn       = safeEscape( trim($_POST["gn"]));
	  $date       = EscapeStr( trim($_POST["date"]));
	  $expire       = EscapeStr( trim($_POST["expire"]));
	  $warn       = EscapeStr( trim($_POST["warn"]));
	  
	  if ( empty($ip) ) {
	    $sth = $db->prepare("SELECT * FROM ".OSDB_GP." WHERE name = '".$name."' AND ip!='' LIMIT 1");
		$result = $sth->execute();
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		$ip = $row["ip"];
	  }
	  
	  if ( date("Y", strtotime($expire) )<=1990 ) $expire = "";
	  
	  if ( strlen( $name)<=2 ) $errors.="<div>Field Name does not have enough characters</div>";
	  
	  if ( !OS_IsRoot() AND strstr($ip, ":") )
	  $errors.="<div style='color: #ab0900; font-weight:bold;'><img src='del.png' alt='delete' width='16' height='16' class='imgvalign' /> You don't have permission to ban IP range</div>";
	  
	  
	  $time = date( "Y-m-d H:i:s", time() );
	  
	  if ( !empty($ip) AND $GeoIP == 1) {
	    $Letter   = geoip_country_code_by_addr($GeoIPDatabase, $ip);
	    //$Country  = geoip_country_name_by_addr($GeoIPDatabase, $ip);
		$country = $Letter ;
	  } else $country = "";
	  
	  if ( isset($_GET["edit"]) ) {
	  $sql = "UPDATE ".OSDB_BANS." SET 
	  name= '".$name."', server = '".$server."', reason = '".$reason."', ip='".$ip."', ip_part='".$ip_part."', admin = '".$admin."', gamename='".$gn."', expiredate='".$expire."', warn = '".$warn."', date='".$date."', country = '".$country."' WHERE id ='".$id."' LIMIT 1 ";
	  
	  //$sql2 = "UPDATE ".OSDB_GO. " SET player_name = '".$name."', reason = '".$reason."', offence_expire = //'".$expire."', pp = '".$warn."', admin = '".$admin."'
	  //WHERE player_name='".$name."' AND offence_time = '".$date."' ";
	  
	  }
	  
	  if ( isset($_GET["add"]) ) {

        if ( $name=="iprange" AND !isset($_GET["confirm"]) ) {
		  $chk = $db->prepare("SELECT COUNT(*) FROM ".OSDB_BANS." WHERE name='iprange' AND ip='".$ip."' ");
		  $result  = $chk->execute();
		  $r = $chk->fetch(PDO::FETCH_NUM);
		  
		  if ($r[0]>=1) {
			$errors.= "<h3>".$ip.' already banned. <a href="'.OS_HOME.'adm/?bans&amp;add=iprange&amp;ip='.$ip.'&amp;confirm">Continue?</a></h3>';
		  }
		}
	  
	  $sql = "INSERT INTO ".OSDB_BANS."(name, server, reason, ip, ip_part, admin, gamename, date, expiredate, warn, country) 
	  VALUES('".$name."', '".$server."', '".$reason."', '".$ip."', '".$ip_part."', '".$admin."', '".$gn."', '".$time ."', '".$expire."', '".$warn."', '".$country."' )";
	  
	  if ( empty($admin) ) $admin= "[system]";
	  
	  if ( $name!="iprange" AND !strstr($ip, ":") ) {
	  $sql2 = "INSERT INTO ".OSDB_GO."(player_name, reason, offence_time, offence_expire, pp, admin) 
	  VALUES('".$name."', '".$reason."', '".$time."', '".$expire."', '".$warn."', '".$admin."' )";
	  
	  //$sql3 = "UPDATE ".OSDB_STATS." SET penalty = penalty+'".$warn."' WHERE player = '".$name."' LIMIT 1";
	  }
	  
	  }
	  
	  $sth = $db->prepare("UPDATE ".OSDB_STATS." SET banned = 1 WHERE LOWER(player) = LOWER('".$name."') LIMIT 1");
	  $result  = $sth->execute();
	  
	  
	  if ( empty($errors) ) {
	  
	  $sth = $db->prepare($sql);
	  $result = $sth->execute();
	  
	  if (!empty($sql2) ) { 
	  $sth = $db->prepare($sql2);
	  $result = $sth->execute();
	  }
	  
	  if ( isset($sql3) ) {
	  $sth = $db->prepare($sql3);
	  $result = $sth->execute(); 
	  }
	  
	  if ( isset($_GET["add"]) )  OS_AddLog($_SESSION["username"], "[os_addban] Added ban: $name");
	  if ( isset($_GET["edit"]) ) OS_AddLog($_SESSION["username"], "[os_editban] Edited ban: $name");
	  
	  if ( $result ) {
	  	  ?>
	  <div align="center">
	    <h2>Ban successfully updated. <a href="<?=OS_HOME?>adm/?bans">&laquo; Back</a></h2>
	  </div>
	  <?php 
	  }
	 } else {
	?>
	<div align="center"><?=$errors?></div>
	<?php
	}
	}
  
     if ( isset($_GET["edit"])  AND is_numeric($_GET["edit"])  ) {
	 $sth = $db->prepare("SELECT * FROM ".OSDB_BANS." WHERE id = '".$id."' ");
	 $result = $sth->execute();
	 $row = $sth->fetch(PDO::FETCH_ASSOC);
	 $name     = ( $row["name"]);
	 $server   = ( $row["server"]);
	 $reason   = ($row["reason"]);
	 $ip       = ( $row["ip"]);
	 $ip_part       = ( $row["ip_part"]);
	 $admin    = ( $row["admin"]);
	 $gn       = ( $row["gamename"]);
	 $date       = ( $row["date"]);
	 $expire       = ( $row["expiredate"]);
	 $warn       = ( $row["warn"]);
	 $country = $row["country"];
	 $button = "Edit Ban";
	 
	  if ( isset($_GET["findip"]) ) {
	    $sth2 = $db->prepare("SELECT * FROM ".OSDB_GP." 
		WHERE name = '".$name."' AND ip!='' AND ip!='0.0.0.0' ORDER BY id DESC LIMIT 1");
		$result = $sth2->execute();
		$row2 = $sth2->fetch(PDO::FETCH_ASSOC);
		$foundIP = $row2["ip"];
	  }
	 
	 } else { 
	 $button = "Add Ban"; 
	 $country = "";
	 if ( $_SERVER["REQUEST_METHOD"] != "POST"  AND isset($_GET["confirm"]) ) {
	   $button.=" <CONFIRM BAN>";
	 }
	 $warn       = 1;
	 if (isset($_GET["add"]) AND !empty($_GET["add"]) AND strlen($_GET["add"])>=2 ) {
	   $name = safeEscape( $_GET["add"]);
	   $date = date("Y-m-d H:i:00", time());
	   $expire = "";
	   
	   $foundIP = "";
	   //echo "WARN: ".$warn;
	   if ( isset($_GET["reason"]) AND strlen($_GET["reason"])>=3 ) $reason = $_GET["reason"];
	   else $reason = "";
	   
	   if ( isset($_GET["realm"]) AND strlen($_GET["realm"])>=3 ) $server = $_GET["realm"];
	   else $server = "";
	   
	   if ( isset($_GET["ip"]) AND strlen($_GET["ip"])>=4 AND $_GET["ip"]!="0.0.0.0") $ip = $_GET["ip"];
	   
	 }
	 
	 }
	 
	 if ( isset($_GET["add"]) AND isset($_GET["gid"]) AND is_numeric($_GET["gid"]) ) {
	 $gn = "#".(int)$_GET["gid"];  
	 $admin = $_SESSION["username"];
	 }
	 ?>
	 <div align="center">
	 <form action="" method="post">
	 <h2><?=$button?></h2>
	 <table>
	   <tr class="row">
	     <td width="80" class="padLeft">Name:</td>
		 <td><input name="name" style="width: 380px; height: 28px;" type="text" value="<?=$name ?>" /></td>
	   </tr>
	   <tr class="row">
	     <td width="80"  class="padLeft">Server:</td>
		 <td><input name="server" style="width: 380px; height: 28px;" type="text" value="<?=$server?>" /></td>
	   </tr>
	   <tr class="row">
	     <td width="80"  class="padLeft">Reason:</td>
		 <td><input name="reason" style="width: 380px; height: 28px;" type="text" value="<?=$reason?>" /></td>
	   </tr>
	   <tr class="row">
	     <td width="80"  class="padLeft">Gamename:</td>
		 <td><input name="gn" style="width: 380px; height: 28px;" type="text" value="<?=$gn?>" /></td>
	   </tr>
	   <tr class="row">
	     <td width="80"  class="padLeft">IP:</td>
		 <td>
		 <input name="ip" id="foundip" style="width: 380px; height: 28px;" type="text" value="<?=$ip?>" />
		 <?php if (!isset($_GET["add"])) { ?>
		 <a href="<?=OS_HOME?>adm/?bans&amp;edit=<?=(int)$_GET["edit"]?>&amp;findip">Search IP in DB</a>
		 <?php if (!empty($foundIP) ) {?>
		 <input style="width: 120px; height: 28px;" type="text" value="<?=$foundIP?>" />
		 <a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=$foundIP?>', 'foundip')" >Set IP</a>
		 <?php } ?>
		 <?php } ?>
		 <div>
		  <input type="text" style="width:60px;" disabled="disabled" value="<?=$ip_part?>" /> IP Part
		  <?php if (!empty($ip_part)) { ?>
		  <a href="<?=OS_HOME?>adm/?bans&amp;ip_range=<?=$ip_part?>" class="menuButtons">Check this IP Range</a>
		  <?php } ?>
		 </div>
		 </td>
	   </tr>
	   <tr class="row">
	     <td width="80"  class="padLeft">Banned by:</td>
		 <td><input name="admin" style="width: 380px; height: 28px;" type="text" value="<?=$admin?>" /></td>
	   </tr>
	   
	   <tr class="row">
	     <td width="80"  class="padLeft">Ban date:</td>
		 <td><input name="date" id="bandate" style="width: 180px; height: 28px;" type="text" value="<?=$date?>" />
		 YYYY-MM-DD H:i:s 
	<div class="padTop padBottom">
	 Set: <a href="javascript:;" class="menuButtons"  onclick="SetDateField('<?=date("Y-m-d H:i:00", time() )?>', 'bandate')" >Today</a>
	</div>
		 </td>
	   </tr>
	   
	   <tr class="row">
	     <td width="80"  class="padLeft">Expire date:</td>
		 <td><input name="expire" id="expiredate" style="width: 180px; height: 28px;" type="text" value="<?=$expire?>" />
	<div class="padTop padBottom">
	 Set: 
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600 )?>', 'expiredate')" >+1h</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*10 )?>', 'expiredate')" >+10h</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24 )?>', 'expiredate')" >+1 day</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*48 )?>', 'expiredate')" >+2 days</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*72 )?>', 'expiredate')" >+3 days</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*5 )?>', 'expiredate')" >+5 days</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*7 )?>', 'expiredate')" >+7 days</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*30 )?>', 'expiredate')" >+1 month</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*60 )?>', 'expiredate')" >+2 months</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*90 )?>', 'expiredate')" >+3 months</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*30*12 )?>', 'expiredate')" >+1 year</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*30*24 )?>', 'expiredate')" >+2 year</a>
    		 <div>YYYY-MM-DD H:i:s (<b>Leave blank</b> if you do not want ban expire)</div>
	</div>
		 </td>
	   </tr>
	   
	   <tr class="row">
	     <td width="80"  class="padLeft">Penalty points:</td>
		 <td><?php if (isset($_GET["add"]) ) $type="text"; else $type="hidden" ?><input type="<?=$type?>" size="1" value="<?=$warn?>" name="warn" /></td>
	   </tr>
	   
	   <tr class="row">
	     <td width="80"  class="padLeft">Country:</td>
		 <td><input type="text" size="2" maxlength="2" value="<?=$country?>" name="country" /></td>
	   </tr>
	   
	   <tr>
	     <td width="80"></td>
		 <td class="padTop padBottom">
		 <input type="submit" value="Submit" name="edit_ban" class="menuButtons" /> &nbsp; &nbsp; &nbsp; &nbsp;
		 <a class="menuButtons" href="<?=OS_HOME?>adm/?bans">&laquo; Back to Bans</a>
<?php if ( isset($_GET["edit"])  AND is_numeric($_GET["edit"])  ) { ?>
		 <a onclick="if (confirm('Delete ban?') ) { location.href='<?=OS_HOME?>adm/?bans&amp;del=<?=$id?>' }" class="menuButtons" href="javascript:;">&times; Delete Ban</a><?php } ?>
		 </td>
	   </tr>
	  </table>
	 </form>
	  </div>
	  
	  <div class="padBottom"></div>
	 <?php
  }
  
  	//CHECK IP RANGE
	
	if ( isset($_GET["ip_range"]) AND strlen($_GET["ip_range"]) >= 4 AND empty($_GET["check_ip_range"]) ) {
	  $ipr = trim( strip_tags( $_GET["ip_range"] ) );
	  $errors = "";
	  $iprange = explode(".", $ipr);
	  
	  if ( COUNT($iprange)>=2 AND COUNT($iprange)<=4) {
	    if ( !is_numeric( $iprange[0] ) AND !is_numeric($iprange[1]) ) $errors=1;
		if ( $iprange[0]<0 OR $iprange[0]>255 ) $errors=2;
		if ( $iprange[1]<0 OR $iprange[1]>255 ) $errors=3;
		
	  } else $errors = 4;
	  
	  if ( $errors >= 1 ) echo '<h2>Invalid IP Address (errn:'.$errors.')</h2>'; else {
	  
	  $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_STATS." WHERE ip LIKE('".$ipr."%') LIMIT 1");
	  $result = $sth->execute();
	  $r = $sth->fetch(PDO::FETCH_NUM);
	  $TotalStats = $r[0];
	  
	  $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_USERS." WHERE user_ip LIKE('".$ipr."%') LIMIT 1");
	  $result = $sth->execute();
	  $r = $sth->fetch(PDO::FETCH_NUM);
	  $TotalUsers = $r[0];
	  
	  $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_GP." WHERE ip LIKE('".$ipr."%') GROUP BY name LIMIT 1");
	  $result = $sth->execute();
	  $r = $sth->fetch(PDO::FETCH_NUM);
	  $TotalGP = $r[0];
	  
	  $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_BANS." WHERE ip_part = '$ipr' LIMIT 1");
	  $result = $sth->execute();
	  $r = $sth->fetch(PDO::FETCH_NUM);
	  $TotalBans = $r[0];
	  
	  ?>
	  <h2>IP Range Search 
	  <a class="menuButtons" href="<?=OS_HOME?>adm/?bans">Back</a>
	  <a class="menuButtons" href="<?=OS_HOME?>adm/?bans&amp;add=iprange&amp;ip=:<?=$ipr?>">Ban Range</a>
	  </h2>
	  <table>
	    <tr>
		  <th width="70">Search IP</th>
		  <th width="180">Stats</th>
		  <th width="180">Members</th>
		  <th width="180">Games</th>
		  <th width="180">Bans</th>
		  <th></th>
		</tr>
		<tr>
		  <td><b><?=$ipr?></b></td>
		  <td>
		  <?=$TotalStats?> <a href="<?=OS_HOME?>adm/?bans&amp;ip_range=<?=$ipr?>&amp;show=stats">[Show]</a>
		  <?php if ( isset($_GET["show"]) AND ($_GET["show"] == "stats" OR $_GET["show"] == "all") ) { 
		  $sth = $db->prepare("SELECT * FROM ".OSDB_STATS." WHERE ip LIKE('".$ipr."%') LIMIT 20");
		  $result = $sth->execute();
		  ?><table style="font-size:12px;"><?php
		   while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
		    ?>
			<tr><td>
			<a target="_blank" href="<?=OS_HOME?>?u=<?=$row["player"]?>"><?=$row["player"]?></a></td>
			<td><?=$row["ip"]?></td>
			</tr>
			<?php
		   } ?></table><?php
		   } ?>
		  </td>
		  <td><?=$TotalUsers?> <a href="<?=OS_HOME?>adm/?bans&amp;ip_range=<?=$ipr?>&amp;show=members">[Show]</a>
		  <?php if ( isset($_GET["show"]) AND ($_GET["show"] == "members" OR $_GET["show"] == "all") ) { 
		  $sth = $db->prepare("SELECT * FROM ".OSDB_USERS." WHERE user_ip LIKE('".$ipr."%') LIMIT 20");
		  $result = $sth->execute();
		   ?><table style="font-size:12px;"><?php
		   while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
		    ?>
		    <tr><td>
			<a target="_blank" href="<?=OS_HOME?>?member=<?=$row["user_id"]?>"><?=$row["user_name"]?></a></td>
			<td><?=$row["user_ip"]?></td>
			</tr>
			<?php
		    } ?></table><?php
		   } ?>
		  
		  </td>
		  <td><?=$TotalGP?> <a href="<?=OS_HOME?>adm/?bans&amp;ip_range=<?=$ipr?>&amp;show=games">[Show]</a>
		  <?php if ( isset($_GET["show"]) AND ($_GET["show"] == "games" OR $_GET["show"] == "all") ) { 
		  $sth = $db->prepare("SELECT * FROM ".OSDB_GP." WHERE ip LIKE('".$ipr."%') GROUP BY name LIMIT 20");
		  $result = $sth->execute();
		  ?><table style="font-size:12px;"><?php
		   while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
		   ?>
		   <tr><td>
		   <a target="_blank" href="<?=OS_HOME?>?u=<?=$row["name"]?>"><?=$row["name"]?></a></td>
		   <td><?=$row["ip"]?></td>
		   </tr><?php
		   } ?></table><?php
		   } ?>
		  </td>
		  <td><?=$TotalBans?> <a href="<?=OS_HOME?>adm/?bans&amp;ip_range=<?=$ipr?>&amp;show=bans">[Show]</a>
		  <?php if ( isset($_GET["show"]) AND ($_GET["show"] == "bans" OR $_GET["show"] == "all") ) { 
		  $sth = $db->prepare("SELECT * FROM ".OSDB_BANS." WHERE ip_part = '$ipr' GROUP BY name LIMIT 20");
		  $result = $sth->execute();
		  ?><table style="font-size:12px;"><?php
		   while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
		    ?>
			<tr><td>
		   <a target="_blank" href="<?=OS_HOME?>?u=<?=$row["name"]?>"><?=$row["name"]?></a></td>
		   <td><?=$row["ip"]?></td>
		   </tr><?php
		   } ?></table><?php
		   } ?>
		  
		  </td>
		  <td> <a href="<?=OS_HOME?>adm/?bans&amp;ip_range=<?=$ipr?>&amp;show=all">[Show ALL]</a></td>
		</tr>
	  </table>
	  
	  <div style="margin-top: 160px;">&nbsp;</div>
	  <?php
	  }
	}
	else {
  
  if (isset($_POST['checkbox']) ) {
    $sql = "DELETE FROM ".OSDB_BANS." WHERE ";
	$c = 0;
	for ($i = 0; $i < count($_POST['checkbox']); $i++) {
	   if ( is_numeric( $_POST['checkbox'][$i] )  ) {
	   $sql.=" id='".(int)$_POST['checkbox'][$i]."' OR ";
	   $c++;
	   }
	}
	
	if ( $c>=1 ) {
	
	$sql = substr($sql,0, -3);
	//echo $sql; 
	$delete = $db->query( $sql );
	if ( $delete ) { ?>Deleted <?=$c?> ban(s)<?php
	}
	}
  }
  
  
  if ( isset($_GET["search_bans"]) AND strlen($_GET["search_bans"])>=2 ) {
     $search_bans = safeEscape( $_GET["search_bans"]);
	 $sql = " AND LOWER(name) LIKE LOWER('%".$search_bans."%') ";
  } else {
   $sql = "";
   $search_bans= "";
  }
  
  if ( !empty($_GET["check_ip_range"]) ) {
  
    $check_ip_range = strip_tags( trim($_GET["check_ip_range"]));
	$sql = " AND ip = ':".$check_ip_range."' ";
  }
  
  if ( !isset($_GET["duplicate"])  ) {
  $sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_BANS." WHERE id>=1 $sql" );
  $result = $sth->execute();
  $r = $sth->fetch(PDO::FETCH_NUM);
  $numrows = $r[0];
  
  
  } else {
     $sth = $db->prepare( "SELECT count(*), name FROM ".OSDB_BANS." 
	 GROUP BY name having count(*) > 1 ORDER BY name DESC" );
	 $result = $sth->execute();
	 $numrows = $sth->rowCount();
  }
  
?>
<div align="center">
<?php
  $result_per_page = 20;
  $draw_pagination = 1;
  $SHOW_TOTALS = 1;
  include('pagination.php');
  
  $orderby = "id DESC, date DESC";
  //id, name, date, expire, warns, bannedby
  if ( isset($_GET["sort"])) {
    if ($_GET["sort"] =="name" )     $orderby = "LOWER(b.name) ASC, date DESC";
	if ($_GET["sort"] =="date" )     $orderby = "b.date DESC, id DESC";
	if ($_GET["sort"] =="expire" )   $orderby = "b.expiredate ASC, id DESC";
	if ($_GET["sort"] =="warns" )    $orderby = "b.warn DESC, id DESC";
	if ($_GET["sort"] =="bannedby" ) $orderby = "LOWER(b.admin) ASC, id DESC";
	if ($_GET["sort"] =="country" )  $orderby = "LOWER(b.country) ASC, id DESC";
  }
	if ( !isset($_GET["duplicate"]) ) 
   $sth = $db->prepare("SELECT b.id, b.name, b.reason, b.date, b.admin, b.warn, b.expiredate, b.ip, b.country 
   FROM ".OSDB_BANS." as b
   WHERE b.id>=1 $sql 
   ORDER BY $orderby 
   LIMIT $offset, $rowsperpage");
   else 
   $sth = $db->prepare( "SELECT count(*), b.id, b.name, b.reason, b.date, b.admin, b.warn, b.expiredate, b.ip, b.country
   FROM ".OSDB_BANS." as b 
   GROUP BY b.name having count(*) > 1 ORDER BY b.id DESC, b.date DESC" );
   
   $result = $sth->execute();
   ?>
   
   <?php if ( isset($_GET["duplicate"]) ) { 
   if ($numrows<=0) $message = "<tr><td class='padLeft'><h2>No duplicate bans</h2></td><td></td><td></td><td></td><td></td></tr>";
   ?>
   <h2>Duplicate bans</h2>
   <?php } else $message = ""; ?>
   <form method="post" name="delete" action="">
   <table style="width:99%;">
    <tr>
	  <th width="200" class="padLeft"><input type="checkbox" onClick="toggle(this)" /> Player</th>
	  <th width="64">Action</th>
	  <th width="180">Reason</th>
	  <?php if ( isset($_GET["showPP"]) ) { ?>
	  <th width="35">Penalties</th>
	  <?php } ?>
	  <th width="120">Banned by</th>
	  <th>Expire</th>
	</tr>
   <?php
    echo $message ;
	
	$Letter = ""; $Country = "";
	
   while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	if ($GeoIP == 1 ) {
	$Letter   = geoip_country_code_by_addr($GeoIPDatabase, $row["ip"]);
	$Country  = geoip_country_name_by_addr($GeoIPDatabase, $row["ip"]);
	}
	
	if ( isset($_GET["showPP"]) ) {
	$sth2 = $db->prepare( "SELECT SUM(pp) as points FROM ".OSDB_GO." WHERE player_name = '".$row["name"]."' " );
	$result2 = $sth2->execute();
	$row2 = $sth2->fetch(PDO::FETCH_ASSOC);
	$totalPP = $row2["points"];
	} else $totalPP = "";
	
	//FORCE UPDATE user IP Address
	if ( isset($_GET["update_ips"]) AND ($row["ip"] == "" OR $row["ip"]=="0.0.0.0") ) {
	  	$sth2 = $db->prepare("SELECT * FROM ".OSDB_GP." 
		WHERE name = '".trim(strtolower($row["name"]))."' AND ip!='' AND ip!='0.0.0.0' ORDER BY id DESC LIMIT 1");
		$result = $sth2->execute();
		$row2 = $sth2->fetch(PDO::FETCH_ASSOC);
		$foundIP = $row2["ip"];
		echo $row["name"]." IP: ".$foundIP;
		if ( $foundIP!="" ) {
		$upd = $db->prepare("UPDATE ".OSDB_BANS." SET `ip` = '$foundIP' WHERE `name` = '".$row["name"]."' LIMIT 1");
		$result = $upd->execute();
		}
		
	}
	
	
   if ( empty($row["admin"]) ) $admin = '<span class="banned">[system]</span>'; else $admin = $row["admin"];
   
   $reason = str_replace( array("&amp;#039;", "&amp;quot;"), array("'", '"'), $row["reason"]);
   ?>
   <tr class="row" style="height:36px;">
     <td width="180" class="padLeft font12">
	 <input type="checkbox" name="checkbox[]" value="<?=$row["id"]?>">
     <?=OS_ShowUserFlag( $Letter, $Country, 175 )?> 
	 <?php if ( isset($_GET["duplicate"]) ) { ?>
	 <a href="<?=OS_HOME?>adm/?bans=&search_bans=<?=trim($row["name"])?>"><span style="color:red;">[show]</span></a>
	 <?php } ?>
	 <a href="<?=OS_HOME?>adm/?bans&amp;edit=<?=$row["id"]?>"><b><?=$row["name"]?></b></a> 
	 <span class="floatR"><?=$row["country"]?></span>
	 <div style="font-size:11px;">
	 date: <i><?=date(OS_DATE_FORMAT, strtotime($row["date"]))?></i> 
	  <a class="floatR padLeft" title="Link to profile" target="_blank" href="<?=OS_HOME?>?u=<?=$row["name"]?>">link</a> 
	 <?php
	 $ip_part = OS_GetIpRange( $row["ip"] );
	 if (!empty($ip_part)) {
	 ?>
	 <a class="floatR" title="Show IP Range" href="<?=OS_HOME?>adm/?bans&amp;ip_range=<?=$ip_part?>">ipr</a>
	 <?php } ?>
	 </div>
	 </td>
	 <td width="64" class="font12">
	 <a href="<?=OS_HOME?>adm/?bans&amp;edit=<?=$row["id"]?>"><img src="<?=OS_HOME?>adm/edit.png" alt="img" /></a>
	 <a href="javascript:;" onclick="if (confirm('Delete ban?') ) { location.href='<?=OS_HOME?>adm/?bans&amp;del=<?=$row["id"]?>' }"><img src="<?=OS_HOME?>adm/del.png" alt="img" /></a>
	 </td>
	 <td width="180" class="overflow_hidden font12">
	 <span title="<?=$reason?>"><?=stripslashes($reason)?></span>
	 <div style="font-size:10px;"><?=$ip_part?></div>
	 </td>
	 <?php if ( isset($_GET["showPP"]) ) { ?>
	 <td width="35" class="font12"><span title="<?=$row["warn"]?>"><?=$totalPP?></span></td>
	 <?php } ?>
	 <td width="120" class="font12"><?=$admin?></td>
	 <td class="font12">

	 <?php if ( date("Y", strtotime($row["expiredate"]) )>=1990 ) { 
	 $expiredate = $row["expiredate"];
	 $expire = strtotime($expiredate);
	 $time = time();
	 if ($expire<=$time) { $expired = '<b>expired</b>'; $col = 'AD2C1A'; } else { $expired = ""; $col = '1A56AD'; }
	 ?>
	 <div style="color:#<?=$col?>;"><i><?=date(OS_DATE_FORMAT, strtotime($row["expiredate"]))?></i></div>
	 <?=$expired?>
	 <?=OS_ExpireDateRemain( $row["expiredate"] ) ?>
	 <?php } else { ?><span class="perm_ban">PERMANENT</span><?php } ?>
	 </td>
    </tr>
   <?php 
   }
   
   if ( isset($GeoIP) AND $GeoIP == 1) geoip_close($GeoIPDatabase);
  
?>
  </table>
  
  <div align="left" class="padLeft">
    <input class="menuButtons" type="submit" name="Submit" value="Delete Selected" />
  </div>
  </form>
<?php
include('pagination.php');
  }
 }
?>
  </div>