</head>
<body>
  <header>
    <nav class="navbar">
      <div class="logo">
        <a href="<?= HOME_URL ?>">
          <img src="<?= DOMAIN . HOME_URL . 'assets/images/logo/logo.png' ?>" alt="Logo" height="60" />
        </a>
        <?php if (isset($_SESSION['connectedAdmin'])) : ?>
          <?php include __DIR__ . '/notification_badge.php'; ?>
        <?php endif; ?>
      </div>

      <div class="burger" id="burger-menu">
        <span></span>
        <span></span>
        <span></span>
      </div>

      <ul class="nav-links" id="nav-links">
        <!-- Public Links -->
        <li><a class="link nav-item" href="<?= HOME_URL ?>">
            <span class="material-icons">home</span>
            <span class="nav-text">Accueil</span>
          </a></li>

        <?php if (isset($_SESSION['connectedAdmin']) || isset($_SESSION['connectedSuperAdmin'])) : ?>
          <!-- Admin Links -->
          <li><a class="link nav-item" href="<?= HOME_URL . 'admin/dashboard_admin' ?>">
              <span class="material-icons">dashboard</span>
              <span class="nav-text">Dashboard</span>
            </a></li>

          <!-- Admin Profile Dropdown -->
          <li class="nav-item-dropdown">
            <button class="link nav-item nav-dropdown-toggle" id="moiDropdownToggle" aria-haspopup="true" aria-expanded="false">
              <img id="currentProfilePictureInNavbar" src="<?= $_SESSION['avatarPath'] ?>" alt="admin profile image" width="32" height="32">
              <span class="nav-text">Admin</span>
              <span class="material-icons dropdown-arrow">arrow_drop_down</span>
            </button>
            <div class="nav-dropdown-menu" id="moiDropdownMenu">
              <a class="nav-dropdown-item" href="<?= HOME_URL . 'mon_compte' ?>">
                <span class="material-icons">account_circle</span>
                <span><?= $_SESSION['firstName'] ?? 'Admin' ?> <?= $_SESSION['lastName'] ?? '' ?></span>
              </a>
              <a class="nav-dropdown-item" href="<?= HOME_URL . 'admin/dashboard_admin' ?>">
                <span class="material-icons">admin_panel_settings</span>
                <span>Panel Admin</span>
              </a>
              <div class="nav-dropdown-divider"></div>
              <a class="nav-dropdown-item" href="<?= HOME_URL . 'deconnexion' ?>">
                <span class="material-icons">logout</span>
                <span>Déconnexion</span>
              </a>
            </div>
          </li>
        <?php else : ?>
          <li><a class="btn btn-primary linkNotDecorated" href="<?= HOME_URL . 'connexion' ?>">Connexion</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>