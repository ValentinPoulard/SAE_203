<?php
session_start();
require_once("../scripts/fonctions.php");

if (!isset($_SESSION['utilisateur'])) {
    header("Location: ../connexion.php");
    exit();
}

$annuaire = $_GET['annuaire'] ?? 'entreprise';
$allowed_annuaires = ['entreprise', 'partenaires', 'clients'];

if (!in_array($annuaire, $allowed_annuaires)) {
    $annuaire = 'entreprise';
}

parametres("Annuaires");
entete();
navigation("annuaires");
?>

<section class="container">
    <h2 class="mb-4">Annuaires</h2>
    
    <!-- Navigation entre annuaires -->
    <ul class="nav nav-tabs mb-4" id="annuaireTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?php echo $annuaire === 'entreprise' ? 'active' : ''; ?>" 
               href="?annuaire=entreprise" id="entreprise-tab">
                👥 Annuaire Entreprise
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $annuaire === 'partenaires' ? 'active' : ''; ?>" 
               href="?annuaire=partenaires" id="partenaires-tab">
                🤝 Annuaire Partenaires
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $annuaire === 'clients' ? 'active' : ''; ?>" 
               href="?annuaire=clients" id="clients-tab">
                🔒 Annuaire Clients
            </a>
        </li>
    </ul>
    
    <?php
    switch ($annuaire) {
        case 'entreprise':
            include('annuaire_entreprise.php');
            break;
        case 'partenaires':
            include('annuaire_partenaires.php');
            break;
        case 'clients':
            include('annuaire_clients.php');
            break;
    }
    ?>
</section>

<?php
pieddepage();
?>
