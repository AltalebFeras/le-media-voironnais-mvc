<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/associations/modifier_association.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="modifier-association-container">
    <div class="modifier-association-header">
        <div class="flex-row justify-content-between">
            <h1>Modifier l'association</h1>
            <a href="<?= HOME_URL . 'mes_associations?action=voir&uiid=' . $association->getUiid() ?>" class="">
                <span class="material-icons btn" style="color:white;">arrow_back</span>
            </a>
        </div>
    </div>
    <div class="flex-row align-items-start">
        <div class="max-width-50">
            <?php include_once __DIR__ . '/../includes/messages.php'; ?>

            <form action="<?= HOME_URL . 'association/modifier' ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="modifier_association">
                <div class="card">
                    <input type="hidden" name="uiid" value="<?= $association->getUiid() ?>">
                    <div>
                        <div>
                            <label for="name">Nom de l'association *</label>
                            <input type="text" id="name" name="name"
                                value="<?= (isset($_GET['error']) && isset($_SESSION['form_data']['name'])) ? htmlspecialchars($_SESSION['form_data']['name']) : htmlspecialchars($association->getName()) ?>" required>
                        </div>

                        <div>
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4"><?= (isset($_GET['error']) && isset($_SESSION['form_data']['description'])) ? htmlspecialchars($_SESSION['form_data']['description']) : htmlspecialchars($association->getDescription() ?? '') ?></textarea>
                        </div>

                        <div>
                            <label for="address">Adresse</label>
                            <input type="text" id="address" name="address"
                                value="<?= (isset($_GET['error']) && isset($_SESSION['form_data']['address'])) ? htmlspecialchars($_SESSION['form_data']['address']) : htmlspecialchars($association->getAddress() ?? '') ?>">
                        </div>

                        <!-- Postal code and city fields -->
                        <div class="flex-row">
                            <div class="max-width-50">
                                <div>
                                    <label for="codePostal">Code postal</label>
                                    <input type="text" id="codePostal" name="codePostal" maxlength="5"
                                        value="<?= (isset($_GET['error']) && isset($_SESSION['form_data']['codePostal'])) ? htmlspecialchars($_SESSION['form_data']['codePostal']) : ($ville ? htmlspecialchars($ville['ville_code_postal']) : '') ?>">
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
                                        value="<?= (isset($_GET['error']) && isset($_SESSION['form_data']['idVille'])) ? htmlspecialchars($_SESSION['form_data']['idVille']) : htmlspecialchars($association->getIdVille()) ?>">
                                </div>
                            </div>
                        </div>
                        <!-- End of new fields -->

                        <div class="flex-row">
                            <div class="max-width-50">
                                <div>
                                    <label for="phone">Téléphone</label>
                                    <input type="tel" id="phone" name="phone"
                                        value="<?= (isset($_GET['error']) && isset($_SESSION['form_data']['phone'])) ? htmlspecialchars($_SESSION['form_data']['phone']) : htmlspecialchars($association->getPhone() ?? '') ?>">
                                </div>
                            </div>
                            <div class="max-width-50">
                                <div>
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email"
                                        value="<?= (isset($_GET['error']) && isset($_SESSION['form_data']['email'])) ? htmlspecialchars($_SESSION['form_data']['email']) : htmlspecialchars($association->getEmail() ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="website">Site web</label>
                            <input type="url" id="website" name="website"
                                placeholder="https://example.com"
                                value="<?= (isset($_GET['error']) && isset($_SESSION['form_data']['website'])) ? htmlspecialchars($_SESSION['form_data']['website']) : htmlspecialchars($association->getWebsite() ?? '') ?>">
                        </div>
                    </div>

                    <div class="flex-row justify-content-between mt">
                        <a href="<?= HOME_URL . 'mes_associations?action=voir&uiid=' . $association->getUiid() ?>" class="btn">Annuler</a>
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