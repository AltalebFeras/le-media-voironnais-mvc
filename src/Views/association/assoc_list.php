<?php
$title = 'Toutes les associations';
$description = 'Découvrez toutes les associations actives sur Le Média Voironnais';
?>

<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h2 mb-4">
                <i class="material-icons me-2">groups</i>
                Toutes les associations
            </h1>

            <?php if (!empty($associations)): ?>
                <div class="row">
                    <?php foreach ($associations as $association): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-start mb-3">
                                        <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($association['logoPath']) ?>" 
                                             alt="Logo <?= htmlspecialchars($association['name']) ?>" 
                                             class="rounded me-3" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <h5 class="card-title mb-1">
                                                <a href="<?= HOME_URL . 'associations/' . htmlspecialchars($association['slug']) ?>" 
                                                   class="text-decoration-none">
                                                    <?= htmlspecialchars($association['name']) ?>
                                                </a>
                                            </h5>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <i class="material-icons me-1" style="font-size: 16px;">location_on</i>
                                                    <?= htmlspecialchars($association['ville_nom_reel']) ?>
                                                </small>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="<?= HOME_URL . 'associations/' . htmlspecialchars($association['slug']) ?>" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="material-icons me-1" style="font-size: 16px;">visibility</i>
                                            Voir l'association
                                        </a>
                                        
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-outline-secondary btn-sm" 
                                                    title="Contacter l'association">
                                                <i class="material-icons" style="font-size: 16px;">email</i>
                                            </button>
                                            <button class="btn btn-outline-secondary btn-sm" 
                                                    title="Partager">
                                                <i class="material-icons" style="font-size: 16px;">share</i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-footer bg-transparent">
                                    <small class="text-muted">
                                        <i class="material-icons me-1" style="font-size: 14px;">groups</i>
                                        Association active
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="material-icons text-muted" style="font-size: 4rem;">groups</i>
                    </div>
                    <h4 class="text-muted">Aucune association trouvée</h4>
                    <p class="text-muted">Il n'y a actuellement aucune association enregistrée.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
