<?php
include("../scripts/fonctions.php");
parametres("Voir les utilisateurs");
entete();
navigation("administration");

$fichier_utilisateurs = '../data/r209-tp_utilisateurs.json';

if (file_exists($fichier_utilisateurs)) {
    $contenu = file_get_contents($fichier_utilisateurs);
    $utilisateurs = json_decode($contenu, true);
} else {
    $utilisateurs = [];
}

echo '<section class="container">
    <h2>Visualisation des utilisateurs</h2>';

if (!empty($utilisateurs)) {
    echo '<pre>';
    print_r($utilisateurs);
    echo '</pre>';
} else {
    echo '<p>Aucun utilisateur trouvé.</p>';
}

echo '</section>';

pieddepage();
?>