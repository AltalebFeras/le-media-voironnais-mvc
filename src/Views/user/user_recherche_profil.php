<?php
$title = htmlspecialchars($user['firstName'] . ' ' . $user['lastName']);
$description = $user['bio'] ?? 'Profil utilisateur sur Le Média Voironnais';
?>

<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- User Banner -->
            <?php if ($user['bannerPath']): ?>
                <div class="profile-banner mb-4">
                    <img src="<?= htmlspecialchars($user['bannerPath']) ?>" alt="Bannière de profil" class="img-fluid w-100" style="height: 300px; object-fit: cover; border-radius: 8px;">
                </div>
            <?php endif; ?>

            <!-- User Profile Card -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <img src="<?= $user['avatarPath'] ?? DOMAIN . HOME_URL . 'assets/images/default-avatar.png' ?>" 
                                 alt="Avatar de <?= htmlspecialchars($user['firstName']) ?>" 
                                 class="rounded-circle img-fluid" 
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        </div>
                        <div class="col-md-9">
                            <h1 class="h2 mb-2"><?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?></h1>
                            
                            <?php if ($user['bio']): ?>
                                <p class="text-muted mb-3"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
                            <?php endif; ?>

                            <div class="row">
                                <?php if ($user['dateOfBirth']): ?>
                                    <div class="col-sm-6 mb-2">
                                        <small class="text-muted">
                                            <i class="material-icons me-1" style="font-size: 16px;">cake</i>
                                            <?= date('d/m/Y', strtotime($user['dateOfBirth'])) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>

                                <?php if ($user['phone']): ?>
                                    <div class="col-sm-6 mb-2">
                                        <small class="text-muted">
                                            <i class="material-icons me-1" style="font-size: 16px;">phone</i>
                                            <?= htmlspecialchars($user['phone']) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>

                                <div class="col-sm-6 mb-2">
                                    <small class="text-muted">
                                        <i class="material-icons me-1" style="font-size: 16px;">schedule</i>
                                        Membre depuis <?= date('M Y', strtotime($user['createdAt'])) ?>
                                    </small>
                                </div>

                                <?php if ($user['isOnline']): ?>
                                    <div class="col-sm-6 mb-2">
                                        <small class="text-success">
                                            <i class="material-icons me-1" style="font-size: 16px;">fiber_manual_record</i>
                                            En ligne
                                        </small>
                                    </div>
                                <?php elseif ($user['lastSeen']): ?>
                                    <div class="col-sm-6 mb-2">
                                        <small class="text-muted">
                                            <i class="material-icons me-1" style="font-size: 16px;">schedule</i>
                                            Vu le <?= date('d/m/Y à H:i', strtotime($user['lastSeen'])) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Stats Row -->
                            <div class="row mt-3">
                                <div class="col-sm-4">
                                    <div class="text-center">
                                        <h5 class="mb-0 text-primary"><?= $user['eventCount'] ?></h5>
                                        <small class="text-muted">Événements</small>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="text-center">
                                        <h5 class="mb-0 text-success"><?= $user['associationCount'] ?></h5>
                                        <small class="text-muted">Associations</small>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="text-center">
                                        <h5 class="mb-0 text-info"><?= $user['enterpriseCount'] ?></h5>
                                        <small class="text-muted">Entreprises</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User's Recent Activity -->
            <div class="row mt-4">
                <?php if (!empty($userEvents)): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="material-icons me-2">event</i>
                                    Événements récents
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($userEvents as $event): ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="<?= $event['bannerPath'] ?? DOMAIN . HOME_URL . 'assets/images/default-event.png' ?>" 
                                             alt="<?= htmlspecialchars($event['title']) ?>" 
                                             class="rounded me-3" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="<?= HOME_URL . 'evenements/' . $event['slug'] ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($event['title']) ?>
                                                </a>
                                            </h6>
                                            <small class="text-muted">
                                                <?= date('d/m/Y', strtotime($event['startDate'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($userAssociations)): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="material-icons me-2">groups</i>
                                    Associations
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($userAssociations as $association): ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="<?= $association['logoPath'] ?? DOMAIN . HOME_URL . 'assets/images/default-association.png' ?>" 
                                             alt="<?= htmlspecialchars($association['name']) ?>" 
                                             class="rounded me-3" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="<?= HOME_URL . 'associations/' . $association['slug'] ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($association['name']) ?>
                                                </a>
                                            </h6>
                                            <small class="text-muted">
                                                <?= ucfirst($association['role']) ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
