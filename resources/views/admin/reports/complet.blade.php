
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Complet - AccessPOS Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-color: #28a745;
            --info-color: #17a2b8;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }

        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .header-section {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-3px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 1rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .table th {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 15px;
        }

        .table td {
            padding: 12px 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .action-buttons {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
        }

        .fab-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: none;
            margin: 0.5rem;
            font-size: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }

        .fab-btn:hover {
            transform: scale(1.1);
        }

        .fab-pdf {
            background: var(--danger-color);
            color: white;
        }

        .fab-back {
            background: var(--primary-gradient);
            color: white;
        }

        .progress-bar {
            background: var(--primary-gradient);
        }

        @media (max-width: 768px) {
            .action-buttons {
                position: static;
                text-align: center;
                margin-top: 2rem;
            }
            
            .fab-btn {
                position: relative;
                margin: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">
                        <i class="fas fa-chart-line me-3"></i>
                        Rapport Complet des Ventes
                    </h1>
                    <p class="mb-0 fs-5">
                        <i class="fas fa-calendar me-2"></i>
                        Période: {{ $statistiques['periode'] }}
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.reports.complet.pdf') }}?date_debut={{ $dateDebut->format('Y-m-d') }}&date_fin={{ $dateFin->format('Y-m-d') }}" 
                           class="btn btn-light btn-lg me-2">
                            <i class="fas fa-file-pdf text-danger me-2"></i>
                            PDF
                        </a>
                        <a href="{{ route('admin.reports.index') }}" 
                           class="btn btn-outline-light btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>
                            Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Statistiques Principales -->
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #28a745, #20c997);">
                        <i class="fas fa-euro-sign text-white"></i>
                    </div>
                    <div class="stat-value text-success">{{ number_format($statistiques['total_ventes'], 2) }}</div>
                    <div class="stat-label">Total des Ventes (TTC)</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #17a2b8, #6f42c1);">
                        <i class="fas fa-receipt text-white"></i>
                    </div>
                    <div class="stat-value text-info">{{ number_format($statistiques['nombre_factures']) }}</div>
                    <div class="stat-label">Nombre de Factures</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #ffc107, #fd7e14);">
                        <i class="fas fa-calculator text-white"></i>
                    </div>
                    <div class="stat-value text-warning">{{ number_format($statistiques['ticket_moyen'], 2) }}</div>
                    <div class="stat-label">Ticket Moyen</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #dc3545, #e83e8c);">
                        <i class="fas fa-hand-holding-usd text-white"></i>
                    </div>
                    <div class="stat-value text-danger">{{ number_format($statistiques['total_ventes_ht'], 2) }}</div>
                    <div class="stat-label">Total HT</div>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <h4 class="mb-3">
                        <i class="fas fa-credit-card me-2"></i>
                        Répartition par Mode de Paiement
                    </h4>
                    <canvas id="paymentChart" width="400" height="200"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h4 class="mb-3">
                        <i class="fas fa-chart-bar me-2"></i>
                        Évolution des Ventes
                    </h4>
                    <canvas id="salesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Tableau des Ventes -->
        <div class="table-container">
            <h4 class="mb-3">
                <i class="fas fa-table me-2"></i>
                Détail des Ventes
                <small class="text-muted">({{ $sales->count() }} factures)</small>
            </h4>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Montant HT</th>
                            <th>Montant TTC</th>
                            <th>Mode de Paiement</th>
                            <th>Caissier</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                        <tr>
                            <td>
                                <span class="badge bg-primary">{{ $sale->FCTV_REF }}</span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($sale->fctv_date)->format('d/m/Y H:i') }}</td>
                            <td class="text-end">{{ number_format($sale->fctv_mnt_ht, 2) }} €</td>
                            <td class="text-end">
                                <strong>{{ number_format($sale->fctv_mnt_ttc, 2) }} €</strong>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $sale->fctv_modepaiement }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $sale->fctv_utilisateur }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Analyses Supplémentaires -->
        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <h4 class="mb-3">
                        <i class="fas fa-users me-2"></i>
                        Performance par Caissier
                    </h4>
                    <canvas id="cashierChart" width="400" height="200"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h4 class="mb-3">
                        <i class="fas fa-chart-line me-2"></i>
                        Ventes par Jour
                    </h4>
                    <canvas id="dailyChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Configuration des graphiques
        const colors = {
            primary: '#667eea',
            success: '#28a745',
            info: '#17a2b8',
            warning: '#ffc107',
            danger: '#dc3545'
        };

        // Graphique des modes de paiement
        const paymentData = @json($modesPayement);
        const paymentLabels = Object.keys(paymentData);
        const paymentValues = Object.values(paymentData).map(item => item.montant);

        new Chart(document.getElementById('paymentChart'), {
            type: 'doughnut',
            data: {
                labels: paymentLabels,
                datasets: [{
                    data: paymentValues,
                    backgroundColor: [colors.primary, colors.success, colors.info, colors.warning, colors.danger],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Graphique des ventes par jour
        const dailyData = @json($ventesParJour);
        const dailyLabels = Object.keys(dailyData);
        const dailyValues = Object.values(dailyData).map(item => item.montant);

        new Chart(document.getElementById('dailyChart'), {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Ventes par Jour',
                    data: dailyValues,
                    borderColor: colors.primary,
                    backgroundColor: colors.primary + '20',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Graphique des caissiers
        const cashierData = @json($ventesParCaissier);
        const cashierLabels = Object.keys(cashierData).map(key => 'Caissier ' + key);
        const cashierValues = Object.values(cashierData).map(item => item.montant);

        new Chart(document.getElementById('cashierChart'), {
            type: 'bar',
            data: {
                labels: cashierLabels,
                datasets: [{
                    label: 'Ventes par Caissier',
                    data: cashierValues,
                    backgroundColor: [colors.success, colors.info, colors.warning],
                    borderColor: [colors.success, colors.info, colors.warning],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Graphique d'évolution des ventes
        new Chart(document.getElementById('salesChart'), {
            type: 'bar',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Évolution des Ventes',
                    data: dailyValues,
                    backgroundColor: colors.primary + '80',
                    borderColor: colors.primary,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>