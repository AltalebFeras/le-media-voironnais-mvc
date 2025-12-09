<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/entreprises/mes_entreprises.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="mes-entreprises-container ">
    <div class="mes-entreprises-header">
        <div>
            <h1>Mes entreprises</h1>
            <p class="text-muted">Gérez vos entreprises et leurs informations</p>
        </div>
        <div class="mes-entreprises-actions">
            <a href="<?= HOME_URL . 'entreprise/ajouter' ?>" class="btn btn-success linkNotDecorated">
                <span class="material-icons" style="font-size: 18px; vertical-align: middle;">add_business</span>
                Ajouter une entreprise
            </a>
            <a href="<?= HOME_URL . 'dashboard' ?>" >
                <span class="material-icons btn" style="color:white;">arrow_back</span>
            </a>
        </div>
    </div>

    <?php include_once __DIR__ . '/../includes/messages.php'; ?>

    <?php if (empty($entreprises)): ?>
        <div class="empty-state-card">
            <span class="material-icons">business</span>
            <h3>Aucune entreprise</h3>
            <p>Vous n'avez pas encore créé d'entreprise. Commencez dès maintenant !</p>
            <a href="<?= HOME_URL . 'entreprise/ajouter' ?>" class="btn btn-primary linkNotDecorated">
                <span class="material-icons" style="font-size: 18px; vertical-align: middle;">add</span>
                Créer ma première entreprise
            </a>
        </div>
    <?php else: ?>
        <div class="entreprise-grid">
            <?php foreach ($entreprises as $entreprise): ?>
                <div class="entreprise-card-wrapper">
                    <div class="entreprise-card">
                        <?php if ($entreprise->getBannerPath()): ?>
                            <img src="<?= $entreprise->getBannerPath() ?>" 
                                 alt="Bannière de <?= htmlspecialchars($entreprise->getName()) ?>"
                                 class="entreprise-card-banner">
                        <?php else: ?>
                            <div class="entreprise-card-banner" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-accent) 100%);"></div>
                        <?php endif; ?>

                        <div class="entreprise-card-content">
                            <div class="entreprise-card-header">
                                <?php if ($entreprise->getLogoPath()): ?>
                                    <img src="<?= $entreprise->getLogoPath() ?>" 
                                         class="entreprise-card-logo"
                                         alt="Logo de <?= htmlspecialchars($entreprise->getName()) ?>">
                                <?php else: ?>
                                    <div class="entreprise-card-logo" style="background: var(--bg-light); display: flex; align-items: center; justify-content: center;">
                                        <span class="material-icons" style="color: var(--color-primary);">business</span>
                                    </div>
                                <?php endif; ?>
                                <h5><?= htmlspecialchars($entreprise->getName()) ?></h5>
                            </div>

                            <p class="entreprise-card-description">
                                <?= htmlspecialchars($entreprise->getDescription() ?? 'Aucune description') ?>
                            </p>

                            <div class="entreprise-card-meta">
                                <?php if ($entreprise->getSiret()): ?>
                                    <small>
                                        <span class="material-icons" style="font-size: 14px;">badge</span>
                                        SIRET: <?= htmlspecialchars($entreprise->getSiret()) ?>
                                    </small>
                                <?php endif; ?>
                                <div class="entreprise-card-status">
                                    <strong>Statut:</strong>
                                    <span class="status-indicator <?= $entreprise->getIsActive() ? 'active' : 'inactive' ?>">
                                        <span class="material-icons" style="font-size: 14px;">
                                            <?= $entreprise->getIsActive() ? 'check_circle' : 'cancel' ?>
                                        </span>
                                        <?= $entreprise->getIsActive() ? 'Active' : 'Inactive' ?>
                                    </span>
                                </div>
                            </div>

                            <div class="entreprise-card-actions">
                                <a href="<?= HOME_URL . 'mes_entreprises?action=voir&uiid=' . $entreprise->getUiid() ?>" 
                                   class="btn btn-primary linkNotDecorated">
                                    <span class="material-icons" style="font-size: 18px; vertical-align: middle;">visibility</span>
                                    Voir
                                </a>
                                <a href="<?= HOME_URL . 'entreprise/modifier?uiid=' . $entreprise->getUiid() ?>" 
                                   class="btn btn-light linkNotDecorated">
                                    <span class="material-icons" style="font-size: 18px; vertical-align: middle;">edit</span>
                                    Modifier
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php include_once __DIR__ . '/../includes/pagination.php'; ?>
    <?php endif; ?>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>