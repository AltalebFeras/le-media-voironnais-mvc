<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<main>
  <h1>Connexion</h1>
  <p>Connectez-vous pour accéder à votre espace personnel sur Le Media Voironnais.<br>
  Créez, gérez vos événements, associations, entreprises et discutez avec la communauté locale !</p>
  <!-- Alert Messages -->
  <?php include_once __DIR__ . '/../includes/messages.php'; ?>

  <form class="card" action="<?= HOME_URL . './signIn' ?>" method="POST">
    <div>
      <label for="email">Adresse e-mail:</label>
      <input type="email" id="email" name="email" placeholder="Entrez votre e-mail"
        <?= isset($_SESSION['form_data']['email']) ? 'value="' . $_SESSION['form_data']['email'] . '"' : '' ?> required autocomplete="email"/>
    </div>
    <div>
      <label for="password">Mot de passe:</label>
      <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" <?= isset($_SESSION['form_data']['password']) ? 'value="' . $_SESSION['form_data']['password'] . '"' : '' ?> required autocomplete="current-password" />
    </div>
    <div class="g-recaptcha mb mt" data-sitekey="6Lc8mVYrAAAAAFQcZnr7_3rLS65SegjP0Yk0nX-s"></div>

    <button type="submit" class="btn btnSubmit">Se connecter</button>
  </form>
  <div class="forgot-password-container">
    <a class="linkNotDecorated link" href="<?= HOME_URL . './forget_my_password' ?>">Mot de passe oublié ?</a>
  </div>
  <p>
    Pas encore de compte ?
    <a href="<?= HOME_URL . './signUp' ?>" class="link">S'inscrire gratuitement</a>
  </p>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>