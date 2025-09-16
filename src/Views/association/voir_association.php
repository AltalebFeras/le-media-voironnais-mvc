<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<main>
    <div class="flex-row justify-content-between mb">
        <div>
            <h1><?= htmlspecialchars($association->getName()) ?></h1>
        </div>
        <div>
            <a href="<?= HOME_URL . 'association/mes-associations' ?>" class="">
                <span class="material-icons btn" style="color:white;">arrow_back</span>
            </a>
        </div>
    </div>

    <?php if (!$association): ?>
        <div class="custom-alert custom-alert-danger">
            <p>L'association demandée n'existe pas ou vous n'avez pas les permissions nécessaires pour y accéder.</p>
        </div>
    <?php else: ?>
        <div class="card mb">
            <!-- Banner -->
            <?php if ($association->getBannerPath()): ?>
                <div class="association-banner">
                    <img src="<?= $association->getBannerPath() ?>" alt="Bannière de <?= htmlspecialchars($association->getName()) ?>" style="width:100%; max-height:300px; object-fit:cover;">
                </div>
            <?php endif; ?>

            <!-- Header with logo and title -->
            <div class="flex-row align-items-center mb p-3">
                <?php if ($association->getLogoPath()): ?>
                    <img src="<?= $association->getLogoPath() ?>" style="width:100px; height:100px; border-radius:50%; margin-right:20px;" alt="Logo de <?= htmlspecialchars($association->getName()) ?>">
                <?php endif; ?>
                <div>
                    <h2><?= htmlspecialchars($association->getName()) ?></h2>
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
                <h3>Informations de l'association</h3>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4>Coordonnées</h4>
                        <dl>
                            <dt>Adresse</dt>
                            <dd><?= htmlspecialchars($association->getAddress() ?? 'Non renseignée') ?></dd>
                            
                            <dt>Téléphone</dt>
                            <dd><?= htmlspecialchars($association->getPhone() ?? 'Non renseigné') ?></dd>
                            
                            <dt>Email</dt>
                            <dd>
                                <?php if ($association->getEmail()): ?>
                                    <a href="mailto:<?= htmlspecialchars($association->getEmail()) ?>">
                                        <?= htmlspecialchars($association->getEmail()) ?>
                                    </a>
                                <?php else: ?>
                                    Non renseigné
                                <?php endif; ?>
                            </dd>
                            
                            <dt>Site web</dt>
                            <dd>
                                <?php if ($association->getWebsite()): ?>
                                    <a href="<?= htmlspecialchars($association->getWebsite()) ?>" target="_blank">
                                        <?= htmlspecialchars($association->getWebsite()) ?>
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
                            <dt>ID Association</dt>
                            <dd><?= $association->getIdAssociation() ?></dd>
                            
                            <dt>ID Utilisateur propriétaire</dt>
                            <dd><?= $association->getIdUser() ?></dd>
                            
                            <dt>ID Ville</dt>
                            <dd><?= $association->getIdVille() ?></dd>
                            
                            <dt>Créée le</dt>
                            <dd><?= $association->getCreatedAt() ?></dd>
                            
                            <dt>Dernière mise à jour</dt>
                            <dd><?= $association->getUpdatedAt() ?? 'Jamais' ?></dd>
                        </dl>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h4>Description</h4>
                    <div class="card p-3 bg-light">
                        <p><?= nl2br(htmlspecialchars($association->getDescription() ?? 'Aucune description disponible')) ?></p>
                    </div>
                </div>
                
                <?php if ($association->getIdUser() == $_SESSION['user_id']): ?>
                    <div class="flex-row justify-content-between mt-4">
                        <a href="/association/modifier/<?= $association->getIdAssociation() ?>" class="btn">
                            Modifier l'association
                        </a>
                        <button type="button" class="btn btn-danger"
                            onclick="document.getElementById('deleteModal').style.display='block'">
                            Supprimer l'association
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Delete Confirmation Modal -->
        <?php if ($association->getIdUser() == $_SESSION['user_id']): ?>
            <div id="deleteModal" class="d-none" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; display:flex; justify-content:center; align-items:center;">
                <div class="card" style="max-width:500px;">
                    <h3>Confirmer la suppression</h3>
                    <button type="button" onclick="this.parentElement.parentElement.style.display='none'" style="position:absolute; right:10px; top:10px; background:none; border:none; font-size:18px; cursor:pointer;">×</button>
                    <div class="mt mb">
                        <p>Êtes-vous sûr de vouloir supprimer l'association "<?= htmlspecialchars($association->getName()) ?>" ?</p>
                        <p class="text-danger"><strong>Attention :</strong> Cette action est irréversible.</p>
                    </div>
                    <div class="flex-row justify-content-between">
                        <button type="button" class="btn" onclick="document.getElementById('deleteModal').style.display='none'">Annuler</button>
                        <form action="/association/supprimer/<?= $association->getIdAssociation() ?>" method="post">
                            <button type="submit" class="btn deconnexion">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
