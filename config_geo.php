<?php

// Fonction permettant d'avoir des informations sur la livraison (délais et distance)
// $from sont les coordonnées de départ, $to sont les coordonnées d'arriver
// $type est le type de moyen de transport : 
// driving-hgv = véhicule lourd, cycling-electric = vélo électrique /!\ Non disponible sur MapBox 
// scooter = scooter /!\ Uniquement disponible sur GraphHopper
// driving-car = véhicule léger, cycling = vélo, foot-walking = à pied
// $service égal à 1 pour openrouteservice.org, 2 mapbox.com, 3 pour graphhopper.com, 
function get_info_delivery($from,$to,$type,$service)
{
  	if($service == 1)
    {
          $address."+".$postcode."+".$city;
          $address = str_replace(" ", "+", $address);
          $req = "https://api.openrouteservice.org/v2/directions/".$type."?api_key=5b3ce3597851110001cf624812224030091e40bf8ee293287030dd30&start=".$from."&end=".$to;

          $ch = curl_init();

          curl_setopt($ch, CURLOPT_URL, $req);

          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

          $output = curl_exec($ch);

          curl_close($ch);  

          $geojson = json_decode($output, true);

          $distance = $geojson['features'][0]['properties']['summary']['distance'];
          $duration = intval($geojson['features'][0]['properties']['summary']['duration']);

          if($duration > 3600){
              /*
              $h = intdiv($duration,3600);
              $min = ($duration%(3600/60));
              $duration = intdiv($duration,3600)."h ".$min."min";
              */
              $duration = s_en_hmins($duration);
          }
          elseif($duration > 60)
              $duration = intdiv($duration,60)."min ".($duration%60)."s";
          else
              $duration = $duration." s";

          if($distance > 1000)
              $distance = intdiv($distance,1000).",".($distance%1000)." km";
          else
              $distance = $distance." m";

          return($distance." - ".$duration);
    }
  	if($service == 2)
    {
      	  if($type == "driving-car")
          		$type = "driving";
      	  if($type == "foot-walking")
          		$type = "walking";
          $address."+".$postcode."+".$city;
          $address = str_replace(" ", "+", $address);
          $req = "https://api.mapbox.com/directions/v5/mapbox/".$type."/".$from.";".$to."?annotations=maxspeed&overview=full&geometries=geojson&access_token=pk.eyJ1IjoiY2hhbWJyZWRpZ2l0YWxlIiwiYSI6ImNrbWh4em43NjBjMHkydm5hOG95YjF5NXEifQ.E1INZrysjA-_R6khjoytZw";

          $ch = curl_init();

          curl_setopt($ch, CURLOPT_URL, $req);

          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

          $output = curl_exec($ch);

          curl_close($ch);  

          $geojson = json_decode($output, true);

          $distance = $geojson['routes'][0]['distance'];
          $duration = intval($geojson['routes'][0]['duration']);

          if($duration > 3600){
              /*
              $h = intdiv($duration,3600);
              $min = ($duration%(3600/60));
              $duration = intdiv($duration,3600)."h ".$min."min";
              */
              $duration = s_en_hmins($duration);
          }
          elseif($duration > 60)
              $duration = intdiv($duration,60)."min ".($duration%60)."s";
          else
              $duration = $duration." s";

          if($distance > 1000)
              $distance = intdiv($distance,1000).",".($distance%1000)." km";
          else
              $distance = $distance." m";

          return($distance." - ".$duration);
    }
  	if($service == 3)
    {
      	  if($type == "driving-car")
          		$type = "car";
      	  if($type == "foot-walking")
          		$type = "foot";
      	  if($type == "cycling")
          		$type = "bike";
      	  if($type == "cycling-electric")
          		$type = "racingbike";
      	  if($type == "driving-hgv")
          		$type = "truck";
      	  if($type == "scooter")
          		$type = "scooter";
          $address."+".$postcode."+".$city;
          $address = str_replace(" ", "+", $address);
          $req = "https://graphhopper.com/api/1/route?point=".$from."&point=".$to."&vehicle=".$type."&debug=true&key=fad336fd-6ee9-4289-b2dd-fe33449165c5&type=json";

          $ch = curl_init();

          curl_setopt($ch, CURLOPT_URL, $req);

          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

          $output = curl_exec($ch);

          curl_close($ch);  

          $geojson = json_decode($output, true);

          $distance = $geojson['paths'][0]['distance'];
          $duration = intval(($geojson['paths'][0]['duration'])/1000);

          if($duration > 3600){
              /*
              $h = intdiv($duration,3600);
              $min = ($duration%(3600/60));
              $duration = intdiv($duration,3600)."h ".$min."min";
              */
              $duration = s_en_hmins($duration);
          }
          elseif($duration > 60)
              $duration = intdiv($duration,60)."min ".($duration%60)."s";
          else
              $duration = $duration." s";

          if($distance > 1000)
              $distance = intdiv($distance,1000).",".($distance%1000)." km";
          else
              $distance = $distance." m";

          return($distance." - ".$duration);
    }
}

// Fonction permettant l'enregistrement du passage du client
function recup_geo_info($type,$company,$dep)
{
  	$adresse = $_SERVER['REMOTE_ADDR'];
  	$agent = $_SERVER['HTTP_USER_AGENT'];

    $location = json_decode(file_get_contents('https://freegeoip.app/json/'.$adresse), true);
  
    $latitude = $location['latitude'];
    $longitude = $location['longitude'];
    $country = $location['country_name'];
    $region = $location['region_name'];
    $post_code = $location['zip_code'];
    $ville = $location['city'];

  	return(array($latitude,$longitude,$country,$region,$post_code,$ville));
}

// Fonction permattant de récupérer les données géographiques via le GPS
function recup_coord()
{
	echo '
    <script>
    
    function maPosition(position) {
     // Recupère identifiant des lignes cochées
    var lat = position.coords.latitude;
    // Transmet la variable latitude dans le input hidden de la page
    document.getElementById("lat").value= lat; 
    
    // Recupère identifiant des lignes cochées
    var lng = position.coords.longitude;
    // Transmet la variable longitude dans le input hidden de la page
    document.getElementById("lng").value= lng;    
    
    // Recupère identifiant des lignes cochées
    var alt = position.coords.altitude;
    // Transmet la variable valeur_id dans le input hidden de la page
    document.getElementById("lat").value= alt;
 	}

    if(navigator.geolocation)
      navigator.geolocation.getCurrentPosition(maPosition);
    </script>';
	
	//return array($longitude,$latitude);
	//list($longitude,$latitude) = recup_coord();
}

// Fonction permattant de récupérer les données géographiques via le GPS
function recup_geo()
{
	echo'<div id="infoposition"></div>
    <script>
function maPosition(position) {
  var infopos = "Position déterminée :\n";
  infopos += "Latitude : "+position.coords.latitude +"\n";
  infopos += "Longitude: "+position.coords.longitude+"\n";
  infopos += "Altitude : "+position.coords.altitude +"\n";
  document.getElementById("infoposition").innerHTML = infopos;
}

if(navigator.geolocation)
  navigator.geolocation.getCurrentPosition(maPosition);
</script>';
}

?>
  	
