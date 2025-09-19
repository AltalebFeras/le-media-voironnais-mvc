<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<main>
    <div class="flex-row justify-content-between mb">
        <div>
            <h1><?= $association->getName() ?></h1>
        </div>
        <div>
            <a href="<?= HOME_URL . 'mes_associations' ?>" class="">
                <span class="material-icons btn" style="color:white;">arrow_back</span>
            </a>
        </div>
    </div>

    <?php if (!$association): ?>
        <div class="custom-alert custom-alert-danger">
            <p>L'association demandée n'existe pas ou vous n'avez pas les permissions nécessaires pour y accéder.</p>
        </div>
    <?php else: ?>
        <div class="flex-row">
            <!-- Main Content -->
            <div class="max-width-66">
                <div class="card mb">
                    <!-- Banner -->
                    <?php if ($association->getBannerPath()): ?>
                        <div class="association-banner">
                            <img src="<?= $association->getBannerPath() ?>" alt="Bannière de <?= $association->getName() ?>" style="width:100%; max-height:300px; object-fit:cover;">
                        </div>
                    <?php endif; ?>

                    <!-- Header with logo and title -->
                    <div class="flex-row align-items-center mb p-3">
                        <?php if ($association->getLogoPath()): ?>
                            <img src="<?= $association->getLogoPath() ?>" style="width:100px; height:100px; border-radius:50%; margin-right:20px;" alt="Logo de <?= $association->getName() ?>">
                        <?php endif; ?>
                        <div>
                            <h2><?= $association->getName() ?></h2>
                            <div class="flex-row">
                                <span class="badge <?= $association->getIsActive() ? 'badge-success' : 'badge-secondary' ?> mr-2">
                                    <?= $association->getIsActive() ? 'Active' : 'Inactive' ?>
                                </span>
                                <span class="badge <?= $association->getIsPublic() ? 'badge-info' : 'badge-warning' ?>">
                                    <?= $association->getIsPublic() ? 'Publique' : 'Privée' ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="p-3">
                        <div class="mb-3">
                            <h4>Description</h4>
                            <div class="card p-3 bg-light">
                                <p><?= nl2br($association->getDescription() ?? 'Aucune description disponible') ?></p>
                            </div>
                        </div>
                        
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
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
