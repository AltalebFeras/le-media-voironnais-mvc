<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/users/mon_compte.css' ?>">

<?php
if (isset($_SESSION['connectedAdmin']) || isset($_SESSION['connectedSuperAdmin'])) {
    include_once __DIR__ . '/../includes/navbar_admin.php';
} else {
    include_once __DIR__ . '/../includes/navbar.php';
}
?>
<main style="padding:0;">
    <div class="account-banner-section">
        <div class="account-banner-wrapper">
            <?php if (!empty($_SESSION['bannerPath'])): ?>
                <img id="currentBanner" src="<?= $_SESSION['bannerPath'] ?>" alt="Banner" class="account-banner-img">
            <?php else: ?>
                <div id="currentBanner" class="account-banner-placeholder">Aucune bannière</div>
            <?php endif; ?>
            <img id="bannerPreview" style="display:none;">
            <span id="toggleBannerActions" class="material-icons more_vert bg-linear-primary">photo_camera</span>
        </div>
        
        <!-- Profile picture overlaps banner -->
        <div class="account-profile-picture ">
            <img id="currentProfilePicture" src="<?= $_SESSION['avatarPath'] ?>" alt="user profile image">
            <span id="toggleLogoActions" class="material-icons more_vert more_vert_logo bg-linear-primary">photo_camera</span>
        </div>
    </div>

    <!-- Banner Popup Modal -->
    <div class="popup" id="bannerPopup">
        <div class="card max-width-50">
            <div class="flex-row justify-content-between align-items-center mb">
                <h3 class="m-0">Gérer ma bannière</h3>
                <button id="closeBannerPopup" class="btn btn-primary" style="padding: 0.5rem;">
                    <span class="material-icons">close</span>
                </button>
            </div>
            
            <div class="banner-preview-container" style="text-align: center; margin: 1rem 0;">
                <?php if (!empty($_SESSION['bannerPath'])): ?>
                    <img id="bannerPreviewModal" src="<?= $_SESSION['bannerPath'] ?>" alt="Banner preview" style="max-width: 100%; max-height: 300px; border-radius: 12px; margin: 0 auto;">
                <?php else: ?>
                    <div id="bannerPreviewModal" class="account-banner-placeholder" style="max-width: 100%; height: 200px; margin: 0 auto;">Aucune bannière</div>
                <?php endif; ?>
            </div>

            <div id="bannerActionsDefault">
                <form method="post" action="<?= HOME_URL . 'mon_compte' ?>" enctype="multipart/form-data" id="bannerForm">
                    <input type="hidden" name="action" value="edit_banner">
                    <div class="flex-row justify-content-center gap-2" style="gap: 1rem;">
                        <label for="bannerInput" class="btn">
                            Changer bannière
                            <input type="file" id="bannerInput" name="banner" accept="image/*" style="display: none;" required>
                        </label>
                        <button type="submit" class="btn btn-success d-none mb" id="bannerSubmitBtn">Valider</button>
                    </div>
                </form>
                <?php if (!empty($_SESSION['bannerPath'])): ?>
                    <form action="<?= HOME_URL . 'mon_compte' ?>" method="post" class="mt" id="deleteBannerForm">
                        <input type="hidden" name="action" value="delete_banner">
                        <div class="flex-row justify-content-center">
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>

            <div id="bannerActionsPreview" class="d-none">
                <div class="flex-row justify-content-center gap-2" style="gap: 1rem;">
                    <button type="button" id="cancelBannerBtn" class="btn btn-dark">Annuler</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Picture Popup Modal -->
    <div class="popup" id="profilePicturePopup">
        <div class="card max-width-50">
            <div class="flex-row justify-content-between align-items-center mb">
                <h3 class="m-0">Gérer ma photo de profil</h3>
                <button id="closeProfilePopup" class="btn btn-primary" style="padding: 0.5rem;">
                    <span class="material-icons">close</span>
                </button>
            </div>
            
            <div class="profile-preview-container" style="text-align: center; margin: 1rem 0;">
                <img id="profilePicturePreviewModal" src="<?= $_SESSION['avatarPath'] ?>" alt="Profile preview" style="max-width: 180px; max-height: 180px; border-radius: 50%; margin: 0 auto;">
            </div>

            <div id="profileActionsDefault">
                <form method="post" action="<?= HOME_URL . 'mon_compte' ?>" enctype="multipart/form-data" id="profilePictureForm">
                    <input type="hidden" name="action" value="edit_profile_picture">
                    <div class="flex-row justify-content-center gap-2" style="gap: 1rem;">
                        <label for="profilePicture" class="btn">
                            Modifier photo
                            <input type="file" id="profilePicture" name="profilePicture" accept="image/*" style="display: none;" required>
                        </label>
                        <button type="submit" class="btn btn-success d-none mb" id="profileSubmitBtn">Valider</button>
                    </div>
                </form>
                <form action="<?= HOME_URL . 'mon_compte' ?>" method="post" class="mt" id="deleteProfileForm">
                    <input type="hidden" name="action" value="delete_profile_picture">
                    <div class="flex-row justify-content-center">
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </div>
                </form>
            </div>

            <div id="profileActionsPreview" class="d-none">
                <div class="flex-row justify-content-center gap-2" style="gap: 1rem;">
                    <button type="button" id="cancelProfilePicture" class="btn btn-dark">Annuler</button>
                </div>
            </div>
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
                <form action="<?= HOME_URL . 'mon_compte' ?>" method="POST">
                    <input type="hidden" name="action" value="edit_profile">
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
                <form action="<?= HOME_URL . 'mon_compte' ?>" method="POST">
                    <input type="hidden" name="action" value="add_phone">
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
        <?php elseif ($_GET['action'] === 'edit_profile' && $_GET['field'] == 'email') : ?>
            <div class="card">
                <h3>Modifier mon adresse e-mail</h3>
                <form action="<?= HOME_URL . 'mon_compte' ?>" method="POST">
                    <input type="hidden" name="action" value="edit_email">
                    <p>Votre adresse e-mail actuelle est : <span class="text-bold">
                            <?= htmlspecialchars($_SESSION['email']) ?>
                        </span></p>
                    <div>
                        <label for="email">Adresse e-mail :</label>
                        <input type="email" id="email" name="email" placeholder="Entrez votre e-mail"
                            value="<?= htmlspecialchars($_SESSION['form_data']['email'] ?? '') ?>" required />
                    </div>
                    <div>
                        <button class="btn linkNotDecorated" type="submit">Modifier</button>
                    </div>
                </form>
            </div>
        <?php elseif ($_GET['action'] === 'edit_profile' && $_GET['field'] == 'bio' && empty($_SESSION['bio'])) : ?>
            <div class="card">
                <h3>Ajouter ma bio</h3>
                <form action="<?= HOME_URL . 'mon_compte' ?>" method="POST">
                    <input type="hidden" name="action" value="add_bio">
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
                <form action="<?= HOME_URL . 'mon_compte' ?>" method="POST">
                    <input type="hidden" name="action" value="add_date_of_birth">
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
                <form action="<?= HOME_URL . 'mon_compte' ?>" method="POST">
                    <input type="hidden" name="action" value="change_password">
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
                <form action="<?= HOME_URL . 'mon_compte' ?>" method="POST">
                    <input type="hidden" name="action" value="delete_account">
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
                        <a href="<?= HOME_URL . 'mon_compte?action=edit_profile&field=email' ?>" class=" linkNotDecorated btn btn-warning text-bold">Modifier</a>
                    </p>
                    <?php if (isset($_SESSION['newEmail'])): ?>
                        <div class="card">

                            <p>
                                <strong>Nouvelle adresse e-mail :</strong> <?= $_SESSION['newEmail'] ?>
                            </p>
                            <div>
                                <h6>Validation</h6>

                                <form action="<?= HOME_URL . 'mon_compte' ?>" method="POST">
                                    <input type="hidden" name="action" value="validate_new_email">
                                    <div>
                                        <label for="authCode">Code de confirmation :</label>
                                        <input type="text" id="authCode" name="authCode" placeholder="Entrez le code reçu par e-mail" value="<?= $_SESSION['form_data']['authCode'] ?? ''; ?>" required />
                                    </div>
                                    <div>
                                        <button class="btn linkNotDecorated" type="submit">Valider</button>
                                    </div>
                                </form>
                                <div class="d-flex flex-column pt gap-2">
                                    <form action="<?= HOME_URL . 'mon_compte' ?>" method="POST">
                                        <input type="hidden" name="action" value="cancel_email_change">
                                        <div>
                                            <button class="btn linkNotDecorated bg-danger" type="submit">Annuler la modification d'e-mail</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <p>Téléphone :
                        <?= $_SESSION['phone'] ?? '' ?>
                        <?php if (empty($_SESSION['phone'])): ?>
                            <a href="<?= HOME_URL . 'mon_compte?action=edit_profile&field=phone' ?>" class="btn linkNotDecorated btn-success">Ajouter</a>
                        <?php endif; ?>
                    </p>
                    <p>Bio :
                        <?= $_SESSION['bio'] ?? '' ?>
                        <?php if (empty($_SESSION['bio'])): ?>
                            <a href="<?= HOME_URL . 'mon_compte?action=edit_profile&field=bio' ?>" class="btn linkNotDecorated  btn-success">Ajouter</a>
                        <?php endif; ?>
                    </p>
                    <p>Date de naissance :
                        <?= $_SESSION['dateOfBirth'] ?? '' ?>
                        <?php if (empty($_SESSION['dateOfBirth'])): ?>
                            <a href="<?= HOME_URL . 'mon_compte?action=edit_profile&field=date_of_birth' ?>" class="btn linkNotDecorated btn-success">Ajouter</a>
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
                    <div style="width: auto;">
                        <a href="<?= HOME_URL . 'mon_compte?action=edit_profile' ?>" class="btn linkNotDecorated">Modifier</a>
                    </div>
                </div>


            </div>
            <div class="account-actions">
                <a href="<?= HOME_URL . 'mon_compte?action=change_password' ?>" class="btn linkNotDecorated btn-secondary text-bold">Changer mon mot de passe</a>
                <a href="<?= HOME_URL . 'mon_compte?action=delete_account' ?>" class="btn linkNotDecorated btn-danger text-bold">Supprimer mon compte</a>
            </div>
        <?php endif; ?>
</main>
<script src="<?= HOME_URL . 'assets/javascript/banner-logo-management.js' ?>"></script>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>