<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<main>
    <div class="flex-row justify-content-between mb">
        <div>
            <h1><?= $entreprise->getName() ?></h1>
        </div>
        <div>
            <a href="<?= HOME_URL . 'mes_entreprises' ?>" class="">
                <span class="material-icons btn" style="color:white;">arrow_back</span>
            </a>
        </div>
    </div>

    <?php if (!$entreprise): ?>
        <div class="custom-alert custom-alert-danger">
            <p>L'entreprise demandée n'existe pas ou vous n'avez pas les permissions nécessaires pour y accéder.</p>
        </div>
    <?php else: ?>
        <div class="flex-row">
            <!-- Main Content -->
            <div class="max-width-66">
                <div class="card mb">
                    <!-- Banner -->
                    <?php if ($entreprise->getBannerPath()): ?>
                        <div class="entreprise-banner">
                            <img src="<?= $entreprise->getBannerPath() ?>" alt="Bannière de <?= $entreprise->getName() ?>" style="width:100%; max-height:300px; object-fit:cover;">
                        </div>
                    <?php endif; ?>

                    <!-- Header with logo and title -->
                    <div class="flex-row align-items-center mb p-3">
                        <?php if ($entreprise->getLogoPath()): ?>
                            <img src="<?= $entreprise->getLogoPath() ?>" style="width:100px; height:100px; border-radius:50%; margin-right:20px;" alt="Logo de <?= $entreprise->getName() ?>">
                        <?php endif; ?>
                        <div>
                            <h2><?= $entreprise->getName() ?></h2>
                            <div class="flex-row">
                                <span class="badge <?= $entreprise->getIsActive() ? 'badge-success' : 'badge-secondary' ?> mr-2">
                                    <?= $entreprise->getIsActive() ? 'Active' : 'Inactive' ?>
                                </span>
                                <span class="badge <?= $entreprise->getStatus() === 'validee' ? 'badge-info' : ($entreprise->getStatus() === 'brouillon' ? 'badge-warning' : 'badge-danger') ?>">
                                    <?= ucfirst($entreprise->getStatus()) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="p-3">
                        <div class="mb-3">
                            <h4>Description</h4>
                            <div class="card p-3 bg-light">
                                <p><?= nl2br($entreprise->getDescription() ?? 'Aucune description disponible') ?></p>
                            </div>
                        </div>
                        
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
                                <a href="<?= HOME_URL.'entreprise/modifier?uiid='.$entreprise->getIdEntreprise() ?>" class="btn linkNotDecorated">
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
            
            <!-- Services/Products Sidebar -->
            <div class="max-width-33">
                <div class="card">
                    <h3>Services & Produits</h3>
                    <?php if (!empty($services)): ?>
                        <div class="services-list">
                            <?php foreach ($services as $service): ?>
                                <div class="service-item p-2 mb-2 border-bottom">
                                    <strong><?= $service['name'] ?></strong>
                                    <br>
                                    <small class="text-muted"><?= $service['description'] ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Aucun service ou produit renseigné pour le moment.</p>
                    <?php endif; ?>
                    
                    <div class="card mt-3">
                        <h4>Statistiques</h4>
                        <dl>
                            <dt>Vues du profil</dt>
                            <dd><?= $stats['views'] ?? 0 ?></dd>
                            
                            <dt>Dernière activité</dt>
                            <dd><?= $stats['last_activity'] ?? 'Inconnue' ?></dd>
                        </dl>
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
                        <p>Êtes-vous sûr de vouloir supprimer l'entreprise "<?= $entreprise->getName() ?>" ?</p>
                        <p class="text-danger"><strong>Attention :</strong> Cette action est irréversible.</p>
                    </div>
                    <div class="flex-row justify-content-between">
                        <button type="button" class="btn" onclick="document.getElementById('deleteModal').style.display='none'">Annuler</button>
                        <form action="<?= HOME_URL . 'mes_entreprises?action=delete&uiid=' . $entreprise->getIdEntreprise() ?>" method="post">
                            <button type="submit" class="btn deconnexion">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
