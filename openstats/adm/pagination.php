<?php
/* 
Ivan Antonijević (ivan.anta [at] gmail.com), 2012
*/
if (strstr($_SERVER['REQUEST_URI'],basename(__FILE__))){header('HTTP/1.1 404 Not Found');die;}

$prefix = "adm/";
$strana = "&amp;page=";
$end = "";

if ( isset($_GET["games"]) )  $prefix.="?games";
if ( isset($_GET["top"]) )    $prefix.="?top";
if ( isset($_GET["bans"]) )   $prefix.="?bans";
if ( isset($_GET["admins"]) ) $prefix.="?admins";
if ( isset($_GET["posts"]) )  $prefix.="?posts";
if ( isset($_GET["comments"]) )  $prefix.="?comments";
if ( isset($_GET["safelist"]) )  $prefix.="?safelist";
if ( isset($_GET["notes"]) )     $prefix.="?notes";
if ( isset($_GET["ban_reports"]) )     $prefix.="?ban_reports";
if ( isset($_GET["ban_appeals"]) )     $prefix.="?ban_appeals";
if ( isset($_GET["heroes"]) )     $prefix.="?heroes";
if ( isset($_GET["items"]) )     $prefix.="?items";
if ( isset($_GET["users"]) )     $prefix.="?users";
if ( isset($_GET["guides"]) )    $prefix.="?guides";
if ( isset($_GET["players"]) )   $prefix.="?players";
if ( isset($_GET["live_games"]) ) { $prefix.="?live_games"; $end = "#live"; }
if ( isset($_GET["bnet_pm"]) )   $prefix.="?bnet_pm";
if ( isset($_GET["pm"]) )        $prefix.="&amp;pm";
if ( isset($_GET["find_leavers"]) )        $prefix.="&amp;find_leavers";
if ( isset($_GET["admin_logs"]) )    $prefix.="?admin_logs";
if ( isset($_GET["pp"]) )            $prefix.="?pp";
if ( isset($_GET["geoip"]) )         $prefix.="?geoip";
if ( isset($_GET["letter"]) ) $prefix.="&amp;letter=".substr($_GET["letter"],0,2);

if ( isset($_GET["country"]) ) $prefix.="&amp;country=".substr($_GET["country"],0,2);

if ( isset($_GET["player"]) ) $prefix.="&amp;player=".($_GET["player"]);
if ( isset($_GET["realm"]) ) $prefix.="&amp;realm=".($_GET["realm"]);

if ( isset($_GET["show"]) ) {
   if ($_GET["show"] == "unparsed") $prefix.="&amp;show=unparsed";
   if ($_GET["show"] == "ips")      $prefix.="&amp;show=ips";
}

if ( isset($_GET["post"]) ) $prefix.="&amp;post=".(int)$_GET["post"];
if ( isset($_GET["show_all"]) ) $prefix.="&amp;show_all";

if ( isset($_GET["showPP"]) ) $prefix.="&amp;showPP";

if ( isset($_GET["search"]) AND isset($s) ) $prefix.="?search=".$s;
if ( isset($_GET["search_bans"]) AND isset($search_bans) ) $prefix.="&amp;search_bans=".$search_bans;
if ( isset($_GET["search_users"]) AND isset($search_users) ) $prefix.="&amp;search_users=".$search_users;
if ( isset($_GET["search_logs"]) AND isset($search_logs) ) $prefix.="&amp;search_logs=".$search_logs;
if ( isset($_GET["log_admin"]) AND isset($search_admin) ) $prefix.="&amp;log_admin=".$search_admin;
if ( isset($_GET["ip_range"]) ) $prefix.="&amp;ip_range=".(int) strip_tags($_GET["ip_range"]);

if ( isset($_GET["sort"]) ) $prefix.="&amp;sort=".safeEscape( $_GET["sort"]);
if ( isset($_GET["uid"]) )  $prefix.="&amp;uid=".safeEscape( (int) $_GET["uid"]);
if ( isset($_GET["u"]) )    { $prefix.="?u=".safeEscape( (int) $_GET["u"]).""; $end ="#game_history"; }

if ( isset($_GET["game_type"]) ) $prefix.="&amp;game_type=".(int)$_GET["game_type"];

              $rowsperpage = $result_per_page;
              $totalpages = ceil($numrows / $rowsperpage);
              if (isset($_GET['page']) && is_numeric($_GET['page'])) {
                  $currentpage = (int)$_GET['page'];
              } else {
                  $currentpage = 1;
              }
              if ($currentpage > $totalpages) {
                  $currentpage = $totalpages;
              }
              if ($currentpage < 1) {
                  $currentpage = 1;
              }
              if ($totalpages <= 1) {
                  $totalpages = 1;
              }

              $offset = ($currentpage - 1) * $rowsperpage;
              if (isset($_GET['page']) AND is_numeric($_GET['page'])){
                          $current_page = safeEscape($_GET['page']);
                          }

                          if (!isset($current_page)) {
                              $current_page = 1;
                          }
              if (!isset($MaxPaginationLinks) ) $range = 5;
			  else  $range = $MaxPaginationLinks;
			  
              if ($range >= $totalpages) {
                  $range = $totalpages;
              }
			  
			  if ($current_page > $totalpages) {$current_page = $totalpages;}
			  
if ($draw_pagination == 1 AND $totalpages>=2) { 
             ?>
	  <div class="pagination2"> 
	   <?php
              if ($currentpage > 1) {
                  ?><a class="button orange" href="<?=$website?><?=$prefix?>"><span>&laquo;</span></a><?php
                  $prevpage = $currentpage - 1;
                  ?><a class="button orange" href="<?=$website?><?=$prefix?><?=$strana?><?=$prevpage?><?=$end?>"><span><</span></a><?php
              }
              for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
                  if (($x > 0) && ($x <= $totalpages)) {
                      if ($x == $currentpage) {
                         ?>
					  <a class="button orange" href="javascript:;"><span class="active"><?=$x?></span></a><?php
                      } else {
                          ?>
					  <a class="button orange" href="<?=$website?><?=$prefix?><?=$strana?><?=$x?><?=$end?>"><span><?=$x?></span></a><?php
                      }
                  }
              }
              if ($currentpage != $totalpages) {
                  $nextpage = $currentpage + 1;
                 ?>
				 <a class="button orange" href="<?=$website?><?=$prefix?><?=$strana?><?=$nextpage?><?=$end?>"><span>></span></a>
				 
				 <a class="button orange" href="<?=$website?><?=$prefix?><?=$strana?><?=$totalpages?><?=$end?>"><span><?=$totalpages?></span></a><?php
              }
             ?>   
			<?php if (isset($SHOW_TOTALS) ) { ?>
			 &nbsp;
			 <span class="totals"><?=$lang["page"]?> <b><?=$current_page?></b> <?=$lang["pageof"]?> <?=$totalpages?> 
			 (<?=$numrows?> <?=$lang["total"]?>)
			 </span>
			 <?php } ?>
			 </div>
			 <?php
}