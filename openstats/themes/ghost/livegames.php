<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }  
  /////////////////////////////////////////
  //CHAT - New Window
  /////////////////////////////////////////

if ( $ChatOpen == 1) {
?>
<div id="content" class="s-c-x">
<div class="wrapper">   
    <div id="main-column">
     <div class="padding">
      <div class="inner">
	 <?php if ( !empty($InfoMessage) ) { ?><h4><?=$InfoMessage?></h4><?php } ?>  
	 <div class="h32 marginBottom12"><h2><img src="<?=OS_HOME?>img/chat-icon.png" alt="chat" width="32" height="32" class="imgvalign" /> <a target="_blank" href="<?=OS_HOME?>?u=<?=$chatWith?>"><?=$chatWith?></a> <?=$lang["chat_title_player"]?></h2></div>
	  <form action="" method="post">
	    <div class="padLeft PlayerChatWrapper">
		<div class="marginBottom12">
	    <input autocomplete="off" type="text" disabled="disabled" name="send_to" value="<?=$chatWith?>" class="chatField" id="sendTo" />
		</div>
		<input <?=$dis?> type="text" size="80" maxlength="150" value="" name="commandSend" autocomplete="off" class="chatMessage" />
		<div class="chatSubmitMessage">
		<input <?=$dis?> type="submit" value="Send a Message" name="SendSubmit" class="menuButtons"  />
		</div>
		
		<?= $message ?>
		
		<?=$return?>
		
		<div class="chatAttention">
		<?=$lang["chat_attention"]?>
		</div>
		<?=os_display_custom_fields()?> 
		</div>
		
	  </form>
     </div>
    </div>
   </div>
  </div>
</div>
	<?php
   } else {  
?>
<div id="content" class="s-c-x">
<div class="wrapper">   
    <div id="main-column">
     <div class="padding">
      <div class="inner">
	 <div class="clr"></div>
	 <div class="h32 padLeft">
	   <div class="LiveGamesTitle">
	   <span id="BotStatus"><img src="<?=OS_HOME?>img/BotOffline.png" alt="" width="24" height="24" class="imgvalign" /></span> 
	   <?=$lang["live_games"]?> 
	   <span id="InfoLoader"></span>
	   </div>
	   <div class="GameLogGameName" id="InfoGameName">
	   <?=$GameName?>
	   <input type="hidden" id="gninfo" value="<?=$GameName?>" />
	   <input onclick="ToClipboard('gninfo')" type="button" value="Copy" class="menuButtons" />
	   </div>
	 </div>
	<?php if ( !empty($InfoMessage) ) { ?><h4><?=$InfoMessage?></h4><?php } ?>   
	 <div class="clr"></div>
	 
	 <div class="padLeft padBottom">

    <div class="h32" style="display:none;"><span id="lastID">0</span> <span id="aliasID">1</span></div>

<div style="padding: 5px;border: 4px solid #E7F9FB;margin-bottom: 6px;">
  <?php
    foreach ( $GameAliases as $Alias ) {
	?>
	<input class="menuButtons" type="button" id="alias_<?=$Alias["alias_id"]?>" value="<?=$Alias["alias_name"]?>" onclick="ChangeAlias('<?=$Alias["alias_id"]?>')" />
	<?php
	}
  ?>
</div>
	
<div id="AllLiveGames" <?=$PageDisabled?>>
<span id="gamerefresher" class="h32"><img src="<?=OS_HOME?>/img/blank.gif" width="16" height="16" class="imgvalign" /></span>
<?php
  $c=0;
  foreach($IDS as $cID) {
  ?>
<input <?=$PageDisabled?> id="b<?=$cID["chatid"]?>" type="button" class="menuButtons<?=$cID["button"]?>" onclick="clearLiveGamesTimer('<?=$cID["botid"]?>', '<?=$cID["chatid"]?>', '<?=$cID["gn"]?>');" value="<?=$cID["status"]?>#<?=$cID["chatid"]?>" />
  <?php
  if ( $c==0 AND $PageDisabled=="") {
   $c++;
   $SelChatID = $cID["chatid"];
   if ( isset($_GET["gameid"]) AND is_numeric($_GET["gameid"]) AND isset($_GET["botid"]) ) { 
   $SelChatID = ( int )  $_GET["gameid"] ;
   $cID["chatid"] = $SelChatID;
   $cID["gn"] = $GameNameSelected ;
   $SelBotID = ( int )  $_GET["botid"] ;
   } else $SelBotID = $cID["botid"];
   ?>
   <script type="text/javascript">
   setTimeout( function(){ clearLiveGamesTimer('<?=$SelBotID?>', '<?=$cID["chatid"]?>', '<?=$cID["gn"]?>') }, 5 );
   setTimeout( function(){ OS_CheckBotOnline() }, 500 );
   </script>
   <?php
  }
  }
?>  
</div>
	 
	 <div <?=$PageDisabled?> id="WrapperData" class="LiveGamesWrapper">
	      <div id="ShowData" class="LiveGamesContent"></div>
		  <div id="PlayerList" class="LiveGamesPlayerList"></div>
	 </div>

	 <input <?=$PageDisabled?> type="hidden" value="<?=$SelBotID?>"  id="selectedBot" />
	 <input <?=$PageDisabled?> type="hidden" value="<?=$SelChatID?>" id="selectedGameID" />
	 <input <?=$PageDisabled?> type="hidden" value="<?php if (isset($SelID)) { echo $SelID; } ?>"     id="selectedID" />
	 
	 
	 <div class="h32 padLeft">
	     <?php if (OS_is_user_admin($_SESSION["level"]) ) { ?>
		 <input type="button" class="menuButtons padRight" value="Open Chat" onclick="showhide('chat_')" /> 
		 <?php } ?>
	    <input <?=$disabled?> id="winchance" type="button" class="menuButtons" value="<?=$lang["gl_calculate"]?>" onclick="OS_WinChance()" /> 
		<span id="WinChanceLoader"></span>
	 </div>
	 
    <?=OS_OpenChat()?>
	 
	 <div <?=$PageDisabled?> id="WinChanceWrapper"></div>
	  
	 </div>
	 
	 <?=os_display_custom_fields()?> 
	 <div id="debug_"></div>
     </div>
    </div>
   </div>
  </div>
</div>
<?php } ?>