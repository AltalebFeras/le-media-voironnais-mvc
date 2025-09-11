<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <div class="flex-row justify-content-between">
        <h1 class="flex-row justify-content-center">Mon profil</h1>
        <a href="<?= isset($_GET['action']) ? HOME_URL . 'my_account' : HOME_URL . 'dashboard' ?>" class="btn">
            <i class="fa-solid fa-arrow-left fa-fade fa-xl" style="color:white;"></i>
        </a>
    </div>
    <?php include_once __DIR__ . '/../includes/messages.php'; ?>
    <div class="flex-row justify-content-around">

        <?php if ($_GET['action'] === 'edit_profile') : ?>
            <div class="card">

                <h3>Modifier mes infos</h3>
                <form action="<?= HOME_URL . 'my_account?action=edit_profile' ?>" method="POST">
                    <div>
                        <label for="firstName">Prénom :</label>
                        <input type="text" id="firstName" name="firstName" placeholder="Entrez votre prénom"
                            value="<?= htmlspecialchars($_SESSION['form_data']['firstName'] ? $_SESSION['form_data']['firstName'] : $_SESSION['firstName']) ?>" required />
                    </div>
                    <div>
                        <label for="lastName">Nom :</label>
                        <input type="text" id="lastName" name="lastName" placeholder="Entrez votre nom"
                            value="<?= htmlspecialchars($_SESSION['form_data']['lastName'] ? $_SESSION['form_data']['lastName'] : $_SESSION['lastName']) ?>" required />
                    </div>
                    <div>
                        <label for="email">Adresse e-mail :</label>
                        <input type="email" id="email" name="email" placeholder="Entrez votre e-mail"
                            value="<?= htmlspecialchars($_SESSION['form_data']['email'] ? $_SESSION['form_data']['email'] : $_SESSION['email']) ?>" required />
                    </div>
                    <div>
                        <button class="btn linkNotDecorated" type="submit">Modifier</button>
                    </div>
                </form>

            </div>

        <?php elseif ($_GET['action'] === 'change_password') : ?>
            <div class="card">
                <h3>Changer mon mot de passe</h3>
                <form action="<?= HOME_URL . 'my_account?action=change_password' ?>" method="POST">
                    <div>
                        <label for="currentPassword">Mot de passe actuel :</label>
                        <input type="password" id="currentPassword" name="currentPassword" value="<?= $_SESSION['form_data']['currentPassword'] ?? ''; ?>" required />
                    </div>
                    <div>
                        <label for="newPassword">Nouveau mot de passe :</label>
                        <input type="password" id="newPassword" name="newPassword" value="<?= $_SESSION['form_data']['newPassword'] ?? ''; ?>" required />
                    </div>
                    <div>
                        <label for="confirmPassword">Confirmer le nouveau mot de passe :</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" value="<?= $_SESSION['form_data']['confirmPassword'] ?? ''; ?>" required />
                    </div>
                    <div>
                        <button class="btn linkNotDecorated" type="submit">Changer</button>
                    </div>
                </form>
            </div>
        <?php elseif ($_GET['action'] === 'delete_account') : ?>
            <div class="card">
                <h3>Supprimer mon compte</h3>
                <p>Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.</p>
                <form action="<?= HOME_URL . 'my_account?action=delete_account' ?>" method="POST">
                    <div>
                        <label for="confirmDelete">Pour confirmer, tapez "je confirme" :</label>
                        <input type="text" id="confirmDelete" name="confirmDelete" value="<?= $_SESSION['form_data']['confirmDelete'] ?? ''; ?>" placeholder="je confirme" required />
                    </div>
                    <div>
                        <button class="btn linkNotDecorated" type="submit">Supprimer mon compte</button>
                    </div>
                </form>
            </div>
        <?php else : ?>
            <div class="account-info-container">
                <div class="account-info-details">
                    <h3>Mes infos</h3>
                    <p>Prénom : <?= $_SESSION['firstName'] ?></p>
                    <p>Nom : <?= $_SESSION['lastName']  ?> </p>
                    <p>Email : <?= $_SESSION['email'] ?> </p>
                    <p>Compte valide : <?= $_SESSION['isActivated'] ? 'Oui' : 'Non' ?> </p>
                    <p>Date de création : <?= $_SESSION['createdAt'] ?> </p>
                    <p>Mise à jour : <?= $_SESSION['updatedAt'] ?? 'Jamais' ?> </p>
                    <p>Role : <?= $_SESSION['roleName'] ?> </p>
                    <p>Dernière connexion : <?= (new DateTime($_SESSION['lastConnection']))->format('d/m/Y') . ' à ' . (new DateTime($_SESSION['lastConnection']))->format('H:i') ?> </p>
                    <a href="<?= HOME_URL . 'my_account?action=edit_profile' ?>" class="btn linkNotDecorated">Modifier</a>
                </div>

                <div class="account-profile-picture">
                    <img id="currentProfilePicture" src="<?= $_SESSION['profilePicturePath'] ?>" alt="user profile image" width="200" height="200">
                    <h5>Changer votre photo de profil</h5>
                    <form method="post" action="<?= HOME_URL . 'my_account?action=edit_profile_picture' ?>" enctype="multipart/form-data">
                        <label for="profilePicture" class="custom-file-label">
                            Sélectionnez une photo (PNG, JPG, JPEG, max 2 Mo)
                            <input type="file" id="profilePicture" name="profilePicture" accept="image/*" class="custom-file-input" required>
                        </label>
                        <button type="submit" class="btn">Changer</button>
                        <button type="button" id="cancelProfilePicture" class="btn" style="margin-left: 0.5rem; display:none;">Annuler</button>
                    </form>
                    <form action="<?= HOME_URL . 'my_account?action=delete_profile_picture' ?>" method="post">
                        <button type="submit" class="btn">Supprimer</button>
                    </form>
                </div>
            </div>
            <div class="account-actions">
                <a href="<?= HOME_URL . 'my_account?action=change_password' ?>" class="btn linkNotDecorated bg-info text-bold">Changer mon mot de passe</a>
                <a href="<?= HOME_URL . 'my_account?action=delete_account' ?>" class="btn linkNotDecorated bg-danger text-bold">Supprimer mon compte</a>
            </div>
        <?php endif; ?>

</main>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>