<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<main>
    <div class="flex-row justify-content-between">
        <h1>Ajouter une association</h1>
        <a href="<?=HOME_URL . 'mes_associations' ?>" class="">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
        </a>
    </div>
    <div class="flex-row">
        <div class="max-width-50">
            <?php include_once __DIR__ . '/../includes/messages.php'; ?>
            <form action="/association/ajouter" method="post" enctype="multipart/form-data">
                <div class="card">
                    <div>
                        <div>
                            <label for="name">Nom de l'association *</label>
                            <input type="text" id="name" name="name"
                                value="<?= isset($formData['name']) ? htmlspecialchars($formData['name']) : '' ?>" required>
                        </div>

                        <div>
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4"><?= isset($formData['description']) ? htmlspecialchars($formData['description']) : '' ?></textarea>
                        </div>

                        <div>
                            <label for="address">Adresse</label>
                            <input type="text" id="address" name="address"
                                value="<?= isset($formData['address']) ? htmlspecialchars($formData['address']) : '' ?>">
                        </div>

                        <!-- New postal code and city fields -->
                        <div class="flex-row">
                            <div class="max-width-50">
                                <div>
                                    <label for="codePostal">Code postal</label>
                                    <input type="text" id="codePostal" name="codePostal" maxlength="5" 
                                           value="<?= isset($formData['codePostal']) ? htmlspecialchars($formData['codePostal']) : '' ?>">
                                    <small class="text-muted">Saisissez 5 chiffres pour voir les villes</small>
                                </div>
                            </div>
                            <div class="max-width-50">
                                <div>
                                    <label for="ville">Ville</label>
                                    <select id="ville" name="ville" disabled>
                                        <option value="">Sélectionnez une ville</option>
                                    </select>
                                    <input type="hidden" id="idVille" name="idVille" 
                                           value="<?= isset($formData['idVille']) ? htmlspecialchars($formData['idVille']) : '' ?>">
                                </div>
                            </div>
                        </div>
                        <!-- End of new fields -->

                        <div class="flex-row">
                            <div class="max-width-50">
                                <div>
                                    <label for="phone">Téléphone</label>
                                    <input type="tel" id="phone" name="phone"
                                        value="<?= isset($formData['phone']) ? htmlspecialchars($formData['phone']) : '' ?>">
                                </div>
                            </div>
                            <div class="max-width-50">
                                <div>
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email"
                                        value="<?= isset($formData['email']) ? htmlspecialchars($formData['email']) : '' ?>">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="website">Site web</label>
                            <input type="url" id="website" name="website"
                                placeholder="https://example.com"
                                value="<?= isset($formData['website']) ? htmlspecialchars($formData['website']) : '' ?>">
                        </div>

                        <div>
                            <label for="logo">Logo</label>
                            <input type="file" id="logo" name="logo" accept="image/*">
                            <small class="text-muted">Formats acceptés : JPG, PNG, GIF. Max 5MB.</small>
                        </div>

                        <div>
                            <label for="banner">Bannière</label>
                            <input type="file" id="banner" name="banner" accept="image/*">
                            <small class="text-muted">Formats acceptés : JPG, PNG, GIF. Max 5MB.</small>
                        </div>
                    </div>

                    <div class="flex-row justify-content-between mt">
                        <a href="/mes-associations" class="btn">Annuler</a>
                        <button type="submit" class="btn">Créer l'association</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="max-width-33">
            <div class="card">
                <h3>Informations</h3>
                <p>Les champs marqués d'un * sont obligatoires.</p>
                <p>Le logo et la bannière sont optionnels mais recommandés pour personnaliser votre association.</p>
                <p>En tant que créateur, vous serez automatiquement administrateur de cette association.</p>
            </div>
        </div>
    </div>

    <!-- Add JavaScript for handling city selection -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const codePostalInput = document.getElementById('codePostal');
            const villeSelect = document.getElementById('ville');
            const idVilleInput = document.getElementById('idVille');

            codePostalInput.addEventListener('input', function() {
                const codePostal = this.value.trim();
                
                // Check if we have exactly 5 digits
                if (codePostal.length === 5 && /^\d{5}$/.test(codePostal)) {
                    fetchVilles(codePostal);
                } else {
                    // Reset the city select if postal code is not valid
                    resetVilleSelect();
                }
            });

            villeSelect.addEventListener('change', function() {
                // Update the hidden idVille field when a city is selected
                const selectedOption = this.options[this.selectedIndex];
                idVilleInput.value = selectedOption.value;
            });

            async function fetchVilles(codePostal) {
                // Show loading state
                villeSelect.innerHTML = '<option value="">Chargement...</option>';
                villeSelect.disabled = true;

                try {
                    const response = await fetch('http://le-media-voironnais/villes', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            codePostal: codePostal
                        })
                    });

                    const data = await response.json();
                    populateVilleSelect(data);
                } catch (error) {
                    console.error('Error fetching cities:', error);
                    villeSelect.innerHTML = '<option value="">Erreur lors du chargement</option>';
                    villeSelect.disabled = true;
                }
            }

            function populateVilleSelect(response) {
                villeSelect.innerHTML = '<option value="">Sélectionnez une ville</option>';
                
                // Check if the response has success and data
                if (response && response.succes && response.data && response.data.length > 0) {
                    response.data.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.idVille;
                        option.textContent = city.ville_nom_reel;
                        villeSelect.appendChild(option);
                    });
                    villeSelect.disabled = false;
                    
                    // If there's only one city, select it by default
                    if (response.data.length === 1) {
                        villeSelect.value = response.data[0].idVille;
                        idVilleInput.value = response.data[0].idVille;
                    }
                } else {
                    villeSelect.innerHTML = '<option value="">Aucune ville trouvée</option>';
                    villeSelect.disabled = true;
                }
            }

            function resetVilleSelect() {
                villeSelect.innerHTML = '<option value="">Sélectionnez une ville</option>';
                villeSelect.disabled = true;
                idVilleInput.value = '';
            }
        });
    </script>
</main>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>