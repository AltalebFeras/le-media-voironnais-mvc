<h1>Modifier l'association</h1>

<div class="row">
    <div class="col-md-8">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="/association/modifier/<?= $association->getIdAssociation() ?>" method="post" enctype="multipart/form-data">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom de l'association *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($association->getName()) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($association->getDescription() ?? '') ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Adresse</label>
                        <input type="text" class="form-control" id="address" name="address" 
                               value="<?= htmlspecialchars($association->getAddress() ?? '') ?>">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?= htmlspecialchars($association->getPhone() ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($association->getEmail() ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="website" class="form-label">Site web</label>
                        <input type="url" class="form-control" id="website" name="website" 
                               placeholder="https://example.com" 
                               value="<?= htmlspecialchars($association->getWebsite() ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isActive" name="isActive" 
                                   <?= $association->getIsActive() ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isActive">
                                Association active
                            </label>
                            <div class="form-text">Une association inactive ne sera pas visible publiquement</div>
                        </div>
                    </div>

                    <hr>

                    <?php if ($association->getLogoPath()): ?>
                        <div class="mb-3">
                            <label class="form-label">Logo actuel</label>
                            <div>
                                <img src="<?= $association->getLogoPath() ?>" alt="Logo actuel" class="img-thumbnail mb-2" style="max-height: 100px;">
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="logo" class="form-label">Nouveau logo</label>
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                        <div class="form-text">Formats acceptés : JPG, PNG, GIF. Max 5MB.</div>
                    </div>

                    <?php if ($association->getBannerPath()): ?>
                        <div class="mb-3">
                            <label class="form-label">Bannière actuelle</label>
                            <div>
                                <img src="<?= $association->getBannerPath() ?>" alt="Bannière actuelle" class="img-thumbnail mb-2" style="max-height: 150px;">
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="banner" class="form-label">Nouvelle bannière</label>
                        <input type="file" class="form-control" id="banner" name="banner" accept="image/*">
                        <div class="form-text">Formats acceptés : JPG, PNG, GIF. Max 5MB.</div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="/mes-associations" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
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
                <p>Téléchargez de nouvelles images uniquement si vous souhaitez les remplacer.</p>
            </div>
        </div>
    </div>
</div>
