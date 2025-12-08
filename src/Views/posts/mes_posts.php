<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <div class="flex-row justify-content-between mb">
        <h1><?= htmlspecialchars($title ?? 'Mes Actualités') ?></h1>
        <a href="<?= HOME_URL ?>post/ajouter" class="btn linkNotDecorated">
            + Nouvelle actualité
        </a>
    </div>

    <?php include_once __DIR__ . '/../includes/messages.php'; ?>

    <?php if (empty($posts)): ?>
        <div class="custom-alert custom-alert-info">
            <p>Vous n'avez pas encore créé d'actualité. Cliquez sur "Nouvelle actualité" pour commencer.</p>
        </div>
    <?php else: ?>
        <div class="flex-row align-items-center mb">
            <p><strong><?= $totalPosts ?></strong> actualité(s) au total</p>
        </div>

        <div class="flex-row flex-wrap">
            <?php foreach ($posts as $post): ?>
                <div class="max-width-33">
                    <div class="card">
                        <?php if ($post['imagePath']): ?>
                            <div style="height: 220px; overflow: hidden; border-radius: 12px; margin-bottom: 1rem;">
                                <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['imagePath']) ?>" 
                                     alt="<?= htmlspecialchars($post['title']) ?>" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        <?php else: ?>
                            <div style="height: 220px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                                        border-radius: 12px; display: flex; align-items: center; justify-content: center; 
                                        color: white; font-size: 1.5rem; margin-bottom: 1rem;">
                                <?= htmlspecialchars(substr($post['title'], 0, 2)) ?>
                            </div>
                        <?php endif; ?>

                        <div>
                            <h5><?= htmlspecialchars($post['title']) ?></h5>
                            <p><?= htmlspecialchars(substr($post['content'], 0, 100)) ?>...</p>

                            <div class="flex-row mt">
                                <span class="badge <?= $post['isPublished'] ? 'badge-success' : 'badge-warning' ?> mr-2">
                                    <?= $post['isPublished'] ? 'Publié' : 'Brouillon' ?>
                                </span>

                                <?php if ($post['authorType'] === 'association'): ?>
                                    <span class="badge badge-info mr-2">Association</span>
                                <?php elseif ($post['authorType'] === 'entreprise'): ?>
                                    <span class="badge badge-primary mr-2">Entreprise</span>
                                <?php endif; ?>
                            </div>

                            <?php if ($post['association_name']): ?>
                                <p><small class="text-muted">Association : <?= htmlspecialchars($post['association_name']) ?></small></p>
                            <?php elseif ($post['entreprise_name']): ?>
                                <p><small class="text-muted">Entreprise : <?= htmlspecialchars($post['entreprise_name']) ?></small></p>
                            <?php endif; ?>
                        </div>

                        <div class="flex-row justify-content-between mt">
                            <a href="<?= HOME_URL ?>post/modifier?uiid=<?= htmlspecialchars($post['uiid']) ?>" 
                               class="btn linkNotDecorated">
                                Modifier
                            </a>
                            <form method="POST" action="<?= HOME_URL ?>post/supprimer" style="display: inline;">
                                <input type="hidden" name="uiid" value="<?= htmlspecialchars($post['uiid']) ?>">
                                <button type="submit" class="btn bg-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette actualité ?')">
                                    Supprimer
                                </button>
                            </form>
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
