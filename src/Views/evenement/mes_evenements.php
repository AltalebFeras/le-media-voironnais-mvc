<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<?php include_once __DIR__ . '/../includes/messages.php'; ?>

<main>
    <div class="flex-row align-items-center mb">
        <h1>Mes événements</h1>
        <a href="<?= HOME_URL . 'dashboard' ?>">
            <span class="material-icons btn" style="color:white;">arrow_back</span>

        </a>
    </div>
    <div>
        <a href="<?= HOME_URL ?>evenement/ajouter" class="btn linkNotDecorated">Créer un événement</a>
    </div>
    <?php if (empty($evenements)): ?>
        <div class="card max-width-50">
            <h3>Aucun événement créé</h3>
            <p>Vous n'avez pas encore créé d'événement. Commencez par créer votre premier événement !</p>
        </div>
    <?php else: ?>
        <div class="dashboard-grid">
            <?php foreach ($evenements as $evenement): ?>
                <div class="card">
                    <?php if ($evenement['bannerPath']): ?>
                        <img src="<?= $evenement['bannerPath'] ?>" alt="<?= $evenement['title'] ?>">
                    <?php endif; ?>

                    <h3><?= $evenement['title'] ?></h3>

                    <div class="event-meta">
                        <p><strong>Date:</strong> <?= date('d/m/Y H:i', strtotime($evenement['startDate'])) ?? '' ?></p>
                        <p><strong>Lieu:</strong> <?= $evenement['address'] ?? '' ?></p>
                        <p><strong>Ville:</strong> <?= $evenement['ville_nom_reel'] ?? 'Inconnu' ?></p>
                        <p><strong>Catégorie:</strong> <?= $evenement['category_name'] ?? 'Inconnue' ?></p>
                        <?php if ($evenement['association_name']): ?>
                            <p><strong>Association:</strong> <?= $evenement['association_name'] ?? 'Inconnue' ?></p>
                        <?php endif; ?>
                        <p><strong>Statut:</strong>
                            <span class="status-badge status-<?= $evenement['status'] ?? 'Inconnu' ?>">
                                <?= ucfirst($evenement['status']) ?>
                            </span>
                        </p>
                        <?php if ($evenement['maxParticipants']): ?>
                            <p><strong>Participants:</strong> <?= $evenement['currentParticipants'] ?? 0 ?>/<?= $evenement['maxParticipants'] ?? 0 ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="event-actions flex-row">
                        <a href="<?= HOME_URL ?>mes_evenements?action=voir&id=<?= $evenement['idEvenement'] ?>" class="btn btn-info linkNotDecorated">
                            Voir
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php include_once __DIR__ . '/../includes/pagination.php'; ?>
    <?php endif; ?>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>