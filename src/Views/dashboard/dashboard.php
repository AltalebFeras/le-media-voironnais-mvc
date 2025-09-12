<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/dashboard.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="dashboard-container">
    <h1 class="dashboard-title">Tableau de bord</h1>
    <?php include_once __DIR__ . '/../includes/messages.php'; ?>
    <div class="dashboard-grid">

        <a href="<?= HOME_URL ?>" class="cardDashboard">
            <h2>Accueil</h2>
            <p>Retour à la page d'accueil du Media Voironnais.</p>
        </a>
        <a href="<?= HOME_URL . "all_lists" ?>" class="cardDashboard">
            <h2>Mes listes</h2>
            <p>Voir toutes mes listes de courses.</p>
        </a>
        <a href="<?= HOME_URL . "list_operations?action=add_new_list" ?>" class="cardDashboard">
            <h2>Créer une nouvelle liste</h2>
            <p>Créer une nouvelle liste de courses.</p>
        </a>
        <a href="<?= HOME_URL . "mon_compte" ?>" class="cardDashboard">
            <h2>Mon compte</h2>
            <p>Voir et modifier mon profil, gérer mes événements, associations, entreprises, et accéder à la messagerie.</p>
        </a>
        <a href="<?= HOME_URL . "deconnexion" ?>" class="cardDashboard">
            <h2>Se déconnecter</h2>
            <p>Déconnexion de votre compte.</p>
        </a>

    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>