@extends('layouts.sb-admin')

@section('title', 'Détails du Chiffre d\'Affaires - AccessPos Pro')

@section('page-heading')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line"></i>
            Détails du Chiffre d'Affaires
        </h1>
        <p class="mb-0 text-muted">Analyse détaillée des performances financières</p>
    </div>
    <div class="btn-group">
        <a href="#" onclick="exportReport('pdf')" class="btn btn-danger btn-sm">
            <i class="fas fa-file-pdf"></i>
            PDF
        </a>
        <a href="#" onclick="exportReport('excel')" class="btn btn-success btn-sm">
            <i class="fas fa-file-excel"></i>
            Excel
        </a>
        <button type="button" class="btn btn-primary btn-sm" onclick="refreshData()">
            <i class="fas fa-sync"></i>
            Actualiser
        </button>
    </div>
</div>
@endsection

@section('content')

{{-- Filtres de Période --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter"></i>
            Filtres de Période
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ url()->current() }}" id="filterForm">
            <div class="row">
                <div class="col-md-3">
                    <label for="periode" class="form-label">Période</label>
                    <select class="form-control" id="periode" name="periode" onchange="toggleCustomDates()">
                        <option value="jour" {{ request('periode', 'jour') == 'jour' ? 'selected' : '' }}>Aujourd'hui</option>
                        <option value="semaine" {{ request('periode') == 'semaine' ? 'selected' : '' }}>Cette Semaine</option>
                        <option value="mois" {{ request('periode') == 'mois' ? 'selected' : '' }}>Ce Mois</option>
                        <option value="trimestre" {{ request('periode') == 'trimestre' ? 'selected' : '' }}>Ce Trimestre</option>
                        <option value="annee" {{ request('periode') == 'annee' ? 'selected' : '' }}>Cette Année</option>
                        <option value="personnalise" {{ request('periode') == 'personnalise' ? 'selected' : '' }}>Période Personnalisée</option>
                    </select>
                </div>
                <div class="col-md-3" id="dateDebut" style="display: {{ request('periode') == 'personnalise' ? 'block' : 'none' }};">
                    <label for="date_debut" class="form-label">Date de Début</label>
                    <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ request('date_debut') }}">
                </div>
                <div class="col-md-3" id="dateFin" style="display: {{ request('periode') == 'personnalise' ? 'block' : 'none' }};">
                    <label for="date_fin" class="form-label">Date de Fin</label>
                    <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ request('date_fin') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Filtrer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- KPIs Principaux --}}
<div class="row">
    
    {{-- Chiffre d'Affaires Total --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Chiffre d'Affaires Total
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($kpis['ca_total'] ?? 0, 2) }} DH
                        </div>
                        @if(isset($kpis['evolution_ca']))
                            <div class="text-xs mt-1 {{ $kpis['evolution_ca'] >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-arrow-{{ $kpis['evolution_ca'] >= 0 ? 'up' : 'down' }} mr-1"></i>
                                {{ $kpis['evolution_ca'] >= 0 ? '+' : '' }}{{ number_format($kpis['evolution_ca'], 1) }}% vs période précédente
                            </div>
                        @endif
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
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
                            {{ number_format($kpis['nb_ventes'] ?? 0) }}
                        </div>
                        @if(isset($kpis['evolution_ventes']))
                            <div class="text-xs mt-1 {{ $kpis['evolution_ventes'] >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-arrow-{{ $kpis['evolution_ventes'] >= 0 ? 'up' : 'down' }} mr-1"></i>
                                {{ $kpis['evolution_ventes'] >= 0 ? '+' : '' }}{{ number_format($kpis['evolution_ventes'], 1) }}%
                            </div>
                        @endif
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
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Ticket Moyen
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($kpis['ticket_moyen'] ?? 0, 2) }} DH
                        </div>
                        @if(isset($kpis['evolution_ticket']))
                            <div class="text-xs mt-1 {{ $kpis['evolution_ticket'] >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-arrow-{{ $kpis['evolution_ticket'] >= 0 ? 'up' : 'down' }} mr-1"></i>
                                {{ $kpis['evolution_ticket'] >= 0 ? '+' : '' }}{{ number_format($kpis['evolution_ticket'], 1) }}%
                            </div>
                        @endif
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-receipt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Marge Brute --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Marge Brute
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($kpis['marge_brute'] ?? 0, 2) }} DH
                        </div>
                        @if(isset($kpis['taux_marge']))
                            <div class="text-xs mt-1 text-muted">
                                Taux: {{ number_format($kpis['taux_marge'], 1) }}%
                            </div>
                        @endif
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Graphiques et Analyses --}}
<div class="row">

    {{-- Évolution du CA --}}
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-area mr-2"></i>
                    Évolution du Chiffre d'Affaires
                </h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <div class="dropdown-header">Options:</div>
                        <a class="dropdown-item" href="#" onclick="changeChartType('line')">Vue Linéaire</a>
                        <a class="dropdown-item" href="#" onclick="changeChartType('bar')">Vue Barres</a>
                        <a class="dropdown-item" href="#" onclick="changeChartType('area')">Vue Aire</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" onclick="exportChart('ca_evolution')">
                            <i class="fas fa-download mr-1"></i>
                            Exporter
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="chartEvolutionCA" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Répartition par Méthode de Paiement --}}
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-credit-card mr-2"></i>
                    Répartition par Paiement
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="chartPaiements"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    @if(isset($repartitionPaiements))
                        @foreach($repartitionPaiements as $mode => $montant)
                            <span class="mr-2">
                                <i class="fas fa-circle {{ $loop->index == 0 ? 'text-primary' : ($loop->index == 1 ? 'text-success' : 'text-info') }}"></i>
                                {{ ucfirst($mode) }}: {{ number_format($montant, 2) }} DH
                            </span>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Analyse par Tranches Horaires --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-clock mr-2"></i>
            Analyse par Tranches Horaires
        </h6>
    </div>
    <div class="card-body">
        <div class="chart-bar">
            <canvas id="chartTrancheHoraire" width="100%" height="50"></canvas>
        </div>
    </div>
</div>

{{-- Top Produits et Catégories --}}
<div class="row">

    {{-- Top Produits --}}
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-trophy mr-2"></i>
                    Top 10 Produits (CA)
                </h6>
            </div>
            <div class="card-body">
                @if(isset($topProduits) && count($topProduits) > 0)
                    @foreach($topProduits->take(10) as $index => $produit)
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
                                    {{ number_format($produit['quantite'] ?? 0) }} unités vendues
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-weight-bold text-success">
                                    {{ number_format($produit['ca'] ?? 0, 2) }} DH
                                </div>
                                @if(isset($produit['pourcentage']))
                                    <div class="text-xs text-muted">
                                        {{ number_format($produit['pourcentage'], 1) }}%
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if($index < 9 && $index < count($topProduits) - 1)
                            <hr class="my-2">
                        @endif
                    @endforeach
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-box fa-3x mb-3"></i>
                        <p>Aucune vente pour cette période</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Top Catégories --}}
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tags mr-2"></i>
                    Top Catégories (CA)
                </h6>
            </div>
            <div class="card-body">
                @if(isset($topCategories) && count($topCategories) > 0)
                    @foreach($topCategories as $index => $categorie)
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
                                    {{ $categorie['nom'] ?? 'Catégorie inconnue' }}
                                </div>
                                <div class="text-xs text-muted">
                                    {{ number_format($categorie['nb_produits'] ?? 0) }} produits
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-weight-bold text-primary">
                                    {{ number_format($categorie['ca'] ?? 0, 2) }} DH
                                </div>
                                @if(isset($categorie['pourcentage']))
                                    <div class="text-xs text-muted">
                                        {{ number_format($categorie['pourcentage'], 1) }}%
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if($index < count($topCategories) - 1)
                            <hr class="my-2">
                        @endif
                    @endforeach
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-tags fa-3x mb-3"></i>
                        <p>Aucune catégorie trouvée</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- Tableau Détaillé des Ventes --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table mr-2"></i>
            Détail des Ventes
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>N° Facture</th>
                        <th>Client</th>
                        <th>Articles</th>
                        <th>Montant HT</th>
                        <th>TVA</th>
                        <th>Montant TTC</th>
                        <th>Mode Paiement</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($ventesDetails) && count($ventesDetails) > 0)
                        @foreach($ventesDetails as $vente)
                            <tr>
                                <td>{{ $vente['date'] ? $vente['date']->format('d/m/Y H:i') : 'N/A' }}</td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $vente['numero'] ?? 'N/A' }}</code>
                                </td>
                                <td>{{ $vente['client'] ?? 'Client anonyme' }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $vente['nb_articles'] ?? 0 }} article(s)</span>
                                </td>
                                <td class="text-right">{{ number_format($vente['montant_ht'] ?? 0, 2) }} DH</td>
                                <td class="text-right">{{ number_format($vente['tva'] ?? 0, 2) }} DH</td>
                                <td class="text-right font-weight-bold">{{ number_format($vente['montant_ttc'] ?? 0, 2) }} DH</td>
                                <td>
                                    <span class="badge badge-secondary">{{ $vente['mode_paiement'] ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="#" class="btn btn-info btn-sm" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-primary btn-sm" title="Imprimer">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-receipt fa-3x mb-3"></i>
                                    <p class="mb-0">Aucune vente trouvée pour cette période</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Configuration des graphiques
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Graphique d'évolution du CA
var ctx1 = document.getElementById("chartEvolutionCA");
var chartEvolutionCA = new Chart(ctx1, {
    type: 'line',
    data: {
        labels: {!! json_encode($evolutionCA['labels'] ?? []) !!},
        datasets: [{
            label: "Chiffre d'Affaires (DH)",
            lineTension: 0.3,
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: {!! json_encode($evolutionCA['data'] ?? []) !!},
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
                    maxTicksLimit: 7
                }
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
                    return 'CA: ' + tooltipItem.yLabel.toLocaleString() + ' DH';
                }
            }
        }
    }
});

// Graphique répartition paiements
var ctx2 = document.getElementById("chartPaiements");
var chartPaiements = new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($repartitionPaiements['labels'] ?? []) !!},
        datasets: [{
            data: {!! json_encode($repartitionPaiements['data'] ?? []) !!},
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
            display: false
        },
        cutoutPercentage: 80,
    },
});

// Graphique tranches horaires
var ctx3 = document.getElementById("chartTrancheHoraire");
var chartTrancheHoraire = new Chart(ctx3, {
    type: 'bar',
    data: {
        labels: {!! json_encode($tranchesHoraires['labels'] ?? []) !!},
        datasets: [{
            label: "CA par Heure (DH)",
            backgroundColor: "#4e73df",
            hoverBackgroundColor: "#2e59d9",
            borderColor: "#4e73df",
            data: {!! json_encode($tranchesHoraires['data'] ?? []) !!},
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
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
        },
    }
});

// DataTable
$(document).ready(function() {
    $('#dataTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
        },
        "pageLength": 25,
        "order": [[ 0, "desc" ]],
        "columnDefs": [
            { "orderable": false, "targets": [8] }
        ]
    });
});

// Fonctions utilitaires
function toggleCustomDates() {
    var periode = document.getElementById('periode').value;
    var dateDebut = document.getElementById('dateDebut');
    var dateFin = document.getElementById('dateFin');
    
    if (periode === 'personnalise') {
        dateDebut.style.display = 'block';
        dateFin.style.display = 'block';
    } else {
        dateDebut.style.display = 'none';
        dateFin.style.display = 'none';
    }
}

function exportReport(format) {
    console.log('Export report in ' + format);
    // Logique d'export
}

function exportChart(chartName) {
    console.log('Export chart: ' + chartName);
    // Logique d'export de graphique
}

function changeChartType(type) {
    console.log('Change chart type to: ' + type);
    // Logique de changement de type de graphique
}

function refreshData() {
    window.location.reload();
}
</script>
@endsection
