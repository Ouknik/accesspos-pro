@extends('layouts.sb-admin')

@section('title', 'Modes de Paiement - AccessPOS')

@section('page-heading')
<h1 class="h3 mb-2 text-gray-800">
    <i class="fas fa-credit-card mr-2"></i>Modes de Paiement
</h1>
<p class="mb-4">Analyse détaillée des moyens de paiement</p>
@endsection

@section('styles')
<style>
    .stat-card {
        border-left: 4px solid;
        border-radius: 0.35rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .stat-card.especes {
        border-left-color: #1cc88a;
    }
    
    .stat-card.carte {
        border-left-color: #4e73df;
    }
    
    .stat-card.cheque {
        border-left-color: #f6c23e;
    }
    
    .stat-card.autre {
        border-left-color: #6c757d;
    }
    
    .payment-method {
        background: white;
        border: 2px solid #e3e6f0;
        border-radius: 0.35rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .payment-method:hover {
        border-color: #4e73df;
        transform: translateY(-2px);
        box-shadow: 0 0.3rem 3rem 0 rgba(58, 59, 69, 0.25);
    }
    
    .payment-method.especes {
        border-color: #1cc88a;
        background: linear-gradient(135deg, #f8fff9 0%, #f0fdf4 100%);
    }
    
    .payment-method.carte {
        border-color: #4e73df;
        background: linear-gradient(135deg, #f8faff 0%, #eff6ff 100%);
    }
    
    .payment-method.cheque {
        border-color: #f6c23e;
        background: linear-gradient(135deg, #fffef8 0%, #fffbeb 100%);
    }
    
    .payment-method.autre {
        border-color: #6c757d;
        background: linear-gradient(135deg, #fafbfc 0%, #f9fafb 100%);
    }
    
    .chart-container {
        position: relative;
        height: 400px;
        margin: 1rem 0;
    }
    
    .progress-custom {
        height: 8px;
        border-radius: 10px;
        background-color: #eaecf4;
    }
    
    .progress-bar-custom {
        border-radius: 10px;
    }
    
    .animated-card {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.5s ease forwards;
    }
    
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .btn-back {
        background: #6c757d;
        color: white;
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 0.35rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .btn-back:hover {
        background: #5a6268;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
        text-decoration: none;
    }
</style>
@endsection

@section('content')
<!-- Navigation Actions -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ url('/admin/tableau-de-bord-moderne') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i>
        Retour au Tableau de Bord
    </a>
    <button class="btn btn-primary" onclick="window.print()">
        <i class="fas fa-print mr-1"></i>
        Imprimer
    </button>
</div>

<!-- KPIs Paiements -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card especes h-100 py-2 animated-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            <i class="fas fa-money-bill-wave mr-1"></i>Total Espèces
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format(rand(15000, 25000), 2) }} DH
                        </div>
                        <div class="text-xs text-muted">{{ rand(40, 60) }}% des paiements</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card carte h-100 py-2 animated-card" style="animation-delay: 0.1s;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            <i class="fas fa-credit-card mr-1"></i>Total Cartes
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format(rand(8000, 15000), 2) }} DH
                        </div>
                        <div class="text-xs text-muted">{{ rand(25, 40) }}% des paiements</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-credit-card fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card cheque h-100 py-2 animated-card" style="animation-delay: 0.2s;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            <i class="fas fa-file-invoice mr-1"></i>Total Chèques
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format(rand(3000, 8000), 2) }} DH
                        </div>
                        <div class="text-xs text-muted">{{ rand(10, 20) }}% des paiements</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-invoice fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card autre h-100 py-2 animated-card" style="animation-delay: 0.3s;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            <i class="fas fa-mobile-alt mr-1"></i>Autres
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format(rand(500, 2000), 2) }} DH
                        </div>
                        <div class="text-xs text-muted">{{ rand(2, 8) }}% des paiements</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-mobile-alt fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques Répartition -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card shadow mb-4 animated-card" style="animation-delay: 0.4s;">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie mr-2"></i>Répartition par Montant
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="paiementChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card shadow mb-4 animated-card" style="animation-delay: 0.5s;">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar mr-2"></i>Évolution Hebdomadaire
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="evolutionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Analyse Détaillée par Mode -->
<div class="card shadow mb-4 animated-card" style="animation-delay: 0.6s;">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list mr-2"></i>Analyse Détaillée par Mode de Paiement
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            @php
                $modes = [
                    [
                        'nom' => 'Espèces',
                        'icon' => 'money-bill-wave',
                        'couleur' => 'success',
                        'class' => 'especes',
                        'montant' => 18750.50,
                        'transactions' => 156,
                        'pourcentage' => 52.3,
                        'ticket_moyen' => 120.20
                    ],
                    [
                        'nom' => 'Carte Bancaire',
                        'icon' => 'credit-card',
                        'couleur' => 'primary',
                        'class' => 'carte',
                        'montant' => 12380.75,
                        'transactions' => 89,
                        'pourcentage' => 34.5,
                        'ticket_moyen' => 139.10
                    ],
                    [
                        'nom' => 'Chèque',
                        'icon' => 'file-invoice',
                        'couleur' => 'warning',
                        'class' => 'cheque',
                        'montant' => 4250.00,
                        'transactions' => 23,
                        'pourcentage' => 11.8,
                        'ticket_moyen' => 184.78
                    ],
                    [
                        'nom' => 'Autres (Mobile, Crédit)',
                        'icon' => 'mobile-alt',
                        'couleur' => 'info',
                        'class' => 'autre',
                        'montant' => 520.25,
                        'transactions' => 7,
                        'pourcentage' => 1.4,
                        'ticket_moyen' => 74.32
                    ]
                ];
            @endphp
            
            @foreach($modes as $index => $mode)
            <div class="col-lg-6 mb-3">
                <div class="payment-method {{ $mode['class'] }} animated-card" style="animation-delay: {{ 0.7 + ($index * 0.1) }}s;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-{{ $mode['icon'] }} text-{{ $mode['couleur'] }} mr-2"></i>
                            {{ $mode['nom'] }}
                        </h6>
                        <span class="badge badge-{{ $mode['couleur'] }}">
                            {{ $mode['pourcentage'] }}%
                        </span>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-gray-600 text-uppercase mb-1">
                                Montant Total
                            </div>
                            <div class="font-weight-bold text-gray-800">
                                {{ number_format($mode['montant'], 2) }} DH
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-gray-600 text-uppercase mb-1">
                                Transactions
                            </div>
                            <div class="font-weight-bold text-gray-800">
                                {{ $mode['transactions'] }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="text-xs font-weight-bold text-gray-600 text-uppercase mb-1">
                            Ticket Moyen
                        </div>
                        <div class="font-weight-bold text-gray-800">
                            {{ number_format($mode['ticket_moyen'], 2) }} DH
                        </div>
                    </div>
                    
                    <div class="progress progress-custom mb-0">
                        <div class="progress-bar progress-bar-custom bg-{{ $mode['couleur'] }}" 
                             role="progressbar" 
                             style="width: {{ $mode['pourcentage'] }}%"
                             aria-valuenow="{{ $mode['pourcentage'] }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Tendances et Recommandations -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100 animated-card" style="animation-delay: 1.1s;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-lightbulb mr-2"></i>Recommandations
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-check-circle text-success mr-2 mt-1"></i>
                            <span>Promouvoir les paiements par carte pour réduire la gestion d'espèces</span>
                        </div>
                    </li>
                    <li class="mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-check-circle text-success mr-2 mt-1"></i>
                            <span>Proposer des solutions de paiement mobile</span>
                        </div>
                    </li>
                    <li class="mb-0">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-check-circle text-success mr-2 mt-1"></i>
                            <span>Analyser les frais bancaires vs avantages</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100 animated-card" style="animation-delay: 1.2s;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-chart-line mr-2"></i>Tendances
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-arrow-up text-success mr-2 mt-1"></i>
                            <span>Paiements par carte en hausse de 15%</span>
                        </div>
                    </li>
                    <li class="mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-arrow-down text-danger mr-2 mt-1"></i>
                            <span>Paiements en espèces en baisse de 8%</span>
                        </div>
                    </li>
                    <li class="mb-0">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-minus text-gray-600 mr-2 mt-1"></i>
                            <span>Paiements par chèque stables</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration Chart.js avec thème SB Admin
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';
    
    // Graphique en camembert
    const ctx1 = document.getElementById('paiementChart').getContext('2d');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Espèces', 'Carte Bancaire', 'Chèque', 'Autres'],
            datasets: [{
                data: [52.3, 34.5, 11.8, 1.4],
                backgroundColor: [
                    '#1cc88a',
                    '#4e73df',
                    '#f6c23e',
                    '#6c757d'
                ],
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverBorderColor: "rgba(234, 236, 244, 1)"
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        fontColor: '#858796',
                        usePointStyle: true,
                        padding: 20
                    }
                }
            },
            cutout: '80%'
        }
    });
    
    // Graphique d'évolution
    const ctx2 = document.getElementById('evolutionChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
            datasets: [
                {
                    label: 'Espèces',
                    data: [2800, 3200, 2950, 3400, 3800, 4200, 3900],
                    backgroundColor: '#1cc88a',
                    borderColor: '#1cc88a',
                    borderWidth: 1
                },
                {
                    label: 'Carte',
                    data: [1800, 2100, 1950, 2200, 2400, 2600, 2450],
                    backgroundColor: '#4e73df',
                    borderColor: '#4e73df',
                    borderWidth: 1
                },
                {
                    label: 'Chèque',
                    data: [600, 700, 550, 800, 750, 900, 650],
                    backgroundColor: '#f6c23e',
                    borderColor: '#f6c23e',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: true,
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        fontColor: '#858796'
                    }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    gridLines: {
                        color: '#e3e6f0',
                        zeroLineColor: '#e3e6f0',
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    },
                    ticks: {
                        fontColor: '#858796',
                        callback: function(value) {
                            return value + ' DH';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        fontColor: '#858796',
                        usePointStyle: true,
                        padding: 20
                    }
                }
            }
        }
    });
});
</script>
@endsection
