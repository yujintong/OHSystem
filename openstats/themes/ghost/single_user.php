<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

  foreach ( $UserData as $User ) {
  ?>
<div id="content" class="s-c-x">
<div class="wrapper">   
    <div id="main-column">
     <div class="padding">
      <div class="inner">
	 
<table>
   <tr>
     <td class="padLeft" width="400">
  <h1>
	<?=OS_ShowUserFlag( $User["letter"], $User["country"], 220 )?>
    <?=$User["player"]?>  
	<?=OS_IsUserGameBanned( $User["banned"], $lang["banned"] )?> 
	<?=OS_IsUserGameAdmin( $User["GameAdmin"] )?>
	<?=OS_IsUserGameLeaver( $User["leaver"], $lang["leaves"].": ".$User["leaver"]."<div>".$lang["stayratio"].": ".$User["stayratio"]."%</div>",1 )?>
	<?=OS_bnet_icon($User["user_bnet"], 14, 14, "imgvalign")?>
	<?=OS_protected_icon( $User["user_ppwd"], $User["user_bnet"], $lang["protected_account"], 14, 14, "imgvalign" ) ?> 
  </h1>
	 </td>
	 <td style="text-align:right">
	 <?=DisplayGameTypePlayer($GameAliases, $UserOtherGames, $User["player"])?></td>
   </tr>
   <tr>
    <td  class="padLeft">
	<?=$User["realm"]?>
	</td>
	<td style="font-size:12px; text-align:right">
	<?=OS_AdminTools($User["id"], $User["player"])?>
	</td>
   </tr>
   <?php if (isset($UserOtherGames["id"]) ) { ?>
   <tr>
   <td  class="padLeft">
   <a class="menuButtons" href="<?=OS_HOME?>?u=<?=$UserOtherGames["id"]?>"><?=$lang["most_recent_stats"]?></a></td>
   <td></td>
   </tr>
   <?php } ?>
   <tr>
   <td  class="padLeft"><?php
  if ( !empty($LastSeen["gameid"]) ) {
  ?>
  <div> <?=$lang["last_seen"]?>:
  <a href="<?=OS_HOME?>?live_games&amp;gameid=<?=$LastSeen["gameid"]?>&amp;botid=<?=$LastSeen["botid"]?>">
   <span <?=ShowToolTip("Last seen: <div>".$LastSeen["time"]."</div>", OS_HOME.'img/BotOnline.png', 210, 32, 32)?>><img src="<?=OS_HOME?>img/BotOnline.png" width="16" class="imgvalign" /></span>
    <?=$lang["game"]?> #<?=$LastSeen["gameid"]?>, <?=($LastSeen["time"])?>
   </a>
   </div>
  <?php
  }
  ?></td>
   <td></td>
   </tr>
</table>	

  <?php ShowModeratorPanel($UserData); ?>

 <?php
  if ( OS_is_banned_player( $User["banname"] ) ) {
  ?>
  <h2><span class="banned"><?=$lang["banned"]?></span> 	<?=OS_is_ban_perm($User["expiredate"], $lang["permanent_ban"])?></h2>
  <table class="Table500px">
  <tr>
    <td width="100"><b><?=$lang["reason"]?>:</b> </td>
	<td><span class="banned padTop"><?=$User["reason"]?></span></td>
  <tr>
    <td width="100"><b><?=$lang["bannedby"]?>:</b> </td>
	<td><?=$User["bannedby"]?></td>
  </tr>
  <tr>
    <td width="100"><b><?=$lang["date"]?>:</b> </td>
	<td><?=$User["bandate"]?></td>
  </tr>
  <tr>
    <td width="100"><b><?=$lang["expires"]?>:</b> </td>
	<td>
	<?=OS_ban_expired($User["expiredate"], "", "" )?>
	<div><?=OS_ExpireDateRemain($User["expiredate"])?></div>
	</td>
  </tr>
  </table>
  <?php } ?> 
  
  <?php if ($User["hide"] == 0 ) { ?>
  <div class="padTop">
  
  <?php if (isset($User["admin_info"])) { ?>
  <h4><span class="banned"><?=$lang["hide_stats_message"]?></h4>
  <?php } ?>
  
  <table class="Table500px">
      <tr class="row">
	  <th class="padLeft" width="140"><?=$lang["stats"] ?>: <?=$User["month"]?> / <?=$User["year"]?></th>
	  <th width="160"></th>
	  <th width="60"></th>
	  <th width="175"></th>
	  <th width="175"></th>
	  <th width="175"></th>
	</tr>
    <tr class="row">
	  <td class="padLeft" width="140"><b><?=$lang["score"]?>:</b></td>
	  <td width="160"><?=$User["score"]?> </td>
	  <td class="padLeft" width="60"><b><?=$lang["win_percent"] ?>:</b></td>
	  <td width="160"><?=$User["winslosses"]?> % (<?=$User["totalwinslosses"]?> %)</td>
	  <td class="padLeft" width="140"><b><?=$lang["ck"] ?>:</b></td>
	  <td width="160"><?=$User["creeps"]?> (<?=$User["totalcreeps"]?>)</td>
	</tr>
	
    <tr class="row">
	  <td class="padLeft" width="140"><b><?=$lang["kills"]?>:</b></td>
	  <td width="160"><?=$User["kills"]?> (<?=$User["totalkills"]?>)</td>
	  <td class="padLeft" width="60"><b><?=$lang["assists"]?>:</b></td>
	  <td width="160"><?=$User["assists"]?> (<?=$User["totalassists"]?>)</td>
	  <td class="padLeft" width="140"><b><?=$lang["cd"]?>:</b></td>
	  <td width="180"><?=$User["denies"]?> (<?=$User["totaldenies"]?>)</td>
	</tr>
	
    <tr class="row">
	  <td class="padLeft" width="140"><b><?=$lang["deaths"]?>:</b></td>
	  <td width="160"><?=$User["deaths"]?>  (<?=$User["totaldeaths"]?>)</td>
	  <td class="padLeft" width="90"><b><?=$lang["kd_ratio"]?>:</b></td>
	  <td width="160"><?=($User["kd"])?> (<?=$User["totalkd"]?>)</td>
	  <td class="padLeft" width="140"><b><?=$lang["neutrals"]?>:</b></td>
	  <td width="160"><?=$User["neutrals"]?> (<?=$User["totalneutrals"]?>)</td>
	</tr>
	
    <tr class="row">
	  <td class="padLeft" width="140"><b><?=$lang["games"]?>:</b></td>
	  <td width="160"><a href="<?=OS_HOME?>?games&amp;uid=<?=$User["id"]?>"><?=$User["games"]?></a> (<?=$User["totalgames"]?>)</td>
	  <td class="padLeft" width="60"><b><?=$lang["wl"] ?>:</b></td>
	  <td width="160"><?=($User["wins"])?> / <?=($User["losses"])?> (<?=$User["totalwins"]?> / <?=$User["totallosses"]?>)</td>
	  <td class="padLeft" width="60"><b><?=$lang["towers"]?>:</b></td>
	  <td width="160"><?=($User["towers"])?> (<?=$User["totaltowers"]?>)</td>
	</tr>
	
    <tr class="row">
	  <td class="padLeft" width="140"><span <?=ShowToolTip($lang["kills_per_game"], OS_HOME.'img/winner.png', 130, 32, 32)?>><b><?=$lang["kpg"]?>:</b></span> </td>
	  <td width="160"><?=$User["kpg"]?> (<?=$User["totalkpg"]?>)</td>
	  <td class="padLeft" width="60"><span <?=ShowToolTip($lang["deaths_per_game"], OS_HOME.'img/skull.png', 160, 32, 32)?>><b><?=$lang["dpg"]?>:</b></span></td>
	  <td width="160"><?=($User["dpg"])?> (<?=$User["totaldpg"]?>)</td>
	  <td class="padLeft" width="60"><b><?=$lang["rax"]?>:</b></td>
	  <td width="160"><?=($User["rax"])?> (<?=$User["totalrax"]?>)</td>
	</tr>
	
    <tr class="row">
	  <td class="padLeft" width="140"><span <?=ShowToolTip($lang["assists_per_game"], OS_HOME.'img/winner.png', 180, 32, 32)?>><b><?=$lang["apg"]?>:</b></span></td>
	  <td width="160"><?=$User["apg"]?> (<?=$User["totalapg"]?>)</td>
	  <td class="padLeft" width="60"><span <?=ShowToolTip($lang["creeps_per_game"], OS_HOME.'img/winner.png', 190, 32, 32)?>><b><?=$lang["ckpg"]?>:</b></span></td>
	  <td width="160"><?=($User["ckpg"])?> (<?=$User["totalckpg"]?>)</td>
	  <td class="padLeft" width="60"><span <?=ShowToolTip($lang["denies_per_game"], OS_HOME.'img/winner.png', 190, 32, 32)?>><b><?=$lang["cdpg"]?>:</b></span></td>
	  <td width="160"><?=($User["cdpg"])?> (<?=$User["totalcdpg"]?>)</td>
	</tr>
	
    <tr class="row">
	  <td class="padLeft" width="140"><span <?=ShowToolTip($lang["left_info"], OS_HOME.'img/disc.png', 250, 32, 32)?>><b><?=$lang["left"]?>:</b></span></td>
	  <td width="160"><?=$User["leaver"]?> x (<?=$User["totalleaver"]?>)</td>
	  <td class="padLeft" width="60"><span <?=ShowToolTip($lang["stayratio"], OS_HOME.'img/winner.png', 120, 32, 32)?>><b><?=$lang["stayratio"]?>:</b></span></td>
	  <td width="160"><?=($User["stayratio"])?> % <?=OS_StayRatioIcon( $User["stayratio"] )?> (<?=$User["totalstayratio"]?> %)</td>
	  <td class="padLeft" width="60"><span <?=ShowToolTip($lang["longest_streak"], OS_HOME.'img/streak.gif', 190, 24, 24)?>><b><?=$lang["streak"]?>:</b></span></td>
	  <td width="160"><?=($User["maxstreak"])?> (<?=$User["totalmaxstreak"]?>)</td>
	</tr>
	
  </table>
  </div>

  <div class="padTop"></div>
  <table class="Table500px">
    <tr class="scourgeRow">
	  <td width="190" class="padLeft"><b><?=$lang["time_played"] ?></b>:</td>
	  <td class="padLeft"><?=$TimePlayed["timeplayed"]?></td>
	</tr>
    <tr class="scourgeRow">
	  <td width="190" class="padLeft"><b>Points</b>:</td>
	  <td class="padLeft"><?=$User["points"]?></td>
	</tr>
  </table>

 
 <?php 
  if ( !empty($PenaltyData) ) {
  ?>
  <div class="padTop"></div>
  <div class="padLeft">
  <h3><?=$lang["player_offences"]?></h3>
  
  <table>
  <tr>
  <th>
    <div><?=$lang["player_warned"]?> <b><?=$PenaltyData[0]["warned"]?></b> <?=$lang["has_total_of"] ?> <span class="penalty_points"><b><?=$PenaltyData[0]["total"]?> <?=$lang["penalty_points"]?></b></span>.</div>
  </th>
  </tr>
  </table>
  
  <?=OS_DisplayPPBar( $PenaltyData[0]["total"] )?>
  <?php if ($PenaltyData[0]["total"] >=1 ) {  ?>
  <a name="pp"></a>
  <table>
    <tr>
	  <th width="60"><?=$lang["pp"]?></th>
	  <th width="230"><?=$lang["reason"]?></th>
	  <th width="160"><?=$lang["admin"]?></th>
	  <th width="160"><?=$lang["date"]?></th>
	  <th><?=$lang["expire"]?></th>
	</tr>
	<?php
	  foreach ( $PenaltyData as $PP ) {
	?>
	</tr>
	  <td width="60"><b>+<?=$PP["pp"]?></b> <?php OS_ToolRemovePP( $PP["id"] ) ?></td>
	  <td width="190"><?=$PP["reason"]?></td>
	  <td><?=$PP["admin"]?></td>
	  <td><?=$PP["date"]?></td>
	  <td><?=$PP["expire_date"]?></td>
	</tr>
	<?php
	  }
	?>
  </table>
  <?php } ?>
  </div>
   <?php
  }
?> 
<div class="padTop"></div>
<?php
  include(OS_CURRENT_THEME_PATH.'/single_user_hero_stats.php');
  include(OS_CURRENT_THEME_PATH.'/single_user_map.php');
?> 
  <div class="padTop"></div>
   <!-- FASTEST AND LONGEST GAME -->
<?php if (isset($FastestGame ) AND !empty($FastestGame) ) { ?> 
  <table class="Table500px">
    <tr>
	  <th class="padLeft" width="250"><?=$lang["fastest_game"]?></th>
	  <th><?=$lang["duration"]?></th>
	  <th><?=$lang["kills"]?></th>
	  <th><?=$lang["deaths"]?></th>
	  <th><?=$lang["assists"]?></th>
	</tr>
    <tr>
	  <td width="250" class="slot<?=$FastestGame["newcolour"]?> padLeft font12">
	      <a href="<?=OS_HOME?>?game=<?=$FastestGame["gameid"]?>"><?=$FastestGame["gamename"]?></a>
	  </td>
	  <td><?=$FastestGame["duration"]?></td>
	  <td><?=$FastestGame["kills"]?></td>
	  <td><?=$FastestGame["deaths"]?></td>
	  <td><?=$FastestGame["assists"]?></td>
	</tr>
	
   </table>
   
<?php } ?>
<?php if (isset($LongestGame ) AND !empty($LongestGame) ) { ?> 
  <table class="Table500px">
    <tr>
	  <th class="padLeft" width="250"><?=$lang["longest_game"]?></th>
	  <th><?=$lang["duration"]?></th>
	  <th><?=$lang["kills"]?></th>
	  <th><?=$lang["deaths"]?></th>
	  <th><?=$lang["assists"]?></th>
	</tr>
    <tr>
	  <td width="250" class="slot<?=$LongestGame["newcolour"]?> padLeft font12">
	      <a href="<?=OS_HOME?>?game=<?=$LongestGame["gameid"]?>"><?=$LongestGame["gamename"]?></a>
	  </td>
	  <td><?=$LongestGame["duration"]?></td>
	  <td><?=$LongestGame["kills"]?></td>
	  <td><?=$LongestGame["deaths"]?></td>
	  <td><?=$LongestGame["assists"]?></td>
	</tr>
	
   </table>
   
<?php } ?>
 
<?=os_display_custom_fields()?>
 
  <div class="padTop"></div>
  <div class="padTop"></div>
 
<table class="Table500px">
<tr>
  <td>
     <div class="padTop aligncenter" align="center">
       <h2><a name="game_history" href="<?=OS_HOME?>?games&amp;uid=<?=$User["id"]?>"><?=$lang["user_game_history"] ?></a></h2>
    </div>
  </td>
</tr>
</table>

  <?=DisplayGameTypes( $GameAliasesGames, 'u='.$User["id"], 1, '#game_history')?>
  
   <table>
    <tr>
	 <th width="220" class="padLeft"><?=$lang["game"]?></th>
	 <?php if (isset($_GET["u"]) ) { ?>
	 <th width="40"><?=$lang["hero"]?></th>
	 <th width="90"><?=$lang["kda"]?></th>
	 <th width="90"><?=$lang["cdn"]?></th>
	 <?php } ?>
	 <th width="80"><?=$lang["duration"]?></th>
	 <th width="50"><?=$lang["type"]?></th>
	 <th width="140"><?=$lang["date"]?></th>
	 <?php if (!isset($_GET["u"]) ) { ?>
	 <th width="160"><?=$lang["map"]?></th>
	 <?php } ?>
	 <th width="160"><?=$lang["creator"]?></th>
   </tr>
  <?php
  
  foreach ($GamesData as $Games) {
  ?>
  <tr class="row GameHistoryRow">
	 <td width="260" class="padLeft overflow_hidden slot<?=$Games["newcolour"]?>">
	 <?=OS_WinLoseIcon( $Games["win"] )?>
	   <a href="<?=OS_HOME?>?game=<?=$Games["id"]?>"><span class="winner<?=$Games["winner"]?>"><?=$Games["gamename"]?></span></a>
	 <?=OS_IsUserGameLeaver($Games["leaver"])?>
	 </td>
	 <?php if (isset($_GET["u"]) ) { ?>
	 <td width="40" height="40"><?=OS_UserHeroHistoryLink($User["id"], $Games["hero"], $lang["show_hero_history"]) ?></td>
	 <td width="90">
	 	<span class="won"><?=($Games["kills"])?></span> / 
	    <span class="lost"><?=$Games["deaths"]?></span> / 
	    <span class="assists"><?=$Games["assists"]?></span>
	 </td>
	 <td width="90">
	 	<span class="won"><?=($Games["creepkills"])?></span> / 
	    <span class="lost"><?=$Games["creepdenies"]?></span> / 
	    <span class="assists"><?=$Games["neutrals"]?></span>
	 </td>
	 <?php } ?>
	 <td width="80"><?=secondsToTime($Games["duration"])?></td>
	 <td width="50"><?=$Games["type"]?></td>
	 <td width="140"><?=date($DateFormat, strtotime($Games["datetime"]))?></td>
	 <?php if (!isset($_GET["u"]) ) { ?>
	 <td width="160"><?=$Games["map"]?></td>
	 <?php } ?>
	 <td width="160"><?=$Games["ownername"]?></td>
   </tr>
  <?php
  }
  ?>
  </table> 
     </div>
    </div>
   </div>
  </div>
</div>
  
  <?php
   $SHOW_TOTALS = 1;
   include('inc/pagination.php');
   
  if ($ReportUserLink == 1) {
  ?>
  <div class="reportUser">
  <a href="<?=OS_HOME?>?ban_report&amp;user=<?=$User["player"]?>"><?=$lang["report_user"]?></a>
  </div>
  <div style="width:100%; margin-top:10px;">&nbsp;</div>
  <?php }
  
  } else {
  ?>
  <h4><span class="banned"><?=$lang["hide_stats_message"]?></h4>
  <div style="width:100%; margin-top:100px;">&nbsp;</div>
  <?php
  }
 }
 ?>