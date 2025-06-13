<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    header('Location: index.php');
    exit;
}

// Script d'installation pour créer la base de données et les tables nécessaires

// Paramètres de connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";

// Créer la connexion
$conn = new mysqli($servername, $username, $password);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Créer la base de données
$sql = "CREATE DATABASE IF NOT EXISTS lartisan_pizzeria";
if ($conn->query($sql) === TRUE) {
    echo "Base de données créée avec succès<br>";
} else {
    echo "Erreur lors de la création de la base de données : " . $conn->error . "<br>";
}

// Sélectionner la base de données
$conn->select_db("lartisan_pizzeria");

// Créer la table des utilisateurs
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    role VARCHAR(20) DEFAULT 'admin'
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'users' créée avec succès<br>";
} else {
    echo "Erreur lors de la création de la table 'users' : " . $conn->error . "<br>";
}

// Créer la table des statistiques
$sql = "CREATE TABLE IF NOT EXISTS stats (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    page VARCHAR(100) NOT NULL,
    ip_address VARCHAR(50) NOT NULL,
    user_agent TEXT,
    visit_date DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'stats' créée avec succès<br>";
} else {
    echo "Erreur lors de la création de la table 'stats' : " . $conn->error . "<br>";
}

// Vérifier si un utilisateur admin existe déjà
$sql = "SELECT id FROM users WHERE username = 'superadmin'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    // Créer un utilisateur admin par défaut
    $admin_username = "adminpizza";
    $admin_password = password_hash("admin123", PASSWORD_DEFAULT); // Mot de passe par défaut: admin123
    $admin_email = "";
    
    $sql = "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'admin')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $admin_username, $admin_password, $admin_email);
    
    if ($stmt->execute()) {
        echo "Utilisateur admin créé avec succès<br>";
        echo "Nom d'utilisateur: admin<br>";
        echo "Mot de passe: admin123<br>";
        echo "<strong>IMPORTANT: Veuillez changer ce mot de passe dès que possible!</strong><br>";
    } else {
        echo "Erreur lors de la création de l'utilisateur admin : " . $stmt->error . "<br>";
    }
    
    $stmt->close();
} else {
    echo "L'utilisateur admin existe déjà<br>";
}

// Fermer la connexion
$conn->close();

echo "<br>Installation terminée!<br>";
echo "<a href='index.php'>Aller à la page de connexion</a>";
?>