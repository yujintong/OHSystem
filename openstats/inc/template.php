<?php
   os_content();
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

  //PREMIUM
  if ( isset($_GET["premium"]) ) include(OS_PAGE_PATH."premium/index.php"); else 
  
  //MODERATOR PAGE
  if ( isset($_GET["moderator"]) ) include(OS_CURRENT_THEME_PATH."moderator.php"); else 

  //LIVE GAMES
  if ( isset($_GET["live_games"]) ) include(OS_CURRENT_THEME_PATH."livegames.php"); else 
 
  //MEMBER
  if ( isset($_GET["member"]) ) include(OS_CURRENT_THEME_PATH."single_member.php"); else 

  //GAMES
  if ( isset($_GET["games"]) AND isset($GamesData) AND !empty($GamesData) )
  include(OS_CURRENT_THEME_PATH.'/games.php');
  else
  //SINGLE GAME
  if ( isset($_GET["game"]) AND isset($GameData) AND !empty($GameData) ) 
  include(OS_CURRENT_THEME_PATH.'/single_game.php');
 else
  //TOP STATS
  if ( isset($_GET["top"]) AND isset($TopData) AND !empty($TopData) ) {
  include(OS_CURRENT_THEME_PATH.'/top.php');
  }
  else
  //USER DATA
  if ( isset($_GET["u"]) AND isset($UserData) AND !empty($UserData) ) {
  include(OS_CURRENT_THEME_PATH.'/single_user.php');
  }
  else
  //SEARCH
  if ( isset($_GET["search"]) AND isset($s) ) {
  include(OS_CURRENT_THEME_PATH.'/search.php');
  }
  else
  //BANS
  if ( isset($_GET["bans"]) AND isset($BansData) ) {
  include(OS_CURRENT_THEME_PATH.'/bans.php');
  }
  else
  //ADMINS
  if ( isset( $_GET["admins"]) AND $AdminsPage == 1 AND isset($AdminsData) AND !empty($AdminsData) ) {
  include(OS_CURRENT_THEME_PATH.'/admins.php');
  }
  else
  //WARN
  if ( isset($_GET["warn"]) AND isset($BansData) ) {
  include(OS_CURRENT_THEME_PATH.'/warn.php');
  }
  else
  //Safelist
  if ( isset($_GET["safelist"]) AND isset($SafelistData) AND $SafelistPage == 1) {
  include(OS_CURRENT_THEME_PATH.'/safelist.php');
  }
  else
  if ( isset($_GET["members"]) AND isset($MemberListPage) AND $MemberListPage == 1) {
  include(OS_CURRENT_THEME_PATH.'/memberlist.php');
  }
  else
  //GAMELIST PATCH
  if ($GameListPatch == 1 AND isset($LiveGamesData) ) {
     include(OS_CURRENT_THEME_PATH.'/gamelist.php');
  }
   //NEWS
  if ( isset( $NewsData ) AND !empty( $NewsData ) ) {
  include(OS_CURRENT_THEME_PATH.'/recent_games.php');
  include(OS_CURRENT_THEME_PATH.'/news.php');
  }
  else
  //HEROES
  if ( isset( $HeroesData ) AND !empty( $HeroesData ) ) {
  if (file_exists(OS_CURRENT_THEME_PATH.'/heroes_gridview.php')) include(OS_CURRENT_THEME_PATH.'/heroes_gridview.php');
  else include(OS_CURRENT_THEME_PATH.'/heroes.php');
  }
  else
  //GUIDES
  if ( isset( $GuidesData ) AND !empty( $GuidesData ) ) {
  include(OS_CURRENT_THEME_PATH.'/guides.php');
  }
  else
  //ITEMS
  if ( isset( $ItemsData ) AND !empty( $ItemsData ) AND isset($_GET["items"] ) ) {
  include(OS_CURRENT_THEME_PATH.'/items.php');
  }
  else
  //ITEM
  if ( isset( $ItemData ) AND !empty( $ItemData ) AND isset($_GET["item"]) ) {
  include(OS_CURRENT_THEME_PATH.'/single_item.php');
  }
  else
  //HERO
  if ( isset($_GET["hero"]) ) {
  include(OS_CURRENT_THEME_PATH.'/single_hero.php');
  }
  else
  //LOGIN
  if ( isset($_GET["login"]) AND !is_logged() AND $UserRegistration == 1) {
  include(OS_CURRENT_THEME_PATH.'/login.php');
  }
  else
  //FB
  if ( isset($_GET["fb"]) AND !is_logged() AND $UserRegistration == 1) {
  include('inc/fb/connect.php');
  }
  else
  //PROFILE
  if ( isset($_GET["profile"]) AND is_logged() ) {
  include(OS_CURRENT_THEME_PATH.'/user_profile.php');
  }
  else 
  if ( isset($_GET["ban_appeal"]) AND $BanAppeals ==1) {
  include(OS_CURRENT_THEME_PATH.'/ban_appeal.php');
  }
  else 
  if ( isset($_GET["ban_report"]) AND $BanReports ==1) {
  include(OS_CURRENT_THEME_PATH.'/ban_report.php');
  }
  else 
  if ( isset($_GET["tos"]) ) {
  OS_ShowTOS();
  } else 
  //VOTE - HEROES
  if ( isset($_GET["vote"]) AND isset($HeroVote) AND $HeroVote == 1 ) {
  include('inc/addons/vote.php');
  } 
  else
  //FAME
  if ( isset($_GET["fame"]) AND $Fame_Shame_Pages == 1 ) {
  include(OS_CURRENT_THEME_PATH.'/fame.php');
  }
  else
  //SHAME
  if ( isset($_GET["shame"]) AND $Fame_Shame_Pages == 1 ) {
  include(OS_CURRENT_THEME_PATH.'/shame.php');
  }
  else
  if ( isset($_GET["404"]) ) {
  include(OS_CURRENT_THEME_PATH.'/404.php');
   } else 
   if ( isset($_GET["compare_players"]) ) include(OS_CURRENT_THEME_PATH.'/compare_players.php');
   ?>
