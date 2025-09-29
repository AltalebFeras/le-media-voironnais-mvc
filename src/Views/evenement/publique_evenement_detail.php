<?php include_once __DIR__ . '/../includes/header.php'; ?>

<?php
$startDate = new DateTime($evenement['startDate']);
$endDate = new DateTime($evenement['endDate']);

// Meta tags for social sharing
$metaTitle = htmlspecialchars($evenement['title']);
$metaDescription = htmlspecialchars(substr($evenement['shortDescription'] ?? $evenement['description'], 0, 160));
$metaImage = !empty($evenement['bannerPath']) ? DOMAIN . HOME_URL . ltrim($evenement['bannerPath'], '/') : DOMAIN . HOME_URL . 'assets/images/uploads/banners/default_banner.png';
$metaUrl = DOMAIN . $_SERVER['REQUEST_URI'];
?>

<!-- Open Graph / Facebook Meta Tags -->
<meta property="og:type" content="website">
<meta property="og:url" content="<?= $metaUrl ?>">
<meta property="og:title" content="<?= $metaTitle ?>">
<meta property="og:description" content="<?= $metaDescription ?>">
<meta property="og:image" content="<?= $metaImage ?>">

<!-- Twitter Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="<?= $metaUrl ?>">
<meta name="twitter:title" content="<?= $metaTitle ?>">
<meta name="twitter:description" content="<?= $metaDescription ?>">
<meta name="twitter:image" content="<?= $metaImage ?>">

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
                
                <!-- Social sharing -->
                <div class="social-share">
                    <span class="share-label">Partager:</span>
                    <div class="share-buttons">
                        <a href="<?= $shareTable['facebook'] ?>" target="_blank" title="Partager sur Facebook" class="share-btn facebook">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white" aria-hidden="true"><path d="M22.675 0h-21.35C.595 0 0 .595 0 1.326v21.348C0 23.405.595 24 1.326 24h11.495v-9.294H9.691v-3.622h3.13V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.797.143v3.24l-1.92.001c-1.504 0-1.797.715-1.797 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116C23.405 24 24 23.405 24 22.674V1.326C24 .595 23.405 0 22.675 0z"/></svg>
                        </a>
                        <a href="<?= $shareTable['twitter'] ?>" target="_blank" title="Partager sur Twitter" class="share-btn twitter">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white" aria-hidden="true"><path d="M24 4.557a9.83 9.83 0 0 1-2.828.775 4.932 4.932 0 0 0 2.165-2.724c-.951.564-2.005.974-3.127 1.195A4.916 4.916 0 0 0 16.616 3c-2.717 0-4.92 2.206-4.92 4.917 0 .386.044.762.127 1.124C7.728 8.816 4.1 6.884 1.671 3.149c-.423.724-.666 1.562-.666 2.475 0 1.708.87 3.216 2.188 4.099a4.904 4.904 0 0 1-2.229-.616c-.054 2.281 1.581 4.415 3.949 4.89a4.936 4.936 0 0 1-2.224.084c.627 1.956 2.444 3.377 4.6 3.417A9.867 9.867 0 0 1 0 21.543a13.94 13.94 0 0 0 7.548 2.212c9.058 0 14.009-7.513 14.009-14.009 0-.213-.005-.425-.014-.636A10.025 10.025 0 0 0 24 4.557z"/></svg>
                        </a>
                        <a href="<?= $shareTable['linkedin'] ?>" target="_blank" title="Partager sur LinkedIn" class="share-btn linkedin">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.327-.027-3.037-1.849-3.037-1.851 0-2.132 1.445-2.132 2.939v5.667H9.358V9h3.414v1.561h.049c.476-.9 1.637-1.849 3.369-1.849 3.602 0 4.267 2.369 4.267 5.455v6.285zM5.337 7.433a2.062 2.062 0 1 1 0-4.124 2.062 2.062 0 0 1 0 4.124zm1.777 13.019H3.56V9h3.554v11.452zM22.225 0H1.771C.792 0 0 .771 0 1.723v20.549C0 23.229.792 24 1.771 24h20.451C23.2 24 24 23.229 24 22.272V1.723C24 .771 23.2 0 22.225 0z"/></svg>
                        </a>
                        <a href="<?= $shareTable['whatsapp'] ?>" target="_blank" title="Partager sur WhatsApp" class="share-btn whatsapp">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.028-.967-.271-.099-.468-.149-.666.15-.197.297-.767.967-.94 1.164-.173.198-.347.223-.644.075-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.373-.025-.522-.075-.149-.666-1.611-.916-2.207-.242-.579-.487-.5-.666-.51-.173-.009-.373-.009-.572-.009-.198 0-.522.075-.797.373-.271.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.099 3.2 5.077 4.358.709.306 1.262.489 1.694.625.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.075-.124-.271-.198-.568-.347zM12.004 2.003c-5.522 0-9.997 4.475-9.997 9.997 0 1.762.462 3.479 1.338 4.991l-1.414 5.163 5.287-1.391c1.469.805 3.127 1.247 4.786 1.247h.006c5.522 0 9.997-4.475 9.997-9.997 0-2.669-1.037-5.178-2.921-7.062-1.884-1.884-4.393-2.948-7.082-2.948zm0 18.13c-1.545 0-3.063-.399-4.377-1.153l-.313-.179-3.137.826.837-3.053-.203-.314c-.845-1.308-1.292-2.823-1.292-4.389 0-4.411 3.588-7.999 7.999-7.999 2.137 0 4.146.832 5.656 2.344 1.511 1.511 2.344 3.52 2.344 5.656 0 4.411-3.588 7.999-7.999 7.999z"/></svg>
                        </a>
                        <a href="<?= $shareTable['email'] ?>" title="Partager par email" class="share-btn email">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white" aria-hidden="true"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 2v.01L12 13 4 6.01V6h16zm0 12H4V8l8 5 8-5v10z"/></svg>
                        </a>
                    </div>
                </div>
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

<script>
// Lightbox functionality
function openLightbox(src, alt) {
    document.getElementById('lightbox').style.display = 'flex';
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox-caption').innerHTML = alt;
    document.body.style.overflow = 'hidden';
}

document.querySelector('.close-lightbox').addEventListener('click', function() {
    document.getElementById('lightbox').style.display = 'none';
    document.body.style.overflow = 'auto';
});

document.getElementById('lightbox').addEventListener('click', function(e) {
    if (e.target === this) {
        this.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
});

// Registration button functionality (example - replace with your actual registration logic)
document.querySelectorAll('.btn-register, .btn-register-full').forEach(button => {
    button.addEventListener('click', function() {
        // Replace with actual registration logic or redirect
        alert('La fonctionnalité d\'inscription sera disponible prochainement!');
    });
});
</script>

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

.social-share {
    margin-top: 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.share-buttons {
    display: flex;
    gap: 0.5rem;
}

.share-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(255,255,255,0.2);
    color: white;
    transition: all 0.3s;
}

.share-btn:hover {
    background-color: rgba(255,255,255,0.4);
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
