<?php
// Fichier pour enregistrer les statistiques de visite

// Inclure le fichier de configuration
require_once "admin/config.php";

// Récupérer les informations de la visite
$page = isset($_GET['page']) ? $_GET['page'] : 'index';
$ip = $_SERVER['REMOTE_ADDR'];
$userAgent = $_SERVER['HTTP_USER_AGENT'];

// Insérer les données dans la base de données
$sql = "INSERT INTO stats (page, ip_address, user_agent) VALUES (?, ?, ?)";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("sss", $page, $ip, $userAgent);
    $stmt->execute();
    $stmt->close();
}

// Fermer la connexion
$mysqli->close();

// Retourner une image transparente 1x1 pixel
header('Content-Type: image/gif');
echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
?>