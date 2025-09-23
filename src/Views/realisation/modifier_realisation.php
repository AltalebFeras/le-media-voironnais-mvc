<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <div class="flex-row justify-content-between">
        <h1><?= htmlspecialchars($title ?? 'Modifier la réalisation') ?></h1>
        <a href="<?= HOME_URL . 'realisation/voir?realisation_uiid=' . htmlspecialchars($realisation->getUiid()) ?>" class="">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
        </a>
    </div>

    <div class="flex-row">
        <div class="max-width-50">
            <?php include_once __DIR__ . '/../includes/messages.php'; ?>

            <div class="card mb-4">
                <div class="p-3">
                    <h3>Entreprise : <?= htmlspecialchars($entreprise->getName()) ?></h3>
                </div>
            </div>
            
            <form method="post" action="<?= HOME_URL . 'realisation/modifier?realisation_uiid=' . htmlspecialchars($realisation->getUiid()) ?>">
                <div class="card">
                    <div>
                        <div>
                            <label for="title">Titre de la réalisation *</label>
                            <input type="text" name="title" id="title" required 
                                   value="<?= isset($_SESSION['form_data']['title']) ? htmlspecialchars($_SESSION['form_data']['title']) : htmlspecialchars($realisation->getTitle()) ?>">
                        </div>

                        <div>
                            <label for="dateRealized">Date de réalisation</label>
                            <input type="date" name="dateRealized" id="dateRealized" 
                                   value="<?= isset($_SESSION['form_data']['dateRealized']) ? htmlspecialchars($_SESSION['form_data']['dateRealized']) : htmlspecialchars($realisation->getDateRealized() ?? '') ?>">
                        </div>

                        <div>
                            <label for="description">Description</label>
                            <textarea name="description" id="description" rows="8" 
                                      placeholder="Description détaillée de la réalisation, objectifs, technologies utilisées..."><?= isset($_SESSION['form_data']['description']) ? htmlspecialchars($_SESSION['form_data']['description']) : htmlspecialchars($realisation->getDescription() ?? '') ?></textarea>
                        </div>

                        <div>
                            <div class="flex-row align-items-center">
                                <input type="checkbox" name="isPublic" value="1" id="isPublic"
                                       <?= (isset($_SESSION['form_data']['isPublic']) ? $_SESSION['form_data']['isPublic'] : $realisation->getIsPublic()) ? 'checked' : '' ?>>
                                <label for="isPublic">Réalisation publique (visible par tous)</label>
                            </div>
                        </div>

                        <div>
                            <div class="flex-row align-items-center">
                                <input type="checkbox" name="isFeatured" value="1" id="isFeatured"
                                       <?= (isset($_SESSION['form_data']['isFeatured']) ? $_SESSION['form_data']['isFeatured'] : $realisation->getIsFeatured()) ? 'checked' : '' ?>>
                                <label for="isFeatured">Mettre en avant cette réalisation</label>
                            </div>
                        </div>
                    </div>

                    <div class="flex-row justify-content-between mt">
                        <a href="<?= HOME_URL . 'realisation/voir?realisation_uiid=' . htmlspecialchars($realisation->getUiid()) ?>" class="btn linkNotDecorated">Annuler</a>
                        <button type="submit" class="btn">Enregistrer les modifications</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="max-width-33">
            <div class="card">
                <h3>Informations</h3>
                <p>Les champs marqués d'un * sont obligatoires.</p>
                <p>Une réalisation publique sera visible par tous les visiteurs du site.</p>
                <p>Mettre en avant une réalisation la rendra plus visible dans les listes.</p>
            </div>
        </div>
    </div>
</main>

<?php 
// Clean form data after displaying
unset($_SESSION['form_data']); 
include_once __DIR__ . '/../includes/footer.php'; 
?>
