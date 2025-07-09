<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modes de Paiement - AccessPOS</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .details-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .details-header {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .details-content {
            padding: 2rem;
        }
        
        .stat-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #2563eb;
        }
        
        .payment-method {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        
        .payment-method:hover {
            border-color: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .payment-method.especes {
            border-color: #059669;
            background: #f0fdf4;
        }
        
        .payment-method.carte {
            border-color: #2563eb;
            background: #eff6ff;
        }
        
        .payment-method.cheque {
            border-color: #f59e0b;
            background: #fffbeb;
        }
        
        .payment-method.autre {
            border-color: #6b7280;
            background: #f9fafb;
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            background: #5a6268;
            color: white;
            transform: translateY(-2px);
        }
        
        .chart-container {
            position: relative;
            height: 400px;
            margin: 2rem 0;
        }
        
        .progress-bar-custom {
            border-radius: 10px;
            height: 8px;
        }
    </style>
</head>
<body>
    <div class="details-container">
        <div class="details-header">
            <h1><i class="fas fa-credit-card me-2"></i>Modes de Paiement</h1>
            <p class="mb-0">Analyse détaillée des moyens de paiement</p>
        </div>
        
        <div class="details-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ url('/admin/tableau-de-bord-moderne') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Retour au Tableau de Bord
                </a>
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>
                    Imprimer
                </button>
            </div>
            
            <!-- KPIs Paiements -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5><i class="fas fa-money-bill text-success me-2"></i>Total Espèces</h5>
                        <h3 class="text-success">{{ number_format(rand(15000, 25000), 2) }} DH</h3>
                        <small class="text-muted">{{ rand(40, 60) }}% des paiements</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5><i class="fas fa-credit-card text-primary me-2"></i>Total Cartes</h5>
                        <h3 class="text-primary">{{ number_format(rand(8000, 15000), 2) }} DH</h3>
                        <small class="text-muted">{{ rand(25, 40) }}% des paiements</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5><i class="fas fa-file-invoice text-warning me-2"></i>Total Chèques</h5>
                        <h3 class="text-warning">{{ number_format(rand(3000, 8000), 2) }} DH</h3>
                        <small class="text-muted">{{ rand(10, 20) }}% des paiements</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5><i class="fas fa-mobile-alt text-info me-2"></i>Autres</h5>
                        <h3 class="text-info">{{ number_format(rand(500, 2000), 2) }} DH</h3>
                        <small class="text-muted">{{ rand(2, 8) }}% des paiements</small>
                    </div>
                </div>
            </div>
            
            <!-- Graphique Répartition -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Répartition par Montant</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="paiementChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Évolution Hebdomadaire</h5>
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
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Analyse Détaillée par Mode de Paiement</h5>
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
                        
                        @foreach($modes as $mode)
                        <div class="col-md-6">
                            <div class="payment-method {{ $mode['class'] }}">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">
                                        <i class="fas fa-{{ $mode['icon'] }} text-{{ $mode['couleur'] }} me-2"></i>
                                        <strong>{{ $mode['nom'] }}</strong>
                                    </h6>
                                    <span class="badge bg-{{ $mode['couleur'] }}">
                                        {{ $mode['pourcentage'] }}%
                                    </span>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">Montant Total</small>
                                        <div><strong>{{ number_format($mode['montant'], 2) }} DH</strong></div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Transactions</small>
                                        <div><strong>{{ $mode['transactions'] }}</strong></div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <small class="text-muted">Ticket Moyen</small>
                                    <div><strong>{{ number_format($mode['ticket_moyen'], 2) }} DH</strong></div>
                                </div>
                                
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $mode['couleur'] }}" 
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
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Recommandations</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Promouvoir les paiements par carte pour réduire la gestion d'espèces
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Proposer des solutions de paiement mobile
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Analyser les frais bancaires vs avantages
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-warning text-white">
                            <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Tendances</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-arrow-up text-success me-2"></i>
                                    Paiements par carte en hausse de 15%
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-arrow-down text-danger me-2"></i>
                                    Paiements en espèces en baisse de 8%
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-minus text-muted me-2"></i>
                                    Paiements par chèque stables
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Graphique en camembert
            const ctx1 = document.getElementById('paiementChart').getContext('2d');
            new Chart(ctx1, {
                type: 'doughnut',
                data: {
                    labels: ['Espèces', 'Carte Bancaire', 'Chèque', 'Autres'],
                    datasets: [{
                        data: [52.3, 34.5, 11.8, 1.4],
                        backgroundColor: [
                            '#059669',
                            '#2563eb',
                            '#f59e0b',
                            '#6b7280'
                        ],
                        borderWidth: 3,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
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
                            backgroundColor: '#059669'
                        },
                        {
                            label: 'Carte',
                            data: [1800, 2100, 1950, 2200, 2400, 2600, 2450],
                            backgroundColor: '#2563eb'
                        },
                        {
                            label: 'Chèque',
                            data: [600, 700, 550, 800, 750, 900, 650],
                            backgroundColor: '#f59e0b'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Animation des cartes
            const cards = document.querySelectorAll('.stat-card, .payment-method, .card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 50);
            });
        });
    </script>
</body>
</html>
