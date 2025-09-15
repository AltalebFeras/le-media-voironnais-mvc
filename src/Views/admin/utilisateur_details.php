<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/utilisateur_details.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar_admin.php'; ?>

<main>
    <div class="container py-4">

        <h1>Détails de l'utilisateur</h1>
        <?php include_once __DIR__ . '/../includes/messages.php'; ?>

        <?php if (isset($user) && is_array($user)): ?>
            <?php
            // Sanitize user data for display
            $id = htmlspecialchars($user['id'] ?? '');
            $firstName = htmlspecialchars($user['firstName'] ?? '');
            $lastName = htmlspecialchars($user['lastName'] ?? '');
            $email = htmlspecialchars($user['email'] ?? '');
            $phone = htmlspecialchars($user['phone'] ?? '');
            $roleName = htmlspecialchars($user['roleName'] ?? '');
            $avatarPath = !empty($user['avatarPath']) ? htmlspecialchars($user['avatarPath']) : (DOMAIN . HOME_URL . 'assets/images/uploads/avatars/default_avatar.png');
            $bannerPath = !empty($user['bannerPath']) ? htmlspecialchars($user['bannerPath']) : null;
            $bio = htmlspecialchars($user['bio'] ?? '');
            $dateOfBirth = !empty($user['dateOfBirth']) ? (new DateTime($user['dateOfBirth']))->format('d/m/Y') : 'Non renseignée';
            $isActivated = isset($user['isActivated']) ? ((int)$user['isActivated'] === 1 ? 'Oui' : 'Non') : 'Inconnu';
            $isBanned = isset($user['isBanned']) ? ((int)$user['isBanned'] === 1 ? 'Oui' : 'Non') : 'Inconnu';
            $isOnline = isset($user['isOnline']) ? ((int)$user['isOnline'] === 1 ? 'Oui' : 'Non') : 'Inconnu';
            $lastSeen = !empty($user['lastSeen']) ? (new DateTime($user['lastSeen']))->format('d/m/Y à H:i') : 'Jamais';
            $rgpdAcceptedDate = !empty($user['rgpdAcceptedDate']) ? (new DateTime($user['rgpdAcceptedDate']))->format('d/m/Y à H:i') : 'Jamais';
            $createdAt = !empty($user['createdAt']) ? (new DateTime($user['createdAt']))->format('d/m/Y à H:i') : 'Inconnu';
            $updatedAt = !empty($user['updatedAt']) ? (new DateTime($user['updatedAt']))->format('d/m/Y à H:i') : 'Jamais';
            $emailChangedAt = !empty($user['emailChangedAt']) ? (new DateTime($user['emailChangedAt']))->format('d/m/Y à H:i') : 'Jamais';
            $passwordResetAt = !empty($user['passwordResetAt']) ? (new DateTime($user['passwordResetAt']))->format('d/m/Y à H:i') : 'Jamais';
            $deletedAt = !empty($user['deletedAt']) ? (new DateTime($user['deletedAt']))->format('d/m/Y à H:i') : 'N/A';
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
                        <form method="POST" action="<?= HOME_URL . 'admin/utilisateur_details?id=' . $id . '&action=unblock'; ?>">
                            <button type="submit" class="btn btn-success">Débannir l'utilisateur</button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="<?= HOME_URL . 'admin/utilisateur_details?id=' . $id . '&action=block'; ?>" onsubmit="return confirm('Confirmer le bannissement de cet utilisateur ?');">
                            <button type="submit" class="btn btn-danger">Bannir l'utilisateur</button>
                        </form>
                    <?php endif; ?>

                    <!-- email form -->
                    <form method="POST" action="<?= HOME_URL . 'admin/utilisateur_details?id=' . $id . '&action=send_email'; ?>">
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