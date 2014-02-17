<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
?>

<div id="content" class="s-c-x">
<div class="wrapper">   
    <div id="main-column">
     <div class="padding">
      <div class="inner">
	  
  <table>
    <tr>
	 <th width="240" class="padLeft"><?=$lang["game"]?></th>
	 <th width="150">Players</th>
	 <th width="80"><?=$lang["duration"]?></th>
	 <th width="50"><?=$lang["type"]?></th>
	 <th width="120"><?=$lang["date"]?></th>
	 <th><?=$lang["map"]?></th>
   </tr>
	  <?php
	  foreach($CommonGames as $Games) {
	  ?>
  <tr class="row">
	 <td width="240" class="padLeft" class="font12">
	 <a href="<?=OS_HOME?>?game=<?=$Games["id"]?>"><span class="font13 winner<?=$Games["winner"]?>"><?=$Games["gamename"]?></span></a>
	 <?php if (isset( $Games["replay"]) AND !empty($Games["replay"] ) ) { ?>
	 <img class="float_right padRight" src="<?=OS_HOME?>img/replay.gif" alt="replay" width="16" height="16" />
	 <?php } ?>
	 </td> 
	 <td>
	   <a href="<?=OS_HOME?>?u=<?=$Games["player1"]?>"><?=$Games["player1"]?></a> &amp; 
	   <a href="<?=OS_HOME?>?u=<?=$Games["player2"]?>"><?=$Games["player2"]?></a>
	 </td>
	 <td width="80"  class="font12"><?=secondsToTime($Games["duration"])?></td>
	 <td width="50"  class="font12"><?=$Games["type"]?></td>
	 <td width="120" class="font12"><?=date($DateFormat, strtotime($Games["datetime"]))?></td>
	 <td class="font12"><?=$Games["map"]?></td>
   </tr>
	  <?php } ?>
	 </table>
     </div>
    </div>
   </div>
  </div>
</div>
  <?php
  $SHOW_TOTALS = 1;
  include('inc/pagination.php');
?> 