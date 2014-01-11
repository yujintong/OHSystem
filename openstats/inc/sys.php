<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }


$DefaultHomeTitle = $HomeTitle;
$DefaultHomeDescription = $HomeDesc;

foreach($_POST as $key => $value) {
    $_POST[$key] = FilterData($value);
}

foreach($_GET as $key => $value) {
    $_GET[$key] = FilterData($value);
}

include(OS_PAGE_PATH."registration_login_page.php"); 
include(OS_PAGE_PATH."add_comment_page.php"); 

  //Update last_login on active sessions after 15 min.
  if ( os_is_logged() AND isset($_SESSION["logged"]) AND $_SESSION["logged"]+60*15<=time() ) {
    $LastLogin = $db->update(OSDB_USERS, array("user_last_login" => (int)time() ), 
		                         "user_id = '".(int)$_SESSION["user_id"]."'");
  }
    
	 //If "u" is not a number, found in the database this user (if exists)
	 if ( isset($_GET["u"]) AND !is_numeric( $_GET["u"]) ) {
	    $uid = OS_StrToUTF8( trim($_GET["u"]) );
	    $sql = "";
		if ( isset($_GET["game_type"]) ) { 
		$game_type = (int)$_GET["game_type"];
		$sql = "AND alias_id = '".$game_type."' ";
		}
		
		$sth = $db->prepare("SELECT * FROM ".OSDB_ALIASES." WHERE default_alias = 1 LIMIT 1");
	    $result = $sth->execute();
		
		if ( $sth->rowCount()>=1) {
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		$sql.= " AND alias_id = '".$row["alias_id"]."' ";
		}
		
		$sth = $db->prepare("SELECT *
	    FROM ".OSDB_STATS." as s WHERE s.player = :player $sql ORDER BY id DESC LIMIT 1");
		$sth->bindValue(':player', $uid, PDO::PARAM_STR);
		$result = $sth->execute();
	  
		if ( $sth->rowCount()>=1 ) {
		   $row = $sth->fetch(PDO::FETCH_ASSOC);
		   header( 'location: '.OS_HOME.'?u='.$row["id"] ); die;
		}
	 }
	 
	 $MenuClass = array();
	 $MenuClass["home"] = "menulink";
     $MenuClass["games"] = "menulink";
	 $MenuClass["top"] = "menulink";
	 $MenuClass["bans"] = "menulink";
	 $MenuClass["admins"] = "menulink";
	 $MenuClass["warn"] = "menulink";
	 $MenuClass["safelist"] = "menulink";
	 $MenuClass["heroes"] = "menulink";
	 $MenuClass["members"] = "menulink";
	 $MenuClass["misc"] = "menulink";
	 $MenuClass["items"] = "menulink";
	 $MenuClass["profile"] = "menulink";
	 $MenuClass["about_us"] = "menulink";
	 $MenuClass["fb"] = "menulink";
	 $MenuClass["live"] = "";
	 
	 if ( isset( $_GET["login"]) OR isset($_GET["register"]) ) $MenuClass["profile"] = "active";
	 if ( isset( $_GET["live_games"])) $MenuClass["live"] = "active";
     if ( isset( $_GET["action"]) AND $_GET["action"] == "facebook") $MenuClass["fb"] = "active";
	 
  if ( isset($_GET["games"]) OR isset($_GET["u"]) )            include(OS_PAGE_PATH."games_page.php"); 
  if ( isset($_GET["game"]) AND is_numeric($_GET["game"]) )    include(OS_PAGE_PATH."single_games_page.php"); else
  if ( isset($_GET["live_games"]) )                            include(OS_PAGE_PATH."live_games_page.php"); else
  if ( isset( $_GET["top"]) AND $TopPage==1)                   include(OS_PAGE_PATH."top_page.php"); else
  if ( isset( $_GET["u"]) )                                    include(OS_PAGE_PATH."user_stats_page.php"); else
  if ( isset($_GET["search"]) AND strlen($_GET["search"])>=2 ) include(OS_PAGE_PATH."search_page.php"); else
  if ( isset($_GET["bans"]) AND $BansPage == 1)                include(OS_PAGE_PATH."bans_page.php"); else
  if ( isset( $_GET["admins"]) AND $AdminsPage == 1 )          include(OS_PAGE_PATH."admins_page.php"); else
  if ( isset( $_GET["warn"]) AND $WarnPage == 1 )              include(OS_PAGE_PATH."warn_page.php"); else
  if ( isset($_GET["safelist"]) AND $SafelistPage == 1)        include(OS_PAGE_PATH."safelist_page.php"); else
  if ( isset($_GET["heroes"]) AND $HeroesPage == 1)            include(OS_PAGE_PATH."heroes_page.php"); else
  if ( isset($_GET["hero"]) AND $HeroesPage == 1)              include(OS_PAGE_PATH."single_hero_page.php"); else
  if ( isset($_GET["guides"]) AND $GuidesPage == 1)            include(OS_PAGE_PATH."guides_page.php"); else
  if ( isset($_GET["members"]) AND $MemberListPage == 1 )      include(OS_PAGE_PATH."memberlist_page.php"); else
  if ( isset( $_GET["member"]) AND is_numeric($_GET["member"]))include(OS_PAGE_PATH."single_member_page.php"); else
  if ( isset($_GET["items"]) AND $ItemsPage == 1)              include(OS_PAGE_PATH."items_page.php"); else
  if ( isset($_GET["item"]) AND $ItemsPage == 1)               include(OS_PAGE_PATH."single_item_page.php"); else
  
  if (  OS_is_home_page())                                     include(OS_PAGE_PATH."home_page.php"); else
  if ( isset($_GET["profile"]) AND os_is_logged() )            include(OS_PAGE_PATH."user_profile_page.php"); else
  if ( isset($_GET["profile"]) AND !os_is_logged() )           { header("location: ".OS_HOME."?login"); die; } else
  if ( isset($_GET["ban_report"]) AND $BanReports == 1)        include(OS_PAGE_PATH."ban_reports_page.php"); else
  if ( isset($_GET["ban_appeal"]))                             include(OS_PAGE_PATH."ban_appeals_page.php");
   
   include("inc/compare_players.php");
   
   AddEvent("os_add_meta","OS_MetaVersion");
   
   function OS_MetaVersion() {
   ?>
<meta name="generator" content="OpenStats <?=OS_VERSION?>" />
<?php
   }
?>
