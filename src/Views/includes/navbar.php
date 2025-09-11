<header>
  <nav class="navbar">
    <div class="logo" style="display: flex; align-items: center;">
      <a href="/"><img
          src="<?= DOMAIN . HOME_URL . 'assets/imgs/logo.png' ?>"
          alt="Logo"
          height="60" />
      </a>
      <?php if (isset($_SESSION['connected'])) : ?>
        <a class="link" href="<?= HOME_URL . 'my_account' ?>">
          <img id="currentProfilePictureInNavbar" src="<?= $_SESSION['profilePicturePath'] ?>" alt="user profile image" width="44" height="44" style="margin-left: 1rem;">
        </a>
      <?php endif; ?>
    </div>
    <div class="burger mr ml" id="burger-menu">
      <span></span>
      <span></span>
      <span></span>
    </div>
    <ul class="nav-links" id="nav-links">
      <li><a class="link" href="<?= HOME_URL  ?>">Accueil</a></li>
      <?php if (isset($_SESSION['connected'])) : ?>
        <li>
          <a class="link" href="<?= HOME_URL . 'all_lists' ?>">Mes listes</a>
        </li>
        <li><a class="link" href="<?= HOME_URL . 'dashboard' ?>">Dashboard</a></li>
        <li>
          <a class="btn linkNotDecorated signOut" href="<?= HOME_URL . 'signOut' ?>">Déconnexion</a>
        </li>
      <?php else : ?>
        <li>
          <a class="btn linkNotDecorated" href="<?= HOME_URL . 'signIn' ?>">Connexion</a>
        </li>
      <?php endif; ?>
    </ul>
  </nav>
</header>