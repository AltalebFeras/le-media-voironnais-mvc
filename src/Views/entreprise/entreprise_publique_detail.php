<?php
$title = htmlspecialchars($entreprise['name']);
$description = $entreprise['description'] ? substr(strip_tags($entreprise['description']), 0, 160) : 'Entreprise sur Le Média Voironnais';
?>

<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Enterprise Banner -->
            <?php if ($entreprise['bannerPath']): ?>
                <div class="enterprise-banner mb-4">
                    <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($entreprise['bannerPath']) ?>" 
                         alt="Bannière <?= htmlspecialchars($entreprise['name']) ?>" 
                         class="img-fluid w-100" 
                         style="height: 300px; object-fit: cover; border-radius: 8px;">
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Enterprise Details -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-4">
                                <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($entreprise['logoPath']) ?>" 
                                     alt="Logo <?= htmlspecialchars($entreprise['name']) ?>" 
                                     class="rounded me-4" 
                                     style="width: 100px; height: 100px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h1 class="h2 mb-2"><?= htmlspecialchars($entreprise['name']) ?></h1>
                                            <?php if ($entreprise['isPartner']): ?>
                                                <span class="badge bg-warning text-dark mb-2">
                                                    <i class="material-icons me-1" style="font-size: 14px;">star</i>
                                                    Partenaire
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($entreprise['ville_nom_reel']): ?>
                                                <div class="text-muted mt-2">
                                                    <i class="material-icons me-1" style="font-size: 16px;">location_on</i>
                                                    <?= htmlspecialchars($entreprise['ville_nom_reel']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">
                                                Membre depuis <?= date('M Y', strtotime($entreprise['createdAt'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="row mb-4">
                                <?php if ($entreprise['address']): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="material-icons text-primary me-3">location_on</i>
                                            <div>
                                                <strong>Adresse</strong><br>
                                                <span class="text-muted"><?= htmlspecialchars($entreprise['address']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($entreprise['phone']): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="material-icons text-primary me-3">phone</i>
                                            <div>
                                                <strong>Téléphone</strong><br>
                                                <a href="tel:<?= htmlspecialchars($entreprise['phone']) ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($entreprise['phone']) ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($entreprise['email']): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="material-icons text-primary me-3">email</i>
                                            <div>
                                                <strong>Email</strong><br>
                                                <a href="mailto:<?= htmlspecialchars($entreprise['email']) ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($entreprise['email']) ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($entreprise['website']): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="material-icons text-primary me-3">language</i>
                                            <div>
                                                <strong>Site web</strong><br>
                                                <a href="<?= htmlspecialchars($entreprise['website']) ?>" 
                                                   target="_blank" 
                                                   class="text-decoration-none">
                                                    <?= htmlspecialchars($entreprise['website']) ?>
                                                    <i class="material-icons ms-1" style="font-size: 16px;">open_in_new</i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($entreprise['siret']): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="material-icons text-primary me-3">business</i>
                                            <div>
                                                <strong>SIRET</strong><br>
                                                <span class="text-muted"><?= htmlspecialchars($entreprise['siret']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Description -->
                            <?php if ($entreprise['description']): ?>
                                <div class="mb-4">
                                    <h4>À propos</h4>
                                    <div class="text-content">
                                        <?= nl2br(htmlspecialchars($entreprise['description'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Creator Info -->
                            <?php if ($entreprise['creator_firstName'] && $entreprise['creator_lastName']): ?>
                                <div class="border-top pt-3">
                                    <h5>Créé par</h5>
                                    <div class="d-flex align-items-center">
                                        <i class="material-icons me-3 text-primary">person</i>
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="<?= HOME_URL . 'profil/' . $entreprise['creator_slug'] ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($entreprise['creator_firstName'] . ' ' . $entreprise['creator_lastName']) ?>
                                                </a>
                                            </h6>
                                            <small class="text-muted">Propriétaire</small>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Événements -->
                    <?php if (!empty($entreprise['evenements'])): ?>
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <h4 class="mb-0">
                                    <i class="material-icons me-2">event</i>
                                    Événements organisés
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach ($entreprise['evenements'] as $evenement): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100">
                                                <?php if ($evenement['evenement_bannerPath']): ?>
                                                    <img src="<?= BASE_URL . HOME_URL . htmlspecialchars($evenement['evenement_bannerPath']) ?>" 
                                                         class="card-img-top" 
                                                         alt="<?= htmlspecialchars($evenement['evenement_title']) ?>"
                                                         style="height: 150px; object-fit: cover;">
                                                <?php endif; ?>
                                                <div class="card-body">
                                                    <h6 class="card-title">
                                                        <a href="<?= HOME_URL . 'evenements/' . htmlspecialchars($evenement['evenement_slug']) ?>" class="text-decoration-none">
                                                            <?= htmlspecialchars($evenement['evenement_title']) ?>
                                                        </a>
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Réalisations -->
                    <?php if (!empty($entreprise['realisations'])): ?>
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h4 class="mb-0">
                                    <i class="material-icons me-2">work</i>
                                    Nos réalisations
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach ($entreprise['realisations'] as $realisation): ?>
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h5 class="card-title">
                                                        <?= htmlspecialchars($realisation['realisation_title']) ?>
                                                    </h5>
                                                    <small class="text-muted">
                                                        <i class="material-icons me-1" style="font-size: 16px;">work</i>
                                                        Réalisation
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <!-- Contact Card -->
                    <div class="card shadow-sm sticky-top" style="top: 100px;">
                        <div class="card-header text-center">
                            <h5 class="mb-0">Contacter l'entreprise</h5>
                        </div>
                        <div class="card-body text-center">
                            <?php if ($entreprise['phone']): ?>
                                <a href="tel:<?= htmlspecialchars($entreprise['phone']) ?>" 
                                   class="btn btn-primary btn-lg w-100 mb-3">
                                    <i class="material-icons me-2">phone</i>
                                    Appeler
                                </a>
                            <?php endif; ?>

                            <?php if ($entreprise['email']): ?>
                                <a href="mailto:<?= htmlspecialchars($entreprise['email']) ?>" 
                                   class="btn btn-outline-primary w-100 mb-3">
                                    <i class="material-icons me-2">email</i>
                                    Envoyer un email
                                </a>
                            <?php endif; ?>

                            <?php if ($entreprise['website']): ?>
                                <a href="<?= htmlspecialchars($entreprise['website']) ?>" 
                                   target="_blank" 
                                   class="btn btn-outline-secondary w-100 mb-3">
                                    <i class="material-icons me-2">language</i>
                                    Visiter le site
                                </a>
                            <?php endif; ?>

                            <div class="d-flex gap-2 justify-content-center">
                                <button class="btn btn-outline-secondary" title="Partager">
                                    <i class="material-icons">share</i>
                                </button>
                                <button class="btn btn-outline-secondary" title="Signaler">
                                    <i class="material-icons">flag</i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Enterprise Stats -->
                    <div class="card shadow-sm mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">Statistiques</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="border rounded p-2">
                                        <h5 class="mb-0 text-primary"><?= count($entreprise['evenements']) ?></h5>
                                        <small class="text-muted">Événements</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="border rounded p-2">
                                        <h5 class="mb-0 text-success"><?= count($entreprise['realisations']) ?></h5>
                                        <small class="text-muted">Réalisations</small>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <small class="text-muted">
                                    Actif depuis <?= date('M Y', strtotime($entreprise['createdAt'])) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
