<header>
  <nav class="navbar">
    <div class="logo" style="display: flex; align-items: center;">
      <a href="/"><img
          src="<?= DOMAIN . HOME_URL . 'assets/imgs/logo.gif' ?>"
          alt="Logo"
          width="60"
          height="60" />
      </a>
      <p class="logo_paragraph" style="margin-right: 1rem;">TIRSO</p>
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
<style>
/* Navbar profile image beside logo */
#currentProfilePictureInNavbar {
    border-radius: 50%;
    border: 2px solid #2a7ae2;
    box-shadow: 0 2px 8px rgba(42, 122, 226, 0.15);
    object-fit: cover;
    width: 44px;
    height: 44px;
    margin-left: 1rem;
    margin-right: 0.5rem;
    vertical-align: middle;
    transition: box-shadow 0.2s, border-color 0.2s;
    background: #fff;
}

#currentProfilePictureInNavbar:hover {
    border-color: #1a5bb8;
    box-shadow: 0 4px 16px rgba(42, 122, 226, 0.25);
}
</style>