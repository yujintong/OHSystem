<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
?>
<div id="content" class="s-c-x">
<div class="wrapper">   
    <div id="main-column">
     <div class="padding">
      <div class="inner">
  
<div style="margin-bottom: 12px;">
  <form action="" method="get">
  <input type="hidden" name="bans" />
   <input 
   type="text" 
   value="<?=$search_bans?>" 
   style="height: 26px;" 
   onblur= "if (this.value == '')  {this.value = '<?=$search_bans?>';}"
   onfocus="if (this.value == '<?=$search_bans?>') {this.value = '';}" 
   name="search_bans"
   />
   <input type="submit" value="<?=$lang["search"]?>" class="menuButtons" />
   <span><?=LettersLink("bans", "search_bans")?></span>
   </form>
</div>

<?=os_display_custom_fields()?>

  <table>
   <tr>
     <th width="180" class="padLeft"><?=$lang["player"] ?></th>
	 <th width="200"><?=$lang["expire"]?></th>
	 <th width="180"><?=$lang["game_name"]?> / <?=$lang["reason"] ?></th>
	 <th width="130"><?=$lang["date"]?></th>
	 <th width="120"><?=$lang["bannedby"]?></th>
   </tr>
  <?php
  foreach ($BansData as $Ban) {
  ?>
  <tr class="row">
    <td width="180" class="padLeft font12">
	  <?=OS_ShowUserFlag( $Ban["letter"], $Ban["country"] )?>
	  <a href="<?=OS_HOME?>?u=<?=strtolower($Ban["name"])?>"><?=$Ban["name"]?></a>
	  <?php if (is_logged() AND isset($_SESSION["level"] ) AND $_SESSION["level"]>=9 ) { ?>
	  <a style="float: right; font-size:11px; padding-right: 5px;" href="<?=OS_HOME?>adm/?bans&amp;edit=<?=$Ban["id"]?>">Edit</a>
	  <?php } ?>
	</td>
	 <td width="200" class="ban_reason">
	    <div><?=OS_ExpireDateRemain($Ban["expiredate"])?></div>
		<div><?=OS_is_ban_perm($Ban["expiredate"], $lang["permanent_ban"])?></div>
        <div><?=OS_ban_expired($Ban["expiredate"], "", "" )?></div>
	 </td>
	 <td width="180" class="font12">
	    <b><?=$Ban["gamename"]?></b>
		<div><span <?=ShowToolTip( $Ban["reason_full"], '', 180, 0, 0 )?>><?=$Ban["reason"]?></span>&nbsp;</div>
	 </td>
	 <td width="130" class="font12">
	 <?=$Ban["date"]?>
	 </td>
	 <td width="120" class="font12"><?=$Ban["admin"]?></td>
  </tr>
  <?php
  }
  ?>
  </table>
     </div>
    </div>
   </div>
  </div>
</div>
  <?php
  $SHOW_TOTALS = 1;
  include('inc/pagination.php');
?>