<?php
include("../scripts/fonctions.php");
parametres("Voir les annonces");
entete();
navigation("administration");

$fichier_annonces = '../data/r209-tp_annonces.json';

if (file_exists($fichier_annonces)) {
    $contenu = file_get_contents($fichier_annonces);
    $annonces = json_decode($contenu, true);
} else {
    $annonces = [];
}

echo '<section class="container">
    <h2>Visualisation des annonces</h2>';

if (!empty($annonces)) {
    echo '<pre>';
    print_r($annonces);
    echo '</pre>';
} else {
    echo '<p>Aucune annonce trouvée.</p>';
}

echo '</section>';

pieddepage();
?>
