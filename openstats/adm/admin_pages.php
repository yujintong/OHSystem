<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

if ( !$_GET OR (isset($_GET["delete_file"]) OR isset($_GET["delete_cache"]) OR isset($_GET["delete_replay_cache"]) OR isset($_GET["view_cache"]) OR isset($_GET["optimize_tables"]) )) {

   	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_GAMES." 
	WHERE map LIKE '%".OS_DEFAULT_MAP."%' AND stats = 0 AND duration>='".$MinDuration."'" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalGamesForUpdate = $r[0];
	
   	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_GAMES." 
	WHERE map LIKE '%".OS_DEFAULT_MAP."%' AND stats = 1" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalRankedGames = $r[0];
	
	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_BANS." 
	WHERE id>=1" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalBans = $r[0];
	
	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_STATS." 
	WHERE id>=1" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalRankedUsers = $r[0];
	
	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_USERS." 
	WHERE user_id>=1 AND user_level>=1" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalAdmins = $r[0];
	
	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_USERS." 
	WHERE user_id>=1 AND user_level=1" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalReserved = $r[0];
	
	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_USERS." 
	WHERE user_id>=1 AND user_level=2" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalSafelist = $r[0];
	
	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_USERS." 
	WHERE user_id>=1 AND user_level=3" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalSafelistReserved = $r[0];
	
	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_USERS." 
	WHERE user_id>=1 AND user_level=4" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalWebsiteMod = $r[0];
	
	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_USERS." 
	WHERE user_id>=1 AND user_level=5" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalSimpleMod = $r[0];
	
	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_USERS." 
	WHERE user_id>=1 AND user_level=6" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalFullMod = $r[0];
	
	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_USERS." 
	WHERE user_id>=1 AND user_level=7" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalGlobalMod = $r[0];
	
	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_USERS." 
	WHERE user_id>=1 AND user_level=8" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalHosters = $r[0];
	
	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_USERS." 
	WHERE user_id>=1 AND user_level=9" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalAdminsR = $r[0];
	
	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_USERS." 
	WHERE user_id>=1 AND user_level=10" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalRoot = $r[0];
	
	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_USERS." 
	WHERE user_id>=1" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalUsers = $r[0];
	
	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_NEWS." 
	WHERE news_id>=1" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalNews = $r[0];
	
   	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_COMMENTS." 
	WHERE id >= 1" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalComments = $r[0];
	
   	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_REPORTS."" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalBanReports = $r[0];
	
   	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_APPEALS."" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalBanAppeals = $r[0];
	
	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_GUIDES."" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalGuides = $r[0];
	
	$sth = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_GAMELOG."" );
	$result = $sth->execute();
    $r = $sth->fetch(PDO::FETCH_NUM);
    $TotalGameLogs = $r[0];
	
	$c = 0;
	$CachedFiles = array();
	$cacheDir = "../inc/cache/pdheroes";
if ( $PlayDotaHeroes == 1 AND file_exists($cacheDir) ) {
   if ($handle = opendir($cacheDir)) {
       while (false !== ($file = readdir($handle))) {
	    if ($file !="." AND  $file !="index.html" AND $file !=".."  ) {
		$CachedFiles[$c]["path"] = $cacheDir; 
		$CachedFiles[$c]["file"] = $file; 
		$c++;
		}
	   
	   }
   }
}
$TotalFiles = $c;
	?>
	<a name="files"></a>
	<div align="center" style="margin-top: 6px; margin-bottom: 100px;">
	<?php if (isset($OptimizedTables ) ) { ?>
	<h2>All tables successfully optimized <a href="<?=OS_HOME?>adm/">[OK]</a></h2>
	<?php } ?>

	<?php
	if (!$_GET) {
	?>
	<table>
	<tr>
	  <th width="90">Setup BOT:</th>
	  <th>
	  <a class="menuButtons" href="<?=OS_HOME?>adm/?announcements">Annoucements</a>
	  <?php if (OS_IsRoot() ) { ?>
	  <a class="menuButtons" href="<?=OS_HOME?>adm/?aliases">Game Types (Aliases)</a>
	  <?php } ?>
	  <a class="menuButtons" href="<?=OS_HOME?>adm/?ban_names">Ban Names</a>
	  <a class="menuButtons" href="<?=OS_HOME?>adm/?word_filter">Word filter</a>
	  <a class="menuButtons" href="<?=OS_HOME?>adm/?bans&amp;addcountry">Ban Country</a>
	  <a class="menuButtons" href="<?=OS_HOME?>adm/?remote">Remote control</a>
	  <a class="menuButtons" href="<?=OS_HOME?>adm/?gamelist">Gamelist</a>
	  <a class="menuButtons" href="<?=OS_HOME?>adm/?live_games">Live games</a>
	  <?php if (OS_IsRoot()) { ?>
	  <a class="menuButtons" href="<?=OS_HOME?>adm/?admin_logs">Logs</a>
	  <?php } ?>
	  </th>
	</tr>  
	</table>
	<?php
	}
	$RealPath = realpath(dirname(__FILE__));
	$BotPath = explode("/", $RealPath);
	$TotalD = count($BotPath);
	$FindPath = "";
	for ($i=0; $i<=($TotalD-3); $i++) {
	$FindPath.=$BotPath[$i]."/";
	}
	$BotPath = $FindPath."ghost++/default.cfg";
	?>
	<div align="left" style="text-align:left; margin-left:32px;">
	  <div><b>Real path:</b> <?=$RealPath;?></div>
	  <div><b>Bot path:</b> <?=$BotPath;?><?php if (!file_exists(realpath($BotPath) ) ) echo " <span style='color:red;'>(not exists!)</span>"; ?>  </div>
	  <div><b>Server OS:</b> <?php echo PHP_OS; ?>, PHP: <?php echo PHP_VERSION; ?>, <?=$_SERVER["SERVER_SOFTWARE"] ?></div>
	  <div><b>Address:</b> <?=$_SERVER['SERVER_ADDR']?>, <b>Port:</b> <?=$_SERVER['SERVER_PORT'] ?></div>
	  
	  <form action="<?=OS_HOME?>adm/daemon/" method="get" autocomplete = "off" >
	    <input type="text" name="pw" value="" required /> <input type="submit" value="Start Daemon" class="menuButtons" /> - Type cron password
	  </form>
	  
	</div>
	<?php
	if ( isset($_SESSION["intro_message"]) ) echo $_SESSION["intro_message"];
	?>
	
	<table>
	  <tr>
	    <th class="padLeft" width="200">Dashboard</th>
		<th>You are: <?=OS_ShowUserRole( $_SESSION["level"] )?></th>
	  </tr>
	  <tr class="row">
	    <td width="200" class="padLeft"><b>Unranked games:</b></td>
	    <td>
		<?=number_format($TotalGamesForUpdate,0)?>
		<?php if ($TotalGamesForUpdate>=1 AND isset($FORCE_UPDATE) ) { ?><a class="menuButtons" href="<?=OS_HOME?>adm/update_stats.php">Update</a><?php } ?>
		</td>
	  </tr>
	  
	  <tr class="row">
	    <td width="200" class="padLeft"><b>Ranked games:</b></td>
	    <td>
		<a href="<?=OS_HOME?>adm/?games"><?=number_format($TotalRankedGames,0)?></a>
		</td>
	  </tr>
	  
	  <tr class="row">
	    <td width="200" class="padLeft"><b>Ranked Players:</b></td>
	    <td>
		<a href="<?=OS_HOME?>adm/?players"><?=number_format($TotalRankedUsers,0)?></a>
		</td>
	  </tr>
	  
	  <tr class="row">
	    <td width="200" class="padLeft"><b>Total Bans:</b></td>
	    <td>
		<a href="<?=OS_HOME?>adm/?bans"><?=number_format($TotalBans,0)?></a>
		</td>
	  </tr>
	  
	  <tr class="row">
	    <td width="200" class="padLeft"><b>Total User Roles:</b></td>
	    <td>
		<a href="<?=OS_HOME?>adm/?admins"><?=number_format($TotalAdmins,0)?></a> 
		<a class="padLeft" href="javascript:;" onclick="showhide('roles')"><b>[Show]</b></a>
		<table style="width:300px; display: none;" id="roles" >
		  <tr>
		    <th width="150">Role</th>
			<th width="150">Total</th>
		  </tr>
		    <tr>
			  <td>Reserved Slot</td>
			  <td><a href="<?=OS_HOME?>adm/?admins&amp;sort=1"><?=$TotalReserved?></a></td>
			</tr>
		    <tr>
			  <td>Safelisted</td>
			  <td><a href="<?=OS_HOME?>adm/?admins&amp;sort=2"><?=$TotalSafelist?></a></td>
			</tr>
		    <tr>
			  <td>Safelist+Reserved</td>
			  <td><a href="<?=OS_HOME?>adm/?admins&amp;sort=3"><?=$TotalSafelistReserved?></a></td>
			</tr>
		    <tr>
			  <td>Website Moderators</td>
			  <td><a href="<?=OS_HOME?>adm/?admins&amp;sort=4"><?=$TotalWebsiteMod?></a></td>
			</tr>
		    <tr>
			  <td>Simple Bot Moderator</td>
			  <td><a href="<?=OS_HOME?>adm/?admins&amp;sort=5"><?=$TotalSimpleMod?></a></td>
			</tr>
		    <tr>
			  <td>Full Bot Moderator</td>
			  <td><a href="<?=OS_HOME?>adm/?admins&amp;sort=6"><?=$TotalFullMod?></a></td>
			</tr>
		    <tr>
			  <td>Global Bot Moderator</td>
			  <td><a href="<?=OS_HOME?>adm/?admins&amp;sort=7"><?=$TotalGlobalMod?></a></td>
			</tr>
		    <tr>
			  <td>Hosters</td>
			  <td><a href="<?=OS_HOME?>adm/?admins&amp;sort=8"><?=$TotalHosters?></a></td>
			</tr>
		    <tr>
			  <td>Admins</td>
			  <td><a href="<?=OS_HOME?>adm/?admins&amp;sort=9"><?=$TotalAdminsR?></a></td>
			</tr>
		    <tr>
			  <td>root</td>
			  <td><a href="<?=OS_HOME?>adm/?admins&amp;sort=10"><?=$TotalRoot?></a></td>
			</tr>
		</table>
		</td>
	  </tr>
	  
	  <tr class="row">
	    <td width="200" class="padLeft"><b>Total Members:</b></td>
	    <td>
		<a href="<?=OS_HOME?>adm/?users"><?=number_format($TotalUsers,0)?></a>
		</td>
	  </tr>
	  
	  <tr class="row">
	    <td width="200" class="padLeft"><b>Total Posts:</b></td>
	    <td>
		<a href="<?=OS_HOME?>adm/?posts"><?=number_format($TotalNews,0)?></a>
		</td>
	  </tr>
	  <tr class="row">
	    <td width="200" class="padLeft"><b>Total Comments:</b></td>
	    <td>
		<a href="<?=OS_HOME?>adm/?comments"><?=number_format($TotalComments,0)?></a>
		</td>
	  </tr>
	  
	  <tr class="row">
	    <td width="200" class="padLeft"><b>Game Logs:</b></td>
	    <td>
		<a href="<?=OS_HOME?>adm/?live_games"><?=number_format($TotalGameLogs,0)?></a> (max. <?=number_format($LiveGamesTotalLogs,0)?>)
		</td>
	  </tr>
	  
	  <tr class="row">
	    <td width="200" class="padLeft"><b>Ban Reports:</b></td>
	    <td>
		<a href="<?=OS_HOME?>adm/?ban_reports"><?=number_format($TotalBanReports,0)?></a>
		</td>
	  </tr>
	  
	  <tr class="row">
	    <td width="200" class="padLeft"><b>Ban Appeals:</b></td>
	    <td>
		<a href="<?=OS_HOME?>adm/?ban_appeals"><?=number_format($TotalBanAppeals,0)?></a>
		</td>
	  </tr>
	  
	  <tr class="row">
	    <td width="200" class="padLeft"><b>Guides:</b></td>
	    <td>
		<a href="<?=OS_HOME?>adm/?guides"><?=number_format($TotalGuides, 0)?></a>
		</td>
	  </tr>
	  
	</table>
	<div class="padTop"></div>
	<table>
	<tr>
	<td width="480" class="padLeft"><a class="menuButtons" href="javascript:;" onclick="if (confirm('Are you sure you want to reset all statistics?') ) {  location.href='<?=OS_HOME?>adm/update_stats.php?reset' }" >Reset Statistics</a>
	  
	  <a class="menuButtons" href="javascript:;" onclick="if(confirm('Enable All Comments?') ) { location.href='<?=OS_HOME?>adm/?posts&amp;com=1' }">Enable All Comments</a>
	  
	  <a class="menuButtons" href="javascript:;" onclick="if(confirm('Disable All Comments?') ) { location.href='<?=OS_HOME?>adm/?posts&amp;com=0' }">Disable All Comments</a>
	
	</td>
	</tr>
	</table>
	
	<div class="padTop"></div>
	<table>
	<tr>
	<td width="170" class="padLeft"><b>Cached files:</b> <?=$TotalFiles?></td>
	<td>
	<a class="menuButtons" href="<?=OS_HOME?>adm/?delete_cache">Delete</a><?php if ($TotalFiles>=1) { ?> | 
	<a class="menuButtons" href="<?=OS_HOME?>adm/?view_cache#files">View files</a><?php } ?>
	</td>
	</tr>
<?php if (isset($_GET["view_cache"]) AND $TotalFiles>=1) { ?>
	  <?php 
	  foreach($CachedFiles as $File) {
	  $FilePath = str_replace("../", "", $File["path"]);
	  ?>
    <tr class="row">
	  <td width="480" class="padLeft">
	  <div>
	  <?=str_replace("../", "", $File["path"])?>/<b><?=$File["file"]?></b>
	  <a href="<?=OS_HOME?>adm/?delete_file=<?=urlencode($FilePath)?>/<?=urlencode($File["file"])?>">&times;</a>
	  </div>
	  </td>
	</tr>
	  <?php
	  }
	  ?>
<?php } ?>
	</table>
	
<?php 	
		

	$c = 0;
	$CachedReplayFiles = array();
	$cacheReplayDir = "../".$ReplayLocation;
	

	if ( isset($_GET["delete_replay_cache"]) AND file_exists($cacheReplayDir) ) {
	   if ($handle = opendir($cacheReplayDir)) {
	    while (false !== ($file = readdir($handle))) { 
		 if (file_exists($cacheReplayDir."/".$file) AND substr($file, -4) == "html"  AND  $file !="index.html" ) 
		 unlink($cacheReplayDir."/".$file);
		}
	   }
	}
	
if ( file_exists($cacheReplayDir) ) {
   if ($handle = opendir($cacheReplayDir)) {
       while (false !== ($file = readdir($handle))) {
	    if ($file !="." AND  $file !="index.html" AND $file !=".."  ) {
		if (substr($file, -4) == "html" ) {
		$CachedReplayFiles[$c]["path"] = $cacheReplayDir; 
		$CachedReplayFiles[$c]["file"] = $file; 
		}
		$c++;
		}
	   
	   }
   }
}

if (count($CachedReplayFiles) >=0 ) { ?>

	<table>
	<tr>
	<td width="170" class="padLeft"><b>Cached replay files:</b> <?=count($CachedReplayFiles)?></td>
	<td>
	<a class="menuButtons" href="<?=OS_HOME?>adm/?delete_replay_cache">Delete cached replay files</a>
	</td>
	</tr>
	</table>
	<?php
}
?>
   <table>
	<tr>
	  <td width="170" class="padLeft"><b>Reset player points:</b></td>
	  <td>
	  <form action="" method="get">
	  <input type="text" value="50" size="5" name="reset_points" />
	  <input type="checkbox" name="rp" value="1" /> Confirm reset points
	  <input type="submit" value="Reset player points" class="menuButtons" />
	  </form>
	  </td>
	</tr>
	</table>
<?php if (defined('OS_VERSION') ) { ?>	
	<div class="padTop">
	Version: <?=OS_VERSION?>
	</div>
<?php } ?>	
	</div>
<div style="margin-top: 220px;">&nbsp;</div>
	<?php
} else 
if ( isset( $_GET["gamelist"]) )     include('admin_gamelist_patch.php');   else

if ( isset( $_GET["posts"]) )     include('admin_posts.php');   else
if ( isset( $_GET["bans"]) )      include('admin_bans.php');    else
if ( isset( $_GET["admins"]) )    include('admin_admins.php');  else
if ( isset( $_GET["safelist"]) )  include('admin_safelist.php');else
if ( isset( $_GET["users"]) )     include('admin_users.php');   else
if ( isset( $_GET["games"]) )     include('admin_games.php');   else
if ( isset( $_GET["comments"]) )  include('admin_comments.php');else
if ( isset( $_GET["cfg"]) )       include('admin_cfg.php');     else
if ( isset( $_GET["notes"]) )     include('admin_notes.php');   else
if ( isset( $_GET["ban_reports"]) )  include('admin_ban_reports.php'); else
if ( isset( $_GET["ban_appeals"]) )  include('admin_ban_appeals.php'); else 
if ( isset( $_GET["tos"]) )          include('admin_tos.php'); else
if ( isset( $_GET["heroes"]) )       include('admin_heroes.php'); else
if ( isset( $_GET["items"]) )       include('admin_items.php'); else 
if ( isset( $_GET["guides"]) )      include('admin_guides.php'); else
if ( isset( $_GET["plugins"]) )     include('admin_plugins.php'); else 
if ( isset( $_GET["players"]) )     include('admin_players.php'); else
if ( isset( $_GET["remote"]) )      include('admin_remote_control.php'); else
if ( isset( $_GET["live_games"]) )  include('admin_live_games.php'); else
if ( isset( $_GET["bnet_pm"]) )     include('admin_bnet_pm.php'); else
if ( isset( $_GET["word_filter"]) ) include('admin_word_filter.php'); else
if ( isset( $_GET["pp"]) )          include('admin_pp.php'); else
if ( isset( $_GET["announcements"]) ) include('admin_announcements.php'); else
if ( isset( $_GET["ban_email"]) AND OS_IsRoot() )     include('admin_ban_email.php'); else
if ( isset( $_GET["geoip"]) )       include('admin_geoip.php'); else
if ( isset( $_GET["admin_logs"])    AND $_SESSION["level"]>=10 ) include('admin_logs.php'); else 
if ( isset( $_GET["warns"]) )       include('admin_warns.php'); else 
if ( isset( $_GET["aliases"]) )     include('admin_aliases.php'); else
if ( isset( $_GET["ban_names"]) )     include('admin_banned_names.php');
?>
