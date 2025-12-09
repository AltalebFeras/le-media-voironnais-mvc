<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/entreprises/entreprise_list.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="p-0">
    <!-- Hero Section -->
    <div class="entreprises-hero">
        <div class="entreprises-hero-content">
            <h1 class="entreprises-hero-title">Nos Entreprises Partenaires</h1>
            <p class="entreprises-hero-subtitle">Découvrez les entreprises locales qui font vivre notre communauté
            </p>
        </div>

        <!-- Search Section -->
        <div class="entreprises-search-filter">
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Rechercher une entreprise..." id="searchInput">
                <button class="search-btn" type="button">
                    <span class="material-icons">search</span>
                </button>
            </div>
        </div>
    </div>
    <?php include_once __DIR__ . '/../includes/messages.php'; ?>

    <!-- Entreprises Section -->
    <div class="entreprises-section">
        <?php if (!empty($entreprises)): ?>
            <div class="entreprise-public-grid">
                <?php foreach ($entreprises as $entreprise): ?>
                    <div class="entreprise-public-card">
                        <!-- Card Banner -->
                        <div class="entreprise-card-banner">
                            <?php if ($entreprise['bannerPath']): ?>
                                <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($entreprise['bannerPath']) ?>"
                                    alt="Bannière <?= htmlspecialchars($entreprise['name']) ?>">
                            <?php endif; ?>

                            <?php if ($entreprise['isPartner']): ?>
                                <span class="partner-badge-overlay">
                                    <span class="material-icons">star</span>
                                    Partenaire
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body">
                            <div class="entreprise-logo-wrapper">
                                <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($entreprise['logoPath']) ?>"
                                    alt="Logo <?= htmlspecialchars($entreprise['name']) ?>"
                                    class="entreprise-logo-image">

                                <div class="entreprise-info">
                                    <h3 class="entreprise-name">
                                        <a href="<?= HOME_URL . 'entreprises/' . htmlspecialchars($entreprise['slug']) ?>">
                                            <?= htmlspecialchars($entreprise['name']) ?>
                                        </a>
                                    </h3>
                                    <?php if ($entreprise['ville_nom_reel']): ?>
                                        <div class="entreprise-location">
                                            <span class="material-icons">location_on</span>
                                            <?= htmlspecialchars($entreprise['ville_nom_reel']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ($entreprise['description']): ?>
                                <p class="entreprise-description">
                                    <?= htmlspecialchars(substr($entreprise['description'], 0, 120)) ?>
                                    <?= strlen($entreprise['description']) > 120 ? '...' : '' ?>
                                </p>
                            <?php endif; ?>

                            <div class="entreprise-actions-row">
                                <a href="<?= HOME_URL . 'entreprises/' . htmlspecialchars($entreprise['slug']) ?>"
                                    class="btn-view-entreprise linkNotDecorated">
                                    <span class="material-icons">visibility</span>
                                    Voir l'entreprise
                                </a>

                                <div class="entreprise-quick-actions">
                                    <?php if ($entreprise['email']): ?>
                                        <button class="quick-action-btn" title="Contacter"
                                            onclick="window.location.href='mailto:<?= htmlspecialchars($entreprise['email']) ?>'">
                                            <span class="material-icons">email</span>
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($entreprise['phone']): ?>
                                        <button class="quick-action-btn" title="Appeler"
                                            onclick="window.location.href='tel:<?= htmlspecialchars($entreprise['phone']) ?>'">
                                            <span class="material-icons">phone</span>
                                        </button>
                                    <?php endif; ?>

                                    <button class="quick-action-btn" title="Partager">
                                        <span class="material-icons">share</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <?php if ($entreprise['isPartner']): ?>
                            <div class="card-footer">
                                <small>
                                    <span class="material-icons">star</span>
                                    Entreprise partenaire
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php include_once __DIR__ . '/../includes/pagination.php'; ?>

        <?php else: ?>
            <div class="empty-state">
                <span class="material-icons">business</span>
                <h4>Aucune entreprise trouvée</h4>
                <p>Il n'y a actuellement aucune entreprise enregistrée.</p>
                <p>Revenez plus tard pour découvrir nos partenaires locaux.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
    // Simple search functionality
    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.entreprise-public-card');

        cards.forEach(card => {
            const name = card.querySelector('.entreprise-name')?.textContent.toLowerCase() || '';
            const description = card.querySelector('.entreprise-description')?.textContent.toLowerCase() || '';
            const location = card.querySelector('.entreprise-location')?.textContent.toLowerCase() || '';

            if (name.includes(searchTerm) || description.includes(searchTerm) || location.includes(searchTerm)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>