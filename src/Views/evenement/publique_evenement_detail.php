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
                    <button id="like-btn" data-id="<?= $evenement['idEvenement'] ?>" <?= !empty($userHasLiked) ? 'style="color:#007bff;font-weight:bold;"' : '' ?>>
                        👍 J'aime<?= !empty($userHasLiked) ? ' (Vous aimez)' : '' ?>
                    </button>
                    <button id="favourite-btn" data-id="<?= $evenement['idEvenement'] ?>" <?= !empty($userHasFavourited) ? 'style="color:#ffc107;font-weight:bold;"' : '' ?>>
                        ⭐ Favori<?= !empty($userHasFavourited) ? ' (Favori)' : '' ?>
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
                            <span><?= $comment['likesCount'] ?> 👍</span>
                            <?php if (isset($_SESSION['idUser'])): ?>
                                <button class="like-comment-btn" data-id="<?= $comment['idEventComment'] ?>">J'aime</button>
                                <button class="report-comment-btn" data-id="<?= $comment['idEventComment'] ?>">Signaler</button>
                                <button class="reply-comment-btn" data-id="<?= $comment['idEventComment'] ?>">Répondre</button>
                                <?php if ($comment['idUser'] == $_SESSION['idUser']): ?>
                                    <button class="delete-comment-btn" data-id="<?= $comment['idEventComment'] ?>">Supprimer</button>
                                <?php endif; ?>
                            <?php endif; ?>
                            <!-- Replies -->
                            <?php if (!empty($repliesByParent[$comment['idEventComment']])): ?>
                                <div class="replies" style="margin-left:2em;">
                                    <?php foreach ($repliesByParent[$comment['idEventComment']] as $reply): ?>
                                        <div class="comment reply" data-id="<?= $reply['idEventComment'] ?>">
                                            <b><?= htmlspecialchars($reply['firstName'] . ' ' . $reply['lastName']) ?></b>
                                            <p><?= nl2br(htmlspecialchars($reply['content'])) ?></p>
                                            <span><?= $reply['likesCount'] ?> 👍</span>
                                            <?php if (isset($_SESSION['idUser'])): ?>
                                                <button class="like-comment-btn" data-id="<?= $reply['idEventComment'] ?>">J'aime</button>
                                                <button class="report-comment-btn" data-id="<?= $reply['idEventComment'] ?>">Signaler</button>
                                                <?php if ($reply['idUser'] == $_SESSION['idUser']): ?>
                                                    <button class="delete-comment-btn" data-id="<?= $reply['idEventComment'] ?>">Supprimer</button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
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