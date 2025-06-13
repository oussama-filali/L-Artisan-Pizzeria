<?php
// Inclure le fichier de configuration
require_once "config.php";

// Définir les informations de l'utilisateur de test
$username = "admin";
$password = "admin123"; // Mot de passe simple pour les tests
$email = "admin@example.com";
$role = "admin";

// Hacher le mot de passe
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Vérifier si l'utilisateur existe déjà
$check_sql = "SELECT id FROM users WHERE username = ?";
if ($check_stmt = $mysqli->prepare($check_sql)) {
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        echo "L'utilisateur '$username' existe déjà. Mise à jour du mot de passe...<br>";
        
        // Mettre à jour le mot de passe
        $update_sql = "UPDATE users SET password = ? WHERE username = ?";
        if ($update_stmt = $mysqli->prepare($update_sql)) {
            $update_stmt->bind_param("ss", $hashed_password, $username);
            
            if ($update_stmt->execute()) {
                echo "Mot de passe mis à jour avec succès!<br>";
                echo "Nom d'utilisateur: $username<br>";
                echo "Mot de passe: $password<br>";
                echo "Rôle: $role<br>";
            } else {
                echo "Erreur lors de la mise à jour du mot de passe: " . $mysqli->error . "<br>";
            }
            
            $update_stmt->close();
        }
    } else {
        echo "Création d'un nouvel utilisateur...<br>";
        
        // Préparer une instruction d'insertion
        $insert_sql = "INSERT INTO users (username, password, email, role, created_at) VALUES (?, ?, ?, ?, NOW())";
        
        if ($insert_stmt = $mysqli->prepare($insert_sql)) {
            // Lier les variables à l'instruction préparée
            $insert_stmt->bind_param("ssss", $username, $hashed_password, $email, $role);
            
            // Exécuter l'instruction préparée
            if ($insert_stmt->execute()) {
                echo "Utilisateur créé avec succès!<br>";
                echo "Nom d'utilisateur: $username<br>";
                echo "Mot de passe: $password<br>";
                echo "Rôle: $role<br>";
            } else {
                echo "Erreur lors de la création de l'utilisateur: " . $mysqli->error . "<br>";
            }
            
            // Fermer la déclaration
            $insert_stmt->close();
        }
    }
    
    $check_stmt->close();
}

// Fermer la connexion
$mysqli->close();
?>