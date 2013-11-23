<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
?>

<div id="content" class="s-c-x">
<div class="wrapper">   
    <div id="main-column">
     <div class="padding">
      <div class="inner">
	  
      <?=OS_404($lang["404_error"])?>
	  
     </div>
    </div>
   </div>
  </div>
</div>

<div style="height: 400px;"></div>