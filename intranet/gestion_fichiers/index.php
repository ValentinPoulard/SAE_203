<?php
session_start();
require_once("../scripts/fonctions.php");

if (!isset($_SESSION['utilisateur'])) {
    header("Location: ../connexion.php");
    exit();
}

$espace = $_GET['espace'] ?? 'public';
$dossier = $_GET['dossier'] ?? ''; // Pour la navigation dans les sous-dossiers
$groupes_possibles = ['admin', 'salaries', 'managers', 'direction', 'perso'];
$allowed_spaces = ['public', 'prive', ...$groupes_possibles];

if (!in_array($espace, $allowed_spaces)) {
    $espace = 'public';
}

// Définition des chemins
$base_path = __DIR__;
$public_path = $base_path . '/public';
$prive_path = $base_path . '/prive';
$logs_path = $base_path . '/logs';

// Créer les dossiers de groupes s'ils n'existent pas
foreach ($groupes_possibles as $groupe) {
    $groupe_path = $base_path . '/' . $groupe;
    if (!is_dir($groupe_path)) {
        mkdir($groupe_path, 0755, true);
    }
}

// Vérification des droits d'accès
$access_denied = false;
$can_delete = false;

switch ($espace) {
    case 'public':
        $current_path = $public_path;
        $can_delete = (in_array('admin', $_SESSION['groupes']) || in_array('managers', $_SESSION['groupes']));
        break;
        
    case 'prive':
        // Espace privé individuel par utilisateur
        // Les admins voient tous les dossiers privés, les autres voient seulement le leur
        if (in_array('admin', $_SESSION['groupes'])) {
            // Pour les admins, permettre la navigation dans les sous-dossiers
            if (!empty($dossier)) {
                // Sécuriser le chemin du dossier
                $dossier = str_replace('..', '', $dossier);
                $current_path = $prive_path . '/' . $dossier;
            } else {
                $current_path = $prive_path;
            }
            $can_delete = true; // Les admins peuvent supprimer tous les fichiers privés
        } else {
            $current_path = $prive_path . '/' . $_SESSION['utilisateur'];
            $can_delete = true; // L'utilisateur peut supprimer ses propres fichiers
            if (!is_dir($current_path)) {
                mkdir($current_path, 0755, true);
            }
        }
        break;
        
    default:
        // Espaces de groupes
        if (in_array($espace, $groupes_possibles)) {
            // Vérifier si l'utilisateur appartient à ce groupe
            if (!in_array($espace, $_SESSION['groupes']) && !in_array('admin', $_SESSION['groupes'])) {
                $access_denied = true;
            } else {
                $current_path = $base_path . '/' . $espace;
                $can_delete = (in_array('admin', $_SESSION['groupes']) || in_array('managers', $_SESSION['groupes']));
            }
        }
        break;
}

// Gestion de l'upload
$erreur_upload = "";
$succes_upload = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fichier']) && !$access_denied) {
    $fichier = $_FILES['fichier'];
    
    // Validation de sécurité
    $allowed_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'jpg', 'jpeg', 'png', 'zip', 'rar'];
    $max_size = 10 * 1024 * 1024; // 10 Mo
    
    $file_extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
    $file_size = $fichier['size'];
    
    // Logs d'erreur
    $log_message = date('Y-m-d H:i:s') . " - Upload tentative - User: " . $_SESSION['utilisateur'] . " - File: " . $fichier['name'];
    
    if ($fichier['error'] !== UPLOAD_ERR_OK) {
        $erreur_upload = "Erreur lors de l'upload: " . $fichier['error'];
        $log_message .= " - Error: " . $fichier['error'];
        file_put_contents($logs_path . '/upload_errors.log', $log_message . "\n", FILE_APPEND);
    } elseif (!in_array($file_extension, $allowed_extensions)) {
        $erreur_upload = "Type de fichier non autorisé. Extensions autorisées: " . implode(', ', $allowed_extensions);
        $log_message .= " - Extension non autorisée: " . $file_extension;
        file_put_contents($logs_path . '/upload_errors.log', $log_message . "\n", FILE_APPEND);
    } elseif ($file_size > $max_size) {
        $erreur_upload = "Fichier trop volumineux. Maximum: 10 Mo";
        $log_message .= " - Taille dépassée: " . $file_size;
        file_put_contents($logs_path . '/upload_errors.log', $log_message . "\n", FILE_APPEND);
    } else {
        // Sécurisation du nom de fichier
        $safe_filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fichier['name']);
        $safe_filename = time() . '_' . $safe_filename;
        $destination = $current_path . '/' . $safe_filename;
        
        if (move_uploaded_file($fichier['tmp_name'], $destination)) {
            $succes_upload = "Fichier uploadé avec succès !";
            $log_message .= " - Succès";
            file_put_contents($logs_path . '/upload_success.log', $log_message . "\n", FILE_APPEND);
        } else {
            $erreur_upload = "Erreur lors du déplacement du fichier.";
            $log_message .= " - Erreur déplacement";
            file_put_contents($logs_path . '/upload_errors.log', $log_message . "\n", FILE_APPEND);
        }
    }
}

// Gestion de la suppression
if (isset($_GET['supprimer']) && $can_delete) {
    $fichier_a_supprimer = basename($_GET['supprimer']);
    $chemin_fichier = $current_path . '/' . $fichier_a_supprimer;
    
    if (file_exists($chemin_fichier) && is_file($chemin_fichier)) {
        if (unlink($chemin_fichier)) {
            $succes_upload = "Fichier supprimé avec succès !";
            $log_message = date('Y-m-d H:i:s') . " - Suppression - User: " . $_SESSION['utilisateur'] . " - File: " . $fichier_a_supprimer;
            file_put_contents($logs_path . '/delete.log', $log_message . "\n", FILE_APPEND);
        } else {
            $erreur_upload = "Erreur lors de la suppression du fichier.";
        }
    }
}

// Lecture des fichiers et dossiers
$dossiers = [];
$fichiers = [];
if (!$access_denied && is_dir($current_path)) {
    $items = scandir($current_path);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $item_path = $current_path . '/' . $item;
        if (is_dir($item_path)) {
            $dossiers[] = $item;
        } else {
            $fichiers[] = $item;
        }
    }
}

parametres("Gestionnaire de Fichiers");
entete();
navigation("fichiers");
?>

<section class="container">
    <h2 class="mb-4">Gestionnaire de Fichiers</h2>
    
    <?php if ($espace === 'prive' && in_array('admin', $_SESSION['groupes'])): ?>
        <!-- Fil d'ariane pour les admins -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="?espace=prive">Espace Privé</a></li>
                <?php if (!empty($dossier)): ?>
                    <?php
                    $dossier_parts = explode('/', $dossier);
                    $path_so_far = '';
                    foreach ($dossier_parts as $index => $part):
                        $path_so_far .= ($index > 0 ? '/' : '') . $part;
                        $is_last = ($index === count($dossier_parts) - 1);
                    ?>
                        <li class="breadcrumb-item <?php echo $is_last ? 'active' : ''; ?>">
                            <?php if (!$is_last): ?>
                                <a href="?espace=prive&dossier=<?php echo urlencode($path_so_far); ?>"><?php echo htmlspecialchars($part); ?></a>
                            <?php else: ?>
                                <?php echo htmlspecialchars($part); ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ol>
        </nav>
    <?php endif; ?>
    
    <!-- Navigation entre espaces -->
    <ul class="nav nav-tabs mb-4" id="espaceTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?php echo $espace === 'public' ? 'active' : ''; ?>" 
               href="?espace=public" id="public-tab">
                📁 Espace Public
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $espace === 'prive' ? 'active' : ''; ?>" 
               href="?espace=prive" id="prive-tab">
                🔒 Espace Privé
            </a>
        </li>
        <?php foreach ($groupes_possibles as $groupe): ?>
            <?php if (in_array($groupe, $_SESSION['groupes'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $espace === $groupe ? 'active' : ''; ?>" 
                       href="?espace=<?php echo $groupe; ?>" id="<?php echo $groupe; ?>-tab">
                        🏢 <?php echo ucfirst($groupe); ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    
    <?php if ($access_denied): ?>
        <div class="alert alert-danger">
            <strong>Accès refusé</strong> - Vous n'avez pas les droits pour accéder à cet espace.
        </div>
    <?php else: ?>
        
        <?php if (!empty($erreur_upload)): ?>
            <div class="alert alert-danger"><?php echo $erreur_upload; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($succes_upload)): ?>
            <div class="alert alert-success"><?php echo $succes_upload; ?></div>
        <?php endif; ?>
        
        <!-- Formulaire d'upload -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Ajouter un fichier</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="fichier" class="form-label">Sélectionner un fichier</label>
                        <input type="file" name="fichier" class="form-control" id="fichier" required>
                        <small class="text-muted">
                            Extensions autorisées: pdf, doc, docx, xls, xlsx, ppt, pptx, txt, csv, jpg, jpeg, png, zip, rar<br>
                            Taille maximale: 10 Mo
                        </small>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        📤 Uploader
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Liste des fichiers -->
        <div class="card">
            <div class="card-header">
                <h5>Fichiers dans l'espace <?php echo ucfirst($espace); ?></h5>
                <?php if ($espace === 'prive' && !in_array('admin', $_SESSION['groupes'])): ?>
                    <small class="text-muted">Dossier: <?php echo htmlspecialchars($_SESSION['utilisateur']); ?></small>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($dossiers) && empty($fichiers)): ?>
                    <p class="text-muted">Aucun fichier ou dossier dans cet espace.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Taille</th>
                                    <th>Date de modification</th>
                                    <th>Type</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dossiers as $dossier_item): ?>
                                    <?php
                                    $chemin_complet = $current_path . '/' . $dossier_item;
                                    $date_modif = date('d/m/Y H:i', filemtime($chemin_complet));
                                    $new_dossier = !empty($dossier) ? $dossier . '/' . $dossier_item : $dossier_item;
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="?espace=<?php echo $espace; ?>&dossier=<?php echo urlencode($new_dossier); ?>" class="text-decoration-none">
                                                📁 <?php echo htmlspecialchars($dossier_item); ?>
                                            </a>
                                        </td>
                                        <td>-</td>
                                        <td><?php echo $date_modif; ?></td>
                                        <td>
                                            <span class="badge bg-primary">Dossier</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="?espace=<?php echo $espace; ?>&dossier=<?php echo urlencode($new_dossier); ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                👁️ Ouvrir
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php foreach ($fichiers as $fichier): ?>
                                    <?php
                                    $chemin_complet = $current_path . '/' . $fichier;
                                    $taille = filesize($chemin_complet);
                                    $date_modif = date('d/m/Y H:i', filemtime($chemin_complet));
                                    $extension = strtolower(pathinfo($fichier, PATHINFO_EXTENSION));
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($fichier); ?>
                                        </td>
                                        <td><?php echo format_taille($taille); ?></td>
                                        <td><?php echo $date_modif; ?></td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo strtoupper($extension); ?></span>
                                        </td>
                                        <td class="text-end">
                                            <a href="telecharger.php?espace=<?php echo $espace; ?>&fichier=<?php echo urlencode($fichier); ?>&dossier=<?php echo urlencode($dossier); ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                📥 Télécharger
                                            </a>
                                            <?php if ($can_delete): ?>
                                                <a href="?espace=<?php echo $espace; ?>&dossier=<?php echo urlencode($dossier); ?>&supprimer=<?php echo urlencode($fichier); ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Supprimer ce fichier ?')">
                                                    🗑️ Supprimer
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php
pieddepage();

// Fonction utilitaire pour formater la taille
function format_taille($octets) {
    if ($octets == 0) {
        return '0 o';
    }
    $unites = ['o', 'Ko', 'Mo', 'Go'];
    $puissance = floor(log($octets, 1024));
    return round($octets / pow(1024, $puissance), 2) . ' ' . $unites[$puissance];
}
?>
