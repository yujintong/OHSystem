<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

  if ( isset($_GET["autoban"]) ) {
  
    if ( isset($_GET["create_banlist"]) ) {
	
	 $sql="";
	 
	 $lt = (int) $_GET["left"]; 
	 
	 $lefttime = $lt*60;
	 
	 if ( isset($_GET["exclude_adm"]) )    $exclude_admins = 1;          else $exclude_admins = 0;
	 if ( isset($_GET["exclude_conn"]) )   $exclude_lost_connection = 1; else $exclude_lost_connection = 0;
	 if ( isset($_GET["exclude_banned"]) ) $exclude_banned = 1;          else $exclude_banned = 0;
	 
	  $mingames = (int) $_GET["mingames"]; 
	  $times = (int) $_GET["times"]; 
	  $total = (int) $_GET["total"];
      $lessthan = (int) $_GET["lessthan"]; 
	  
	  $stayRatio = (int) $_GET["stayratio"]; 
	  
	  $days = (int) $_GET["days"]; 
	  
	  $bt = $_GET["bantime"]; 
	  
	  $pp = $_GET["pp"]; 
	  
	  if ( $bt == "2h" )  $bantime = time()+3600*2;
	  if ( $bt == "10h" ) $bantime = time()+3600*10;
	  if ( $bt == "1d" )  $bantime = time()+3600*24;
	  if ( $bt == "5d" )  $bantime = time()+3600*24*5;
	  if ( $bt == "7d" )  $bantime = time()+3600*24*7;
	  if ( $bt == "1m" )  $bantime = time()+3600*24*30;
	  
	  if ( $bt != "p" )   $bantimedate = date("Y-m-d H:i:00", $bantime);
	  if ( $bt == "p" )   $bantimedate = "";
	  
	  
	 //BAN PROCESS
	 if ( file_exists("../inc/geoip/geoip.inc") AND $GeoIP==0) {
	 include("../inc/geoip/geoip.inc");
	 $GeoIPDatabase = geoip_open("../inc/geoip/GeoIP.dat", GEOIP_STANDARD);
	 $GeoIP = 1;
	 }
      $BannedTotal = 0;
	  $CanBan = 0;
	  if ( isset($_POST["banlist"]) AND isset($bantimedate) ) {
	  
	    $sql = "";
	    $sql.="INSERT INTO ".OSDB_BANS."(name, server, reason, ip, ip_part, admin, gamename, date, expiredate, country) VALUES";
		//UPDATE stats table too
		$sql2 = "UPDATE ".OSDB_STATS." SET banned =  1 WHERE ";
		//GameOffenses
		$sql3 ="INSERT INTO ".OSDB_GO."(player_name, reason, offence_time, offence_expire, pp, admin) VALUES";
	    
		$offence_time = date("Y-m-d H:i:00", time() );
		
        foreach( $_POST["banlist"] as $BannedPlayers) { 
		$banned = strtolower($BannedPlayers["name"]);
		$ip = $BannedPlayers["ip"];
		$realm = $BannedPlayers["realm"];
		$ip_part = OS_GetIpRange( $ip );
		$Letter   = geoip_country_code_by_addr( $GeoIPDatabase, $ip );
		$Country  = geoip_country_name_by_addr( $GeoIPDatabase, $ip );
		$time = date("Y-m-d H:i:00", time() );
		$reason = "Left after ".$BannedPlayers["left"]." min. (#".$BannedPlayers["gameid"].")";
		 if ( !empty($banned) ) {
		 
		 $sql.=" ('$banned', '$realm', '$reason',  '$ip', '$ip_part', '', '#".$BannedPlayers["gameid"]."', '$time', '$bantimedate', '$Letter'),";
		 //PP
		 $sql2.=" player = '".$banned."' OR ";
		 //GameOffenses
		 $sql3.=" ('$banned', '$reason', '$offence_time', '', '".(int)$pp."', '[system]'),";
		 
		 $CanBan = 1;
		 $BannedTotal++;
		 }
		}
		
		$sql = substr($sql,0, -1);
		$sql2 = substr($sql2,0, -3);
        $sql3 = substr($sql3,0, -1);
		
		if (isset($CanBan) AND $CanBan >= 1) {
		  $sth = $db->prepare($sql);
		  $result = $sth->execute();
		  
		  //Add penalty points
		  if ( $pp>=1 ) { 
		  $sth2 = $db->prepare($sql2); 
		  $result = $sth2->execute(); 
		  $sth3 = $db->prepare($sql3); 
		  $result = $sth3->execute(); 
		  $inf=", PP: $pp" ; 
		  }
		  else $inf = "";
		  
		  OS_AddLog($_SESSION["username"], "[os_editban] AutoBan ( $BannedTotal banned".$inf.")");
		}

		?>
		<h3><?=$BannedTotal?> players added to banlist</h3>
		<?php
      }
    else {
	?>
	  <table>
	    <tr>
		  <td>Left time: <b><?=$lt?></b> min. after game start</td>
		</tr>
	    <tr>
		  <td>Exclude: 
		  <?php if ( isset($_GET["exclude_adm"]) )    echo "<div><b>Protected (safelist, reserved...)</b><div>";?>
		  <?php if ( isset($_GET["exclude_conn"]) )   echo "<div><b>Lost the connection</b><div>";?>
		  <?php if ( isset($_GET["exclude_banned"]) ) echo "<div><b>Banned players</b><div>";?>
		  </td>
		</tr>
	    <tr>
		  <td>Min. games: <b><?=$mingames?></b> <?php if ($lessthan>=1) { ?>, <b>Less than:</b> <?=$lessthan?> games<?php } ?></td>
		</tr>
	    <tr>
		  <td>Number of leaving: <b><?=$times?></b></td>
		</tr>
	    <tr>
		  <td>Search limit: <b><?=$total?></b> players</td>
		</tr>
		<?php if ($days>=1) { ?>
	    <tr>
		  <td>Search in the last: <b><?=$days?></b> day(s)</td>
		</tr>
		<?php } ?>
	    <tr>
		  <td>Stay Ratio less than: <b><?php if ( $stayRatio <= 0) echo "Any"; else echo $stayRatio; ?></b> %</td>
		</tr>
	    <tr>
		  <td>Add <b><?=$pp?></b> penalty points</td>
		</tr>
	    <tr>
		  <td>Ban time: <b><?=$bt?></b> (expires: <?=$bantimedate?> )</td>
		</tr>
	  </table>	
	<?php
	  $sql="";
	  //Exclude lost connection
	  if ( $exclude_lost_connection == 1 ) $sql.=" AND gp.leftreason NOT LIKE('%has lost the connection%') ";
	  
	  
	  if ($days>=1) $sql.=" AND datetime>= ( NOW() - INTERVAL ".$days." DAY)";
	  //Exclude admins by default
	  if ($exclude_admins == 1) {
	    $admins = array();
	    $sth = $db->prepare("SELECT * FROM ".OSDB_USERS." WHERE user_level>=1");
		$result = $sth->execute();
		 while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
		   $admins[]= strtolower($row["user_name"]);
		 }
	  }
	  	  $sth = $db->prepare("SELECT gp.gameid, gp.name, gp.ip, gp.spoofed, gp.reserved, gp.loadingtime, gp.left, gp.leftreason, gp.team, gp.colour, gp.spoofedrealm, g.duration
		  FROM ".OSDB_GP." as gp
		  LEFT JOIN ".OSDB_GAMES." as g ON g.id = gp.gameid 
		  LEFT JOIN ".OSDB_DG." as dg ON dg.gameid = g.id
		  WHERE 
		  gp.left >=$lefttime AND 
		  g.duration>=$lefttime AND 
		  gp.left <= g.duration-$LeftTimePenalty AND 
		  dg.winner>=1 $sql 
		  GROUP BY gp.name 
		  ORDER BY gp.gameid DESC 
		  LIMIT $total");
		  
		  $result = $sth->execute();
		  $BanList = array();
		  $c = 0;
		  $TotalPlayers = 0;
		  $games = 0; $leaver = 0;
		  ?>
		  <h3>Creating Banlist <a href="javascript:;" onclick="showhide('bl')">Show/Hide</a></h3>
		  <div id="bl" style="height:260px; overflow: scroll; text-align:left; margin-left:32px; display:none;" align="left">
		  <table style="width:680px;">
		  <?php
		  while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
		  
		    $b = "";
			$skip = 0; 
			$realm = $row["spoofedrealm"];
			
			if ($exclude_banned == 1) {
			 $chban = $db->prepare("SELECT * FROM ".OSDB_BANS." as b WHERE b.name = '".$row["name"]."' LIMIT 1");
		     $result = $chban->execute();
		     $ban = $chban->fetch(PDO::FETCH_ASSOC);
			 $expiredate = strtotime( $ban["expiredate"] );
			 if ( !empty($ban["name"]) ) { $b = 1; $skip=1; }  else $b = "";
			 if ( $expiredate+3600*24*7 > time() ) { $skip = 1; }
			 
			 //echo "<Br>$skip xpired: ".$ban["name"]." - ".date("d.m.Y H:i", $expiredate);
			}
			$name = strtolower($row["name"]);
			if ($exclude_admins == 1 AND in_array( $name, $admins) ) $skip = 1;
			
			$leftafter = ROUND(($row["left"])/60, 0);
			
			if ( $mingames>=1 OR $times>=1 OR $lessthan>=1 OR $stayRatio>=1) {
              $chgames = $db->prepare("SELECT * FROM ".OSDB_STATS." as s WHERE s.player = '".$row["name"]."' LIMIT 1");
			  $result = $chgames->execute();
			  $g = $chgames->fetch(PDO::FETCH_ASSOC);
			  $games = $g["games"];
			  $leaver =$g["leaver"];
			  if ($games>=1) { 
			  //$LeftRatio = ROUND($games/($games+$leaver), 3)*100; 
			  $left = $games - $leaver;
			  $LeftRatio = round(($left/$games)*100, 1);
			  }
			  else $LeftRatio = 100;
			  if ( $g["games"]<$mingames ) $skip = 1;
			  if ( $g["leaver"]<$times) $skip=1;
			  
			  if ($lessthan>=1 AND $g["games"]>$lessthan) $skip=1;
			  
			  if ( $stayRatio>=5 AND $LeftRatio>$stayRatio) $skip = 1; 
            }

		    if ( $skip!=1 ) {
			$BanList[$c]["ip"] = $row["ip"];
			$BanList[$c]["name"] = $row["name"];
			$BanList[$c]["left"] = $leftafter;
			$BanList[$c]["gameid"] = $row["gameid"];
			$BanList[$c]["games"] = $games;
			$BanList[$c]["leaver"] = $leaver;
			$BanList[$c]["realm"] = $realm;
			$TotalPlayers++;
			}			
		    ?>
			<tr <?php if ($skip==1) { ?>style="border:1px solid #ccc;"<?php } ?>>
			  <td width="46"><a href="<?=OS_HOME?>?game=<?=$row["gameid"]?>" target="_blank" >#<?=$row["gameid"]?></a></td>
			  <td width="180"><a href="<?=OS_HOME?>?u=<?=strtolower($row["name"])?>" target="_blank" ><b><?=$row["name"]?></b></a></td>
			  <td width="90"><span class="banned"><b>-<?=ROUND(($row["duration"] - $row["left"])/60, 1) ?></b></span> min. </td>
			  <td>
			  <?php if ($skip==1) {?>(<b>skipping</b>)<?php } ?>
			  <?php if ($b == 1) { ?> <b>banned</b><?php } ?> 
			  <?php if ($mingames>=1 OR $times>=1 OR $lessthan>=1) { 
			  if ($games>=1) {
			  //$LeftRatio = ROUND($games/($games+$leaver), 3)*100;
			  $left = $games - $leaver;
			  $LeftRatio = round(($left/$games)*100, 1);
			  }
			  else $LeftRatio = 100;
			  ?>
			  <?=$games?> games,
			  <?=$leaver?> x  left ( <?=$LeftRatio?> % )
			  <?php } ?> 
			  </td>
			</tr>
			<?php
			$c++;
		  }
	      ?></table>
		  </div>
		  
		  <h3>Total <?=$TotalPlayers?> Players </h3>
		  <form action="" method="post">
		  <table>
		  <?php
		  $c=0;
	  foreach ($BanList as $Banned) {
	    ?>
		<tr>
		  <td width="440">
		  
		  <a href="javascript:;" onclick="SetDateField('', 'banplayer_<?=$c?>'); showhide('temp<?=$c?>')">[x]</a> 
		  <input id="banplayer_<?=$c?>" type="text" name="banlist[<?=$c?>][name]" value="<?=$Banned["name"]?>" /> 
		  		  
		  <input type="hidden" name="banlist[<?=$c?>][realm]" value="<?=$Banned["realm"]?>" /> 
		  <input type="hidden" name="banlist[<?=$c?>][ip]" value="<?=$Banned["ip"]?>" /> 

		  <input type="hidden" name="banlist[<?=$c?>][left]" value="<?=$Banned["left"]?>" /> 
		  <input type="hidden" name="banlist[<?=$c?>][gameid]" value="<?=$Banned["gameid"]?>" /> 
		  
		  <span style="font-size:11px;"><b>Reason:</b> Left after <?=$Banned["left"] ?> min. (#<?=$Banned["gameid"] ?>)</span>
		  
		  <div class="floatR">
		   <span style="display:none;" id="temp<?=$c?>">
		    <a href="javascript:;" onclick="SetDateField('<?=$Banned["name"]?>', 'banplayer_<?=$c?>'); showhide('temp<?=$c?>')">undo</a>
		  </span>
		  </div>
		  </td>
		  <td>
		  <a target="_blank" href="<?=OS_HOME?>?u=<?=$Banned["name"]?>" class="menuButtons">View stats</a>
		  <a target="_blank" href="<?=OS_HOME?>?game=<?=$Banned["gameid"]?>" class="menuButtons">#<?=$Banned["gameid"]?></a>
		  
		  <a target="_blank" href="<?=OS_HOME?>adm/?bans&amp;add=<?=$Banned["name"]?>&amp;gid=<?=$Banned["gameid"]?>&amp;reason=leaver&amp;realm=<?=$Banned["realm"]?>&amp;ip=<?=$Banned["ip"]?>" class="menuButtons">Ban</a>
		  
		  </td>
		  <td>
		  <?php if ($mingames>=1 OR $times>=1 OR $lessthan>=1) { 
		  //$LeftRatio = ROUND($Banned["games"]/($Banned["games"]+$Banned["leaver"]), 3)*100;
		  $left = $Banned["games"] - $Banned["leaver"];
		  $LeftRatio = round(($left/$Banned["games"])*100, 1);
		  
		  ?>
		  <b>Games:</b> <?=$Banned["games"]?>,
		  <b>Left:</b> <?=$Banned["leaver"]?> x ( <?=$LeftRatio?>% )
		  <?php } ?> </td>
		</tr>
		<?php
		$c++;
	  }
	  ?>
	  <tr>
	    <td><?php if ($TotalPlayers>=1) { ?> <input type="submit" value="ADD TO BANLIST" class="menuButtons" /><?php } ?></td>
		<td></td>
		<td></td>
	  </tr>
	  </table>
	    </form>
	  <?php	  
	  }
	} else {
    ?>
	<div align="center">
	<h2>Setup AutoBan Option</h2>
	<form action="" method="">
	<input type="hidden" name="bans" />
	<input type="hidden" name="autoban" />
	  <table>
	    <tr>
		  <td width="160">Ban players who left</td>
		  <td>
		  <select name="left">
		    <option value="0">Any</option>
		    <option selected="selected" value="5">5</option>
			<option value="10">10</option>
			<option value="20">20</option>
			<option value="30">30</option>
		  </select> min. after game start</td>
		</tr>
	    <tr>
		  <td width="160">Exclude</td>
		  <td>
		  <div><input type="checkbox" checked name="exclude_adm" /> All protected (admins, safelisted, reserved...) </div>
		  <div><input type="checkbox" name="exclude_conn" /> Exclude players who lost the connection</div>
		  <div><input type="checkbox" checked name="exclude_banned" /> Exclude already banned players</div>
		  </td>
		</tr>
	    <tr>
		  <td width="160">Min. games played</td>
		  <td><select name="mingames">
		  	<option selected="selected" value="0">Any</option>
		    <option value="1">1 game</option>
			<option value="5">5 games</option>
			<option value="10">10 games</option>
			<option value="20">20 games</option>
			<option value="30">30 games</option>
			<option value="50">50 games</option>
		  </select></td>
		</tr>
		
	    <tr>
		  <td width="160">Less than</td>
		  <td><select name="lessthan">
		  	<option selected="selected" value="0">Any games</option>
		    <option value="1">1 game</option>
			<option value="3">3 games</option>
			<option value="5">5 games</option>
			<option value="10">10 games</option>
			<option value="20">20 games</option>
			<option value="30">30 games</option>
			<option value="50">50 games</option>
			<option value="100">100 games</option>
		  </select></td>
		</tr>
		
	    <tr>
		  <td width="160">Min. number of leaving</td>
		  <td><select name="times">
		    <option value="0">Any</option>
		    <option value="1">1 x</option>
			<option selected="selected" value="2">2 x</option>
			<option value="3">3 x</option>
			<option value="4">4 x</option>
			<option value="5">5 x</option>
			<option value="6">6 x</option>
			<option value="7">7 x</option>
			<option value="8">8 x</option>
			<option value="9">9 x</option>
			<option value="10">10 x</option>
			<option value="15">15 x</option>
			<option value="20">20 x</option>
			<option value="30">30 x</option>
		  </select></td>
		</tr>
	    <tr>
		  <td width="160">Search total</td>
		  <td><select name="total">
		    <option value="1">1 Player</option>
			<option value="10">10 Players</option>
			<option value="30">30 Players</option>
			<option selected="selected" value="50">50 Players</option>
			<option value="100">100 Players</option>
			<option value="200">200 Players</option>
			<option value="500">500 Players</option>
			<option value="1000">1000 Players</option>
			<option value="2000">2000 Players</option>
		  </select></td>
		</tr>
		
	    <tr>
		  <td width="160">Search in the last</td>
		  <td><select name="days">
		    <option value="0">Any</option>
		    <option value="1">1 day</option>
			<option value="2">2 days</option>
			<option value="3">3 days</option>
			<option value="5">5 days</option>
		    <option value="7">7 days</option>
			<option value="30">1 month</option>
			<option value="90">3 months</option>
			<option value="180">6 months</option>
		  </select></td>
		</tr>
		
	    <tr>
		  <td width="160">Stay ratio less than</td>
		  <td><select name="stayratio">
		  	<option selected="selected" value="0">Any</option>
		    <option value="80">&laquo; 80%</option>
			<option value="70">&laquo; 70%</option>
			<option value="60">&laquo; 60%</option>
			<option value="55">&laquo; 55%</option>
			<option value="50">&laquo; 50%</option>
			<option value="45">&laquo; 45%</option>
			<option value="40">&laquo; 40%</option>
			<option value="35">&laquo; 35%</option>
			<option value="30">&laquo; 30%</option>
			<option value="25">&laquo; 25%</option>
			<option value="20">&laquo; 20%</option>
			<option value="10">&laquo; 10%</option>
		  </select></td>
		</tr>
		
	    <tr>
		  <td width="160">Add penalty points</td>
		  <td><select name="pp">
		  	<option value="0">No</option>
		    <option selected="selected" value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
			<option value="7">7</option>
			<option value="8">8</option>
			<option value="9">9</option>
			<option value="10">10</option>
		  </select></td>
		</tr>
		
	    <tr>
		  <td width="160">Ban Time</td>
		  <td><select name="bantime">
		    <option value="2h">2 hours</option>
			<option value="10h">10 hours</option>
		    <option value="1d">1 day</option>
			<option selected="selected" value="5d">5 days</option>
			<option value="7d">7 days</option>
			<option value="1m">1 month</option>
			<option value="p">Permanent</option>
		  </select></td>
		</tr>
		
	    <tr>
		  <td width="160"></td>
		  <td>
		  <input type="submit" value="Create banlist" class="menuButtons" name="create_banlist" />
		  <input type="button" value=" << Back to Bans " class="menuButtons" onclick="location.href='<?=OS_HOME?>adm/?bans'" />
		  </td>
		</tr>
	  </table>
	</form>
	</div>
	<?php
	}
  }
  ?>