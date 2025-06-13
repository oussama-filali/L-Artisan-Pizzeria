<?php
// Afficher les informations sur la configuration PHP
echo "<h1>Configuration PHP</h1>";

// Vérifier si les sessions sont activées
echo "<h2>Configuration des sessions</h2>";
echo "session.save_path: " . ini_get('session.save_path') . "<br>";
echo "session.use_cookies: " . ini_get('session.use_cookies') . "<br>";
echo "session.use_only_cookies: " . ini_get('session.use_only_cookies') . "<br>";
echo "session.use_strict_mode: " . ini_get('session.use_strict_mode') . "<br>";
echo "session.cookie_lifetime: " . ini_get('session.cookie_lifetime') . "<br>";
echo "session.cookie_path: " . ini_get('session.cookie_path') . "<br>";
echo "session.cookie_domain: " . ini_get('session.cookie_domain') . "<br>";
echo "session.cookie_secure: " . ini_get('session.cookie_secure') . "<br>";
echo "session.cookie_httponly: " . ini_get('session.cookie_httponly') . "<br>";
echo "session.gc_maxlifetime: " . ini_get('session.gc_maxlifetime') . "<br>";

// Vérifier les permissions du répertoire de session
$session_path = ini_get('session.save_path');
if (!empty($session_path)) {
    echo "<h2>Permissions du répertoire de session</h2>";
    echo "Chemin: " . $session_path . "<br>";
    echo "Existe: " . (file_exists($session_path) ? "Oui" : "Non") . "<br>";
    if (file_exists($session_path)) {
        echo "Est un répertoire: " . (is_dir($session_path) ? "Oui" : "Non") . "<br>";
        echo "Permissions: " . substr(sprintf('%o', fileperms($session_path)), -4) . "<br>";
        echo "Accessible en lecture: " . (is_readable($session_path) ? "Oui" : "Non") . "<br>";
        echo "Accessible en écriture: " . (is_writable($session_path) ? "Oui" : "Non") . "<br>";
    }
}

// Tester la création d'une session
echo "<h2>Test de création de session</h2>";
session_start();
$_SESSION['test'] = "Test à " . date('H:i:s');
echo "Variable de session créée: test = " . $_SESSION['test'] . "<br>";
echo "ID de session: " . session_id() . "<br>";

// Vérifier les headers
echo "<h2>Headers HTTP</h2>";
echo "<pre>";
var_dump(headers_list());
echo "</pre>";

// Vérifier la configuration du serveur
echo "<h2>Configuration du serveur</h2>";
echo "SERVER_SOFTWARE: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "SERVER_NAME: " . $_SERVER['SERVER_NAME'] . "<br>";
echo "SERVER_ADDR: " . $_SERVER['SERVER_ADDR'] . "<br>";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "SCRIPT_FILENAME: " . $_SERVER['SCRIPT_FILENAME'] . "<br>";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "<br>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";

// Vérifier les extensions PHP
echo "<h2>Extensions PHP</h2>";
echo "Extensions chargées: <br>";
$extensions = get_loaded_extensions();
sort($extensions);
echo implode(", ", $extensions);
?>