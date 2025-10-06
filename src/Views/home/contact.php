<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<main>

    <h2>Contactez l'administration</h2>
    <form method="post" action="contact">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
        <div>
            <label>Nom:</label>
            <input type="text" name="name" required value="<?= htmlspecialchars($_SESSION['form_data']['name'] ?? '') ?>">
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($_SESSION['form_data']['email'] ?? '') ?>">
        </div>
        <div>
            <label>Sujet:</label>
            <input type="text" name="title" required value="<?= htmlspecialchars($_SESSION['form_data']['title'] ?? '') ?>">
        </div>
        <div>
            <label>Type:</label>
            <select name="type">
                <option value="info">Demande d'information</option>
                <option value="alerte">Alerte</option>
                <option value="autre">Autre</option>
            </select>
        </div>
        <div>
            <label>Message:</label>
            <textarea name="message" required><?= htmlspecialchars($_SESSION['form_data']['message'] ?? '') ?></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Envoyer</button>
    </form>
</main>
<script type="module" src="<?= HOME_URL . 'assets/javascript/page/contact.js' ?>"></script>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>