<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/globals/banners_logos.css' ?>">
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/entreprises/voir_entreprise.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="p-0 entreprise-detail-container">
    <?php if (!$entreprise): ?>
        <div class="custom-alert custom-alert-danger" style="margin: 20px;">
            <p>L'entreprise demandée n'existe pas ou vous n'avez pas les permissions nécessaires pour y accéder.</p>
        </div>
    <?php else: ?>
        <!-- Banner and Logo Section (like Facebook) -->
        <div class="entreprise-banner-section">
            <div class="entreprise-banner-wrapper">
                <?php if ($entreprise->getBannerPath()): ?>
                    <img id="currentBanner" src="<?= $entreprise->getBannerPath() ?>" alt="Bannière de <?= $entreprise->getName() ?>" class="entreprise-banner-img">
                <?php else: ?>
                    <div id="currentBanner" class="entreprise-banner-placeholder">Aucune bannière</div>
                <?php endif; ?>
                <img id="bannerPreview" style="display:none;">
                <?php if ($isOwner): ?>
                    <span id="toggleBannerActions" class="material-icons more_vert bg-linear-primary">photo_camera</span>
                <?php endif; ?>
            </div>

            <!-- Logo overlaps banner -->
            <?php if ($isOwner): ?>
                <div class="entreprise-logo-picture">
                    <img id="currentLogo"
                        src="<?= $entreprise->getLogoPath() ?: HOME_URL . 'assets/images/default-entreprise-logo.png' ?>"
                        alt="Logo de <?= $entreprise->getName() ?>">
                    <span id="toggleLogoActions" class="material-icons more_vert more_vert_logo bg-linear-primary">photo_camera</span>
                </div>
            <?php else: ?>
                <div class="entreprise-logo-display">
                    <?php if ($entreprise->getLogoPath()): ?>
                        <img src="<?= $entreprise->getLogoPath() ?>" alt="Logo de <?= $entreprise->getName() ?>">
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
                        <?php if ($entreprise->getBannerPath()): ?>
                            <img id="bannerPreviewModal" src="<?= $entreprise->getBannerPath() ?>" alt="Banner preview" style="max-width: 100%; max-height: 300px; border-radius: 12px; margin: 0 auto;">
                        <?php else: ?>
                            <div id="bannerPreviewModal" class="entreprise-banner-placeholder" style="max-width: 100%; height: 200px; margin: 0 auto;">Aucune bannière</div>
                        <?php endif; ?>
                    </div>

                    <div id="bannerActionsDefault">
                        <form method="post" action="<?= HOME_URL . 'entreprise/modifier' ?>" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="modifier_banner">
                            <input type="hidden" name="uiid" value="<?= $entreprise->getUiid() ?>">
                            <div class="flex-row justify-content-center gap-2" style="gap: 1rem;">
                                <label for="bannerInput" class="btn">
                                    Changer bannière
                                    <input type="file" id="bannerInput" name="banner" accept="image/*" style="display: none;" required>
                                </label>
                                <button type="submit" class="btn btn-success d-none mb" id="bannerSubmitBtn">Valider</button>
                            </div>
                        </form>
                        <?php if ($entreprise->getBannerPath()): ?>
                            <form method="post" action="<?= HOME_URL . 'entreprise/modifier' ?>" class="mt" id="deleteBannerForm">
                                <input type="hidden" name="action" value="supprimer_banner">
                                <input type="hidden" name="uiid" value="<?= $entreprise->getUiid() ?>">
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
                        <img id="logoPreviewModal" src="<?= $entreprise->getLogoPath() ?: HOME_URL . 'assets/images/default-entreprise-logo.png' ?>" alt="Logo preview" style="max-width: 180px; max-height: 180px; border-radius: 50%; margin: 0 auto;">
                    </div>

                    <div id="logoActionsDefault">
                        <form method="post" action="<?= HOME_URL . 'entreprise/modifier' ?>" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="modifier_logo">
                            <input type="hidden" name="uiid" value="<?= $entreprise->getUiid() ?>">
                            <div class="flex-row justify-content-center gap-2" style="gap: 1rem;">
                                <label for="logoInput" class="btn">
                                    Modifier logo
                                    <input type="file" id="logoInput" name="logo" accept="image/*" style="display: none;" required>
                                </label>
                                <button type="submit" class="btn btn-success d-none mb" id="logoSubmitBtn">Valider</button>
                            </div>
                        </form>
                        <?php if ($entreprise->getLogoPath()): ?>
                            <form action="<?= HOME_URL . 'entreprise/modifier' ?>" method="post" class="mt" id="deleteLogoForm">
                                <input type="hidden" name="action" value="supprimer_logo">
                                <input type="hidden" name="uiid" value="<?= $entreprise->getUiid() ?>">
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
        <div class="entreprise-main-content pr pl">
            <!-- Header with back button and title -->
            <div class="flex-row justify-content-between align-items-center mb-4">
                <div>
                    <h1 style="margin: 0;"><?= $entreprise->getName() ?></h1>
                    <div class="flex-row mt-2">
                        <span class="badge <?= $entreprise->getIsActive() ? 'badge-success' : 'badge-secondary' ?> mr-2">
                            <?= $entreprise->getIsActive() ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                </div>
                <div>
                    <a href="<?= HOME_URL . 'mes_entreprises' ?>" class="">
                        <span class="material-icons btn" style="color:white;">arrow_back</span>
                    </a>
                </div>
            </div>

            <div class="flex-row align-items-start" style="gap: 20px;">
                <!-- Main Details Section -->
                <div class="max-width-66">
                    <div class="card mb-4">
                        <!-- Description -->
                        <div class="p-3">
                            <h4>Description</h4>
                            <div class="card p-3 bg-light">
                                <p><?= nl2br($entreprise->getDescription() ?? 'Aucune description disponible') ?></p>
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
                                        <dd><?= $entreprise->getAddress() ?? 'Non renseignée' ?></dd>

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
                                            <?php if ($entreprise->getPhone()): ?>
                                                <a href="tel:<?= $entreprise->getPhone() ?>">
                                                    <?= $entreprise->getPhone() ?>
                                                </a>
                                            <?php else: ?>
                                                Non renseigné
                                            <?php endif; ?>
                                        </dd>

                                        <dt>Email</dt>
                                        <dd>
                                            <?php if ($entreprise->getEmail()): ?>
                                                <a href="mailto:<?= $entreprise->getEmail() ?>">
                                                    <?= $entreprise->getEmail() ?>
                                                </a>
                                            <?php else: ?>
                                                Non renseigné
                                            <?php endif; ?>
                                        </dd>

                                        <dt>Site web</dt>
                                        <dd>
                                            <?php if ($entreprise->getWebsite()): ?>
                                                <a href="<?= $entreprise->getWebsite() ?>" target="_blank">
                                                    <?= $entreprise->getWebsite() ?>
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
                                        <dt>SIRET</dt>
                                        <dd><?= $entreprise->getSiret() ?? 'Non renseigné' ?></dd>

                                        <dt>Créée le</dt>
                                        <dd><?= $entreprise->getCreatedAtFormatted() ?></dd>

                                        <dt>Dernière mise à jour</dt>
                                        <dd><?= $entreprise->getUpdatedAtFormatted() ?? 'Jamais' ?></dd>
                                    </dl>
                                </div>
                            </div>

                            <?php if ($isOwner): ?>
                                <div class="flex-row justify-content-between mt-4">
                                    <a href="<?= HOME_URL . 'entreprise/modifier?uiid=' . $entreprise->getUiid() ?>" class="btn linkNotDecorated">
                                        Modifier l'entreprise
                                    </a>
                                    <button type="button" class="btn btn-danger"
                                        onclick="document.getElementById('popup').style.display='flex'">
                                        Supprimer l'entreprise
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php if ($entreprise->getIsActive() == true): ?>
                    <div class="max-width-33">
                        <div class="card mt-3">
                            <div class="p-3">
                                <h4>Mes réalisations</h4>
                                <p>Vous avez <strong><?= $totalRealisations ?></strong> réalisation(s) pour cette entreprise.</p>
                                <?php if ($totalRealisations > 0): ?>
                                    <a href="<?= HOME_URL . 'entreprise/mes_realisations?entreprise_uiid=' . $entreprise->getUiid() ?>" class="btn linkNotDecorated">
                                        Voir mes réalisations
                                    </a>
                                <?php else: ?>
                                    <p class="text-muted">Vous n'avez pas encore créé de réalisations pour cette entreprise.</p>
                                    <a href="<?= HOME_URL . 'entreprise/mes_realisations/ajouter?entreprise_uiid=' . $entreprise->getUiid() . '&back_to=mes_entreprises&action=voir' ?>" class="btn linkNotDecorated">
                                        Créer ma première réalisation
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php elseif ($entreprise->getIsActive() == false && $entreprise->getHasRequestForActivation() == true && $entreprise->getRequestDate() > date('Y-m-d H:i:s', strtotime('-3 days'))) : ?>
                    <div class="max-width-33">
                        <div class="card mt-3">
                            <div class="p-3">
                                <h4>Demande en cours</h4>
                                <p>Date de la demande : <?= $entreprise->getRequestDateFormatted() ?></p>
                                <p class="text-muted">Vous avez déjà une demande d'activation en cours. Notre équipe examine votre demande et vous contactera sous 2-3 jours ouvrés.</p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="max-width-33">
                        <div class="card mt">
                            <div class="p">
                                <h4>Entreprise inactive</h4>
                                <p class="text-muted">Cette entreprise est actuellement inactive. Vous ne pouvez pas ajouter ou gérer des réalisations tant qu'elle n'est pas réactivée.</p>

                                <div>
                                    <h5>Demande d'activation</h5>
                                    <p class="small text-muted mb-3">Pour activer votre entreprise, veuillez nous fournir un des documents suivants :</p>
                                    <ul class="list-unstyled mb">
                                        <li class="mb-1"><span class="text-primary">•</span> Extrait Kbis</li>
                                        <li class="mb-1"><span class="text-primary">•</span> Avis de situation SIRENE + attestation URSSAF</li>
                                        <li class="mb-1"><span class="text-primary">•</span> Attestation CMA ou RNE</li>
                                        <li class="mb-1"><span class="text-primary">•</span> Autre document officiel prouvant l'existence légale de l'entreprise</li>
                                    </ul>
                                    <form class="mt" action="<?= HOME_URL . 'entreprise/demander_activation_mon_entreprise' ?>" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="uiid" value="<?= $entreprise->getUiid() ?>">
                                        <div class="mb-3">
                                            <label for="kbis" class="form-label">
                                                Votre document<span class="text-danger">*</span>
                                            </label>
                                            <input type="file" id="kbis" name="kbis" class="form-control" accept=".pdf" required>
                                            <small class="form-text text-muted">Fichier PDF uniquement, taille max : 5MB</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="message" class="form-label">Message complémentaire</label>
                                            <textarea id="message" name="message" class="form-control" rows="3" placeholder="Ajoutez des informations complémentaires si nécessaire (optionnel)"></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <strong>Note :</strong> Votre demande sera examinée par nos équipes sous 2-3 jours ouvrés.
                                                Vous recevrez une notification par email.
                                            </small>
                                        </div>
                                        <?php
                                        if ($entreprise->getHasRequestForActivation() == true && $entreprise->getRequestDate() < date('Y-m-d H:i:s', strtotime('-3 days'))) : ?>
                                            <p class="text-warning">Une demande d'activation a déjà été envoyée pour cette entreprise. Vous pouvez la renvoyer si nécessaire.</p>
                                            <button type="submit" class="btn btn-success w-100">
                                                <span class="material-icons" style="vertical-align: middle; font-size: 18px;">send</span>
                                                Renvoyer la demande d'activation
                                            </button>
                                        <?php else : ?>
                                            <button type="submit" class="btn btn-success w-100">
                                                <span class="material-icons" style="vertical-align: middle; font-size: 18px;">send</span>
                                                Envoyer la demande d'activation
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <?php if ($isOwner): ?>
            <div id="popup" class="d-none popup">
                <div class="card" style="max-width:500px;">
                    <h3>Confirmer la suppression</h3>
                    <button type="button" onclick="document.getElementById('popup').style.display='none'" style="position:absolute; right:10px; top:10px; background:none; border:none; font-size:18px; cursor:pointer;">×</button>
                    <div class="mt mb">
                        <p>Êtes-vous sûr de vouloir supprimer l'entreprise "<?= $entreprise->getName() ?>" ?</p>
                        <p class="text-danger"><strong>Attention :</strong> Cette action est irréversible.</p>
                    </div>
                    <div class="flex-row justify-content-between">
                        <button type="button" class="btn" onclick="document.getElementById('popup').style.display='none'">Annuler</button>
                        <form action="<?= HOME_URL . 'entreprise/supprimer' ?>" method="post">
                            <input type="hidden" name="uiid" value="<?= $entreprise->getUiid() ?>">
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