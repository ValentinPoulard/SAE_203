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


if (isset($_POST['submit']) && isset($_POST['index'])) {
    $index = (int)$_POST['index'];
    
    if ($index >= 0 && $index < count($annonces)) {
        $annonce = $annonces[$index];
        
        $peut_modifier = ($annonce['Pseudo'] === $_SESSION['utilisateur']) || 
                         (in_array('managers', $_SESSION['groupes'])) || 
                         (in_array('admin', $_SESSION['groupes']));
        
        if ($peut_modifier) {
            $annonces[$index]['Date'] = $_POST['date'];
            $annonces[$index]['Depart'] = $_POST['depart'];
            $annonces[$index]['Arrivee'] = $_POST['arrivee'];
            $annonces[$index]['Places'] = (int)$_POST['places'];
            $annonces[$index]['Commentaire'] = $_POST['commentaire'];
            
            if (file_put_contents($fichier_annonces, json_encode($annonces, JSON_PRETTY_PRINT))) {
                $succes = "Mission modifiée avec succès !";
            } else {
                $erreur = "Une erreur est survenue lors de la modification.";
            }
        } else {
            $erreur = "Vous n'avez pas les droits pour modifier cette mission.";
        }
    }
}

if (isset($_POST['supprimer']) && isset($_POST['index'])) {
    $index = (int)$_POST['index'];
    
    if ($index >= 0 && $index < count($annonces)) {
        $annonce = $annonces[$index];
        
        $peut_supprimer = ($annonce['Pseudo'] === $_SESSION['utilisateur']) || 
                          (in_array('managers', $_SESSION['groupes'])) || 
                          (in_array('admin', $_SESSION['groupes']));
        
        if ($peut_supprimer) {
            array_splice($annonces, $index, 1);
            
            if (file_put_contents($fichier_annonces, json_encode($annonces, JSON_PRETTY_PRINT))) {
                $succes = "Mission supprimée avec succès !";
            } else {
                $erreur = "Une erreur est survenue lors de la suppression.";
            }
        } else {
            $erreur = "Vous n'avez pas les droits pour supprimer cette mission.";
        }
    }
}

parametres("Gérer les missions");
entete();
navigation("modifier");
?>

<section class="container">
    <h2>Gestion des missions</h2>
    
    <?php if (!empty($erreur)): ?>
        <div class="alert alert-danger"><?php echo $erreur; ?></div>
    <?php endif; ?>
    <?php if (!empty($succes)): ?>
        <div class="alert alert-success"><?php echo $succes; ?></div>
    <?php endif; ?>

    <?php
    $annonces_modifiables = array_filter($annonces, function($annonce) {
        return ($annonce['Pseudo'] === $_SESSION['utilisateur']) || 
               (in_array('managers', $_SESSION['groupes'])) || 
               (in_array('admin', $_SESSION['groupes']));
    });
    
    if (!empty($annonces_modifiables)): ?>
        <div class="row">
            <?php foreach ($annonces_modifiables as $index => $annonce): ?>
                <?php 
                $peut_modifier = ($annonce['Pseudo'] === $_SESSION['utilisateur']) || 
                               (in_array('managers', $_SESSION['groupes'])) || 
                               (in_array('admin', $_SESSION['groupes']));
                ?>
                
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($annonce['Depart']); ?> - <?php echo htmlspecialchars($annonce['Arrivee']); ?>
                                <?php if ($annonce['Pseudo'] === $_SESSION['utilisateur']): ?>
                                    <span class="badge bg-primary">Ma mission</span>
                                <?php endif; ?>
                            </h5>
                            
                            <?php if ($peut_modifier): ?>
                                <form method="POST" action="modifier.php">
                                    <input type="hidden" name="index" value="<?php echo $index; ?>">
                                    
                                    <div class="mb-2">
                                        <label class="form-label">Date et heure</label>
                                        <input type="datetime-local" name="date" class="form-control form-control-sm" 
                                               value="<?php echo $annonce['Date']; ?>" required>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label">Site d'intervention</label>
                                            <input type="text" name="depart" class="form-control form-control-sm" 
                                                   value="<?php echo htmlspecialchars($annonce['Depart']); ?>" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Type de mission</label>
                                            <input type="text" name="arrivee" class="form-control form-control-sm" 
                                                   value="<?php echo htmlspecialchars($annonce['Arrivee']); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-2">
                                        <div class="col-6">
                                            <label class="form-label">Agents requis</label>
                                            <input type="number" name="places" class="form-control form-control-sm" 
                                                   value="<?php echo $annonce['Places']; ?>" min="1" max="10" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Responsable</label>
                                            <input type="text" class="form-control form-control-sm" 
                                                   value="<?php echo htmlspecialchars($annonce['Pseudo']); ?>" readonly>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2 mt-2">
                                        <label class="form-label">Détails de la mission</label>
                                        <textarea name="commentaire" class="form-control form-control-sm" rows="2"><?php echo htmlspecialchars($annonce['Commentaire']); ?></textarea>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="submit" name="submit" class="btn btn-primary btn-sm">Modifier</button>
                                        <button type="submit" name="supprimer" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Supprimer cette mission ?')">Supprimer</button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <p><strong>Date :</strong> <?php echo (new DateTime($annonce['Date']))->format('d/m/Y H:i'); ?></p>
                                <p><strong>Places :</strong> <?php echo $annonce['Places']; ?></p>
                                <p><strong>Conducteur :</strong> <?php echo htmlspecialchars($annonce['Pseudo']); ?></p>
                                <?php if (!empty($annonce['Commentaire'])): ?>
                                    <p><strong>Commentaire :</strong> <?php echo htmlspecialchars($annonce['Commentaire']); ?></p>
                                <?php endif; ?>
                                <p class="text-muted">Vous ne pouvez pas modifier cette mission</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Aucune mission disponible.</p>
    <?php endif; ?>
    
    <div class="text-center mt-4">
        <a href="visualiser.php" class="btn btn-secondary">Voir les missions</a>
        <a href="proposer.php" class="btn btn-primary">Créer une mission</a>
    </div>
</section>

<?php
pieddepage();
?>
