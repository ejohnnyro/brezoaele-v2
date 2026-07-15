/**
 * Forms and interactive elements helper for Brezoaele V2 Theme.
 * Handles frontend UI interactions and toggles.
 */
document.addEventListener("DOMContentLoaded", function() {
    // 1. Forum Topic Creation Form Toggle (archive-intrebare.php)
    const toggleFormBtn = document.getElementById("toggle-form-btn");
    const newTopicForm = document.getElementById("new-topic-form");
    
    if (toggleFormBtn && newTopicForm) {
        toggleFormBtn.addEventListener("click", function() {
            if (newTopicForm.style.display === "none" || newTopicForm.style.display === "") {
                newTopicForm.style.display = "block";
                toggleFormBtn.innerText = "✖️ Închide Formularul";
                toggleFormBtn.style.backgroundColor = "#dc2626";
                toggleFormBtn.style.borderColor = "#0f172a";
            } else {
                newTopicForm.style.display = "none";
                toggleFormBtn.innerText = "➕ Propune un Subiect Nou";
                toggleFormBtn.style.backgroundColor = "var(--color-primary)";
                toggleFormBtn.style.borderColor = "var(--color-border)";
            }
        });
    }

    // 2. Mobile Navigation Hamburger Menu Toggle
    const siteNavigation = document.getElementById("site-navigation");
    const menuToggle = document.querySelector(".menu-toggle");
    
    if (siteNavigation && menuToggle) {
        menuToggle.addEventListener("click", function() {
            siteNavigation.classList.toggle("toggled");
            const expanded = siteNavigation.classList.contains("toggled");
            menuToggle.setAttribute("aria-expanded", expanded ? "true" : "false");
        });
    }

    // 3. Mobile Dropdown Submenus Accordion Toggle
    const parentLinks = siteNavigation ? siteNavigation.querySelectorAll(".menu-item-has-children > a") : [];
    parentLinks.forEach(link => {
        // Creare buton de toggle sub-meniu
        const dropdownToggle = document.createElement("button");
        dropdownToggle.className = "dropdown-toggle-btn";
        dropdownToggle.innerHTML = "▾";
        dropdownToggle.setAttribute("aria-expanded", "false");
        dropdownToggle.setAttribute("aria-label", "Deschide Sub-meniu");
        
        // Introducerea lui imediat după link-ul părinte
        link.parentNode.insertBefore(dropdownToggle, link.nextSibling);
        
        dropdownToggle.addEventListener("click", function(e) {
            e.preventDefault();
            const subMenu = link.parentNode.querySelector("ul.sub-menu");
            if (subMenu) {
                subMenu.classList.toggle("toggled-on");
                dropdownToggle.classList.toggle("toggled-on");
                const isExpanded = subMenu.classList.contains("toggled-on");
                dropdownToggle.setAttribute("aria-expanded", isExpanded ? "true" : "false");
            }
        });
    });

    // 4. Fullscreen Search Overlay Toggle
    const searchToggleBtn = document.getElementById('search-toggle-btn');
    const searchOverlay = document.getElementById('fullscreen-search-overlay');
    const searchCloseBtn = document.getElementById('search-close-btn');
    const searchInput = searchOverlay ? searchOverlay.querySelector('.search-field') : null;

    if (searchToggleBtn && searchOverlay && searchCloseBtn) {
        searchToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            searchOverlay.style.display = 'flex';
            searchOverlay.offsetHeight; // Force reflow
            searchOverlay.style.opacity = '1';
            document.body.style.overflow = 'hidden';
            if (searchInput) {
                setTimeout(() => searchInput.focus(), 100);
            }
        });

        const closeOverlay = function() {
            searchOverlay.style.opacity = '0';
            document.body.style.overflow = '';
            setTimeout(() => {
                searchOverlay.style.display = 'none';
            }, 300);
        };

        searchCloseBtn.addEventListener('click', closeOverlay);

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && searchOverlay.style.display === 'flex') {
                closeOverlay();
            }
        });

        // Close on clicking outside container
        searchOverlay.addEventListener('click', function(e) {
            if (e.target === searchOverlay) {
                closeOverlay();
            }
        });
    }

    // 5. GDPR Cookie Consent Banner & Preferences Modal Logic
    const cookieBanner = document.getElementById('cookie-consent-banner');
    const cookieModal = document.getElementById('cookie-preferences-modal');
    const cookieAcceptBtn = document.getElementById('cookie-accept-btn');
    const cookieRejectBtn = document.getElementById('cookie-reject-btn');
    const cookieSettingsBtn = document.getElementById('cookie-settings-btn');
    const openCookieSettingsFooter = document.getElementById('open-cookie-settings-footer');
    const cookieModalClose = document.getElementById('cookie-modal-close');
    const cookieSavePreferencesBtn = document.getElementById('cookie-save-preferences-btn');
    
    const checkAnalytics = document.getElementById('cookie-category-analytics');
    const checkMarketing = document.getElementById('cookie-category-marketing');

    // Predefined dictionary of cookies, their categories, purposes, and legal bases
    const cookieDictionary = [
        { pattern: /^wordpress_(?!test_cookie)/, category: 'Necesare', purpose: 'Menținerea stării de autentificare și a sesiunii administrative WordPress.', legalBasis: 'Interes Legitim / Obligație Legală' },
        { pattern: /^wordpress_test_cookie$/, category: 'Necesare', purpose: 'Verificarea dacă navigatorul utilizatorului acceptă cookies.', legalBasis: 'Interes Legitim' },
        { pattern: /^wp-settings-/, category: 'Necesare', purpose: 'Memorarea preferințelor de aspect și limbă ale utilizatorului.', legalBasis: 'Interes Legitim' },
        { pattern: /^PHPSESSID$/, category: 'Necesare', purpose: 'Sesiunea PHP activă a serverului pentru navigare.', legalBasis: 'Interes Legitim' },
        { pattern: /^cookie_consent_/, category: 'Necesare', purpose: 'Memorarea consimțământului acordat de utilizator privind categoriile de cookie-uri.', legalBasis: 'Obligație Legală' },
        { pattern: /^comment_author_/, category: 'Necesare', purpose: 'Precompletarea numelui și emailului utilizatorului la adăugarea unui comentariu.', legalBasis: 'Consimțământ / Interes Legitim' },
        { pattern: /^_ga$/, category: 'Analitice', purpose: 'Identificator unic utilizat de Google Analytics pentru generarea de date statistice.', legalBasis: 'Consimțământ' },
        { pattern: /^_gid$/, category: 'Analitice', purpose: 'Înregistrarea unui ID unic folosit pentru statistici de trafic pe zi.', legalBasis: 'Consimțământ' },
        { pattern: /^_ga_/, category: 'Analitice', purpose: 'Păstrarea stării sesiunii Google Analytics.', legalBasis: 'Consimțământ' },
        { pattern: /^_fbp$/, category: 'Marketing', purpose: 'Pixel Meta/Facebook utilizat pentru publicitate personalizată și conversii.', legalBasis: 'Consimțământ' },
        { pattern: /^fr$/, category: 'Marketing', purpose: 'Cookie publicitar Meta/Facebook folosit pentru direcționare comportamentală.', legalBasis: 'Consimțământ' },
        { pattern: /^_gcl_au$/, category: 'Marketing', purpose: 'Utilizat de Google AdSense pentru experimente de conversie publicitară.', legalBasis: 'Consimțământ' }
    ];

    // Read cookie utility
    function getCookie(name) {
        let nameEQ = name + "=";
        let ca = document.cookie.split(';');
        for(let i=0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    // Set cookie utility
    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            let date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/; SameSite=Lax; Secure";
    }

    // Delete cookie utility
    function eraseCookie(name) {
        document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT; SameSite=Lax; Secure';
        document.cookie = name + '=; Path=/; Domain=' + window.location.hostname + '; Expires=Thu, 01 Jan 1970 00:00:01 GMT; SameSite=Lax; Secure';
        // Erase with leading dot for subdomains
        const parts = window.location.hostname.split('.');
        if (parts.length >= 2) {
            const domain = '.' + parts.slice(-2).join('.');
            document.cookie = name + '=; Path=/; Domain=' + domain + '; Expires=Thu, 01 Jan 1970 00:00:01 GMT; SameSite=Lax; Secure';
        }
    }

    // Clean up cookies based on user preference
    function enforceCookieBlocking(consent) {
        if (!consent.analytics) {
            const cookies = document.cookie.split(';');
            cookies.forEach(cookie => {
                const name = cookie.split('=')[0].trim();
                if (name.match(/^_ga/) || name.match(/^_gid/)) {
                    eraseCookie(name);
                }
            });
        }
        if (!consent.marketing) {
            const cookies = document.cookie.split(';');
            cookies.forEach(cookie => {
                const name = cookie.split('=')[0].trim();
                if (name.match(/^_fbp/) || name.match(/^fr/) || name.match(/^_gcl/)) {
                    eraseCookie(name);
                }
            });
        }
    }

    // Trigger custom consent event
    function triggerConsentEvent(consent) {
        window.CookieConsent = consent;
        const event = new CustomEvent('cookie-consent-updated', { detail: consent });
        document.dispatchEvent(event);
    }

    // Dynamic cookie scanning and listing in preferences table
    function scanAndDisplayCookies() {
        const tableBody = document.querySelector('#cookie-detection-table tbody');
        if (!tableBody) return;

        const cookiesStr = document.cookie;
        if (!cookiesStr || cookiesStr.trim() === '') {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="4" style="padding: 16px; text-align: center; color: var(--color-text-muted); font-style: italic;">
                        Nu s-au găsit cookie-uri active în acest moment în browser (le poți vedea după ce reîncarci portalul).
                    </td>
                </tr>
            `;
            return;
        }

        const cookiePairs = cookiesStr.split(';');
        let html = '';

        cookiePairs.forEach(pair => {
            const name = pair.split('=')[0].trim();
            if (name === '') return;

            let matched = false;
            let category = 'Statistici / Analitice';
            let purpose = 'Cookie terță parte neclasificat. Utilizat pentru monitorizare și preferințe de sesiune.';
            let legalBasis = 'Consimțământ';

            // Find matching pattern in the dictionary
            for (let i = 0; i < cookieDictionary.length; i++) {
                if (cookieDictionary[i].pattern.test(name)) {
                    category = cookieDictionary[i].category;
                    purpose = cookieDictionary[i].purpose;
                    legalBasis = cookieDictionary[i].legalBasis;
                    matched = true;
                    break;
                }
            }

            html += `
                <tr style="border-bottom: 1px solid var(--color-border);">
                    <td style="padding: 10px 12px; font-weight: 700; color: var(--color-primary-dark);">${name}</td>
                    <td style="padding: 10px 12px; color: var(--color-text-dark);">${window.location.hostname}</td>
                    <td style="padding: 10px 12px;">
                        <span style="background-color: ${category === 'Necesare' ? '#e2e8f0' : 'var(--color-primary-light)'}; color: ${category === 'Necesare' ? '#475569' : 'var(--color-primary-dark)'}; font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 30px; text-transform: uppercase;">
                            ${category}
                        </span>
                    </td>
                    <td style="padding: 10px 12px; color: var(--color-text-muted);">
                        <div>${purpose}</div>
                        <div style="font-size: 0.7rem; font-style: italic; margin-top: 2px; color: var(--color-text-dark);">
                            Bază legală: <b>${legalBasis}</b>
                        </div>
                    </td>
                </tr>
            `;
        });

        tableBody.innerHTML = html;
    }

    // Initialize Consent UI and handlers
    const savedConsentStr = getCookie('cookie_consent_categories');
    let consentState = { essential: true, analytics: false, marketing: false };

    if (savedConsentStr) {
        try {
            consentState = JSON.parse(savedConsentStr);
            triggerConsentEvent(consentState);
            enforceCookieBlocking(consentState);
        } catch (e) {
            // Cookie format error, show banner
            if (cookieBanner) {
                cookieBanner.style.display = 'block';
                setTimeout(() => {
                    cookieBanner.style.transform = 'translateX(-50%) translateY(0)';
                    cookieBanner.style.opacity = '1';
                }, 100);
            }
        }
    } else {
        // No choice saved, show banner
        if (cookieBanner) {
            cookieBanner.style.display = 'block';
            setTimeout(() => {
                cookieBanner.style.transform = 'translateX(-50%) translateY(0)';
                cookieBanner.style.opacity = '1';
            }, 100);
        }
    }

    // Action: Accept All
    if (cookieAcceptBtn) {
        cookieAcceptBtn.addEventListener('click', function() {
            const consent = { essential: true, analytics: true, marketing: true };
            setCookie('cookie_consent_categories', JSON.stringify(consent), 365);
            consentState = consent;
            triggerConsentEvent(consent);
            enforceCookieBlocking(consent);
            
            // Hide banner
            if (cookieBanner) {
                cookieBanner.style.transform = 'translateX(-50%) translateY(100px)';
                cookieBanner.style.opacity = '0';
                setTimeout(() => { cookieBanner.style.display = 'none'; }, 400);
            }
        });
    }

    // Action: Reject All
    if (cookieRejectBtn) {
        cookieRejectBtn.addEventListener('click', function() {
            const consent = { essential: true, analytics: false, marketing: false };
            setCookie('cookie_consent_categories', JSON.stringify(consent), 365);
            consentState = consent;
            triggerConsentEvent(consent);
            enforceCookieBlocking(consent);
            
            // Hide banner
            if (cookieBanner) {
                cookieBanner.style.transform = 'translateX(-50%) translateY(100px)';
                cookieBanner.style.opacity = '0';
                setTimeout(() => { cookieBanner.style.display = 'none'; }, 400);
            }
        });
    }

    // Show/Hide Preferences Modal
    const showModal = function() {
        if (!cookieModal) return;
        
        // Sync checkboxes with current consent state
        if (checkAnalytics) checkAnalytics.checked = consentState.analytics;
        if (checkMarketing) checkMarketing.checked = consentState.marketing;
        
        // Scan current cookies
        scanAndDisplayCookies();

        cookieModal.style.display = 'flex';
        cookieModal.offsetHeight; // Force reflow
        cookieModal.style.opacity = '1';
        
        const modalCard = cookieModal.querySelector('.cookie-modal-card');
        if (modalCard) {
            modalCard.style.transform = 'scale(1)';
        }
    };

    const hideModal = function() {
        if (!cookieModal) return;
        
        cookieModal.style.opacity = '0';
        const modalCard = cookieModal.querySelector('.cookie-modal-card');
        if (modalCard) {
            modalCard.style.transform = 'scale(0.95)';
        }
        
        setTimeout(() => {
            cookieModal.style.display = 'none';
        }, 300);
    };

    if (cookieSettingsBtn) {
        cookieSettingsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showModal();
        });
    }

    if (openCookieSettingsFooter) {
        openCookieSettingsFooter.addEventListener('click', function(e) {
            e.preventDefault();
            showModal();
        });
    }

    if (cookieModalClose) {
        cookieModalClose.addEventListener('click', hideModal);
    }

    if (cookieModal) {
        cookieModal.addEventListener('click', function(e) {
            if (e.target === cookieModal) {
                hideModal();
            }
        });
    }

    // Action: Save Preferences
    if (cookieSavePreferencesBtn) {
        cookieSavePreferencesBtn.addEventListener('click', function() {
            const consent = {
                essential: true,
                analytics: checkAnalytics ? checkAnalytics.checked : false,
                marketing: checkMarketing ? checkMarketing.checked : false
            };
            
            setCookie('cookie_consent_categories', JSON.stringify(consent), 365);
            consentState = consent;
            
            triggerConsentEvent(consent);
            enforceCookieBlocking(consent);
            
            // Hide modal & banner
            hideModal();
            if (cookieBanner) {
                cookieBanner.style.transform = 'translateX(-50%) translateY(100px)';
                cookieBanner.style.opacity = '0';
                setTimeout(() => { cookieBanner.style.display = 'none'; }, 400);
            }
        });
    }
});
