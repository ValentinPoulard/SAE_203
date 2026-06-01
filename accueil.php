<?php
session_start();
include("scripts/fonctions.php");

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit();
}

parametres("Accueil");
entete();
navigation("accueil");

$fichier_annonces = 'data/r209-tp_annonces.json';
$fichier_utilisateurs = 'data/r209-tp_utilisateurs.json';

$nombre_annonces = 0;
$nombre_utilisateurs = 0;

if (file_exists($fichier_annonces)) {
    $annonces = json_decode(file_get_contents($fichier_annonces), true);
    $nombre_annonces = is_array($annonces) ? count($annonces) : 0;
}

if (file_exists($fichier_utilisateurs)) {
    $utilisateurs = json_decode(file_get_contents($fichier_utilisateurs), true);
    $nombre_utilisateurs = is_array($utilisateurs) ? count($utilisateurs) : 0;
}

echo '<section class="container">
    <h1>Bienvenue sur l\'intranet SecuriWatch</h1>
    <p>Système de gestion des missions de télésurveillance.</p>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <h5>Missions</h5>
                    <p class="display-4">' . $nombre_annonces . '</p>
                    <p>missions disponibles</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <h5>Agents</h5>
                    <p class="display-4">' . $nombre_utilisateurs . '</p>
                    <p>agents inscrits</p>
                </div>
            </div>
        </div>
    </div>';

if (!isset($_SESSION['utilisateur'])) {
    echo '<div class="row mt-4">
        <div class="col-12 text-center">
            <a href="inscription.php" class="btn btn-primary btn-lg me-3">S\'inscrire</a>
            <a href="connexion.php" class="btn btn-outline-primary btn-lg">Se connecter</a>
        </div>
    </div>';
}

echo '</section>';

pieddepage();
?>