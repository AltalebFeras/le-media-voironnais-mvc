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
            <label id="villeResultsLabel">Villes trouvées :</label>
            <ul id="villeResults" aria-labelledby="villeResultsLabel"></ul>
        </div>
        <div class="form-group">
            <label id="selectedVillesListLabel">Villes sélectionnées :</label>
            <ul id="selectedVillesList" aria-labelledby="selectedVillesListLabel"></ul>
        </div>
        <input type="hidden" name="villes[]" id="selectedVillesInput">
        <div class="form-group">
            <label for="categories">Sélectionnez vos catégories d'événements préférées :</label>
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
    // --- Ville selection logic ---
    const BASE_URL = "<?= BASE_URL ?>";
    const HOME_URL = "<?= HOME_URL ?>";
    const $codePostalInput = document.getElementById('codePostalInput');
    const $villeResults = document.getElementById('villeResults');
    const $selectedVillesList = document.getElementById('selectedVillesList');
    const $selectedVillesInput = document.getElementById('selectedVillesInput');

    let selectedVilles = [];
    let fetchTimeout = null;

    $codePostalInput.addEventListener('input', function () {
        const codePostal = $codePostalInput.value.trim();
        if (codePostal.length >= 3 && /^\d{3,5}$/.test(codePostal)) {
            clearTimeout(fetchTimeout);
            fetchTimeout = setTimeout(() => {
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
                            li.textContent = ville.ville_nom_reel + ' ';
                            const addBtn = document.createElement('button');
                            addBtn.type = 'button';
                            addBtn.textContent = 'Ajouter';
                            addBtn.className = 'add-ville btn btn-sm btn-success';
                            addBtn.dataset.slug = ville.ville_slug;
                            addBtn.dataset.nomReel = ville.ville_nom_reel;
                            // Disable button if already selected
                            addBtn.disabled = selectedVilles.some(v => v.slug === ville.ville_slug);
                            li.appendChild(addBtn);
                            $villeResults.appendChild(li);
                        });
                    } else {
                        $villeResults.innerHTML = '<li>Aucune ville trouvée</li>';
                    }
                })
                .catch(() => {
                    $villeResults.innerHTML = '<li>Erreur lors du chargement</li>';
                });
            }, 300); // debounce
        } else {
            $villeResults.innerHTML = '';
        }
    });

    // Add ville to selected list
    $villeResults.addEventListener('click', function (e) {
        if (e.target.classList.contains('add-ville')) {
            const slug = e.target.dataset.slug;
            const nomReel = e.target.dataset.nomReel;
            if (!selectedVilles.some(v => v.slug === slug)) {
                selectedVilles.push({ slug, nom_reel: nomReel });
                renderSelectedVilles();
                // After adding, re-render villeResults to update button states
                renderVilleResultsButtons();
            }
        }
    });

    // Remove ville from selected list
    $selectedVillesList.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-ville')) {
            const slug = e.target.dataset.slug;
            selectedVilles = selectedVilles.filter(v => v.slug !== slug);
            renderSelectedVilles();
            // After removing, re-render villeResults to update button states
            renderVilleResultsButtons();
        }
    });

    function renderSelectedVilles() {
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
        // Update hidden input with array of slugs
        $selectedVillesInput.value = selectedVilles.map(v => v.slug);
    }

    // Update villeResults buttons after add/remove
    function renderVilleResultsButtons() {
        Array.from($villeResults.querySelectorAll('.add-ville')).forEach(btn => {
            btn.disabled = selectedVilles.some(v => v.slug === btn.dataset.slug);
        });
    }

    // On form submit, ensure villes[] is sent as array
    document.querySelector('.preferences-form').addEventListener('submit', function (e) {
        document.querySelectorAll('input[name="villes[]"]').forEach(el => el.remove());
        selectedVilles.forEach(ville => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'villes[]';
            input.value = ville.slug;
            this.appendChild(input);
        });
    });
</script>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>