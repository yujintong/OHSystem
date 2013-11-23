<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

$field_name = "oh_banemail";

if ( isset($_POST["submit_be"]) AND isset($_POST["bad_email"]) ) {
   $words = strip_tags( trim( $_POST["bad_email"] ) );
   OS_add_custom_field(1, $field_name, $words);
   $saved=1;
   OS_AddLog($_SESSION["username"], "[oh_banemail] Edited Bad words");
}

$banemails = OS_get_custom_field(1, $field_name);
?>
<div align="center"> 
<h2>Ban Email Address</h2>

<?php if (!empty($banemails) ) { 
$total = count(explode(",", $banemails));
?>
<div><b>Banned:</b> <?=$total?> email addresses</div>
<?php } ?>

<form action="" method="post">
  <textarea rows="18" cols="80" name="bad_email"><?=$banemails?></textarea>
  
  <div>
    <input type="submit" value="Save Ban Emails" name="submit_be" class="menuButtons" />
  </div>
</form>

<div>Use par of email address. Example: <b>gmail.com</b> will ban user@gmail.com</div>
<div>Separate email domains by comma (example: <b>gmail, yahoo, hotmail</b>)</div>

<?php if (isset($saved) ) { ?>
 <h4>Form is successfully stored</h4>
<?php } ?>

 </div>
 
 <div style="margin-top:150px;">&nbsp;</div>