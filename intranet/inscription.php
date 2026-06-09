<?php
session_start();
include("scripts/fonctions.php");

$erreur = "";
$succes = "";

if (isset($_POST['submit'])) {
    $login = $_POST['login'];
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];
    $vehicule = $_POST['vehicule'];
    $groupes = isset($_POST['groupes']) ? $_POST['groupes'] : ['salaries'];

    $fichier_utilisateurs = 'data/r209-tp_utilisateurs.json';
    
    if (file_exists($fichier_utilisateurs)) {
        $contenu_json = file_get_contents($fichier_utilisateurs);
        $utilisateurs = json_decode($contenu_json, true);
    } else {
        $utilisateurs = [];
    }

    $utilisateur_existant = false;
    foreach ($utilisateurs as $user) {
        if ($user['utilisateur'] === $login) {
            $utilisateur_existant = true;
            break;
        }
    }

    if ($utilisateur_existant) {
        $erreur = "Cet identifiant est déjà utilisé.";
    } elseif (empty($login) || empty($email) || empty($mdp)) {
        $erreur = "Tous les champs obligatoires doivent être remplis.";
    } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@alarme-videosurveillance-35\.fr$/', $email)) {
        $erreur = "L'email doit appartenir au domaine @alarme-videosurveillance-35.fr";
    } else {
        $hashed_password = password_hash($mdp, PASSWORD_DEFAULT);

        $token_confirmation = bin2hex(random_bytes(32));
        $nouvel_utilisateur = [
            "utilisateur" => $login,
            "motdepasse" => $hashed_password,
            "vehicule" => $vehicule,
            "email" => $email,
            "groupes" => is_array($groupes) ? $groupes : [$groupes],
            "actif" => false,
            "token_confirmation" => $token_confirmation,
            "date_creation" => date('Y-m-d H:i:s')
        ];

        $utilisateurs[] = $nouvel_utilisateur;

        if (file_put_contents($fichier_utilisateurs, json_encode($utilisateurs, JSON_PRETTY_PRINT))) {
            // Simulation d'envoi d'email de confirmation
            $lien_confirmation = "http://localhost/intranet/confirmer.php?token=" . $token_confirmation;
            $succes = "Demande d'accès envoyée ! Un email de confirmation a été envoyé à " . htmlspecialchars($email) . ".<br>";
            $succes .= "<small class='text-muted'>Simulation: <a href='" . $lien_confirmation . "' class='alert-link'>Cliquez ici pour confirmer votre compte</a></small>";
        } else {
            $erreur = "Une erreur est survenue lors de l'inscription.";
        }
    }
}

parametres("Demande d'accès - Vidéosurveillance Saint-Malo");
entete();
navigation("inscription");
?>

<section class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Demande d'accès Agent</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($erreur)): ?>
                        <div class="alert alert-danger"><?php echo $erreur; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="inscription.php">
                        <div class="mb-3">
                            <label for="login" class="form-label">Identifiant Agent *</label>
                            <input type="text" name="login" class="form-control" id="login" required>
                        </div>
                        <div class="mb-3">
                            <label for="vehicule" class="form-label">Poste / Service</label>
                            <input type="text" name="vehicule" class="form-control" id="vehicule">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email professionnel *</label>
                            <input type="email" name="email" class="form-control" id="email" required>
                            <small class="text-muted">Doit appartenir au domaine @alarme-videosurveillance-35.fr</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Groupes d'appartenance *</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="groupes[]" value="salaries" id="salaries" checked>
                                <label class="form-check-label" for="salaries">Salariés</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="groupes[]" value="managers" id="managers">
                                <label class="form-check-label" for="managers">Managers</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="groupes[]" value="direction" id="direction">
                                <label class="form-check-label" for="direction">Direction</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="groupes[]" value="perso" id="perso">
                                <label class="form-check-label" for="perso">Perso</label>
                            </div>
                            <small class="text-muted">Sélectionnez au moins un groupe</small>
                        </div>
                        <div class="mb-3">
                            <label for="mdp" class="form-label">Mot de passe *</label>
                            <input type="password" name="mdp" class="form-control" id="mdp" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="submit" class="btn btn-primary">Envoyer la demande</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <small>* champs obligatoires</small><br>
                    <a href="connexion.php">Déjà un accès ? Se connecter</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
pieddepage();
?>
