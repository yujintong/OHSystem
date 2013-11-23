<?php

if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

    $HomeTitle = $lang["heroes"] ;
	$HomeDesc = $lang["heroes"];
	$HomeKeywords = strtolower( os_strip_quotes($lang["heroes"])).','.$HomeKeywords;
	$MenuClass["misc"] = "active";
   
    if ( isset($_GET["search_heroes"]) AND strlen($_GET["search_heroes"])>=2  ) {
	    $search_heroes = safeEscape( trim($_GET["search_heroes"]));
		$sql = "AND (description) LIKE ? ";
	 } else $sql = ""; 
	 
    $HeroesData = array();
	$HeroesData[0] = "Heroes";
   
    /*
     $sth = $db->prepare("SELECT COUNT(*) FROM ".OSDB_HEROES." WHERE summary!= '-' $sql LIMIT 1");
	 if ( !empty($sql) ) $sth->bindValue(1, "%".strtolower($search_heroes)."%", PDO::PARAM_STR);
	 
	 $result = $sth->execute();
   	 $r = $sth->fetch(PDO::FETCH_NUM);
	 $numrows = $r[0];
	 $result_per_page = $HeroesPerPage;
	 $result_per_page = $HeroesPerPage;
	 $draw_pagination = 0;
	 $total_comments  = $numrows;
	 include('inc/pagination.php');
	 $draw_pagination = 1;
	 
	 $sth = $db->prepare("SELECT * FROM ".OSDB_HEROES." 
	 WHERE summary!= '-' $sql 
	 ORDER BY `type` ASC, `description` ASC  
	 LIMIT $offset, $rowsperpage");
	 
	 if ( !empty($sql) ) $sth->bindValue(1, "%".strtolower($search_heroes)."%", PDO::PARAM_STR);
	 $result = $sth->execute();
	 $c=0;
     $HeroesData = array();
	 
	$CurrentType = 0;
	while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	$Type = "";
	if ( $row["type"] == 0 AND !isset($str) ) { $str = "Strength"; $Type = "Strength"; }
	if ( $row["type"] == 1 AND !isset($agi) ) { $agi = "Agility"; $Type = "Agility"; }
	if ( $row["type"] == 2 AND !isset($int) ) { $int = "int"; $Type = "Intelligence"; }
	
	$HeroesData[$c]["type"]        = ($Type);
	$HeroesData[$c]["id"]        = ($row["heroid"]);
	$HeroesData[$c]["original"]  = ($row["original"]);
	$HeroesData[$c]["description"]  = ($row["description"]);
	$HeroesData[$c]["summary"]  = removeSpaces(removeDoubleSpaces(convEnt($row["summary"])));
	$HeroesData[$c]["stats"]  = removeSpaces(removeDoubleSpaces(convEnt($row["stats"])));
	$HeroesData[$c]["skills"]  = removeSpaces(removeDoubleSpaces(convEnt($row["skills"])));
	$c++;
	}
	*/
?>