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
        <a href="<?= HOME_URL . "mes_associations" ?>" class="cardDashboard">
            <h2>Mes Associations</h2>
            <p>Voir toutes mes associations.</p>
        </a>
        <a href="<?= HOME_URL . "mes_entreprises" ?>" class="cardDashboard">
            <h2>Mes Entreprises</h2>
            <p>Voir toutes mes entreprises.</p>
        </a>

        <a href="<?= HOME_URL . "mes_evenements" ?>" class="cardDashboard">
            <h2>Mes Événements</h2>
            <p>Voir tous mes événements.</p>
        </a>
        <a href="<?= HOME_URL . "mon_compte" ?>" class="cardDashboard">
            <h2>Mon compte</h2>
            <p>Voir et modifier mon profil, gérer mes événements, associations, entreprises, et accéder à la messagerie.</p>
        </a>

    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>