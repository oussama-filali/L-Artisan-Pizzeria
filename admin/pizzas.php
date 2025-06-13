<?php
// Initialiser la session
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    header('Location: index.php');
    exit;
}



// Vérifier si l'utilisateur est connecté, sinon le rediriger vers la page de connexion
if(!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true){
    header("location: index.php");
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

// Fonction pour écrire dans les fichiers JSON
function writeJsonFile($file, $data) {
    $jsonFile = "../assets/data/" . $file;
    $jsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($jsonFile, $jsonContent);
}

// Fonction pour enregistrer une activité
function logActivity($activity) {
    $data = [
        'action' => 'log_activity',
        'activity' => $activity
    ];
    
    $ch = curl_init('http://' . $_SERVER['HTTP_HOST'] . '/L-Artisan-Pizzeria/admin/api/update_stats.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

// Fonction pour mettre à jour les statistiques des produits
function updateProductStats($productType) {
    $data = [
        'action' => 'update_product_stats',
        'product_type' => $productType
    ];
    
    $ch = curl_init('http://' . $_SERVER['HTTP_HOST'] . '/L-Artisan-Pizzeria/admin/api/update_stats.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

// Initialiser les variables
$action = isset($_GET['action']) ? $_GET['action'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'base_tomate';
$message = '';
$messageType = '';

// Déterminer le fichier JSON en fonction du type
switch ($type) {
    case 'base_tomate':
        $jsonFile = 'pizzas_tomate.json';
        $typeLabel = 'Base Tomate';
        break;
    case 'base_creme':
        $jsonFile = 'pizzas_creme.json';
        $typeLabel = 'Base Crème';
        break;
    case 'desserts':
        $jsonFile = 'desserts.json';
        $typeLabel = 'Desserts';
        break;
    case 'boissons':
        $jsonFile = 'boissons.json';
        $typeLabel = 'Boissons';
        break;
    default:
        $jsonFile = 'pizzas_tomate.json';
        $typeLabel = 'Base Tomate';
}

// Charger les données
$items = readJsonFile($jsonFile);

// Traitement des actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ajouter un nouvel élément
    if ($action == 'add') {
        $newItem = [
            'nom' => $_POST['nom'],
            'description' => $_POST['description'],
            'prix' => $_POST['prix']
        ];
        
        $items[] = $newItem;
        
        if (writeJsonFile($jsonFile, $items)) {
            // Enregistrer l'activité
            $activity = "Ajout d'une nouvelle " . ($type == 'desserts' ? "dessert" : ($type == 'boissons' ? "boisson" : "pizza")) . ": " . $_POST['nom'];
            logActivity($activity);
            
            // Mettre à jour les statistiques
            updateProductStats($type);
            
            $message = "L'élément a été ajouté avec succès.";
            $messageType = "success";
            // Rediriger pour éviter la resoumission du formulaire
            header("Location: pizzas.php?type=$type&message=$message&messageType=$messageType");
            exit;
        } else {
            $message = "Erreur lors de l'ajout de l'élément.";
            $messageType = "danger";
        }
    }
    
    // Modifier un élément existant
    if ($action == 'edit' && isset($_POST['index'])) {
        $index = $_POST['index'];
        
        if (isset($items[$index])) {
            $items[$index] = [
                'nom' => $_POST['nom'],
                'description' => $_POST['description'],
                'prix' => $_POST['prix']
            ];
            
            if (writeJsonFile($jsonFile, $items)) {
                // Enregistrer l'activité
                $activity = "Modification d'un(e) " . ($type == 'desserts' ? "dessert" : ($type == 'boissons' ? "boisson" : "pizza")) . ": " . $_POST['nom'];
                logActivity($activity);
                
                // Mettre à jour les statistiques
                updateProductStats($type);
                
                $message = "L'élément a été modifié avec succès.";
                $messageType = "success";
                // Rediriger pour éviter la resoumission du formulaire
                header("Location: pizzas.php?type=$type&message=$message&messageType=$messageType");
                exit;
            } else {
                $message = "Erreur lors de la modification de l'élément.";
                $messageType = "danger";
            }
        }
    }
    
    // Supprimer un élément
    if ($action == 'delete' && isset($_POST['index'])) {
        $index = $_POST['index'];
        
        if (isset($items[$index])) {
            array_splice($items, $index, 1);
            
            if (writeJsonFile($jsonFile, $items)) {
                // Enregistrer l'activité
                $activity = "Suppression d'un(e) " . ($type == 'desserts' ? "dessert" : ($type == 'boissons' ? "boisson" : "pizza"));
                logActivity($activity);
                
                // Mettre à jour les statistiques
                updateProductStats($type);
                
                $message = "L'élément a été supprimé avec succès.";
                $messageType = "success";
                // Rediriger pour éviter la resoumission du formulaire
                header("Location: pizzas.php?type=$type&message=$message&messageType=$messageType");
                exit;
            } else {
                $message = "Erreur lors de la suppression de l'élément.";
                $messageType = "danger";
            }
        }
    }
}

// Récupérer les messages de la redirection
if (isset($_GET['message']) && isset($_GET['messageType'])) {
    $message = $_GET['message'];
    $messageType = $_GET['messageType'];
}

// Préparer les données pour l'édition
$editItem = null;
$editIndex = -1;

if ($action == 'edit' && isset($_GET['index'])) {
    $editIndex = $_GET['index'];
    if (isset($items[$editIndex])) {
        $editItem = $items[$editIndex];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Pizzas - L'Artisan Pizzeria</title>
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
        .nav-tabs {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .nav-tabs .nav-link {
            color: #fff;
            border: none;
            border-bottom: 2px solid transparent;
        }
        .nav-tabs .nav-link.active {
            color: #ff6347;
            background-color: transparent;
            border-bottom: 2px solid #ff6347;
        }
        .nav-tabs .nav-link:hover {
            border-color: transparent;
            color: #ff6347;
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
            <a href="pizzas.php" class="active"><i class="fas fa-pizza-slice"></i> Gestion des pizzas</a>
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
                    <h2><i class="fas fa-pizza-slice mr-2"></i>Gestion des Pizzas et Produits</h2>
                    <p>Gérez les pizzas et autres produits disponibles sur le site.</p>
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

            <!-- Navigation tabs -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($type == 'base_tomate') ? 'active' : ''; ?>" href="pizzas.php?type=base_tomate">Base Tomate</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($type == 'base_creme') ? 'active' : ''; ?>" href="pizzas.php?type=base_creme">Base Crème</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($type == 'desserts') ? 'active' : ''; ?>" href="pizzas.php?type=desserts">Desserts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($type == 'boissons') ? 'active' : ''; ?>" href="pizzas.php?type=boissons">Boissons</a>
                </li>
            </ul>

            <!-- Add/Edit Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><?php echo ($action == 'edit') ? 'Modifier' : 'Ajouter'; ?> un élément - <?php echo $typeLabel; ?></h5>
                </div>
                <div class="card-body">
                    <form method="post" action="pizzas.php?action=<?php echo $action; ?>&type=<?php echo $type; ?>">
                        <?php if ($action == 'edit'): ?>
                            <input type="hidden" name="index" value="<?php echo $editIndex; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="nom">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?php echo ($editItem) ? htmlspecialchars($editItem['nom']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo ($editItem) ? htmlspecialchars($editItem['description']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="prix">Prix</label>
                            <input type="text" class="form-control" id="prix" name="prix" value="<?php echo ($editItem) ? htmlspecialchars($editItem['prix']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <?php echo ($action == 'edit') ? 'Mettre à jour' : 'Ajouter'; ?>
                            </button>
                            <?php if ($action == 'edit'): ?>
                                <a href="pizzas.php?type=<?php echo $type; ?>" class="btn btn-secondary">Annuler</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Items List -->
            <div class="card">
                <div class="card-header">
                    <h5>Liste des éléments - <?php echo $typeLabel; ?></h5>
                </div>
                <div class="card-body">
                    <?php if (empty($items)): ?>
                        <p class="text-center">Aucun élément trouvé.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nom</th>
                                        <th>Description</th>
                                        <th>Prix</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $index => $item): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($item['nom']); ?></td>
                                            <td><?php echo htmlspecialchars($item['description']); ?></td>
                                            <td><?php echo htmlspecialchars($item['prix']); ?></td>
                                            <td>
                                                <a href="pizzas.php?action=edit&type=<?php echo $type; ?>&index=<?php echo $index; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal<?php echo $index; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                
                                                <!-- Delete Confirmation Modal -->
                                                <div class="modal fade" id="deleteModal<?php echo $index; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel<?php echo $index; ?>" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content bg-dark text-white">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteModalLabel<?php echo $index; ?>">Confirmer la suppression</h5>
                                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Êtes-vous sûr de vouloir supprimer <strong><?php echo htmlspecialchars($item['nom']); ?></strong> ?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                                                <form method="post" action="pizzas.php?action=delete&type=<?php echo $type; ?>">
                                                                    <input type="hidden" name="index" value="<?php echo $index; ?>">
                                                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
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

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>