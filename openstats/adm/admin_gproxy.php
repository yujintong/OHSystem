<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

// TESTER : 80.133.136.199
$sql = "";
$Info = "";
$error = "";
$button = "Add record";

if (!empty($_GET["search"])) $search = strip_tags( trim($_GET["search"]));
else $search= "";

if (!isset($s)) $s ="";

if ( isset($_GET["delete"]) AND is_numeric($_GET["delete"]) ) {
  $del = (int)$_GET["delete"];
  $sth = $db->prepare("DELETE FROM ".OSDB_GPROXY." WHERE id = '".$del."' ");
  $result = $sth->execute();
  $Info = '<div>Record successfully deleted</div>';
  
  OS_AddLog($_SESSION["username"], "[os_gproxy] Deleted Gproxy record (#".$del.")");
}
?>
<div align="center"> 
<h2>GProxy Users</h2>
<?php
if (!empty($Info)) echo $Info;

	    if ( isset($_GET["edit"]) ) {
		
		$id = safeEscape( (int) $_GET["edit"] );
		$sth = $db->prepare("SELECT * FROM ".OSDB_GPROXY." WHERE id= '".$id."' ");
        $result = $sth->execute();
        while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { 
        $player = $row["player"];
		$ip = $row["ip"];
        }
	} else { $player = ""; $ip = "";  }

  if ( isset($_GET["add"]) ) {
  
    if ( isset($_POST["add_record"]) ) {
	
	  $player = strip_tags( strtolower(trim($_POST["player"])));
	  $ip = strip_tags( trim($_POST["ip"]));
	  
	  if ( strlen($player)<=2 AND strlen($ip)<=2 ) $error.="<div>At least one field must be filled</div>";
	  
	  if (empty($error)) {
	  
	    if ( isset($_GET["edit"]) ) {
		   $id = safeEscape( (int) $_GET["edit"] );
		   $upd = $db->prepare("UPDATE ".OSDB_GPROXY." SET player = '".$player."', ip='".$ip."' 
		   WHERE id = '".$id."' ");
           $result = $upd->execute();
		   
		   OS_AddLog($_SESSION["username"], "[os_gproxy] Edited Gproxy record: ".$player." (#".$id.")");
		   
		} else {
		   $upd = $db->prepare("INSERT INTO ".OSDB_GPROXY." (player, ip, addded, added_by) VALUES('".$player."', '".$ip."', '".date("Y-m-d H:i:s")."', '".$_SESSION["username"]."')");
           $result = $upd->execute();
		   
		   OS_AddLog($_SESSION["username"], "[os_gproxy] Added Gproxy record (".$player.")");
		}
	  
	  }
	
	}
  
  
  if ( isset($_GET["edit"]) ) {
    $button = "Edit Record";
 }  
 
  if (!empty($error)) echo $error;
  ?>
  <?php if (!isset($_GET["edit"])) { ?><div><b>Add a new record</b></div><?php } else { ?>
  <div><b>Edit Record</b></div><?php } ?>
  <form action="" method="post">
  <table>
    <tr>
	  <td width="100">Player</td>
	  <td><input type="text" value="<?=$player?>" name="player" /></td>
	</tr>
    <tr>
	  <td>IP</td>
	  <td><input type="text" value="<?=$ip?>" name="ip" /></td>
	</tr>
    <tr>
	  <td></td>
	  <td>
	  <input type="submit" value="<?=$button?>" name="add_record" /> 
	  <a href="<?=OS_HOME?>adm/?gproxy">Cancel</a>
	  </td>
	</tr>
  </table>
  </form>
  <?php
  
  }
?>

<table>
<tr>
<td>
Search: <input type="text" value="<?=$search?>" name="search" id="search_player" />

<select name="" id="gproxy">
  <option <?=$s?> value="">Show ALL</option>
<?php if (isset($_GET["sort"]) AND $_GET["sort"] == "players") $s='selected="selected"'; else $s=""; ?>
 <option <?=$s?> value="sort=players">Show only players</option>
<?php if (isset($_GET["sort"]) AND $_GET["sort"] == "ip") $s='selected="selected"'; else $s=""; ?>
 <option <?=$s?> value="sort=ip">Show only IP</option>
<?php if (isset($_GET["sort"]) AND $_GET["sort"] == "iprange") $s='selected="selected"'; else $s=""; ?>
 <option <?=$s?> value="sort=iprange">Show only IP Ranges</option>
</select>

<input type="button" onclick="location.href='<?=OS_HOME?>adm/?gproxy&amp;search='+search_player.value+'&amp;'+gproxy.value" value="Show" />
</td>

<td>
  <a class="menuButtons" href="<?=OS_HOME?>adm/?gproxy&amp;add">Add GProxy User or IP</a>
</td>

</tr>
</table>

<table>
<tr>
  <th width="190">Player</th>
  <th width="160">Action</th>
  <th width="120">IP/Range</th>
  <th width="120">Added</th>
  <th>Added By</th>
</tr>
<?php
  
  if ( isset($_GET["sort"]) ) {
    if ( $_GET["sort"] == "players" ) $sql.=" AND player!='' ";
	if ( $_GET["sort"] == "ip" )      $sql.=" AND ip!='' ";
	if ( $_GET["sort"] == "iprange" ) $sql.=" AND ip LIKE (':%') ";
  }

  $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_GPROXY." 
  WHERE id>= 1 $sql");
  $result = $sth->execute();

  $r = $sth->fetch(PDO::FETCH_NUM);
  $numrows = $r[0];
  $result_per_page = 30;
  $draw_pagination = 1;
  include('pagination.php');
  
  $sth = $db->prepare("SELECT * FROM ".OSDB_GPROXY." 
  WHERE id>= 1 $sql ORDER BY id DESC 
  LIMIT $offset, $rowsperpage");
  $result = $sth->execute();
  while ($row = $sth->fetch(PDO::FETCH_ASSOC)) { ?>
  <tr>
    <td><a target="_blank" href="<?=OS_HOME?>?u=<?=$row["player"]?>"><?=$row["player"]?></a></td>
	<td style="font-size:12px;">
	<a href="<?=OS_HOME?>adm/?gproxy&amp;add&amp;edit=<?=$row["id"]?>">Edit</a>
	| <a href="javascript:;" onclick="if(confirm('Delete record?')) { location.href='<?=OS_HOME?>adm/?gproxy&amp;delete=<?=$row["id"]?>' }">x</a>
	<?php if (!empty($row["ip"]) ) { ?>
	| <a href="">Check IP</a>
	<?php } ?>
	</td>
	<td><?=$row["ip"]?></td>
	<td><?=date( OS_DATE_FORMAT, strtotime($row["added"]))?></td>
	<td><?=$row["added_by"]?></td>
  </tr>
  <?php } ?>
</table>
