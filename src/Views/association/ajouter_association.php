<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/associations/ajouter_association.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="ajouter-association-container">
    <div class="ajouter-association-header">
        <h1>Ajouter une association</h1>
        <a href="<?= HOME_URL . 'mes_associations' ?>" class="">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
        </a>
    </div>

    <div class="flex-row align-items-start">
        <div class="max-width-50">
            <?php include_once __DIR__ . '/../includes/messages.php'; ?>

            <form action="/association/ajouter" method="post" enctype="multipart/form-data" class="ajouter-association-form">
                <div class="card">
                    <div class="form-section">
                        <h3 class="form-section-title">Informations générales</h3>

                        <div class="form-group">
                            <label for="name">Nom de l'association <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name"
                                value="<?= isset($_SESSION['form_data']['name']) ? $_SESSION['form_data']['name'] : '' ?>"
                                placeholder="Ex: Association Culturelle de Voiron"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4"
                                placeholder="Décrivez l'association, ses objectifs et ses activités..."><?= isset($_SESSION['form_data']['description']) ? $_SESSION['form_data']['description'] : '' ?></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">Localisation</h3>

                        <div class="form-group">
                            <label for="address">Adresse</label>
                            <input type="text" id="address" name="address"
                                value="<?= isset($_SESSION['form_data']['address']) ? $_SESSION['form_data']['address'] : '' ?>"
                                placeholder="123 Rue Exemple">
                        </div>

                        <!-- New postal code and city fields -->
                        <div class="flex-row">
                            <div class="max-width-50">
                                <div class="form-group">
                                    <label for="codePostal">Code postal</label>
                                    <input type="text" id="codePostal" name="codePostal" maxlength="5"
                                        value="<?= isset($_SESSION['form_data']['codePostal']) ? $_SESSION['form_data']['codePostal'] : '' ?>"
                                        placeholder="38000">
                                    <small class="text-muted">Saisissez 5 chiffres pour voir les villes</small>
                                </div>
                            </div>
                            <div class="max-width-50">
                                <div class="form-group">
                                    <label for="ville">Ville</label>
                                    <select id="ville" name="ville" disabled>
                                        <option value="">Sélectionnez une ville</option>
                                    </select>
                                    <input type="hidden" id="idVille" name="idVille" value="">
                                </div>
                            </div>
                        </div>
                        <!-- End of new fields -->
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">Contact</h3>

                        <div class="flex-row">
                            <div class="max-width-50">
                                <div class="form-group">
                                    <label for="phone">Téléphone</label>
                                    <input type="tel" id="phone" name="phone"
                                        value="<?= isset($_SESSION['form_data']['phone']) ? $_SESSION['form_data']['phone'] : '' ?>"
                                        placeholder="04 76 XX XX XX">
                                </div>
                            </div>
                            <div class="max-width-50">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email"
                                        value="<?= isset($_SESSION['form_data']['email']) ? $_SESSION['form_data']['email'] : '' ?>"
                                        placeholder="contact@association.fr">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="website">Site web</label>
                            <input type="url" id="website" name="website"
                                placeholder="https://www.exemple.com"
                                value="<?= isset($_SESSION['form_data']['website']) ? $_SESSION['form_data']['website'] : '' ?>">
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="<?= HOME_URL . 'mes_associations' ?>" class="btn btn-light linkNotDecorated">
                            <span class="material-icons" style="font-size: 18px; vertical-align: middle;">close</span>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <span class="material-icons" style="font-size: 18px; vertical-align: middle;">group_add</span>
                            Créer l'association
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="max-width-33">
            <div class="card info-sidebar">
                <h3>
                    <span class="material-icons" style="vertical-align: middle;">info</span>
                    Informations
                </h3>
                <p><strong>Champs obligatoires :</strong> Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires.</p>
                <p><strong>Personnalisation :</strong> Le logo et la bannière sont optionnels mais recommandés pour personnaliser votre association.</p>
                <p><strong>Rôle :</strong> En tant que créateur, vous serez automatiquement administrateur de cette association.</p>
                <p><strong>Membres :</strong> Vous pourrez inviter d'autres membres après la création de l'association.</p>
            </div>
        </div>
    </div>
</main>

<script src="<?= HOME_URL . 'assets/javascript/villes.js' ?>"></script>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>