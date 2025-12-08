<div class="container mx-auto px-4 py-8 max-w-3xl">
    <h1 class="text-3xl font-bold mb-6">Modifier l'actualité</h1>

    <!-- Image management -->
    <?php if ($post['imagePath']): ?>
        <div class="card bg-base-200 mb-6">
            <div class="card-body">
                <h3 class="card-title">Image actuelle</h3>
                <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($post['imagePath']) ?>" alt="Image" class="rounded-lg max-w-md">
                <div class="card-actions">
                    <form method="POST" action="<?= HOME_URL ?>post/modifier" enctype="multipart/form-data" class="inline">
                        <input type="hidden" name="action" value="modifier_image">
                        <input type="hidden" name="uiid" value="<?= htmlspecialchars($post['uiid']) ?>">
                        <input type="file" name="image" class="file-input file-input-sm" required>
                        <button type="submit" class="btn btn-sm btn-primary">Changer</button>
                    </form>
                    <form method="POST" action="<?= HOME_URL ?>post/modifier" onsubmit="return confirm('Supprimer l\'image ?')" class="inline">
                        <input type="hidden" name="action" value="supprimer_image">
                        <input type="hidden" name="uiid" value="<?= htmlspecialchars($post['uiid']) ?>">
                        <button type="submit" class="btn btn-sm btn-error">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= HOME_URL ?>post/modifier" class="space-y-6">
        <input type="hidden" name="action" value="modifier_post">
        <input type="hidden" name="uiid" value="<?= htmlspecialchars($post['uiid']) ?>">

        <div class="form-control">
            <label class="label"><span class="label-text">Titre *</span></label>
            <input type="text" name="title" class="input input-bordered" required value="<?= htmlspecialchars($post['title']) ?>">
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text">Contenu *</span></label>
            <textarea name="content" class="textarea textarea-bordered h-48" required><?= htmlspecialchars($post['content']) ?></textarea>
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text">Publier en tant que</span></label>
            <select name="authorType" id="authorType" class="select select-bordered" onchange="toggleAuthorFields()">
                <option value="user" <?= $post['authorType'] === 'user' ? 'selected' : '' ?>>Moi-même</option>
                <?php if (!empty($associations)): ?>
                    <option value="association" <?= $post['authorType'] === 'association' ? 'selected' : '' ?>>Une association</option>
                <?php endif; ?>
                <?php if (!empty($entreprises)): ?>
                    <option value="entreprise" <?= $post['authorType'] === 'entreprise' ? 'selected' : '' ?>>Une entreprise</option>
                <?php endif; ?>
            </select>
        </div>

        <div id="associationField" class="form-control <?= $post['authorType'] !== 'association' ? 'hidden' : '' ?>">
            <label class="label"><span class="label-text">Association</span></label>
            <select name="association_uiid" class="select select-bordered">
                <option value="">Sélectionner une association</option>
                <?php foreach ($associations as $assoc): ?>
                    <option value="<?= htmlspecialchars($assoc->getUiid()) ?>" <?= $post['idAssociation'] == $assoc->getIdAssociation() ? 'selected' : '' ?>>
                        <?= htmlspecialchars($assoc->getName()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="entrepriseField" class="form-control <?= $post['authorType'] !== 'entreprise' ? 'hidden' : '' ?>">
            <label class="label"><span class="label-text">Entreprise</span></label>
            <select name="entreprise_uiid" class="select select-bordered">
                <option value="">Sélectionner une entreprise</option>
                <?php foreach ($entreprises as $entr): ?>
                    <option value="<?= htmlspecialchars($entr->getUiid()) ?>" <?= $post['idEntreprise'] == $entr->getIdEntreprise() ? 'selected' : '' ?>>
                        <?= htmlspecialchars($entr->getName()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-control">
            <label class="label cursor-pointer justify-start gap-2">
                <input type="checkbox" name="isPublished" class="checkbox" <?= $post['isPublished'] ? 'checked' : '' ?>>
                <span class="label-text">Publié</span>
            </label>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="<?= HOME_URL ?>mes_posts" class="btn btn-outline">Annuler</a>
        </div>
    </form>
</div>

<script>
function toggleAuthorFields() {
    const type = document.getElementById('authorType').value;
    document.getElementById('associationField').classList.toggle('hidden', type !== 'association');
    document.getElementById('entrepriseField').classList.toggle('hidden', type !== 'entreprise');
}
</script>
