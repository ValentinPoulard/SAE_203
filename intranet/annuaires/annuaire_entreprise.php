<?php
$fichier_entreprise = 'data/annuaire_entreprise.json';
$entreprises = [];

if (file_exists($fichier_entreprise)) {
    $entreprises = json_decode(file_get_contents($fichier_entreprise), true);
}

// Récupération des services uniques pour les filtres
$services = [];
foreach ($entreprises as $e) {
    if (!in_array($e['service'], $services)) {
        $services[] = $e['service'];
    }
}

// Filtre par service
$service_filtre = $_GET['service'] ?? '';
if (!empty($service_filtre) && $service_filtre !== 'all') {
    $entreprises = array_filter($entreprises, function($e) use ($service_filtre) {
        return $e['service'] === $service_filtre;
    });
}

// Gestion CRUD pour administrateurs
$erreur = "";
$succes = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (in_array('admin', $_SESSION['groupes']) || in_array('managers', $_SESSION['groupes']))) {
    
    // Ajout
    if (isset($_POST['ajouter'])) {
        $nouveau_id = max(array_column($entreprises, 'id')) + 1;
        $nouvelle_entreprise = [
            "id" => $nouveau_id,
            "nom" => $_POST['nom'],
            "prenom" => $_POST['prenom'],
            "email" => $_POST['email'],
            "telephone" => $_POST['telephone'],
            "poste" => $_POST['poste'],
            "service" => $_POST['service'],
            "date_embauche" => $_POST['date_embauche']
        ];
        $entreprises[] = $nouvelle_entreprise;
        
        if (file_put_contents($fichier_entreprise, json_encode($entreprises, JSON_PRETTY_PRINT))) {
            $succes = "Employé ajouté avec succès !";
        } else {
            $erreur = "Erreur lors de l'ajout.";
        }
    }
    
    // Modification
    if (isset($_POST['modifier'])) {
        $id = (int)$_POST['id'];
        foreach ($entreprises as &$e) {
            if ($e['id'] === $id) {
                $e['nom'] = $_POST['nom'];
                $e['prenom'] = $_POST['prenom'];
                $e['email'] = $_POST['email'];
                $e['telephone'] = $_POST['telephone'];
                $e['poste'] = $_POST['poste'];
                $e['service'] = $_POST['service'];
                $e['date_embauche'] = $_POST['date_embauche'];
                break;
            }
        }
        
        if (file_put_contents($fichier_entreprise, json_encode($entreprises, JSON_PRETTY_PRINT))) {
            $succes = "Employé modifié avec succès !";
        } else {
            $erreur = "Erreur lors de la modification.";
        }
    }
    
    // Suppression
    if (isset($_POST['supprimer']) && in_array('admin', $_SESSION['groupes'])) {
        $id = (int)$_POST['id'];
        $entreprises = array_filter($entreprises, function($e) use ($id) {
            return $e['id'] !== $id;
        });
        $entreprises = array_values($entreprises);
        
        if (file_put_contents($fichier_entreprise, json_encode($entreprises, JSON_PRETTY_PRINT))) {
            $succes = "Employé supprimé avec succès !";
        } else {
            $erreur = "Erreur lors de la suppression.";
        }
    }
}
?>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">👥 Annuaire Entreprise (<?php echo count($entreprises); ?> employés)</h5>
        <?php if (in_array('admin', $_SESSION['groupes']) || in_array('managers', $_SESSION['groupes'])): ?>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAjouter">
                ➕ Ajouter un employé
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
        
        <!-- Filtre par service -->
        <div class="mb-3">
            <label class="form-label">Filtrer par service :</label>
            <div class="btn-group" role="group">
                <a href="?annuaire=entreprise&service=all" 
                   class="btn btn-outline-primary <?php echo $service_filtre === '' || $service_filtre === 'all' ? 'active' : ''; ?>">
                    Tous
                </a>
                <?php foreach ($services as $service): ?>
                    <a href="?annuaire=entreprise&service=<?php echo urlencode($service); ?>" 
                       class="btn btn-outline-primary <?php echo $service_filtre === $service ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($service); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Liste des employés -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Poste</th>
                        <th>Service</th>
                        <th>Date d'embauche</th>
                        <?php if (in_array('admin', $_SESSION['groupes']) || in_array('managers', $_SESSION['groupes'])): ?>
                            <th class="text-end">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entreprises as $e): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($e['nom']); ?></td>
                            <td><?php echo htmlspecialchars($e['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($e['email']); ?></td>
                            <td><?php echo htmlspecialchars($e['telephone']); ?></td>
                            <td><?php echo htmlspecialchars($e['poste']); ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo htmlspecialchars($e['service']); ?></span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($e['date_embauche'])); ?></td>
                            <?php if (in_array('admin', $_SESSION['groupes']) || in_array('managers', $_SESSION['groupes'])): ?>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalModifier<?php echo $e['id']; ?>">
                                        ✏️ Modifier
                                    </button>
                                    <?php if (in_array('admin', $_SESSION['groupes'])): ?>
                                        <form method="POST" class="d-inline" 
                                              onsubmit="return confirm('Supprimer cet employé ?')">
                                            <input type="hidden" name="id" value="<?php echo $e['id']; ?>">
                                            <input type="hidden" name="supprimer" value="1">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                🗑️ Supprimer
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                        
                        <!-- Modal de modification -->
                        <?php if (in_array('admin', $_SESSION['groupes']) || in_array('managers', $_SESSION['groupes'])): ?>
                            <div class="modal fade" id="modalModifier<?php echo $e['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Modifier l'employé</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?php echo $e['id']; ?>">
                                                <input type="hidden" name="modifier" value="1">
                                                <div class="mb-3">
                                                    <label class="form-label">Nom</label>
                                                    <input type="text" name="nom" class="form-control" 
                                                           value="<?php echo htmlspecialchars($e['nom']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Prénom</label>
                                                    <input type="text" name="prenom" class="form-control" 
                                                           value="<?php echo htmlspecialchars($e['prenom']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" name="email" class="form-control" 
                                                           value="<?php echo htmlspecialchars($e['email']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Téléphone</label>
                                                    <input type="text" name="telephone" class="form-control" 
                                                           value="<?php echo htmlspecialchars($e['telephone']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Poste</label>
                                                    <input type="text" name="poste" class="form-control" 
                                                           value="<?php echo htmlspecialchars($e['poste']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Service</label>
                                                    <select name="service" class="form-select" required>
                                                        <?php foreach ($services as $service): ?>
                                                            <option value="<?php echo $service; ?>" 
                                                                    <?php echo $e['service'] === $service ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($service); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Date d'embauche</label>
                                                    <input type="date" name="date_embauche" class="form-control" 
                                                           value="<?php echo $e['date_embauche']; ?>" required>
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
                    <h5 class="modal-title">Ajouter un employé</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="ajouter" value="1">
                        <div class="mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="form-control" required>
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
                            <label class="form-label">Poste</label>
                            <input type="text" name="poste" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Service</label>
                            <select name="service" class="form-select" required>
                                <?php foreach ($services as $service): ?>
                                    <option value="<?php echo $service; ?>">
                                        <?php echo htmlspecialchars($service); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date d'embauche</label>
                            <input type="date" name="date_embauche" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
