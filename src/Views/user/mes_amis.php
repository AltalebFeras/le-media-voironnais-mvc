<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/globals/mes-amis.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="dashboard-container">
    <div class="friends-container">
        <!-- Header -->
        <div class="friends-header">
            <h1 class="friends-title">Mes amis</h1>
            <button onclick="openSearchModal()" class="add-friend-btn">
                <span class="material-icons">person_add</span>
                Ajouter un ami
            </button>
        </div>

        <!-- Tabs -->
        <div class="friends-tabs">
            <div class="tabs-nav">
                <button class="tab-btn active" data-tab="friends">
                    Mes amis (<?= count($friends) ?>)
                </button>
                <button class="tab-btn" data-tab="requests">
                    Demandes reçues (<?= count($pendingRequests) ?>)
                </button>
                <button class="tab-btn" data-tab="sent">
                    Demandes envoyées (<?= count($sentRequests) ?>)
                </button>
            </div>
        </div>

        <!-- Friends List -->
        <div id="friends-tab" class="tab-content active">
            <?php if (empty($friends)): ?>
                <div class="empty-state">
                    <span class="material-icons">group</span>
                    <h3 class="empty-state-title">Aucun ami pour le moment</h3>
                    <p class="empty-state-text">Commencez à ajouter des amis pour élargir votre réseau !</p>
                </div>
            <?php else: ?>
                <div class="friends-grid">
                    <?php foreach ($friends as $friend): ?>
                        <div class="friend-card">
                            <div class="friend-info">
                                <img
                                    src="<?= htmlspecialchars($friend['avatarPath'] ?? BASE_URL . HOME_URL . 'assets/images/uploads/avatars/default_avatar.png') ?>"
                                    alt="Avatar"
                                    class="friend-avatar">
                                <div class="friend-details">
                                    <h3 class="friend-name">
                                        <?= htmlspecialchars($friend['firstName'] . ' ' . $friend['lastName']) ?>
                                    </h3>
                                    <?php if ($friend['bio']): ?>
                                        <p class="friend-bio"><?= htmlspecialchars($friend['bio']) ?></p>
                                    <?php endif; ?>
                                    <div class="friend-status">
                                        <?php if ($friend['isOnline']): ?>
                                            <span class="status-online">En ligne</span>
                                        <?php else: ?>
                                            <span class="status-offline">
                                                Vu <?= date('d/m/Y', strtotime($friend['lastSeen'])) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="friend-actions">
                                <a
                                    href="<?= HOME_URL ?>profil/<?= htmlspecialchars($friend['slug']) ?>"
                                    class="profile-btn">
                                    Voir profil
                                </a>
                                <button
                                    class="options-btn friend-options-btn"
                                    data-friend-uiid="<?= htmlspecialchars($friend['uiid']) ?>"
                                    data-friend-name="<?= htmlspecialchars($friend['firstName'] . ' ' . $friend['lastName']) ?>">
                                    <span class="material-icons">more_vert</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php include __DIR__ . '/../includes/pagination.php'; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Pending Requests Tab -->
        <div id="requests-tab" class="tab-content">
            <?php if (empty($pendingRequests)): ?>
                <div class="empty-state">
                    <span class="material-icons">inbox</span>
                    <h3 class="empty-state-title">Aucune demande en attente</h3>
                </div>
            <?php else: ?>
                <div class="request-list">
                    <?php foreach ($pendingRequests as $request): ?>
                        <div class="request-card">
                            <div class="request-info">
                                <img
                                    src="<?= htmlspecialchars($request['avatarPath'] ?? BASE_URL . HOME_URL . 'assets/images/uploads/avatars/default_avatar.png') ?>"
                                    alt="Avatar"
                                    class="request-avatar">
                                <div>
                                    <h3 class="request-name">
                                        <?= htmlspecialchars($request['firstName'] . ' ' . $request['lastName']) ?>
                                    </h3>
                                    <p class="request-date">
                                        Demande envoyée le <?= date('d/m/Y à H:i', strtotime($request['requestedAt'])) ?>
                                    </p>
                                </div>
                            </div>

                            <div class="request-actions">
                                <form method="POST" action="<?= HOME_URL ?>amis/accepter" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                                    <input type="hidden" name="friend_uiid" value="<?= htmlspecialchars($request['uiid']) ?>">
                                    <button type="submit" class="accept-btn">
                                        Accepter
                                    </button>
                                </form>
                                <form method="POST" action="<?= HOME_URL ?>amis/refuser" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                                    <input type="hidden" name="friend_uiid" value="<?= htmlspecialchars($request['uiid']) ?>">
                                    <button type="submit" class="refuse-btn">
                                        Refuser
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sent Requests Tab -->
        <div id="sent-tab" class="tab-content">
            <?php if (empty($sentRequests)): ?>
                <div class="empty-state">
                    <span class="material-icons">send</span>
                    <h3 class="empty-state-title">Aucune demande envoyée</h3>
                </div>
            <?php else: ?>
                <div class="request-list">
                    <?php foreach ($sentRequests as $sent): ?>
                        <div class="request-card">
                            <div class="request-info">
                                <img
                                    src="<?= htmlspecialchars($sent['avatarPath'] ?? BASE_URL . HOME_URL . 'assets/images/uploads/avatars/default_avatar.png') ?>"
                                    alt="Avatar"
                                    class="request-avatar">
                                <div>
                                    <h3 class="request-name">
                                        <?= htmlspecialchars($sent['firstName'] . ' ' . $sent['lastName']) ?>
                                    </h3>
                                    <p class="request-date">
                                        Demande envoyée le <?= date('d/m/Y à H:i', strtotime($sent['requestedAt'])) ?>
                                    </p>
                                </div>
                            </div>

                            <span class="status-badge <?= $sent['status'] === 'en_attente' ? 'status-pending' : 'status-refused' ?>">
                                <?= $sent['status'] === 'en_attente' ? 'En attente' : 'Refusée' ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Search Modal -->
    <div id="searchModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Rechercher un ami</h3>
                <button type="button" onclick="closeSearchModal()" class="modal-close">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <div class="modal-body">
                <input
                    type="text"
                    id="userSearch"
                    placeholder="Rechercher par nom..."
                    class="search-input"
                    autocomplete="off">

                <div class="searchFriendsResults"></div>
            </div>
        </div>
    </div>

    <!-- Friend Options Modal -->
    <div id="friendOptionsModal" class="modal-overlay options-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="friendOptionsTitle" class="modal-title"></h3>
                <button type="button" onclick="closeFriendOptionsModal()" class="modal-close">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="option-actions">
                    <form method="POST" id="removeFriendForm" style="margin: 0;">
                        <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                        <input type="hidden" name="friend_uiid" id="removeFriendUiid">
                        <button
                            type="submit"
                            class="option-btn remove-btn"
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet ami ?')">
                            <span class="material-icons">person_remove</span>
                            Supprimer de mes amis
                        </button>
                    </form>

                    <form method="POST" id="blockFriendForm" style="margin: 0;">
                        <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                        <input type="hidden" name="friend_uiid" id="blockFriendUiid">
                        <button
                            type="submit"
                            class="option-btn block-btn"
                            onclick="return confirm('Êtes-vous sûr de vouloir bloquer cet utilisateur ?')">
                            <span class="material-icons">block</span>
                            Bloquer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>