<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<?php
$upcomingEvents = $upcomingEvents ?? [];
$recentEvents = $recentEvents ?? [];
$enterprises = $enterprises ?? [];
$associations = $associations ?? [];
$featuredCities = $featuredCities ?? [];
$stats = $stats ?? ['totalEvents' => 0, 'totalEnterprises' => 0, 'totalAssociations' => 0, 'totalCities' => 0];
?>

<link rel="stylesheet" href="<?= BASE_URL . HOME_URL ?>assets/css/home/accueil.css">

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <h1 class="hero-title">Bienvenue sur Le Média Voironnais</h1>
        <p class="hero-subtitle">Découvrez les événements, entreprises et associations de votre région</p>
        <div class="hero-search">
            <input type="text" id="search-input-hero" placeholder="Rechercher un événement, une entreprise, une ville..." class="hero-search-input">
            <div id="search-results-hero" class="search-results"></div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-number"><?= number_format($stats['totalEvents']) ?></div>
                <div class="stat-label">Événements</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🏢</div>
                <div class="stat-number"><?= number_format($stats['totalEnterprises']) ?></div>
                <div class="stat-label">Entreprises</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🏛️</div>
                <div class="stat-number"><?= number_format($stats['totalAssociations']) ?></div>
                <div class="stat-label">Associations</div>
            </div>
        </div>
    </div>
</section>

<!-- Upcoming Events Section -->
<?php if (!empty($upcomingEvents)): ?>
    <section class="content-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Événements à venir</h2>
                <a href="<?= HOME_URL ?>evenements" class="section-link">Voir tous les événements →</a>
            </div>
            <div class="cards-grid">
                <?php foreach ($upcomingEvents as $event): ?>
                    <div class="event-card">
                        <div class="card-image">
                            <img src="<?= BASE_URL . HOME_URL . ($event['bannerPath'] ?? 'assets/images/uploads/banners/default_banner.png') ?>"
                                alt="<?= htmlspecialchars($event['title']) ?>">
                            <div class="card-badge"><?= date('d M', strtotime($event['startDate'])) ?></div>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?= htmlspecialchars($event['title']) ?></h3>
                            <p class="card-meta">
                                <span>📍 <?= htmlspecialchars($event['ville_nom_reel'] ?? 'Ville') ?></span>
                                <span>🏷️ <?= htmlspecialchars($event['category_name'] ?? 'Catégorie') ?></span>
                            </p>
                            <p class="card-description"><?= htmlspecialchars(substr($event['shortDescription'] ?? $event['description'], 0, 100)) ?>...</p>
                            <a href="<?= HOME_URL ?>evenements/<?= $event['ville_slug'] ?>/<?= $event['category_slug'] ?>/<?= $event['slug'] ?>" class="card-link">En savoir plus →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Recent Events Section -->
<?php if (!empty($recentEvents)): ?>
    <section class="content-section alt-bg">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Événements récents</h2>
                <a href="<?= HOME_URL ?>evenements" class="section-link">Tous les événements →</a>
            </div>
            <div class="cards-grid">
                <?php foreach ($recentEvents as $event): ?>
                    <div class="event-card">
                        <div class="card-image">
                            <img src="<?= BASE_URL . HOME_URL . ($event['bannerPath'] ?? 'assets/images/uploads/banners/default_banner.png') ?>"
                                alt="<?= htmlspecialchars($event['title']) ?>">
                            <div class="card-badge past">Terminé</div>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?= htmlspecialchars($event['title']) ?></h3>
                            <p class="card-meta">
                                <span>📍 <?= htmlspecialchars($event['ville_nom_reel'] ?? 'Ville') ?></span>
                                <span>🏷️ <?= htmlspecialchars($event['category_name'] ?? 'Catégorie') ?></span>
                            </p>
                            <p class="card-description"><?= htmlspecialchars(substr($event['shortDescription'] ?? $event['description'], 0, 100)) ?>...</p>
                            <a href="<?= HOME_URL ?>evenements/<?= $event['ville_slug'] ?>/<?= $event['category_slug'] ?>/<?= $event['slug'] ?>" class="card-link">Voir les détails →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Enterprises Section -->
<?php if (!empty($enterprises)): ?>
    <section class="content-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Entreprises partenaires</h2>
                <a href="<?= HOME_URL ?>entreprises" class="section-link">Voir toutes les entreprises →</a>
            </div>
            <div class="cards-grid">
                <?php foreach ($enterprises as $entreprise): ?>
                    <div class="entity-card">
                        <div class="entity-logo">
                            <img src="<?= BASE_URL . HOME_URL . ($entreprise['logoPath'] ?? 'assets/images/uploads/logos/default_logo.png') ?>"
                                alt="<?= htmlspecialchars($entreprise['name']) ?>">
                        </div>
                        <div class="entity-content">
                            <h3 class="entity-title"><?= htmlspecialchars($entreprise['name']) ?></h3>
                            <p class="entity-meta">📍 <?= htmlspecialchars($entreprise['ville_nom_reel'] ?? 'Ville') ?></p>
                            <p class="entity-description"><?= htmlspecialchars(substr($entreprise['description'], 0, 80)) ?>...</p>
                            <a href="<?= HOME_URL ?>entreprises/<?= $entreprise['slug'] ?>" class="entity-link">Découvrir →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Associations Section -->
<?php if (!empty($associations)): ?>
    <section class="content-section alt-bg">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Associations actives</h2>
                <a href="<?= HOME_URL ?>associations" class="section-link">Voir toutes les associations →</a>
            </div>
            <div class="cards-grid">
                <?php foreach ($associations as $association): ?>
                    <div class="entity-card">
                        <div class="entity-logo">
                            <img src="<?= BASE_URL . HOME_URL . ($association['logoPath'] ?? 'assets/images/uploads/logos/default_logo.png') ?>"
                                alt="<?= htmlspecialchars($association['name']) ?>">
                        </div>
                        <div class="entity-content">
                            <h3 class="entity-title"><?= htmlspecialchars($association['name']) ?></h3>
                            <p class="entity-meta">📍 <?= htmlspecialchars($association['ville_nom_reel'] ?? 'Ville') ?></p>
                            <p class="entity-description"><?= htmlspecialchars(substr($association['description'], 0, 80)) ?>...</p>
                            <a href="<?= HOME_URL ?>associations/<?= $association['slug'] ?>" class="entity-link">Découvrir →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<script>
    // Initialize search for hero section
    $(document).ready(function() {
        setupSearch('search-input-hero', 'search-results-hero');
    });
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>