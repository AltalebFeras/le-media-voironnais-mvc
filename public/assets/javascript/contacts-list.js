$(function () {
  // Toggle contact details
  window.toggleContact = function (uiid) {
    const $details = $("#contact-" + uiid);
    const $toggle = $(`[onclick="toggleContact('${uiid}')"] .material-icons`);

    if ($details.is(":hidden")) {
      $details.show();
      $toggle.text("expand_less");
    } else {
      $details.hide();
      $toggle.text("expand_more");
    }
  };

  // Show reply modal
  window.showReplyModal = function (uiid, email, name) {
    $("#replyUiid").val(uiid);
    $("#replyTo").val(email);
    $("#replyToDisplay").text(email);

    const subject = "Re: Votre message de contact";
    $("#replySubject").val(subject);
    $("#replySubjectDisplay").text(subject);

    $("#replyMessage").val(
      `Merci pour votre message.\n\n\nCordialement,\nL'équipe du Média Voironnais`
    );
    $("#replyModal").show();
  };

  // Show delete modal
  window.showDeleteModal = function (uiid, contactName) {
    $("#deleteUiid").val(uiid);
    $("#deleteContactName").text(contactName);
    $("#deleteModal").show();
  };

  // Close reply modal
  window.closeReplyModal = function () {
    $("#replyModal").hide();
    $("#replyForm")[0].reset();
  };

  // Close delete modal
  window.closeDeleteModal = function () {
    $("#deleteModal").hide();
    $("#deleteForm")[0].reset();
  };

  // Close modals when clicking outside
  $("#replyModal").on("click", function (e) {
    if (e.target === this) {
      closeReplyModal();
    }
  });

  $("#deleteModal").on("click", function (e) {
    if (e.target === this) {
      closeDeleteModal();
    }
  });

  // Close modals with Escape key
  $(document).on("keydown", function (e) {
    if (e.key === "Escape") {
      if ($("#replyModal").is(":visible")) {
        closeReplyModal();
      }
      if ($("#deleteModal").is(":visible")) {
        closeDeleteModal();
      }
    }
  });

  // Auto-resize textarea in reply modal
  $("#replyMessage").on("input", function () {
    this.style.height = "auto";
    this.style.height = this.scrollHeight + "px";
  });

  // Form validation for reply modal
  $("#replyForm").on("submit", function (e) {
    const message = $("#replyMessage").val().trim();
    if (message.length < 10) {
      e.preventDefault();
      alert("Le message doit contenir au moins 10 caractères.");
      return false;
    }
  });
});
