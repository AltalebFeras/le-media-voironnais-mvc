<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/associations/mes_associations.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="mes-associations-container">
    <div class="mes-associations-header">
        <div>
            <h1>Mes associations</h1>
            <p class="text-muted">Gérez vos associations et leurs membres</p>
        </div>
        <div class="mes-associations-actions">
            <a href="<?= HOME_URL . 'association/ajouter' ?>" class="btn btn-success linkNotDecorated">
                <span class="material-icons" style="font-size: 18px; vertical-align: middle;">group_add</span>
                Ajouter une association
            </a>
            <a href="<?= HOME_URL . 'dashboard' ?>" class="">
                <span class="material-icons btn" style="color:white;">arrow_back</span>
            </a>
        </div>
    </div>

    <?php include_once __DIR__ . '/../includes/messages.php'; ?>

    <?php if (empty($associations)): ?>
        <div class="empty-state-card">
            <span class="material-icons">groups</span>
            <h3>Aucune association</h3>
            <p>Vous n'avez pas encore créé d'association. Commencez dès maintenant !</p>
            <a href="<?= HOME_URL . 'association/ajouter' ?>" class="btn btn-primary linkNotDecorated">
                <span class="material-icons" style="font-size: 18px; vertical-align: middle;">add</span>
                Créer ma première association
            </a>
        </div>
    <?php else: ?>
        <div class="association-grid">
            <?php foreach ($associations as $association): ?>
                <div class="association-card-wrapper">
                    <div class="association-card">
                        <?php if ($association->getBannerPath()): ?>
                            <img src="<?= $association->getBannerPath() ?>" 
                                 alt="Bannière de <?= htmlspecialchars($association->getName()) ?>"
                                 class="association-card-banner">
                        <?php else: ?>
                            <div class="association-card-banner" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-accent) 100%);"></div>
                        <?php endif; ?>

                        <div class="association-card-content">
                            <div class="association-card-header">
                                <?php if ($association->getLogoPath()): ?>
                                    <img src="<?= $association->getLogoPath() ?>" 
                                         class="association-card-logo"
                                         alt="Logo de <?= htmlspecialchars($association->getName()) ?>">
                                <?php else: ?>
                                    <div class="association-card-logo" style="background: var(--bg-light); display: flex; align-items: center; justify-content: center;">
                                        <span class="material-icons" style="color: var(--color-primary);">groups</span>
                                    </div>
                                <?php endif; ?>
                                <h5><?= htmlspecialchars($association->getName()) ?></h5>
                            </div>

                            <p class="association-card-description">
                                <?= htmlspecialchars($association->getDescription() ?? 'Aucune description') ?>
                            </p>

                            <div class="association-card-meta">
                                <div class="association-card-status">
                                    <strong>Statut:</strong>
                                    <span class="status-indicator <?= $association->getIsActive() ? 'active' : 'inactive' ?>">
                                        <span class="material-icons" style="font-size: 14px;">
                                            <?= $association->getIsActive() ? 'check_circle' : 'cancel' ?>
                                        </span>
                                        <?= $association->getIsActive() ? 'Active' : 'Inactive' ?>
                                    </span>
                                </div>
                                <small>
                                    <span class="material-icons" style="font-size: 14px;">people</span>
                                    Vous êtes membre de cette association
                                </small>
                            </div>

                            <div class="association-card-actions">
                                <a href="<?= HOME_URL . 'mes_associations?action=voir&uiid=' . $association->getUiid() ?>" 
                                   class="btn btn-primary linkNotDecorated">
                                    <span class="material-icons" style="font-size: 18px; vertical-align: middle;">visibility</span>
                                    Voir
                                </a>
                                <a href="<?= HOME_URL . 'association/modifier?uiid=' . $association->getUiid() ?>" 
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