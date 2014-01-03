<?php
/**
 * Copyright [2013-2014] [OHsystem]
 * 
 * OHSystem is free software: You can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Please save the copyrights and notifications on the footer.
 * This file is part of DOTA OPENSTATS.
 * 
 * You can contact the developers on: ohsystem-public@googlegroups.com
 * or join us directly here: https://groups.google.com/d/forum/ohsystem-public
 * 
 * Visit us also on http://ohsystem.net/ and keep track always of the latest
 * features and changes.
 * 
 * 
 * This is modified from GHOST++: http://ghostplusplus.googlecode.com/
 * Official GhostPP-Forum: http://ghostpp.com/
*/

if (strstr($_SERVER['REQUEST_URI'], basename(__FILE__) ) ) {header('HTTP/1.1 404 Not Found'); die; }

  $time = microtime();
  $time = explode(' ', $time);
  $time = $time[1] + $time[0];
  $start = $time;
  
   include('config.php');
   require_once('inc/common.php');

   require_once('lang/'.OS_LANGUAGE.'.php');
   require_once('inc/integration.php');
   if(isset($DBDriver) AND $DBDriver == "mysql" ) require_once('inc/class.database.php'); else
   require_once('inc/class.db.PDO.php'); 
   require_once('inc/db_connect.php');


   os_init();
   require_once('inc/sys.php');  
   require_once('plugins/index.php');
   
   os_start();
   if ( file_exists('themes/'.OS_THEMES_DIR.'/functions.php') )
   include('themes/'.OS_THEMES_DIR.'/functions.php');

   include('themes/'.OS_THEMES_DIR.'/header.php');
   include('themes/'.OS_THEMES_DIR.'/menu.php');

   include('inc/template.php');
   
   os_after_content();
   include('themes/'.OS_THEMES_DIR.'/footer.php');
?>
