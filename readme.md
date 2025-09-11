# Application de Création de Groupes en MVC

Bienvenue sur mon application web **TIRSO**, c'est une application web de gestion de groupes et de tirages au sort, développée en PHP avec une architecture MVC (Modèle-Vue-Contrôleur).

## Description

TIRSO permet de :
- Créer et gérer des listes de personnes.
- Réaliser des tirages au sort pour répartir des personnes en groupes.
- Gérer les comptes utilisateurs (inscription, connexion, réinitialisation du mot de passe, modification du profil, etc.).
- Sécuriser les données utilisateurs.
- Utiliser une interface moderne et responsive.

## Fonctionnalités principales

- **Gestion des utilisateurs** : inscription, connexion, activation par email, modification du profil, suppression du compte, gestion du mot de passe.
- **Gestion des listes** : création, modification, suppression de listes.
- **Gestion des personnes** : ajout, modification, suppression de personnes dans une liste.
- **Tirage au sort** : répartition aléatoire des personnes en groupes.
- **Sécurité** : validation des données, protection contre les accès non autorisés, gestion des sessions.
- **Interface utilisateur** : pages HTML/CSS modernes, navigation intuitive.

## Structure du projet

```
/
├── public/                 # Fichiers accessibles publiquement (index.php, assets, etc.)
├── src/
│   ├── Abstracts/          # Contrôleurs abstraits
│   ├── Controllers/        # Contrôleurs principaux (User, List, Person, etc.)
│   ├── Models/             # Modèles (User, Lists, Person, etc.)
│   ├── Repositories/       # Accès aux données (UserRepository, ListRepository, etc.)
│   ├── Services/           # Services (Mail, Router, etc.)
│   ├── Views/              # Vues HTML/PHP
│   ├── Tests/Unitaires/    # Tests unitaires PHPUnit
│   └── init.php            # Initialisation de l'application
├── composer.json           # Dépendances PHP
├── phpunit.xml             # Configuration PHPUnit
└── readme.md               # Ce fichier
```

## Prérequis

- PHP >= 8.1
- Composer
- Un serveur web (Apache, Nginx, ou PHP built-in server)
- Une base de données MySQL/MariaDB

## Installation

1. **Cloner le dépôt**
   ```sh
   git clone <https://gitlab.com/AltalebFeras/V2-Application-Creation-de-Groupes-en-MVC.git>
   cd V2-Application-Creation-de-Groupes-en-MVC
   ```

2. **Installer les dépendances**
   ```sh
   composer install
   ```

3. **Configurer l'environnement du projet**

   - **Copier le fichier de configuration :**
     ```sh
     cp config_example.php config.php
     ```
   - **Ouvrir `config.php` et renseigner vos informations :**
     - **Pour la base de données locale (développement) :**
       ```php
       define("DB_HOST", "localhost");
       define("DB_PORT", "3306");
       define("DB_USER", "votre_utilisateur");
       define("DB_PWD", "votre_mot_de_passe");
       define("DB_NAME", "nom_de_votre_base");
       ```
     - **Pour la production**, remplissez la section correspondante.
     - **Clé d’encryptage** : Remplacez `YOUR_ENCRYPTION_KEY` par une chaîne de 32 caractères aléatoires.
     - **Configuration mail** : Renseignez les paramètres SMTP pour l’envoi d'emails (HOST, PORT, USERNAME, PASSWORD, FROM_EMAIL, etc.).
     - **DOMAIN et HOME_URL** : Adaptez selon votre environnement (localhost ou domaine réel).

4. **Importer la base de données**
   - Importez les fichiers SQL présents dans `src/Migrations/` dans votre base MySQL/MariaDB.

5. **Configurer avec WampServer (ou autre serveur local)**
   - Démarrez WampServer.
   - Créez un hôte virtuel pointant vers le dossier `public/` de votre projet.
   - Redémarrez les DNS si nécessaire.

6. **Accéder à l'application**
   - Ouvrez [http://localhost/nom_du_projet](http://localhost/nom_du_projet) dans votre navigateur (remplacez `nom_du_projet` par le nom du dossier de votre projet).

## Connexion à la base de données

La connexion à la base de données se fait automatiquement via les paramètres définis dans `config.php`.  
Assurez-vous que :
- Le serveur MySQL/MariaDB est démarré.
- Les identifiants et le nom de la base sont corrects.
- L'utilisateur a les droits nécessaires sur la base.

**Exemple de configuration locale dans `config.php` :**
```php
define("DB_HOST", "localhost");
define("DB_PORT", "3306");
define("DB_USER", "test_user");
define("DB_PWD", "test_password");
define("DB_NAME", "test_db");
define("ENCRYPTION_KEY", "YOUR_ENCRYPTION_KEY");
define("MAIL_HOST", "smtp.example.com");
define("MAIL_PORT", "587");
define("MAIL_USERNAME", "your_email@example.com");
define("MAIL_PASSWORD", "your_email_password");
define("MAIL_FROM", "your_email@example.com");  
```

## Lancer les tests unitaires

1. **Vérifier que PHPUnit est installé**
   ```sh
   composer require --dev phpunit/phpunit
   ```

2. **Exécuter les tests**
   ```sh
   vendor/bin/phpunit
   ```
   ou pour n'exécuter que les tests unitaires :
   ```sh
   vendor/bin/phpunit --testsuite "Application Test Suite"
   ```

## Points importants

- **Sécurité** : Ne jamais exposer vos identifiants de base de données ou de mail en production.
- **Structure MVC** : Respectez la séparation des responsabilités MVC (Modèle, Vue, Contrôleur).
- **Tests** : Les tests unitaires sont dans `src/Tests/Unitaires/` et n'utilisent pas la base de données réelle (utilisation de mocks).
- **Personnalisation** : Vous pouvez adapter les vues et les styles dans `src/Views/` et `public/assets/styles/`.

