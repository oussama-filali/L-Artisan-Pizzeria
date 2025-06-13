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

// Fonction pour lire le contenu d'un fichier
function readFileContent($file) {
    if (file_exists($file)) {
        return file_get_contents($file);
    }
    return '';
}

// Fonction pour écrire dans un fichier
function writeFileContent($file, $content) {
    return file_put_contents($file, $content);
}

// Traitement des actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Modifier le contenu HTML
    if ($action == 'edit_html') {
        $file = $_POST['file'];
        $content = $_POST['content'];
        
        // Vérifier que le fichier existe et est dans le répertoire du site
        $realPath = realpath($file);
        $siteRoot = realpath('../');
        
        if ($realPath && strpos($realPath, $siteRoot) === 0) {
            if (writeFileContent($file, $content)) {
                $message = "Le fichier a été modifié avec succès.";
                $messageType = "success";
            } else {
                $message = "Erreur lors de la modification du fichier.";
                $messageType = "danger";
            }
        } else {
            $message = "Erreur: Chemin de fichier invalide.";
            $messageType = "danger";
        }
    }
    
    // Modifier le contenu JSON
    if ($action == 'edit_json') {
        $file = $_POST['file'];
        $content = $_POST['content'];
        
        // Vérifier que le fichier existe et est dans le répertoire du site
        $realPath = realpath($file);
        $siteRoot = realpath('../');
        
        if ($realPath && strpos($realPath, $siteRoot) === 0) {
            // Vérifier que le contenu est un JSON valide
            json_decode($content);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (writeFileContent($file, $content)) {
                    $message = "Le fichier a été modifié avec succès.";
                    $messageType = "success";
                } else {
                    $message = "Erreur lors de la modification du fichier.";
                    $messageType = "danger";
                }
            } else {
                $message = "Erreur: Le contenu JSON n'est pas valide.";
                $messageType = "danger";
            }
        } else {
            $message = "Erreur: Chemin de fichier invalide.";
            $messageType = "danger";
        }
    }
    
    // Modifier le contenu CSS
    if ($action == 'edit_css') {
        $file = $_POST['file'];
        $content = $_POST['content'];
        
        // Vérifier que le fichier existe et est dans le répertoire du site
        $realPath = realpath($file);
        $siteRoot = realpath('../');
        
        if ($realPath && strpos($realPath, $siteRoot) === 0) {
            if (writeFileContent($file, $content)) {
                $message = "Le fichier a été modifié avec succès.";
                $messageType = "success";
            } else {
                $message = "Erreur lors de la modification du fichier.";
                $messageType = "danger";
            }
        } else {
            $message = "Erreur: Chemin de fichier invalide.";
            $messageType = "danger";
        }
    }
    
    // Modifier le contenu JavaScript
    if ($action == 'edit_js') {
        $file = $_POST['file'];
        $content = $_POST['content'];
        
        // Vérifier que le fichier existe et est dans le répertoire du site
        $realPath = realpath($file);
        $siteRoot = realpath('../');
        
        if ($realPath && strpos($realPath, $siteRoot) === 0) {
            if (writeFileContent($file, $content)) {
                $message = "Le fichier a été modifié avec succès.";
                $messageType = "success";
            } else {
                $message = "Erreur lors de la modification du fichier.";
                $messageType = "danger";
            }
        } else {
            $message = "Erreur: Chemin de fichier invalide.";
            $messageType = "danger";
        }
    }
}

// Récupérer les fichiers du site
$htmlFiles = [];
$cssFiles = [];
$jsFiles = [];
$jsonFiles = [];

// Fonction récursive pour parcourir les répertoires
function scanDirectory($dir, &$htmlFiles, &$cssFiles, &$jsFiles, &$jsonFiles) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file != "." && $file != ".." && $file != "admin") {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                scanDirectory($path, $htmlFiles, $cssFiles, $jsFiles, $jsonFiles);
            } else {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                switch ($ext) {
                    case 'html':
                        $htmlFiles[] = $path;
                        break;
                    case 'css':
                        $cssFiles[] = $path;
                        break;
                    case 'js':
                        $jsFiles[] = $path;
                        break;
                    case 'json':
                        $jsonFiles[] = $path;
                        break;
                }
            }
        }
    }
}

scanDirectory('..', $htmlFiles, $cssFiles, $jsFiles, $jsonFiles);

// Préparer les données pour l'édition
$editFile = '';
$editContent = '';
$editType = '';

if ($action == 'edit') {
    $file = isset($_GET['file']) ? $_GET['file'] : '';
    $type = isset($_GET['type']) ? $_GET['type'] : '';
    
    // Vérifier que le fichier existe et est dans le répertoire du site
    $realPath = realpath($file);
    $siteRoot = realpath('../');
    
    if ($realPath && strpos($realPath, $siteRoot) === 0) {
        $editFile = $file;
        $editContent = readFileContent($file);
        $editType = $type;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion du Contenu - L'Artisan Pizzeria</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/theme/monokai.min.css">
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
        .CodeMirror {
            height: 500px;
            border-radius: 10px;
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
            <a href="content.php" class="active"><i class="fas fa-edit"></i> Contenu du site</a>
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
                    <h2><i class="fas fa-edit mr-2"></i>Gestion du Contenu</h2>
                    <p>Modifiez le contenu de votre site web.</p>
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

            <?php if ($action == 'edit'): ?>
                <!-- Editor -->
                <div class="card">
                    <div class="card-header">
                        <h5>Éditer <?php echo basename($editFile); ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="content.php?action=edit_<?php echo $editType; ?>">
                            <input type="hidden" name="file" value="<?php echo $editFile; ?>">
                            
                            <div class="form-group">
                                <textarea id="editor" name="content" class="form-control"><?php echo htmlspecialchars($editContent); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-2"></i>Enregistrer
                                </button>
                                <a href="content.php" class="btn btn-secondary">
                                    <i class="fas fa-times mr-2"></i>Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <!-- File Tabs -->
                <ul class="nav nav-tabs mb-4" id="contentTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="html-tab" data-toggle="tab" href="#html" role="tab" aria-controls="html" aria-selected="true">HTML</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="css-tab" data-toggle="tab" href="#css" role="tab" aria-controls="css" aria-selected="false">CSS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="js-tab" data-toggle="tab" href="#js" role="tab" aria-controls="js" aria-selected="false">JavaScript</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="json-tab" data-toggle="tab" href="#json" role="tab" aria-controls="json" aria-selected="false">JSON</a>
                    </li>
                </ul>
                
                <div class="tab-content" id="contentTabsContent">
                    <!-- HTML Files -->
                    <div class="tab-pane fade show active" id="html" role="tabpanel" aria-labelledby="html-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Fichiers HTML</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($htmlFiles)): ?>
                                    <p class="text-center">Aucun fichier HTML trouvé.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Nom du fichier</th>
                                                    <th>Chemin</th>
                                                    <th>Dernière modification</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($htmlFiles as $file): ?>
                                                    <tr>
                                                        <td><?php echo basename($file); ?></td>
                                                        <td><?php echo $file; ?></td>
                                                        <td><?php echo date('d/m/Y H:i', filemtime($file)); ?></td>
                                                        <td>
                                                            <a href="content.php?action=edit&type=html&file=<?php echo $file; ?>" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-edit"></i> Éditer
                                                            </a>
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
                    
                    <!-- CSS Files -->
                    <div class="tab-pane fade" id="css" role="tabpanel" aria-labelledby="css-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Fichiers CSS</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($cssFiles)): ?>
                                    <p class="text-center">Aucun fichier CSS trouvé.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Nom du fichier</th>
                                                    <th>Chemin</th>
                                                    <th>Dernière modification</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($cssFiles as $file): ?>
                                                    <tr>
                                                        <td><?php echo basename($file); ?></td>
                                                        <td><?php echo $file; ?></td>
                                                        <td><?php echo date('d/m/Y H:i', filemtime($file)); ?></td>
                                                        <td>
                                                            <a href="content.php?action=edit&type=css&file=<?php echo $file; ?>" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-edit"></i> Éditer
                                                            </a>
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
                    
                    <!-- JavaScript Files -->
                    <div class="tab-pane fade" id="js" role="tabpanel" aria-labelledby="js-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Fichiers JavaScript</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($jsFiles)): ?>
                                    <p class="text-center">Aucun fichier JavaScript trouvé.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Nom du fichier</th>
                                                    <th>Chemin</th>
                                                    <th>Dernière modification</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($jsFiles as $file): ?>
                                                    <tr>
                                                        <td><?php echo basename($file); ?></td>
                                                        <td><?php echo $file; ?></td>
                                                        <td><?php echo date('d/m/Y H:i', filemtime($file)); ?></td>
                                                        <td>
                                                            <a href="content.php?action=edit&type=js&file=<?php echo $file; ?>" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-edit"></i> Éditer
                                                            </a>
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
                    
                    <!-- JSON Files -->
                    <div class="tab-pane fade" id="json" role="tabpanel" aria-labelledby="json-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5>Fichiers JSON</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($jsonFiles)): ?>
                                    <p class="text-center">Aucun fichier JSON trouvé.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Nom du fichier</th>
                                                    <th>Chemin</th>
                                                    <th>Dernière modification</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($jsonFiles as $file): ?>
                                                    <tr>
                                                        <td><?php echo basename($file); ?></td>
                                                        <td><?php echo $file; ?></td>
                                                        <td><?php echo date('d/m/Y H:i', filemtime($file)); ?></td>
                                                        <td>
                                                            <a href="content.php?action=edit&type=json&file=<?php echo $file; ?>" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-edit"></i> Éditer
                                                            </a>
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
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/mode/htmlmixed/htmlmixed.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/mode/xml/xml.min.js"></script>
    <script>
        <?php if ($action == 'edit'): ?>
            // Initialiser l'éditeur de code
            var editor = CodeMirror.fromTextArea(document.getElementById('editor'), {
                lineNumbers: true,
                theme: 'monokai',
                mode: '<?php 
                    switch ($editType) {
                        case 'html':
                            echo 'htmlmixed';
                            break;
                        case 'css':
                            echo 'css';
                            break;
                        case 'js':
                            echo 'javascript';
                            break;
                        case 'json':
                            echo 'application/json';
                            break;
                        default:
                            echo 'text/plain';
                    }
                ?>',
                indentUnit: 4,
                indentWithTabs: false,
                lineWrapping: true,
                autoCloseBrackets: true,
                autoCloseTags: true,
                matchBrackets: true,
                matchTags: true
            });
        <?php endif; ?>
    </script>
</body>
</html>