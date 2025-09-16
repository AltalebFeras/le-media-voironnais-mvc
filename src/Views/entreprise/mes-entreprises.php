<div class="row mb-4">
    <div class="col-md-8">
        <h1>Mes entreprises</h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="/entreprise/ajouter" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Ajouter une entreprise
        </a>
    </div>
</div>

<?php if (empty($entreprises)): ?>
    <div class="alert alert-info">
        Vous n'avez pas encore d'entreprises. Cliquez sur "Ajouter une entreprise" pour commencer.
    </div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($entreprises as $entreprise): ?>
            <div class="col">
                <div class="card h-100">
                    <?php if ($entreprise->getBannerPath()): ?>
                        <img src="<?= $entreprise->getBannerPath() ?>" class="card-img-top" alt="Bannière de <?= htmlspecialchars($entreprise->getName()) ?>">
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <?php if ($entreprise->getLogoPath()): ?>
                                <img src="<?= $entreprise->getLogoPath() ?>" class="me-3 rounded-circle" width="50" height="50" alt="Logo de <?= htmlspecialchars($entreprise->getName()) ?>">
                            <?php endif; ?>
                            <h5 class="card-title mb-0"><?= htmlspecialchars($entreprise->getName()) ?></h5>
                        </div>
                        
                        <p class="card-text">
                            <?= htmlspecialchars($entreprise->getDescription() ?? 'Aucune description') ?>
                        </p>
                        
                        <?php if ($entreprise->getSiret()): ?>
                            <p class="card-text"><small class="text-muted">SIRET: <?= htmlspecialchars($entreprise->getSiret()) ?></small></p>
                        <?php endif; ?>
                        
                        <div class="mb-2">
                            <?php if ($entreprise->getStatus() === 'actif'): ?>
                                <span class="badge bg-success">Actif</span>
                            <?php elseif ($entreprise->getStatus() === 'brouillon'): ?>
                                <span class="badge bg-secondary">Brouillon</span>
                            <?php elseif ($entreprise->getStatus() === 'suspendu'): ?>
                                <span class="badge bg-warning text-dark">Suspendu</span>
                            <?php endif; ?>
                            
                            <?php if ($entreprise->getIsActive()): ?>
                                <span class="badge bg-info">Publié</span>
                            <?php else: ?>
                                <span class="badge bg-light text-dark">Non publié</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="btn-group w-100">
                            <a href="/entreprise/modifier/<?= $entreprise->getIdEntreprise() ?>" class="btn btn-sm btn-outline-primary">
                                Modifier
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModal<?= $entreprise->getIdEntreprise() ?>">
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteModal<?= $entreprise->getIdEntreprise() ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirmer la suppression</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Êtes-vous sûr de vouloir supprimer l'entreprise "<?= htmlspecialchars($entreprise->getName()) ?>" ?</p>
                            <p class="text-danger"><strong>Attention :</strong> Cette action est irréversible.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <form action="/entreprise/supprimer/<?= $entreprise->getIdEntreprise() ?>" method="post">
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
