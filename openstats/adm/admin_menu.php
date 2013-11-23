<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
?>
<div class="main-nav-main" style="position:fixed; top: 0px; left: 0px; width:100%; font-size:13px; z-index:1000;">
  <div id="menu-bar">
	 <div class="wrapper" style="z-index: 10000 !important;">
		<div id="horiz-menu" class="splitmenu">
<ul class="menu">
  <li><a href="<?=OS_HOME?>adm/">Dashboard</a>
    <ul>
	  <li><a href="<?=OS_HOME?>adm/?cfg">Configuration</a></li>
	  <li><a href="<?=OS_HOME?>adm/?geoip">GeoIP</a></li>
	  <?php if (OS_IsRoot() ) { ?>
	  <li><a href="<?=OS_HOME?>adm/?admin_logs">BOT: Admin Logs</a></li>
	  <?php } ?>
	  <li><a href="<?=OS_HOME?>adm/?announcements">BOT: Announcements</a></li>
	  <li><a href="<?=OS_HOME?>adm/?word_filter">BOT: Word Filter</a></li>
	  <li><a href="<?=OS_HOME?>adm/?bans&amp;addcountry">BOT: Ban Country</a></li>
	  <li><a href="<?=OS_HOME?>adm/?remote">BOT: RCON</a></li>
	  <li><a href="<?=OS_HOME?>adm/?gamelist">BOT: Gamelist</a></li>
	  <li><a href="<?=OS_HOME?>adm/?live_games">BOT: Live Games</a></li>
	</ul>
  </li>
  <li><a href="<?=OS_HOME?>adm/?games">Games</a></li>
  <li><a href="<?=OS_HOME?>adm/?players">Players</a></li>
  <li><a href="<?=OS_HOME?>adm/?posts">Posts</a></li>
  <li><a href="<?=OS_HOME?>adm/?comments">Comments</a></li>
  <li><a href="<?=OS_HOME?>adm/?users">Members</a></li>
  <li>
  <a href="<?=OS_HOME?>adm/?bans">Bans</a>
    <ul>
	  <!--<li><a href="<?=OS_HOME?>adm/?warns">Temp Bans/Warns</a></li>-->
	  <li><a href="<?=OS_HOME?>adm/?bans&amp;add">Add Ban</a></li>
	  <li><a href="<?=OS_HOME?>adm/?pp">Penalty points</a></li>
	  <li><a href="<?=OS_HOME?>adm/?bans&amp;autoban">Mass Ban Leavers</a></li>
	  <li><a href="<?=OS_HOME?>adm/?ban_reports">Ban Reports</a></li>
	  <li><a href="<?=OS_HOME?>adm/?ban_appeals">Ban Appeals</a></li>
	  <?php if (OS_IsRoot() ) { ?>
	  <li><a href="<?=OS_HOME?>adm/?ban_email">Ban Email</a></li>
	  <?php } ?>
	</ul>
  </li>
  <li><a href="<?=OS_HOME?>adm/?admins">User Roles</a></li>
  <!--// <li><a href="<?=OS_HOME?>adm/?menu">Menu editor</a></li> //-->
  <li><a href="<?=OS_HOME?>adm/?plugins">Plugins</a></li>
  <li><a href="javascript:;">Misc</a>
     <ul>
	 <!--
	   <?php if (OS_IsRoot() ) { ?>
	   <li><a href="<?=OS_HOME?>adm/?admin_logs">Admin logs</a></li>
	   <?php } ?>
	   <li><a href="<?=OS_HOME?>adm/?word_filter">Word Filter</a></li>
	   <li><a href="<?=OS_HOME?>adm/?gamelist">Gamelist</a></li>
	   <li><a href="<?=OS_HOME?>adm/?live_games">Live Games</a></li>
	   <li><a href="<?=OS_HOME?>adm/?remote">Remote Control</a></li>
	   -->
	   <?php if (OS_IsRoot() ) { ?>
	   <li><a href="<?=OS_HOME?>adm/?bnet_pm">BNET PM</a></li>
	   <?php } ?>
	   <li><a href="<?=OS_HOME?>adm/?heroes"><?=$lang["heroes"]?></a></li>
	   <li><a href="<?=OS_HOME?>adm/?items"><?=$lang["items"]?></a></li>
	   <li><a href="<?=OS_HOME?>adm/?tos">Terms of service</a></li>
	  <li><a href="<?=OS_HOME?>adm/?guides">Guides</a></li>
	   <li><a href="<?=OS_HOME?>adm/?optimize_tables">Optimize Tables</a></li>
	 </ul>
  </li>
  
  <li><a href="<?=OS_HOME?>adm/?logout"><?=substr($_SESSION["username"],0,20)?></a>
    <ul>
	  <li><a href="<?=OS_HOME?>adm/?users&amp;edit=<?=$_SESSION["user_id"]?>">Edit Account</a></li>
	  <li><a href="<?=OS_HOME?>">Go to OS&raquo; </a></li>
	  <li><a href="<?=OS_HOME?>adm/?logout">(logout)</a></li>
	</ul>
  </li>
</ul> 

</div>
</div>
</div>
</div>