<h1>Ajouter une association</h1>

<div class="row">
    <div class="col-md-8">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="/association/ajouter" method="post" enctype="multipart/form-data">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom de l'association *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= isset($formData['name']) ? htmlspecialchars($formData['name']) : '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= isset($formData['description']) ? htmlspecialchars($formData['description']) : '' ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Adresse</label>
                        <input type="text" class="form-control" id="address" name="address" 
                               value="<?= isset($formData['address']) ? htmlspecialchars($formData['address']) : '' ?>">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?= isset($formData['phone']) ? htmlspecialchars($formData['phone']) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= isset($formData['email']) ? htmlspecialchars($formData['email']) : '' ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="website" class="form-label">Site web</label>
                        <input type="url" class="form-control" id="website" name="website" 
                               placeholder="https://example.com" 
                               value="<?= isset($formData['website']) ? htmlspecialchars($formData['website']) : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo</label>
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                        <div class="form-text">Formats acceptés : JPG, PNG, GIF. Max 5MB.</div>
                    </div>

                    <div class="mb-3">
                        <label for="banner" class="form-label">Bannière</label>
                        <input type="file" class="form-control" id="banner" name="banner" accept="image/*">
                        <div class="form-text">Formats acceptés : JPG, PNG, GIF. Max 5MB.</div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="/mes-associations" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Créer l'association</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Informations
            </div>
            <div class="card-body">
                <p>Les champs marqués d'un * sont obligatoires.</p>
                <p>Le logo et la bannière sont optionnels mais recommandés pour personnaliser votre association.</p>
                <p>En tant que créateur, vous serez automatiquement administrateur de cette association.</p>
            </div>
        </div>
    </div>
</div>
