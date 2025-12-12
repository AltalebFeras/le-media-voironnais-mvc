<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <div class="page-header">
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <?php 
        $backUrl = HOME_URL . 'actu';
        if (isset($_GET['back'])) {
            $backUrl = $_GET['back'] === 'mes_posts' ? HOME_URL . 'mes_posts' : HOME_URL . 'actu';
        }
        ?>
        <a href="<?= $backUrl ?>" class="linkNotDecorated">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
        </a>
    </div>

    <?php include_once __DIR__ . '/../includes/messages.php'; ?>

    <div class="post-detail-container">
        <div class="post-detail-main">
            <?php if ($post['imagePath']): ?>
                <div class="post-detail-image">
                    <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['imagePath']) ?>"
                        alt="<?= htmlspecialchars($post['title']) ?>">
                </div>
            <?php endif; ?>

            <div class="post-detail-header">
                <div class="post-detail-author">
                    <?php if ($post['authorType'] === 'user'): ?>
                        <img class="post-detail-author-avatar"
                            src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['user_avatar'] ?? 'assets/images/uploads/avatars/default_avatar.png') ?>"
                            alt="Avatar">
                        <div class="post-detail-author-info">
                            <h3><?= htmlspecialchars($post['user_firstName'] . ' ' . $post['user_lastName']) ?></h3>
                            <p class="post-date"><?= date('d/m/Y à H:i', strtotime($post['createdAt'])) ?></p>
                        </div>
                    <?php elseif ($post['authorType'] === 'association'): ?>
                        <img class="post-detail-author-avatar"
                            src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['association_logo']) ?>"
                            alt="Logo">
                        <div class="post-detail-author-info">
                            <h3><?= htmlspecialchars($post['association_name']) ?></h3>
                            <p class="post-date"><?= date('d/m/Y à H:i', strtotime($post['createdAt'])) ?></p>
                        </div>
                    <?php else: ?>
                        <img class="post-detail-author-avatar"
                            src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['entreprise_logo']) ?>"
                            alt="Logo">
                        <div class="post-detail-author-info">
                            <h3><?= htmlspecialchars($post['entreprise_name']) ?></h3>
                            <p class="post-date"><?= date('d/m/Y à H:i', strtotime($post['createdAt'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="post-detail-content">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
            </div>
        </div>

        <div class="post-detail-sidebar">
            <div class="post-info-box">
                <h3>Informations</h3>
                <dl>
                    <dt>Publié le</dt>
                    <dd><?= date('d/m/Y à H:i', strtotime($post['createdAt'])) ?></dd>

                    <?php if ($post['updatedAt']): ?>
                        <dt>Modifié le</dt>
                        <dd><?= date('d/m/Y à H:i', strtotime($post['updatedAt'])) ?></dd>
                    <?php endif; ?>

                    <dt>Type d'auteur</dt>
                    <dd>
                        <?php
                        $types = [
                            'user' => 'Utilisateur',
                            'association' => 'Association',
                            'entreprise' => 'Entreprise'
                        ];
                        echo $types[$post['authorType']] ?? 'Inconnu';
                        ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>