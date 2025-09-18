<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<main>
    <div class="flex-row justify-content-between">
        <h1>Modifier l'association</h1>
        <a href="<?=HOME_URL . 'mes_associations?action=voir&id=' . $association->getIdAssociation() ?>" class="">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
        </a>
    </div>
    <div class="flex-row">
        <div class="max-width-50">
            <?php include_once __DIR__ . '/../includes/messages.php'; ?>

            <form action="/association/modifier?id=<?= $association->getIdAssociation() ?>" method="post" enctype="multipart/form-data">
                <div class="card">
                    <div>
                        <div>
                            <label for="name">Nom de l'association *</label>
                            <input type="text" id="name" name="name"
                                value="<?= $association->getName() ?>" required>
                        </div>

                        <div>
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4"><?= $association->getDescription() ?? '' ?></textarea>
                        </div>

                        <div>
                            <label for="address">Adresse</label>
                            <input type="text" id="address" name="address"
                                value="<?= $association->getAddress() ?? '' ?>">
                        </div>

                        <!-- Postal code and city fields -->
                        <div class="flex-row">
                            <div class="max-width-50">
                                <div>
                                    <label for="codePostal">Code postal</label>
                                    <input type="text" id="codePostal" name="codePostal" maxlength="5" 
                                           value="<?= $ville ? $ville['ville_code_postal'] : '' ?>">
                                    <small class="text-muted">Saisissez 5 chiffres pour voir les villes</small>
                                </div>
                            </div>
                            <div class="max-width-50">
                                <div>
                                    <label for="ville">Ville</label>
                                    <select id="ville" name="ville">
                                        <?php if ($ville): ?>
                                            <option value="<?= $ville['ville_nom_reel'] ?>" selected><?= $ville['ville_nom_reel'] ?></option>
                                        <?php else: ?>
                                            <option value="">Sélectionnez une ville</option>
                                        <?php endif; ?>
                                    </select>
                                    <input type="hidden" id="idVille" name="idVille" 
                                           value="<?= $association->getIdVille() ?>">
                                </div>
                            </div>
                        </div>

                        <div class="flex-row">
                            <div class="max-width-50">
                                <div>
                                    <label for="phone">Téléphone</label>
                                    <input type="tel" id="phone" name="phone"
                                        value="<?= $association->getPhone() ?? '' ?>">
                                </div>
                            </div>
                            <div class="max-width-50">
                                <div>
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email"
                                        value="<?= $association->getEmail() ?? '' ?>">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="website">Site web</label>
                            <input type="url" id="website" name="website"
                                placeholder="https://example.com"
                                value="<?= $association->getWebsite() ?? '' ?>">
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
                    </div>

                    <div class="flex-row justify-content-between mt">
                        <a href="<?= HOME_URL . 'mes_associations?action=voir&id=' . $association->getIdAssociation() ?>" class="btn">Annuler</a>
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
<script src="<?= HOME_URL . 'assets/javascript/villes.js' ?>"></script>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>