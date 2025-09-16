<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<main>
    <div class="flex-row justify-content-between">
        <h1>Modifier l'entreprise</h1>
        <a href="<?=HOME_URL . 'mes_entreprises' ?>" class="">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
        </a>
    </div>
    <div class="flex-row">
        <div class="max-width-50">
            <?php include_once __DIR__ . '/../includes/messages.php'; ?>

            <form action="/entreprise/modifier/<?= $entreprise->getIdEntreprise() ?>" method="post" enctype="multipart/form-data">
                <div class="card">
                    <div>
                        <div>
                            <label for="name">Nom de l'entreprise *</label>
                            <input type="text" id="name" name="name"
                                value="<?= htmlspecialchars($entreprise->getName()) ?>" required>
                        </div>

                        <div>
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4"><?= htmlspecialchars($entreprise->getDescription() ?? '') ?></textarea>
                        </div>

                        <div>
                            <label for="siret">Numéro SIRET</label>
                            <input type="text" id="siret" name="siret"
                                value="<?= htmlspecialchars($entreprise->getSiret() ?? '') ?>">
                            <small class="text-muted">Format : 14 chiffres sans espace</small>
                        </div>

                        <div>
                            <label for="address">Adresse</label>
                            <input type="text" id="address" name="address"
                                value="<?= htmlspecialchars($entreprise->getAddress() ?? '') ?>">
                        </div>

                        <div class="flex-row">
                            <div class="max-width-50">
                                <div>
                                    <label for="phone">Téléphone</label>
                                    <input type="tel" id="phone" name="phone"
                                        value="<?= htmlspecialchars($entreprise->getPhone() ?? '') ?>">
                                </div>
                            </div>
                            <div class="max-width-50">
                                <div>
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email"
                                        value="<?= htmlspecialchars($entreprise->getEmail() ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="website">Site web</label>
                            <input type="url" id="website" name="website"
                                placeholder="https://example.com"
                                value="<?= htmlspecialchars($entreprise->getWebsite() ?? '') ?>">
                        </div>

                        <div>
                            <label for="status">Statut</label>
                            <select id="status" name="status">
                                <option value="brouillon" <?= $entreprise->getStatus() === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                                <option value="actif" <?= $entreprise->getStatus() === 'actif' ? 'selected' : '' ?>>Actif</option>
                                <option value="suspendu" <?= $entreprise->getStatus() === 'suspendu' ? 'selected' : '' ?>>Suspendu</option>
                            </select>
                        </div>

                        <div>
                            <div class="flex-row align-items-center">
                                <input type="checkbox" id="isActive" name="isActive"
                                    <?= $entreprise->getIsActive() ? 'checked' : '' ?>>
                                <label for="isActive">
                                    Entreprise publiée
                                </label>
                            </div>
                            <small class="text-muted">Une entreprise non publiée ne sera pas visible publiquement</small>
                        </div>

                        <hr>

                        <?php if ($entreprise->getLogoPath()): ?>
                            <div>
                                <label>Logo actuel</label>
                                <div>
                                    <img src="<?= $entreprise->getLogoPath() ?>" alt="Logo actuel" style="max-height: 100px;">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div>
                            <label for="logo">Nouveau logo</label>
                            <input type="file" id="logo" name="logo" accept="image/*">
                            <small class="text-muted">Formats acceptés : JPG, PNG, GIF. Max 5MB.</small>
                        </div>

                        <?php if ($entreprise->getBannerPath()): ?>
                            <div>
                                <label>Bannière actuelle</label>
                                <div>
                                    <img src="<?= $entreprise->getBannerPath() ?>" alt="Bannière actuelle" style="max-height: 150px;">
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
                        <a href="/mes-entreprises" class="btn">Annuler</a>
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
                <p>Pour rendre votre entreprise visible publiquement, cochez la case "Entreprise publiée" et assurez-vous que le statut est défini sur "Actif".</p>
            </div>
        </div>
    </div>
</main>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>