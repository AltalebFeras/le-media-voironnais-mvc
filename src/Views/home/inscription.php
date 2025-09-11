<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
  <h1>Inscription</h1>
  <p>Veuillez vous inscrire pour créer un compte.</p>
  <!-- Alert Messages -->
  <?php include_once __DIR__ . '/../includes/messages.php'; ?>

  <form class="card" id="connexionForm" action="<?= HOME_URL . 'connexion' ?>" method="POST" novalidate>
    <div>
      <label for="firstName">Prénom:</label>
      <input type="text" id="firstName" name="firstName" placeholder="Entrez votre prénom" value="<?= $_SESSION['form_data']['firstName'] ?? '' ?>" required autocomplete="given-name" />
    </div>
    <div>
      <label for="lastName">Nom:</label>
      <input type="text" id="lastName" name="lastName" placeholder="Entrez votre nom" value="<?= $_SESSION['form_data']['lastName'] ?? '' ?>" required autocomplete="family-name" />
    </div>
    <div>
      <label for="email">Adresse e-mail:</label>
      <input type="email" id="email" name="email" placeholder="Entrez votre e-mail" value="<?= $_SESSION['form_data']['email'] ?? '' ?>" required autocomplete="email" />
    </div>
    <div>
      <label for="password">Mot de passe:</label>
      <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" value="<?= $_SESSION['form_data']['password'] ?? '' ?>" required autocomplete="new-password" />
    </div>
    <div>
      <label for="passwordConfirmation">Mot de passe confirmé:</label>
      <input type="password" id="passwordConfirmation" name="passwordConfirmation" placeholder="Confirmez votre mot de passe" value="<?= $_SESSION['form_data']['passwordConfirmation'] ?? '' ?>" required autocomplete="new-password" />
    </div>
    <div>
      <input type="checkbox" name="rgpd" id="rgpd" <?= isset($_SESSION['form_data']['rgpd']) && $_SESSION['form_data']['rgpd'] === 'on' ? 'checked' : '' ?> required />
      <label for="rgpd">J'accepte Le règlement général de protection des données <span><a target="_blank" href="<?= HOME_URL . 'cgu' ?>" class="">RGPD</a></span> </label>
    </div>
    <div class="g-recaptcha mb mt" data-sitekey="6Lc8mVYrAAAAAFQcZnr7_3rLS65SegjP0Yk0nX-s"></div>

    <button class="btn" id="submitBtn" type="submit">S'inscrire</button>
  </form>
  <div>
    <a class="linkNotDecorated link" href="<?= HOME_URL . 'connexion' ?>">Déjà inscrit ?</a>
  </div>
  <div>
    <a class="linkNotDecorated link" href="<?= HOME_URL  ?>">Retour à l'accueil</a>
  </div>
</main>
<script>

</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>