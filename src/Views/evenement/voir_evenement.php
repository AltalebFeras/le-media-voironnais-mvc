<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<?php include_once __DIR__ . '/../includes/messages.php'; ?>

<main>
    <div class="event-header">
        <div class="flex-row align-items-center">
            <h1><?= $evenement->getTitle() ?></h1>
            <a href="<?= HOME_URL ?>mes_evenements">
            <span class="material-icons btn" style="color:white;">arrow_back</span>
            </a>
        </div>
    </div>

    <div class="event-content">
        <?php if ($evenement->getBannerPath()): ?>
            <div class="event-banner">
                <img src="<?= $evenement->getBannerPath() ?>"
                    alt="<?= $evenement->getTitle() ?>">
            </div>
        <?php endif; ?>

        <div class="event-details-grid">
            <div class="event-info card">
                <h2>Détails de l'événement</h2>

                <div class="info-item">
                    <strong>Description :</strong>
                    <p><?= nl2br($evenement->getDescription()) ?></p>
                </div>

                <?php if ($evenement->getShortDescription()): ?>
                    <div class="info-item">
                        <strong>Description courte :</strong>
                        <p><?= nl2br($evenement->getShortDescription()) ?></p>
                    </div>
                <?php endif; ?>

                <div class="info-item">
                    <strong>Date et heure :</strong>
                    <p><?= $evenement->getStartDateFormatted() ?></p>
                </div>

                <?php if ($evenement->getEndDate()): ?>
                    <div class="info-item">
                        <strong>Date de fin :</strong>
                        <p><?= date('d/m/Y H:i', strtotime($evenement->getEndDate())) ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($evenement->getRegistrationDeadline()): ?>
                    <div class="info-item">
                        <strong>Date limite d'inscription :</strong>
                        <p><?= date('d/m/Y H:i', strtotime($evenement->getRegistrationDeadline())) ?></p>
                    </div>
                <?php endif; ?>

                <div class="info-item">
                    <strong>Adresse :</strong>
                    <p><?= $evenement->getAddress() ?></p>
                    <?php if ($ville): ?>
                        <p><?= $ville['ville_code_postal'] ?> <?= $ville['ville_nom_reel'] ?></p>
                    <?php endif; ?>
                </div>

                <?php if ($evenement->getMaxParticipants()): ?>
                    <div class="info-item">
                        <strong>Participants :</strong>
                        <p><?= $evenement->getCurrentParticipants() ?> / <?= $evenement->getMaxParticipants() ?></p>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= ($evenement->getCurrentParticipants() / $evenement->getMaxParticipants()) * 100 ?>%"></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($evenement->getPrice()): ?>
                    <div class="info-item">
                        <strong>Prix :</strong>
                        <p><?= number_format($evenement->getPrice(), 2) ?> <?= $evenement->getCurrency() ?></p>
                    </div>
                <?php endif; ?>

                <div class="info-item">
                    <strong>Statut :</strong>
                    <span class="status-badge status-<?= $evenement->getStatus() ?>">
                        <?= ucfirst($evenement->getStatus()) ?>
                    </span>
                </div>

                <div class="info-item">
                    <strong>Visibilité :</strong>
                    <span class="visibility-badge <?= $evenement->getIsPublic() ? 'public' : 'private' ?>">
                        <?= $evenement->getIsPublic() ? 'Public' : 'Privé' ?>
                    </span>
                </div>

                <?php if ($evenement->getRequiresApproval()): ?>
                    <div class="info-item">
                        <strong>Inscription :</strong>
                        <span class="approval-badge">Avec approbation</span>
                    </div>
                <?php endif; ?>

                <div class="info-item">
                    <strong>Créé le :</strong>
                    <p><?= $evenement->getCreatedAtFormatted() ?></p>
                </div>

                <?php if ($evenement->getUpdatedAt()): ?>
                    <div class="info-item">
                        <strong>Modifié le :</strong>
                        <p><?= date('d/m/Y H:i', strtotime($evenement->getUpdatedAt())) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="event-meta card">
                <h3>Informations complémentaires</h3>


                <?php if ($evenement->getIdAssociation()): ?>
                    <div class="info-item">
                        <strong>Organisé par :</strong>
                        <p>Association</p>
                    </div>
                <?php else: ?>
                    <div class="info-item">
                        <strong>Organisé par :</strong>
                        <p>Particulier</p>
                    </div>
                <?php endif; ?>

                <?php if ($evenement->getBannerPath()): ?>
                    <div class="info-item">
                        <strong>Images :</strong>
                        <div class="event-images">
                            <img src="<?= $evenement->getBannerPath() ?>"
                                alt="Image de l'événement" class="event-thumb">
                        </div>
                    </div>
                <?php endif; ?>

                <div class="event-statistics">
                    <h4>Statistiques</h4>
                    <div class="stat-item">
                        <span class="stat-label">Participants inscrits :</span>
                        <span class="stat-value"><?= $evenement->getCurrentParticipants() ?? 0 ?></span>
                    </div>
                    <?php if ($evenement->getMaxParticipants()): ?>
                        <div class="stat-item">
                            <span class="stat-label">Places disponibles :</span>
                            <span class="stat-value"><?= $evenement->getMaxParticipants() - ($evenement->getCurrentParticipants() ?? 0) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- edit _ delete -->
                <?php if ($isOwner): ?>
                <div class="event-actions">
                    <a href="<?= HOME_URL ?>evenement/modifier?id=<?= $evenement->getIdEvenement() ?>" class="btn linkNotDecorated mr">
                        Modifier
                    </a>
                    <button type="button" class="btn btn-danger"
                        onclick="document.getElementById('deleteModal').style.display='flex'">
                        Supprimer l'événement
                    </button>
                </div>
                    <div id="deleteModal" class="d-none" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; display:none; justify-content:center; align-items:center;">
                        <div class="card" style="max-width:500px;">
                            <h3>Confirmer la suppression</h3>
                            <button type="button" onclick="document.getElementById('deleteModal').style.display='none'" style="position:absolute; right:10px; top:10px; background:none; border:none; font-size:18px; cursor:pointer;">×</button>
                            <div class="mt mb">
                                <p>Êtes-vous sûr de vouloir supprimer l'événement "<?= $evenement->getTitle() ?>" ?</p>
                                <p class="text-danger"><strong>Attention :</strong> Cette action est irréversible.</p>
                            </div>
                            <div class="flex-row justify-content-between">
                                <button type="button" class="btn" onclick="document.getElementById('deleteModal').style.display='none'">Annuler</button>
                                <form action="<?= HOME_URL . 'mes_evenements?action=delete&id=' . $evenement->getIdEvenement() ?>" method="post">
                                    <button type="submit" class="btn deconnexion">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>


<style>
    .event-header {
        margin-bottom: 2rem;
    }

    .event-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .event-actions .btn {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }

    .event-banner {
        margin-bottom: 2rem;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }

    .event-banner img {
        width: 100%;
        height: 300px;
        object-fit: cover;
    }

    .event-details-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
    }

    .info-item {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
    }

    .info-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .info-item strong {
        color: #3a7ca5;
        font-size: 1rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .info-item p {
        margin: 0.25rem 0;
        color: #333;
    }

    .status-badge,
    .visibility-badge,
    .approval-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
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

    .visibility-badge.public {
        background: #d1ecf1;
        color: #0c5460;
    }

    .visibility-badge.private {
        background: #f8d7da;
        color: #721c24;
    }

    .approval-badge {
        background: #e2e3e5;
        color: #383d41;
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 0.5rem;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #3a7ca5 0%, #6ed3cf 100%);
        transition: width 0.3s ease;
    }

    .event-images {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 0.5rem;
    }

    .event-thumb {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #ddd;
    }

    .event-statistics {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        margin-top: 1rem;
    }

    .event-statistics h4 {
        margin-bottom: 1rem;
        color: #3a7ca5;
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #666;
    }

    .stat-value {
        font-weight: 600;
        color: #333;
    }

    @media (max-width: 968px) {
        .event-details-grid {
            grid-template-columns: 1fr;
        }

        .event-banner img {
            height: 200px;
        }
    }

    @media (max-width: 768px) {
        .event-actions {
            flex-direction: column;
            width: 100%;
        }

        .event-actions .btn {
            justify-content: center;
        }

        .flex-row {
            flex-direction: column;
            align-items: flex-start;
        }

        .event-banner img {
            height: 150px;
        }
    }
</style>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>