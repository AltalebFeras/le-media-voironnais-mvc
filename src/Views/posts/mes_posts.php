<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <div class="page-header">
        <h1><?= htmlspecialchars($title ?? 'Mes Actualités') ?></h1>
        <a href="<?= HOME_URL ?>post/ajouter" class="btn linkNotDecorated">
            <span class="material-icons">add</span> Nouvelle actualité
        </a>
    </div>

    <?php include_once __DIR__ . '/../includes/messages.php'; ?>

    <?php if (empty($posts)): ?>
        <div class="posts-empty-state">
            <span class="material-icons">article</span>
            <p>Vous n'avez pas encore créé d'actualité.</p>
            <p>Cliquez sur "Nouvelle actualité" pour commencer.</p>
        </div>
    <?php else: ?>
        <div class="post-stats">
            <p><strong><?= $totalPosts ?></strong> actualité(s) au total</p>
        </div>

        <div class="posts-grid">
            <?php foreach ($posts as $post): ?>
                <div class="post-card">
                    <div class="post-image-container">
                        <?php if ($post['imagePath']): ?>
                            <img class="post-image" 
                                src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['imagePath']) ?>"
                                alt="<?= htmlspecialchars($post['title']) ?>">
                        <?php else: ?>
                            <div class="post-placeholder">
                                <?= htmlspecialchars(substr($post['title'], 0, 2)) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="post-content">
                        <h5 class="post-title"><?= htmlspecialchars($post['title']) ?></h5>
                        <p class="post-excerpt"><?= htmlspecialchars(substr($post['content'], 0, 100)) ?>...</p>

                        <div class="post-meta">
                            <span class="badge <?= $post['isPublished'] ? 'badge-success' : 'badge-warning' ?>">
                                <?= $post['isPublished'] ? 'Publié' : 'Brouillon' ?>
                            </span>

                            <?php if ($post['authorType'] === 'association'): ?>
                                <span class="badge badge-info">Association</span>
                            <?php elseif ($post['authorType'] === 'entreprise'): ?>
                                <span class="badge badge-secondary">Entreprise</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($post['association_name'] || $post['entreprise_name']): ?>
                            <p class="post-date">
                                <?= $post['association_name'] ? 'Association : ' . htmlspecialchars($post['association_name']) : '' ?>
                                <?= $post['entreprise_name'] ? 'Entreprise : ' . htmlspecialchars($post['entreprise_name']) : '' ?>
                            </p>
                        <?php endif; ?>

                        <div class="post-actions">
                            <a href="<?= HOME_URL ?>actu/<?= htmlspecialchars($post['uiid']) ?>?back=mes_posts"
                                class="btn btn-info linkNotDecorated" target="_blank">
                                Voir
                            </a>
                            <a href="<?= HOME_URL ?>post/modifier?uiid=<?= htmlspecialchars($post['uiid']) ?>&back=mes_posts"
                                class="btn linkNotDecorated">
                                Modifier
                            </a>
                            <form method="POST" action="<?= HOME_URL ?>post/supprimer">
                                <input type="hidden" name="uiid" value="<?= htmlspecialchars($post['uiid']) ?>">
                                <button type="submit" class="btn bg-danger" 
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette actualité ?')">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <?php include_once __DIR__ . '/../includes/pagination.php'; ?>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>