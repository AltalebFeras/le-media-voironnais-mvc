<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/admin_contacts.css' ?>">
<?php include_once __DIR__ . '/../includes/navbar_admin.php'; ?>

<main class="pt">
    <div class="container">
        <div class="flex-row justify-between align-center mb-4">
            <div>
                <h2>Messages de Contact</h2>
                <p class="text-muted"><?= $totalContacts ?> message<?= $totalContacts > 1 ? 's' : '' ?> au total</p>
            </div>
            <a href="<?= HOME_URL ?>admin/dashboard_admin" class="btn btn-outline">
                <span class="material-icons">arrow_back</span>
                Retour au Dashboard
            </a>
        </div>

        <!-- Filter Tabs -->
        <div class="contact-filters">
            <a href="<?= HOME_URL ?>admin/contacts" class="filter-btn <?= !isset($_GET['status']) ? 'active' : '' ?>">
                Tous (<?= $stats['total'] ?? 0 ?>)
            </a>
            <a href="<?= HOME_URL ?>admin/contacts?status=nouveau" class="filter-btn <?= ($_GET['status'] ?? '') === 'nouveau' ? 'active' : '' ?>">
                Nouveaux (<?= $stats['nouveau'] ?? 0 ?>)
            </a>
            <a href="<?= HOME_URL ?>admin/contacts?status=lu" class="filter-btn <?= ($_GET['status'] ?? '') === 'lu' ? 'active' : '' ?>">
                Lus (<?= $stats['lu'] ?? 0 ?>)
            </a>
            <a href="<?= HOME_URL ?>admin/contacts?status=traite" class="filter-btn <?= ($_GET['status'] ?? '') === 'traite' ? 'active' : '' ?>">
                Traités (<?= $stats['traite'] ?? 0 ?>)
            </a>
            <a href="<?= HOME_URL ?>admin/contacts?status=archive" class="filter-btn <?= ($_GET['status'] ?? '') === 'archive' ? 'active' : '' ?>">
                Archivés (<?= $stats['archive'] ?? 0 ?>)
            </a>
        </div>

        <?php if (empty($contacts)): ?>
            <div class="empty-state text-center py-5">
                <span class="material-icons" style="font-size: 4rem; color: var(--gray-400);">mail_outline</span>
                <h3>Aucun message</h3>
                <p class="text-muted">Aucun message de contact pour le moment.</p>
            </div>
        <?php else: ?>
            <!-- Contacts List -->
            <div class="contacts-list">
                <?php foreach ($contacts as $contact): ?>
                    <div class="contact-card <?= $contact->getStatus() ?>">
                        <div class="contact-header">
                            <div class="contact-info">
                                <div class="contact-name">
                                    <?= htmlspecialchars($contact->getFirstName() . ' ' . $contact->getLastName()) ?>
                                </div>
                                <div class="contact-email">
                                    <span class="material-icons">email</span>
                                    <?= htmlspecialchars($contact->getEmail()) ?>
                                </div>
                                <?php if ($contact->getPhone()): ?>
                                    <div class="contact-phone">
                                        <span class="material-icons">phone</span>
                                        <?= htmlspecialchars($contact->getPhone()) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="contact-meta">
                                <span class="contact-status badge-<?= $contact->getStatus() ?>">
                                    <?= ucfirst($contact->getStatus()) ?>
                                </span>
                                <div class="contact-date">
                                    <?= date('d/m/Y H:i', strtotime($contact->getCreatedAt())) ?>
                                </div>
                                <button class="contact-toggle" onclick="toggleContact('<?= $contact->getUiid() ?>')">
                                    <span class="material-icons">expand_more</span>
                                </button>
                            </div>
                        </div>

                        <div class="contact-subject">
                            <h4><?= htmlspecialchars($contact->getSubject()) ?></h4>
                        </div>

                        <div class="contact-details" id="contact-<?= $contact->getUiid() ?>" style="display: none;">
                            <div class="contact-message">
                                <h5>Message :</h5>
                                <div class="message-content">
                                    <?= nl2br(htmlspecialchars($contact->getMessage())) ?>
                                </div>
                            </div>

                            <?php if ($contact->getResponse()): ?>
                                <div class="contact-response">
                                    <h5>Réponse envoyée :</h5>
                                    <div class="response-content">
                                        <?= nl2br(htmlspecialchars($contact->getResponse())) ?>
                                    </div>
                                    <?php if ($contact->getRepliedAt()): ?>
                                        <div class="response-date">
                                            Répondu le <?= date('d/m/Y à H:i', strtotime($contact->getRepliedAt())) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="contact-actions">
                                <?php if ($contact->getStatus() === 'nouveau'): ?>
                                    <form method="POST" action="<?= HOME_URL ?>admin/contacts" class="inline-form">
                                        <input type="hidden" name="uiid" value="<?= $contact->getUiid() ?>">
                                        <input type="hidden" name="action" value="mark_read">
                                        <button type="submit" class="btn btn-secondary">
                                            <span class="material-icons">visibility</span>
                                            Marquer comme lu
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($contact->getStatus() !== 'traite'): ?>
                                    <button onclick="showReplyModal('<?= $contact->getUiid() ?>', '<?= htmlspecialchars($contact->getEmail()) ?>', '<?= htmlspecialchars($contact->getFirstName() . ' ' . $contact->getLastName()) ?>')" class="btn btn-primary">
                                        <span class="material-icons">reply</span>
                                        Répondre
                                    </button>
                                <?php endif; ?>

                                <?php if ($contact->getStatus() !== 'archive'): ?>
                                    <form method="POST" action="<?= HOME_URL ?>admin/contacts" class="inline-form">
                                        <input type="hidden" name="uiid" value="<?= $contact->getUiid() ?>">
                                        <input type="hidden" name="action" value="archive">
                                        <button type="submit" class="btn btn-warning">
                                            <span class="material-icons">archive</span>
                                            Archiver
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <form method="POST" action="<?= HOME_URL ?>admin/contacts" class="inline-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?')">
                                    <input type="hidden" name="uiid" value="<?= $contact->getUiid() ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn btn-danger">
                                        <span class="material-icons">delete</span>
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav class="pagination-wrapper" aria-label="Navigation des pages">
                    <ul class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= HOME_URL ?>admin/contacts?page=<?= $currentPage - 1 ?><?= isset($_GET['status']) ? '&status=' . $_GET['status'] : '' ?>">
                                    <span class="material-icons">chevron_left</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                            <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= HOME_URL ?>admin/contacts?page=<?= $i ?><?= isset($_GET['status']) ? '&status=' . $_GET['status'] : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= HOME_URL ?>admin/contacts?page=<?= $currentPage + 1 ?><?= isset($_GET['status']) ? '&status=' . $_GET['status'] : '' ?>">
                                    <span class="material-icons">chevron_right</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<!-- Reply Modal -->
<div class="popup" id="replyModal" style="display: none;">
    <div class="card" style="max-width: 600px; width: 90%;">
        <div class="flex-row justify-between align-center mb-4">
            <h3>Répondre au message</h3>
            <button onclick="closeReplyModal()" class="btn btn-secondary">
                <span class="material-icons">close</span>
            </button>
        </div>

        <form method="POST" action="<?= HOME_URL ?>admin/contacts" id="replyForm">
            <input type="hidden" name="uiid" id="replyUiid">
            <input type="hidden" name="action" value="reply">
            
            <div class="form-group">
                <label for="replyTo">Destinataire :</label>
                <input type="email" id="replyTo" name="replyTo" readonly>
            </div>

            <div class="form-group">
                <label for="replySubject">Sujet :</label>
                <input type="text" id="replySubject" name="replySubject" required>
            </div>

            <div class="form-group">
                <label for="replyMessage">Message :</label>
                <textarea id="replyMessage" name="replyMessage" rows="8" required placeholder="Tapez votre réponse ici..."></textarea>
            </div>

            <div class="flex-row justify-between">
                <button type="button" onclick="closeReplyModal()" class="btn btn-secondary">
                    Annuler
                </button>
                <button type="submit" class="btn btn-primary">
                    <span class="material-icons">send</span>
                    Envoyer la réponse
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleContact(uiid) {
    const details = document.getElementById('contact-' + uiid);
    const toggle = document.querySelector(`[onclick="toggleContact('${uiid}')"] .material-icons`);
    
    if (details.style.display === 'none') {
        details.style.display = 'block';
        toggle.textContent = 'expand_less';
    } else {
        details.style.display = 'none';
        toggle.textContent = 'expand_more';
    }
}

function showReplyModal(uiid, email, name) {
    document.getElementById('replyUiid').value = uiid;
    document.getElementById('replyTo').value = email;
    document.getElementById('replySubject').value = 'Re: Votre message de contact';
    document.getElementById('replyMessage').value = `Bonjour ${name},\n\nMerci pour votre message.\n\n\n\nCordialement,\nL'équipe du Média Voironnais`;
    document.getElementById('replyModal').style.display = 'flex';
}

function closeReplyModal() {
    document.getElementById('replyModal').style.display = 'none';
    document.getElementById('replyForm').reset();
}

// Close modal when clicking outside
document.getElementById('replyModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReplyModal();
    }
});
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
