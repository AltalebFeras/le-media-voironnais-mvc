<div class="row mb-4">
    <div class="col-md-8">
        <h1>Mes associations</h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="/association/ajouter" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Ajouter une association
        </a>
    </div>
</div>

<?php if (empty($associations)): ?>
    <div class="alert alert-info">
        Vous n'avez pas encore d'associations. Cliquez sur "Ajouter une association" pour commencer.
    </div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($associations as $association): ?>
            <div class="col">
                <div class="card h-100">
                    <?php if ($association->getBannerPath()): ?>
                        <img src="<?= $association->getBannerPath() ?>" class="card-img-top" alt="Bannière de <?= htmlspecialchars($association->getName()) ?>">
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <?php if ($association->getLogoPath()): ?>
                                <img src="<?= $association->getLogoPath() ?>" class="me-3 rounded-circle" width="50" height="50" alt="Logo de <?= htmlspecialchars($association->getName()) ?>">
                            <?php endif; ?>
                            <h5 class="card-title mb-0"><?= htmlspecialchars($association->getName()) ?></h5>
                        </div>
                        
                        <p class="card-text">
                            <?= htmlspecialchars($association->getDescription() ?? 'Aucune description') ?>
                        </p>
                        
                        <?php if ($association->getIsActive()): ?>
                            <span class="badge bg-success mb-2">Active</span>
                        <?php else: ?>
                            <span class="badge bg-secondary mb-2">Inactive</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer">
                        <?php if ($association->getIdUser() == $_SESSION['user_id']): ?>
                            <div class="btn-group w-100">
                                <a href="/association/modifier/<?= $association->getIdAssociation() ?>" class="btn btn-sm btn-outline-primary">
                                    Modifier
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal<?= $association->getIdAssociation() ?>">
                                    Supprimer
                                </button>
                            </div>
                        <?php else: ?>
                            <p class="text-muted small mb-0">Vous êtes membre de cette association</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Delete Confirmation Modal -->
            <?php if ($association->getIdUser() == $_SESSION['user_id']): ?>
                <div class="modal fade" id="deleteModal<?= $association->getIdAssociation() ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Confirmer la suppression</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Êtes-vous sûr de vouloir supprimer l'association "<?= htmlspecialchars($association->getName()) ?>" ?</p>
                                <p class="text-danger"><strong>Attention :</strong> Cette action est irréversible.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <form action="/association/supprimer/<?= $association->getIdAssociation() ?>" method="post">
                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
