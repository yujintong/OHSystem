<?php

if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

     $HomeTitle = $lang["admins"] ;
	 $HomeDesc = $lang["admins"];
	 $HomeKeywords = strtolower( os_strip_quotes($lang["admins"])).','.$HomeKeywords;
	 
	 $MenuClass["admins"] = "active";
    
	 $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_USERS." WHERE user_level>=4 LIMIT 1");
	 $result = $sth->execute();
	 $r = $sth->fetch(PDO::FETCH_NUM);
	 $numrows = $r[0];
	 $result_per_page = $TopPlayersPerPage;
	 $draw_pagination = 0;
	 include('inc/pagination.php');
	 $draw_pagination = 1;
	 
	 $c=0;
    $AdminsData = array();
	
	$sth = $db->prepare("SELECT u.user_id, u.user_name, u.admin_realm, u.user_level, u.user_last_login 
	FROM ".OSDB_USERS." as u
	WHERE u.user_level>=4 ORDER BY u.user_level DESC, LOWER(u.user_name) ASC LIMIT $offset, $rowsperpage");
	
	$result = $sth->execute();
	
	while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	$AdminsData[$c]["id"]        = (int)($row["user_id"]);
	$AdminsData[$c]["name"]  = ($row["user_name"]);
	$AdminsData[$c]["server"]  = ($row["admin_realm"]);
	$AdminsData[$c]["level"]  = ($row["user_level"]);
	$AdminsData[$c]["last_login"]  = date(OS_DATE_FORMAT, ($row["user_last_login"]));
	$c++;
	}
?>