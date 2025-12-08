<div class="container mx-auto px-4 py-8 max-w-3xl">
    <h1 class="text-3xl font-bold mb-6">Créer une actualité</h1>

    <form method="POST" action="<?= HOME_URL ?>post/ajouter" enctype="multipart/form-data" class="space-y-6">
        <div class="form-control">
            <label class="label"><span class="label-text">Titre *</span></label>
            <input type="text" name="title" class="input input-bordered" required value="<?= htmlspecialchars($_SESSION['form_data']['title'] ?? '') ?>">
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text">Contenu *</span></label>
            <textarea name="content" class="textarea textarea-bordered h-48" required><?= htmlspecialchars($_SESSION['form_data']['content'] ?? '') ?></textarea>
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text">Image</span></label>
            <input type="file" name="image" class="file-input file-input-bordered" accept="image/*">
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text">Publier en tant que</span></label>
            <select name="authorType" id="authorType" class="select select-bordered" onchange="toggleAuthorFields()">
                <option value="user">Moi-même</option>
                <?php if (!empty($associations)): ?>
                    <option value="association">Une association</option>
                <?php endif; ?>
                <?php if (!empty($entreprises)): ?>
                    <option value="entreprise">Une entreprise</option>
                <?php endif; ?>
            </select>
        </div>

        <div id="associationField" class="form-control hidden">
            <label class="label"><span class="label-text">Association</span></label>
            <select name="association_uiid" class="select select-bordered">
                <option value="">Sélectionner une association</option>
                <?php foreach ($associations as $assoc): ?>
                    <option value="<?= htmlspecialchars($assoc->getUiid()) ?>"><?= htmlspecialchars($assoc->getName()) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="entrepriseField" class="form-control hidden">
            <label class="label"><span class="label-text">Entreprise</span></label>
            <select name="entreprise_uiid" class="select select-bordered">
                <option value="">Sélectionner une entreprise</option>
                <?php foreach ($entreprises as $entr): ?>
                    <option value="<?= htmlspecialchars($entr->getUiid()) ?>"><?= htmlspecialchars($entr->getName()) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-control">
            <label class="label cursor-pointer justify-start gap-2">
                <input type="checkbox" name="isPublished" class="checkbox" checked>
                <span class="label-text">Publier immédiatement</span>
            </label>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="btn btn-primary">Créer l'actualité</button>
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
<?php unset($_SESSION['form_data']); ?>
