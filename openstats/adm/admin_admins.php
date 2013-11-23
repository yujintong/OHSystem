<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
$errors = "";

if (( isset($_GET["del"]) OR isset($_GET["edit"]) OR isset($_GET["add"]) ) AND $_SESSION["level"]<=9 ) {
	  ?>
	  <div align="center">
	    <h2>Only root administrators can access this options</h2>
	  </div>
	  <?php 
}

//delete
  if ( isset( $_GET["del"]) AND is_numeric($_GET["del"]) AND $_SESSION["level"]>9) {
      $id = safeEscape( (int) $_GET["del"] );
	  //$sth = $db->prepare("DELETE FROM ".OSDB_ADMINS." WHERE id =? LIMIT 1 ");
	  $sth = $db->prepare("UPDATE ".OSDB_USERS." SET admin_realm = '', user_level = '0' WHERE user_id =? LIMIT 1 ");
	  $sth->bindValue(1, (int)$id, PDO::PARAM_INT);
	  $result = $sth->execute();
	  if ( isset($_GET["n"]) )
	  
	$result = $db->update(OSDB_STATS, array(
		   "admin" => 0
	                                    ), "LOWER(player) = ".safeEscape( trim( strtolower($_GET["n"])) )."");
	  ?>
	  <div align="center">
	  <h2>Admin successfully deleted. <a href="<?=$website?>adm/?admins">&laquo; Back</a></h2>
	  </div>
	  <?php 
	  OS_AddLog($_SESSION["username"], "[os_editadmin] Removed Admin ( #$id  )");
  }
//eDIT
  if ( (isset( $_GET["edit"]) AND is_numeric($_GET["edit"]) AND $_SESSION["level"]>9 ) OR isset($_GET["add"]) AND $_SESSION["level"]>9  ) {
   $name = ""; $server = ""; $level = "";
   if ( isset($_GET["edit"])  AND is_numeric($_GET["edit"])  ) $id = safeEscape( (int) $_GET["edit"] );
   //UPDATE
    if ( isset($_POST["edit_admin"]) ) {
	  $name     = safeEscape( $_POST["name"]);
	  $server   = safeEscape( $_POST["server"]);
	  $level   = safeEscape( $_POST["level"]);
	  
	  if ( strlen( $name)<=2 ) $errors.="<div>Field Name does not have enough characters</div>";
	  
	  
	  $time = date( "Y-m-d H:i:s", time() );
	  
	  if ( isset($_GET["edit"]) ) $sql = "UPDATE ".OSDB_USERS." SET 
	  user_name= '".$name."', admin_realm = '".$server."', user_level = '".$level."' WHERE user_id ='".$id."' LIMIT 1 ";
	  
	  /*
	  if ( isset($_GET["add"]) ) { $sql = "INSERT INTO ".OSDB_USERS."(user_name, admin_realm, user_level) VALUES('".$name."', '".$server."', '".$access."' )";
	  $update = $db->query("UPDATE ".OSDB_STATS." SET user_level = '".$level."' WHERE (player) = ('".$name."') LIMIT 1");
	  }
	  */
	  if ( empty($errors) ) {
	  $sth = $db->prepare($sql);
	  $result = $sth->execute();
	  
	  if ( $result ) {
	  	  ?>
	  <div align="center">
	    <h2>Admin successfully updated. <a href="<?=$website?>adm/?admins">&laquo; Back</a></h2>
	  </div>
	  <?php 
	  OS_AddLog($_SESSION["username"], "[os_editadmin] Edited Admin ( $name, Level: $level )");
	  }
	 } else {
	?>
	<div align="center"><?=$errors?></div>
	<?php
	}
	}
  
     if ( isset($_GET["edit"])  AND is_numeric($_GET["edit"])  ) {
	 $sth = $db->prepare("SELECT * FROM ".OSDB_USERS." WHERE user_id = :id ");
	 $sth->bindValue(':id', (int) $id, PDO::PARAM_INT); 	
	 $result = $sth->execute();
	 $row = $sth->fetch(PDO::FETCH_ASSOC);
	 $name       = ( $row["user_name"]);
	 $server     = ( $row["admin_realm"]);
	 $level = ( $row["user_level"]);
	 $button = "Edit Admin";
	 } else { $button = "Add Admin"; }
	
	 if ( isset($_GET["add"]) AND !empty($_GET["add"]) ) {
	    $name = trim( safeEscape($_GET["add"]) );
	 }
	 ?>
	 
	 <form action="" method="post">
	 <div align="center">
	 <h2><?=$button?></h2>
	 <table>
	   <tr class="row">
	     <td width="80" class="padLeft">Name:</td>
		 <td><input name="name" style="width: 380px; height: 28px;" type="text" value="<?=$name ?>" /></td>
	   </tr>
	   <tr class="row">
	     <td width="80"  class="padLeft">Server:</td>
		 <td>
		 <input name="server" id="realm" style="width: 180px; height: 28px;" type="text" value="<?=$server?>" />
		 <a class="menuButtons" href="javascript:;" onclick="SetDateField('europe.battle.net','realm')">europe</a>
		 <a class="menuButtons" href="javascript:;" onclick="SetDateField('useast.battle.net','realm')">useast</a>
		 <a class="menuButtons" href="javascript:;" onclick="SetDateField('uswest.battle.net','realm')">uswest</a>
		 <a class="menuButtons" href="javascript:;" onclick="SetDateField('asia.battle.net','realm')">asia</a>
		 </td>
	   </tr>
	   <tr class="row">
	     <td width="80"  class="padLeft">Access:</td>
		 <td>
		 <select name="level">
		 <?php if ($level<=0) $sel='selected="selected"'; else $sel = ""; ?>
		   <option <?=$sel?> value="0">Member</option>
		 <?php if ($level==1) $sel='selected="selected"'; else $sel = ""; ?>
		   <option <?=$sel?> value="1"><?=$lang["member_reserved"]?></option>
		 <?php if ($level==2) $sel='selected="selected"'; else $sel = ""; ?>
		   <option <?=$sel?> value="2"><?=$lang["member_safe"]?></option>
		 <?php if ($level==3) $sel='selected="selected"'; else $sel = ""; ?>
		   <option <?=$sel?> value="3"><?=$lang["member_safe_reserved"]?></option>
		   
		 <?php if ($level==4) $sel='selected="selected"'; else $sel = ""; ?>
		   <option <?=$sel?> value="4"><?=$lang["member_web_moderator"]?></option>
		   
		 <?php if ($level==5) $sel='selected="selected"'; else $sel = ""; ?>
		   <option <?=$sel?> value="5"><?=$lang["member_bot_moderator"]?></option>
		 <?php if ($level==6) $sel='selected="selected"'; else $sel = ""; ?>
		   <option <?=$sel?> value="6"><?=$lang["member_bot_full_mod"]?></option>
		 <?php if ($level==7) $sel='selected="selected"'; else $sel = ""; ?>
		   <option <?=$sel?> value="7"><?=$lang["member_global_mod"]?></option>
		 <?php if ($level==8) $sel='selected="selected"'; else $sel = ""; ?>
		   <option <?=$sel?> value="8"><?=$lang["member_bot_hoster"]?></option>
		 <?php if ($level==8) $sel='selected="selected"'; else $sel = ""; ?>
		 <?php
		 // only root admin can add admins 
		 if ( $_SESSION["level"] >9) $dis = "";   else $dis = "disabled";
		 ?>
		 <?php if ($level>=9 AND $level<10) $sel='selected="selected"'; else $sel = ""; ?>
		   <option <?=$sel.$dis?> value="9"><?=$lang["member_admin"]?></option>
		<?php if ($level>=10) $sel='selected="selected"'; else $sel = ""; ?>
		   <option <?=$sel?> value="10"><?=$lang["member_root"]?></option>
		 </select>
		 
		 </td>
	   </tr>
	   <tr>
	     <td width="80"></td>
		 <td class="padTop padBottom">
		 <input type="submit" value="Submit" name="edit_admin" class="menuButtons" />
		 <a class="menuButtons" href="<?=$website?>adm/?admins">&laquo; Back</a>
		 </td>
	   </tr>
	  </table>
	  </div>
	 </form>
	 <?php
  }
  $sql="";
  if ( isset($_GET["sort"]) AND is_numeric($_GET["sort"]) ) {
    $sort = (int) $_GET["sort"];
	$sql.=' AND user_level = '.$sort.'';
  }
  
  $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_USERS." WHERE user_level>=1 $sql");
  $result = $sth->execute();
  $r = $sth->fetch(PDO::FETCH_NUM);
  $numrows = $r[0];
  $result_per_page = 40;
?>
<div align="center">
<div class="padBottom padTop"><a class="menuButtons" href="<?=OS_HOME?>adm/?users&amp;add">[+] Add Admin</a></div>

<div>
<a class="menuButtons" href="<?=OS_HOME?>adm/?admins">Show All</a>
<a class="menuButtons" href="<?=OS_HOME?>adm/?admins&amp;sort=1">Reserved</a>
<a class="menuButtons" href="<?=OS_HOME?>adm/?admins&amp;sort=2">Safelist</a>
<a class="menuButtons" href="<?=OS_HOME?>adm/?admins&amp;sort=3">Safe+Res</a>
<a class="menuButtons" href="<?=OS_HOME?>adm/?admins&amp;sort=4">Website</a>
<a class="menuButtons" href="<?=OS_HOME?>adm/?admins&amp;sort=5">SimpleMod</a>
<a class="menuButtons" href="<?=OS_HOME?>adm/?admins&amp;sort=6">FullMod</a>
<a class="menuButtons" href="<?=OS_HOME?>adm/?admins&amp;sort=7">GlobalMod</a>
<a class="menuButtons" href="<?=OS_HOME?>adm/?admins&amp;sort=8">Hosters</a>
<a class="menuButtons" href="<?=OS_HOME?>adm/?admins&amp;sort=9">Admins</a>
<a class="menuButtons" href="<?=OS_HOME?>adm/?admins&amp;sort=10">root</a>
</div>
<?php
  
  $draw_pagination = 1;
  $SHOW_TOTALS = 1;
  include('pagination.php');
  
   $sth = $db->prepare("SELECT * FROM ".OSDB_USERS." 
   WHERE user_level>=1 $sql 
   ORDER BY user_level DESC, user_id DESC 
   LIMIT $offset, $rowsperpage");
   $result = $sth->execute();
   ?>
   <table>
    <tr>
	  <th width="150" class="padLeft">Admin</th>
	  <th width="100">Action</th>
	  <th width="180">Access/Level</th>
	  <th>Server</th>
	  <th>Email</th>
	</tr>
   <?php
   while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { ?>
   <tr class="row" style="height:30px;">
     <td width="150" class="padLeft font12"><a href="<?=OS_HOME?>adm/?users&amp;edit=<?=$row["user_id"]?>"><?=$row["user_name"]?></a></td>
	 <td width="100" class="font12">
	 <a href="<?=OS_HOME?>adm/?users&amp;edit=<?=$row["user_id"]?>"><img src="<?=OS_HOME?>adm/edit.png" alt="img" /></a>
	 <a href="javascript:;" onclick="if (confirm('Delete Admin?') ) { location.href='<?=OS_HOME?>adm/?users&amp;del=<?=$row["user_id"]?>&amp;n=<?=$row["user_name"]?>' }"><img src="<?=OS_HOME?>adm/del.png" alt="img" /></a>
	 </td>
	 <td width="180" class="overflow_hidden font12">
	 <?=OS_IsUserGameAdmin( $row["user_level"] )?>
	 <?=OS_ShowUserRole( $row["user_level"] )?> ( <?=$row["user_level"]?> )
	 </td>
	 <td class="overflow_hidden font12"><span title="<?=$row["admin_realm"]?>"><?=stripslashes($row["admin_realm"])?></span></td>
	 <td><?=$row["user_email"]?></td>
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