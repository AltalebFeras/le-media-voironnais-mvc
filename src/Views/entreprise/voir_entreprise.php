<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/banners-logos.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main style="padding:0;">
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
            </div>

            <?php if ($isOwner): ?>
                <div class="entreprise-banner-actions">
                    <form method="post" action="<?= HOME_URL . 'entreprise/modifier?action=modifier_banner&uiid=' . $entreprise->getUiid() ?>" enctype="multipart/form-data">
                        <label for="bannerInput" class="btn">
                            Changer bannière
                            <input type="file" id="bannerInput" name="banner" accept="image/*" required>
                        </label>
                        <button type="submit" class="btn" id="bannerSubmitBtn" disabled>Valider</button>
                        <button type="button" id="cancelBannerBtn" class="btn" style="display:none;">Annuler</button>
                    </form>
                    <?php if ($entreprise->getBannerPath()): ?>
                        <form method="post" action="<?= HOME_URL . 'entreprise/modifier?action=supprimer_banner&uiid=' . $entreprise->getUiid() ?>">
                            <button type="submit" class="btn bg-danger">Supprimer</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Logo overlaps banner -->
            <?php if ($isOwner): ?>
                <div class="entreprise-logo-picture">
                    <img id="currentLogo"
                        src="<?= $entreprise->getLogoPath() ?: HOME_URL . 'assets/images/default-entreprise-logo.png' ?>"
                        data-original-src="<?= $entreprise->getLogoPath() ?: HOME_URL . 'assets/images/default-entreprise-logo.png' ?>"
                        alt="Logo de <?= $entreprise->getName() ?>">

                    <form method="post" action="<?= HOME_URL . 'entreprise/modifier?action=modifier_logo&uiid=' . $entreprise->getUiid() ?>" enctype="multipart/form-data">
                        <label for="logoInput" class="btn">
                            Modifier logo
                            <input type="file" id="logoInput" name="logo" accept="image/*" required>
                        </label>
                        <button type="submit" class="btn">Valider</button>
                        <button type="button" id="cancelLogo" class="btn" style="display:none;">Annuler</button>
                    </form>
                    <?php if ($entreprise->getLogoPath()): ?>
                        <form action="<?= HOME_URL . 'entreprise/modifier?action=supprimer_logo&uiid=' . $entreprise->getUiid() ?>" method="post">
                            <button type="submit" class="btn">Supprimer</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="entreprise-logo-display">
                    <?php if ($entreprise->getLogoPath()): ?>
                        <img src="<?= $entreprise->getLogoPath() ?>" alt="Logo de <?= $entreprise->getName() ?>">
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php include_once __DIR__ . '/../includes/messages.php'; ?>

        <!-- Main Content Below Banner -->
        <div class="entreprise-main-content">
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

            <div class="flex-row" style="gap: 20px;">
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
                                        onclick="document.getElementById('deleteModal').style.display='flex'">
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
                                    <form class="mt" action="<?= HOME_URL . 'entreprise/demander_activation_mon_entreprise?uiid=' . $entreprise->getUiid() ?>" method="post" enctype="multipart/form-data">
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
            <div id="deleteModal" class="d-none" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; display:none; justify-content:center; align-items:center;">
                <div class="card" style="max-width:500px;">
                    <h3>Confirmer la suppression</h3>
                    <button type="button" onclick="document.getElementById('deleteModal').style.display='none'" style="position:absolute; right:10px; top:10px; background:none; border:none; font-size:18px; cursor:pointer;">×</button>
                    <div class="mt mb">
                        <p>Êtes-vous sûr de vouloir supprimer l'entreprise "<?= $entreprise->getName() ?>" ?</p>
                        <p class="text-danger"><strong>Attention :</strong> Cette action est irréversible.</p>
                    </div>
                    <div class="flex-row justify-content-between">
                        <button type="button" class="btn" onclick="document.getElementById('deleteModal').style.display='none'">Annuler</button>
                        <form action="<?= HOME_URL . 'mes_entreprises?action=delete&uiid=' . $entreprise->getUiid() ?>" method="post">
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