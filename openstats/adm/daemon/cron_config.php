<?php
if (strstr($_SERVER['REQUEST_URI'], basename(__FILE__) ) ) { header('HTTP/1.1 404 Not Found'); die; }

//Load config (and before that disable session handler)
$NoSession = '1';  
include("../../config.php");

//Specify password for cron access
$CronPassword = 'mypassword';
//Update every 30 sec
$CronUpdate = '60';
//Max number of queries every X ($CronUpdate) seconds
$MaxQueries = '100';

$StatsCountryUpdate = '1';
$BanIPUpdate = '1';
//Check and verify user bans (or remove if player is not banned)
$CheckUserBans = '1';
$MaxCronLogs = '10000';
$RemoveOldLiveGames = '1';
//0 disabled, 1 basic, 2 full details
$CronReportDetails = '1';
?>