<?php
session_start();
include("scripts/fonctions.php");

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit();
}

$erreur = "";
$succes = "";

if (isset($_POST['submit'])) {
    $date = $_POST['date'];
    $depart = $_POST['depart'];
    $arrivee = $_POST['arrivee'];
    $places = $_POST['places'];
    $commentaire = $_POST['commentaire'];

    if (empty($date) || empty($depart) || empty($arrivee) || empty($places)) {
        $erreur = "Tous les champs obligatoires doivent être remplis.";
    } elseif ($places < 1 || $places > 10) {
        $erreur = "Le nombre de places doit être entre 1 et 10.";
    } else {
        $fichier_annonces = 'data/r209-tp_annonces.json';
        
        if (file_exists($fichier_annonces)) {
            $annonces = json_decode(file_get_contents($fichier_annonces), true);
        } else {
            $annonces = [];
        }

        $nouvelle_annonce = [
            "Pseudo" => $_SESSION['utilisateur'],
            "Date" => $date,
            "Depart" => $depart,
            "Arrivee" => $arrivee,
            "Places" => (int)$places,
            "Commentaire" => $commentaire,
            "Inscrits" => []
        ];

        $annonces[] = $nouvelle_annonce;

        if (file_put_contents($fichier_annonces, json_encode($annonces, JSON_PRETTY_PRINT))) {
            $succes = "Mission créée avec succès !";
        } else {
            $erreur = "Une erreur est survenue lors de la création de la mission.";
        }
    }
}

parametres("Créer une mission");
entete();
navigation("proposer");
?>

<section class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Créer une mission</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($erreur)): ?>
                        <div class="alert alert-danger"><?php echo $erreur; ?></div>
                    <?php endif; ?>
                    <?php if (!empty($succes)): ?>
                        <div class="alert alert-success"><?php echo $succes; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="proposer.php">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date et heure de l'intervention *</label>
                            <input type="datetime-local" name="date" class="form-control" id="date" required>
                        </div>
                        <div class="mb-3">
                            <label for="depart" class="form-label">Client / Site *</label>
                            <input type="text" name="depart" class="form-control" id="depart" required>
                        </div>
                        <div class="mb-3">
                            <label for="arrivee" class="form-label">Type d'intervention *</label>
                            <select name="arrivee" class="form-select" id="arrivee" required>
                                <option value="">Sélectionner...</option>
                                <option value="Installation">Installation</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Dépannage">Dépannage</option>
                                <option value="Contrôle">Contrôle technique</option>
                                <option value="Formation">Formation client</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="places" class="form-label">Nombre d'agents requis *</label>
                            <input type="number" name="places" class="form-control" id="places" min="1" max="10" required>
                        </div>
                        <div class="mb-3">
                            <label for="commentaire" class="form-label">Détails de l'intervention</label>
                            <textarea name="commentaire" class="form-control" id="commentaire" rows="3" placeholder="Description du travail à effectuer..."></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="submit" class="btn btn-primary">Créer l'intervention</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <small>* champs obligatoires</small><br>
                    <a href="visualiser.php">Voir les missions</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
pieddepage();
?>
