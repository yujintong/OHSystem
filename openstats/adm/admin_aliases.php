<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }


if ( isset($_POST["submit_gt"]) ) {
   $words = strip_tags( trim( $_POST["bad_words"] ) );
   $saved=1;
}

$alias_name = "";
$alias_id = "";
$button = "Add";
    
	if ( isset($_GET["reset"]) AND is_numeric($_GET["reset"]) ) {
	  $alias = (int) $_GET["reset"];
	  
	  $upd = $db->prepare("UPDATE ".OSDB_GAMES." SET stats=0 WHERE alias_id = '".$alias."' ");
	  $result = $upd->execute();
	  $upd2 = $db->prepare("DELETE FROM ".OSDB_STATS." WHERE alias_id = '".$alias."' ");
	  $result = $upd2->execute();
	}


    if ( isset($_GET["default"]) AND is_numeric($_GET["default"]) ) {
	
	  $sth = $db->prepare("UPDATE ".OSDB_ALIASES." SET `default_alias` = '0' ");
	  $result = $sth->execute();
	  
	  $sth = $db->prepare("UPDATE ".OSDB_ALIASES." SET `default_alias` = '1' WHERE alias_id = '".(int) $_GET["default"]."' ");
	  $result = $sth->execute();
	  $saved=1;
	}

    if ( isset($_GET["delete"]) AND is_numeric($_GET["delete"]) ) {
	
	  $sth = $db->prepare("DELETE FROM ".OSDB_ALIASES." WHERE alias_id = '".(int) $_GET["delete"]."' ");
	  $result = $sth->execute();
	  $saved=1;
	}


    if ( !isset($_GET["edit"]) AND isset($_POST["form_gt"]) ) {
	
	  $alias_name = strip_tags(trim($_POST["alias_name"]));
	  $alias_id = strip_tags(trim($_POST["alias_id"]));
	  $sth = $db->prepare("INSERT INTO ".OSDB_ALIASES." (alias_id, alias_name ) VALUES('".$alias_id."', '".$alias_name."') ");
	  $result = $sth->execute();
	  $saved=1;
	}
	
if ( isset($_GET["edit"]) ) {

    if ( isset($_POST["form_gt"]) ) {
	
	  $alias_name = strip_tags(trim($_POST["alias_name"]));
	  $alias_id = strip_tags(trim($_POST["alias_id"]));
	  $sth = $db->prepare("UPDATE ".OSDB_ALIASES." SET alias_id= '".$alias_id."', alias_name='".$alias_name."' 
	  WHERE alias_id = '".(int) $_GET["edit"]."' ");
	  $result = $sth->execute();
	  $saved=1;
	}

    $sth = $db->prepare("SELECT * FROM ".OSDB_ALIASES." WHERE alias_id = '".(int) $_GET["edit"]."' ");
	$result = $sth->execute();
	$row = $sth->fetch(PDO::FETCH_ASSOC);
	$button = "Edit";
	$alias_name = $row["alias_name"];
	$alias_id = $row["alias_id"];
}
?>
<div align="center"> 
<h2>Game Types</h2>

<table>
<tr>
  <th width="50">Alias ID</th>
  <th width="220">Name</th>
  <th>Action</th>
  <th></th>
</tr>
<?php
	//GAME TYPES/ALIASES (dota, lod)
	
    $sth = $db->prepare("SELECT * FROM ".OSDB_ALIASES." ORDER BY alias_id ASC");
	$result = $sth->execute();
	$GameAliases = array();
	$c = 0;
	$DefaultValue = 0;
	while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	$aid = ($row["alias_id"] + 1);
	if ($row["default_alias"] == 1) $DefaultValue = 1;
	 ?>
	 <tr>
	   <td><?=$row["alias_id"]?></td>
	   <td><?=$row["alias_name"]?></td>
	   <td width="230">
	   <a class="menuButtons" href="<?=OS_HOME?>adm/?aliases&amp;edit=<?=$row["alias_id"]?>">Edit</a>
	   <a class="menuButtons" onclick="if(confirm('Delete this game type?')) { location.href='<?=OS_HOME?>adm/?aliases&amp;delete=<?=$row["alias_id"]?>' }" href="javascript:;">Delete</a>
	   <?php if ($row["default_alias"] == 0) { ?>
	   <a onclick="if(confirm('Make default this game type?')) { location.href='<?=OS_HOME?>adm/?aliases&amp;default=<?=$row["alias_id"]?>' }" href="javascript:;">Make default</a>
	   <?php } else { ?>
	   <span style="color: green; font-weight:bold;">Default</span>
	   <?php } ?>
	   </td>
	   <td>
	     <a href="javascript:;" onclick="if(confirm('Reset stats for this game type? WARNING: This will delete some records from stats?')){ location.href='<?=OS_HOME?>adm/?aliases&reset=<?=$row["alias_id"]?>'  }">Reset stats</a>
		 <?php if (isset($_GET["reset"]) AND $_GET["reset"] == $row["alias_id"] ) { ?>
		 <span style="color:red;margin-left:10px;">Done!</span>
		 <?php } ?>
	   </td>
	 </tr>
	 <?php
	 
	 
	 $c++;
	}
	
	if (!isset($_GET["edit"]) AND isset($aid) ) $alias_id = ($aid);
?>
</table>

<?php if ($DefaultValue == 0 ) { ?>
<div style="text-align:left; margin-left:32px; color:red;">
Note: You don't have default game type. Please select default game type (click on Make default link).
</div>
<?php } ?>

<div align="left" style="text-align:left; margin-left:40px;"><h2><?=$button?> game type <?=$alias_id?></h2></div>
<?php if (!empty($alias_id)) { ?>
<a href="<?=OS_HOME?>adm/?aliases" class="menuButtons">Add new game type</a>
<?php } ?>
<form action="" method="post">
  <table>
    <tr>
	  <td width="100">Alias ID:</td>
	  <td><input type="text" value="<?=$alias_id?>" name="alias_id" size="2" /></td>
	</tr>
    <tr>
	  <td width="100">Alias name:</td>
	  <td><input type="text" value="<?=$alias_name?>" name="alias_name" size="32" style="height:26px;" /></td>
	</tr>
    <tr>
	  <td></td>
	  <td><input type="submit" value="<?=$button?> Game type" name="form_gt" /></td>
	</tr>
  </table>
 
</form>

<?php if (isset($saved) ) { ?>
 <h4>Form is successfully saved</h4>
<?php } ?>
<div style="margin-top:32px; margin-left:32px;  text-align:left; font-size:12px;">

<div>You can setup different game types (example: for DotA, LoD...).  </div>
<div>It will display separated stats for each game types.</div>
<div><b>NOTE: By adding or deleting game types you need to reset your statistics or may lead to side effects.</b></div>
<div><b>NOTE: This option should be set before you start bot.</b></div>
<div><b>NOTE: By default on top page will be shown alias with id 1 (alias_id = 1).</b></div>

</div>

</div>
 
 <div style="margin-top:150px;">&nbsp;</div>