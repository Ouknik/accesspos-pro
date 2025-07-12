/**
 * AccessPos Pro - Loading Indicators System
 * Comprehensive loading states for better UX
 */

class AccessPosLoadingIndicators {
    constructor() {
        this.activeLoaders = new Set();
        this.init();
    }

    init() {
        this.createLoadingTemplates();
        this.setupGlobalLoading();
        this.setupFormLoading();
        this.setupTableLoading();
        this.setupButtonLoading();
        this.setupPageTransitions();
        this.setupProgressBars();
    }

    /**
     * Create loading HTML templates
     */
    createLoadingTemplates() {
        // Main application loader template
        const mainLoaderTemplate = `
            <div id="app-main-loader" class="main-loader">
                <div class="loader-content">
                    <div class="sb-admin-spinner"></div>
                    <div class="loader-text">Chargement d'AccessPos Pro...</div>
                    <div class="loader-progress">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Page transition loader template
        const pageTransitionTemplate = `
            <div id="page-transition-loader" class="page-transition">
                <div class="transition-bar"></div>
            </div>
        `;

        // Component loader template
        const componentLoaderTemplate = `
            <div class="component-loader">
                <div class="component-spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Chargement...</span>
                    </div>
                </div>
                <div class="component-loader-text">Chargement du composant...</div>
            </div>
        `;

        // Store templates
        this.templates = {
            mainLoader: mainLoaderTemplate,
            pageTransition: pageTransitionTemplate,
            componentLoader: componentLoaderTemplate
        };
    }

    /**
     * Global loading management
     */
    setupGlobalLoading() {
        // Create global loading overlay
        this.createGlobalLoader();
        
        // Setup AJAX loading indicators
        this.setupAjaxLoading();
        
        // Setup navigation loading
        this.setupNavigationLoading();
    }

    /**
     * Create global loading overlay
     */
    createGlobalLoader() {
        const loaderHtml = `
            <div id="global-loader" class="global-loader" style="display: none;">
                <div class="global-loader-backdrop"></div>
                <div class="global-loader-content">
                    <div class="loader-spinner">
                        <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
                            <span class="sr-only">Chargement...</span>
                        </div>
                    </div>
                    <div class="loader-message">Traitement en cours...</div>
                    <div class="loader-submessage">Veuillez patienter</div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', loaderHtml);
    }

    /**
     * Show global loader
     */
    showGlobalLoader(message = 'Traitement en cours...', submessage = 'Veuillez patienter') {
        const loader = document.getElementById('global-loader');
        const messageEl = loader.querySelector('.loader-message');
        const submessageEl = loader.querySelector('.loader-submessage');
        
        messageEl.textContent = message;
        submessageEl.textContent = submessage;
        
        loader.style.display = 'flex';
        this.activeLoaders.add('global');
        
        // Auto-hide after 30 seconds to prevent stuck loaders
        setTimeout(() => {
            if (this.activeLoaders.has('global')) {
                this.hideGlobalLoader();
            }
        }, 30000);
    }

    /**
     * Hide global loader
     */
    hideGlobalLoader() {
        const loader = document.getElementById('global-loader');
        loader.style.display = 'none';
        this.activeLoaders.delete('global');
    }

    /**
     * AJAX loading indicators
     */
    setupAjaxLoading() {
        // jQuery AJAX setup
        if (window.jQuery) {
            $(document).ajaxStart(() => {
                this.showGlobalLoader('Chargement des données...', 'Synchronisation en cours');
            });

            $(document).ajaxStop(() => {
                this.hideGlobalLoader();
            });

            $(document).ajaxError((event, xhr, settings, error) => {
                this.hideGlobalLoader();
                this.showErrorLoader('Erreur de chargement', error);
            });
        }

        // Fetch API interception
        this.interceptFetchAPI();
    }

    /**
     * Intercept Fetch API for loading indicators
     */
    interceptFetchAPI() {
        const originalFetch = window.fetch;
        
        window.fetch = async (...args) => {
            const [resource, config] = args;
            
            // Show loader for non-background requests
            if (!config?.background) {
                this.showGlobalLoader('Chargement...', 'Récupération des données');
            }
            
            try {
                const response = await originalFetch(resource, config);
                
                if (!config?.background) {
                    this.hideGlobalLoader();
                }
                
                return response;
            } catch (error) {
                if (!config?.background) {
                    this.hideGlobalLoader();
                    this.showErrorLoader('Erreur réseau', error.message);
                }
                throw error;
            }
        };
    }

    /**
     * Navigation loading
     */
    setupNavigationLoading() {
        // Intercept link clicks
        document.addEventListener('click', (event) => {
            const link = event.target.closest('a[href]');
            if (link && !link.hasAttribute('data-no-loader')) {
                const href = link.getAttribute('href');
                
                // Only show for same-origin links
                if (this.isSameOrigin(href)) {
                    this.showPageTransition();
                }
            }
        });

        // Handle form submissions
        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!form.hasAttribute('data-no-loader')) {
                this.showFormLoader(form);
            }
        });
    }

    /**
     * Check if URL is same origin
     */
    isSameOrigin(url) {
        try {
            const link = new URL(url, window.location.origin);
            return link.origin === window.location.origin;
        } catch {
            return false;
        }
    }

    /**
     * Show page transition
     */
    showPageTransition() {
        let transition = document.getElementById('page-transition-loader');
        if (!transition) {
            transition = document.createElement('div');
            transition.id = 'page-transition-loader';
            transition.className = 'page-transition';
            document.body.appendChild(transition);
        }
        
        transition.classList.add('loading');
        this.activeLoaders.add('page-transition');
        
        // Hide on page unload
        window.addEventListener('beforeunload', () => {
            this.hidePageTransition();
        });
    }

    /**
     * Hide page transition
     */
    hidePageTransition() {
        const transition = document.getElementById('page-transition-loader');
        if (transition) {
            transition.classList.remove('loading');
            this.activeLoaders.delete('page-transition');
        }
    }

    /**
     * Form loading indicators
     */
    setupFormLoading() {
        // Handle form submissions
        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!form.hasAttribute('data-no-loader')) {
                this.showFormLoader(form);
            }
        });
    }

    /**
     * Show form loader
     */
    showFormLoader(form) {
        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
        
        if (submitBtn) {
            // Save original text
            submitBtn.dataset.originalText = submitBtn.textContent || submitBtn.value;
            
            // Add loading state
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
            
            // Update text
            if (submitBtn.tagName === 'BUTTON') {
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                    Traitement...
                `;
            } else {
                submitBtn.value = 'Traitement...';
            }
        }

        // Add loading overlay to form
        const overlay = document.createElement('div');
        overlay.className = 'form-loading-overlay';
        overlay.innerHTML = `
            <div class="form-loader">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Traitement...</span>
                </div>
                <div class="form-loader-text">Traitement du formulaire...</div>
            </div>
        `;
        
        form.style.position = 'relative';
        form.appendChild(overlay);
        
        this.activeLoaders.add(`form-${Date.now()}`);
    }

    /**
     * Hide form loader
     */
    hideFormLoader(form) {
        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
        
        if (submitBtn && submitBtn.dataset.originalText) {
            // Restore original state
            submitBtn.classList.remove('btn-loading');
            submitBtn.disabled = false;
            
            if (submitBtn.tagName === 'BUTTON') {
                submitBtn.innerHTML = submitBtn.dataset.originalText;
            } else {
                submitBtn.value = submitBtn.dataset.originalText;
            }
            
            delete submitBtn.dataset.originalText;
        }

        // Remove loading overlay
        const overlay = form.querySelector('.form-loading-overlay');
        if (overlay) {
            overlay.remove();
        }
    }

    /**
     * Table loading indicators
     */
    setupTableLoading() {
        // DataTables loading customization
        if (window.jQuery && window.jQuery.fn.DataTable) {
            $.fn.dataTable.defaults.oLanguage.sProcessing = `
                <div class="datatable-loader">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Chargement...</span>
                    </div>
                    <div class="loader-text">Chargement des données...</div>
                </div>
            `;
        }
    }

    /**
     * Show table loader
     */
    showTableLoader(table, message = 'Chargement des données...') {
        const tbody = table.querySelector('tbody');
        const colCount = table.querySelectorAll('thead th').length || 1;
        
        const loaderRow = document.createElement('tr');
        loaderRow.className = 'table-loader-row';
        loaderRow.innerHTML = `
            <td colspan="${colCount}" class="text-center p-4">
                <div class="table-loader">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Chargement...</span>
                    </div>
                    <div class="loader-text mt-2">${message}</div>
                </div>
            </td>
        `;
        
        tbody.innerHTML = '';
        tbody.appendChild(loaderRow);
    }

    /**
     * Hide table loader
     */
    hideTableLoader(table) {
        const loaderRow = table.querySelector('.table-loader-row');
        if (loaderRow) {
            loaderRow.remove();
        }
    }

    /**
     * Button loading indicators
     */
    setupButtonLoading() {
        // Auto-setup for buttons with data-loading attribute
        document.addEventListener('click', (event) => {
            const button = event.target.closest('[data-loading]');
            if (button) {
                this.showButtonLoader(button);
            }
        });
    }

    /**
     * Show button loader
     */
    showButtonLoader(button, text = 'Chargement...') {
        if (button.classList.contains('btn-loading')) return;
        
        // Save original content
        button.dataset.originalContent = button.innerHTML;
        button.dataset.originalDisabled = button.disabled;
        
        // Set loading state
        button.classList.add('btn-loading');
        button.disabled = true;
        
        // Update content
        button.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            ${text}
        `;
        
        // Auto-hide after timeout (prevent stuck buttons)
        const timeout = parseInt(button.dataset.loadingTimeout) || 10000;
        setTimeout(() => {
            if (button.classList.contains('btn-loading')) {
                this.hideButtonLoader(button);
            }
        }, timeout);
    }

    /**
     * Hide button loader
     */
    hideButtonLoader(button) {
        if (!button.classList.contains('btn-loading')) return;
        
        // Restore original state
        button.classList.remove('btn-loading');
        button.innerHTML = button.dataset.originalContent || button.innerHTML;
        button.disabled = button.dataset.originalDisabled === 'true';
        
        // Clean up
        delete button.dataset.originalContent;
        delete button.dataset.originalDisabled;
    }

    /**
     * Progress bars
     */
    setupProgressBars() {
        // File upload progress
        this.setupFileUploadProgress();
        
        // Task progress
        this.setupTaskProgress();
    }

    /**
     * File upload progress
     */
    setupFileUploadProgress() {
        document.addEventListener('change', (event) => {
            const fileInput = event.target;
            if (fileInput.type === 'file' && fileInput.files.length > 0) {
                this.showFileUploadProgress(fileInput);
            }
        });
    }

    /**
     * Show file upload progress
     */
    showFileUploadProgress(fileInput) {
        const progressContainer = document.createElement('div');
        progressContainer.className = 'file-upload-progress mt-2';
        progressContainer.innerHTML = `
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" style="width: 0%">
                    <span class="progress-text">0%</span>
                </div>
            </div>
            <div class="upload-status text-muted mt-1">
                <small>Préparation du téléchargement...</small>
            </div>
        `;
        
        fileInput.parentNode.appendChild(progressContainer);
        
        // Simulate upload progress (replace with actual upload logic)
        this.simulateUploadProgress(progressContainer);
    }

    /**
     * Simulate upload progress
     */
    simulateUploadProgress(container) {
        const progressBar = container.querySelector('.progress-bar');
        const progressText = container.querySelector('.progress-text');
        const statusText = container.querySelector('.upload-status small');
        
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 100) progress = 100;
            
            progressBar.style.width = `${progress}%`;
            progressText.textContent = `${Math.round(progress)}%`;
            
            if (progress < 30) {
                statusText.textContent = 'Téléchargement en cours...';
            } else if (progress < 70) {
                statusText.textContent = 'Traitement du fichier...';
            } else if (progress < 100) {
                statusText.textContent = 'Finalisation...';
            } else {
                statusText.textContent = 'Téléchargement terminé !';
                progressBar.classList.remove('progress-bar-animated', 'progress-bar-striped');
                progressBar.classList.add('bg-success');
                clearInterval(interval);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    container.remove();
                }, 3000);
            }
        }, 200);
    }

    /**
     * Task progress indicator
     */
    showTaskProgress(taskName, steps = []) {
        const progressModal = document.createElement('div');
        progressModal.className = 'modal fade';
        progressModal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Progression de la tâche</h5>
                    </div>
                    <div class="modal-body">
                        <h6 class="mb-3">${taskName}</h6>
                        <div class="progress mb-3">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 0%"></div>
                        </div>
                        <div class="task-steps">
                            ${steps.map((step, index) => `
                                <div class="step-item" data-step="${index}">
                                    <i class="fas fa-circle step-icon text-muted"></i>
                                    <span class="step-text text-muted">${step}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(progressModal);
        
        // Show modal
        const modal = new bootstrap.Modal(progressModal);
        modal.show();
        
        return {
            updateProgress: (percent, currentStep = null) => {
                const progressBar = progressModal.querySelector('.progress-bar');
                progressBar.style.width = `${percent}%`;
                
                if (currentStep !== null) {
                    // Update step indicators
                    const stepItems = progressModal.querySelectorAll('.step-item');
                    stepItems.forEach((item, index) => {
                        const icon = item.querySelector('.step-icon');
                        const text = item.querySelector('.step-text');
                        
                        if (index < currentStep) {
                            icon.className = 'fas fa-check-circle step-icon text-success';
                            text.className = 'step-text text-success';
                        } else if (index === currentStep) {
                            icon.className = 'fas fa-spinner fa-spin step-icon text-primary';
                            text.className = 'step-text text-primary';
                        } else {
                            icon.className = 'fas fa-circle step-icon text-muted';
                            text.className = 'step-text text-muted';
                        }
                    });
                }
            },
            complete: () => {
                progressModal.querySelector('.progress-bar').classList.add('bg-success');
                setTimeout(() => {
                    modal.hide();
                    progressModal.remove();
                }, 2000);
            },
            error: (message) => {
                progressModal.querySelector('.progress-bar').classList.add('bg-danger');
                progressModal.querySelector('.modal-body').insertAdjacentHTML('beforeend', `
                    <div class="alert alert-danger mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${message}
                    </div>
                `);
            }
        };
    }

    /**
     * Show error loader
     */
    showErrorLoader(title = 'Erreur', message = 'Une erreur est survenue') {
        const errorLoader = document.createElement('div');
        errorLoader.className = 'error-loader';
        errorLoader.innerHTML = `
            <div class="error-loader-content">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                </div>
                <div class="error-title">${title}</div>
                <div class="error-message">${message}</div>
                <button type="button" class="btn btn-primary btn-sm mt-3" onclick="this.closest('.error-loader').remove()">
                    Fermer
                </button>
            </div>
        `;
        
        document.body.appendChild(errorLoader);
        
        // Auto-remove after 10 seconds
        setTimeout(() => {
            if (errorLoader.parentNode) {
                errorLoader.remove();
            }
        }, 10000);
    }

    /**
     * Cleanup all active loaders
     */
    cleanupAll() {
        // Hide global loader
        this.hideGlobalLoader();
        
        // Hide page transition
        this.hidePageTransition();
        
        // Reset all buttons
        document.querySelectorAll('.btn-loading').forEach(btn => {
            this.hideButtonLoader(btn);
        });
        
        // Remove all form overlays
        document.querySelectorAll('.form-loading-overlay').forEach(overlay => {
            overlay.remove();
        });
        
        // Clear active loaders set
        this.activeLoaders.clear();
    }
}

// Initialize loading indicators system
document.addEventListener('DOMContentLoaded', () => {
    window.accessPosLoading = new AccessPosLoadingIndicators();
});

// Export for use in other modules
window.AccessPosLoadingIndicators = AccessPosLoadingIndicators;
