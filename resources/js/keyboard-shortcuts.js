/**
 * AccessPos Pro - Keyboard Shortcuts System
 * Enhanced keyboard navigation and shortcuts for better productivity
 */

class AccessPosKeyboardShortcuts {
    constructor() {
        this.shortcuts = new Map();
        this.isEnabled = true;
        this.modifierKeys = {
            ctrl: false,
            alt: false,
            shift: false,
            meta: false
        };
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.registerDefaultShortcuts();
        this.createShortcutHelp();
        this.setupContextualShortcuts();
        this.setupAccessibilityEnhancements();
    }

    /**
     * Setup keyboard event listeners
     */
    setupEventListeners() {
        document.addEventListener('keydown', this.handleKeyDown.bind(this), true);
        document.addEventListener('keyup', this.handleKeyUp.bind(this), true);
        
        // Prevent shortcuts when typing in inputs
        document.addEventListener('focusin', (event) => {
            const target = event.target;
            if (this.isInputElement(target)) {
                this.isEnabled = false;
            }
        });
        
        document.addEventListener('focusout', (event) => {
            const target = event.target;
            if (this.isInputElement(target)) {
                this.isEnabled = true;
            }
        });
    }

    /**
     * Check if element is an input that should disable shortcuts
     */
    isInputElement(element) {
        const tagName = element.tagName.toLowerCase();
        const inputTypes = ['input', 'textarea', 'select'];
        const contentEditable = element.contentEditable === 'true';
        
        return inputTypes.includes(tagName) || contentEditable;
    }

    /**
     * Handle keydown events
     */
    handleKeyDown(event) {
        if (!this.isEnabled) return;
        
        // Update modifier keys state
        this.modifierKeys.ctrl = event.ctrlKey;
        this.modifierKeys.alt = event.altKey;
        this.modifierKeys.shift = event.shiftKey;
        this.modifierKeys.meta = event.metaKey;
        
        // Create key combination string
        const combination = this.createKeyCombination(event);
        
        // Check if shortcut exists
        if (this.shortcuts.has(combination)) {
            const shortcut = this.shortcuts.get(combination);
            
            // Prevent default if shortcut should override
            if (shortcut.preventDefault !== false) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            // Execute shortcut action
            try {
                shortcut.action(event);
            } catch (error) {
                console.error('Error executing shortcut:', combination, error);
            }
        }
    }

    /**
     * Handle keyup events
     */
    handleKeyUp(event) {
        // Update modifier keys state
        this.modifierKeys.ctrl = event.ctrlKey;
        this.modifierKeys.alt = event.altKey;
        this.modifierKeys.shift = event.shiftKey;
        this.modifierKeys.meta = event.metaKey;
    }

    /**
     * Create key combination string
     */
    createKeyCombination(event) {
        const parts = [];
        
        if (event.ctrlKey) parts.push('ctrl');
        if (event.altKey) parts.push('alt');
        if (event.shiftKey) parts.push('shift');
        if (event.metaKey) parts.push('meta');
        
        // Convert key to lowercase for consistency
        const key = event.key.toLowerCase();
        parts.push(key);
        
        return parts.join('+');
    }

    /**
     * Register a keyboard shortcut
     */
    register(combination, action, options = {}) {
        const shortcut = {
            combination,
            action,
            description: options.description || '',
            context: options.context || 'global',
            preventDefault: options.preventDefault !== false,
            group: options.group || 'general'
        };
        
        this.shortcuts.set(combination, shortcut);
        
        // Add to help system
        this.addToHelpSystem(shortcut);
    }

    /**
     * Unregister a keyboard shortcut
     */
    unregister(combination) {
        this.shortcuts.delete(combination);
    }

    /**
     * Register default AccessPos shortcuts
     */
    registerDefaultShortcuts() {
        // Navigation shortcuts
        this.register('ctrl+h', () => this.navigateTo('/admin/dashboard'), {
            description: 'Aller au tableau de bord',
            group: 'navigation'
        });
        
        this.register('ctrl+shift+a', () => this.navigateTo('/admin/articles'), {
            description: 'Aller aux articles',
            group: 'navigation'
        });
        
        this.register('ctrl+shift+c', () => this.navigateTo('/admin/clients'), {
            description: 'Aller aux clients',
            group: 'navigation'
        });
        
        this.register('ctrl+shift+v', () => this.navigateTo('/admin/ventes'), {
            description: 'Aller aux ventes',
            group: 'navigation'
        });
        
        // Application shortcuts
        this.register('ctrl+shift+s', () => this.openSearch(), {
            description: 'Ouvrir la recherche globale',
            group: 'application'
        });
        
        this.register('ctrl+shift+n', () => this.createNew(), {
            description: 'Créer un nouvel élément',
            group: 'application'
        });
        
        this.register('ctrl+shift+r', () => this.refreshPage(), {
            description: 'Actualiser la page',
            group: 'application'
        });
        
        this.register('escape', () => this.closeModals(), {
            description: 'Fermer les modals/dialogs',
            group: 'application'
        });
        
        // Sidebar shortcuts
        this.register('ctrl+shift+\\', () => this.toggleSidebar(), {
            description: 'Basculer la barre latérale',
            group: 'interface'
        });
        
        this.register('ctrl+shift+d', () => this.toggleDarkMode(), {
            description: 'Basculer le mode sombre',
            group: 'interface'
        });
        
        // Help shortcuts
        this.register('f1', () => this.showShortcutHelp(), {
            description: 'Afficher l\'aide des raccourcis',
            group: 'help'
        });
        
        this.register('ctrl+shift+?', () => this.showShortcutHelp(), {
            description: 'Afficher l\'aide des raccourcis',
            group: 'help'
        });
        
        // Table shortcuts
        this.register('ctrl+f', () => this.focusTableSearch(), {
            description: 'Rechercher dans le tableau',
            group: 'table',
            context: 'table'
        });
        
        this.register('ctrl+shift+e', () => this.exportTable(), {
            description: 'Exporter le tableau',
            group: 'table',
            context: 'table'
        });
        
        // Form shortcuts
        this.register('ctrl+s', (event) => this.saveForm(event), {
            description: 'Sauvegarder le formulaire',
            group: 'form',
            context: 'form'
        });
        
        this.register('ctrl+shift+z', () => this.resetForm(), {
            description: 'Réinitialiser le formulaire',
            group: 'form',
            context: 'form'
        });
        
        // Developer shortcuts (only in development)
        if (this.isDevelopment()) {
            this.register('ctrl+shift+i', () => this.toggleDevInfo(), {
                description: 'Afficher les informations de développement',
                group: 'developer'
            });
            
            this.register('ctrl+shift+l', () => this.clearLogs(), {
                description: 'Effacer les logs',
                group: 'developer'
            });
        }
    }

    /**
     * Navigation helper
     */
    navigateTo(url) {
        if (window.accessPosLoading) {
            window.accessPosLoading.showPageTransition();
        }
        window.location.href = url;
    }

    /**
     * Open global search
     */
    openSearch() {
        // Check if search modal exists
        let searchModal = document.getElementById('global-search-modal');
        
        if (!searchModal) {
            // Create search modal
            searchModal = this.createSearchModal();
            document.body.appendChild(searchModal);
        }
        
        // Show modal and focus input
        const modal = new bootstrap.Modal(searchModal);
        modal.show();
        
        setTimeout(() => {
            const searchInput = searchModal.querySelector('#global-search-input');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }, 300);
    }

    /**
     * Create global search modal
     */
    createSearchModal() {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = 'global-search-modal';
        modal.setAttribute('tabindex', '-1');
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-search me-2"></i>
                            Recherche Globale
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="search-container">
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="global-search-input"
                                   placeholder="Rechercher dans AccessPos Pro..."
                                   autocomplete="off">
                            <div class="search-results mt-3" id="search-results">
                                <div class="search-suggestions">
                                    <h6>Suggestions rapides :</h6>
                                    <div class="list-group">
                                        <a href="/admin/articles" class="list-group-item list-group-item-action">
                                            <i class="fas fa-box me-2"></i> Articles
                                        </a>
                                        <a href="/admin/clients" class="list-group-item list-group-item-action">
                                            <i class="fas fa-users me-2"></i> Clients
                                        </a>
                                        <a href="/admin/ventes" class="list-group-item list-group-item-action">
                                            <i class="fas fa-shopping-cart me-2"></i> Ventes
                                        </a>
                                        <a href="/admin/rapports" class="list-group-item list-group-item-action">
                                            <i class="fas fa-chart-bar me-2"></i> Rapports
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <small class="text-muted">
                            Utilisez <kbd>Ctrl+Shift+S</kbd> pour ouvrir cette recherche
                        </small>
                    </div>
                </div>
            </div>
        `;
        
        // Add search functionality
        const searchInput = modal.querySelector('#global-search-input');
        searchInput.addEventListener('input', (event) => {
            this.handleGlobalSearch(event.target.value);
        });
        
        return modal;
    }

    /**
     * Handle global search
     */
    handleGlobalSearch(query) {
        if (query.length < 2) return;
        
        const resultsContainer = document.getElementById('search-results');
        
        // Show loading
        resultsContainer.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Recherche...</span>
                </div>
                <div class="mt-2">Recherche en cours...</div>
            </div>
        `;
        
        // Simulate search (replace with actual search implementation)
        setTimeout(() => {
            const mockResults = this.getMockSearchResults(query);
            this.displaySearchResults(mockResults);
        }, 500);
    }

    /**
     * Get mock search results
     */
    getMockSearchResults(query) {
        const results = {
            articles: [
                { id: 1, name: 'Article 1', url: '/admin/articles/1' },
                { id: 2, name: 'Article 2', url: '/admin/articles/2' }
            ],
            clients: [
                { id: 1, name: 'Client 1', url: '/admin/clients/1' },
                { id: 2, name: 'Client 2', url: '/admin/clients/2' }
            ],
            ventes: [
                { id: 1, name: 'Vente #001', url: '/admin/ventes/1' },
                { id: 2, name: 'Vente #002', url: '/admin/ventes/2' }
            ]
        };
        
        return results;
    }

    /**
     * Display search results
     */
    displaySearchResults(results) {
        const resultsContainer = document.getElementById('search-results');
        let html = '';
        
        Object.keys(results).forEach(category => {
            const items = results[category];
            if (items.length > 0) {
                html += `
                    <div class="search-category mb-3">
                        <h6 class="text-muted text-uppercase">${category}</h6>
                        <div class="list-group">
                `;
                
                items.forEach(item => {
                    html += `
                        <a href="${item.url}" class="list-group-item list-group-item-action">
                            <i class="fas fa-${this.getCategoryIcon(category)} me-2"></i>
                            ${item.name}
                        </a>
                    `;
                });
                
                html += `
                        </div>
                    </div>
                `;
            }
        });
        
        if (html === '') {
            html = `
                <div class="text-center text-muted">
                    <i class="fas fa-search fa-2x mb-3"></i>
                    <div>Aucun résultat trouvé</div>
                </div>
            `;
        }
        
        resultsContainer.innerHTML = html;
    }

    /**
     * Get category icon
     */
    getCategoryIcon(category) {
        const icons = {
            articles: 'box',
            clients: 'users',
            ventes: 'shopping-cart',
            rapports: 'chart-bar'
        };
        
        return icons[category] || 'file';
    }

    /**
     * Create new item
     */
    createNew() {
        const currentPath = window.location.pathname;
        
        if (currentPath.includes('/articles')) {
            this.navigateTo('/admin/articles/create');
        } else if (currentPath.includes('/clients')) {
            this.navigateTo('/admin/clients/create');
        } else if (currentPath.includes('/ventes')) {
            this.navigateTo('/admin/ventes/create');
        } else {
            // Show creation modal
            this.showCreateModal();
        }
    }

    /**
     * Show creation modal
     */
    showCreateModal() {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Créer un nouvel élément</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="list-group">
                            <a href="/admin/articles/create" class="list-group-item list-group-item-action">
                                <i class="fas fa-box me-2"></i> Nouvel Article
                            </a>
                            <a href="/admin/clients/create" class="list-group-item list-group-item-action">
                                <i class="fas fa-user-plus me-2"></i> Nouveau Client
                            </a>
                            <a href="/admin/ventes/create" class="list-group-item list-group-item-action">
                                <i class="fas fa-shopping-cart me-2"></i> Nouvelle Vente
                            </a>
                            <a href="/admin/fournisseurs/create" class="list-group-item list-group-item-action">
                                <i class="fas fa-truck me-2"></i> Nouveau Fournisseur
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
        
        // Remove modal after hide
        modal.addEventListener('hidden.bs.modal', () => {
            modal.remove();
        });
    }

    /**
     * Refresh page
     */
    refreshPage() {
        if (window.accessPosLoading) {
            window.accessPosLoading.showGlobalLoader('Actualisation...', 'Rechargement de la page');
        }
        window.location.reload();
    }

    /**
     * Close modals
     */
    closeModals() {
        // Close Bootstrap modals
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            const bootstrapModal = bootstrap.Modal.getInstance(modal);
            if (bootstrapModal) {
                bootstrapModal.hide();
            }
        });
        
        // Close any custom overlays
        const overlays = document.querySelectorAll('.overlay, .popup, .dropdown.show');
        overlays.forEach(overlay => {
            overlay.classList.remove('show');
        });
    }

    /**
     * Toggle sidebar
     */
    toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const content = document.querySelector('.content-wrapper');
        
        if (sidebar) {
            sidebar.classList.toggle('collapsed');
            if (content) {
                content.classList.toggle('sidebar-collapsed');
            }
        }
    }

    /**
     * Toggle dark mode
     */
    toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
        
        // Save preference
        const isDark = document.body.classList.contains('dark-mode');
        localStorage.setItem('accesspos-dark-mode', isDark);
        
        // Show notification
        this.showNotification(
            isDark ? 'Mode sombre activé' : 'Mode clair activé',
            'success'
        );
    }

    /**
     * Focus table search
     */
    focusTableSearch() {
        const searchInput = document.querySelector('.dataTables_filter input, .table-search input');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }

    /**
     * Export table
     */
    exportTable() {
        const exportBtn = document.querySelector('.export-btn, [data-action="export"]');
        if (exportBtn) {
            exportBtn.click();
        } else {
            this.showNotification('Fonction d\'export non disponible', 'warning');
        }
    }

    /**
     * Save form
     */
    saveForm(event) {
        const form = document.querySelector('form');
        if (form) {
            // Trigger form submission
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn) {
                submitBtn.click();
            } else {
                form.requestSubmit();
            }
        }
    }

    /**
     * Reset form
     */
    resetForm() {
        const form = document.querySelector('form');
        if (form) {
            if (confirm('Voulez-vous vraiment réinitialiser le formulaire ?')) {
                form.reset();
                this.showNotification('Formulaire réinitialisé', 'info');
            }
        }
    }

    /**
     * Show shortcut help
     */
    showShortcutHelp() {
        let helpModal = document.getElementById('shortcuts-help-modal');
        
        if (!helpModal) {
            helpModal = this.createShortcutHelpModal();
            document.body.appendChild(helpModal);
        }
        
        const modal = new bootstrap.Modal(helpModal);
        modal.show();
    }

    /**
     * Create shortcut help modal
     */
    createShortcutHelpModal() {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = 'shortcuts-help-modal';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-keyboard me-2"></i>
                            Raccourcis Clavier
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="shortcuts-help-content">
                            ${this.generateShortcutHelpContent()}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <small class="text-muted">
                            Utilisez <kbd>F1</kbd> ou <kbd>Ctrl+Shift+?</kbd> pour afficher cette aide
                        </small>
                    </div>
                </div>
            </div>
        `;
        
        return modal;
    }

    /**
     * Generate shortcut help content
     */
    generateShortcutHelpContent() {
        const groups = {};
        
        // Group shortcuts by category
        this.shortcuts.forEach(shortcut => {
            const group = shortcut.group || 'general';
            if (!groups[group]) {
                groups[group] = [];
            }
            groups[group].push(shortcut);
        });
        
        let html = '';
        
        Object.keys(groups).forEach(groupName => {
            const shortcuts = groups[groupName];
            const groupTitle = this.getGroupTitle(groupName);
            
            html += `
                <div class="shortcut-group mb-4">
                    <h6 class="text-primary text-uppercase mb-3">${groupTitle}</h6>
                    <div class="row">
            `;
            
            shortcuts.forEach(shortcut => {
                const keys = this.formatShortcutKeys(shortcut.combination);
                html += `
                    <div class="col-md-6 mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="shortcut-description">${shortcut.description}</span>
                            <span class="shortcut-keys">${keys}</span>
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        });
        
        return html;
    }

    /**
     * Get group title
     */
    getGroupTitle(groupName) {
        const titles = {
            navigation: 'Navigation',
            application: 'Application',
            interface: 'Interface',
            table: 'Tableaux',
            form: 'Formulaires',
            help: 'Aide',
            developer: 'Développement',
            general: 'Général'
        };
        
        return titles[groupName] || groupName;
    }

    /**
     * Format shortcut keys for display
     */
    formatShortcutKeys(combination) {
        const keys = combination.split('+');
        const formatted = keys.map(key => {
            const keyMap = {
                ctrl: 'Ctrl',
                alt: 'Alt',
                shift: 'Shift',
                meta: 'Cmd',
                escape: 'Esc',
                f1: 'F1'
            };
            
            return `<kbd>${keyMap[key] || key.toUpperCase()}</kbd>`;
        });
        
        return formatted.join(' + ');
    }

    /**
     * Create shortcut help system
     */
    createShortcutHelp() {
        // Add help icon to topbar
        const topbar = document.querySelector('.topbar .navbar-nav');
        if (topbar) {
            const helpItem = document.createElement('li');
            helpItem.className = 'nav-item';
            helpItem.innerHTML = `
                <a class="nav-link" href="#" id="shortcuts-help-trigger" title="Raccourcis clavier (F1)">
                    <i class="fas fa-keyboard"></i>
                </a>
            `;
            
            topbar.appendChild(helpItem);
            
            // Add click event
            const helpTrigger = helpItem.querySelector('#shortcuts-help-trigger');
            helpTrigger.addEventListener('click', (event) => {
                event.preventDefault();
                this.showShortcutHelp();
            });
        }
    }

    /**
     * Setup contextual shortcuts
     */
    setupContextualShortcuts() {
        // Table context shortcuts
        this.setupTableContextShortcuts();
        
        // Form context shortcuts
        this.setupFormContextShortcuts();
        
        // Modal context shortcuts
        this.setupModalContextShortcuts();
    }

    /**
     * Setup table context shortcuts
     */
    setupTableContextShortcuts() {
        document.addEventListener('focusin', (event) => {
            const table = event.target.closest('.dataTables_wrapper, .table-container');
            if (table) {
                this.currentContext = 'table';
            }
        });
    }

    /**
     * Setup form context shortcuts
     */
    setupFormContextShortcuts() {
        document.addEventListener('focusin', (event) => {
            const form = event.target.closest('form');
            if (form) {
                this.currentContext = 'form';
            }
        });
    }

    /**
     * Setup modal context shortcuts
     */
    setupModalContextShortcuts() {
        document.addEventListener('shown.bs.modal', () => {
            this.currentContext = 'modal';
        });
        
        document.addEventListener('hidden.bs.modal', () => {
            this.currentContext = 'global';
        });
    }

    /**
     * Setup accessibility enhancements
     */
    setupAccessibilityEnhancements() {
        // Skip to main content
        this.register('alt+m', () => this.skipToMainContent(), {
            description: 'Aller au contenu principal',
            group: 'accessibility'
        });
        
        // Focus navigation
        this.register('alt+n', () => this.focusNavigation(), {
            description: 'Aller à la navigation',
            group: 'accessibility'
        });
        
        // Focus search
        this.register('alt+s', () => this.focusSearch(), {
            description: 'Aller à la recherche',
            group: 'accessibility'
        });
    }

    /**
     * Skip to main content
     */
    skipToMainContent() {
        const mainContent = document.querySelector('main, #main-content, .content-wrapper');
        if (mainContent) {
            mainContent.focus();
            mainContent.scrollIntoView();
        }
    }

    /**
     * Focus navigation
     */
    focusNavigation() {
        const nav = document.querySelector('.sidebar .nav-link, .navbar .nav-link');
        if (nav) {
            nav.focus();
        }
    }

    /**
     * Focus search
     */
    focusSearch() {
        const searchInput = document.querySelector('input[type="search"], .search-input, #global-search-input');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible notification-toast`;
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Add to page
        const container = document.querySelector('.notification-container') || document.body;
        container.appendChild(notification);
        
        // Auto-remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    /**
     * Add shortcut to help system
     */
    addToHelpSystem(shortcut) {
        // This would update the help modal content
        // Implementation depends on how the help system is structured
    }

    /**
     * Check if in development mode
     */
    isDevelopment() {
        return window.location.hostname === 'localhost' || 
               window.location.hostname === '127.0.0.1' ||
               window.location.search.includes('debug=true');
    }

    /**
     * Toggle development info
     */
    toggleDevInfo() {
        let devInfo = document.getElementById('dev-info-panel');
        
        if (!devInfo) {
            devInfo = this.createDevInfoPanel();
            document.body.appendChild(devInfo);
        } else {
            devInfo.style.display = devInfo.style.display === 'none' ? 'block' : 'none';
        }
    }

    /**
     * Create development info panel
     */
    createDevInfoPanel() {
        const panel = document.createElement('div');
        panel.id = 'dev-info-panel';
        panel.className = 'dev-info-panel';
        panel.innerHTML = `
            <div class="dev-info-header">
                <h6>Development Info</h6>
                <button type="button" class="btn-close" onclick="this.closest('.dev-info-panel').style.display='none'"></button>
            </div>
            <div class="dev-info-content">
                <div><strong>User Agent:</strong> ${navigator.userAgent}</div>
                <div><strong>Screen:</strong> ${screen.width}x${screen.height}</div>
                <div><strong>Viewport:</strong> ${window.innerWidth}x${window.innerHeight}</div>
                <div><strong>Active Shortcuts:</strong> ${this.shortcuts.size}</div>
                <div><strong>Current Context:</strong> ${this.currentContext || 'global'}</div>
            </div>
        `;
        
        return panel;
    }

    /**
     * Clear console logs
     */
    clearLogs() {
        console.clear();
        this.showNotification('Console logs cleared', 'success');
    }

    /**
     * Enable/disable shortcuts
     */
    setEnabled(enabled) {
        this.isEnabled = enabled;
    }

    /**
     * Get all registered shortcuts
     */
    getAllShortcuts() {
        return Array.from(this.shortcuts.values());
    }

    /**
     * Get shortcuts by group
     */
    getShortcutsByGroup(group) {
        return Array.from(this.shortcuts.values()).filter(shortcut => shortcut.group === group);
    }
}

// Initialize keyboard shortcuts system
document.addEventListener('DOMContentLoaded', () => {
    window.accessPosKeyboard = new AccessPosKeyboardShortcuts();
});

// Export for use in other modules
window.AccessPosKeyboardShortcuts = AccessPosKeyboardShortcuts;
