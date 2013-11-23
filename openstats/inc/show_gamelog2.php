<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

$GameLogFile = "gamelogs/GameLog_".$GameData[0]["gameid"].".html";

if (file_exists($GameLogFile)) {
  $GameLogDataFile = file_get_contents($GameLogFile);
  $GLD = explode("\n", $GameLogDataFile);
  
  $LobbyData = array();
  $GameLogData = array();
  foreach ( $GLD as $GLog) {
    $data = explode("	", $GLog);
	$GLog = strip_tags($GLog);
	if ( $data[0] == "[Lobby]" AND !empty($data[0]) ) $LobbyData[] = $GLog;
	else if (!empty($data[0])) {
	$data1 = str_replace(array("[", "]"), array("",""), $data[0]);
	if (isset($data[1])) $data2 = $data[1]; else $data2 = "";
	$cl  = '';
	if ( $data2 == "[System]" )    $cl = 'class="GameSystem"';
	if ( $data2 == "[Sentinel]" )  $cl = 'class="sentinel"';
	if ( $data2 == "[Scourge]" )   $cl = 'class="scourge"';
	$data3 = substr( $GLog, (strlen($data1)+strlen($data2))+3, strlen($GLog) );
	$GameLogData[] = '<div '.$cl.'> <b>'.$data1."</b> ".$data2." ".$data3.'</div>';
	
	}
  }
  ?>
  <a href="javascript:;" onclick="showhide('gamelogdata')" class="menuButtons">GameLog</a>
  <div id="gamelogdata" style="display:none; padding: 10px;">
  
  <a name="lobby"></a>
  <h2>Lobby</h2>
  <table class="ReplayTable">
      <?php
  foreach($LobbyData as $Lobby) { ?><tr class="row"><td class="GameSystem"><?=$Lobby?></td></tr><?php }
  ?>
  </table>
  
  <h2>GameLog</h2>
  <table class="ReplayTable">
      <?php
  foreach($GameLogData as $GLData) { ?><tr class="row"><td class="GameSystem"><?=$GLData?></td></tr><?php }
  ?>
  </table>
  </div><?php
} else include("inc/show_gamelog.php");
?>