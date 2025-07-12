/**
 * AccessPos Pro - Application JavaScript
 * =======================================
 * Configuration principale JS intégrée avec SB Admin 2
 * Version: 1.0
 * Date: July 2025
 */

// Bootstrap et configuration de base
import './bootstrap';

// Import des fonctions AccessPos personnalisées
import './accesspos-functions';

// Configuration globale pour l'application
window.AccessPos = {
    // Configuration de base
    config: {
        appName: 'AccessPos Pro',
        version: '1.0',
        apiBaseUrl: '/admin/api/',
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        locale: 'fr'
    },
    
    // Utilitaires globaux
    utils: {
        /**
         * Afficher notification toast
         */
        showToast(message, type = 'success') {
            // Création du toast dynamiquement
            const toastContainer = document.getElementById('toast-container') || this.createToastContainer();
            const toast = this.createToast(message, type);
            toastContainer.appendChild(toast);
            
            // Afficher le toast
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);
            
            // Supprimer après 5 secondes
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, 5000);
        },
        
        /**
         * Créer container pour toasts
         */
        createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1055';
            document.body.appendChild(container);
            return container;
        },
        
        /**
         * Créer toast element
         */
        createToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            return toast;
        },
        
        /**
         * Formater un nombre avec séparateurs
         */
        formatNumber(number, decimals = 2) {
            return new Intl.NumberFormat('fr-FR', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            }).format(number);
        },
        
        /**
         * Formater une devise
         */
        formatCurrency(amount, currency = 'DZD') {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: currency,
                minimumFractionDigits: 2
            }).format(amount);
        },
        
        /**
         * Afficher loading overlay
         */
        showLoading(element) {
            const overlay = document.createElement('div');
            overlay.className = 'loading-overlay';
            overlay.innerHTML = '<div class="spinner-accesspos"></div>';
            element.style.position = 'relative';
            element.appendChild(overlay);
            return overlay;
        },
        
        /**
         * Cacher loading overlay
         */
        hideLoading(element) {
            const overlay = element.querySelector('.loading-overlay');
            if (overlay) {
                overlay.remove();
            }
        },
        
        /**
         * Confirmation avec SweetAlert2 style
         */
        confirm(title, text, confirmButtonText = 'Oui', cancelButtonText = 'Annuler') {
            return new Promise((resolve) => {
                // Si SweetAlert2 est disponible
                if (window.Swal) {
                    Swal.fire({
                        title: title,
                        text: text,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#e74a3b',
                        confirmButtonText: confirmButtonText,
                        cancelButtonText: cancelButtonText
                    }).then((result) => {
                        resolve(result.isConfirmed);
                    });
                } else {
                    // Fallback vers confirm natif
                    resolve(confirm(`${title}\n${text}`));
                }
            });
        }
    },
    
    // Gestionnaire d'événements globaux
    events: {
        /**
         * Initialiser les événements globaux
         */
        init() {
            this.setupTooltips();
            this.setupPopovers();
            this.setupFormValidation();
            this.setupAjaxErrorHandling();
            this.setupSidebarToggle();
        },
        
        /**
         * Initialiser tooltips Bootstrap
         */
        setupTooltips() {
            if (typeof bootstrap !== 'undefined') {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        },
        
        /**
         * Initialiser popovers Bootstrap
         */
        setupPopovers() {
            if (typeof bootstrap !== 'undefined') {
                const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
                popoverTriggerList.map(function (popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl);
                });
            }
        },
        
        /**
         * Configuration validation de formulaires
         */
        setupFormValidation() {
            // Validation HTML5 personnalisée
            const forms = document.querySelectorAll('.needs-validation');
            forms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            });
        },
        
        /**
         * Gestion d'erreurs AJAX globales
         */
        setupAjaxErrorHandling() {
            // Si jQuery est disponible
            if (window.jQuery) {
                $(document).ajaxError(function(event, xhr, settings) {
                    if (xhr.status === 419) {
                        AccessPos.utils.showToast('Session expirée. Veuillez vous reconnecter.', 'danger');
                        setTimeout(() => {
                            window.location.href = '/login';
                        }, 2000);
                    } else if (xhr.status === 500) {
                        AccessPos.utils.showToast('Erreur serveur. Veuillez réessayer.', 'danger');
                    }
                });
            }
        },
        
        /**
         * Configuration sidebar toggle
         */
        setupSidebarToggle() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('accordionSidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('toggled');
                    
                    // Sauvegarder l'état dans localStorage
                    const isToggled = sidebar.classList.contains('toggled');
                    localStorage.setItem('sidebarToggled', isToggled);
                });
                
                // Restaurer l'état depuis localStorage
                const sidebarToggled = localStorage.getItem('sidebarToggled');
                if (sidebarToggled === 'true') {
                    sidebar.classList.add('toggled');
                }
            }
        }
    },
    
    // Gestionnaire DataTables
    datatables: {
        /**
         * Configuration par défaut pour DataTables
         */
        defaultConfig: {
            pageLength: 25,
            responsive: true,
            language: {
                url: '/vendor/datatables/fr-FR.json'
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm'
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF', 
                    className: 'btn btn-danger btn-sm'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Imprimer',
                    className: 'btn btn-info btn-sm'
                }
            ]
        },
        
        /**
         * Initialiser une DataTable
         */
        init(selector, config = {}) {
            const finalConfig = { ...this.defaultConfig, ...config };
            return $(selector).DataTable(finalConfig);
        }
    },
    
    // Gestionnaire Charts
    charts: {
        /**
         * Configuration par défaut pour Chart.js
         */
        defaultConfig: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(234, 236, 244, 1)',
                        zeroLineColor: 'rgba(234, 236, 244, 1)',
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(234, 236, 244, 1)',
                        zeroLineColor: 'rgba(234, 236, 244, 1)',
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        },
        
        /**
         * Couleurs AccessPos pour les graphiques
         */
        colors: {
            primary: '#4e73df',
            success: '#1cc88a', 
            info: '#36b9cc',
            warning: '#f6c23e',
            danger: '#e74a3b',
            secondary: '#858796',
            light: '#f8f9fc',
            dark: '#5a5c69'
        }
    }
};

// Initialisation au chargement du DOM
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les événements globaux
    AccessPos.events.init();
    
    // Animation des cartes au chargement
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('fade-in-accesspos');
        }, index * 100);
    });
    
    // Affichage console
    console.log(`%c${AccessPos.config.appName} v${AccessPos.config.version}`, 
        'color: #4e73df; font-size: 16px; font-weight: bold;');
    console.log('Application initialisée avec succès');
});
