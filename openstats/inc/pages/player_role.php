<?php
//Player ranks and descriptions

$PlayerRankData = array();

//Lets see player penalties first
$PlayerRankData["warned"] = $Warned;
$PlayerRankData["penalties"] = $TotalPP;
$PlayerRankData["FavoriteHero"] = $FavoriteHero["original"];
//Overall (total stats)
$PlayerRankData["games"] = $UserData[0]["totalgames"];
$PlayerRankData["wins"] = $UserData[0]["totalwins"];
$PlayerRankData["losses"] = $UserData[0]["totallosses"];
$PlayerRankData["kills"] = $UserData[0]["totalkills"];
$PlayerRankData["deaths"] = $UserData[0]["totaldeaths"];
$PlayerRankData["assists"] = $UserData[0]["totalassists"];
$PlayerRankData["creeps"] = $UserData[0]["totalcreeps"];
$PlayerRankData["denies"] = $UserData[0]["totaldenies"];
$PlayerRankData["neutrals"] = $UserData[0]["totalneutrals"];
$PlayerRankData["zerodeaths"] = $UserData[0]["totalzerodeaths"];

$PlayerRankData["winslosses"] = $UserData[0]["totalwinslosses"];
$PlayerRankData["kdratio"] = $UserData[0]["totalkd"];
$PlayerRankData["kpg"] = $UserData[0]["totalkpg"]; //kills per game
$PlayerRankData["dpg"] = $UserData[0]["totaldpg"]; //deaths per game
$PlayerRankData["apg"] = $UserData[0]["totalapg"]; //assists per game
$PlayerRankData["cpg"] = $UserData[0]["totalckpg"]; //creeps per game
$PlayerRankData["cdpg"] = $UserData[0]["totalcdpg"]; //denies per game

//By this data we will create player rank and role
?>