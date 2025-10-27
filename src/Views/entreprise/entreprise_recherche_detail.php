<?php
$title = htmlspecialchars($entreprise['name']);
$description = $entreprise['description'] ? substr(strip_tags($entreprise['description']), 0, 160) : 'Entreprise sur Le Média Voironnais';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Enterprise Banner -->
            <?php if ($entreprise['bannerPath']): ?>
                <div class="enterprise-banner mb-4">
                    <img src="<?= htmlspecialchars($entreprise['bannerPath']) ?>" 
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
                                <img src="<?= $entreprise['logoPath'] ?? DOMAIN . HOME_URL . 'assets/images/default-company.png' ?>" 
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
                        </div>
                    </div>

                    <!-- Realisations -->
                    <?php if (!empty($realisations)): ?>
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h4 class="mb-0">
                                    <i class="material-icons me-2">work</i>
                                    Nos réalisations
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach ($realisations as $realisation): ?>
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100">
                                                <?php if ($realisation['mainImage']): ?>
                                                    <img src="<?= htmlspecialchars($realisation['mainImage']) ?>" 
                                                         class="card-img-top" 
                                                         alt="<?= htmlspecialchars($realisation['title']) ?>"
                                                         style="height: 200px; object-fit: cover;">
                                                <?php endif; ?>
                                                <div class="card-body">
                                                    <h5 class="card-title"><?= htmlspecialchars($realisation['title']) ?></h5>
                                                    <?php if ($realisation['description']): ?>
                                                        <p class="card-text text-muted">
                                                            <?= htmlspecialchars(substr($realisation['description'], 0, 100)) ?>...
                                                        </p>
                                                    <?php endif; ?>
                                                    <?php if ($realisation['dateRealized']): ?>
                                                        <small class="text-muted">
                                                            <i class="material-icons me-1" style="font-size: 16px;">schedule</i>
                                                            <?= date('M Y', strtotime($realisation['dateRealized'])) ?>
                                                        </small>
                                                    <?php endif; ?>
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
                                   class="btn btn-outline-secondary w-100">
                                    <i class="material-icons me-2">language</i>
                                    Visiter le site
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
