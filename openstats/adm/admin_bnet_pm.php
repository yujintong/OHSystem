<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
$errors = "";

if (!OS_IsRoot() ) {
?>
    <div align="center">
	<h2>You can't access this page.</h2>
	<a href="<?=OS_HOME?>adm/">Back to Dashboard</a>
	</div>
<?php
} else {

   if ( isset($_GET["pm"]) AND OS_IsRoot() ) {
   
    ?>
	<div align="center">
	<h2><a href="<?=OS_HOME?>adm/?bnet_pm&amp;pm">Private Messages</a></h2>
	</div>
	<?php
	
	if ( isset($_GET["fn"]) AND isset($_GET["fid"]) ) {
	  $fn = $_GET["fn"];
	  $fid = $_GET["fid"];
	  $sth = $db->prepare("DELETE FROM ".OSDB_CUSTOM_FIELDS." 
	  WHERE field_id = '".$fid."' AND field_name='".$fn."' LIMIT 1");
	  $result = $sth->execute();
	  
	  ?>
	<div align="center">
	<h2>Message successfully deleted. <a href="<?=OS_HOME?>adm/?bnet_pm&amp;pm">Refresh</a></h2>
	</div>
	  <?php
	}
   
     $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_CUSTOM_FIELDS." as c WHERE c.field_name LIKE ('%__p.m.%') ");
	 $result = $sth->execute();
	 $r = $sth->fetch(PDO::FETCH_NUM);
	 $numrows = $r[0];
	 $result_per_page = 20;
	 $SHOW_TOTALS = 1;
	 $draw_pagination = 1;
	 include('pagination.php');
	 
	 $sth = $db->prepare("SELECT c.field_id, c.field_name, c.field_value, u.user_name, u.user_id 
	 FROM ".OSDB_CUSTOM_FIELDS." as c 
	 LEFT JOIN ".OSDB_USERS." as u ON u.user_id = c.field_id
	 WHERE c.field_name LIKE ('%__p.m.%') 
	 ORDER BY c.field_name DESC
	 LIMIT $offset, $result_per_page");
	 
	 $result = $sth->execute();
	 ?>
	 <table>
	 <tr>
	    <th>To/From</th><th>Text</th><th>Date</th>
	 </tr>
	 <?php
	 while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	 $dateFor = explode("|", $row["field_name"]);
	 $date = $dateFor[0];
	 $text = limit_words( convEnt($row["field_value"]), 6); 
	 $sendTo = $row["user_name"];
	 $sendID = $row["user_id"];
	 
	 if (isset($_GET["page"]) AND is_numeric($_GET["page"]) ) $page = '&amp;page='.(int)$_GET["page"];
	 else $page = '';
	 
	 $fromID = $dateFor[1];
	 
	  $status = substr($row["field_name"],strlen($row["field_name"])-1, strlen($row["field_name"]) );
	  $MFrom = "";
	  if ( $status == 1) $info = '<span style="float:right;">[read]</span>';
	  if ( $status == 0) $info = '<span style="float:right;"><b>[unread]</b></span>';
	  
		$sth2 = $db->prepare("SELECT * FROM ".OSDB_USERS." WHERE user_id = '$fromID' ");
		$result = $sth2->execute();
		$row2 = $sth2->fetch(PDO::FETCH_ASSOC);
		$from = $row2["user_name"];
		$MFrom = '<span style="font-size:11px;">from: <a href="'.OS_HOME.'?member='.$row2["user_id"].'"><i>'.$from.'</i></a></span>';
	  
	 if ( isset($_GET["pm"]) AND !empty($_GET["pm"]) AND $_GET["pm"] == $row["field_name"]) {
		
	    $text = AutoLinkShort(convEnt($row["field_value"])).' <div></div> <a href="'.OS_HOME.'adm/?bnet_pm&amp;pm'.$page.'">&laquo; back</a>';
		$style= 'style="border: 4px solid #ccc; padding: 5px;"';
	 } else {
     $style = "style='padding: 5px;'";
	 $text = '<a href="'.OS_HOME.'adm/?bnet_pm&amp;pm='.$row["field_name"].$page.'#'.$row["field_name"].'">'.limit_words( convEnt($row["field_value"]), 15).'</a>';
	 }
	    ?>
		<tr class="row">
		  <td width="200"><a class="anchor" name="<?=$row["field_name"]?>"></a><b>to:</b> <a href="<?=OS_HOME?>?member=<?=$sendID?>"><b><?=$sendTo?></b></a>
		  <div><?=$MFrom?></div>
		  </td>
		  <td width="550">
		  <div <?=$style?>>
		  <?=$text?>
		  <span style="float:right;"><a href="javascript:;" onclick="if (confirm('Delete message?')) { location.href='<?=OS_HOME?>adm/?bnet_pm&pm&fn=<?=$row["field_name"]?>&fid=<?=$row["field_id"]?>' }">&times;</a></span>
		  </div>

		  </td>
		  <td><?=date(OS_DATE_FORMAT, $date)?></td>
		</tr>
		<?php
	 }
	 ?>
	 </table>
	 <?php
	 	 include('pagination.php');
	 ?><div>&nbsp;</div><a href="<?=OS_HOME?>adm/?bnet_pm" class="menuButtons">BNet PM</a><div>&nbsp;</div><?php
   } else {

   if ( isset($_GET["purge_pm"])  AND OS_IsRoot() ) {
    $sth = $db->prepare("TRUNCATE TABLE ".OSDB_BNET_PM." ");
    $result = $sth->execute();
	?>
    <div align="center">
	<h2>All messages are deleted successfully.</h2>
	<a href="<?=OS_HOME?>adm/?bnet_pm">Refresh page</a> to continue.
	</div>
	<?php
   }

  $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_BNET_PM."");
  $result = $sth->execute();
  $r = $sth->fetch(PDO::FETCH_NUM);
  $numrows = $r[0];
  $result_per_page = 30;
  
  $draw_pagination = 1;
  $SHOW_TOTALS = 1;
  include('pagination.php');
  
   $sth = $db->prepare("SELECT * FROM ".OSDB_BNET_PM." ORDER BY id DESC LIMIT $offset, $rowsperpage");
   $result = $sth->execute();
   ?>
   <?php if ($_SESSION["level"]>=10) { ?>
   <a href="<?=OS_HOME?>adm/?bnet_pm&amp;pm" class="menuButtons">Private Messages</a>
   <?php } ?>
   <table>
    <tr>
	  <th width="32">#</th>
	  <th width="250">Message</th>
	  <th width="120">From</th>
	  <th width="120">To</th>
	  <th width="120">Time</th>
	</tr>
   <?php
   while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
    $id = $row["id"];
	$from = $row["m_from"];
	$to = $row["m_to"];
	$time = $row["m_time"];
	$read = $row["m_read"];
	$text = trim($row["m_message"]);
	
	if ($read==0) $status = '[unread]'; else $status = '';
	?>
	<tr>
	  <td class="padLeft"><?=$id?></td>
	  <td><?=$status." ".$text?></td>
	  <td><?=$from?> <a style="float:right;" title="Send a message" href="<?=OS_HOME?>adm/?remote&amp;m=<?=$from?>">[send]</a></td>
	  <td><?=$to?> <a style="float:right;" title="Send a message" href="<?=OS_HOME?>adm/?remote&amp;m=<?=$to?>">[send]</a></td>
	  <td><?=date(OS_DATE_FORMAT, strtotime($time))?></td>
	</tr>
	<?php
   }
   ?>
   </table>
   
   <div class="padTop padBottom">
    <a href="javascript:;" onclick="if (confirm('Delete all messages?')) { location.href='<?=OS_HOME?>adm/?bnet_pm&purge_pm' }" class="menuButtons" >Delete All</a>
   </div>
   <?php
   
include('pagination.php');
  }
}
?>