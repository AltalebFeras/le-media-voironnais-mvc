<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<main>
    <div class="flex-row justify-content-between">
        <h1>Modifier l'association</h1>
        <a href="<?=HOME_URL . 'mes_associations' ?>" class="">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
        </a>
    </div>
    <div class="flex-row">
        <div class="max-width-50">
            <?php include_once __DIR__ . '/../includes/messages.php'; ?>

            <form action="/association/modifier/<?= $association->getIdAssociation() ?>" method="post" enctype="multipart/form-data">
                <div class="card">
                    <div>
                        <div>
                            <label for="name">Nom de l'association *</label>
                            <input type="text" id="name" name="name"
                                value="<?= htmlspecialchars($association->getName()) ?>" required>
                        </div>

                        <div>
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4"><?= htmlspecialchars($association->getDescription() ?? '') ?></textarea>
                        </div>

                        <div>
                            <label for="address">Adresse</label>
                            <input type="text" id="address" name="address"
                                value="<?= htmlspecialchars($association->getAddress() ?? '') ?>">
                        </div>

                        <div class="flex-row">
                            <div class="max-width-50">
                                <div>
                                    <label for="phone">Téléphone</label>
                                    <input type="tel" id="phone" name="phone"
                                        value="<?= htmlspecialchars($association->getPhone() ?? '') ?>">
                                </div>
                            </div>
                            <div class="max-width-50">
                                <div>
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email"
                                        value="<?= htmlspecialchars($association->getEmail() ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="website">Site web</label>
                            <input type="url" id="website" name="website"
                                placeholder="https://example.com"
                                value="<?= htmlspecialchars($association->getWebsite() ?? '') ?>">
                        </div>

                        <div>
                            <div class="flex-row align-items-center">
                                <input type="checkbox" id="isActive" name="isActive"
                                    <?= $association->getIsActive() ? 'checked' : '' ?>>
                                <label for="isActive">
                                    Association active
                                </label>
                            </div>
                            <small class="text-muted">Une association inactive ne sera pas visible publiquement</small>
                        </div>

                        <hr>

                        <?php if ($association->getLogoPath()): ?>
                            <div>
                                <label>Logo actuel</label>
                                <div>
                                    <img src="<?= $association->getLogoPath() ?>" alt="Logo actuel" style="max-height: 100px;">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div>
                            <label for="logo">Nouveau logo</label>
                            <input type="file" id="logo" name="logo" accept="image/*">
                            <small class="text-muted">Formats acceptés : JPG, PNG, GIF. Max 5MB.</small>
                        </div>

                        <?php if ($association->getBannerPath()): ?>
                            <div>
                                <label>Bannière actuelle</label>
                                <div>
                                    <img src="<?= $association->getBannerPath() ?>" alt="Bannière actuelle" style="max-height: 150px;">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div>
                            <label for="banner">Nouvelle bannière</label>
                            <input type="file" id="banner" name="banner" accept="image/*">
                            <small class="text-muted">Formats acceptés : JPG, PNG, GIF. Max 5MB.</small>
                        </div>
                    </div>

                    <div class="flex-row justify-content-between mt">
                        <a href="/mes-associations" class="btn">Annuler</a>
                        <button type="submit" class="btn">Enregistrer les modifications</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="max-width-33">
            <div class="card">
                <h3>Informations</h3>
                <p>Les champs marqués d'un * sont obligatoires.</p>
                <p>Téléchargez de nouvelles images uniquement si vous souhaitez les remplacer.</p>
            </div>
        </div>
    </div>
</main>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>