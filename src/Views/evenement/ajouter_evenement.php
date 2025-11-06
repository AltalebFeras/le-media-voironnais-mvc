<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <div class="flex-row align-items-center mb">
        <h1>Créer un événement</h1>
        <a href="<?= HOME_URL . 'mes_evenements' ?>">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
        </a>
    </div>
    <!-- messages -->
    <?php include_once __DIR__ . '/../includes/messages.php'; ?>
    <div class="card max-width-75">
        <form action="<?= HOME_URL ?>evenement/ajouter" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="title">Titre de l'événement *</label>
                    <input type="text" id="title" name="title" required
                        value="<?= isset($_SESSION['form_data']['title']) ? $_SESSION['form_data']['title'] : '' ?>">
                </div>

                <div class="form-group">
                    <label for="idEventCategory">Catégorie *</label>
                    <select id="idEventCategory" name="idEventCategory" required>
                        <option value="">Sélectionnez une catégorie</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['idEventCategory'] ?>"
                                <?= isset($_SESSION['form_data']['idEventCategory']) && $_SESSION['form_data']['idEventCategory'] == $category['idEventCategory'] ? 'selected' : '' ?>>
                                <?= $category['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" rows="4" required><?= isset($_SESSION['form_data']['description']) ? $_SESSION['form_data']['description'] : '' ?></textarea>
            </div>

            <div class="form-group">
                <label for="shortDescription">Description courte</label>
                <textarea id="shortDescription" name="shortDescription" rows="2"><?= isset($_SESSION['form_data']['shortDescription']) ? $_SESSION['form_data']['shortDescription'] : '' ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="startDate">Date et heure de début *</label>
                    <input type="datetime-local" id="startDate" name="startDate" required
                        value="<?= isset($_SESSION['form_data']['startDate']) ? $_SESSION['form_data']['startDate'] : '' ?>">
                </div>

                <div class="form-group">
                    <label for="endDate">Date et heure de fin</label>
                    <input type="datetime-local" id="endDate" name="endDate"
                        value="<?= isset($_SESSION['form_data']['endDate']) ? $_SESSION['form_data']['endDate'] : '' ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="registrationDeadline">Date limite d'inscription</label>
                    <input type="datetime-local" id="registrationDeadline" name="registrationDeadline"
                        value="<?= isset($_SESSION['form_data']['registrationDeadline']) ? $_SESSION['form_data']['registrationDeadline'] : '' ?>">
                </div>

                <div class="form-group">
                    <label for="maxParticipants">Nombre maximum de participants</label>
                    <input type="number" id="maxParticipants" name="maxParticipants" min="1"
                        value="<?= isset($_SESSION['form_data']['maxParticipants']) ? $_SESSION['form_data']['maxParticipants'] : '' ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Adresse *</label>
                <input type="text" id="address" name="address" required
                    value="<?= isset($_SESSION['form_data']['address']) ? $_SESSION['form_data']['address'] : '' ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="codePostal">Code postal *</label>
                    <input type="text" id="codePostal" name="codePostal" maxlength="5" required
                        value="<?= isset($_SESSION['form_data']['codePostal']) ? $_SESSION['form_data']['codePostal'] : '' ?>">
                </div>

                <div class="form-group">
                    <label for="ville">Ville *</label>
                    <select id="ville" name="ville" disabled required>
                        <option value="">Sélectionnez une ville</option>
                    </select>
                    <input type="hidden" id="idVille" name="idVille"
                        value="<?= isset($_SESSION['form_data']['idVille']) ? $_SESSION['form_data']['idVille'] : '' ?>">
                </div>
                <div class="form-group">
                    <label for="price">Prix (€)</label>
                    <input type="number" id="price" name="price" min="0" step="0.01"
                        value="<?= isset($_SESSION['form_data']['price']) ? $_SESSION['form_data']['price'] : '' ?>">
                </div>
            </div>

            <div class="form-row">

                <div class="form-group">
                    <label for="association_uiid">Association organisatrice</label>
                    <select id="association_uiid" name="association_uiid">
                        <option value="">Aucune association</option>
                        <?php foreach ($associations as $association): ?>
                            <option value="<?= $association['association_uiid'] ?>"
                                <?= isset($_SESSION['form_data']['association_uiid']) && $_SESSION['form_data']['association_uiid'] == $association['association_uiid'] ? 'selected' : '' ?>>
                                <?= $association['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="entreprise_uiid">Entreprise organisatrice</label>
                    <select id="entreprise_uiid" name="entreprise_uiid">
                        <option value="">Aucune entreprise</option>
                        <?php foreach ($entreprises as $entreprise): ?>
                            <option value="<?= $entreprise['entreprise_uiid'] ?>"
                                <?= isset($_SESSION['form_data']['entreprise_uiid']) && $_SESSION['form_data']['entreprise_uiid'] == $entreprise['entreprise_uiid'] ? 'selected' : '' ?>>
                                <?= $entreprise['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

            <div class="form-group d-flex">
                <input id="isPublic" class="form-check-input flex-basis-content" type="checkbox" name="isPublic" value="1"
                <?= isset($_SESSION['form_data']['isPublic']) && $_SESSION['form_data']['isPublic'] ? 'checked' : '' ?>>
                <label for="isPublic">
                    Événement public
                </label>
            </div>

            <div class="form-group d-flex">
                <input id="requiresApproval" class="form-check-input flex-basis-content" type="checkbox" name="requiresApproval" value="1"
                        <?= isset($_SESSION['form_data']['requiresApproval']) && $_SESSION['form_data']['requiresApproval'] ? 'checked' : '' ?>>
                <label for="requiresApproval">
                    Inscription avec approbation
                </label>
            </div>

            <div class="form-actions">
                <a href="<?= HOME_URL ?>mes_evenements" class="btn btn-secondary linkNotDecorated">Annuler</a>
                <button type="submit" class="btn">Créer l'événement</button>
            </div>
        </form>
    </div>
</main>

<script src="<?= HOME_URL ?>assets/javascript/villes.js"></script>

<style>
    .form-row {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .form-group {
        flex: 1;
        margin-bottom: 1rem;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
        }
    }
</style>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>