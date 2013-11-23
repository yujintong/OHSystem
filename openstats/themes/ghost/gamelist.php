<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
  $runninggames = 0;
  $players = 0;
?>
<div id="content" class="s-c-x">
<div class="wrapper">   
    <div id="main-column">
     <div class="padding">
      <div class="inner">
	  
	 <div style="height:24px"><h4><?=$lang["current_games"]?> <span id="CGRefresh"></span></h4> </div> 
  
  <div id="currentgames"></div>
  
   <script type="text/javascript">
   setTimeout( function(){ OS_CurrentGames() }, 2 );
   </script>
  
     </div>
    </div>
   </div>
  </div>

