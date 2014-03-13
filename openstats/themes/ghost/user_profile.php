<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

if ( isset($ProfileData) AND !empty($ProfileData)  ) { 
?>

<div id="content" class="s-c-x">
<div class="wrapper">   
    <div id="main-column">
     <div class="padding">
      <div class="inner">
   <h2><?=$_SESSION["username"]?></h2>
 
<script type="text/javascript">
 setTimeout('RemoveDiv2("message")', 4000);
</script> 

<form action="" method="post" enctype="multipart/form-data">
<table class="Table500px">

    <tr>
	  <td class="padLeft" width="130"><?=$lang["username"]?>:</td>
	  <td>
	  <input disabled class="field" type="text" value="<?=trim($_SESSION["username"])?>" />
<?php if ($ProfileData[0]["user_fbid"] >=1) {  ?>
      <a target="_blank" href="http://www.facebook.com/profile.php?id=<?=$ProfileData[0]["user_fbid"]?>"><img src="<?=OS_HOME?>img/facebook_icon.png" alt="" class="imgvalign" /></a>
<?php } ?>
      <?=OS_bnet_icon( $ProfileData[0]["user_bnet"], 32, 32, "imgvalign" )?>
	  <?=OS_protected_icon( $ProfileData[0]["user_ppwd"], $ProfileData[0]["user_bnet"], $lang["protected_account"], 32, 32, "imgvalign" ) ?>
	  </td>
	</tr>
<?=OS_RealmUsername( $ProfileData[0]["bnet_username"] )?>
<?php if ($ProfileData[0]["user_level_expire"]!='0000-00-00 00:00:00') { ?>
<tr>
  <td class="padLeft" width="130"><?=$lang["expire"]?>:</td>
  <td>
      <div>
	  <?=OS_IsUserGameAdmin( $ProfileData[0]["user_level"] )?>
	  <?=OS_ShowUserRole( $ProfileData[0]["user_level"] )?>
	  </div>
      <div><?=OS_ExpireDateRemain( $ProfileData[0]["user_level_expire"]) ?></div>
      <div><?=$ProfileData[0]["role_expire_date"]?></div>
  </td>
</tr>  
<?php } ?>
<?php $ProfileData[0]["hide_stats"] = 1; if ($ProfileData[0]["hide_stats"]) { ?>	
    <tr>
	  <td class="padLeft" width="130"><?=$lang["stats"]?>:</td>
	  <td class="imgvalign" valign="middle">
	    <select name="hide">
		  <option <?=$ProfileData[0]["sel_hide"]?> value="1"><?=$lang["hide_stats"]?></option>
		  <option <?=$ProfileData[0]["sel_show"]?> value="0"><?=$lang["show_stats"]?></option>
		</select>
		<div><?=$lang["hide_stats_info"] ?></div>
	  </td>
	</tr>
<?php } ?>
    <tr>
	  <td class="padLeft" width="130"><?=$lang["email"]?>:</td>
	  <td><input disabled class="field" type="text" value="<?=trim($ProfileData[0]["user_email"])?>" /></td>
	</tr>
	
    <tr>
	  <td class="padLeft" width="130"><?=$lang["password"]?>:</td>
	  <td><input name="pw_1" class="field" type="password" value="" /></td>
	</tr>

    <tr>
	  <td class="padLeft" width="130"><?=$lang["confirm_password"]?>:</td>
	  <td><input name="pw_2" class="field" type="password" value="" /></td>
	</tr>	
	
    <tr>
	  <td class="padLeft" width="130"></td>
	  <td align="left">
	  <input style="text-align:left;" name="pw_confirm"  type="checkbox" value="1" /> <?=$lang["change_password"]?>?  
	  </td>
	</tr>
	
    <tr>
	  <td class="padLeft" width="130"><?=$lang["avatar"]?>:</td>
	  <td>
	  <?=ShowUserAvatar(trim($ProfileData[0]["user_avatar"]), 64, 64, "", $lang["remove_avatar"]);?>
	  <?=UploadAvatar($AllowUploadAvatar, $ProfileData[0]["user_avatar"] ) ?>
	  </td>
	</tr>
	
    <tr>
	  <td class="padLeft" width="130"><a name="protect_account"></a><?=$lang["protect_account"]?>:</td>
	  <td>
	  <?=OS_ProtectAccountFileds($ProfileData[0]["user_ppwd"], $ProfileData[0]["user_bnet"], $ProfileData[0]["ppwd_checked"], $ProfileData[0]["ppwd_show_info"] )?>
	  </td>
	</tr>
	
	
<?php
if ($ProfileData[0]["user_level"]>=2) {
?>
    <tr>
	  <td class="padLeft" width="130">Change realm:</td>
	  <td>
	  <select name="admin_realm">
	  <?php if ($ProfileData[0]["admin_realm"] == "OHConnect") $s='selected="selected"'; else $s=''; ?>
	   <option <?=$s?> value="OHConnect">OHConnect</option>
	  <?php if ($ProfileData[0]["admin_realm"] == "europe.battle.net") $s='selected="selected"'; else $s=''; ?>
	   <option <?=$s?> value="europe.battle.net">europe.battle.net</option>
	  <?php if ($ProfileData[0]["admin_realm"] == "useast.battle.net") $s='selected="selected"'; else $s=''; ?>
	   <option <?=$s?> value="useast.battle.net">useast.battle.net</option>
	  <?php if ($ProfileData[0]["admin_realm"] == "uswest.battle.net") $s='selected="selected"'; else $s=''; ?>
	   <option <?=$s?> value="uswest.battle.net">uswest.battle.net</option>
	  <?php if ($ProfileData[0]["admin_realm"] == "server.eurobattle.net") $s='selected="selected"'; else $s=''; ?>
	   <option <?=$s?> value="server.eurobattle.net">server.eurobattle.net</option>
	  </select> Only for +premium members
	  </td>
	</tr>
<?php
}
?>
	
    <tr>
	  <td class="padLeft" width="130"><?=$lang["member_clan"] ?>:</td>
	  <td><input name="clan" class="field" type="text" value="<?=trim($ProfileData[0]["user_clan"])?>" /></td>
	</tr>
	
    <tr>
	  <td class="padLeft" width="130"><?=$lang["location"] ?>:</td>
	  <td><input name="location" class="field" type="text" value="<?=trim($ProfileData[0]["user_location"])?>" /></td>
	</tr>
	
    <tr>
	  <td class="padLeft" width="130"><?=$lang["realm"] ?>:</td>
	  <td><input name="realm" class="field" type="text" value="<?=trim($ProfileData[0]["user_realm"])?>" /></td>
	</tr>
	
    <tr>
	  <td class="padLeft" width="130"><?=$lang["website"] ?>:</td>
	  <td><input name="website" class="field" type="text" value="<?=trim($ProfileData[0]["user_website"])?>" /></td>
	</tr>
	
    <tr>
	  <td class="padLeft" width="130"><?=$lang["gender"] ?>:</td>
	  <td>
	   <select name="gender">
	     <option value="0"></option>
<?php if ( $ProfileData[0]["user_gender"] == 1 ) $sel = 'selected="selected"'; else $sel = ''; ?>
		 <option <?=$sel?> value="1"><?=$lang["male"] ?></option>
<?php if ( $ProfileData[0]["user_gender"] == 2 ) $sel = 'selected="selected"'; else $sel = ''; ?>
		 <option <?=$sel?> value="2"><?=$lang["female"] ?></option>
	   </select>
	  </td>
	</tr>

    <tr>
	  <td class="padLeft" width="130"><?=$lang["language"]?>:</td>
	  <td>
	   <select name="lang">
	      <?php foreach($UserLang as $Language) { ?>
		    <option <?=$Language["selected"]?> value="<?=$Language["lang_name"]?>"><?=$Language["lang_name"]?></option>
		  <?php } ?>
	   </select>
	  </td>
	</tr>

	<?=os_custom_user_fields()?>
    <tr>
	  <td class="padLeft" width="130"></td>
	  <td>
	     <div class="padTop"></div>
	     <input name="change_profile" class="menuButtons" type="submit" value="<?=$lang["submit"] ?>" /> 
<span id="message">
 <?php if (isset( $_GET["updated"])) { ?><?=$lang["profile_changed"]?><?php } 
 else { 
    if ( isset( $_GET["pwchange"] ) AND $_GET["pwchange"] == 1) { ?><?=$lang["password_changed"]?><?php }
	if ( isset( $_GET["pwchange"] ) AND $_GET["pwchange"] == 2) { ?><?=$lang["error_passwords"]?><?php }
	if ( isset( $_GET["pwchange"] ) AND $_GET["pwchange"] == 3) { ?><?=$lang["error_short_pw"]?><?php }
 } ?> 
</span>
		 <div class="padTop"></div>
	  </td>
	</tr>
	
</table>

<?=os_display_custom_fields()?>

</form>
     </div>
    </div>
   </div>
  </div>
</div>
<div style="margin-top: 100px;"></div>
<?php } ?>