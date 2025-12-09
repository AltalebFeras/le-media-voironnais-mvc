<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/villes/villes.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <div class="villes-container">
        <div class="villes-header">
            <h1>
                <i class="material-icons">location_city</i>
                Toutes les villes
            </h1>
        </div>

        <div class="villes-grid">
            <?php foreach ($villes as $ville): ?>
                <div class="ville-card">
                    <div class="ville-card-body">
                        <h5 class="ville-card-title">
                            <a href="<?= HOME_URL . 'villes/' . htmlspecialchars($ville['ville_slug']) ?>">
                                <?= htmlspecialchars($ville['ville_nom_reel']) ?>
                            </a>
                        </h5>
                        <div class="ville-info">
                            <div class="ville-info-item">
                                <i class="material-icons">markunread_mailbox</i>
                                <span><?= htmlspecialchars($ville['ville_code_postal']) ?></span>
                            </div>
                            <?php if ($ville['population']): ?>
                                <div class="ville-info-item">
                                    <i class="material-icons">people</i>
                                    <span><?= number_format($ville['population']) ?> hab.</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- pagination -->
        <?php include_once __DIR__ . '/../includes/pagination.php'; ?>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>