/**
 * BOCA SANTA OFERTAS - CORE CLIENT JS
 */

document.addEventListener('DOMContentLoaded', () => {
    initLgpdConsent();
    initCitySelector();
    initSearchAutocomplete();
    initFloatingCta();
});

function initFloatingCta() {
    const floatingCta = document.getElementById('floatingGoogleCta');
    if (!floatingCta) return;

    if (sessionStorage.getItem('bocasanta_hide_floating_cta') === '1') {
        floatingCta.style.display = 'none';
        return;
    }

    const btnClose = document.getElementById('btnCloseFloatingCta');
    if (btnClose) {
        btnClose.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            floatingCta.style.display = 'none';
            sessionStorage.setItem('bocasanta_hide_floating_cta', '1');
        });
    }
}

/* --------------------------------------------------------------------------
   1. LGPD COOKIE CONSENT BANNER & MODAL
   -------------------------------------------------------------------------- */
function initLgpdConsent() {
    const banner = document.getElementById('lgpdCookieBanner');
    if (!banner) return;

    const consentCookie = getCookie('bocasanta_lgpd_consent');
    if (!consentCookie) {
        setTimeout(() => {
            banner.classList.add('show');
        }, 1200);
    }

    const btnAcceptAll = document.getElementById('btnLgpdAcceptAll');
    const btnRejectNonEssential = document.getElementById('btnLgpdReject');
    const btnSaveCustom = document.getElementById('btnLgpdSaveCustom');

    if (btnAcceptAll) {
        btnAcceptAll.addEventListener('click', () => {
            saveConsentPreferences({ essential: true, analytics: true, marketing: true });
            banner.classList.remove('show');
        });
    }

    if (btnRejectNonEssential) {
        btnRejectNonEssential.addEventListener('click', () => {
            saveConsentPreferences({ essential: true, analytics: false, marketing: false });
            banner.classList.remove('show');
        });
    }

    if (btnSaveCustom) {
        btnSaveCustom.addEventListener('click', () => {
            const analytics = document.getElementById('lgpdConsentAnalytics')?.checked ?? false;
            const marketing = document.getElementById('lgpdConsentMarketing')?.checked ?? false;
            saveConsentPreferences({ essential: true, analytics, marketing });
            banner.classList.remove('show');
            const modal = bootstrap.Modal.getInstance(document.getElementById('lgpdPreferencesModal'));
            if (modal) modal.hide();
        });
    }
}

function saveConsentPreferences(prefs) {
    const value = JSON.stringify(prefs);
    setCookie('bocasanta_lgpd_consent', value, 365);

    // Envia assincronamente ao servidor para registro LGPD
    fetch(window.BocaSantaConfig?.baseUrl + 'lgpd/consent', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: value
    }).catch(err => console.debug('LGPD Consent saved locally'));
}

/* --------------------------------------------------------------------------
   2. CITY SELECTOR
   -------------------------------------------------------------------------- */
function initCitySelector() {
    const citySelectButtons = document.querySelectorAll('.select-city-trigger');
    citySelectButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const citySlug = btn.getAttribute('data-city-slug');
            if (citySlug) {
                localStorage.setItem('bocasanta_preferred_city', citySlug);
                setCookie('bocasanta_city_slug', citySlug, 30);
                window.location.href = window.BocaSantaConfig?.baseUrl + citySlug;
            }
        });
    });
}

/* --------------------------------------------------------------------------
   3. SEARCH AUTOCOMPLETE & RECENT SEARCHES
   -------------------------------------------------------------------------- */
function initSearchAutocomplete() {
    const searchInput = document.getElementById('mainSearchInput');
    if (!searchInput) return;

    let timeout = null;
    searchInput.addEventListener('input', (e) => {
        clearTimeout(timeout);
        const query = e.target.value.trim();
        if (query.length < 2) return;

        timeout = setTimeout(() => {
            // Live search hook
        }, 300);
    });
}

/* --------------------------------------------------------------------------
   4. HELPER UTILS (COOKIES)
   -------------------------------------------------------------------------- */
function setCookie(name, value, days) {
    let expires = "";
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (encodeURIComponent(value) || "") + expires + "; path=/; SameSite=Lax";
}

function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
    }
    return null;
}
