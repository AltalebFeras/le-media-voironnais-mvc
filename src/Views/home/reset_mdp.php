<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
    <h1 class="h1Password">Réinitialisation du mot de passe</h1>
    <!-- Alert Messages -->
  <?php include_once __DIR__ . '/../includes/messages.php'; ?>
    <form action="<?= DOMAIN . HOME_URL . 'reset_my_password' ?>" method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
        <p>
            Veuillez entrer votre nouveau mot de passe.
        </p>
        <!-- form inputs -->
        <div>
            <label for="newPassword">Nouveau mot de passe:</label>
            <input type="password" id="newPassword" name="newPassword" value="<?= $_SESSION['form_data']['newPassword'] ?? '' ?>" placeholder="Entrez votre nouveau mot de passe" required />
        </div>
        <div>
            <label for="confirmPassword">Confirmer le mot de passe:</label>
            <input type="password" id="confirmPassword" name="confirmPassword" value="<?= $_SESSION['form_data']['confirmPassword'] ?? '' ?>" placeholder="Confirmez votre mot de passe" required />
        </div>
    <div class="g-recaptcha mb mt" data-sitekey="6Lc8mVYrAAAAAFQcZnr7_3rLS65SegjP0Yk0nX-s"></div>

        <button type="submit" class="btn">
            Réinitialiser le mot de passe
        </button>
    </form>
    <div>
        <a class="linkNotDecorated link" href="<?= HOME_URL ?>">Retour à l'accueil</a>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>