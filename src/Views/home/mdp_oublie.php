<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
  <h1 class="h1Password">Réinitialisation du mot de passe</h1>
  <?php include_once __DIR__ . '/../includes/messages.php'; ?>

  <form class="card" action="<?= HOME_URL . 'forget_my_password' ?>" method="POST">
    <p>
      Veuillez entrer votre adresse e-mail pour réinitialiser votre mot de
      passe.
    </p>
    <!-- form inputs -->
    <div>
      <label for="email">Adresse e-mail:</label>
      <input type="email" id="email" name="email" placeholder="Entrez votre e-mail" value="<?= $_SESSION['form_data']['email'] ?? '' ?>" required />
    </div>
    <div class="g-recaptcha mb mt" data-sitekey="6Lc8mVYrAAAAAFQcZnr7_3rLS65SegjP0Yk0nX-s"></div>

    <button type="submit" class="btn btnSubmit">
      Réinitialiser le mot de passe
    </button>
  </form>
  <div>
    <a class="linkNotDecorated link" href="<?= HOME_URL ?>">Retour à l'accueil</a>
  </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>