<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/associations/assoc_list.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="p-0">
    <!-- Hero Section -->
    <div class="associations-hero">
        <div class="associations-hero-content">
            <h1 class="associations-hero-title">Nos Associations</h1>
            <p class="associations-hero-subtitle">Découvrez les associations locales qui animent notre territoire
            </p>
        </div>

        <!-- Search Section -->
        <div class="associations-search-filter">
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Rechercher une association..." id="searchInput">
                <button class="search-btn" type="button">
                    <span class="material-icons">search</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Associations Section -->
    <div class="associations-section">
        <?php if (!empty($associations)): ?>
            <div class="association-public-grid">
                <?php foreach ($associations as $association): ?>
                    <div class="association-public-card">
                        <!-- Card Banner -->
                        <div class="association-card-banner">
                            <?php if ($association['bannerPath']): ?>
                                <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($association['bannerPath']) ?>"
                                    alt="Bannière <?= htmlspecialchars($association['name']) ?>">
                            <?php endif; ?>

                            <?php if ($association['isActive']): ?>
                                <span class="active-badge-overlay">
                                    <span class="material-icons">groups</span>
                                    Active
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body">
                            <div class="association-logo-wrapper">
                                <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($association['logoPath']) ?>"
                                    alt="Logo <?= htmlspecialchars($association['name']) ?>"
                                    class="association-logo-image">

                                <div class="association-info">
                                    <h3 class="association-name">
                                        <a href="<?= HOME_URL . 'associations/' . htmlspecialchars($association['slug']) ?>">
                                            <?= htmlspecialchars($association['name']) ?>
                                        </a>
                                    </h3>
                                    <?php if ($association['ville_nom_reel']): ?>
                                        <div class="association-location">
                                            <span class="material-icons">location_on</span>
                                            <?= htmlspecialchars($association['ville_nom_reel']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ($association['description']): ?>
                                <p class="association-description">
                                    <?= htmlspecialchars(substr($association['description'], 0, 120)) ?>
                                    <?= strlen($association['description']) > 120 ? '...' : '' ?>
                                </p>
                            <?php endif; ?>

                            <div class="association-actions-row">
                                <a href="<?= HOME_URL . 'associations/' . htmlspecialchars($association['slug']) ?>"
                                    class="btn-view-association linkNotDecorated">
                                    <span class="material-icons">visibility</span>
                                    Voir l'association
                                </a>

                                <div class="association-quick-actions">
                                    <?php if ($association['email']): ?>
                                        <button class="quick-action-btn"
                                            title="Contacter"
                                            onclick="window.location.href='mailto:<?= htmlspecialchars($association['email']) ?>'">
                                            <span class="material-icons">email</span>
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($association['phone']): ?>
                                        <button class="quick-action-btn"
                                            title="Appeler"
                                            onclick="window.location.href='tel:<?= htmlspecialchars($association['phone']) ?>'">
                                            <span class="material-icons">phone</span>
                                        </button>
                                    <?php endif; ?>

                                    <button class="quick-action-btn"
                                        title="Partager">
                                        <span class="material-icons">share</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer">
                            <small>
                                <span class="material-icons">groups</span>
                                Association active
                            </small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php include_once __DIR__ . '/../includes/pagination.php'; ?>

        <?php else: ?>
            <div class="empty-state">
                <span class="material-icons">groups</span>
                <h4>Aucune association trouvée</h4>
                <p>Il n'y a actuellement aucune association enregistrée.</p>
                <p>Revenez plus tard pour découvrir nos associations locales.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
// Simple search functionality
document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.association-public-card');

    cards.forEach(card => {
        const name = card.querySelector('.association-name')?.textContent.toLowerCase() || '';
        const description = card.querySelector('.association-description')?.textContent.toLowerCase() || '';
        const location = card.querySelector('.association-location')?.textContent.toLowerCase() || '';

        if (name.includes(searchTerm) || description.includes(searchTerm) || location.includes(searchTerm)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>