<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<?php include_once __DIR__ . '/../includes/messages.php'; ?>

<main>
    <h1>Modifier l'événement</h1>

    <div class="card max-width-75">
        <form action="<?= HOME_URL ?>evenement/modifier?id=<?= $evenement->getIdEvenement() ?>" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="title">Titre de l'événement *</label>
                    <input type="text" id="title" name="title" required 
                           value="<?= htmlspecialchars($evenement->getTitle()) ?>">
                </div>

                <div class="form-group">
                    <label for="idEventCategory">Catégorie *</label>
                    <select id="idEventCategory" name="idEventCategory" required>
                        <option value="">Sélectionnez une catégorie</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['idEventCategory'] ?>"
                                    <?= $evenement->getIdEventCategory() == $category['idEventCategory'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($evenement->getDescription()) ?></textarea>
            </div>

            <div class="form-group">
                <label for="shortDescription">Description courte</label>
                <textarea id="shortDescription" name="shortDescription" rows="2"><?= htmlspecialchars($evenement->getShortDescription() ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="eventDate">Date et heure de début *</label>
                    <input type="datetime-local" id="eventDate" name="eventDate" required
                           value="<?= date('Y-m-d\TH:i', strtotime($evenement->getEventDate())) ?>">
                </div>

                <div class="form-group">
                    <label for="endDate">Date et heure de fin</label>
                    <input type="datetime-local" id="endDate" name="endDate"
                           value="<?= $evenement->getEndDate() ? date('Y-m-d\TH:i', strtotime($evenement->getEndDate())) : '' ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="registrationDeadline">Date limite d'inscription</label>
                    <input type="datetime-local" id="registrationDeadline" name="registrationDeadline"
                           value="<?= $evenement->getRegistrationDeadline() ? date('Y-m-d\TH:i', strtotime($evenement->getRegistrationDeadline())) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="maxParticipants">Nombre maximum de participants</label>
                    <input type="number" id="maxParticipants" name="maxParticipants" min="1"
                           value="<?= $evenement->getMaxParticipants() ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Adresse *</label>
                <input type="text" id="address" name="address" required
                       value="<?= htmlspecialchars($evenement->getAddress()) ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="codePostal">Code postal *</label>
                    <input type="text" id="codePostal" name="codePostal" maxlength="5" required
                           value="<?= $ville ? $ville['ville_code_postal'] : '' ?>">
                </div>

                <div class="form-group">
                    <label for="ville">Ville *</label>
                    <select id="ville" name="ville" required>
                        <option value="">Sélectionnez une ville</option>
                        <?php if ($ville): ?>
                            <option value="<?= $ville['idVille'] ?>" selected>
                                <?= htmlspecialchars($ville['ville_nom_reel']) ?>
                            </option>
                        <?php endif; ?>
                    </select>
                    <input type="hidden" id="idVille" name="idVille" value="<?= $evenement->getIdVille() ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Prix (€)</label>
                    <input type="number" id="price" name="price" min="0" step="0.01"
                           value="<?= $evenement->getPrice() ?>">
                </div>

                <div class="form-group">
                    <label for="idAssociation">Association organisatrice</label>
                    <select id="idAssociation" name="idAssociation">
                        <option value="">Aucune association</option>
                        <?php foreach ($associations as $association): ?>
                            <option value="<?= $association['idAssociation'] ?>"
                                    <?= $evenement->getIdAssociation() == $association['idAssociation'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($association['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <?php if ($evenement->getImagePath()): ?>
                <div class="form-group">
                    <label>Image actuelle</label>
                    <div class="current-image">
                        <img src="<?= htmlspecialchars($evenement->getImagePath()) ?>" alt="Image actuelle" style="max-width: 200px; height: auto;">
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($evenement->getBannerPath()): ?>
                <div class="form-group">
                    <label>Bannière actuelle</label>
                    <div class="current-banner">
                        <img src="<?= htmlspecialchars($evenement->getBannerPath()) ?>" alt="Bannière actuelle" style="max-width: 300px; height: auto;">
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="image">Nouvelle image de l'événement</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="banner">Nouvelle bannière</label>
                    <input type="file" id="banner" name="banner" accept="image/*">
                </div>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="isPublic" value="1" 
                           <?= $evenement->getIsPublic() ? 'checked' : '' ?>>
                    Événement public
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="requiresApproval" value="1"
                           <?= $evenement->getRequiresApproval() ? 'checked' : '' ?>>
                    Inscription avec approbation
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Mettre à jour l'événement</button>
                <a href="<?= HOME_URL . 'evenement/modifier?id=' . $evenement->getIdEvenement() ?>" class="btn btn-secondary">Annuler</a>
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
