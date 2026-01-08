<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="preferences-container">
    <h1 class="preferences-title">Mes Préférences</h1>
    <?php include_once __DIR__ . '/../includes/messages.php'; ?>

    <form action="<?= HOME_URL . 'mes_preferences' ?>" method="POST" class="preferences-form">
        <div class="form-group">
            <label for="villes">Sélectionnez vos villes préférées :</label>
            here to make a fetch by code ostal call this api and returning the result , so the ville selected will add to the array of preferred villes selected

        </div>

        <div class="form-group">
            <label for="categories">Sélectionnez vos catégories d'événements préférées :</label>
            <?php foreach ($categories as $category): ?>
                <div class="checkbox-group" style="display: flex;">
                    <input id="category<?= $category['slug'] ?>" type="checkbox" name="categories[]" value="<?= $category['slug'] ?>">
                    <label for="category<?= $category['slug'] ?>"> <?= $category['name'] ?></label>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer mes préférences</button>

    </form>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>