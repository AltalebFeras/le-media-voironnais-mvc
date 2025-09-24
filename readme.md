# Le Média Voironnais

Projet PHP (PDO + MVC léger) pour la gestion d'événements, associations, entreprises et système de chat/notifications.

## Présentation

Application web minimale organisée en logique contrôleur / repository / modèle. Comprend :

- Gestion utilisateurs (inscription, activation, réinitialisation MDp)
- Événements, associations, entreprises
- Chats et notifications
- Uploads d'avatars et bannières
- Templates d'e-mails via PHPMailer

## Prérequis

- PHP 8.0+ (extensions : pdo_mysql, mbstring, fileinfo, openssl, exif pour orientation JPEG)
- Composer
- MySQL / MariaDB
- Serveur web (Apache/Nginx) ou serveur PHP intégré

## Installation rapide

1. Cloner le dépôt dans votre environnement de développement.
2. Installer les dépendances :
   - composer install
3. Créer un fichier de configuration à partir de l'exemple :
   - Copier `config_example.php` → `config.php` et adapter les constantes (DB_HOST, DB_NAME, DB_USER, DB_PWD, DOMAIN, HOME_URL, SEL, ADMIN_EMAIL, etc.).
4. Importer la base de données :
   - Utiliser `src/Migrations/db.sql` ou `src/Migrations/erd.sql` selon vos besoins pour créer les tables et insérer des données factices.
   - Exemple : mysql -u user -p le-media-voironnais < src/Migrations/db.sql

## Configuration

- `config.php` doit définir au minimum :
  - DB_HOST, DB_NAME, DB_USER, DB_PWD
  - DOMAIN (ex : <http://localhost>)
  - HOME_URL (chemin racine de l'app, ex : /)
  - SEL (chaîne utilisée comme "pepper" pour le hash des mots de passe)
  - ADMIN_EMAIL, ADMIN_SENDER_NAME
  - SECRET_KEY (reCAPTCHA) si activé
  - IS_PROD (true/false) pour activer les vérifications reCAPTCHA et comportements de prod

Remarque : ne pas committer `config.php` contenant des secrets.

## Lancer l'application en local

Option 1 — serveur PHP intégré (développement) :

- Depuis la racine `public/` :
  - php -S 127.0.0.1:8000

Option 2 — Apache/Nginx :

- Pointer la racine web sur le dossier `public/`.
- `.htaccess` présent pour la réécriture vers `index.php`.

## Structure du projet (repères rapides)

- public/ — point d'entrée (index.php), assets (css, js, images)
- src/
  - Controllers/ — logique des actions (HomeController, UserController, ...)
  - Models/ — entités (User, ...)
  - Repositories/ — accès DB
  - Services/ — Database, Mail, Helpers
  - Views/ — vues (includes, pages)
  - Migrations/ — schémas et dumps SQL
- config_example.php — exemple de configuration à copier en config.php
- composer.json — dépendances (PHPMailer, ...)

## Base de données

- Les fichiers SQL utiles :
  - `src/Migrations/db.sql` — dump DB complet
  - `src/Migrations/erd.sql` — schéma amélioré + données d'exemple
- Vérifier le charset / engine lors de l'import si nécessaire.

## Sécurité & bonnes pratiques

- Ne pas committer config.php contenant des secrets.
- Utiliser SEL (pepper) et les bonnes pratiques de hachage de mot de passe (déjà présents dans le code).
- Vérifier et remplacer les clés reCAPTCHA / SMTP avant déploiement.
**Attention en production :**  
Le SEL utilisé pour le hachage des mots de passe **ne doit jamais être vide** et **doit rester fixe pendant toute la vie de l’application**.  
- Il **ne doit pas être généré aléatoirement à chaque démarrage**.
- Il **ne doit pas être exposé dans le code source ni dans les fichiers de configuration**.
- **Il est interdit de le changer** une fois l’application en production, sous peine d’invalider tous les mots de passe existants.
- Stockez-le de façon sécurisée (ex : gestionnaire de secrets, variable d’environnement protégée).

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
