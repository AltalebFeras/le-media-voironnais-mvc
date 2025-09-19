<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <div class="flex-row justify-content-between">
        <h1>Modifier l'entreprise</h1>
        <a href="<?= HOME_URL . 'mes_entreprises?action=voir&uiid=' . $entreprise->getIdEntreprise() ?>" class="">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
        </a>
    </div>
    <div class="flex-row">
        <div class="max-width-50">
            <?php include_once __DIR__ . '/../includes/messages.php'; ?>

            <form action="<?= HOME_URL . 'entreprise/modifier?uiid=' . $entreprise->getIdEntreprise() ?>" method="post" enctype="multipart/form-data">
                <div class="card">
                    <div>
                        <div>
                            <label for="name">Nom de l'entreprise *</label>
                            <input type="text" id="name" name="name"
                                value="<?= (isset($_GET['error']) && isset($_SESSION['form_data']['name'])) ? htmlspecialchars($_SESSION['form_data']['name']) : htmlspecialchars($entreprise->getName()) ?>" required>
                        </div>

                        <div>
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4"><?= (isset($_GET['error']) && isset($_SESSION['form_data']['description'])) ? htmlspecialchars($_SESSION['form_data']['description']) : htmlspecialchars($entreprise->getDescription() ?? '') ?></textarea>
                        </div>

                        <div>
                            <label for="siret">Numéro SIRET</label>
                            <input type="text" id="siret" name="siret"
                                value="<?= (isset($_GET['error']) && isset($_SESSION['form_data']['siret'])) ? htmlspecialchars($_SESSION['form_data']['siret']) : htmlspecialchars($entreprise->getSiret() ?? '') ?>">
                            <small class="text-muted">Format : 14 chiffres sans espace</small>
                        </div>

                        <div>
                            <label for="address">Adresse</label>
                            <input type="text" id="address" name="address"
                                value="<?= (isset($_GET['error']) && isset($_SESSION['form_data']['address'])) ? htmlspecialchars($_SESSION['form_data']['address']) : htmlspecialchars($entreprise->getAddress() ?? '') ?>">
                        </div>
                        <!-- New postal code and city fields -->
                        <div class="flex-row">
                            <div class="max-width-50">
                                <div>
                                    <label for="codePostal">Code postal</label>
                                    <input type="text" id="codePostal" name="codePostal" maxlength="5"
                                        value="<?= (isset($_GET['error']) && isset($_SESSION['form_data']['codePostal'])) ? htmlspecialchars($_SESSION['form_data']['codePostal']) : '' ?>">
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
                                        value="<?= (isset($_GET['error']) && isset($_SESSION['form_data']['idVille'])) ? htmlspecialchars($_SESSION['form_data']['idVille']) : '' ?>">
                                </div>
                            </div>
                        </div>
                        <!-- End of new fields -->
                        <div class="flex-row">
                            <div class="max-width-50">
                                <div>
                                    <label for="phone">Téléphone</label>
                                    <input type="tel" id="phone" name="phone"
                                        value="<?= (isset($_GET['error']) && isset($_SESSION['form_data']['phone'])) ? htmlspecialchars($_SESSION['form_data']['phone']) : htmlspecialchars($entreprise->getPhone() ?? '') ?>">
                                </div>
                            </div>
                            <div class="max-width-50">
                                <div>
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email"
                                        value="<?= (isset($_GET['error']) && isset($_SESSION['form_data']['email'])) ? htmlspecialchars($_SESSION['form_data']['email']) : htmlspecialchars($entreprise->getEmail() ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="website">Site web</label>
                            <input type="url" id="website" name="website"
                                placeholder="https://example.com"
                                value="<?= (isset($_GET['error']) && isset($_SESSION['form_data']['website'])) ? htmlspecialchars($_SESSION['form_data']['website']) : htmlspecialchars($entreprise->getWebsite() ?? '') ?>">
                        </div>

                        <div>
                            <label for="status">Statut</label>
                            <select id="status" name="status">
                                <?php $selectedStatus = (isset($_GET['error']) && isset($_SESSION['form_data']['status'])) ? $_SESSION['form_data']['status'] : $entreprise->getStatus(); ?>
                                <option value="brouillon" <?= $selectedStatus === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                                <option value="actif" <?= $selectedStatus === 'actif' ? 'selected' : '' ?>>Actif</option>
                                <option value="suspendu" <?= $selectedStatus === 'suspendu' ? 'selected' : '' ?>>Suspendu</option>
                            </select>
                        </div>

                        <div>
                            <div class="flex-row align-items-center">
                                <input type="checkbox" id="isActive" name="isActive"
                                    <?= ((isset($_GET['error']) && isset($_SESSION['form_data']['isActive'])) ? $_SESSION['form_data']['isActive'] : $entreprise->getIsActive()) ? 'checked' : '' ?>>
                                <label for="isActive">
                                    Entreprise publiée
                                </label>
                            </div>
                            <small class="text-muted">Une entreprise non publiée ne sera pas visible publiquement</small>
                        </div>

                        <hr>
                    </div>

                    <div class="flex-row justify-content-between mt">
                        <a href="<?= HOME_URL . 'mes_entreprises' ?>" class="btn">Annuler</a>
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

<script src="<?= HOME_URL . 'assets/javascript/villes.js' ?>"></script>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>