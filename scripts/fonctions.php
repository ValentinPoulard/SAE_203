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
    echo '<header class="jumbotron bg-primary text-white p-4 mb-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1>SecuriWatch Intranet</h1>
                <p>Système de gestion des missions de télésurveillance</p>
            </div>
            <div class="col-md-6 text-end">
                <img src="images/logo.png" alt="Logo" style="max-height: 80px;">';
                
    if (isset($_SESSION['utilisateur'])) {
        echo '<div class="mt-2">
            <span class="me-3">Bienvenue, ' . $_SESSION['utilisateur'] . '</span>
            <a href="deconnexion.php" class="btn btn-outline-light">Se déconnecter</a>
        </div>';
    } else {
        echo '<div class="mt-2">
            <a href="connexion.php" class="btn btn-outline-light">S\'identifier</a>
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
        'annonces' => 'Missions',
        'rechercher' => 'Rechercher',
        'proposer' => 'Créer Mission',
        'modifier' => 'Gérer',
        'calendar' => 'Planning',
        'administration' => 'Administration',
        'profil' => 'Profil',
        'wiki' => 'Documentation'
    ];
    
    echo '<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="accueil.php">SecuriWatch</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">';
            
    foreach ($pages as $page => $nom) {
        $active_class = ($page_active === $page) ? 'active' : '';
        $href = ($page === 'annonces') ? 'visualiser.php' : $page . '.php';
        echo '<li class="nav-item">
            <a class="nav-link ' . $active_class . '" href="' . $href . '">' . $nom . '</a>
        </li>';
    }
    
    echo '</ul>
            <form class="d-flex">
                <input class="form-control me-2" type="search" placeholder="Rechercher...">
                <button class="btn btn-outline-success" type="submit">Rechercher</button>
            </form>
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
        <p>SecuriWatch - contact@securiwatch.fr - Service Télésurveillance</p>
        <p>' . $date_heure . ' - © ' . $annee . ' SecuriWatch Intranet - IP: ' . $ip . ':' . $port . '</p>
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
