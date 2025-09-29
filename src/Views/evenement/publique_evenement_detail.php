<?php include_once __DIR__ . '/../includes/header.php'; ?>

<?php
$startDate = new DateTime($evenement['startDate']);
$endDate = new DateTime($evenement['endDate']);

?>

<link rel="stylesheet" href="<?= HOME_URL ?>assets/css/public_event_detail.css">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="event-detail-main">
    <!-- Hero Banner Section -->
    <section class="event-hero" style="background-image: url('<?= HOME_URL . ltrim($evenement['bannerPath'], '/') ?>');">
        <div class="event-hero-overlay">
            <div class="event-hero-content">
                <div class="event-meta">
                    <span class="event-category"><?= htmlspecialchars($evenement['category_name'] ?? 'Événement') ?></span>
                    <span class="event-date">
                        <span class="material-icons">event</span>
                        <?= $startDate->format('d/m/Y H:i') ?>
                        <?php if ($startDate->format('d/m/Y') !== $endDate->format('d/m/Y')): ?>
                            - <?= $endDate->format('d/m/Y H:i') ?>
                        <?php elseif ($startDate->format('H:i') !== $endDate->format('H:i')): ?>
                            - <?= $endDate->format('H:i') ?>
                        <?php endif; ?>
                    </span>
                </div>
                <h1 class="event-title"><?= htmlspecialchars($evenement['title']) ?></h1>
                <p class="event-location">
                    <span class="material-icons">location_on</span>
                    <?= htmlspecialchars($evenement['ville_nom_reel'] ?? 'Lieu non spécifié') ?>
                </p>
                <?php if (!empty($evenement['address'])): ?>
                    <p class="event-address"><?= htmlspecialchars($evenement['address']) ?></p>
                <?php endif; ?>
                
                <?php if (!empty($evenement['association_name'])): ?>
                    <p class="event-organizer">
                        <span class="material-icons">people</span>
                        Organisé par: <?= htmlspecialchars($evenement['association_name']) ?>
                    </p>
                <?php elseif (!empty($evenement['entreprise_name'])): ?>
                    <p class="event-organizer">
                        <span class="material-icons">business</span>
                        Organisé par: <?= htmlspecialchars($evenement['entreprise_name']) ?>
                    </p>
                <?php endif; ?>
                
                <!-- Registration button if registration is available -->
                <?php if (strtotime($evenement['registrationDeadline']) > time() && $evenement['currentParticipants'] < $evenement['maxParticipants']): ?>
                    <button class="btn-register">S'inscrire à l'événement</button>
                <?php elseif ($evenement['currentParticipants'] >= $evenement['maxParticipants']): ?>
                    <div class="event-full">Événement complet</div>
                <?php elseif (strtotime($evenement['registrationDeadline']) < time()): ?>
                    <div class="event-closed">Inscriptions fermées</div>
                <?php endif; ?>
                
                <!-- Social sharing include -->
             <?php include __DIR__ . '/../includes/social_share_btns.php'; ?>
            </div>
        </div>
    </section>
    
    <!-- Event Content Section -->
    <section class="event-content">
        <div class="event-container">
            <div class="event-main">
                <!-- Description -->
                <div class="event-description">
                    <h2>À propos de cet événement</h2>
                    <?php if (!empty($evenement['shortDescription'])): ?>
                        <p class="event-short-description"><?= htmlspecialchars($evenement['shortDescription']) ?></p>
                    <?php endif; ?>
                    <div class="event-full-description">
                        <?= nl2br(htmlspecialchars($evenement['description'])) ?>
                    </div>
                </div>
                
                <!-- Event Gallery -->
                <?php if (!empty($eventImages)): ?>
                <div class="event-gallery">
                    <h2>Galerie photos</h2>
                    <div class="gallery-grid">
                        <?php foreach ($eventImages as $image): ?>
                            <div class="gallery-item">
                                <img src="<?= HOME_URL . ltrim($image['imagePath'], '/') ?>" 
                                     alt="<?= htmlspecialchars($image['altText'] ?? $evenement['title']) ?>" 
                                     class="gallery-image" 
                                     onclick="openLightbox(this.src, this.alt)">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="event-sidebar">
                <!-- Event Details Card -->
                <div class="event-details-card">
                    <h3>Détails de l'événement</h3>
                    <ul class="event-details-list">
                        <li>
                            <span class="detail-icon"><i class="material-icons">event</i></span>
                            <div class="detail-content">
                                <span class="detail-label">Date et heure</span>
                                <span class="detail-value">
                                    <?= $startDate->format('d/m/Y H:i') ?>
                                    <?php if ($startDate->format('d/m/Y') !== $endDate->format('d/m/Y')): ?>
                                        - <?= $endDate->format('d/m/Y H:i') ?>
                                    <?php elseif ($startDate->format('H:i') !== $endDate->format('H:i')): ?>
                                        - <?= $endDate->format('H:i') ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </li>
                        <li>
                            <span class="detail-icon"><i class="material-icons">location_on</i></span>
                            <div class="detail-content">
                                <span class="detail-label">Lieu</span>
                                <span class="detail-value">
                                    <?= htmlspecialchars($evenement['ville_nom_reel'] ?? 'Non spécifié') ?>
                                    <?php if (!empty($evenement['address'])): ?>
                                        <br><?= htmlspecialchars($evenement['address']) ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </li>
                        <?php if (!empty($evenement['price'])): ?>
                        <li>
                            <span class="detail-icon"><i class="material-icons">euro</i></span>
                            <div class="detail-content">
                                <span class="detail-label">Prix</span>
                                <span class="detail-value">
                                    <?= number_format($evenement['price'], 2, ',', ' ') ?> €
                                </span>
                            </div>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($evenement['maxParticipants'])): ?>
                        <li>
                            <span class="detail-icon"><i class="material-icons">group</i></span>
                            <div class="detail-content">
                                <span class="detail-label">Participants</span>
                                <span class="detail-value">
                                    <?= $evenement['currentParticipants'] ?>/<?= $evenement['maxParticipants'] ?>
                                </span>
                            </div>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($evenement['registrationDeadline'])): ?>
                        <li>
                            <span class="detail-icon"><i class="material-icons">schedule</i></span>
                            <div class="detail-content">
                                <span class="detail-label">Inscription jusqu'au</span>
                                <span class="detail-value">
                                    <?= date('d/m/Y H:i', strtotime($evenement['registrationDeadline'])) ?>
                                </span>
                            </div>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <!-- Google Map Embed if address is available -->
                <?php if (!empty($evenement['address']) && !empty($evenement['ville_nom_reel'])): ?>
                <div class="event-map">
                    <h3>Localisation</h3>
                    <div class="map-container">
                        <iframe
                            width="100%"
                            height="250"
                            style="border:0"
                            loading="lazy"
                            allowfullscreen
                            referrerpolicy="no-referrer-when-downgrade"
                            src="https://www.google.com/maps/embed/v1/place?key=YOUR_API_KEY&q=<?= urlencode($evenement['address'] . ', ' . $evenement['ville_nom_reel']) ?>">
                        </iframe>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Call to Action -->
                <?php if (strtotime($evenement['registrationDeadline']) > time() && $evenement['currentParticipants'] < $evenement['maxParticipants']): ?>
                    <div class="event-cta">
                        <button class="btn-register-full">S'inscrire maintenant</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<!-- Lightbox for gallery images -->
<div id="lightbox" class="lightbox">
    <span class="close-lightbox">&times;</span>
    <img id="lightbox-img" class="lightbox-content">
    <div id="lightbox-caption" class="lightbox-caption"></div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
