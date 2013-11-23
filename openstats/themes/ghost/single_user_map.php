<?php
if (!isset($website) ) {header('HTTP/1.1 404 Not Found'); die; }

if (!empty($User["latitude"]) AND !empty($User["longitude"]) ) {
?>
<script type="text/javascript">
      var map;
      function initialize() {
        var mapOptions = {
          zoom: <?=$User["zoom_map"]?>,
          center: new google.maps.LatLng(<?=$User["latitude"]?>, <?=$User["longitude"]?>),
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById('g_mapa'), mapOptions);
		var myLatLng = new google.maps.LatLng(<?=$User["latitude"]?>, <?=$User["longitude"]?> );
		var beachMarker = new google.maps.Marker({
        position: myLatLng,
        map: map
	    });

      }

   google.maps.event.addDomListener(window, 'load', initialize);
</script> 
<div align="center">
  <div id="g_mapa" class="user_location" ></div>
  <h3 class="user_country"><?=$User["country"]?></h3>
</div>
<?php } ?>