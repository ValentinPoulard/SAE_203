<?php
session_start();
include("scripts/fonctions.php");

if (isset($_POST['submit'])) {
    $login_saisi = $_POST['login'];
    $mdp_saisi = $_POST['mdp'];

    $fichier = 'data/r209-tp_utilisateurs.json';
    
    if (file_exists($fichier)) {
        $contenu = file_get_contents($fichier);
        $utilisateurs = json_decode($contenu, true);
    } else {
        $utilisateurs = [];
    }

    $user_trouve = null;

    foreach ($utilisateurs as $user) {
        if ($user['utilisateur'] === $login_saisi) {
            $user_trouve = $user;
            break;
        }
    }

    if ($user_trouve && password_verify($mdp_saisi, $user_trouve['motdepasse'])) {
        if (isset($user_trouve['actif']) && $user_trouve['actif'] === false) {
            $erreur = "Votre compte n'est pas encore activé. Contactez un administrateur.";
        } else {
            $_SESSION['utilisateur'] = $user_trouve['utilisateur'];
            $_SESSION['vehicule'] = $user_trouve['vehicule'];
            $_SESSION['email'] = $user_trouve['email'];
            $_SESSION['groupes'] = $user_trouve['groupes'] ?? ['salaries'];
            header("Location: accueil.php");
            exit();
        }
    } else {
        $erreur = "Identifiants incorrects.";
    }
}

parametres("Connexion - Vidéosurveillance Saint-Malo");
entete();
navigation("connexion");
?>

<section class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Connexion Agent</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($erreur)): ?>
                        <div class="alert alert-danger">
                            <?php echo $erreur; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="connexion.php">
                        <div class="mb-3">
                            <label for="login" class="form-label">Identifiant Agent</label>
                            <input type="text" name="login" class="form-control" id="login" required>
                        </div>
                        <div class="mb-3">
                            <label for="mdp" class="form-label">Mot de passe</label>
                            <input type="password" name="mdp" class="form-control" id="mdp" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="submit" class="btn btn-primary">Se connecter</button>
                            <a href="inscription.php" class="btn btn-outline-secondary">Demander un accès</a>
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