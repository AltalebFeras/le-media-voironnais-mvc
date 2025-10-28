<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-calendar-check me-2"></i>
                    Mes inscriptions
                </h1>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Statistics -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <h3 class="text-primary"><?= $total ?? 0 ?></h3>
                                <p class="mb-0">Total des inscriptions</p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (empty($inscriptions)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Vous n'êtes inscrit à aucun événement pour le moment.
                        <a href="<?= HOME_URL ?>evenements" class="alert-link">Découvrir les événements</a>
                    </div>
                <?php else: ?>
                    <!-- Registrations Grid -->
                    <div class="row g-4">
                        <?php foreach ($inscriptions as $inscription): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 shadow-sm">
                                    <?php if (!empty($inscription['bannerPath'])): ?>
                                        <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($inscription['bannerPath']) ?>"
                                            class="card-img-top"
                                            alt="<?= htmlspecialchars($inscription['title']) ?>"
                                            style="height: 200px; object-fit: cover;">
                                    <?php endif; ?>

                                    <div class="card-body">
                                        <!-- Status Badge -->
                                        <?php if ($inscription['status'] === 'inscrit'): ?>
                                            <span class="badge bg-success mb-2">
                                                <i class="fas fa-check-circle me-1"></i>Inscrit
                                            </span>
                                        <?php elseif ($inscription['status'] === 'liste_attente'): ?>
                                            <span class="badge bg-warning text-dark mb-2">
                                                <i class="fas fa-clock me-1"></i>Liste d'attente
                                            </span>
                                        <?php endif; ?>

                                        <h5 class="card-title">
                                            <?= htmlspecialchars($inscription['title']) ?>
                                        </h5>

                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?= date('d/m/Y à H:i', strtotime($inscription['startDate'])) ?>
                                            </small>
                                        </p>

                                        <?php if (!empty($inscription['ville_nom_reel'])): ?>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    <?= htmlspecialchars($inscription['ville_nom_reel']) ?>
                                                </small>
                                            </p>
                                        <?php endif; ?>

                                        <?php if (!empty($inscription['category_name'])): ?>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <i class="fas fa-tag me-1"></i>
                                                    <?= htmlspecialchars($inscription['category_name']) ?>
                                                </small>
                                            </p>
                                        <?php endif; ?>

                                        <p class="card-text">
                                            <small class="text-muted">
                                                Inscrit le <?= date('d/m/Y', strtotime($inscription['joinedAt'])) ?>
                                            </small>
                                        </p>
                                    </div>

                                    <div class="card-footer bg-transparent">
                                        <a href="<?= HOME_URL ?>evenements/<?= htmlspecialchars($inscription['ville_slug']) ?>/<?= htmlspecialchars($inscription['category_slug']) ?>/<?= htmlspecialchars($inscription['slug']) ?>"
                                            class="btn btn-primary btn-sm w-100">
                                            <i class="fas fa-eye me-1"></i>Voir l'événement
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php include_once __DIR__ . '/../includes/pagination.php'; ?>

                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>