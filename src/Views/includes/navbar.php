</head>

<body>
  <header>
    <nav class="navbar">
      <div class="logo" style="display: flex; align-items: center; position: relative;">
        <a href="<?= HOME_URL ?>"><img
            src="<?= DOMAIN . HOME_URL . 'assets/images/logo/logo.png' ?>"
            alt="Logo"
            height="60" />
        </a>
        <?php if (isset($_SESSION['connected'])) : ?>
          <a class="link" href="<?= HOME_URL . 'mon_compte' ?>">
            <img id="currentProfilePictureInNavbar" src="<?= $_SESSION['avatarPath'] ?>" alt="user profile image" width="44" height="44" style="margin-left: 1rem;">
          </a>
          <!-- Notification Badge include -->
          <?php include __DIR__ . '/notification_badge.php'; ?>
        <?php endif; ?>
      </div>
      <div class="burger mr ml" id="burger-menu">
        <span></span>
        <span></span>
        <span></span>
      </div>
      <ul class="nav-links" id="nav-links">
        <li><a class="link" href="<?= HOME_URL . 'evenements' ?>">Événements</a></li>
        <?php if (isset($_SESSION['connected'])) : ?>
          <?php  ?>
          <li><a class="link" href="<?= HOME_URL . 'dashboard' ?>">Dashboard</a></li>
          <li><a class="link" href="<?= HOME_URL . 'mes_associations' ?>">Mes Associations</a></li>
          <li><a class="link" href="<?= HOME_URL . 'mes_entreprises' ?>">Mes Entreprises</a></li>
          <li><a class="link" href="<?= HOME_URL . 'mes_evenements' ?>">Mes Événements</a></li>
          <li><a class="btn linkNotDecorated deconnexion" href="<?= HOME_URL . 'deconnexion' ?>">Déconnexion</a></li>
        <?php else : ?>
          <?php if (isset($_SESSION['connectedAdmin']) || isset($_SESSION['connectedSuperAdmin'])) : ?>
            <li><a class="link" href="<?= HOME_URL . 'admin/dashboard_admin' ?>">Retour au Dashboard</a></li>
          <?php else: ?>
            <li><a class="btn linkNotDecorated " href="<?= HOME_URL . 'connexion' ?>">Connexion</a></li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>
    </nav>
  </header>