<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../components/messages.php';
?>

<main>
    <div class="flex-row align-items-center mb">
        <h1><?= htmlspecialchars($title ?? 'Ajouter une réalisation') ?></h1>
    </div>

    <div class="card mb">
        <h3>Entreprise : <?= htmlspecialchars($entreprise->getName()) ?></h3>
        <p>
            <a href="<?= HOME_URL ?>realisation/mes_realisations?uiid=<?= htmlspecialchars($entreprise->getUiid()) ?>" class="link">
                ← Retour à mes réalisations
            </a>
        </p>
    </div>

    <div class="card max-width-75">
        <form method="post" action="<?= HOME_URL ?>realisation/ajouter?uiid=<?= htmlspecialchars($entreprise->getUiid()) ?>">
            <label for="title">Titre de la réalisation *</label>
            <input type="text" name="title" id="title" required 
                   value="<?= isset($_SESSION['form_data']['title']) ? htmlspecialchars($_SESSION['form_data']['title']) : '' ?>">

            <label for="dateRealized">Date de réalisation</label>
            <input type="date" name="dateRealized" id="dateRealized" 
                   value="<?= isset($_SESSION['form_data']['dateRealized']) ? htmlspecialchars($_SESSION['form_data']['dateRealized']) : '' ?>">

            <label for="description">Description</label>
            <textarea name="description" id="description" rows="8" 
                      placeholder="Description détaillée de la réalisation, objectifs, technologies utilisées..."><?= isset($_SESSION['form_data']['description']) ? htmlspecialchars($_SESSION['form_data']['description']) : '' ?></textarea>

            <div class="flex-row">
                <label>
                    <input type="checkbox" name="isPublic" value="1" 
                           <?= (isset($_SESSION['form_data']['isPublic']) && $_SESSION['form_data']['isPublic']) ? 'checked' : 'checked' ?>>
                    Réalisation publique (visible par tous)
                </label>

                <label>
                    <input type="checkbox" name="isFeatured" value="1" 
                           <?= (isset($_SESSION['form_data']['isFeatured']) && $_SESSION['form_data']['isFeatured']) ? 'checked' : '' ?>>
                    Mettre en avant cette réalisation
                </label>
            </div>

            <div class="flex-row mt">
                <input type="submit" value="Créer la réalisation" class="btn">
                <a href="<?= HOME_URL ?>realisation/mes_realisations?uiid=<?= htmlspecialchars($entreprise->getUiid()) ?>" 
                   class="btn bg-danger">Annuler</a>
            </div>
        </form>
    </div>
</main>

<?php 
// Clean form data after displaying
unset($_SESSION['form_data']); 
include __DIR__ . '/../layouts/footer.php'; 
?>
            </div>

            <div class="flex-row mt">
                <input type="submit" value="Créer la réalisation" class="btn">
                <a href="<?= HOME_URL ?>realisation/mes_realisations?uiid=<?= htmlspecialchars($entreprise->getUiid()) ?>" 
                   class="btn bg-danger">Annuler</a>
            </div>
        </form>
    </div>
</main>

<?php 
// Clean form data after displaying
unset($_SESSION['form_data']); 
include __DIR__ . '/../layouts/footer.php'; 
?>
