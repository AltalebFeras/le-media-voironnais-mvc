/**
 * Global Banner and Logo Management Script
 * Handles banner preview, logo preview, profile picture preview, and form interactions
 * for associations, entreprises, and evenements
 */

document.addEventListener("DOMContentLoaded", function () {
  // Toggle banner actions visibility - Open Popup
  const toggleBannerActions = document.getElementById("toggleBannerActions");
  const bannerPopup = document.getElementById("bannerPopup");
  const closeBannerPopup = document.getElementById("closeBannerPopup");

  if (toggleBannerActions && bannerPopup) {
    toggleBannerActions.addEventListener("click", function () {
      bannerPopup.style.display = "flex";
    });
  }

  if (closeBannerPopup && bannerPopup) {
    closeBannerPopup.addEventListener("click", function () {
      bannerPopup.style.display = "none";
      const bannerInput = document.getElementById("bannerInput");
      if (bannerInput) {
        bannerInput.value = "";
        resetBannerPreview();
      }
    });

    // Close popup when clicking outside
    bannerPopup.addEventListener("click", function (e) {
      if (e.target === bannerPopup) {
        bannerPopup.style.display = "none";
        const bannerInput = document.getElementById("bannerInput");
        if (bannerInput) {
          bannerInput.value = "";
          resetBannerPreview();
        }
      }
    });
  }

  // Toggle logo actions visibility - Open Popup
  const toggleLogoActions = document.getElementById("toggleLogoActions");
  const logoPopup = document.getElementById("logoPopup");
  const closeLogoPopup = document.getElementById("closeLogoPopup");

  if (toggleLogoActions && logoPopup) {
    toggleLogoActions.addEventListener("click", function () {
      logoPopup.style.display = "flex";
    });
  }

  if (closeLogoPopup && logoPopup) {
    closeLogoPopup.addEventListener("click", function () {
      logoPopup.style.display = "none";
      const logoInput = document.getElementById("logoInput");
      if (logoInput) {
        logoInput.value = "";
        resetLogoPreview();
      }
    });

    // Close popup when clicking outside
    logoPopup.addEventListener("click", function (e) {
      if (e.target === logoPopup) {
        logoPopup.style.display = "none";
        const logoInput = document.getElementById("logoInput");
        if (logoInput) {
          logoInput.value = "";
          resetLogoPreview();
        }
      }
    });
  }

  // Toggle profile picture actions visibility - Open Popup (for mon_compte.php)
  const profilePicturePopup = document.getElementById("profilePicturePopup");
  const closeProfilePopup = document.getElementById("closeProfilePopup");

  if (toggleLogoActions && profilePicturePopup && !logoPopup) {
    toggleLogoActions.addEventListener("click", function () {
      profilePicturePopup.style.display = "flex";
    });
  }

  if (closeProfilePopup && profilePicturePopup) {
    closeProfilePopup.addEventListener("click", function () {
      profilePicturePopup.style.display = "none";
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
  const bannerSubmitBtn = document.getElementById("bannerSubmitBtn");
  const cancelBannerBtn = document.getElementById("cancelBannerBtn");
  const bannerPreviewModal = document.getElementById("bannerPreviewModal");
  const bannerActionsDefault = document.getElementById("bannerActionsDefault");
  const bannerActionsPreview = document.getElementById("bannerActionsPreview");
  const deleteBannerForm = document.getElementById("deleteBannerForm");
  const modifyBannerLabel = document.querySelector('label[for="bannerInput"]');
  const originalBannerSrc = bannerPreviewModal && bannerPreviewModal.tagName === 'IMG' 
    ? bannerPreviewModal.src 
    : null;

  function resetBannerPreview() {
    if (bannerPreviewModal) {
      if (originalBannerSrc && bannerPreviewModal.tagName === 'IMG') {
        bannerPreviewModal.src = originalBannerSrc;
      }
    }
    if (bannerSubmitBtn) bannerSubmitBtn.classList.add("d-none");
    if (bannerActionsDefault) bannerActionsDefault.classList.remove("d-none");
    if (bannerActionsPreview) bannerActionsPreview.classList.add("d-none");
    if (deleteBannerForm) deleteBannerForm.style.display = "block";
    if (modifyBannerLabel) modifyBannerLabel.style.display = "inline-block";
  }

  if (bannerInput) {
    bannerInput.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          if (bannerPreviewModal) {
            if (bannerPreviewModal.tagName === 'IMG') {
              bannerPreviewModal.src = e.target.result;
            } else {
              const newImg = document.createElement('img');
              newImg.id = 'bannerPreviewModal';
              newImg.src = e.target.result;
              newImg.alt = 'Banner preview';
              newImg.style.cssText = 'max-width: 100%; max-height: 300px; border-radius: 12px; margin: 0 auto;';
              bannerPreviewModal.parentNode.replaceChild(newImg, bannerPreviewModal);
            }
          }
          if (bannerSubmitBtn) bannerSubmitBtn.classList.remove("d-none");
          if (deleteBannerForm) deleteBannerForm.style.display = "none";
          if (modifyBannerLabel) modifyBannerLabel.style.display = "none";
          if (bannerActionsPreview) bannerActionsPreview.classList.remove("d-none");
        };
        reader.readAsDataURL(file);
      } else {
        resetBannerPreview();
      }
    });

    if (cancelBannerBtn) {
      cancelBannerBtn.addEventListener("click", function () {
        bannerInput.value = "";
        resetBannerPreview();
      });
    }
  }

  // Logo preview functionality
  const logoInput = document.getElementById("logoInput");
  const logoSubmitBtn = document.getElementById("logoSubmitBtn");
  const cancelLogoBtn = document.getElementById("cancelLogo");
  const logoPreviewModal = document.getElementById("logoPreviewModal");
  const logoActionsDefault = document.getElementById("logoActionsDefault");
  const logoActionsPreview = document.getElementById("logoActionsPreview");
  const deleteLogoForm = document.getElementById("deleteLogoForm");
  const modifyLogoLabel = document.querySelector('label[for="logoInput"]');
  const originalLogoSrc = logoPreviewModal ? logoPreviewModal.src : null;

  function resetLogoPreview() {
    if (logoPreviewModal && originalLogoSrc) {
      logoPreviewModal.src = originalLogoSrc;
    }
    if (logoSubmitBtn) logoSubmitBtn.classList.add("d-none");
    if (logoActionsDefault) logoActionsDefault.classList.remove("d-none");
    if (logoActionsPreview) logoActionsPreview.classList.add("d-none");
    if (deleteLogoForm) deleteLogoForm.style.display = "block";
    if (modifyLogoLabel) modifyLogoLabel.style.display = "inline-block";
  }

  if (logoInput) {
    logoInput.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          if (logoPreviewModal) {
            logoPreviewModal.src = e.target.result;
          }
          if (logoSubmitBtn) logoSubmitBtn.classList.remove("d-none");
          if (deleteLogoForm) deleteLogoForm.style.display = "none";
          if (modifyLogoLabel) modifyLogoLabel.style.display = "none";
          if (logoActionsPreview) logoActionsPreview.classList.remove("d-none");
        };
        reader.readAsDataURL(file);
      } else {
        resetLogoPreview();
      }
    });

    if (cancelLogoBtn) {
      cancelLogoBtn.addEventListener("click", function () {
        logoInput.value = "";
        resetLogoPreview();
      });
    }
  }

  // Profile picture preview functionality (for mon_compte.php)
  const profilePictureInput = document.getElementById("profilePicture");
  const profileSubmitBtn = document.getElementById("profileSubmitBtn");
  const cancelProfilePictureBtn = document.getElementById("cancelProfilePicture");
  const profilePicturePreviewModal = document.getElementById("profilePicturePreviewModal");
  const profileActionsDefault = document.getElementById("profileActionsDefault");
  const profileActionsPreview = document.getElementById("profileActionsPreview");
  const deleteProfileForm = document.getElementById("deleteProfileForm");
  const modifyProfilePictureLabel = document.querySelector('label[for="profilePicture"]');
  const originalProfileSrc = profilePicturePreviewModal ? profilePicturePreviewModal.src : "";

  function resetProfilePreview() {
    if (profilePicturePreviewModal) profilePicturePreviewModal.src = originalProfileSrc;
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
          if (profileActionsPreview) profileActionsPreview.classList.remove("d-none");
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
