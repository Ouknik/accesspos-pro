/**
 * AccessPos Pro - Accessibility (A11y) JavaScript Enhancements
 * Comprehensive accessibility features for better user experience
 */

class AccessPosAccessibility {
    constructor() {
        this.preferences = this.loadPreferences();
        this.liveRegions = new Map();
        this.focusHistory = [];
        this.modalStack = [];
        
        this.init();
    }

    init() {
        this.setupLiveRegions();
        this.setupFocusManagement();
        this.setupKeyboardNavigation();
        this.setupScreenReaderEnhancements();
        this.setupARIALiveRegions();
        this.setupModalAccessibility();
        this.setupFormAccessibility();
        this.setupTableAccessibility();
        this.setupTooltipAccessibility();
        this.setupColorContrastChecker();
        this.setupUserPreferences();
        this.setupSkipLinks();
        this.setupLandmarkNavigation();
    }

    /**
     * Load user accessibility preferences
     */
    loadPreferences() {
        const defaults = {
            reducedMotion: false,
            highContrast: false,
            largeText: false,
            focusIndicators: true,
            screenReaderMode: false,
            keyboardOnly: false
        };

        try {
            const saved = localStorage.getItem('accesspos-a11y-preferences');
            return saved ? { ...defaults, ...JSON.parse(saved) } : defaults;
        } catch {
            return defaults;
        }
    }

    /**
     * Save user accessibility preferences
     */
    savePreferences() {
        try {
            localStorage.setItem('accesspos-a11y-preferences', JSON.stringify(this.preferences));
        } catch (error) {
            console.warn('Unable to save accessibility preferences:', error);
        }
    }

    /**
     * Setup live regions for dynamic content announcements
     */
    setupLiveRegions() {
        // Create polite live region
        const politeRegion = document.createElement('div');
        politeRegion.id = 'live-region-polite';
        politeRegion.className = 'live-region';
        politeRegion.setAttribute('aria-live', 'polite');
        politeRegion.setAttribute('aria-atomic', 'true');
        document.body.appendChild(politeRegion);
        this.liveRegions.set('polite', politeRegion);

        // Create assertive live region
        const assertiveRegion = document.createElement('div');
        assertiveRegion.id = 'live-region-assertive';
        assertiveRegion.className = 'live-region';
        assertiveRegion.setAttribute('aria-live', 'assertive');
        assertiveRegion.setAttribute('aria-atomic', 'true');
        document.body.appendChild(assertiveRegion);
        this.liveRegions.set('assertive', assertiveRegion);

        // Create status region
        const statusRegion = document.createElement('div');
        statusRegion.id = 'live-region-status';
        statusRegion.className = 'live-region';
        statusRegion.setAttribute('role', 'status');
        statusRegion.setAttribute('aria-atomic', 'true');
        document.body.appendChild(statusRegion);
        this.liveRegions.set('status', statusRegion);
    }

    /**
     * Announce message to screen readers
     */
    announce(message, type = 'polite') {
        const region = this.liveRegions.get(type);
        if (region) {
            // Clear first to ensure announcement
            region.textContent = '';
            setTimeout(() => {
                region.textContent = message;
            }, 100);

            // Clear after announcement
            setTimeout(() => {
                region.textContent = '';
            }, 1000);
        }
    }

    /**
     * Setup focus management
     */
    setupFocusManagement() {
        // Track focus history
        document.addEventListener('focusin', (event) => {
            this.focusHistory.push(event.target);
            // Keep only last 10 focused elements
            if (this.focusHistory.length > 10) {
                this.focusHistory.shift();
            }
        });

        // Handle focus trapping in modals
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Tab' && this.modalStack.length > 0) {
                this.handleModalFocusTrap(event);
            }
        });

        // Enhanced focus indicators
        this.setupFocusIndicators();
    }

    /**
     * Setup enhanced focus indicators
     */
    setupFocusIndicators() {
        if (this.preferences.focusIndicators) {
            document.body.classList.add('enhanced-focus');
        }

        // Detect keyboard vs mouse navigation
        let isKeyboardUser = false;

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Tab') {
                isKeyboardUser = true;
                document.body.classList.add('keyboard-nav');
            }
        });

        document.addEventListener('mousedown', () => {
            isKeyboardUser = false;
            document.body.classList.remove('keyboard-nav');
        });
    }

    /**
     * Handle modal focus trapping
     */
    handleModalFocusTrap(event) {
        const modal = this.modalStack[this.modalStack.length - 1];
        if (!modal) return;

        const focusableElements = modal.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );

        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        if (event.shiftKey) {
            if (document.activeElement === firstElement) {
                event.preventDefault();
                lastElement.focus();
            }
        } else {
            if (document.activeElement === lastElement) {
                event.preventDefault();
                firstElement.focus();
            }
        }
    }

    /**
     * Setup keyboard navigation enhancements
     */
    setupKeyboardNavigation() {
        // Arrow key navigation for menus
        this.setupMenuNavigation();
        
        // Tab navigation improvements
        this.setupTabNavigation();
        
        // Custom keyboard shortcuts
        this.setupCustomShortcuts();
    }

    /**
     * Setup menu navigation with arrow keys
     */
    setupMenuNavigation() {
        document.addEventListener('keydown', (event) => {
            const menu = event.target.closest('[role="menu"], [role="menubar"]');
            if (!menu) return;

            const items = menu.querySelectorAll('[role="menuitem"], [role="menuitemcheckbox"], [role="menuitemradio"]');
            const currentIndex = Array.from(items).indexOf(event.target);

            switch (event.key) {
                case 'ArrowDown':
                    event.preventDefault();
                    const nextIndex = (currentIndex + 1) % items.length;
                    items[nextIndex].focus();
                    break;

                case 'ArrowUp':
                    event.preventDefault();
                    const prevIndex = (currentIndex - 1 + items.length) % items.length;
                    items[prevIndex].focus();
                    break;

                case 'Home':
                    event.preventDefault();
                    items[0].focus();
                    break;

                case 'End':
                    event.preventDefault();
                    items[items.length - 1].focus();
                    break;

                case 'Escape':
                    event.preventDefault();
                    this.closeMenu(menu);
                    break;
            }
        });
    }

    /**
     * Setup tab navigation improvements
     */
    setupTabNavigation() {
        // Tab panels navigation
        document.addEventListener('keydown', (event) => {
            const tab = event.target.closest('[role="tab"]');
            if (!tab) return;

            const tablist = tab.closest('[role="tablist"]');
            const tabs = tablist.querySelectorAll('[role="tab"]');
            const currentIndex = Array.from(tabs).indexOf(tab);

            switch (event.key) {
                case 'ArrowRight':
                case 'ArrowLeft':
                    event.preventDefault();
                    const direction = event.key === 'ArrowRight' ? 1 : -1;
                    const newIndex = (currentIndex + direction + tabs.length) % tabs.length;
                    const newTab = tabs[newIndex];
                    
                    this.activateTab(newTab);
                    newTab.focus();
                    break;

                case 'Home':
                    event.preventDefault();
                    this.activateTab(tabs[0]);
                    tabs[0].focus();
                    break;

                case 'End':
                    event.preventDefault();
                    const lastTab = tabs[tabs.length - 1];
                    this.activateTab(lastTab);
                    lastTab.focus();
                    break;
            }
        });
    }

    /**
     * Activate tab and show associated panel
     */
    activateTab(tab) {
        const tablist = tab.closest('[role="tablist"]');
        const tabs = tablist.querySelectorAll('[role="tab"]');
        const panels = document.querySelectorAll('[role="tabpanel"]');

        // Deactivate all tabs
        tabs.forEach(t => {
            t.setAttribute('aria-selected', 'false');
            t.setAttribute('tabindex', '-1');
        });

        // Hide all panels
        panels.forEach(panel => {
            panel.hidden = true;
        });

        // Activate current tab
        tab.setAttribute('aria-selected', 'true');
        tab.setAttribute('tabindex', '0');

        // Show associated panel
        const panelId = tab.getAttribute('aria-controls');
        const panel = document.getElementById(panelId);
        if (panel) {
            panel.hidden = false;
        }

        this.announce(`Onglet ${tab.textContent} activé`);
    }

    /**
     * Setup screen reader enhancements
     */
    setupScreenReaderEnhancements() {
        // Add screen reader helpers
        this.addScreenReaderHelpers();
        
        // Enhance dynamic content
        this.enhanceDynamicContent();
        
        // Add context information
        this.addContextInformation();
    }

    /**
     * Add screen reader helper text
     */
    addScreenReaderHelpers() {
        // Add helper text to form controls
        document.querySelectorAll('input, select, textarea').forEach(control => {
            if (!control.getAttribute('aria-describedby')) {
                const helperId = `helper-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
                const helper = document.createElement('div');
                helper.id = helperId;
                helper.className = 'sr-only';
                
                // Add contextual help based on input type
                let helperText = '';
                switch (control.type) {
                    case 'email':
                        helperText = 'Format requis: utilisateur@domaine.com';
                        break;
                    case 'password':
                        helperText = 'Saisissez votre mot de passe';
                        break;
                    case 'tel':
                        helperText = 'Format: +33 1 23 45 67 89';
                        break;
                    case 'date':
                        helperText = 'Format: JJ/MM/AAAA';
                        break;
                    default:
                        if (control.hasAttribute('required')) {
                            helperText = 'Champ obligatoire';
                        }
                }
                
                if (helperText) {
                    helper.textContent = helperText;
                    control.parentNode.appendChild(helper);
                    control.setAttribute('aria-describedby', helperId);
                }
            }
        });

        // Add context to buttons
        document.querySelectorAll('button').forEach(button => {
            if (!button.getAttribute('aria-label') && !button.textContent.trim()) {
                const icon = button.querySelector('i, svg');
                if (icon) {
                    const className = icon.className;
                    if (className.includes('edit')) {
                        button.setAttribute('aria-label', 'Modifier');
                    } else if (className.includes('delete') || className.includes('trash')) {
                        button.setAttribute('aria-label', 'Supprimer');
                    } else if (className.includes('view') || className.includes('eye')) {
                        button.setAttribute('aria-label', 'Voir');
                    }
                }
            }
        });
    }

    /**
     * Enhance dynamic content for screen readers
     */
    enhanceDynamicContent() {
        // Observe DOM changes and announce them
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            this.enhanceNewElement(node);
                        }
                    });
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    /**
     * Enhance newly added elements
     */
    enhanceNewElement(element) {
        // Add ARIA labels to new elements
        if (element.tagName === 'TABLE' && !element.querySelector('caption')) {
            const caption = document.createElement('caption');
            caption.textContent = 'Tableau de données';
            caption.className = 'sr-only';
            element.prepend(caption);
        }

        // Announce important additions
        if (element.classList.contains('alert') || element.role === 'alert') {
            this.announce(element.textContent, 'assertive');
        }
    }

    /**
     * Setup ARIA live regions for dynamic updates
     */
    setupARIALiveRegions() {
        // Monitor form validation messages
        document.addEventListener('invalid', (event) => {
            const field = event.target;
            const message = field.validationMessage;
            this.announce(`Erreur de validation: ${message}`, 'assertive');
        });

        // Monitor successful form submissions
        document.addEventListener('submit', (event) => {
            this.announce('Formulaire soumis', 'polite');
        });

        // Monitor AJAX content updates
        this.monitorAJAXUpdates();
    }

    /**
     * Monitor AJAX content updates
     */
    monitorAJAXUpdates() {
        // Intercept fetch requests
        const originalFetch = window.fetch;
        window.fetch = async (...args) => {
            try {
                const response = await originalFetch(...args);
                if (response.ok) {
                    this.announce('Contenu mis à jour', 'polite');
                }
                return response;
            } catch (error) {
                this.announce('Erreur de chargement', 'assertive');
                throw error;
            }
        };

        // Monitor jQuery AJAX if available
        if (window.jQuery) {
            $(document).ajaxSuccess(() => {
                this.announce('Données chargées', 'polite');
            });

            $(document).ajaxError(() => {
                this.announce('Erreur de chargement des données', 'assertive');
            });
        }
    }

    /**
     * Setup modal accessibility
     */
    setupModalAccessibility() {
        // Monitor modal show/hide events
        document.addEventListener('shown.bs.modal', (event) => {
            const modal = event.target;
            this.modalStack.push(modal);
            
            // Focus first focusable element
            const firstFocusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            if (firstFocusable) {
                firstFocusable.focus();
            }

            // Announce modal opening
            const title = modal.querySelector('.modal-title');
            if (title) {
                this.announce(`Fenêtre modale ouverte: ${title.textContent}`, 'polite');
            }
        });

        document.addEventListener('hidden.bs.modal', (event) => {
            const modal = event.target;
            const index = this.modalStack.indexOf(modal);
            if (index > -1) {
                this.modalStack.splice(index, 1);
            }

            // Return focus to trigger element
            const focusHistory = this.focusHistory;
            for (let i = focusHistory.length - 1; i >= 0; i--) {
                const element = focusHistory[i];
                if (element && element !== modal && document.contains(element)) {
                    element.focus();
                    break;
                }
            }

            this.announce('Fenêtre modale fermée', 'polite');
        });
    }

    /**
     * Setup form accessibility enhancements
     */
    setupFormAccessibility() {
        // Add form validation announcements
        document.addEventListener('invalid', (event) => {
            const field = event.target;
            field.setAttribute('aria-invalid', 'true');
            
            // Add error message if not present
            let errorId = field.getAttribute('aria-describedby');
            if (!errorId || !document.getElementById(errorId)) {
                errorId = `error-${Date.now()}`;
                const errorDiv = document.createElement('div');
                errorDiv.id = errorId;
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = field.validationMessage;
                field.parentNode.appendChild(errorDiv);
                field.setAttribute('aria-describedby', errorId);
            }
        });

        // Remove validation states on input
        document.addEventListener('input', (event) => {
            const field = event.target;
            if (field.checkValidity()) {
                field.setAttribute('aria-invalid', 'false');
            }
        });

        // Enhance fieldsets
        document.querySelectorAll('fieldset').forEach(fieldset => {
            if (!fieldset.querySelector('legend')) {
                const legend = document.createElement('legend');
                legend.textContent = 'Groupe de champs';
                legend.className = 'sr-only';
                fieldset.prepend(legend);
            }
        });
    }

    /**
     * Setup table accessibility
     */
    setupTableAccessibility() {
        document.querySelectorAll('table').forEach(table => {
            // Add caption if missing
            if (!table.querySelector('caption')) {
                const caption = document.createElement('caption');
                caption.textContent = 'Tableau de données';
                caption.className = 'sr-only';
                table.prepend(caption);
            }

            // Add scope attributes to headers
            table.querySelectorAll('th').forEach(th => {
                if (!th.getAttribute('scope')) {
                    const isColumnHeader = th.parentNode.parentNode.tagName === 'THEAD';
                    th.setAttribute('scope', isColumnHeader ? 'col' : 'row');
                }
            });

            // Add sortable functionality
            table.querySelectorAll('th[data-sortable]').forEach(th => {
                th.setAttribute('role', 'columnheader');
                th.setAttribute('aria-sort', 'none');
                th.style.cursor = 'pointer';
                
                th.addEventListener('click', () => {
                    this.handleTableSort(th);
                });
            });
        });
    }

    /**
     * Handle table sorting
     */
    handleTableSort(header) {
        const currentSort = header.getAttribute('aria-sort');
        const table = header.closest('table');
        
        // Reset all headers
        table.querySelectorAll('th[aria-sort]').forEach(th => {
            th.setAttribute('aria-sort', 'none');
        });

        // Set new sort direction
        const newSort = currentSort === 'ascending' ? 'descending' : 'ascending';
        header.setAttribute('aria-sort', newSort);

        // Announce sort change
        this.announce(`Tableau trié par ${header.textContent} en ordre ${newSort === 'ascending' ? 'croissant' : 'décroissant'}`, 'polite');
    }

    /**
     * Setup tooltip accessibility
     */
    setupTooltipAccessibility() {
        document.querySelectorAll('[data-bs-toggle="tooltip"], [title]').forEach(element => {
            // Convert title to aria-label if not present
            if (element.title && !element.getAttribute('aria-label')) {
                element.setAttribute('aria-label', element.title);
                element.removeAttribute('title');
            }

            // Add describedby for complex tooltips
            if (element.hasAttribute('data-tooltip-content')) {
                const tooltipId = `tooltip-${Date.now()}`;
                const tooltipDiv = document.createElement('div');
                tooltipDiv.id = tooltipId;
                tooltipDiv.className = 'sr-only';
                tooltipDiv.textContent = element.getAttribute('data-tooltip-content');
                document.body.appendChild(tooltipDiv);
                element.setAttribute('aria-describedby', tooltipId);
            }
        });
    }

    /**
     * Setup color contrast checker
     */
    setupColorContrastChecker() {
        if (this.preferences.highContrast) {
            document.body.classList.add('high-contrast');
        }

        // Monitor contrast preference changes
        if (window.matchMedia) {
            const contrastQuery = window.matchMedia('(prefers-contrast: high)');
            contrastQuery.addListener((e) => {
                if (e.matches) {
                    document.body.classList.add('high-contrast');
                } else {
                    document.body.classList.remove('high-contrast');
                }
            });
        }
    }

    /**
     * Setup user preferences panel
     */
    setupUserPreferences() {
        // Create accessibility preferences panel
        this.createPreferencesPanel();
        
        // Apply saved preferences
        this.applyPreferences();
        
        // Monitor system preferences
        this.monitorSystemPreferences();
    }

    /**
     * Create accessibility preferences panel
     */
    createPreferencesPanel() {
        const panel = document.createElement('div');
        panel.id = 'a11y-preferences-panel';
        panel.className = 'a11y-preferences-panel';
        panel.innerHTML = `
            <div class="a11y-panel-header">
                <h3>Préférences d'accessibilité</h3>
                <button type="button" class="btn-close" aria-label="Fermer les préférences"></button>
            </div>
            <div class="a11y-panel-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="reduce-motion" ${this.preferences.reducedMotion ? 'checked' : ''}>
                    <label class="form-check-label" for="reduce-motion">
                        Réduire les animations
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="high-contrast" ${this.preferences.highContrast ? 'checked' : ''}>
                    <label class="form-check-label" for="high-contrast">
                        Contraste élevé
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="large-text" ${this.preferences.largeText ? 'checked' : ''}>
                    <label class="form-check-label" for="large-text">
                        Texte agrandi
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="focus-indicators" ${this.preferences.focusIndicators ? 'checked' : ''}>
                    <label class="form-check-label" for="focus-indicators">
                        Indicateurs de focus renforcés
                    </label>
                </div>
            </div>
            <div class="a11y-panel-footer">
                <button type="button" class="btn btn-primary" id="save-a11y-preferences">
                    Sauvegarder
                </button>
                <button type="button" class="btn btn-secondary" id="reset-a11y-preferences">
                    Réinitialiser
                </button>
            </div>
        `;

        document.body.appendChild(panel);

        // Add event listeners
        panel.querySelector('.btn-close').addEventListener('click', () => {
            panel.classList.remove('show');
        });

        panel.querySelector('#save-a11y-preferences').addEventListener('click', () => {
            this.saveCurrentPreferences();
            panel.classList.remove('show');
            this.announce('Préférences sauvegardées', 'polite');
        });

        panel.querySelector('#reset-a11y-preferences').addEventListener('click', () => {
            this.resetPreferences();
            panel.classList.remove('show');
            this.announce('Préférences réinitialisées', 'polite');
        });
    }

    /**
     * Apply accessibility preferences
     */
    applyPreferences() {
        if (this.preferences.reducedMotion) {
            document.body.classList.add('reduce-motion');
        }

        if (this.preferences.highContrast) {
            document.body.classList.add('high-contrast');
        }

        if (this.preferences.largeText) {
            document.body.classList.add('large-text');
        }

        if (this.preferences.focusIndicators) {
            document.body.classList.add('enhanced-focus');
        }
    }

    /**
     * Monitor system accessibility preferences
     */
    monitorSystemPreferences() {
        if (window.matchMedia) {
            // Monitor reduced motion preference
            const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
            motionQuery.addListener((e) => {
                this.preferences.reducedMotion = e.matches;
                this.applyPreferences();
            });

            // Monitor contrast preference
            const contrastQuery = window.matchMedia('(prefers-contrast: high)');
            contrastQuery.addListener((e) => {
                this.preferences.highContrast = e.matches;
                this.applyPreferences();
            });
        }
    }

    /**
     * Setup skip links
     */
    setupSkipLinks() {
        // Add skip to main content link
        const skipLink = document.createElement('a');
        skipLink.href = '#main-content';
        skipLink.className = 'skip-link';
        skipLink.textContent = 'Aller au contenu principal';
        
        skipLink.addEventListener('click', (event) => {
            event.preventDefault();
            const mainContent = document.querySelector('#main-content, main, .content-wrapper');
            if (mainContent) {
                mainContent.focus();
                mainContent.scrollIntoView();
            }
        });

        document.body.prepend(skipLink);
    }

    /**
     * Setup landmark navigation
     */
    setupLandmarkNavigation() {
        // Add landmark roles if missing
        if (!document.querySelector('main')) {
            const contentWrapper = document.querySelector('.content-wrapper');
            if (contentWrapper) {
                contentWrapper.setAttribute('role', 'main');
                contentWrapper.id = 'main-content';
            }
        }

        // Add navigation landmark
        const sidebar = document.querySelector('.sidebar');
        if (sidebar && !sidebar.getAttribute('role')) {
            sidebar.setAttribute('role', 'navigation');
            sidebar.setAttribute('aria-label', 'Navigation principale');
        }

        // Add complementary landmark for aside content
        document.querySelectorAll('.widget, .sidebar-widget').forEach(widget => {
            if (!widget.getAttribute('role')) {
                widget.setAttribute('role', 'complementary');
            }
        });
    }

    /**
     * Save current preferences from panel
     */
    saveCurrentPreferences() {
        const panel = document.getElementById('a11y-preferences-panel');
        this.preferences.reducedMotion = panel.querySelector('#reduce-motion').checked;
        this.preferences.highContrast = panel.querySelector('#high-contrast').checked;
        this.preferences.largeText = panel.querySelector('#large-text').checked;
        this.preferences.focusIndicators = panel.querySelector('#focus-indicators').checked;

        this.savePreferences();
        this.applyPreferences();
    }

    /**
     * Reset preferences to defaults
     */
    resetPreferences() {
        this.preferences = this.loadPreferences();
        
        // Remove all accessibility classes
        document.body.classList.remove('reduce-motion', 'high-contrast', 'large-text', 'enhanced-focus');
        
        // Reapply defaults
        this.applyPreferences();
        this.savePreferences();
    }

    /**
     * Show accessibility preferences panel
     */
    showPreferencesPanel() {
        const panel = document.getElementById('a11y-preferences-panel');
        if (panel) {
            panel.classList.add('show');
            const firstInput = panel.querySelector('input');
            if (firstInput) {
                firstInput.focus();
            }
        }
    }

    /**
     * Public API methods
     */
    
    // Get current accessibility preferences
    getPreferences() {
        return { ...this.preferences };
    }

    // Update specific preference
    updatePreference(key, value) {
        this.preferences[key] = value;
        this.savePreferences();
        this.applyPreferences();
    }

    // Check if element is accessible
    checkElementAccessibility(element) {
        const issues = [];

        // Check for alt text on images
        if (element.tagName === 'IMG' && !element.alt) {
            issues.push('Image manque d\'attribut alt');
        }

        // Check for labels on form controls
        if (['INPUT', 'SELECT', 'TEXTAREA'].includes(element.tagName)) {
            const label = document.querySelector(`label[for="${element.id}"]`);
            if (!label && !element.getAttribute('aria-label') && !element.getAttribute('aria-labelledby')) {
                issues.push('Contrôle de formulaire manque de label');
            }
        }

        // Check for headings hierarchy
        if (/^H[1-6]$/.test(element.tagName)) {
            const level = parseInt(element.tagName[1]);
            const prevHeading = this.findPreviousHeading(element);
            if (prevHeading && level > parseInt(prevHeading.tagName[1]) + 1) {
                issues.push('Hiérarchie des titres incorrecte');
            }
        }

        return issues;
    }

    // Find previous heading in document order
    findPreviousHeading(element) {
        const headings = document.querySelectorAll('h1, h2, h3, h4, h5, h6');
        const elementIndex = Array.from(headings).indexOf(element);
        return elementIndex > 0 ? headings[elementIndex - 1] : null;
    }

    // Get accessibility summary for page
    getAccessibilitySummary() {
        const summary = {
            totalElements: 0,
            accessibleElements: 0,
            issues: []
        };

        const elements = document.querySelectorAll('*');
        elements.forEach(element => {
            summary.totalElements++;
            const issues = this.checkElementAccessibility(element);
            if (issues.length === 0) {
                summary.accessibleElements++;
            } else {
                summary.issues.push(...issues);
            }
        });

        summary.accessibilityScore = Math.round((summary.accessibleElements / summary.totalElements) * 100);
        
        return summary;
    }
}

// Initialize accessibility system
document.addEventListener('DOMContentLoaded', () => {
    window.accessPosA11y = new AccessPosAccessibility();
});

// Export for use in other modules
window.AccessPosAccessibility = AccessPosAccessibility;
