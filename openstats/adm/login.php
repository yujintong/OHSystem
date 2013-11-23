<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
?>
<div id="content" class="s-c-x">
<div class="wrapper">   
    <div id="main-column">
     <div class="padding">
      <div class="inner">
<div style="margin-top: 55px;">
  <h1>LOGIN</h1>
</div>  

<?php //include("../themes/ghost/menu.php"); ?>



<div align="center" style="background-color: #fff; width: 960px; margin: 0 auto; padding-top: 18px; border: 10px solid #2B0202; border-radius: 10px;">

<?php if (isset($errors) AND !empty($errors) ) { ?>
<div style="color: red;"><?=$errors?></div>
<?php } ?>
     <form action="" method="post">
	 <table>
	 <tr>
	   <th width="100"></th>
	   <th width="300" class="alignleft">Please login to continue:</th>
	  </tr>
	  
	  <tr class="row">
	   <td width="100" class="alignleft padLeft">E-mail:</td>
	   <td width="300"> <input type="text" value="" name="login_email" /></td>
	  </tr>
	  
	  <tr class="row">
	   <td width="100" class="alignleft padLeft">Password:</td>
	   <td width="300"> <input type="password" value="" name="login_password" /></td>
	  </tr>
	  
	  <tr class="row">
	   <td width="100" class="alignleft padLeft"></td>
	   <td width="300"> <input class="menuButtons" type="submit" value="Login" name="login_" /></td>
	  </tr>
	 
	 </table>
	
	 </form>
</div>
<div style="margin-bottom: 60px;">&nbsp; </div>
<?php
//var_dump($_SESSION);
//include('../themes/'.$DefaultStyle.'/footer.php');
?>
     </div>
    </div>
   </div>
  </div>
</div>