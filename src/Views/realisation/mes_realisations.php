<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <div class="flex-row justify-content-between mb">
        <h1><?= htmlspecialchars($title ?? 'Mes réalisations') ?></h1>
        <a href="<?= HOME_URL . 'mes_entreprises?action=voir&uiid=' . htmlspecialchars($entreprise->getUiid()) ?>">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
        </a>
    </div>

    <div class="card mb-4">
        <div class="p-3">
            <h3>Entreprise : <?= htmlspecialchars($entreprise->getName()) ?></h3>
            <div class="flex-row justify-content-between mt-3">
                <a href="<?= HOME_URL . 'entreprise/mes_realisations/ajouter?entreprise_uiid=' . $entreprise->getUiid() ?>" class="btn linkNotDecorated">
                    Ajouter une réalisation
                </a>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/../includes/messages.php'; ?>

    <?php if (empty($realisations)): ?>
        <div class="custom-alert custom-alert-success">
            <p>Vous n'avez pas encore créé de réalisations pour cette entreprise. Cliquez sur "Ajouter une réalisation" pour commencer.</p>
        </div>
    <?php else: ?>
        <div class="flex-row align-items-center mb">
            <p><strong><?= $total ?></strong> réalisation(s) au total</p>
        </div>

        <div class="flex-row flex-wrap">
            <?php foreach ($realisations as $realisation): ?>
                <div class="max-width-33">
                    <div class="card">
                        <div style="height: 220px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                                    border-radius: 12px; display: flex; align-items: center; justify-content: center; 
                                    color: white; font-size: 1.2rem; margin-bottom: 1rem;">
                            <?= htmlspecialchars(substr($realisation->getTitle(), 0, 2)) ?>
                        </div>

                        <div>
                            <h5><?= htmlspecialchars($realisation->getTitle()) ?></h5>

                            <?php if ($realisation->getDateRealized()): ?>
                                <p><small class="text-muted">Réalisé le : <?= htmlspecialchars($realisation->getFormattedDateRealized()) ?></small></p>
                            <?php endif; ?>

                            <p><?= htmlspecialchars($realisation->getDisplayDescription(100)) ?></p>

                            <div class="flex-row mt">
                                <?php if ($realisation->getIsFeatured()): ?>
                                    <span class="badge badge-success mr-2">Mise en avant</span>
                                <?php endif; ?>
                                <?php if (!$realisation->getIsPublic()): ?>
                                    <span class="badge badge-secondary">Privée</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="flex-row justify-content-between mt">
                            <a href="<?= HOME_URL . 'entreprise/mes_realisations?action=voir&entreprise_uiid=' . $entreprise->getUiid() . '&realisation_uiid=' . $realisation->getUiid() ?>"
                                class="btn linkNotDecorated">Voir</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <?php include_once __DIR__ . '/../includes/pagination.php'; ?>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>