<?php 
//include("../../config.php");
//require_once('../../inc/common.php');

include("cron_config.php");

require_once('../../inc/common.php');
require_once('../../inc/default-constants.php');

$CronPasswordLink = 'pw='.$CronPassword;

if ( !isset($_GET["pw"]) ) { header('location: '.$website.'?404'); die;  }
if ( isset($_GET["pw"]) AND $_GET["pw"]!=$CronPassword ) { header('location: '.$website.'?404'); die;  }


//Save config
if ( isset($_POST["cron_save"]) ) {
   
   $CronUpdate = strip_tags(trim($_POST["CronUpdate"]));
   $MaxQueries = strip_tags(trim($_POST["MaxQueries"]));
   $StatsCountryUpdate = strip_tags(trim($_POST["StatsCountryUpdate"]));
   $MaxCronLogs = strip_tags(trim($_POST["MaxCronLogs"]));
   $CronReportDetails = strip_tags(trim($_POST["CronReportDetails"]));
   
   if ( isset($_POST["CronPassword"]) AND !empty($_POST["CronPassword"]) AND $_POST["CronPassword2"] == $CronPassword ) {
     $NewPassword = strip_tags(trim($_POST["CronPassword2"]));
	 write_value_of('$CronPassword', "$CronPassword", $NewPassword , "cron_config.php");
	 $pwchanged = 1;
   }
   
    write_value_of('$CronUpdate', "$CronUpdate", $CronUpdate , "cron_config.php");
	write_value_of('$MaxQueries', "$MaxQueries", $MaxQueries , "cron_config.php");
	write_value_of('$StatsCountryUpdate', "$StatsCountryUpdate", $StatsCountryUpdate , "cron_config.php");
    write_value_of('$MaxCronLogs', "$MaxCronLogs", $MaxCronLogs , "cron_config.php");
	write_value_of('$CronReportDetails', "$CronReportDetails", $CronReportDetails , "cron_config.php");
	   
	   if (!isset($pwchanged)) header("location: ?pw=".$CronPassword);
	   else header("location: ?pw=".$NewPassword);
	   die();
}

require_once('../../inc/class.db.PDO.php'); 

// below: just to load default constants (we dont want to load config.php, because cron+session conflict)
// this is temporary solution
$website = ''; $HomeTitle = ''; $TimeZone = ''; $BotName = '';
$DefaultStyle = 'ghost'; $default_language = 'english';
$DateFormat = ''; $MinDuration = ''; $DefaultMap = 'dota';
$TopPage = 1; $HeroesPage = '';  $OnlineOfflineOnTopPage = '';
// end

require_once("../../inc/default-constants.php");
require_once("../../inc/common-queries.php");

date_default_timezone_set($TimeZone);

$CronTempFile = '_working.txt';

   $db = new db("mysql:host=".$server.";dbname=".$database."", $username, $password);
   $sth = $db->prepare("SET NAMES 'utf8'");
   $result = $sth->execute();
//require_once('../../inc/db_connect.php');

$interval=$CronUpdate; // do every X sec (loaded from config)... 

if ( isset($_GET["clear"]) ) {
   	$sth = $db->prepare("TRUNCATE TABLE `cron_logs` ");
	$result = $sth->execute();  
	header('location: ?'.$CronPasswordLink);
	die();
}

//This is just call from ajax
if ( isset($_GET["status"]) ) {
   if ( file_exists($CronTempFile) ) {
   $time = filemtime($CronTempFile);
   $next = $time +  $interval;
   $counter = file_get_contents($CronTempFile);
   echo '<div style="padding:20px; line-height:16px; height:260px;">';
   echo "<h2>Process Still Active ( executed: ".$counter."x )</h2>";
   echo "<div><b>Daemon is working.</b> You can now close this window.";
   echo "<div><b>If you want to stop daemon later, load this page again and click on STOP button.";
   echo "<div><b>Last update:</b> ". date("d.m.Y, H:i:s", $time)."</div>";
   echo "<div><b>Next update:</b> ". date("d.m.Y, H:i:s", $next)."</div>";
   echo "<div><b>Interval set to:</b> ".$interval." sec.</div>";
    echo '</div>';
   }
   
   die();
}

//Force stop daemon
if (isset($_GET["stop"]))  {
ignore_user_abort(false); 
if ( file_exists($CronTempFile) ) unlink($CronTempFile);
file_put_contents("stop.tmp", date("d.m.Y, H:i") );
}

//Start script forever (until user abort)
if (isset($_GET["start"])) {
ignore_user_abort(1); // run script in background 
set_time_limit(0); // run script forever 
if ( file_exists("stop.tmp") ) unlink("stop.tmp");

    $cron_data = '<span style="color:green;"><b>►►► DAEMON START ►►►</b></span> @ '.date("d.m.Y, H:i");
	$sth = $db->prepare("INSERT INTO cron_logs (cron_data, cron_date) VALUES('$cron_data', '".time()."' ) ");
	$result = $sth->execute();  
}

    //FOR BAN IP update
	 if ( file_exists("../../inc/geoip/geoip.inc") ) {
	 include("../../inc/geoip/geoip.inc");
	 $GeoIPDatabase = geoip_open("../../inc/geoip/GeoIP.dat", GEOIP_STANDARD);
	 $GeoIP = 1;
	 }


$counter = 1;
if (isset($_GET["start"])) 
do{ 
   //WORK HERE
   file_put_contents($CronTempFile, $counter );
   $counter++;
   $debug = "";
   
   //TRUNCATE TABLE - clean up
   	$sth = $db->prepare("SELECT COUNT(*) FROM cron_logs LIMIT 1");
	$result = $sth->execute();
	$r = $sth->fetch(PDO::FETCH_NUM);
	$numrows = $r[0];
	
	if ( $numrows >= $MaxCronLogs AND $MaxCronLogs>=1 ) {
	
	$sth = $db->prepare("TRUNCATE TABLE cron_logs ");
	$result = $sth->execute();
	}

//CRON PROCESS #1
//UPDATE IPs FROM BANS TABLE 
if ($BanIPUpdate == 1) {  

   $sth = $db->prepare("SELECT * FROM ".OSDB_BANS." 
   WHERE id>=1 AND country='' AND ip NOT LIKE (':%')
   ORDER BY RAND()
   LIMIT $MaxQueries");
   
   $result = $sth->execute();
   $total = $sth->rowCount();
   
   $debug = "";
   $count = 0;
   //I will set detail level for debug: 1 simple, 2 full details (default), 0 none
   while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
    $name = $row["name"];
	$Letter = ""; $Country = "";
	
	$sth2 = $db->prepare("SELECT * FROM ".OSDB_GP." WHERE id>=1 AND name='".$name."' LIMIT 1");
    $result2 = $sth2->execute();
	$row2 = $sth2->fetch(PDO::FETCH_ASSOC);
	
	$ip = $row2["ip"];
	$ipv = explode(".", $ip);
	if ( count($ipv)>2 ) $ip_part = $ipv[0].".".$ipv[1]; else $ip_part = '';
	
	$Letter   = geoip_country_code_by_addr($GeoIPDatabase, $ip);
	$Country  = geoip_country_name_by_addr($GeoIPDatabase, $ip);
	
	if ( !empty($Country) ) {
	$upd = $db->prepare("UPDATE ".OSDB_BANS." SET country='$Letter', ip = '".$ip."', ip_part = '".$ip_part."' 
	WHERE id = '".$row["id"]."' ");
	$result = $upd->execute();
	$count++;
	
	if ( $CronReportDetails ==2 ) $debug.= "<div><b>$name</b>, $ip, $Letter, $Country</div>";
	} else {
	  if ( $CronReportDetails ==2 ) $debug.= "<div><span style=\"color:red\">FAILED: <b>$name</b>, IP: $ip, LETTER: $Letter, COUNTRY: $Country</span></div>";
	  }
	
   }
    
	if ( $count>=1 OR $CronReportDetails == 2)  $debug = " <b>IP (Bans table) (found: $count entries) </b>";
    //Cron entry example - LOG
	if ( $CronReportDetails >=1 AND !empty($debug) ) {
    $cron_data = 'DAEMON: '.$debug.'';
	$sth = $db->prepare("INSERT INTO cron_logs (cron_data, cron_date) VALUES('$cron_data', '".time()."' ) ");
	$result = $sth->execute();  
	}
   
}




   //COUNTRY UPDATE FROM "stats" table
 if ($StatsCountryUpdate == 1) {
   
    $sth = $db->prepare("SELECT * FROM ".OSDB_STATS." 
    WHERE country = ''
    LIMIT $MaxQueries");
    $result = $sth->execute();
	$total = $sth->rowCount();
	
	$debug = "";

	 while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
    $name = $row["player"];
	$Letter = ""; $Country = "";
	
	$ip = $row["ip"];
	
	$Letter   = geoip_country_code_by_addr($GeoIPDatabase, $ip);
	$Country  = geoip_country_name_by_addr($GeoIPDatabase, $ip);
	
	if ( substr($ip, 0,7) == "23.243." OR substr($ip, 0,7) == "23.242." OR substr($ip, 0,7) == "23.241." ) {
	 $Letter  = "US";
	 $Country = "United States";
	}
	
	if ( !empty($Country) ) {
	$upd = $db->prepare("UPDATE ".OSDB_STATS." SET country='".$Country."', country_code = '".$Letter."' WHERE id = '".$row["id"]."' ");
	$result = $upd->execute();
	
	if ($total>=1 AND empty($debug) )  $debug = " <b>Updating Countries (found: $total entries)</b>";
	
	if ( $CronReportDetails ==2 ) $debug.= "<div><b>$name</b>, $ip, $Letter, $Country</div>";
	} else {
	  if ( $CronReportDetails ==2 ) $debug.= "<div><span style=\"color:red\">FAILED: <b>$name</b>, IP: $ip, LETTER: $Letter, COUNTRY: $Country</span></div>";
	  }
	  
     	 
	 }
	 
    //Cron entry example - LOG
	if ( $CronReportDetails >=1 AND !empty($debug) ) {
    $cron_data = 'DAEMON: '.$debug.'';
	$sth = $db->prepare("INSERT INTO cron_logs (cron_data, cron_date) VALUES('$cron_data', '".time()."' ) ");
	$result = $sth->execute();  
	}
	
	$debug = "";
	
 }
 
 
  
   
   
   
   //END WORK HERE
   
   
   
   //Check process kill
   if (file_exists("stop.tmp"))  {
   unlink("stop.tmp");
   if ( file_exists($CronTempFile) ) unlink($CronTempFile);
   
    //ADD LOG - CRON STOPPED
    $cron_data = '<span style="color:red;"><b>◄◄◄ DAEMON STOPPED ◄◄◄</b></span> @ '.date("d.m.Y, H:i");
	$sth = $db->prepare("INSERT INTO cron_logs (cron_data, cron_date) VALUES('$cron_data', '".time()."' ) ");
	$result = $sth->execute();  
   
   exit;
   die; 
   }
   
   sleep($interval); // wait [ $interval ] sec
} while(true); 


if ( isset($GeoIP) AND $GeoIP == 1) geoip_close($GeoIPDatabase);
?>
<!DOCTYPE html>

<html lang="en-US">
<head>
 	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="content-style-type" content="text/css" />
	
	<link rel="shortcut icon" href="<?=OS_THEME_PATH?>favicon.ico" />
	<link rel="stylesheet" href="<?=OS_THEME_PATH?>style.css" type="text/css" />
	<style>

	</style>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>
<body>

<div id="inset">
<div class="wrapper">
<a href="http://ohsystem.net/stats/" class="nounder">
<img src="http://ohsystem.net/stats/themes/ghost/images/blank.gif" style="border:0;" alt="" id="logo">
</a>

</div>
</div>

<div id="content" class="s-c-x">
<div id="main-column">
<div class="padding">
<div class="inner">
 <?php
   if ( file_exists($CronTempFile) ) {
   $time = filemtime($CronTempFile);
   ?>
   <script>setTimeout( function() { GetStatus() }, 1000 );</script>
   <div>&nbsp;</div>
   <div style="padding:20px; line-height:16px;">
   <a class="menuButtons" href="?stop&amp;<?=$CronPasswordLink?>">STOP</a> | 
   <a class="menuButtons" href="?<?=$CronPasswordLink?>">REFRESH</a> | 
   <a class="menuButtons" href="?show_logs=1&amp;<?=$CronPasswordLink?>">SHOW LAST LOGS</a> 
   </div>
   <?php
    if (isset($_GET["show_logs"]) ) {
	$sth = $db->prepare("SELECT COUNT(*) FROM cron_logs LIMIT 1");
	$result = $sth->execute();
	$r = $sth->fetch(PDO::FETCH_NUM);
	$numrows = $r[0];
	$DisplayLogs = '100';
	
	if ( $DisplayLogs> $numrows ) $DisplayLogs = $numrows;
    ?>
	<div style="margin-left:30px; margin-top:40px; font-family:arial, verdana;">
	<h2>Recent logs (<?=$DisplayLogs?>/<?=$numrows?>):</h2>
   <div style="font-family:arial, verdana;font-size:13px; width:500px; height:320px; overflow:scroll; ">
   <?php 
   	 $sth = $db->prepare("SELECT * FROM cron_logs WHERE id>=1 ORDER BY id DESC LIMIT $DisplayLogs ");	
	 $result = $sth->execute();
	 while ( $row = $sth->fetch(PDO::FETCH_ASSOC) ) {
	 ?>
	 <div>&raquo; <span style="font-size:11px;"><i><?=date("d.m.Y, H:i:s", $row["cron_date"])?></i></span>, <?=$row["cron_data"]?></div>
	 <?php
	 }
	 ?></div>
	 </div><?php
    }
   
   
   } else {
   ?>

   <div style="display:none;">Config: 
   <b>update</b> (<?=$CronUpdate?>sec), 
   <b>Max.Queries</b> (<?=$MaxQueries?>), 
   <b>Stats Country UPD</b> (<?=$StatsCountryUpdate?>), 
   <b>IPs from BANS</b> (<?=$BanIPUpdate?>), 
   <b>Max.Logs</b> (<?=$MaxCronLogs?>)
   <b>Detail Level</b> (<?=$CronReportDetails?>)
   </div>
   
   <h2>Setup</h2>
   <form action="" method="post">
   <table>
     <tr>
	   <td width="150"><b>Cron update:</b></td>
	   <td>
	   <input type="text" value="<?=$CronUpdate?>" name="CronUpdate"  /> sec.
	   </td>
	 </tr>
     <tr>
	   <td width="150"><b>Max. queries:</b></td>
	   <td>
	   <input type="text" value="<?=$MaxQueries?>" name="MaxQueries"  />
	   </td>
	 </tr>
     <tr>
	   <td width="150"><b>Update countries:</b></td>
	   <td>
	   <select name="StatsCountryUpdate">
	   <?php if ($StatsCountryUpdate == 1) $s='selected="selected"'; else $s='';?>
	   <option <?=$s?> value="1">On</option>
	   <?php if ($StatsCountryUpdate == 0) $s='selected="selected"'; else $s='';?>
	   <option <?=$s?> value="0">Off</option>
	   </select>
	   </td>
	 </tr>
     <tr>
	   <td width="150"><b>Max. cron logs:</b></td>
	   <td>
	   <input type="text" value="<?=$MaxCronLogs?>" name="MaxCronLogs" /> 0 - keep all logs
	   </td>
	 </tr>
    <tr>
	   <td width="150"><b>Report details:</b></td>
	   <td>
	   <select name="CronReportDetails">
	   <?php if ($CronReportDetails == 1) $s='selected="selected"'; else $s='';?>
	   <option <?=$s?> value="1">Basic details</option>
	   <?php if ($StatsCountryUpdate == 2) $s='selected="selected"'; else $s='';?>
	   <option <?=$s?> value="2">Full log report</option>
	   <?php if ($StatsCountryUpdate == 0) $s='selected="selected"'; else $s='';?>
	   <option <?=$s?> value="0">Disabled</option>
	   </select>
	   </td>
	 </tr>
	 
     <tr>
	   <td width="150"><b>Change cron password:</b></td>
	   <td>
	   <input type="password" value="" name="CronPassword"  />
	   <span>Current password:</span>
	   <input type="password" value="" name="CronPassword2" />
	   </td>
	 </tr>
     <tr>
	   <td width="150"></td>
	   <td>
	   <input type="submit" class="menuButtons" name="cron_save" value="Save configuration" />
	   </td>
	 </tr>
   </table>
   </form>
   
   <div>&nbsp;</div>
   <?php if (!file_exists("stop.tmp") ) { ?>
   <a class="menuButtons" href="javascript:;" onclick="StartDaemon()">START CRON</a> | 
   <?php } else { ?>
   <div style="margin-bottom:12px;"><b>Stopping service.</b> Please wait (max <?=$CronUpdate?> sec)... <a href="?<?=$CronPasswordLink?>">Check again</a></div>
   <?php } ?>
   <a class="menuButtons" href="?clear&amp;<?=$CronPasswordLink?>" >PURGE LOGS</a> | 
   <a class="menuButtons" href="?<?=$CronPasswordLink?>">REFRESH</a>
   
   <div style="margin-top:40px;font-family:arial, verdana; "><h2>Recent logs:</h2></div>
   <div style="font-family:arial, verdana;font-size:13px;">
   <?php 
   	 $sth = $db->prepare("SELECT * FROM cron_logs WHERE id>=1 ORDER BY id DESC LIMIT 20 ");	
	 $result = $sth->execute();
	 while ( $row = $sth->fetch(PDO::FETCH_ASSOC) ) {
	 ?>
	 <div>&raquo; <span style="font-size:11px;"><i><?=date("d.m.Y, H:i:s", $row["cron_date"])?></i></span>, <?=$row["cron_data"]?></div>
	 <?php
	 }
	 ?></div><?php
   
   } ?>

    <div id="info" style="height:168px;">&nbsp;</div>

    <div id="loader" style="height:100px;"><img class="imgvalign" width="96" height="96" src="loader_stop.gif" alt="" /></div>
 
 <script>
    function GetStatus() {
    
	  document.getElementById("loader").innerHTML = '';
	  
	  setTimeout( function() { GetStatus() }, 2000 );
	  
   var $js = jQuery;
   $js.ajax({
                    type: "POST",
                    url: "index.php?status&<?=$CronPasswordLink?>",
                    data: "ccc=1",
                    success: function(msg){
					
                    document.getElementById("info").innerHTML = msg;
					document.getElementById("loader").innerHTML = '<img class="imgvalign" width="96" height="96" src="loader.gif" alt="" />';
                    },
                    error: function(msg){
                    document.getElementById("info").innerHTML = msg;
					document.getElementById("loader").innerHTML = '';
                    }
                });
	  
   }
   
   function StartDaemon() {
   
document.getElementById("loader").innerHTML = '<h2>Loading...</h2><img class="imgvalign" width="96" height="96" src="loader.gif" alt="" />'; 
   
   var $js = jQuery;
   setTimeout( function() { location.href='?<?=$CronPasswordLink?>' }, 300 );
   $js.ajax({
                    type: "POST",
                    url: "index.php?start&<?=$CronPasswordLink?>",
                    data: "",
                    success: function(msg){

                    },
                    error: function(msg){

                    }
                });
   
   //setTimeout( function() { location.href='cron.php' }, 500 );
   }
 </script>
 
       </div>
     </div>
  </div>
</div>

</body>
</html>