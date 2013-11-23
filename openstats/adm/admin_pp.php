<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
$errors = "";
$sql = "";
$search = "";

  if ( isset($_GET["del"]) AND is_numeric($_GET["del"]) ) {
   $sth = $db->prepare("DELETE FROM ".OSDB_GO." WHERE id='".(int)$_GET["del"]."'");
   $result = $sth->execute();
   OS_AddLog($_SESSION["username"], "[os_addpp] REMOVED PP, #".(int)$_GET["del"]." ");
  }
  

  if ( isset($_GET["remove_all"]) AND strlen($_GET["remove_all"])>=2 ) {
   $player = strip_tags( $_GET["remove_all"] );
   $sth = $db->prepare("DELETE FROM ".OSDB_GO." WHERE player_name='".$_GET["remove_all"]."'");
   $result = $sth->execute();
   OS_AddLog($_SESSION["username"], "[os_addpp] REMOVED ALL PP ($player) ");
  }
  
  if ( isset($_GET["search"]) ) {
    $search = safeEscape( trim($_GET["search"]));
	$sql.=" AND player_name LIKE ('".$search."%') ";
  }
  
  
  if ( isset($_POST["add_pp"]) ) {
    $player_name = trim( strip_tags($_POST["player_name"]));
	$reason = trim( strip_tags(safeEscape($_POST["reason"])));
	$expiredate = trim( $_POST["expires"]);
	$admin = trim( strip_tags($_POST["admin"]));
	$pp = trim( (int) $_POST["pp"]);
	$date = date("Y-m-d H:i:00", time() );
	
	if ( strlen($player_name)<=2 ) $errors.="<div>Player name does not have enough characters</div>";
	if ( $pp<=0 ) $errors.="<div>Penalty points can not be less than 1</div>";
	
	if ( empty($errors) ) {
	    
		if ( !isset($_GET["edit"]) ) {
	   	$sqlqr = "INSERT INTO ".OSDB_GO."(player_name, reason, offence_time, offence_expire, pp, admin) 
	     VALUES('".$player_name."', '".$reason."', '".$date."', '".$expiredate."', '".$pp."', '".$admin."' )";
		 OS_AddLog($_SESSION["username"], "[os_addpp] Added PP $player_name  + $pp");
		 }
		 else {
		 $id = (int) $_GET["edit"];
		 $sqlqr = "UPDATE ".OSDB_GO." SET player_name = '$player_name', reason = '$reason', offence_expire = '$expiredate', pp = '$pp', admin = '$admin'
		 WHERE id = $id LIMIT 1";
		 OS_AddLog($_SESSION["username"], "[os_addpp] EDIT PP $player_name ");
		 }
		 
		$sth = $db->prepare($sqlqr);
	    $result = $sth->execute();
	}
  }
  
?>
<div align="center">

<table>
<tr><td>
<form action="" method="get">
  <input type="hidden" name="pp" />
 <input type="text" name="search" value = "<?=$search?>" />
 <input type="submit" value = "Search" class="menuButtons" />
 <a href="<?=OS_HOME?>adm/?pp" class="menuButtons">Reset</a>
</form>
</td></tr>
</table>

<h4>Player Offences - Penalty points</h4>
<a href="<?=OS_HOME?>adm/?pp&amp;addpp" class="menuButtons">Add PP</a>

<?php

 if (!empty($errors)) echo $errors;

 if ( isset($_GET["addpp"]) ) {
    
	$admin = $_SESSION["username"];
	
  if ( !empty($_GET["addpp"]) ) $player_name = $_GET["addpp"]; else $player_name = "";
  
  if ( isset($_GET["edit"]) AND is_numeric($_GET["edit"]) ) {
    $id = (int) $_GET["edit"];
    $sth = $db->prepare("SELECT * FROM ".OSDB_GO." WHERE id=$id LIMIT 1");
    $result = $sth->execute();
	
	$row = $sth->fetch(PDO::FETCH_ASSOC);
	$player_name = $row["player_name"];
	$reason = $row["reason"];
	$offence_expire = $row["offence_expire"];
	if ($offence_expire == "0000-00-00 00:00:00" ) $offence_expire = ""; 
	$pp = $row["pp"];
	$admin = $row["admin"];
	$button = "EDIT PP";
  } else {
    $reason = ""; $offence_expire = ""; $pp = 1; $button = "ADD PP";
  }
  ?>
  <form action="" method="post">
  <div>
    <table>
	  <tr>
	    <td width="120">Player name:</td>
		<td><input style="width: 180px; height: 28px;" type="text" value="<?=$player_name?>" name="player_name" /></td>
	  </tr>
	  <tr>
	    <td width="120">Reason:</td>
		<td><input style="width: 380px; height: 28px;" type="text" value="<?=$reason?>" name="reason" /></td>
	  </tr>
	  <tr>
	    <td width="120">Expires:</td>
		<td>
		<input style="width: 180px; height: 28px;" type="text" value="<?=$offence_expire?>" name="expires" id="expiredate" />
			<div class="padTop padBottom">
	 Set: 
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600 )?>', 'expiredate')" >+1h</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*10 )?>', 'expiredate')" >+10h</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24 )?>', 'expiredate')" >+1 day</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*48 )?>', 'expiredate')" >+2 days</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*72 )?>', 'expiredate')" >+3 days</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*7 )?>', 'expiredate')" >+7 days</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*30 )?>', 'expiredate')" >+1 month</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*60 )?>', 'expiredate')" >+2 months</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*90 )?>', 'expiredate')" >+3 months</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*30*12 )?>', 'expiredate')" >+1 year</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*30*24 )?>', 'expiredate')" >+2 year</a>
    		 <div>YYYY-MM-DD H:i:s (<b>Leave blank</b> if you do not want expire date)</div>
	</div>
		</td>
	  </tr>
	  <tr>
	    <td width="120">PP:</td>
		<td><input type="text" value="<?=$pp?>" name="pp" size="1" /></td>
	  </tr>
	  <tr>
	    <td width="120">Admin:</td>
		<td><input type="text" value="<?=$admin?>" name="admin" style="width: 180px; height: 28px;" /></td>
	  </tr>
	  <tr>
	    <td width="120"></td>
		<td><input type="submit" value="<?=$button?>" name="add_pp" class="menuButtons" /></td>
	  </tr>
	</table>
  </div>
  </form>
  <?php
 }
?>

<?php

  $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_GO." WHERE id>=1 $sql LIMIT 1");
  $result = $sth->execute();
  $r = $sth->fetch(PDO::FETCH_NUM);
  $numrows = $r[0];
  $result_per_page = 30;
  $draw_pagination = 1;
  $SHOW_TOTALS = 1;
  include('pagination.php');

   $sth = $db->prepare("SELECT * FROM ".OSDB_GO." WHERE id>=1 $sql 
   ORDER BY id DESC LIMIT $offset, $rowsperpage");
   $result = $sth->execute();
   
?>
   <table>
    <tr>
	  <th width="32">PP</th>
	  <th width="160">Player</th>
	  <th width="160">Reason</th>
	  <th></th>
	  <th width="130">Expires</th>
	  <th width="130">Date</th>
	  <th width="100">Admin</th>
	</tr>
	<?php
	while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
	$expires = date(OS_DATE_FORMAT, strtotime($row["offence_expire"]));
	if ( date("Y", strtotime($row["offence_expire"]) )<=2000 ) $expires  = "<span class='perm_ban'><i>never</i></span>";
	
	$playername = ($row["player_name"]);
	$color="";
	if ( strstr(strtolower($playername), "|c") ) {
	$col = substr( $playername, 3, 6 );
	$name = substr( $playername, 11, strlen($playername) );
	$playername = '<span style="color: #'.$col.'; text-shadow: 1px 1px 2px #474747; font-weight: bold;">'.$name.'</span>';
	
	
	$debug = explode("|cff", strtolower($row["player_name"]));
	if (isset($debug[1]) ) {
	$color = substr($debug[1],0,6);
	$playername = str_ireplace($color, '<span style="color:#'.substr($color,0,6).'">', strtolower($row["player_name"]))."</span>";
	$playername = str_ireplace ("|cff", "", $playername);
	
	$playername = '<span style="text-shadow: 1px 1px 2px #474747; font-weight: bold;">'.$playername.'</span>';
	}
	}
	
	if ( isset($search) AND strlen($search)>=2 ) $qry = '&search='.$search.''; else $qry = "";
	
	?>
	<tr>
	  <td>+<?=$row["pp"]?></td>
	  <td class="padLeft"><a target="_blank" href="<?=OS_HOME?>?u=<?=$row["player_name"]?>"><?=$playername?></a>
	  
	  <span class="floatR">
	  <a href="<?=OS_HOME?>adm/?pp&amp;addpp&amp;edit=<?=$row["id"].$qry ?>"><img src="edit.png" alt="" width="16" height="16" class="imgvalign" /></a>
	  <a href="javascript:;" onclick="if(confirm('Delete pp?')) { location.href='<?=OS_HOME?>adm/?pp&amp;del=<?=$row["id"].$qry ?>'} ">&times;</a>
	  
	  </span>
	  </td>
	  <td><span style="font-size:11px"><?=$row["reason"]?></span></td>
	  <td>
	  <a href="<?=OS_HOME?>adm/?pp&amp;search=<?=$row["player_name"]?>">All</a> 
	  <a href="javascript:;" onclick="if(confirm('Remove All PP for \'<?=$row["player_name"]?>\'?')) { location.href='<?=OS_HOME?>adm/?pp&amp;remove_all=<?=$row["player_name"]?>'} "><span style="font-size:10px; color: red; float:right;">Remove ALL</span></a>
	  </td>
	  <td><?=$expires ?></td>
	  <td><?=date(OS_DATE_FORMAT, strtotime($row["offence_time"]))?></td>
	  <td><?=$row["admin"]?></td>
	</tr>
	<?php
	}
	?>
	</table>
	<?php
include('pagination.php');
?>
</div>

<div style="margin-top: 180px;">&nbsp;</div>