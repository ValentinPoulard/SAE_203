<?php
session_start();
include("scripts/fonctions.php");

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit();
}

parametres("Documentation");
entete();
navigation("wiki");
?>

<section class="container">
    <h2>Documentation de l'Intranet Vidéosurveillance Saint-Malo</h2>
    <p class="lead">Bienvenue sur la documentation de l'intranet de Vidéosurveillance Saint-Malo. Ce système permet la gestion des interventions, des agents et des ressources de l'entreprise.</p>
    
    <div class="accordion" id="accordionExample">
        
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#presentation">
                    Présentation du système
                </button>
            </h2>
            <div id="presentation" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <h5>Overview</h5>
                    <p>L'intranet Vidéosurveillance Saint-Malo est un système de gestion interne permettant aux agents de :</p>
                    <ul>
                        <li>Consulter et gérer les interventions de télésurveillance</li>
                        <li>Accéder aux annuaires (entreprise, partenaires, clients)</li>
                        <li>Gérer les fichiers et documents partagés</li>
                        <li>Visualiser leur planning personnel</li>
                        <li>Gérer leur profil utilisateur</li>
                    </ul>
                    
                    <h5>Accès</h5>
                    <p>L'accès à l'intranet est réservé au personnel autorisé. Chaque agent dispose d'un compte personnel sécurisé.</p>
                    <p><strong>Email de contact :</strong> contact@videosurveillance-saintmalo.fr</p>
                </div>
            </div>
        </div>
        
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fonctionnalites">
                    Fonctionnalités principales
                </button>
            </h2>
            <div id="fonctionnalites" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <h5>Gestion des interventions</h5>
                    <ul>
                        <li>Création de nouvelles interventions avec détails (date, site, type, agents requis)</li>
                        <li>Consultation de toutes les interventions en cours</li>
                        <li>Recherche d'interventions par critères</li>
                        <li>Modification des interventions (selon les droits)</li>
                        <li>Planning personnel des interventions assignées</li>
                    </ul>
                    
                    <h5>Gestion des fichiers</h5>
                    <ul>
                        <li>Espace public : documents accessibles à tous</li>
                        <li>Espace privé : documents par groupe/service</li>
                        <li>Espace personnel : dossier individuel de chaque agent</li>
                        <li>Téléchargement sécurisé des fichiers</li>
                    </ul>
                    
                    <h5>Annuaires</h5>
                    <ul>
                        <li>Annuaire interne de l'entreprise (22 employés)</li>
                        <li>Annuaire des partenaires (12 partenaires)</li>
                        <li>Annuaire clients confidentiel (42 clients)</li>
                        <li>Génération de fiches clients</li>
                    </ul>
                    
                    <h5>Administration</h5>
                    <ul>
                        <li>Gestion des comptes utilisateurs (réservé aux admins)</li>
                        <li>Modification des groupes et des droits</li>
                        <li>Activation/désactivation des comptes</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#roles">
                    Groupes et permissions
                </button>
            </h2>
            <div id="roles" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <h5>Admin</h5>
                    <p>Accès complet à toutes les fonctionnalités du système.</p>
                    <ul>
                        <li>Gestion complète des utilisateurs et des missions</li>
                        <li>Activation/désactivation des comptes</li>
                        <li>Accès à tous les espaces du gestionnaire de fichiers</li>
                        <li>Accès à l'annuaire clients confidentiel</li>
                        <li>Modification des groupes des utilisateurs</li>
                    </ul>
                    
                    <h5>Managers</h5>
                    <p>Gestion des utilisateurs et missions, mais pas de suppression d'utilisateurs.</p>
                    <ul>
                        <li>Création et modification de missions</li>
                        <li>Gestion des utilisateurs (sans suppression)</li>
                        <li>Accès aux espaces de fichiers selon le groupe</li>
                        <li>Modification des annuaires (entreprise et partenaires)</li>
                    </ul>
                    
                    <h5>Direction</h5>
                    <p>Accès étendu pour la direction.</p>
                    <ul>
                        <li>Accès aux annuaires</li>
                        <li>Lecture des missions</li>
                        <li>Accès aux fichiers selon les permissions</li>
                    </ul>
                    
                    <h5>Salariés</h5>
                    <p>Accès basique aux fonctionnalités de l'intranet.</p>
                    <ul>
                        <li>Création de missions</li>
                        <li>Modification de son propre profil</li>
                        <li>Upload dans son espace personnel</li>
                        <li>Lecture des annuaires</li>
                    </ul>
                    
                    <h5>Perso</h5>
                    <p>Groupe personnalisé pour des besoins spécifiques.</p>
                    <ul>
                        <li>Permissions configurables selon les besoins</li>
                        <li>Utilisation flexible</li>
                    </ul>
                    
                    <p class="text-muted"><strong>Note :</strong> Un utilisateur peut appartenir à plusieurs groupes simultanément.</p>
                </div>
            </div>
        </div>
        
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#securite">
                Sécurité
                </button>
            </h2>
            <div id="securite" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <h5>Mesures de sécurité</h5>
                    <ul>
                        <li>Connexion sécurisée avec hashage des mots de passe</li>
                        <li>Validation obligatoire du domaine email professionnel (@alarme-videosurveillance-35.fr)</li>
                        <li>Activation de compte requise avant tout accès</li>
                        <li>Contrôle d'accès basé sur les groupes</li>
                        <li>Blocage de l'accès anonyme</li>
                    </ul>
                    
                    <h5>Bonnes pratiques</h5>
                    <ul>
                        <li>Utilisez un mot de passe fort et unique</li>
                        <li>Ne partagez jamais vos identifiants</li>
                        <li>Déconnectez-vous après chaque session</li>
                        <li>Signalez toute activité suspecte à l'administrateur</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#support">
                Support et contact
                </button>
            </h2>
            <div id="support" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <h5>Assistance technique</h5>
                    <p>Pour toute question ou problème technique :</p>
                    <ul>
                        <li><strong>Email :</strong> contact@videosurveillance-saintmalo.fr</li>
                        <li><strong>Service :</strong> Service Télésurveillance</li>
                    </ul>
                    
                    <h5>Demande de compte</h5>
                    <p>Pour demander un accès à l'intranet, contactez votre supérieur hiérarchique ou l'administrateur système.</p>
                    
                    <h5>Site vitrine</h5>
                    <p>Le site public de l'entreprise est accessible via le bouton "Site Vitrine" dans la barre de navigation.</p>
                </div>
            </div>
        </div>
        
    </div>
    
    <div class="mt-4 text-center">
        <a href="accueil.php" class="btn btn-primary">Retour à l'accueil</a>
    </div>
</section>

<?php
pieddepage();
?>
