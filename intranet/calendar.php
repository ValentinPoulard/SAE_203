<?php
session_start();
include("scripts/fonctions.php");

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit();
}

$fichier_annonces = 'data/r209-tp_annonces.json';
$annonces = [];

if (file_exists($fichier_annonces)) {
    $annonces = json_decode(file_get_contents($fichier_annonces), true);
}

$erreur = "";
$succes = "";

if (isset($_POST['annuler']) && isset($_POST['index'])) {
    // Recharge les annonces (correction erreur)
    $annonces = file_exists($fichier_annonces) ? json_decode(file_get_contents($fichier_annonces), true) : [];
    
    $index = (int)$_POST['index'];
    
    if ($index >= 0 && $index < count($annonces)) {
        $annonce = &$annonces[$index];
        
        if (in_array($_SESSION['utilisateur'], $annonce['Inscrits'])) {
            unset($annonce['Inscrits'][array_search($_SESSION['utilisateur'], $annonce['Inscrits'])]);
            $annonce['Inscrits'] = array_values($annonce['Inscrits']);
            
            file_put_contents($fichier_annonces, json_encode($annonces, JSON_PRETTY_PRINT));
            $succes = "Réservation annulée !";
            
            // Recharger pour éviter les problèmes de cache
            header("Location: calendar.php");
            exit();
        }
    }
}

// Filtrer les annonces de l'utilisateur
$annonces_utilisateur = [];
foreach ($annonces as $annonce) {
    if ($annonce['Pseudo'] === $_SESSION['utilisateur'] || 
        in_array($_SESSION['utilisateur'], $annonce['Inscrits'])) {
        $annonces_utilisateur[] = $annonce;
    }
}

// Trier par date
usort($annonces_utilisateur, function($a, $b) {
    return strtotime($a['Date']) - strtotime($b['Date']);
});

parametres("Mon Planning");
entete();
navigation("calendar");
?>

<section class="container">
    <h2>Mon Planning d'Interventions</h2>
    <p class="text-muted">Interventions où vous êtes responsable ou agent assigné</p>
    
    <?php if (!empty($erreur)): ?>
        <div class="alert alert-danger"><?php echo $erreur; ?></div>
    <?php endif; ?>
    <?php if (!empty($succes)): ?>
        <div class="alert alert-success"><?php echo $succes; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($annonces_utilisateur)): ?>
        <div class="row">
            <?php 
            $current_month = '';
            foreach ($annonces_utilisateur as $index => $annonce): 
                $date = new DateTime($annonce['Date']);
                $month_year = $date->format('F Y');
                
                // Affichage du bon mois
                if ($month_year !== $current_month) {
                    if ($current_month !== '') {
                        echo '</div>'; // fermer le mois précédent
                    }
                    $current_month = $month_year;
                    echo '<div class="col-12 mb-4">
                        <h4 class="text-primary">' . $current_month . '</h4>
                        <div class="row">';
                }
                
                $date_formatee = $date->format('d/m/Y H:i');
                $role = ($annonce['Pseudo'] === $_SESSION['utilisateur']) ? 'Responsable' : 'Agent assigné';
                $role_class = ($annonce['Pseudo'] === $_SESSION['utilisateur']) ? 'bg-primary' : 'bg-success';
                $places_disponibles = $annonce['Places'] - count($annonce['Inscrits']);
                
                $index_original = array_search($annonce, $annonces);
            ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-1">
                                    <?php echo htmlspecialchars($annonce['Depart']); ?> - <?php echo htmlspecialchars($annonce['Arrivee']); ?>
                                </h6>
                                <span class="badge <?php echo $role_class; ?>"><?php echo $role; ?></span>
                            </div>
                            
                            <p class="mb-2"><strong><?php echo $date_formatee; ?></strong></p>
                            <p class="mb-2">
                                <small class="text-muted">
                                    Agents: <?php echo $places_disponibles; ?>/<?php echo $annonce['Places']; ?>
                                    <?php if (!empty($annonce['Inscrits'])): ?>
                                        | Assignés: <?php echo implode(', ', $annonce['Inscrits']); ?>
                                    <?php endif; ?>
                                </small>
                            </p>
                            
                            <?php if (!empty($annonce['Commentaire'])): ?>
                                <p class="mb-2"><small><?php echo htmlspecialchars($annonce['Commentaire']); ?></small></p>
                            <?php endif; ?>
                            
                            <?php if ($role === 'Agent assigné'): ?>
                                <form method="POST" action="calendar.php" class="mt-2">
                                    <input type="hidden" name="index" value="<?php echo $index_original; ?>">
                                    <button type="submit" name="annuler" class="btn btn-danger btn-sm">Annuler</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if ($current_month !== ''): ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
    <?php else: ?>
        <div class="text-center py-5">
            <h4>Aucune mission prévue</h4>
            <p>Vous n'avez aucune mission assignée ou créée.</p>
            <a href="rechercher.php" class="btn btn-primary me-2">Rechercher une mission</a>
            <a href="proposer.php" class="btn btn-outline-primary">Créer une mission</a>
        </div>
    <?php endif; ?>
    
    <div class="text-center mt-4">
        <a href="profil.php" class="btn btn-secondary">Retour au profil</a>
        <a href="rechercher.php" class="btn btn-primary">Rechercher des missions</a>
    </div>
</section>

<?php
pieddepage();
?>
