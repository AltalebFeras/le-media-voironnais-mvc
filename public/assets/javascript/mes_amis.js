// Friends Search Modal (moved from mes_amis.php view)
(function () {
  let searchTimeout;

  // Helper function to escape HTML
  function escapeHtml(str) {
    const map = {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;",
    };
    return String(str).replace(/[&<>"']/g, (m) => map[m]);
  }

  // Global functions - modal already exists in PHP
  window.openSearchModal = function () {
    const $modal = $("#searchModal");
    if (!$modal.length) {
      console.warn("searchModal not found in DOM");
      return;
    }
    $modal.addClass("show");
    $("#userSearch").val("").focus();
    $(".searchFriendsResults").html("");
  };

  window.closeSearchModal = function () {
    const $modal = $("#searchModal");
    if (!$modal.length) return;
    $modal.removeClass("show");
    $("#userSearch").val("");
    $(".searchFriendsResults").html("");
  };

  window.openFriendOptionsModal = function (friendUiid, friendName) {
    const $modal = $("#friendOptionsModal");
    if (!$modal.length) {
      console.warn("friendOptionsModal not found in DOM");
      return;
    }
    $("#friendOptionsTitle").text(friendName);
    $("#removeFriendUiid").val(friendUiid);
    $("#blockFriendUiid").val(friendUiid);
    
    // Set form actions dynamically
    $("#removeFriendForm").attr("action", (window.HOME_URL || "/") + "amis/supprimer");
    $("#blockFriendForm").attr("action", (window.HOME_URL || "/") + "amis/bloquer");
    
    $modal.addClass("show");
  };

  window.closeFriendOptionsModal = function () {
    const $modal = $("#friendOptionsModal");
    if (!$modal.length) return;
    $modal.removeClass("show");
  };

  // Initialize on page load
  $(document).ready(function () {
    // Verify modals exist
    if (!$("#searchModal").length) {
      console.warn("searchModal not found - modal HTML may not be in DOM");
    }
    if (!$("#friendOptionsModal").length) {
      console.warn("friendOptionsModal not found - modal HTML may not be in DOM");
    }

    // Tab functionality
    $(document).on("click", ".tab-btn", function () {
      const tabName = $(this).data("tab");
      $(".tab-btn").removeClass("active");
      $(this).addClass("active");
      $(".tab-content").removeClass("active");
      $("#" + tabName + "-tab").addClass("active");
    });

    // User search input with fetch API
    $(document).on("input", "#userSearch", function () {
      clearTimeout(searchTimeout);
      const query = $(this).val().trim();

      if (query.length < 2) {
        $(".searchFriendsResults").html("");
        return;
      }

      searchTimeout = setTimeout(() => {
        performUserSearch(query);
      }, 300);
    });

    // Friend options buttons
    $(document).on("click", ".friend-options-btn", function () {
      const friendUiid = $(this).data("friend-uiid");
      const friendName = $(this).data("friend-name");
      window.openFriendOptionsModal(friendUiid, friendName);
    });

    // Close modals on outside click
    $(document).on("click", ".modal-overlay", function (e) {
      if ($(e.target).is(this)) {
        if ($(this).is("#searchModal")) {
          window.closeSearchModal();
        } else if ($(this).is("#friendOptionsModal")) {
          window.closeFriendOptionsModal();
        }
      }
    });

    // Close modals with Escape key
    $(document).on("keydown", function (e) {
      if (e.key === "Escape") {
        if ($("#searchModal").hasClass("show")) {
          window.closeSearchModal();
        }
        if ($("#friendOptionsModal").hasClass("show")) {
          window.closeFriendOptionsModal();
        }
      }
    });
  });

  // Fetch function using fetch API
  async function performUserSearch(query) {
    const $resultsDiv = $(".searchFriendsResults");
    
    // Safety check
    if (!$resultsDiv.length) {
      console.error("searchFriendsResults div not found in DOM");
      return;
    }

    $resultsDiv.html(
      '<div style="text-align: center; color: #6b7280; padding: 1rem;">Recherche en cours...</div>'
    );

    try {
      const formData = new FormData();
      formData.append("query", query);

      // Use window.HOME_URL if defined, fallback to root
      const apiUrl = (window.HOME_URL || "/") + "amis/rechercher";
      console.log("Fetching from:", apiUrl);

      const response = await fetch(apiUrl, {
        method: "POST",
        body: formData,
        headers: {
          Accept: "application/json",
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      console.log("Search response:", data);

      if (data.success && data.users && data.users.length > 0) {
        let resultsHtml = "";
        const defaultAvatar = (window.BASE_URL || "") + (window.HOME_URL || "/") + "assets/images/uploads/avatars/default_avatar.png";
        const csrfToken = $('input[name="csrf_token"]').val() || "";

        data.users.forEach((user) => {
          const avatar = user.avatar || defaultAvatar;
          const bio = user.bio
            ? `<p class="search-user-bio">${escapeHtml(user.bio)}</p>`
            : "";

          resultsHtml += `
            <div class="search-result">
              <div class="search-user-info">
                <img 
                  src="${escapeHtml(avatar)}" 
                  alt="Avatar" 
                  class="search-user-avatar" 
                  onerror="this.src='${escapeHtml(defaultAvatar)}'"
                >
                <div>
                  <h4 class="search-user-name">${escapeHtml(user.name)}</h4>
                  ${bio}
                </div>
              </div>
              <form method="POST" action="${(window.HOME_URL || "/")}amis/ajouter" style="margin: 0; display: inline;">
                <input type="hidden" name="csrf_token" value="${escapeHtml(csrfToken)}">
                <input type="hidden" name="friend_uiid" value="${escapeHtml(user.uiid)}">
                <button type="submit" class="add-user-btn">
                  Ajouter
                </button>
              </form>
            </div>
          `;
        });
        
        $resultsDiv.html(resultsHtml);
        console.log("Results displayed successfully, count:", data.users.length);
      } else {
        $resultsDiv.html(
          '<div style="text-align: center; color: #6b7280; padding: 1rem;">Aucun utilisateur trouvé</div>'
        );
      }
    } catch (error) {
      console.error("Search error:", error);
      $resultsDiv.html(
        '<div style="text-align: center; color: #dc2626; padding: 1rem;">Erreur lors de la recherche: ' + escapeHtml(error.message) + '</div>'
      );
    }
  }
})();
