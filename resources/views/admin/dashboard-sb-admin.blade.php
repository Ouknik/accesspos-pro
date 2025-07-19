@extends('layouts.sb-admin')

@section('title', 'Tableau de Bord - AccessPos Pro')

@section('page-heading')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-tachometer-alt"></i>
        Tableau de Bord
    </h1>
    <div class="d-flex align-items-center">
        <div class="mr-3">
            <small class="text-muted">Dernière mise à jour: {{ now()->format('H:i:s') }}</small>
        </div>
        <span class="badge badge-success">
            <i class="fas fa-circle text-success" style="font-size: 8px;"></i>
            En Direct
        </span>
    </div>
</div>
@endsection

@section('content')

{{-- Messages d'erreur --}}
@if(isset($erreur))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle mr-2"></i>
    {{ $erreur }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

{{-- Statistiques Principales --}}
<div class="row">

    {{-- Chiffre d'Affaires du Jour --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Chiffre d'Affaires du Jour
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($statistiquesFinancieres['ca_du_jour'] ?? 0, 2) }} DH
                        </div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-arrow-up text-success mr-1"></i>
                            +{{ number_format($statistiquesFinancieres['evolution_ventes'] ?? 0, 1) }}% vs mois dernier
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="d-flex">
                        <a href="{{ route('admin.dashboard.chiffre-affaires') }}" 
                           target="_blank" 
                           class="btn btn-success btn-sm mr-2 flex-fill">
                            <i class="fas fa-external-link-alt mr-1"></i>
                            Détails
                        </a>
                        <button onclick="exportData('chiffre-affaires', 'pdf')" 
                                class="btn btn-outline-success btn-sm">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CA du Mois --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            CA du Mois
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($statistiquesFinancieres['ca_du_mois'] ?? 0, 2) }} DH
                        </div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-receipt mr-1"></i>
                            {{ number_format($statistiquesFinancieres['nb_factures_jour'] ?? 0) }} factures aujourd'hui
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-month fa-2x text-gray-300"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="d-flex">
                        <a href="{{ route('admin.dashboard.chiffre-affaires') }}?periode=mois" 
                           target="_blank" 
                           class="btn btn-info btn-sm mr-2 flex-fill">
                            <i class="fas fa-external-link-alt mr-1"></i>
                            Détails
                        </a>
                        <button onclick="exportData('ca-mois', 'pdf')" 
                                class="btn btn-outline-info btn-sm">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Nombre de Ventes --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Nombre de Ventes
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($statistiquesFinancieres['nb_ventes_jour'] ?? 0) }}
                        </div>
                        <div class="text-xs text-muted mt-1">
                            Transactions aujourd'hui
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ticket Moyen --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Ticket Moyen
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($statistiquesFinancieres['ticket_moyen'] ?? 0, 2) }} DH
                        </div>
                        <div class="text-xs text-muted mt-1">
                            Par transaction
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-receipt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Graphiques et Tableaux --}}
<div class="row">

    {{-- Graphique des Ventes --}}
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line mr-2"></i>
                    Évolution des Ventes
                </h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Options:</div>
                        <a class="dropdown-item" href="#" onclick="changePeriode('7jours')">7 derniers jours</a>
                        <a class="dropdown-item" href="#" onclick="changePeriode('30jours')">30 derniers jours</a>
                        <a class="dropdown-item" href="#" onclick="changePeriode('3mois')">3 derniers mois</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" onclick="exportChart('ventes')">
                            <i class="fas fa-download mr-1"></i>
                            Exporter
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="chartVentes" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Produits --}}
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-trophy mr-2"></i>
                    Top Produits
                </h6>
                <a href="{{ route('admin.articles.index') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye mr-1"></i>
                    Voir tout
                </a>
            </div>
            <div class="card-body">
                @if(isset($topProduits) && count($topProduits) > 0)
                    @foreach($topProduits as $index => $produit)
                    <div class="d-flex align-items-center mb-3">
                        <div class="mr-3">
                            <span class="badge badge-pill 
                                @if($index == 0) badge-warning
                                @elseif($index == 1) badge-secondary  
                                @elseif($index == 2) badge-dark
                                @else badge-light @endif">
                                {{ $index + 1 }}
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="font-weight-bold text-gray-800">
                                {{ $produit['nom'] ?? 'Produit inconnu' }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ number_format($produit['quantite_vendue'] ?? 0) }} unités vendues
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-weight-bold text-success">
                                {{ number_format($produit['ca_genere'] ?? 0, 2) }} DH
                            </div>
                        </div>
                    </div>
                    @if($index < count($topProduits) - 1)
                    <hr class="my-2">
                    @endif
                    @endforeach
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-box fa-3x mb-3"></i>
                        <p>Aucun produit vendu aujourd'hui</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- Tableaux de Gestion --}}
<div class="row">

    {{-- Tables en Service --}}
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-utensils mr-2"></i>
                    État des Tables
                </h6>
                <a href="{{ route('admin.dashboard.etat-tables') }}" 
                   target="_blank" 
                   class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-external-link-alt mr-1"></i>
                    Détails
                </a>
            </div>
            <div class="card-body">
                @if(isset($etatTables))
                <div class="row text-center">
                    <div class="col-4">
                        <div class="border-right">
                            <h4 class="text-success font-weight-bold">{{ $etatTables['libres'] ?? 0 }}</h4>
                            <span class="text-xs text-uppercase text-muted">Libres</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border-right">
                            <h4 class="text-warning font-weight-bold">{{ $etatTables['occupees'] ?? 0 }}</h4>
                            <span class="text-xs text-uppercase text-muted">Occupées</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <h4 class="text-danger font-weight-bold">{{ $etatTables['reservees'] ?? 0 }}</h4>
                        <span class="text-xs text-uppercase text-muted">Réservées</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modes de Paiement --}}
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-credit-card mr-2"></i>
                    Modes de Paiement
                </h6>
                <a href="{{ route('admin.dashboard.modes-paiement') }}" 
                   target="_blank" 
                   class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-external-link-alt mr-1"></i>
                    Détails
                </a>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="chartModesPaiement"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Alertes et Notifications --}}
<div class="row">
    
    {{-- Stock en Rupture --}}
    <div class="col-xl-4 col-lg-6">
        <div class="card border-left-danger shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Stock en Rupture
                </h6>
            </div>
            <div class="card-body">
                @if(isset($stockEnRupture) && count($stockEnRupture) > 0)
                    @foreach($stockEnRupture->take(5) as $produit)
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-exclamation-circle text-danger mr-2"></i>
                        <span class="text-gray-800">{{ $produit['nom'] ?? 'Produit inconnu' }}</span>
                    </div>
                    @endforeach
                    @if(count($stockEnRupture) > 5)
                    <a href="{{ route('admin.dashboard.stock-rupture') }}" 
                       target="_blank" 
                       class="btn btn-danger btn-sm mt-2">
                        <i class="fas fa-eye mr-1"></i>
                        Voir tout ({{ count($stockEnRupture) }})
                    </a>
                    @endif
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                        <p class="mb-0">Stock optimal</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Performance Horaire --}}
    <div class="col-xl-8 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-clock mr-2"></i>
                    Performance par Heure
                </h6>
                <a href="{{ route('admin.dashboard.performance-horaire') }}" 
                   target="_blank" 
                   class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-external-link-alt mr-1"></i>
                    Détails
                </a>
            </div>
            <div class="card-body">
                <div class="chart-bar">
                    <canvas id="chartPerformanceHoraire" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
// Configuration des graphiques
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Graphique des Ventes (Line Chart)
var ctx = document.getElementById("chartVentes");
var chartVentes = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($ventesParJour['labels'] ?? []) !!},
        datasets: [{
            label: "Ventes (DH)",
            lineTension: 0.3,
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: {!! json_encode($ventesParJour['data'] ?? []) !!},
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                time: {
                    unit: 'date'
                },
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 7
                }
            }],
            yAxes: [{
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    callback: function(value, index, values) {
                        return value.toLocaleString() + ' DH';
                    }
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }],
        },
        legend: {
            display: false
        },
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            intersect: false,
            mode: 'index',
            caretPadding: 10,
            callbacks: {
                label: function(tooltipItem, chart) {
                    var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                    return datasetLabel + ': ' + tooltipItem.yLabel.toLocaleString() + ' DH';
                }
            }
        }
    }
});

// Graphique Modes de Paiement (Pie Chart)
var ctx2 = document.getElementById("chartModesPaiement");
var chartModesPaiement = new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($modesPaiement['labels'] ?? []) !!},
        datasets: [{
            data: {!! json_encode($modesPaiement['data'] ?? []) !!},
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#e02d1b'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
        },
        legend: {
            display: true,
            position: 'bottom',
            labels: {
                padding: 20
            }
        },
        cutoutPercentage: 80,
    },
});

// Graphique Performance Horaire (Bar Chart)
var ctx3 = document.getElementById("chartPerformanceHoraire");
var chartPerformanceHoraire = new Chart(ctx3, {
    type: 'bar',
    data: {
        labels: {!! json_encode($performanceHoraire['labels'] ?? []) !!},
        datasets: [{
            label: "Ventes (DH)",
            backgroundColor: "#4e73df",
            hoverBackgroundColor: "#2e59d9",
            borderColor: "#4e73df",
            data: {!! json_encode($performanceHoraire['data'] ?? []) !!},
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 6
                },
                maxBarThickness: 25,
            }],
            yAxes: [{
                ticks: {
                    padding: 10,
                    callback: function(value, index, values) {
                        return value.toLocaleString() + ' DH';
                    }
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }],
        },
        legend: {
            display: false
        },
        tooltips: {
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
            callbacks: {
                label: function(tooltipItem, chart) {
                    var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                    return datasetLabel + ': ' + tooltipItem.yLabel.toLocaleString() + ' DH';
                }
            }
        },
    }
});

// Fonctions utilitaires
function exportData(type, format) {
    // Logique d'export
    console.log('Export:', type, format);
    // Ici vous pouvez ajouter la logique d'export
}

function changePeriode(periode) {
    // Logique de changement de période
    console.log('Changement période:', periode);
    // Ici vous pouvez ajouter la logique de rechargement des données
}

function exportChart(chartType) {
    // Logique d'export de graphique
    console.log('Export chart:', chartType);
}

// Auto-refresh des données (optionnel)
setInterval(function() {
    // Rechargement automatique des données toutes les 5 minutes
    // window.location.reload();
}, 300000);

</script>
@endsection