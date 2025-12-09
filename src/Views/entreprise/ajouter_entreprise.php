<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/entreprises/ajouter_entreprise.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="ajouter-entreprise-container ">
    <div class="ajouter-entreprise-header">
        <h1>Ajouter une entreprise</h1>
        <a href="<?= HOME_URL . 'mes_entreprises' ?>" class="">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
        </a>
    </div>

    <div class="flex-row align-items-start">
        <div class="max-width-50">
            <?php include_once __DIR__ . '/../includes/messages.php'; ?>

            <form action="/entreprise/ajouter" method="post" enctype="multipart/form-data" class="ajouter-entreprise-form">
                <div class="card">
                    <div class="form-section">
                        <h3 class="form-section-title">Informations générales</h3>

                        <div class="form-group">
                            <label for="name">Nom de l'entreprise <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name"
                                value="<?= isset($_SESSION['form_data']['name']) ? htmlspecialchars($_SESSION['form_data']['name']) : '' ?>"
                                placeholder="Ex: Ma Super Entreprise"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4"
                                placeholder="Décrivez votre entreprise, ses activités et ses valeurs..."><?= isset($_SESSION['form_data']['description']) ? htmlspecialchars($_SESSION['form_data']['description']) : '' ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="siret">Numéro SIRET</label>
                            <input type="text" id="siret" name="siret"
                                value="<?= isset($_SESSION['form_data']['siret']) ? htmlspecialchars($_SESSION['form_data']['siret']) : '' ?>"
                                placeholder="12345678901234">
                            <small class="text-muted">
                                <span class="material-icons" style="font-size: 14px; vertical-align: middle;">info</span>
                                Format : 14 chiffres sans espace
                            </small>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">Localisation</h3>

                        <div class="form-group">
                            <label for="address">Adresse</label>
                            <input type="text" id="address" name="address"
                                value="<?= isset($_SESSION['form_data']['address']) ? htmlspecialchars($_SESSION['form_data']['address']) : '' ?>"
                                placeholder="123 Rue Exemple">
                        </div>

                        <div class="flex-row">
                            <div class="max-width-50">
                                <div class="form-group">
                                    <label for="codePostal">Code postal</label>
                                    <input type="text" id="codePostal" name="codePostal" maxlength="5"
                                        value="<?= isset($_SESSION['form_data']['codePostal']) ? htmlspecialchars($_SESSION['form_data']['codePostal']) : '' ?>"
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
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">Contact</h3>

                        <div class="flex-row">
                            <div class="max-width-50">
                                <div class="form-group">
                                    <label for="phone">Téléphone</label>
                                    <input type="tel" id="phone" name="phone"
                                        value="<?= isset($_SESSION['form_data']['phone']) ? htmlspecialchars($_SESSION['form_data']['phone']) : '' ?>"
                                        placeholder="04 76 XX XX XX">
                                </div>
                            </div>
                            <div class="max-width-50">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email"
                                        value="<?= isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : '' ?>"
                                        placeholder="contact@entreprise.fr">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="website">Site web</label>
                            <input type="url" id="website" name="website"
                                placeholder="https://www.exemple.com"
                                value="<?= isset($_SESSION['form_data']['website']) ? htmlspecialchars($_SESSION['form_data']['website']) : '' ?>">
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="<?= HOME_URL . 'mes_entreprises' ?>" class="btn btn-light linkNotDecorated">
                            <span class="material-icons" style="font-size: 18px; vertical-align: middle;">close</span>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <span class="material-icons" style="font-size: 18px; vertical-align: middle;">add_business</span>
                            Créer l'entreprise
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
                <p><strong>Personnalisation :</strong> Le logo et la bannière sont optionnels mais fortement recommandés pour donner une identité visuelle à votre entreprise.</p>
                <p><strong>Visibilité :</strong> Par défaut, votre entreprise sera en mode "Brouillon" et ne sera pas visible publiquement.</p>
                <p><strong>Activation :</strong> Pour rendre votre entreprise visible, vous devrez la faire activer en transmettant un document officiel (Kbis, etc.).</p>
            </div>
        </div>
    </div>
</main>

<script src="<?= HOME_URL . 'assets/javascript/villes.js' ?>"></script>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>