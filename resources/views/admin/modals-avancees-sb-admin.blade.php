<!-- ===================================================================== -->
<!-- SYSTÈME DE MODALS AVANCÉES - SB ADMIN 2 VERSION -->
<!-- AccessPOS Pro - Dashboard Analytics System -->
<!-- ===================================================================== -->

<!-- Modal Analytics Principal -->
<div class="modal fade" id="analyticsModal" tabindex="-1" role="dialog" aria-labelledby="analyticsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <!-- Header Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="analyticsModalLabel">
                    <i class="fas fa-chart-bar mr-2"></i>
                    <span id="modalTitleText">Analyse Détaillée</span>
                </h5>
                <div class="ml-auto d-flex align-items-center">
                    <span class="badge badge-success mr-2">
                        <i class="fas fa-circle text-success" style="font-size: 0.6rem;"></i>
                        Données en direct
                    </span>
                    <small class="text-white-50 mr-3">
                        Mis à jour: <span id="lastUpdateTime">{{ date('H:i:s') }}</span>
                    </small>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            <!-- Navigation par Onglets -->
            <div class="modal-body p-0">
                <ul class="nav nav-tabs" id="analyticsTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview" role="tab">
                            <i class="fas fa-chart-bar mr-1"></i>Vue d'Ensemble
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="details-tab" data-toggle="tab" href="#details" role="tab">
                            <i class="fas fa-table mr-1"></i>Détails
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="charts-tab" data-toggle="tab" href="#charts" role="tab">
                            <i class="fas fa-chart-pie mr-1"></i>Graphiques
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="actions-tab" data-toggle="tab" href="#actions" role="tab">
                            <i class="fas fa-cog mr-1"></i>Actions
                        </a>
                    </li>
                </ul>

                <!-- Contenu des Onglets -->
                <div class="tab-content p-3" id="analyticsTabContent">
                    <!-- Onglet Vue d'Ensemble -->
                    <div class="tab-pane fade show active" id="overview" role="tabpanel">
                        <div class="row">
                            <!-- Statistiques Principales -->
                            <div class="col-lg-8">
                                <div id="overviewStats" class="row mb-4">
                                    <!-- Générées dynamiquement -->
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Évolution</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="overviewChart" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Points Clés -->
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-info">Points Clés</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="overviewHighlights">
                                            <!-- Générés dynamiquement -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet Détails -->
                    <div class="tab-pane fade" id="details" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="modalSearchInput" placeholder="Rechercher...">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" id="modalFilterSelect">
                                    <option value="">Tous les éléments</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" id="modalSortSelect">
                                    <option value="default">Trier par défaut</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="modalDataTable">
                                <!-- Généré dynamiquement -->
                            </table>
                        </div>
                        
                        <nav aria-label="Pagination">
                            <ul class="pagination justify-content-center" id="modalPagination">
                                <!-- Générée dynamiquement -->
                            </ul>
                        </nav>
                    </div>

                    <!-- Onglet Graphiques -->
                    <div class="tab-pane fade" id="charts" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Répartition</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="pieChart" height="150"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-success">Tendances</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="lineChart" height="150"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-warning">Comparaison</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="barChart" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet Actions -->
                    <div class="tab-pane fade" id="actions" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-success">Actions Rapides</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="list-group list-group-flush">
                                            <a href="#" class="list-group-item list-group-item-action" onclick="exportData('pdf')">
                                                <i class="fas fa-file-pdf text-danger mr-2"></i>
                                                Exporter en PDF
                                            </a>
                                            <a href="#" class="list-group-item list-group-item-action" onclick="exportData('excel')">
                                                <i class="fas fa-file-excel text-success mr-2"></i>
                                                Exporter en Excel
                                            </a>
                                            <a href="#" class="list-group-item list-group-item-action" onclick="sendEmail()">
                                                <i class="fas fa-envelope text-primary mr-2"></i>
                                                Envoyer par Email
                                            </a>
                                            <a href="#" class="list-group-item list-group-item-action" onclick="scheduleReport()">
                                                <i class="fas fa-calendar text-warning mr-2"></i>
                                                Programmer un Rapport
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-info">Paramètres</h6>
                                    </div>
                                    <div class="card-body">
                                        <form id="modalSettingsForm">
                                            <div class="form-group">
                                                <label for="dateRange">Période d'analyse:</label>
                                                <select class="form-control" id="dateRange">
                                                    <option value="today">Aujourd'hui</option>
                                                    <option value="week">Cette semaine</option>
                                                    <option value="month" selected>Ce mois</option>
                                                    <option value="quarter">Ce trimestre</option>
                                                    <option value="year">Cette année</option>
                                                    <option value="custom">Personnalisé</option>
                                                </select>
                                            </div>
                                            
                                            <div class="form-group" id="customDateRange" style="display: none;">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label for="startDate">Du:</label>
                                                        <input type="date" class="form-control" id="startDate">
                                                    </div>
                                                    <div class="col-6">
                                                        <label for="endDate">Au:</label>
                                                        <input type="date" class="form-control" id="endDate">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="refreshInterval">Actualisation automatique:</label>
                                                <select class="form-control" id="refreshInterval">
                                                    <option value="0">Désactivé</option>
                                                    <option value="30">30 secondes</option>
                                                    <option value="60" selected>1 minute</option>
                                                    <option value="300">5 minutes</option>
                                                </select>
                                            </div>
                                            
                                            <button type="button" class="btn btn-primary" onclick="applySettings()">
                                                <i class="fas fa-check mr-1"></i>Appliquer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="modal-footer bg-light">
                <div class="w-100 d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        <small>
                            <i class="fas fa-info-circle mr-1"></i>
                            Données mises à jour automatiquement
                        </small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>Fermer
                        </button>
                        <button type="button" class="btn btn-primary" onclick="refreshModalData()">
                            <i class="fas fa-sync-alt mr-1"></i>Actualiser
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles CSS pour le Modal -->
<style>
.modal-xl {
    max-width: 95%;
}

.nav-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #6c757d;
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    background-color: transparent;
    border-bottom: 3px solid #4e73df;
    color: #4e73df;
}

.nav-tabs .nav-link:hover {
    border-bottom: 3px solid #4e73df;
    color: #4e73df;
}

.table-responsive {
    max-height: 400px;
    overflow-y: auto;
}

.list-group-item-action:hover {
    background-color: #f8f9fc;
}

.badge-success {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.chart-container {
    position: relative;
    height: 300px;
}

.loading-spinner {
    display: none;
    text-align: center;
    padding: 2rem;
}

.loading-spinner .spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>

<!-- Scripts JavaScript pour le Modal -->
<script>
// Variables globales pour le modal
let modalData = {};
let currentModalType = '';
let refreshInterval = null;

// Fonction d'ouverture du modal avec données
function openAdvancedModal(type, title, data = {}) {
    currentModalType = type;
    modalData = data;
    
    // Mise à jour du titre
    document.getElementById('modalTitleText').textContent = title;
    
    // Chargement des données
    loadModalData();
    
    // Ouverture du modal
    $('#analyticsModal').modal('show');
    
    // Démarrage de l'actualisation automatique
    startAutoRefresh();
}

// Chargement des données dans le modal
function loadModalData() {
    // Affichage du spinner de chargement
    showLoading();
    
    setTimeout(() => {
        // Simulation du chargement des données
        generateOverviewStats();
        generateOverviewChart();
        generateHighlights();
        generateDetailsTable();
        generateCharts();
        
        hideLoading();
        updateTimestamp();
    }, 1000);
}

// Génération des statistiques
function generateOverviewStats() {
    const statsContainer = document.getElementById('overviewStats');
    const stats = getStatsForType(currentModalType);
    
    let html = '';
    stats.forEach(stat => {
        html += `
            <div class="col-md-3 mb-3">
                <div class="card border-left-${stat.color} shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-${stat.color} text-uppercase mb-1">
                                    ${stat.label}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ${stat.value}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="${stat.icon} fa-2x text-${stat.color}"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    statsContainer.innerHTML = html;
}

// Génération du graphique principal
function generateOverviewChart() {
    const ctx = document.getElementById('overviewChart').getContext('2d');
    
    // Configuration selon le type de modal
    const chartConfig = getChartConfigForType(currentModalType);
    
    new Chart(ctx, chartConfig);
}

// Génération des points clés
function generateHighlights() {
    const highlightsContainer = document.getElementById('overviewHighlights');
    const highlights = getHighlightsForType(currentModalType);
    
    let html = '';
    highlights.forEach(highlight => {
        html += `
            <div class="d-flex align-items-center mb-3">
                <i class="${highlight.icon} text-${highlight.color} mr-2"></i>
                <span class="text-sm">${highlight.text}</span>
            </div>
        `;
    });
    
    highlightsContainer.innerHTML = html;
}

// Génération du tableau de détails
function generateDetailsTable() {
    const tableContainer = document.getElementById('modalDataTable');
    const tableData = getTableDataForType(currentModalType);
    
    let html = '<thead class="thead-light"><tr>';
    tableData.headers.forEach(header => {
        html += `<th>${header}</th>`;
    });
    html += '</tr></thead><tbody>';
    
    tableData.rows.forEach(row => {
        html += '<tr>';
        row.forEach(cell => {
            html += `<td>${cell}</td>`;
        });
        html += '</tr>';
    });
    html += '</tbody>';
    
    tableContainer.innerHTML = html;
}

// Génération des graphiques
function generateCharts() {
    // Graphique en secteurs
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, getPieChartConfig());
    
    // Graphique linéaire
    const lineCtx = document.getElementById('lineChart').getContext('2d');
    new Chart(lineCtx, getLineChartConfig());
    
    // Graphique à barres
    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, getBarChartConfig());
}

// Fonctions utilitaires pour obtenir les données selon le type
function getStatsForType(type) {
    const statsData = {
        'chiffre-affaires': [
            { label: 'CA Aujourd\'hui', value: '12,345 DH', icon: 'fas fa-dollar-sign', color: 'primary' },
            { label: 'Transactions', value: '156', icon: 'fas fa-receipt', color: 'success' },
            { label: 'Ticket Moyen', value: '79.17 DH', icon: 'fas fa-chart-line', color: 'info' },
            { label: 'Évolution', value: '+15.3%', icon: 'fas fa-arrow-up', color: 'warning' }
        ],
        'stock': [
            { label: 'Articles Totaux', value: '1,247', icon: 'fas fa-boxes', color: 'primary' },
            { label: 'En Rupture', value: '23', icon: 'fas fa-exclamation-triangle', color: 'danger' },
            { label: 'Stock Critique', value: '67', icon: 'fas fa-exclamation-circle', color: 'warning' },
            { label: 'Valeur Stock', value: '145,230 DH', icon: 'fas fa-money-bill', color: 'success' }
        ]
        // Ajouter d'autres types selon les besoins
    };
    
    return statsData[type] || statsData['chiffre-affaires'];
}

function getChartConfigForType(type) {
    // Configuration de base pour un graphique linéaire
    return {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
            datasets: [{
                label: 'Évolution',
                data: [12, 19, 3, 5, 2, 3],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    };
}

function getHighlightsForType(type) {
    return [
        { text: 'Pic d\'activité entre 19h-20h', icon: 'fas fa-clock', color: 'info' },
        { text: 'Meilleur jour: Vendredi', icon: 'fas fa-calendar', color: 'success' },
        { text: 'Tendance à la hausse', icon: 'fas fa-arrow-up', color: 'primary' }
    ];
}

function getTableDataForType(type) {
    return {
        headers: ['Article', 'Quantité', 'Prix', 'Total'],
        rows: [
            ['Couscous', '5', '25 DH', '125 DH'],
            ['Tagine', '3', '45 DH', '135 DH'],
            ['Thé', '10', '15 DH', '150 DH']
        ]
    };
}

function getPieChartConfig() {
    return {
        type: 'doughnut',
        data: {
            labels: ['Produit A', 'Produit B', 'Produit C'],
            datasets: [{
                data: [30, 40, 30],
                backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    };
}

function getLineChartConfig() {
    return {
        type: 'line',
        data: {
            labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
            datasets: [{
                label: 'Ventes',
                data: [65, 59, 80, 81, 56, 55, 40],
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    };
}

function getBarChartConfig() {
    return {
        type: 'bar',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
            datasets: [{
                label: 'Ventes',
                data: [12, 19, 3, 5, 2, 3],
                backgroundColor: '#4e73df'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    };
}

// Fonctions d'actions
function exportData(format) {
    alert(`Export en ${format.toUpperCase()} - Fonctionnalité à implémenter`);
}

function sendEmail() {
    alert('Envoi par email - Fonctionnalité à implémenter');
}

function scheduleReport() {
    alert('Programmation de rapport - Fonctionnalité à implémenter');
}

function applySettings() {
    alert('Paramètres appliqués');
    loadModalData();
}

function refreshModalData() {
    loadModalData();
}

// Gestion de l'actualisation automatique
function startAutoRefresh() {
    const interval = document.getElementById('refreshInterval')?.value || 60;
    if (interval > 0) {
        refreshInterval = setInterval(() => {
            loadModalData();
        }, interval * 1000);
    }
}

function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
        refreshInterval = null;
    }
}

// Gestion des événements
$(document).ready(function() {
    // Arrêt de l'actualisation automatique à la fermeture du modal
    $('#analyticsModal').on('hidden.bs.modal', function() {
        stopAutoRefresh();
    });
    
    // Gestion du changement de période
    $('#dateRange').change(function() {
        if ($(this).val() === 'custom') {
            $('#customDateRange').show();
        } else {
            $('#customDateRange').hide();
        }
    });
    
    // Gestion de la recherche dans le tableau
    $('#modalSearchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('#modalDataTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});

// Fonctions utilitaires
function showLoading() {
    // Ajouter spinner de chargement si nécessaire
}

function hideLoading() {
    // Masquer spinner de chargement
}

function updateTimestamp() {
    const now = new Date();
    document.getElementById('lastUpdateTime').textContent = 
        now.toLocaleTimeString('fr-FR');
}
</script>
