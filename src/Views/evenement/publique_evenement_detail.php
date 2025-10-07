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
                    <button id="like-btn" data-id="<?= $evenement['idEvenement'] ?>" style="background:none;border:none;vertical-align:middle;">
                        <?php if (!empty($userHasLiked)): ?>
                            <!-- Blue thumb SVG -->
                            <svg stroke="currentColor" fill="#0053f9ff" stroke-width="1" viewBox="0 0 1024 1024" height="32px" width="32px" xmlns="http://www.w3.org/2000/svg">
                                <path d="M885.9 533.7c16.8-22.2 26.1-49.4 26.1-77.7 0-44.9-25.1-87.4-65.5-111.1a67.67 67.67 0 0 0-34.3-9.3H572.4l6-122.9c1.4-29.7-9.1-57.9-29.5-79.4A106.62 106.62 0 0 0 471 99.9c-52 0-98 35-111.8 85.1l-85.9 311h-.3v428h472.3c9.2 0 18.2-1.8 26.5-5.4 47.6-20.3 78.3-66.8 78.3-118.4 0-12.6-1.8-25-5.4-37 16.8-22.2 26.1-49.4 26.1-77.7 0-12.6-1.8-25-5.4-37 16.8-22.2 26.1-49.4 26.1-77.7-.2-12.6-2-25.1-5.6-37.1zM112 528v364c0 17.7 14.3 32 32 32h65V496h-65c-17.7 0-32 14.3-32 32z"></path>
                            </svg>
                        <?php else: ?>
                            <!-- Grey thumb SVG -->
                            <svg stroke="currentColor" fill="#a99a9aff" stroke-width="1" viewBox="0 0 1024 1024" height="32px" width="32px" xmlns="http://www.w3.org/2000/svg">
                                <path d="M885.9 533.7c16.8-22.2 26.1-49.4 26.1-77.7 0-44.9-25.1-87.4-65.5-111.1a67.67 67.67 0 0 0-34.3-9.3H572.4l6-122.9c1.4-29.7-9.1-57.9-29.5-79.4A106.62 106.62 0 0 0 471 99.9c-52 0-98 35-111.8 85.1l-85.9 311h-.3v428h472.3c9.2 0 18.2-1.8 26.5-5.4 47.6-20.3 78.3-66.8 78.3-118.4 0-12.6-1.8-25-5.4-37 16.8-22.2 26.1-49.4 26.1-77.7 0-12.6-1.8-25-5.4-37 16.8-22.2 26.1-49.4 26.1-77.7-.2-12.6-2-25.1-5.6-37.1zM112 528v364c0 17.7 14.3 32 32 32h65V496h-65c-17.7 0-32 14.3-32 32z"></path>
                            </svg>
                        <?php endif; ?>
                    </button>
                    <button id="favourite-btn" data-id="<?= $evenement['idEvenement'] ?>" style="background:none;border:none;vertical-align:middle;">
                        <?php if (!empty($userHasFavourited)): ?>
                            <!-- Gold star SVG -->
                            <svg width="32px" height="32px" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="50" cy="50" r="48" fill="#fff" stroke="black" stroke-width="2" />
                                <polygon points="50,20 61,39 82,42 67,57 71,78 50,67 29,78 33,57 18,42 39,39" fill="#FFD700" />
                            </svg>
                        <?php else: ?>
                            <!-- Grey star SVG -->
                            <svg width="32px" height="32px" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="50" cy="50" r="48" fill="#a99a9aff" stroke="black" stroke-width="2" />
                                <polygon points="50,20 61,39 82,42 67,57 71,78 50,67 29,78 33,57 18,42 39,39" fill="#fff" />
                            </svg>
                        <?php endif; ?>
                    </button>
                <?php endif; ?>
                <div>
                    <span id="event-likes-count"><?= $likesCount ?> personne<?= $likesCount > 1 ? 's' : '' ?> aiment cet événement</span>
                    <span id="event-comments-count" style="margin-left:1em;"><?= $commentsCount ?> commentaire<?= $commentsCount > 1 ? 's' : '' ?></span>
                </div>

                <!-- Comments Section -->
                <h3>Commentaires</h3>
                <div id="comments-list">
                    <?php
                    // $comments and $replies are passed from the controller
                    $comments = $comments ?? [];
                    $replies = $replies ?? [];
                    // Helper to group replies by parentId
                    $repliesByParent = [];
                    foreach ($replies as $reply) {
                        if ($reply['parentId']) {
                            $repliesByParent[$reply['parentId']][] = $reply;
                        }
                    }
                    foreach ($comments as $comment):
                        if ($comment['parentId']) continue; // Only top-level comments here
                    ?>
                        <div class="comment" data-id="<?= $comment['idEventComment'] ?>">
                            <b><?= htmlspecialchars($comment['firstName'] . ' ' . $comment['lastName']) ?></b>
                            <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                            <span>
                                <?php if( $comment['likesCount'] > 0):echo $comment['likesCount']; endif; ?>
                             
                            </span>
                            <?php if (isset($_SESSION['idUser'])): ?>
                                <button class="like-comment-btn" data-id="<?= $comment['idEventComment'] ?>" style="background:none;border:none;vertical-align:middle;">
                                    <!-- Blue thumb SVG for like -->
                                    <svg stroke="currentColor" fill="#0053f9ff" stroke-width="1" viewBox="0 0 1024 1024" height="20px" width="20px" style="vertical-align:middle;" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M885.9 533.7c16.8-22.2 26.1-49.4 26.1-77.7 0-44.9-25.1-87.4-65.5-111.1a67.67 67.67 0 0 0-34.3-9.3H572.4l6-122.9c1.4-29.7-9.1-57.9-29.5-79.4A106.62 106.62 0 0 0 471 99.9c-52 0-98 35-111.8 85.1l-85.9 311h-.3v428h472.3c9.2 0 18.2-1.8 26.5-5.4 47.6-20.3 78.3-66.8 78.3-118.4 0-12.6-1.8-25-5.4-37 16.8-22.2 26.1-49.4 26.1-77.7 0-12.6-1.8-25-5.4-37 16.8-22.2 26.1-49.4 26.1-77.7-.2-12.6-2-25.1-5.6-37.1zM112 528v364c0 17.7 14.3 32 32 32h65V496h-65c-17.7 0-32 14.3-32 32z"></path>
                                    </svg>
                                </button>
                                <button class="report-comment-btn" data-id="<?= $comment['idEventComment'] ?>" style="background:none;border:none;vertical-align:middle;">
                                    <!-- Red warning triangle SVG -->
                                    <svg stroke="" fill="#fff" viewBox="0 0 24 24" height="20px" width="20px" style="vertical-align:middle;" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke="black" stroke-width="0.4" d="M9.836 3.244c.963-1.665 3.365-1.665 4.328 0l8.967 15.504c.963 1.667-.24 3.752-2.165 3.752H3.034c-1.926 0-3.128-2.085-2.165-3.752Z" />
                                        <path d="M12 8.5a.75.75 0 0 0-.75.75v4.5a.75.75 0 0 0 1.5 0v-4.5A.75.75 0 0 0 12 8.5Zm1 9a1 1 0 1 0-2 0 1 1 0 0 0 2 0Z" fill="#ff0000" />
                                    </svg>
                                </button>
                                <button class="reply-comment-btn" data-id="<?= $comment['idEventComment'] ?>" style="background:none;border:none;vertical-align:middle;">
                                    <!-- Blue plus SVG for reply -->
                                    <svg stroke="currentColor" fill="#0053f9ff" stroke-width="0" viewBox="0 0 512 512" height="20px" width="20px" style="vertical-align:middle;" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M256 32C114.62 32 0 125.12 0 240c0 49.56 21.41 95 57 130.74C44.46 421.05 2.7 466 2.2 466.5A8 8 0 0 0 8 480c66.26 0 116-31.75 140.6-51.38A304.66 304.66 0 0 0 256 448c141.39 0 256-93.12 256-208S397.39 32 256 32zm96 232a8 8 0 0 1-8 8h-56v56a8 8 0 0 1-8 8h-48a8 8 0 0 1-8-8v-56h-56a8 8 0 0 1-8-8v-48a8 8 0 0 1 8-8h56v-56a8 8 0 0 1 8-8h48a8 8 0 0 1 8 8v56h56a8 8 0 0 1 8 8z"></path>
                                    </svg>
                                </button>
                                <?php if ($comment['idUser'] == $_SESSION['idUser']): ?>
                                    <button class="delete-comment-btn" data-id="<?= $comment['idEventComment'] ?>" style="background:none;border:none;vertical-align:middle;">
                                        <!-- Red trash SVG for delete -->
                                        <svg stroke="currentColor" fill="#ff0000" stroke-width="0" viewBox="0 0 1024 1024" height="20px" width="20px" style="vertical-align:middle;" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M864 256H736v-80c0-35.3-28.7-64-64-64H352c-35.3 0-64 28.7-64 64v80H160c-17.7 0-32 14.3-32 32v32c0 4.4 3.6 8 8 8h60.4l24.7 523c1.6 34.1 29.8 61 63.9 61h454c34.2 0 62.3-26.8 63.9-61l24.7-523H888c4.4 0 8-3.6 8-8v-32c0-17.7-14.3-32-32-32zm-200 0H360v-72h304v72z"></path>
                                        </svg>
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                            <!-- Replies -->
                            <?php if (!empty($repliesByParent[$comment['idEventComment']])): ?>
                                <div class="replies" id="replies-<?= $comment['idEventComment'] ?>" style="margin-left:2em;display:none;">
                                    <?php foreach ($repliesByParent[$comment['idEventComment']] as $reply): ?>
                                        <div class="comment reply" data-id="<?= $reply['idEventComment'] ?>">
                                            <b><?= htmlspecialchars($reply['firstName'] . ' ' . $reply['lastName']) ?></b>
                                            <p><?= nl2br(htmlspecialchars($reply['content'])) ?></p>
                                            <span>
                                                <?= $reply['likesCount'] ?>
                                                <svg stroke="currentColor" fill="#a99a9aff" stroke-width="1" viewBox="0 0 1024 1024" height="20px" width="20px" style="vertical-align:middle;" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M885.9 533.7c16.8-22.2 26.1-49.4 26.1-77.7 0-44.9-25.1-87.4-65.5-111.1a67.67 67.67 0 0 0-34.3-9.3H572.4l6-122.9c1.4-29.7-9.1-57.9-29.5-79.4A106.62 106.62 0 0 0 471 99.9c-52 0-98 35-111.8 85.1l-85.9 311h-.3v428h472.3c9.2 0 18.2-1.8 26.5-5.4 47.6-20.3 78.3-66.8 78.3-118.4 0-12.6-1.8-25-5.4-37 16.8-22.2 26.1-49.4 26.1-77.7 0-12.6-1.8-25-5.4-37 16.8-22.2 26.1-49.4 26.1-77.7-.2-12.6-2-25.1-5.6-37.1zM112 528v364c0 17.7 14.3 32 32 32h65V496h-65c-17.7 0-32 14.3-32 32z"></path>
                                                </svg>
                                            </span>
                                            <?php if (isset($_SESSION['idUser'])): ?>
                                                <button class="like-comment-btn" data-id="<?= $reply['idEventComment'] ?>" style="background:none;border:none;vertical-align:middle;">
                                                    <svg stroke="currentColor" fill="#0053f9ff" stroke-width="1" viewBox="0 0 1024 1024" height="20px" width="20px" style="vertical-align:middle;" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M885.9 533.7c16.8-22.2 26.1-49.4 26.1-77.7 0-44.9-25.1-87.4-65.5-111.1a67.67 67.67 0 0 0-34.3-9.3H572.4l6-122.9c1.4-29.7-9.1-57.9-29.5-79.4A106.62 106.62 0 0 0 471 99.9c-52 0-98 35-111.8 85.1l-85.9 311h-.3v428h472.3c9.2 0 18.2-1.8 26.5-5.4 47.6-20.3 78.3-66.8 78.3-118.4 0-12.6-1.8-25-5.4-37 16.8-22.2 26.1-49.4 26.1-77.7 0-12.6-1.8-25-5.4-37 16.8-22.2 26.1-49.4 26.1-77.7-.2-12.6-2-25.1-5.6-37.1zM112 528v364c0 17.7 14.3 32 32 32h65V496h-65c-17.7 0-32 14.3-32 32z"></path>
                                                    </svg>
                                                </button>
                                                <button class="report-comment-btn" data-id="<?= $reply['idEventComment'] ?>" style="background:none;border:none;vertical-align:middle;">
                                                    <svg stroke="" fill="#fff" viewBox="0 0 24 24" height="20px" width="20px" style="vertical-align:middle;" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke="black" stroke-width="0.4" d="M9.836 3.244c.963-1.665 3.365-1.665 4.328 0l8.967 15.504c.963 1.667-.24 3.752-2.165 3.752H3.034c-1.926 0-3.128-2.085-2.165-3.752Z" />
                                                        <path d="M12 8.5a.75.75 0 0 0-.75.75v4.5a.75.75 0 0 0 1.5 0v-4.5A.75.75 0 0 0 12 8.5Zm1 9a1 1 0 1 0-2 0 1 1 0 0 0 2 0Z" fill="#ff0000" />
                                                    </svg>
                                                </button>
                                                <button class="reply-comment-btn" data-id="<?= $reply['idEventComment'] ?>" style="background:none;border:none;vertical-align:middle;">
                                                    <svg stroke="currentColor" fill="#0053f9ff" stroke-width="0" viewBox="0 0 512 512" height="20px" width="20px" style="vertical-align:middle;" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M256 32C114.62 32 0 125.12 0 240c0 49.56 21.41 95 57 130.74C44.46 421.05 2.7 466 2.2 466.5A8 8 0 0 0 8 480c66.26 0 116-31.75 140.6-51.38A304.66 304.66 0 0 0 256 448c141.39 0 256-93.12 256-208S397.39 32 256 32zm96 232a8 8 0 0 1-8 8h-56v56a8 8 0 0 1-8 8h-48a8 8 0 0 1-8-8v-56h-56a8 8 0 0 1-8-8v-48a8 8 0 0 1 8-8h56v-56a8 8 0 0 1 8-8h48a8 8 0 0 1 8 8v56h56a8 8 0 0 1 8 8z"></path>
                                                    </svg>
                                                </button>
                                                <?php if ($reply['idUser'] == $_SESSION['idUser']): ?>
                                                    <button class="delete-comment-btn link" data-id="<?= $reply['idEventComment'] ?>" style="background:none;border:none;vertical-align:middle;">
                                                        <svg stroke="currentColor" fill="#ff0000" stroke-width="0" viewBox="0 0 1024 1024" height="20px" width="20px" style="vertical-align:middle;" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M864 256H736v-80c0-35.3-28.7-64-64-64H352c-35.3 0-64 28.7-64 64v80H160c-17.7 0-32 14.3-32 32v32c0 4.4 3.6 8 8 8h60.4l24.7 523c1.6 34.1 29.8 61 63.9 61h454c34.2 0 62.3-26.8 63.9-61l24.7-523H888c4.4 0 8-3.6 8-8v-32c0-17.7-14.3-32-32-32zm-200 0H360v-72h304v72z"></path>
                                                        </svg>
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <!-- Show/Hide replies button -->
                                <button class="show-replies-btn" data-id="<?= $comment['idEventComment'] ?>">
                                    Voir toutes les <?= count($repliesByParent[$comment['idEventComment']]) ?> réponse<?= count($repliesByParent[$comment['idEventComment']]) > 1 ? 's' : '' ?>
                                </button>
                            <?php endif; ?>
                            <!-- Reply form (hidden by default) -->
                            <form class="reply-form" style="display:none;margin-left:2em;">
                                <textarea name="content" required></textarea>
                                <input type="hidden" name="idEvenement" value="<?= $evenement['idEvenement'] ?>">
                                <input type="hidden" name="parentId" value="<?= $comment['idEventComment'] ?>">
                                <button type="submit">Envoyer la réponse</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (isset($_SESSION['idUser'])): ?>
                    <form id="add-comment-form">
                        <textarea name="content" required></textarea>
                        <input type="hidden" name="idEvenement" value="<?= $evenement['idEvenement'] ?>">
                        <button type="submit">Commenter</button>
                    </form>
                <?php else: ?>
                    <p>Connectez-vous pour commenter.</p>
                <?php endif; ?>

                <script>
                document.getElementById('like-btn')?.addEventListener('click', function() {
                    fetch('/evenement/like', {method:'POST', body: new URLSearchParams({idEvenement: this.dataset.id})})
                        .then(r=>r.json()).then(d=>alert(d.liked ? "Vous aimez cet événement" : "Like retiré"));
                });
                document.getElementById('favourite-btn')?.addEventListener('click', function() {
                    fetch('/evenement/favourite', {method:'POST', body: new URLSearchParams({idEvenement: this.dataset.id})})
                        .then(r=>r.json()).then(d=>alert(d.favourited ? "Ajouté aux favoris" : "Retiré des favoris"));
                });
                document.querySelectorAll('.like-comment-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        fetch('/evenement/comment/like', {method:'POST', body: new URLSearchParams({idEventComment: this.dataset.id})})
                            .then(r=>r.json()).then(d=>alert(d.liked ? "Commentaire liké" : "Like retiré"));
                    });
                });
                document.querySelectorAll('.report-comment-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        let reason = prompt("Pourquoi signalez-vous ce commentaire ?");
                        if (reason) {
                            fetch('/evenement/comment/report', {method:'POST', body: new URLSearchParams({idEventComment: this.dataset.id, reason})})
                                .then(r=>r.json()).then(d=>alert("Commentaire signalé"));
                        }
                    });
                });
                document.getElementById('add-comment-form')?.addEventListener('submit', function(e) {
                    e.preventDefault();
                    fetch('/evenement/comment', {method:'POST', body: new FormData(this)})
                        .then(r=>r.json()).then(d=>{
                            if (d.success) location.reload();
                            else alert(d.error || "Erreur");
                        });
                });
                // Reply to comment
                document.querySelectorAll('.reply-comment-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        let parentDiv = this.closest('.comment');
                        let form = parentDiv.querySelector('.reply-form');
                        form.style.display = form.style.display === 'none' ? 'block' : 'none';
                    });
                });
                document.querySelectorAll('.reply-form').forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        let data = new FormData(this);
                        fetch('/evenement/comment/reply', {method:'POST', body: data})
                            .then(r=>r.json()).then(d=>{
                                if (d.success) location.reload();
                                else alert(d.error || "Erreur");
                            });
                    });
                });
                // Delete comment
                document.querySelectorAll('.delete-comment-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        if (confirm("Supprimer ce commentaire ?")) {
                            fetch('/evenement/comment/delete', {method:'POST', body: new URLSearchParams({idEventComment: this.dataset.id})})
                                .then(r=>r.json()).then(d=>{
                                    if (d.success) location.reload();
                                    else alert(d.error || "Erreur");
                                });
                        }
                    });
                });

                // Show/hide replies on click
                document.querySelectorAll('.show-replies-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const repliesDiv = document.getElementById('replies-' + this.dataset.id);
                        if (repliesDiv) {
                            if (repliesDiv.style.display === 'none' || repliesDiv.style.display === '') {
                                repliesDiv.style.display = 'block';
                                this.textContent = "Masquer les réponses";
                            } else {
                                repliesDiv.style.display = 'none';
                                // Get reply count for label
                                const count = repliesDiv.children.length;
                                this.textContent = "Voir toutes les " + count + " réponse" + (count > 1 ? "s" : "");
                            }
                        }
                    });
                });
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