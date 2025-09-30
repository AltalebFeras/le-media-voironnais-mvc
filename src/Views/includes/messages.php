<?php
// Single error responseMessage
if (isset($_SESSION['error'])): ?>
    <div class="responseMessage custom-alert custom-alert-danger">
        <p><?= $_SESSION['error']; ?></p>
        <button class="responseMessage-close" aria-label="Close">❌</button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php
// Multiple error responseMessages
if (!empty($_SESSION['errors'])): ?>
    <ul class="responseMessage error-list">
        <?php foreach ($_SESSION['errors'] as $error): ?>
            <li class="error-item responseMessage">
                <?= $error ?>
                <button class="responseMessage-close" aria-label="Close">❌</button>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php unset($_SESSION['errors']); ?>
<?php endif; ?>

<?php
// Toast responseMessages for success, info and warning messages
if (isset($_SESSION['success']) || isset($_SESSION['info']) || isset($_SESSION['warning'])):
    $toastMsg = isset($_SESSION['success']) ? $_SESSION['success'] : (isset($_SESSION['info']) ? $_SESSION['info'] : $_SESSION['warning']);
    $toastType = isset($_SESSION['success']) ? 'success' : (isset($_SESSION['info']) ? 'info' : 'warning');
    unset($_SESSION['success'], $_SESSION['info'], $_SESSION['warning']);
?>
    <div class="responseMessage toast toast-<?= $toastType ?>" id="toast">
        <?= htmlspecialchars($toastMsg) ?>
        <button class="responseMessage-close" aria-label="Close">❌</button>
    </div>
<?php endif; ?>