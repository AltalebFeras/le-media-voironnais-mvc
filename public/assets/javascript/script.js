/**
 * adds a loading overlay to all forms on the page when they are submitted.
 * The overlay contains a loading image and disables the submit button to prevent multiple submissions.
 */
// if form id is not "formGroupCalculator", do not apply the loader

// Loader logic: apply loader on any form submit, remove on form:invalid
$("form").each(function() {
  $(this).on("submit", function(e) {
    // Exclude specific forms if needed, e.g.:
    let formIdExclusions = ["add-comment-form", "reply-comment-form"];
    if (formIdExclusions.includes(this.id)) return;
    if ($("#loaderOverlay").length === 0) {
      const overlay = $('<div></div>').attr('id', 'loaderOverlay').css({
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
        const loaderContainer = $('<div></div>').html(`
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
        `).attr("role", "status").attr("aria-label", "Chargement...").css({
          display: "flex",
          justifyContent: "center",
          alignItems: "center"
        });
        overlay.append(loaderContainer);
      } else {
        // Use external SVG for other browsers
        const loaderImage = $('<img>').attr({
          src: "/assets/images/loader/loader.svg",
          alt: "Chargement..."
        }).css({
          width: "150px",
          zIndex: 9999
        });
        overlay.append(loaderImage);
      }

      $("body").append(overlay);
    }

    const $submitBtn = $(this).find('[type="submit"]');
    if ($submitBtn.length) {
      if (!$submitBtn.data("originalText")) {
        $submitBtn.data("originalText", $submitBtn.text());
      }
      $submitBtn.prop("disabled", true);
      $submitBtn.text("Chargement...");
    }
  });
});

// Remove loader overlay if present when navigating back/forward
$(window).on("pageshow", function(event) {
  if (
    event.originalEvent.persisted ||
    performance.getEntriesByType("navigation")[0]?.type === "back_forward"
  ) {
    const $overlay = $("#loaderOverlay");
    if ($overlay.length) $overlay.remove();
    // Optionally re-enable submit buttons if needed
    $("form [type='submit']").each(function() {
      $(this).prop("disabled", false);
      if ($(this).data("originalText")) {
        $(this).text($(this).data("originalText"));
      }
    });
  }
});

// Listen for custom "form:valid" event to show loader and submit
$(document).on("form:valid", function(e) {
  const form = e.target;
  // Loader already shown on submit, just submit the form
  form.submit();
});

// Listen for custom "form:invalid" event to remove loader and re-enable submit
$(document).on("form:invalid", function(e) {
  const $form = $(e.target);
  const $overlay = $("#loaderOverlay");
  if ($overlay.length) $overlay.remove();
  const $submitBtn = $form.find('[type="submit"]');
  if ($submitBtn.length) {
    $submitBtn.prop("disabled", false);
    if ($submitBtn.data("originalText")) {
      $submitBtn.text($submitBtn.data("originalText"));
    }
  }
});

// This is a toggle functionality to password inputs, allowing users to show or hide their passwords with an eye icon.
$('input[type="password"]').each(function() {
  const $input = $(this);
  const $wrapper = $('<div></div>').addClass("password-wrapper");
  $input.before($wrapper);
  $wrapper.append($input);

  const $eyeIcon = $('<span></span>').addClass("toggle-password").text("👁️‍🗨️");
  $wrapper.append($eyeIcon);

  $eyeIcon.on("click", function() {
    const currentType = $input.attr("type");
    $input.attr("type", currentType === "password" ? "text" : "password");
    $eyeIcon.text(currentType === "password" ? "🙈" : "👁️‍🗨️");
  });

  const toggleIconVisibility = () => {
    $eyeIcon.css("display", $input.val() ? "inline" : "none");
  };

  toggleIconVisibility();
  $input.on("input", toggleIconVisibility);
});

//  handle the burger menu functionality for mobile navigation.
$(document).ready(function() {
  const $burger = $("#burger-menu");
  const $nav = $("#nav-links");

  $burger.on("click", function(e) {
    e.stopPropagation();
    $burger.toggleClass("active");
    $nav.toggleClass("open");
  });

  $nav.find("a").each(function() {
    $(this).on("click", function() {
      $burger.removeClass("active");
      $nav.removeClass("open");
    });
  });

  $(document).on("click", function(e) {
    if (
      $nav.hasClass("open") &&
      !$nav.is(e.target) && !$nav.has(e.target).length &&
      !$burger.is(e.target) && !$burger.has(e.target).length
    ) {
      $burger.removeClass("active");
      $nav.removeClass("open");
    }
  });

  // Profile dropdown toggle (Moi)
  const $moiToggle = $("#moiDropdownToggle");
  const $moiMenu = $("#moiDropdownMenu");

  if ($moiToggle.length && $moiMenu.length) {
    $moiToggle.on("click", function(e) {
      e.stopPropagation();
      const isExpanded = $moiToggle.attr("aria-expanded") === "true";
      $moiToggle.attr("aria-expanded", !isExpanded);
      $moiMenu.toggleClass("show");
    });

    // Close dropdown when clicking outside
    $(document).on("click", function(e) {
      if (!$moiToggle.is(e.target) && !$moiToggle.has(e.target).length && 
          !$moiMenu.is(e.target) && !$moiMenu.has(e.target).length) {
        $moiToggle.attr("aria-expanded", "false");
        $moiMenu.removeClass("show");
      }
    });

    // Close dropdown when clicking on menu items
    $moiMenu.find("a").each(function() {
      $(this).on("click", function() {
        $moiToggle.attr("aria-expanded", "false");
        $moiMenu.removeClass("show");
      });
    });
  }
});

// Notifications (polling + popup)
(function() {
  const $bell = $("#notifBell");
  const $badge = $("#notifCount");
  const $popup = $("#notifDropdown");
  const $list = $("#notifList");
  const $markAllBtn = $("#notifMarkAll");
  const $closeBtn = $("#notifClosePopup");

  if (!$bell.length || !$badge.length || !$popup.length || !$list.length) return;

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
        $badge.text(String(count));
        $badge.removeClass("d-none");
      } else {
        $badge.addClass("d-none");
      }
    } catch (_) {
      /* silent */
    }
  }

  async function loadList(page = 1, limit = 10) {
    $list.html('<li class="notif-item muted">Chargement…</li>');
    try {
      const res = await apiGet(
        `notifications/list?page=${page}&limit=${limit}`
      );
      const data = await res.json();
      if (!data || data.success !== true) {
        $list.html('<li class="notif-item muted">Erreur lors du chargement</li>');
        return;
      }
      if (!data.items || data.items.length === 0) {
        $list.html('<li class="notif-item muted">Aucune notification</li>');
        return;
      }
      $list.html("");
      data.items.forEach((item) => {
        const $li = $('<li></li>').addClass(`notif-item ${item.isRead ? "read" : "unread"}`).data('id', item.idNotification);
        $li.html(`
          <div class="notif-title">${escapeHtml(
            item.title || "(sans titre)"
          )}</div>
          ${
            item.message
              ? `<div class="notif-message">${escapeHtml(item.message)}</div>`
              : ""
          }
          <div class="notif-meta">
            <span class="notif-type badge-info ${item.type || "info"}">${
          item.type || "info"
        }</span>
            <time class="notif-date">${new Date(
              item.createdAt
            ).toLocaleString()}</time>
          </div>
        `);
        $li.on("click", async () => {
          if (!item.isRead) {
            try {
              await apiPost("notifications/mark-read", {
                id: item.idNotification,
              });
              $li.removeClass("unread");
              $li.addClass("read");
              const current = parseInt($badge.text() || "0", 10);
              if (current > 1) {
                $badge.text(String(current - 1));
              } else {
                $badge.addClass("d-none");
              }
            } catch (_) {
              /* ignore */
            }
          }
          if (item.url) {
            window.location.href = item.url;
          }
        });
        $list.append($li);
      });
    } catch (_) {
      $list.html('<li class="notif-item muted">Erreur lors du chargement</li>');
    }
  }

  function escapeHtml(str) {
    return String(str)
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  function openPopup() {
    $popup.removeClass("d-none");
    $bell.attr("aria-expanded", "true");
    $('body').css('overflow', 'hidden');
    const now = Date.now();
    if (now - lastOpenAt > 800) {
      loadList(1, 10);
      lastOpenAt = now;
    }
  }

  function closePopup() {
    $popup.addClass("d-none");
    $bell.attr("aria-expanded", "false");
    $('body').css('overflow', 'auto');
  }

  $bell.on("click", (e) => {
    e.preventDefault();
    e.stopPropagation();
    if ($popup.hasClass("d-none")) openPopup();
    else closePopup();
  });

  // Close button
  if ($closeBtn.length) {
    $closeBtn.on("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      closePopup();
    });
  }

  // Click outside to close (on overlay)
  $popup.on("click", function(e) {
    if ($(e.target).is($popup)) {
      closePopup();
    }
  });

  // Prevent clicks inside popup content from closing
  $popup.find('.notif-popup-content').on('click', function(e) {
    e.stopPropagation();
  });

  if ($markAllBtn.length) {
    $markAllBtn.on("click", async () => {
      try {
        const res = await apiPost("notifications/mark-all-read", {});
        const data = await res.json();
        if (data && data.success) {
          $badge.addClass("d-none");
          $list.find(".notif-item.unread").each(function() {
            $(this).removeClass("unread");
            $(this).addClass("read");
          });
        }
      } catch (_) {
        /* ignore */
      }
    });
  }

  function startPolling() {
    if (polling) return;
    polling = setInterval(refreshCount, 20000);
    refreshCount();
  }
  function stopPolling() {
    if (polling) clearInterval(polling);
    polling = null;
  }
  $(document).on("visibilitychange", () => {
    if (document.hidden) stopPolling();
    else startPolling();
  });
  startPolling();
})();

// Notifications page (full list + load more + mark all)
(function() {
  const $listEl = $("#pageNotifList");
  const $loadMoreBtn = $("#pageLoadMore");
  const $markAllBtn = $("#pageMarkAll");
  const $bellBadge = $("#notifCount");
  if (!$listEl.length || !$loadMoreBtn.length) return;

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
    const $li = $('<li></li>').addClass(`notif-item ${item.isRead ? "read" : "unread"}`).data('id', item.idNotification || item.id);
    $li.html(`
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
    `);
    $li.on("click", async () => {
      if (!item.isRead) {
        try {
          await apiPost("notifications/mark-read", {
            id: item.idNotification || item.id,
          });
          $li.removeClass("unread");
          $li.addClass("read");
          item.isRead = 1;
          if ($bellBadge.length && !$bellBadge.hasClass("d-none")) {
            const current = parseInt($bellBadge.text() || "0", 10);
            if (current > 1) $bellBadge.text(String(current - 1));
            else $bellBadge.addClass("d-none");
          }
        } catch (_) {
          /* ignore */
        }
      }
      if (item.url) {
        window.location.href = item.url;
      }
    });
    return $li;
  }

  async function loadPage() {
    if (loading || !hasMore) return;
    loading = true;
    $loadMoreBtn.text("Chargement…");
    try {
      const res = await apiGet(
        `notifications/list?page=${page}&limit=${limit}`
      );
      const data = await res.json();
      if (!data || data.success !== true) throw new Error("bad response");

      if (page === 1 && (!data.items || data.items.length === 0)) {
        $listEl.html('<li class="notif-item muted">Aucune notification</li>');
        hasMore = false;
        $loadMoreBtn.addClass("d-none");
        return;
      }

      data.items.forEach((item) => $listEl.append(renderItem(item)));
      hasMore = !!data.hasMore;
      if (!hasMore) $loadMoreBtn.addClass("d-none");
      page += 1;
    } catch (_) {
      if (page === 1) {
        $listEl.html('<li class="notif-item muted">Erreur lors du chargement</li>');
      }
    } finally {
      loading = false;
      $loadMoreBtn.text("Charger plus");
    }
  }

  $loadMoreBtn.on("click", loadPage);

  if ($markAllBtn.length) {
    $markAllBtn.on("click", async () => {
      try {
        const res = await apiPost("notifications/mark-all-read", {});
        const data = await res.json();
        if (data && data.success) {
          $listEl.find(".notif-item.unread").each(function() {
            $(this).removeClass("unread");
            $(this).addClass("read");
          });
          if ($bellBadge.length) $bellBadge.addClass("d-none");
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
$(document).ready(function() {
  // Unified responseMessage handling

  // Function to dismiss any responseMessage
  function dismissResponseMessage($element, isToast = false) {
    if (isToast) {
      $element.removeClass("show");
      $element.addClass("hide");
    } else {
      $element.addClass("fade-out");
    }

    // Wait for animation to complete before removing
    setTimeout(
      function() {
        if ($element.parent().length) {
          $element.remove();

          // Special handling for error list items
          if ($element.hasClass("error-item")) {
            const $ul = $element.closest(".error-list");
            if ($ul.length && $ul.children().length === 0) {
              $ul.remove();
            }
          }
        }
      },
      isToast ? 400 : 0
    );
  }

  // Handle all close buttons
  $(".responseMessage-close").each(function() {
    $(this).on("click", function() {
      const $responseMessage = $(this).closest(".responseMessage");
      const isToast = $responseMessage.hasClass("toast");
      dismissResponseMessage($responseMessage, isToast);
    });
  });

  // Auto-dismiss standard responseMessages
  $(".custom-alert, .error-list").each(function() {
    const $alert = $(this);
    setTimeout(function() {
      dismissResponseMessage($alert, false);
    }, 10000);
  });

  // Handle toast responseMessages
  const $toast = $("#toast");
  if ($toast.length) {
    setTimeout(function() {
      $toast.addClass("show");
    }, 100);

    setTimeout(function() {
      dismissResponseMessage($toast, true);
    }, 7000);
  }
});

