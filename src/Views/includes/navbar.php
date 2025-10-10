</head>

<body>
  <header>
    <nav class="navbar">
      <div class="logo">
        <a href="<?= HOME_URL ?>">
          <img src="<?= DOMAIN . HOME_URL . 'assets/images/logo/logo.png' ?>" alt="Logo" height="60" />
        </a>
        <?php if (isset($_SESSION['connected'])) : ?>
          <?php include __DIR__ . '/notification_badge.php'; ?>
        <?php endif; ?>
      </div>

      <!-- Search Form (always visible on desktop) -->
      <form class="navbar-search" action="<?= HOME_URL . 'recherche' ?>" method="GET">
        <input type="search" name="q" placeholder="Rechercher..." aria-label="Rechercher" />
      </form>

      <div class="burger" id="burger-menu">
        <span></span>
        <span></span>
        <span></span>
      </div>

      <ul class="nav-links" id="nav-links">
        <!-- Public Links -->
        <li><a class="link nav-item" href="<?= HOME_URL . 'actus' ?>">
            <span class="material-icons">article</span>
            <span class="nav-text">Actus</span>
          </a></li>
        <li><a class="link nav-item" href="<?= HOME_URL . 'evenements' ?>">
            <span class="material-icons">event</span>
            <span class="nav-text">Événements</span>
          </a></li>

        <?php if (isset($_SESSION['connected'])) : ?>
          <!-- Connected User Links -->
          <li><a class="link nav-item" href="<?= HOME_URL . 'chat' ?>">
              <span class="material-icons">chat</span>
              <span class="nav-text">Chat</span>
            </a></li>

          <li><a class="link nav-item" href="<?= HOME_URL . 'mes_entreprises' ?>">
              <span class="material-icons">business</span>
              <span class="nav-text">Structures</span>
            </a></li>

          <!-- Moi Dropdown -->
          <li class="nav-item-dropdown">
            <button class="link nav-item nav-dropdown-toggle" id="moiDropdownToggle" aria-haspopup="true" aria-expanded="false">
              <img id="currentProfilePictureInNavbar" src="<?= $_SESSION['avatarPath'] ?>" alt="user profile image" width="32" height="32">
              <span class="nav-text">Moi</span>
              <span class="material-icons dropdown-arrow">arrow_drop_down</span>
            </button>
            <div class="nav-dropdown-menu" id="moiDropdownMenu">
              <a class="nav-dropdown-item" href="<?= HOME_URL . 'mon_compte' ?>">
                <span class="material-icons">account_circle</span>
                <span><?= $_SESSION['firstName'] ?> <?= $_SESSION['lastName'] ?></span>
              </a>
              <a class="nav-dropdown-item" href="<?= HOME_URL . 'mes_entreprises' ?>">
                <span class="material-icons">business</span>
                <span class="nav-text">Mes entreprises</span>
              </a>
              <a class="nav-dropdown-item" href="<?= HOME_URL . 'mes_evenements' ?>">
                <span class="material-icons">calendar_today</span>
                <span>Mes Événements</span>
              </a>
              <a class="nav-dropdown-item" href="<?= HOME_URL . 'mes_associations' ?>">
                <span class="material-icons">groups</span>
                <span>Mes Associations</span>
              </a>
              <div class="nav-dropdown-divider"></div>
              <a class="nav-dropdown-item" href="<?= HOME_URL . 'deconnexion' ?>">
                <span class="material-icons">logout</span>
                <span>Déconnexion</span>
              </a>
            </div>
          </li>
        <?php else : ?>
          <?php if (isset($_SESSION['connectedAdmin']) || isset($_SESSION['connectedSuperAdmin'])) : ?>
            <li><a class="link nav-item" href="<?= HOME_URL . 'admin/dashboard_admin' ?>">
                <span class="material-icons">admin_panel_settings</span>
                <span class="nav-text">Dashboard Admin</span>
              </a></li>
          <?php else: ?>
            <li><a class="btn btn-primary linkNotDecorated btnConnexion" href="<?= HOME_URL . 'connexion' ?>">Connexion</a></li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>
    </nav>
  </header>