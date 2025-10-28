<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<main>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="h2 mb-4">
                    <i class="material-icons me-2">location_city</i>
                    Toutes les villes
                </h1>

                <div class="row">
                    <?php foreach ($villes as $ville): ?>
                        <div class="col-md-4 col-lg-3 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="<?= HOME_URL . 'villes/' . htmlspecialchars($ville['slug']) ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($ville['name']) ?>
                                        </a>
                                    </h5>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <i class="material-icons me-1" style="font-size: 16px;">markunread_mailbox</i>
                                            <?= htmlspecialchars($ville['code_postal']) ?>
                                        </small>
                                        <?php if ($ville['population']): ?>
                                            <br>
                                            <small class="text-muted">
                                                <i class="material-icons me-1" style="font-size: 16px;">people</i>
                                                <?= number_format($ville['population']) ?> hab.
                                            </small>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <!-- pagination -->
                    <?php include_once __DIR__ . '/../includes/pagination.php'; ?>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>