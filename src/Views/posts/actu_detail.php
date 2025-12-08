<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <div class="flex-row justify-content-between mb">
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <a href="<?= HOME_URL ?>actu" class="">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
        </a>
    </div>

    <?php include_once __DIR__ . '/../includes/messages.php'; ?>

    <div class="flex-row" style="gap: 20px;">
        <!-- Main Content -->
        <div class="max-width-66">
            <?php if ($post['imagePath']): ?>
                <div style="width: 100%; height: 400px; overflow: hidden; border-radius: 12px; margin-bottom: 2rem;">
                    <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['imagePath']) ?>" 
                         alt="<?= htmlspecialchars($post['title']) ?>" 
                         style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="p-3">
                    <div class="flex-row align-items-center mb">
                        <?php if ($post['authorType'] === 'user'): ?>
                            <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['user_avatar'] ?? 'assets/images/uploads/avatars/default_avatar.png') ?>" 
                                 alt="Avatar" style="width: 48px; height: 48px; border-radius: 50%; margin-right: 1rem;">
                            <div>
                                <p><strong><?= htmlspecialchars($post['user_firstName'] . ' ' . $post['user_lastName']) ?></strong></p>
                                <p><small class="text-muted"><?= date('d/m/Y à H:i', strtotime($post['createdAt'])) ?></small></p>
                            </div>
                        <?php elseif ($post['authorType'] === 'association'): ?>
                            <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['association_logo']) ?>" 
                                 alt="Logo" style="width: 48px; height: 48px; border-radius: 50%; margin-right: 1rem;">
                            <div>
                                <p><strong><?= htmlspecialchars($post['association_name']) ?></strong></p>
                                <p><small class="text-muted"><?= date('d/m/Y à H:i', strtotime($post['createdAt'])) ?></small></p>
                            </div>
                        <?php else: ?>
                            <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['entreprise_logo']) ?>" 
                                 alt="Logo" style="width: 48px; height: 48px; border-radius: 50%; margin-right: 1rem;">
                            <div>
                                <p><strong><?= htmlspecialchars($post['entreprise_name']) ?></strong></p>
                                <p><small class="text-muted"><?= date('d/m/Y à H:i', strtotime($post['createdAt'])) ?></small></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="p-3">
                    <div style="line-height: 1.8; white-space: pre-wrap;">
                        <?= nl2br(htmlspecialchars($post['content'])) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="max-width-33">
            <div class="card">
                <div class="p-3">
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
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
