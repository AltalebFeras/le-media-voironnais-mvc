# Le Média Voironnais - Documentation Complète

## 📋 Table des Matières

1. [Introduction](#introduction)
2. [Prérequis](#prérequis)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [Architecture](#architecture)
6. [Routes](#routes)
7. [Base de Données](#base-de-données)
8. [Contrôleurs](#contrôleurs)
9. [Vues](#vues)
10. [Assets (CSS/JavaScript)](#assets)
11. [Services](#services)
12. [Sécurité](#sécurité)
13. [API](#api)
14. [Notifications](#notifications)
15. [Interactions Événements](#interactions-événements)

---

## 🎯 Introduction

**Le Média Voironnais** est une plateforme web complète permettant de gérer et promouvoir les événements, entreprises et associations de la région de Voiron.

### Fonctionnalités Principales

- 👥 **Gestion des utilisateurs** : Inscription, connexion, profils publics/privés
- 📅 **Événements** : Création, modification, inscription, commentaires, likes
- 🏢 **Entreprises** : Annuaire, demandes d'activation, réalisations
- 🏛️ **Associations** : Gestion complète avec membres
- 🔔 **Notifications** : Système en temps réel avec polling
- 💬 **Commentaires** : Système hiérarchique avec réponses
- ⭐ **Favoris** : Sauvegarde d'événements favoris
- 👫 **Amis** : Système d'amitié entre utilisateurs
- 🔍 **Recherche** : Recherche globale (utilisateurs, événements, entreprises, associations)

---

## ⚙️ Prérequis

### Logiciels Requis

- **PHP** >= 8.0
- **MySQL** >= 5.7 ou **MariaDB** >= 10.3
- **Apache** ou **Nginx**
- **Composer** (gestionnaire de dépendances PHP)
- **npm** ou **yarn** (pour les dépendances JavaScript, optionnel)

### Extensions PHP Nécessaires

```ini
extension=pdo
extension=pdo_mysql
extension=mbstring
extension=openssl
extension=curl
extension=gd
extension=fileinfo
```

---

## 🚀 Installation

### Étape 1 : Cloner le Projet

```bash
git clone https://github.com/votre-repo/le-media-voironnais.git
cd le-media-voironnais
```

### Étape 2 : Installation des Dépendances

```bash
# Dépendances PHP
composer install

# Dépendances JavaScript (si nécessaire)
npm install
```

### Étape 3 : Configuration de la Base de Données

1. Créez une base de données MySQL :

```sql
CREATE DATABASE le_media_voironnais CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Importez le schéma de base :

```bash
mysql -u root -p le_media_voironnais < src/Migrations/empty_database.sql
```

3. Importez les villes de France :

```bash
mysql -u root -p le_media_voironnais < src/Migrations/villes_france.sql
```

4. (Optionnel) Importez les données de test :

```bash
mysql -u root -p le_media_voironnais < src/Migrations/data.sql
```

### Étape 4 : Configuration de l'Application

1. Copiez le fichier de configuration exemple :

```bash
cp config_example.php config.php
```

2. Modifiez `config.php` avec vos paramètres :

```php
// Base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'le_media_voironnais');
define('DB_USER', 'root');
define('DB_PASS', 'votre_mot_de_passe');

// URLs
define('BASE_URL', 'http://localhost');
define('HOME_URL', '/le-media-voironnais/');

// Email
define('ADMIN_EMAIL', 'admin@example.com');
define('NO_REPLY_EMAIL', 'noreply@example.com');

// Sécurité
define('SECRET_KEY', 'votre_clé_secrète_google_recaptcha');
define('SITE_KEY', 'votre_site_key_google_recaptcha');
```

### Étape 5 : Configuration du Serveur Web

#### Apache (.htaccess)

Le fichier `.htaccess` est déjà configuré :

```apache
RewriteEngine on
RewriteCond %{REQUEST_URI} !\.(css|js|jpg|jpeg|png|svg|webp|gif|ico|woff2|ttf)$
RewriteRule (.*) index.php
```

#### Nginx

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~* \.(css|js|jpg|jpeg|png|svg|webp|gif|ico|woff2|ttf)$ {
    expires 1y;
    access_log off;
}
```

---

## 🏗️ Architecture

### Structure du Projet

```
le-media-voironnais/
├── config.php                  # Configuration principale
├── public/                     # Racine web publique
│   ├── index.php              # Point d'entrée
│   ├── assets/                # Ressources statiques
│   │   ├── css/              # Feuilles de style
│   │   ├── javascript/       # Scripts JavaScript
│   │   ├── images/           # Images
│   │   └── favicon/          # Favicons
│   └── .htaccess             # Configuration Apache
├── src/                       # Code source
│   ├── Abstracts/            # Classes abstraites
│   ├── Controllers/          # Contrôleurs
│   ├── Models/               # Modèles de données
│   ├── Repositories/         # Repositories (accès données)
│   ├── Services/             # Services (router, mail, etc.)
│   ├── Views/                # Vues (templates PHP)
│   ├── Migrations/           # Scripts SQL
│   └── init.php              # Initialisation
├── composer.json             # Dépendances PHP
└── package.json              # Dépendances JavaScript
```

### Pattern MVC Utilisé

```
Requête HTTP → Router → Controller → Repository → Model
                  ↓          ↓
              Service    Service
                  ↓          ↓
               View ← Controller
```

---

## 🛣️ Routes

### Configuration du Router

Le routeur est configuré dans `src/Services/ConfigRouter.php`.

### Routes Principales

#### Authentification

```php
'connexion'              => ['controller' => 'UserController', 'action' => 'displayLoginForm']
'deconnexion'            => ['controller' => 'UserController', 'action' => 'logout']
'inscription'            => ['controller' => 'UserController', 'action' => 'displayRegisterForm']
'traitement-inscription' => ['controller' => 'UserController', 'action' => 'processRegisterForm']
```

#### Utilisateurs

```php
'dashboard'              => ['controller' => 'UserController', 'action' => 'displayDashboard']
'mon_compte'             => ['controller' => 'UserController', 'action' => 'displayMonCompte']
'mes_preferences'        => ['controller' => 'UserController', 'action' => 'displayMesPreferences']
'mes_favoris'            => ['controller' => 'EvenementController', 'action' => 'getAllMyFavouriteEvents']
'mes_amis'               => ['controller' => 'FriendController', 'action' => 'displayFriendsList']
'notifications'          => ['controller' => 'NotificationController', 'action' => 'displayNotifications']
```

#### Événements

```php
'evenements'                => ['controller' => 'EvenementController', 'action' => 'listEvents']
'mes_evenements'            => ['controller' => 'EvenementController', 'action' => 'mesEvenements']
'evenement/ajouter'         => ['controller' => 'EvenementController', 'action' => 'showAddEventForm']
'evenement/modifier'        => ['controller' => 'EvenementController', 'action' => 'showEditEventForm']
'evenement/supprimer'       => ['controller' => 'EvenementController', 'action' => 'deleteEvent']
'evenement/like'            => ['controller' => 'EvenementController', 'action' => 'likeEvent']
'evenement/favourite'       => ['controller' => 'EvenementController', 'action' => 'favouriteEvent']
'evenement/comment'         => ['controller' => 'EvenementController', 'action' => 'addEventComment']
```

#### Entreprises

```php
'entreprises'               => ['controller' => 'EntrepriseController', 'action' => 'listPublicEntreprises']
'mes_entreprises'           => ['controller' => 'EntrepriseController', 'action' => 'mesEntreprises']
'entreprise/ajouter'        => ['controller' => 'EntrepriseController', 'action' => 'showAddForm']
'entreprise/modifier'       => ['controller' => 'EntrepriseController', 'action' => 'showEditForm']
'entreprise/supprimer'      => ['controller' => 'EntrepriseController', 'action' => 'deleteEntreprise']
```

#### Associations

```php
'associations'              => ['controller' => 'AssociationController', 'action' => 'listPublicAssociations']
'mes_associations'          => ['controller' => 'AssociationController', 'action' => 'mesAssociations']
'association/ajouter'       => ['controller' => 'AssociationController', 'action' => 'showAddForm']
'association/modifier'      => ['controller' => 'AssociationController', 'action' => 'showEditForm']
```

#### Administration

```php
'admin/dashboard'           => ['controller' => 'AdminController', 'action' => 'displayAdminDashboard']
'admin/utilisateurs'        => ['controller' => 'AdminController', 'action' => 'displayAllUsers']
'admin/contacts'            => ['controller' => 'ContactController', 'action' => 'displayAllContacts']
```

### Routes Dynamiques

Les routes dynamiques utilisent des segments composés :

```php
// Événement par slug : /evenements/{ville}/{categorie}/{slug}
'evenements' => [
    'controller' => 'EvenementController',
    'action' => 'viewEventBySlug',
    'composed' => true
]

// Profil utilisateur : /profil/{slug}
'profil' => [
    'controller' => 'UserController',
    'action' => 'displayPublicProfile',
    'composed' => true
]
```

---

## 🗄️ Base de Données

### Tables Principales

#### Users (utilisateurs)

```sql
CREATE TABLE users (
    idUser INT PRIMARY KEY AUTO_INCREMENT,
    uiid VARCHAR(16) UNIQUE NOT NULL,
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    bio TEXT,
    slug VARCHAR(150) UNIQUE,
    avatarPath VARCHAR(255),
    bannerPath VARCHAR(255),
    isOnline BOOLEAN DEFAULT FALSE,
    isBanned BOOLEAN DEFAULT FALSE,
    role ENUM('user', 'admin') DEFAULT 'user',
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME ON UPDATE CURRENT_TIMESTAMP
);
```

#### Evenements

```sql
CREATE TABLE evenements (
    idEvenement INT PRIMARY KEY AUTO_INCREMENT,
    uiid VARCHAR(16) UNIQUE NOT NULL,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(300) UNIQUE NOT NULL,
    description TEXT,
    shortDescription VARCHAR(500),
    startDate DATETIME NOT NULL,
    endDate DATETIME,
    registrationDeadline DATETIME,
    maxParticipants INT,
    currentParticipants INT DEFAULT 0,
    address VARCHAR(255),
    price DECIMAL(10,2),
    currency VARCHAR(3) DEFAULT 'EUR',
    bannerPath VARCHAR(255),
    isPublic BOOLEAN DEFAULT TRUE,
    requiresApproval BOOLEAN DEFAULT FALSE,
    isDeleted BOOLEAN DEFAULT FALSE,
    idUser INT NOT NULL,
    idVille INT NOT NULL,
    idEventCategory INT NOT NULL,
    idAssociation INT,
    idEntreprise INT,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUser) REFERENCES users(idUser),
    FOREIGN KEY (idVille) REFERENCES villes_france(idVille)
);
```

#### Entreprises

```sql
CREATE TABLE entreprises (
    idEntreprise INT PRIMARY KEY AUTO_INCREMENT,
    uiid VARCHAR(16) UNIQUE NOT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    description TEXT,
    address VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(100),
    website VARCHAR(255),
    siret VARCHAR(14),
    logoPath VARCHAR(255),
    bannerPath VARCHAR(255),
    isActive BOOLEAN DEFAULT FALSE,
    hasRequestForActivation BOOLEAN DEFAULT FALSE,
    requestDate DATETIME,
    isDeleted BOOLEAN DEFAULT FALSE,
    idUser INT NOT NULL,
    idVille INT NOT NULL,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

#### Notifications

```sql
CREATE TABLE notifications (
    idNotification INT PRIMARY KEY AUTO_INCREMENT,
    idUser INT NOT NULL,
    idEvenement INT,
    type VARCHAR(50),
    title VARCHAR(200),
    message TEXT,
    url VARCHAR(255),
    priority BOOLEAN DEFAULT FALSE,
    isRead BOOLEAN DEFAULT FALSE,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUser) REFERENCES users(idUser)
);
```

### Relations Clés

- **users** ← **evenements** : Un utilisateur peut créer plusieurs événements
- **users** ← **entreprises** : Un utilisateur peut gérer plusieurs entreprises
- **users** ← **associations** : Un utilisateur peut créer plusieurs associations
- **evenements** ← **event_participants** : Gestion des inscriptions
- **users** ← **friends** : Relations d'amitié (auto-référence)
- **evenements** ← **event_comments** : Commentaires hiérarchiques

---

## 🎮 Contrôleurs

### AbstractController

Classe de base pour tous les contrôleurs.

**Méthodes principales :**

```php
// Rendu de vues
render($view, array $data = []): void

// Redirection
redirect($route, array $query = []): void

// Gestion des erreurs
returnAllErrors($errors, $route, array $query = []): void

// CSRF
generateCsrfToken(): string
validateCsrfToken($route): bool

// Notifications
sendNotification(int $idUser, string $type, string $title, ...): bool
sendNotificationForEvent(int $idUser, int $idEvenement, ...): bool
```

### UserController

Gestion des utilisateurs.

**Actions principales :**

- `displayLoginForm()` : Affiche le formulaire de connexion
- `processLoginForm()` : Traite la connexion
- `displayRegisterForm()` : Affiche le formulaire d'inscription
- `processRegisterForm()` : Traite l'inscription
- `displayDashboard()` : Tableau de bord utilisateur
- `displayMonCompte()` : Page de gestion du compte
- `displayPublicProfile()` : Profil public d'un utilisateur
- `updateProfile()` : Mise à jour du profil
- `updateAvatar()` : Mise à jour de l'avatar
- `updateBanner()` : Mise à jour de la bannière

### EvenementController

Gestion complète des événements.

**Actions principales :**

- `mesEvenements()` : Liste des événements de l'utilisateur
- `listEvents()` : Liste publique des événements
- `showAddEventForm()` : Formulaire d'ajout
- `addEvent()` : Création d'événement
- `showEditEventForm()` : Formulaire de modification
- `updateEvent()` : Mise à jour d'événement
- `deleteEvent()` : Suppression
- `viewEventBySlug()` : Vue détaillée publique
- `inscriptionEvent()` : Inscription à un événement
- `acceptParticipant()` : Accepter un participant
- `refuseParticipant()` : Refuser un participant
- `likeEvent()` : Liker un événement
- `favouriteEvent()` : Mettre en favori
- `addEventComment()` : Ajouter un commentaire
- `replyEventComment()` : Répondre à un commentaire
- `deleteEventComment()` : Supprimer un commentaire
- `getEventInteractions()` : Récupérer les interactions (API)

### EntrepriseController

Gestion des entreprises.

**Actions principales :**

- `mesEntreprises()` : Liste des entreprises de l'utilisateur
- `listPublicEntreprises()` : Liste publique
- `showAddForm()` : Formulaire d'ajout
- `addEntreprise()` : Création
- `showEditForm()` : Formulaire de modification
- `updateEntreprise()` : Mise à jour
- `deleteEntreprise()` : Suppression
- `demanderActivation()` : Demande d'activation (avec Kbis)
- `updateLogo()` : Mise à jour du logo
- `updateBanner()` : Mise à jour de la bannière

### AssociationController

Gestion des associations.

**Actions principales :**

- `mesAssociations()` : Liste des associations de l'utilisateur
- `listPublicAssociations()` : Liste publique
- `displayAssociationDetails()` : Détails d'une association
- `showAddForm()` : Formulaire d'ajout
- `addAssociation()` : Création
- `showEditForm()` : Formulaire de modification
- `updateAssociation()` : Mise à jour
- `deleteAssociation()` : Suppression
- `updateLogo()` : Mise à jour du logo
- `updateBanner()` : Mise à jour de la bannière

### FriendController

Gestion du système d'amis.

**Actions principales :**

- `displayFriendsList()` : Liste des amis
- `sendFriendRequest()` : Envoyer une demande
- `acceptFriendRequest()` : Accepter une demande
- `refuseFriendRequest()` : Refuser une demande
- `removeFriend()` : Supprimer un ami
- `blockFriend()` : Bloquer un utilisateur
- `searchUsers()` : Rechercher des utilisateurs (AJAX)

### NotificationController

Gestion des notifications.

**Actions principales :**

- `displayNotifications()` : Page des notifications
- `getNotificationCount()` : Compteur (API)
- `getNotificationsList()` : Liste paginée (API)
- `markAsRead()` : Marquer comme lu
- `markAllAsRead()` : Tout marquer comme lu

### AdminController

Administration.

**Actions principales :**

- `displayAdminDashboard()` : Tableau de bord admin
- `displayAllUsers()` : Liste des utilisateurs
- `displayUserById()` : Détails d'un utilisateur
- `blockUser()` : Bannir un utilisateur
- `unblockUser()` : Débannir
- `sendEmailToUser()` : Envoyer un email
- `displayAllEntreprisesActivationRequests()` : Demandes d'activation
- `acceptEntrepriseActivationRequest()` : Accepter une demande
- `refuseEntrepriseActivationRequest()` : Refuser une demande

### ContactController

Gestion des messages de contact.

**Actions principales :**

- `displayContactForm()` : Formulaire de contact
- `submitContactForm()` : Traitement du formulaire
- `displayAllContacts()` : Liste des contacts (admin)
- `markContactAsRead()` : Marquer comme lu
- `replyToContact()` : Répondre à un contact
- `archiveContact()` : Archiver
- `deleteContact()` : Supprimer

---

## 👁️ Vues

### Structure des Vues

```
src/Views/
├── includes/              # Éléments réutilisables
│   ├── header.php        # En-tête
│   ├── footer.php        # Pied de page
│   ├── navbar.php        # Navigation utilisateur
│   ├── navbar_admin.php  # Navigation admin
│   ├── messages.php      # Messages flash
│   └── pagination.php    # Pagination
├── user/                  # Vues utilisateur
│   ├── dashboard.php
│   ├── mon_compte.php
│   ├── mes_preferences.php
│   ├── mes_favoris.php
│   ├── mes_amis.php
│   ├── profil_utilisateur.php
│   └── notifications.php
├── evenement/             # Vues événements
│   ├── mes_evenements.php
│   ├── evenements_liste.php
│   ├── ajouter_evenement.php
│   ├── modifier_evenement.php
│   ├── voir_evenement.php
│   └── evenement_publique_detail.php
├── entreprise/            # Vues entreprises
├── association/           # Vues associations
├── admin/                 # Vues admin
└── contact/               # Vues contact
```

### Includes Communs

#### header.php

```php
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Le Média Voironnais' ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= BASE_URL . HOME_URL ?>assets/css/root.css">
    <link rel="stylesheet" href="<?= BASE_URL . HOME_URL ?>assets/css/style.css">
    
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const BASE_URL = '<?= BASE_URL ?>';
        const HOME_URL = '<?= HOME_URL ?>';
    </script>
</head>
<body>
```

#### navbar.php

Navigation dynamique selon l'état de connexion :

```php
<nav class="navbar">
    <?php if (isset($_SESSION['connected']) && $_SESSION['connected']): ?>
        <!-- Menu connecté -->
        <a href="<?= HOME_URL ?>dashboard">Tableau de bord</a>
        <a href="<?= HOME_URL ?>mes_evenements">Mes événements</a>
        <a href="<?= HOME_URL ?>notifications">Notifications</a>
        <a href="<?= HOME_URL ?>deconnexion">Déconnexion</a>
    <?php else: ?>
        <!-- Menu non connecté -->
        <a href="<?= HOME_URL ?>connexion">Connexion</a>
        <a href="<?= HOME_URL ?>inscription">Inscription</a>
    <?php endif; ?>
</nav>
```

#### messages.php

Affichage des messages flash :

```php
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['errors'])): ?>
    <ul class="error-list">
        <?php foreach ($_SESSION['errors'] as $error): ?>
            <li><?= $error ?></li>
        <?php endforeach; ?>
    </ul>
    <?php unset($_SESSION['errors']); ?>
<?php endif; ?>
```

---

## 🎨 Assets (CSS/JavaScript)

### CSS

#### root.css

Variables CSS globales :

```css
:root {
    --color-primary: #3a7ca5;
    --color-secondary: #0E2A34;
    --color-accent: #6ed3cf;
    --font-family-sans: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    --spacing-lg: 1rem;
    --radius-md: 8px;
    --shadow-md: 0 6px 24px rgba(58, 124, 165, 0.10);
}
```

### JavaScript

#### script.js

Fonctionnalités principales :

```javascript
// Loader sur soumission de formulaire
$("form").on("submit", function() {
    // Afficher overlay de chargement
});

// Toggle password visibility
$('input[type="password"]').each(function() {
    // Ajouter icône œil
});

// Burger menu
$("#burger-menu").on("click", function() {
    // Toggle menu mobile
});

// Notifications polling
setInterval(refreshCount, 60000);
```

#### villes.js

Recherche de villes par code postal :

```javascript
$("#codePostal").on("input", function() {
    const codePostal = $(this).val().trim();
    if (codePostal.length === 5) {
        fetchVilles(codePostal);
    }
});
```

#### event-interactions.js

Gestion des interactions sur les événements :

```javascript
const EventInteractions = (function() {
    async function likeEvent() { /* ... */ }
    async function favouriteEvent() { /* ... */ }
    async function addComment() { /* ... */ }
    async function replyComment() { /* ... */ }
    // ...
})();
```

#### mes_amis.js

Gestion du système d'amis :

```javascript
function openSearchModal() { /* ... */ }
function performUserSearch(query) { /* ... */ }
function openFriendOptionsModal() { /* ... */ }
```

---

## 🛠️ Services

### Router (router.php)

```php
class Router {
    public function route(string $route): void {
        // Parse la route
        // Instancie le contrôleur
        // Appelle l'action
    }
}
```

### Mail (Mail.php)

```php
class Mail {
    public function sendEmail(
        string $from,
        string $fromName,
        string $to,
        string $toName,
        string $subject,
        string $body
    ): bool {
        // Configuration PHPMailer
        // Envoi de l'email
    }
}
```

### Helper (Helper.php)

```php
class Helper {
    public function generateSlug(...$parts): string { /* ... */ }
    public function generateUiid(): string { /* ... */ }
    public function handleImageUpload($fieldName, $folder): string { /* ... */ }
    public function handleDeleteImage($path): bool { /* ... */ }
}
```

### Database (Database.php)

```php
class Database {
    private static $instance = null;
    
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            self::$instance = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS
            );
        }
        return self::$instance;
    }
}
```

---

## 🔒 Sécurité

### Protection CSRF

```php
// Génération du token
$csrfToken = $this->generateCsrfToken();

// Dans le formulaire
<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

// Validation
$this->validateCsrfToken('route_name');
```

### Validation des Données

```php
// Nettoyage des entrées
$email = filter_var($email, FILTER_SANITIZE_EMAIL);
$title = htmlspecialchars(trim($_POST['title']));

// Validation email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Email invalide";
}
```

### Protection XSS

```php
// Dans les vues, toujours échapper les données
<?= htmlspecialchars($user->getFirstName()) ?>
```

### Protection SQL Injection

```php
// Utilisation de requêtes préparées
$stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
```

### Upload de Fichiers

```php
// Validation du type MIME
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);

// Taille maximale
if ($file['size'] > 5 * 1024 * 1024) {
    throw new Exception("Fichier trop volumineux");
}

// Extensions autorisées
$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
```

---

## 📡 API

### Endpoints JSON

#### GET /notifications/count

Retourne le nombre de notifications non lues.

**Réponse :**
```json
{
    "success": true,
    "count": 5
}
```

#### GET /notifications/list

Liste paginée des notifications.

**Paramètres :**
- `page` (int) : Numéro de page
- `limit` (int) : Nombre par page

**Réponse :**
```json
{
    "success": true,
    "items": [...],
    "hasMore": true
}
```

#### POST /evenement/like

Liker/unliker un événement.

**Body :**
```json
{
    "eventUiid": "abc123..."
}
```

**Réponse :**
```json
{
    "success": true,
    "liked": true
}
```

#### POST /amis/rechercher

Rechercher des utilisateurs.

**Body (FormData) :**
```
query: "Jean"
```

**Réponse :**
```json
{
    "success": true,
    "users": [
        {
            "uiid": "...",
            "name": "Jean Dupont",
            "avatar": "...",
            "bio": "..."
        }
    ]
}
```

---

## 🔔 Notifications

### Système de Polling

```javascript
// Polling toutes les 60 secondes
setInterval(refreshCount, 60000);

async function refreshCount() {
    const res = await fetch('/notifications/count');
    const data = await res.json();
    updateBadge(data.count);
}
```

### Types de Notifications

- `activation` : Activation de compte entreprise
- `inscription` : Inscription à un événement
- `preinscription` : En attente d'approbation
- `invitation` : Demande d'ami
- `mise_a_jour` : Mise à jour d'événement
- `rappel` : Rappel d'événement
- `systeme` : Message système
- `alert` : Alerte importante
- `message` : Message général
- `mention` : Mention dans un commentaire

### Priorités

- `0` : Normale
- `1` : Haute priorité

---

## 💬 Interactions Événements

### Système de Commentaires

#### Structure Hiérarchique

```
Commentaire parent
├── Réponse 1
├── Réponse 2
└── Réponse 3
```

#### Actions Disponibles

- Liker un commentaire
- Répondre à un commentaire
- Supprimer son commentaire
- Signaler un commentaire

#### Chargement AJAX

```javascript
EventInteractions.init(eventUiid, isLoggedIn, currentUserUiid);
```

### Système de Likes

- Like/Unlike sur événement
- Like sur commentaire
- Compteur en temps réel

### Système de Favoris

- Ajouter/retirer des favoris
- Page "Mes favoris"
- Suppression depuis la page favoris

---

## 📊 Statistiques

### Métriques Disponibles

- Nombre total d'utilisateurs
- Événements actifs
- Entreprises actives
- Associations actives
- Inscriptions aux événements
- Taux de participation

---

## 🐛 Débogage

### Mode Debug

Dans `config.php` :

```php
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
```

### Logs

```php
error_log("Message de debug : " . print_r($data, true));
```

---

## 🚀 Déploiement en Production

### Checklist

1. [ ] Désactiver le mode debug
2. [ ] Changer les clés secrètes
3. [ ] Configurer HTTPS
4. [ ] Optimiser les images
5. [ ] Minifier CSS/JS
6. [ ] Configurer le cache
7. [ ] Sauvegardes automatiques
8. [ ] Monitoring des erreurs

### Configuration Apache

```apache
# Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css text/javascript
</IfModule>

# Cache navigateur
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
</IfModule>
```

---

## 📝 Licence

Ce projet est sous licence propriétaire. Tous droits réservés.

---

## 👥 Support

Pour toute question ou problème :

- Email : support@le-media-voironnais.fr
- Documentation complète : [lien vers documentation complète]

---

**Version :** 1.0.0  
**Dernière mise à jour :** Décembre 2024
