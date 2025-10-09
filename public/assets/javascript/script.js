/**
 * adds a loading overlay to all forms on the page when they are submitted.
 * The overlay contains a loading image and disables the submit button to prevent multiple submissions.
 */
// if form id is not "formGroupCalculator", do not apply the loader

// Loader logic: apply loader on any form submit, remove on form:invalid
document.querySelectorAll("form").forEach(function (form) {
  form.addEventListener("submit", function (e) {
    // Exclude specific forms if needed, e.g.:
    let formIdExclusions = ["add-comment-form", "reply-comment-form"];
    if (formIdExclusions.includes(form.id)) return;
    if (!document.getElementById("loaderOverlay")) {
      const overlay = document.createElement("div");
      overlay.id = "loaderOverlay";
      Object.assign(overlay.style, {
        position: "fixed",
        top: 0,
        left: 0,
        width: "100vw",
        height: "100vh",
        backgroundColor: "rgba(255, 255, 255, 0.5)",
        display: "flex",
        justifyContent: "center",
        alignItems: "center",
        zIndex: 9999,
      });

      // Detect Firefox browser
      const isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;

      if (isFirefox) {
        // Use inline SVG for Firefox (matches external loader design)
        const loaderContainer = document.createElement("div");
        loaderContainer.innerHTML = `
          <svg width="150" height="150" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <style>
                @keyframes spiral-spin {
                  0% { transform: rotate(0deg); }
                  100% { transform: rotate(360deg); }
                }
                .spiral-loader {
                  transform-origin: 50% 50%;
                  animation: spiral-spin 1.2s linear infinite;
                }
              </style>
            </defs>
            <g class="spiral-loader">
              <path d="M50,10 A40,40 0 0,1 90,50" fill="none" stroke="#000000" stroke-width="8" stroke-linecap="round"/>
              <path d="M90,50 A40,40 0 0,1 50,90" fill="none" stroke="#000000" stroke-width="8" stroke-linecap="round" opacity="0.7"/>
              <path d="M50,90 A40,40 0 0,1 10,50" fill="none" stroke="#000000" stroke-width="8" stroke-linecap="round" opacity="0.4"/>
              <path d="M10,50 A40,40 0 0,1 50,10" fill="none" stroke="#000000" stroke-width="8" stroke-linecap="round" opacity="0.1"/>
            </g>
          </svg>
        `;
        loaderContainer.setAttribute("role", "status");
        loaderContainer.setAttribute("aria-label", "Chargement...");
        loaderContainer.style.display = "flex";
        loaderContainer.style.justifyContent = "center";
        loaderContainer.style.alignItems = "center";
        overlay.appendChild(loaderContainer);
      } else {
        // Use external SVG for other browsers
        const loaderImage = document.createElement("img");
        loaderImage.src = "/assets/images/loader/loader.svg";
        loaderImage.alt = "Chargement...";
        loaderImage.style.width = "150px";
        loaderImage.style.zIndex = 9999;
        overlay.appendChild(loaderImage);
      }

      document.body.appendChild(overlay);
    }

    const submitBtn = form.querySelector('[type="submit"]');
    if (submitBtn) {
      if (!submitBtn.dataset.originalText) {
        submitBtn.dataset.originalText = submitBtn.textContent;
      }
      submitBtn.disabled = true;
      submitBtn.textContent = "Chargement...";
    }
  });
});

// Remove loader overlay if present when navigating back/forward
window.addEventListener("pageshow", function (event) {
  if (
    event.persisted ||
    performance.getEntriesByType("navigation")[0]?.type === "back_forward"
  ) {
    const overlay = document.getElementById("loaderOverlay");
    if (overlay) overlay.remove();
    // Optionally re-enable submit buttons if needed
    document.querySelectorAll("form [type='submit']").forEach((btn) => {
      btn.disabled = false;
      if (btn.dataset.originalText) {
        btn.textContent = btn.dataset.originalText;
      }
    });
  }
});

// Listen for custom "form:valid" event to show loader and submit
document.addEventListener("form:valid", function (e) {
  const form = e.target;
  // Loader already shown on submit, just submit the form
  form.submit();
});

// Listen for custom "form:invalid" event to remove loader and re-enable submit
document.addEventListener("form:invalid", function (e) {
  const form = e.target;
  const overlay = document.getElementById("loaderOverlay");
  if (overlay) overlay.remove();
  const submitBtn = form.querySelector('[type="submit"]');
  if (submitBtn) {
    submitBtn.disabled = false;
    if (submitBtn.dataset.originalText) {
      submitBtn.textContent = submitBtn.dataset.originalText;
    }
  }
});

// This  is a toggle functionality to password inputs, allowing users to show or hide their passwords with an eye icon.
document.querySelectorAll('input[type="password"]').forEach(function (input) {
  const wrapper = document.createElement("div");
  wrapper.classList.add("password-wrapper");
  input.parentNode.insertBefore(wrapper, input);
  wrapper.appendChild(input);

  const eyeIcon = document.createElement("span");
  eyeIcon.classList.add("toggle-password");
  eyeIcon.textContent = "👁️‍🗨️";
  wrapper.appendChild(eyeIcon);

  eyeIcon.addEventListener("click", function () {
    input.type = input.type === "password" ? "text" : "password";
    eyeIcon.textContent = input.type === "password" ? "👁️‍🗨️" : "🙈";
  });

  const toggleIconVisibility = () => {
    eyeIcon.style.display = input.value ? "inline" : "none";
  };

  toggleIconVisibility();
  input.addEventListener("input", toggleIconVisibility);
});

//  handle the burger menu functionality for mobile navigation.
document.addEventListener("DOMContentLoaded", function () {
  const burger = document.getElementById("burger-menu");
  const nav = document.getElementById("nav-links");

  burger.addEventListener("click", function (e) {
    e.stopPropagation();
    burger.classList.toggle("active");
    nav.classList.toggle("open");
  });

  nav.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      burger.classList.remove("active");
      nav.classList.remove("open");
    });
  });

  document.addEventListener("click", function (e) {
    if (
      nav.classList.contains("open") &&
      !nav.contains(e.target) &&
      !burger.contains(e.target)
    ) {
      burger.classList.remove("active");
      nav.classList.remove("open");
    }
  });
});

// Notifications (polling + dropdown)
(function () {
  const bell = document.getElementById("notifBell");
  const badge = document.getElementById("notifCount");
  const dropdown = document.getElementById("notifDropdown");
  const list = document.getElementById("notifList");
  const markAllBtn = document.getElementById("notifMarkAll");

  if (!bell || !badge || !dropdown || !list) return;

  let polling = null;
  let lastOpenAt = 0;

  const apiGet = (path) =>
    fetch(`/${path}`, { headers: { Accept: "application/json" } });
  const apiPost = (path, body) =>
    fetch(`/${path}`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify(body || {}),
    });

  async function refreshCount() {
    try {
      const res = await apiGet("notifications/count");
      const data = await res.json();
      if (!data || data.success !== true) return;
      const count = Number(data.count || 0);
      if (count > 0) {
        badge.textContent = String(count);
        badge.classList.remove("d-none");
      } else {
        badge.classList.add("d-none");
      }
    } catch (_) {
      /* silent */
    }
  }

  async function loadList(page = 1, limit = 10) {
    list.innerHTML = '<li class="notif-item muted">Chargement…</li>';
    try {
      const res = await apiGet(
        `notifications/list?page=${page}&limit=${limit}`
      );
      const data = await res.json();
      if (!data || data.success !== true) {
        list.innerHTML =
          '<li class="notif-item muted">Erreur lors du chargement</li>';
        return;
      }
      if (!data.items || data.items.length === 0) {
        list.innerHTML =
          '<li class="notif-item muted">Aucune notification</li>';
        return;
      }
      list.innerHTML = "";
      data.items.forEach((item) => {
        const li = document.createElement("li");
        li.className = `notif-item ${item.isRead ? "read" : "unread"}`;
        li.dataset.id = item.idNotification;
        li.innerHTML = `
          <div class="notif-title">${escapeHtml(
            item.title || "(sans titre)"
          )}</div>
          ${
            item.message
              ? `<div class="notif-message">${escapeHtml(item.message)}</div>`
              : ""
          }
          <div class="notif-meta">
            <span class="notif-type ${item.type || "info"}">${
          item.type || "info"
        }</span>
            <time class="notif-date">${new Date(
              item.createdAt
            ).toLocaleString()}</time>
          </div>
        `;
        li.addEventListener("click", async () => {
          if (!item.isRead) {
            try {
              await apiPost("notifications/mark-read", {
                id: item.idNotification,
              });
              li.classList.remove("unread");
              li.classList.add("read");
              // decrement badge
              const current = parseInt(badge.textContent || "0", 10);
              if (current > 1) {
                badge.textContent = String(current - 1);
              } else {
                badge.classList.add("d-none");
              }
            } catch (_) {
              /* ignore */
            }
          }
          if (item.url) {
            window.location.href = item.url;
          }
        });
        list.appendChild(li);
      });
    } catch (_) {
      list.innerHTML =
        '<li class="notif-item muted">Erreur lors du chargement</li>';
    }
  }

  // basic HTML escaping
  function escapeHtml(str) {
    return String(str)
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  function openDropdown() {
    dropdown.classList.remove("d-none");
    bell.setAttribute("aria-expanded", "true");
    const now = Date.now();
    if (now - lastOpenAt > 800) {
      loadList(1, 10);
      lastOpenAt = now;
    }
    // click-outside to close
    const closeOnOutside = (e) => {
      if (!dropdown.contains(e.target) && !bell.contains(e.target)) {
        closeDropdown();
        document.removeEventListener("click", closeOnOutside, true);
      }
    };
    setTimeout(
      () => document.addEventListener("click", closeOnOutside, true),
      0
    );
  }

  function closeDropdown() {
    dropdown.classList.add("d-none");
    bell.setAttribute("aria-expanded", "false");
  }

  bell.addEventListener("click", (e) => {
    e.preventDefault();
    if (dropdown.classList.contains("d-none")) openDropdown();
    else closeDropdown();
  });

  if (markAllBtn) {
    markAllBtn.addEventListener("click", async () => {
      try {
        const res = await apiPost("notifications/mark-all-read", {});
        const data = await res.json();
        if (data && data.success) {
          badge.classList.add("d-none");
          list.querySelectorAll(".notif-item.unread").forEach((li) => {
            li.classList.remove("unread");
            li.classList.add("read");
          });
        }
      } catch (_) {
        /* ignore */
      }
    });
  }

  // Start polling when tab visible, stop when hidden
  function startPolling() {
    if (polling) return;
    polling = setInterval(refreshCount, 20000);
    refreshCount();
  }
  function stopPolling() {
    if (polling) clearInterval(polling);
    polling = null;
  }
  document.addEventListener("visibilitychange", () => {
    if (document.hidden) stopPolling();
    else startPolling();
  });
  startPolling();
})();

// Notifications page (full list + load more + mark all)
(function () {
  const listEl = document.getElementById("pageNotifList");
  const loadMoreBtn = document.getElementById("pageLoadMore");
  const markAllBtn = document.getElementById("pageMarkAll");
  const bellBadge = document.getElementById("notifCount");
  if (!listEl || !loadMoreBtn) return;

  let page = 1;
  const limit = 15;
  let loading = false;
  let hasMore = true;

  const apiGet = (path) =>
    fetch(`/${path}`, { headers: { Accept: "application/json" } });
  const apiPost = (path, body) =>
    fetch(`/${path}`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify(body || {}),
    });

  function escapeHtml(str) {
    return String(str)
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  function renderItem(item) {
    const li = document.createElement("li");
    li.className = `notif-item ${item.isRead ? "read" : "unread"}`;
    li.dataset.id = item.idNotification || item.id;
    li.innerHTML = `
      <div class="notif-title">${escapeHtml(item.title || "(sans titre)")}</div>
      ${
        item.message
          ? `<div class="notif-message">${escapeHtml(item.message)}</div>`
          : ""
      }
      <div class="notif-meta">
        <span class="notif-type ${item.type || "info"}">${
      item.type || "info"
    }</span>
        <time class="notif-date">${new Date(
          item.createdAt
        ).toLocaleString()}</time>
      </div>
    `;
    li.addEventListener("click", async () => {
      if (!item.isRead) {
        try {
          await apiPost("notifications/mark-read", {
            id: item.idNotification || item.id,
          });
          li.classList.remove("unread");
          li.classList.add("read");
          item.isRead = 1;
          if (bellBadge && !bellBadge.classList.contains("d-none")) {
            const current = parseInt(bellBadge.textContent || "0", 10);
            if (current > 1) bellBadge.textContent = String(current - 1);
            else bellBadge.classList.add("d-none");
          }
        } catch (_) {
          /* ignore */
        }
      }
      if (item.url) {
        window.location.href = item.url;
      }
    });
    return li;
  }

  async function loadPage() {
    if (loading || !hasMore) return;
    loading = true;
    loadMoreBtn.textContent = "Chargement…";
    try {
      const res = await apiGet(
        `notifications/list?page=${page}&limit=${limit}`
      );
      const data = await res.json();
      if (!data || data.success !== true) throw new Error("bad response");

      if (page === 1 && (!data.items || data.items.length === 0)) {
        listEl.innerHTML =
          '<li class="notif-item muted">Aucune notification</li>';
        hasMore = false;
        loadMoreBtn.classList.add("d-none");
        return;
      }

      data.items.forEach((item) => listEl.appendChild(renderItem(item)));
      hasMore = !!data.hasMore;
      if (!hasMore) loadMoreBtn.classList.add("d-none");
      page += 1;
    } catch (_) {
      if (page === 1) {
        listEl.innerHTML =
          '<li class="notif-item muted">Erreur lors du chargement</li>';
      }
    } finally {
      loading = false;
      loadMoreBtn.textContent = "Charger plus";
    }
  }

  loadMoreBtn.addEventListener("click", loadPage);

  if (markAllBtn) {
    markAllBtn.addEventListener("click", async () => {
      try {
        const res = await apiPost("notifications/mark-all-read", {});
        const data = await res.json();
        if (data && data.success) {
          listEl.querySelectorAll(".notif-item.unread").forEach((li) => {
            li.classList.remove("unread");
            li.classList.add("read");
          });
          if (bellBadge) bellBadge.classList.add("d-none");
        }
      } catch (_) {
        /* ignore */
      }
    });
  }

  // initial load
  loadPage();
})();

// Response messages handling (alerts, toasts, error lists)
document.addEventListener("DOMContentLoaded", function () {
  // Unified responseMessage handling

  // Function to dismiss any responseMessage
  function dismissResponseMessage(element, isToast = false) {
    if (isToast) {
      element.classList.remove("show");
      element.classList.add("hide");
    } else {
      element.classList.add("fade-out");
    }

    // Wait for animation to complete before removing
    setTimeout(
      function () {
        if (element.parentElement) {
          element.remove();

          // Special handling for error list items
          if (element.classList.contains("error-item")) {
            const ul = element.closest(".error-list");
            if (ul && ul.children.length === 0) {
              ul.remove();
            }
          }
        }
      },
      isToast ? 400 : 0
    );
  }

  // Handle all close buttons
  document.querySelectorAll(".responseMessage-close").forEach(function (btn) {
    btn.addEventListener("click", function () {
      const responseMessage = this.closest(".responseMessage");
      const isToast = responseMessage.classList.contains("toast");
      dismissResponseMessage(responseMessage, isToast);
    });
  });

  // Auto-dismiss standard responseMessages
  document
    .querySelectorAll(".custom-alert, .error-list")
    .forEach(function (alert) {
      setTimeout(function () {
        dismissResponseMessage(alert, false);
      }, 10000);
    });

  // Handle toast responseMessages
  const toast = document.getElementById("toast");
  if (toast) {
    setTimeout(function () {
      toast.classList.add("show");
    }, 100);

    setTimeout(function () {
      dismissResponseMessage(toast, true);
    }, 7000);
  }
});
