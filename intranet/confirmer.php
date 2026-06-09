<?php
session_start();
include("scripts/fonctions.php");

$fichier_utilisateurs = 'data/r209-tp_utilisateurs.json';
$utilisateurs = [];

if (file_exists($fichier_utilisateurs)) {
    $utilisateurs = json_decode(file_get_contents($fichier_utilisateurs), true);
}

$erreur = "";
$succes = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $utilisateur_trouve = null;
    $index = -1;

    foreach ($utilisateurs as $i => $user) {
        if (isset($user['token_confirmation']) && $user['token_confirmation'] === $token) {
            $utilisateur_trouve = $user;
            $index = $i;
            break;
        }
    }

    if ($utilisateur_trouve) {
        if ($utilisateurs[$index]['actif'] === true) {
            $succes = "Votre compte est déjà activé. Vous pouvez vous connecter.";
        } else {
            $utilisateurs[$index]['actif'] = true;
            unset($utilisateurs[$index]['token_confirmation']);
            
            if (file_put_contents($fichier_utilisateurs, json_encode($utilisateurs, JSON_PRETTY_PRINT))) {
                $succes = "Votre compte a été activé avec succès ! Vous pouvez maintenant vous connecter.";
            } else {
                $erreur = "Une erreur est survenue lors de l'activation.";
            }
        }
    } else {
        $erreur = "Token de confirmation invalide ou expiré.";
    }
} else {
    $erreur = "Aucun token de confirmation fourni.";
}

parametres("Confirmation de compte");
entete();
navigation("connexion");
?>

<section class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Confirmation de compte</h3>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($erreur)): ?>
                        <div class="alert alert-danger">
                            <?php echo $erreur; ?>
                        </div>
                        <a href="inscription.php" class="btn btn-secondary">Retour à l'inscription</a>
                    <?php endif; ?>
                    
                    <?php if (!empty($succes)): ?>
                        <div class="alert alert-success">
                            <?php echo $succes; ?>
                        </div>
                        <a href="connexion.php" class="btn btn-primary">Se connecter</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
pieddepage();
?>
