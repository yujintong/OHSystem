<?php
//Plugin: Top By Country
//Author: Ivan
//Display most players from country

if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

$PluginEnabled = '1';

if ($PluginEnabled == 1) {
   
   AddEvent("os_add_menu_misc",  "OS_TopByCountry"); 
   
   if ( OS_GetAction("top_country") ) AddEvent("os_content",  "OS_ShowTopCountry");
   
   function OS_TopByCountry() {
    ?>
	<li><a href="<?=OS_HOME?>?action=top_country">Top Players by Country</a></li>
	<?php
   }
   
   function OS_ShowTopCountry() {
    global $db;
   	//TOP BY COUNTRIES
    $sth = $db->prepare("SELECT COUNT( country_code ) AS total, country_code 
    FROM  `".OSDB_STATS."` 
    GROUP BY country_code
    ORDER BY total DESC 
    LIMIT 100");
  
    $result = $sth->execute();
   	$c=0;
    $TopCountriesData = array();
	if ( empty($Countries) ) include("inc/countries.php");
	
    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	
	$TopCountriesData[$c]["counter"] = ($c+1);
    $TopCountriesData[$c]["total"] = $row["total"];
	$TopCountriesData[$c]["code"] = $row["country_code"];
	if ( isset($Countries[ strtoupper($row["country_code"]) ]) )
	$TopCountriesData[$c]["country"] = $Countries[ strtoupper($row["country_code"]) ];
	else 
	$TopCountriesData[$c]["country"] = $row["country_code"];
	$c++;
    }
?>
<a name="top_countries"></a>
<div id="content" class="s-c-x">
<div class="wrapper">   
    <div id="main-column">
     <div class="padding">
      <div class="inner">  
	  <h2>Total players by country</h2>
	  <div>
	  <table>
	  <tr>
	    <th>#</th>
	    <th>Country</th>
		<th>Total players:</th>
	  </tr>
	  <?php
	  foreach ($TopCountriesData as $TopCountry) {
	  ?>
	  <tr>
	    <td width="32"><?=$TopCountry["counter"]?></td>
	    <td width="195">
		<?=OS_ShowUserFlag( $TopCountry["code"], $TopCountry["country"] )?> 
		<?=$TopCountry["country"]?>
		</td>
		<td><a href="<?=OS_HOME?>?top&amp;country=<?=$TopCountry["code"]?>"><?=$TopCountry["total"]?> players</a></td>
	  </tr>
	  <?php
	  }
	  
  $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_STATS." 
  WHERE id>=1 LIMIT 1");
  $result = $sth->execute();
  
  $r = $sth->fetch(PDO::FETCH_NUM);
  $TotalStatsPlayers = number_format($r[0]);
	  ?>
	   </table>
	   </div>
	   <div class="padLeft">
	   There are total of <b><?=$TotalStatsPlayers?></b> players.
	   </div>
     </div>
    </div>
   </div>
  </div>
</div>
   <?php
   
}

}
?>