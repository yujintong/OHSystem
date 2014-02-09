<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
?>
<div id="content" class="s-c-x">
<div class="wrapper">   
    <div id="main-column">
     <div class="padding">
      <div class="inner">
	    <h2><?=$lang["moderator_panel"]?></h2>
		
	<form action="" method="get">
	<table>
	<tr>
	  <td style="font-size:13px;">
	   <a href="<?=OS_HOME?>?moderator" class="menuButtons">MCP</a> &middot; 
	   <a href="<?=OS_HOME?>?moderator&amp;option=addban" class="menuButtons">Add ban</a>
	   <a href="<?=OS_HOME?>?moderator&amp;option=bans" class="menuButtons">Bans</a> &middot; 
	   <a href="<?=OS_HOME?>?moderator&amp;option=pp&amp;add" class="menuButtons">Add PP</a>
	   <a href="<?=OS_HOME?>?moderator&amp;option=pp" class="menuButtons">Penalty Points</a> &middot; 
	   <a href="<?=OS_HOME?>?moderator&amp;option=roles" class="menuButtons">Roles</a> &middot; 
	   <a href="<?=OS_HOME?>?moderator&amp;option=ip" class="menuButtons">IP</a> 
	 </td>
	 <td>
	 <?php if ( isset($_GET["option"]) AND $_GET["option"] == "pp" ) { ?>
	   <input type="hidden" value="" name="moderator" />
	   <input type="hidden" value="pp" name="option" />
	   <input type="text" value="<?=$SearchValue?>" name="spp" /> 
	   <input type="submit" value="Search PP" class="menuButtons" />
	   <a href="<?=OS_HOME?>?moderator&amp;option=pp" class="menuButtons">Reset</a>
	 <?php } else { ?>
	   <input type="hidden" value="" name="moderator" />
	   <input type="hidden" value="bans" name="option" />
	   <input type="text" value="<?=$SearchValue?>" name="sban" /> 
	   <input type="submit" value="Search bans" class="menuButtons" />
	   <a href="<?=OS_HOME?>?moderator&amp;option=bans" class="menuButtons">Reset</a>
	 <?php } ?>
	 </td>
	</tr>
	</table>
	</form>
		<?php
		if (!isset($_GET["option"])) {
		?>
		<div style="margin-top:4px;">&nbsp;</div>
		<table>
		  <tr>
		    <td width="100">Total Games:</td>
			<td><a href="<?=OS_HOME?>?games"><?=$TotalRankedGames?></a></td>
		  </tr>
		  <tr>
		    <td width="100">Total Bans:</td>
			<td><a href="<?=OS_HOME?>?bans"><?=$TotalBans?></a></td>
		  </tr>
		  <tr>
		    <td width="100">Total Players:</td>
			<td><a href="<?=OS_HOME?>?top"><?=$TotalRankedUsers ?></a></td>
		  </tr>
		  
		  <tr>
		    <td width="140">Banned Countries:</td>
			<td>
			<?=$CountryList?>
			</td>
		  </tr>
		  
		</table>
		<div style="margin-top:220px;">&nbsp;</div>
		<?php } ?>
		
		<?php
		if ( isset($_GET["option"]) AND $_GET["option"] == "addban" ) {
		  ?>
		  <form action="" method="post">
		  <table>
		    <tr>
			 <td width="100">Player:</td>
			 <td>
			 <input type="text" value="<?=$BanPlayer?>" name="player" size="45" style="height:24px;" />
			 <div style="font-size:12px;">Banned by: <b><?=$BanAdmin?></b></div>
			 </td>
			</tr>
		    <tr>
			 <td  width="100">Server:</td>
			 <td>
			 <input  id="server" type="text" value="<?=$BanServer?>" name="server" size="45" style="height:24px;" />
			 <a href="javascript:;" class="menuButtons" onclick="SetDateField('europe.battle.net', 'server')" >europe</a>
			 <a href="javascript:;" class="menuButtons" onclick="SetDateField('useast.battle.net', 'server')" >useast</a>
			 <a href="javascript:;" class="menuButtons" onclick="SetDateField('uswest.battle.net', 'server')" >uswest</a>
			 <a href="javascript:;" class="menuButtons" onclick="SetDateField('Garena', 'server')" >Garena</a>
			 </td>
			</tr>
		    <tr>
			 <td  width="100">Reason:</td>
			 <td><input type="text" value="<?=$BanReason?>" name="reason" size="45" style="height:24px;" /></td>
			</tr>
		    <tr>
			 <td  width="100">Gamename:</td>
			 <td>
			 <input type="text" value="<?=$BanGamename?>" name="gamename" size="45" style="height:24px;" />
			  <?php if (!empty($BansDate)) { ?>Ban Date: <?=$BansDate?><?php } ?>
			 </td>
			</tr>
		    <tr>
			 <td  width="100">Expire:</td>
			 <td>
	<?php
	if (empty($BanExpire) AND isset($DropDown) ) {
	?>
	  <select name="expire">
	   <option value="<?=date("Y-m-d H:i:00", time()+3600*10 )?>">+10h</option>
	   <option value="<?=date("Y-m-d H:i:00", time()+3600*24 )?>">+1 day</option>
	   <option selected="selected" value="<?=date("Y-m-d H:i:00", time()+3600*48 )?>">+2 days</option>
	   <option value="<?=date("Y-m-d H:i:00", time()+3600*72 )?>">+3 days</option>
	   <option value="<?=date("Y-m-d H:i:00", time()+3600*24*5 )?>">+5 days</option>
	   <option value="<?=date("Y-m-d H:i:00", time()+3600*24*7 )?>">+7 days</option>
	   <option value="<?=date("Y-m-d H:i:00", time()+3600*24*30 )?>">+1 month</option>
	   <option value="<?=date("Y-m-d H:i:00", time()+3600*24*60 )?>">+2 months</option>
	   <option value="<?=date("Y-m-d H:i:00", time()+3600*24*90 )?>">+3 months</option>
	   <option value="<?=date("Y-m-d H:i:00", time()+3600*24*180 )?>">+6 months</option>
	   <option value="0000-00-00 00:00:00">[permanent]</option>
	  </select>
	  <?php } else { ?>
	  
	  <input name="expire" id="expiredate" style="width: 180px; height: 28px;" type="text" value="<?=$BanExpire?>" />
	  <span style="font-size:13px; vertical-align:middle;"><?=OS_ExpireDateRemain( $BanExpire )?></span>
	<div class="padTop padBottom">
	 Set: 
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24 )?>', 'expiredate')" >+1 day</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*48 )?>', 'expiredate')" >+2 days</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*72 )?>', 'expiredate')" >+3 days</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*5 )?>', 'expiredate')" >+5 days</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*7 )?>', 'expiredate')" >+7 days</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*30 )?>', 'expiredate')" >+1 month</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*60 )?>', 'expiredate')" >+2 months</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*90 )?>', 'expiredate')" >+3 months</a>
<a href="javascript:;" class="menuButtons" onclick="SetDateField('<?=date("Y-m-d H:i:00", time()+3600*24*30*12 )?>', 'expiredate')" >+1 year</a>
<div>YYYY-MM-DD H:i:s (<b>Leave blank</b> if you do not want ban expire)</div>
	</div>
	
	  <?php } ?>
			 </td>
			</tr>
	<?php
    if ( !isset($_GET["edit"]) ) {
	?>
	<tr>
	  <td>Add PP:</td>
	  <td><input type="text" size="1" value="1" name="pp" /></td>
	</tr>
	<?php
	}
    ?>	
			
			<tr>
			 <td></td>
			 <td><input type="submit" value="<?=$Button?>" name="add_ban" /></td>
			</tr>
			
		  </table>
		  </form>
		  <?php
		}
		
		if ( isset($_GET["option"]) AND $_GET["option"] == "bans" AND !empty($BansData) ) {
		
		?>
		<table style="font-size:12px;">
		 <tr>
		   <th width="160">Player</th>
		   <th width="55">Option</th>
		   <th width="160">Expire</th>
		   <th width="190">Game</th>
		   <th>Realm</th>
		   <th>Banned by</th>
		 </tr>
		 <?php
		 foreach ( $BansData as $ban ) {
		 ?>
		 <tr>
		   <td width="160">
		     <?=OS_ShowUserFlag( $ban["letter"], $ban["country"], 175 )?> 
		     <a href="<?=OS_HOME?>?u=<?=$ban["name"]?>&amp;mcp"><b><?=$ban["name"]?></b></a>
			 <div style="font-size:11px;"><?=$ban["date"]?></div>
		   </td>
		   <td>
		   <a href="<?=OS_HOME?>?moderator&amp;option=addban&amp;edit=<?=$ban["id"]?>">edit</a> | 
		   <a href="javascript:;" onclick="if(confirm('Delete Ban?')) { location.href='<?=OS_HOME?>?moderator&amp;option=bans&amp;delete=<?=$ban["id"]?><?php if (!empty($Page)) { ?>&amp;page=<?=$Page?><?php } ?>&amp;player=<?=$ban["name"]?>' }">X</a></td>
		   <td width="160">
		   <?php if ($ban["expiredate_db"]!='0000-00-00 00:00:00') { ?>
		   <div><?=OS_ExpireDateRemain( $ban["expiredate_db"] ) ?></div>
		   <div><?=$ban["expiredate"]?></div>
		   <?php } else { ?><div class="perm_ban">Permanent</div><?php } ?>
		   </td>
		   <td width="190"><?=$ban["gamename"]?></td>
		   <td><?=$ban["server"]?></td>
		   <td><?=$ban["admin"]?></td>
		 </tr>
		 <?php
		 }
		 ?>
		</table>
		<?php
		include('inc/pagination.php');
		}
		?>
		
		
		<?php
		if ( isset($_GET["option"]) AND $_GET["option"] == "pp" AND !empty($PPData) ) {
		
		if ( (isset($_GET["edit"]) AND is_numeric($_GET["edit"])) OR isset($_GET["add"])  ) {
		   ?>
		 <form action="" method="post">
		 <table>
		   <tr>
		    <td>Player</td>
			<td><input type="text" value="<?=$PP_PlayerName?>" name="player_name" size="35" style="height:25px;" /></td>
		   </tr>
		   <tr>
		    <td>PP</td>
			<td><input type="text" value="<?=$PP_Value?>" name="pp" size="1" style="height:25px;" /></td>
		   </tr>
		   <tr>
		    <td>Reason</td>
			<td><input type="text" value="<?=$PP_Reason?>" name="reason" size="50" style="height:25px;" /></td>
		   </tr>
		   <tr>
		    <td></td>
			<td><input type="submit" value="<?=$Button?>" name="add_pp" class="menuButtons" /></td>
		   </tr>
		 </table>
		 </form>
		   <?php
		}
		
		?>
		<table style="font-size:12px;">
		 <tr>
		   <th width="24">PP</th>
		   <th width="160">Player</th>
		   <th width="95">Option</th>
		   <th width="160">Reason</th>
		   <th width="190">Date</th>
		   <th>Admin</th>
		 </tr>
		 <?php
		 foreach ( $PPData as $pp ) {
		 ?>
		 <tr>
		  <td>+<?=$pp["pp"]?></td>
		  <td><a target="_blank" href="<?=OS_HOME?>?u=<?=$pp["player_name"]?>"><b><?=$pp["player_name"]?></b></td>
		  <td>
           <a href="<?=OS_HOME?>?moderator&amp;option=pp&amp;edit=<?=$pp["id"]?><?php if (!empty($Page)) { ?>&amp;page=<?=$Page?><?php } ?>">edit</a> | 
		   <a href="<?=OS_HOME?>?moderator&amp;option=pp&amp;spp=<?=$pp["player_name"]?>">search</a> | 
		   <a href="javascript:;" onclick="if(confirm('Delete PP?')) { location.href='<?=OS_HOME?>?moderator&amp;option=pp&amp;delete=<?=$pp["id"]?><?php if (!empty($Page)) { ?>&amp;page=<?=$Page?><?php } ?>&amp;player=<?=$pp["player_name"]?>' }">X</a>
		  </td>
		  <td><?=$pp["reason"]?></td>
		  <td><?=$pp["offence_time"]?></td>
		  <td><?=$pp["admin"]?></td>
		 </tr>
		 <?php
		 }
		 ?>
		</table>
		<?php
		include('inc/pagination.php');
		}
		?>
		

		<?php
		if ( isset($_GET["option"]) AND $_GET["option"] == "roles" AND !empty($RoleData) ) {
		 ?>
		 <table style="font-size:13px;">
		   <tr>
		     <th>Username</th>
			 <th>Bnet</th>
			 <th><a href="<?=OS_HOME?>?moderator&amp;option=roles">Role</a></th>
			 <th><a href="<?=OS_HOME?>?moderator&amp;option=roles&amp;sort=expire">Expire</a></th>
		   </tr>
		   <?php foreach ($RoleData as $data) {
		   if ($data["user_level_expire"] != '0000-00-00 00:00:00' AND !empty($data["user_level_expire"]) )
		   $expire = ' ('.date( "d.m.Y", strtotime($data["user_level_expire"]) ).")";
		   else $expire = ""; 
		   
		   ?>
		   <tr>
		    <td width="150"><a href="<?=OS_HOME?>?member=<?=$data["id"]?>"><?=$data["user_name"]?></a></td>
			<td width="180">
			<img src="<?=OS_HOME?>img/bnet.png" width="16" class="imgvalign" />
			<a target="_blank" href="<?=OS_HOME?>?u=<?=$data["bnet_username"]?>"><?=$data["bnet_username"]?></a>
			</td>
			<td width="180">
			<?=OS_IsUserGameAdmin( $data["user_level"] )?> 
			<?=OS_ShowUserRole( $data["user_level"] )?> ( <?=$data["user_level"]?> )</td>
			<td>
     <?php if ($data["user_level_expire"]!='0000-00-00 00:00:00') { ?>	 
	 <?=OS_ExpireDateRemain($data["user_level_expire"])?>
	 <?php } else { ?>never<?php } ?>
	  <?=$expire?>
			</td>
		   </tr>
		   <?php
		   }
		   ?>
		 </table>
		 <?php
		 include('inc/pagination.php');
		} 
?>
		
		<?php
		if ( isset($_GET["option"]) AND $_GET["option"] == "ip" ) {
		?>
		<form action="" method="get">
		<input type="hidden" name="moderator" />
		<input type="hidden" name="option" value="ip" />
		<table>
		  <tr>
		    <td width="180">Search IP:</td>
			<td><input type="text" name="search_ip" value="<?=$IPSearch?>" /></td>
		  </tr>
		  <tr>
		    <td width="180"></td>
			<td><input type="submit" value="Search" class="menuButtons" /></td>
		  </tr>
		</table>
		</form>
		
		<?php 
        if (!empty($IPData)) {
		?>
		<table style="font-size:13px;">
		<tr>
		  <th width="150">Player / Reason</th>
		  <th width="100">IP</th>
		  <th width="165">Expire</th>
		  <th width="180">Game Name</th>
		  <th width="120">Date</th>
		  <th>Banned by</th>
		</tr>
		<?php
		   foreach($IPData as $data) {
		   ?>
		   <tr>
		     <td>
			 <?=OS_ShowUserFlag( $data["letter"], $data["country"], 175 )?>  
			 <a href="<?=OS_HOME?>?u=<?=$data["name"]?>"><b><?=$data["name"]?></b></a>
			 <div style="background-color: #EAF3FF"><?=$data["reason"]?></div>
			 </td>
			 <td><?=$data["ip"]?></td>
			 <td><?php if ( date( strtotime($data["expiredate"]) )>1990 ) { ?><?=OS_ExpireDateRemain( $data["expiredate"] )?><?php } else {  ?><span class="perm_ban">Permanent</span><?php } ?></td>
			 <td>
			 <?=$data["gamename"]?>
			 </td>
			 <td><?=$data["date"]?></td>
			 <td><?=$data["admin"]?></td>
		   </tr>
		   <?php
		   }
		 ?>
		 </table>
		 <?php
		}
		?>
		<?php
		
		}
?>
    <script type="text/javascript">
	function toggle(source) {
    checkboxes = document.getElementsByName('checkbox[]');
    for(var i in checkboxes)
    checkboxes[i].checked = source.checked;
    }
	
	function SetDateField(datetime, fieldID) {
		  document.getElementById(fieldID).value = datetime;
	}
	</script>
	
     </div>
    </div>
   </div>
  </div>
</div>