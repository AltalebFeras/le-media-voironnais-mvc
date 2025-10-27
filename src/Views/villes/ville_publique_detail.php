<?php
$title = htmlspecialchars($ville['ville_nom']);
$description = 'Découvrez ' . $ville['ville_nom'] . ' (' . $ville['ville_code_postal'] . ') - événements, entreprises et associations locales';
?>

<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- City Header -->
            <div class="city-header mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="h2 mb-2">
                                    <i class="material-icons text-primary me-2" style="font-size: 2rem;">location_city</i>
                                    <?= htmlspecialchars($ville['ville_nom']) ?> 
                                    <small class="text-muted">(<?= htmlspecialchars($ville['ville_code_postal']) ?>)</small>
                                </h1>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <small class="text-muted">
                                            <i class="material-icons me-1" style="font-size: 16px;">map</i>
                                            Département: <?= htmlspecialchars($ville['ville_departement']) ?>
                                        </small>
                                    </div>
                                    <?php if ($ville['ville_population_2012']): ?>
                                        <div class="col-sm-6">
                                            <small class="text-muted">
                                                <i class="material-icons me-1" style="font-size: 16px;">people</i>
                                                Population: <?= number_format($ville['ville_population_2012']) ?> hab.
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <?php if ($ville['ville_longitude_deg'] && $ville['ville_latitude_deg']): ?>
                                    <a href="https://www.google.com/maps/search/?api=1&query=<?= $ville['ville_latitude_deg'] ?>,<?= $ville['ville_longitude_deg'] ?>" 
                                       target="_blank" 
                                       class="btn btn-outline-primary">
                                        <i class="material-icons me-2">map</i>
                                        Voir sur la carte
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Events Section -->
                <?php if (!empty($events)): ?>
                    <div class="col-12 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">
                                    <i class="material-icons me-2">event</i>
                                    Événements à <?= htmlspecialchars($ville['ville_nom']) ?>
                                </h4>
                                <a href="<?= HOME_URL . 'evenements?ville=' . $ville['ville_slug'] ?>" class="btn btn-outline-primary btn-sm">
                                    Voir tous
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach (array_slice($events, 0, 6) as $event): ?>
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100">
                                                <?php if ($event['bannerPath']): ?>
                                                    <img src="<?= htmlspecialchars($event['bannerPath']) ?>" 
                                                         class="card-img-top" 
                                                         alt="<?= htmlspecialchars($event['title']) ?>"
                                                         style="height: 150px; object-fit: cover;">
                                                <?php endif; ?>
                                                <div class="card-body">
                                                    <h6 class="card-title">
                                                        <a href="<?= HOME_URL . 'evenements/' . $event['ville_slug'] . '/'  . $event['category_slug'] . '/' . $event['slug'] ?>" class="text-decoration-none">
                                                            <?= htmlspecialchars($event['title']) ?>
                                                        </a>
                                                    </h6>
                                                    <small class="text-muted">
                                                        <i class="material-icons me-1" style="font-size: 14px;">schedule</i>
                                                        <?= date('d/m/Y', strtotime($event['startDate'])) ?>
                                                    </small>
                                                    <?php if ($event['price'] > 0): ?>
                                                        <div class="mt-2">
                                                            <span class="badge bg-primary"><?= number_format($event['price'], 2) ?> €</span>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="mt-2">
                                                            <span class="badge bg-success">Gratuit</span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Enterprises Section -->
                <?php if (!empty($entreprises)): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">
                                    <i class="material-icons me-2">business</i>
                                    Entreprises locales
                                </h4>
                                <a href="<?= HOME_URL . 'entreprises?ville=' . $ville['ville_slug'] ?>" class="btn btn-outline-primary btn-sm">
                                    Voir toutes
                                </a>
                            </div>
                            <div class="card-body">
                                <?php foreach (array_slice($entreprises, 0, 5) as $entreprise): ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="<?= $entreprise['logoPath'] ?? DOMAIN . HOME_URL . 'assets/images/default-company.png' ?>" 
                                             alt="<?= htmlspecialchars($entreprise['name']) ?>" 
                                             class="rounded me-3" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="<?= HOME_URL . 'entreprises/' . $entreprise['slug'] ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($entreprise['name']) ?>
                                                </a>
                                            </h6>
                                            <?php if ($entreprise['isPartner']): ?>
                                                <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">Partenaire</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Associations Section -->
                <?php if (!empty($associations)): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">
                                    <i class="material-icons me-2">groups</i>
                                    Associations
                                </h4>
                                <a href="<?= HOME_URL . 'associations?ville=' . $ville['ville_slug'] ?>" class="btn btn-outline-primary btn-sm">
                                    Voir toutes
                                </a>
                            </div>
                            <div class="card-body">
                                <?php foreach (array_slice($associations, 0, 5) as $association): ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="<?= $association['logoPath'] ?? DOMAIN . HOME_URL . 'assets/images/default-association.png' ?>" 
                                             alt="<?= htmlspecialchars($association['name']) ?>" 
                                             class="rounded me-3" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="<?= HOME_URL . 'associations/' . $association['slug'] ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($association['name']) ?>
                                                </a>
                                            </h6>
                                            <small class="text-muted">Association</small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Statistics -->
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4 class="mb-0">
                                <i class="material-icons me-2">analytics</i>
                                Statistiques de <?= htmlspecialchars($ville['ville_nom']) ?>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body">
                                            <h3 class="mb-0"><?= count($events ?? []) ?></h3>
                                            <small>Événements</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <h3 class="mb-0"><?= count($entreprises ?? []) ?></h3>
                                            <small>Entreprises</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body">
                                            <h3 class="mb-0"><?= count($associations ?? []) ?></h3>
                                            <small>Associations</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-warning text-dark">
                                        <div class="card-body">
                                            <h3 class="mb-0"><?= $ville['ville_population_2012'] ? number_format($ville['ville_population_2012']) : 'N/A' ?></h3>
                                            <small>Habitants</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
