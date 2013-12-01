<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
?>

<div id="content" class="s-c-x">
<div class="wrapper">   
    <div id="main-column">
     <div class="padding">
      <div class="inner">
	 
     <?=OS_SortTopPlayers()?>
	 <?=OS_ComparePlayers( 'form_start' )?>
	 <!--<?=OS_DisplayCountries('country', 1, 'top' )?> will be added in next version -->
    <table>
     <tr> 
	   <th width="32" class="padLeft">&nbsp;</th>
	   <?php if (OS_TOP_ONLINE == 1) { ?>
	   <th width="26" class="padLeft">&nbsp;</th>
	   <?php } ?>
	   <th width="200"><?=$lang["player"]?></th>
	   <th width="80"><?=$lang["score"]?></th>
	   <th width="70"><?=$lang["games"]?></th>
	   <th width="40"><span <?=ShowToolTip($lang["longest_streak"]." / ".$lang["losing_streak"], OS_HOME.'img/winner.png', 230, 32, 32)?>><img src="<?=OS_HOME?>img/streak.gif" width="20" /></span></th>
	   <th width="30"><span <?=ShowToolTip($lang["zero_deaths"], OS_HOME.'img/winner.png', 210, 32, 32)?>><img src="<?=OS_HOME?>img/winner.png" width="20" /></span></th>
	   <th width="90"><?=$lang["wld"]?></th>
	   <th width="70"><?=$lang["wl_percent"]?></th>
	   <th width="120"><?=$lang["kda"]?></th>
	   <th><?=$lang["cdn"]?></th>
	  </tr>
<?php 
foreach ($TopData as $Data) { ?>
  <tr class="row">
    <td width="32" class="padLeft"><?=$Data["counter"]?></td>
	<?php if (OS_TOP_ONLINE == 1) { ?>
    <td width="26"><?=OS_DisplayOnline( $Data["gamename"], $Data["gameid"], $Data["botid"]  )?></td>
	<?php } ?>
    <td width="190" class="font12">
	<?=OS_ComparePlayers( 'checkbox', $Data["id"] )?>
	
	<?=OS_ShowUserFlag( $Data["letter"], $Data["country"] )?>
	<?=OS_TopUser($Data["id"], $Data["player"])?>
	<?=OS_IsUserGameBanned( $Data["banned"], $lang["banned"] )?>	
	<?=OS_IsUserGameAdmin( $Data["admin"] )?>
	<?=OS_IsUserGameWarned( $Data["warn"],  $Data["warn_expire"], $lang["warned"] )?>
	<!--<?=OS_IsUserGameSafe( $Data["safelist"], $lang["safelist"] )?>
	<?=OS_IsUserGameLeaver( $Data["leaver"], $lang["leaves"].": ".$Data["leaver"]."<div>".$lang["stayratio"].": ".$Data["stayratio"]."%</div>" )?>-->
	</td>
	<td width="80" class="font12"><?=$Data["score"]?></td>
    <td width="60" class="font12"><?=$Data["games"]?></td>
	<td width="60" class="font12">
	  <span class="won"><?=$Data["maxstreak"]?></span> / 
	  <span class="lost"><?=$Data["maxlosingstreak"]?></span>
	</td>
	<td width="30" class="font12"><?=$Data["zerodeaths"]?></td>
	<td width="90" class="font12">
	  <span class="won"><?=$Data["wins"]?></span>/
	  <span class="lost"><?=$Data["losses"]?></span>/
	  <span class="draw"><?=$Data["draw"]?></span>
	  </td>
	<td width="60" class="font12"><?=$Data["winslosses"]?>%</td>
    <td width="150" class="font12">
	  <span class="won"><?=($Data["avg_kills"])?></span>/
	  <span class="lost"><?=$Data["avg_deaths"]?></span>/
	  <span class="assists"><?=$Data["avg_assists"]?></span>
	</td>
	<td width="160" class="font12">
	  <span class="won"><?=$Data["avg_creeps"]?></span>/
	  <span class="lost"><?=$Data["avg_denies"]?></span>/
	  <span class="assists"><?=$Data["avg_neutrals"]?></span>
	
	</td>
  </tr>
   
  <?php
}
?>	  
    </table>
	<?=OS_ComparePlayers( 'submit' )?>
     </div>
    </div>
   </div>
  </div>
</div>
<?php
	 include('inc/pagination.php');
?>
