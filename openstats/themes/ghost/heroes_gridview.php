<?php
if (!isset($website) ) { header('HTTP/1.1 404 Not Found'); die; }

  if ( !isset($search_heroes) ) $search_heroes=$lang["search_heroes"] ;
?>

<div id="content" class="s-c-x">
  <div class="wrapper">   
     <div id="main-column">
        <div class="padding">
          <div class="inner">
  
  <div class="padLeft"><h2><?=$lang["heroes"]?></h2></div>
<?php
   if ( file_exists("inc/cache/HeroList.php") ) include("inc/cache/HeroList.php");
   ?>
     <div class="HeroInfoPageWrapper">
    <div class="HeroInfoMainLeft">
	<div><b>Strength</b></div>
	<?php foreach ( $HeroListStr as $Hero ) { ?>
	<?php 
	$Hero["description"] = convEnt($Hero["description"]); 
	$Hero["stats"] = convEnt($Hero["stats"]); 
	$Hero["summary"] = convEnt($Hero["summary"]); 
	$Hero["skills"] = convEnt($Hero["skills"]); 
	
   OS_DisplayHeroesData( $Hero["original"], $Hero["description"], $Hero["stats"], $Hero["summary"], $Hero["skills"]);
    } ?>
	
	<div><b>Agility</b></div>
	<?php foreach ( $HeroListAgi as $Hero ) { ?>
	<?php 
	$Hero["description"] = convEnt($Hero["description"]); 
	$Hero["stats"] = convEnt($Hero["stats"]); 
	$Hero["summary"] = convEnt($Hero["summary"]); 
	$Hero["skills"] = convEnt($Hero["skills"]); 
   OS_DisplayHeroesData( $Hero["original"], $Hero["description"], $Hero["stats"], $Hero["summary"], $Hero["skills"]);
	} ?>
	
	<div><b>Intelligence</b></div>
	<?php foreach ( $HeroListInt as $Hero ) { ?>
	<?php 
	$Hero["description"] = convEnt($Hero["description"]); 
	$Hero["stats"] = convEnt($Hero["stats"]); 
	$Hero["summary"] = convEnt($Hero["summary"]); 
	$Hero["skills"] = convEnt($Hero["skills"]); 
  OS_DisplayHeroesData( $Hero["original"], $Hero["description"], $Hero["stats"], $Hero["summary"], $Hero["skills"]);  
    } ?>
	
	</div>
	
	<div class="HeroInfoMainRight">
	  <div id="HeroInfo"></div>
	</div>
  </div>
  
  
<script type="text/javascript">
  function OS_HeroInfo(hid){
    info    = document.getElementById("hero"+hid+"description").innerHTML;
	stats   = document.getElementById("hero"+hid+"stats").innerHTML;
	skills  = document.getElementById("hero"+hid+"skills").innerHTML;
	summary = document.getElementById("hero"+hid+"summary").innerHTML;
	
    html = '<img class="imgvaligntop" width="64" height="64" src="<?=OS_HOME?>img/heroes/'+hid+'.gif" alt="" /> ';
	html+= '<span><b>'+info+'</b></span>';
	html+= '<div class="HeroInfoStats"><b>'+stats+'</b></div>';
	html+= '<div class="HeroInfoSkills">'+skills+'</div>';
	//html+= '<div class="HeroInfoSkills">'+summary+'</div>';
	
	document.getElementById("HeroInfo").innerHTML = html;
  }
  
  function OS_ClearHeroInfo(){
    document.getElementById("HeroInfo").innerHTML = "";
  }
</script>
     </div>
    </div>
   </div>
  </div>
</div>
