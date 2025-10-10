<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/banners_logos.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="p-0">
    <?php if (!$association): ?>
        <div class="custom-alert custom-alert-danger" style="margin: 20px;">
            <p>L'association demandée n'existe pas ou vous n'avez pas les permissions nécessaires pour y accéder.</p>
        </div>
    <?php else: ?>
        <!-- Banner and Logo Section -->
        <div class="association-banner-section">
            <div class="association-banner-wrapper">
                <?php if ($association->getBannerPath()): ?>
                    <img id="currentBanner" src="<?= DOMAIN . HOME_URL . $association->getBannerPath() ?>" alt="Bannière de <?= $association->getName() ?>" class="association-banner-img">
                <?php else: ?>
                    <div id="currentBanner" class="association-banner-placeholder">Aucune bannière</div>
                <?php endif; ?>
                <img id="bannerPreview" style="display:none;">
                <?php if ($isOwner): ?>
                    <span id="toggleBannerActions" class="material-icons more_vert bg-linear-primary">photo_camera</span>
                <?php endif; ?>
            </div>

            <!-- Logo overlaps banner -->
            <?php if ($isOwner): ?>
                <div class="association-logo-picture">
                    <img id="currentLogo"
                        src="<?= $association->getLogoPath() ? $association->getLogoPath() : HOME_URL . 'assets/images/uploads/logos/default_logo.png' ?>"
                        alt="Logo de <?= $association->getName() ?>">
                    <span id="toggleLogoActions" class="material-icons more_vert more_vert_logo bg-linear-primary">photo_camera</span>
                </div>
            <?php else: ?>
                <div class="association-logo-display">
                    <?php if ($association->getLogoPath()): ?>
                        <img src="<?= $association->getLogoPath() ?>" alt="Logo de <?= $association->getName() ?>">
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Banner Popup Modal -->
        <?php if ($isOwner): ?>
            <div class="popup" id="bannerPopup">
                <div class="card max-width-50">
                    <div class="flex-row justify-content-between align-items-center mb">
                        <h3 class="m-0">Gérer la bannière</h3>
                        <button id="closeBannerPopup" class="btn btn-primary" style="padding: 0.5rem;">
                            <span class="material-icons">close</span>
                        </button>
                    </div>
                    
                    <div class="banner-preview-container" style="text-align: center; margin: 1rem 0;">
                        <?php if ($association->getBannerPath()): ?>
                            <img id="bannerPreviewModal" src="<?= DOMAIN . HOME_URL . $association->getBannerPath() ?>" alt="Banner preview" style="max-width: 100%; max-height: 300px; border-radius: 12px; margin: 0 auto;">
                        <?php else: ?>
                            <div id="bannerPreviewModal" class="association-banner-placeholder" style="max-width: 100%; height: 200px; margin: 0 auto;">Aucune bannière</div>
                        <?php endif; ?>
                    </div>

                    <div id="bannerActionsDefault">
                        <form method="post" action="<?= HOME_URL . 'association/modifier' ?>" enctype="multipart/form-data">
                            <input type="hidden" name="uiid" value="<?= $association->getUiid() ?>">
                            <input type="hidden" name="action" value="modifier_banner">
                            <div class="flex-row justify-content-center gap-2" style="gap: 1rem;">
                                <label for="bannerInput" class="btn">
                                    Changer bannière
                                    <input type="file" id="bannerInput" name="banner" accept="image/*" style="display: none;" required>
                                </label>
                                <button type="submit" class="btn btn-success d-none mb" id="bannerSubmitBtn">Valider</button>
                            </div>
                        </form>
                        <?php if ($association->getBannerPath()): ?>
                            <form method="post" action="<?= HOME_URL . 'association/modifier' ?>" class="mt" id="deleteBannerForm">
                                <input type="hidden" name="uiid" value="<?= $association->getUiid() ?>">
                                <input type="hidden" name="action" value="supprimer_banner">
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

            <!-- Logo Popup Modal -->
            <div class="popup" id="logoPopup">
                <div class="card max-width-50">
                    <div class="flex-row justify-content-between align-items-center mb">
                        <h3 class="m-0">Gérer le logo</h3>
                        <button id="closeLogoPopup" class="btn btn-primary" style="padding: 0.5rem;">
                            <span class="material-icons">close</span>
                        </button>
                    </div>
                    
                    <div class="logo-preview-container" style="text-align: center; margin: 1rem 0;">
                        <img id="logoPreviewModal" src="<?= $association->getLogoPath() ? $association->getLogoPath() : HOME_URL . 'assets/images/uploads/logos/default_logo.png' ?>" alt="Logo preview" style="max-width: 180px; max-height: 180px; border-radius: 50%; margin: 0 auto;">
                    </div>

                    <div id="logoActionsDefault">
                        <form method="post" action="<?= HOME_URL . 'association/modifier' ?>" enctype="multipart/form-data">
                            <input type="hidden" name="uiid" value="<?= $association->getUiid() ?>">
                            <input type="hidden" name="action" value="modifier_logo">
                            <div class="flex-row justify-content-center gap-2" style="gap: 1rem;">
                                <label for="logoInput" class="btn">
                                    Modifier logo
                                    <input type="file" id="logoInput" name="logo" accept="image/*" style="display: none;" required>
                                </label>
                                <button type="submit" class="btn btn-success d-none mb" id="logoSubmitBtn">Valider</button>
                            </div>
                        </form>
                        <?php if ($association->getLogoPath()): ?>
                            <form action="<?= HOME_URL . 'association/modifier' ?>" method="post" class="mt" id="deleteLogoForm">
                                <input type="hidden" name="uiid" value="<?= $association->getUiid() ?>">
                                <input type="hidden" name="action" value="supprimer_logo">
                                <div class="flex-row justify-content-center">
                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>

                    <div id="logoActionsPreview" class="d-none">
                        <div class="flex-row justify-content-center gap-2" style="gap: 1rem;">
                            <button type="button" id="cancelLogo" class="btn btn-dark">Annuler</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php include_once __DIR__ . '/../includes/messages.php'; ?>

        <!-- Main Content Below Banner -->
        <div class="association-main-content pl pr">
            <!-- Header with back button and title -->
            <div class="flex-row justify-content-between align-items-center mb-4">
                <div>
                    <h1 style="margin: 0;"><?= $association->getName() ?></h1>
                    <div class="flex-row mt-2">
                        <span class="badge <?= $association->getIsActive() ? 'badge-success' : 'badge-secondary' ?> mr-2">
                            <?= $association->getIsActive() ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                </div>
                <div>
                    <a href="<?= HOME_URL . 'mes_associations' ?>" class="">
                        <span class="material-icons btn" style="color:white;">arrow_back</span>
                    </a>
                </div>
            </div>

            <div class="flex-row" style="gap: 20px;">
                <!-- Main Details Section -->
                <div class="max-width-66">
                    <div class="card mb-4">
                        <!-- Description -->
                        <div class="p-3">
                            <h4>Description</h4>
                            <div class="card p-3 bg-light">
                                <p><?= nl2br($association->getDescription() ?? 'Aucune description disponible') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <!-- Contact and Administrative Info -->
                        <div class="p-3">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h4>Coordonnées</h4>
                                    <dl>
                                        <dt>Adresse</dt>
                                        <dd><?= $association->getAddress() ?? 'Non renseignée' ?></dd>

                                        <dt>Ville</dt>
                                        <dd>
                                            <?php if ($ville): ?>
                                                <?= $ville['ville_nom_reel'] ?> (<?= $ville['ville_code_postal'] ?>)
                                            <?php else: ?>
                                                Non renseignée
                                            <?php endif; ?>
                                        </dd>

                                        <dt>Téléphone</dt>
                                        <dd>
                                            <?php if ($association->getPhone()): ?>
                                                <a href="tel:<?= $association->getPhone() ?>">
                                                    <?= $association->getPhone() ?>
                                                </a>
                                            <?php else: ?>
                                                Non renseigné
                                            <?php endif; ?>
                                        </dd>

                                        <dt>Email</dt>
                                        <dd>
                                            <?php if ($association->getEmail()): ?>
                                                <a href="mailto:<?= $association->getEmail() ?>">
                                                    <?= $association->getEmail() ?>
                                                </a>
                                            <?php else: ?>
                                                Non renseigné
                                            <?php endif; ?>
                                        </dd>

                                        <dt>Site web</dt>
                                        <dd>
                                            <?php if ($association->getWebsite()): ?>
                                                <a href="<?= $association->getWebsite() ?>" target="_blank">
                                                    <?= $association->getWebsite() ?>
                                                </a>
                                            <?php else: ?>
                                                Non renseigné
                                            <?php endif; ?>
                                        </dd>
                                    </dl>
                                </div>

                                <div class="col-md-6">
                                    <h4>Informations administratives</h4>
                                    <dl>
                                        <dt>Créée le</dt>
                                        <dd><?= $association->getCreatedAtFormatted() ?></dd>

                                        <dt>Dernière mise à jour</dt>
                                        <dd><?= $association->getUpdatedAtFormatted() ?? 'Jamais' ?></dd>
                                    </dl>
                                </div>
                            </div>

                            <?php if ($isOwner): ?>
                                <div class="flex-row justify-content-between mt-4">
                                    <a href="<?= HOME_URL . 'association/modifier?uiid=' . $association->getUiid() ?>" class="btn linkNotDecorated">
                                        Modifier l'association
                                    </a>
                                    <button type="button" class="btn btn-danger"
                                        onclick="document.getElementById('popup').style.display='flex'">
                                        Supprimer l'association
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Members Sidebar -->
                <div class="max-width-33">
                    <div class="card">
                        <div class="p-3">
                            <h3>Membres (<?= count($members) ?>)</h3>
                            <?php if (!empty($members)): ?>
                                <div class="members-list">
                                    <?php foreach ($members as $member): ?>
                                        <div class="member-item p-2 mb-2 border-bottom">
                                            <div class="flex-row align-items-center">
                                                <div class="member-avatar">
                                                    <div style="width:40px; height:40px; border-radius:50%; background-color:#ddd; display:flex; align-items:center; justify-content:center; margin-right:10px;">
                                                        <?= strtoupper(substr($member['firstName'], 0, 1) . substr($member['lastName'], 0, 1)) ?>
                                                    </div>
                                                </div>
                                                <div class="member-info flex-grow">
                                                    <strong><?= $member['firstName'] . ' ' . $member['lastName'] ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <span class="badge badge-sm <?= $member['role'] === 'admin' ? 'badge-primary' : 'badge-secondary' ?>">
                                                            <?= ucfirst($member['role']) ?>
                                                        </span>
                                                    </small>
                                                </div>
                                            </div>
                                            <small class="text-muted">Membre depuis <?= date('d/m/Y', strtotime($member['joinedAt'])) ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Aucun membre pour le moment.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <?php if ($isOwner): ?>
            <div id="popup" class="d-none popup">
                <div class="card" style="max-width:500px;">
                    <h3>Confirmer la suppression</h3>
                    <button type="button" onclick="document.getElementById('popup').style.display='none'" style="position:absolute; right:10px; top:10px; background:none; border:none; font-size:18px; cursor:pointer;">×</button>
                    <div class="mt mb">
                        <p>Êtes-vous sûr de vouloir supprimer l'association "<?= $association->getName() ?>" ?</p>
                        <p class="text-danger"><strong>Attention :</strong> Cette action est irréversible.</p>
                    </div>
                    <div class="flex-row justify-content-between">
                        <button type="button" class="btn" onclick="document.getElementById('popup').style.display='none'">Annuler</button>
                        <form action="<?= HOME_URL . 'association/supprimer' ?>" method="post">
                            <input type="hidden" name="uiid" value="<?= $association->getUiid() ?>">
                            <button type="submit" class="btn deconnexion">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>

<script src="<?= HOME_URL . 'assets/javascript/banner-logo-management.js' ?>"></script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>