<?php include_once __DIR__ . '/../includes/header.php'; ?>

<?php
$startDate = new DateTime($evenement['startDate']);
$endDate = new DateTime($evenement['endDate']);

?>

<link rel="stylesheet" href="<?= HOME_URL ?>assets/css/public_event_detail.css">

<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="event-detail-main">
    <a href="<?= HOME_URL ?>evenements">
        <span class="material-icons btn btn-back" style="color:white;">arrow_back</span>
    </a>

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
                <?php if ($isOwner): ?>
                    <p class="event-registered text-success text-center bounce-in-top">Vous êtes propriétaire de cet événement</p>
                <?php elseif ($isSubscribed): ?>
                    <p class="event-registered text-success text-center bounce-in-top">Vous êtes inscrit à cet événement</p>
                <?php elseif ($isSubscribeOnWaitingList): ?>
                    <p class="event-registered text-success text-center bounce-in-top">Vous êtes sur la liste d'attente de cet événement</p>
                <?php elseif ($isRefused): ?>
                    <p class="event-registered text-danger text-center bounce-in-top">Votre inscription à cet événement a été refusée</p>
                <?php elseif ($isCancelled): ?>
                    <p class="event-registered text-warning text-center bounce-in-top">Votre inscription à cet événement a été annulée</p>
                <?php else: ?>
                    <?php if (strtotime($evenement['registrationDeadline']) > time() && $evenement['currentParticipants'] < $evenement['maxParticipants']): ?>
                        <form action="<?= HOME_URL . 'evenements/' . $evenement['ville_slug'] . '/' . $evenement['category_slug'] . '/' . $evenement['slug']  ?>" method="post">
                            <input type="hidden" name="slug" value="<?= $evenement['slug'] ?>">

                            <button type="submit" class="btn btn-success m">
                                <?php if ($evenement['requiresApproval']): ?>
                                    Demande d'inscription <?php else : ?> S'inscrire à l'événement
                                <?php endif; ?>
                            </button>
                        </form>
                    <?php elseif ($evenement['currentParticipants'] >= $evenement['maxParticipants']): ?>
                        <div class="event-full">Événement complet</div>
                    <?php elseif (strtotime($evenement['registrationDeadline']) < time()): ?>
                        <div class="event-closed">Inscriptions fermées</div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php include_once __DIR__ . '/../includes/messages.php'; ?>

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

                <!-- Like/Favourite buttons and event likes/comments counter -->
                <?php if (isset($_SESSION['idUser'])): ?>
                    <button id="like-btn" data-uiid="<?= $evenement['uiid'] ?>" style="background:none;border:none;vertical-align:middle;">
                        <span id="like-icon">
                            <!-- Icon will be updated by JavaScript -->
                        </span>
                    </button>
                    <button id="favourite-btn" data-uiid="<?= $evenement['uiid'] ?>" style="background:none;border:none;vertical-align:middle;">
                        <span id="favourite-icon">
                            <!-- Icon will be updated by JavaScript -->
                        </span>
                    </button>
                    <button id="comments-btn" data-uiid="<?= $evenement['uiid'] ?>" style="background:none;border:none;vertical-align:middle;cursor:pointer;">
                        <svg stroke="currentColor" fill="#0053f9ff" stroke-width="0" viewBox="0 0 24 24" height="32px" width="32px" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"></path>
                        </svg>
                    </button>
                <?php else: ?>
                    <button id="comments-btn" data-uiid="<?= $evenement['uiid'] ?>" style="background:none;border:none;vertical-align:middle;cursor:pointer;">
                        <svg stroke="currentColor" fill="#0053f9ff" stroke-width="0" viewBox="0 0 24 24" height="32px" width="32px" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"></path>
                        </svg>
                    </button>
                <?php endif; ?>
                <div>
                    <span id="event-likes-count">0 personne aime cet événement</span>
                    <span id="event-comments-count" style="margin-left:1em;cursor:pointer;" onclick="document.getElementById('comments-btn').click()">0 commentaire</span>
                </div>

                <!-- Comments Modal -->
                <div id="comments-modal" class="comments-modal" style="display:none;">
                    <div class="comments-modal-content">
                        <div class="comments-modal-header">
                            <h2>Commentaires</h2>
                            <button class="close-comments-modal" style="background:none;border:none;font-size:32px;cursor:pointer;">&times;</button>
                        </div>
                        <div class="comments-modal-body">
                            <div id="comments-list">
                                <p>Chargement des commentaires...</p>
                            </div>
                        </div>
                        <?php if (isset($_SESSION['idUser'])): ?>
                            <div class="comments-modal-footer">
                                <form id="add-comment-form" style="display:flex;align-items:center;gap:0.5em;width:100%;">
                                    <textarea name="content" required placeholder="Ajouter un commentaire..." style="flex:1;min-height:50px;"></textarea>
                                    <input type="hidden" name="eventUiid" value="<?= $evenement['uiid'] ?>">
                                    <button type="submit" style="background:none;border:none;padding:0;cursor:pointer;">
                                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="32px" width="32px" style="vertical-align:middle;" xmlns="http://www.w3.org/2000/svg">
                                            <path d="m476.59 227.05-.16-.07L49.35 49.84A23.56 23.56 0 0 0 27.14 52 24.65 24.65 0 0 0 16 72.59v113.29a24 24 0 0 0 19.52 23.57l232.93 43.07a4 4 0 0 1 0 7.86L35.53 303.45A24 24 0 0 0 16 327v113.31A23.57 23.57 0 0 0 26.59 460a23.94 23.94 0 0 0 13.22 4 24.55 24.55 0 0 0 9.52-1.93L476.4 285.94l.19-.09a32 32 0 0 0 0-58.8z"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="comments-modal-footer">
                                <a href="<?= HOME_URL ?>connexion" class="btn">Connectez-vous pour commenter</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <script src="<?= HOME_URL ?>assets/javascript/event-interactions.js"></script>
                <script>
                    // Initialize the interactions
                    const eventUiid = "<?= $evenement['uiid'] ?>";
                    const isLoggedIn = <?= isset($_SESSION['userUiid']) ? 'true' : 'false' ?>;
                    const currentUserUiid = <?= isset($_SESSION['userUiid']) ? '"' . $_SESSION['userUiid'] . '"' : 'null' ?>;
                    EventInteractions.init(eventUiid, isLoggedIn, currentUserUiid);
                </script>
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
                                height="320"
                                style="border:0"
                                loading="lazy"
                                allowfullscreen
                                referrerpolicy="no-referrer-when-downgrade"
                                src="https://www.google.com/maps?q=<?= urlencode($evenement['address'] . ', ' . $evenement['ville_nom_reel']) ?>&output=embed">
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