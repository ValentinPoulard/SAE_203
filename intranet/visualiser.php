<?php
session_start();
include("scripts/fonctions.php");

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit();
}

parametres("Visualiser les missions");
entete();
navigation("annonces");

$fichier_annonces = 'data/r209-tp_annonces.json';

if (file_exists($fichier_annonces)) {
    $contenu = file_get_contents($fichier_annonces);
    $annonces = json_decode($contenu, true);
} else {
    $annonces = [];
}

echo '<section class="container">
    <h2>Interventions planifiées</h2>';

if (!empty($annonces)) {
    echo '<div class="row">';
    foreach ($annonces as $index => $annonce) {
        $date = new DateTime($annonce['Date']);
        $date_formatee = $date->format('d/m/Y H:i');
        $est_passe = $date < new DateTime();
        
        echo '<div class="col-md-6 mb-4">
            <div class="card ' . ($est_passe ? 'border-warning' : '') . '">
                <div class="card-body">
                    <h5 class="card-title">' . htmlspecialchars($annonce['Depart']) . '</h5>
                    <p><strong>Type :</strong> <span class="badge bg-info">' . htmlspecialchars($annonce['Arrivee']) . '</span></p>
                    <p><strong>Date et heure :</strong> ' . $date_formatee . '</p>
                    <p><strong>Agents requis :</strong> ' . $annonce['Places'] . '</p>
                    <p><strong>Responsable :</strong> ' . htmlspecialchars($annonce['Pseudo']) . '</p>';
        
        if (!empty($annonce['Commentaire'])) {
            echo '<p><strong>Détails :</strong> ' . htmlspecialchars($annonce['Commentaire']) . '</p>';
        }
        
        if (!empty($annonce['Inscrits'])) {
            echo '<p><strong>Assignés :</strong> ' . implode(', ', $annonce['Inscrits']) . '</p>';
        }
        
        echo '<div class="mt-3">
                <a href="rechercher.php?index=' . $index . '" class="btn btn-primary btn-sm">S\'assigner</a>';
        
        if (in_array('admin', $_SESSION['groupes']) || in_array('managers', $_SESSION['groupes']) || $annonce['Pseudo'] === $_SESSION['utilisateur']) {
            echo '<a href="modifier.php?index=' . $index . '" class="btn btn-secondary btn-sm">Modifier</a>';
        }
        
        echo '</div>
            </div>
            </div>
        </div>';
    }
    echo '</div>';
} else {
    echo '<p>Aucune intervention planifiée.</p>';
}

echo '</section>';

pieddepage();
?>
