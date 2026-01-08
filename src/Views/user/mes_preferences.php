<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/users/mes_preferences.css' ?>">

<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="preferences-container">
    <h1 class="preferences-title">Mes Préférences</h1>
    <?php include_once __DIR__ . '/../includes/messages.php'; ?>

    <form action="<?= HOME_URL . 'mes_preferences' ?>" method="POST" class="preferences-form" id="preferencesForm">
        <?php include_once __DIR__ . '/../includes/csrf_token.php'; ?>
        
        <!-- Code Postal Input -->
        <div class="form-group">
            <label for="codePostalInput" class="form-label">Code postal :</label>
            <input type="text" id="codePostalInput" maxlength="5" pattern="\d{3,5}" class="form-control" placeholder="Entrez un code postal (ex: 38700)">
        </div>

        <!-- Ville Selection Section - Horizontal Layout -->
        <div class="ville-selection-row">
            <!-- Villes trouvées -->
            <div class="ville-column">
                <p class="form-label">
                    <i class="fas fa-search"></i> Villes trouvées
                </p>
                <div class="ville-list-container">
                    <ul id="villeResults" aria-label="Liste des villes trouvées">
                        <li class="empty-state">Entrez un code postal pour rechercher des villes</li>
                    </ul>
                </div>
            </div>

            <!-- Villes sélectionnées -->
            <div class="ville-column">
                <p class="form-label">
                    <i class="fas fa-check-circle"></i> Villes sélectionnées
                    <span id="villeCount" class="ville-count">0</span>
                </p>
                <div class="ville-list-container selected-container">
                    <ul id="selectedVillesList" aria-label="Liste des villes sélectionnées">
                        <li class="empty-state">Aucune ville sélectionnée</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="selectedVillesHiddenInputs"></div>

        <!-- Categories Section -->
        <div class="form-group categories-section">
            <p class="form-label">
                <i class="fas fa-tags"></i> Sélectionnez vos catégories d'événements préférées
            </p>
            <div class="categories-flex">
            <?php foreach ($categories as $category): ?>
                <div class="checkbox-group">
                    <input id="category<?= $category['slug'] ?>" type="checkbox" name="categories[]" value="<?= $category['slug'] ?>">
                    <label for="category<?= $category['slug'] ?>"> <?= $category['name'] ?></label>
                </div>
            <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-submit">
            <i class="fas fa-save"></i> Enregistrer mes préférences
        </button>
    </form>
</main>

<script>
    // --- Ville selection logic with debugging ---
    const BASE_URL = "<?= BASE_URL ?>";
    const HOME_URL = "<?= HOME_URL ?>";
    const $codePostalInput = document.getElementById('codePostalInput');
    const $villeResults = document.getElementById('villeResults');
    const $selectedVillesList = document.getElementById('selectedVillesList');
    const $selectedVillesHiddenInputs = document.getElementById('selectedVillesHiddenInputs');
    const $villeCount = document.getElementById('villeCount');

    let selectedVilles = [];
    let fetchTimeout = null;


    $codePostalInput.addEventListener('input', function () {
        const codePostal = $codePostalInput.value.trim();
        
        if (codePostal.length >= 3 && /^\d{3,5}$/.test(codePostal)) {
            clearTimeout(fetchTimeout);
            fetchTimeout = setTimeout(() => {
                $villeResults.innerHTML = '<li class="loading-state"><i class="fas fa-spinner fa-spin"></i> Recherche en cours...</li>';
                
                fetch(BASE_URL + HOME_URL + 'villes', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ codePostal })
                })
                .then(res => res.json())
                .then(data => {
                    $villeResults.innerHTML = '';
                    if (data && data.succes && data.data && data.data.length > 0) {
                        data.data.forEach(ville => {
                            const li = document.createElement('li');
                            li.className = 'ville-item';
                            
                            const villeInfo = document.createElement('span');
                            villeInfo.className = 'ville-name';
                            villeInfo.textContent = ville.ville_nom_reel;
                            
                            const addBtn = document.createElement('button');
                            addBtn.type = 'button';
                            addBtn.className = 'add-ville btn btn-sm btn-success';
                            addBtn.dataset.slug = ville.ville_slug;
                            addBtn.dataset.nomReel = ville.ville_nom_reel;
                            
                            const isAlreadySelected = selectedVilles.some(v => v.slug === ville.ville_slug);
                            addBtn.disabled = isAlreadySelected;
                            
                            if (isAlreadySelected) {
                                addBtn.innerHTML = '<i class="fas fa-check"></i> Ajoutée';
                                addBtn.classList.add('disabled-btn');
                            } else {
                                addBtn.innerHTML = '<i class="fas fa-plus"></i> Ajouter';
                            }
                            
                            li.appendChild(villeInfo);
                            li.appendChild(addBtn);
                            $villeResults.appendChild(li);
                        });
                    } else {
                        $villeResults.innerHTML = '<li class="empty-state"><i class="fas fa-info-circle"></i> Aucune ville trouvée pour ce code postal</li>';
                    }
                })
                .catch((error) => {
                    console.error('❌ Error fetching villes:', error);
                    $villeResults.innerHTML = '<li class="error-state"><i class="fas fa-exclamation-triangle"></i> Erreur lors du chargement</li>';
                });
            }, 300);
        } else {
            $villeResults.innerHTML = '<li class="empty-state">Entrez un code postal pour rechercher des villes</li>';
        }
    });

    // Add ville to selected list
    $villeResults.addEventListener('click', function (e) {
        if (e.target.classList.contains('add-ville') && !e.target.disabled) {
            const slug = e.target.dataset.slug;
            const nomReel = e.target.dataset.nomReel;
            
            if (!selectedVilles.some(v => v.slug === slug)) {
                selectedVilles.push({ slug, nom_reel: nomReel });
                renderSelectedVilles();
                updateVilleResultsButtons();
            } else {
                console.warn('⚠️ Ville already selected:', slug);
            }
        }
    });

    // Remove ville from selected list
    $selectedVillesList.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-ville') || e.target.parentElement.classList.contains('remove-ville')) {
            const btn = e.target.classList.contains('remove-ville') ? e.target : e.target.parentElement;
            const slug = btn.dataset.slug;
            selectedVilles = selectedVilles.filter(v => v.slug !== slug);
            renderSelectedVilles();
            updateVilleResultsButtons();
        }
    });

    function renderSelectedVilles() {
        // Render selected villes list
        $selectedVillesList.innerHTML = '';
        
        if (selectedVilles.length === 0) {
            $selectedVillesList.innerHTML = '<li class="empty-state">Aucune ville sélectionnée</li>';
        } else {
            selectedVilles.forEach(ville => {
                const li = document.createElement('li');
                li.className = 'ville-item selected-item';
                
                const villeInfo = document.createElement('span');
                villeInfo.className = 'ville-name';
                villeInfo.innerHTML = `<i class="fas fa-map-marker-alt"></i> ${ville.nom_reel}`;
                
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'remove-ville btn btn-sm btn-danger';
                removeBtn.dataset.slug = ville.slug;
                removeBtn.innerHTML = '<span class="material-icons" style="font-size:16px; vertical-align:middle;">remove_circle</span>';
                removeBtn.title = 'Supprimer cette ville';
                
                li.appendChild(villeInfo);
                li.appendChild(removeBtn);
                $selectedVillesList.appendChild(li);
            });
        }
        
        // Update count badge
        $villeCount.textContent = selectedVilles.length;
        
        // Update hidden inputs
        $selectedVillesHiddenInputs.innerHTML = '';
        selectedVilles.forEach(ville => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'villes[]';
            input.value = ville.slug;
            $selectedVillesHiddenInputs.appendChild(input);
        });
    }

    // Update villeResults buttons after add/remove
    function updateVilleResultsButtons() {
        Array.from($villeResults.querySelectorAll('.add-ville')).forEach(btn => {
            const isSelected = selectedVilles.some(v => v.slug === btn.dataset.slug);
            btn.disabled = isSelected;
            if (isSelected) {
                btn.innerHTML = '<i class="fas fa-check"></i> Ajoutée';
                btn.classList.add('disabled-btn');
            } else {
                btn.innerHTML = '<i class="fas fa-plus"></i> Ajouter';
                btn.classList.remove('disabled-btn');
            }
        });
    }

    // Debug form submission
    document.querySelector('.preferences-form').addEventListener('submit', function (e) {
        console.log('📤 Hidden inputs:', Array.from(document.querySelectorAll('input[name="villes[]"]')).map(i => i.value));
    });

    // Form validation before submission
    document.querySelector('#preferencesForm').addEventListener('submit', function (e) {
        const selectedVillesCount = selectedVilles.length;
        const selectedCategoriesCount = document.querySelectorAll('input[name="categories[]"]:checked').length;
        
        if (selectedVillesCount === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins une ville.');
            return false;
        }
        
        if (selectedCategoriesCount === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins une catégorie.');
            return false;
        }
        
        console.log('📤 Form submitting with:');
        console.log('  - Villes:', selectedVillesCount, Array.from(document.querySelectorAll('input[name="villes[]"]')).map(i => i.value));
        console.log('  - Categories:', selectedCategoriesCount, Array.from(document.querySelectorAll('input[name="categories[]"]:checked')).map(i => i.value));
    });
</script>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>