<?php
// Initialiser la session
session_start();

// Vérifier si l'utilisateur est connecté, sinon le rediriger vers la page de connexion
if(!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true){
    header("location: index.php");
    exit;
}

// Initialiser les variables
$action = isset($_GET['action']) ? $_GET['action'] : '';
$message = '';
$messageType = '';

// Définir le dossier des médias
$mediaDir = "../assets/img/";

// Traitement des actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Télécharger un nouveau média
    if ($action == 'upload') {
        if (isset($_FILES["media"]) && $_FILES["media"]["error"] == 0) {
            $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png", "mp4" => "video/mp4");
            $filename = $_FILES["media"]["name"];
            $filetype = $_FILES["media"]["type"];
            $filesize = $_FILES["media"]["size"];
            
            // Vérifier l'extension du fichier
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if (!array_key_exists($ext, $allowed)) {
                $message = "Erreur: Veuillez sélectionner un format de fichier valide.";
                $messageType = "danger";
            }
            
            // Vérifier la taille du fichier - 5MB maximum
            $maxsize = 5 * 1024 * 1024;
            if ($filesize > $maxsize) {
                $message = "Erreur: La taille du fichier est supérieure à la limite autorisée (5 MB).";
                $messageType = "danger";
            }
            
            // Vérifier le type MIME du fichier
            if (in_array($filetype, $allowed)) {
                // Vérifier si le fichier existe avant de le télécharger
                if (file_exists($mediaDir . $filename)) {
                    $message = "Erreur: Le fichier $filename existe déjà.";
                    $messageType = "danger";
                } else {
                    // Tout est bon, télécharger le fichier
                    if (move_uploaded_file($_FILES["media"]["tmp_name"], $mediaDir . $filename)) {
                        $message = "Le fichier $filename a été téléchargé avec succès.";
                        $messageType = "success";
                        // Rediriger pour éviter la resoumission du formulaire
                        header("Location: media.php?message=$message&messageType=$messageType");
                        exit;
                    } else {
                        $message = "Erreur: Il y a eu un problème lors du téléchargement de votre fichier. Veuillez réessayer.";
                        $messageType = "danger";
                    }
                }
            } else {
                $message = "Erreur: Il y a eu un problème lors du téléchargement de votre fichier. Veuillez réessayer.";
                $messageType = "danger";
            }
        } else {
            $message = "Erreur: " . $_FILES["media"]["error"];
            $messageType = "danger";
        }
    }
    
    // Supprimer un média
    if ($action == 'delete' && isset($_POST['filename'])) {
        $filename = $_POST['filename'];
        $filepath = $mediaDir . $filename;
        
        if (file_exists($filepath)) {
            if (unlink($filepath)) {
                $message = "Le fichier $filename a été supprimé avec succès.";
                $messageType = "success";
                // Rediriger pour éviter la resoumission du formulaire
                header("Location: media.php?message=$message&messageType=$messageType");
                exit;
            } else {
                $message = "Erreur: Impossible de supprimer le fichier $filename.";
                $messageType = "danger";
            }
        } else {
            $message = "Erreur: Le fichier $filename n'existe pas.";
            $messageType = "danger";
        }
    }
}

// Récupérer les messages de la redirection
if (isset($_GET['message']) && isset($_GET['messageType'])) {
    $message = $_GET['message'];
    $messageType = $_GET['messageType'];
}

// Récupérer la liste des médias
$mediaFiles = [];
if (is_dir($mediaDir)) {
    $files = scandir($mediaDir);
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            $filepath = $mediaDir . $file;
            $filesize = filesize($filepath);
            $filetype = mime_content_type($filepath);
            $mediaFiles[] = [
                'name' => $file,
                'size' => $filesize,
                'type' => $filetype,
                'modified' => filemtime($filepath)
            ];
        }
    }
}

// Trier les médias par date de modification (plus récent en premier)
usort($mediaFiles, function($a, $b) {
    return $b['modified'] - $a['modified'];
});
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Médias - L'Artisan Pizzeria</title>
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
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        .media-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }
        .media-item img, .media-item video {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .media-info {
            padding: 10px;
        }
        .media-info h6 {
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .media-info p {
            margin: 5px 0 0;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
        }
        .media-actions {
            position: absolute;
            top: 5px;
            right: 5px;
            display: flex;
            gap: 5px;
        }
        .media-actions button {
            background: rgba(0, 0, 0, 0.5);
            border: none;
            color: #fff;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .media-actions button:hover {
            background: rgba(0, 0, 0, 0.8);
        }
        .custom-file-label {
            background-color: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
            border-radius: 10px;
        }
        .custom-file-label::after {
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            border: none;
            border-radius: 0 10px 10px 0;
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
            .media-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
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
            <a href="media.php" class="active"><i class="fas fa-photo-video"></i> Médias</a>
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
                    <h2><i class="fas fa-photo-video mr-2"></i>Gestion des Médias</h2>
                    <p>Gérez les images et vidéos utilisées sur le site.</p>
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

            <!-- Upload Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Télécharger un nouveau média</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="media.php?action=upload" enctype="multipart/form-data">
                        <div class="form-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="media" name="media" required>
                                <label class="custom-file-label" for="media">Choisir un fichier</label>
                            </div>
                            <small class="form-text text-muted">Formats acceptés: JPG, JPEG, PNG, GIF, MP4. Taille maximale: 5 MB.</small>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload mr-2"></i>Télécharger
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Media Grid -->
            <div class="card">
                <div class="card-header">
                    <h5>Bibliothèque de médias</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($mediaFiles)): ?>
                        <p class="text-center">Aucun média trouvé.</p>
                    <?php else: ?>
                        <div class="media-grid">
                            <?php foreach ($mediaFiles as $media): ?>
                                <div class="media-item">
                                    <?php if (strpos($media['type'], 'image') !== false): ?>
                                        <img src="<?php echo $mediaDir . $media['name']; ?>" alt="<?php echo $media['name']; ?>">
                                    <?php elseif (strpos($media['type'], 'video') !== false): ?>
                                        <video>
                                            <source src="<?php echo $mediaDir . $media['name']; ?>" type="<?php echo $media['type']; ?>">
                                        </video>
                                        <div class="video-overlay">
                                            <i class="fas fa-play-circle fa-3x"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="file-icon">
                                            <i class="fas fa-file fa-3x"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="media-info">
                                        <h6><?php echo $media['name']; ?></h6>
                                        <p><?php echo date('d/m/Y H:i', $media['modified']); ?> - <?php echo round($media['size'] / 1024, 2); ?> KB</p>
                                    </div>
                                    
                                    <div class="media-actions">
                                        <button type="button" class="copy-url" data-url="<?php echo $mediaDir . $media['name']; ?>" title="Copier l'URL">
                                            <i class="fas fa-link"></i>
                                        </button>
                                        <button type="button" data-toggle="modal" data-target="#deleteModal<?php echo md5($media['name']); ?>" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Confirmation Modal -->
                                    <div class="modal fade" id="deleteModal<?php echo md5($media['name']); ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel<?php echo md5($media['name']); ?>" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content bg-dark text-white">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel<?php echo md5($media['name']); ?>">Confirmer la suppression</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous sûr de vouloir supprimer le fichier <strong><?php echo $media['name']; ?></strong> ?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                                    <form method="post" action="media.php?action=delete">
                                                        <input type="hidden" name="filename" value="<?php echo $media['name']; ?>">
                                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Afficher le nom du fichier sélectionné
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
        
        // Copier l'URL du média
        $('.copy-url').on('click', function() {
            let url = $(this).data('url');
            let tempInput = document.createElement('input');
            document.body.appendChild(tempInput);
            tempInput.value = url;
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            
            // Afficher une notification
            alert('URL copiée dans le presse-papiers: ' + url);
        });
        
        // Prévisualiser les vidéos
        $('.video-overlay').on('click', function() {
            let video = $(this).siblings('video')[0];
            if (video.paused) {
                video.play();
                $(this).hide();
            } else {
                video.pause();
                $(this).show();
            }
        });
    </script>
</body>
</html>