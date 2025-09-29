<?php include_once __DIR__ . '/../includes/header.php'; ?>

<?php
$startDate = new DateTime($evenement['startDate']);
$endDate = new DateTime($evenement['endDate']);

?>



<link rel="stylesheet" href="<?= HOME_URL ?>assets/css/events.css">
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

<style>
/* Additional styles for event detail page */
.event-detail-main {
    margin: 0;
    padding: 0;
}

.event-hero {
    position: relative;
    height: 60vh;
    min-height: 400px;
    background-size: cover;
    background-position: center;
    color: white;
}

.event-hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, rgba(0,0,0,0.4), rgba(0,0,0,0.8));
    display: flex;
    align-items: center;
}

.event-hero-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
    width: 100%;
}

.event-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.event-category {
    background-color: var(--primary-color);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.event-date {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.9rem;
}

.event-title {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    font-weight: 700;
}

.event-location, .event-address, .event-organizer {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-size: 1rem;
}

.btn-register {
    margin-top: 1.5rem;
    padding: 0.75rem 2rem;
    background-color: var(--primary-color);
    color: white;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-register:hover {
    background-color: var(--secondary-color);
}

.event-full, .event-closed {
    margin-top: 1.5rem;
    padding: 0.75rem 2rem;
    background-color: #f44336;
    color: white;
    border-radius: 4px;
    font-weight: 600;
    display: inline-block;
}

.event-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.event-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
}

@media (max-width: 768px) {
    .event-container {
        grid-template-columns: 1fr;
    }
}

.event-description h2, .event-gallery h2 {
    margin-bottom: 1rem;
    color: var(--text-dark);
    font-size: 1.5rem;
}

.event-short-description {
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.1rem;
    color: #555;
}

.event-full-description {
    line-height: 1.6;
    color: #333;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.gallery-item {
    overflow: hidden;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.gallery-image {
    width: 100%;
    height: 150px;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.3s;
}

.gallery-image:hover {
    transform: scale(1.05);
}

.event-details-card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.event-details-card h3 {
    margin-bottom: 1rem;
    color: var(--text-dark);
    font-size: 1.25rem;
}

.event-details-list {
    list-style: none;
    padding: 0;
}

.event-details-list li {
    display: flex;
    margin-bottom: 1rem;
}

.detail-icon {
    margin-right: 1rem;
    color: var(--primary-color);
}

.detail-content {
    flex: 1;
}

.detail-label {
    display: block;
    font-size: 0.875rem;
    color: #666;
    margin-bottom: 0.25rem;
}

.detail-value {
    font-weight: 600;
    color: #333;
}

.event-map {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.event-map h3 {
    margin-bottom: 1rem;
    color: var(--text-dark);
    font-size: 1.25rem;
}

.map-container {
    border-radius: 4px;
    overflow: hidden;
}

.event-cta {
    margin-top: 2rem;
}

.btn-register-full {
    width: 100%;
    padding: 1rem;
    background-color: var(--primary-color);
    color: white;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-register-full:hover {
    background-color: var(--secondary-color);
}

/* Lightbox styles */
.lightbox {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.9);
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

.lightbox-content {
    max-width: 90%;
    max-height: 80%;
    object-fit: contain;
}

.close-lightbox {
    position: absolute;
    top: 15px;
    right: 25px;
    color: white;
    font-size: 35px;
    cursor: pointer;
}

.lightbox-caption {
    color: white;
    margin-top: 1rem;
    font-size: 1.1rem;
    text-align: center;
    padding: 0 1rem;
}
</style>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
