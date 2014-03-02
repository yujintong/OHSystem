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

?>
<div align="center" class="padBottom">
	 <form action="" method="get">
	 <table>
	   <tr>
	    <td width="290">
		  <input type="hidden" name="users" />
		  <input style="width: 180px; height: 24px;" type="text" name="search_users" value="<?=$s?>" />
		  <input class="menuButtons" type="submit" value="Search users" />
		</td>
	   </tr>
	 </table>
	 </form>
</div>
<?php
if ( isset($_GET["activate"]) AND is_numeric($_GET["activate"]) ) {
   $id = safeEscape( $_GET["activate"]);
   $update = $db->prepare("UPDATE ".OSDB_USERS." SET code = '' WHERE user_id = '".(int) $id."' LIMIT 1");
   $result = $update->execute();
   OS_AddLog($_SESSION["username"], "[os_users] Activated user ( #".(int)$id." )");
} 

//delete
  if ( isset( $_GET["del"]) AND is_numeric($_GET["del"]) AND OS_IsRoot() ) {
      $id = safeEscape( (int) $_GET["del"] );
	  $delete = $db->prepare("DELETE FROM ".OSDB_USERS." WHERE user_id ='".(int)$id."' LIMIT 1 ");
	  $result = $delete->execute();
	  $del2 = $db->query("DELETE FROM ".OSDB_COMMENTS." WHERE user_id ='".(int)$id."' ");
	  $result = $del2->execute();
	  OS_AddLog($_SESSION["username"], "[os_deleteuser] Deleted member ( #$id )");
	  ?>
	  <div align="center">
	  <h2>User successfully deleted. <a href="<?=OS_HOME?>adm/?users">&laquo; Back</a></h2>
	  </div>
	  <?php 
  }
//eDIT
  if ( (!OS_IsRoot() AND isset( $_GET["edit"]) AND is_numeric($_GET["edit"]) ) OR (isset($_GET["add"]) AND !OS_IsRoot() ) OR ( isset( $_GET["del"]) AND is_numeric($_GET["del"]) AND !OS_IsRoot() ) ) {
      ?>
	<div align="center" style="margin-bottom: 60px;">
	<h2>You don't have permission to access this page</h2>
	</div>
	<?php
  } else
  if ( (OS_IsRoot() AND isset( $_GET["edit"]) AND is_numeric($_GET["edit"]) ) OR isset($_GET["add"])  ) {
   $name = ""; $email = ""; $bnet = "";
   if ( isset($_GET["edit"])  AND is_numeric($_GET["edit"])  ) $id = safeEscape( (int) $_GET["edit"] ); else $id =0;
   //UPDATE
    if ( isset($_POST["edit_user"]) ) {
	  $name     = safeEscape( $_POST["name"]);
	  $bnet     = safeEscape( $_POST["bnet"]);
	  $bnet     = strtolower($bnet);
	  $email   = safeEscape( $_POST["email"]);
	  //if not root admin do not change access level
	  if ( $_SESSION["level"] <=9) {
	  $sth = $db->prepare("SELECT * FROM ".OSDB_USERS." WHERE user_id = '".$id."' ");
	  $result = $sth->execute();
	  $UserROW = $sth->fetch(PDO::FETCH_ASSOC);
	  $level = $UserROW["user_level"]; 
	  
	    if ( isset($_POST["level"]) AND $level > $_POST["level"] ) {
	    $e_message = 'Only root admins can change user roles'; 
		
	    }
	  }
	  else 
	  $level   = safeEscape( $_POST["level"]);
	  $avatar     = strip_tags( $_POST["avatar"]);
	  $www        = strip_tags( $_POST["www"]);
	  $gender     = safeEscape( $_POST["gender"]);
	  $user_realm = strip_tags( $_POST["user_realm"]);
	  $user_clan  = strip_tags( $_POST["user_clan"]);
	  $admin_realm  = strip_tags( $_POST["admin_realm"]);
	  $alias_id   = strip_tags($_POST["alias_id"]);
	  if (isset($_POST["blacklisted"])) $blacklisted = strip_tags( $_POST["blacklisted"]);
	  else $blacklisted = 0;
	  $sql_update_pw = "";
	  
	  $user_level_expire = strip_tags($_POST["user_level_expire"]);
	  
	  $user_bnet = safeEscape( $_POST["user_bnet"]);
	   
	  if ( isset( $_POST["chpw"]) AND $_POST["chpw"] == 1 AND !isset($_GET["add"]) ) {
	    $password = $_POST["password_"];
	    $password2 = $_POST["password_2"];
		
		if ( strlen($password)<=2 ) $errors.="<div>Field Password does not have enough characters</div>";
		if ($password!=$password2)  $errors.="<div>Password and confirmation password do not match</div>";
		
		if ( empty($errors) ) {
		  $hash = generate_hash(16,1);
		  $password_db = generate_password($password, $hash);
		  $sql_update_pw = ", user_password = '".$password_db."', password_hash = '".$hash."' ";
		}
		
	  }
	  
	  if ( isset($_GET["add"]) ) {
	    $password = $_POST["password_"];
	    $password2 = $_POST["password_2"];
		
		if ( strlen($password)<=2 ) $errors.="<div>Field Password does not have enough characters</div>";
		if ($password!=$password2)  $errors.="<div>Password and confirmation password do not match</div>";
		$hash = generate_hash(16,1);
		$password_db = generate_password($password, $hash);
		
	  }
	  
	  if ( isset($_POST["ppw"]) AND !empty($_POST["ppw"]) ) {
	    $sql_update_pw.=", user_ppwd = '".trim( safeEscape($_POST["ppw"]) )."'";
	  }
	  
	  if ( strlen( $name)<=2 ) $errors.="<div>Field Name does not have enough characters</div>";
	  if (!preg_match("/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i", $email)) 
	  $errors.="<div>E-mail address is not valid</div>";
	  
	  $time = date( "Y-m-d H:i:s", time() );
	  
	  if ( isset($_GET["edit"]) ) $sql = "UPDATE ".OSDB_USERS." SET 
	  user_name= '".$name."', alias_id = '".$alias_id."', user_email = '".$email."', user_level = '".$level."', user_website = '".$www."', user_avatar = '".$avatar."', user_gender = '".$gender."', bnet_username = '".$bnet."', user_bnet = '".$user_bnet."', user_realm = '".$user_realm."', user_clan = '".$user_clan."', admin_realm = '".$admin_realm."', blacklisted = '$blacklisted', user_level_expire = '".$user_level_expire."'
	  $sql_update_pw 
	  WHERE user_id ='".$id."' LIMIT 1 ";
	  
	  if ( isset($_GET["add"]) ) $sql = "INSERT INTO ".OSDB_USERS."(user_name, alias_id, user_email, user_password, password_hash, user_joined, bnet_username, user_bnet, user_realm, user_clan, blacklisted, user_level_expire) VALUES('".$name."', '".$alias_id."', '".$email."', '".$password_db."', '".$hash."', '".time()."', '".$bnet."', '".$user_bnet."', '".$user_realm."', '".$user_clan."', '".$blacklisted."', '".$user_level_expire."')";
	  
	  $sth = $db->prepare("SELECT * FROM ".OSDB_USERS." WHERE (user_name) = ('".$name."') AND user_id!='".$id."' ");
	  $result = $sth->execute();
	  if ( $sth->rowCount() >=1 )  $errors.="<div>Username already taken</div>";
	  
	  $sth = $db->prepare("SELECT * FROM ".OSDB_USERS." 
	  WHERE (user_email) = ('".$email."') AND user_id!='".$id."' AND user_fbid!='' ");
	  $result = $sth->execute();
	  if ( $sth->rowCount() >=1 AND !isset($_GET["edit"]) )  $errors.="<div>E-mail already taken</div>";
	  
	  $sth = $db->prepare("SELECT * FROM ".OSDB_USERS." 
	  WHERE (bnet_username) = ('".$bnet."') ");
	  $result = $sth->execute();
	  if ( $sth->rowCount() >=1 AND !isset($_GET["edit"]) )  $errors.="<div>Battle.net account already exists</div>";
	  
	  
	  if ( empty($errors) ) {
	  $sth = $db->prepare($sql);
	  $result = $sth->execute();
	  if ( isset($_GET["edit"]) ) OS_AddLog($_SESSION["username"], "[os_edituser] Edit member ( $name )");
	  else OS_AddLog($_SESSION["username"], "[os_edituser] ADD member ( $name )");
	  
	  if ( !empty($bnet) ) {
	    $sth = $db->prepare("UPDATE ".OSDB_STATS." SET user_level = '".$level."' WHERE player = '".$bnet."' ");
		$result = $sth->execute();
	  }
	  
	  $r=1;
	  if ( $r ) {
	  	  ?>
	  <div align="center">
	    <h2>User successfully updated. 
		   <a href="<?=OS_HOME?>adm/?users&amp;edit=<?=$_GET["edit"]?>">Refresh</a> | 
		   <a href="<?=OS_HOME?>adm/?users">&laquo; Back</a>  
		</h2>
		<?php if ( isset($e_message) AND !empty($e_message) ) echo $e_message; ?>
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
	 
	 if ( isset($_GET["remove_protection"]) AND $_SERVER["REQUEST_METHOD"] != "POST" ) {
	    $sth = $db->prepare("UPDATE ".OSDB_USERS." SET user_ppwd = '' WHERE user_id = '".$id."' ");
		$result = $sth->execute();
	 }
	 
	 $sth = $db->prepare("SELECT * FROM ".OSDB_USERS." WHERE user_id = '".$id."' ");
	 $result = $sth->execute();
	 $row = $sth->fetch(PDO::FETCH_ASSOC);
	 $name       = ( $row["user_name"]);
	 $alias_id   = $row["alias_id"];
	 $email      = ( $row["user_email"]);
	 $level      = ( $row["user_level"]);
	 $avatar     = ( $row["user_avatar"]);
	 $www        = ( $row["user_website"]);
	 $gender     = ( $row["user_gender"]);
	 $bnet     = ( $row["bnet_username"]);
	 $blacklisted      = ( $row["blacklisted"]);
	 $user_bnet = $row["user_bnet"];
	 $user_realm = $row["user_realm"];
	 $user_clan = $row["user_clan"];
	 $user_pw =  $row["user_ppwd"];
	 $admin_realm = $row["admin_realm"];
	 $user_level_expire = $row["user_level_expire"];

	 $button = "Edit User";
	 } else { $button = "Add User"; $level = ""; $avatar  = ""; $www  = ""; $gender = ""; $bnet =""; 
	 $user_bnet = ""; $user_realm = ""; $user_clan = ""; $user_pw = ""; $admin_realm = ""; 
	 $user_level_expire = ""; $blacklisted = "";
	 }
	 ?>
	 
	 <form action="" method="post">
	 <div align="center">
	 <h2><?=$name?>
	 <?=OS_bnet_icon($user_bnet, 16, 16, "imgvalign" ) ?>  
	 <?=OS_protected_icon( $user_pw, $user_bnet, $lang["protected_account"], 16, 16, "imgvalign" ) ?>
	 
	 <?php if (isset($_GET["add"]) AND strlen($_GET["add"])>=2 AND !is_numeric($_GET["add"]) ) { $name = trim($_GET["add"]); } ?>
	 </h2>
	 <table>
	   <tr class="row">
	     <td width="80" class="padLeft">Name:</td>
		 <td><input name="name" style="width: 380px; height: 28px;" type="text" value="<?=$name ?>" /></td>
	   </tr>
	   
	   <tr class="row">
	     <td width="80" class="padLeft">Game Type:</td>
		 <td>
		 <select name="alias_id">
		   <option value="0">All Games</option>
		   <?php
		   foreach($GameAliases as $gt) {
		   if ( isset($_GET["edit"]) AND $alias_id == $gt["alias_id"]) 
		   $s='selected="selected"'; else $s="";
		   ?>
		   <option <?=$s?> value="<?=$gt["alias_id"]?>"><?=$gt["alias_name"]?></option>
		   <?php
		   }
		   ?>
		 </select>
		 </td>
	   </tr>
	   
	   
	   <tr class="row">
	     <td width="80" class="padLeft">Battle.net account:</td>
		 <td><input name="bnet" style="width: 380px; height: 28px;" type="text" value="<?=$bnet ?>" /></td>
	   </tr>
	   <tr class="row">
	     <td width="80" class="padLeft">Battle.net confirmed:</td>
		 <td>
		 <select name="user_bnet">
		 <?php if ($user_bnet == 0) $sel = 'selected="selected"'; else $sel = ""; ?>
		   <option <?=$sel?> value="0">Not confirmed</option>
		 <?php if ($user_bnet == 1) $sel = 'selected="selected"'; else $sel = ""; ?> 
		   <option <?=$sel?> value="1">Confirmed</option>
		 <?php if ($user_bnet == 2) $sel = 'selected="selected"'; else $sel = ""; ?> 
		   <option <?=$sel?> value="2">Confirmed (and Email)</option>
		 </select>
     	 <?=OS_bnet_icon($user_bnet, 16, 16, "imgvalign" ) ?>  
		 <?=OS_protected_icon( $user_pw, $user_bnet, $lang["protected_account"], 16, 16, "imgvalign" ) ?>
		 <?php
		 if (!empty($user_pw) ) {
		 ?><a class="menuButtons" onclick="if(confirm('Remove user password protection?')) {location.href='<?=OS_HOME?>adm/?users&edit=<?=$_GET["edit"]?>&remove_protection' } " href="javascript:;">Remove protection password</a><?php
		 } else if ( !empty($name) ) {
         ?>
		 <a class="menuButtons" href="javascript:;" onclick="showhide('ppw')">Set password protection</a>
		 <span id="ppw" style="display:none;">
		  Password protection: <input type="text" value="" name="ppw" size="10" />
		 </span>
		 <?php
         }		 
		 ?>
		 </td>
	   </tr>
	   
	   <tr class="row">
	     <td width="80" class="padLeft">Admin realm:</td>
		 <td><input name="admin_realm" style="width: 380px; height: 28px;" type="text" value="<?=$admin_realm ?>" /> (For admins and moderators)</td>
	   </tr>
	   
	   <tr class="row">
	     <td width="80"  class="padLeft">E-mail:</td>
		 <td><input name="email" style="width: 380px; height: 28px;" type="text" value="<?=$email?>" /></td>
	   </tr>
	   <?php if ( !isset($_GET["add"]) ) { ?>
	   <tr class="row">
	     <td width="80"  class="padLeft">Password:</td>
		 <td>
		 <input type="checkbox" name="chpw" value="1" onclick="showhide('cw')" /> Change password?
		 <div id="cw" style="display: none;">
		   <div><input type="password" value="" name="password_" /></div>
		   <div>Confirm password:</div>
		   <div><input type="password" value="" name="password_2" /></div>
		 </div>
		 </td>
	   </tr>
	   <?php } else { ?>
	   <tr class="row">
	     <td width="80"  class="padLeft">Password:</td>
		 <td>
		   <div><input type="password" value="" name="password_" /></div>
		   <div>Confirm password:</div>
		   <div><input type="password" value="" name="password_2" /></div>
		 </td>
	   </tr>
	   <?php } ?>
	   <tr class="row">
	     <td width="80"  class="padLeft">Avatar:</td>
		 <td>
		 <input name="avatar" style="width: 380px; height: 28px;" type="text" value="<?=$avatar?>" />
		 <?php
		 if ( !empty($avatar) ) {
		 ?>
		 <a href="javascript:;" onclick="showhide('avatar')">Show avatar</a>
		 <div id="avatar" style="display:none">
		   <a href="<?=$avatar?>" target="_blank"><img src="<?=$avatar?>" width="320" alt="avatar" /></a>
		 </div>
		 <?php
		 }
		 ?>
		 </td>
	   </tr>
	   <tr class="row">
	     <td width="80"  class="padLeft">Website:</td>
		 <td><input name="www" style="width: 380px; height: 28px;" type="text" value="<?=$www?>" /></td>
	   </tr>
	   <tr class="row">
	     <td width="80"  class="padLeft">Realm:</td>
		 <td><input name="user_realm" style="width: 380px; height: 28px;" type="text" value="<?=$user_realm?>" /></td>
	   </tr>
	   <tr class="row">
	     <td width="80"  class="padLeft">Clan:</td>
		 <td><input name="user_clan" style="width: 380px; height: 28px;" type="text" value="<?=$user_clan?>" /></td>
	   </tr>
	   <tr class="row">
	     <td width="80"  class="padLeft">Gender:</td>
		 <td>
		 <select name="gender">
		   <option value="0" ></option>
		   <?php if ($gender == 1) $sel ='selected="selected"'; else $sel = ''; ?>
		   <option <?=$sel?> value="1" >Male</option>
		   <?php if ($gender == 2) $sel ='selected="selected"'; else $sel = ''; ?>
		   <option <?=$sel?> value="2" >Female</option>
		 </select>
		 </td>
	   </tr>
	   
	   <tr class="row">
	     <td width="80"  class="padLeft">Role:</td>
		 <td>
		 <div class="padTop"></div>
		 <?php
		 // only root admin can add admins 
		 if ( $_SESSION["level"] >9) $dis = "";   else $dis = "disabled";
		 ?>
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
		   <option <?=$sel.$dis?> value="4"><?=$lang["member_web_moderator"]?></option>
		   
		 <?php if ($level==5) $sel='selected="selected"'; else $sel = ""; ?>
		   <option <?=$sel.$dis?> value="5"><?=$lang["member_bot_moderator"]?></option>
		 <?php if ($level==6) $sel='selected="selected"'; else $sel = ""; ?>
		   <option <?=$sel.$dis?> value="6"><?=$lang["member_bot_full_mod"]?></option>
		 <?php if ($level==7) $sel='selected="selected"'; else $sel = ""; ?>
		   <option <?=$sel.$dis?> value="7"><?=$lang["member_global_mod"]?></option>
		 <?php if ($level==8) $sel='selected="selected"'; else $sel = ""; ?>
		   <option <?=$sel.$dis?> value="8"><?=$lang["member_bot_hoster"]?></option>
		 <?php if ($level==8) $sel='selected="selected"'; else $sel = ""; ?>

		 <?php if ($level>=9 AND $level<10) $sel='selected="selected"'; else $sel = ""; ?>
		   <option <?=$sel.$dis?> value="9"><?=$lang["member_admin"]?></option>
		<?php if ($level>=10) $sel='selected="selected"'; else $sel = ""; ?>
		   <option <?=$sel.$dis?> value="10"><?=$lang["member_root"]?></option>
		 </select>
		 
		 Expire: <input name="user_level_expire" id="expiredate" style="width: 180px; height: 28px;" type="text" value="<?=$user_level_expire?>" />
		 <?php if (!empty($user_level_expire)) echo date( OS_DATE_FORMAT, strtotime($user_level_expire)); ?> ( YYYY-MM-DD H:i:s )
<div class="padTop padBottom">
	 Set: 
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600 )?>', 'expiredate')" >+1h</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*10 )?>', 'expiredate')" >+10h</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24 )?>', 'expiredate')" >+1 day</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*72 )?>', 'expiredate')" >+3 days</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*7 )?>', 'expiredate')" >+7 days</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*30 )?>', 'expiredate')" >+1 month</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*90 )?>', 'expiredate')" >+3 months</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*180 )?>', 'expiredate')" >+6 months</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*30*12 )?>', 'expiredate')" >+1 year</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*30*24 )?>', 'expiredate')" >+2 year</a>
    		 <div>YYYY-MM-DD H:i:s (<b>Leave blank</b> if you do not want role expire)</div>
	</div>
		 
		 <div class="padBottom"></div>
		 <?php if($blacklisted == 1) $s='checked="checked"'; else $s=""; ?>
		 <input <?=$s?> type="checkbox" value="1" name="blacklisted" /> <span style="color:red;">Blacklisted user</span>
		 </td>
	   </tr>
	   <?php if (!isset($_GET["add"])) { ?>
	   <tr>
	      <td width="80">Last login:</td>
		  <td><?=date($DateFormat, $row["user_last_login"])?></td>
	   </tr>
	   <?php } ?>
	   <tr>
	     <td width="80"></td>
		 <td class="padTop padBottom">
		 <input type="submit" value="Submit" name="edit_user" class="menuButtons" />
		 <a class="menuButtons" href="<?=OS_HOME?>adm/?admins">&laquo; Back</a>
		 </td>
	   </tr>
	  </table>
	  </div>
	 </form>
	 <?php
  }
  
  if ( isset($_GET["search_users"]) AND strlen($_GET["search_users"])>=2 ) {
     $search_users = safeEscape( trim($_GET["search_users"]));
	 if ( strstr($search_users, "@") )  $sql = " AND (user_email) LIKE ('%".$search_users."%') ";
	 else
	 $sql = " AND ( (user_name) LIKE ('%".$search_users."%') OR (bnet_username) LIKE ('%".$search_users."%') ) ";
  } else {
   $sql = "";
   $search_users= "";
  }
  
  $inactive_time = time()-3600*24*30; //1 MONTH
  
  if ( isset($_GET["sort"])  AND $_GET["sort"] == "inactive_users")  $sql.=" AND user_last_login <= '".$inactive_time."' ";
  if ( isset($_GET["sort"])  AND $_GET["sort"] == "inactive_admins") $sql.=" AND user_last_login <= '".$inactive_time."' AND user_level>=5";
  
  if ( isset($_GET["sort"])  AND $_GET["sort"] == "blacklisted") $sql.=" AND blacklisted >=1";

  $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_USERS." WHERE user_id>=1 $sql ");
  $result = $sth->execute();
  $r = $sth->fetch(PDO::FETCH_NUM);
  $numrows = $r[0];
  $result_per_page = 30;
  
  $ord = 'user_id DESC';
  $sel = array(); $sel[0]='';$sel[1]='';$sel[2]='';$sel[3]='';$sel[4]='';$sel[5]=''; $sel[6]=''; $sel[7]='';
  if ( isset($_GET["sort"]) AND $_GET["sort"] == "id" )     { $ord = 'user_id DESC'; $sel[0]='selected="selected"'; }else
  if ( isset($_GET["sort"]) AND $_GET["sort"] == "name" )   { $ord = '(user_name) ASC'; $sel[1]='selected="selected"'; } else
  if ( isset($_GET["sort"]) AND $_GET["sort"] == "email" )  { $ord = '(user_email) ASC'; $sel[2]='selected="selected"'; } else
  if ( isset($_GET["sort"]) AND $_GET["sort"] == "joined" ) { $ord = 'user_joined DESC'; $sel[3]='selected="selected"'; } else
  if ( isset($_GET["sort"]) AND $_GET["sort"] == "login" )  { $ord = 'user_last_login DESC'; $sel[4]='selected="selected"'; } else
  if ( isset($_GET["sort"]) AND $_GET["sort"] == "bnet" )  { $ord = 'user_bnet DESC, user_id DESC'; $sel[5]='selected="selected"'; }
  if ( isset($_GET["sort"]) AND $_GET["sort"] == "role" )  { $ord = 'user_level DESC, user_id DESC'; $sel[6]='selected="selected"'; }
  if ( isset($_GET["sort"]) AND $_GET["sort"] == "inactive" )  { $ord = 'code DESC, user_id DESC'; $sel[7]='selected="selected"'; }
?>
<div align="center">

<?php
//SEND EMAIL
if ( isset($_GET["email"]) AND is_numeric($_GET["email"]) ) {
   $userID = (int)$_GET["email"];
   $sth = $db->prepare("SELECT * FROM ".OSDB_USERS." WHERE user_id = $userID LIMIT 1");
   $result = $sth->execute();
   $row = $sth->fetch(PDO::FETCH_ASSOC);
   $email = $row["user_email"];
   $errors = "";
   if ( isset($_POST["sendEmail"]) AND isset($_POST["userID"]) AND isset($_POST["text"]) ) {
   
    $text = trim($_POST["text"]);
    $text = str_replace("\n","<br />",$text);
     
	 if ( $_POST["userID"] != $_GET["email"] ) $errors.="<div>Wrong userID!</div>";
	 if ( strlen($text)<=3 ) $errors.="<div>Message does not contain enough characters</div>";
	 
	 if ( empty($errors) ) {
	    require("../inc/class.phpmailer.php");
		
		$from = $lang["email_from"]; $fromName = $lang["email_from_full"];
		
		if ( isset($_POST["from"]) AND $_POST["from"] == 0) {
		$from = $_SESSION["email"]; $fromName = $_SESSION["username"];
		}
		
	      $message = "You have just received a message from ".$_SESSION["username"]."<br />";
		  //$message.= "<br />~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br />";
		  $message.= ($text);
		  $message.= "<br />___________________<br />".OS_HOME."";
	      $mail  = new PHPMailer();
	      $mail->CharSet = 'UTF-8';
	      $mail->SetFrom($from, $fromName);
	      $mail->AddReplyTo($lang["email_from"], $lang["email_from_full"]);
	      $mail->AddAddress($email, "");
	      $mail->Subject = "[Admin] Message from the site administrator";
	      $mail->MsgHTML($message);
	      $mail->Send();
		  
		  $MailSend = 1;
	 }
   }
   
   if ( !empty($row["user_name"]) ) {
    ?>
	<div class="padBottom padTop" align="left" style="margin-left: 64px; border: 4px solid #ccc; padding: 10px;">
	 <h2>Send Email</h2> <?=$errors?>
	  <?php if (isset($MailSend) ) { ?><h2>Message was successfully sent</h2><?php } else { ?>
	  <form action="" method="post">
	  <div>Send to: <input size="35" type="text" name="name" disabled value="<?=$row["user_name"]?>" /> <?=$email?></div>
	  <div>From: 
	  <label for="sys">
	  <input id="sys" type="radio" checked="checked" name="from" value="1" /> 
	  <?=$lang["email_from"]?> (<b>System</b>)  | 
	  </label>
	  
	  <label for="my">
	  <input id="my" type="radio" name="from" value="0" /> <?=$_SESSION["email"]?> 
	  (<b>My Email</b>)
	  </label>
		  
	  </div>
	  <input type="hidden" value="<?=$userID?>" name="userID" />
	  <div><b>Message:</b></div>
	  <textarea name="text" style="width:500px; height:120px;" ></textarea>
	  
	  <div class="padTop">
	  <input type="submit" value="Send Email" class="menuButtons" name="sendEmail" />
	  <a href="<?=OS_HOME?>adm/?users">&laquo; cancel</a>
	  </div>
	  </form>
	  <?php } ?>
	</div>
	<?php
   } else echo "<h2>Wrong username</h2>";
}
?>

<div class="padBottom padTop">
        <form action="" method="get">
		<a class="menuButtons" href="<?=OS_HOME?>adm/?users&amp;add">[+] Add User</a>
		
		 <a class="menuButtons" href="<?=OS_HOME?>adm/?users&amp;sort=blacklisted">Blacklisted Users</a>
		 <a class="menuButtons" href="<?=OS_HOME?>adm/?users&amp;sort=inactive_users">Inactive Users</a>
		 <a class="menuButtons" href="<?=OS_HOME?>adm/?users&amp;sort=inactive_admins">Inactive Admins</a>
		
		<input type="hidden" name="users" />
		 Sort by: <select name="sort">
		  <option <?= $sel[0]?> value="id">ID</option>
		  <option <?= $sel[1]?> value="name">Name</option>
		  <option <?= $sel[2]?> value="email">Email</option>
		  <option <?= $sel[3]?> value="joined">Joined</option>
		  <option <?= $sel[4]?> value="login">Last Login</option>
		  <option <?= $sel[5]?> value="bnet">Bnet Account</option>
		  <option <?= $sel[6]?> value="role">User Role</option>
		  <option <?= $sel[7]?> value="inactive">Inactive</option>
		 </select>
		 <input type="submit" value="Submit" class="menuButtons" />
		 </form>
		 
</div>
<?php
  
  $draw_pagination = 1;
  $SHOW_TOTALS = 1;
  include('pagination.php');
  
   $sth = $db->prepare("SELECT * FROM ".OSDB_USERS." WHERE user_id>=1 $sql 
   ORDER BY $ord LIMIT $offset, $rowsperpage");
   $result = $sth->execute();
   ?>
   <table>
    <tr>
	  <th width="40">Avatar</th>
	  <th width="220" class="padLeft">Username</th>
	  <th width="48">Action</th>
	  <th width="72" class="padLeft">Role</th>
	  <th width="48">Confirmed</th>
	  <th width="150" class="padLeft">Email <?php if ( isset($_GET["sort"]) AND $_GET["sort"] == "login" ) { ?>/ Last login <?php } ?></th>
	  <th width="150" class="padLeft">IP</th>
	  <th width="120">Joined</th>
	</tr>
   <?php
	 if ( file_exists("../inc/geoip/geoip.inc") ) {
	 include("../inc/geoip/geoip.inc");
	 $GeoIPDatabase = geoip_open("../inc/geoip/GeoIP.dat", GEOIP_STANDARD);
	 $GeoIP = 1;
	 }
    
	if ( isset($_GET["page"]) AND is_numeric($_GET["page"]) ) $p = '&amp;page='.(int) $_GET["page"]; else $p = '';
	
   while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
   
	if ($GeoIP == 1 ) {
	$Letter   = geoip_country_code_by_addr($GeoIPDatabase, $row["user_ip"]);
	$Country  = geoip_country_name_by_addr($GeoIPDatabase, $row["user_ip"]);
	}
	
	if ( isset( $_GET["edit"] ) AND $row["user_id"] == $_GET["edit"] ) $border = 'style="border: 2px solid #BE0000;"'; else $border = "";
	
	$role = "Member";
	
	if ($row["user_level"]==0) $role = "Member";
	if ($row["user_level"]==1) $role = "Safelisted";
	if ($row["user_level"]==2) $role = "Bot Moderator";
	if ($row["user_level"]==3) $role = "Website Moderator";
	if ($row["user_level"]==5) $role = "Global Moderator";
	
	if ($row["user_level"]==9) $role = "Admin";
	if ($row["user_level"]>=9) $role = '<span style="color: rgb(199, 31, 31);">root</span>';
	
	$role = OS_ShowUserRole($row["user_level"] );
	if ($row["user_level"]>=9) $role = '<span style="color: rgb(199, 31, 31);">'.$role.'</span>';
   ?>
   
   <tr class="row" style="height:36px;">
     <td width="40" class="padLeft font12" <?=$border?>>
	 <?php if (!empty($row["user_fbid"]) ) { ?>
	 <a href="http://www.facebook.com/profile.php?id=<?=$row["user_fbid"]?>" target="_blank">
	 <img class="imgvalign" src="https://graph.facebook.com/<?=$row["user_fbid"]?>/picture" alt="" width="32" height="32" />
	 </a>
	 <?php } else { 
	 if (!empty($row["user_avatar"]) AND is_valid_url( $row["user_avatar"] )  ) {
	 ?>
	 <img class="imgvalign" src="<?=$row["user_avatar"]?>" alt="" width="32" height="32" />
	 <?php } else { ?>
	 <img class="imgvalign" src="<?=OS_HOME?>img/avatar_64.png" alt="" width="32" height="32" />
	 <?php } 
	 } ?>
	 </td>
     <td width="220" class="padLeft font12" <?=$border?>>
	 <a href="<?=OS_HOME?>adm/?users&amp;edit=<?=$row["user_id"].$p?>"><b><?=$row["user_name"]?></b></a>
	 
	 <a href="<?=OS_HOME?>adm/?users&amp;email=<?=$row["user_id"]?>" title="Send Email"><img width="16" height="16" style="float:right;" src="<?=OS_HOME?>adm/email.gif" alt="email" class="imgvalign" /></a>
	 <div>
	   <?php if (!empty($row["bnet_username"]) AND $row["user_bnet"]>=1 ) { ?>
	   <b><?=$row["bnet_username"]?></b> 
	   <?php } ?>
	  <div class="floatR">
	   <?=OS_IsMemberActive( $row["code"], "Waiting for activation", 1, "imgvalign", 16, 16 )?>
	   <?=OS_bnet_icon($row["user_bnet"], 16, 16, "imgvalign" ) ?>  
	   <?=OS_protected_icon( $row["user_ppwd"], $row["user_bnet"], $lang["protected_account"], 16, 16, "imgvalign" ) ?>
	   </div>
	   
	 
	 <?php if($row["blacklisted"] == 1) { ?>
	 <span style="color:red">blacklisted</span>
	 <?php } ?>
	 </div>
	 </td>
	 <td width="48" class="font12">
	 <a href="<?=OS_HOME?>adm/?users&amp;edit=<?=$row["user_id"].$p?>"><img src="<?=OS_HOME?>adm/edit.png" alt="img" /></a>
	 <a href="javascript:;" onclick="if (confirm('Delete User?') ) { location.href='<?=OS_HOME?>adm/?users&amp;del=<?=$row["user_id"]?>' }"><img src="<?=OS_HOME?>adm/del.png" alt="img" /></a>
	 </td>
	 <td width="72" class="font12">
	 <?=$role?>
	 </td>
	 <td width="48" class="font12">
	   <?php if (!empty($row["code"]) ) { ?><a href="<?=OS_HOME?>adm/?users&amp;activate=<?=$row["user_id"]?>">Activate</a><?php } else { ?>Y<?php } ?>
	 </td>
	 <td width="166" class="overflow_hidden font12"><a href="<?=OS_HOME?>adm/?users&amp;email=<?=$row["user_id"]?>" title="Send Email"><span title="<?=$row["user_email"]?>"><?=stripslashes($row["user_email"])?></span></a>
	 <?php if ( isset($_GET["sort"]) AND $_GET["sort"] == "login" OR (isset($_GET["sort"]) AND $_GET["sort"] == "inactive_admins") ) { 
	 ?>
	 <div style="font-size:11px;">
	 <?php 	 if ( date("Y", $row["user_last_login"])>=1990 ) { ?>
	 <div><b>Last login:</b></div>
	 <i><?=date($DateFormat, $row["user_last_login"])?></i>
	 <?php } else { ?><div><b>Last login:</b> <i>never</i></div><?php } ?>
	 </div>
	 <?php
	 
	 } ?>
	 </td>
	 <td width="150" class="overflow_hidden font12">
	 <span style="padding-right: 4px;"> <?php if ($GeoIP == 1 AND !empty($Letter) ) { ?><img src="<?=OS_HOME?>img/flags/<?=$Letter?>.gif" class="imgvalign" title="<?=$Country?>" alt="" /><?php } ?></span>
	 <?php
	  $ip_part = OS_GetIpRange( $row["user_ip"] );
	  if (!empty($ip_part)) {
	 ?>
	 <a title="Show IP Range" href="<?=OS_HOME?>adm/?bans&amp;ip_range=<?=$ip_part?>&amp;show=all"><?=$row["user_ip"]?></a>
	 <?php } else echo $row["user_ip"]; ?>
	
	 </td>
	 <td width="120" class="overflow_hidden font12"><?=date( $DateFormat, ($row["user_joined"]) )?></td>
    </tr>
   <?php 
   }
   if ( isset($GeoIP) AND $GeoIP == 1) geoip_close($GeoIPDatabase);
?>
  </table>
<?php
include('pagination.php');
?>
  </div>
  
  <div style="margin-top: 180px;">&nbsp;</div>
