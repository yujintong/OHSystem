<?php

if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }
unset($_SESSION["email_send"]);
$MenuClass["profile"] = "active"; 

   $UserIP = $_SERVER["REMOTE_ADDR"];
   if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
   $UserIP = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
   }
   
//LOGOUT
if ( isset($_GET["logout"]) AND is_logged() ) {
  require_once(OS_PLUGINS_DIR.'index.php');
  os_init();
  os_logout();
  //logout redirect
  if ( isset($_SESSION["logout"]) ) {
  header("location: ".$_SESSION["logout"].""); die;
  } else {
  header("location: ".OS_HOME.""); die;
  }
}

//Resend Activation code
if ( !os_is_logged() AND isset($_GET["resend"]) AND !empty($_GET["resend"]) AND !isset($_SESSION["email_send"]) ) {

    $email = $_GET["resend"];
	$errors = "";

    if (!preg_match("/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i", $email)) 
    $errors.="<div>".$lang["error_email"]."</div>";
	
	if ( empty($errors) ) {
	   $sth = $db->prepare("SELECT * FROM ".OSDB_USERS." WHERE user_email=:user_email AND code!='' ");
	   $sth->bindValue(':user_email', $email, PDO::PARAM_STR); 
	   $result = $sth->execute();
	   
	   if ( $sth->rowCount()>=1) {
	    $row = $sth->fetch(PDO::FETCH_ASSOC);
	  	    $message = $lang["email_activation1"]." ".$row["user_name"].",<br /><br />";
	        $message.= $lang["email_activation2"]." ".OS_HOME." <br />";
			$message.= $lang["email_activation3"]."<br /><br />";
			$message.= OS_HOME."?login&code=".$row["code"]."&e=".$row["user_email"]."<br />";
	        $message.="------------------------------------------<br />";
	        $message.="$DefaultHomeTitle<br />";
	 
		    //$send_mail = mail($email, "Account Activation", $message, $headers);
			require("inc/class.phpmailer.php");
	        $mail  = new PHPMailer();
			$mail->CharSet = $lang["email_charset"];
			$mail->SetFrom($lang["email_from"], $lang["email_from_full"]);
			//$mail->AddReplyTo($lang["email_from"], $lang["email_from_full"]);
			$mail->AddAddress($email, "");
			$mail->Subject = $lang["email_subject_activation"];
			$mail->MsgHTML($message);
			$mail->AltBody = "This is the body in plain text for non-HTML mail clients";
			$mail->Send();
			
			$errors.= $lang["email_send"]; //not error - just notification
			$_SESSION["email_send"] = 1;
	        //header("location: ".OS_HOME."?login&success");
		
	   }
	}
} else if ( isset($_GET["resend"]) AND isset($_SESSION["email_send"]) ) {
   $errors.= $lang["error_email_send"]; //
}


//USER ACTIVATION - login&code=$code&e=$email
if ( !is_logged() AND isset($_GET["login"]) AND isset($_GET["code"]) AND isset($_GET["e"]) AND strlen($_GET["code"])>=8 ) {
   $code = safeEscape( $_GET["code"]);
   $e = $_GET["e"];
   $errors = "";
   
   if (!preg_match("/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i", $e)) 
   $errors.="<div>".$lang["error_email"]."</div>";
   if ( empty($errors) ) {
   $sth = $db->prepare("SELECT * FROM ".OSDB_USERS." WHERE user_email=:user_email AND code=:code ");
   
   $sth->bindValue(':user_email', $e, PDO::PARAM_STR); 
   $sth->bindValue(':code', $code, PDO::PARAM_STR); 
   
   $result = $sth->execute();
   
   if ( $sth->rowCount()>=1) {
   
    $update = $db->update(OSDB_USERS, array("code" => ''), "user_email = '".$e."' AND code = '".$code."'");
  
	 $errors.="<div>".$lang["acc_activated"]."</div>"; //Not error...just a message
   } else $errors.="<div>".$lang["invalid_link"]."</div>";
   
   }
}

//BNET - USER ACTIVATION - bnet&c=$code&e=$email
if ( isset($_GET["login"]) AND isset($_GET["bnet"]) AND isset($_GET["c"]) AND strlen($_GET["c"])>=8 AND isset($_GET["e"]) ) {
   $code = safeEscape( $_GET["c"]);
   $e = $_GET["e"];
   $errors = "";
   
   if (!preg_match("/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i", $e)) 
   $errors.="<div>".$lang["error_email"]."</div>";
   if ( empty($errors) ) {
   $sth = $db->prepare("SELECT * FROM ".OSDB_USERS." WHERE user_email=:user_email AND code=:code ");
   
   $sth->bindValue(':user_email', $e, PDO::PARAM_STR); 
   $sth->bindValue(':code', $code, PDO::PARAM_STR); 
   
   $result = $sth->execute();
   
   if ( $sth->rowCount()>=1) {
   
    $row = $sth->fetch(PDO::FETCH_ASSOC);
	
    if ( $row["user_bnet"] == 1 )
    $update = $db->update(OSDB_USERS, array("code" => '', "user_bnet"=>2), "user_email = '".$e."' AND code = '".$code."'");
	
   AddEvent("os_content","OS_BNET_Message");
   
   function OS_BNET_Message() {
   global $lang;
   ?>
<div id="content" class="s-c-x">
<div class="wrapper">   
    <div id="main-column">
     <div class="padding">
      <div class="inner">
       <h2><?=$lang["succes_registration"]?></h2>
     </div>
    </div>
   </div>
  </div>
</div>
	   <?php
   }
  
	 $errors.="<div>".$lang["acc_activated"]."</div>"; //Not error...just a message
   } else $errors.="<div>".$lang["invalid_link"]."</div>";
   
   }
}



  if ( isset($_GET["login"]) )    {
    $HomeTitle = ($lang["login"]);
    if (isset($_GET["success"]) ) $registration_errors = '<div  style="padding-left:370px;"><h3>'.$lang["succes_registration"].'</h3></div>';
	
	if ( is_logged() AND isset($_GET["success"]) ) { header("location: ".OS_HOME.""); die; } 
  }

//LOGIN
if ( isset( $_GET["login"]) AND !is_logged() AND isset($_POST["login_"] ) ) {

   $email = safeEscape( $_POST["login_email"]);
   $password = safeEscape( $_POST["login_pw"]);
   $errors = "";
   $welcome = "";
   if (!preg_match("/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i", $email)) 
   $errors.="<div>".$lang["error_email"]."</div>";
   if ( strlen($password)<=2 ) $errors.="<div>".$lang["error_short_pw"]."</div>";
   
   $BannedEmails = OS_get_custom_field(1, "oh_banemail");
   
   if ( !empty( $BannedEmails ) AND empty($errors) ) {
    
	$BanEmail = explode( ",", $BannedEmails );
	$UserEmail = explode("@", $email);
	if ( isset($UserEmail[1]) ) $CheckEmail = trim($UserEmail[1]); else $CheckEmail = "";

	if (!empty($CheckEmail) ) foreach ( $BanEmail as $em ) {
	  $CheckEmail = strtolower( trim($CheckEmail) );
	  $em = strtolower( trim($em) );
	  if ($CheckEmail == $em) $errors.="<div>".$lang["error_email_banned"]."</div>";
	 }
   }
   
   if ( empty($errors) ) {
   
    $sth = $db->prepare("SELECT * FROM ".OSDB_USERS." WHERE user_email=:user_email ");
   
    $sth->bindValue(':user_email', $email, PDO::PARAM_STR); 
   
    $result = $sth->execute();
   
	  if ( $sth->rowCount()>=1 ) {
	  
	  $row = $sth->fetch(PDO::FETCH_ASSOC);
	  $CheckPW = generate_password($password, $row["password_hash"]);
	  
	  //Bnet user
	  if ( $row["user_bnet"] == 1 AND $row["user_last_login"]<=0) {
	    $rawPW = $row["user_password"];
		//if (!empty($row["bnet_username"]) ) $bnet_username = $row["bnet_username"]; else 
		//$bnet_username = $row["user_name"];
		if ( $password == $rawPW ) {
		
		 $code = generate_hash(10,0);
	     $hashpw = generate_hash(16,1);
	     $password_db = generate_password($password, $hashpw);
		 $updatePW = $db->update(OSDB_USERS, array(
		 
		 "user_password" => $password_db, 
		 "password_hash"=>$hashpw,
		 "code"         =>$code,
		 "user_joined" => time(),
		 "user_last_login" => time(),
		 "user_ip" => $UserIP,
		 
		 ), 
		     "user_email = '".$email."'");
		 
		 $CheckPW = $row["user_password"];
		 $welcome = "?welcome";
		 $_SESSION["welcome"] = 1;
		 
		  $message = $lang["bnet_welcome_email"];
		  $message.= OS_HOME."?login&bnet&c=$code&e=".$row["user_email"];
		  $message.= "<br />~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>$DefaultHomeTitle";
		  
	      require("inc/class.phpmailer.php");
	      $mail  = new PHPMailer();
	      $mail->CharSet = 'UTF-8';
	      $mail->SetFrom($lang["email_from"], $lang["email_from_full"]);
	      $mail->AddReplyTo($lang["email_from"], $lang["email_from_full"]);
	      $mail->AddAddress($row["user_email"], "");
	      $mail->Subject = $lang["bnet_welcome_email_subject"];
	      $mail->MsgHTML($message);
	      $mail->Send();
		 
		}
	  }
	  
	  //DAILY POINTS
	  if ( $row["user_bnet"] >= 1 AND $row["user_points_time"]<= time() AND $DailyPoints>=1) {
	  
	    $nextTime = time()+3600*24;
		$sth2 = $db->prepare("UPDATE ".OSDB_STATS." SET points = points+".(int) $DailyPoints." 
		WHERE player='".$row["bnet_username"]."' LIMIT 1");
        $result = $sth2->execute();
		
		$sth3 = $db->prepare("UPDATE ".OSDB_USERS." SET user_points_time = '".(int)$nextTime."' 
		WHERE user_email='".$row["user_email"]."' LIMIT 1");
		$result = $sth3->execute();
		
		$_SESSION["points_updated"] = 2;
	  }
	  
	  if (!empty($row["code"]) ) $errors.="<div>".$lang["error_inactive_acc"].". <a href='".OS_HOME."?login&amp;resend=$email'>Re-send activation code</a> to $email</div>";
	  
	  if ( $CheckPW == $row["user_password"] AND empty($errors)) {
	  
	  $_SESSION["user_id"] = $row["user_id"];
	  $_SESSION["username"] = $row["user_name"];
	  $_SESSION["email"]    = $row["user_email"];
	  $_SESSION["level"]    = $row["user_level"];
	  $_SESSION["can_comment"]    = $row["can_comment"];
	  $_SESSION["logged"]    = time();
	  $_SESSION["user_lang"]    = $row["user_lang"];
	  $_SESSION["bnet"] = $row["user_bnet"];
	  $_SESSION["bnet_username"] = $row["bnet_username"];
	  
	  $LastLogin = $db->update(OSDB_USERS, array("user_last_login" => (int)time() ), "user_email = '".$email."'");

	  require_once(OS_PLUGINS_DIR.'index.php');
	  os_init();
	  header("location: ".OS_HOME.$welcome); die;
	  }
	  
	 }  else $errors.="<div>".$lang["error_invalid_login"]."</div>";
   }
}

//REGISTER
if ( isset( $_GET["login"]) AND !is_logged() AND isset($_POST["register_"] ) ) {

   if ($UserActivation == 2) { 
   	require_once(OS_PLUGINS_DIR.'index.php');
	os_init();
   header('location: '.OS_HOME.''); die; 
   }
   
   $username = OS_StrToUTF8( $_POST["reg_un"] );
   $username = EscapeStr( trim( $username ));
   $email = safeEscape( trim($_POST["reg_email"]));
   $password = safeEscape( $_POST["reg_pw"]);
   $password2 = safeEscape( $_POST["reg_pw2"]);
   $registration_errors = "";

   $AllowedCharacters = 'QWERTZUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklyxcvbnmљњертзуиопшђасдфгхјклчћжѕџцвбнмšđčćžŠĐČĆŽЉЊЕРТЗУИОПШЂАСДФГХЈКЛЧЋЖЅЏЦВБНМ_-';
   
   if (!preg_match ('/^['.$AllowedCharacters.']+$/', $username))
   $registration_errors.="<div>".$lang["error_username"]."</div>";
   
   //die($registration_errors." - ".$username);
   
   if (!preg_match("/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i", $email)) 
   $registration_errors.="<div>".$lang["error_email"]."</div>";
   if ( strlen($username)<=2 )  $registration_errors.="<div>".$lang["error_short_un"]."</div>";
   if ( strlen($password)<=2 )  $registration_errors.="<div>".$lang["error_short_pw"]."</div>";
   if ( $password!=$password2 ) $registration_errors.="<div>".$lang["error_passwords"]."</div>";
   
   $BannedEmails = OS_get_custom_field(1, "oh_banemail");
   
   if ( !empty( $BannedEmails ) AND empty($errors) ) {
    
	$BanEmail = explode( ",", $BannedEmails );
	$UserEmail = explode("@", $email);
	if ( isset($UserEmail[1]) ) $CheckEmail = trim($UserEmail[1]); else $CheckEmail = "";

	if (!empty($CheckEmail)) foreach ( $BanEmail as $em ) {
	  $CheckEmail = strtolower( trim($CheckEmail) );
	  $em = strtolower( trim($em) );
	  if ($CheckEmail == $em) $registration_errors.="<div>".$lang["error_email_banned"]."</div>";
	 }
   }
   
   if ( empty($registration_errors) ) {
    //$result = $db->query("SELECT COUNT(*) FROM ".OSDB_USERS." WHERE (user_name) = ('".$username."') ");
	 
   $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_USERS." WHERE user_name=:user_name LIMIT 1");
   $sth->bindValue(':user_name', $username, PDO::PARAM_STR); 
   $result = $sth->execute();
   $r = $sth->fetch(PDO::FETCH_NUM);

     if ( $r[0] >=1 )
	 $registration_errors.="<div>".$lang["error_un_taken"]."</div>";
	 
	 $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_USERS." WHERE user_email=:user_email LIMIT 1");
	 $sth->bindValue(':user_email', $email, PDO::PARAM_STR); 
	 $result = $sth->execute();
	 $r = $sth->fetch(PDO::FETCH_NUM);

     if ( $r[0] >=1 )
	 $registration_errors.="<div>".$lang["error_email_taken"]."</div>";
	 
	  if ( empty($registration_errors) ) {
	  
	  $hash = generate_hash(16,1);
	  $password_db = generate_password($password, $hash);
	  
	  if ($UserActivation == 1) $code = generate_hash(16,0); else $code = '';
	  
	  //FIND user location
	if ( file_exists("inc/geoip/geoip.inc") ) {
	include("inc/geoip/geoip.inc");
	$GeoIPDatabase = geoip_open("inc/geoip/GeoIP.dat", GEOIP_STANDARD);
	$GeoIP = 1;
	$Letter  = geoip_country_code_by_addr($GeoIPDatabase, $UserIP);
	$Country = geoip_country_name_by_addr($GeoIPDatabase, $UserIP);
	
	 geoip_close($GeoIPDatabase);
	}
	
	if (!empty($Country) ) $location = $Country; else $location = '';
	  
    $db->insert( OSDB_USERS, array(
	"user_name" => $username,
	"user_password" => $password_db,
	"password_hash" => $hash,
	"user_email" => $email,
	"user_joined" => (int) time(),
	"user_location" => $location,
	"user_level" => 0,
	"user_ip" => $UserIP,
	"can_comment" => 1,
	"code" => $code
                                 ));
	  
	  $result = 1;
	  
	  if ( $code=="" AND $result) {
	    $uid = $db->lastInsertId(); 
	    $_SESSION["user_id"] = $uid;
	    $_SESSION["username"] = $username;
	    $_SESSION["email"]    = $email;
	    $_SESSION["level"]    = 0;
	    $_SESSION["can_comment"]    = 1;
	    $_SESSION["logged"]    = time();
	  
	    $LastLogin = $db->update(OSDB_USERS, array("user_last_login" => (int)time() ), 
		                                                                     "user_email = '".$email."'");
	  }
	  
	  //SEND EMAIL
	  if ($UserActivation == 1) {
	  	    $message = $lang["email_activation1"]." $username,<br /><br />";
	        $message.= $lang["email_activation2"]." ".OS_HOME." <br />";
			$message.= $lang["email_activation3"]."<br /><br />";
			$message.= $website."?login&code=$code&e=$email<br />";
	        $message.="------------------------------------------<br />";
	        $message.="$DefaultHomeTitle<br />";
	 
		    //$send_mail = mail($email, "Account Activation", $message, $headers);
			require("inc/class.phpmailer.php");
	        $mail  = new PHPMailer();
			$mail->CharSet = $lang["email_charset"];
			$mail->SetFrom($lang["email_from"], $lang["email_from_full"]);
			$mail->AddReplyTo($lang["email_from"], $lang["email_from_full"]);
			$mail->AddAddress($email, "");
			$mail->Subject = $lang["email_subject_activation"];
			$mail->MsgHTML($message);
			$mail->Send();
	       }
	   header("location: ".OS_HOME."?login&success");
	  }
   }
}
?>