<?php
// Inclure le fichier de configuration
require_once "config.php";

// Définir un nom d'utilisateur et un mot de passe de test
$test_username = "admin";
$test_password = "admin123"; // Utilisateur de test créé

echo "Test d'authentification pour l'utilisateur: " . $test_username . "<br>";

// Préparer une instruction select
$sql = "SELECT id, username, password, role FROM users WHERE username = ?";

if ($stmt = $mysqli->prepare($sql)) {
    // Lier les variables à l'instruction préparée en tant que paramètres
    $stmt->bind_param("s", $param_username);
    
    // Définir les paramètres
    $param_username = $test_username;
    
    // Tenter d'exécuter l'instruction préparée
    if ($stmt->execute()) {
        // Stocker le résultat
        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            echo "Utilisateur trouvé dans la base de données.<br>";
            
            // Lier les variables de résultat
            $stmt->bind_result($id, $username, $hashed_password, $role);
            
            if ($stmt->fetch()) {
                echo "Mot de passe haché en base de données: " . $hashed_password . "<br>";
                
                // Vérifier le mot de passe
                if (password_verify($test_password, $hashed_password)) {
                    echo "Authentification réussie! Le mot de passe est correct.<br>";
                    echo "ID: " . $id . "<br>";
                    echo "Nom d'utilisateur: " . $username . "<br>";
                    echo "Rôle: " . $role . "<br>";
                } else {
                    echo "Échec de l'authentification. Le mot de passe est incorrect.<br>";
                    
                    // Créer un nouveau mot de passe haché pour référence
                    echo "Nouveau hachage pour 'password123': " . password_hash("password123", PASSWORD_DEFAULT) . "<br>";
                }
            }
        } else {
            echo "Aucun utilisateur trouvé avec ce nom d'utilisateur.<br>";
        }
    } else {
        echo "Erreur lors de l'exécution de la requête.<br>";
    }
    
    // Fermer la déclaration
    $stmt->close();
} else {
    echo "Erreur lors de la préparation de la requête.<br>";
}

// Fermer la connexion
$mysqli->close();
?>