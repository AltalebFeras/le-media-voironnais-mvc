/**
 * Global Banner and Logo Management Script
 * Handles banner preview, logo preview, profile picture preview, and form interactions
 * for associations, entreprises, and evenements
 */

document.addEventListener("DOMContentLoaded", function () {
  // Toggle banner actions visibility
  const toggleBannerActions = document.getElementById("toggleBannerActions");
  const actionBanner = document.querySelector(".action-banner");

  if (toggleBannerActions && actionBanner) {
    toggleBannerActions.addEventListener("click", function () {
      actionBanner.classList.toggle("d-none");
    });
  }

  // Toggle logo actions visibility - Open Popup
  const toggleLogoActions = document.getElementById("toggleLogoActions");
  const profilePicturePopup = document.getElementById("profilePicturePopup");
  const closeProfilePopup = document.getElementById("closeProfilePopup");

  if (toggleLogoActions && profilePicturePopup) {
    toggleLogoActions.addEventListener("click", function () {
      profilePicturePopup.style.display = "flex";
    });
  }

  if (closeProfilePopup && profilePicturePopup) {
    closeProfilePopup.addEventListener("click", function () {
      profilePicturePopup.style.display = "none";
      // Reset preview if exists
      const profilePictureInput = document.getElementById("profilePicture");
      if (profilePictureInput) {
        profilePictureInput.value = "";
        resetProfilePreview();
      }
    });

    // Close popup when clicking outside
    profilePicturePopup.addEventListener("click", function (e) {
      if (e.target === profilePicturePopup) {
        profilePicturePopup.style.display = "none";
        const profilePictureInput = document.getElementById("profilePicture");
        if (profilePictureInput) {
          profilePictureInput.value = "";
          resetProfilePreview();
        }
      }
    });
  }

  // Banner preview functionality
  const bannerInput = document.getElementById("bannerInput");
  const bannerPreview = document.getElementById("bannerPreview");
  const currentBanner = document.getElementById("currentBanner");
  const bannerSubmitBtn = document.getElementById("bannerSubmitBtn");
  const cancelBannerBtn = document.getElementById("cancelBannerBtn");

  if (bannerInput) {
    bannerInput.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          bannerPreview.src = e.target.result;
          bannerPreview.style.display = "block";
          currentBanner.style.display = "none";
          if (bannerSubmitBtn) bannerSubmitBtn.disabled = false;
          if (cancelBannerBtn) cancelBannerBtn.style.display = "inline-block";
        };
        reader.readAsDataURL(file);
      }
    });

    if (cancelBannerBtn) {
      cancelBannerBtn.addEventListener("click", function () {
        bannerPreview.style.display = "none";
        currentBanner.style.display = "block";
        bannerInput.value = "";
        if (bannerSubmitBtn) bannerSubmitBtn.disabled = true;
        cancelBannerBtn.style.display = "none";
      });
    }
  }

  // Logo preview functionality
  const logoInput = document.getElementById("logoInput");
  const currentLogo = document.getElementById("currentLogo");
  const cancelLogo = document.getElementById("cancelLogo");
  const logoSubmitBtn = logoInput
    ? logoInput.closest("form").querySelector('button[type="submit"]')
    : null;

  if (logoInput && currentLogo) {
    // Store the original logo source
    const originalLogoSrc = currentLogo.dataset.originalSrc || currentLogo.src;

    // Initially hide the submit button
    if (logoSubmitBtn) logoSubmitBtn.style.display = "none";

    logoInput.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          currentLogo.src = e.target.result;
          if (cancelLogo) cancelLogo.style.display = "inline-block";
          if (logoSubmitBtn) logoSubmitBtn.style.display = "inline-block";
        };
        reader.readAsDataURL(file);
      } else {
        // No file selected
        if (logoSubmitBtn) logoSubmitBtn.style.display = "none";
        if (cancelLogo) cancelLogo.style.display = "none";
      }
    });

    if (cancelLogo) {
      cancelLogo.addEventListener("click", function () {
        logoInput.value = "";
        currentLogo.src = originalLogoSrc;
        cancelLogo.style.display = "none";
        if (logoSubmitBtn) logoSubmitBtn.style.display = "none";
      });
    }
  }

  // Profile picture preview functionality
  const profilePictureInput = document.getElementById("profilePicture");
  const profilePictureForm = document.getElementById("profilePictureForm");
  const profileSubmitBtn = document.getElementById("profileSubmitBtn");
  const cancelProfilePictureBtn = document.getElementById(
    "cancelProfilePicture"
  );
  const profilePicturePreviewModal = document.getElementById(
    "profilePicturePreviewModal"
  );
  const profileActionsDefault = document.getElementById(
    "profileActionsDefault"
  );
  const profileActionsPreview = document.getElementById(
    "profileActionsPreview"
  );
  const deleteProfileForm = document.getElementById("deleteProfileForm");
  const modifyProfilePictureLabel = document.querySelector('label[for="profilePicture"]');
  const originalProfileSrc = profilePicturePreviewModal
    ? profilePicturePreviewModal.src
    : "";

  function resetProfilePreview() {
    if (profilePicturePreviewModal)
      profilePicturePreviewModal.src = originalProfileSrc;
    if (profileSubmitBtn) profileSubmitBtn.classList.add("d-none");
    if (profileActionsDefault) profileActionsDefault.classList.remove("d-none");
    if (profileActionsPreview) profileActionsPreview.classList.add("d-none");
    if (deleteProfileForm) deleteProfileForm.style.display = "block";
    if (modifyProfilePictureLabel) modifyProfilePictureLabel.style.display = "inline-block";
  }

  if (profilePictureInput) {
    profilePictureInput.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          if (profilePicturePreviewModal) {
            profilePicturePreviewModal.src = e.target.result;
          }
          if (profileSubmitBtn) profileSubmitBtn.classList.remove("d-none");
          if (deleteProfileForm) deleteProfileForm.style.display = "none";
          if (modifyProfilePictureLabel) modifyProfilePictureLabel.style.display = "none";
          if (profileActionsPreview)
            profileActionsPreview.classList.remove("d-none");
        };
        reader.readAsDataURL(file);
      } else {
        resetProfilePreview();
      }
    });

    if (cancelProfilePictureBtn) {
      cancelProfilePictureBtn.addEventListener("click", function () {
        profilePictureInput.value = "";
        resetProfilePreview();
      });
    }
  }
});
   
if (cancelProfilePictureBtn) {
  cancelProfilePictureBtn.addEventListener("click", function () {
    profilePictureInput.value = "";
    profilePicturePreview.style.display = "none";
    if (currentProfilePicture) currentProfilePicture.style.display = "block";
    if (profilePictureSubmitBtn) {
      profilePictureSubmitBtn.disabled = true;
      profilePictureSubmitBtn.classList.add("d-none");
    }
    cancelProfilePictureBtn.style.display = "none";
    // Show modifier photo label and supprimer button
    if (modifyProfilePictureLabel) {
      modifyProfilePictureLabel.style.display = "inline-block";
    }
    if (deleteProfilePictureBtn) {
      deleteProfilePictureBtn.style.display = "block";
    }
  });
}
