<?php
// Initialiser la session
session_start();

// Vérifier si l'utilisateur est connecté, sinon le rediriger vers la page de connexion
if(!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true){
    header("location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - L'Artisan Pizzeria</title>
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
        .stats-card {
            background: linear-gradient(45deg, #ff6347, #ff8c7a);
            border-radius: 18px;
            padding: 20px;
            margin-bottom: 20px;
            color: #fff;
            box-shadow: 0 8px 32px 0 rgba(0,0,0,0.45);
        }
        .stats-card i {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .stats-card h3 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .stats-card p {
            margin: 0;
            opacity: 0.8;
        }
        .btn-primary {
            background-color: #ff6347;
            border: none;
        }
        .btn-primary:hover {
            background-color: #e5533d;
        }
        .table {
            color: #fff;
        }
        .table thead th {
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }
        .table td, .table th {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
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
            <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
            <a href="pizzas.php"><i class="fas fa-pizza-slice"></i> Gestion des pizzas</a>
            <a href="users.php"><i class="fas fa-users"></i> Gestion des utilisateurs</a>
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
                    <h2><i class="fas fa-tachometer-alt mr-2"></i>Tableau de bord</h2>
                    <p>Bienvenue, <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong>. Gérez votre site depuis ce panneau d'administration.</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <i class="fas fa-pizza-slice"></i>
                        <h3>42</h3>
                        <p>Pizzas au menu</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <i class="fas fa-users"></i>
                        <h3>3</h3>
                        <p>Utilisateurs</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <i class="fas fa-eye"></i>
                        <h3>1,254</h3>
                        <p>Visites ce mois</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <i class="fas fa-photo-video"></i>
                        <h3>18</h3>
                        <p>Médias</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-bolt mr-2"></i>Actions rapides</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <a href="pizzas.php?action=add" class="btn btn-primary btn-block"><i class="fas fa-plus mr-2"></i>Ajouter une pizza</a>
                                </div>
                                <div class="col-6 mb-3">
                                    <a href="users.php?action=add" class="btn btn-primary btn-block"><i class="fas fa-user-plus mr-2"></i>Ajouter un utilisateur</a>
                                </div>
                                <div class="col-6 mb-3">
                                    <a href="content.php" class="btn btn-primary btn-block"><i class="fas fa-edit mr-2"></i>Modifier le contenu</a>
                                </div>
                                <div class="col-6 mb-3">
                                    <a href="media.php?action=upload" class="btn btn-primary btn-block"><i class="fas fa-upload mr-2"></i>Ajouter un média</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-history mr-2"></i>Activité récente</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush bg-transparent">
                                <li class="list-group-item bg-transparent text-white border-light">
                                    <i class="fas fa-user-edit mr-2"></i> Modification du profil utilisateur
                                    <small class="text-muted d-block">Il y a 2 heures</small>
                                </li>
                                <li class="list-group-item bg-transparent text-white border-light">
                                    <i class="fas fa-pizza-slice mr-2"></i> Ajout d'une nouvelle pizza
                                    <small class="text-muted d-block">Il y a 1 jour</small>
                                </li>
                                <li class="list-group-item bg-transparent text-white border-light">
                                    <i class="fas fa-upload mr-2"></i> Téléchargement d'une nouvelle image
                                    <small class="text-muted d-block">Il y a 3 jours</small>
                                </li>
                                <li class="list-group-item bg-transparent text-white border-light">
                                    <i class="fas fa-edit mr-2"></i> Mise à jour du contenu de la page d'accueil
                                    <small class="text-muted d-block">Il y a 1 semaine</small>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-server mr-2"></i>Informations système</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Version PHP</td>
                                            <td><?php echo phpversion(); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Serveur Web</td>
                                            <td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Base de données</td>
                                            <td>MySQL</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Espace disque</td>
                                            <td><?php echo round(disk_free_space("/") / 1024 / 1024 / 1024, 2); ?> GB libre</td>
                                        </tr>
                                        <tr>
                                            <td>Dernière mise à jour</td>
                                            <td><?php echo date("d/m/Y H:i", filemtime(__FILE__)); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Version du site</td>
                                            <td>1.0</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>