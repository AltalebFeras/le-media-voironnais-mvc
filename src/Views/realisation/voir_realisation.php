<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../components/messages.php';
?>

<link rel="stylesheet" href="<?= HOME_URL ?>assets/css/carousel.css">

<main>
    <!-- Main Content -->
    <div class="realisation-main-content" style="padding: 20px;">
        <div class="flex-row align-items-center mb">
            <h1><?= htmlspecialchars($realisation->getTitle()) ?></h1>
            <?php if ($isOwner): ?>
                <div>
                    <a href="<?= HOME_URL ?>realisation/modifier?realisation_uiid=<?= htmlspecialchars($realisation->getUiid()) ?>" 
                       class="btn">Modifier</a>
                    <a href="<?= HOME_URL ?>realisation/supprimer?realisation_uiid=<?= htmlspecialchars($realisation->getUiid()) ?>" 
                       class="btn bg-danger" 
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réalisation ?');">
                        Supprimer
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <p>
                <a href="<?= HOME_URL ?>realisation/mes_realisations?uiid=<?= htmlspecialchars($entreprise->getUiid()) ?>" class="link">
                    ← Retour à mes réalisations
                </a>
            </p>
            
            <h3>Entreprise : <?= htmlspecialchars($entreprise->getName()) ?></h3>
        </div>

        <!-- Realisation Details -->
        <div class="card">
            <div class="flex-row">
                <?php if ($realisation->getIsFeatured()): ?>
                    <span class="badge-success">Mise en avant</span>
                <?php endif; ?>
                <?php if (!$realisation->getIsPublic()): ?>
                    <span class="badge-secondary">Privée</span>
                <?php endif; ?>
            </div>

            <?php if ($realisation->getDateRealized()): ?>
                <p><strong>Date de réalisation :</strong> <?= htmlspecialchars($realisation->getFormattedDateRealized()) ?></p>
            <?php endif; ?>

            <?php if ($realisation->getDescription()): ?>
                <p><strong>Description :</strong></p>
                <p><?= nl2br(htmlspecialchars($realisation->getDescription())) ?></p>
            <?php endif; ?>

            <p><strong>Créé le :</strong> <?= htmlspecialchars($realisation->getFormattedCreatedAt()) ?></p>
            <?php if ($realisation->getUpdatedAt()): ?>
                <p><strong>Modifié le :</strong> <?= htmlspecialchars($realisation->getFormattedUpdatedAt()) ?></p>
            <?php endif; ?>
        </div>

        <!-- Images Gallery -->
        <div class="card">
            <div class="flex-row align-items-center mb">
                <h3>Galerie d'images</h3>
                <?php if ($isOwner): ?>
                    <button onclick="document.getElementById('addImageForm').style.display = 'block'" class="btn">
                        + Ajouter une image
                    </button>
                <?php endif; ?>
            </div>

            <!-- Add Image Form -->
            <?php if ($isOwner): ?>
                <div id="addImageForm" style="display: none;" class="mb">
                    <form method="post" action="<?= HOME_URL ?>realisation/add_image?realisation_uiid=<?= htmlspecialchars($realisation->getUiid()) ?>" enctype="multipart/form-data">
                        <label for="realisationImage">Image :</label>
                        <input type="file" name="realisationImage" id="realisationImage" accept="image/*" required>
                        
                        <label for="altText">Texte alternatif (optionnel) :</label>
                        <input type="text" name="altText" id="altText" placeholder="Description de l'image">
                        
                        <div class="flex-row">
                            <button type="submit" class="btn">Ajouter l'image</button>
                            <button type="button" onclick="document.getElementById('addImageForm').style.display = 'none'" class="btn bg-danger">
                                Annuler
                            </button>
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
                                <img src="<?= HOME_URL . htmlspecialchars($image['imagePath']) ?>" 
                                     alt="<?= htmlspecialchars($image['altText'] ?: $realisation->getTitle()) ?>">
                                
                                <?php if ($isOwner): ?>
                                    <div class="image-actions">
                                        <a href="<?= HOME_URL ?>realisation/delete_image?realisation_uiid=<?= htmlspecialchars($realisation->getUiid()) ?>&imageId=<?= $image['idRealisationImage'] ?>" 
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
</main>

<script src="<?= HOME_URL ?>assets/javascript/carousel.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
                        <label for="realisationImage">Image :</label>
                        <input type="file" name="realisationImage" id="realisationImage" accept="image/*" required>
                        
                        <label for="altText">Texte alternatif (optionnel) :</label>
                        <input type="text" name="altText" id="altText" placeholder="Description de l'image">
                        
                        <div class="flex-row">
                            <button type="submit" class="btn">Ajouter l'image</button>
                            <button type="button" onclick="document.getElementById('addImageForm').style.display = 'none'" class="btn bg-danger">
                                Annuler
                            </button>
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
                                <img src="<?= HOME_URL . htmlspecialchars($image['imagePath']) ?>" 
                                     alt="<?= htmlspecialchars($image['altText'] ?: $realisation->getTitle()) ?>">
                                
                                <?php if ($isOwner): ?>
                                    <div class="image-actions">
                                        <a href="<?= HOME_URL ?>realisation/delete_image?realisation_uiid=<?= htmlspecialchars($realisation->getUiid()) ?>&imageId=<?= $image['idRealisationImage'] ?>" 
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
</main>

<script src="<?= HOME_URL ?>assets/javascript/banner-logo-management.js"></script>
<script src="<?= HOME_URL ?>assets/javascript/carousel.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
