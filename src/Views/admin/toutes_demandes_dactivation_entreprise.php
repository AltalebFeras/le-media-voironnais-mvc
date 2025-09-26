<?php include_once __DIR__ . '/../includes/header.php'; ?>
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
                    $uiid = $request['uiid'] ?? '';
                    $name = $request['name'] ?? '';
                    $email = $request['email'] ?? '';
                    $phone = $request['phone'] ?? '';
                    $address = $request['address'] ?? '';
                    $requestDate = $request['requestDate'] ?? '';
                    $createdAt = $request['createdAt'] ?? '';
                    $logo = !empty($request['logoPath']) ? DOMAIN . HOME_URL . $request['logoPath'] : (DOMAIN . HOME_URL . 'assets/images/uploads/logos/default_logo.png');
                    ?>
                    <article class="card user-card" role="article" aria-labelledby="request-<?= $uiid; ?>">
                        <div class="user-header">
                            <span class="avatar" aria-hidden="true">
                                <img src="<?= $logo; ?>" alt="Logo <?= htmlspecialchars($name); ?>">
                            </span>
                            <div class="user-title">
                                <span class="user-name" id="request-<?= $uiid; ?>"><?= htmlspecialchars($name); ?></span>
                                <span class="status-dot status-offline" title="En attente d'activation" aria-label="En attente d'activation"></span>
                            </div>
                        </div>

                        <div class="email"><strong>Email:</strong> <?= htmlspecialchars($email); ?></div>
                        <?php if ($phone): ?>
                            <div class="phone"><strong>Téléphone:</strong> <?= htmlspecialchars($phone); ?></div>
                        <?php endif; ?>
                        <?php if ($address): ?>
                            <div class="address"><strong>Adresse:</strong> <?= htmlspecialchars($address); ?></div>
                        <?php endif; ?>
                        <div class="created">Demande le: <?= (new DateTime($requestDate))->format('d/m/Y à H:i'); ?></div>
                        <div class="created">Créée le: <?= (new DateTime($createdAt))->format('d/m/Y à H:i'); ?></div>

                        <div class="actions" style="display: flex; gap: 10px;">
                            <button type="button" class="btn btn-danger" onclick="document.getElementById('popup_refuse_<?= $uiid; ?>').style.display='flex'">
                                Refuser
                            </button>
                            <button type="button" class="btn btn-success" onclick="document.getElementById('popup_accept_<?= $uiid; ?>').style.display='flex'">
                                Accepter
                            </button>
                        </div>
                    </article>
                    <!-- Modal de confirmation de refusement -->
                    <div id="popup_refuse_<?= $uiid; ?>" class="d-none popup">
                        <div class="card" style="max-width:500px;">
                            <form method="post" action="<?= HOME_URL . 'admin/toutes_demandes_dactivation_entreprise'; ?>">
                                <input type="hidden" name="entreprise_uiid" value="<?= $uiid; ?>">
                                <input type="hidden" name="action" value="refuse">

                                <div style="margin-bottom: 10px;">
                                    <label for="message_refuse_<?= $uiid; ?>">Message :</label>
                                    <textarea name="message" id="message_refuse_<?= $uiid; ?>" placeholder="Message optionnel pour l'entreprise..." style="width: 100%; min-height: 60px;"></textarea>
                                </div>

                                <div style="display: flex; gap: 10px;">
                                    <button type="submit" name="refuse" value="1" class="btn btn-danger">
                                        Refuser
                                    </button>

                                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('popup_refuse_<?= $uiid; ?>').style.display='none'">
                                        Annuler
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Modal de confirmation d'acceptation -->
                    <div id="popup_accept_<?= $uiid; ?>" class="d-none popup">
                        <div class="card" style="max-width:500px;">
                            <form method="post" action="<?= HOME_URL . 'admin/toutes_demandes_dactivation_entreprise'; ?>">
                                <input type="hidden" name="entreprise_uiid" value="<?= $uiid; ?>">
                                <input type="hidden" name="action" value="accept">

                                <div style="margin-bottom: 10px;">
                                    <label for="message_accept_<?= $uiid; ?>">Message :</label>
                                    <textarea name="message" id="message_accept_<?= $uiid; ?>" placeholder="Message optionnel pour l'entreprise..." style="width: 100%; min-height: 60px;"></textarea>
                                </div>

                                <div style="display: flex; gap: 10px;">
                                    <button type="submit" name="accept" value="1" class="btn btn-success">
                                        Accepter
                                    </button>

                                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('popup_accept_<?= $uiid; ?>').style.display='none'">
                                        Annuler
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php include_once __DIR__ . '/../includes/pagination.php'; ?>
        <?php endif; ?>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>