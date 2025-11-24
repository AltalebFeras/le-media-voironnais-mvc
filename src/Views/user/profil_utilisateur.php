<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/users/profil-utilisateur.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="dashboard-container">
    <div class="profile-container">
        <!-- Banner Section -->
        <div class="profile-banner">
            <img 
                src="<?= htmlspecialchars($user->getBannerPath() ?? BASE_URL . HOME_URL . 'assets/images/uploads/banners/default_banner.jpg') ?>" 
                alt="Bannière de profil"
                class="banner-image"
            >
        </div>

        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="profile-avatar-section">
                    <img 
                        src="<?= htmlspecialchars($user->getAvatarPath() ?? BASE_URL . HOME_URL . 'assets/images/uploads/avatars/default_avatar.png') ?>" 
                        alt="Avatar"
                        class="profile-avatar"
                    >
                    <div class="profile-info">
                        <h1 class="profile-name">
                            <?= htmlspecialchars($user->getFirstName() . ' ' . $user->getLastName()) ?>
                        </h1>
                        <div class="profile-status">
                            <?php if ($user->getIsOnline()): ?>
                                <span class="status-online">En ligne</span>
                            <?php else: ?>
                                <span class="status-offline">
                                    Vu <?= date('d/m/Y', strtotime($user->getLastSeen())) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <?php if ($user->getBio()): ?>
                            <p class="profile-bio"><?= htmlspecialchars($user->getBio()) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Action Buttons -->
                <?php if (!$isOwnProfile && isset($_SESSION['idUser'])): ?>
                    <div class="profile-actions">
                        <?php if ($friendshipStatus === 'friends'): ?>
                            <a href="<?= HOME_URL ?>messages?user=<?= htmlspecialchars($user->getUiid()) ?>" class="btn btn-primary">
                                <span class="material-icons">message</span>
                                Envoyer un message
                            </a>
                            <button 
                                class="btn btn-secondary friend-options-btn"
                                data-friend-uiid="<?= htmlspecialchars($user->getUiid()) ?>"
                                data-friend-name="<?= htmlspecialchars($user->getFirstName() . ' ' . $user->getLastName()) ?>"
                            >
                                <span class="material-icons">more_vert</span>
                            </button>
                        <?php elseif ($friendshipStatus === 'request_sent'): ?>
                            <button class="btn btn-disabled" disabled>
                                <span class="material-icons">schedule</span>
                                Demande envoyée
                            </button>
                        <?php elseif ($friendshipStatus === 'request_received'): ?>
                            <form method="POST" action="<?= HOME_URL ?>amis/accepter" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                                <input type="hidden" name="friend_uiid" value="<?= htmlspecialchars($user->getUiid()) ?>">
                                <button type="submit" class="btn btn-success">
                                    <span class="material-icons">person_add</span>
                                    Accepter la demande
                                </button>
                            </form>
                            <form method="POST" action="<?= HOME_URL ?>amis/refuser" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                                <input type="hidden" name="friend_uiid" value="<?= htmlspecialchars($user->getUiid()) ?>">
                                <button type="submit" class="btn btn-secondary">
                                    <span class="material-icons">close</span>
                                    Refuser
                                </button>
                            </form>
                        <?php elseif ($friendshipStatus === 'blocked_by_me'): ?>
                            <button class="btn btn-disabled" disabled>
                                <span class="material-icons">block</span>
                                Utilisateur bloqué
                            </button>
                        <?php elseif ($friendshipStatus === 'blocked_by_them'): ?>
                            <button class="btn btn-disabled" disabled>
                                <span class="material-icons">block</span>
                                Profil indisponible
                            </button>
                        <?php else: ?>
                            <form method="POST" action="<?= HOME_URL ?>amis/ajouter" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                                <input type="hidden" name="friend_uiid" value="<?= htmlspecialchars($user->getUiid()) ?>">
                                <button type="submit" class="btn btn-primary">
                                    <span class="material-icons">person_add</span>
                                    Ajouter en ami
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php elseif ($isOwnProfile): ?>
                    <div class="profile-actions">
                        <a href="<?= HOME_URL ?>mon_compte" class="btn btn-primary">
                            <span class="material-icons">edit</span>
                            Modifier mon profil
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="profile-details">
            <div class="details-card">
                <h2 class="details-title">
                    <span class="material-icons">info</span>
                    Informations
                </h2>
                <div class="details-content">
                    <?php if ($user->getPhone()): ?>
                        <div class="detail-item">
                            <span class="material-icons">phone</span>
                            <span class="detail-label">Téléphone:</span>
                            <span class="detail-value"><?= htmlspecialchars($user->getPhone()) ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($user->getDateOfBirth()): ?>
                        <div class="detail-item">
                            <span class="material-icons">cake</span>
                            <span class="detail-label">Date de naissance:</span>
                            <span class="detail-value"><?= htmlspecialchars($user->getDateOfBirthFormatted()) ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="detail-item">
                        <span class="material-icons">calendar_today</span>
                        <span class="detail-label">Membre depuis:</span>
                        <span class="detail-value"><?= date('F Y', strtotime($user->getCreatedAt())) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Friend Options Modal (reuse from mes_amis.php) -->
<?php if (!$isOwnProfile && $friendshipStatus === 'friends'): ?>
<div id="friendOptionsModal" class="modal-overlay options-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="friendOptionsTitle" class="modal-title"><?= htmlspecialchars($user->getFirstName() . ' ' . $user->getLastName()) ?></h3>
            <button type="button" onclick="closeFriendOptionsModal()" class="modal-close">
                <span class="material-icons">close</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="option-actions">
                <form method="POST" id="removeFriendForm" action="<?= HOME_URL ?>amis/supprimer" style="margin: 0;">
                    <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                    <input type="hidden" name="friend_uiid" value="<?= htmlspecialchars($user->getUiid()) ?>">
                    <button 
                        type="submit"
                        class="option-btn remove-btn"
                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet ami ?')"
                    >
                        <span class="material-icons">person_remove</span>
                        Supprimer de mes amis
                    </button>
                </form>
                
                <form method="POST" id="blockFriendForm" action="<?= HOME_URL ?>amis/bloquer" style="margin: 0;">
                    <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                    <input type="hidden" name="friend_uiid" value="<?= htmlspecialchars($user->getUiid()) ?>">
                    <button 
                        type="submit"
                        class="option-btn block-btn"
                        onclick="return confirm('Êtes-vous sûr de vouloir bloquer cet utilisateur ?')"
                    >
                        <span class="material-icons">block</span>
                        Bloquer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
