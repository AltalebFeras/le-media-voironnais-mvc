/**
 * Global Banner and Logo Management Script
 * Handles banner preview, logo preview, profile picture preview, and form interactions
 * for associations, entreprises, and evenements
 */

document.addEventListener('DOMContentLoaded', function() {
    // Banner preview functionality
    const bannerInput = document.getElementById('bannerInput');
    const bannerPreview = document.getElementById('bannerPreview');
    const currentBanner = document.getElementById('currentBanner');
    const bannerSubmitBtn = document.getElementById('bannerSubmitBtn');
    const cancelBannerBtn = document.getElementById('cancelBannerBtn');

    if (bannerInput) {
        bannerInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    bannerPreview.src = e.target.result;
                    bannerPreview.style.display = 'block';
                    currentBanner.style.display = 'none';
                    if (bannerSubmitBtn) bannerSubmitBtn.disabled = false;
                    if (cancelBannerBtn) cancelBannerBtn.style.display = 'inline-block';
                };
                reader.readAsDataURL(file);
            }
        });

        if (cancelBannerBtn) {
            cancelBannerBtn.addEventListener('click', function() {
                bannerPreview.style.display = 'none';
                currentBanner.style.display = 'block';
                bannerInput.value = '';
                if (bannerSubmitBtn) bannerSubmitBtn.disabled = true;
                cancelBannerBtn.style.display = 'none';
            });
        }
    }

    // Logo preview functionality
    const logoInput = document.getElementById('logoInput');
    const currentLogo = document.getElementById('currentLogo');
    const cancelLogo = document.getElementById('cancelLogo');
    const logoSubmitBtn = logoInput ? logoInput.closest('form').querySelector('button[type="submit"]') : null;

    if (logoInput && currentLogo) {
        // Store the original logo source
        const originalLogoSrc = currentLogo.dataset.originalSrc || currentLogo.src;
        
        // Initially hide the submit button
        if (logoSubmitBtn) logoSubmitBtn.style.display = 'none';
        
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    currentLogo.src = e.target.result;
                    if (cancelLogo) cancelLogo.style.display = 'inline-block';
                    if (logoSubmitBtn) logoSubmitBtn.style.display = 'inline-block';
                };
                reader.readAsDataURL(file);
            } else {
                // No file selected
                if (logoSubmitBtn) logoSubmitBtn.style.display = 'none';
                if (cancelLogo) cancelLogo.style.display = 'none';
            }
        });

        if (cancelLogo) {
            cancelLogo.addEventListener('click', function() {
                logoInput.value = '';
                currentLogo.src = originalLogoSrc;
                cancelLogo.style.display = 'none';
                if (logoSubmitBtn) logoSubmitBtn.style.display = 'none';
            });
        }
    }

    // Profile picture preview functionality
    const profilePictureInput = document.getElementById('profilePicture');
    const profilePictureSubmitBtn = profilePictureInput ? profilePictureInput.closest('form').querySelector('button[type="submit"]') : null;
    const cancelProfilePictureBtn = document.getElementById('cancelProfilePicture');
    const currentProfilePicture = document.getElementById('currentProfilePicture');

    if (profilePictureInput) {
        // Create preview image element if not exists
        let profilePicturePreview = document.getElementById('profilePicturePreview');
        if (!profilePicturePreview) {
            profilePicturePreview = document.createElement('img');
            profilePicturePreview.id = 'profilePicturePreview';
            profilePicturePreview.style.display = 'none';
            profilePicturePreview.style.maxWidth = '180px';
            profilePicturePreview.style.maxHeight = '180px';
            profilePicturePreview.style.margin = '1rem auto';
            profilePicturePreview.style.borderRadius = '50%';
            const profilePictureDiv = profilePictureInput.closest('.account-profile-picture');
            if (profilePictureDiv) {
                profilePictureDiv.insertBefore(profilePicturePreview, profilePictureDiv.firstChild);
            }
        }

        // Initially disable submit button and hide cancel button
        if (profilePictureSubmitBtn) profilePictureSubmitBtn.disabled = true;
        if (cancelProfilePictureBtn) cancelProfilePictureBtn.style.display = 'none';

        profilePictureInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePicturePreview.src = e.target.result;
                    profilePicturePreview.style.display = 'block';
                    if (currentProfilePicture) currentProfilePicture.style.display = 'none';
                    if (profilePictureSubmitBtn) profilePictureSubmitBtn.disabled = false;
                    if (cancelProfilePictureBtn) cancelProfilePictureBtn.style.display = 'inline-block';
                };
                reader.readAsDataURL(file);
            } else {
                // No file selected
                profilePicturePreview.style.display = 'none';
                if (currentProfilePicture) currentProfilePicture.style.display = 'block';
                if (profilePictureSubmitBtn) profilePictureSubmitBtn.disabled = true;
                if (cancelProfilePictureBtn) cancelProfilePictureBtn.style.display = 'none';
            }
        });

        if (cancelProfilePictureBtn) {
            cancelProfilePictureBtn.addEventListener('click', function() {
                profilePictureInput.value = '';
                profilePicturePreview.style.display = 'none';
                if (currentProfilePicture) currentProfilePicture.style.display = 'block';
                if (profilePictureSubmitBtn) profilePictureSubmitBtn.disabled = true;
                cancelProfilePictureBtn.style.display = 'none';
            });
        }
    }
});
