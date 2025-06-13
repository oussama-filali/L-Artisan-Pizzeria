<?php
// Fonction pour lire les fichiers JSON
function readJsonFile($file) {
    $jsonFile = "assets/data/" . $file;
    if (file_exists($jsonFile)) {
        $jsonContent = file_get_contents($jsonFile);
        return json_decode($jsonContent, true);
    }
    return [];
}

// Fonction pour écrire dans les fichiers JSON
function writeJsonFile($file, $data) {
    $jsonFile = "assets/data/" . $file;
    $jsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($jsonFile, $jsonContent);
}

// Récupérer la page visitée
$page = isset($_GET['page']) ? $_GET['page'] : 'Accueil';

// Charger les statistiques
$statsData = readJsonFile("stats.json");

// Incrémenter le nombre total de visites
$statsData['visits']['total']++;

// Mettre à jour les statistiques par page
$pageFound = false;
foreach ($statsData['visits']['pages'] as &$pageStat) {
    if ($pageStat['page'] === $page) {
        $pageStat['count']++;
        $pageFound = true;
        break;
    }
}

if (!$pageFound) {
    $statsData['visits']['pages'][] = [
        'page' => $page,
        'count' => 1
    ];
}

// Trier les pages par nombre de visites (ordre décroissant)
usort($statsData['visits']['pages'], function($a, $b) {
    return $b['count'] - $a['count'];
});

// Mettre à jour les statistiques par jour
$today = date('Y-m-d');
$dayFound = false;
foreach ($statsData['visits']['daily'] as &$dayStat) {
    if ($dayStat['date'] === $today) {
        $dayStat['count']++;
        $dayFound = true;
        break;
    }
}

if (!$dayFound) {
    $statsData['visits']['daily'][] = [
        'date' => $today,
        'count' => 1
    ];
}

// Trier les jours par date (ordre croissant)
usort($statsData['visits']['daily'], function($a, $b) {
    return strtotime($a['date']) - strtotime($b['date']);
});

// Limiter à 30 jours
if (count($statsData['visits']['daily']) > 30) {
    $statsData['visits']['daily'] = array_slice($statsData['visits']['daily'], -30);
}

// Mettre à jour les statistiques par navigateur
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$browser = 'Autre';

if (strpos($userAgent, 'Chrome') !== false) {
    $browser = 'Chrome';
} elseif (strpos($userAgent, 'Firefox') !== false) {
    $browser = 'Firefox';
} elseif (strpos($userAgent, 'Safari') !== false) {
    $browser = 'Safari';
} elseif (strpos($userAgent, 'Edge') !== false) {
    $browser = 'Edge';
} elseif (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) {
    $browser = 'Internet Explorer';
}

$browserFound = false;
foreach ($statsData['visits']['browsers'] as &$browserStat) {
    if ($browserStat['browser'] === $browser) {
        $browserStat['count']++;
        $browserFound = true;
        break;
    }
}

if (!$browserFound) {
    $statsData['visits']['browsers'][] = [
        'browser' => $browser,
        'count' => 1
    ];
}

// Trier les navigateurs par nombre de visites (ordre décroissant)
usort($statsData['visits']['browsers'], function($a, $b) {
    return $b['count'] - $a['count'];
});

// Enregistrer les modifications
writeJsonFile("stats.json", $statsData);

// Renvoyer une image transparente 1x1 pixel
header('Content-Type: image/gif');
echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
?>