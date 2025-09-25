<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/tous_les_utilisateurs.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar_admin.php'; ?>

<main>
    <div class="container py-4">
        <h1>Demandes d'activation d'entreprise</h1>
        <?php include_once __DIR__ . '/../includes/messages.php'; ?>

        <?php
        $totalRequests = isset($totalRequests) ? (int)$totalRequests : null;
        ?>
        <?php if ($totalRequests !== null): ?>
            <p class="text-muted" style="margin-top:0.25rem;">Total demandes : <?= $totalRequests; ?></p>
        <?php endif; ?>

        <?php
        $requests = $allRequests ?? [];
        ?>

        <?php if (empty($requests)): ?>
            <p>Aucune demande d'activation trouvée.</p>
        <?php else: ?>
            <div class="users-list">
                <?php foreach ($requests as $request): ?>
                    <?php
                    $id = $request['idEntreprise'] ?? '';
                    $uiid = $request['uiid'] ?? '';
                    $name = $request['name'] ?? '';
                    $email = $request['email'] ?? '';
                    $phone = $request['phone'] ?? '';
                    $address = $request['address'] ?? '';
                    $requestDate = $request['requestDate'] ?? '';
                    $createdAt = $request['createdAt'] ?? '';
                    $logo = !empty($request['logoPath']) ? DOMAIN . HOME_URL . $request['logoPath'] : (DOMAIN . HOME_URL . 'assets/images/uploads/logos/default_logo.png');
                    ?>
                    <article class="card user-card" role="article" aria-labelledby="request-<?= $id; ?>">
                        <div class="user-header">
                            <span class="avatar" aria-hidden="true">
                                <img src="<?= $logo; ?>" alt="Logo <?= $name; ?>">
                            </span>
                            <div class="user-title">
                                <span class="user-name" id="request-<?= $id; ?>"><?= $name; ?></span>
                                <span class="status-dot status-offline" title="En attente d'activation" aria-hidden="false"></span>
                            </div>
                        </div>
                        
                        <div class="email"><strong>Email:</strong> <?= $email; ?></div>
                        <?php if ($phone): ?>
                            <div class="phone"><strong>Téléphone:</strong> <?= $phone; ?></div>
                        <?php endif; ?>
                        <?php if ($address): ?>
                            <div class="address"><strong>Adresse:</strong> <?= $address; ?></div>
                        <?php endif; ?>
                        <div class="created">Demande le: <?= (new DateTime($requestDate))->format('d/m/Y à H:i'); ?></div>
                        <div class="created">Créée le: <?= (new DateTime($createdAt))->format('d/m/Y à H:i'); ?></div>

                        <div class="actions" style="display: flex; gap: 10px;">
                            <form method="POST" action="<?= HOME_URL; ?>admin/toutes_demandes_dactivation_entreprise?action=accepter&entreprise_uiid=<?= $uiid; ?>" style="display: inline;">
                                <button type="submit" class="btn btn-success" onclick="return confirm('Êtes-vous sûr de vouloir accepter cette demande ?')">
                                    Accepter
                                </button>
                            </form>
                            <form method="POST" action="<?= HOME_URL; ?>admin/toutes_demandes_dactivation_entreprise?action=refuser&entreprise_uiid=<?= $uiid; ?>" style="display: inline;">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir refuser cette demande ?')">
                                    Refuser
                                </button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php include_once __DIR__ . '/../includes/pagination.php'; ?>
        <?php endif; ?>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>