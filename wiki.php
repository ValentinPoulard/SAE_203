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
    <h2>Documentation de l'Intranet SecuriWatch</h2>
    
    <div class="accordion" id="accordionExample">
        
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#fonctions">
                    Fonctionnalités implémentées
                </button>
            </h2>
            <div id="fonctions" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <h5>Gestion des agents</h5>
                    <ul>
                        <li>Inscription avec identifiant unique, email professionnel, mot de passe, poste/service</li>
                        <li>Validation de domaine email (@securiwatch.fr uniquement)</li>
                        <li>Connexion sécurisée avec password_verify()</li>
                        <li>Gestion des sessions PHP</li>
                        <li>3 rôles : user (agent), modo (modérateur), admin (administrateur)</li>
                        <li>Activation de compte par administrateur</li>
                        <li>Profil agent modifiable</li>
                    </ul>
                    
                    <h5>Gestion des missions</h5>
                    <ul>
                        <li>Création de missions (date, site d'intervention, type, agents requis, détails)</li>
                        <li>Visualisation de toutes les missions</li>
                        <li>Recherche par critères (date, site, type, agents)</li>
                        <li>Modification/suppression (droits par rôle)</li>
                        <li>Assignation d'agents aux missions</li>
                        <li>Planning personnel des missions</li>
                    </ul>
                    
                    <h5>Administration</h5>
                    <ul>
                        <li>Gestion des agents (modification rôle, activation/désactivation, suppression)</li>
                        <li>Recherche d'agents</li>
                        <li>Accès restreint aux administrateurs</li>
                        <li>Les modérateurs peuvent gérer mais pas supprimer</li>
                    </ul>
                    
                    <h5>Sécurité</h5>
                    <ul>
                        <li>Accès anonyme bloqué - redirection vers connexion</li>
                        <li>Validation de domaine email obligatoire</li>
                        <li>Activation de compte requise avant connexion</li>
                        <li>Contrôle d'accès basé sur les rôles</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#comptes">
                    Comptes de test
                </button>
            </h2>
            <div id="comptes" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Administrateur</h6>
                            <p><strong>Login:</strong> admin<br>
                            <strong>Mot de passe:</strong> motdepasse<br>
                            <strong>Email:</strong> admin@securiwatch.fr</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Modérateur</h6>
                            <p><strong>Login:</strong> modo<br>
                            <strong>Mot de passe:</strong> motdepasse<br>
                            <strong>Email:</strong> modo@securiwatch.fr</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <h6>Agent</h6>
                            <p><strong>Login:</strong> user<br>
                            <strong>Mot de passe:</strong> motdepasse<br>
                            <strong>Email:</strong> user@securiwatch.fr</p>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3">
                        <strong>Note:</strong> Les mots de passe sont "motdepasse" ou "bonjour" pour les comptes.<br>
                        <strong>Important:</strong> Seuls les comptes avec email @securiwatch.fr peuvent être créés.
                    </div>
                </div>
            </div>
        </div>
        
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#php">
                    Fonctions PHP utilisées
                </button>
            </h2>
            <div id="php" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <h6>Gestion des mots de passe</h6>
                    <ul>
                        <li><code>password_hash()</code> - Hashage des mots de passe</li>
                        <li><code>password_verify()</code> - Vérification des mots de passe</li>
                    </ul>
                    
                    <h6>Gestion des sessions</h6>
                    <ul>
                        <li><code>session_start()</code> - Démarrage de session</li>
                        <li><code>$_SESSION</code> - Stockage des données utilisateur</li>
                        <li><code>session_destroy()</code> - Destruction de session</li>
                    </ul>
                    
                    <h6>Gestion des fichiers</h6>
                    <ul>
                        <li><code>file_exists()</code> - Vérification de l'existence des fichiers</li>
                        <li><code>file_get_contents()</code> - Lecture des fichiers JSON</li>
                        <li><code>file_put_contents()</code> - Écriture dans les fichiers JSON</li>
                        <li><code>json_decode()</code> - Décodage JSON</li>
                        <li><code>json_encode()</code> - Encodage JSON</li>
                    </ul>
                    
                    <h6>Fonctions personnalisées</h6>
                    <ul>
                        <li><code>parametres($titre)</code> - Génération du head HTML</li>
                        <li><code>entete()</code> - Génération du header</li>
                        <li><code>navigation($page_active)</code> - Génération de la barre de navigation</li>
                        <li><code>pieddepage()</code> - Génération du footer</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Ce qui fonctionne -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fonctionne">
                    Ce qui fonctionne ou pas
                </button>
            </h2>
            <div id="fonctionne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <h6>Authentification</h6>
                    <ul>
                        <li>✅ Inscription avec validation email</li>
                        <li>✅ Connexion</li>
                        <li>✅ Déconnexion</li>
                        <li>✅ Sessions persistantes</li>
                        <li>✅ Activation de compte par admin</li>
                        <li>✅ Blocage des comptes inactifs</li>
                    </ul>
                    
                    <h6>Gestion des missions</h6>
                    <ul>
                        <li>✅ Création de missions</li>
                        <li>✅ Affichage des missions</li>
                        <li>✅ Recherche</li>
                        <li>✅ Modification des missions</li>
                        <li>✅ Suppression des missions</li>
                        <li>✅ Assignation d'agents</li>
                    </ul>
                    
                    <h6>Administration</h6>
                    <ul>
                        <li>✅ Gestion des agents</li>
                        <li>✅ Modification des rôles</li>
                        <li>✅ Activation/désactivation des comptes</li>
                        <li>✅ Suppression d'agents (admin uniquement)</li>
                        <li>✅ Recherche d'agents</li>
                    </ul>
                    
                    <h6>Sécurité</h6>
                    <ul>
                        <li>✅ Blocage accès anonyme</li>
                        <li>✅ Validation domaine email</li>
                        <li>✅ Contrôle d'accès par rôle</li>
                        <li>✅ Protection mot de passe</li>
                    </ul>
                    
                    <h6>Interface</h6>
                    <ul>
                        <li>✅ Responsive</li>
                        <li>✅ Navigation</li>
                        <li>✅ Planning des missions</li>
                        <li>✅ Profil modifiable</li>
                        <li>❌ Recherche dans la navbar </li>
                        <li>❌ Photo de profil </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#limites">
                    Limites et subtilités
                </button>
            </h2>
            <div id="limites" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <h6>Améliorations de sécurité implémentées</h6>
                    <ul>
                        <li>✅ Validation stricte du domaine email</li>
                        <li>✅ Activation de compte requise</li>
                        <li>✅ Blocage de l'accès anonyme</li>
                        <li>✅ Contrôle d'accès basé sur les rôles</li>
                        <li>⚠️ HTTPS recommandé pour la production</li>
                        <li>⚠️ Rate limiting non implémenté</li>
                        <li>⚠️ Logs d'audit non implémentés</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#structure">
                    Structure des fichiers
                </button>
            </h2>
            <div id="structure" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <h6>Racine (/)</h6>
                    <ul>
                        <li><code>accueil.php</code> - Page d'accueil avec statistiques</li>
                        <li><code>connexion.php</code> - Formulaire de connexion agent</li>
                        <li><code>inscription.php</code> - Demande d'accès agent</li>
                        <li><code>deconnexion.php</code> - Déconnexion</li>
                        <li><code>profil.php</code> - Modification du profil agent</li>
                        <li><code>administration.php</code> - Gestion des agents</li>
                        <li><code>visualiser.php</code> - Visualisation des missions</li>
                        <li><code>proposer.php</code> - Création de missions</li>
                        <li><code>modifier.php</code> - Gestion des missions</li>
                        <li><code>rechercher.php</code> - Recherche de missions</li>
                        <li><code>calendar.php</code> - Planning personnel</li>
                        <li><code>wiki.php</code> - Documentation (cette page)</li>
                    </ul>
                    
                    <h6>/admin</h6>
                    <ul>
                        <li><code>view_utilisateurs.php</code> - Vue brute des agents</li>
                        <li><code>view_annonces.php</code> - Vue brute des missions</li>
                    </ul>
                    
                    <h6>/data</h6>
                    <ul>
                        <li><code>r209-tp_utilisateurs.json</code> - Base agents</li>
                        <li><code>r209-tp_annonces.json</code> - Base missions</li>
                    </ul>
                    
                    <h6>/scripts</h6>
                    <ul>
                        <li><code>fonctions.php</code> - Fonctions communes</li>
                    </ul>
                    
                    <h6>/styles</h6>
                    <ul>
                        <li><code>style.css</code> - Styles personnalisés</li>
                    </ul>
                    
                    <h6>/images</h6>
                    <ul>
                        <li><code>logo.png</code> - Logo SecuriWatch</li>
                    </ul>
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
