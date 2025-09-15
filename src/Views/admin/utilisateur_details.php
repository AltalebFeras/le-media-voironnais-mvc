<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/utilisateur_details.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar_admin.php'; ?>

<main>
    <div class="container py-4">

        <h1>Détails de l'utilisateur</h1>
        
        <?php if (isset($user) && is_array($user)): ?>
            <?php include_once __DIR__ . '/../includes/messages.php'; ?>
            <?php
            // Sanitize user data for display and create safe URL/id values
            $rawId = $user['id'] ?? '';
            $id = htmlspecialchars($rawId);
            $idUrl = urlencode($rawId);

            $firstName = htmlspecialchars($user['firstName'] ?? '');
            $lastName = htmlspecialchars($user['lastName'] ?? '');
            $email = htmlspecialchars($user['email'] ?? '');
            $phone = htmlspecialchars($user['phone'] ?? '');
            $roleName = htmlspecialchars($user['roleName'] ?? '');
            $bioRaw = $user['bio'] ?? '';
            $bio = htmlspecialchars($bioRaw);

            // Helper to safely format dates (avoids warnings on invalid/empty values)
            $formatDate = function($value, $format = 'd/m/Y à H:i', $default = 'Jamais') {
                if (empty($value)) return $default;
                try {
                    $dt = new DateTime($value);
                    return $dt->format($format);
                } catch (Exception $e) {
                    return $default;
                }
            };

            // Avatar/banner: accept absolute URLs or root-relative paths; otherwise prepend HOME_URL
            $avatarPathRaw = $user['avatarPath'] ?? '';
            if (!empty($avatarPathRaw) && (strpos($avatarPathRaw, 'http') === 0 || strpos($avatarPathRaw, '/') === 0)) {
                $avatarPath = htmlspecialchars($avatarPathRaw);
            } elseif (!empty($avatarPathRaw)) {
                $avatarPath = htmlspecialchars(HOME_URL . ltrim($avatarPathRaw, '/'));
            } else {
                $avatarPath = htmlspecialchars(HOME_URL . 'assets/images/uploads/avatars/default_avatar.png');
            }

            $bannerPathRaw = $user['bannerPath'] ?? '';
            if (!empty($bannerPathRaw) && (strpos($bannerPathRaw, 'http') === 0 || strpos($bannerPathRaw, '/') === 0)) {
                $bannerPath = htmlspecialchars($bannerPathRaw);
            } elseif (!empty($bannerPathRaw)) {
                $bannerPath = htmlspecialchars(HOME_URL . ltrim($bannerPathRaw, '/'));
            } else {
                $bannerPath = null;
            }

            $dateOfBirth = $formatDate($user['dateOfBirth'] ?? null, 'd/m/Y', 'Non renseignée');
            $isActivated = isset($user['isActivated']) ? ((int)$user['isActivated'] === 1 ? 'Oui' : 'Non') : 'Inconnu';
            $isBanned = isset($user['isBanned']) ? ((int)$user['isBanned'] === 1 ? 'Oui' : 'Non') : 'Inconnu';
            $isOnline = isset($user['isOnline']) ? ((int)$user['isOnline'] === 1 ? 'Oui' : 'Non') : 'Inconnu';

            $lastSeen = $formatDate($user['lastSeen'] ?? null);
            $rgpdAcceptedDate = $formatDate($user['rgpdAcceptedDate'] ?? null);
            $createdAt = $formatDate($user['createdAt'] ?? null, 'd/m/Y à H:i', 'Inconnu');
            $updatedAt = $formatDate($user['updatedAt'] ?? null);
            $emailChangedAt = $formatDate($user['emailChangedAt'] ?? null);
            $passwordResetAt = $formatDate($user['passwordResetAt'] ?? null);
            $deletedAt = $formatDate($user['deletedAt'] ?? null, 'd/m/Y à H:i', 'N/A');
            ?>

            <div class="user-panel">
                <div class="user-info">
                    <?php if ($bannerPath): ?>
                        <div class="banner"><img src="<?= $bannerPath; ?>" alt="Bannière de <?= $firstName . ' ' . $lastName; ?>" style="max-width:100%; border-radius:6px;"></div>
                    <?php endif; ?>
                    <div style="display:flex; gap:12px; align-items:center; margin-top:12px;">
                        <div class="avatar"><img src="<?= $avatarPath; ?>" alt="Avatar" style="width:96px; height:96px; object-fit:cover; border-radius:50%;"></div>
                        <div>
                            <h2 style="margin:0;"><?= ($firstName || $lastName) ? $firstName . ' ' . $lastName : 'Utilisateur #' . $id; ?></h2>
                            <div style="color:#666;"><?= $roleName; ?> • <?= $email; ?></div>
                        </div>
                    </div>
                    <p><strong>Compte activé:</strong> <?= $isActivated; ?></p>

                    <hr>
                    <p><strong>Téléphone:</strong> <?= $phone ?: 'Non renseigné'; ?></p>
                    <p><strong>Date de naissance:</strong> <?= $dateOfBirth; ?></p>
                    <p><strong>Bio:</strong> <?= nl2br($bio) ?: 'Aucune bio'; ?></p>

                    <hr>
                    <h3>Activité</h3>
                    <p><strong>En ligne:</strong> <?= $isOnline; ?></p>
                    <p><strong>Dernière connexion:</strong> <?= $lastSeen; ?></p>
                </div>

                <aside class="user-actions">
                    <h3>Actions administrateur</h3>

                    <!-- block/unblock form -->
                    <?php if ((int)($user['isBanned'] ?? 0) === 1): ?>
                        <form method="POST" action="<?= HOME_URL . 'admin/utilisateur_details?id=' . $idUrl . '&action=unblock'; ?>">
                            <button type="submit" class="btn btn-success">Débannir l'utilisateur</button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="<?= HOME_URL . 'admin/utilisateur_details?id=' . $idUrl . '&action=block'; ?>" onsubmit="return confirm('Confirmer le bannissement de cet utilisateur ?');">
                            <button type="submit" class="btn btn-danger">Bannir l'utilisateur</button>
                        </form>
                    <?php endif; ?>

                    <!-- email form -->
                    <form method="POST" action="<?= HOME_URL . 'admin/utilisateur_details?id=' . $idUrl . '&action=send_email'; ?>">
                        <h4>Envoyer un email</h4>
                        <label>Sujet</label>
                        <input type="text" name="subject" required placeholder="Sujet de l'email">
                        <label>Message</label>
                        <textarea name="body" required placeholder="Votre message..."></textarea>
                        <button type="submit" class="btn btn-secondary">Envoyer</button>
                    </form>

                    <hr>
                </aside>
            </div>

        <?php else: ?>
            <p>Utilisateur non trouvé.</p>
        <?php endif; ?>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>