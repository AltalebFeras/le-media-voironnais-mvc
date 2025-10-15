<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/favoris.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="pt">
    <div class="container">
        <div class="flex-row justify-between align-center mb-4">
            <div>
                <h2>Mes favoris</h2>
                <p class="text-muted"><?= $total ?> événement<?= $total > 1 ? 's' : '' ?> favori<?= $total > 1 ? 's' : '' ?></p>
            </div>
            <a href="<?= HOME_URL ?>evenements" class="btn btn-outline">
                <span class="material-icons">arrow_back</span>
            </a>
        </div>

        <?php if (empty($favouriteEvents)): ?>
            <div class="empty-state text-center py-5">
                <span class="material-icons" style="font-size: 4rem; color: var(--gray-400);">favorite_border</span>
                <h3>Aucun événement favori</h3>
                <p class="text-muted mb-4">Vous n'avez pas encore ajouté d'événements à vos favoris.</p>
                <a href="<?= HOME_URL ?>evenements" class="btn btn-primary linkNotDecorated">
                    <span class="material-icons">explore</span>
                    Découvrir des événements
                </a>
            </div>
        <?php else: ?>
            <!-- Events Grid -->
            <div class="events-grid">
                <?php foreach ($favouriteEvents as $evenement): ?>
                    <div class="event-card">
                        <div class="event-image">
                            <img src="<?= DOMAIN . HOME_URL . $evenement['bannerPath'] ?>" 
                                 alt="<?= htmlspecialchars($evenement['title']) ?>" 
                                 loading="lazy">
                            <div class="event-category">
                                <?= htmlspecialchars($evenement['category_name']) ?>
                            </div>
                            <form method="POST" action="<?= HOME_URL ?>mes_favoris" class="remove-favorite-form">
                                <input type="hidden" name="eventUiid" value="<?= $evenement['uiid'] ?>">
                                <input type="hidden" name="action" value="remove_favorite">
                                <button type="submit" class="event-favourite active" title="Retirer des favoris">
                                    <span class="material-icons">favorite</span>
                                </button>
                            </form>
                        </div>
                        
                        <div class="event-content">
                            <div class="event-meta">
                                <span class="event-date">
                                    <span class="material-icons">event</span>
                                    <?= date('d/m/Y', strtotime($evenement['startDate'])) ?>
                                </span>
                                <span class="event-time">
                                    <?= date('H:i', strtotime($evenement['startDate'])) ?>
                                </span>
                            </div>
                            
                            <h3 class="event-title">
                                <a href="<?= HOME_URL ?>evenements/<?= $evenement['ville_slug'] ?>/<?= $evenement['category_slug'] ?>/<?= $evenement['slug'] ?>">
                                    <?= htmlspecialchars($evenement['title']) ?>
                                </a>
                            </h3>
                            
                            <p class="event-description">
                                <?= htmlspecialchars(substr($evenement['shortDescription'] ?? $evenement['description'], 0, 120)) ?>...
                            </p>
                            
                            <div class="event-location">
                                <span class="material-icons">location_on</span>
                                <?= htmlspecialchars($evenement['ville_nom_reel']) ?>
                            </div>
                            
                            <?php if ($evenement['association_name']): ?>
                                <div class="event-organizer">
                                    <span class="material-icons">groups</span>
                                    <?= htmlspecialchars($evenement['association_name']) ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="event-actions">
                                <a href="<?= HOME_URL ?>evenements/<?= $evenement['ville_slug'] ?>/<?= $evenement['category_slug'] ?>/<?= $evenement['slug'] ?>" 
                                   class="btn btn-primary">
                                    Voir détails
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav class="pagination-wrapper" aria-label="Navigation des pages">
                    <ul class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= HOME_URL ?>mes_favoris?page=<?= $currentPage - 1 ?>">
                                    <span class="material-icons">chevron_left</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                            <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= HOME_URL ?>mes_favoris?page=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= HOME_URL ?>mes_favoris?page=<?= $currentPage + 1 ?>">
                                    <span class="material-icons">chevron_right</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>