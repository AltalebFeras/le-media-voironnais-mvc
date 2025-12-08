<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <div class="flex-row justify-content-between mb">
        <h1><?= htmlspecialchars($title ?? 'Actualités') ?></h1>
    </div>

    <?php include_once __DIR__ . '/../includes/messages.php'; ?>

    <!-- Filter buttons -->
    <div class="flex-row mb">
        <a href="<?= HOME_URL ?>actu" class="btn <?= !$filter ? '' : 'btn-outline' ?>">
            Toutes
        </a>
        <a href="<?= HOME_URL ?>actu?filter=user" class="btn <?= $filter === 'user' ? '' : 'btn-outline' ?>">
            Utilisateurs
        </a>
        <a href="<?= HOME_URL ?>actu?filter=association" class="btn <?= $filter === 'association' ? '' : 'btn-outline' ?>">
            Associations
        </a>
        <a href="<?= HOME_URL ?>actu?filter=entreprise" class="btn <?= $filter === 'entreprise' ? '' : 'btn-outline' ?>">
            Entreprises
        </a>
    </div>

    <?php if (empty($posts)): ?>
        <div class="custom-alert custom-alert-info">
            <p>Aucune actualité disponible pour le moment.</p>
        </div>
    <?php else: ?>
        <!-- Posts grid -->
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

                        <div class="flex-row align-items-center mb">
                            <?php if ($post['authorType'] === 'user'): ?>
                                <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['user_avatar'] ?? 'assets/images/uploads/avatars/default_avatar.png') ?>" 
                                     alt="Avatar" style="width: 32px; height: 32px; border-radius: 50%; margin-right: 0.5rem;">
                                <small><?= htmlspecialchars($post['user_firstName'] . ' ' . $post['user_lastName']) ?></small>
                            <?php elseif ($post['authorType'] === 'association'): ?>
                                <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['association_logo']) ?>" 
                                     alt="Logo" style="width: 32px; height: 32px; border-radius: 50%; margin-right: 0.5rem;">
                                <small><?= htmlspecialchars($post['association_name']) ?></small>
                            <?php else: ?>
                                <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['entreprise_logo']) ?>" 
                                     alt="Logo" style="width: 32px; height: 32px; border-radius: 50%; margin-right: 0.5rem;">
                                <small><?= htmlspecialchars($post['entreprise_name']) ?></small>
                            <?php endif; ?>
                        </div>

                        <h5><?= htmlspecialchars($post['title']) ?></h5>
                        <p><?= htmlspecialchars(substr($post['content'], 0, 150)) ?>...</p>
                        
                        <p><small class="text-muted">Publié le : <?= date('d/m/Y', strtotime($post['createdAt'])) ?></small></p>

                        <div class="flex-row justify-content-between mt">
                            <a href="<?= HOME_URL ?>actu/<?= htmlspecialchars($post['uiid']) ?>" class="btn linkNotDecorated">
                                Lire la suite
                            </a>
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
