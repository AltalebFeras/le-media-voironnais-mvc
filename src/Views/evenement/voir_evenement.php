<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/banners-logos.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<?php include_once __DIR__ . '/../includes/messages.php'; ?>

<main style="padding:0;">
    <?php if (!$evenement): ?>
        <div class="custom-alert custom-alert-danger" style="margin: 20px;">
            <p>L'événement demandé n'existe pas ou vous n'avez pas les permissions nécessaires pour y accéder.</p>
        </div>
    <?php else: ?>
        <!-- Banner Section (like Facebook) -->
        <div class="evenement-banner-section">
            <div class="evenement-banner-wrapper">
                <?php if ($evenement['bannerPath']): ?>
                    <img id="currentBanner" src="<?= $evenement['bannerPath'] ?>" alt="Bannière de <?= $evenement['title'] ?>" class="evenement-banner-img">
                <?php else: ?>
                    <div id="currentBanner" class="evenement-banner-placeholder">Aucune bannière</div>
                <?php endif; ?>
                <img id="bannerPreview" style="display:none;">
            </div>
            
            <?php if ($isOwner): ?>
                <div class="evenement-banner-actions">
                    <form method="post" action="<?= HOME_URL . 'evenement/edit_banner?uiid=' . $evenement['uiid'] ?>" enctype="multipart/form-data">
                        <label for="bannerInput" class="btn">
                            Changer bannière
                            <input type="file" id="bannerInput" name="banner" accept="image/*" required>
                        </label>
                        <button type="submit" class="btn" id="bannerSubmitBtn" disabled>Valider</button>
                        <button type="button" id="cancelBannerBtn" class="btn" style="display:none;">Annuler</button>
                    </form>
                    <?php if ($evenement['bannerPath']): ?>
                        <form method="post" action="<?= HOME_URL . 'evenement/delete_banner?uiid=' . $evenement['uiid'] ?>">
                            <button type="submit" class="btn bg-danger">Supprimer</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Main Content Below Banner -->
        <div class="evenement-main-content">
            <!-- Header with back button and title -->
            <div class="flex-row justify-content-between align-items-center mb-4">
                <div>
                    <h1 style="margin: 0;"><?= $evenement['title'] ?></h1>
                    <div class="flex-row mt-2">
                        <span class="badge status-<?= $evenement['status'] ?> mr-2">
                            <?= ucfirst($evenement['status']) ?>
                        </span>
                        <span class="badge <?= $evenement['isPublic'] ? 'badge-info' : 'badge-warning' ?>">
                            <?= $evenement['isPublic'] ? 'Public' : 'Privé' ?>
                        </span>
                    </div>
                </div>
                <div>
                    <a href="<?= HOME_URL ?>mes_evenements">
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
                                <p><?= nl2br($evenement['description']) ?></p>
                            </div>
                            
                            <?php if ($evenement['shortDescription']): ?>
                                <h4 class="mt-3">Description courte</h4>
                                <div class="card p-3 bg-light">
                                    <p><?= nl2br($evenement['shortDescription']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <!-- Event Details -->
                        <div class="p-3">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h4>Informations pratiques</h4>
                                    <dl>
                                        <dt>Date et heure</dt>
                                        <dd><?= date('d/m/Y H:i', strtotime($evenement['startDate'])) ?></dd>

                                        <?php if ($evenement['endDate']): ?>
                                            <dt>Date de fin</dt>
                                            <dd><?= date('d/m/Y H:i', strtotime($evenement['endDate'])) ?></dd>
                                        <?php endif; ?>

                                        <?php if ($evenement['registrationDeadline']): ?>
                                            <dt>Date limite d'inscription</dt>
                                            <dd><?= date('d/m/Y H:i', strtotime($evenement['registrationDeadline'])) ?></dd>
                                        <?php endif; ?>

                                        <dt>Adresse</dt>
                                        <dd><?= $evenement['address'] ?></dd>
                                        
                                        <?php if ($ville): ?>
                                            <dt>Ville</dt>
                                            <dd><?= $ville['ville_code_postal'] ?> <?= $ville['ville_nom_reel'] ?></dd>
                                        <?php endif; ?>

                                        <?php if ($evenement['price'] > 0): ?>
                                            <dt>Prix</dt>
                                            <dd><?= number_format($evenement['price'], 2) ?> <?= $evenement['currency'] ?></dd>
                                        <?php endif; ?>
                                    </dl>
                                </div>
                                
                                <div class="col-md-6">
                                    <h4>Organisation</h4>
                                    <dl>
                                        <?php if ($evenement['category_name']): ?>
                                            <dt>Catégorie</dt>
                                            <dd><?= $evenement['category_name'] ?></dd>
                                        <?php endif; ?>

                                        <dt>Organisé par</dt>
                                        <dd>
                                            <?php if ($evenement['idAssociation'] && $evenement['association_name']): ?>
                                                Association: <?= $evenement['association_name'] ?>
                                            <?php elseif ($evenement['idEntreprise'] && $evenement['entreprise_name']): ?>
                                                Entreprise: <?= $evenement['entreprise_name'] ?>
                                            <?php else: ?>
                                                Particulier
                                            <?php endif; ?>
                                        </dd>

                                        <?php if ($evenement['maxParticipants'] > 0): ?>
                                            <dt>Participants</dt>
                                            <dd>
                                                <?= $evenement['currentParticipants'] ?> / <?= $evenement['maxParticipants'] ?>
                                                <div class="progress-bar" style="width: 100%; height: 8px; background: #e9ecef; border-radius: 4px; overflow: hidden; margin-top: 0.5rem;">
                                                    <div class="progress-fill" style="height: 100%; background: linear-gradient(90deg, #3a7ca5 0%, #6ed3cf 100%); width: <?= ($evenement['currentParticipants'] / $evenement['maxParticipants']) * 100 ?>%;"></div>
                                                </div>
                                            </dd>
                                        <?php endif; ?>

                                        <dt>Créé le</dt>
                                        <dd><?= date('d/m/Y H:i', strtotime($evenement['createdAt'])) ?></dd>
                                        
                                        <?php if ($evenement['updatedAt']): ?>
                                            <dt>Modifié le</dt>
                                            <dd><?= date('d/m/Y H:i', strtotime($evenement['updatedAt'])) ?></dd>
                                        <?php endif; ?>
                                    </dl>
                                </div>
                            </div>

                            <?php if (!empty($evenement['images'])): ?>
                                <div class="mb-3">
                                    <h4>Images</h4>
                                    <div class="event-images" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <?php foreach ($evenement['images'] as $image): ?>
                                            <img src="<?= $image['imagePath'] ?>"
                                                alt="<?= $image['altText'] ?? 'Image de l\'événement' ?>" 
                                                style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 2px solid #ddd;">
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($isOwner): ?>
                                <div class="flex-row justify-content-between mt-4">
                                    <a href="<?= HOME_URL ?>evenement/modifier?uiid=<?= $evenement['uiid'] ?>" class="btn linkNotDecorated">
                                        Modifier l'événement
                                    </a>
                                    <button type="button" class="btn btn-danger"
                                        onclick="document.getElementById('deleteModal').style.display='flex'">
                                        Supprimer l'événement
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Participants/Stats Sidebar -->
                <div class="max-width-33">
                    <?php if (!empty($evenement['participants'])): ?>
                        <div class="card mb-3">
                            <div class="p-3">
                                <h3>Participants inscrits (<?= count($evenement['participants']) ?>)</h3>
                                <div class="participants-list" style="max-height: 300px; overflow-y: auto;">
                                    <?php foreach ($evenement['participants'] as $participant): ?>
                                        <div class="participant-item p-2 mb-2 border-bottom">
                                            <div class="flex-row align-items-center">
                                                <div class="participant-avatar">
                                                    <div style="width:40px; height:40px; border-radius:50%; background-color:#ddd; display:flex; align-items:center; justify-content:center; margin-right:10px;">
                                                        <?= strtoupper(substr($participant['firstName'], 0, 1) . substr($participant['lastName'], 0, 1)) ?>
                                                    </div>
                                                </div>
                                                <div class="participant-info flex-grow">
                                                    <strong><?= $participant['firstName'] ?> <?= $participant['lastName'] ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <span class="badge badge-sm <?= $participant['status'] === 'confirme' ? 'badge-success' : 'badge-warning' ?>">
                                                            <?= ucfirst($participant['status']) ?>
                                                        </span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($evenement['invitations'])): ?>
                        <div class="card mb-3">
                            <div class="p-3">
                                <h3>Invitations envoyées (<?= count($evenement['invitations']) ?>)</h3>
                                <div class="invitations-list" style="max-height: 200px; overflow-y: auto;">
                                    <?php foreach ($evenement['invitations'] as $invitation): ?>
                                        <div class="invitation-item p-2 mb-2 border-bottom">
                                            <div class="flex-row align-items-center">
                                                <div class="invitation-info flex-grow">
                                                    <strong><?= $invitation['firstName'] ?> <?= $invitation['lastName'] ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <span class="badge badge-sm <?= $invitation['status'] === 'accepte' ? 'badge-success' : ($invitation['status'] === 'refuse' ? 'badge-danger' : 'badge-secondary') ?>">
                                                            <?= ucfirst($invitation['status']) ?>
                                                        </span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="p-3">
                            <h4>Statistiques</h4>
                            <dl>
                                <dt>Participants inscrits</dt>
                                <dd><?= $evenement['currentParticipants'] ?? 0 ?></dd>
                                
                                <?php if ($evenement['maxParticipants'] > 0): ?>
                                    <dt>Places disponibles</dt>
                                    <dd><?= $evenement['maxParticipants'] - ($evenement['currentParticipants'] ?? 0) ?></dd>
                                <?php endif; ?>

                                <?php if ($evenement['requiresApproval']): ?>
                                    <dt>Inscription</dt>
                                    <dd><span class="badge badge-warning">Avec approbation</span></dd>
                                <?php endif; ?>
                            </dl>
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
                        <p>Êtes-vous sûr de vouloir supprimer l'événement "<?= $evenement['title'] ?>" ?</p>
                        <p class="text-danger"><strong>Attention :</strong> Cette action est irréversible.</p>
                    </div>
                    <div class="flex-row justify-content-between">
                        <button type="button" class="btn" onclick="document.getElementById('deleteModal').style.display='none'">Annuler</button>
                        <form action="<?= HOME_URL . 'mes_evenements?action=delete&uiid=' . $evenement['uiid'] ?>" method="post">
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