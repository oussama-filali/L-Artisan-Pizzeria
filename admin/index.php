<?php
session_start();

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Définir les variables et initialiser avec des valeurs vides
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Traitement du formulaire lors de la soumission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Vérifier si le nom d'utilisateur est vide
    if (empty(trim($_POST["username"]))) {
        $username_err = "Veuillez entrer un nom d'utilisateur.";
    } else {
        $username = trim($_POST["username"]);
    }
    
    // Vérifier si le mot de passe est vide
    if (empty(trim($_POST["password"]))) {
        $password_err = "Veuillez entrer votre mot de passe.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Valider les identifiants
    if (empty($username_err) && empty($password_err)) {
        // Inclure le fichier de configuration
        require_once "config.php";
        
        // Préparer une instruction select
        $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
        
        if ($stmt = $mysqli->prepare($sql)) {
            // Lier les variables à l'instruction préparée en tant que paramètres
            $stmt->bind_param("s", $param_username);
            
            // Définir les paramètres
            $param_username = $username;
            
            // Tenter d'exécuter l'instruction préparée
            if ($stmt->execute()) {
                // Stocker le résultat
                $stmt->store_result();
                
                // Utilisation d'un switch pour gérer les différents cas d'authentification
                switch (true) {
                    // Cas 1: Nom d'utilisateur inexistant
                    case $stmt->num_rows != 1:
                        $login_err = "Nom d'utilisateur ou mot de passe invalide.";
                        break;
                        
                    // Cas 2: Nom d'utilisateur existe, vérification du mot de passe
                    case $stmt->num_rows == 1:
                        // Lier les variables de résultat
                        $stmt->bind_result($id, $username, $hashed_password, $role);
                        
                        if ($stmt->fetch()) {
                            // Sous-cas 2.1: Mot de passe correct
                            if (password_verify($password, $hashed_password)) {
                                // Le mot de passe est correct
                                
                                // Stocker les données dans des variables de session
                                $_SESSION["admin_logged_in"] = true;
                                $_SESSION["User_id"] = $id;
                                $_SESSION["username"] = $username;
                                $_SESSION["role"] = $role;
                                
                                // Ajouter un message de débogage
                                $_SESSION["debug_time"] = date('H:i:s');
                                $_SESSION["auth_success"] = true;
                                
                                // Rediriger l'utilisateur vers la page d'accueil
                                header("Location: dashboard.php");
                                exit(); // Important pour arrêter l'exécution après la redirection
                            } 
                            // Sous-cas 2.2: Mot de passe incorrect
                            else {
                                $login_err = "Nom d'utilisateur ou mot de passe invalide.";
                            }
                        }
                        break;
                }
            } else {
                echo "Oups! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
            }

            // Fermer la déclaration
            $stmt->close();
        }
        
        // Fermer la connexion
        $mysqli->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - L'Artisan Pizzeria</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #101014 0%, #18181c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        .login-container {
            background: rgba(20, 20, 24, 0.92);
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(0,0,0,0.45);
            padding: 40px;
            width: 100%;
            max-width: 450px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ff6347;
        }
        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
        }
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            box-shadow: none;
        }
        .btn-login {
            background-color: #ff6347;
            border: none;
            color: #fff;
            padding: 12px;
            border-radius: 10px;
            width: 100%;
            font-weight: bold;
            margin-top: 10px;
        }
        .btn-login:hover {
            background-color: #e5533d;
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <img src="../assets/img/lartisan pizza.png" alt="Logo L'Artisan Pizzeria">
            <h2 class="mt-3">Administration</h2>
        </div>
        
        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Nom d'utilisateur</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-login">Connexion</button>
            </div>
        </form>
        
        <div class="text-center mt-3">
            <a href="../index.html" class="text-white"><i class="fas fa-arrow-left mr-2"></i>Retour au site</a>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>