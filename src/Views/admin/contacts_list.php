<?php include_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= HOME_URL . 'assets/css/admin/admin_contacts.css' ?>">
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

                                <?php
                                // Can reply only if no response exists yet
                                $hasResponse = !empty($contact->getResponse()) && !empty($contact->getRepliedAt());
                                if (!$hasResponse):
                                ?>
                                    <button onclick="showReplyModal('<?= $contact->getUiid() ?>', '<?= htmlspecialchars($contact->getEmail()) ?>', '<?= htmlspecialchars($contact->getFirstName() . ' ' . $contact->getLastName()) ?>')" class="btn btn-primary">
                                        <span class="material-icons">reply</span>
                                        Répondre
                                    </button>
                                <?php endif; ?>

                                <?php
                                // Status change logic based on response existence
                                if ($hasResponse):
                                    // If has response, can only move between 'traite' and 'archive'
                                    if ($contact->getStatus() === 'traite'): ?>
                                        <form method="POST" action="<?= HOME_URL ?>admin/contacts" class="inline-form">
                                            <input type="hidden" name="uiid" value="<?= $contact->getUiid() ?>">
                                            <input type="hidden" name="action" value="archive">
                                            <button type="submit" class="btn btn-warning">
                                                <span class="material-icons">archive</span>
                                                Archiver
                                            </button>
                                        </form>
                                    <?php elseif ($contact->getStatus() === 'archive'): ?>
                                        <form method="POST" action="<?= HOME_URL ?>admin/contacts" class="inline-form">
                                            <input type="hidden" name="uiid" value="<?= $contact->getUiid() ?>">
                                            <input type="hidden" name="action" value="mark_treated">
                                            <button type="submit" class="btn btn-success">
                                                <span class="material-icons">mark_email_read</span>
                                                Marquer traité
                                            </button>
                                        </form>
                                    <?php endif;
                                else:
                                    // If no response, flexible movement between 'lu' and 'archive'
                                    if ($contact->getStatus() === 'lu'): ?>
                                        <form method="POST" action="<?= HOME_URL ?>admin/contacts" class="inline-form">
                                            <input type="hidden" name="uiid" value="<?= $contact->getUiid() ?>">
                                            <input type="hidden" name="action" value="archive">
                                            <button type="submit" class="btn btn-warning">
                                                <span class="material-icons">archive</span>
                                                Archiver
                                            </button>
                                        </form>
                                    <?php elseif ($contact->getStatus() === 'archive'): ?>
                                        <form method="POST" action="<?= HOME_URL ?>admin/contacts" class="inline-form">
                                            <input type="hidden" name="uiid" value="<?= $contact->getUiid() ?>">
                                            <input type="hidden" name="action" value="mark_read">
                                            <button type="submit" class="btn btn-info">
                                                <span class="material-icons">visibility</span>
                                                Marquer lu
                                            </button>
                                        </form>
                                <?php endif;
                                endif; ?>

                                <form method="POST" action="<?= HOME_URL ?>admin/contacts" class="inline-form">
                                    <input type="hidden" name="uiid" value="<?= $contact->getUiid() ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="button" class="btn btn-danger" onclick="showDeleteModal('<?= $contact->getUiid() ?>', '<?= htmlspecialchars($contact->getFirstName() . ' ' . $contact->getLastName()) ?>')">
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
                <label>Destinataire :</label>
                <p id="replyToDisplay" class="reply-info"></p>
                <input type="hidden" id="replyTo" name="replyTo">
            </div>

            <div class="form-group">
                <label>Sujet :</label>
                <p id="replySubjectDisplay" class="reply-info"></p>
                <input type="hidden" id="replySubject" name="replySubject">
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

<!-- Delete Confirmation Modal -->
<div class="popup" id="deleteModal" style="display: none;">
    <div class="card" style="max-width: 500px; width: 90%;">
        <div class="flex-row justify-between align-center mb-4">
            <h3>Confirmer la suppression</h3>
            <button onclick="closeDeleteModal()" class="btn btn-secondary">
                <span class="material-icons">close</span>
            </button>
        </div>

        <div class="mb-4">
            <p>Êtes-vous sûr de vouloir supprimer le message de <strong id="deleteContactName"></strong> ?</p>
            <p class="text-muted">Cette action est irréversible.</p>
        </div>

        <form method="POST" action="<?= HOME_URL ?>admin/contacts" id="deleteForm">
            <input type="hidden" name="uiid" id="deleteUiid">
            <input type="hidden" name="action" value="delete">

            <div class="flex-row justify-between">
                <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">
                    Annuler
                </button>
                <button type="submit" class="btn btn-danger">
                    <span class="material-icons">delete</span>
                    Supprimer définitivement
                </button>
            </div>
        </form>
    </div>
</div>
<script src="<?= HOME_URL . 'assets/javascript/contacts-list.js' ?>"></script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>