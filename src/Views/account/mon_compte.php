<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/mon_compte.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main style="padding:0;">
    <div class="account-banner-section">
        <div class="account-banner-wrapper">
            <?php if (!empty($_SESSION['bannerPath'])): ?>
                <img id="currentBanner" src="<?= $_SESSION['bannerPath'] ?>" alt="Banner" class="account-banner-img">
            <?php else: ?>
                <div id="currentBanner" class="account-banner-placeholder">Aucune bannière</div>
            <?php endif; ?>
            <img id="bannerPreview" style="display:none;">
        </div>
        <div class="account-banner-actions">
            <form method="post" action="<?= HOME_URL . 'mon_compte?action=edit_banner' ?>" enctype="multipart/form-data">
                <label for="bannerInput" class="btn">
                    Changer bannière
                    <input type="file" id="bannerInput" name="banner" accept="image/*" required>
                </label>
                <button type="submit" class="btn" id="bannerSubmitBtn" disabled>Valider</button>
                <button type="button" id="cancelBannerBtn" class="btn" style="display:none;">Annuler</button>
            </form>
            <?php if (!empty($_SESSION['bannerPath'])): ?>
                <form method="post" action="<?= HOME_URL . 'mon_compte?action=delete_banner' ?>">
                    <button type="submit" class="btn bg-danger">Supprimer</button>
                </form>
            <?php endif; ?>
        </div>
        <!-- Profile picture overlaps banner -->
        <div class="account-profile-picture">
            <img id="currentProfilePicture" src="<?= $_SESSION['avatarPath'] ?>" alt="user profile image">
            <form method="post" action="<?= HOME_URL . 'mon_compte?action=edit_profile_picture' ?>" enctype="multipart/form-data">
                <label for="profilePicture" class="btn">
                    Modifier photo
                    <input type="file" id="profilePicture" name="profilePicture" accept="image/*" required>
                </label>
                <button type="submit" class="btn">Valider</button>
                <button type="button" id="cancelProfilePicture" class="btn" style="display:none;">Annuler</button>
            </form>
            <form action="<?= HOME_URL . 'mon_compte?action=delete_profile_picture' ?>" method="post">
                <button type="submit" class="btn">Supprimer</button>
            </form>
        </div>
    </div>

    <div class="flex-row justify-content-between">
        <h1 class="flex-row justify-content-center">Mon profil</h1>
        <a href="<?= isset($_GET['action']) ? HOME_URL . 'mon_compte' : HOME_URL . 'dashboard' ?>" class="">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
        </a>
    </div>
    <?php include_once __DIR__ . '/../includes/messages.php'; ?>
    <div class="flex-row justify-content-around">

        <?php if ($_GET['action'] === 'edit_profile' && !isset($_GET['field'])) : ?>
            <div class="card">

                <h3>Modifier mes infos</h3>
                <form action="<?= HOME_URL . 'mon_compte?action=edit_profile' ?>" method="POST">
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
                        <label for="phone">Téléphone :</label>
                        <input type="text" id="phone" name="phone" placeholder="Entrez votre téléphone"
                            value="<?= htmlspecialchars($_SESSION['form_data']['phone'] ?? $_SESSION['phone'] ?? '') ?>" />
                    </div>
                    <div>
                        <label for="bio">Bio :</label>
                        <textarea id="bio" name="bio" placeholder="Entrez votre bio"><?= htmlspecialchars($_SESSION['form_data']['bio'] ?? $_SESSION['bio'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <label for="dateOfBirth">Date de naissance :</label>
                        <input type="date" id="dateOfBirth" name="dateOfBirth"
                            value="<?php
                                    // Prefer form_data if present
                                    if (!empty($_SESSION['form_data']['dateOfBirth'])) {
                                        echo htmlspecialchars($_SESSION['form_data']['dateOfBirth']);
                                    } elseif (!empty($_SESSION['dateOfBirth'])) {
                                        // Try to parse dd/mm/YYYY or dd/mm/YYYY à HH:ii
                                        $dob = $_SESSION['dateOfBirth'];
                                        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})/', $dob, $matches)) {
                                            // Convert to YYYY-MM-DD
                                            echo $matches[3] . '-' . $matches[2] . '-' . $matches[1];
                                        } else {
                                            // Fallback: try strtotime
                                            echo date('Y-m-d', strtotime($dob));
                                        }
                                    }
                                    ?>" />
                    </div>
                    <div>
                        <button class="btn linkNotDecorated" type="submit">Modifier</button>
                    </div>
                </form>

            </div>
        <?php elseif ($_GET['action'] === 'edit_profile' && $_GET['field'] == 'phone' && empty($_SESSION['phone'])) : ?>
            <div class="card">
                <h3>Ajouter mon numéro de téléphone</h3>
                <form action="<?= HOME_URL . 'mon_compte?action=add_phone' ?>" method="POST">
                    <div>
                        <label for="phone">Téléphone :</label>
                        <input type="text" id="phone" name="phone" placeholder="Entrez votre téléphone"
                            value="<?= htmlspecialchars($_SESSION['form_data']['phone'] ?? $_SESSION['phone'] ?? '') ?>" required />
                    </div>
                    <div>
                        <button class="btn linkNotDecorated" type="submit">Ajouter</button>
                    </div>
                </form>
            </div>
        <?php elseif ($_GET['action'] === 'edit_profile' && $_GET['field'] == 'bio' && empty($_SESSION['bio'])) : ?>
            <div class="card">
                <h3>Ajouter ma bio</h3>
                <form action="<?= HOME_URL . 'mon_compte?action=add_bio' ?>" method="POST">
                    <div>
                        <label for="bio">Bio :</label>
                        <textarea id="bio" name="bio" placeholder="Entrez votre bio"><?= htmlspecialchars($_SESSION['form_data']['bio'] ?? $_SESSION['bio'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <button class="btn linkNotDecorated" type="submit">Ajouter</button>
                    </div>
                </form>
            </div>
        <?php elseif ($_GET['action'] === 'edit_profile' && $_GET['field'] == 'date_of_birth' && empty($_SESSION['dateOfBirth'])) : ?>
            <div class="card">
                <h3>Ajouter ma date de naissance</h3>
                <form action="<?= HOME_URL . 'mon_compte?action=add_date_of_birth' ?>" method="POST">
                    <div>
                        <label for="dateOfBirth">Date de naissance :</label>
                        <input type="date" id="dateOfBirth" name="dateOfBirth"
                            value="<?= isset($_SESSION['form_data']['dateOfBirth']) ? $_SESSION['form_data']['dateOfBirth'] : (isset($_SESSION['dateOfBirth']) && $_SESSION['dateOfBirth'] ? date('Y-m-d\TH:i', strtotime($_SESSION['dateOfBirth'])) : '') ?>" />
                    </div>
                    <div>
                        <button class="btn linkNotDecorated" type="submit">Ajouter</button>
                    </div>
                </form>
            </div>
        <?php elseif ($_GET['action'] === 'change_password') : ?>
            <div class="card">
                <h3>Changer mon mot de passe</h3>
                <form action="<?= HOME_URL . 'mon_compte?action=change_password' ?>" method="POST">
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
                <form action="<?= HOME_URL . 'mon_compte?action=delete_account' ?>" method="POST">
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
                    <p>Prénom :
                        <?= $_SESSION['firstName'] ?>
                    </p>
                    <p>Nom :
                        <?= $_SESSION['lastName'] ?>
                    </p>
                    <p>Email :
                        <?= $_SESSION['email'] ?>
                    </p>
                    <p>Téléphone :
                        <?= $_SESSION['phone'] ?? '' ?>
                        <?php if (empty($_SESSION['phone'])): ?>
                            <a href="<?= HOME_URL . 'mon_compte?action=edit_profile&field=phone' ?>" class="btn linkNotDecorated btn-add">Ajouter</a>
                        <?php endif; ?>
                    </p>
                    <p>Bio :
                        <?= $_SESSION['bio'] ?? '' ?>
                        <?php if (empty($_SESSION['bio'])): ?>
                            <a href="<?= HOME_URL . 'mon_compte?action=edit_profile&field=bio' ?>" class="btn linkNotDecorated btn-add">Ajouter</a>
                        <?php endif; ?>
                    </p>
                    <p>Date de naissance :
                        <?= $_SESSION['dateOfBirth'] ?? '' ?>
                        <?php if (empty($_SESSION['dateOfBirth'])): ?>
                            <a href="<?= HOME_URL . 'mon_compte?action=edit_profile&field=date_of_birth' ?>" class="btn linkNotDecorated btn-add">Ajouter</a>
                        <?php endif; ?>
                    </p>
                    <p>Compte valide : <?= $_SESSION['isActivated'] ? 'Oui' : 'Non' ?> </p>
                    <p>En ligne : <?= isset($_SESSION['isOnline']) ? ($_SESSION['isOnline'] ? 'Oui' : 'Non') : '' ?>
                    </p>
                    <p>Date de création : <?= $_SESSION['createdAt'] ?> </p>
                    <p>Mise à jour : <?= $_SESSION['updatedAt'] ?? 'Jamais' ?> </p>
                    <p>Role : <?= $_SESSION['role'] ?> </p>
                    <p>Dernière connexion :
                        <?php if (!empty($_SESSION['lastSeen'])): ?>
                            <?= $_SESSION['lastSeen'] ?>
                        <?php else: ?>
                            Jamais
                        <?php endif; ?>
                    </p>
                    <a href="<?= HOME_URL . 'mon_compte?action=edit_profile' ?>" class="btn linkNotDecorated">Modifier</a>
                </div>


            </div>
            <div class="account-actions">
                <a href="<?= HOME_URL . 'mon_compte?action=change_password' ?>" class="btn linkNotDecorated bg-info text-bold">Changer mon mot de passe</a>
                <a href="<?= HOME_URL . 'mon_compte?action=delete_account' ?>" class="btn linkNotDecorated bg-danger text-bold">Supprimer mon compte</a>
            </div>
        <?php endif; ?>
</main>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>