<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/banners-logos.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main style="padding:0;">
    <?php if (!$association): ?>
        <div class="custom-alert custom-alert-danger" style="margin: 20px;">
            <p>L'association demandée n'existe pas ou vous n'avez pas les permissions nécessaires pour y accéder.</p>
        </div>
    <?php else: ?>
        <!-- Banner and Logo Section (like Facebook) -->
        <div class="association-banner-section">
            <div class="association-banner-wrapper">
                <?php if ($association->getBannerPath()): ?>
                    <img id="currentBanner" src="<?= $association->getBannerPath() ?>" alt="Bannière de <?= $association->getName() ?>" class="association-banner-img">
                <?php else: ?>
                    <div id="currentBanner" class="association-banner-placeholder">Aucune bannière</div>
                <?php endif; ?>
                <img id="bannerPreview" style="display:none;">
            </div>
            
            <?php if ($isOwner): ?>
                <div class="association-banner-actions">
                    <form method="post" action="<?= HOME_URL . 'association/edit_banner?uiid=' . $association->getUiid() ?>" enctype="multipart/form-data">
                        <label for="bannerInput" class="btn">
                            Changer bannière
                            <input type="file" id="bannerInput" name="banner" accept="image/*" required>
                        </label>
                        <button type="submit" class="btn" id="bannerSubmitBtn" disabled>Valider</button>
                        <button type="button" id="cancelBannerBtn" class="btn" style="display:none;">Annuler</button>
                    </form>
                    <?php if ($association->getBannerPath()): ?>
                        <form method="post" action="<?= HOME_URL . 'association/delete_banner?uiid=' . $association->getUiid() ?>">
                            <button type="submit" class="btn bg-danger">Supprimer</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Logo overlaps banner -->
            <?php if ($isOwner): ?>
                <div class="association-logo-picture">
                    <img id="currentLogo" src="<?= $association->getLogoPath() ?: HOME_URL . 'assets/images/default-association-logo.png' ?>" alt="Logo de <?= $association->getName() ?>">
                    <form method="post" action="<?= HOME_URL . 'association/edit_logo?uiid=' . $association->getUiid() ?>" enctype="multipart/form-data">
                        <label for="logoInput" class="btn">
                            Modifier logo
                            <input type="file" id="logoInput" name="logo" accept="image/*" required>
                        </label>
                        <button type="submit" class="btn">Valider</button>
                        <button type="button" id="cancelLogo" class="btn" style="display:none;">Annuler</button>
                    </form>
                    <?php if ($association->getLogoPath()): ?>
                        <form action="<?= HOME_URL . 'association/delete_logo?uiid=' . $association->getUiid() ?>" method="post">
                            <button type="submit" class="btn">Supprimer</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="association-logo-display">
                    <?php if ($association->getLogoPath()): ?>
                        <img src="<?= $association->getLogoPath() ?>" alt="Logo de <?= $association->getName() ?>">
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Main Content Below Banner -->
        <div class="association-main-content">
            <!-- Header with back button and title -->
            <div class="flex-row justify-content-between align-items-center mb-4">
                <div>
                    <h1 style="margin: 0;"><?= $association->getName() ?></h1>
                    <div class="flex-row mt-2">
                        <span class="badge <?= $association->getIsActive() ? 'badge-success' : 'badge-secondary' ?> mr-2">
                            <?= $association->getIsActive() ? 'Active' : 'Inactive' ?>
                        </span>
                        <span class="badge <?= $association->getIsPublic() ? 'badge-info' : 'badge-warning' ?>">
                            <?= $association->getIsPublic() ? 'Publique' : 'Privée' ?>
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
                                    <a href="<?= HOME_URL.'association/modifier?uiid='.$association->getUiid() ?>" class="btn linkNotDecorated">
                                        Modifier l'association
                                    </a>
                                    <button type="button" class="btn btn-danger"
                                        onclick="document.getElementById('deleteModal').style.display='flex'">
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
            <div id="deleteModal" class="d-none" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; display:none; justify-content:center; align-items:center;">
                <div class="card" style="max-width:500px;">
                    <h3>Confirmer la suppression</h3>
                    <button type="button" onclick="document.getElementById('deleteModal').style.display='none'" style="position:absolute; right:10px; top:10px; background:none; border:none; font-size:18px; cursor:pointer;">×</button>
                    <div class="mt mb">
                        <p>Êtes-vous sûr de vouloir supprimer l'association "<?= $association->getName() ?>" ?</p>
                        <p class="text-danger"><strong>Attention :</strong> Cette action est irréversible.</p>
                    </div>
                    <div class="flex-row justify-content-between">
                        <button type="button" class="btn" onclick="document.getElementById('deleteModal').style.display='none'">Annuler</button>
                        <form action="<?= HOME_URL . 'mes_associations?action=delete&uiid=' . $association->getUiid() ?>" method="post">
                            <button type="submit" class="btn deconnexion">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>

<script>
// Banner preview functionality
document.addEventListener('DOMContentLoaded', function() {
    const bannerInput = document.getElementById('bannerInput');
    const bannerPreview = document.getElementById('bannerPreview');
    const currentBanner = document.getElementById('currentBanner');
    const bannerSubmitBtn = document.getElementById('bannerSubmitBtn');
    const cancelBannerBtn = document.getElementById('cancelBannerBtn');

    if (bannerInput) {
        bannerInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    bannerPreview.src = e.target.result;
                    bannerPreview.style.display = 'block';
                    currentBanner.style.display = 'none';
                    bannerSubmitBtn.disabled = false;
                    cancelBannerBtn.style.display = 'inline-block';
                };
                reader.readAsDataURL(file);
            }
        });

        if (cancelBannerBtn) {
            cancelBannerBtn.addEventListener('click', function() {
                bannerPreview.style.display = 'none';
                currentBanner.style.display = 'block';
                bannerInput.value = '';
                bannerSubmitBtn.disabled = true;
                cancelBannerBtn.style.display = 'none';
            });
        }
    }

    // Logo preview functionality
    const logoInput = document.getElementById('logoInput');
    const currentLogo = document.getElementById('currentLogo');
    const cancelLogo = document.getElementById('cancelLogo');

    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    currentLogo.src = e.target.result;
                    cancelLogo.style.display = 'inline-block';
                };
                reader.readAsDataURL(file);
            }
        });

        if (cancelLogo) {
            cancelLogo.addEventListener('click', function() {
                logoInput.value = '';
                currentLogo.src = '<?= $association->getLogoPath() ?: "/default-logo.png" ?>';
                cancelLogo.style.display = 'none';
            });
        }
    }
});
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
