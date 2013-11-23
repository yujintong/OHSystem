<?php

if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

  $time = microtime();
  $time = explode(' ', $time);
  $time = $time[1] + $time[0];
  $finish = $time;
  $total_time = round(($finish - $start), 4);
  
  
  function show_debug($os_debug = 0  ) {
    if ($os_debug == 1) {
	global $time, $finish, $total_time;
	
	$db = new db("mysql:host=".OSDB_SERVER.";dbname=".OSDB_DATABASE."", OSDB_USERNAME, OSDB_PASSWORD); 
	?>
	<div>Generated in: <?=$total_time?> sec. | Total: <?=$db->QueryCount?> queries.</div>
	<?php
	}
  }
  
  function OS_GetFirstImage($text) {
  $c = 0;
  $_imgs  = array();
  $dom = new DOMDocument();
  @$dom->loadHTML( convEnt($text) );
  $xpath = new DOMXPath($dom);
  $entries = $xpath->query('//img');
  $default = OS_HOME."themes/".OS_THEMES_DIR."/images/dota_banner.png";
  
    foreach($entries as $e)
    {
    $_imgs[$c] =  $e->getAttribute("src"); $c++; break;
	}
	
  if (!empty($_imgs[0]) ) return $_imgs[0];
  else return $default;
}

  AddEvent("os_head", "OS_LiveGames");
  
  function OS_LiveGames() {
  ?>
  <?php if (isset($_GET["live_games"]) OR !$_GET) { ?>
<?php /* <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script><?php */ ?>
<script src="<?=OS_HOME?>themes/live.js"></script>
<?php } ?>
  <?php
  }
?>