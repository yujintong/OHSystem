<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

$field_name = "oh_announcements";

if ( isset($_POST["submit_ann"]) AND isset($_POST["announcements"]) ) {
   $words = strip_tags( trim( $_POST["announcements"] ) );
   OS_add_custom_field(1, $field_name, $words);
   $saved=1;
   OS_AddLog($_SESSION["username"], "[os_announcements] Edited Announcements");
}

$announcements = OS_get_custom_field(1, $field_name);
?>
<div align="center"> 
<h2>Bot Announcements</h2>
<form action="" method="post">
  <textarea rows="10" cols="60" name="announcements"><?=$announcements?></textarea>
  
  <div>
    <input type="submit" value="Save Annoucement" name="submit_ann" class="menuButtons" />
  </div>
</form>

<?php if (isset($saved) ) { ?>
 <h4>Form is successfully stored</h4>
<?php } ?>

<div style="margin-top:32px;margin-bottom:62px;">This is the information that will displayed after X minutes.</div>


<?php
$field_name = "oh_announcements_config";

if ( isset($_POST["a_saved"]) AND isset($_POST["a_time"]) ) {
   $time = (int) strip_tags( trim( $_POST["a_time"] ) );
   if ( !is_numeric($time) ) $time = 30;
   if ( $time<0 OR $time>60*24 ) $time = 30;
   $lobby = strip_tags( trim( $_POST["a_lobby"] ) );
   $data = "repeat	$time	lobby	$lobby";
   OS_add_custom_field(1, $field_name, $data);
   $saved=1;
   OS_AddLog($_SESSION["username"], "[os_announcements] Edited Announcements Config");
}

$config = OS_get_custom_field(1, $field_name);
$cfg = explode("	", $config);
if ( isset($cfg[1]) ) $time = $cfg[1]; else $time = 30;
if ( isset($cfg[3]) ) $lby  = $cfg[3]; else $lby  = 0;

?>
<form action="" method="post">
<h2>Announcements configuration</h2>
<table>
 <tr>
   <td width="130">Display every:</td>
   <td><input type="text" name="a_time" size="1" value="<?=$time?>" /> min.</td>
 </tr>
 <tr>
   <td width="130">Display on Lobby:</td>
   <td>
   <select name="a_lobby">
   <?php if ( $lby == 1 ) $sel = 'selected="selected"'; else $sel = ""; ?>
    <option <?=$sel?> value="1">Yes</option>
   <?php if ( $lby == 0 ) $sel = 'selected="selected"'; else $sel = ""; ?>
	<option <?=$sel?> value="0">No</option>
   </select>
   </td>
 </tr>
 <tr>
   <td width="100"></td>
   <td><input type="Submit" value="Save Configuration" name="a_saved" class="menuButtons" /></td>
 </tr>
</table>

</form>

<?php if (isset($saved) ) { ?>
 <h4>Configuration has been successfully saved</h4>
<?php } ?>

 </div>
 
 <div style="margin-top:150px;">&nbsp;</div>