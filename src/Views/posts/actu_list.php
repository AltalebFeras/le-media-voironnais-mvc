<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <div class="page-header">
        <h1><?= htmlspecialchars($title ?? 'Actualités') ?></h1>
    </div>

    <?php include_once __DIR__ . '/../includes/messages.php'; ?>

    <div class="filter-buttons">
        <a href="<?= HOME_URL ?>actu" class="btn-filter <?= !$filter ? 'active' : '' ?> linkNotDecorated">
            Toutes
        </a>
        <a href="<?= HOME_URL ?>actu?filter=user" class="btn-filter <?= $filter === 'user' ? 'active' : '' ?> linkNotDecorated">
            Utilisateurs
        </a>
        <a href="<?= HOME_URL ?>actu?filter=association" class="btn-filter <?= $filter === 'association' ? 'active' : '' ?> linkNotDecorated">
            Associations
        </a>
        <a href="<?= HOME_URL ?>actu?filter=entreprise" class="btn-filter <?= $filter === 'entreprise' ? 'active' : '' ?> linkNotDecorated">
            Entreprises
        </a>
    </div>

    <?php if (empty($posts)): ?>
        <div class="posts-empty-state">
            <span class="material-icons">newspaper</span>
            <p>Aucune actualité disponible pour le moment.</p>
        </div>
    <?php else: ?>
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
                        <div class="post-author">
                            <?php if ($post['authorType'] === 'user'): ?>
                                <img class="post-author-avatar" 
                                    src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['user_avatar'] ?? 'assets/images/uploads/avatars/default_avatar.png') ?>"
                                    alt="Avatar">
                                <span class="post-author-name"><?= htmlspecialchars($post['user_firstName'] . ' ' . $post['user_lastName']) ?></span>
                            <?php elseif ($post['authorType'] === 'association'): ?>
                                <img class="post-author-avatar"
                                    src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['association_logo']) ?>"
                                    alt="Logo">
                                <span class="post-author-name"><?= htmlspecialchars($post['association_name']) ?></span>
                            <?php else: ?>
                                <img class="post-author-avatar"
                                    src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['entreprise_logo']) ?>"
                                    alt="Logo">
                                <span class="post-author-name"><?= htmlspecialchars($post['entreprise_name']) ?></span>
                            <?php endif; ?>
                        </div>

                        <h5 class="post-title"><?= htmlspecialchars($post['title']) ?></h5>
                        <p class="post-excerpt"><?= htmlspecialchars(substr($post['content'], 0, 150)) ?>...</p>

                        <p class="post-date">Publié le : <?= date('d/m/Y', strtotime($post['createdAt'])) ?></p>

                        <div class="post-actions">
                            <a href="<?= HOME_URL ?>actu/<?= htmlspecialchars($post['uiid']) ?>?back=actu" 
                                class="btn linkNotDecorated">
                                Lire la suite
                            </a>
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