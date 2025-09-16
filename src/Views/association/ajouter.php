<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<main>
    <div class="flex-row justify-content-between">
        <h1>Ajouter une association</h1>
        <a href="<?=HOME_URL . 'mes_associations' ?>" class="">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
        </a>
    </div>
    <div class="flex-row">
        <div class="max-width-50">
            <?php include_once __DIR__ . '/../includes/messages.php'; ?>
            <form action="/association/ajouter" method="post" enctype="multipart/form-data">
                <div class="card">
                    <div>
                        <div>
                            <label for="name">Nom de l'association *</label>
                            <input type="text" id="name" name="name"
                                value="<?= isset($_SESSION['form_data']['name']) ? htmlspecialchars($_SESSION['form_data']['name']) : '' ?>"  >
                        </div>

                        <div>
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4"><?= isset($_SESSION['form_data']['description']) ? htmlspecialchars($_SESSION['form_data']['description']) : '' ?></textarea>
                        </div>

                        <div>
                            <label for="address">Adresse</label>
                            <input type="text" id="address" name="address"
                                value="<?= isset($_SESSION['form_data']['address']) ? htmlspecialchars($_SESSION['form_data']['address']) : '' ?>">
                        </div>

                        <!-- New postal code and city fields -->
                        <div class="flex-row">
                            <div class="max-width-50">
                                <div>
                                    <label for="codePostal">Code postal</label>
                                    <input type="text" id="codePostal" name="codePostal" maxlength="5" 
                                           value="<?= isset($_SESSION['form_data']['codePostal']) ? htmlspecialchars($_SESSION['form_data']['codePostal']) : '' ?>">
                                    <small class="text-muted">Saisissez 5 chiffres pour voir les villes</small>
                                </div>
                            </div>
                            <div class="max-width-50">
                                <div>
                                    <label for="ville">Ville</label>
                                    <select id="ville" name="ville" disabled>
                                        <option value="">Sélectionnez une ville</option>
                                    </select>
                                    <input type="hidden" id="idVille" name="idVille" 
                                           value="">
                                </div>
                            </div>
                        </div>
                        <!-- End of new fields -->

                        <div class="flex-row">
                            <div class="max-width-50">
                                <div>
                                    <label for="phone">Téléphone</label>
                                    <input type="tel" id="phone" name="phone"
                                        value="<?= isset($_SESSION['form_data']['phone']) ? htmlspecialchars($_SESSION['form_data']['phone']) : '' ?>">
                                </div>
                            </div>
                            <div class="max-width-50">
                                <div>
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email"
                                        value="<?= isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : '' ?>">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="website">Site web</label>
                            <input type="url" id="website" name="website"
                                placeholder="https://example.com"
                                value="<?= isset($_SESSION['form_data']['website']) ? htmlspecialchars($_SESSION['form_data']['website']) : '' ?>">
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
                        <a href="/mes-associations" class="btn">Annuler</a>
                        <button type="submit" class="btn">Créer l'association</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="max-width-33">
            <div class="card">
                <h3>Informations</h3>
                <p>Les champs marqués d'un * sont obligatoires.</p>
                <p>Le logo et la bannière sont optionnels mais recommandés pour personnaliser votre association.</p>
                <p>En tant que créateur, vous serez automatiquement administrateur de cette association.</p>
            </div>
        </div>
    </div>
</main>
  <script src="<?= HOME_URL . 'assets/javascript/villes.js' ?>"></script>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>