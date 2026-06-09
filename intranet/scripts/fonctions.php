<?php
function parametres($titre) {
    echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . $titre . '</title>
    <meta name="author" content="Sécurité Privée">
    <meta name="description" content="Intranet de télésurveillance">
    <meta name="keywords" content="télésurveillance, sécurité, missions, interventions">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>';
}

function entete() {
    echo '<header class="jumbotron text-white p-4 mb-0" style="background-image: url(\'/intranet/images/image_de_font.png\'); background-size: cover; background-position: center;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1>Vidéosurveillance Saint-Malo</h1>
                <p>Système de gestion des missions de télésurveillance</p>
            </div>
            <div class="col-md-6 text-end">';
                
    if (isset($_SESSION['utilisateur'])) {
        echo '<div class="mt-2">
            <span class="me-3">Bienvenue, ' . $_SESSION['utilisateur'] . '</span>
            <a href="/intranet/deconnexion.php" class="btn btn-outline-light">Se déconnecter</a>
        </div>';
    } else {
        echo '<div class="mt-2">
            <a href="/intranet/connexion.php" class="btn btn-outline-light">S\'identifier</a>
        </div>';
    }
    
    echo '</div>
        </div>
    </div>
</header>';
}

function navigation($page_active) {
    $pages = [
        'accueil' => 'Accueil',
        'annonces' => 'Interventions',
        'rechercher' => 'Rechercher',
        'proposer' => 'Créer Intervention',
        'modifier' => 'Gérer',
        'calendar' => 'Planning',
        'fichiers' => 'Gestion Fichiers',
        'annuaires' => 'Annuaires',
        'profil' => 'Profil',
        'wiki' => 'Documentation'
    ];
    
    // Ajouter Administration uniquement pour les admins
    if (isset($_SESSION['groupes']) && in_array('admin', $_SESSION['groupes'])) {
        $pages['administration'] = 'Administration';
    }
    
    echo '<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/intranet/accueil.php">
            <img src="/intranet/images/logo.png" alt="Logo" style="height: 40px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">';
            
    foreach ($pages as $page => $nom) {
        $active_class = ($page_active === $page) ? 'active' : '';
        if ($page === 'annonces') {
            $href = '/intranet/visualiser.php';
        } elseif ($page === 'fichiers') {
            $href = '/intranet/gestion_fichiers/index.php';
        } elseif ($page === 'annuaires') {
            $href = '/intranet/annuaires/index.php';
        } else {
            $href = '/intranet/' . $page . '.php';
        }
        echo '<li class="nav-item">
            <a class="nav-link ' . $active_class . '" href="' . $href . '">' . $nom . '</a>
        </li>';
    }
    
    echo '</ul>
            <a href="http://172.18.203.210/vitrine" target="_blank" class="btn btn-outline-light ms-3">Site Vitrine</a>
        </div>
    </div>
</nav>';
}

function pieddepage() {
    $annee = date('Y');
    $date_heure = date('d/m/Y H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    $port = $_SERVER['SERVER_PORT'];
    
    echo '<footer class="jumbotron bg-secondary text-white p-4 mt-5">
    <div class="container text-center">
        <p>Vidéosurveillance Saint-Malo - contact@videosurveillance-saintmalo.fr - Service Télésurveillance</p>
        <p>' . $date_heure . ' - © ' . $annee . ' Vidéosurveillance Saint-Malo Intranet - IP: ' . $ip . ':' . $port . '</p>
        <div>
            <small>Accès réservé au personnel autorisé</small>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';
}
?>
