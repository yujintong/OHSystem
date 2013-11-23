<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
?>
<div id="content" class="s-c-x">
<div class="wrapper">   
    <div id="main-column">
     <div class="padding">
      <div class="inner">
	 
    <h2><?=$lang["members"]?></h2>
	<div class="memberlistSort"><?=OS_memberlistSort()?></div>
   <form action="" method="get"> 
   <input type="hidden" name="members" />
   <input 
   type="text" 
   value="<?=$search_members?>" 
   style="height: 26px;" 
   onblur= "if (this.value == '')  {this.search_members = '<?=$search_members?>';}"
   onfocus="if (this.value == '<?=$search_members?>') {this.value = '';}" 
   name="search_members"
   />
   
   <input type="submit" value="<?=$lang["search"]?>" class="menuButtons" />
   <span><?=LettersLink("members", "search_members")?></span>
   </form>
	 
  <table>
    <tr>
	 <th width="72"   class="padLeft"><?=$lang["avatar"] ?></th>
	 <th width="200"><?=$lang["username"] ?></th>
	 <th width="160"><?=$lang["joined"]?></th>
	 <th><?=$lang["user_info"]?></th>
	</tr>
<?php foreach ( $MembersData as $User ) { ?>
    <tr class="row">
	 <td width="72"  class="padLeft font13"><?=ShowUserAvatar ($User["user_avatar"], 64, 64, "", 0, 1) ?></td>
	 <td width="200" class="font13">
	 <?=OS_ShowUserFlag($User["letter"], $User["country"], 160  )?>
	   <a href="<?=OS_HOME?>?member=<?=$User["id"]?>"><b><?=$User["user_name"]?></b></a>
	   <?=OS_IsMemberActive( $User["code"], $lang["unconfirmed_email"]  )?>
	   <?=OS_IsUserGameAdmin( $User["user_level"], 1, "imgvalign floatR", 20, 20 )?>
	   <?=OS_bnet_icon( $User["user_bnet"] ) ?>
	   <?=OS_protected_icon( $User["user_ppwd"], $User["user_bnet"], $lang["protected_account"] ) ?>
	     <?php if ( OS_is_admin() ) { ?>
	     <div><?=$User["user_email"]?></div><?=EditUserLink($User["id"])?>
	     <?php } ?>
	 </td>
	 <td width="160"><?=($User["user_joined"])?></td>
	 <td>
	    <div><b><?=$lang["member_clan"]?></b>: <?=$User["user_clan"]?></div>
	    <div><b><?=$lang["location"]?></b>: <?=$User["user_location"]?></div>
		<div><b><?=$lang["realm"]?></b>:    <?=$User["user_realm"]?></div>
		<div><b><?=$lang["website"]?></b>:  <?=AutoLinkShort($User["user_website"], 'target="_blank"')?></div>
		<div><b><?=$lang["gender"]?></b>:   <?=UserGender($User["user_gender"])?></div>
		<?php if (OS_is_bnet($User["user_bnet"]) ) { ?>
		<div><a class="menuButtons" href="<?=OS_HOME?>?u=<?=$User["user_name"]?>"><?=$lang["bnet_mystats"]?></a></div>
		<?php } ?>
		<?=os_display_custom_fields()?>
	 </td>
	</tr>
<?php } ?>
  </table>
  
<?php
include('inc/pagination.php');
?> 
	  <div style="margin-bottom:64px;">&nbsp;</div> 
     </div>
    </div>
   </div>
  </div>
</div>