<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

$field_name = "oh_bannednamepartials";

if ( isset($_POST["submit_bn"]) AND isset($_POST["BannedNames"]) ) {
   $words = strip_tags( trim( $_POST["BannedNames"] ) );
   OS_add_custom_field(1, $field_name, $words);
   $saved=1;
   OS_AddLog($_SESSION["username"], "[oh_bannednamepartials] Edited Banned names");
}

$BannedNames = OS_get_custom_field(1, $field_name);
?>
<div align="center"> 
<h2>Banned names</h2>
<form action="" method="post">
  <textarea rows="10" cols="60" name="BannedNames"><?=$BannedNames?></textarea>
  
  <div>
    <input type="submit" value="Save word filter" name="submit_bn" class="menuButtons" />
  </div>
</form>

<?php if (isset($saved) ) { ?>
 <h4>Form is successfully saved</h4>
<?php } ?>

<div style="margin-top:32px;">This is a filter of banned names that are used for bot</div>
<div>Separate each name (partial name) with a space</div>
 </div>
 
 <div style="margin-top:150px;">&nbsp;</div>