<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<main>
    <div class="flex-row justify-content-between mb">
        <div>
            <h1>Mes entreprises</h1>
        </div>
        <div>
            <a href="<?= HOME_URL . 'dashboard' ?>">
                <span class="material-icons btn" style="color:white;">arrow_back</span>

            </a>
        </div>
    </div>
    <div class="flex-row justify-content-between mb">
        <a href="<?= HOME_URL . 'entreprise/ajouter' ?>" class="btn linkNotDecorated">
            Ajouter une entreprise
        </a>
    </div>
    <?php include_once __DIR__ . '/../includes/messages.php'; ?>

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
                            <img src="<?= $entreprise->getBannerPath() ?>" alt="Bannière de <?= ($entreprise->getName()) ?>">
                        <?php endif; ?>

                        <div>
                            <div class="flex-row align-items-center mb">
                                <?php if ($entreprise->getLogoPath()): ?>
                                    <img src="<?= $entreprise->getLogoPath() ?>" style="width:50px; height:50px; border-radius:50%;" alt="Logo de <?= ($entreprise->getName()) ?>">
                                <?php endif; ?>
                                <h5><?= ($entreprise->getName()) ?></h5>
                            </div>

                            <p>
                                <?= ($entreprise->getDescription() ?? 'Aucune description') ?>
                            </p>

                            <?php if ($entreprise->getSiret()): ?>
                                <p><small class="text-muted">SIRET: <?= ($entreprise->getSiret()) ?></small></p>
                            <?php endif; ?>

                            <div>
                                <span class="text-success"><?= $entreprise->getStatusLabel() ?></span>

                                <?php if ($entreprise->getIsActive()): ?>
                                    <span class="text-info">Publié</span>
                                <?php else: ?>
                                    <span class="text-muted">Non publié</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="flex-row justify-content-between mt">
                            <a href="<?= HOME_URL . 'mes_entreprises?action=voir&uiid=' . $entreprise->getIdEntreprise() ?>" class="btn linkNotDecorated">
                                Voir
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php include_once __DIR__ . '/../includes/pagination.php'; ?>
    <?php endif; ?>
</main>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>