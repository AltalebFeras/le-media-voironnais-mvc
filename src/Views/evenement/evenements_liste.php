<?php include_once __DIR__ . '/../includes/header.php'; ?>

<link rel="stylesheet" href="<?= HOME_URL ?>assets/css/evenements/events.css">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="p-0">
    <!-- Hero Section -->
    <section class="events-hero">
        <div class="events-hero-content">
            <h1 class="events-hero-title">Événements à Venir</h1>
            <p class="events-hero-subtitle">Découvrez tous les événements passionnants organisés dans le Voironnais</p>
            <a href="#all-events" class="btn-timetable">VOIR LE PROGRAMME</a>
        </div>


        <!-- message -->
        <?php include_once __DIR__ . '/../includes/messages.php'; ?>
        <!-- Upcoming Events Slider -->
        <?php if (!empty($upcomingEvents)): ?>
            <div class="upcoming-events-slider">
                <?php foreach ($upcomingEvents as $index => $event): ?>
                    <div class="event-slide <?= $index === 0 ? 'active' : '' ?>" data-slide="<?= $index ?>">
                        <div class="event-slide-content">
                            <span class="event-date"><?= date('d M Y', strtotime($event['startDate'])) ?></span>
                            <h3 class="event-slide-title"><?= htmlspecialchars($event['title']) ?></h3>
                            <p class="event-slide-description"><?= htmlspecialchars($event['shortDescription'] ?? 'Rejoignez-nous pour cet événement exceptionnel dans le Voironnais') ?></p>
                            <a href="<?= HOME_URL . 'evenements/' . $event['ville_slug'] . '/' . $event['category_slug'] . '/' . $event['slug'] ?>" class="btn-buy-tickets">VOIR</a>
                        </div>
                        <?php if ($event['bannerPath']): ?>
                            <div class="event-slide-image">
                                <img src="<?= HOME_URL . ltrim($event['bannerPath'], '/') ?>" alt="<?= htmlspecialchars($event['title']) ?>">
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <!-- Navigation dots -->
                <div class="slider-navigation">
                    <span class="slide-counter"><?= count($upcomingEvents) > 0 ? '1' : '0' ?></span>
                    <div class="slider-dots">
                        <?php foreach ($upcomingEvents as $index => $event): ?>
                            <button class="dot <?= $index === 0 ? 'active' : '' ?>" data-slide="<?= $index ?>" onclick="changeSlide(<?= $index ?>)"></button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="no-upcoming-events">
                <h3>Aucun événement à venir pour le moment</h3>
                <p>De nouveaux événements seront bientôt disponibles</p>
            </div>
        <?php endif; ?>
    </section>

    <!-- Recent Events Section -->
    <?php if (!empty($recentEvents)): ?>
        <section class="recent-events-section">
            <div class="section-header">
                <h2 class="section-title">Événements Récents</h2>
                <p class="section-subtitle">Découvrez les derniers événements qui ont eu lieu dans notre région</p>
            </div>

            <div class="recent-events-grid">
                <?php foreach ($recentEvents as $event): ?>
                    <div class="recent-event-card">
                        <?php if ($event['bannerPath']): ?>
                            <div class="event-card-image">
                                <img src="<?= HOME_URL . ltrim($event['bannerPath'], '/') ?>" alt="<?= htmlspecialchars($event['title']) ?>">
                            </div>
                        <?php endif; ?>
                        <div class="event-card-content">
                            <span class="event-date"><?= date('d M Y', strtotime($event['startDate'])) ?></span>
                            <h4 class="event-card-title"><?= htmlspecialchars($event['title']) ?></h4>
                            <p class="event-location">
                                <span class="material-icons" style="font-size: 1rem; margin-right: 0.5rem;">location_on</span>
                                <?= htmlspecialchars($event['ville_nom_reel'] ?? 'Lieu non spécifié') ?>
                            </p>
                            <?php if ($event['category_name']): ?>
                                <p class="event-category">
                                    <span class="material-icons" style="font-size: 1rem; margin-right: 0.5rem;">category</span>
                                    <?= htmlspecialchars($event['category_name']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- All Events Section -->
    <section id="all-events" class="all-events-section">
        <div class="section-header">
            <h2 class="section-title">Tous les Événements</h2>
            <p class="section-subtitle">Explorez notre collection complète d'événements dans le Voironnais</p>
        </div>

        <!-- Search and Filter -->
        <div class="events-search-filter">
            <div class="search-box">
                <input type="text" placeholder="Rechercher des événements..." class="search-input">
                <button class="search-btn">
                    <span class="material-icons">search</span>
                </button>
            </div>
        </div>

        <!-- Events Grid -->
        <?php if (!empty($allEvents)): ?>
            <div class="all-events-grid">
                <?php foreach ($allEvents as $event): ?>
                    <div class="event-card" onclick="window.location.href='<?= HOME_URL ?>evenements/<?= $event['ville_slug'] ?>/<?= $event['category_slug'] ?>/<?= $event['slug'] ?>'">
                        <?php if ($event['bannerPath']): ?>
                            <div class="event-card-image">
                                <img src="<?= HOME_URL . ltrim($event['bannerPath'], '/') ?>" alt="<?= htmlspecialchars($event['title']) ?>">
                                <div class="event-card-overlay">
                                    <span class="event-date"><?= date('d M Y', strtotime($event['startDate'])) ?></span>
                                    <h4 class="event-card-title"><?= htmlspecialchars($event['title']) ?></h4>
                                    <p class="event-category">
                                        <span class="material-icons" style="font-size: 0.9rem; margin-right: 0.3rem;">category</span>
                                        <?= htmlspecialchars($event['category_name'] ?? 'Catégorie non spécifiée') ?>
                                    </p>
                                    <p class="event-location">
                                        <span class="material-icons" style="font-size: 0.9rem; margin-right: 0.3rem;">location_on</span>
                                        <?= htmlspecialchars($event['ville_nom_reel'] ?? 'Lieu non spécifié') ?>
                                    </p>
                                    <?php if ($event['association_name']): ?>
                                        <p class="event-association">
                                            <span class="material-icons" style="font-size: 0.9rem; margin-right: 0.3rem;">group</span>
                                            <?= htmlspecialchars($event['association_name']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="event-card-no-image">
                                <div class="event-card-overlay">
                                    <span class="event-date"><?= date('d M Y', strtotime($event['startDate'])) ?></span>
                                    <h4 class="event-card-title"><?= htmlspecialchars($event['title']) ?></h4>
                                    <p class="event-category">
                                        <span class="material-icons" style="font-size: 0.9rem; margin-right: 0.3rem;">category</span>
                                        <?= htmlspecialchars($event['category_name'] ?? 'Catégorie non spécifiée') ?>
                                    </p>
                                    <p class="event-location">
                                        <span class="material-icons" style="font-size: 0.9rem; margin-right: 0.3rem;">location_on</span>
                                        <?= htmlspecialchars($event['ville_nom_reel'] ?? 'Lieu non spécifié') ?>
                                    </p>
                                    <?php if ($event['association_name']): ?>
                                        <p class="event-association">
                                            <span class="material-icons" style="font-size: 0.9rem; margin-right: 0.3rem;">group</span>
                                            <?= htmlspecialchars($event['association_name']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php include_once __DIR__ . '/../includes/pagination.php'; ?>


        <?php else: ?>
            <div class="no-events">
                <span class="material-icons" style="font-size: 4rem; opacity: 0.3; margin-bottom: 1rem;">event_busy</span>
                <h3>Aucun événement trouvé</h3>
                <p>Il n'y a actuellement aucun événement public disponible.</p>
                <p>Revenez bientôt pour découvrir de nouveaux événements !</p>
            </div>
        <?php endif; ?>
    </section>
</main>

<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.event-slide');
    const dots = document.querySelectorAll('.dot');
    const slideCounter = document.querySelector('.slide-counter');

    function changeSlide(slideIndex) {
        if (slides.length === 0) return;

        // Hide current slide
        slides[currentSlide].classList.remove('active');
        dots[currentSlide].classList.remove('active');

        // Show new slide
        currentSlide = slideIndex;
        slides[currentSlide].classList.add('active');
        dots[currentSlide].classList.add('active');

        // Update counter
        if (slideCounter) {
            slideCounter.textContent = currentSlide + 1;
        }
    }

    // Auto slide functionality
    if (slides.length > 1) {
        setInterval(() => {
            const nextSlide = (currentSlide + 1) % slides.length;
            changeSlide(nextSlide);
        }, 5000);
    }

    // Search functionality
    document.querySelector('.search-input').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const eventCards = document.querySelectorAll('.event-card');

        eventCards.forEach(card => {
            const title = card.querySelector('.event-card-title').textContent.toLowerCase();
            const category = card.querySelector('.event-category') ? card.querySelector('.event-category').textContent.toLowerCase() : '';
            const location = card.querySelector('.event-location') ? card.querySelector('.event-location').textContent.toLowerCase() : '';

            if (title.includes(searchTerm) || category.includes(searchTerm) || location.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>