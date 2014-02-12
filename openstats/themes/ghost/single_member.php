<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
?>
<div id="content" class="s-c-x">
  <div class="wrapper">   
    <div id="main-column">
     <div class="padding">
      <div class="inner">
	<?php foreach($MemberData as $Member) { ?>  
	<table>
	     <tr>
		    <td width="220"><img src="<?=$Member["avatar"]?>" alt="user_avatar" width="220" /></td>
			<td>
			<h2>
			<?=OS_bnet_icon( $Member["user_bnet"], 32, 32, "imgvalign padLeft" )?>
			<?=$Member["user_name"]?> 
			<?=OS_ShowUserFlag($Member["letter"], $Member["country"], 160 )?>
			<?=OS_IsMemberActive( $Member["code"], "Waiting for activation", 1, "imgvalign", 24, 24 )?>
			<?=OS_IsUserGameAdmin( $Member["user_level"], 1, "imgvalign", 24, 24 )?>
			<!--<?=OS_user_admin_icon( $Member["user_level"], $lang["member_root"], $lang["member_admin"], 24, 24, "imgvalign" )?>-->
			<?=OS_protected_icon( $Member["user_ppwd"], $Member["user_bnet"], $lang["protected_account"], 24, 24, "imgvalign" ) ?>
			</h2>
			<?php if ( OS_is_admin() ) { ?>
	        <div class="padLeft"><?=$Member["user_email"]?> | <?=EditUserLink( $Member["user_id"] )?></div>
	        <?php } ?>
			<?php if (OS_is_bnet($Member["user_bnet"]) ) { ?>
			<div class="padLeft padTop"><a class="menuButtons" href="<?=OS_HOME?>?u=<?=$Member["bnet_username"]?>"><?=$lang["bnet_mystats"]?></a></div>
			<?php } ?>
			</td>
		 </tr>
	</table>
	
	   <table>
	     <tr>
		   <td width="130" class="padLeft"><b><?=$lang["member_comments"]?>:</b></td> 
		   <td><?=$Member["total_comments"]?></td>
		 </tr>
	     <tr>
		   <td width="130" class="padLeft"><b><?=$lang["member_registered"]?>:</b></td> 
		   <td><?=$Member["user_joined"]?></td>
		 </tr>
	     <tr>
		   <td width="130" class="padLeft"><b><?=$lang["member_clan"]?>:</b></td> 
		   <td><?=($Member["user_clan"])?></td>
		 </tr>
	     <tr>
		   <td width="130" class="padLeft"><b><?=$lang["member_location"]?>:</b></td> 
		   <td><?=($Member["user_location"])?></td>
		 </tr>
	     <tr>
		   <td width="130" class="padLeft"><b><?=$lang["member_realm"] ?>:</b></td> 
		   <td><?=($Member["user_realm"])?></td>
		 </tr>
	     <tr>
		   <td width="130" class="padLeft"><b><?=$lang["member_website"]?>:</b></td> 
		   <td><?=AutoLinkShort($Member["user_website"] , 'target="_blank"' )?></td>
		 </tr>
	     <tr>
		   <td width="130" class="padLeft"><b><?=$lang["member_gender"]?>:</b></td> 
		   <td><?=UserGender($Member["user_gender"])?></td>
		 </tr>
	     <tr>
		   <td width="130" class="padLeft"><b><?=$lang["member_last_login"] ?>:</b></td> 
		   <td><?php if ($Member["user_last_login"]>=1) echo $Member["user_last_login"]?></td>
		 </tr>
		 <?=os_custom_user_fields()?>
	   </table>
	   
	   <?php if ( $Member["user_bnet"] ) { ?>
	   <h4 class="padLeft"><?=$lang["stats"]?> </h4>
	    <table>

		<tr>
			<td width="130" class="padLeft"><b><?=$lang["games"]?>:</b></td> 
			<td><a href="<?=OS_HOME?>?u=<?=$Member["uid"]?>#game_history"><?=$Member["games"]?></a></td>
		</tr>
		<tr>
			<td width="130" class="padLeft"><b><?=$lang["wins"]?>/<?=$lang["losses"]?>:</b></td> 
			<td>
			<span class="won"><?=$Member["wins"]?></span>/
			<span class="lost"><?=$Member["losses"]?></span> 
			(<?=$Member["winslosses"]?>%) 
			<a class="menuButtons" href="<?=OS_HOME?>?u=<?=$Member["uid"]?>"><?=$lang["bnet_mystats"]?></a>
			</td>
		</tr>
		<tr>
			<td width="130" class="padLeft"><b><?=$lang["score"]?>:</b></td> 
			<td><?=$Member["score"]?></td>
		</tr>
		
		<tr>
			<td width="130" class="padLeft"><b>Points:</b></td> 
			<td><?=$Member["points"]?></td>
		</tr>
		
		</table>
	   <?php } ?>
		<h4 class="padLeft"><?=$lang["latest_comments"]?></h4>
		<table>
		<?php
        foreach($MemberComments as $Comments) {
		?>
		<tr>
		 <td width="220"><a <?=ShowToolTip( $Comments["text"],  OS_HOME.'img/arrow.png', 290, 21, 15 )?> href="<?=OS_HOME?>?post_id=<?=$Comments["post_id"]?>#comments"><?=$Comments["news_title"]?></a></td>
		 <td><?=$Comments["date"]?></td>
		</tr>
		<?php
		  }
		?>
		</table>
	   


<?php } ?>	
	 </div>
    </div>
   </div>
  </div>
</div>