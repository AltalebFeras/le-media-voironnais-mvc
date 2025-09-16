<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<main>
    <div class="flex-row justify-content-between mb">
        <div>
            <h1>Mes associations</h1>
        </div>
        <div>
            <a href="<?= HOME_URL . 'dashboard' ?>" class="">
                <span class="material-icons btn" style="color:white;">arrow_back</span>
            </a>
        </div>
    </div>
    <div class="flex-row justify-content-between mb">
        <a href="<?= HOME_URL . 'association/ajouter' ?>" class="btn linkNotDecorated">
            Ajouter une association
        </a>
    </div>

    <?php if (empty($associations)): ?>
        <div class="custom-alert custom-alert-success">
            <p>Vous n'avez pas encore d'associations. Cliquez sur "Ajouter une association" pour commencer.</p>
        </div>
    <?php else: ?>
        <div class="flex-row flex-wrap">
            <?php foreach ($associations as $association): ?>
                <div class="max-width-33">
                    <div class="card">
                        <?php if ($association->getBannerPath()): ?>
                            <img src="<?= $association->getBannerPath() ?>" alt="Bannière de <?= htmlspecialchars($association->getName()) ?>">
                        <?php endif; ?>

                        <div>
                            <div class="flex-row align-items-center mb">
                                <?php if ($association->getLogoPath()): ?>
                                    <img src="<?= $association->getLogoPath() ?>" style="width:50px; height:50px; border-radius:50%;" alt="Logo de <?= htmlspecialchars($association->getName()) ?>">
                                <?php endif; ?>
                                <h5><?= htmlspecialchars($association->getName()) ?></h5>
                            </div>

                            <p>
                                <?= htmlspecialchars($association->getDescription() ?? 'Aucune description') ?>
                            </p>

                            <div>
                                <?php if ($association->getIsActive()): ?>
                                    <span class="text-success">Active</span>
                                <?php else: ?>
                                    <span class="text-muted">Inactive</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div>
                            <?php if ($association->getIdUser() == $_SESSION['user_id']): ?>
                                <div class="flex-row justify-content-between mt">
                                    <a href="/association/modifier/<?= $association->getIdAssociation() ?>" class="btn">
                                        Modifier
                                    </a>
                                    <button type="button" class="btn"
                                        onclick="document.getElementById('deleteModal<?= $association->getIdAssociation() ?>').style.display='block'">
                                        Supprimer
                                    </button>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Vous êtes membre de cette association</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Delete Confirmation Modal -->
                <?php if ($association->getIdUser() == $_SESSION['user_id']): ?>
                    <div id="deleteModal<?= $association->getIdAssociation() ?>" class="d-none" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; display:flex; justify-content:center; align-items:center;">
                        <div class="card" style="max-width:500px;">
                            <h3>Confirmer la suppression</h3>
                            <button type="button" onclick="this.parentElement.parentElement.style.display='none'" style="position:absolute; right:10px; top:10px; background:none; border:none; font-size:18px; cursor:pointer;">×</button>
                            <div class="mt mb">
                                <p>Êtes-vous sûr de vouloir supprimer l'association "<?= htmlspecialchars($association->getName()) ?>" ?</p>
                                <p class="text-danger"><strong>Attention :</strong> Cette action est irréversible.</p>
                            </div>
                            <div class="flex-row justify-content-between">
                                <button type="button" class="btn" onclick="document.getElementById('deleteModal<?= $association->getIdAssociation() ?>').style.display='none'">Annuler</button>
                                <form action="/association/supprimer/<?= $association->getIdAssociation() ?>" method="post">
                                    <button type="submit" class="btn deconnexion">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>