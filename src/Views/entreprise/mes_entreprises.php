<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<main>
    <div class="flex-row justify-content-between mb">
        <div>
            <h1>Mes entreprises</h1>
        </div>
        <div>
            <a href="/entreprise/ajouter" class="btn linkNotDecorated"> Ajouter une entreprise
            </a>
        </div>
    </div>

    <?php if (empty($entreprises)): ?>
        <div class="custom-alert custom-alert-success">
            <p>Vous n'avez pas encore d'entreprises. Cliquez sur "Ajouter une entreprise" pour commencer.</p>
        </div>
    <?php else: ?>
        <div class="flex-row flex-wrap">
            <?php foreach ($entreprises as $entreprise): ?>
                <div class="max-width-33">
                    <div class="card">
                        <?php if ($entreprise->getBannerPath()): ?>
                            <img src="<?= $entreprise->getBannerPath() ?>" alt="Bannière de <?= htmlspecialchars($entreprise->getName()) ?>">
                        <?php endif; ?>

                        <div>
                            <div class="flex-row align-items-center mb">
                                <?php if ($entreprise->getLogoPath()): ?>
                                    <img src="<?= $entreprise->getLogoPath() ?>" style="width:50px; height:50px; border-radius:50%;" alt="Logo de <?= htmlspecialchars($entreprise->getName()) ?>">
                                <?php endif; ?>
                                <h5><?= htmlspecialchars($entreprise->getName()) ?></h5>
                            </div>

                            <p>
                                <?= htmlspecialchars($entreprise->getDescription() ?? 'Aucune description') ?>
                            </p>

                            <?php if ($entreprise->getSiret()): ?>
                                <p><small class="text-muted">SIRET: <?= htmlspecialchars($entreprise->getSiret()) ?></small></p>
                            <?php endif; ?>

                            <div>
                                <?php if ($entreprise->getStatus() === 'actif'): ?>
                                    <span class="text-success">Actif</span>
                                <?php elseif ($entreprise->getStatus() === 'brouillon'): ?>
                                    <span class="text-muted">Brouillon</span>
                                <?php elseif ($entreprise->getStatus() === 'suspendu'): ?>
                                    <span class="text-warning">Suspendu</span>
                                <?php endif; ?>

                                <?php if ($entreprise->getIsActive()): ?>
                                    <span class="text-info">Publié</span>
                                <?php else: ?>
                                    <span class="text-muted">Non publié</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="flex-row justify-content-between mt">
                            <a href="/entreprise/modifier/<?= $entreprise->getIdEntreprise() ?>" class="btn">
                                Modifier
                            </a>
                            <button type="button" class="btn" 
                                onclick="document.getElementById('deleteModal<?= $entreprise->getIdEntreprise() ?>').style.display='block'">
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Delete Confirmation Modal -->
                <div id="deleteModal<?= $entreprise->getIdEntreprise() ?>" class="d-none" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; display:flex; justify-content:center; align-items:center;">
                    <div class="card" style="max-width:500px;">
                        <h3>Confirmer la suppression</h3>
                        <button type="button" onclick="this.parentElement.parentElement.style.display='none'" style="position:absolute; right:10px; top:10px; background:none; border:none; font-size:18px; cursor:pointer;">×</button>
                        <div class="mt mb">
                            <p>Êtes-vous sûr de vouloir supprimer l'entreprise "<?= htmlspecialchars($entreprise->getName()) ?>" ?</p>
                            <p class="text-danger"><strong>Attention :</strong> Cette action est irréversible.</p>
                        </div>
                        <div class="flex-row justify-content-between">
                            <button type="button" class="btn" onclick="document.getElementById('deleteModal<?= $entreprise->getIdEntreprise() ?>').style.display='none'">Annuler</button>
                            <form action="/entreprise/supprimer/<?= $entreprise->getIdEntreprise() ?>" method="post">
                                <button type="submit" class="btn deconnexion">Supprimer</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>