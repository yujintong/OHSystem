<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
$errors = "";

if (is_logged() AND isset($_SESSION["level"] ) AND $_SESSION["level"]<=9 AND isset($RootOnly) ) {
?>
<div align="center">
<h2>Only root administrators can run this option</h2>
</div>
<?php
} else {

    if ( isset($_GET["remove_old"]) ) {
	   $sel = $db->prepare("SELECT * FROM ".OSDB_GAMESTATUS." WHERE gametime <= ( NOW() - INTERVAL 2 HOUR)");
	   $result = $sel->execute();
	   $Total = $sel->rowCount();
	   
	   $del = $db->prepare("DELETE FROM ".OSDB_GAMESTATUS." WHERE gametime <= ( NOW() - INTERVAL 2 HOUR)");
	   $result = $del->execute();
	   
	   echo "Removed: <b>". $Total. " games</b> from log";
	   OS_AddLog($_SESSION["username"], "[os_live] Removed: <b>$Total</b> games from log");
	}

    if ( isset($_GET["remove"]) AND is_numeric($_GET["remove"]) ) {
	  $id = (int) $_GET["remove"];
	  $del = $db->prepare("DELETE FROM ".OSDB_GAMESTATUS." WHERE gameid='".$id."'");
	  $result = $del->execute();
	  OS_AddLog($_SESSION["username"], "[os_live] Removed game ( #$id ) from log");
	}

     if ( isset($_GET["del"]) AND $_GET["del"] == "finished") {
    $del = $db->prepare("DELETE FROM ".OSDB_GAMESTATUS." WHERE gamestatus>=3");
    $result = $del->execute();
	?>
    <div align="center">
	<h2>All finished games are deleted successfully.</h2>
	<a href="<?=OS_HOME?>adm/?live_games">Refresh page</a> to continue.
	</div>
	<?php
	OS_AddLog($_SESSION["username"], "[os_live] Removed finished games");
   }

   if ( isset($_GET["purge_logs"]) ) {
    $sth = $db->prepare("TRUNCATE TABLE ".OSDB_GAMELOG." ");
    $result = $sth->execute();
    $del = $db->prepare("DELETE FROM ".OSDB_GAMESTATUS." WHERE gamestatus>=3");
    $result = $del->execute();
	?>
    <div align="center">
	<h2>All logs are deleted successfully.</h2>
	<a href="<?=OS_HOME?>adm/?live_games">Refresh page</a> to continue.
	</div>
	<?php
	OS_AddLog($_SESSION["username"], "[os_live] REMOVED ALL LOGS (PURGE) ");
   }
   
    $tot = $db->prepare( "SELECT COUNT(*) FROM ".OSDB_GAMESTATUS." 
	WHERE gamestatus>=3" );
	$result = $tot->execute();
    $r = $tot->fetch(PDO::FETCH_NUM);
    $TotalFinishedGames = $r[0];
   
     $sth = $db->prepare( "SELECT * FROM ".OSDB_GAMESTATUS." WHERE gamestatus<=2 
     ORDER BY gamestatus ASC, gametime DESC LIMIT 50"  );
     $result = $sth->execute();
     $IDS = array();
     $c = 0;
     $GameName = "";
	 
	 $TotalGames = $sth->rowCount();
	 ?>
	 <h4><a href="javascript:;" onclick="showhide('cg')">Show/Hide current games</a> <?php if (isset($_GET["remove"])) { ?><a href="<?=OS_HOME?>adm/?live_games">[Refresh]</a><?php } ?></h4>
	 <div id="cg" <?php if (!isset($_GET["remove"])) { ?>style="display:none;" <?php } ?>>
	 <table>
	 <tr class="row">
	   <th width="32" class="padLeft">#</th>
	   <th width="250">Game Name</th>
	   <th width="120" class="padLeft">Game Time</th>
	   <th>Action</th>
	 </tr>
	 <?php
     while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	 $GameStatus = $row["gamestatus"];
	  if ($GameStatus <= 1 ) { $status = "Lobby"; }
	  if ($GameStatus == 2 ) { $status = "Game";  }
	 ?>
	 <tr class="row">
	   <td><?=$row["gameid"]?></td>
	   <td>[<?=$status?>] <a href="<?=OS_HOME?>adm/?live_games&amp;sort=<?=$row["gameid"]?>"><?=$row["gamename"]?></a></td>
	   <td><?=date(OS_DATE_FORMAT, strtotime($row["gametime"]))?></td>
	   <td>
	     <?php 
		  if ( $_SESSION["level"]>=10) {
		  ?>
		  <a class="menuButtons" href="javascript:;" onclick="if (confirm('Remove game from database?')) { location.href='<?=OS_HOME?>adm/?live_games&amp;remove=<?=$row["gameid"]?>' }">&times; Remove</a>
		  <?php
		  }
		 ?>
	   </td>
	 </tr>
	 <?php
	 }
	 ?>
	 </table>
	 </div>
	 <?php
	 
	 if ( isset($_GET["sort"]) AND is_numeric($_GET["sort"]) ) {
	    $id = (int) $_GET["sort"];
		$sql = " AND gameid=$id";
	 } else $sql ="";
	 
	 if ( isset($_GET["sort"]) ) {
		 
		if($_GET["sort"] == "pm")
		$sql = " AND log_data LIKE('%whisp	%')";
	 }
	 
	 $sth = $db->prepare("SELECT COUNT(*) FROM `".OSDB_GAMELOG."` WHERE id>=0 $sql ");
     $result = $sth->execute();
     $r = $sth->fetch(PDO::FETCH_NUM);
     $numrows = $r[0];
     $result_per_page = 30;
	 
     $draw_pagination = 1;
     $SHOW_TOTALS = 1;

	?>
	<a name="live"></a>
	<h2>Game Logs</h2>
	<div class="padTop padBottom">
	<a href="<?=OS_HOME?>adm/?live_games" class="menuButtons">Refresh</a> 
	<?php if (!isset($_GET["show"]) ) { ?>
	<a href="<?=OS_HOME?>adm/?live_games&amp;show=unparsed" class="menuButtons">Show unparsed data</a>
    <?php } else { ?>
    <a href="<?=OS_HOME?>adm/?live_games" class="menuButtons">Show Default</a>
     <?php } ?>	
	<!--<a href="<?=OS_HOME?>adm/?live_games&amp;sort=pm" class="menuButtons">Show User PM</a>-->
	<a href="javascript:;" class="menuButtons" onclick="if (confirm('Purge all logs?') ) { location.href='<?=OS_HOME?>adm/?live_games&purge_logs' }">Purge logs</a>
	
	<a class="menuButtons" onclick="if(confirm('Delete all finished games from log?')) { location.href='<?=OS_HOME?>adm/?live_games&amp;del=finished' }" href="javascript:;"><b>Finished games:</b> <?=$TotalFinishedGames?>  &times;</a>
	
	<a class="menuButtons" href="javascript:;" onclick="if (confirm('This will remove all the games over 2 hours  from logs. Sure?')) { location.href='<?=OS_HOME?>adm/?live_games&amp;remove_old' }">&times; Remove old games</a>
	</div>
	<?php  include('pagination.php'); 
	$sth = $db->prepare("SELECT * FROM `".OSDB_GAMELOG."` 
	WHERE id>=0 $sql ORDER BY id DESC, log_time DESC LIMIT $offset, $rowsperpage");
	$result = $sth->execute();
	?>
	<table>
	 <tr class="row">
	   <th class="padLeft" width="32">#</th>
	   <th width="32">GameID</th>
	   <th width="120" class="padLeft">Game Time</th>
	   <th>Log Data</th>
	 </tr>
	<?php
	require_once('../inc/live_games_functions.php');
	while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	$log_time = date( OS_DATE_FORMAT, strtotime($row["log_time"]) );
	$log_data = OS_ParseGameLog($row["log_data"]);
	if ( (isset($_GET["show"]) AND $_GET["show"] == "unparsed") OR (isset($_GET["sort"]) AND $_GET["sort"] == "pm") ) { 
	$log_data = str_replace("\n", "<br />", $row["log_data"]);
	$log_data = str_replace("	", " &nbsp; &nbsp; ", $log_data);
	$log_data.='<div><a href="javascript:;" onclick="showhide(\'g'.$row["id"].'\')">[Show]</a></div>';
	$log_data.='<div id="g'.$row["id"].'" style="display:none;">'.OS_ParseGameLog($row["log_data"]).'</div>';
	}
	$ID = $row["id"];
	?>
	<tr class="row">
	  <td><?=$ID ?></td>
	  <td><?=$row["gameid"];?></td>
	  <td><?=$log_time?></td>
	  <td><?=($log_data)?></td>
	</tr>
	<?php
	}
	?></table><?php
	include('pagination.php');
   
}