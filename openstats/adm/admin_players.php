<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
$errors = "";
if ( isset($_GET["search_users"]) ) $s = safeEscape($_GET["search_users"]); else $s=""; 

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
	   
	include("../inc/countries.php");  
	
	if ( isset($_GET["disable_gproxy"]) AND strlen($_GET["disable_gproxy"])>=2 ) {
	  $player = strip_tags( $_GET["disable_gproxy"] );
      $sth = $db->prepare("UPDATE ".OSDB_STATS." SET forced_gproxy = '0' WHERE player='".$player."' ");
	  $result = $sth->execute();
	}
	
	if ( isset($_GET["gproxy"]) AND strlen($_GET["gproxy"])>=2 ) {
	  $player = strip_tags( $_GET["gproxy"] );
      $sth = $db->prepare("UPDATE ".OSDB_STATS." SET forced_gproxy = '1' WHERE player='".$player."' ");
	  $result = $sth->execute();
	}
?>

<div align="center" class="padBottom">
	 <form action="" method="get">
	 <table>
	   <tr>
	    <td width="290">
		  <input type="hidden" name="players" />
		  <input style="width: 180px; height: 24px;" type="text" name="search_users" value="<?=$s?>" />
		  <input class="menuButtons" type="submit" value="Search players" />
		</td>
		<td>
		<select name="country">
		   <option value=""></option>
		  <?php
		  foreach( $Countries as $e=>$v ) {
		  if ( !empty($_GET["country"]) AND $_GET["country"] == $e ) $s='selected="selected"'; else $s = "";
		  ?>
		  <option <?=$s?> value="<?=$e?>"><?=$v?></option>
		  <?php
		  }
		  ?>
		</select>  
		<input class="menuButtons" type="submit" value="Search players" />
		</td>
		<td>
		  <select name="realm">
		    <option value="">Realm:</option>
<?php if (isset($_GET["realm"]) AND $_GET["realm"] == 'europe.battle.net') $s='selected="selected"'; else $s=''; ?>
		    <option <?=$s?> value="europe.battle.net">europe.battle.net</option>
<?php if (isset($_GET["realm"]) AND $_GET["realm"] == 'useast.battle.net') $s='selected="selected"'; else $s=''; ?>
			<option <?=$s?> value="useast.battle.net">useast.battle.net</option>
<?php if (isset($_GET["realm"]) AND $_GET["realm"] == 'uswest.battle.net') $s='selected="selected"'; else $s=''; ?>
			<option <?=$s?> value="uswest.battle.net">uswest.battle.net</option>
<?php if (isset($_GET["realm"]) AND $_GET["realm"] == 'server.eurobattle.net') $s='selected="selected"'; else $s=''; ?>
			<option <?=$s?> value="server.eurobattle.net">server.eurobattle.net</option>
<?php if (isset($_GET["realm"]) AND $_GET["realm"] == 'WC3Connect') $s='selected="selected"'; else $s=''; ?>
			<option <?=$s?> value="OHConnect">OH Connect</option>
<?php if (isset($_GET["realm"]) AND $_GET["realm"] == 'garena') $s='selected="selected"'; else $s=''; ?>
			<option <?=$s?> value="garena">Garena</option>
		  </select>
		  <input class="menuButtons" type="submit" value="Search players" />
		</td>
	   </tr>
	 </table>
	 </form>
</div>
<?php
  
  //This will fix player total leave in /stats\ table
  if ( isset($_GET["fix_leave"]) ) {
      $sth = $db->prepare("SELECT * FROM ".OSDB_STATS." WHERE id>=1 AND botid=0 ORDER BY score DESC LIMIT 500");
	  $result = $sth->execute();
	  while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	  
	      $sth2 = $db->prepare("SELECT COUNT(*)
		  FROM ".OSDB_GP." as gp
		  LEFT JOIN ".OSDB_GAMES." as g ON g.id = gp.gameid 
		  LEFT JOIN ".OSDB_DG." as dg ON dg.gameid = g.id
		  WHERE gp.left <= g.duration-$LeftTimePenalty 
		  AND name='".$row["player"]."' 
		  AND dg.winner>=1
		  LIMIT 1");
		  
		  $res = $sth2->execute();
		  $r = $sth2->fetch(PDO::FETCH_NUM);
		  $numrows = $r[0];
	      echo "<div>".$row["player"]." | Games/Left: ".$row["games"]."/<b>$numrows</b></div>";
		  
		  $upd = $db->prepare("UPDATE ".OSDB_STATS." SET 
		  leaver = '".$numrows."', botid=1
		  WHERE player = '".$row["player"]."'");
		  $update = $upd->execute();
	  }
  }
 
  //OPTIONS
  //admins - remove
  $del = 1;
  if ( isset($_GET["remove_admin"]) AND !empty($_GET["remove_admin"]) AND OS_IsRoot() ) {
    $remove = safeEscape( trim($_GET["remove_admin"]) );
	$sth = $db->prepare("UPDATE ".OSDB_USERS." SET admin_realm = '', user_level = '0' WHERE (name) = ('".$remove."') ");
	$result = $sth->execute();
	if ( $del ) echo '<h2>User removed from admins</h2>';
	$sth = $db->prepare("UPDATE ".OSDB_STATS." SET admin = 0 WHERE (player) = ('".$remove."')");
	$result = $sth->execute();
	OS_AddLog($_SESSION["username"], "[os_players] Removed admin: ( #".$remove." ) ");
  }
  //safelist - remove DEPRECATED
    if ( isset($_GET["remove_safe"]) AND !empty($_GET["remove_safe"]) AND OS_IsRoot() ) {
	/*
    $remove = safeEscape( trim($_GET["remove_safe"]) );
	$sth = $db->prepare("UPDATE ".OSDB_USERS." SET admin_realm = '', user_level = '0' WHERE (name) = ('".$remove."') ");
	$result = $sth->execute();
	if ( $del ) echo '<h2>User removed from safelist</h2>';
	$sth = $db->prepare("UPDATE ".OSDB_STATS." SET safelist = 0 WHERE (player) = ('".$remove."')");
	$result = $sth->execute();
	OS_AddLog($_SESSION["username"], "[os_players] Removed safelisted: ( #".$remove." ) ");
	*/
  }
  
  //ban - remove
    if ( isset($_GET["remove_ban"]) AND !empty($_GET["remove_ban"]) ) {
    $remove = safeEscape( trim($_GET["remove_ban"]) );
	$sth = $db->prepare("DELETE FROM ".OSDB_BANS." WHERE (name) = ('".$remove."') ");
	$result = $sth->execute();
	if ( $del ) echo '<h2>User removed from Bans</h2>';
	$sth = $db->prepare("UPDATE ".OSDB_STATS." SET banned = 0 
	WHERE (player) = ('".$remove."')");
	$result = $sth->execute();
	OS_AddLog($_SESSION["username"], "[os_players] Removed ban: ( #".$remove." ) ");
  }
  
  //warn - remove
    if ( isset($_GET["remove_warn"]) AND !empty($_GET["remove_warn"]) AND OS_IsRoot() ) {
    $remove = safeEscape( trim($_GET["remove_warn"]) );
	$sth = $db->prepare("DELETE FROM ".OSDB_BANS." WHERE (name) = ('".$remove."') ");
	$result = $sth->execute();
	if ( $del ) echo '<h2>User removed from Warns</h2>';
	$sth = $db->query("UPDATE ".OSDB_STATS." SET banned = 0
	WHERE (player) = ('".$remove."')");
	$result = $sth->execute();
  }
    $sql = "";
   //SEARCH
   if ( isset($_GET["search_users"]) AND strlen($_GET["search_users"])>=2 ) {
     $search_users = safeEscape( $_GET["search_users"]);
	 $sql = " AND (player) LIKE ('%".$search_users."%') ";
  } else {
   $sql = "";
   $search_users= "";
  }
  
   //SEARCH COUNTREIS
   if ( isset($_GET["country"]) AND strlen($_GET["country"])>=2 ) {
     $search_country = safeEscape( trim( $_GET["country"]));
	 $sql = " AND country_code = '".$search_country."' ";
  } else {
   //$sql = "";
   $search_country= "";
  }
  
  //GEOIP CITIES - TESTING
  if ( isset($_GET["city"]) ) {
      $sth = $db->prepare("SELECT * FROM ".OSDB_STATS." WHERE id>=1 ORDER BY score DESC LIMIT 50");
	  $result = $sth->execute();
	  include("../inc/geoip/geoipcity.inc");
	  $gi = geoip_open("../inc/geoip/GeoLiteCity.dat",GEOIP_STANDARD);
	  while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	  $IP = $row["ip"];
	  $record = geoip_record_by_addr($gi,$IP);
	  //$city = iconv(mb_detect_encoding($record->city, mb_detect_order(), true), "UTF-8", $record->city );
	  if (isset($record->city) ) $city = mb_convert_encoding($record->city, "UTF-8"); else $city="";
	  if ( $IP == "0.0.0.0" OR $IP = "" ) continue;
	    ?>
	<div><b><?=$row["player"]?></b> <?=$row["ip"]?> | <?=$city?> | <?=$record->country_code . "  " . $record->country_name?> | Lat: <?=$record->latitude?> Lon: <?=$record->longitude?> | Continent: <?=$record->continent_code?> </div>
		<?php
	  }
	  geoip_close($gi);
  }
  
  $ord = "score DESC";
  if ( !isset($_GET["find_leavers"]) ) {
  
  if ( isset($_GET["sort"])   AND $_GET["sort"] == 'admins' )   $sql.=' AND user_level>=9 '; 
  if ( isset($_GET["sort"])   AND $_GET["sort"] == 'banned' )   $sql.=' AND banned>=1 '; 
  if ( isset($_GET["sort"])   AND $_GET["sort"] == 'safelist' ) $sql.=' AND user_level=2 OR user_level=3'; 
  if ( isset($_GET["sort"])   AND $_GET["sort"] == 'hidden' )   $sql.=" AND hide>=1"; 
  if ( isset($_GET["sort"])   AND $_GET["sort"] == 'gproxy' )   $sql.=" AND forced_gproxy>=1"; 
  
  
  if ( isset($_GET["realm"])   AND !empty($_GET["realm"]) )   $sql.=" AND realm = '".strip_tags($_GET["realm"])."' "; 
  
  if ( isset($_GET["sort"])   AND $_GET["sort"] == 'points' )   $ord = "points DESC";
  
  if ( isset($_GET["points"]) AND isset($_GET["id"]) AND OS_IsRoot() ) {
    $pid = (int) $_GET["id"];
	$points = (int) $_GET["points"];
	$sth = $db->prepare("UPDATE ".OSDB_STATS." SET points = '".$points."' WHERE id='".$pid."' ");
	$result = $sth->execute();
	OS_AddLog($_SESSION["username"], "[os_players] Edited points: ( #".$pid.", <b>$points</b> points ) ");
  }

/*
  	$sth = $db->prepare("SELECT * FROM  `oh_stats` GROUP BY country");
	$result = $sth->execute();
	?><textarea style="width:400px;">$Countries = array();
<?php
	while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	?>
$Countries["<?=$row["country_code"]?>"] = "<?=$row["country"]?>";
<?php
	}
	?></textarea><?php
	*/
  ?>
  <div align="center">
  
  Show: 
  <a class="menuButtons" href="<?=OS_HOME?>adm/?players&amp;sort=admins">Admins</a>
  <a class="menuButtons" href="<?=OS_HOME?>adm/?players&amp;sort=banned">Banned</a>
  <a class="menuButtons" href="<?=OS_HOME?>adm/?players&amp;sort=safelist">On Safelist</a>
  <a class="menuButtons" title="Show players who hide their stats" href="<?=OS_HOME?>adm/?players&amp;sort=hidden">Hidden</a>
  <a class="menuButtons" href="<?=OS_HOME?>adm/?players">All ranked players</a>
  <?php if (!isset($_GET["find_leavers"]) ) { ?>
  <a class="menuButtons" href="<?=OS_HOME?>adm/?players&amp;find_leavers">Find Leavers</a>
  <?php } else { ?>
  <a class="menuButtons" href="<?=OS_HOME?>adm/?players">All Players</a>
  <?php } ?>
  <a class="menuButtons" href="<?=OS_HOME?>adm/?players&amp;sort=points">Sort by Points</a>
  <a class="menuButtons" href="<?=OS_HOME?>adm/?players&amp;sort=gproxy">GProxy</a>
  
  <?php
    //SHOW ALL PLAYER IPs
  if (isset($_GET["show"]) AND $_GET["show"] == "ips" AND isset($_GET["player"]) AND strlen($_GET["player"])>=2) {
	?>
	<a class="menuButtons" href="<?=OS_HOME?>adm/?players">&laquo; Back to Players</a>
	<?php  
	  $player = trim($_GET["player"]);
	  $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_STATS." WHERE player=:player ");
	  $sth->bindValue(':player', $player, PDO::PARAM_STR); 
	  $result = $sth->execute();
	  $r = $sth->fetch(PDO::FETCH_NUM);
	  $numrows = $r[0];
	  if ( $numrows<=0 ) {
	  ?><h2>Player not found</h2><a href="<?=OS_HOME?>adm/?players">&laquo Back</a><?php 
	  } else {
	  
	  $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_GP." WHERE name=:player LIMIT 1");
	  $sth->bindValue(':player', $player, PDO::PARAM_STR); 
	  $result = $sth->execute();
	  $r = $sth->fetch(PDO::FETCH_NUM);
	  $numrows = $r[0];
	  $draw_pagination = 1;
	  $result_per_page = 50;
	  $SHOW_TOTALS = 1;
	  include('pagination.php');
	  
	  $sth = $db->prepare("SELECT gp.name, gp.ip, gp.gameid, g.gamename, g.datetime
	  FROM ".OSDB_GP." as gp 
	  LEFT JOIN ".OSDB_GAMES." as g ON gp.gameid = g.id
	  WHERE gp.name=:player 
	  ORDER BY gp.ip DESC, gp.gameid DESC
	  LIMIT $offset, $rowsperpage");
	  $sth->bindValue(':player', $player, PDO::PARAM_STR); 
	  $result = $sth->execute();
	  
	  ?>
	  <table>
	    <tr>
		 <th width="190">Player</th>
		 <th width="126">IP</th>
		 <th width="230">Game</th>
		 <th>Date</th>
		</tr>
	  
	  <?php
	 if ( file_exists("../inc/geoip/geoip.inc") ) {
	 if (!isset($_GET["city"]) ) include("../inc/geoip/geoip.inc");
	 $GeoIPDatabase = geoip_open("../inc/geoip/GeoIP.dat", GEOIP_STANDARD);
	 $GeoIP = 1;
	 }
	  
	  while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	if ($GeoIP == 1 ) {
	$Letter   = geoip_country_code_by_addr($GeoIPDatabase, $row["ip"]);
	$Country  = geoip_country_name_by_addr($GeoIPDatabase, $row["ip"]);
	}
	if ($GeoIP == 1 AND empty($Letter) ) {
	$Letter = "blank";
	$Country  = "Reserved";
	}
	  
	  ?>
	  <tr>
	    <td>
		<?=OS_ShowUserFlag( $Letter, $Country)?> 
		<a href="<?=OS_HOME?>?u=<?=$row["name"]?>" target="_blank"><?=$row["name"]?></a>
		</td>
		<td><?=$row["ip"]?></td>
		<td><a href="<?=OS_HOME?>?game=<?=$row["gameid"]?>" target="_blank"><?=$row["gamename"]?></a></td>
		<td><?=date(OS_DATE_FORMAT, strtotime($row["datetime"]))?></td>
	  </tr>
	  <?php
	  }
	  ?>
	  </table>
	  <?php
	  }
	  include('pagination.php');
	}
	else {
	
  //$groupBy = 'GROUP BY player';  
  $groupBy = '';
  $where = '';
  if (!isset($_GET["search_users"])) $where = " AND `month` = '".date("n")."' AND `year` = '".date("Y")."' ";
  //else { $groupBy = 'GROUP BY player';   }
  $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_STATS." WHERE id>=1 $sql $where $groupBy ");
  $result = $sth->execute();
  $r = $sth->fetch(PDO::FETCH_NUM);
  $numrows = $r[0];
  $result_per_page = 30;
    
  $draw_pagination = 1;
  $SHOW_TOTALS = 1;
  include('pagination.php');
  
  $sth = $db->prepare("SELECT id, ip, user_level, banned, player, leaver, forced_gproxy, realm, wins, points, score, games, losses, alias_id, month, year
  FROM ".OSDB_STATS." WHERE id>=1 $sql $where $groupBy 
  ORDER BY $ord LIMIT $offset, $rowsperpage");
  $result = $sth->execute();
  ?>
  
   <table>
    <tr>
	  <th width="160" class="padLeft">Player/Game Type</th>
	  <th width="95" class="padLeft">Realm/Date</th>
	  <th width="240">Action</th>
	  <th width="80">Score</th>
	  <th width="125">Games (W/L/<span style="color:red">Left</span>)</th>
	  <th width="100">Points</th>
	  <th class="padLeft">Status</th>
	</tr>
    <?php
	 if ( file_exists("../inc/geoip/geoip.inc") ) {
	 if (!isset($_GET["city"]) ) include("../inc/geoip/geoip.inc");
	 $GeoIPDatabase = geoip_open("../inc/geoip/GeoIP.dat", GEOIP_STANDARD);
	 $GeoIP = 1;
	 }
	 
	 if ( isset($_GET["page"]) AND is_numeric($_GET["page"]) ) $p = '&amp;page='.safeEscape( $_GET["page"] );
	 else $p = '';
	 
	 if ( isset($_GET["sort"]) ) $p.= '&amp;sort='.safeEscape( $_GET["sort"] );
	 else $p = '';
	 
	 //LOOP
	 while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
   
	if ($GeoIP == 1 ) {
	$Letter   = geoip_country_code_by_addr($GeoIPDatabase, $row["ip"]);
	$Country  = geoip_country_name_by_addr($GeoIPDatabase, $row["ip"]);
	}
	if ($GeoIP == 1 AND empty($Letter) ) {
	$Letter = "blank";
	$Country  = "Reserved";
	}
	
	if ( $row["user_level"] >= 1 )    
	$is_admin = '<img width="16" height="16" src="ranked.png" alt="" class="imgvalign"/> Admin'; 
	else $is_admin = "";
	
	//if ( $row["safelist"] >= 1 ) $is_safe = '<img width="16" height="16" src="check.png" alt="" class="imgvalign"/> Safelist'; else $is_safe = "";
	
	if ( $row["banned"] >= 1 )   $banned = '<img width="16" height="16" src="del.png" alt="" class="imgvalign"/>  <span style="color:red">Banned</span>'; else $banned = "";
	
	if ( $row["leaver"]>=1 ) $col='red'; else $col = '#000';
	
	if ( $row["forced_gproxy"] >= 1 ) $GProxy = '(G)'; else $GProxy = ''; 
	?>
	<tr class="row">
	  <td><img <?=ShowToolTip($Country , OS_HOME.'img/flags/'.$Letter.'.gif', 130, 21, 15)?> class="imgvalign" width="21" height="15" src="<?=OS_HOME?>img/flags/<?=$Letter?>.gif" alt="" /> 
	  <a target="_blank" href="<?=OS_HOME?>?u=<?=$row["id"]?>"><?=$row["player"]?></a> <?=$GProxy?>
	  <?php
	  if (isset($_GET["search_users"]) AND !empty( $GameAliases[($row["alias_id"]-1)]["alias_name"] ) ) {
	  ?>
	  <div><?=$GameAliases[($row["alias_id"]-1)]["alias_name"]?></div>
	  <?php
	  }
	  ?>
	  </td>
	  <td>
	  <?=($row["realm"])?>
	  <div><b><?=getMonthName( strtolower($row["month"]) )?>, <?=$row["year"]?></b></div>
	  </td>
	  
	  <td>
	  <a href="javascript:;" onclick="showhide('o_<?=$row["id"]?>')">[+] edit</a>
	  
	 <?php
	  $ip_part = OS_GetIpRange( $row["ip"] );
	  if (!empty($ip_part)) {
	 ?>
	 &nbsp; <a class="menuButtons" title="Show All Player IP Address" href="<?=OS_HOME?>adm/?players&amp;player=<?=$row["player"]?>&amp;show=ips">All IPs</a> 
	  
	 <a target="_blank" class="menuButtons" title="Show IP Range" href="<?=OS_HOME?>adm/?bans&amp;ip_range=<?=$ip_part?>&amp;show=all">IP Range</a>
	 
	 <a target="_blank" class="menuButtons" title="Check IP Range" href="<?=OS_HOME?>adm/?bans&amp;check_ip_range=<?=$ip_part?>">Check</a>
	 <?php } ?>
	  <div id="o_<?=$row["id"]?>" style="display:none;">
	  
	  <div><?=$row["player"]?></div>
	  
	  <div>
	  <?php if (empty($GProxy)) { ?>
	  <a class="menuButtons" href="javascript:;" onclick="if (confirm('Force user to use GProxy?') ) { location.href='<?=OS_HOME?>adm/?players&amp;gproxy=<?=$row["player"]?><?=$p?>' }">&raquo; GProxy</a>
	  <?php } else { ?>
	  <a class="menuButtons" href="javascript:;" onclick="if (confirm('Disable GProxy?') ) { location.href='<?=OS_HOME?>adm/?players&amp;disable_gproxy=<?=$row["player"]?><?=$p?>' }">&raquo; Disable GProxy</a>
	  <?php } ?>
	  
	  <?php if (!empty($is_admin) ) { ?>
	  <a class="menuButtons" href="javascript:;" onclick="if (confirm('Remove admin?') ) { location.href='<?=OS_HOME?>adm/?players&amp;remove_admin=<?=$row["player"]?><?=$p?>' }">&raquo; Remove admin</a>
	  <?php } else { ?>
	  <?php if ( OS_IsRoot() ) { ?>
	  <a class="menuButtons" href="javascript:;" onclick="if (confirm('Set user as admin?') ) { location.href='<?=OS_HOME?>adm/?users&amp;add=<?=$row["player"]?>' }">&raquo; Add as admin</a>
	  <?php } else { ?><a href="javascript:;" onclick="alert('You don\'t have permission to change this option.')" class="menuButtons">&raquo; Add as admin</a><?php } ?>
	  <?php } ?>
	  </div>
	   
	  <div>
	  <?php if (!empty($banned) ) { ?>
	  <a class="menuButtons" href="javascript:;" onclick="if (confirm('Remove from bans?') ) { location.href='<?=OS_HOME?>adm/?players&amp;remove_ban=<?=$row["player"]?><?=$p?>' }">&raquo; Remove Ban</a>
	  <?php } else { ?>
	  <a class="menuButtons" href="javascript:;" onclick="if (confirm('Ban player?') ) { location.href='<?=OS_HOME?>adm/?bans&amp;add=<?=$row["player"]?>' }">&raquo; Ban player</a>
	  <?php } ?>
	   </div>
	   
	  <div>
	   </div>
	   
	   <div>
	   <a class="menuButtons" href="javascript:;" onclick="if (confirm('Add Penalty points?') ) { location.href='<?=OS_HOME?>adm/?pp&amp;addpp=<?=$row["player"]?>' }">&raquo; Add PP</a></div>
	   
	  </div>
	  </td>
	  
	  <td><b><?=number_format($row["score"],0)?></b></td>
	  <td><b><?=$row["games"]?></b> (<?=$row["wins"]?> / <?=$row["losses"]?> / <span style="color:<?=$col?>"><?=$row["leaver"]?></span>)</td>
	  <td>
	  <?=number_format($row["points"],0)?> <a href="javascript:;" onclick="showhide('po_<?=$row["id"]?>')">[+]</a>
	  <div id="po_<?=$row["id"]?>" style="display:none;">
	  <form action="" method="get">
	  <input type="hidden" name="players" />
	  <input type="hidden" name="id" value="<?=$row["id"]?>" />
	  <?php if (isset($_GET["page"]) AND is_numeric($_GET["page"]) ) { ?>
	  <input type="hidden" name="page" value="<?=(int) $_GET["page"]?>" />
	  <?php } ?>
	  <?php if (!OS_IsRoot()) $dis = "disabled"; else $dis = ""; ?>
	  <input <?=$dis?> type="text" size="10" value="<?=$row["points"]?>" name="points" />
	  <input <?=$dis?> type="submit" value="save" class="menuButtons" />
	  </form>
	  </div>
	  </td>
	  <td>
	   <?=OS_IsUserGameAdmin( $row["user_level"] )?>
	   <?=OS_ShowUserRole( $row["user_level"] )?>
	  <?=$banned?> 
	  </td>
	</tr>
	<?php
	}
	if ( isset($GeoIP) AND $GeoIP == 1) geoip_close($GeoIPDatabase);
?>
  </table>
  <?php  include('pagination.php'); ?>
  </div>
  <?php 
    }
  } 
    //FIND LEAVERS
	
	if ( isset($_GET["find_leavers"]) ) {
	
	   	  $sth = $db->prepare("SELECT COUNT(*)
		  FROM ".OSDB_GP." as gp
		  LEFT JOIN ".OSDB_GAMES." as g ON g.id = gp.gameid 
		  WHERE gp.left <= g.duration-$LeftTimePenalty LIMIT 1");
		  
		  $result = $sth->execute();
		  $r = $sth->fetch(PDO::FETCH_NUM);
		  $numrows = $r[0];
		  
		  $result_per_page = 20;
		  $draw_pagination = 1;
		  $SHOW_TOTALS = 1;
		  include('pagination.php');
		  
	   	  $sth = $db->prepare("SELECT gp.gameid, gp.name, gp.ip, gp.spoofed, gp.reserved, gp.loadingtime, gp.left, gp.leftreason, gp.team, gp.colour, gp.spoofedrealm, g.duration
		  FROM ".OSDB_GP." as gp
		  LEFT JOIN ".OSDB_GAMES." as g ON g.id = gp.gameid 
		  WHERE gp.left <= g.duration-$LeftTimePenalty 
		  ORDER BY gp.gameid DESC
		  LIMIT $offset, $rowsperpage");
		  
		  $result = $sth->execute();
?>
   <div align="center"><a class="menuButtons" href="<?=OS_HOME?>adm/?players">All Players</a></div>
   
   <table>
    <tr>
	  <th width="32">GameID</th>
	  <th width="220" >Player</th>
	  <th width="90">Banned by</th>
	  <th width="95">Left</th>
	  <th>Reason</th>
	</tr>
<?php		  
		  while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
		  
		  //Query: check ban - Faster then using query like: LEFT JOIN bans
	   	  $chban = $db->prepare("SELECT * FROM ".OSDB_BANS." as b WHERE b.name = '".$row["name"]."' LIMIT 1");
		  $result = $chban->execute();
		  $ban = $chban->fetch(PDO::FETCH_ASSOC);
		  
		  if ( !empty($ban["name"]) ) $b = 1;  else $b = "";
		  
		  if (empty($ban["admin"]) AND !empty($ban["name"]) ) $bannedby = "[system]"; else $bannedby = $ban["admin"];
		  
		  if ( isset($_GET["page"]) AND is_numeric($_GET["page"]) ) $page = (int)$_GET["page"]; else $page = 1;
		  
		  $realm = $row["spoofedrealm"];
		  $ip = $row["ip"];
		    ?>
			<tr>
			  <td class="padLeft"><a href="<?=OS_HOME?>?game=<?=$row["gameid"]?>" target="_blank" >#<?=$row["gameid"]?></a></td>
			  <td><a href="<?=OS_HOME?>?u=<?=strtolower($row["name"])?>" target="_blank" ><b><?=$row["name"]?></b></a> 
			  <?=OS_IsUserGameBanned($b, "Banned by ".$bannedby."", 220) ?>
			  <a href="?bans&amp;add=<?=$row["name"]?>&amp;gid=<?=$row["gameid"]?>&amp;reason=leaver&amp;realm=<?=$realm?>&amp;ip=<?=$ip?>" class="menuButtons floatR" target="_blank" >Ban</a>
			  </td>
			  <td><?=$bannedby?></td>
			  <td><span class="banned"><b>-<?=ROUND(($row["duration"] - $row["left"])/60, 1) ?></b></span> min. </td>
			  <td style="font-size:11px;"><?=$row["leftreason"]?></td>
			</tr>
			<?php
		  }
?>
</table>
<?php	
   include('pagination.php');  
	}
  ?>
  
  
  <div style="margin-top: 180px;">&nbsp;</div>