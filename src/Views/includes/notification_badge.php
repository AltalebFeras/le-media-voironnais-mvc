<span
  id="notificationBell"
  class="material-icons"
  role="button"
  tabindex="0"
  aria-controls="notificationPanel"
  aria-expanded="false"
  style="color:grey; font-size: 2rem; cursor: pointer;">circle_notifications</span>

<div
  id="notificationPanel"
  style="position:absolute; top: calc(100% + 8px); right: -40px; width: 320px; max-height: 60vh; overflow:auto; background:#fff; border:1px solid #ddd; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,0.15); display:none; z-index:1000;">
  <div style="padding:.75rem 1rem; border-bottom:1px solid #eee; font-weight:600;">Notifications</div>
  <ul id="notificationList" style="list-style:none; margin:0; padding:0;">
    <li style="padding:.75rem 1rem; color:#666;">Aucune notification</li>
  </ul>
</div>

<script>
  (function () {
    const bell = document.getElementById('notificationBell');
    const panel = document.getElementById('notificationPanel');
    if (!bell || !panel) return;

    function setOpen(open) {
      panel.style.display = open ? 'block' : 'none';
      bell.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    function toggle() {
      const isOpen = panel.style.display === 'block';
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
      if (panel.style.display === 'block') {
        const within = panel.contains(e.target) || bell.contains(e.target);
        if (!within) setOpen(false);
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') setOpen(false);
    });
  })();
</script>
