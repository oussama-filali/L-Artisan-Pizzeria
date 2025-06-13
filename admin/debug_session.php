<?php
// Démarrer la session
session_start();

// Afficher toutes les variables de session
echo "<h2>Variables de session actuelles :</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Afficher les informations sur la session
echo "<h2>Informations sur la session :</h2>";
echo "ID de session : " . session_id() . "<br>";
echo "Nom de session : " . session_name() . "<br>";
echo "Chemin du cookie de session : " . session_get_cookie_params()['path'] . "<br>";
echo "Durée de vie du cookie de session : " . session_get_cookie_params()['lifetime'] . " secondes<br>";
echo "Domaine du cookie de session : " . session_get_cookie_params()['domain'] . "<br>";
echo "Secure : " . (session_get_cookie_params()['secure'] ? "Oui" : "Non") . "<br>";
echo "HttpOnly : " . (session_get_cookie_params()['httponly'] ? "Oui" : "Non") . "<br>";

// Afficher les cookies
echo "<h2>Cookies :</h2>";
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";

// Afficher les informations sur le serveur
echo "<h2>Informations sur le serveur :</h2>";
echo "Nom du serveur : " . $_SERVER['SERVER_NAME'] . "<br>";
echo "Port du serveur : " . $_SERVER['SERVER_PORT'] . "<br>";
echo "Protocole : " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "HTTPS" : "HTTP") . "<br>";
echo "User Agent : " . $_SERVER['HTTP_USER_AGENT'] . "<br>";

// Tester la création d'une variable de session
$_SESSION['test_var'] = "Ceci est un test à " . date('H:i:s');
echo "<h2>Variable de test créée :</h2>";
echo "test_var = " . $_SESSION['test_var'] . "<br>";
?>