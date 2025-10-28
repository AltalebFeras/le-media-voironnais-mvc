# Le Média Voironnais

Plateforme web communautaire pour la région de Voiron, permettant la gestion d'événements, d'entreprises, d'associations, de réalisations et d'utilisateurs.

---

## Sommaire

- [Fonctionnalités principales](#fonctionnalités-principales)
- [Architecture du projet](#architecture-du-projet)
- [Routes principales](#routes-principales)
- [Logique métier & Contrôleurs](#logique-métier--contrôleurs)
- [Vues](#vues)
- [Modèles & Repositories](#modèles--repositories)
- [Sécurité](#sécurité)
- [Installation & Configuration](#installation--configuration)
- [Base de données](#base-de-données)
- [Lancer l'application](#lancer-lapplication)
- [Tests unitaires](#tests-unitaires)
- [Annexes](#annexes)

---

## Fonctionnalités principales

- **Gestion des utilisateurs** : inscription, connexion, activation par email, édition du profil, changement d'email, réinitialisation du mot de passe, suppression de compte, notifications.
- **Entreprises** : création, modification, suppression, gestion des logos/bannières, demande d'activation (avec envoi de Kbis), réalisations associées.
- **Associations** : création, modification, suppression, gestion des membres, logos/bannières, invitations.
- **Événements** : création, modification, suppression, images, catégories, gestion des participants, invitations, pagination.
- **Réalisation** : ajout, modification, suppression, images, rattachement à une entreprise.
- **Administration** : gestion des utilisateurs (bannir/débannir, email), validation/refus d'entreprises, notifications système.
- **Notifications** : badge, liste, lecture, marquer comme lu, API JSON.
- **Sécurité** : CSRF, validation serveur, gestion des sessions, reCAPTCHA (optionnel), gestion des droits.
- **Pagination** : sur toutes les listes (utilisateurs, entreprises, associations, événements, réalisations).
- **Gestion des fichiers** : upload sécurisé (images, PDF), suppression, renommage, validation MIME/type/taille.

---

## Architecture du projet

- **MVC** (Modèle-Vue-Contrôleur) en PHP natif.
- **src/** : code source principal
  - **Controllers/** : logique métier et gestion des routes
  - **Models/** : entités métiers (User, Entreprise, Association, Evenement, Realisation)
  - **Repositories/** : accès aux données (requêtes SQL, pagination, etc.)
  - **Services/** : utilitaires (mail, helper, sécurité, etc.)
  - **Views/** : templates PHP pour l'affichage
  - **Migrations/** : scripts SQL pour la base de données
- **config.php** : configuration principale (DB, email, etc.)
- **public/** ou racine : point d'entrée, .htaccess, assets

---

## Routes principales

| Route                                    | Méthode | Description                                      |
|-------------------------------------------|---------|--------------------------------------------------|
| `/`                                      | GET     | Accueil                                          |
| `/connexion`                             | GET/POST| Connexion utilisateur                            |
| `/inscription`                           | GET/POST| Inscription utilisateur                          |
| `/mon_compte`                            | GET     | Profil utilisateur                               |
| `/dashboard`                             | GET     | Tableau de bord utilisateur                      |
| `/notifications`                         | GET     | Page notifications                               |
| `/notifications/count`                   | GET     | API JSON : nombre de notifications non lues      |
| `/notifications/list`                    | GET     | API JSON : liste paginée des notifications       |
| `/notifications/mark_read`               | POST    | API JSON : marquer une notification comme lue    |
| `/notifications/mark_all_read`           | POST    | API JSON : marquer toutes comme lues             |
| `/mes_entreprises`                       | GET     | Liste des entreprises de l'utilisateur           |
| `/entreprise/ajouter`                    | GET/POST| Ajouter une entreprise                           |
| `/entreprise/modifier`                   | GET/POST| Modifier une entreprise                          |
| `/entreprise/voir`                       | GET     | Détail d'une entreprise                          |
| `/entreprise/mes_realisations`           | GET     | Liste des réalisations d'une entreprise          |
| `/realisation/ajouter`                   | GET/POST| Ajouter une réalisation                          |
| `/realisation/modifier`                  | GET/POST| Modifier une réalisation                         |
| `/realisation/voir`                      | GET     | Détail d'une réalisation                         |
| `/mes_associations`                      | GET     | Liste des associations de l'utilisateur          |
| `/association/ajouter`                   | GET/POST| Ajouter une association                          |
| `/association/modifier`                  | GET/POST| Modifier une association                         |
| `/association/voir`                      | GET     | Détail d'une association                         |
| `/mes_evenements`                        | GET     | Liste des événements de l'utilisateur            |
| `/evenement/ajouter`                     | GET/POST| Ajouter un événement                             |
| `/evenement/modifier`                    | GET/POST| Modifier un événement                            |
| `/evenement/voir`                        | GET     | Détail d'un événement                            |
| `/evenements`                            | GET     | Liste publique des événements                    |
| `/admin/dashboard_admin`                 | GET     | Tableau de bord admin                            |
| `/admin/tous_les_utilisateurs`           | GET     | Liste des utilisateurs (admin)                   |
| `/admin/utilisateur_details`             | GET     | Détail utilisateur (admin)                       |
| `/admin/toutes_demandes_dactivation_entreprise` | GET | Liste des demandes d'activation d'entreprise     |

> **Remarque** : la plupart des routes POST sont protégées par CSRF et nécessitent une session utilisateur.

---

## Logique métier & Contrôleurs

- **Controllers/** : chaque entité a son contrôleur dédié (UserController, EntrepriseController, AssociationController, EvenementController, RealisationController, AdminController, NotificationController, HomeController).
- **AbstractController** : fournit les méthodes utilitaires (`render`, `redirect`, gestion des erreurs, CSRF, etc.).
- **Validation** : chaque action POST valide les entrées, gère les erreurs via la session, et redirige vers la vue concernée.
- **Sécurité** : vérification des droits (ownership), validation des fichiers, gestion des sessions, etc.
- **Notifications** : ajoutées via NotificationRepository, affichées en temps réel (badge, liste AJAX).

---

## Vues

- **Views/** : organisation par entité et usage (home, user, entreprise, association, evenement, realisation, admin, includes).
- **Templates** : header, footer, navbar, messages, pagination, notification_badge.
- **Formulaires** : tous les formulaires incluent un token CSRF.
- **Affichage conditionnel** : selon le rôle, l'état de l'entité, la propriété, etc.
- **Pagination** : incluse sur toutes les listes longues.

---

## Modèles & Repositories

- **Models/** : classes métiers (User, Entreprise, Association, Evenement, Realisation) avec getters/setters, validation, formatage.
- **Repositories/** : accès aux données (CRUD, pagination, recherche, vérifications d'unicité, etc.), une classe par entité.
- **Hydration** : conversion des résultats SQL en objets métiers.
- **Services/** : utilitaires (Mail, Helper pour upload/suppression fichiers, Encrypt_decrypt, etc.).

---

## Sécurité

- **CSRF** : tous les formulaires POST sont protégés.
- **Validation serveur** : toutes les entrées sont validées côté serveur.
- **Gestion des sessions** : session fixation, fingerprint, logout sécurisé.
- **Gestion des droits** : vérification de la propriété avant modification/suppression.
- **Upload fichiers** : validation MIME/type/taille, suppression sécurisée.
- **reCAPTCHA** : optionnel, prêt à être activé en production.
- **Hashage des mots de passe** : `password_hash` avec pepper (SEL).

---

## Installation & Configuration

### Installation rapide

1. Cloner le dépôt dans votre environnement de développement.
2. Installer les dépendances PHP :
   ```sh
   composer install
   ```
3. Créer un fichier de configuration à partir de l'exemple :
   - Copier `config_example.php` → `config.php` et adapter les constantes (DB_HOST, DB_NAME, DB_USER, DB_PWD, BASE_URL, HOME_URL, SEL, ADMIN_EMAIL, etc.).
4. Importer la base de données :
   - Utiliser `src/Migrations/db.sql` ou `src/Migrations/erd.sql` selon vos besoins pour créer les tables et insérer des données factices.
   - Exemple : `mysql -u user -p le-media-voironnais < src/Migrations/db.sql`

### Configuration

- `config.php` doit définir au minimum :
  - DB_HOST, DB_NAME, DB_USER, DB_PWD
  - BASE_URL (ex : <http://localhost>)
  - HOME_URL (chemin racine de l'app, ex : /)
  - SEL (chaîne utilisée comme "pepper" pour le hash des mots de passe)
  - ADMIN_EMAIL, ADMIN_SENDER_NAME
  - SECRET_KEY (reCAPTCHA) si activé
  - IS_PROD (true/false) pour activer les vérifications reCAPTCHA et comportements de prod

Remarque : ne pas committer `config.php` contenant des secrets.

### Lancer l'application en local

Option 1 — serveur PHP intégré (développement) :

- Depuis la racine `public/` :
  ```sh
  php -S 127.0.0.1:8000
  ```

Option 2 — Apache/Nginx :

- Pointer la racine web sur le dossier `public/`.
- `.htaccess` présent pour la réécriture vers `index.php`.

---

## Base de données

- Les fichiers SQL utiles :
  - `src/Migrations/db.sql` — dump DB complet
  - `src/Migrations/erd.sql` — schéma amélioré + données d'exemple
- Vérifier le charset / engine lors de l'import si nécessaire.

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

---

## Lancer l'application

1. **Importer la base de données**
   - Importez les fichiers SQL présents dans `src/Migrations/` dans votre base MySQL/MariaDB.

2. **Configurer avec WampServer (ou autre serveur local)**
   - Démarrez WampServer.
   - Créez un hôte virtuel pointant vers le dossier `public/` de votre projet.
   - Redémarrez les DNS si nécessaire.

3. **Accéder à l'application**
   - Ouvrez [http://localhost/nom_du_projet](http://localhost/nom_du_projet) dans votre navigateur (remplacez `nom_du_projet` par le nom du dossier de votre projet).

---

## Tests unitaires

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

---

## Points importants & Sécurité

- **Ne pas committer config.php contenant des secrets.**
- Utiliser SEL (pepper) et les bonnes pratiques de hachage de mot de passe (déjà présents dans le code).
- Vérifier et remplacer les clés reCAPTCHA / SMTP avant déploiement.
- Le SEL utilisé pour le hachage des mots de passe **ne doit jamais être vide** et **doit rester fixe pendant toute la vie de l’application**.
- Il **ne doit pas être généré aléatoirement à chaque démarrage**.
- Il **ne doit pas être exposé dans le code source ni dans les fichiers de configuration**.
- **Il est interdit de le changer** une fois l’application en production, sous peine d’invalider tous les mots de passe existants.
- Stockez-le de façon sécurisée (ex : gestionnaire de secrets, variable d’environnement protégée).

---

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

---

## Annexes

- **Dépendances** : PHPMailer, Composer autoload, etc.
- **Personnalisation** : couleurs, logos, bannières, catégories d'événements.
- **Évolutions possibles** : API REST, gestion avancée des droits, recherche avancée, etc.

---

**Pour toute question ou contribution, contactez l'équipe du projet.**

⚠️ **Important :**

Des protections contre l'indexation par les moteurs de recherche sont activées dans ce projet (balise `<meta name="robots" content="noindex, nofollow">` dans le header, directive `X-Robots-Tag` dans le `.htaccess`, et fichier `robots.txt`).
