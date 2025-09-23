<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../components/messages.php';
?>

<main>
    <div class="flex-row align-items-center mb">
        <h1><?= htmlspecialchars($title ?? 'Mes réalisations') ?></h1>
        <a href="<?= HOME_URL ?>realisation/ajouter?uiid=<?= htmlspecialchars($entreprise->getUiid()) ?>" class="btn">
            + Ajouter une réalisation
        </a>
    </div>

    <div class="card mb">
        <h3>Entreprise : <?= htmlspecialchars($entreprise->getName()) ?></h3>
        <p>
            <a href="<?= HOME_URL ?>mes_entreprises?action=voir&uiid=<?= htmlspecialchars($entreprise->getUiid()) ?>" class="link">
                ← Retour à l'entreprise
            </a>
        </p>
    </div>

    <?php if (empty($realisations)): ?>
        <div class="card text-center">
            <h3>Aucune réalisation</h3>
            <p>Vous n'avez pas encore créé de réalisations pour cette entreprise.</p>
            <a href="<?= HOME_URL ?>realisation/ajouter?uiid=<?= htmlspecialchars($entreprise->getUiid()) ?>" class="btn">
                Créer ma première réalisation
            </a>
        </div>
    <?php else: ?>
        <div class="flex-row align-items-center mb">
            <p><strong><?= $total ?></strong> réalisation(s) au total</p>
        </div>

        <div class="dashboard-grid">
            <?php foreach ($realisations as $realisation): ?>
                <div class="card">
                    <div style="height: 220px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                                border-radius: 12px; display: flex; align-items: center; justify-content: center; 
                                color: white; font-size: 1.2rem; margin-bottom: 1rem;">
                        <?= htmlspecialchars(substr($realisation->getTitle(), 0, 2)) ?>
                    </div>
                    
                    <h3><?= htmlspecialchars($realisation->getTitle()) ?></h3>
                    
                    <?php if ($realisation->getDateRealized()): ?>
                        <p><strong>Réalisé le :</strong> <?= htmlspecialchars($realisation->getFormattedDateRealized()) ?></p>
                    <?php endif; ?>

                    <p><?= htmlspecialchars($realisation->getDisplayDescription(100)) ?></p>

                    <div class="flex-row mt">
                        <?php if ($realisation->getIsFeatured()): ?>
                            <span class="badge-success">Mise en avant</span>
                        <?php endif; ?>
                        <?php if (!$realisation->getIsPublic()): ?>
                            <span class="badge-secondary">Privée</span>
                        <?php endif; ?>
                    </div>

                    <div class="flex-row mt">
                        <a href="<?= HOME_URL ?>realisation/voir?realisation_uiid=<?= htmlspecialchars($realisation->getUiid()) ?>" 
                           class="btn btn-info">Voir</a>
                        <a href="<?= HOME_URL ?>realisation/modifier?realisation_uiid=<?= htmlspecialchars($realisation->getUiid()) ?>" 
                           class="btn">Modifier</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pager">
                <?php if ($currentPage > 1): ?>
                    <a href="?uiid=<?= htmlspecialchars($entreprise->getUiid()) ?>&page=<?= $currentPage - 1 ?>" 
                       class="page-link">← Précédent</a>
                <?php else: ?>
                    <span class="page-link disabled">← Précédent</span>
                <?php endif; ?>

                <span class="page-info">Page <?= $currentPage ?> sur <?= $totalPages ?></span>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?uiid=<?= htmlspecialchars($entreprise->getUiid()) ?>&page=<?= $currentPage + 1 ?>" 
                       class="page-link">Suivant →</a>
                <?php else: ?>
                    <span class="page-link disabled">Suivant →</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
