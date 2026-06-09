<?php
// API pour Binôme B - Accès aux données partenaires pour le site vitrine
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

$fichier_partenaires = 'data/annuaire_partenaires.json';
$partenaires = [];

if (file_exists($fichier_partenaires)) {
    $partenaires = json_decode(file_get_contents($fichier_partenaires), true);
}

// Filtrer uniquement les partenaires actifs
$partenaires_actifs = array_filter($partenaires, function($p) {
    return $p['actif'] === true;
});

// Préparer les données pour l'export (informations publiques uniquement)
$donnees_export = [];
foreach ($partenaires_actifs as $p) {
    $donnees_export[] = [
        'id' => $p['id'],
        'nom' => $p['nom'],
        'specialite' => $p['specialite'],
        'logo' => $p['logo'],
        'date_partenariat' => $p['date_partenariat']
    ];
}

echo json_encode($donnees_export, JSON_PRETTY_PRINT);
?>
