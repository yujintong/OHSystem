<?php

if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

    $userID = safeEscape( (int) $_GET["member"] );
	
	$MenuClass["members"] = "active";
	
	$sth = $db->prepare("SELECT u.*, COUNT(c.user_id) as total_comments 
	FROM ".OSDB_USERS." as u 
	LEFT JOIN ".OSDB_COMMENTS." as c ON c.user_id = u.user_id
	WHERE u.user_id = :userID LIMIT 1");
		
	$sth->bindValue(':userID', $userID, PDO::PARAM_INT); 
	$result = $sth->execute();
	
	$c=0;
    $MemberData = array();
	
	if ( file_exists("inc/geoip/geoip.inc") ) {
	include("inc/geoip/geoip.inc");
	$GeoIPDatabase = geoip_open("inc/geoip/GeoIP.dat", GEOIP_STANDARD);
	$GeoIP = 1;
	}
	
	if ( $sth->rowCount()>=1 ) {
	
    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	
	  //USER REGISTERED/CONFIRMED BNET ACCOUNT
	 if ( $row["user_bnet"] >=1 ) {
	 
	 $sth2 = $db->prepare("SELECT * FROM ".OSDB_STATS." 
	 WHERE player = '".$row["user_name"]."' 
	 ORDER BY id DESC 
	 LIMIT 1 ");
	 
	 $result = $sth2->execute();
	 $row2 = $sth2->fetch(PDO::FETCH_ASSOC);
	 $MemberData[$c]["points"] = number_format($row2["points"]);
	 $MemberData[$c]["games"] = ($row2["games"]);
	 $MemberData[$c]["score"] = ($row2["score"]);
	 $MemberData[$c]["wins"] = ($row2["wins"]);
	 $MemberData[$c]["losses"] = ($row2["losses"]);
	 $MemberData[$c]["uid"] = ($row2["id"]);
	 
	if ($row2["wins"] >0 )
	$MemberData[$c]["winslosses"] = ROUND($row2["wins"]/($row2["wins"]+$row2["losses"]), 3)*100;
	else $MemberData[$c]["winslosses"] = 0;
	
	 }
	
	$MemberData[$c]["user_id"] = $row["user_id"];
	$MemberData[$c]["user_name"] = $row["user_name"];
	$MemberData[$c]["user_bnet"] = $row["user_bnet"];
	$MemberData[$c]["user_ppwd"] = ($row["user_ppwd"]);
	$MemberData[$c]["bnet_username"] = $row["bnet_username"];
	$MemberData[$c]["user_email"] = $row["user_email"];
	$MemberData[$c]["user_level"] = ($row["user_level"]);
	$MemberData[$c]["total_comments"] = $row["total_comments"];
	$MemberData[$c]["user_joined"] = date(OS_DATE_FORMAT, $row["user_joined"]);
	$MemberData[$c]["user_joined_date"] = $row["user_joined"];
	$MemberData[$c]["user_location"] = $row["user_location"];
	$MemberData[$c]["user_realm"] = $row["user_realm"];
	$MemberData[$c]["user_clan"] = $row["user_clan"];
	$MemberData[$c]["user_website"] = $row["user_website"];
	$MemberData[$c]["user_gender"] = $row["user_gender"];
	$MemberData[$c]["user_last_login_date"] = $row["user_last_login"];
	$MemberData[$c]["user_last_login"] = date(OS_DATE_FORMAT, $row["user_last_login"]);
	$MemberData[$c]["code"] = $row["code"];
	
	if ($row["user_last_login"]=="" OR date("Y", $row["user_last_login"])<=1990 )
	{ $MemberData[$c]["user_last_login"] = ""; $MemberData[$c]["user_last_login_date"] = ""; }
	
	if ($row["user_joined"]=="" OR date("Y", $row["user_joined"])<=1990 )
	{ $MemberData[$c]["user_joined"] = ""; $MemberData[$c]["user_joined_date"] = ""; }
	
	$avatar = $row["user_avatar"];
	 if ( empty($avatar) ) $avatar = OS_HOME."img/avatar_64.png";
	 
	 if (empty($row["user_avatar"])) {
     $avatar = "http://www.gravatar.com/avatar/".md5( strtolower($row["user_email"]) )."?s=220&d=monsterid&r=g";
     }
	
    $MemberData[$c]["avatar"]   = $avatar ;	
	$MemberData[$c]["letter"]   = geoip_country_code_by_addr($GeoIPDatabase, $row["user_ip"]);
	$MemberData[$c]["country"]  = geoip_country_name_by_addr($GeoIPDatabase, $row["user_ip"]);
	if ($GeoIP == 1 AND empty($MemberData[$c]["letter"]) ) {
	$MemberData[$c]["letter"] = "blank";
	$MemberData[$c]["country"] = "Reserved";
	}
	$c++;
	
	}
	
	if ( isset($GeoIP) AND $GeoIP == 1) geoip_close($GeoIPDatabase);
	
	//GET USER COMMENTS
	
	 $sth = $db->prepare("SELECT c.user_id, c.post_id, c.text, c.`date`, n.news_title
     FROM ".OSDB_COMMENTS." as c 
	 LEFT JOIN ".OSDB_NEWS." as n ON n.news_id = c.post_id
	 WHERE c.user_id = :userID AND n.status >= 1
	 ORDER BY c.`date` DESC
	 LIMIT 50");
	 
	$sth->bindValue(':userID', $userID, PDO::PARAM_INT); 
	$result = $sth->execute();
	 
	$c=0;
    $MemberComments = array();
	 
	 while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	 
		$ShortText = limit_words( convEnt($row["text"]), 30);
		$ShortText = str_replace(array("'", '"'), array(" "," "), $ShortText);
		
		$MemberComments[$c]["short_text"] = $ShortText;
		$MemberComments[$c]["text"] = $row["text"];
		$MemberComments[$c]["news_title"] = $row["news_title"];
		$MemberComments[$c]["post_id"] = $row["post_id"];
		$MemberComments[$c]["date"] = date(OS_DATE_FORMAT, $row["date"]);
		$MemberComments[$c]["date_int"] = $row["date"];
		$c++;
	 }
	
	} else {
    header('location:'.OS_HOME.'?404');
	die;
	}
?>