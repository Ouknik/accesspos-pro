/**
 * AccessPos Pro - Fonctions Métier Personnalisées
 * ================================================
 * Fonctions spécifiques à AccessPos Pro intégrées avec SB Admin 2
 * Version: 1.0
 * Date: July 2025
 */

/**
 * ==========================================
 * GESTIONNAIRE TABLEAU DE BORD
 * ==========================================
 */
window.AccessPosDashboard = {
    /**
     * Rafraîchir les données en temps réel
     */
    refreshLiveData() {
        const loadingElements = document.querySelectorAll('.card-body');
        loadingElements.forEach(el => AccessPos.utils.showLoading(el));
        
        fetch('/admin/api/live-data')
            .then(response => response.json())
            .then(data => {
                this.updateStatCards(data.statistiques);
                this.updateCharts(data.graphiques);
                AccessPos.utils.showToast('Données mises à jour', 'success');
            })
            .catch(error => {
                console.error('Erreur lors du rafraîchissement:', error);
                AccessPos.utils.showToast('Erreur lors de la mise à jour', 'danger');
            })
            .finally(() => {
                loadingElements.forEach(el => AccessPos.utils.hideLoading(el));
            });
    },
    
    /**
     * Mettre à jour les cartes de statistiques
     */
    updateStatCards(data) {
        Object.keys(data).forEach(key => {
            const element = document.getElementById(`stat-${key}`);
            if (element && data[key] !== undefined) {
                // Animation de compteur
                this.animateCounter(element, data[key]);
            }
        });
    },
    
    /**
     * Animation de compteur pour les statistiques
     */
    animateCounter(element, targetValue) {
        const startValue = parseFloat(element.textContent.replace(/[^\d.-]/g, '')) || 0;
        const increment = (targetValue - startValue) / 30;
        let currentValue = startValue;
        
        const timer = setInterval(() => {
            currentValue += increment;
            if ((increment > 0 && currentValue >= targetValue) || 
                (increment < 0 && currentValue <= targetValue)) {
                currentValue = targetValue;
                clearInterval(timer);
            }
            
            // Formatage selon le type de donnée
            if (element.classList.contains('currency')) {
                element.textContent = AccessPos.utils.formatCurrency(currentValue);
            } else if (element.classList.contains('percentage')) {
                element.textContent = currentValue.toFixed(1) + '%';
            } else {
                element.textContent = AccessPos.utils.formatNumber(currentValue, 0);
            }
        }, 50);
    },
    
    /**
     * Mettre à jour les graphiques
     */
    updateCharts(data) {
        // Mise à jour du graphique des ventes
        if (window.ventesChart && data.ventes) {
            window.ventesChart.data.datasets[0].data = data.ventes.data;
            window.ventesChart.data.labels = data.ventes.labels;
            window.ventesChart.update();
        }
        
        // Mise à jour du graphique des produits
        if (window.produitsChart && data.produits) {
            window.produitsChart.data.datasets[0].data = data.produits.data;
            window.produitsChart.data.labels = data.produits.labels;
            window.produitsChart.update();
        }
    },
    
    /**
     * Ouvrir modal de détails
     */
    openDetailsModal(type, title) {
        const modal = document.getElementById('detailsModal');
        const modalTitle = modal.querySelector('.modal-title');
        const modalBody = modal.querySelector('.modal-body');
        
        modalTitle.textContent = title;
        modalBody.innerHTML = '<div class="text-center"><div class="spinner-accesspos"></div></div>';
        
        // Afficher le modal
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
        
        // Charger les données
        fetch(`/admin/api/${type}-details`)
            .then(response => response.json())
            .then(data => {
                modalBody.innerHTML = this.generateDetailsTable(data);
            })
            .catch(error => {
                modalBody.innerHTML = '<div class="alert alert-danger">Erreur lors du chargement des données</div>';
                console.error('Erreur:', error);
            });
    },
    
    /**
     * Générer tableau pour modal de détails
     */
    generateDetailsTable(data) {
        if (!data || !data.length) {
            return '<div class="alert alert-warning">Aucune donnée disponible</div>';
        }
        
        const headers = Object.keys(data[0]);
        let html = '<div class="table-responsive"><table class="table table-striped">';
        
        // En-têtes
        html += '<thead><tr>';
        headers.forEach(header => {
            html += `<th>${header}</th>`;
        });
        html += '</tr></thead>';
        
        // Corps du tableau
        html += '<tbody>';
        data.forEach(row => {
            html += '<tr>';
            headers.forEach(header => {
                let value = row[header];
                if (typeof value === 'number' && header.includes('montant')) {
                    value = AccessPos.utils.formatCurrency(value);
                }
                html += `<td>${value}</td>`;
            });
            html += '</tr>';
        });
        html += '</tbody></table></div>';
        
        return html;
    },
    
    /**
     * Exporter données dashboard
     */
    exportData(format = 'excel') {
        AccessPos.utils.showToast('Préparation de l\'export...', 'info');
        
        fetch(`/admin/api/dashboard-export?format=${format}`)
            .then(response => {
                if (response.ok) {
                    return response.blob();
                }
                throw new Error('Erreur lors de l\'export');
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `dashboard_${new Date().toISOString().split('T')[0]}.${format}`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                AccessPos.utils.showToast('Export terminé', 'success');
            })
            .catch(error => {
                console.error('Erreur export:', error);
                AccessPos.utils.showToast('Erreur lors de l\'export', 'danger');
            });
    }
};

/**
 * ==========================================
 * GESTIONNAIRE ARTICLES/PRODUITS
 * ==========================================
 */
window.AccessPosArticles = {
    /**
     * Initialiser la page articles
     */
    init() {
        this.initDataTable();
        this.setupEventListeners();
        this.setupQuickActions();
    },
    
    /**
     * Initialiser DataTable pour articles
     */
    initDataTable() {
        if (document.getElementById('articlesTable')) {
            window.articlesDataTable = AccessPos.datatables.init('#articlesTable', {
                ajax: {
                    url: '/admin/articles/api/data',
                    type: 'GET'
                },
                columns: [
                    { data: 'ART_ID', title: 'ID' },
                    { data: 'ART_DESIGNATION', title: 'Désignation' },
                    { data: 'ART_CODE_BARE', title: 'Code Barres' },
                    { data: 'famille_nom', title: 'Famille' },
                    { 
                        data: 'ART_PVT_TTC', 
                        title: 'Prix',
                        render: function(data) {
                            return AccessPos.utils.formatCurrency(data);
                        }
                    },
                    { 
                        data: 'stock_actuel', 
                        title: 'Stock',
                        render: function(data, type, row) {
                            const badgeClass = data < row.ART_STOCK_MIN ? 'badge-danger' : 'badge-success';
                            return `<span class="badge ${badgeClass}">${data}</span>`;
                        }
                    },
                    { 
                        data: 'ART_ACTIVE', 
                        title: 'Statut',
                        render: function(data) {
                            return data ? 
                                '<span class="badge badge-success">Actif</span>' : 
                                '<span class="badge badge-secondary">Inactif</span>';
                        }
                    },
                    {
                        data: null,
                        title: 'Actions',
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="btn-group btn-group-sm">
                                    <a href="/admin/articles/${row.ART_ID}" class="btn btn-info btn-sm" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/admin/articles/${row.ART_ID}/edit" class="btn btn-warning btn-sm" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="AccessPosArticles.toggleStatus(${row.ART_ID})" class="btn btn-secondary btn-sm" title="Activer/Désactiver">
                                        <i class="fas fa-toggle-on"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ]
            });
        }
    },
    
    /**
     * Configuration des événements
     */
    setupEventListeners() {
        // Filtre par famille
        const familleFilter = document.getElementById('familleFilter');
        if (familleFilter) {
            familleFilter.addEventListener('change', () => {
                if (window.articlesDataTable) {
                    window.articlesDataTable.ajax.reload();
                }
            });
        }
        
        // Recherche rapide
        const quickSearch = document.getElementById('quickSearch');
        if (quickSearch) {
            let timeout;
            quickSearch.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    if (window.articlesDataTable) {
                        window.articlesDataTable.search(quickSearch.value).draw();
                    }
                }, 300);
            });
        }
    },
    
    /**
     * Actions rapides
     */
    setupQuickActions() {
        // Scanner code-barres (si disponible)
        const scanButton = document.getElementById('scanBarcode');
        if (scanButton) {
            scanButton.addEventListener('click', this.scanBarcode);
        }
        
        // Import/Export
        const exportButton = document.getElementById('exportArticles');
        if (exportButton) {
            exportButton.addEventListener('click', () => this.exportArticles());
        }
    },
    
    /**
     * Basculer le statut d'un article
     */
    async toggleStatus(articleId) {
        const confirmed = await AccessPos.utils.confirm(
            'Confirmer l\'action',
            'Voulez-vous changer le statut de cet article ?'
        );
        
        if (confirmed) {
            try {
                const response = await fetch(`/admin/articles/${articleId}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': AccessPos.config.csrfToken,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    AccessPos.utils.showToast('Statut mis à jour', 'success');
                    if (window.articlesDataTable) {
                        window.articlesDataTable.ajax.reload(null, false);
                    }
                } else {
                    throw new Error('Erreur lors de la mise à jour');
                }
            } catch (error) {
                console.error('Erreur:', error);
                AccessPos.utils.showToast('Erreur lors de la mise à jour', 'danger');
            }
        }
    },
    
    /**
     * Ajouter du stock
     */
    async addStock(articleId) {
        const quantity = prompt('Quantité à ajouter:');
        if (quantity && !isNaN(quantity) && quantity > 0) {
            try {
                const response = await fetch(`/admin/articles/${articleId}/add-stock`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': AccessPos.config.csrfToken,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ quantity: parseFloat(quantity) })
                });
                
                if (response.ok) {
                    AccessPos.utils.showToast('Stock mis à jour', 'success');
                    if (window.articlesDataTable) {
                        window.articlesDataTable.ajax.reload(null, false);
                    }
                } else {
                    throw new Error('Erreur lors de la mise à jour du stock');
                }
            } catch (error) {
                console.error('Erreur:', error);
                AccessPos.utils.showToast('Erreur lors de la mise à jour du stock', 'danger');
            }
        }
    },
    
    /**
     * Scanner code-barres (WebRTC si disponible)
     */
    scanBarcode() {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            // Implémentation scanner caméra (nécessite librairie ZXing ou similaire)
            AccessPos.utils.showToast('Fonctionnalité scanner en développement', 'info');
        } else {
            AccessPos.utils.showToast('Caméra non disponible', 'warning');
        }
    },
    
    /**
     * Exporter articles
     */
    exportArticles() {
        AccessPos.utils.showToast('Préparation de l\'export...', 'info');
        
        // Utiliser l'export DataTables ou endpoint dédié
        if (window.articlesDataTable) {
            const buttons = window.articlesDataTable.buttons();
            // Déclencher export Excel
            buttons[0].trigger();
        }
    }
};

/**
 * ==========================================
 * GESTIONNAIRE FORMULAIRES
 * ==========================================
 */
window.AccessPosForms = {
    /**
     * Validation en temps réel
     */
    setupRealTimeValidation() {
        const forms = document.querySelectorAll('form[data-validate="true"]');
        forms.forEach(form => {
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });
                
                input.addEventListener('input', () => {
                    if (input.classList.contains('is-invalid')) {
                        this.validateField(input);
                    }
                });
            });
        });
    },
    
    /**
     * Valider un champ individuel
     */
    validateField(field) {
        const isValid = field.checkValidity();
        
        // Nettoyer les classes précédentes
        field.classList.remove('is-valid', 'is-invalid');
        
        // Ajouter la classe appropriée
        field.classList.add(isValid ? 'is-valid' : 'is-invalid');
        
        // Afficher/cacher message d'erreur personnalisé
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.style.display = isValid ? 'none' : 'block';
        }
        
        return isValid;
    },
    
    /**
     * Soumission AJAX de formulaire
     */
    submitFormAjax(form, successCallback = null) {
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        
        // Désactiver le bouton pendant la soumission
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
        }
        
        fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': AccessPos.config.csrfToken
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Erreur lors de la soumission');
        })
        .then(data => {
            AccessPos.utils.showToast(data.message || 'Formulaire soumis avec succès', 'success');
            if (successCallback) {
                successCallback(data);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            AccessPos.utils.showToast('Erreur lors de la soumission', 'danger');
        })
        .finally(() => {
            // Réactiver le bouton
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = submitButton.getAttribute('data-original-text') || 'Enregistrer';
            }
        });
    }
};

/**
 * ==========================================
 * GESTIONNAIRE MODALS
 * ==========================================
 */
window.AccessPosModals = {
    /**
     * Ouvrir modal avec contenu AJAX
     */
    openAjaxModal(url, title = '') {
        const modal = document.getElementById('ajaxModal') || this.createAjaxModal();
        const modalTitle = modal.querySelector('.modal-title');
        const modalBody = modal.querySelector('.modal-body');
        
        if (title) modalTitle.textContent = title;
        modalBody.innerHTML = '<div class="text-center"><div class="spinner-accesspos"></div></div>';
        
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
        
        fetch(url)
            .then(response => response.text())
            .then(html => {
                modalBody.innerHTML = html;
                // Réinitialiser les tooltips et validation dans le modal
                AccessPos.events.setupTooltips();
                AccessPosForms.setupRealTimeValidation();
            })
            .catch(error => {
                modalBody.innerHTML = '<div class="alert alert-danger">Erreur lors du chargement</div>';
                console.error('Erreur:', error);
            });
    },
    
    /**
     * Créer modal AJAX générique
     */
    createAjaxModal() {
        const modal = document.createElement('div');
        modal.id = 'ajaxModal';
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modal</h5>
                        <button type="button" class="close" data-bs-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Contenu chargé ici -->
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        return modal;
    }
};

/**
 * ==========================================
 * INITIALISATION GLOBALE
 * ==========================================
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser validation de formulaires
    AccessPosForms.setupRealTimeValidation();
    
    // Initialiser articles si on est sur la page
    if (document.getElementById('articlesTable')) {
        AccessPosArticles.init();
    }
    
    // Auto-refresh dashboard toutes les 5 minutes
    if (document.getElementById('dashboard-container')) {
        setInterval(() => {
            AccessPosDashboard.refreshLiveData();
        }, 300000); // 5 minutes
    }
    
    console.log('AccessPos Functions initialisées');
});

// Export global pour utilisation dans les templates
window.AccessPosDashboard = AccessPosDashboard;
window.AccessPosArticles = AccessPosArticles;
window.AccessPosForms = AccessPosForms;
window.AccessPosModals = AccessPosModals;
