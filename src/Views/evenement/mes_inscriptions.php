<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <div class="container-custom">
        <div class="page-header">
            <h1>
                <span class="material-icons" style="vertical-align: middle; margin-right: 0.5rem;">event_available</span>
                Mes inscriptions
            </h1>
        </div>

        <?php include_once __DIR__ . '/../includes/messages.php'; ?>

        <!-- Statistics -->
        <div class="stats-container mb">
            <div class="stat-card">
                <h3 class="mb-0">Total des inscriptions : <span class="text-success "><?= $totalInscriptions ?? 0 ?></span></h3>
            </div>
        </div>

        <?php if (empty($inscriptions)): ?>
            <div class="custom-alert custom-alert-info">
                <p>
                    <span class="material-icons" style="vertical-align: middle; margin-right: 0.5rem;">info</span>
                    Vous n'êtes inscrit à aucun événement pour le moment.
                    <a href="<?= HOME_URL ?>evenements" class="link">Découvrir les événements</a>
                </p>
            </div>
        <?php else: ?>
            <!-- Registrations Grid -->
            <div class="events-grid">
                <?php foreach ($inscriptions as $inscription): ?>
                    <div class="event-card-wrapper">
                        <div class="card">
                            <?php if (!empty($inscription['bannerPath'])): ?>
                                <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($inscription['bannerPath']) ?>"
                                    alt="<?= htmlspecialchars($inscription['title']) ?>">
                            <?php endif; ?>

                            <div class="card-content">
                                <!-- Status Badge -->
                                <?php if ($inscription['status'] === 'inscrit'): ?>
                                    <span class="badge-success">
                                        <span class="material-icons" style="font-size: 14px; vertical-align: middle;">check_circle</span>
                                        Inscrit
                                    </span>
                                <?php elseif ($inscription['status'] === 'liste_attente'): ?>
                                    <span class="badge-warning">
                                        <span class="material-icons" style="font-size: 14px; vertical-align: middle;">schedule</span>
                                        Liste d'attente
                                    </span>
                                <?php endif; ?>

                                <h5 class="mt">
                                    <?= htmlspecialchars($inscription['title']) ?>
                                </h5>

                                <p class="text-muted">
                                    <span class="material-icons" style="font-size: 16px; vertical-align: middle; margin-right: 0.25rem;">event</span>
                                    <?= date('d/m/Y à H:i', strtotime($inscription['startDate'])) ?>
                                </p>

                                <?php if (!empty($inscription['ville_nom_reel'])): ?>
                                    <p class="text-muted">
                                        <span class="material-icons" style="font-size: 16px; vertical-align: middle; margin-right: 0.25rem;">location_on</span>
                                        <?= htmlspecialchars($inscription['ville_nom_reel']) ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($inscription['category_name'])): ?>
                                    <p class="text-muted">
                                        <span class="material-icons" style="font-size: 16px; vertical-align: middle; margin-right: 0.25rem;">label</span>
                                        <?= htmlspecialchars($inscription['category_name']) ?>
                                    </p>
                                <?php endif; ?>

                                <p class="text-muted">
                                    Inscrit le <?= date('d/m/Y', strtotime($inscription['joinedAt'])) ?>
                                </p>
                            </div>

                            <div class="card-footer">
                                <a href="<?= HOME_URL ?>evenements/<?= htmlspecialchars($inscription['ville_slug']) ?>/<?= htmlspecialchars($inscription['category_slug']) ?>/<?= htmlspecialchars($inscription['slug']) ?>"
                                    class="btn btn-primary full-width linkNotDecorated">
                                    <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 0.25rem;">visibility</span>
                                    Voir
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
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>