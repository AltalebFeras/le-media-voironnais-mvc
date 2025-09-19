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
    <?php include_once __DIR__ . '/../includes/messages.php'; ?>

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
                            <img src="<?= $association->getBannerPath() ?>" alt="Bannière de <?= $association->getName() ?>">
                        <?php endif; ?>

                        <div>
                            <div class="flex-row align-items-center mb">
                                <?php if ($association->getLogoPath()): ?>
                                    <img src="<?= $association->getLogoPath() ?>" style="width:50px; height:50px; border-radius:50%;" alt="Logo de <?= $association->getName() ?>">
                                <?php endif; ?>
                                <h5><?= $association->getName() ?></h5>
                            </div>
                            
                            <div>
                                <p>
                                    <strong>Statut:</strong>
                                    <?php if ($association->getIsActive()): ?>
                                        <span class="text-success">Active</span>
                                    <?php else: ?>
                                        <span class="text-muted">Inactive</span>
                                    <?php endif; ?>
                                    |
                                    <strong>Visibilité:</strong>
                                    <?php if ($association->getIsPublic()): ?>
                                        <span class="text-success">Publique</span>
                                    <?php else: ?>
                                        <span class="text-muted">Privée</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <div>
                            <?php if ($association->getIdUser() == $_SESSION['user_id']): ?>
                                <div class="flex-row justify-content-between mt">
                                    <a href="<?= HOME_URL . 'association/voir/' . $association->getIdAssociation() ?>" class="btn">
                                        Voir détails
                                    </a>
                                    <a href="<?= HOME_URL . 'association/modifier/' . $association->getIdAssociation() ?>" class="btn">
                                        Modifier
                                    </a>
                                    <button type="button" class="btn"
                                        onclick="document.getElementById('deleteModal<?= $association->getIdAssociation() ?>').style.display='block'">
                                        Supprimer
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="flex-row justify-content-between mt">
                                    <a href="<?= HOME_URL . 'mes_associations?action=voir&uiid=' . $association->getIdAssociation() ?>" class="btn linkNotDecorated">
                                        Voir
                                    </a>
                                    <p class="text-muted">Vous êtes membre de cette association</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
			<?php include_once __DIR__ . '/../includes/pagination.php'; ?>

    <?php endif; ?>
</main>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>