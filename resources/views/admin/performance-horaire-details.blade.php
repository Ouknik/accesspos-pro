<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Horaire - AccessPOS</title>
    
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
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
            border-left: 4px solid #f59e0b;
        }
        
        .time-slot {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        
        .time-slot:hover {
            border-color: #f59e0b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .time-slot.peak {
            border-color: #dc2626;
            background: #fef2f2;
        }
        
        .time-slot.high {
            border-color: #f59e0b;
            background: #fffbeb;
        }
        
        .time-slot.normal {
            border-color: #10b981;
            background: #f0fdf4;
        }
        
        .time-slot.low {
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
    </style>
</head>
<body>
    <div class="details-container">
        <div class="details-header">
            <h1><i class="fas fa-clock me-2"></i>Performance Horaire</h1>
            <p class="mb-0">Analyse de l'activité par tranches horaires</p>
        </div>
        
        <div class="details-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ url('/admin/tableau-de-bord-moderne') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Retour au Tableau de Bord
                </a>
                <button class="btn btn-warning" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>
                    Imprimer
                </button>
            </div>
            
            <!-- KPIs Performance -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5><i class="fas fa-fire text-danger me-2"></i>Heure de Pointe</h5>
                        <h3 class="text-danger">19h-20h</h3>
                        <small class="text-muted">{{ rand(45, 80) }} transactions/h</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5><i class="fas fa-chart-line text-warning me-2"></i>Pic Secondaire</h5>
                        <h3 class="text-warning">12h-13h</h3>
                        <small class="text-muted">{{ rand(30, 55) }} transactions/h</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5><i class="fas fa-moon text-info me-2"></i>Heure Calme</h5>
                        <h3 class="text-info">15h-16h</h3>
                        <small class="text-muted">{{ rand(5, 15) }} transactions/h</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5><i class="fas fa-percentage text-success me-2"></i>Taux Occupation</h5>
                        <h3 class="text-success">{{ rand(65, 85) }}%</h3>
                        <small class="text-muted">Moyenne journalière</small>
                    </div>
                </div>
            </div>
            
            <!-- Graphique Performance Horaire -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Évolution du Trafic par Heure</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Analyse Détaillée par Tranche Horaire -->
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Analyse par Tranche Horaire</h5>
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
                        
                        @foreach($tranches as $tranche)
                        <div class="col-md-6 col-lg-4">
                            <div class="time-slot {{ $tranche['niveau'] }}">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0"><strong>{{ $tranche['heure'] }}</strong></h6>
                                    <span class="badge bg-{{ $tranche['niveau'] === 'peak' ? 'danger' : ($tranche['niveau'] === 'high' ? 'warning' : ($tranche['niveau'] === 'normal' ? 'success' : 'secondary')) }}">
                                        {{ $tranche['niveau'] === 'peak' ? 'POINTE' : ($tranche['niveau'] === 'high' ? 'ÉLEVÉ' : ($tranche['niveau'] === 'normal' ? 'NORMAL' : 'FAIBLE')) }}
                                    </span>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Transactions</small>
                                        <div><strong>{{ $tranche['transactions'] }}</strong></div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Chiffre d'Affaires</small>
                                        <div><strong>{{ number_format($tranche['ca'], 2) }} DH</strong></div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        Ticket moyen: <strong>{{ number_format($tranche['ca'] / $tranche['transactions'], 2) }} DH</strong>
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Graphique de performance horaire
        document.addEventListener('DOMContentLoaded', function() {
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
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#f59e0b',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8
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
                            borderColor: '#f59e0b',
                            borderWidth: 1,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return 'Transactions: ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                padding: 10
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                padding: 10
                            }
                        }
                    }
                }
            });
            
            // Animation des cartes
            const cards = document.querySelectorAll('.stat-card, .time-slot, .card');
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
