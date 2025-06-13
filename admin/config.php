<?php
/* Configuration de la base de données */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'lartisan_pizzeria');

/* Connexion à la base de données MySQL */
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

/* Vérifier la connexion */
if($mysqli === false){
    die("ERREUR : Impossible de se connecter à la base de données. " . $mysqli->connect_error);
}
?>