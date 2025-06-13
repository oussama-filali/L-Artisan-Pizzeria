<?php
// Initialiser la session
session_start();

// Vérifier si l'utilisateur est connecté, sinon le rediriger vers la page de connexion
if(!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true){
    // Ajouter des informations de débogage
    echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border-radius: 5px;'>";
    echo "<h3>Erreur d'authentification</h3>";
    echo "<p>Vous n'êtes pas connecté ou votre session a expiré.</p>";
    echo "<p>Variables de session :</p>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    echo "<p><a href='index.php'>Retourner à la page de connexion</a></p>";
    echo "</div>";
    exit;
}

// Fonction pour lire les fichiers JSON
function readJsonFile($file) {
    $jsonFile = "../assets/data/" . $file;
    if (file_exists($jsonFile)) {
        $jsonContent = file_get_contents($jsonFile);
        return json_decode($jsonContent, true);
    }
    return [];
}

// Charger les données statistiques
$statsData = readJsonFile("stats.json");

// Charger les données des produits
$pizzasTomate = readJsonFile("pizzas_tomate.json");
$pizzasCreme = readJsonFile("pizzas_creme.json");
$desserts = readJsonFile("desserts.json");
$boissons = readJsonFile("boissons.json");

// Calculer les totaux
$totalPizzas = count($pizzasTomate) + count($pizzasCreme);
$totalDesserts = count($desserts);
$totalBoissons = count($boissons);
$totalProduits = $totalPizzas + $totalDesserts + $totalBoissons;
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
                        <h3><?php echo $totalPizzas; ?></h3>
                        <p>Pizzas au menu</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <i class="fas fa-users"></i>
                        <h3><?php echo $statsData['users']['total']; ?></h3>
                        <p>Utilisateurs</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <i class="fas fa-eye"></i>
                        <h3><?php echo number_format($statsData['visits']['total']); ?></h3>
                        <p>Visites ce mois</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <i class="fas fa-utensils"></i>
                        <h3><?php echo $totalProduits; ?></h3>
                        <p>Produits totaux</p>
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
                                <?php foreach ($statsData['users']['activity'] as $activity): ?>
                                <li class="list-group-item bg-transparent text-white border-light">
                                    <?php 
                                    $icon = 'fa-history';
                                    if (strpos($activity['action'], 'profil') !== false) {
                                        $icon = 'fa-user-edit';
                                    } elseif (strpos($activity['action'], 'pizza') !== false) {
                                        $icon = 'fa-pizza-slice';
                                    } elseif (strpos($activity['action'], 'image') !== false || strpos($activity['action'], 'téléchargement') !== false) {
                                        $icon = 'fa-upload';
                                    } elseif (strpos($activity['action'], 'contenu') !== false) {
                                        $icon = 'fa-edit';
                                    }
                                    ?>
                                    <i class="fas <?php echo $icon; ?> mr-2"></i> <?php echo $activity['action']; ?>
                                    <small class="text-muted d-block">
                                        <?php 
                                        $date = new DateTime($activity['date']);
                                        $now = new DateTime();
                                        $interval = $date->diff($now);
                                        
                                        if ($interval->d == 0) {
                                            if ($interval->h == 0) {
                                                echo "Il y a " . $interval->i . " minutes";
                                            } else {
                                                echo "Il y a " . $interval->h . " heures";
                                            }
                                        } elseif ($interval->d == 1) {
                                            echo "Il y a 1 jour";
                                        } elseif ($interval->d < 7) {
                                            echo "Il y a " . $interval->d . " jours";
                                        } else {
                                            echo "Il y a " . floor($interval->d / 7) . " semaine(s)";
                                        }
                                        ?>
                                    </small>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popular Products Chart -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-bar mr-2"></i>Pizzas les plus populaires</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="popularProductsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- System Info -->
                <div class="col-md-6">
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
                                            <td>JSON</td>
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
            
            <!-- Product Categories -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-th-large mr-2"></i>Répartition des produits par catégorie</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="chart-container">
                                        <canvas id="categoriesChart"></canvas>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Catégorie</th>
                                                <th>Nombre</th>
                                                <th>%</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Pizzas Base Tomate</td>
                                                <td><?php echo count($pizzasTomate); ?></td>
                                                <td><?php echo round(count($pizzasTomate) / $totalProduits * 100); ?>%</td>
                                            </tr>
                                            <tr>
                                                <td>Pizzas Base Crème</td>
                                                <td><?php echo count($pizzasCreme); ?></td>
                                                <td><?php echo round(count($pizzasCreme) / $totalProduits * 100); ?>%</td>
                                            </tr>
                                            <tr>
                                                <td>Desserts</td>
                                                <td><?php echo count($desserts); ?></td>
                                                <td><?php echo round(count($desserts) / $totalProduits * 100); ?>%</td>
                                            </tr>
                                            <tr>
                                                <td>Boissons</td>
                                                <td><?php echo count($boissons); ?></td>
                                                <td><?php echo round(count($boissons) / $totalProduits * 100); ?>%</td>
                                            </tr>
                                        </tbody>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
    // Données pour les graphiques
    const popularProducts = <?php echo json_encode($statsData['products']['popular']); ?>;
    
    // Graphique des produits populaires
    const popularProductsCtx = document.getElementById('popularProductsChart').getContext('2d');
    const popularProductsChart = new Chart(popularProductsCtx, {
        type: 'bar',
        data: {
            labels: popularProducts.map(item => item.nom),
            datasets: [{
                label: 'Nombre de ventes',
                data: popularProducts.map(item => item.ventes),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    }
                }
            }
        }
    });
    
    // Graphique des catégories de produits
    const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
    const categoriesChart = new Chart(categoriesCtx, {
        type: 'pie',
        data: {
            labels: ['Pizzas Base Tomate', 'Pizzas Base Crème', 'Desserts', 'Boissons'],
            datasets: [{
                data: [
                    <?php echo count($pizzasTomate); ?>, 
                    <?php echo count($pizzasCreme); ?>, 
                    <?php echo count($desserts); ?>, 
                    <?php echo count($boissons); ?>
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    }
                }
            }
        }
    });
    </script>
</body>
</html>