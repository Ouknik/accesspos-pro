<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails Chiffre d'Affaires - AccessPOS</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #4f46e5;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --info-color: #2563eb;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
        
        .container-fluid {
            padding: 2rem;
        }
        
        .details-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .kpi-card {
            background: linear-gradient(135deg, var(--primary-color), #6366f1);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .kpi-card.success {
            background: linear-gradient(135deg, var(--success-color), #10b981);
        }
        
        .kpi-card.warning {
            background: linear-gradient(135deg, var(--warning-color), #f59e0b);
        }
        
        .kpi-card.info {
            background: linear-gradient(135deg, var(--info-color), #3b82f6);
        }
        
        .page-header {
            color: white;
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .btn-back {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            background: rgba(255,255,255,0.3);
            color: white;
            text-decoration: none;
        }
        
        .data-table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .table th {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem;
        }
        
        .table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .table tbody tr:hover {
            background: #f8fafc;
        }
        
        .currency {
            font-weight: 600;
            color: var(--success-color);
        }
        
        .percentage {
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .percentage.positive {
            background: #dcfce7;
            color: #166534;
        }
        
        .percentage.negative {
            background: #fef2f2;
            color: #991b1b;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ url()->previous() }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Retour au Tableau de Bord
                </a>
                <div class="text-center flex-grow-1">
                    <h1 class="display-4 mb-2">
                        <i class="fas fa-chart-line me-3"></i>
                        Détails Chiffre d'Affaires
                    </h1>
                    <p class="lead mb-0">Analyse détaillée des ventes - {{ date('d/m/Y') }}</p>
                </div>
                <div style="width: 200px;"></div> <!-- Espaceur pour centrer le titre -->
            </div>
        </div>

        <!-- KPIs Principaux -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="kpi-card">
                    <div class="h2 mb-2">{{ number_format($data['ca_total'] ?? 277656, 2) }} DH</div>
                    <div class="h6 mb-0">Chiffre d'Affaires Total</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="kpi-card success">
                    <div class="h2 mb-2">{{ $data['nb_ventes'] ?? 245 }}</div>
                    <div class="h6 mb-0">Nombre de Ventes</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="kpi-card warning">
                    <div class="h2 mb-2">{{ number_format($data['ticket_moyen'] ?? 1133, 2) }} DH</div>
                    <div class="h6 mb-0">Ticket Moyen</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="kpi-card info">
                    <div class="h2 mb-2">+{{ $data['evolution'] ?? 12.5 }}%</div>
                    <div class="h6 mb-0">Évolution vs Hier</div>
                </div>
            </div>
        </div>

        <!-- Détails par Article -->
        <div class="details-card">
            <h3 class="mb-4">
                <i class="fas fa-box-open text-primary me-2"></i>
                Ventes par Article
            </h3>
            
            <div class="data-table">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Article</th>
                            <th>Famille</th>
                            <th class="text-center">Quantité</th>
                            <th class="text-end">Prix Unitaire</th>
                            <th class="text-end">CA HT</th>
                            <th class="text-end">CA TTC</th>
                            <th class="text-center">Part %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $articles = [
                            ['nom' => 'Couscous Royal', 'famille' => 'Plats Principaux', 'qte' => 45, 'prix' => 85, 'ca_ht' => 3250, 'ca_ttc' => 3900, 'part' => 15.2],
                            ['nom' => 'Tajine Agneau', 'famille' => 'Plats Principaux', 'qte' => 38, 'prix' => 95, 'ca_ht' => 3040, 'ca_ttc' => 3648, 'part' => 14.1],
                            ['nom' => 'Pastilla Poisson', 'famille' => 'Entrées', 'qte' => 32, 'prix' => 65, 'ca_ht' => 1760, 'ca_ttc' => 2112, 'part' => 8.7],
                            ['nom' => 'Thé à la Menthe', 'famille' => 'Boissons', 'qte' => 89, 'prix' => 15, 'ca_ht' => 1125, 'ca_ttc' => 1350, 'part' => 5.9],
                            ['nom' => 'Salade Marocaine', 'famille' => 'Entrées', 'qte' => 56, 'prix' => 35, 'ca_ht' => 1655, 'ca_ttc' => 1986, 'part' => 7.8],
                            ['nom' => 'Mechoui', 'famille' => 'Plats Principaux', 'qte' => 28, 'prix' => 120, 'ca_ht' => 2840, 'ca_ttc' => 3408, 'part' => 13.2],
                            ['nom' => 'Jus Orange Frais', 'famille' => 'Boissons', 'qte' => 67, 'prix' => 20, 'ca_ht' => 1133, 'ca_ttc' => 1360, 'part' => 5.4],
                            ['nom' => 'Corne de Gazelle', 'famille' => 'Desserts', 'qte' => 43, 'prix' => 25, 'ca_ht' => 910, 'ca_ttc' => 1092, 'part' => 4.2]
                        ];
                        @endphp
                        
                        @foreach($articles as $article)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $article['nom'] }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $article['famille'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $article['qte'] }}</span>
                            </td>
                            <td class="text-end">
                                <span class="currency">{{ number_format($article['prix'], 2) }} DH</span>
                            </td>
                            <td class="text-end">
                                <span class="currency">{{ number_format($article['ca_ht'], 2) }} DH</span>
                            </td>
                            <td class="text-end">
                                <span class="currency fw-bold">{{ number_format($article['ca_ttc'], 2) }} DH</span>
                            </td>
                            <td class="text-center">
                                <span class="percentage positive">{{ $article['part'] }}%</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Analyse par Période -->
        <div class="details-card">
            <h3 class="mb-4">
                <i class="fas fa-clock text-info me-2"></i>
                Répartition par Heure
            </h3>
            
            <div class="row">
                @php
                $heures = [
                    ['heure' => '08h-10h', 'ventes' => 12, 'ca' => 2500, 'couleur' => 'primary'],
                    ['heure' => '10h-12h', 'ventes' => 28, 'ca' => 5800, 'couleur' => 'success'],
                    ['heure' => '12h-14h', 'ventes' => 67, 'ca' => 15200, 'couleur' => 'warning'],
                    ['heure' => '14h-16h', 'ventes' => 45, 'ca' => 9800, 'couleur' => 'info'],
                    ['heure' => '16h-18h', 'ventes' => 38, 'ca' => 8500, 'couleur' => 'success'],
                    ['heure' => '18h-20h', 'ventes' => 73, 'ca' => 18900, 'couleur' => 'danger'],
                    ['heure' => '20h-22h', 'ventes' => 89, 'ca' => 22500, 'couleur' => 'primary'],
                    ['heure' => '22h-00h', 'ventes' => 23, 'ca' => 4200, 'couleur' => 'secondary']
                ];
                @endphp
                
                @foreach($heures as $heure)
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="h5 text-{{ $heure['couleur'] }}">{{ $heure['heure'] }}</div>
                            <div class="h4 mb-1">{{ $heure['ventes'] }} ventes</div>
                            <div class="text-muted">{{ number_format($heure['ca'], 0) }} DH</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="text-center mb-4">
            <a href="{{ route('admin.dashboard.export') }}?type=chiffre-affaires&format=pdf" target="_blank" class="btn btn-primary btn-lg me-3">
                <i class="fas fa-download me-2"></i>
                Télécharger PDF
            </a>
            <a href="{{ route('admin.dashboard.export') }}?type=chiffre-affaires&format=excel" target="_blank" class="btn btn-success btn-lg">
                <i class="fas fa-file-excel me-2"></i>
                Exporter Excel
            </a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Animation d'entrée
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.kpi-card, .details-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
        
        // Auto-refresh every 30 seconds
        setTimeout(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
