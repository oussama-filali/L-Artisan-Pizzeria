<?php
// Initialiser la session
session_start();

// Vérifier si l'utilisateur est connecté, sinon le rediriger vers la page de connexion
if(!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true){
    header("location: index.php");
    exit;
}

// Inclure le fichier de configuration
require_once "config.php";

// Initialiser les variables
$action = isset($_GET['action']) ? $_GET['action'] : '';
$message = '';
$messageType = '';

// Traitement des actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ajouter un nouvel utilisateur
    if ($action == 'add') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $email = trim($_POST['email']);
        $role = trim($_POST['role']);
        
        // Vérifier si le nom d'utilisateur existe déjà
        $sql = "SELECT id FROM users WHERE username = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $message = "Ce nom d'utilisateur existe déjà.";
                $messageType = "danger";
            } else {
                // Insérer le nouvel utilisateur
                $sql = "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)";
                if ($stmt = $mysqli->prepare($sql)) {
                    // Hacher le mot de passe
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    $stmt->bind_param("ssss", $username, $hashed_password, $email, $role);
                    
                    if ($stmt->execute()) {
                        $message = "L'utilisateur a été ajouté avec succès.";
                        $messageType = "success";
                        // Rediriger pour éviter la resoumission du formulaire
                        header("Location: users.php?message=$message&messageType=$messageType");
                        exit;
                    } else {
                        $message = "Erreur lors de l'ajout de l'utilisateur.";
                        $messageType = "danger";
                    }
                }
            }
            
            $stmt->close();
        }
    }
    
    // Modifier un utilisateur existant
    if ($action == 'edit' && isset($_POST['id'])) {
        $id = $_POST['id'];
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $role = trim($_POST['role']);
        $password = trim($_POST['password']);
        
        // Vérifier si le nom d'utilisateur existe déjà (sauf pour l'utilisateur actuel)
        $sql = "SELECT id FROM users WHERE username = ? AND id != ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("si", $username, $id);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $message = "Ce nom d'utilisateur existe déjà.";
                $messageType = "danger";
            } else {
                // Mettre à jour l'utilisateur
                if (!empty($password)) {
                    // Si un nouveau mot de passe est fourni
                    $sql = "UPDATE users SET username = ?, email = ?, role = ?, password = ? WHERE id = ?";
                    if ($stmt = $mysqli->prepare($sql)) {
                        // Hacher le mot de passe
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        $stmt->bind_param("ssssi", $username, $email, $role, $hashed_password, $id);
                        
                        if ($stmt->execute()) {
                            $message = "L'utilisateur a été mis à jour avec succès.";
                            $messageType = "success";
                            // Rediriger pour éviter la resoumission du formulaire
                            header("Location: users.php?message=$message&messageType=$messageType");
                            exit;
                        } else {
                            $message = "Erreur lors de la mise à jour de l'utilisateur.";
                            $messageType = "danger";
                        }
                    }
                } else {
                    // Si aucun nouveau mot de passe n'est fourni
                    $sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
                    if ($stmt = $mysqli->prepare($sql)) {
                        $stmt->bind_param("sssi", $username, $email, $role, $id);
                        
                        if ($stmt->execute()) {
                            $message = "L'utilisateur a été mis à jour avec succès.";
                            $messageType = "success";
                            // Rediriger pour éviter la resoumission du formulaire
                            header("Location: users.php?message=$message&messageType=$messageType");
                            exit;
                        } else {
                            $message = "Erreur lors de la mise à jour de l'utilisateur.";
                            $messageType = "danger";
                        }
                    }
                }
            }
            
            $stmt->close();
        }
    }
    
    // Supprimer un utilisateur
    if ($action == 'delete' && isset($_POST['id'])) {
        $id = $_POST['id'];
        
        // Empêcher la suppression de l'utilisateur connecté
        if ($id == $_SESSION['id']) {
            $message = "Vous ne pouvez pas supprimer votre propre compte.";
            $messageType = "danger";
        } else {
            $sql = "DELETE FROM users WHERE id = ?";
            if ($stmt = $mysqli->prepare($sql)) {
                $stmt->bind_param("i", $id);
                
                if ($stmt->execute()) {
                    $message = "L'utilisateur a été supprimé avec succès.";
                    $messageType = "success";
                    // Rediriger pour éviter la resoumission du formulaire
                    header("Location: users.php?message=$message&messageType=$messageType");
                    exit;
                } else {
                    $message = "Erreur lors de la suppression de l'utilisateur.";
                    $messageType = "danger";
                }
                
                $stmt->close();
            }
        }
    }
}

// Récupérer les messages de la redirection
if (isset($_GET['message']) && isset($_GET['messageType'])) {
    $message = $_GET['message'];
    $messageType = $_GET['messageType'];
}

// Récupérer la liste des utilisateurs
$users = [];
$sql = "SELECT id, username, email, role, created_at FROM users ORDER BY id";
if ($result = $mysqli->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $result->free();
}

// Préparer les données pour l'édition
$editUser = null;

if ($action == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT id, username, email, role FROM users WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $editUser = $result->fetch_assoc();
        }
        
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - L'Artisan Pizzeria</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #101014 0%, #18181c 100%);
            min-height: 100vh;
            color: #fff;
        }
        .sidebar {
            background: rgba(20, 20, 24, 0.92);
            border-radius: 0 18px 18px 0;
            box-shadow: 0 8px 32px 0 rgba(0,0,0,0.45);
            padding: 20px 0;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
        }
        .sidebar-logo {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar-logo img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ff6347;
        }
        .sidebar-menu {
            padding: 20px 0;
        }
        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: #fff;
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid #ff6347;
        }
        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .card {
            background: rgba(20, 20, 24, 0.92);
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(0,0,0,0.45);
            margin-bottom: 20px;
            border: none;
        }
        .card-header {
            background: rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 18px 18px 0 0 !important;
        }
        .card-body {
            color: #fff;
        }
        .btn-primary {
            background-color: #ff6347;
            border: none;
        }
        .btn-primary:hover {
            background-color: #e5533d;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .table {
            color: #fff;
        }
        .table thead th {
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }
        .table td, .table th {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            vertical-align: middle;
        }
        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
            border-radius: 10px;
        }
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            box-shadow: none;
        }
        .logout-btn {
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
        }
        .logout-btn a {
            color: #fff;
            text-decoration: none;
            padding: 10px;
            display: inline-block;
        }
        .logout-btn a:hover {
            color: #ff6347;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                border-radius: 0;
            }
            .main-content {
                margin-left: 0;
            }
            .logout-btn {
                position: relative;
                bottom: auto;
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="../assets/img/lartisan pizza.png" alt="Logo L'Artisan Pizzeria">
            <h5 class="mt-2">L'Artisan Pizzeria</h5>
            <p class="small">Administration</p>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
            <a href="pizzas.php"><i class="fas fa-pizza-slice"></i> Gestion des pizzas</a>
            <a href="users.php" class="active"><i class="fas fa-users"></i> Gestion des utilisateurs</a>
            <a href="content.php"><i class="fas fa-edit"></i> Contenu du site</a>
            <a href="media.php"><i class="fas fa-photo-video"></i> Médias</a>
            <a href="stats.php"><i class="fas fa-chart-bar"></i> Statistiques</a>
        </div>
        <div class="logout-btn">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-12">
                    <h2><i class="fas fa-users mr-2"></i>Gestion des Utilisateurs</h2>
                    <p>Gérez les utilisateurs qui ont accès au panneau d'administration.</p>
                </div>
            </div>

            <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>

            <!-- Add/Edit Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><?php echo ($action == 'edit') ? 'Modifier' : 'Ajouter'; ?> un utilisateur</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="users.php?action=<?php echo $action; ?>">
                        <?php if ($action == 'edit'): ?>
                            <input type="hidden" name="id" value="<?php echo $editUser['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="username">Nom d'utilisateur</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo ($editUser) ? htmlspecialchars($editUser['username']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo ($editUser) ? htmlspecialchars($editUser['email']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Mot de passe <?php echo ($action == 'edit') ? '(laisser vide pour ne pas changer)' : ''; ?></label>
                            <input type="password" class="form-control" id="password" name="password" <?php echo ($action == 'add') ? 'required' : ''; ?>>
                        </div>
                        
                        <div class="form-group">
                            <label for="role">Rôle</label>
                            <select class="form-control" id="role" name="role">
                                <option value="admin" <?php echo ($editUser && $editUser['role'] == 'admin') ? 'selected' : ''; ?>>Administrateur</option>
                                <option value="editor" <?php echo ($editUser && $editUser['role'] == 'editor') ? 'selected' : ''; ?>>Éditeur</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <?php echo ($action == 'edit') ? 'Mettre à jour' : 'Ajouter'; ?>
                            </button>
                            <?php if ($action == 'edit'): ?>
                                <a href="users.php" class="btn btn-secondary">Annuler</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users List -->
            <div class="card">
                <div class="card-header">
                    <h5>Liste des utilisateurs</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($users)): ?>
                        <p class="text-center">Aucun utilisateur trouvé.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nom d'utilisateur</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                        <th>Date de création</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td>
                                                <?php if ($user['role'] == 'admin'): ?>
                                                    <span class="badge badge-primary">Administrateur</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Éditeur</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                                            <td>
                                                <a href="users.php?action=edit&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if ($user['id'] != $_SESSION['id']): ?>
                                                    <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal<?php echo $user['id']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    
                                                    <!-- Delete Confirmation Modal -->
                                                    <div class="modal fade" id="deleteModal<?php echo $user['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel<?php echo $user['id']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content bg-dark text-white">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="deleteModalLabel<?php echo $user['id']; ?>">Confirmer la suppression</h5>
                                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Êtes-vous sûr de vouloir supprimer l'utilisateur <strong><?php echo htmlspecialchars($user['username']); ?></strong> ?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                                                    <form method="post" action="users.php?action=delete">
                                                                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>