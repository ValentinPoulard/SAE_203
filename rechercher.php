<?php
session_start();
include("scripts/fonctions.php");

$fichier_annonces = 'data/r209-tp_annonces.json';
$annonces = file_exists($fichier_annonces) ? json_decode(file_get_contents($fichier_annonces), true) : [];

$erreur = "";
$succes = "";

if (isset($_POST['inscrire']) && isset($_POST['index'])) {
    if (!isset($_SESSION['utilisateur'])) {
        $erreur = "Vous devez être connecté pour vous inscrire.";
    } else {
        $annonces = file_exists($fichier_annonces) ? json_decode(file_get_contents($fichier_annonces), true) : [];
        
        $index = (int)$_POST['index'];
        
        if ($index >= 0 && $index < count($annonces)) {
            $annonce = &$annonces[$index];
            
            if ($annonce['Pseudo'] !== $_SESSION['utilisateur'] && 
                !in_array($_SESSION['utilisateur'], $annonce['Inscrits'])) {
                
                $annonce['Inscrits'][] = $_SESSION['utilisateur'];
                file_put_contents($fichier_annonces, json_encode($annonces, JSON_PRETTY_PRINT));
                $succes = "Inscription réussie !";
                
                header("Location: rechercher.php");
                exit();
            } else {
                $erreur = "Vous êtes déjà inscrit ou vous êtes le conducteur.";
            }
        }
    }
}

$annonces_filtrees = $annonces;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET)) {
    $annonces_filtrees = array_filter($annonces, function($annonce) {
        $date = $_GET['date'] ?? '';
        $depart = $_GET['depart'] ?? '';
        $arrivee = $_GET['arrivee'] ?? '';
        $places = $_GET['places'] ?? '';
        
        if (!empty($date) && date('Y-m-d', strtotime($annonce['Date'])) !== date('Y-m-d', strtotime($date))) {
            return false;
        }
        if (!empty($depart) && stripos($annonce['Depart'], $depart) === false) {
            return false;
        }
        if (!empty($arrivee) && stripos($annonce['Arrivee'], $arrivee) === false) {
            return false;
        }
        if (!empty($places) && $annonce['Places'] < (int)$places) {
            return false;
        }
        
        return true;
    });
}

parametres("Rechercher des missions");
entete();
navigation("rechercher");
?>

<section class="container">
    <h2>Rechercher des missions</h2>
    
    <?php if (!empty($erreur)): ?>
        <div class="alert alert-danger"><?php echo $erreur; ?></div>
    <?php endif; ?>
    <?php if (!empty($succes)): ?>
        <div class="alert alert-success"><?php echo $succes; ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="rechercher.php">
                <div class="row">
                    <div class="col-md-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" id="date" value="<?php echo $_GET['date'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="depart" class="form-label">Site d'intervention</label>
                        <input type="text" name="depart" class="form-control" id="depart" value="<?php echo $_GET['depart'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="arrivee" class="form-label">Type de mission</label>
                        <input type="text" name="arrivee" class="form-control" id="arrivee" value="<?php echo $_GET['arrivee'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="places" class="form-label">Agents minimum</label>
                        <input type="number" name="places" class="form-control" id="places" min="1" max="10" value="<?php echo $_GET['places'] ?? ''; ?>">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Rechercher</button>
                        <a href="rechercher.php" class="btn btn-secondary">Effacer</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <h3>Résultats (<?php echo count($annonces_filtrees); ?>)</h3>
    
    <?php if (!empty($annonces_filtrees)): ?>
        <div class="row">
            <?php foreach ($annonces_filtrees as $index => $annonce): ?>
                <?php 
                $index_original = array_search($annonce, $annonces);
                $date_formatee = (new DateTime($annonce['Date']))->format('d/m/Y H:i');
                $places_disponibles = $annonce['Places'] - count($annonce['Inscrits']);
                ?>
                
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5><?php echo htmlspecialchars($annonce['Depart']); ?> - <?php echo htmlspecialchars($annonce['Arrivee']); ?></h5>
                            <p><strong>Date :</strong> <?php echo $date_formatee; ?></p>
                            <p><strong>Agents :</strong> <?php echo $places_disponibles; ?>/<?php echo $annonce['Places']; ?></p>
                            <p><strong>Responsable :</strong> <?php echo htmlspecialchars($annonce['Pseudo']); ?></p>
                            
                            <?php if (!empty($annonce['Commentaire'])): ?>
                                <p><strong>Commentaire :</strong> <?php echo htmlspecialchars($annonce['Commentaire']); ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($annonce['Inscrits'])): ?>
                                <p><strong>Inscrits :</strong> <?php echo implode(', ', $annonce['Inscrits']); ?></p>
                            <?php endif; ?>
                            
                            <?php if (isset($_SESSION['utilisateur']) && 
                                     $annonce['Pseudo'] !== $_SESSION['utilisateur'] && 
                                     !in_array($_SESSION['utilisateur'], $annonce['Inscrits']) && 
                                     $places_disponibles > 0): ?>
                                <form method="POST" action="rechercher.php" class="mt-2">
                                    <input type="hidden" name="index" value="<?php echo $index_original; ?>">
                                    <button type="submit" name="inscrire" class="btn btn-success btn-sm">S'inscrire</button>
                                </form>
                            <?php elseif (isset($_SESSION['utilisateur']) && in_array($_SESSION['utilisateur'], $annonce['Inscrits'])): ?>
                                <span class="badge bg-success">Déjà inscrit</span>
                            <?php elseif (isset($_SESSION['utilisateur']) && $annonce['Pseudo'] === $_SESSION['utilisateur']): ?>
                                <span class="badge bg-primary">Votre annonce</span>
                            <?php elseif ($places_disponibles <= 0): ?>
                                <span class="badge bg-danger">Complet</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Aucune mission ne correspond à vos critères.</p>
    <?php endif; ?>
    
    <div class="text-center mt-4">
        <a href="visualiser.php" class="btn btn-secondary">Voir toutes les missions</a>
        <?php if (isset($_SESSION['utilisateur'])): ?>
            <a href="proposer.php" class="btn btn-primary">Créer une mission</a>
        <?php endif; ?>
    </div>
</section>

<?php
pieddepage();
?>
