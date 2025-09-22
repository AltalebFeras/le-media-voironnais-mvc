/**
 * Global Banner and Logo Management Script
 * Handles banner preview, logo preview, and form interactions
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

    if (logoInput && currentLogo) {
        // Store the original logo source
        const originalLogoSrc = currentLogo.dataset.originalSrc || currentLogo.src;
        
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    currentLogo.src = e.target.result;
                    if (cancelLogo) cancelLogo.style.display = 'inline-block';
                };
                reader.readAsDataURL(file);
            }
        });

        if (cancelLogo) {
            cancelLogo.addEventListener('click', function() {
                logoInput.value = '';
                currentLogo.src = originalLogoSrc;
                cancelLogo.style.display = 'none';
            });
        }
    }
});
