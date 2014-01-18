<?php  
if (strstr($_SERVER['REQUEST_URI'], basename(__FILE__) ) ) {header('HTTP/1.1 404 Not Found'); die; }

//We will disable session handler when cron is active
if ( !isset($NoSession) ) {
$OSAppID = 'openstats_1';

$SN = session_name( $OSAppID );
if (!isset($_SESSION)) { session_start(); }

}

$server = 'localhost';
$username = 'root';
$password = '';
$database = 'ohsystem';

$website = 'http://localhost/ohsystem/openstats/';
$HomeTitle = 'OpenStats 4';
$HomeDesc = 'OpenStats - OpenHosting System';
$HomeKeywords = 'dota, games, heroes, players, best players, top list, top players, statistics';

$default_language = 'english';
$DateFormat = 'd.m.Y, H:i';
$DefaultStyle = 'ghost';

$BotName = 'MyBot1';

$LogoText = 'DotA OpenStats 4';

//FACEBOOK APP SETUP
//Enable Login via Facebook
$FBLogin = '1';
//FB Application ID
$FacebookAppID = 'FB Application ID';
//FB Application Secret
$FacebookAppSecret = 'FB Application Secret';

//Forum and WP integration
$PHPbb3Integration = '0';
$SMFIntegration = '0';
$WPIntegration = '0';

//Path to phpbb3 forum
$phpbb_forum = 'forum/';
$phpbb_forum_url = 'http://localhost/openstats/forum/';
//Path to SMF forum
$smf_forum = 'smf/';
$smf_forum_url = 'http://localhost/openstats/smf/';

//Path to wordpress
$wp_path = '../';
$wp_url = 'http://localhost/wordpress/';

//$HeroVote = '1'; //not working - removed
$HeroVote = ""; //disabled
$HeroVoteShow = '20';

$HeroFileExt = 'gif';

$ReplayLocation = 'replays';

$TopPageStartYear = '2013';

$GamesPerPage = '50';
$TopPlayersPerPage = '50';
//Heroes AND Items per page
$HeroesPerPage = '300';
$ItemsPerPage = '20';
$MembersPerPage = '20';

$NewsPerPage = '5';
$CommentsPerPage = '10';
//Limit words on news on homepage // 0 - to display full text
$NewsWordLimit = '40';

// Sort user comments: 1 - ID , 2 - newer , 3 - older
$SortComments = '1'; 

//Auto link in comments: 1 - allow, 2 - show plain text, 3 - remove all links
$AutoLinkComments = '1';
//If links removed, replace all LINKS with following text:
$AutoLinkTextReplace = '';
//Display full or short urls: 1 - full, 2 - short 
$AutoLinkFull = '0';

//Show hero stats on user page (favorite hero, hero with most kills, deaths, assists...)
$ShowUserHeroStats = '0';

$UserRegistration = '1';
$AllowComments = '1';

//Allow users to upload avatar image
$AllowUploadAvatar = '1';
//Max image size in pixels (default: 320px, quality: 85)
$MaxImageSize = '320';
$ImageQuality = '85';

$RecentGames = '1';
$TotalRecentGames = '5';

$ScoreStart = '0';
$ScoreWins = '5';
$ScoreLosses = '3';
$ScoreDisc = '10';

//Enable/Disable Ban reports and appeals
$BanReports = '0';
$BanAppeals = '0';

//Add report user link on user page
$ReportUserLink = '0';

//After how much time a user can write next report
$BanReportTime = '180';

//Penalty points - expire time (in days)
$PPExpireDays = '181';

//How many games to update at once
$updateGames = '50';
//CronJob Update Games
$updateGamesCron = '10';

//When user register: 1 - user must confirm registration via email, 0 - instant activation
$UserActivation = '0';

//Get heroes data from playdota website
$PlayDotaHeroes = '0';

$MaxPaginationLinks = '2';

//Show fastest and longest game won
$ShowLongFastGameWon = '0';

$TopPage = '1';
$HeroesPage = '1';
$ItemsPage = '1';
$BansPage = '1';
$WarnPage = '0';
$AdminsPage = '1';
$SafelistPage = '0';
$MemberListPage = '1';
$GuidesPage = '0';
$AboutUs = '0';

$ShowMembersCountry = '1';

//Allow comparing players
$ComparePlayers = '1';
$MaxPlayersToCompare = '10';

//Show or hide (1/0) empty slots (empty username, or left time = 0 ... )
$HideEmptySlots = '1';

//Minimum game duration > 5*60 = 5 min (or 300 sec) 
//Only games with defined time (longer then $MinDuration ) will be counted in the statistics

$MinDuration = '60*3';

//Time a player leaves before the end of the game, which loses points ($ScoreDisc)
//Eg. if the user leaves the game 5 minutes before game end he will receive negative points -10
// $LeftTimePenalty = '300; in seconds (300 = 5 min), default
$LeftTimePenalty = '300';

$LiveGames = '1';
$LiveGamesTotalLogs = '20000';
//Max. number of games to display
$LiveGamesLimit = '50'; 
//Log FILTERS  0 - off | 1 - always display | 2 - only registered users
$LiveShowAegis = '1';
$LiveShowSwaps = '1';
$LiveShowHeroPicks = '1';
$LiveShowPlayerLevels = '1';
$LiveShowKills = '1';
$LiveShowAssists = '1';
$LiveShowSuicides = '1';
$LiveShowRunes = '2';
$LiveShowTowerKills = '1';
$LiveShowRoshanKill = '1';

$TimeZone = 'Europe/Belgrade';

//Gamelist patch support
$GameListPatch = '1';

//Save admin logs - can be viewed only by root admins
$SaveAdminLogs = '1';

//Points that will ranked members receive every 24h
$DailyPoints = '5';

//** CUSTOM ADDINGS **//
// Custom Mapname for maps who havent '%dota%' in the mapname.
$DefaultMap = "";

//Display online/offline players on top page (req. extra queries)
$OnlineOfflineOnTopPage = '0';

//Enable/disable info about time to create page and total queries on every page
$pageGen = '1'; 
//Enable error reportings
$_debug = '1';

$OS_INSTALLED = '0';
?>
