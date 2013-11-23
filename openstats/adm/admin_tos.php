<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

if ( isset($_POST["about_us_text"]) ) {
   $text = $_POST["about_us_text"];
   
   file_put_contents("../inc/tos.php", $text);
}

$content = file_get_contents("../inc/tos.php");

?>
<div align="center"> 
<h2>Term of service</h2>
<form action="" method="post">
<table> 
 <tr>
  <td>
 <textarea class="ckeditor" cols="90" id="editor1" name="about_us_text" rows="20"><?=$content?></textarea>
 </td>
 </tr>
 <tr>
 <td>
 <div class="padTop"></div>
 <div>
   <input type="submit" value="Submit" name="about" class="menuButtons" /> 
   <a href="<?=OS_HOME?>?tos" class="menuButtons">View</a>
 </div>
 <div class="padTop"></div>
 </td>
 </tr>
 </table>
 <script type="text/javascript" src="<?php echo OS_HOME;?>adm/editor.js"></script>
</form>
 </div>