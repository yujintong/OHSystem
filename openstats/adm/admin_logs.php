<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
$errors = "";

  if ( isset($_GET["del"]) AND is_numeric($_GET["del"]) ) {
   $sth = $db->prepare("DELETE FROM ".OSDB_ADMIN_LOG." WHERE id='".(int)$_GET["del"]."'");
  $result = $sth->execute();
  OS_AddLog($_SESSION["username"], "[os_adminlog] Deleted admin log ( #".(int)$_GET["del"]." ) ");
  }
  
   if ( isset($_GET["purge_admin_logs"]) ) {
    $sth = $db->prepare("TRUNCATE TABLE ".OSDB_ADMIN_LOG." ");
    $result = $sth->execute();
	?>
    <div align="center">
	<h2>All logs are deleted successfully.</h2>
	<a href="<?=OS_HOME?>adm/?admin_logs">Refresh page</a> to continue.
	</div>
	<?php
	OS_AddLog($_SESSION["username"], "[os_adminlog] Deleted all admin logs ");
   }
   if ( isset($_GET["search_logs"]) ) $s = safeEscape($_GET["search_logs"]); else $s=""; 
   ?>
    <form action="" method="get">
     	<input type="hidden" name="admin_logs" />
	    <input style="width: 180px; height: 24px;" id="sl" type="text" name="search_logs" value="<?=$s?>" />
	    <input class="menuButtons" type="submit" value="Search logs" />
		<!--
		<a href="javascript:;" class="menuButtons" onclick="SetDateField('[os_', 'sl')" >OpenStats</a>
		-->
		<a class="menuButtons" href="<?=OS_HOME?>adm/?admin_logs=&search_logs=%5Bos_">OpenStats</a>
		<a href="javascript:;" class="menuButtons" onclick="SetDateField('unban', 'sl')" >Unban</a>
		<a href="javascript:;" class="menuButtons" onclick="SetDateField('tban', 'sl')" >Tban</a>
		<a href="javascript:;" class="menuButtons" onclick="SetDateField('ban', 'sl')" >Ban</a>
		<a href="javascript:;" class="menuButtons" onclick="SetDateField('kick', 'sl')" >Kick</a>
		<a href="javascript:;" class="menuButtons" onclick="SetDateField('rcon', 'sl')" >Rcon</a>
	</form>
  <?php	
  $sql = ""; 
  if ( isset($_GET["search_logs"]) AND strlen($_GET["search_logs"])>=2 ) {
     $search_logs = safeEscape( $_GET["search_logs"]);
	 $sql.= " AND LOWER(log_data) LIKE LOWER('%".$search_logs."%') ";
  } else {
   //$sql = "";
   $search_logs= "";
  }
  
  if ( isset($_GET["log_admin"]) AND strlen($_GET["log_admin"])>=2 ) {
     $search_admin = safeEscape( trim($_GET["log_admin"]));
	 $sql.= " AND log_admin = '".$search_admin."' ";
  }
  
  
  $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_ADMIN_LOG." WHERE id>=1 $sql LIMIT 1");
  $result = $sth->execute();
  $r = $sth->fetch(PDO::FETCH_NUM);
  $numrows = $r[0];
  $result_per_page = 30;
?>
<div align="center">
<h4>
<a class="menuButtons" href="javascript:;" onclick="if (confirm('Delete all logs?') ) {location.href='<?=OS_HOME?>adm/?admin_logs&amp;purge_admin_logs'} " >Purge logs (<?=$numrows?>)</a></h4>
<?php
  $draw_pagination = 1;
  $SHOW_TOTALS = 1;
  include('pagination.php');
  
   $sth = $db->prepare("SELECT * FROM ".OSDB_ADMIN_LOG." WHERE id>=1  $sql  
   ORDER BY id DESC LIMIT $offset, $rowsperpage");
   $result = $sth->execute();
   
   ?>
   <table>
    <tr>
	  <th width="12" class="padLeft">#b</th>
	  <!--<th width="32">#game</th>-->
	  <th width="220">Admin</th>
	  <th width="300">Log</th>
	  <th>Time</th>
	</tr>
	
	<?php
	while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	$data = explode("	", $row["log_data"]);
	if (isset($data[0]) ) $glcl = $data[0];   else $glcl= "";
	if (isset($data[1]) ) $level = $data[1];  else $level= "";
	
	$command = $row["log_data"];
	if ($glcl == "cl") $gamelobby = "Lobby";
	if ($glcl == "gl") $gamelobby = "Game";
	else $gamelobby = "OS";
	
	$admin = $row["log_admin"];
	
	$command = str_replace( array(
	'[os_login]', '[os_addban]', '[os_editban]'), 
	array(
	'Logged', '<b>BAN</b>', '<b>BAN</b>'), $command);
	
	if ( $row["gameid"]>=1 ) $gid = '<a target="_blank" href="'.OS_HOME.'?game='.$row["gameid"].'">'.$row["gameid"].'</a>'; else $gid = "";
	?>
	<tr>
	  <td><?=$row["botid"]?></td>
	  <?php if (!empty($search_logs))   $addstr = "&amp;search_logs=".$search_logs; else $addstr= ""; ?>
	  <?php if (!empty($search_admin)) $addstr2= "&amp;log_admin=".$search_admin; else $addstr2= ""; ?>
	  <?php if (isset($_GET["page"]))   $addstr3= "&amp;page=".(int)$_GET["page"]; else $addstr3= ""; ?>
	  <td><?=OS_IsUserGameAdmin( $level  )?> <a href="<?=OS_HOME?>adm/?admin_logs<?=$addstr?>&amp;log_admin=<?=$admin?>"><?=$admin?></a> [<?=$gamelobby?>]</td>
	  <td>
	  <div style="width:300px; word-wrap: break-word;">
	  <span class="no_rank"><?=$command?></span> 
	  <a href="<?=OS_HOME?>adm/?admin_logs<?=$addstr?><?=$addstr2?><?=$addstr3?>&amp;del=<?=$row["id"]?>" class="floatR">&times;</a>
	  </div>
	  </td>
	  <td><?=date(OS_DATE_FORMAT, strtotime($row["log_time"]))?></td>
	</tr>
	<?php } ?>
</table>
<?php
include('pagination.php');
?>
</div>
  
  <div style="margin-top: 180px;">&nbsp;</div>