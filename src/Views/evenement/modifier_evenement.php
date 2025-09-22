<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <div class="event-header">
        <div class="flex-row align-items-center">
            <h1>Modifier l'événement</h1>
            <a href="<?= HOME_URL ?>mes_evenements?action=voir&uiid=<?= $evenement['uiid'] ?>">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
            </a>
        </div>
    </div>

    <?php include_once __DIR__ . '/../includes/messages.php'; ?>
    <div class="card max-width-75">
        <form action="<?= HOME_URL ?>evenement/modifier?uiid=<?= $evenement['uiid'] ?>" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="title">Titre de l'événement *</label>
                    <input type="text" id="title" name="title" required
                           value="<?= htmlspecialchars((isset($_GET['error']) && isset($_SESSION['form_data']['title'])) ? $_SESSION['form_data']['title'] : ($evenement['title'] ?? '')) ?>">
                </div>

                <div class="form-group">
                    <label for="idEventCategory">Catégorie *</label>
                    <select id="idEventCategory" name="idEventCategory" required>
                        <option value="">Sélectionnez une catégorie</option>
                        <?php foreach ($categories as $category): ?>
                            <?php $selectedCategory = (isset($_GET['error']) && isset($_SESSION['form_data']['idEventCategory'])) ? $_SESSION['form_data']['idEventCategory'] : ($evenement['idEventCategory'] ?? ''); ?>
                            <option value="<?= $category['idEventCategory'] ?>"
                                    <?= $selectedCategory == $category['idEventCategory'] ? 'selected' : '' ?>>
                                <?= $category['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" rows="4" required><?= htmlspecialchars((isset($_GET['error']) && isset($_SESSION['form_data']['description'])) ? $_SESSION['form_data']['description'] : ($evenement['description'] ?? '')) ?></textarea>
            </div>

            <div class="form-group">
                <label for="shortDescription">Description courte</label>
                <textarea id="shortDescription" name="shortDescription" rows="2"><?= htmlspecialchars((isset($_GET['error']) && isset($_SESSION['form_data']['shortDescription'])) ? $_SESSION['form_data']['shortDescription'] : ($evenement['shortDescription'] ?? '')) ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="startDate">Date et heure de début *</label>
                    <input type="datetime-local" id="startDate" name="startDate" required
                           value="<?= (isset($_GET['error']) && isset($_SESSION['form_data']['startDate'])) ? $_SESSION['form_data']['startDate'] : ($evenement['startDate'] ? date('Y-m-d\TH:i', strtotime($evenement['startDate'])) : '') ?>">
                </div>

                <div class="form-group">
                    <label for="endDate">Date et heure de fin</label>
                    <input type="datetime-local" id="endDate" name="endDate"
                           value="<?= (isset($_GET['error']) && isset($_SESSION['form_data']['endDate'])) ? $_SESSION['form_data']['endDate'] : ($evenement['endDate'] ? date('Y-m-d\TH:i', strtotime($evenement['endDate'])) : '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="registrationDeadline">Date limite d'inscription</label>
                    <input type="datetime-local" id="registrationDeadline" name="registrationDeadline"
                           value="<?= (isset($_GET['error']) && isset($_SESSION['form_data']['registrationDeadline'])) ? $_SESSION['form_data']['registrationDeadline'] : ($evenement['registrationDeadline'] ? date('Y-m-d\TH:i', strtotime($evenement['registrationDeadline'])) : '') ?>">
                </div>

                <div class="form-group">
                    <label for="maxParticipants">Nombre maximum de participants</label>
                    <input type="number" id="maxParticipants" name="maxParticipants" min="1"
                           value="<?= htmlspecialchars((isset($_GET['error']) && isset($_SESSION['form_data']['maxParticipants'])) ? $_SESSION['form_data']['maxParticipants'] : ($evenement['maxParticipants'] ?? '')) ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Adresse *</label>
                <input type="text" id="address" name="address" required
                       value="<?= htmlspecialchars((isset($_GET['error']) && isset($_SESSION['form_data']['address'])) ? $_SESSION['form_data']['address'] : ($evenement['address'] ?? '')) ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="codePostal">Code postal *</label>
                    <input type="text" id="codePostal" name="codePostal" maxlength="5" required
                           value="<?= htmlspecialchars((isset($_GET['error']) && isset($_SESSION['form_data']['codePostal'])) ? $_SESSION['form_data']['codePostal'] : ($ville ? $ville['ville_code_postal'] : '')) ?>">
                </div>

                <div class="form-group">
                    <label for="ville">Ville *</label>
                    <select id="ville" name="ville" required>
                        <option value="">Sélectionnez une ville</option>
                        <?php if ($ville): ?>
                            <option value="<?= $ville['idVille'] ?>" selected>
                                <?= $ville['ville_nom_reel'] ?>
                            </option>
                        <?php endif; ?>
                    </select>
                    <input type="hidden" id="idVille" name="idVille" value="<?= htmlspecialchars((isset($_GET['error']) && isset($_SESSION['form_data']['idVille'])) ? $_SESSION['form_data']['idVille'] : ($evenement['idVille'] ?? '')) ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Prix (€)</label>
                    <input type="number" id="price" name="price" min="0" step="0.01"
                           value="<?= htmlspecialchars((isset($_GET['error']) && isset($_SESSION['form_data']['price'])) ? $_SESSION['form_data']['price'] : ($evenement['price'] ?? '')) ?>">
                </div>

                <div class="form-group">
                    <label for="idAssociation">Association organisatrice</label>
                    <select id="idAssociation" name="idAssociation">
                        <option value="">Aucune association</option>
                        <?php foreach ($associations as $association): ?>
                            <?php $selectedAssociation = (isset($_GET['error']) && isset($_SESSION['form_data']['idAssociation'])) ? $_SESSION['form_data']['idAssociation'] : ($evenement['idAssociation'] ?? ''); ?>
                            <option value="<?= $association['idAssociation'] ?>"
                                    <?= $selectedAssociation == $association['idAssociation'] ? 'selected' : '' ?>>
                                <?= $association['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
              <div class="form-group">
                    <label for="idEntreprise">Entreprise organisatrice</label>
                    <select id="idEntreprise" name="idEntreprise">
                        <option value="">Aucune entreprise choisie</option>
                        <?php foreach ($entreprises as $entreprise): ?>
                            <option value="<?= $entreprise['idEntreprise'] ?>"
                                <?= isset($_SESSION['form_data']['idEntreprise']) && $_SESSION['form_data']['idEntreprise'] == $entreprise['idEntreprise'] ? 'selected' : '' ?>>
                                <?= $entreprise['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="isPublic" value="1" 
                           <?= ((isset($_GET['error']) && isset($_SESSION['form_data']['isPublic'])) ? $_SESSION['form_data']['isPublic'] : ($evenement['isPublic'] ?? false)) ? 'checked' : '' ?>>
                    Événement public
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="requiresApproval" value="1"
                           <?= ((isset($_GET['error']) && isset($_SESSION['form_data']['requiresApproval'])) ? $_SESSION['form_data']['requiresApproval'] : ($evenement['requiresApproval'] ?? false)) ? 'checked' : '' ?>>
                    Inscription avec approbation
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Mettre à jour l'événement</button>
                <a href="<?= HOME_URL . 'mes_evenements?action=voir&uiid=' . $evenement['uiid'] ?>" class="btn btn-secondary">Annuler</a>
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

.current-image, .current-banner {
    margin-top: 0.5rem;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    display: inline-block;
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
    }
}
</style>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>