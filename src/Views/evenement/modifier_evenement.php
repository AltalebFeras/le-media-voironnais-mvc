<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<?php
var_dump($_SESSION['form_data']); // Debug line to inspect the $evenement variable
function getFormValue($fieldName, $evenement, $default = '') {
    if (isset($_GET['error']) && isset($_SESSION['form_data'][$fieldName])) {
        return $_SESSION['form_data'][$fieldName];
    }
    return $evenement[$fieldName] ?? $default;
}

function getDateValue($fieldName, $evenement) {
    if (isset($_GET['error']) && isset($_SESSION['form_data'][$fieldName])) {
        return $_SESSION['form_data'][$fieldName];
    }
    return $evenement[$fieldName] ? date('Y-m-d\TH:i', strtotime($evenement[$fieldName])) : '';
}
?>
<main>
    <div class="event-header">
        <div class="flex-row align-items-center">
            <h1>Modifier l'événement</h1>
            <a href="<?= HOME_URL ?>mes_evenements?action=voir&id=<?= $evenement['idEvenement'] ?>">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
            </a>
        </div>
    </div>

    <?php include_once __DIR__ . '/../includes/messages.php'; ?>
    <div class="card max-width-75">
        <form action="<?= HOME_URL ?>evenement/modifier?id=<?= $evenement['idEvenement'] ?>" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="title">Titre de l'événement *</label>
                    <input type="text" id="title" name="title" required
                           value="<?= htmlspecialchars(getFormValue('title', $evenement)) ?>">
                </div>

                <div class="form-group">
                    <label for="idEventCategory">Catégorie *</label>
                    <select id="idEventCategory" name="idEventCategory" required>
                        <option value="">Sélectionnez une catégorie</option>
                        <?php foreach ($categories as $category): ?>
                            <?php $selectedCategory = getFormValue('idEventCategory', $evenement); ?>
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
                <textarea id="description" name="description" rows="4" required><?= htmlspecialchars(getFormValue('description', $evenement)) ?></textarea>
            </div>

            <div class="form-group">
                <label for="shortDescription">Description courte</label>
                <textarea id="shortDescription" name="shortDescription" rows="2"><?= htmlspecialchars(getFormValue('shortDescription', $evenement)) ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="startDate">Date et heure de début *</label>
                    <input type="datetime-local" id="startDate" name="startDate" required
                           value="<?= getDateValue('startDate', $evenement) ?>">
                </div>

                <div class="form-group">
                    <label for="endDate">Date et heure de fin</label>
                    <input type="datetime-local" id="endDate" name="endDate"
                           value="<?= getDateValue('endDate', $evenement) ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="registrationDeadline">Date limite d'inscription</label>
                    <input type="datetime-local" id="registrationDeadline" name="registrationDeadline"
                           value="<?= getDateValue('registrationDeadline', $evenement) ?>">
                </div>

                <div class="form-group">
                    <label for="maxParticipants">Nombre maximum de participants</label>
                    <input type="number" id="maxParticipants" name="maxParticipants" min="1"
                           value="<?= htmlspecialchars(getFormValue('maxParticipants', $evenement)) ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Adresse *</label>
                <input type="text" id="address" name="address" required
                       value="<?= htmlspecialchars(getFormValue('address', $evenement)) ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="codePostal">Code postal *</label>
                    <input type="text" id="codePostal" name="codePostal" maxlength="5" required
                           value="<?= htmlspecialchars(getFormValue('codePostal', $evenement, $ville ? $ville['ville_code_postal'] : '')) ?>">
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
                    <input type="hidden" id="idVille" name="idVille" value="<?= htmlspecialchars(getFormValue('idVille', $evenement)) ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Prix (€)</label>
                    <input type="number" id="price" name="price" min="0" step="0.01"
                           value="<?= htmlspecialchars(getFormValue('price', $evenement)) ?>">
                </div>

                <div class="form-group">
                    <label for="idAssociation">Association organisatrice</label>
                    <select id="idAssociation" name="idAssociation">
                        <option value="">Aucune association</option>
                        <?php foreach ($associations as $association): ?>
                            <?php $selectedAssociation = getFormValue('idAssociation', $evenement); ?>
                            <option value="<?= $association['idAssociation'] ?>"
                                    <?= $selectedAssociation == $association['idAssociation'] ? 'selected' : '' ?>>
                                <?= $association['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="isPublic" value="1" 
                           <?= getFormValue('isPublic', $evenement, false) ? 'checked' : '' ?>>
                    Événement public
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="requiresApproval" value="1"
                           <?= getFormValue('requiresApproval', $evenement, false) ? 'checked' : '' ?>>
                    Inscription avec approbation
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Mettre à jour l'événement</button>
                <a href="<?= HOME_URL . 'mes_evenements?action=voir&id=' . $evenement['idEvenement'] ?>" class="btn btn-secondary">Annuler</a>
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
