<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
$errors = "";

?>
<div align="center">
<h2>Ban Appeals</h2>

<?php
    if ( isset($_POST["answer"]) AND strlen($_POST["answer"])>=2  ) {
      $answer = OS_StrToUTF8( $_POST['answer']); 
	  $answer = PrepareTextDB( strip_tags($answer) );
	  $t = safeEscape( $_GET["t"]);
	  $player = safeEscape( $_GET["edit"]);
	  $sth = $db->prepare("UPDATE ".OSDB_APPEALS." SET resolved_text = '".$answer."', resolved = '".$_SESSION["username"]."' WHERE LOWER(player_name) = LOWER('".$player."') AND added = '".$t."' LIMIT 1");
	  $result = $sth->execute();
	 
	 if ( isset($_POST["email"]) AND $_POST["email"] == 1) {
	 
	  $sth = $db->prepare("SELECT * FROM ".OSDB_USERS." WHERE LOWER(bnet_username) = LOWER('".$player."') LIMIT 1");
	  $result = $sth->execute();
	  $row = $sth->fetch(PDO::FETCH_ASSOC);
	  
	  if ( isset($_SESSION["adm_email_send"]) AND $_SESSION["adm_email_send"]==$row["user_email"]) 
	  $dontSend = 1; //send only one email (when edit)
	  
	  if ( !empty($row["user_email"]) AND !isset($dontSend ) ) {
	  
	    $message = "You have just received a message from ".$_SESSION["username"]."<br />";
		$message.= "<br />~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br />";
		$message.= convEnt($answer);
	    $message.= "<br />~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br />".OS_HOME."";
		require("../inc/class.phpmailer.php");
	    $mail  = new PHPMailer();
	    $mail->CharSet = 'UTF-8';
	    $mail->SetFrom($lang["email_from"], $lang["email_from_full"]);
	    $mail->AddReplyTo($lang["email_from"], $lang["email_from_full"]);
	    $mail->AddAddress($row["user_email"], "");
	    $mail->Subject = "New Private Message";
	    $mail->MsgHTML($message);
		$mail->AltBody = "This is the body in plain text for non-HTML mail clients";
	    $mail->Send();
		
		$_SESSION["adm_email_send"] = $row["user_email"];
		}
	 }
	 
     }
	
	 if ( file_exists("../inc/geoip/geoip.inc") ) {
	 include("../inc/geoip/geoip.inc");
	 $GeoIPDatabase = geoip_open("../inc/geoip/GeoIP.dat", GEOIP_STANDARD);
	 $GeoIP = 1;
	 }

  if ( isset($_GET["del"]) AND isset($_GET["t"]) AND is_numeric($_GET["t"]) ) {
     $del = safeEscape( $_GET["del"]);
	 $t = safeEscape( $_GET["t"]);
	 $sth = $db->prepare("DELETE FROM ".OSDB_APPEALS." 
	 WHERE LOWER(player_name) = LOWER('".$del."') AND added = '".$t."' LIMIT 1");
	 $result = $sth->execute();
  }
  
   if ( isset($_GET["edit"]) AND isset($_GET["t"]) AND is_numeric($_GET["t"]) ) {
	$id = safeEscape( $_GET["edit"]);
	$t = safeEscape( $_GET["t"]);
	 
    if ( isset($_GET["close"]) ) {
	$sth  = $db->prepare("UPDATE ".OSDB_APPEALS." SET status = 1 
	WHERE (player_name) = LOWER('".$id."') AND added = '".$t."' LIMIT 1");
	$result = $sth->execute();
	}
	
	if ( isset($_GET["open"]) ) {
	$sth  = $db->prepare("UPDATE ".OSDB_APPEALS." SET status = 0 
	WHERE (player_name) = LOWER('".$id."') AND added = '".$t."' LIMIT 1");  
	$result = $sth->execute();
	}
	
    if ( isset($_GET["remove_ban"]) ) {
	$date = date("Y-m-d H:i:s", time() );
	$sth = $db->prepare("DELETE FROM ".OSDB_BANS." WHERE LOWER(name) = LOWER('".$id."') ");
	$result = $sth->execute();
	
	$sth  = $db->prepare("UPDATE ".OSDB_APPEALS." SET status = 2 
	WHERE LOWER(player_name) = LOWER('".$id."') AND added = '".$t."' LIMIT 1");  
	$result = $sth->execute();
	}
	
	 $sth = $db->prepare("SELECT b.*, u.user_name 
	 FROM ".OSDB_APPEALS." as b 
	 LEFT JOIN ".OSDB_USERS." as u ON u.user_id = b.user_id
	 WHERE LOWER(player_name) =:player_name AND added =:t LIMIT 1");
	 $sth->bindValue(':player_name', strtolower($id), PDO::PARAM_STR);
	 $sth->bindValue(':t', ($t), PDO::PARAM_INT);
	 $result = $sth->execute();
	 
	 if ( $sth->rowCount()>=1 ) {
	 $row = $sth->fetch(PDO::FETCH_ASSOC);
	 
	 $sth = $db->prepare("SELECT * FROM ".OSDB_BANS." WHERE name ='".$id."' LIMIT 1");
	 //$sth->bindValue(':player_name', strtolower($id), PDO::PARAM_STR);
	 $result = $sth->execute();
	 if ( $sth->rowCount()>=1) {
	 $Banned = 1; 
	 $banRow = $sth->fetch(PDO::FETCH_ASSOC);
	 $BanId = $banRow["id"];
	 $BanName = $banRow["name"];
	 }
	 else {
	 $Banned = 0;
	 $BanId = 0;
	 $BanName = "";
	 }
	 
	if ($GeoIP == 1 ) {
	$Letter   = geoip_country_code_by_addr($GeoIPDatabase, $row["user_ip"]);
	$Country  = geoip_country_name_by_addr($GeoIPDatabase, $row["user_ip"]);
	} 
	 
	 ?>
<table class="Table800px">
	 <tr>
	   <td width="150" class="padLeft"><b>Appeal for:</b></td>
	   <td>
<?php if ($BanId>=1) { ?>
	   <a target="_blank" href="<?=OS_HOME?>?u=<?=($BanName)?>"><span class="banned"><?=$BanName?></span></a> - <b>BANNED</b>
<?php } else { echo '<a target="_blank" href="'.OS_HOME.'?u='.$id.'">'.$id. "</a> - <b>This user is NOT banned</b>"; }?>
	   </td>
	 </tr>
	 <tr>
	   <td width="150" class="padLeft"><b>Appeal by:</b></td>
	   <td><a target="_blank" href="<?=OS_HOME?>?u=<?=($row["user_name"])?>"><span><?=$row["user_name"]?></span></a> <span style="padding-left: 26px;"> <?php if ($GeoIP == 1 AND !empty($Letter) ) { ?><img src="<?=OS_HOME?>img/flags/<?=$Letter?>.gif" class="imgvalign" title="<?=$Country?>" alt="" /><?php } ?> <?=$row["user_ip"]?> ( <?=$Country?> )</span></td>
	 </tr>
	 <tr>
	   <td width="150" class="padLeft"><b>Reason:</b></td>
	   <td><textarea disabled style="width: 500px; height: 110px;"><?=stripslashes(str_replace("<br />", " ", convEnt($row["reason"])))?></textarea></td>
	 </tr>
	 <tr>
	   <td width="150" class="padLeft"><b>Game URL:</b></td>
	   <td><?=($row["game_url"])?></td>
	 </tr>
	 <tr>
	   <td width="150" class="padLeft"><b>Replay URL:</b></td>
	   <td><?=($row["replay_url"])?></td>
	 </tr>
	 <tr>
	   <td width="150" class="padLeft"><b>Added:</b></td>
	   <td><?=date($DateFormat, $row["added"])?></td>
	 </tr>

	 <tr>
	   <td width="150" class="padLeft">Action: </td>
	   <td>
	   <div class="padTop"></div>
	   
	   <div>
<?php if ($Banned  == 0) { ?>
	   <i>This user is NOT banned</i>
<?php } else { ?>
      <a class="menuButtons" onclick="if (confirm('Remove ban - <?=$row["player_name"]?>?') ) { location.href='<?=$website?>adm/?ban_appeals&amp;edit=<?=strtolower($row["player_name"])?>&amp;t=<?=$row["added"]?>&amp;remove_ban' }" href="javascript:;">Remove Ban for "<?=$row["player_name"]?>"</a>
<?php } ?>
<?php if ($row["status"] == 0) { ?>
		  <a class="menuButtons" href="<?=$website?>adm/?ban_appeals&amp;edit=<?=strtolower($row["player_name"])?>&amp;t=<?=$row["added"]?>&amp;close">Close Appeal</a>
<?php } ?>
<?php if ($row["status"] == 1) { ?>
		  <a class="menuButtons" href="<?=$website?>adm/?ban_appeals&amp;edit=<?=strtolower($row["player_name"])?>&amp;t=<?=$row["added"]?>&amp;open">Open Appeal</a>
<?php } ?>
      </div>
	  <form action="" method="post">
      <div style="margin-top:12px;">Answer ( <?=$row["resolved"]?> ) :</div>
	  <div><textarea style="width:430px; height:70px;" name="answer"><?=$row["resolved_text"]?></textarea></div>
       
	   <div><input type="checkbox" value="1" name="email" /> Also send message to user email</div>
       <input type="submit" value="Send answer to <?=$id?>" name="appeal_answer" class="menuButtons" />
       <div class="padTop"></div>
	   </form>
	   </td>
	 </tr>

	  
</table>
	 <?php
	 }
	
	
	}

  $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_APPEALS." LIMIT 1");
  $result = $sth->execute();
  $r = $sth->fetch(PDO::FETCH_NUM);
  $numrows = $r[0];
  $result_per_page = 30;
  
  $draw_pagination = 1;
  $SHOW_TOTALS = 1;
  include('pagination.php');
  
  $ord = "b.added DESC ";
  
  if ( isset($_GET["sort"]) AND $_GET["sort"] == "unsolved" ) $ord = "b.status ASC,  b.added DESC ";
  if ( isset($_GET["sort"]) AND $_GET["sort"] == "solved" )   $ord = "b.status DESC, b.added DESC ";
  
  $sth = $db->prepare("SELECT b.*, u.user_name, ban.name as banname
  FROM ".OSDB_APPEALS." as b 
  LEFT JOIN ".OSDB_USERS." as u ON u.user_id = b.user_id
  LEFT JOIN ".OSDB_BANS." as ban ON ban.name = b.player_name
  ORDER BY $ord 
  LIMIT $offset, $rowsperpage");
  
  $result = $sth->execute();
  
  if ( isset($_GET["page"]) AND is_numeric($_GET["page"]) ) $p = "&amp;page=".(int)$_GET["page"]; else $p = "";
  
 ?>
  Sort by: <a href="<?=OS_HOME?>adm/?ban_appeals&amp;sort=solved">Solved</a> | 
  <a href="<?=OS_HOME?>adm/?ban_appeals&amp;sort=unsolved">Unsolved</a> | 
  <a href="<?=OS_HOME?>adm/?ban_appeals">Report Time</a>
<table class="Table800px">
    <tr>
	  <th width="190" class="padLeft">Appeal for</th>
	  <th width="140" class="padLeft">Appeal by</th>
	  <th width="240">Reason</th>
	  <th width="150">Added</th>
	</tr>
<?php
   while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { ?>
   <tr class="row font13" style="height: 55px;">
     <td>
	 <?php if ($row["status"] == 0) { ?>
	 <img src="<?=OS_HOME?>adm/uncheck.png" alt="*" class="imgvalign" width="16" height="16" />
	 <?php } else { ?>
	 <img src="<?=OS_HOME?>adm/check.png" alt="*" class="imgvalign" width="16" height="16" />
	 <?php } ?>
	   <a href="<?=OS_HOME?>adm/?ban_appeals&amp;edit=<?=strtolower($row["player_name"])?>&amp;t=<?=$row["added"]?><?=$p?>"><span class="sentinel"><b><?=$row["player_name"]?></b></span></a>
	   <div style="float: right" class="font12">
	   <a href="<?=OS_HOME?>adm/?ban_appeals&edit=<?=strtolower($row["player_name"])?>&amp;t=<?=$row["added"]?><?=$p?>"><img src="<?=OS_HOME?>adm/edit.png" alt="*" width="16" height="16" /></a> 
	   <a href="javascript:;" onclick="if (confirm('Delete this appeal?') ) { location.href='<?=OS_HOME?>adm/?ban_appeals&amp;del=<?=strtolower($row["player_name"])?>&amp;t=<?=$row["added"]?><?=$p?>' }" ><img src="<?=OS_HOME?>adm/del.png" alt="*" width="16" height="16" /></a>
	   </div>
	   
	   <?php if (!empty($row["banname"]) ) echo "[banned]"; ?>
	 </td>
	 <td><a class="padLeft" href="<?=OS_HOME?>adm/?users&amp;edit=<?=($row["user_id"])?>"><?=$row["user_name"]?></a></td>
	 <td>
	 <?=limit_words( convEnt($row["reason"]), 12)?>
	 <?php if (!empty($row["resolved_text"]) ) { ?>
	 <div><b><?=$row["resolved"]?>:</b> <?=$row["resolved_text"]?></div>
	 <?php } ?>
	 </td>
	 <td><?=date($DateFormat, $row["added"])?></td>
   </tr>
   <?php } 
   ?>
   </table>
<?php
include('pagination.php');
if ( isset($GeoIP) AND $GeoIP == 1) geoip_close($GeoIPDatabase);
?>

</div>