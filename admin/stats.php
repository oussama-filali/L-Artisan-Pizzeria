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
$period = isset($_GET['period']) ? $_GET['period'] : 'month';

// Déterminer la période pour les statistiques
$startDate = '';
$endDate = date('Y-m-d H:i:s');
$periodLabel = '';

switch ($period) {
    case 'day':
        $startDate = date('Y-m-d 00:00:00');
        $periodLabel = "Aujourd'hui";
        break;
    case 'week':
        $startDate = date('Y-m-d 00:00:00', strtotime('-7 days'));
        $periodLabel = "Cette semaine";
        break;
    case 'month':
        $startDate = date('Y-m-d 00:00:00', strtotime('-30 days'));
        $periodLabel = "Ce mois";
        break;
    case 'year':
        $startDate = date('Y-m-d 00:00:00', strtotime('-365 days'));
        $periodLabel = "Cette année";
        break;
    default:
        $startDate = date('Y-m-d 00:00:00', strtotime('-30 days'));
        $periodLabel = "Ce mois";
}

// Récupérer les statistiques de visite
$totalVisits = 0;
$sql = "SELECT COUNT(*) as total FROM stats WHERE visit_date BETWEEN ? AND ?";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $totalVisits = $row['total'];
    }
    $stmt->close();
}

// Récupérer les statistiques par page
$pageStats = [];
$sql = "SELECT page, COUNT(*) as count FROM stats WHERE visit_date BETWEEN ? AND ? GROUP BY page ORDER BY count DESC";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $pageStats[] = $row;
    }
    $stmt->close();
}

// Récupérer les statistiques par jour
$dailyStats = [];
$sql = "SELECT DATE(visit_date) as date, COUNT(*) as count FROM stats WHERE visit_date BETWEEN ? AND ? GROUP BY DATE(visit_date) ORDER BY date";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $dailyStats[] = $row;
    }
    $stmt->close();
}

// Récupérer les statistiques par navigateur
$browserStats = [];
$sql = "SELECT 
            CASE 
                WHEN user_agent LIKE '%Chrome%' THEN 'Chrome'
                WHEN user_agent LIKE '%Firefox%' THEN 'Firefox'
                WHEN user_agent LIKE '%Safari%' THEN 'Safari'
                WHEN user_agent LIKE '%Edge%' THEN 'Edge'
                WHEN user_agent LIKE '%MSIE%' OR user_agent LIKE '%Trident%' THEN 'Internet Explorer'
                ELSE 'Autre'
            END as browser,
            COUNT(*) as count
        FROM stats 
        WHERE visit_date BETWEEN ? AND ?
        GROUP BY browser
        ORDER BY count DESC";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $browserStats[] = $row;
    }
    $stmt->close();
}

// Fermer la connexion
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques - L'Artisan Pizzeria</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .btn-outline-primary {
            color: #ff6347;
            border-color: #ff6347;
        }
        .btn-outline-primary:hover, .btn-outline-primary.active {
            background-color: #ff6347;
            border-color: #ff6347;
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
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
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
            <a href="users.php"><i class="fas fa-users"></i> Gestion des utilisateurs</a>
            <a href="content.php"><i class="fas fa-edit"></i> Contenu du site</a>
            <a href="media.php"><i class="fas fa-photo-video"></i> Médias</a>
            <a href="stats.php" class="active"><i class="fas fa-chart-bar"></i> Statistiques</a>
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
                    <h2><i class="fas fa-chart-bar mr-2"></i>Statistiques</h2>
                    <p>Consultez les statistiques de visite de votre site.</p>
                </div>
            </div>

            <!-- Period Selection -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="btn-group" role="group">
                        <a href="stats.php?period=day" class="btn btn-outline-primary <?php echo ($period == 'day') ? 'active' : ''; ?>">Aujourd'hui</a>
                        <a href="stats.php?period=week" class="btn btn-outline-primary <?php echo ($period == 'week') ? 'active' : ''; ?>">Cette semaine</a>
                        <a href="stats.php?period=month" class="btn btn-outline-primary <?php echo ($period == 'month') ? 'active' : ''; ?>">Ce mois</a>
                        <a href="stats.php?period=year" class="btn btn-outline-primary <?php echo ($period == 'year') ? 'active' : ''; ?>">Cette année</a>
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="row">
                <div class="col-md-4">
                    <div class="stats-card text-center">
                        <i class="fas fa-eye"></i>
                        <h3><?php echo $totalVisits; ?></h3>
                        <p>Visites totales</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card text-center">
                        <i class="fas fa-file"></i>
                        <h3><?php echo count($pageStats); ?></h3>
                        <p>Pages visitées</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card text-center">
                        <i class="fas fa-calendar-day"></i>
                        <h3><?php echo count($dailyStats); ?></h3>
                        <p>Jours d'activité</p>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>Visites par jour - <?php echo $periodLabel; ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="dailyChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Navigateurs</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="browserChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Stats -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Pages les plus visitées</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($pageStats)): ?>
                                <p class="text-center">Aucune donnée disponible.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Page</th>
                                                <th>Visites</th>
                                                <th>Pourcentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pageStats as $page): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($page['page']); ?></td>
                                                    <td><?php echo $page['count']; ?></td>
                                                    <td>
                                                        <?php 
                                                            $percentage = ($totalVisits > 0) ? round(($page['count'] / $totalVisits) * 100, 2) : 0;
                                                            echo $percentage . '%';
                                                        ?>
                                                        <div class="progress" style="height: 5px;">
                                                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $percentage; ?>%" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
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
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Configuration des graphiques
        Chart.defaults.color = '#fff';
        Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';
        
        // Graphique des visites par jour
        const dailyCtx = document.getElementById('dailyChart').getContext('2d');
        const dailyChart = new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: [
                    <?php 
                        foreach ($dailyStats as $day) {
                            echo "'" . date('d/m', strtotime($day['date'])) . "', ";
                        }
                    ?>
                ],
                datasets: [{
                    label: 'Visites',
                    data: [
                        <?php 
                            foreach ($dailyStats as $day) {
                                echo $day['count'] . ", ";
                            }
                        ?>
                    ],
                    backgroundColor: 'rgba(255, 99, 71, 0.2)',
                    borderColor: 'rgba(255, 99, 71, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    pointBackgroundColor: 'rgba(255, 99, 71, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // Graphique des navigateurs
        const browserCtx = document.getElementById('browserChart').getContext('2d');
        const browserChart = new Chart(browserCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    <?php 
                        foreach ($browserStats as $browser) {
                            echo "'" . $browser['browser'] . "', ";
                        }
                    ?>
                ],
                datasets: [{
                    data: [
                        <?php 
                            foreach ($browserStats as $browser) {
                                echo $browser['count'] . ", ";
                            }
                        ?>
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 71, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>