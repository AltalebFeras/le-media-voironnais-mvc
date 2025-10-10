/**
 * Global Banner and Logo Management Script (jQuery Version)
 * Handles banner preview, logo preview, profile picture preview, and form interactions
 * for associations, entreprises, and evenements
 */

$(document).ready(function () {
  // Toggle banner actions visibility - Open Popup
  const $toggleBannerActions = $("#toggleBannerActions");
  const $bannerPopup = $("#bannerPopup");
  const $closeBannerPopup = $("#closeBannerPopup");

  if ($toggleBannerActions.length && $bannerPopup.length) {
    $toggleBannerActions.on("click", function () {
      $bannerPopup.css("display", "flex");
    });
  }

  if ($closeBannerPopup.length && $bannerPopup.length) {
    $closeBannerPopup.on("click", function () {
      $bannerPopup.css("display", "none");
      const $bannerInput = $("#bannerInput");
      if ($bannerInput.length) {
        $bannerInput.val("");
        resetBannerPreview();
      }
    });

    // Close popup when clicking outside
    $bannerPopup.on("click", function (e) {
      if (e.target === $bannerPopup[0]) {
        $bannerPopup.css("display", "none");
        const $bannerInput = $("#bannerInput");
        if ($bannerInput.length) {
          $bannerInput.val("");
          resetBannerPreview();
        }
      }
    });
  }

  // Toggle logo actions visibility - Open Popup
  const $toggleLogoActions = $("#toggleLogoActions");
  const $logoPopup = $("#logoPopup");
  const $closeLogoPopup = $("#closeLogoPopup");

  if ($toggleLogoActions.length && $logoPopup.length) {
    $toggleLogoActions.on("click", function () {
      $logoPopup.css("display", "flex");
    });
  }

  if ($closeLogoPopup.length && $logoPopup.length) {
    $closeLogoPopup.on("click", function () {
      $logoPopup.css("display", "none");
      const $logoInput = $("#logoInput");
      if ($logoInput.length) {
        $logoInput.val("");
        resetLogoPreview();
      }
    });

    // Close popup when clicking outside
    $logoPopup.on("click", function (e) {
      if (e.target === $logoPopup[0]) {
        $logoPopup.css("display", "none");
        const $logoInput = $("#logoInput");
        if ($logoInput.length) {
          $logoInput.val("");
          resetLogoPreview();
        }
      }
    });
  }

  // Toggle profile picture actions visibility - Open Popup (for mon_compte.php)
  const $profilePicturePopup = $("#profilePicturePopup");
  const $closeProfilePopup = $("#closeProfilePopup");

  if ($toggleLogoActions.length && $profilePicturePopup.length && !$logoPopup.length) {
    $toggleLogoActions.on("click", function () {
      $profilePicturePopup.css("display", "flex");
    });
  }

  if ($closeProfilePopup.length && $profilePicturePopup.length) {
    $closeProfilePopup.on("click", function () {
      $profilePicturePopup.css("display", "none");
      const $profilePictureInput = $("#profilePicture");
      if ($profilePictureInput.length) {
        $profilePictureInput.val("");
        resetProfilePreview();
      }
    });

    // Close popup when clicking outside
    $profilePicturePopup.on("click", function (e) {
      if (e.target === $profilePicturePopup[0]) {
        $profilePicturePopup.css("display", "none");
        const $profilePictureInput = $("#profilePicture");
        if ($profilePictureInput.length) {
          $profilePictureInput.val("");
          resetProfilePreview();
        }
      }
    });
  }

  // Banner preview functionality
  const $bannerInput = $("#bannerInput");
  const $bannerSubmitBtn = $("#bannerSubmitBtn");
  const $cancelBannerBtn = $("#cancelBannerBtn");
  const $bannerPreviewModal = $("#bannerPreviewModal");
  const $bannerActionsDefault = $("#bannerActionsDefault");
  const $bannerActionsPreview = $("#bannerActionsPreview");
  const $deleteBannerForm = $("#deleteBannerForm");
  const $modifyBannerLabel = $('label[for="bannerInput"]');
  const originalBannerSrc = $bannerPreviewModal.length && $bannerPreviewModal.prop('tagName') === 'IMG' 
    ? $bannerPreviewModal.attr('src') 
    : null;

  function resetBannerPreview() {
    if ($bannerPreviewModal.length) {
      if (originalBannerSrc && $bannerPreviewModal.prop('tagName') === 'IMG') {
        $bannerPreviewModal.attr('src', originalBannerSrc);
      }
    }
    if ($bannerSubmitBtn.length) $bannerSubmitBtn.addClass("d-none");
    if ($bannerActionsDefault.length) $bannerActionsDefault.removeClass("d-none");
    if ($bannerActionsPreview.length) $bannerActionsPreview.addClass("d-none");
    if ($deleteBannerForm.length) $deleteBannerForm.css("display", "block");
    if ($modifyBannerLabel.length) $modifyBannerLabel.css("display", "inline-block");
  }

  if ($bannerInput.length) {
    $bannerInput.on("change", function (e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          if ($bannerPreviewModal.length) {
            if ($bannerPreviewModal.prop('tagName') === 'IMG') {
              $bannerPreviewModal.attr('src', e.target.result);
            } else {
              const $newImg = $('<img>', {
                id: 'bannerPreviewModal',
                src: e.target.result,
                alt: 'Banner preview',
                css: {
                  'max-width': '100%',
                  'max-height': '300px',
                  'border-radius': '12px',
                  'margin': '0 auto'
                }
              });
              $bannerPreviewModal.replaceWith($newImg);
            }
          }
          if ($bannerSubmitBtn.length) $bannerSubmitBtn.removeClass("d-none");
          if ($deleteBannerForm.length) $deleteBannerForm.css("display", "none");
          if ($modifyBannerLabel.length) $modifyBannerLabel.css("display", "none");
          if ($bannerActionsPreview.length) $bannerActionsPreview.removeClass("d-none");
        };
        reader.readAsDataURL(file);
      } else {
        resetBannerPreview();
      }
    });

    if ($cancelBannerBtn.length) {
      $cancelBannerBtn.on("click", function () {
        $bannerInput.val("");
        resetBannerPreview();
      });
    }
  }

  // Logo preview functionality
  const $logoInput = $("#logoInput");
  const $logoSubmitBtn = $("#logoSubmitBtn");
  const $cancelLogoBtn = $("#cancelLogo");
  const $logoPreviewModal = $("#logoPreviewModal");
  const $logoActionsDefault = $("#logoActionsDefault");
  const $logoActionsPreview = $("#logoActionsPreview");
  const $deleteLogoForm = $("#deleteLogoForm");
  const $modifyLogoLabel = $('label[for="logoInput"]');
  const originalLogoSrc = $logoPreviewModal.length ? $logoPreviewModal.attr('src') : null;

  function resetLogoPreview() {
    if ($logoPreviewModal.length && originalLogoSrc) {
      $logoPreviewModal.attr('src', originalLogoSrc);
    }
    if ($logoSubmitBtn.length) $logoSubmitBtn.addClass("d-none");
    if ($logoActionsDefault.length) $logoActionsDefault.removeClass("d-none");
    if ($logoActionsPreview.length) $logoActionsPreview.addClass("d-none");
    if ($deleteLogoForm.length) $deleteLogoForm.css("display", "block");
    if ($modifyLogoLabel.length) $modifyLogoLabel.css("display", "inline-block");
  }

  if ($logoInput.length) {
    $logoInput.on("change", function (e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          if ($logoPreviewModal.length) {
            $logoPreviewModal.attr('src', e.target.result);
          }
          if ($logoSubmitBtn.length) $logoSubmitBtn.removeClass("d-none");
          if ($deleteLogoForm.length) $deleteLogoForm.css("display", "none");
          if ($modifyLogoLabel.length) $modifyLogoLabel.css("display", "none");
          if ($logoActionsPreview.length) $logoActionsPreview.removeClass("d-none");
        };
        reader.readAsDataURL(file);
      } else {
        resetLogoPreview();
      }
    });

    if ($cancelLogoBtn.length) {
      $cancelLogoBtn.on("click", function () {
        $logoInput.val("");
        resetLogoPreview();
      });
    }
  }

  // Profile picture preview functionality (for mon_compte.php)
  const $profilePictureInput = $("#profilePicture");
  const $profileSubmitBtn = $("#profileSubmitBtn");
  const $cancelProfilePictureBtn = $("#cancelProfilePicture");
  const $profilePicturePreviewModal = $("#profilePicturePreviewModal");
  const $profileActionsDefault = $("#profileActionsDefault");
  const $profileActionsPreview = $("#profileActionsPreview");
  const $deleteProfileForm = $("#deleteProfileForm");
  const $modifyProfilePictureLabel = $('label[for="profilePicture"]');
  const originalProfileSrc = $profilePicturePreviewModal.length ? $profilePicturePreviewModal.attr('src') : "";

  function resetProfilePreview() {
    if ($profilePicturePreviewModal.length) $profilePicturePreviewModal.attr('src', originalProfileSrc);
    if ($profileSubmitBtn.length) $profileSubmitBtn.addClass("d-none");
    if ($profileActionsDefault.length) $profileActionsDefault.removeClass("d-none");
    if ($profileActionsPreview.length) $profileActionsPreview.addClass("d-none");
    if ($deleteProfileForm.length) $deleteProfileForm.css("display", "block");
    if ($modifyProfilePictureLabel.length) $modifyProfilePictureLabel.css("display", "inline-block");
  }

  if ($profilePictureInput.length) {
    $profilePictureInput.on("change", function (e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          if ($profilePicturePreviewModal.length) {
            $profilePicturePreviewModal.attr('src', e.target.result);
          }
          if ($profileSubmitBtn.length) $profileSubmitBtn.removeClass("d-none");
          if ($deleteProfileForm.length) $deleteProfileForm.css("display", "none");
          if ($modifyProfilePictureLabel.length) $modifyProfilePictureLabel.css("display", "none");
          if ($profileActionsPreview.length) $profileActionsPreview.removeClass("d-none");
        };
        reader.readAsDataURL(file);
      } else {
        resetProfilePreview();
      }
    });

    if ($cancelProfilePictureBtn.length) {
      $cancelProfilePictureBtn.on("click", function () {
        $profilePictureInput.val("");
        resetProfilePreview();
      });
    }
  }
});
