<div class="notif-container">
  <button id="notifBell" class="notif-bell" aria-haspopup="true" aria-expanded="false" aria-label="Notifications">
    <span class="material-icons">notifications</span>
    <span id="notifCount" class="notif-badge d-none">0</span>
  </button>

  <div id="notifDropdown" class="notif-dropdown d-none" role="menu" aria-label="Notifications">
    <div class="notif-header">
      <strong>Notifications</strong>
      <button id="notifMarkAll" class="notif-link" type="button">Tout marquer comme lu</button>
    </div>
    <ul id="notifList" class="notif-list">
      <!-- filled dynamically -->
    </ul>
    <div class="notif-footer">
      <a class="notif-link" href="<?= HOME_URL ?? '/' ?>notifications">Voir tout</a>
    </div>
  </div>
</div>

<script>
  (function () {
    const bell = document.getElementById('notifBell');
    const panel = document.getElementById('notifDropdown');
    if (!bell || !panel) return;

    function setOpen(open) {
      panel.classList.toggle('d-none', !open);
      bell.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    function toggle() {
      const isOpen = !panel.classList.contains('d-none');
      setOpen(!isOpen);
    }

    bell.addEventListener('click', function (e) {
      e.stopPropagation();
      toggle();
    });

    bell.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        toggle();
      }
    });

    document.addEventListener('click', function (e) {
      if (!panel.classList.contains('d-none')) {
        const within = panel.contains(e.target) || bell.contains(e.target);
        if (!within) setOpen(false);
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') setOpen(false);
    });
  })();
</script>

<style>
  .notif-container {
    position: relative;
    display: inline-block;
  }

  .notif-bell {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    outline: inherit;
  }

  .notif-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: red;
    color: white;
    border-radius: 50%;
    padding: 0.2rem 0.6rem;
    font-size: 0.75rem;
  }

  .notif-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 320px;
    max-height: 60vh;
    overflow: auto;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    z-index: 1000;
  }

  .notif-header {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #eee;
    font-weight: 600;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .notif-link {
    color: #007bff;
    text-decoration: none;
    font-size: 0.875rem;
  }

  .notif-list {
    list-style: none;
    margin: 0;
    padding: 0;
  }

  .notif-footer {
    padding: 0.75rem 1rem;
    border-top: 1px solid #eee;
    text-align: right;
  }
</style>
