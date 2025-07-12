@extends('layouts.sb-admin')

@section('title', 'Performance Horaire - AccessPOS')

@section('page-heading')
<h1 class="h3 mb-2 text-gray-800">
    <i class="fas fa-clock mr-2"></i>Performance Horaire
</h1>
<p class="mb-4">Analyse de l'activité par tranches horaires</p>
@endsection

@section('styles')
<style>
    .stat-card {
        border-left: 4px solid;
        border-radius: 0.35rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .stat-card.peak {
        border-left-color: #e74a3b;
    }
    
    .stat-card.high {
        border-left-color: #f6c23e;
    }
    
    .stat-card.normal {
        border-left-color: #1cc88a;
    }
    
    .stat-card.low {
        border-left-color: #36b9cc;
    }
    
    .time-slot {
        background: white;
        border: 2px solid #e3e6f0;
        border-radius: 0.35rem;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .time-slot:hover {
        border-color: #f6c23e;
        transform: translateY(-2px);
        box-shadow: 0 0.3rem 3rem 0 rgba(58, 59, 69, 0.25);
    }
    
    .time-slot.peak {
        border-color: #e74a3b;
        background: linear-gradient(135deg, #fff5f5 0%, #fef2f2 100%);
    }
    
    .time-slot.high {
        border-color: #f6c23e;
        background: linear-gradient(135deg, #fffef5 0%, #fffbeb 100%);
    }
    
    .time-slot.normal {
        border-color: #1cc88a;
        background: linear-gradient(135deg, #f8fff9 0%, #f0fdf4 100%);
    }
    
    .time-slot.low {
        border-color: #36b9cc;
        background: linear-gradient(135deg, #f8fcff 0%, #f0f9ff 100%);
    }
    
    .chart-container {
        position: relative;
        height: 400px;
        margin: 1rem 0;
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
    
    .level-badge {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        text-transform: uppercase;
    }
    
    .level-peak {
        background-color: #e74a3b;
        color: white;
    }
    
    .level-high {
        background-color: #f6c23e;
        color: white;
    }
    
    .level-normal {
        background-color: #1cc88a;
        color: white;
    }
    
    .level-low {
        background-color: #36b9cc;
        color: white;
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
    <button class="btn btn-warning" onclick="window.print()">
        <i class="fas fa-print mr-1"></i>
        Imprimer
    </button>
</div>

<!-- KPIs Performance -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card peak h-100 py-2 animated-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            <i class="fas fa-fire mr-1"></i>Heure de Pointe
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            19h-20h
                        </div>
                        <div class="text-xs text-muted">{{ rand(45, 80) }} transactions/h</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-fire fa-2x text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card high h-100 py-2 animated-card" style="animation-delay: 0.1s;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            <i class="fas fa-chart-line mr-1"></i>Pic Secondaire
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            12h-13h
                        </div>
                        <div class="text-xs text-muted">{{ rand(30, 55) }} transactions/h</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card low h-100 py-2 animated-card" style="animation-delay: 0.2s;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            <i class="fas fa-moon mr-1"></i>Heure Calme
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            15h-16h
                        </div>
                        <div class="text-xs text-muted">{{ rand(5, 15) }} transactions/h</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-moon fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card normal h-100 py-2 animated-card" style="animation-delay: 0.3s;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            <i class="fas fa-percentage mr-1"></i>Taux Occupation
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ rand(65, 85) }}%
                        </div>
                        <div class="text-xs text-muted">Moyenne journalière</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphique Performance Horaire -->
<div class="card shadow mb-4 animated-card" style="animation-delay: 0.4s;">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-warning">
            <i class="fas fa-chart-bar mr-2"></i>Évolution du Trafic par Heure
        </h6>
    </div>
    <div class="card-body">
        <div class="chart-container">
            <canvas id="performanceChart"></canvas>
        </div>
    </div>
</div>

<!-- Analyse Détaillée par Tranche Horaire -->
<div class="card shadow mb-4 animated-card" style="animation-delay: 0.5s;">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-warning">
            <i class="fas fa-clock mr-2"></i>Analyse par Tranche Horaire
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            @php
                $tranches = [
                    ['heure' => '08h-09h', 'transactions' => 12, 'ca' => 1450.75, 'niveau' => 'low'],
                    ['heure' => '09h-10h', 'transactions' => 18, 'ca' => 2180.50, 'niveau' => 'normal'],
                    ['heure' => '10h-11h', 'transactions' => 25, 'ca' => 2875.25, 'niveau' => 'normal'],
                    ['heure' => '11h-12h', 'transactions' => 32, 'ca' => 3650.00, 'niveau' => 'high'],
                    ['heure' => '12h-13h', 'transactions' => 45, 'ca' => 5220.75, 'niveau' => 'high'],
                    ['heure' => '13h-14h', 'transactions' => 38, 'ca' => 4380.50, 'niveau' => 'high'],
                    ['heure' => '14h-15h', 'transactions' => 22, 'ca' => 2540.25, 'niveau' => 'normal'],
                    ['heure' => '15h-16h', 'transactions' => 15, 'ca' => 1725.00, 'niveau' => 'low'],
                    ['heure' => '16h-17h', 'transactions' => 28, 'ca' => 3220.75, 'niveau' => 'normal'],
                    ['heure' => '17h-18h', 'transactions' => 35, 'ca' => 4050.50, 'niveau' => 'high'],
                    ['heure' => '18h-19h', 'transactions' => 42, 'ca' => 4880.25, 'niveau' => 'high'],
                    ['heure' => '19h-20h', 'transactions' => 58, 'ca' => 6720.00, 'niveau' => 'peak'],
                    ['heure' => '20h-21h', 'transactions' => 52, 'ca' => 6030.75, 'niveau' => 'peak'],
                    ['heure' => '21h-22h', 'transactions' => 33, 'ca' => 3825.50, 'niveau' => 'high'],
                    ['heure' => '22h-23h', 'transactions' => 19, 'ca' => 2205.25, 'niveau' => 'normal'],
                    ['heure' => '23h-00h', 'transactions' => 8, 'ca' => 920.00, 'niveau' => 'low']
                ];
            @endphp
            
            @foreach($tranches as $index => $tranche)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                <div class="time-slot {{ $tranche['niveau'] }} animated-card" style="animation-delay: {{ 0.6 + ($index * 0.05) }}s;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 font-weight-bold text-gray-800">{{ $tranche['heure'] }}</h6>
                        <span class="level-badge level-{{ $tranche['niveau'] }}">
                            @if($tranche['niveau'] === 'peak')
                                POINTE
                            @elseif($tranche['niveau'] === 'high')
                                ÉLEVÉ
                            @elseif($tranche['niveau'] === 'normal')
                                NORMAL
                            @else
                                FAIBLE
                            @endif
                        </span>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-gray-600 text-uppercase mb-1">
                                Transactions
                            </div>
                            <div class="font-weight-bold text-gray-800">
                                {{ $tranche['transactions'] }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-gray-600 text-uppercase mb-1">
                                Chiffre d'Affaires
                            </div>
                            <div class="font-weight-bold text-gray-800">
                                {{ number_format($tranche['ca'], 2) }} DH
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-xs text-muted">
                        Ticket moyen: <strong class="text-gray-800">{{ number_format($tranche['ca'] / $tranche['transactions'], 2) }} DH</strong>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Insights et Recommandations -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100 animated-card" style="animation-delay: 1.4s;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-lightbulb mr-2"></i>Insights
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-chart-line text-success mr-2 mt-1"></i>
                            <span>Le pic principal est entre 19h-20h avec 58 transactions</span>
                        </div>
                    </li>
                    <li class="mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-clock text-warning mr-2 mt-1"></i>
                            <span>La période calme optimale pour maintenance: 15h-16h</span>
                        </div>
                    </li>
                    <li class="mb-0">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-users text-info mr-2 mt-1"></i>
                            <span>Heure de déjeuner très active: prévoir plus de personnel</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100 animated-card" style="animation-delay: 1.5s;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-check-circle mr-2"></i>Recommandations
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-plus-circle text-success mr-2 mt-1"></i>
                            <span>Augmenter le personnel pendant les heures de pointe</span>
                        </div>
                    </li>
                    <li class="mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-percentage text-primary mr-2 mt-1"></i>
                            <span>Proposer des promotions en heures creuses</span>
                        </div>
                    </li>
                    <li class="mb-0">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-tools text-warning mr-2 mt-1"></i>
                            <span>Planifier la maintenance système après 23h</span>
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
    
    // Graphique de performance horaire
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    const performanceData = [12, 18, 25, 32, 45, 38, 22, 15, 28, 35, 42, 58, 52, 33, 19, 8];
    const heures = ['08h', '09h', '10h', '11h', '12h', '13h', '14h', '15h', '16h', '17h', '18h', '19h', '20h', '21h', '22h', '23h'];
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: heures,
            datasets: [{
                label: 'Transactions par heure',
                data: performanceData,
                borderColor: '#f6c23e',
                backgroundColor: 'rgba(246, 194, 62, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#f6c23e',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 3,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: '#f6c23e',
                pointHoverBorderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#f6c23e',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        title: function(context) {
                            return 'Heure: ' + context[0].label;
                        },
                        label: function(context) {
                            return 'Transactions: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
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
                        padding: 10,
                        stepSize: 10
                    }
                },
                x: {
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        fontColor: '#858796',
                        padding: 10
                    }
                }
            }
        }
    });
});
</script>
@endsection
