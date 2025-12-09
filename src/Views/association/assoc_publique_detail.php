<?php
$title = htmlspecialchars($association['name']);
$description = $association['description'] ? substr(strip_tags($association['description']), 0, 160) : 'Association sur Le Média Voironnais';
?>

<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/associations/assoc_publique_detail.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="association-detail-page p-0">
    <!-- Association Hero Banner -->
    <div class="association-hero">
        <?php if ($association['bannerPath']): ?>
            <img src="<?= BASE_URL . HOME_URL .  htmlspecialchars($association['bannerPath']) ?>" 
                 alt="Bannière <?= htmlspecialchars($association['name']) ?>" 
                 class="association-hero-image">
        <?php endif; ?>
        
        <div class="association-hero-overlay">
            <div class="association-hero-content">
                <div class="association-header-info">
                    <img src="<?= BASE_URL . HOME_URL .  htmlspecialchars($association['logoPath'] ?? 'assets/images/default-association.png') ?>" 
                         alt="Logo <?= htmlspecialchars($association['name']) ?>" 
                         class="association-logo-large">
                    
                    <div class="association-title-section">
                        <h1><?= htmlspecialchars($association['name']) ?></h1>
                        <span class="association-badge">
                            <span class="material-icons">groups</span>
                            Association
                        </span>
                        <?php if ($association['ville_nom_reel']): ?>
                            <div class="location-info">
                                <span class="material-icons">location_on</span>
                                <?= htmlspecialchars($association['ville_nom_reel']) ?>
                            </div>
                        <?php endif; ?>
                        <div class="member-count">
                            <?= !empty($association['members']) ? count($association['members']) . ' membres' : 'Aucun membre' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="association-content">
        <div class="association-container">
            <!-- Left Column - Main Details -->
            <div>
                <!-- Contact Information -->
                <div class="detail-card">
                    <h3>Coordonnées</h3>
                    <div class="contact-grid">
                        <?php if ($association['address']): ?>
                            <div class="contact-item">
                                <span class="material-icons">location_on</span>
                                <div class="contact-item-content">
                                    <strong>Adresse</strong>
                                    <?= htmlspecialchars($association['address']) ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($association['phone']): ?>
                            <div class="contact-item">
                                <span class="material-icons">phone</span>
                                <div class="contact-item-content">
                                    <strong>Téléphone</strong>
                                    <a href="tel:<?= htmlspecialchars($association['phone']) ?>">
                                        <?= htmlspecialchars($association['phone']) ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($association['email']): ?>
                            <div class="contact-item">
                                <span class="material-icons">email</span>
                                <div class="contact-item-content">
                                    <strong>Email</strong>
                                    <a href="mailto:<?= htmlspecialchars($association['email']) ?>">
                                        <?= htmlspecialchars($association['email']) ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($association['website']): ?>
                            <div class="contact-item">
                                <span class="material-icons">language</span>
                                <div class="contact-item-content">
                                    <strong>Site web</strong>
                                    <a href="<?= htmlspecialchars($association['website']) ?>" target="_blank">
                                        <?= htmlspecialchars($association['website']) ?>
                                        <span class="material-icons open-link">open_in_new</span>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Description -->
                    <?php if ($association['description']): ?>
                        <div class="description-section">
                            <h4>À propos de l'association</h4>
                            <div class="text-content">
                                <?= nl2br(htmlspecialchars($association['description'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Members -->
                    <?php if (!empty($association['members'])): ?>
                        <div class="members-section">
                            <h4>
                                <span class="material-icons">people</span>
                                Membres (<?= count($association['members']) ?>)
                            </h4>
                            <div class="members-grid">
                                <?php foreach (array_slice($association['members'], 0, 6) as $member): ?>
                                    <div class="member-card">
                                        <img src="<?= $member['avatarPath'] ?? BASE_URL . HOME_URL . 'assets/images/default-avatar.png' ?>" 
                                             alt="<?= htmlspecialchars($member['firstName']) ?>" 
                                             class="member-avatar">
                                        <div class="member-info">
                                            <h6>
                                                <a href="<?= HOME_URL . 'profil/' . $member['slug'] ?>">
                                                    <?= htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) ?>
                                                </a>
                                            </h6>
                                            <small><?= ucfirst($member['role']) ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($association['members']) > 6): ?>
                                <div class="text-center mt-3">
                                    <button class="btn btn-outline-primary">
                                        Voir tous les membres
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Recent Events -->
                <?php if (!empty($association['associationEvents'])): ?>
                    <div class="related-content-card">
                        <div class="card-header">
                            <h4>
                                <span class="material-icons">event</span>
                                Événements récents
                            </h4>
                        </div>
                        <div class="card-body">
                            <?php foreach (array_slice($association['associationEvents'], 0, 4) as $event): ?>
                                <div class="related-item-card">
                                    <?php if ($event['bannerPath']): ?>
                                        <img src="<?= htmlspecialchars($event['bannerPath']) ?>" 
                                             class="related-item-image" 
                                             alt="<?= htmlspecialchars($event['title']) ?>">
                                    <?php endif; ?>
                                    <div class="related-item-content">
                                        <div class="related-item-title">
                                            <a href="<?= HOME_URL . 'evenements/' . $event['slug'] ?>">
                                                <?= htmlspecialchars($event['title']) ?>
                                            </a>
                                        </div>
                                        <div class="related-item-meta">
                                            <span class="material-icons">schedule</span>
                                            <?= date('d/m/Y', strtotime($event['startDate'])) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Column - Join Card -->
            <div class="join-sidebar">
                <!-- Join Association Card -->
                <div class="join-card">
                    <div class="card-header">
                        <h5>Rejoindre l'association</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            Participez aux activités et projets de l'association !
                        </p>
                        
                        <button class="btn btn-primary linkNotDecorated" onclick="joinAssociation()">
                            <span class="material-icons">group_add</span>
                            Demander à rejoindre
                        </button>

                        <div class="quick-actions">
                            <?php if ($association['phone']): ?>
                                <a href="tel:<?= htmlspecialchars($association['phone']) ?>" 
                                   class="quick-action-btn" 
                                   title="Appeler">
                                    <span class="material-icons">phone</span>
                                </a>
                            <?php endif; ?>

                            <?php if ($association['email']): ?>
                                <a href="mailto:<?= htmlspecialchars($association['email']) ?>" 
                                   class="quick-action-btn" 
                                   title="Email">
                                    <span class="material-icons">email</span>
                                </a>
                            <?php endif; ?>

                            <button class="quick-action-btn" onclick="shareAssociation()" title="Partager">
                                <span class="material-icons">share</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function joinAssociation() {
    // Implementation for joining association
    console.log('Join association');
}

function shareAssociation() {
    // Implementation for sharing
    if (navigator.share) {
        navigator.share({
            title: '<?= addslashes($association['name']) ?>',
            url: window.location.href
        });
    }
}
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
