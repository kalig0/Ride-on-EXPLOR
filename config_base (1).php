<?php

deleteBD('events');
  
// fonction permettant la connexion à la base
function configBD(){
  	// Utilisation du fichier de configuration utilisateur
	$DB_serveur = 'db5016804164.hosting-data.io'; // Nom du serveur
    $DB_utilisateur = 'dbu2816952'; // Nom de l'utilisateur de la base
    $DB_motdepasse = 'HackathonGenAI2024'; // Mot de passe pour accèder à la base
    $DB_base = 'dbs13580273'; // Nom de la base
        
    $connexion = mysqli_connect($DB_serveur,$DB_utilisateur,$DB_motdepasse,$DB_base);
  
  	// Check connection
	if (mysqli_connect_errno())
  	{
  		echo "Impossible d'accéder à la base MySQL: " . mysqli_connect_error();
  	}
  	else
    	return $connexion;
}

// fonction permettant la création d'un client
function ajout_client($name,$password,$mail,$birthday,$country,$location,$code,$prospection)
{
	// Date du jour
	$date = date("Y-m-d H:i:s");
    // On récupère la date du jour +1 an
  	//$limite = date('Y-m-d', strtotime('+1 year'));
  
    // Connexion à la base
  	$connexion = configBD();
  	mysqli_set_charset($connexion, "utf8");
  	
	// Enregistrement de l'utilisateur dans la base user_id
    $req = mysqli_query($connexion,"INSERT INTO user_id (id, name, password, mail, birthday, country, location, , strava_link, n_mobile) 
            VALUES ('$id, '$name', '$password', '$mail', '$birthday', '$location',  '$country', '$location', '$strava_link', '$n_mobile' )");
            
	// Enregistrement de l'utilisateur dans la base log
    $req = mysqli_query($connexion,"INSERT INTO log (id, log_in, tel_mobile, pays, departement, prospection) 
            VALUES ('$id', '$log_in', '$log_out', '$gps_position')");
  
	// Enregistrement de l'utilisateur dans la base data_security
    $req = mysqli_query($connexion,"INSERT INTO log (id, log_in, tel_mobile, pays, departement, prospection) 
            VALUES ('$name', '$date', '$tel', '$pays', '$departement', '$prospection')");
  
    // Déconnexion du serveur
    mysqli_close($connexion2);
      
  	$client = $user = $pass = $mail = $tel = $birthday = $pays = $departement = $prospection = null;
}

// fonction permettant la mise à jour d'un client
function maj_client($name,$pass,$mail,$birthday,$tel,$pays,$location,$prospection)
{
	// Date du jour
	$date = date("Y-m-d H:i:s");
    // On récupère la date du jour +1 an
  	//$limite = date('Y-m-d', strtotime('+1 year'));
  
	// Enregistrement de l'utilisateur dans la base log
    $req = mysqli_query($connexion,"INSERT INTO user_id (id, name, password, mail, birthday, badge_color, country, location, strava_link) 
            VALUES ('$id', '$name', '$password', '$mail', '$birthday', '$badge_color', '$country', '$location', '$strava_link')");
  
    // Connexion à la base
  	$connexion = configBD();
  	mysqli_set_charset($connexion, "utf8");
    // Déconnexion du serveur
    mysqli_close($connexion2);
      
  	$name = $password = $mail = $birthday  = null;
}

// fonction permettant la mise à jour d'un client
function maj_preference($id,$preference, $gps_point,$level,$disability)
{
	// Date du jour
	$date = date("Y-m-d H:i:s");
    // On récupère la date du jour +1 an
  	//$limite = date('Y-m-d', strtotime('+1 year'));
  
	// Enregistrement de l'utilisateur dans la base log
    $req = mysqli_query($connexion,"INSERT INTO user_id (id, preference, gps_point, level, disability) 
            VALUES ('$id', '$preference', '$gps_point', '$level', '$disability')");
  
    // Connexion à la base
  	$connexion = configBD();
  	mysqli_set_charset($connexion, "utf8");
    // Déconnexion du serveur
    mysqli_close($connexion2);
      
  	$preference = $gps_point = $level = $disability = null;
}

// fonction permettant la mise à jour d'un client
function maj_log($id,$log_in,$log_out,$gps_position)
{
  
	// Enregistrement de l'utilisateur dans la base log
    $req = mysqli_query($connexion,"INSERT INTO log (id, log_in, log_out, gps_position) 
            VALUES ('$id', '$log_in', '$log_out', '$gps_position')");
  
    // Connexion à la base
  	$connexion = configBD();
  	mysqli_set_charset($connexion, "utf8");
    // Déconnexion du serveur
    mysqli_close($connexion2);
      
  	$client = $user = $pass = $mail = $tel = $birthday = $pays = $departement = $prospection = null;
}

// fonction permettant la mise à jour d'un client data_security
function maj_security($id,$ads, $newlsletters,$credit_card,$payment_method)
{
	// Enregistrement de l'utilisateur dans la base log
    $req = mysqli_query($connexion,"INSERT INTO data_security (id, ads, newsletters, $credit_card, $payment_method) 
            VALUES ('$id', '$ads', '$newlsletters', '$credit_card', '$payment_method')");
  
    // Connexion à la base
  	$connexion = configBD();
  	mysqli_set_charset($connexion, "utf8");
    // Déconnexion du serveur
    mysqli_close($connexion2);
      
  	$client = $user = $pass = $mail = $tel = $birthday = $pays = $departement = $prospection = null;
}

// fonction permettant la mise à jour d'un event
function maj_events($link,$date,$city,$gps_event,$type)
{
	// Enregistrement de l'utilisateur dans la base log
    $req = mysqli_query($connexion,"INSERT INTO events ($link, $date, $city, $gps_event, $type) 
            VALUES ('$link', '$date', '$city', '$gps_event', '$type')");
  
    // Connexion à la base
  	$connexion = configBD();
  	mysqli_set_charset($connexion, "utf8");
    // Déconnexion du serveur
    mysqli_close($connexion2);
      
  	$link = $date = $city = $gps_event = $type = null;
}

// Permet la suppression de table sur les différentes bases de données
function deleteBD($table)
{
    $connexion = configBD();

    if(mysqli_connect_errno()) // On se connecte au serveur
    {
      echo mysqli_connect_errno()."<BR>";
      echo "Impossible d'accéder au serveur SQL.";
      exit();
    }
    else
    {
      echo "Connexion au serveur SQL...OK<BR/>";
      $req = "TRUNCATE TABLE ".$table;

      $test = mysqli_query($connexion,$req);

      //return $message;
      if($test == TRUE)
        echo "Suppression réalisée";
      else
        echo "Suppression impossible";

      /* Ferme la connexion */
      mysqli_close($connexion);
    }
}
?>