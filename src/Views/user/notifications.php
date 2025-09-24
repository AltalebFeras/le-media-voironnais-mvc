<?php include_once __DIR__ . '/../includes/header.php'; ?>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="pt">
  <section class="card" style="max-width: 860px; margin: 0.5rem auto;">
    <div class="flex-row">
      <h2>Mes notifications</h2>
      <div class="flex-row" style="gap: 0.5rem;">
        <button id="pageMarkAll" class="notif-link" type="button">Tout marquer comme lu</button>
      </div>
    </div>

    <ul id="pageNotifList" class="notif-list" style="margin-top: 8px;"></ul>

    <div id="pageActions" class="flex-row" style="justify-content:center; margin-top: 8px;">
      <button id="pageLoadMore" class="notif-link" type="button">Charger plus</button>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>