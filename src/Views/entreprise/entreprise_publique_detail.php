<?php
$title = htmlspecialchars($entreprise['name']);
$description = $entreprise['description'] ? substr(strip_tags($entreprise['description']), 0, 160) : 'Entreprise sur Le Média Voironnais';
?>

<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/entreprises/entreprise_publique_detail.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="entreprise-detail-page p-0">
    <!-- Enterprise Hero Banner -->
    <div class="enterprise-hero">
        <?php if ($entreprise['bannerPath']): ?>
            <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($entreprise['bannerPath']) ?>"
                alt="Bannière <?= htmlspecialchars($entreprise['name']) ?>"
                class="enterprise-hero-image">
        <?php endif; ?>

        <div class="enterprise-hero-overlay">
            <div class="enterprise-hero-content">
                <div class="entreprise-header-info">
                    <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($entreprise['logoPath']) ?>"
                        alt="Logo <?= htmlspecialchars($entreprise['name']) ?>"
                        class="entreprise-logo-large">

                    <div class="entreprise-title-section">
                        <h1><?= htmlspecialchars($entreprise['name']) ?></h1>
                        <?php if ($entreprise['isPartner']): ?>
                            <span class="partner-badge">
                                <span class="material-icons">star</span>
                                Partenaire
                            </span>
                        <?php endif; ?>
                        <?php if ($entreprise['ville_nom_reel']): ?>
                            <div class="location-info">
                                <span class="material-icons">location_on</span>
                                <?= htmlspecialchars($entreprise['ville_nom_reel']) ?>
                            </div>
                        <?php endif; ?>
                        <div class="member-since">
                            Membre depuis <?= date('M Y', strtotime($entreprise['createdAt'])) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="enterprise-content">
        <div class="enterprise-container">
            <!-- Left Column - Main Details -->
            <div>
                <!-- Contact Information -->
                <div class="detail-card">
                    <h3>Coordonnées</h3>
                    <div class="contact-grid">
                        <?php if ($entreprise['address']): ?>
                            <div class="contact-item">
                                <span class="material-icons">location_on</span>
                                <div class="contact-item-content">
                                    <strong>Adresse</strong>
                                    <?= htmlspecialchars($entreprise['address']) ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($entreprise['phone']): ?>
                            <div class="contact-item">
                                <span class="material-icons">phone</span>
                                <div class="contact-item-content">
                                    <strong>Téléphone</strong>
                                    <a href="tel:<?= htmlspecialchars($entreprise['phone']) ?>">
                                        <?= htmlspecialchars($entreprise['phone']) ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($entreprise['email']): ?>
                            <div class="contact-item">
                                <span class="material-icons">email</span>
                                <div class="contact-item-content">
                                    <strong>Email</strong>
                                    <a href="mailto:<?= htmlspecialchars($entreprise['email']) ?>">
                                        <?= htmlspecialchars($entreprise['email']) ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($entreprise['website']): ?>
                            <div class="contact-item">
                                <span class="material-icons">language</span>
                                <div class="contact-item-content">
                                    <strong>Site web</strong>
                                    <a href="<?= htmlspecialchars($entreprise['website']) ?>" target="_blank">
                                        <?= htmlspecialchars($entreprise['website']) ?>
                                        <span class="material-icons open-link">open_in_new</span>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($entreprise['siret']): ?>
                            <div class="contact-item">
                                <span class="material-icons">business</span>
                                <div class="contact-item-content">
                                    <strong>SIRET</strong>
                                    <?= htmlspecialchars($entreprise['siret']) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Description -->
                    <?php if ($entreprise['description']): ?>
                        <div class="description-section">
                            <h4>À propos</h4>
                            <div class="text-content">
                                <?= nl2br(htmlspecialchars($entreprise['description'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Creator Info -->
                    <?php if ($entreprise['creator_firstName'] && $entreprise['creator_lastName']): ?>
                        <div class="creator-section">
                            <h5>Créé par</h5>
                            <div class="creator-info">
                                <span class="material-icons">person</span>
                                <div>
                                    <h6>
                                        <a href="<?= HOME_URL . 'profil/' . $entreprise['creator_slug'] ?>">
                                            <?= htmlspecialchars($entreprise['creator_firstName'] . ' ' . $entreprise['creator_lastName']) ?>
                                        </a>
                                    </h6>
                                    <small>Propriétaire</small>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Événements -->
                <?php if (!empty($entreprise['evenements'])): ?>
                    <div class="related-content-card">
                        <div class="card-header">
                            <h4>
                                <span class="material-icons">event</span>
                                Événements organisés
                            </h4>
                        </div>
                        <div class="card-body">
                            <?php foreach ($entreprise['evenements'] as $evenement): ?>
                                <div class="related-item-card">
                                    <?php if ($evenement['evenement_bannerPath']): ?>
                                        <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($evenement['evenement_bannerPath']) ?>"
                                            class="related-item-image"
                                            alt="<?= htmlspecialchars($evenement['evenement_title']) ?>">
                                    <?php endif; ?>
                                    <div class="related-item-content">
                                        <div class="related-item-title">
                                            <a href="<?= HOME_URL . 'evenements/'  . htmlspecialchars($evenement['ville_slug']) . '/' . htmlspecialchars($evenement['category_slug']) . '/' . htmlspecialchars($evenement['evenement_slug']) . '?back_to=entreprise/' . htmlspecialchars($entreprise['slug'])     ?>">
                                                <?= htmlspecialchars($evenement['evenement_title'])  ?>
                                            </a>
                                        </div>
                                        <div class="related-item-meta">
                                            <span class="material-icons">event</span>
                                            Événement
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Réalisations -->
                <?php if (!empty($entreprise['realisations'])): ?>
                    <div class="related-content-card">
                        <div class="card-header">
                            <h4>
                                <span class="material-icons">work</span>
                                Nos réalisations
                            </h4>
                        </div>
                        <div class="card-body">
                            <?php foreach ($entreprise['realisations'] as $realisation): ?>
                                <div class="related-item-card">
                                    <div class="related-item-content">
                                        <div class="related-item-title">
                                            <?= htmlspecialchars($realisation['realisation_title']) ?>
                                        </div>
                                        <div class="related-item-meta">
                                            <span class="material-icons">work</span>
                                            Réalisation
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Column - Contact Card & Stats -->
            <div class="contact-sidebar">
                <!-- Contact Card -->
                <div class="contact-card">
                    <div class="card-header">
                        <h5>Contacter l'entreprise</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($entreprise['phone']): ?>
                            <a href="tel:<?= htmlspecialchars($entreprise['phone']) ?>"
                                class="btn btn-primary linkNotDecorated">
                                <span class="material-icons">phone</span>
                                Appeler
                            </a>
                        <?php endif; ?>

                        <?php if ($entreprise['email']): ?>
                            <a href="mailto:<?= htmlspecialchars($entreprise['email']) ?>"
                                class="btn linkNotDecorated">
                                <span class="material-icons">email</span>
                                Envoyer un email
                            </a>
                        <?php endif; ?>

                        <?php if ($entreprise['website']): ?>
                            <a href="<?= htmlspecialchars($entreprise['website']) ?>"
                                target="_blank"
                                class="btn linkNotDecorated">
                                <span class="material-icons">language</span>
                                Visiter le site
                            </a>
                        <?php endif; ?>

                        <div class="quick-actions">
                            <button class="quick-action-btn" title="Partager">
                                <span class="material-icons">share</span>
                            </button>
                            <button class="quick-action-btn" title="Signaler">
                                <span class="material-icons">flag</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="stats-card">
                    <div class="card-header">
                        <h6>Statistiques</h6>
                    </div>
                    <div class="card-body">
                        <div class="stats-grid">
                            <div class="stat-box">
                                <h5><?= count($entreprise['evenements']) ?></h5>
                                <small>Événements</small>
                            </div>
                            <div class="stat-box">
                                <h5><?= count($entreprise['realisations']) ?></h5>
                                <small>Réalisations</small>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                Actif depuis <?= date('M Y', strtotime($entreprise['createdAt'])) ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>