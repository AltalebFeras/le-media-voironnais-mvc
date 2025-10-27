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

      <div class="burger" id="burger-menu">
        <span></span>
        <span></span>
        <span></span>
      </div>

      <ul class="nav-links" id="nav-links">
        <!-- Public Links -->
        <!-- Search Form for mobile -->
        <li class="nav-item-mobile-search">
          <div class="nav-item search-container">
            <form class="search-form" action="<?= HOME_URL . 'recherche' ?>" method="POST">
              <input type="search" name="q" id="search-input-mobile" placeholder="Rechercher..." aria-label="Rechercher" autocomplete="off" maxlength="100" />
              <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>" />
            </form>
            <div class="search-results" id="search-results-mobile"></div>
          </div>
        </li>

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
              <a class="nav-dropdown-item" href="<?= HOME_URL . 'mes_favoris' ?>">
                <span class="material-icons">favorite</span>
                <span>Mes Favoris</span>
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

  <style>
    .search-container {
      position: relative;
    }
    
    .search-results {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: white;
      border: 1px solid #ddd;
      border-radius: 4px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      z-index: 1000;
      max-height: 400px;
      overflow-y: auto;
      display: none;
    }
    
    .search-result-item {
      padding: 12px;
      border-bottom: 1px solid #eee;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .search-result-item:hover {
      background-color: #f5f5f5;
    }
    
    .search-result-item:last-child {
      border-bottom: none;
    }
    
    .search-result-image {
      width: 40px;
      height: 40px;
      object-fit: cover;
      border-radius: 4px;
    }
    
    .search-result-content {
      flex: 1;
    }
    
    .search-result-title {
      font-weight: 500;
      color: #333;
    }
    
    .search-result-subtitle {
      font-size: 11px;
      color: #999;
    }
    
    .search-result-type {
      font-size: 12px;
      color: #666;
      text-transform: capitalize;
    }
    
    .search-error {
      padding: 12px;
      color: #dc3545;
      font-size: 14px;
      text-align: center;
    }
  </style>

  <script>
    $(document).ready(function() {
      let searchTimeout;
      
      function setupSearch(inputId, resultsId) {
        const $searchInput = $('#' + inputId);
        const $searchResults = $('#' + resultsId);
        
        if (!$searchInput.length || !$searchResults.length) return;
        
        $searchInput.on('input', function() {
          clearTimeout(searchTimeout);
          const query = $(this).val().trim();
          
          if (query.length < 3) {
            $searchResults.hide();
            return;
          }
          
          if (query.length > 100) {
            $searchResults.html('<div class="search-error">La recherche ne peut pas dépasser 100 caractères</div>');
            $searchResults.show();
            return;
          }
          
          // Validate characters - Fixed regex pattern
          const validPattern = /^[a-zA-Z0-9àáâãäåæçèéêëìíîïñòóôõöøùúûüýÿÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝŸ\s\-'\.]+$/u;
          if (!validPattern.test(query)) {
            $searchResults.html('<div class="search-error">Caractères non autorisés dans la recherche</div>');
            $searchResults.show();
            return;
          }
          
          searchTimeout = setTimeout(() => {
            const formData = new FormData();
            formData.append('q', query);
            formData.append('csrf_token', $('input[name="csrf_token"]').val());
            
            $.ajax({
              url: '<?= HOME_URL ?>recherche',
              type: 'POST',
              data: formData,
              processData: false,
              contentType: false,
              dataType: 'json',
              success: function(data) {
                if (data.error) {
                  $searchResults.html('<div class="search-error">' + data.error + '</div>');
                  $searchResults.show();
                } else {
                  displaySearchResults(data.results, $searchResults);
                }
              },
              error: function(xhr, status, error) {
                console.error('Search error:', error);
                $searchResults.html('<div class="search-error">Erreur lors de la recherche</div>');
                $searchResults.show();
              }
            });
          }, 300);
        });
        
        // Hide results when clicking outside
        $(document).on('click', function(event) {
          if (!$searchInput.is(event.target) && !$searchResults.is(event.target) && 
              $searchResults.has(event.target).length === 0) {
            $searchResults.hide();
          }
        });
      }
      
      function displaySearchResults(results, $container) {
        if (results.length === 0) {
          $container.html('<div class="search-result-item">Aucun résultat trouvé</div>');
          $container.show();
          return;
        }
        
        const typeLabels = {
          'user': 'Utilisateur',
          'evenement': 'Événement',
          'entreprise': 'Entreprise',
          'association': 'Association',
          'ville': 'Ville'
        };
        
        const resultsHtml = results.map(function(result) {
          const imageHtml = result.image ? 
            '<img src="' + result.image + '" alt="' + result.title + '" class="search-result-image">' :
            '<div class="search-result-image" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center; font-size: 18px; color: #666;">' +
              (result.type === 'ville' ? '🏙️' : 
               result.type === 'user' ? '👤' : 
               result.type === 'evenement' ? '📅' : 
               result.type === 'entreprise' ? '🏢' : '🏛️') +
            '</div>';
          
          const subtitleHtml = result.subtitle ? 
            '<div class="search-result-subtitle">' + result.subtitle + '</div>' : '';
          
          return '<div class="search-result-item" data-url="' + result.url + '">' +
                   imageHtml +
                   '<div class="search-result-content">' +
                     '<div class="search-result-title">' + result.title + '</div>' +
                     subtitleHtml +
                     '<div class="search-result-type">' + (typeLabels[result.type] || result.type) + '</div>' +
                   '</div>' +
                 '</div>';
        }).join('');
        
        $container.html(resultsHtml);
        $container.show();
        
        // Add click handlers to result items
        $container.off('click', '.search-result-item').on('click', '.search-result-item', function(e) {
          e.preventDefault();
          const url = $(this).data('url');
          if (url) {
            window.location.href = url;
          }
        });
      }
      
      // Initialize search for mobile
      setupSearch('search-input-mobile', 'search-results-mobile');
    });
  </script>