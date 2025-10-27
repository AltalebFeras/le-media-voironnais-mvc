<?php
$title = htmlspecialchars($association['name']);
$description = $association['description'] ? substr(strip_tags($association['description']), 0, 160) : 'Association sur Le Média Voironnais';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Association Banner -->
            <?php if ($association['bannerPath']): ?>
                <div class="association-banner mb-4">
                    <img src="<?= htmlspecialchars($association['bannerPath']) ?>" 
                         alt="Bannière <?= htmlspecialchars($association['name']) ?>" 
                         class="img-fluid w-100" 
                         style="height: 300px; object-fit: cover; border-radius: 8px;">
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Association Details -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-4">
                                <img src="<?= $association['logoPath'] ?? DOMAIN . HOME_URL . 'assets/images/default-association.png' ?>" 
                                     alt="Logo <?= htmlspecialchars($association['name']) ?>" 
                                     class="rounded me-4" 
                                     style="width: 100px; height: 100px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h1 class="h2 mb-2"><?= htmlspecialchars($association['name']) ?></h1>
                                            <span class="badge bg-info text-dark">
                                                <i class="material-icons me-1" style="font-size: 14px;">groups</i>
                                                Association
                                            </span>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">
                                                Créée en <?= date('M Y', strtotime($association['createdAt'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="row mb-4">
                                <?php if ($association['address']): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="material-icons text-primary me-3">location_on</i>
                                            <div>
                                                <strong>Adresse</strong><br>
                                                <span class="text-muted"><?= htmlspecialchars($association['address']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($association['phone']): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="material-icons text-primary me-3">phone</i>
                                            <div>
                                                <strong>Téléphone</strong><br>
                                                <a href="tel:<?= htmlspecialchars($association['phone']) ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($association['phone']) ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($association['email']): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="material-icons text-primary me-3">email</i>
                                            <div>
                                                <strong>Email</strong><br>
                                                <a href="mailto:<?= htmlspecialchars($association['email']) ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($association['email']) ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($association['website']): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="material-icons text-primary me-3">language</i>
                                            <div>
                                                <strong>Site web</strong><br>
                                                <a href="<?= htmlspecialchars($association['website']) ?>" 
                                                   target="_blank" 
                                                   class="text-decoration-none">
                                                    <?= htmlspecialchars($association['website']) ?>
                                                    <i class="material-icons ms-1" style="font-size: 16px;">open_in_new</i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Description -->
                            <?php if ($association['description']): ?>
                                <div class="mb-4">
                                    <h4>À propos de l'association</h4>
                                    <div class="text-content">
                                        <?= nl2br(htmlspecialchars($association['description'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Members -->
                            <?php if (!empty($members)): ?>
                                <div class="border-top pt-4">
                                    <h4 class="mb-3">
                                        <i class="material-icons me-2">people</i>
                                        Membres (<?= count($members) ?>)
                                    </h4>
                                    <div class="row">
                                        <?php foreach (array_slice($members, 0, 6) as $member): ?>
                                            <div class="col-md-4 mb-3">
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= $member['avatarPath'] ?? DOMAIN . HOME_URL . 'assets/images/default-avatar.png' ?>" 
                                                         alt="<?= htmlspecialchars($member['firstName']) ?>" 
                                                         class="rounded-circle me-3" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <a href="<?= HOME_URL . 'profil/' . $member['slug'] ?>" class="text-decoration-none">
                                                                <?= htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) ?>
                                                            </a>
                                                        </h6>
                                                        <small class="text-muted"><?= ucfirst($member['role']) ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php if (count($members) > 6): ?>
                                        <div class="text-center mt-3">
                                            <button class="btn btn-outline-primary">
                                                Voir tous les membres
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Recent Events -->
                    <?php if (!empty($associationEvents)): ?>
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h4 class="mb-0">
                                    <i class="material-icons me-2">event</i>
                                    Événements récents
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach (array_slice($associationEvents, 0, 4) as $event): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100">
                                                <?php if ($event['bannerPath']): ?>
                                                    <img src="<?= htmlspecialchars($event['bannerPath']) ?>" 
                                                         class="card-img-top" 
                                                         alt="<?= htmlspecialchars($event['title']) ?>"
                                                         style="height: 150px; object-fit: cover;">
                                                <?php endif; ?>
                                                <div class="card-body">
                                                    <h6 class="card-title">
                                                        <a href="<?= HOME_URL . 'evenements/' . $event['slug'] ?>" class="text-decoration-none">
                                                            <?= htmlspecialchars($event['title']) ?>
                                                        </a>
                                                    </h6>
                                                    <small class="text-muted">
                                                        <i class="material-icons me-1" style="font-size: 16px;">schedule</i>
                                                        <?= date('d/m/Y', strtotime($event['startDate'])) ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <!-- Join Association Card -->
                    <div class="card shadow-sm sticky-top" style="top: 100px;">
                        <div class="card-header text-center">
                            <h5 class="mb-0">Rejoindre l'association</h5>
                        </div>
                        <div class="card-body text-center">
                            <p class="text-muted mb-3">
                                Participez aux activités et projets de l'association !
                            </p>
                            
                            <button class="btn btn-primary btn-lg w-100 mb-3" onclick="joinAssociation()">
                                <i class="material-icons me-2">group_add</i>
                                Demander à rejoindre
                            </button>

                            <div class="d-flex gap-2 justify-content-center">
                                <?php if ($association['phone']): ?>
                                    <a href="tel:<?= htmlspecialchars($association['phone']) ?>" 
                                       class="btn btn-outline-primary" 
                                       title="Appeler">
                                        <i class="material-icons">phone</i>
                                    </a>
                                <?php endif; ?>

                                <?php if ($association['email']): ?>
                                    <a href="mailto:<?= htmlspecialchars($association['email']) ?>" 
                                       class="btn btn-outline-primary" 
                                       title="Email">
                                        <i class="material-icons">email</i>
                                    </a>
                                <?php endif; ?>

                                <button class="btn btn-outline-primary" onclick="shareAssociation()" title="Partager">
                                    <i class="material-icons">share</i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
