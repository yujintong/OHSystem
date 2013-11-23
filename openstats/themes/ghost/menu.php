<?php
if (!isset($website) ) {header('HTTP/1.1 404 Not Found'); die; }

if ( !isset($s) ) $s = $lang["search_players"];
?>
<body class="f-larger">

<div id="menu-bar">
	<div class="wrapper">
		<div id="horiz-menu" class="splitmenu">
	<ul class="menu">
 <li class="<?=$MenuClass["home"]?> home">
    <a href="<?=OS_HOME?>"><?=$lang["home"]?></a>
 </li>
  <?php if ($LiveGames == 1) { ?>
  <li class="<?=$MenuClass["live"]?>"><a href="<?=OS_HOME?>?live_games"><?=$lang["live_games"]?></a></li>
  <?php } ?>
  <?php if ($TopPage == 1) { ?>
  <li class="<?=$MenuClass["top"]?>"><a href="<?=OS_HOME?>?top"><?=$lang["top"]?></a></li>
  <?php } ?>
  <li class="<?=$MenuClass["games"]?>"><a href="<?=OS_HOME?>?games"><?=$lang["game_archive"]?></a></li>
<?php if ($HeroesPage == 1 AND $ItemsPage == 1 ) { ?>
  <li class="<?=$MenuClass["misc"]?>"><a href="javascript:;"><?=$lang["media"]?></a>
	 <ul>
	   <?=os_add_menu_misc()?>
	   <?php if ($GuidesPage == 1) { ?>
       <li><a href="<?=OS_HOME?>?guides"><?=$lang["guides"]?></a></li>
	   <?php } ?>
	   <?php if ($HeroesPage == 1) { ?>
       <li><a href="<?=OS_HOME?>?heroes"><?=$lang["heroes"]?></a></li>
	   <?php } ?>
	   <?php if ($HeroVote == 1) { ?>
       <li><a href="<?=OS_HOME?>?vote"><?=$lang["heroes_vote"]?></a></li>
	   <?php } ?>
	   <?php if ($ItemsPage == 1) { ?>
	   <li><a href="<?=OS_HOME?>?items"><?=$lang["items"]?></a></li>
	   <?php } ?>
	 </ul>
  </li>
  <?php } ?>
  
<?php if ($BansPage==1) { ?>
  <li class="<?=$MenuClass["bans"]?>">
  <a href="<?=OS_HOME?>?bans"><?=$lang["bans"]?></a>
    <ul>
	   <li><a href="<?=OS_HOME?>?bans"><?=$lang["all_bans"]?></a></li>
<?php if ($BanReports==1) { ?>
	   <li><a href="<?=OS_HOME?>?ban_report"><?=$lang["ban_report"]?></a></li>
 <?php } ?>	  
<?php if ($BanAppeals==1) { ?>
	   <li><a href="<?=OS_HOME?>?ban_appeal"><?=$lang["ban_appeal"]?></a></li>
 <?php } ?>	  
	<?php if ($WarnPage == 1) { ?>
       <li><a href="<?=OS_HOME?>?warn"><?=$lang["warn"]?></a></li>
    <?php } ?>	
	</ul>
  </li>
	<?php if ($SafelistPage == 1) { ?>
    <li  class="<?=$MenuClass["safelist"]?>"><a href="<?=OS_HOME?>?safelist"><?=$lang["safelist"]?></a></li>
	<?php } ?>
 <?php } ?>
   <?php if ($AdminsPage == 1) { ?>
   <li class="<?=$MenuClass["admins"]?>"><a href="<?=OS_HOME?>?admins"><?=$lang["admins"]?></a></li>
   <?php } ?>
   
  <?php if ($AboutUs == 1) { ?>
    <li class="<?=$MenuClass["about_us"]?>"><a href="<?=OS_HOME?>?about_us"><?=$lang["about_us"]?></a></li>
    <?php } ?>	
	
  <?php if ($MemberListPage == 1) { ?>
    <li class="<?=$MenuClass["members"]?>"><a href="<?=OS_HOME?>?members"><?=$lang["members"]?></a></li>
    <?php } ?>	
   
   <?php if (!is_logged() AND $UserRegistration == 1) { ?>
    <li class="<?=$MenuClass["profile"]?>"><a href="<?=OS_HOME?>?login"><?=$lang["login_register"]?></a></li>
	<li class="<?=$MenuClass["fb"]?>"><a href="<?=OS_HOME?>?action=facebook"><img src="<?=OS_HOME?>img/fb.png" alt="fb" class="imgvalign" width="16" height="16" /> LOGIN</a></li>
   <?php } ?>
   
   <?php if (is_logged() ) { ?>
   <li class="<?=$MenuClass["profile"]?>">
    <a href="<?=OS_HOME?>?profile"><b><?=substr($_SESSION["username"],0,30)?></b> <? if( isset( $_SESSION["bnet"]) ) { ?><?=OS_bnet_icon( $_SESSION["bnet"], 24, 24, "imgvalign", 0 )?> <? } ?> </a>
      <ul>
	 <?php if ( OS_CanAccessAdminPanel() ) { ?>
	    <li><a href="<?=OS_HOME?>adm/"><b><?=$lang["admin_panel"]?></b></a></li>
	    <?php } ?>
		 <li><a href="<?=OS_HOME?>?profile"><?=$lang["profile"]?></a></li>
<?php if (isset($_SESSION["phpbb"]) ) { ?>
       <li><a href="<?=OS_HOME?>?logout&amp;sid=<?=$_SESSION["sid"]?>"><?=$lang["logout"]?></a></li>
<?php } else { ?>
		 <li><a href="<?=OS_HOME?>?logout"><?=$lang["logout"]?></a></li>
<?php } ?>
	  </ul>
   </li>
   <?php } ?>
	</ul>


		</div>
	</div>
</div>

<div id="inset">
<div class="wrapper">
<a href="<?=OS_HOME?>" class="nounder">
<img src="<?=OS_THEME_PATH?>images/blank.gif" style="border:0;" alt="" id="logo" />
</a>

</div>

    <div id="search">
     <form action="" id="search-form" method="get">
     <div><input id="s" name="search" onblur='if (this.value == "") {this.value = "<?=$s?>";}' onfocus='if (this.value == "<?=$s?>") {this.value = ""}' type="text" value='<?=$s?>' onkeyup="OS_LiveSearch()" />
     <input type="submit" value="<?=$lang["search"]?>" class="menuButtons" /></div>
     </form>
    </div>
	<div id="search_results"></div>
	
</div>

<?php os_top_menu() ?>
