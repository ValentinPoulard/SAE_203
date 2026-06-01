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
    <h2>Missions de télésurveillance</h2>';

if (!empty($annonces)) {
    echo '<div class="row">';
    foreach ($annonces as $annonce) {
        $date = new DateTime($annonce['Date']);
        $date_formatee = $date->format('d/m/Y H:i');
        
        echo '<div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">' . htmlspecialchars($annonce['Depart']) . ' - ' . htmlspecialchars($annonce['Arrivee']) . '</h5>
                    <p><strong>Date et heure :</strong> ' . $date_formatee . '</p>
                    <p><strong>Agents requis :</strong> ' . $annonce['Places'] . '</p>
                    <p><strong>Responsable :</strong> ' . htmlspecialchars($annonce['Pseudo']) . '</p>';
        
        if (!empty($annonce['Commentaire'])) {
            echo '<p><strong>Commentaire :</strong> ' . htmlspecialchars($annonce['Commentaire']) . '</p>';
        }
        
        if (!empty($annonce['Inscrits'])) {
            echo '<p><strong>Inscrits :</strong> ' . implode(', ', $annonce['Inscrits']) . '</p>';
        }
        
        echo '</div>
            </div>
        </div>';
    }
    echo '</div>';
} else {
    echo '<p>Aucune mission disponible.</p>';
}

echo '</section>';

pieddepage();
?>
