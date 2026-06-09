<?php
session_start();
include("scripts/fonctions.php");

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit();
}

$fichier_utilisateurs = 'data/r209-tp_utilisateurs.json';
$utilisateurs = json_decode(file_get_contents($fichier_utilisateurs), true);

$user_courant_key = null;
foreach ($utilisateurs as $key => $user) {
    if ($user['utilisateur'] === $_SESSION['utilisateur']) {
        $user_courant_key = $key;
        break;
    }
}

$erreur = "";
$succes = "";
$groupes_possibles = ['admin', 'salaries', 'managers', 'direction', 'perso'];

if (isset($_POST['submit'])) {
    if (isset($_POST['vehicule'])) {
        $utilisateurs[$user_courant_key]['vehicule'] = $_POST['vehicule'];
        $_SESSION['vehicule'] = $_POST['vehicule'];
        $succes = "Votre poste/service a bien été mis à jour.";
    }

    if (!empty($_POST['mdp'])) {
        $utilisateurs[$user_courant_key]['motdepasse'] = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
        $succes .= "<br>Votre mot de passe a bien été mis à jour.";
    }

    if (isset($_POST['groupes'])) {
        $utilisateurs[$user_courant_key]['groupes'] = is_array($_POST['groupes']) ? $_POST['groupes'] : [$_POST['groupes']];
        $_SESSION['groupes'] = $utilisateurs[$user_courant_key]['groupes'];
        $succes .= "<br>Vos groupes ont bien été mis à jour.";
    }
    
    if (empty($erreur)) {
        file_put_contents($fichier_utilisateurs, json_encode($utilisateurs, JSON_PRETTY_PRINT));
    }
}

parametres("Profil Agent");
entete();
navigation("profil");
?>

<section class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Mon Profil Agent</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($erreur)): ?>
                        <div class="alert alert-danger"><?php echo $erreur; ?></div>
                    <?php endif; ?>
                    <?php if (!empty($succes)): ?>
                        <div class="alert alert-success"><?php echo $succes; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="profil.php">
                        <div class="mb-3">
                            <label for="login" class="form-label">Identifiant Agent</label>
                            <input type="text" name="login" class="form-control" id="login" value="<?php echo htmlspecialchars($_SESSION['utilisateur']); ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email professionnel</label>
                            <input type="email" name="email" class="form-control" id="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="vehicule" class="form-label">Poste / Service</label>
                            <input type="text" name="vehicule" class="form-control" id="vehicule" value="<?php echo htmlspecialchars($_SESSION['vehicule']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Groupes d'appartenance</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="groupes[]" value="salaries" id="salaries"
                                    <?php echo (isset($_SESSION['groupes']) && in_array('salaries', $_SESSION['groupes'])) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="salaries">Salariés</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="groupes[]" value="managers" id="managers"
                                    <?php echo (isset($_SESSION['groupes']) && in_array('managers', $_SESSION['groupes'])) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="managers">Managers</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="groupes[]" value="direction" id="direction"
                                    <?php echo (isset($_SESSION['groupes']) && in_array('direction', $_SESSION['groupes'])) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="direction">Direction</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="groupes[]" value="perso" id="perso"
                                    <?php echo (isset($_SESSION['groupes']) && in_array('perso', $_SESSION['groupes'])) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="perso">Perso</label>
                            </div>
                            <?php if (isset($_SESSION['groupes']) && in_array('admin', $_SESSION['groupes'])): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="groupes[]" value="admin" id="admin"
                                    <?php echo (isset($_SESSION['groupes']) && in_array('admin', $_SESSION['groupes'])) ? 'checked' : ''; ?> disabled>
                                <label class="form-check-label" for="admin">Admin</label>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <hr>
                        <h5>Changer le mot de passe</h5>
                        <div class="mb-3">
                            <label for="mdp" class="form-label">Nouveau mot de passe</label>
                            <input type="password" name="mdp" class="form-control" id="mdp">
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="submit" class="btn btn-primary">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
pieddepage();
?>
