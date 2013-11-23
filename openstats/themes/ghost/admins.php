<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
?>
<div id="content" class="s-c-x">
<div class="wrapper">   
    <div id="main-column">
     <div class="padding">
      <div class="inner">
  <h2><?=$lang["admins"] ?></h2>
   <table>
    <tr>
	   <th width="72"><img src="<?=OS_HOME?>img/admin.png" class="imgvalign" alt="Admin" /></th>
       <th width="180" class="padLeft"><?=$lang["admin"] ?></th>
	   <th width="32"><?=$lang["role"] ?></th>
	   <th><?=$lang["server"] ?></th>
	   <th><?=$lang["member_last_login"]?></th>
    </tr>
   
   <?php foreach($AdminsData as $Admin) { ?>
    <tr>
	   <td><?=OS_AdminIcon($Admin["level"])?></td>
       <td width="180" class="padLeft"><?=OS_AdminLink( $Admin["id"], $Admin["name"] )?></td>
	   <td width="32"><?=OS_IsUserGameAdmin( $Admin["level"] )?></td>
	   <td width="135"><?=$Admin["server"]?></td>
	   <td><?=$Admin["last_login"]?></td>
    </tr>
   <?php } ?>
   </table>
     </div>
    </div>
   </div>
  </div>
</div>
  <?php
include('inc/pagination.php');
?>