<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>État des Tables - AccessPOS</title>
    
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
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
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
            border-left: 4px solid #dc2626;
        }
        
        .table-item {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .table-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        
        .table-item.occupee {
            border-color: #dc2626;
            background: #fef2f2;
        }
        
        .table-item.libre {
            border-color: #059669;
            background: #f0fdf4;
        }
        
        .table-item.reservee {
            border-color: #f59e0b;
            background: #fffbeb;
        }
        
        .table-item.maintenance {
            border-color: #6b7280;
            background: #f9fafb;
        }
        
        .table-status {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-occupee {
            background: #dc2626;
            color: white;
        }
        
        .status-libre {
            background: #059669;
            color: white;
        }
        
        .status-reservee {
            background: #f59e0b;
            color: white;
        }
        
        .status-maintenance {
            background: #6b7280;
            color: white;
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
        
        .table-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
        }
        
        .table-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .table-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1f2937;
        }
        
        .table-details {
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .occupancy-bar {
            width: 100%;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin: 1rem 0;
        }
        
        .occupancy-fill {
            height: 100%;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="details-container">
        <div class="details-header">
            <h1><i class="fas fa-utensils me-2"></i>État des Tables</h1>
            <p class="mb-0">Gestion et suivi en temps réel des tables</p>
        </div>
        
        <div class="details-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ url('/admin/tableau-de-bord-moderne') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Retour au Tableau de Bord
                </a>
                <div class="d-flex gap-2">
                    <button class="btn btn-success btn-sm" onclick="updateStatus()">
                        <i class="fas fa-sync me-1"></i>
                        Actualiser
                    </button>
                    <button class="btn btn-danger" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>
                        Imprimer
                    </button>
                </div>
            </div>
            
            <!-- KPIs Tables -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5><i class="fas fa-chair text-danger me-2"></i>Tables Occupées</h5>
                        <h3 class="text-danger">{{ rand(8, 15) }}/24</h3>
                        <small class="text-muted">{{ round(rand(30, 70), 1) }}% d'occupation</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5><i class="fas fa-check-circle text-success me-2"></i>Tables Libres</h5>
                        <h3 class="text-success">{{ rand(8, 15) }}</h3>
                        <small class="text-muted">Disponibles immédiatement</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5><i class="fas fa-calendar text-warning me-2"></i>Réservations</h5>
                        <h3 class="text-warning">{{ rand(3, 8) }}</h3>
                        <small class="text-muted">Aujourd'hui</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5><i class="fas fa-tools text-secondary me-2"></i>Maintenance</h5>
                        <h3 class="text-secondary">{{ rand(0, 2) }}</h3>
                        <small class="text-muted">Hors service</small>
                    </div>
                </div>
            </div>
            
            <!-- Barre d'occupation globale -->
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Taux d'Occupation Global</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Occupation actuelle</span>
                        <span><strong>{{ rand(40, 80) }}%</strong></span>
                    </div>
                    <div class="occupancy-bar">
                        <div class="occupancy-fill bg-danger" style="width: {{ rand(40, 80) }}%"></div>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Mise à jour en temps réel - Dernière actualisation: {{ date('H:i:s') }}
                    </small>
                </div>
            </div>
            
            <!-- État Détaillé des Tables -->
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>État Détaillé de Toutes les Tables</h5>
                </div>
                <div class="card-body">
                    <div class="table-grid">
                        @php
                            $tables = [
                                ['numero' => 1, 'statut' => 'occupee', 'capacite' => 4, 'client' => 'Famille ALAMI', 'heure' => '18:30', 'serveur' => 'Mohamed'],
                                ['numero' => 2, 'statut' => 'libre', 'capacite' => 2, 'client' => null, 'heure' => null, 'serveur' => null],
                                ['numero' => 3, 'statut' => 'occupee', 'capacite' => 6, 'client' => 'Groupe BENALI', 'heure' => '19:15', 'serveur' => 'Aicha'],
                                ['numero' => 4, 'statut' => 'reservee', 'capacite' => 4, 'client' => 'M. TAZI', 'heure' => '20:00', 'serveur' => 'En attente'],
                                ['numero' => 5, 'statut' => 'libre', 'capacite' => 2, 'client' => null, 'heure' => null, 'serveur' => null],
                                ['numero' => 6, 'statut' => 'occupee', 'capacite' => 8, 'client' => 'Anniversaire FILALI', 'heure' => '19:00', 'serveur' => 'Hassan'],
                                ['numero' => 7, 'statut' => 'libre', 'capacite' => 4, 'client' => null, 'heure' => null, 'serveur' => null],
                                ['numero' => 8, 'statut' => 'maintenance', 'capacite' => 4, 'client' => 'Hors service', 'heure' => null, 'serveur' => null],
                                ['numero' => 9, 'statut' => 'occupee', 'capacite' => 2, 'client' => 'Couple SQALLI', 'heure' => '18:45', 'serveur' => 'Fatima'],
                                ['numero' => 10, 'statut' => 'libre', 'capacite' => 6, 'client' => null, 'heure' => null, 'serveur' => null],
                                ['numero' => 11, 'statut' => 'reservee', 'capacite' => 4, 'client' => 'Mme BENNANI', 'heure' => '20:30', 'serveur' => 'En attente'],
                                ['numero' => 12, 'statut' => 'libre', 'capacite' => 2, 'client' => null, 'heure' => null, 'serveur' => null]
                            ];
                        @endphp
                        
                        @foreach($tables as $table)
                        <div class="table-item {{ $table['statut'] }}">
                            <div class="table-status status-{{ $table['statut'] }}">
                                @switch($table['statut'])
                                    @case('occupee')
                                        <i class="fas fa-user-friends me-1"></i>Occupée
                                        @break
                                    @case('libre')
                                        <i class="fas fa-check me-1"></i>Libre
                                        @break
                                    @case('reservee')
                                        <i class="fas fa-calendar me-1"></i>Réservée
                                        @break
                                    @case('maintenance')
                                        <i class="fas fa-tools me-1"></i>Maintenance
                                        @break
                                @endswitch
                            </div>
                            
                            <div class="table-info">
                                <div class="table-number">
                                    <i class="fas fa-{{ $table['statut'] === 'occupee' ? 'chair' : ($table['statut'] === 'libre' ? 'check-circle' : ($table['statut'] === 'reservee' ? 'calendar-alt' : 'tools')) }} me-2"></i>
                                    Table {{ $table['numero'] }}
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">{{ $table['capacite'] }} places</small>
                                </div>
                            </div>
                            
                            @if($table['statut'] === 'occupee')
                            <div class="table-details">
                                <div class="row">
                                    <div class="col-6">
                                        <strong>Client:</strong><br>
                                        {{ $table['client'] }}
                                    </div>
                                    <div class="col-6">
                                        <strong>Arrivée:</strong><br>
                                        {{ $table['heure'] }}
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <strong>Serveur:</strong> {{ $table['serveur'] }}
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        Durée: {{ rand(30, 180) }} min
                                    </small>
                                </div>
                            </div>
                            @elseif($table['statut'] === 'reservee')
                            <div class="table-details">
                                <div class="row">
                                    <div class="col-6">
                                        <strong>Réservation:</strong><br>
                                        {{ $table['client'] }}
                                    </div>
                                    <div class="col-6">
                                        <strong>Heure:</strong><br>
                                        {{ $table['heure'] }}
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-phone me-1"></i>
                                        Tel: 066{{ rand(1000000, 9999999) }}
                                    </small>
                                </div>
                            </div>
                            @elseif($table['statut'] === 'libre')
                            <div class="table-details">
                                <div class="text-center">
                                    <i class="fas fa-check-circle text-success" style="font-size: 2rem; margin: 1rem 0;"></i>
                                    <div><strong>Disponible</strong></div>
                                    <small class="text-muted">Prête à accueillir {{ $table['capacite'] }} personnes</small>
                                </div>
                            </div>
                            @else
                            <div class="table-details">
                                <div class="text-center">
                                    <i class="fas fa-tools text-secondary" style="font-size: 2rem; margin: 1rem 0;"></i>
                                    <div><strong>En maintenance</strong></div>
                                    <small class="text-muted">Temporairement indisponible</small>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Légende -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Légende</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <span class="badge bg-danger me-2">Occupée</span>
                                    Table actuellement en service
                                </div>
                                <div class="col-md-3">
                                    <span class="badge bg-success me-2">Libre</span>
                                    Table disponible immédiatement
                                </div>
                                <div class="col-md-3">
                                    <span class="badge bg-warning me-2">Réservée</span>
                                    Table réservée pour plus tard
                                </div>
                                <div class="col-md-3">
                                    <span class="badge bg-secondary me-2">Maintenance</span>
                                    Table hors service
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Fonction pour actualiser le statut
        function updateStatus() {
            // Simulation d'actualisation
            const btn = document.querySelector('[onclick="updateStatus()"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Actualisation...';
            btn.disabled = true;
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                
                // Mettre à jour l'heure
                const timeElements = document.querySelectorAll('.text-muted');
                timeElements.forEach(el => {
                    if (el.textContent.includes('Dernière actualisation')) {
                        el.innerHTML = '<i class="fas fa-info-circle me-1"></i>Mise à jour en temps réel - Dernière actualisation: ' + new Date().toLocaleTimeString();
                    }
                });
            }, 2000);
        }
        
        // Animation d'entrée
        document.addEventListener('DOMContentLoaded', function() {
            const tables = document.querySelectorAll('.table-item');
            tables.forEach((table, index) => {
                table.style.opacity = '0';
                table.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    table.style.transition = 'all 0.5s ease';
                    table.style.opacity = '1';
                    table.style.transform = 'translateY(0)';
                }, index * 100);
            });
            
            // Animation des cartes statistiques
            const cards = document.querySelectorAll('.stat-card, .card');
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
