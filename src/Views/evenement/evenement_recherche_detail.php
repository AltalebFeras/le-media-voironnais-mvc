<?php
$title = htmlspecialchars($event['title']);
$description = $event['shortDescription'] ?? substr(strip_tags($event['description']), 0, 160);
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Event Banner -->
            <?php if ($event['bannerPath']): ?>
                <div class="event-banner mb-4">
                    <img src="<?= htmlspecialchars($event['bannerPath']) ?>" 
                         alt="<?= htmlspecialchars($event['title']) ?>" 
                         class="img-fluid w-100" 
                         style="height: 400px; object-fit: cover; border-radius: 8px;">
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Event Details -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h1 class="h2 mb-2"><?= htmlspecialchars($event['title']) ?></h1>
                                    <?php if ($event['shortDescription']): ?>
                                        <p class="text-muted lead"><?= htmlspecialchars($event['shortDescription']) ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($event['price'] > 0): ?>
                                    <div class="badge bg-primary fs-6 p-2">
                                        <?= number_format($event['price'], 2) ?> <?= $event['currency'] ?>
                                    </div>
                                <?php else: ?>
                                    <div class="badge bg-success fs-6 p-2">Gratuit</div>
                                <?php endif; ?>
                            </div>

                            <!-- Event Info Grid -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="material-icons text-primary me-3">schedule</i>
                                        <div>
                                            <strong>Date de début</strong><br>
                                            <span class="text-muted">
                                                <?= date('d/m/Y à H:i', strtotime($event['startDate'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($event['endDate']): ?>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="material-icons text-primary me-3">event_available</i>
                                            <div>
                                                <strong>Date de fin</strong><br>
                                                <span class="text-muted">
                                                    <?= date('d/m/Y à H:i', strtotime($event['endDate'])) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="material-icons text-primary me-3">location_on</i>
                                        <div>
                                            <strong>Lieu</strong><br>
                                            <span class="text-muted"><?= htmlspecialchars($event['address']) ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="material-icons text-primary me-3">people</i>
                                        <div>
                                            <strong>Participants</strong><br>
                                            <span class="text-muted">
                                                <?= $event['currentParticipants'] ?> / <?= $event['maxParticipants'] ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Event Description -->
                            <?php if ($event['description']): ?>
                                <div class="mb-4">
                                    <h4>Description</h4>
                                    <div class="text-content">
                                        <?= nl2br(htmlspecialchars($event['description'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Event Images -->
                            <?php if (!empty($eventImages)): ?>
                                <div class="mb-4">
                                    <h4>Galerie</h4>
                                    <div class="row">
                                        <?php foreach ($eventImages as $image): ?>
                                            <div class="col-md-4 mb-3">
                                                <img src="<?= htmlspecialchars($image['imagePath']) ?>" 
                                                     alt="<?= htmlspecialchars($image['altText'] ?? $event['title']) ?>" 
                                                     class="img-fluid rounded" 
                                                     style="height: 200px; width: 100%; object-fit: cover;">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Organizer Info -->
                            <?php if ($organizer): ?>
                                <div class="border-top pt-3">
                                    <h5>Organisé par</h5>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= $organizer['avatarPath'] ?? DOMAIN . HOME_URL . 'assets/images/default-avatar.png' ?>" 
                                             alt="<?= htmlspecialchars($organizer['firstName']) ?>" 
                                             class="rounded-circle me-3" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="<?= HOME_URL . 'profil/' . $organizer['slug'] ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($organizer['firstName'] . ' ' . $organizer['lastName']) ?>
                                                </a>
                                            </h6>
                                            <small class="text-muted">Organisateur</small>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <?php if (!empty($comments)): ?>
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="material-icons me-2">comment</i>
                                    Commentaires (<?= count($comments) ?>)
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php foreach (array_slice($comments, 0, 5) as $comment): ?>
                                    <div class="d-flex mb-3">
                                        <img src="<?= $comment['avatarPath'] ?? DOMAIN . HOME_URL . 'assets/images/default-avatar.png' ?>" 
                                             alt="<?= htmlspecialchars($comment['firstName']) ?>" 
                                             class="rounded-circle me-3" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h6 class="mb-1"><?= htmlspecialchars($comment['firstName'] . ' ' . $comment['lastName']) ?></h6>
                                                <small class="text-muted"><?= date('d/m/Y à H:i', strtotime($comment['createdAt'])) ?></small>
                                            </div>
                                            <p class="mb-1"><?= htmlspecialchars($comment['content']) ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                
                                <?php if (count($comments) > 5): ?>
                                    <div class="text-center">
                                        <button class="btn btn-outline-primary">
                                            Voir tous les commentaires
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <!-- Registration Card -->
                    <div class="card shadow-sm sticky-top" style="top: 100px;">
                        <div class="card-body text-center">
                            <?php if ($event['currentParticipants'] >= $event['maxParticipants']): ?>
                                <div class="alert alert-warning">
                                    <i class="material-icons me-2">warning</i>
                                    Événement complet
                                </div>
                            <?php elseif ($event['registrationDeadline'] && strtotime($event['registrationDeadline']) < time()): ?>
                                <div class="alert alert-danger">
                                    <i class="material-icons me-2">schedule</i>
                                    Inscriptions fermées
                                </div>
                            <?php else: ?>
                                <?php if (isset($_SESSION['connected'])): ?>
                                    <button class="btn btn-primary btn-lg w-100 mb-3" onclick="registerForEvent()">
                                        <i class="material-icons me-2">person_add</i>
                                        S'inscrire à l'événement
                                    </button>
                                <?php else: ?>
                                    <a href="<?= HOME_URL ?>connexion" class="btn btn-primary btn-lg w-100 mb-3">
                                        <i class="material-icons me-2">login</i>
                                        Se connecter pour s'inscrire
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>

                            <div class="text-center mb-3">
                                <small class="text-muted">
                                    <?= max(0, $event['maxParticipants'] - $event['currentParticipants']) ?> places restantes
                                </small>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 justify-content-center">
                                <?php if (isset($_SESSION['connected'])): ?>
                                    <button class="btn btn-outline-primary" onclick="toggleLike()" title="J'aime">
                                        <i class="material-icons">favorite_border</i>
                                    </button>
                                    <button class="btn btn-outline-primary" onclick="toggleFavorite()" title="Ajouter aux favoris">
                                        <i class="material-icons">bookmark_border</i>
                                    </button>
                                <?php endif; ?>
                                <button class="btn btn-outline-primary" onclick="shareEvent()" title="Partager">
                                    <i class="material-icons">share</i>
                                </button>
                            </div>

                            <!-- Participants Preview -->
                            <?php if (!empty($participants)): ?>
                                <div class="mt-4">
                                    <h6>Participants</h6>
                                    <div class="d-flex justify-content-center flex-wrap">
                                        <?php foreach (array_slice($participants, 0, 8) as $participant): ?>
                                            <img src="<?= $participant['avatarPath'] ?? DOMAIN . HOME_URL . 'assets/images/default-avatar.png' ?>" 
                                                 alt="<?= htmlspecialchars($participant['firstName']) ?>" 
                                                 class="rounded-circle me-1 mb-1" 
                                                 style="width: 30px; height: 30px; object-fit: cover;"
                                                 title="<?= htmlspecialchars($participant['firstName'] . ' ' . $participant['lastName']) ?>">
                                        <?php endforeach; ?>
                                        <?php if (count($participants) > 8): ?>
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" 
                                                 style="width: 30px; height: 30px; font-size: 12px;">
                                                +<?= count($participants) - 8 ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function registerForEvent() {
    // Implementation for event registration
    console.log('Register for event');
}

function toggleLike() {
    // Implementation for like toggle
    console.log('Toggle like');
}

function toggleFavorite() {
    // Implementation for favorite toggle
    console.log('Toggle favorite');
}

function shareEvent() {
    // Implementation for sharing
    if (navigator.share) {
        navigator.share({
            title: '<?= addslashes($event['title']) ?>',
            url: window.location.href
        });
    }
}
</script>
