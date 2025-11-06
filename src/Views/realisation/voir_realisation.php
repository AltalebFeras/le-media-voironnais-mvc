<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/evenements/carousel.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <!-- Header with back button and title -->
    <div class="flex-row justify-content-between">
        <h1><?= $realisation->getTitle() ?></h1>
        <a href="<?= HOME_URL . 'entreprise/mes_realisations?entreprise_uiid=' . $entreprise->getUiid() ?>" class="">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
        </a>
    </div>

    <?php include_once __DIR__ . '/../includes/messages.php'; ?>

    <div class="flex-row" style="gap: 20px;">
        <!-- Main Details Section -->
        <div class="max-width-66">
            <div class="card mb">
                <div class="p">
                    <div class="flex-row align-items-center mb">
                        <h3>Entreprise : <?= $entreprise->getName() ?></h3>
                        <?php if ($isOwner): ?>
                            <div>
                                <a href="<?= HOME_URL . 'entreprise/mes_realisations/modifier?realisation_uiid=' . $realisation->getUiid() ?>"
                                    class="btn linkNotDecorated">Modifier</a>
                                <!-- Replaced inline confirm with modal trigger -->
                                <button type="button" class="btn bg-danger"
                                    onclick="document.getElementById('popup').style.display='flex'">
                                    Supprimer
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex-row mb-3">
                        <?php if ($realisation->getIsFeatured()): ?>
                            <span class="badge badge-success mr-2">Mise en avant</span>
                        <?php endif; ?>
                        <?php if (!$realisation->getIsPublic()): ?>
                            <span class="badge badge-secondary">Privée</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="p-3">
                    <h4>Description</h4>
                    <div class="card p-3 bg-light">
                        <p><?= nl2br($realisation->getDescription() ?? 'Aucune description disponible') ?></p>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="p-3">
                    <h4>Informations</h4>
                    <dl>
                        <?php if ($realisation->getDateRealized()): ?>
                            <dt>Date de réalisation</dt>
                            <dd><?= ($realisation->getFormattedDateRealized()) ?></dd>
                        <?php endif; ?>

                        <dt>Créé le</dt>
                        <dd><?= ($realisation->getCreatedAtFormatted()) ?></dd>

                        <?php if ($realisation->getUpdatedAt()): ?>
                            <dt>Modifié le</dt>
                            <dd><?= ($realisation->getUpdatedAtFormatted()) ?></dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Images Gallery Sidebar -->
        <div class="max-width-33">
            <div class="card">
                <div class="p-3">
                    <div class="flex-row justify-content-between align-items-center mb-3">
                        <h3>Galerie d'images</h3>
                        <?php if ($isOwner): ?>
                            <button onclick="document.getElementById('addImageForm').style.display = 'block'" class="btn">
                                + Ajouter
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Add Image Form -->
                    <?php if ($isOwner): ?>
                        <div id="addImageForm" style="display: none;" class="mb-3">
                            <form method="post" action="<?= HOME_URL . 'realisation/add_image' ?>" enctype="multipart/form-data">
                                <input type="hidden" name="realisation_uiid" value="<?= $realisation->getUiid() ?>">
                                <div>
                                    <label for="realisationImage">Image :</label>
                                    <input type="file" name="realisationImage" id="realisationImage" accept="image/*" required>
                                </div>
                                <div>
                                    <label for="altText">Texte alternatif (optionnel) :</label>
                                    <input type="text" name="altText" id="altText" placeholder="Description de l'image">
                                </div>
                                <div class="flex-row justify-content-between mt-3">
                                    <button type="button" onclick="document.getElementById('addImageForm').style.display = 'none'" class="btn">
                                        Annuler
                                    </button>
                                    <button type="submit" class="btn">Ajouter l'image</button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Images Display -->
                    <?php if (empty($realisationImages)): ?>
                        <p class="text-muted">Aucune image ajoutée pour cette réalisation.</p>
                    <?php else: ?>
                        <div class="image-carousel">
                            <div class="carousel-container">
                                <?php foreach ($realisationImages as $index => $image): ?>
                                    <div class="carousel-slide <?= $index === 0 ? 'active' : '' ?>">
                                        <img src="<?= HOME_URL . $image['imagePath'] ?>"
                                            alt="<?= $image['altText'] ?: $realisation->getTitle() ?>">

                                        <?php if ($isOwner): ?>
                                            <div class="image-actions">
                                                <a href="<?= HOME_URL . 'realisation/delete_image?realisation_uiid=' . $realisation->getUiid() ?>&imageId=<?= $image['idRealisationImage'] ?>"
                                                    class="btn bg-danger btn-sm"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette image ?');">
                                                    Supprimer
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>

                                <?php if (count($realisationImages) > 1): ?>
                                    <button class="carousel-btn prev" onclick="previousSlide()">‹</button>
                                    <button class="carousel-btn next" onclick="nextSlide()">›</button>

                                    <div class="carousel-indicators">
                                        <?php for ($i = 0; $i < count($realisationImages); $i++): ?>
                                            <span class="indicator <?= $i === 0 ? 'active' : '' ?>" onclick="currentSlide(<?= $i + 1 ?>)"></span>
                                        <?php endfor; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Add delete confirmation modal (same UX as voir_entreprise) -->
<?php if ($isOwner): ?>
    <div id="popup" class="popup d-none">
        <div class="card" style="max-width:500px; position:relative;">
            <h3>Confirmer la suppression</h3>
            <button type="button" onclick="document.getElementById('popup').style.display='none'" style="position:absolute; right:10px; top:10px; background:none; border:none; font-size:18px; cursor:pointer;">×</button>
            <div class="mt mb">
                <p>Êtes-vous sûr de vouloir supprimer la réalisation "<?= $realisation->getTitle() ?>" ?</p>
                <p class="text-danger"><strong>Attention :</strong> Cette action est irréversible.</p>
            </div>
            <div class="flex-row justify-content-between">
                <button type="button" class="btn" onclick="document.getElementById('popup').style.display='none'">Annuler</button>
                <form action="<?= HOME_URL . 'entreprise/mes_realisations/supprimer' ?>" method="post">
                    <input type="hidden" name="entreprise_uiid" value="<?= $entreprise->getUiid() ?>">
                    <input type="hidden" name="realisation_uiid" value="<?= $realisation->getUiid() ?>">
                    <button type="submit" class="btn deconnexion">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script src="<?= HOME_URL . 'assets/javascript/carousel.js' ?>"></script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>