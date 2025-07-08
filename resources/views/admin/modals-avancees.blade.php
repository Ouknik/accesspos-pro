<!-- ===================================================================== -->
<!-- SYSTÈME DE MODALS AVANCÉES - ANALYSES DÉTAILLÉES EN TEMPS RÉEL -->
<!-- AccessPOS Pro - Dashboard Analytics System -->
<!-- ===================================================================== -->

<!-- Modal Container Principal -->
<div id="advancedModalContainer" class="modal-overlay" style="display: none;">
    <div class="modal-advanced-wrapper">
        <div class="modal-advanced-content">
            <!-- Header Modal Dynamique -->
            <div class="modal-advanced-header">
                <h2 class="modal-advanced-title">
                    <i class="modal-icon"></i>
                    <span class="modal-title-text"></span>
                </h2>
                <div class="modal-badges">
                    <span class="badge-live">
                        <div class="live-dot"></div>
                        Données en direct
                    </span>
                    <span class="badge-time">
                        Mis à jour: <span id="lastUpdateTime"></span>
                    </span>
                </div>
                <button class="modal-close-btn" onclick="closeAdvancedModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Navigation par Onglets -->
            <div class="modal-tabs-container">
                <ul class="modal-tabs">
                    <li class="tab-item active" data-tab="overview">
                        <i class="fas fa-chart-bar"></i>
                        <span>Vue d'Ensemble</span>
                    </li>
                    <li class="tab-item" data-tab="details">
                        <i class="fas fa-table"></i>
                        <span>Détails</span>
                    </li>
                    <li class="tab-item" data-tab="charts">
                        <i class="fas fa-chart-pie"></i>
                        <span>Graphiques</span>
                    </li>
                    <li class="tab-item" data-tab="actions">
                        <i class="fas fa-cog"></i>
                        <span>Actions</span>
                    </li>
                </ul>
            </div>

            <!-- Contenu Dynamique des Onglets -->
            <div class="modal-content-area">
                <!-- Onglet Vue d'Ensemble -->
                <div class="tab-content active" data-tab="overview">
                    <div class="overview-grid">
                        <div class="overview-stats" id="overviewStats">
                            <!-- Statistiques principales générées dynamiquement -->
                        </div>
                        <div class="overview-chart" id="overviewChart">
                            <!-- Graphique principal -->
                        </div>
                    </div>
                    <div class="overview-highlights" id="overviewHighlights">
                        <!-- Points clés et alertes -->
                    </div>
                </div>

                <!-- Onglet Détails -->
                <div class="tab-content" data-tab="details">
                    <div class="details-controls">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="modalSearchInput" placeholder="Rechercher dans les données...">
                        </div>
                        <div class="filter-options">
                            <select id="modalFilterSelect">
                                <option value="">Tous les éléments</option>
                            </select>
                        </div>
                        <div class="sort-options">
                            <label>Trier par:</label>
                            <select id="modalSortSelect">
                                <option value="default">Par défaut</option>
                            </select>
                        </div>
                    </div>
                    <div class="details-table-container">
                        <table class="modal-data-table" id="modalDataTable">
                            <!-- Tableau généré dynamiquement -->
                        </table>
                    </div>
                    <div class="pagination-container" id="modalPagination">
                        <!-- Pagination dynamique -->
                    </div>
                </div>

                <!-- Onglet Graphiques -->
                <div class="tab-content" data-tab="charts">
                    <div class="charts-grid">
                        <div class="chart-container">
                            <canvas id="modalChart1"></canvas>
                        </div>
                        <div class="chart-container">
                            <canvas id="modalChart2"></canvas>
                        </div>
                        <div class="chart-container">
                            <canvas id="modalChart3"></canvas>
                        </div>
                        <div class="chart-container">
                            <canvas id="modalChart4"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Onglet Actions -->
                <div class="tab-content" data-tab="actions">
                    <div class="actions-grid">
                        <div class="action-section">
                            <h4>Exports</h4>
                            <div class="action-buttons">
                                <button class="action-btn export-pdf" onclick="exportModalData('pdf')">
                                    <i class="fas fa-file-pdf"></i>
                                    Exporter PDF
                                </button>
                                <button class="action-btn export-excel" onclick="exportModalData('excel')">
                                    <i class="fas fa-file-excel"></i>
                                    Exporter Excel
                                </button>
                                <button class="action-btn export-csv" onclick="exportModalData('csv')">
                                    <i class="fas fa-file-csv"></i>
                                    Exporter CSV
                                </button>
                            </div>
                        </div>
                        <div class="action-section">
                            <h4>Analyses</h4>
                            <div class="action-buttons">
                                <button class="action-btn analyze-trends" onclick="analyzeTrends()">
                                    <i class="fas fa-chart-line"></i>
                                    Analyser Tendances
                                </button>
                                <button class="action-btn generate-report" onclick="generateDetailedReport()">
                                    <i class="fas fa-file-alt"></i>
                                    Rapport Détaillé
                                </button>
                                <button class="action-btn schedule-alert" onclick="scheduleAlert()">
                                    <i class="fas fa-bell"></i>
                                    Programmer Alerte
                                </button>
                            </div>
                        </div>
                        <div class="action-section">
                            <h4>Outils</h4>
                            <div class="action-buttons">
                                <button class="action-btn refresh-data" onclick="refreshModalData()">
                                    <i class="fas fa-sync-alt"></i>
                                    Actualiser
                                </button>
                                <button class="action-btn fullscreen" onclick="toggleFullscreen()">
                                    <i class="fas fa-expand"></i>
                                    Plein Écran
                                </button>
                                <button class="action-btn print" onclick="printModal()">
                                    <i class="fas fa-print"></i>
                                    Imprimer
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="recommendations-section" id="modalRecommendations">
                        <!-- Recommandations intelligentes -->
                    </div>
                </div>
            </div>

            <!-- Footer avec Actions Rapides -->
            <div class="modal-advanced-footer">
                <div class="footer-stats" id="footerStats">
                    <!-- Statistiques résumées -->
                </div>
                <div class="footer-actions">
                    <button class="btn-secondary" onclick="shareModal()">
                        <i class="fas fa-share-alt"></i>
                        Partager
                    </button>
                    <button class="btn-primary" onclick="saveModalState()">
                        <i class="fas fa-save"></i>
                        Sauvegarder
                    </button>
                    <button class="btn-danger" onclick="closeAdvancedModal()">
                        <i class="fas fa-times"></i>
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay pour les Modals -->
<div id="modalLoadingOverlay" class="modal-loading" style="display: none;">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <p>Chargement des données avancées...</p>
        <small>Analyse en cours, veuillez patienter</small>
    </div>
</div>

<!-- ===================================================================== -->
<!-- STYLES CSS POUR LES MODALS AVANCÉES -->
<!-- ===================================================================== -->

<style>
/* Modal Overlay Principal */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(10px);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.modal-overlay.show {
    opacity: 1;
}

/* Container Modal Avancé */
.modal-advanced-wrapper {
    width: 95vw;
    height: 90vh;
    max-width: 1400px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    transform: scale(0.9) translateY(50px);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-overlay.show .modal-advanced-wrapper {
    transform: scale(1) translateY(0);
}

.modal-advanced-content {
    height: 100%;
    display: flex;
    flex-direction: column;
}

/* Header Modal */
.modal-advanced-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
}

.modal-advanced-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 30% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
    pointer-events: none;
}

.modal-advanced-title {
    display: flex;
    align-items: center;
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
    z-index: 2;
    position: relative;
}

.modal-icon {
    margin-right: 15px;
    font-size: 2rem;
}

.modal-badges {
    display: flex;
    gap: 15px;
    z-index: 2;
    position: relative;
}

.badge-live, .badge-time {
    background: rgba(255, 255, 255, 0.15);
    padding: 8px 15px;
    border-radius: 25px;
    font-size: 0.85rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

.badge-live {
    display: flex;
    align-items: center;
    gap: 8px;
}

.live-dot {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

.modal-close-btn {
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 2;
    position: relative;
}

.modal-close-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

/* Navigation par Onglets */
.modal-tabs-container {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.modal-tabs {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    overflow-x: auto;
}

.tab-item {
    padding: 18px 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    border-bottom: 3px solid transparent;
    color: #64748b;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 10px;
    white-space: nowrap;
}

.tab-item:hover {
    background: #e2e8f0;
    color: #334155;
}

.tab-item.active {
    background: white;
    color: #3b82f6;
    border-bottom-color: #3b82f6;
}

/* Contenu des Onglets */
.modal-content-area {
    flex: 1;
    overflow: hidden;
    position: relative;
}

.tab-content {
    height: 100%;
    padding: 30px;
    overflow-y: auto;
    display: none;
}

.tab-content.active {
    display: block;
}

/* Vue d'Ensemble */
.overview-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

.overview-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.overview-stat-card {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 25px;
    border-radius: 15px;
    text-align: center;
    border: 1px solid #e2e8f0;
    transition: transform 0.3s ease;
}

.overview-stat-card:hover {
    transform: translateY(-5px);
}

.overview-stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 8px;
}

.overview-stat-label {
    color: #64748b;
    font-size: 0.9rem;
    font-weight: 500;
}

.overview-chart {
    background: white;
    border-radius: 15px;
    padding: 25px;
    border: 1px solid #e2e8f0;
}

.overview-highlights {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.highlight-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    border-left: 4px solid #3b82f6;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.highlight-title {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 10px;
}

.highlight-content {
    color: #64748b;
    line-height: 1.6;
}

/* Onglet Détails */
.details-controls {
    display: flex;
    gap: 20px;
    margin-bottom: 25px;
    align-items: center;
    flex-wrap: wrap;
}

.search-box {
    position: relative;
    flex: 1;
    min-width: 300px;
}

.search-box i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
}

.search-box input {
    width: 100%;
    padding: 12px 15px 12px 45px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: border-color 0.3s ease;
}

.search-box input:focus {
    outline: none;
    border-color: #3b82f6;
}

.filter-options, .sort-options {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-options select, .sort-options select {
    padding: 12px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    background: white;
    font-size: 0.9rem;
}

/* Tableau de Données */
.details-table-container {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
}

.modal-data-table {
    width: 100%;
    border-collapse: collapse;
}

.modal-data-table th {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    color: white;
    padding: 18px 15px;
    text-align: left;
    font-weight: 600;
    font-size: 0.9rem;
    position: sticky;
    top: 0;
    z-index: 10;
}

.modal-data-table td {
    padding: 15px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.9rem;
    color: #334155;
}

.modal-data-table tbody tr:hover {
    background: #f8fafc;
}

.modal-data-table tbody tr:nth-child(even) {
    background: #fafbfc;
}

/* Graphiques */
.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 25px;
    height: 100%;
}

.chart-container {
    background: white;
    border-radius: 15px;
    padding: 25px;
    border: 1px solid #e2e8f0;
    position: relative;
}

.chart-container canvas {
    max-height: 300px;
}

/* Actions */
.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 30px;
}

.action-section {
    background: white;
    border-radius: 15px;
    padding: 25px;
    border: 1px solid #e2e8f0;
}

.action-section h4 {
    color: #1e293b;
    margin-bottom: 20px;
    font-size: 1.1rem;
    font-weight: 600;
}

.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.action-btn {
    padding: 12px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    background: white;
    color: #334155;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.9rem;
    font-weight: 500;
}

.action-btn:hover {
    border-color: #3b82f6;
    background: #f0f9ff;
    color: #3b82f6;
    transform: translateY(-2px);
}

.action-btn.export-pdf:hover { border-color: #dc2626; background: #fef2f2; color: #dc2626; }
.action-btn.export-excel:hover { border-color: #059669; background: #f0fdf4; color: #059669; }
.action-btn.export-csv:hover { border-color: #d97706; background: #fffbeb; color: #d97706; }

/* Recommandations */
.recommendations-section {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border-radius: 15px;
    padding: 25px;
    border: 1px solid #bae6fd;
}

.recommendation-item {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    border-left: 4px solid #3b82f6;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.recommendation-title {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 8px;
}

.recommendation-description {
    color: #64748b;
    line-height: 1.5;
}

/* Footer Modal */
.modal-advanced-footer {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    padding: 20px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.footer-stats {
    display: flex;
    gap: 30px;
    color: #64748b;
    font-size: 0.9rem;
}

.footer-actions {
    display: flex;
    gap: 15px;
}

.btn-primary, .btn-secondary, .btn-danger {
    padding: 12px 24px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-2px);
}

.btn-secondary {
    background: #64748b;
    color: white;
}

.btn-secondary:hover {
    background: #475569;
    transform: translateY(-2px);
}

.btn-danger {
    background: #dc2626;
    color: white;
}

.btn-danger:hover {
    background: #b91c1c;
    transform: translateY(-2px);
}

/* Loading Modal */
.modal-loading {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 15000;
}

.loading-content {
    background: white;
    border-radius: 20px;
    padding: 40px;
    text-align: center;
    max-width: 400px;
}

.loading-spinner {
    width: 60px;
    height: 60px;
    border: 4px solid #f3f4f6;
    border-top: 4px solid #3b82f6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 1024px) {
    .modal-advanced-wrapper {
        width: 98vw;
        height: 95vh;
    }
    
    .overview-grid {
        grid-template-columns: 1fr;
    }
    
    .charts-grid {
        grid-template-columns: 1fr;
    }
    
    .actions-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .modal-advanced-header {
        padding: 20px;
    }
    
    .modal-advanced-title {
        font-size: 1.4rem;
    }
    
    .modal-tabs {
        flex-wrap: wrap;
    }
    
    .tab-content {
        padding: 20px;
    }
    
    .details-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-box {
        min-width: auto;
    }
    
    .modal-advanced-footer {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
}

/* Animation d'ouverture des cartes */
.overview-stat-card, .highlight-card, .action-section {
    animation: slideInUp 0.6s ease forwards;
    opacity: 0;
    transform: translateY(30px);
}

.overview-stat-card:nth-child(1) { animation-delay: 0.1s; }
.overview-stat-card:nth-child(2) { animation-delay: 0.2s; }
.overview-stat-card:nth-child(3) { animation-delay: 0.3s; }
.overview-stat-card:nth-child(4) { animation-delay: 0.4s; }

@keyframes slideInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Hover Effects pour les tableaux */
.modal-data-table tbody tr {
    transition: all 0.3s ease;
}

.modal-data-table tbody tr:hover {
    transform: scale(1.01);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Badges de statut dans les tableaux */
.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-success { background: #dcfce7; color: #166534; }
.status-warning { background: #fef3c7; color: #92400e; }
.status-danger { background: #fecaca; color: #991b1b; }
.status-info { background: #dbeafe; color: #1e40af; }

/* Indicateurs de progression */
.progress-bar {
    width: 100%;
    height: 8px;
    background: #f1f5f9;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #3b82f6, #1d4ed8);
    border-radius: 4px;
    transition: width 0.3s ease;
}
</style>

<!-- ===================================================================== -->
<!-- JAVASCRIPT POUR LES MODALS AVANCÉES -->
<!-- ===================================================================== -->

<script>
// Variables globales pour les modals
let currentModalType = null;
let currentModalData = null;
let modalCharts = {};

// Configuration des modals
const modalConfigs = {
    'chiffre-affaires': {
        title: 'Analyse Détaillée du Chiffre d\'Affaires',
        icon: 'fas fa-chart-line',
        endpoint: '/admin/api/chiffre-affaires-details',
        color: '#059669'
    },
    'articles-rupture': {
        title: 'Gestion Intelligente des Stocks',
        icon: 'fas fa-exclamation-triangle',
        endpoint: '/admin/api/articles-rupture-details',
        color: '#dc2626'
    },
    'top-clients': {
        title: 'Analyse Avancée de la Clientèle',
        icon: 'fas fa-users',
        endpoint: '/admin/api/top-clients-details',
        color: '#d97706'
    },
    'performance-horaire': {
        title: 'Performance Temporelle Détaillée',
        icon: 'fas fa-clock',
        endpoint: '/admin/api/performance-horaire-details',
        color: '#3b82f6'
    },
    'modes-paiement': {
        title: 'Analyse Financière des Paiements',
        icon: 'fas fa-credit-card',
        endpoint: '/admin/api/modes-paiement-details',
        color: '#8b5cf6'
    },
    'etat-tables': {
        title: 'Gestion Restaurant en Temps Réel',
        icon: 'fas fa-utensils',
        endpoint: '/admin/api/etat-tables-details',
        color: '#f59e0b'
    }
};

/**
 * Ouvrir une modal avancée
 */
async function openAdvancedModal(type, params = {}) {
    currentModalType = type;
    const config = modalConfigs[type];
    
    if (!config) {
        console.error('Type de modal non reconnu:', type);
        return;
    }

    // Afficher le loading
    showModalLoading();
    
    // Configurer l'interface
    setupModalInterface(config);
    
    try {
        // Charger les données
        const response = await fetch(config.endpoint + '?' + new URLSearchParams(params));
        const result = await response.json();
        
        if (result.success) {
            currentModalData = result.data;
            populateModalContent(type, result.data);
            showModal();
        } else {
            throw new Error(result.message || 'Erreur lors du chargement des données');
        }
    } catch (error) {
        console.error('Erreur lors du chargement de la modal:', error);
        alert('Erreur lors du chargement des données: ' + error.message);
    } finally {
        hideModalLoading();
    }
}

/**
 * Configurer l'interface de la modal
 */
function setupModalInterface(config) {
    const modal = document.getElementById('advancedModalContainer');
    const titleIcon = modal.querySelector('.modal-icon');
    const titleText = modal.querySelector('.modal-title-text');
    
    titleIcon.className = `modal-icon ${config.icon}`;
    titleText.textContent = config.title;
    
    // Appliquer la couleur thématique
    const header = modal.querySelector('.modal-advanced-header');
    header.style.background = `linear-gradient(135deg, ${config.color} 0%, ${adjustBrightness(config.color, -20)} 100%)`;
}

/**
 * Peupler le contenu de la modal selon le type
 */
function populateModalContent(type, data) {
    // Effacer le contenu précédent
    clearModalContent();
    
    // Peupler selon le type
    switch (type) {
        case 'chiffre-affaires':
            populateChiffreAffairesModal(data);
            break;
        case 'articles-rupture':
            populateArticlesRuptureModal(data);
            break;
        case 'top-clients':
            populateTopClientsModal(data);
            break;
        case 'performance-horaire':
            populatePerformanceHoraireModal(data);
            break;
        case 'modes-paiement':
            populateModesPaiementModal(data);
            break;
        case 'etat-tables':
            populateEtatTablesModal(data);
            break;
    }
    
    updateLastUpdateTime();
}

/**
 * Peupler la modal Chiffre d'Affaires
 */
function populateChiffreAffairesModal(data) {
    // Vue d'ensemble
    const overviewStats = document.getElementById('overviewStats');
    const stats = data.stats_generales;
    
    overviewStats.innerHTML = `
        <div class="overview-stat-card">
            <div class="overview-stat-value">${formatCurrency(stats.ca_total || 0)}</div>
            <div class="overview-stat-label">Chiffre d'Affaires Total</div>
        </div>
        <div class="overview-stat-card">
            <div class="overview-stat-value">${stats.nb_factures || 0}</div>
            <div class="overview-stat-label">Nombre de Factures</div>
        </div>
        <div class="overview-stat-card">
            <div class="overview-stat-value">${formatCurrency(stats.ticket_moyen || 0)}</div>
            <div class="overview-stat-label">Ticket Moyen</div>
        </div>
        <div class="overview-stat-card">
            <div class="overview-stat-value">${stats.nb_clients_distincts || 0}</div>
            <div class="overview-stat-label">Clients Uniques</div>
        </div>
    `;
    
    // Graphique des ventes par heure
    createHourlyChart(data.ventes_par_heure);
    
    // Highlights
    const highlights = document.getElementById('overviewHighlights');
    highlights.innerHTML = `
        <div class="highlight-card">
            <div class="highlight-title">🏆 Produit Star</div>
            <div class="highlight-content">
                ${data.top_produits[0]?.nom_produit || 'Aucun'} 
                (${data.top_produits[0]?.quantite_vendue || 0} unités vendues)
            </div>
        </div>
        <div class="highlight-card">
            <div class="highlight-title">⏰ Heure de Pointe</div>
            <div class="highlight-content">
                ${findPeakHour(data.ventes_par_heure)}h 
                (${formatCurrency(findPeakAmount(data.ventes_par_heure))} DH)
            </div>
        </div>
        <div class="highlight-card">
            <div class="highlight-title">💳 Mode de Paiement Préféré</div>
            <div class="highlight-content">
                ${determinePreferredPayment(stats)} 
                (${formatCurrency(getPreferredPaymentAmount(stats))} DH)
            </div>
        </div>
    `;
    
    // Tableau détaillé des produits
    populateProductsTable(data.top_produits);
}

/**
 * Créer le graphique des ventes par heure
 */
function createHourlyChart(hourlyData) {
    const ctx = document.getElementById('modalChart1');
    if (!ctx) return;
    
    // Détruire le graphique existant
    if (modalCharts.hourly) {
        modalCharts.hourly.destroy();
    }
    
    modalCharts.hourly = new Chart(ctx, {
        type: 'line',
        data: {
            labels: hourlyData.map(item => `${item.heure}h`),
            datasets: [{
                label: 'Chiffre d\'Affaires (DH)',
                data: hourlyData.map(item => item.ca_heure),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Nombre de Ventes',
                data: hourlyData.map(item => item.nb_ventes),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Chiffre d\'Affaires (DH)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Nombre de Ventes'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Performance par Heure'
                }
            }
        }
    });
}

/**
 * Peupler le tableau des produits
 */
function populateProductsTable(products) {
    const table = document.getElementById('modalDataTable');
    
    let tableHTML = `
        <thead>
            <tr>
                <th>Produit</th>
                <th>Famille</th>
                <th>Quantité</th>
                <th>Prix Unitaire</th>
                <th>CA Total</th>
                <th>Marge</th>
                <th>% CA</th>
            </tr>
        </thead>
        <tbody>
    `;
    
    products.forEach(product => {
        tableHTML += `
            <tr>
                <td><strong>${product.nom_produit}</strong></td>
                <td>${product.famille || '-'}</td>
                <td>${product.quantite_vendue}</td>
                <td>${formatCurrency(product.prix_unitaire)}</td>
                <td>${formatCurrency(product.ca_produit)}</td>
                <td>${formatCurrency(product.marge_unitaire)} <small>(${product.taux_marge?.toFixed(1)}%)</small></td>
                <td>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${product.pourcentage_ca}%"></div>
                    </div>
                    ${product.pourcentage_ca}%
                </td>
            </tr>
        `;
    });
    
    tableHTML += '</tbody>';
    table.innerHTML = tableHTML;
}

/**
 * Utilitaires
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD',
        minimumFractionDigits: 2
    }).format(amount || 0);
}

function findPeakHour(hourlyData) {
    if (!hourlyData || hourlyData.length === 0) return '0';
    return hourlyData.reduce((max, current) => 
        current.ca_heure > max.ca_heure ? current : max
    ).heure;
}

function findPeakAmount(hourlyData) {
    if (!hourlyData || hourlyData.length === 0) return 0;
    return hourlyData.reduce((max, current) => 
        current.ca_heure > max.ca_heure ? current : max
    ).ca_heure;
}

function determinePreferredPayment(stats) {
    const payments = {
        'Espèces': stats.total_especes || 0,
        'Carte': stats.total_carte || 0,
        'Chèque': stats.total_cheque || 0,
        'Crédit': stats.total_credit || 0
    };
    
    return Object.keys(payments).reduce((a, b) => payments[a] > payments[b] ? a : b);
}

function getPreferredPaymentAmount(stats) {
    return Math.max(
        stats.total_especes || 0,
        stats.total_carte || 0,
        stats.total_cheque || 0,
        stats.total_credit || 0
    );
}

function adjustBrightness(hex, percent) {
    const num = parseInt(hex.replace("#", ""), 16);
    const amt = Math.round(2.55 * percent);
    const R = (num >> 16) + amt;
    const G = (num >> 8 & 0x00FF) + amt;
    const B = (num & 0x0000FF) + amt;
    return "#" + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
        (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
        (B < 255 ? B < 1 ? 0 : B : 255)).toString(16).slice(1);
}

/**
 * Gestion des onglets
 */
function switchTab(tabName) {
    // Désactiver tous les onglets
    document.querySelectorAll('.tab-item').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    // Activer l'onglet sélectionné
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    document.querySelector(`.tab-content[data-tab="${tabName}"]`).classList.add('active');
}

/**
 * Gestion des événements
 */
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des clics sur les onglets
    document.querySelectorAll('.tab-item').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            switchTab(tabName);
        });
    });
    
    // Gestion de la recherche
    const searchInput = document.getElementById('modalSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterTableRows(this.value);
        });
    }
    
    // Fermeture avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && currentModalType) {
            closeAdvancedModal();
        }
    });
});

/**
 * Fonctions de contrôle de la modal
 */
function showModal() {
    const modal = document.getElementById('advancedModalContainer');
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
}

function closeAdvancedModal() {
    const modal = document.getElementById('advancedModalContainer');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
        clearModalContent();
        currentModalType = null;
        currentModalData = null;
    }, 300);
}

function showModalLoading() {
    document.getElementById('modalLoadingOverlay').style.display = 'flex';
}

function hideModalLoading() {
    document.getElementById('modalLoadingOverlay').style.display = 'none';
}

function clearModalContent() {
    // Détruire les graphiques existants
    Object.values(modalCharts).forEach(chart => {
        if (chart && typeof chart.destroy === 'function') {
            chart.destroy();
        }
    });
    modalCharts = {};
    
    // Vider les contenus
    const containers = ['overviewStats', 'overviewHighlights', 'modalDataTable'];
    containers.forEach(id => {
        const element = document.getElementById(id);
        if (element) element.innerHTML = '';
    });
}

function updateLastUpdateTime() {
    const timeElement = document.getElementById('lastUpdateTime');
    if (timeElement) {
        timeElement.textContent = new Date().toLocaleTimeString('fr-FR');
    }
}

/**
 * Fonctions d'action
 */
function refreshModalData() {
    if (currentModalType) {
        openAdvancedModal(currentModalType);
    }
}

function exportModalData(format) {
    if (!currentModalData) {
        alert('Aucune donnée à exporter');
        return;
    }
    
    const formData = new FormData();
    formData.append('format', format);
    formData.append('type', currentModalType);
    formData.append('data', JSON.stringify(currentModalData));
    
    fetch('/admin/api/export-modal-data', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${currentModalType}-${new Date().toISOString().split('T')[0]}.${format}`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    })
    .catch(error => {
        console.error('Erreur lors de l\'export:', error);
        alert('Erreur lors de l\'export');
    });
}

function filterTableRows(searchTerm) {
    const table = document.getElementById('modalDataTable');
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm.toLowerCase()) ? '' : 'none';
    });
}

function toggleFullscreen() {
    const modal = document.getElementById('advancedModalContainer');
    
    if (!document.fullscreenElement) {
        modal.requestFullscreen().catch(err => {
            console.error('Erreur fullscreen:', err);
        });
    } else {
        document.exitFullscreen();
    }
}

function printModal() {
    window.print();
}

function shareModal() {
    if (navigator.share) {
        navigator.share({
            title: 'Analyse AccessPOS Pro',
            text: 'Données d\'analyse détaillées',
            url: window.location.href
        });
    } else {
        // Fallback pour copier l'URL
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Lien copié dans le presse-papiers');
        });
    }
}

function saveModalState() {
    if (currentModalData) {
        localStorage.setItem(`modal_${currentModalType}_data`, JSON.stringify(currentModalData));
        alert('État sauvegardé avec succès');
    }
}

// Auto-refresh des données toutes les 5 minutes
setInterval(() => {
    if (currentModalType && document.getElementById('advancedModalContainer').style.display !== 'none') {
        refreshModalData();
    }
}, 300000);
</script>
