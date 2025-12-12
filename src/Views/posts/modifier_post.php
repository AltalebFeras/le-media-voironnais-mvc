<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <div class="page-header">
        <h1><?= htmlspecialchars($title ?? 'Modifier l\'actualité') ?></h1>
        <?php 
        $backUrl = HOME_URL . 'mes_posts';
        if (isset($_GET['back']) && $_GET['back'] === 'actu') {
            $backUrl = HOME_URL . 'actu/' . htmlspecialchars($post['uiid']);
        }
        ?>
        <a href="<?= $backUrl ?>" class="linkNotDecorated">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
        </a>
    </div>

    <div class="post-form-container">
        <div class="max-width-50">
            <?php include_once __DIR__ . '/../includes/messages.php'; ?>

            <?php if ($post['imagePath']): ?>
                <div class="image-management">
                    <h3>Image actuelle</h3>
                    <div class="current-image-container">
                        <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['imagePath']) ?>" alt="Image">
                    </div>
                    <div class="image-actions">
                        <form method="POST" action="<?= HOME_URL ?>post/modifier" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="modifier_image">
                            <input type="hidden" name="uiid" value="<?= htmlspecialchars($post['uiid']) ?>">
                            <input type="file" name="image" required>
                            <button type="submit" class="btn">Changer l'image</button>
                        </form>
                        <form method="POST" action="<?= HOME_URL ?>post/modifier">
                            <input type="hidden" name="action" value="supprimer_image">
                            <input type="hidden" name="uiid" value="<?= htmlspecialchars($post['uiid']) ?>">
                            <button type="submit" class="btn bg-danger" onclick="return confirm('Supprimer l\'image ?')">
                                Supprimer l'image
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= HOME_URL ?>post/modifier">
                <input type="hidden" name="action" value="modifier_post">
                <input type="hidden" name="uiid" value="<?= htmlspecialchars($post['uiid']) ?>">

                <div class="post-form-card">
                    <div class="form-group">
                        <label for="title">Titre *</label>
                        <input type="text" name="title" id="title" required
                            value="<?= htmlspecialchars($post['title']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="content">Contenu *</label>
                        <textarea name="content" id="content" required><?= htmlspecialchars($post['content']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="authorType">Publier en tant que</label>
                        <select name="authorType" id="authorType" onchange="toggleAuthorFields()">
                            <option value="user" <?= $post['authorType'] === 'user' ? 'selected' : '' ?>>Moi-même</option>
                            <?php if (!empty($associations)): ?>
                                <option value="association" <?= $post['authorType'] === 'association' ? 'selected' : '' ?>>Une association</option>
                            <?php endif; ?>
                            <?php if (!empty($entreprises)): ?>
                                <option value="entreprise" <?= $post['authorType'] === 'entreprise' ? 'selected' : '' ?>>Une entreprise</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div id="associationField" class="form-group" style="display: <?= $post['authorType'] === 'association' ? 'block' : 'none' ?>;">
                        <label for="association_uiid">Association</label>
                        <select name="association_uiid" id="association_uiid">
                            <option value="">Sélectionner une association</option>
                            <?php foreach ($associations as $assoc): ?>
                                <option value="<?= htmlspecialchars($assoc->getUiid()) ?>"
                                    <?= $post['idAssociation'] == $assoc->getIdAssociation() ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($assoc->getName()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="entrepriseField" class="form-group" style="display: <?= $post['authorType'] === 'entreprise' ? 'block' : 'none' ?>;">
                        <label for="entreprise_uiid">Entreprise</label>
                        <select name="entreprise_uiid" id="entreprise_uiid">
                            <option value="">Sélectionner une entreprise</option>
                            <?php foreach ($entreprises as $entr): ?>
                                <option value="<?= htmlspecialchars($entr->getUiid()) ?>"
                                    <?= $post['idEntreprise'] == $entr->getIdEntreprise() ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($entr->getName()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" name="isPublished" value="1" id="isPublished"
                            <?= $post['isPublished'] ? 'checked' : '' ?>>
                        <label for="isPublished">Publié</label>
                    </div>

                    <div class="post-actions mt">
                        <a href="<?= $backUrl ?>" class="btn btn-light linkNotDecorated">Annuler</a>
                        <button type="submit" class="btn">Enregistrer les modifications</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="max-width-33">
            <div class="post-info-sidebar">
                <h3>Informations</h3>
                <p>Les champs marqués d'un * sont obligatoires.</p>
                <p>Vous pouvez changer l'auteur de l'actualité si vous êtes propriétaire de plusieurs entités.</p>
                <p>Décochez "Publié" pour mettre l'actualité en brouillon.</p>
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

<?php include_once __DIR__ . '/../includes/footer.php'; ?>