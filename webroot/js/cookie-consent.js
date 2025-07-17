/**
 * Cookie Consent JS
 *
 * Handles the display of the cookie consent banner, user interactions, and AJAX communication
 * with the backend to store and retrieve consent preferences. Also manages setting and reading
 * the consent cookie in the browser.
 *
 * Usage:
 * - Include this script in your layout (usually via the CookieConsentHelper).
 * - The script will automatically show the banner if consent is not yet given.
 * - Handles Accept All, Reject All, and Customize actions.
 */
(() => {
    // Helper: Set consent cookie with the given consent object
    const setConsentCookie = (consentObj) => {
        document.cookie = `cookie_consent=${encodeURIComponent(JSON.stringify(consentObj))};path=/;max-age=31536000`;
    };

    // Helper: Get consent cookie and parse it as an object
    const getConsentCookie = () => {
        const name = "cookie_consent=";
        const decodedCookie = decodeURIComponent(document.cookie);
        const ca = decodedCookie.split(";");
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === " ") c = c.substring(1);
            if (c.indexOf(name) === 0) return JSON.parse(c.substring(name.length, c.length));
        }
        return null;
    };

    // Show the cookie consent banner
    const showBanner = () => {
        const banner = document.getElementById('cookie-consent-banner');
        if (!banner) return;
        banner.style.display = 'flex';
        setTimeout(() => banner.classList.add('visible'), 10);
    };
    // Hide the cookie consent banner
    const hideBanner = () => {
        const banner = document.getElementById('cookie-consent-banner');
        if (!banner) return;
        banner.classList.remove('visible');
        setTimeout(() => (banner.style.display = 'none'), 300);
    };

    // Show the customize modal for granular consent
    const showModal = () => {
        const modal = document.getElementById('cookie-consent-modal');
        if (!modal) return;
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('visible'), 10);
        // Pre-fill checkboxes from consent
        const consent = getConsentCookie() || {};
        const checkboxes = modal.querySelectorAll('.cookie-category-modal');
        checkboxes.forEach(cb => {
            if (cb.disabled) return;
            // If the category is present in consent, use its value; otherwise, default to false
            cb.checked = consent.hasOwnProperty(cb.name) ? !!consent[cb.name] : false;
        });
    };
    // Hide the customize modal
    const hideModal = () => {
        const modal = document.getElementById('cookie-consent-modal');
        if (!modal) return;
        modal.classList.remove('visible');
        setTimeout(() => (modal.style.display = 'none'), 300);
    };

    // AJAX helper: send consent data to the backend and update the cookie
    const postConsent = (url, data, cb) => {
        const csrfToken = document.querySelector('meta[name="csrfToken"]')?.getAttribute('content') || '';
        fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-Token": csrfToken,
            },
            body: JSON.stringify(data),
        })
        .then(response => response.json())
        .then(data => {
            // Update the consent cookie with the categories returned by the backend
            setConsentCookie(data.categories ? data.categories : { essential: true });
            hideBanner();
            hideModal();
            if (cb) cb();
        });
    };

    // Google Consent update (stub for integration with Google Consent Mode)
    const updateGoogleConsent = (granted) => {
        if (typeof gtag !== 'function') return;
        // Example: gtag('consent', 'update', ...)
    };

    // Handler for Accept All button: accept all categories
    const acceptAll = () => {
        // No need to collect checkboxes, just call accept endpoint
        postConsent('/cookie-consent/accept', {}, () => {
            hideBanner();
        });
    };
    // Handler for Reject All button: reject all except essential
    const rejectAll = () => {
        const form = document.getElementById('cookie-consent-form');
        if (!form) return;
        const data = { categories: {} };
        const checkboxes = form.querySelectorAll('.cookie-category');
        checkboxes.forEach(cb => {
            data.categories[cb.name] = cb.disabled ? true : false;
        });
        postConsent('/cookie-consent/reject', data, () => {
            hideBanner();
        });
    };
    // Handler for Customize button: open the modal
    const openCustomize = () => {
        showModal();
    };
    // Handler for Save button in modal: save selected categories
    const saveModal = () => {
        const form = document.getElementById('cookie-consent-modal-form');
        if (!form) return;
        const data = { categories: {} };
        const checkboxes = form.querySelectorAll('.cookie-category-modal');
        checkboxes.forEach(cb => {
            data.categories[cb.name] = cb.checked;
        });
        postConsent('/cookie-consent/customize', data, () => {
            hideBanner();
            hideModal();
        });
    };
    // Handler for Cancel button in modal: close the modal
    const cancelModal = () => {
        hideModal();
    };

    // DOMContentLoaded event: set up event listeners and show banner if needed
    document.addEventListener('DOMContentLoaded', () => {
        const banner = document.getElementById('cookie-consent-banner');
        const modal = document.getElementById('cookie-consent-modal');
        if (!banner) return;
        // If no consent cookie, show the banner
        const consent = getConsentCookie();
        if (!consent) {
            showBanner();
        }
        // Set up button event listeners
        const acceptBtn = banner.querySelector('.cookie-consent-accept');
        const rejectBtn = banner.querySelector('.cookie-consent-reject');
        const customizeBtn = banner.querySelector('.cookie-consent-customize');
        if (acceptBtn) acceptBtn.addEventListener('click', acceptAll);
        if (rejectBtn) rejectBtn.addEventListener('click', rejectAll);
        if (customizeBtn) customizeBtn.addEventListener('click', openCustomize);
        if (modal) {
            const saveBtn = modal.querySelector('.cookie-consent-save-modal');
            const cancelBtn = modal.querySelector('.cookie-consent-cancel-modal');
            if (saveBtn) saveBtn.addEventListener('click', saveModal);
            if (cancelBtn) cancelBtn.addEventListener('click', cancelModal);
        }
    });
    // Expose for revoke: allow opening the modal programmatically
    window.openCookieConsentModal = showModal;
})();