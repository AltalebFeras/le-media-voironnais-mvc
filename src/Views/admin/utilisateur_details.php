<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar_admin.php'; ?>

<main>
    <div class="container py-4">
        <h1>Détails de l'utilisateur</h1>
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
            $isDeleted = isset($user['isDeleted']) ? ((int)$user['isDeleted'] === 1 ? 'Oui' : 'Non') : 'Inconnu';
            $isOnline = isset($user['isOnline']) ? ((int)$user['isOnline'] === 1 ? 'Oui' : 'Non') : 'Inconnu';
            $lastSeen = !empty($user['lastSeen']) ? (new DateTime($user['lastSeen']))->format('d/m/Y à H:i') : 'Jamais';
            $rgpdAcceptedDate = !empty($user['rgpdAcceptedDate']) ? (new DateTime($user['rgpdAcceptedDate']))->format('d/m/Y à H:i') : 'Jamais';
            $createdAt = !empty($user['createdAt']) ? (new DateTime($user['createdAt']))->format('d/m/Y à H:i') : 'Inconnu';
            $updatedAt = !empty($user['updatedAt']) ? (new DateTime($user['updatedAt']))->format('d/m/Y à H:i') : 'Jamais';
            $emailChangedAt = !empty($user['emailChangedAt']) ? (new DateTime($user['emailChangedAt']))->format('d/m/Y à H:i') : 'Jamais';
            $passwordResetAt = !empty($user['passwordResetAt']) ? (new DateTime($user['passwordResetAt']))->format('d/m/Y à H:i') : 'Jamais';
            $deletedAt = !empty($user['deletedAt']) ? (new DateTime($user['deletedAt']))->format('d/m/Y à H:i') : 'N/A';
            ?>
            <div class="user-details">  
                <?php if ($bannerPath): ?>
                    <div class="banner">
                        <img src="<?= $bannerPath; ?>" alt="Bannière de <?= $firstName . ' ' . $lastName; ?>">
                    </div>
                <?php endif; ?>
                <div class="avatar">
                    <img src="<?= $avatarPath; ?>" alt="Avatar de <?= $firstName . ' ' . $lastName; ?>">
                </div>
                <h2><?= ($firstName || $lastName) ? $firstName . ' ' . $lastName : 'Utilisateur #' . $id; ?></h2>
                <p><strong>Rôle:</strong> <?= $roleName; ?></p>
                <p><strong>Email:</strong> <?= $email; ?></p>
                <p><strong>Téléphone:</strong> <?= $phone ?: 'Non renseigné'; ?></p>
                <p><strong>Date de naissance:</strong> <?= $dateOfBirth; ?></p>
                <p><strong>Bio:</strong> <?= nl2br($bio) ?: 'Aucune bio'; ?></p>
                <hr>
                <h3>Statut du compte</h3>
                <p><strong>Activé:</strong> <?= $isActivated; ?></p>
                <p><strong>Banni:</strong> <?= $isBanned; ?></p>
                <p><strong>Supprimé:</strong> <?= $isDeleted; ?></p>
                <hr>
                <h3>Activité</h3>
                <p><strong>En ligne:</strong> <?= $isOnline; ?></p>
                <p><strong>Dernière connexion:</strong> <?= $lastSeen; ?></p>
                <p><strong>Date d'acceptation RGPD:</strong> <?= $rgpdAcceptedDate; ?></p>
                <hr>
                <h3>Dates importantes</h3>
                <p><strong>Date de création:</strong> <?= $createdAt; ?></p>
                <p><strong>Derni��re mise à jour:</strong> <?= $updatedAt; ?></p>
                <p><strong>Derni��re modification d'email:</strong> <?= $emailChangedAt; ?></p>
                <p><strong>Derni��re r��initialisation de mot de passe:</strong> <?= $passwordResetAt; ?></p>
                <p><strong>Date de suppression:</strong> <?= $deletedAt; ?></p>
            </div>



            <?php else: ?>
                <p>Utilisateur non trouvé.</p>
            <?php endif; ?>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>