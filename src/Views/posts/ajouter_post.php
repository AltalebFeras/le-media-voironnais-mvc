<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <div class="flex-row justify-content-between">
        <h1><?= htmlspecialchars($title ?? 'Créer une actualité') ?></h1>
        <a href="<?= HOME_URL ?>mes_posts" class="">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
        </a>
    </div>

    <div class="flex-row">
        <div class="max-width-50">
            <?php include_once __DIR__ . '/../includes/messages.php'; ?>

            <form method="POST" action="<?= HOME_URL ?>post/ajouter" enctype="multipart/form-data">
                <div class="card">
                    <div>
                        <div>
                            <label for="title">Titre *</label>
                            <input type="text" name="title" id="title" required
                                value="<?= isset($_SESSION['form_data']['title']) ? htmlspecialchars($_SESSION['form_data']['title']) : '' ?>">
                        </div>

                        <div>
                            <label for="content">Contenu *</label>
                            <textarea name="content" id="content" rows="10" required
                                placeholder="Écrivez le contenu de votre actualité..."><?= isset($_SESSION['form_data']['content']) ? htmlspecialchars($_SESSION['form_data']['content']) : '' ?></textarea>
                        </div>

                        <div>
                            <label for="image">Image (optionnelle)</label>
                            <input type="file" name="image" id="image" accept="image/*">
                        </div>

                        <div>
                            <label for="authorType">Publier en tant que</label>
                            <select name="authorType" id="authorType" onchange="toggleAuthorFields()">
                                <option value="user">Moi-même</option>
                                <?php if (!empty($associations)): ?>
                                    <option value="association">Une association</option>
                                <?php endif; ?>
                                <?php if (!empty($entreprises)): ?>
                                    <option value="entreprise">Une entreprise</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div id="associationField" style="display: none;">
                            <label for="association_uiid">Association</label>
                            <select name="association_uiid" id="association_uiid">
                                <option value="">Sélectionner une association</option>
                                <?php foreach ($associations as $assoc): ?>
                                    <option value="<?= htmlspecialchars($assoc->getUiid()) ?>">
                                        <?= htmlspecialchars($assoc->getName()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div id="entrepriseField" style="display: none;">
                            <label for="entreprise_uiid">Entreprise</label>
                            <select name="entreprise_uiid" id="entreprise_uiid">
                                <option value="">Sélectionner une entreprise</option>
                                <?php foreach ($entreprises as $entr): ?>
                                    <option value="<?= htmlspecialchars($entr->getUiid()) ?>">
                                        <?= htmlspecialchars($entr->getName()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <div class="flex-row align-items-center">
                                <input type="checkbox" name="isPublished" value="1" id="isPublished" checked>
                                <label for="isPublished">Publier immédiatement</label>
                            </div>
                        </div>
                    </div>

                    <div class="flex-row justify-content-between mt">
                        <a href="<?= HOME_URL ?>mes_posts" class="btn linkNotDecorated">Annuler</a>
                        <button type="submit" class="btn">Créer l'actualité</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="max-width-33">
            <div class="card">
                <h3>Informations</h3>
                <p>Les champs marqués d'un * sont obligatoires.</p>
                <p>Vous pouvez publier au nom d'une association ou entreprise dont vous êtes propriétaire.</p>
                <p>Une actualité non publiée restera en brouillon et pourra être modifiée plus tard.</p>
            </div>
        </div>
    </div>
</main>

<script>
    function toggleAuthorFields() {
        const type = document.getElementById('authorType').value;
        document.getElementById('associationField').style.display = type === 'association' ? 'block' : 'none';
        document.getElementById('entrepriseField').style.display = type === 'entreprise' ? 'block' : 'none';
    }
</script>

<?php unset($_SESSION['form_data']); ?>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>