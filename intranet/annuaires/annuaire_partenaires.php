<?php
$fichier_partenaires = 'data/annuaire_partenaires.json';
$partenaires = [];

if (file_exists($fichier_partenaires)) {
    $partenaires = json_decode(file_get_contents($fichier_partenaires), true);
}

// Gestion CRUD pour administrateurs
$erreur = "";
$succes = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'modo')) {
    
    // Ajout
    if (isset($_POST['ajouter'])) {
        $nouveau_id = max(array_column($partenaires, 'id')) + 1;
        
        // Gestion du logo
        $logo = '';
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $allowed_extensions = ['png', 'jpg', 'jpeg', 'gif'];
            $file_extension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            
            if (in_array($file_extension, $allowed_extensions)) {
                $logo_filename = 'logo_' . $nouveau_id . '_' . time() . '.' . $file_extension;
                $logo_destination = 'logos/' . $logo_filename;
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $logo_destination)) {
                    $logo = $logo_filename;
                }
            }
        }
        
        $nouveau_partenaire = [
            "id" => $nouveau_id,
            "nom" => $_POST['nom'],
            "contact" => $_POST['contact'],
            "email" => $_POST['email'],
            "telephone" => $_POST['telephone'],
            "adresse" => $_POST['adresse'],
            "specialite" => $_POST['specialite'],
            "logo" => $logo,
            "actif" => true,
            "date_partenariat" => $_POST['date_partenariat']
        ];
        $partenaires[] = $nouveau_partenaire;
        
        if (file_put_contents($fichier_partenaires, json_encode($partenaires, JSON_PRETTY_PRINT))) {
            $succes = "Partenaire ajouté avec succès !";
        } else {
            $erreur = "Erreur lors de l'ajout.";
        }
    }
    
    // Modification
    if (isset($_POST['modifier'])) {
        $id = (int)$_POST['id'];
        foreach ($partenaires as &$p) {
            if ($p['id'] === $id) {
                $p['nom'] = $_POST['nom'];
                $p['contact'] = $_POST['contact'];
                $p['email'] = $_POST['email'];
                $p['telephone'] = $_POST['telephone'];
                $p['adresse'] = $_POST['adresse'];
                $p['specialite'] = $_POST['specialite'];
                $p['actif'] = isset($_POST['actif']);
                $p['date_partenariat'] = $_POST['date_partenariat'];
                
                // Gestion du logo
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $allowed_extensions = ['png', 'jpg', 'jpeg', 'gif'];
                    $file_extension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                    
                    if (in_array($file_extension, $allowed_extensions)) {
                        // Supprimer l'ancien logo
                        if (!empty($p['logo']) && file_exists('logos/' . $p['logo'])) {
                            unlink('logos/' . $p['logo']);
                        }
                        
                        $logo_filename = 'logo_' . $id . '_' . time() . '.' . $file_extension;
                        $logo_destination = 'logos/' . $logo_filename;
                        
                        if (move_uploaded_file($_FILES['logo']['tmp_name'], $logo_destination)) {
                            $p['logo'] = $logo_filename;
                        }
                    }
                }
                break;
            }
        }
        
        if (file_put_contents($fichier_partenaires, json_encode($partenaires, JSON_PRETTY_PRINT))) {
            $succes = "Partenaire modifié avec succès !";
        } else {
            $erreur = "Erreur lors de la modification.";
        }
    }
    
    // Suppression
    if (isset($_POST['supprimer']) && in_array('admin', $_SESSION['groupes'])) {
        $id = (int)$_POST['id'];
        
        // Supprimer le logo
        foreach ($partenaires as $p) {
            if ($p['id'] === $id && !empty($p['logo']) && file_exists('logos/' . $p['logo'])) {
                unlink('logos/' . $p['logo']);
                break;
            }
        }
        
        $partenaires = array_filter($partenaires, function($p) use ($id) {
            return $p['id'] !== $id;
        });
        $partenaires = array_values($partenaires);
        
        if (file_put_contents($fichier_partenaires, json_encode($partenaires, JSON_PRETTY_PRINT))) {
            $succes = "Partenaire supprimé avec succès !";
        } else {
            $erreur = "Erreur lors de la suppression.";
        }
    }
}
?>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">🤝 Annuaire Partenaires (<?php echo count($partenaires); ?> partenaires)</h5>
        <?php if (in_array('admin', $_SESSION['groupes']) || in_array('managers', $_SESSION['groupes'])): ?>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAjouter">
                ➕ Ajouter un partenaire
            </button>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger"><?php echo $erreur; ?></div>
        <?php endif; ?>
        <?php if (!empty($succes)): ?>
            <div class="alert alert-success"><?php echo $succes; ?></div>
        <?php endif; ?>
        
        <!-- Liste des partenaires -->
        <div class="row">
            <?php foreach ($partenaires as $p): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <?php if (!empty($p['logo']) && file_exists('logos/' . $p['logo'])): ?>
                                    <img src="logos/<?php echo htmlspecialchars($p['logo']); ?>" 
                                         alt="Logo" class="me-3" style="max-width: 80px; max-height: 80px;">
                                <?php else: ?>
                                    <div class="me-3 bg-light d-flex align-items-center justify-content-center" 
                                         style="width: 80px; height: 80px;">
                                        <span class="text-muted">🏢</span>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-grow-1">
                                    <h5 class="card-title">
                                        <?php echo htmlspecialchars($p['nom']); ?>
                                        <?php if (!$p['actif']): ?>
                                            <span class="badge bg-warning">Inactif</span>
                                        <?php endif; ?>
                                    </h5>
                                    <p class="card-text mb-1">
                                        <strong>Contact:</strong> <?php echo htmlspecialchars($p['contact']); ?>
                                    </p>
                                    <p class="card-text mb-1">
                                        <strong>Email:</strong> <?php echo htmlspecialchars($p['email']); ?>
                                    </p>
                                    <p class="card-text mb-1">
                                        <strong>Téléphone:</strong> <?php echo htmlspecialchars($p['telephone']); ?>
                                    </p>
                                    <p class="card-text mb-1">
                                        <strong>Spécialité:</strong> 
                                        <span class="badge bg-info"><?php echo htmlspecialchars($p['specialite']); ?></span>
                                    </p>
                                    <p class="card-text mb-1">
                                        <small class="text-muted">
                                            Partenariat depuis: <?php echo date('d/m/Y', strtotime($p['date_partenariat'])); ?>
                                        </small>
                                    </p>
                                </div>
                            </div>
                            <?php if (in_array('admin', $_SESSION['groupes']) || in_array('managers', $_SESSION['groupes'])): ?>
                                <div class="mt-3">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalModifier<?php echo $p['id']; ?>">
                                        ✏️ Modifier
                                    </button>
                                    <?php if (in_array('admin', $_SESSION['groupes'])): ?>
                                        <form method="POST" class="d-inline" 
                                              onsubmit="return confirm('Supprimer ce partenaire ?')">
                                            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                            <input type="hidden" name="supprimer" value="1">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                🗑️ Supprimer
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Modal de modification -->
                <?php if (in_array('admin', $_SESSION['groupes']) || in_array('managers', $_SESSION['groupes'])): ?>
                    <div class="modal fade" id="modalModifier<?php echo $p['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Modifier le partenaire</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                        <input type="hidden" name="modifier" value="1">
                                        <div class="mb-3">
                                            <label class="form-label">Nom</label>
                                            <input type="text" name="nom" class="form-control" 
                                                   value="<?php echo htmlspecialchars($p['nom']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Contact</label>
                                            <input type="text" name="contact" class="form-control" 
                                                   value="<?php echo htmlspecialchars($p['contact']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" 
                                                   value="<?php echo htmlspecialchars($p['email']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Téléphone</label>
                                            <input type="text" name="telephone" class="form-control" 
                                                   value="<?php echo htmlspecialchars($p['telephone']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Adresse</label>
                                            <textarea name="adresse" class="form-control" rows="2" required><?php echo htmlspecialchars($p['adresse']); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Spécialité</label>
                                            <input type="text" name="specialite" class="form-control" 
                                                   value="<?php echo htmlspecialchars($p['specialite']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Logo</label>
                                            <input type="file" name="logo" class="form-control" accept="image/*">
                                            <?php if (!empty($p['logo'])): ?>
                                                <small class="text-muted">Logo actuel: <?php echo htmlspecialchars($p['logo']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Date de partenariat</label>
                                            <input type="date" name="date_partenariat" class="form-control" 
                                                   value="<?php echo $p['date_partenariat']; ?>" required>
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" name="actif" class="form-check-input" 
                                                   id="actif<?php echo $p['id']; ?>" 
                                                   <?php echo $p['actif'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="actif<?php echo $p['id']; ?>">Actif</label>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal d'ajout -->
<?php if (in_array('admin', $_SESSION['groupes']) || in_array('managers', $_SESSION['groupes'])): ?>
    <div class="modal fade" id="modalAjouter" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un partenaire</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="ajouter" value="1">
                        <div class="mb-3">
                            <label class="form-label">Nom</label>
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
                            <label class="form-label">Spécialité</label>
                            <input type="text" name="specialite" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Logo</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date de partenariat</label>
                            <input type="date" name="date_partenariat" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
