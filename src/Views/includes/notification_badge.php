<div class="notif-container">
  <button id="notifBell" class="link nav-item notif-bell-btn" aria-haspopup="true" aria-expanded="false" aria-label="Notifications">
    <div class="notif-icon-wrapper">
      <span class="material-icons ">notifications</span>
      <span id="notifCount" class="notif-badge d-none">0</span>
    </div>
  </button>

  <!-- Notification Popup Modal -->
  <div id="notifDropdown" class="notif-popup d-none" role="dialog" aria-label="Notifications">
    <div class="notif-popup-content">
      <div class="notif-header">
        <strong>Notifications</strong>
        <button id="notifClosePopup" class="notif-close-btn" type="button" aria-label="Fermer">×</button>
      </div>
      <div class="notif-actions">
        <button id="notifMarkAll" class="notif-link" type="button">Tout marquer comme lu</button>
      </div>
      <ul id="notifList" class="notif-list">
        <!-- rempli dynamiquement -->
      </ul>
      <div class="notif-footer">
        <a class="notif-link" href="<?= HOME_URL ?>notifications">Voir toutes les notifications</a>
      </div>
    </div>
  </div>
</div>