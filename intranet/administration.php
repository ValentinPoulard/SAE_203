<?php
session_start();
include("scripts/fonctions.php");

if (!isset($_SESSION['groupes']) || !in_array('admin', $_SESSION['groupes'])) {
    header("Location: accueil.php");
    exit();
}

$fichier_utilisateurs = 'data/r209-tp_utilisateurs.json';
$utilisateurs = [];
if (file_exists($fichier_utilisateurs)) {
    $contenu_json = file_get_contents($fichier_utilisateurs);
    $utilisateurs = json_decode($contenu_json, true);
}

$recherche = isset($_GET['recherche']) ? trim($_GET['recherche']) : '';
if (!empty($recherche)) {
    $utilisateurs_filtres = [];
    foreach ($utilisateurs as $user) {
        if (stripos($user['utilisateur'], $recherche) !== false || stripos($user['email'], $recherche) !== false) {
            $utilisateurs_filtres[] = $user;
        }
    }
    $utilisateurs = $utilisateurs_filtres;
}

$groupes_possibles = ['admin', 'salaries', 'managers', 'direction', 'perso'];

// Gestion des requêtes POST (avant tout output HTML)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['toggle_actif']) && isset($_POST['utilisateur']) && in_array('admin', $_SESSION['groupes'])) {
        $utilisateur = $_POST['utilisateur'];
        
        foreach ($utilisateurs as &$user) {
            if ($user['utilisateur'] === $utilisateur && $user['utilisateur'] !== 'admin') {
                $user['actif'] = !isset($user['actif']) || $user['actif'] === false;
                break;
            }
        }
        
        file_put_contents($fichier_utilisateurs, json_encode($utilisateurs, JSON_PRETTY_PRINT));
        header("Location: /intranet/administration.php");
        exit();
    }
    
    if (isset($_POST['modifier_groupes']) && isset($_POST['utilisateur']) && isset($_POST['groupes'])) {
        $utilisateur = $_POST['utilisateur'];
        $nouveaux_groupes = $_POST['groupes'];
        
        foreach ($utilisateurs as &$user) {
            if ($user['utilisateur'] === $utilisateur) {
                $user['groupes'] = is_array($nouveaux_groupes) ? $nouveaux_groupes : [$nouveaux_groupes];
                break;
            }
        }
        
        file_put_contents($fichier_utilisateurs, json_encode($utilisateurs, JSON_PRETTY_PRINT));
        header("Location: /intranet/administration.php");
        exit();
    }
    
    if (isset($_POST['supprimer_utilisateur']) && isset($_POST['utilisateur']) && in_array('admin', $_SESSION['groupes'])) {
        $utilisateur = $_POST['utilisateur'];
        
        if ($utilisateur !== 'admin') {
            $utilisateurs = array_filter($utilisateurs, function($user) use ($utilisateur) {
                return $user['utilisateur'] !== $utilisateur;
            });
            
            file_put_contents($fichier_utilisateurs, json_encode(array_values($utilisateurs), JSON_PRETTY_PRINT));
            header("Location: /intranet/administration.php");
            exit();
        }
    }
}

parametres("Administration");
entete();
navigation("administration");
?>

<section class="container">
    <h2 class="mb-4">Gestion des Agents</h2>
    
    <div class="mb-4">
        <form action="/intranet/administration.php" method="GET" class="d-flex">
            <input class="form-control me-2" type="search" name="recherche" placeholder="Rechercher par nom d'utilisateur ou email..." value="<?php echo htmlspecialchars($recherche); ?>">
            <button class="btn btn-outline-primary" type="submit">Rechercher</button>
            <a href="/intranet/administration.php" class="btn btn-outline-secondary ms-2">Effacer</a>
        </form>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Identifiant</th>
                    <th>Email</th>
                    <th>Poste/Service</th>
                    <th>Groupes</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($utilisateurs)): ?>
                    <?php foreach ($utilisateurs as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['utilisateur']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['vehicule'] ?? '-'); ?></td>
                            <td>
                                <form method="POST" action="/intranet/administration.php" class="d-inline">
                                    <input type="hidden" name="utilisateur" value="<?php echo htmlspecialchars($user['utilisateur']); ?>">
                                    <div class="btn-group" role="group">
                                        <?php foreach ($groupes_possibles as $groupe): ?>
                                            <input type="checkbox" class="btn-check" name="groupes[]" value="<?php echo $groupe; ?>" id="groupe_<?php echo $user['utilisateur']; ?>_<?php echo $groupe; ?>"
                                                <?php echo (isset($user['groupes']) && in_array($groupe, $user['groupes'])) ? 'checked' : ''; ?>
                                                <?php echo ($user['utilisateur'] === 'admin' && $groupe === 'admin') ? 'disabled' : ''; ?>>
                                            <label class="btn btn-outline-secondary btn-sm" for="groupe_<?php echo $user['utilisateur']; ?>_<?php echo $groupe; ?>">
                                                <?php echo ucfirst($groupe); ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="submit" name="modifier_groupes" class="btn btn-sm btn-outline-primary ms-2"
                                            <?php echo ($user['utilisateur'] === 'admin') ? 'disabled' : ''; ?>>
                                        Modifier
                                    </button>
                                </form>
                            </td>
                            <td>
                                <?php 
                                $est_actif = isset($user['actif']) ? $user['actif'] : true;
                                if ($est_actif): ?>
                                    <span class="badge bg-success">Actif</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Inactif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <?php if (in_array('admin', $_SESSION['groupes'])): ?>
                                        <form method="POST" action="/intranet/administration.php" class="d-inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir changer le statut de cet agent ?')">
                                            <input type="hidden" name="utilisateur" value="<?php echo htmlspecialchars($user['utilisateur']); ?>">
                                            <button type="submit" name="toggle_actif" class="btn btn-sm btn-outline-<?php echo $est_actif ? 'warning' : 'success'; ?>"
                                                    <?php echo ($user['utilisateur'] === 'admin') ? 'disabled' : ''; ?>>
                                                <?php echo $est_actif ? 'Désactiver' : 'Activer'; ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" action="/intranet/administration.php" class="d-inline"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet agent ?')">
                                        <input type="hidden" name="utilisateur" value="<?php echo htmlspecialchars($user['utilisateur']); ?>">
                                        <button type="submit" name="supprimer_utilisateur" class="btn btn-sm btn-danger"
                                                <?php echo ($user['utilisateur'] === 'admin' || !in_array('admin', $_SESSION['groupes'])) ? 'disabled' : ''; ?>>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Aucun agent trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php
pieddepage();
?>
