<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

$field_name = "oh_badwords";

if ( isset($_POST["submit_wf"]) AND isset($_POST["bad_words"]) ) {
   $words = strip_tags( trim( $_POST["bad_words"] ) );
   OS_add_custom_field(1, $field_name, $words);
   $saved=1;
   OS_AddLog($_SESSION["username"], "[os_badwords] Edited Bad words");
}

$badwords = OS_get_custom_field(1, $field_name);
?>
<div align="center"> 
<h2>Word Filter</h2>
<form action="" method="post">
  <textarea rows="10" cols="60" name="bad_words"><?=$badwords?></textarea>
  
  <div>
    <input type="submit" value="Save word filter" name="submit_wf" class="menuButtons" />
  </div>
</form>

<?php if (isset($saved) ) { ?>
 <h4>Form is successfully stored</h4>
<?php } ?>

<div style="margin-top:32px;">This is a filter of bad words that are used for bot</div>
<div>Separate each word with a space</div>
 </div>
 
 <div style="margin-top:150px;">&nbsp;</div>