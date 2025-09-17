<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<main>
   
    <div class="flex-row justify-content-between">
        <h1>Ajouter une entreprise</h1>
        <a href="<?=HOME_URL . 'mes_entreprises' ?>" class="">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
        </a>
    </div>
    <div class="flex-row">
        <div class="max-width-50">
            <?php include_once __DIR__ . '/../includes/messages.php'; ?>


            <form action="/entreprise/ajouter" method="post" enctype="multipart/form-data">
                <div class="card">
                    <div>
                        <div>
                            <label for="name">Nom de l'entreprise *</label>
                            <input type="text" id="name" name="name"
                                value="<?= isset($formData['name']) ? htmlspecialchars($formData['name']) : '' ?>" required>
                        </div>

                        <div>
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4"><?= isset($formData['description']) ? htmlspecialchars($formData['description']) : '' ?></textarea>
                        </div>

                        <div>
                            <label for="siret">Numéro SIRET</label>
                            <input type="text" id="siret" name="siret"
                                value="<?= isset($formData['siret']) ? htmlspecialchars($formData['siret']) : '' ?>">
                            <small class="text-muted">Format : 14 chiffres sans espace</small>
                        </div>

                        <div>
                            <label for="address">Adresse</label>
                            <input type="text" id="address" name="address"
                                value="<?= isset($formData['address']) ? htmlspecialchars($formData['address']) : '' ?>">
                        </div>

                        <div class="flex-row">
                            <div class="max-width-50">
                                <div>
                                    <label for="phone">Téléphone</label>
                                    <input type="tel" id="phone" name="phone"
                                        value="<?= isset($formData['phone']) ? htmlspecialchars($formData['phone']) : '' ?>">
                                </div>
                            </div>
                            <div class="max-width-50">
                                <div>
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email"
                                        value="<?= isset($formData['email']) ? htmlspecialchars($formData['email']) : '' ?>">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="website">Site web</label>
                            <input type="url" id="website" name="website"
                                placeholder="https://example.com"
                                value="<?= isset($formData['website']) ? htmlspecialchars($formData['website']) : '' ?>">
                        </div>

                        <div>
                            <label for="status">Statut</label>
                            <select id="status" name="status">
                                <option value="brouillon" <?= (isset($formData['status']) && $formData['status'] === 'brouillon') ? 'selected' : '' ?>>Brouillon</option>
                                <option value="actif" <?= (isset($formData['status']) && $formData['status'] === 'actif') ? 'selected' : '' ?>>Actif</option>
                                <option value="suspendu" <?= (isset($formData['status']) && $formData['status'] === 'suspendu') ? 'selected' : '' ?>>Suspendu</option>
                            </select>
                        </div>

                        <div>
                            <label for="logo">Logo</label>
                            <input type="file" id="logo" name="logo" accept="image/*">
                            <small class="text-muted">Formats acceptés : JPG, PNG, GIF. Max 5MB.</small>
                        </div>

                        <div>
                            <label for="banner">Bannière</label>
                            <input type="file" id="banner" name="banner" accept="image/*">
                            <small class="text-muted">Formats acceptés : JPG, PNG, GIF. Max 5MB.</small>
                        </div>
                    </div>

                    <div class="flex-row justify-content-between mt">
                        <a href="<?= HOME_URL . 'mes_entreprises' ?>" class="btn">Annuler</a>
                        <button type="submit" class="btn">Créer l'entreprise</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="max-width-33">
            <div class="card">
                <h3>Informations</h3>
                <p>Les champs marqués d'un * sont obligatoires.</p>
                <p>Le logo et la bannière sont optionnels mais recommandés pour personnaliser votre entreprise.</p>
                <p>Par défaut, votre entreprise sera enregistrée en mode "Brouillon" et ne sera pas visible publiquement.</p>
                <p>Vous pourrez la rendre visible ultérieurement depuis le formulaire de modification.</p>
            </div>
        </div>
    </div>
</main>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>