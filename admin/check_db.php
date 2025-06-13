<?php
// Inclure le fichier de configuration
require_once "config.php";

// Vérifier si la table users existe
$sql = "SHOW TABLES LIKE 'users'";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    echo "La table 'users' existe.<br>";
    
    // Vérifier les utilisateurs dans la table
    $sql = "SELECT id, username, role FROM users";
    $result = $mysqli->query($sql);
    
    if ($result->num_rows > 0) {
        echo "Utilisateurs trouvés (" . $result->num_rows . "):<br>";
        while($row = $result->fetch_assoc()) {
            echo "ID: " . $row["id"] . " - Nom: " . $row["username"] . " - Rôle: " . $row["role"] . "<br>";
        }
    } else {
        echo "Aucun utilisateur trouvé dans la table 'users'.<br>";
    }
} else {
    echo "La table 'users' n'existe pas.<br>";
}

// Vérifier la structure de la table users
$sql = "DESCRIBE users";
$result = $mysqli->query($sql);

if ($result) {
    echo "<br>Structure de la table 'users':<br>";
    while($row = $result->fetch_assoc()) {
        echo $row["Field"] . " - " . $row["Type"] . " - " . $row["Key"] . "<br>";
    }
} else {
    echo "<br>Impossible d'obtenir la structure de la table 'users'.<br>";
}

// Fermer la connexion
$mysqli->close();
?>