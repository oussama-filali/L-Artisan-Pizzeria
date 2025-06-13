<?php
// Initialiser la session
session_start();

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true){
    header("HTTP/1.1 401 Unauthorized");
    echo json_encode(["error" => "Non autorisé"]);
    exit;
}

// Fonction pour lire les fichiers JSON
function readJsonFile($file) {
    $jsonFile = "../../assets/data/" . $file;
    if (file_exists($jsonFile)) {
        $jsonContent = file_get_contents($jsonFile);
        return json_decode($jsonContent, true);
    }
    return [];
}

// Fonction pour écrire dans les fichiers JSON
function writeJsonFile($file, $data) {
    $jsonFile = "../../assets/data/" . $file;
    $jsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($jsonFile, $jsonContent);
}

// Vérifier si la requête est de type POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données JSON envoyées
    $postData = json_decode(file_get_contents("php://input"), true);
    
    if (!$postData) {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(["error" => "Données invalides"]);
        exit;
    }
    
    // Récupérer le type d'action
    $action = isset($postData['action']) ? $postData['action'] : '';
    
    // Traiter l'action
    switch ($action) {
        case 'log_activity':
            // Ajouter une nouvelle activité
            if (isset($postData['activity'])) {
                $statsData = readJsonFile("stats.json");
                
                // Ajouter la nouvelle activité au début du tableau
                array_unshift($statsData['users']['activity'], [
                    'action' => $postData['activity'],
                    'date' => date('Y-m-d H:i:s')
                ]);
                
                // Limiter à 10 activités
                if (count($statsData['users']['activity']) > 10) {
                    $statsData['users']['activity'] = array_slice($statsData['users']['activity'], 0, 10);
                }
                
                // Enregistrer les modifications
                if (writeJsonFile("stats.json", $statsData)) {
                    echo json_encode(["success" => true]);
                } else {
                    header("HTTP/1.1 500 Internal Server Error");
                    echo json_encode(["error" => "Erreur lors de l'enregistrement des données"]);
                }
            } else {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(["error" => "Activité manquante"]);
            }
            break;
            
        case 'update_product_stats':
            // Mettre à jour les statistiques des produits
            if (isset($postData['product_type'])) {
                $statsData = readJsonFile("stats.json");
                $productType = $postData['product_type'];
                
                // Mettre à jour le nombre total de produits
                $pizzasTomate = readJsonFile("pizzas_tomate.json");
                $pizzasCreme = readJsonFile("pizzas_creme.json");
                $desserts = readJsonFile("desserts.json");
                $boissons = readJsonFile("boissons.json");
                
                $statsData['products']['total'] = count($pizzasTomate) + count($pizzasCreme) + count($desserts) + count($boissons);
                $statsData['products']['categories'] = [
                    'base_tomate' => count($pizzasTomate),
                    'base_creme' => count($pizzasCreme),
                    'desserts' => count($desserts),
                    'boissons' => count($boissons)
                ];
                
                // Enregistrer les modifications
                if (writeJsonFile("stats.json", $statsData)) {
                    echo json_encode(["success" => true]);
                } else {
                    header("HTTP/1.1 500 Internal Server Error");
                    echo json_encode(["error" => "Erreur lors de l'enregistrement des données"]);
                }
            } else {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(["error" => "Type de produit manquant"]);
            }
            break;
            
        default:
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "Action non reconnue"]);
            break;
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(["error" => "Méthode non autorisée"]);
}
?>