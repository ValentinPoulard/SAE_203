<?php
session_start();
require_once("../scripts/fonctions.php");

if (!isset($_SESSION['utilisateur'])) {
    header("Location: ../connexion.php");
    exit();
}

$espace = $_GET['espace'] ?? 'public';
$fichier = $_GET['fichier'] ?? '';
$dossier = $_GET['dossier'] ?? '';

$base_path = __DIR__;
$groupes_possibles = ['admin', 'salaries', 'managers', 'direction', 'perso'];
$allowed_spaces = ['public', 'prive', ...$groupes_possibles];

if (!in_array($espace, $allowed_spaces)) {
    die("Espace non autorisé");
}

// Définition des chemins
switch ($espace) {
    case 'public':
        $current_path = $base_path . '/public';
        break;
    case 'prive':
        // Espace privé individuel par utilisateur
        // Les admins peuvent accéder à tous les dossiers privés
        if (in_array('admin', $_SESSION['groupes'])) {
            // Pour les admins, utiliser le dossier spécifié si fourni
            if (!empty($dossier)) {
                $dossier = str_replace('..', '', $dossier);
                $current_path = $base_path . '/prive/' . $dossier;
            } else {
                // Si pas de dossier, rechercher le fichier dans tous les sous-dossiers
                $fichier = basename($fichier);
                $found = false;
                if (is_dir($base_path . '/prive')) {
                    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base_path . '/prive'));
                    foreach ($iterator as $file) {
                        if ($file->getFilename() === $fichier && $file->isFile()) {
                            $current_path = $file->getPath();
                            $found = true;
                            break;
                        }
                    }
                }
                if (!$found) {
                    die("Fichier non trouvé");
                }
            }
        } else {
            $current_path = $base_path . '/prive/' . $_SESSION['utilisateur'];
            if (!is_dir($current_path)) {
                die("Dossier privé inexistant");
            }
        }
        break;
    default:
        // Espaces de groupes
        if (in_array($espace, $groupes_possibles)) {
            // Vérifier si l'utilisateur appartient à ce groupe
            if (!in_array($espace, $_SESSION['groupes']) && !in_array('admin', $_SESSION['groupes'])) {
                die("Accès refusé - Vous n'avez pas les droits pour cet espace");
            } else {
                $current_path = $base_path . '/' . $espace;
            }
        }
        break;
}

// Sécurisation du nom de fichier
$fichier = basename($fichier);
$chemin_fichier = $current_path . '/' . $fichier;

// Vérification que le fichier existe et est dans le bon répertoire
if (!file_exists($chemin_fichier) || !is_file($chemin_fichier)) {
    die("Fichier non trouvé");
}

// Vérification que le fichier est bien dans le répertoire autorisé
$real_path = realpath($chemin_fichier);
$real_base_path = realpath($current_path);
if (strpos($real_path, $real_base_path) !== 0) {
    die("Accès non autorisé");
}

// Récupération du type MIME
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $chemin_fichier);
finfo_close($finfo);

// Envoi du fichier
header('Content-Type: ' . $mime_type);
header('Content-Disposition: attachment; filename="' . $fichier . '"');
header('Content-Length: ' . filesize($chemin_fichier));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

readfile($chemin_fichier);

// Log du téléchargement
$logs_path = $base_path . '/logs';
$log_message = date('Y-m-d H:i:s') . " - Téléchargement - User: " . $_SESSION['utilisateur'] . " - File: " . $fichier . " - Espace: " . $espace;
file_put_contents($logs_path . '/download.log', $log_message . "\n", FILE_APPEND);
?>
