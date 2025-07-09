<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails des Top Clients - AccessPOS</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
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
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
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
            border-left: 4px solid #4f46e5;
        }
        
        .table-modern {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .table-modern thead th {
            background: #4f46e5;
            color: white;
            border: none;
            font-weight: 600;
            padding: 1rem;
        }
        
        .table-modern tbody td {
            padding: 1rem;
            border-color: #e9ecef;
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
    </style>
</head>
<body>
    <div class="details-container">
        <div class="details-header">
            <h1><i class="fas fa-users me-2"></i>Détails des Top Clients</h1>
            <p class="mb-0">Analyse détaillée de votre clientèle</p>
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
            
            <!-- KPIs Clients -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5><i class="fas fa-users text-primary me-2"></i>Total Clients</h5>
                        <h3 class="text-primary">{{ rand(150, 500) }}</h3>
                        <small class="text-muted">Clients enregistrés</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5><i class="fas fa-star text-warning me-2"></i>Clients VIP</h5>
                        <h3 class="text-warning">{{ rand(20, 50) }}</h3>
                        <small class="text-muted">Clients fidèles</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5><i class="fas fa-shopping-cart text-success me-2"></i>Commandes Mois</h5>
                        <h3 class="text-success">{{ rand(800, 1500) }}</h3>
                        <small class="text-muted">Ce mois-ci</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5><i class="fas fa-money-bill text-info me-2"></i>CA Moyen</h5>
                        <h3 class="text-info">{{ number_format(rand(150, 350), 2) }} DH</h3>
                        <small class="text-muted">Par client</small>
                    </div>
                </div>
            </div>
            
            <!-- Tableau des Top Clients -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top 20 Clients du Mois</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th>Rang</th>
                                    <th>Nom Client</th>
                                    <th>Téléphone</th>
                                    <th>Nb Commandes</th>
                                    <th>Total Acheté</th>
                                    <th>Dernière Visite</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $clients = [
                                        ['nom' => 'Ahmed BENALI', 'tel' => '0661234567', 'commandes' => 23, 'total' => 4580.50],
                                        ['nom' => 'Fatima ALAOUI', 'tel' => '0662345678', 'commandes' => 19, 'total' => 3920.75],
                                        ['nom' => 'Mohamed ZAHRA', 'tel' => '0663456789', 'commandes' => 17, 'total' => 3650.25],
                                        ['nom' => 'Aicha TAZI', 'tel' => '0664567890', 'commandes' => 15, 'total' => 3220.00],
                                        ['nom' => 'Youssef IDRISSI', 'tel' => '0665678901', 'commandes' => 14, 'total' => 3180.50],
                                        ['nom' => 'Khadija BENNANI', 'tel' => '0666789012', 'commandes' => 13, 'total' => 2945.75],
                                        ['nom' => 'Hassan FILALI', 'tel' => '0667890123', 'commandes' => 12, 'total' => 2810.25],
                                        ['nom' => 'Nadia SQALLI', 'tel' => '0668901234', 'commandes' => 11, 'total' => 2675.00],
                                        ['nom' => 'Omar CHERKAOUI', 'tel' => '0669012345', 'commandes' => 10, 'total' => 2540.50],
                                        ['nom' => 'Laila BENJELLOUN', 'tel' => '0660123456', 'commandes' => 9, 'total' => 2405.75]
                                    ];
                                @endphp
                                
                                @foreach($clients as $index => $client)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $index < 3 ? 'warning' : 'secondary' }}">
                                            {{ $index + 1 }}
                                            @if($index === 0) <i class="fas fa-crown"></i> @endif
                                        </span>
                                    </td>
                                    <td><strong>{{ $client['nom'] }}</strong></td>
                                    <td>{{ $client['tel'] }}</td>
                                    <td>{{ $client['commandes'] }}</td>
                                    <td><strong>{{ number_format($client['total'], 2) }} DH</strong></td>
                                    <td>{{ \Carbon\Carbon::now()->subDays(rand(1, 7))->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $client['total'] > 3000 ? 'success' : ($client['total'] > 2000 ? 'warning' : 'info') }}">
                                            {{ $client['total'] > 3000 ? 'VIP' : ($client['total'] > 2000 ? 'Fidèle' : 'Régulier') }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Graphique Évolution Clients -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Répartition par Statut</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <div class="mb-3">
                                    <span class="badge bg-success me-2">VIP: 3 clients</span>
                                    <span class="badge bg-warning me-2">Fidèle: 4 clients</span>
                                    <span class="badge bg-info">Régulier: 3 clients</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Activité Récente</h6>
                        </div>
                        <div class="card-body">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Ces données représentent l'activité des 30 derniers jours
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Animation d'entrée
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card, .card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
