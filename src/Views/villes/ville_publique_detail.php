<?php
$title = htmlspecialchars($ville['ville_nom']);
$description = 'Découvrez ' . $ville['ville_nom'] . ' (' . $ville['ville_code_postal'] . ') - événements, entreprises et associations locales';
?>
<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/villes/villes.css' ?>">
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/villes/ville_detail.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <div class="villes-container">
        <!-- City Header -->
        <div class="ville-header-section">
            <div class="ville-header-card">
                <div class="ville-header-content">
                    <div class="ville-title-section">
                        <h1>
                            <i class="material-icons">location_city</i>
                            <?= htmlspecialchars($ville['ville_nom']) ?>
                            <span class="ville-postal-code">(<?= htmlspecialchars($ville['ville_code_postal']) ?>)</span>
                        </h1>
                        <div class="ville-meta-info">
                            <div class="ville-meta-item">
                                <i class="material-icons">map</i>
                                <span>Département: <?= htmlspecialchars($ville['ville_departement']) ?></span>
                            </div>
                            <?php if ($ville['ville_population_2012']): ?>
                                <div class="ville-meta-item">
                                    <i class="material-icons">people</i>
                                    <span>Population: <?= number_format($ville['ville_population_2012']) ?> hab.</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="ville-actions">
                        <a href="<?= HOME_URL . 'villes' ?>" class="">
                            <span class="material-icons btn" style="color:white;">arrow_back</span>
                        </a>
                        <?php if ($ville['ville_longitude_deg'] && $ville['ville_latitude_deg']): ?>
                            <a href="https://www.google.com/maps/search/?api=1&query=<?= $ville['ville_latitude_deg'] ?>,<?= $ville['ville_longitude_deg'] ?>"
                                target="_blank"
                                class="btn-map">
                                <i class="material-icons">map</i>
                                <span>Voir sur la carte</span>
                            </a>
                        <?php endif; ?>

                    </div>

                </div>
            </div>
        </div>

        <!-- Events Section -->
        <?php if (!empty($events)): ?>
            <div class="section-card">
                <div class="section-card-header">
                    <h4>
                        <i class="material-icons">event</i>
                        Événements à <?= htmlspecialchars($ville['ville_nom']) ?>
                    </h4>
                    <a href="<?= HOME_URL . 'evenements?ville=' . $ville['ville_slug'] ?>" class="btn-view-all">
                        Voir tous
                    </a>
                </div>
                <div class="section-card-body">
                    <div class="events-grid">
                        <?php foreach (array_slice($events, 0, 6) as $event): ?>
                            <div class="event-card">
                                <?php if ($event['bannerPath']): ?>
                                    <img src="<?= htmlspecialchars($event['bannerPath']) ?>"
                                        class="event-card-img"
                                        alt="<?= htmlspecialchars($event['title']) ?>">
                                <?php endif; ?>
                                <div class="event-card-body">
                                    <h6 class="event-card-title">
                                        <a href="<?= HOME_URL . 'evenements/' . $event['ville_slug'] . '/'  . $event['category_slug'] . '/' . $event['slug'] ?>">
                                            <?= htmlspecialchars($event['title']) ?>
                                        </a>
                                    </h6>
                                    <div class="event-card-date">
                                        <i class="material-icons">schedule</i>
                                        <?= date('d/m/Y', strtotime($event['startDate'])) ?>
                                    </div>
                                    <?php if ($event['price'] > 0): ?>
                                        <span class="event-badge paid"><?= number_format($event['price'], 2) ?> €</span>
                                    <?php else: ?>
                                        <span class="event-badge free">Gratuit</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Entities Row -->
        <div class="entities-row">
            <!-- Enterprises Section -->
            <?php if (!empty($entreprises)): ?>
                <div class="entities-section">
                    <div class="section-card">
                        <div class="section-card-header">
                            <h4>
                                <i class="material-icons">business</i>
                                Entreprises locales
                            </h4>
                            <a href="<?= HOME_URL . 'entreprises?ville=' . $ville['ville_slug'] ?>" class="btn-view-all">
                                Voir toutes
                            </a>
                        </div>
                        <div class="section-card-body">
                            <?php foreach (array_slice($entreprises, 0, 5) as $entreprise): ?>
                                <div class="entity-card">
                                    <img src="<?= $entreprise['logoPath'] ?? BASE_URL . HOME_URL . 'assets/images/default-company.png' ?>"
                                        alt="<?= htmlspecialchars($entreprise['name']) ?>"
                                        class="entity-logo">
                                    <div class="entity-info">
                                        <h6 class="entity-name">
                                            <a href="<?= HOME_URL . 'entreprises/' . $entreprise['slug'] ?>">
                                                <?= htmlspecialchars($entreprise['name']) ?>
                                            </a>
                                        </h6>
                                        <?php if ($entreprise['isPartner']): ?>
                                            <span class="entity-badge">Partenaire</span>
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
                <div class="entities-section">
                    <div class="section-card">
                        <div class="section-card-header">
                            <h4>
                                <i class="material-icons">groups</i>
                                Associations
                            </h4>
                            <a href="<?= HOME_URL . 'associations?ville=' . $ville['ville_slug'] ?>" class="btn-view-all">
                                Voir toutes
                            </a>
                        </div>
                        <div class="section-card-body">
                            <?php foreach (array_slice($associations, 0, 5) as $association): ?>
                                <div class="entity-card">
                                    <img src="<?= $association['logoPath'] ?? BASE_URL . HOME_URL . 'assets/images/default-association.png' ?>"
                                        alt="<?= htmlspecialchars($association['name']) ?>"
                                        class="entity-logo">
                                    <div class="entity-info">
                                        <h6 class="entity-name">
                                            <a href="<?= HOME_URL . 'associations/' . $association['slug'] ?>">
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
        </div>

        <!-- Statistics -->
        <div class="section-card">
            <div class="section-card-header">
                <h4>
                    <i class="material-icons">analytics</i>
                    Statistiques de <?= htmlspecialchars($ville['ville_nom']) ?>
                </h4>
            </div>
            <div class="section-card-body">
                <div class="statistics-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?= count($events ?? []) ?></div>
                        <div class="stat-label">Événements</div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-number"><?= count($entreprises ?? []) ?></div>
                        <div class="stat-label">Entreprises</div>
                    </div>
                    <div class="stat-card info">
                        <div class="stat-number"><?= count($associations ?? []) ?></div>
                        <div class="stat-label">Associations</div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-number"><?= $ville['ville_population_2012'] ? number_format($ville['ville_population_2012']) : 'N/A' ?></div>
                        <div class="stat-label">Habitants</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>