<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<?php include_once __DIR__ . '/../includes/messages.php'; ?>

<main>
    <div class="flex-row align-items-center mb">
        <h1>Mes événements</h1>
        <a href="<?= HOME_URL ?>evenement/ajouter" class="btn">
            <span class="material-icons">add</span>
            Créer un événement
        </a>
    </div>

    <?php if (empty($evenements)): ?>
        <div class="card max-width-50">
            <h3>Aucun événement créé</h3>
            <p>Vous n'avez pas encore créé d'événement. Commencez par créer votre premier événement !</p>
            <a href="<?= HOME_URL ?>evenement/ajouter" class="btn">Créer mon premier événement</a>
        </div>
    <?php else: ?>
        <div class="dashboard-grid">
            <?php foreach ($evenements as $evenement): ?>
                <div class="card">
                    <?php if ($evenement['imagePath']): ?>
                        <img src="<?= htmlspecialchars($evenement['imagePath']) ?>" alt="<?= htmlspecialchars($evenement['title']) ?>">
                    <?php elseif ($evenement['bannerPath']): ?>
                        <img src="<?= htmlspecialchars($evenement['bannerPath']) ?>" alt="<?= htmlspecialchars($evenement['title']) ?>">
                    <?php endif; ?>

                    <h3><?= htmlspecialchars($evenement['title']) ?></h3>

                    <div class="event-meta">
                        <p><strong>Date:</strong> <?= date('d/m/Y H:i', strtotime($evenement['eventDate'])) ?></p>
                        <p><strong>Lieu:</strong> <?= htmlspecialchars($evenement['address']) ?></p>
                        <p><strong>Ville:</strong> <?= htmlspecialchars($evenement['ville_nom_reel']) ?></p>
                        <p><strong>Catégorie:</strong> <?= htmlspecialchars($evenement['category_name']) ?></p>
                        <?php if ($evenement['association_name']): ?>
                            <p><strong>Association:</strong> <?= htmlspecialchars($evenement['association_name']) ?></p>
                        <?php endif; ?>
                        <p><strong>Statut:</strong>
                            <span class="status-badge status-<?= $evenement['status'] ?>">
                                <?= ucfirst($evenement['status']) ?>
                            </span>
                        </p>
                        <?php if ($evenement['maxParticipants']): ?>
                            <p><strong>Participants:</strong> <?= $evenement['currentParticipants'] ?>/<?= $evenement['maxParticipants'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="event-actions flex-row">
                        <a href="<?= HOME_URL ?>mes_evenements?action=voir&id=<?= $evenement['idEvenement'] ?>" class="btn btn-info">
                            <span class="material-icons">visibility</span>
                            Voir
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<style>
    .event-meta p {
        margin: 0.25rem 0;
        font-size: 0.9rem;
    }

    .event-actions {
        margin-top: 1rem;
        gap: 0.5rem;
        justify-content: flex-start;
        flex-wrap: wrap;
    }

    .event-actions .btn {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .status-badge {
        padding: 0.2rem 0.5rem;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-brouillon {
        background: #fff3cd;
        color: #856404;
    }

    .status-actif {
        background: #d4edda;
        color: #155724;
    }

    .status-suspendu {
        background: #f8d7da;
        color: #721c24;
    }
</style>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>