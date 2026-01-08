# Le Média Voironnais

Une plateforme collaborative pour les événements, associations et entreprises de la région Voironnaise.

## 📋 Table des matières

- [Vue d'ensemble](#-vue-densemble)
- [Fonctionnalités principales](#-fonctionnalités-principales)
- [Architecture](#-architecture)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Structure du projet](#-structure-du-projet)
- [Fonctionnalités détaillées](#-fonctionnalités-détaillées)
- [API et Routes](#-api-et-routes)
- [Sécurité](#-sécurité)
- [Technologies utilisées](#-technologies-utilisées)
- [Contribution](#-contribution)

## 🎯 Vue d'ensemble

Le Média Voironnais est une plateforme web moderne permettant aux citoyens, associations et entreprises de la région de Voiron de partager des événements, de collaborer et de rester connectés.

### Objectifs principaux

- Centraliser les événements locaux
- Faciliter la communication entre associations et citoyens
- Promouvoir les entreprises locales
- Créer une communauté engagée

## ✨ Fonctionnalités principales

### Pour les utilisateurs

- **Gestion de profil** : Création et personnalisation de profil avec avatar et bannière
- **Système d'amis** : Ajout, recherche et gestion d'amis
- **Événements** : Création, inscription et participation aux événements
- **Interactions sociales** : Likes, commentaires, favoris sur les événements
- **Notifications en temps réel** : Système de notifications avec badge et popup
- **Préférences** : Configuration des villes et catégories d'intérêt

### Pour les associations

- **Gestion d'associations** : Création et administration d'associations
- **Événements associatifs** : Organisation d'événements au nom de l'association
- **Visibilité publique** : Page publique avec logo et bannière personnalisés
- **Gestion des membres** : Administration des membres de l'association

### Pour les entreprises

- **Profil entreprise** : Création de profil avec informations SIRET
- **Demande d'activation** : Processus de validation par l'administrateur
- **Événements professionnels** : Organisation d'événements au nom de l'entreprise
- **Réalisations** : Showcase de projets et réalisations
- **Annuaire professionnel** : Visibilité dans l'annuaire des entreprises

### Administration

- **Gestion des utilisateurs** : Vue d'ensemble, bannissement, envoi d'emails
- **Validation d'entreprises** : Approbation/refus des demandes d'activation
- **Gestion des contacts** : Traitement des messages de contact
- **Statistiques** : Dashboard avec métriques clés

## 🏗 Architecture

### Architecture MVC

```
┌─────────────┐
│   Router    │ ← Point d'entrée (public/index.php)
└──────┬──────┘
       │
       ▼
┌─────────────┐
│ Controllers │ ← Logique métier
└──────┬──────┘
       │
       ▼
┌─────────────┐
│   Models    │ ← Entités métier
└──────┬──────┘
       │
       ▼
┌─────────────┐
│Repositories │ ← Accès aux données
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  Database   │ ← MySQL
└─────────────┘
```

### Composants principaux

#### Services

- **Router** : Gestion des routes et dispatching
- **Database** : Connexion PDO et requêtes
- **Mail** : Envoi d'emails avec PHPMailer
- **Helper** : Fonctions utilitaires (slug, UIID, upload)
- **Encrypt_decrypt** : Chiffrement des données sensibles
- **BadWordsFilter** : Filtrage de contenu inapproprié
- **Hydration** : Mapping objet-relationnel

#### Controllers

- **UserController** : Gestion des utilisateurs
- **EvenementController** : CRUD événements + interactions
- **AssociationController** : Gestion associations
- **EntrepriseController** : Gestion entreprises
- **FriendController** : Système d'amitié
- **NotificationController** : Notifications
- **ContactController** : Formulaire de contact
- **AdminController** : Administration
- **HomeController** : Page d'accueil

## 📦 Installation

### Prérequis

- PHP 8.1 ou supérieur
- MySQL 5.7 ou supérieur
- Composer
- Node.js et npm (pour les assets)
- Serveur web (Apache/Nginx)

### Étapes d'installation

1. **Cloner le repository**

```bash
git clone <https://github.com/AltalebFeras/le-media-voironnais-mvc.git>
cd le-media-voironnais-mvc
```

1. **Installer les dépendances PHP**

```bash
composer install
```

1. **Installer les dépendances JavaScript**

```bash
npm install
```

1. **Configuration de la base de données**

```bash
# Créer la base de données
mysql -u root -p
CREATE DATABASE le_media_voironnais CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Importer le schéma
mysql -u root -p le_media_voironnais < src/Migrations/empty_database.sql

# (Optionnel) Importer les données de test
mysql -u root -p le_media_voironnais < src/Migrations/data.sql
```

1. **Configuration de l'application**

```bash
# Copier le fichier de configuration exemple
cp config_example.php config.php

# Éditer config.php avec vos paramètres
```

1. **Configurer les permissions**

```bash
# Dossiers d'upload
chmod -R 755 public/assets/images/uploads
chown -R www-data:www-data public/assets/images/uploads
```

## ⚙️ Configuration

### Fichier config.php

```php
<?php
// Base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'le_media_voironnais');
define('DB_USER', 'root');
define('DB_PASS', '');

// URLs
define('BASE_URL', 'http://localhost');
define('HOME_URL', '/');

// Email
define('ADMIN_EMAIL', 'admin@example.com');
define('ADMIN_SENDER_NAME', 'Le Média Voironnais');
define('NO_REPLY_EMAIL', 'noreply@example.com');

// SMTP (PHPMailer)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');

// reCAPTCHA
define('SITE_KEY', 'your-site-key');
define('SECRET_KEY', 'your-secret-key');

// Sécurité
define('ENCRYPTION_KEY', 'your-32-char-encryption-key');

// Uploads
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
```

## 📁 Structure du projet

```
le-media-voironnais/
├── public/                          # Fichiers publics
│   ├── assets/
│   │   ├── css/                     # Feuilles de style
│   │   │   ├── root.css             # Variables CSS
│   │   │   ├── style.css            # Styles principaux
│   │   │   └── users/               # Styles utilisateurs
│   │   ├── javascript/              # Scripts JavaScript
│   │   │   ├── script.js            # Script principal
│   │   │   ├── event-interactions.js # Interactions événements
│   │   │   ├── mes_amis.js          # Gestion amis
│   │   │   ├── villes.js            # Sélection villes
│   │   │   └── ...
│   │   ├── images/
│   │   │   └── uploads/             # Uploads utilisateurs
│   │   │       ├── avatars/
│   │   │       ├── banners/
│   │   │       ├── logos/
│   │   │       └── events/
│   │   └── favicon/
│   ├── index.php                    # Point d'entrée
│   ├── .htaccess                    # Configuration Apache
│   └── robots.txt
├── src/
│   ├── Abstracts/
│   │   └── AbstractController.php   # Contrôleur de base
│   ├── Controllers/                 # Contrôleurs
│   │   ├── UserController.php
│   │   ├── EvenementController.php
│   │   ├── AssociationController.php
│   │   ├── EntrepriseController.php
│   │   ├── FriendController.php
│   │   ├── NotificationController.php
│   │   ├── ContactController.php
│   │   ├── AdminController.php
│   │   └── HomeController.php
│   ├── Models/                      # Modèles métier
│   │   ├── User.php
│   │   ├── Evenement.php
│   │   ├── Association.php
│   │   ├── Entreprise.php
│   │   ├── Post.php
│   │   ├── Realisation.php
│   │   └── Contact.php
│   ├── Repositories/                # Accès données
│   │   ├── UserRepository.php
│   │   ├── EvenementRepository.php
│   │   ├── AssociationRepository.php
│   │   ├── EntrepriseRepository.php
│   │   ├── FriendRepository.php
│   │   ├── NotificationRepository.php
│   │   ├── ContactRepository.php
│   │   └── ...
│   ├── Services/                    # Services
│   │   ├── router.php
│   │   ├── ConfigRouter.php
│   │   ├── Database.php
│   │   ├── Mail.php
│   │   ├── Helper.php
│   │   ├── Encrypt_decrypt.php
│   │   ├── Hydration.php
│   │   ├── BadWordsFilter.php
│   │   └── autoload.php
│   ├── Views/                       # Vues
│   │   ├── user/
│   │   ├── evenement/
│   │   ├── association/
│   │   ├── entreprise/
│   │   ├── admin/
│   │   ├── contact/
│   │   └── includes/
│   ├── Migrations/                  # Migrations SQL
│   │   ├── empty_database.sql
│   │   ├── villes_france.sql
│   │   └── data.sql
│   └── init.php                     # Initialisation
├── vendor/                          # Dépendances Composer
├── node_modules/                    # Dépendances npm
├── config.php                       # Configuration
├── config_example.php               # Exemple configuration
├── composer.json
├── composer.lock
├── package.json
├── package-lock.json
├── .htaccess
├── .gitignore
└── readme.md
```

## 🔧 Fonctionnalités détaillées

### Système d'événements

#### Création d'événements

- Titre, description courte et complète
- Dates de début, fin et date limite d'inscription
- Lieu avec sélection ville (code postal)
- Nombre maximum de participants
- Prix et devise
- Catégorie d'événement
- Association/Entreprise organisatrice (optionnel)
- Bannière et galerie d'images
- Public/Privé
- Approbation requise pour inscription

#### Interactions utilisateurs

- **Like** : Aimer un événement
- **Favoris** : Mettre en favoris
- **Commentaires** : Système de commentaires hiérarchiques
- **Réponses** : Répondre aux commentaires avec mentions
- **Signalement** : Signaler des commentaires inappropriés
- **Partage social** : WhatsApp, Facebook, LinkedIn, Twitter, etc.

#### Inscriptions

- Inscription directe ou sur liste d'attente
- Validation par l'organisateur si requis
- Notifications aux participants
- Gestion des participants (accepter/refuser)
- Envoi d'emails de confirmation

### Système de notifications

#### Types de notifications

- `activation` : Activation d'entreprise
- `inscription` : Inscription à un événement
- `preinscription` : Pré-inscription en attente
- `invitation` : Demande d'amitié
- `mention` : Mention dans un commentaire
- `mise_a_jour` : Mise à jour d'événement
- `rappel` : Rappel d'événement
- `systeme` : Notifications système
- `alert` : Alertes importantes
- `message` : Messages divers

#### Fonctionnalités

- Badge avec compteur non lues
- Popup avec liste déroulante
- Polling automatique (60s)
- Marquer comme lu (individuel)
- Marquer tout comme lu
- Page dédiée avec pagination
- Priorités (0: normale, 1: importante)

### Système d'amitié

#### Fonctionnalités

- Recherche d'utilisateurs (AJAX)
- Envoi de demande d'amitié
- Accepter/Refuser les demandes
- Supprimer un ami
- Bloquer un utilisateur
- Liste d'amis avec pagination
- Demandes en attente (reçues/envoyées)

#### Interface

- Modal de recherche
- Onglets (Amis, Demandes reçues, Demandes envoyées)
- Options par ami (Supprimer, Bloquer)

### Gestion des entreprises

#### Processus d'activation

1. Création profil entreprise
2. Upload SIRET (Kbis PDF)
3. Demande d'activation avec message
4. Validation administrateur
5. Notification utilisateur
6. Activation compte

#### Restrictions

- Limiter demandes (1 tous les 3 jours)
- SIRET non modifiable après activation
- Validation manuelle obligatoire

### Système de contact

#### Workflow

1. Formulaire avec CSRF protection
2. Validation anti-spam (60 min entre messages)
3. Stockage en base de données
4. Email à l'administrateur
5. Confirmation utilisateur (email ou notification)
6. Gestion des statuts : nouveau, lu, traité, archivé

#### Interface admin

- Liste avec filtres par statut
- Répondre par email
- Marquer comme lu/traité
- Archiver/Supprimer
- Statistiques par statut

### Recherche globale

#### Entités recherchées

- Utilisateurs (nom, prénom, bio)
- Événements (titre, description)
- Entreprises (nom, description)
- Associations (nom, description)
- Villes (nom, code postal)

#### Fonctionnalités

- Recherche AJAX en temps réel
- Résultats avec images
- Navigation directe
- Protection CSRF
- Validation caractères

## 🔌 API et Routes

### Routes publiques

```
GET  /                              # Page d'accueil
GET  /connexion                     # Page de connexion
POST /connexion                     # Authentification
GET  /inscription                   # Page d'inscription
POST /inscription                   # Création compte
GET  /nous_contacter                # Formulaire contact
POST /nous_contacter                # Envoi message

GET  /evenements                    # Liste événements
GET  /evenements/{ville}/{cat}/{slug} # Détail événement
POST /evenement/like                # Liker événement
POST /evenement/favourite           # Favoriser événement
POST /evenement/comment             # Commenter
POST /evenement/comment/reply       # Répondre commentaire
POST /evenement/comment/like        # Liker commentaire
POST /evenement/comment/report      # Signaler commentaire
POST /evenement/comment/delete      # Supprimer commentaire

GET  /associations                  # Liste associations
GET  /associations/{slug}           # Détail association

GET  /entreprises                   # Liste entreprises
GET  /entreprises/{slug}            # Détail entreprise

POST /recherche                     # Recherche globale
POST /villes                        # Recherche villes (AJAX)
```

### Routes authentifiées

```
# Profil utilisateur
GET  /dashboard                     # Dashboard utilisateur
GET  /mon_compte                    # Gestion compte
POST /mon_compte/modifier           # Modifier profil
POST /profil/avatar                 # Changer avatar
POST /profil/banniere               # Changer bannière
GET  /mes_preferences               # Préférences
POST /mes_preferences               # Sauvegarder préférences
GET  /mes_favoris                   # Événements favoris
GET  /profil/{slug}                 # Profil public

# Amis
GET  /mes_amis                      # Liste amis
POST /amis/ajouter                  # Ajouter ami
POST /amis/accepter                 # Accepter demande
POST /amis/refuser                  # Refuser demande
POST /amis/supprimer                # Supprimer ami
POST /amis/bloquer                  # Bloquer utilisateur
POST /amis/rechercher               # Rechercher (AJAX)

# Événements
GET  /mes_evenements                # Mes événements
GET  /mes_evenements?action=voir    # Voir événement
GET  /evenement/ajouter             # Formulaire création
POST /evenement/ajouter             # Créer événement
GET  /evenement/modifier            # Formulaire édition
POST /evenement/modifier            # Modifier événement
POST /evenement/supprimer           # Supprimer événement
POST /evenement/banniere            # Changer bannière
POST /evenement/banniere/supprimer  # Supprimer bannière
POST /evenement/image/ajouter       # Ajouter image
POST /evenement/image/supprimer     # Supprimer image
POST /evenement/inscription         # S'inscrire
POST /evenement/accepter            # Accepter participant
POST /evenement/refuser             # Refuser participant
GET  /mes_inscriptions              # Mes inscriptions

# Associations
GET  /mes_associations              # Mes associations
GET  /mes_associations?action=voir  # Voir association
GET  /association/ajouter           # Formulaire création
POST /association/ajouter           # Créer association
GET  /association/modifier          # Formulaire édition
POST /association/modifier          # Modifier association
POST /association/supprimer         # Supprimer association
POST /association/logo              # Changer logo
POST /association/banniere          # Changer bannière

# Entreprises
GET  /mes_entreprises               # Mes entreprises
GET  /mes_entreprises?action=voir   # Voir entreprise
GET  /entreprise/ajouter            # Formulaire création
POST /entreprise/ajouter            # Créer entreprise
GET  /entreprise/modifier           # Formulaire édition
POST /entreprise/modifier           # Modifier entreprise
POST /entreprise/supprimer          # Supprimer entreprise
POST /entreprise/logo               # Changer logo
POST /entreprise/banniere           # Changer banniere
POST /entreprise/activer            # Demander activation

# Notifications
GET  /notifications                 # Page notifications
GET  /notifications/count           # Compteur (AJAX)
GET  /notifications/list            # Liste (AJAX)
POST /notifications/mark-read       # Marquer lue
POST /notifications/mark-all-read   # Tout marquer lu
```

### Routes administration

```
GET  /admin                         # Dashboard admin
GET  /admin/utilisateurs            # Liste utilisateurs
GET  /admin/utilisateur_details     # Détails utilisateur
POST /admin/utilisateur/bannir      # Bannir utilisateur
POST /admin/utilisateur/debannir    # Débannir utilisateur
POST /admin/utilisateur/email       # Envoyer email

GET  /admin/entreprises             # Demandes activation
POST /admin/entreprise/accepter     # Accepter demande
POST /admin/entreprise/refuser      # Refuser demande

GET  /admin/contacts                # Liste messages
POST /admin/contact/lire            # Marquer lu
POST /admin/contact/traiter         # Marquer traité
POST /admin/contact/archiver        # Archiver
POST /admin/contact/repondre        # Répondre
POST /admin/contact/supprimer       # Supprimer
```

## 🔒 Sécurité

### Mesures implémentées

#### Protection des formulaires

- **CSRF Tokens** : Tous les formulaires protégés
- **reCAPTCHA v2** : Sur inscription et contact
- **Validation serveur** : Toutes les entrées validées
- **Sanitization** : `htmlspecialchars()` systématique

#### Authentification

- **Mots de passe** : Hashage avec `password_hash()` (bcrypt)
- **Sessions** : Régénération ID après connexion
- **Remember me** : Tokens sécurisés
- **Tentatives** : Limitation des tentatives de connexion

#### Upload de fichiers

- **Validation type MIME** : `finfo_file()`
- **Extension** : Whitelist d'extensions autorisées
- **Taille** : Limite à 5MB
- **Noms** : Randomisation avec UIID
- **Dossiers** : Permissions 755

#### Base de données

- **PDO** : Requêtes préparées uniquement
- **Paramètres bindés** : Pas de concaténation SQL
- **Transactions** : Pour opérations critiques

#### Autres

- **Rate limiting** : Anti-spam sur contact (60 min)
- **Filtrage** : BadWordsFilter pour contenu
- **Chiffrement** : Données sensibles avec OpenSSL
- **Headers** : X-Robots-Tag en développement

## 🛠 Technologies utilisées

### Backend

- **PHP 8.1+** : Langage serveur
- **MySQL 5.7+** : Base de données
- **PDO** : Abstraction base de données
- **Composer** : Gestionnaire de dépendances

### Frontend

- **HTML5** : Structure
- **CSS3** : Styles avec variables CSS
- **JavaScript ES6+** : Interactivité
- **jQuery 3.6** : Manipulation DOM et AJAX
- **Material Icons** : Iconographie

### Bibliothèques

- **PHPMailer** : Envoi d'emails
- **Google reCAPTCHA** : Protection anti-bot

### Outils

- **Git** : Contrôle de version
- **npm** : Gestionnaire de packages JS

## 🤝 Contribution

### Workflow Git

```bash
# Créer une branche
git checkout -b feature/nouvelle-fonctionnalite

# Développer et commiter
git add .
git commit -m "feat: ajout nouvelle fonctionnalité"

# Pousser
git push origin feature/nouvelle-fonctionnalite

# Créer Pull Request
```

### Conventions de code

#### PHP

- PSR-12 pour le style
- Namespaces obligatoires
- Type hints et return types
- Documentation PHPDoc

#### JavaScript

- ES6+ syntaxe moderne
- Commentaires JSDoc
- Nommage camelCase

#### SQL

- UPPERCASE pour mots-clés
- snake_case pour tables/colonnes
- Migrations versionnées

### Tests

```bash
# À implémenter
# phpunit tests/
```

## 📝 License

Propriétaire - Tous droits réservés

---
