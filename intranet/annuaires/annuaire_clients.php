<?php
$fichier_clients = 'data/annuaire_clients.json';
$clients = [];

if (file_exists($fichier_clients)) {
    $clients = json_decode(file_get_contents($fichier_clients), true);
}

// Gestion CRUD pour administrateurs
$erreur = "";
$succes = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (in_array('admin', $_SESSION['groupes']) || in_array('managers', $_SESSION['groupes']))) {
    
    // Ajout
    if (isset($_POST['ajouter'])) {
        $nouveau_id = max(array_column($clients, 'id')) + 1;
        $nouveau_client = [
            "id" => $nouveau_id,
            "nom" => $_POST['nom'],
            "contact" => $_POST['contact'],
            "email" => $_POST['email'],
            "telephone" => $_POST['telephone'],
            "adresse" => $_POST['adresse'],
            "secteur" => $_POST['secteur'],
            "type_contrat" => $_POST['type_contrat'],
            "date_contrat" => $_POST['date_contrat'],
            "notes" => $_POST['notes']
        ];
        $clients[] = $nouveau_client;
        
        if (file_put_contents($fichier_clients, json_encode($clients, JSON_PRETTY_PRINT))) {
            $succes = "Client ajouté avec succès !";
        } else {
            $erreur = "Erreur lors de l'ajout.";
        }
    }
    
    // Modification
    if (isset($_POST['modifier'])) {
        $id = (int)$_POST['id'];
        foreach ($clients as &$c) {
            if ($c['id'] === $id) {
                $c['nom'] = $_POST['nom'];
                $c['contact'] = $_POST['contact'];
                $c['email'] = $_POST['email'];
                $c['telephone'] = $_POST['telephone'];
                $c['adresse'] = $_POST['adresse'];
                $c['secteur'] = $_POST['secteur'];
                $c['type_contrat'] = $_POST['type_contrat'];
                $c['date_contrat'] = $_POST['date_contrat'];
                $c['notes'] = $_POST['notes'];
                break;
            }
        }
        
        if (file_put_contents($fichier_clients, json_encode($clients, JSON_PRETTY_PRINT))) {
            $succes = "Client modifié avec succès !";
        } else {
            $erreur = "Erreur lors de la modification.";
        }
    }
    
    // Suppression
    if (isset($_POST['supprimer']) && in_array('admin', $_SESSION['groupes'])) {
        $id = (int)$_POST['id'];
        $clients = array_filter($clients, function($c) use ($id) {
            return $c['id'] !== $id;
        });
        $clients = array_values($clients);
        
        if (file_put_contents($fichier_clients, json_encode($clients, JSON_PRETTY_PRINT))) {
            $succes = "Client supprimé avec succès !";
        } else {
            $erreur = "Erreur lors de la suppression.";
        }
    }
}

// Génération de fiche client
if (isset($_GET['generer_fiche']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $client = null;
    foreach ($clients as $c) {
        if ($c['id'] === $id) {
            $client = $c;
            break;
        }
    }
    
    if ($client) {
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="fiche_client_' . $client['id'] . '.html"');
        
        echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche Client - ' . htmlspecialchars($client['nom']) . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { background: #0056b3; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; border: 1px solid #ddd; margin-top: 20px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Fiche Client</h1>
        <p>Alarme Vidéosurveillance 35</p>
    </div>
    <div class="content">
        <div class="field">
            <span class="label">Entreprise:</span> ' . htmlspecialchars($client['nom']) . '
        </div>
        <div class="field">
            <span class="label">Contact:</span> ' . htmlspecialchars($client['contact']) . '
        </div>
        <div class="field">
            <span class="label">Email:</span> ' . htmlspecialchars($client['email']) . '
        </div>
        <div class="field">
            <span class="label">Téléphone:</span> ' . htmlspecialchars($client['telephone']) . '
        </div>
        <div class="field">
            <span class="label">Adresse:</span> ' . htmlspecialchars($client['adresse']) . '
        </div>
        <div class="field">
            <span class="label">Secteur:</span> ' . htmlspecialchars($client['secteur']) . '
        </div>
        <div class="field">
            <span class="label">Type de contrat:</span> ' . htmlspecialchars($client['type_contrat']) . '
        </div>
        <div class="field">
            <span class="label">Date de contrat:</span> ' . date('d/m/Y', strtotime($client['date_contrat'])) . '
        </div>
        <div class="field">
            <span class="label">Notes:</span> ' . htmlspecialchars($client['notes']) . '
        </div>
    </div>
    <div class="footer">
        <p>Document généré le ' . date('d/m/Y H:i:s') . '</p>
        <p>Confidentiel - Usage interne uniquement</p>
    </div>
</body>
</html>';
        exit();
    }
}
?>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">🔒 Annuaire Clients (<?php echo count($clients); ?> clients)</h5>
        <div class="d-flex gap-2">
            <?php if (in_array('admin', $_SESSION['groupes']) || in_array('managers', $_SESSION['groupes'])): ?>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAjouter">
                    ➕ Ajouter un client
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="alert alert-warning">
            <strong>⚠️ Confidentiel</strong> - Ces informations sont strictement réservées à l'usage interne de l'entreprise.
        </div>
        
        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger"><?php echo $erreur; ?></div>
        <?php endif; ?>
        <?php if (!empty($succes)): ?>
            <div class="alert alert-success"><?php echo $succes; ?></div>
        <?php endif; ?>
        
        <!-- Liste des clients -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Entreprise</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Secteur</th>
                        <th>Type de contrat</th>
                        <th>Date de contrat</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $c): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($c['nom']); ?></td>
                            <td><?php echo htmlspecialchars($c['contact']); ?></td>
                            <td><?php echo htmlspecialchars($c['email']); ?></td>
                            <td><?php echo htmlspecialchars($c['telephone']); ?></td>
                            <td>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($c['secteur']); ?></span>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo htmlspecialchars($c['type_contrat']); ?></span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($c['date_contrat'])); ?></td>
                            <td class="text-end">
                                <a href="?annuaire=clients&generer_fiche=1&id=<?php echo $c['id']; ?>" 
                                   class="btn btn-sm btn-outline-success" title="Générer fiche client">
                                    📄 Fiche
                                </a>
                                <?php if (in_array('admin', $_SESSION['groupes']) || in_array('managers', $_SESSION['groupes'])): ?>
                                    <button class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalModifier<?php echo $c['id']; ?>">
                                        ✏️ Modifier
                                    </button>
                                    <?php if (in_array('admin', $_SESSION['groupes'])): ?>
                                        <form method="POST" class="d-inline" 
                                              onsubmit="return confirm('Supprimer ce client ?')">
                                            <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                            <input type="hidden" name="supprimer" value="1">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                🗑️ Supprimer
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        
                        <!-- Modal de modification -->
                        <?php if (in_array('admin', $_SESSION['groupes']) || in_array('managers', $_SESSION['groupes'])): ?>
                            <div class="modal fade" id="modalModifier<?php echo $c['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Modifier le client</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                                <input type="hidden" name="modifier" value="1">
                                                <div class="mb-3">
                                                    <label class="form-label">Entreprise</label>
                                                    <input type="text" name="nom" class="form-control" 
                                                           value="<?php echo htmlspecialchars($c['nom']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Contact</label>
                                                    <input type="text" name="contact" class="form-control" 
                                                           value="<?php echo htmlspecialchars($c['contact']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" name="email" class="form-control" 
                                                           value="<?php echo htmlspecialchars($c['email']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Téléphone</label>
                                                    <input type="text" name="telephone" class="form-control" 
                                                           value="<?php echo htmlspecialchars($c['telephone']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Adresse</label>
                                                    <textarea name="adresse" class="form-control" rows="2" required><?php echo htmlspecialchars($c['adresse']); ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Secteur</label>
                                                    <input type="text" name="secteur" class="form-control" 
                                                           value="<?php echo htmlspecialchars($c['secteur']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Type de contrat</label>
                                                    <select name="type_contrat" class="form-select" required>
                                                        <option value="Maintenance" <?php echo $c['type_contrat'] === 'Maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                                                        <option value="Installation" <?php echo $c['type_contrat'] === 'Installation' ? 'selected' : ''; ?>>Installation</option>
                                                        <option value="Télésurveillance" <?php echo $c['type_contrat'] === 'Télésurveillance' ? 'selected' : ''; ?>>Télésurveillance</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Date de contrat</label>
                                                    <input type="date" name="date_contrat" class="form-control" 
                                                           value="<?php echo $c['date_contrat']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Notes</label>
                                                    <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($c['notes']); ?></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal d'ajout -->
<?php if (in_array('admin', $_SESSION['groupes']) || in_array('managers', $_SESSION['groupes'])): ?>
    <div class="modal fade" id="modalAjouter" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="ajouter" value="1">
                        <div class="mb-3">
                            <label class="form-label">Entreprise</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact</label>
                            <input type="text" name="contact" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="telephone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Adresse</label>
                            <textarea name="adresse" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Secteur</label>
                            <input type="text" name="secteur" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type de contrat</label>
                            <select name="type_contrat" class="form-select" required>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Installation">Installation</option>
                                <option value="Télésurveillance">Télésurveillance</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date de contrat</label>
                            <input type="date" name="date_contrat" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
