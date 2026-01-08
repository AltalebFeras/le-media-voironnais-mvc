<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/users/mes_preferences.css' ?>">

<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="preferences-container">
    <h1 class="preferences-title">Mes Préférences</h1>
    <?php include_once __DIR__ . '/../includes/messages.php'; ?>

    <form action="<?= HOME_URL . 'mes_preferences' ?>" method="POST" class="preferences-form">
        <div class="form-group">
            <label for="codePostalInput">Code postal :</label>
            <input type="text" id="codePostalInput" maxlength="5" pattern="\d{3,5}" class="form-control" placeholder="Entrez un code postal">
        </div>
        <div class="form-group">
            <p class="form-label">Villes trouvées :</p>
            <ul id="villeResults" aria-label="Liste des villes trouvées"></ul>
        </div>
        <div class="form-group">
            <p class="form-label">Villes sélectionnées :</p>
            <ul id="selectedVillesList" aria-label="Liste des villes sélectionnées"></ul>
        </div>
        <div id="selectedVillesHiddenInputs"></div>
        <div class="form-group">
            <p class="form-label">Sélectionnez vos catégories d'événements préférées :</p>
            <div class="categories-flex">
            <?php foreach ($categories as $category): ?>
                <div class="checkbox-group">
                    <input id="category<?= $category['slug'] ?>" type="checkbox" name="categories[]" value="<?= $category['slug'] ?>">
                    <label for="category<?= $category['slug'] ?>"> <?= $category['name'] ?></label>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer mes préférences</button>
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

    let selectedVilles = [];
    let fetchTimeout = null;

    console.log('🚀 Ville selection script initialized');

    $codePostalInput.addEventListener('input', function () {
        const codePostal = $codePostalInput.value.trim();
        console.log('📝 Code postal input:', codePostal);
        
        if (codePostal.length >= 3 && /^\d{3,5}$/.test(codePostal)) {
            clearTimeout(fetchTimeout);
            fetchTimeout = setTimeout(() => {
                console.log('🔍 Fetching villes for code postal:', codePostal);
                fetch(BASE_URL + HOME_URL + 'villes', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ codePostal })
                })
                .then(res => res.json())
                .then(data => {
                    console.log('✅ Villes data received:', data);
                    $villeResults.innerHTML = '';
                    if (data && data.succes && data.data && data.data.length > 0) {
                        data.data.forEach(ville => {
                            const li = document.createElement('li');
                            li.textContent = ville.ville_nom_reel + ' ';
                            const addBtn = document.createElement('button');
                            addBtn.type = 'button';
                            addBtn.textContent = 'Ajouter';
                            addBtn.className = 'add-ville btn btn-sm btn-success';
                            addBtn.dataset.slug = ville.ville_slug;
                            addBtn.dataset.nomReel = ville.ville_nom_reel;
                            const isAlreadySelected = selectedVilles.some(v => v.slug === ville.ville_slug);
                            addBtn.disabled = isAlreadySelected;
                            if (isAlreadySelected) {
                                addBtn.textContent = 'Déjà ajoutée';
                            }
                            li.appendChild(addBtn);
                            $villeResults.appendChild(li);
                        });
                    } else {
                        $villeResults.innerHTML = '<li>Aucune ville trouvée</li>';
                    }
                })
                .catch((error) => {
                    console.error('❌ Error fetching villes:', error);
                    $villeResults.innerHTML = '<li>Erreur lors du chargement</li>';
                });
            }, 300);
        } else {
            $villeResults.innerHTML = '';
        }
    });

    // Add ville to selected list
    $villeResults.addEventListener('click', function (e) {
        if (e.target.classList.contains('add-ville') && !e.target.disabled) {
            const slug = e.target.dataset.slug;
            const nomReel = e.target.dataset.nomReel;
            console.log('➕ Adding ville:', { slug, nomReel });
            
            if (!selectedVilles.some(v => v.slug === slug)) {
                selectedVilles.push({ slug, nom_reel: nomReel });
                console.log('✅ Ville added. Current selection:', selectedVilles);
                renderSelectedVilles();
                updateVilleResultsButtons();
            } else {
                console.warn('⚠️ Ville already selected:', slug);
            }
        }
    });

    // Remove ville from selected list
    $selectedVillesList.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-ville')) {
            const slug = e.target.dataset.slug;
            console.log('➖ Removing ville:', slug);
            selectedVilles = selectedVilles.filter(v => v.slug !== slug);
            console.log('✅ Ville removed. Current selection:', selectedVilles);
            renderSelectedVilles();
            updateVilleResultsButtons();
        }
    });

    function renderSelectedVilles() {
        console.log('🔄 Rendering selected villes:', selectedVilles);
        
        // Render selected villes list
        $selectedVillesList.innerHTML = '';
        selectedVilles.forEach(ville => {
            const li = document.createElement('li');
            li.textContent = ville.nom_reel + ' ';
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.textContent = '✖';
            removeBtn.className = 'remove-ville btn btn-sm btn-danger';
            removeBtn.dataset.slug = ville.slug;
            li.appendChild(removeBtn);
            $selectedVillesList.appendChild(li);
        });
        
        // Update hidden inputs
        $selectedVillesHiddenInputs.innerHTML = '';
        selectedVilles.forEach(ville => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'villes[]';
            input.value = ville.slug;
            $selectedVillesHiddenInputs.appendChild(input);
        });
        
        console.log('✅ Hidden inputs updated:', $selectedVillesHiddenInputs.innerHTML);
    }

    // Update villeResults buttons after add/remove
    function updateVilleResultsButtons() {
        console.log('🔄 Updating ville results buttons');
        Array.from($villeResults.querySelectorAll('.add-ville')).forEach(btn => {
            const isSelected = selectedVilles.some(v => v.slug === btn.dataset.slug);
            btn.disabled = isSelected;
            btn.textContent = isSelected ? 'Déjà ajoutée' : 'Ajouter';
        });
    }

    // Debug form submission
    document.querySelector('.preferences-form').addEventListener('submit', function (e) {
        console.log('📤 Form submitting with selected villes:', selectedVilles);
        console.log('📤 Hidden inputs:', Array.from(document.querySelectorAll('input[name="villes[]"]')).map(i => i.value));
    });
</script>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>