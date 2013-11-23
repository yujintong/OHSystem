<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

 if (OS_IsRoot() ) {
    $file = "GeoIPCountryWhois.csv";
	 
	if ( isset($_GET["update"]) ) {
   
	
	if ( !file_exists($file) ) echo "<div align='center'><h2>File 'GeoIPCountryWhois.csv' not exists!</h2></div>";
	else {
    
    $sth = $db->prepare("TRUNCATE TABLE oh_geoip ");
    $r = $sth->execute();
	
    $result = file_get_contents($file);
	
	$lines = explode("\n", $result);
	$c=1;
	?><textarea style="width:500px; height:150px;"><?php
	foreach( $lines as $line) {
	  
	  $row = explode(",", $line);
	  
	  if (!empty($row[0])) $startIP = str_replace('"', '', $row[0]); else $startIP ="";
	  if (!empty($row[1])) $endIP   = str_replace('"', '', $row[1]); else $endIP ="";
	  
	  if (!empty($row[2])) $startIPINT   = str_replace('"', '', $row[2]); else $endIP ="";
	  if (!empty($row[3])) $endIPINT   = str_replace('"', '', $row[3]); else $endIP ="";
	  
	  if (!empty($row[4])) $code    = str_replace('"', '', $row[4]); else $code ="";
	  if (!empty($row[5])) $country = str_replace('"', '', $row[5]); else $country ="";
	  
	  echo "$startIP - $endIP ($country - $code)
";
	  
	if ( !empty($startIP) AND !empty($endIP) AND !empty($code) ) 
    $db->insert( "oh_geoip", array(
	"ip_start" => $startIP,
	"ip_end" => $endIP,
	"ip_start_int" => $startIPINT,
	"ip_end_int" => $endIPINT,
	"code" => $code,
	"country" => $country
	));
	  $c++;
	}
	echo "======== Added $c records ========";
	?></textarea><?php
  }
  
  }
  
  //list
  if ( isset($_GET["list"]) ) {
    
	  $sth = $db->prepare("SELECT ip_start FROM oh_geoip WHERE code = 'A1' AND ip_start LIKE('%.0') AND ip_end LIKE('%.255') GROUP BY ip_start LIMIT 500");
	  $result = $sth->execute();

      $AllIP = array();
	  $c = 0;
	   while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
      $ip_part = OS_GetIpRange( $row["ip_start"] ).".";
	  $AllIP[] = $ip_part;
	   }
	   
	   $IPAddr = array_unique($AllIP);
	   ?>
	   <textarea style="width:400px; height: 150px;">
order allow,deny
<?php
	   foreach($IPAddr as $ip) {
	    echo "deny from ".$ip."
";
	   }
	   ?>allow from all</textarea>
	   <div><b>Total:</b> <?=count($IPAddr)?> IP Address</div>
	   <?php
	
  } 
  
  $letter = "";
  $sqlLetter = "";
  
  if ( isset($_GET["letter"]) AND strlen($_GET["letter"]) == 2 ) {
    $letter = strtoupper( $_GET["letter"] );
  }
  ?>
    <div align="center">
	<table>
	<tr>
	<td width="550" style="padding-top:6px;">
	Sort by: 
	<a class="menuButtons" href="<?=OS_HOME?>adm/?geoip">Default</a>
	<a class="menuButtons" href="<?=OS_HOME?>adm/?geoip&amp;sort=code">Code</a>
	<a class="menuButtons" href="<?=OS_HOME?>adm/?geoip&amp;sort=country">Country</a>
	<a class="menuButtons" href="<?=OS_HOME?>adm/?geoip&amp;sort=proxy">Proxy</a>
	<a class="menuButtons" href="<?=OS_HOME?>adm/?geoip&amp;sort=proxyfull">Proxy Full Range</a>
	<a class="menuButtons" href="javascript:;" onclick="showhide('upd')">GeoIP Update</a>
	</td>
	<td>
	<form action="" method="get">
	<input type="hidden" name="geoip"/>
	Code: <input type="text" size="1" maxlength="2" value="<?=$letter?>" name="letter" />
	<input type="submit" class="menuButtons" value="Search" />
	</form>
	</td>
	</tr>
	</table>
	<div id="upd" style="display:none;">
	<?php if (!file_exists( $file)) { ?>
	File "<?=$file?>" not exists. Please upload file into /adm folder.
	<?php } else { 
	if (filesize($file)>=1) $size = filesize($file)/1024; else $size = 0;
	$time = date(OS_DATE_FORMAT, filemtime($file) );
	?>
	<table>
	 <tr>
	   <th>File name</th>
	   <th>Size</th>
	   <th>Last update</th>
	   <th></th>
	 </tr>
	 <tr>
	  <td width="200"><?=$file?></td>
	  <td width="90"><?=ROUND($size,1)?> kb</td>
	  <td width="120"><?=$time?></td>
	  <td>	<a href="javascript:;" onclick="if(confirm('CAUTION: This will take more than 80,000 queries. Are you sure?')) { location.href='<?=OS_HOME?>adm/?geoip&amp;update' }" class="menuButtons">UPDATE FROM CSV</a>
	  
	  <a href="http://dev.maxmind.com/geoip/legacy/geolite/" target="_blank">Download</a>
	  </td>
	 </tr>
	</table>

	<?php } ?>
	</div>
  <?php
  
    if (isset($_GET["sort"]) AND $_GET["sort"] == "proxyfull") {
	?>
	<a class="menuButtons" href="<?=OS_HOME?>adm/?geoip&amp;sort=proxyfull&amp;list">.htaccess</a>
	<?php
	}
  
  $sql ="";
  $proxy = "";
  
  if (!empty($letter) ) {
    $sqlLetter = " WHERE code = '".$letter."' ";
  }
  
  if ( isset($_GET["sort"]) ) {
    
	if ($_GET["sort"] == "code")    $sql.=" ORDER BY code ASC";
	if ($_GET["sort"] == "country") $sql.=" ORDER BY country ASC";
	if ($_GET["sort"] == "proxy")       $proxy =" WHERE code = 'A1' ";
	if ($_GET["sort"] == "proxyfull")   $proxy =" WHERE code = 'A1' AND ip_start LIKE('%.0') AND ip_end LIKE('%.255') ";
  }
  
  $sth = $db->prepare("SELECT COUNT(*) FROM oh_geoip $proxy $sqlLetter LIMIT 1");
  $result = $sth->execute();
  $r = $sth->fetch(PDO::FETCH_NUM);
  $numrows = $r[0];
  $result_per_page = 50;
  $draw_pagination = 1;
  $SHOW_TOTALS = 1;
  include('pagination.php');
  
  $sth = $db->prepare("SELECT * FROM oh_geoip $sqlLetter $proxy $sql LIMIT $offset, $rowsperpage");
  $result = $sth->execute();
  ?>
    
	<table>
    <tr>
	  <th width="95" class="padLeft">IP Start</th>
	  <th width="95">IP End</th>
	  <th width="64">Code</th>
	  <th>Country</th>
	</tr>
   <?php
   while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
   $ip_part = OS_GetIpRange( $row["ip_start"] ).".";
   ?>
   <tr>
     <td><?=$row["ip_start"]?></td>
	 <td><?=$row["ip_end"]?></td>
	 <td><img src="<?=OS_HOME?>img/flags/<?=$row["code"]?>.gif" alt="code" /> <?=$row["code"]?></td>
	 <td><?=$row["country"]?> 
	 <?php if (isset($_GET["sort"]) AND ($_GET["sort"]=="proxy" OR $_GET["sort"]=="proxyfull")) { ?>
	 &nbsp; ( IP Part: <?=$ip_part?> )
	 <?php } ?></td>
   </tr>
	
   <?php } ?>
	</table>
<?php
include('pagination.php');
?>
  </div>
  <?php
 
 } else {
 ?>
 	  <div align="center">
	    <h2>Only root administrators can access this options</h2>
		 <div style="margin-top:200px;">&nbsp;</div>
	  </div>

 <?php
 }
?>