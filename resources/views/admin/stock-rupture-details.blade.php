<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles en Rupture - AccessPOS</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #4f46e5;
            --danger-color: #dc2626;
            --warning-color: #d97706;
            --success-color: #059669;
        }
        
        body {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 50%, #fecfef 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
        
        .alert-card {
            background: linear-gradient(135deg, var(--danger-color), #ef4444);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(220, 38, 38, 0.3);
        }
        
        .stock-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-left: 5px solid var(--danger-color);
        }
        
        .stock-card.warning {
            border-left-color: var(--warning-color);
        }
        
        .stock-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .status-rupture {
            background: #fef2f2;
            color: #991b1b;
        }
        
        .status-faible {
            background: #fffbeb;
            color: #92400e;
        }
        
        .urgent-action {
            background: linear-gradient(135deg, #ff416c, #ff4b2b);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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
        }
    </style>
</head>

<body>
    <div class="container-fluid p-4">
        <!-- En-tête -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ url()->previous() }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Retour au Tableau de Bord
                </a>
                <div class="text-center flex-grow-1">
                    <h1 class="display-4 mb-2">
                        <i class="fas fa-exclamation-triangle me-3"></i>
                        Articles en Rupture de Stock
                    </h1>
                    <p class="lead mb-0">⚠️ Action urgente requise - {{ date('d/m/Y') }}</p>
                </div>
                <div style="width: 200px;"></div>
            </div>
        </div>

        <!-- Alerte Principale -->
        <div class="alert-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-2">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Attention: 24 articles nécessitent une action immédiate
                    </h3>
                    <p class="mb-0">Des articles sont en rupture ou ont un stock critique. Contactez vos fournisseurs rapidement.</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="#" class="urgent-action">
                        <i class="fas fa-phone"></i>
                        Contacter Fournisseurs
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistiques Rapides -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h2>12</h2>
                        <p class="mb-0">Rupture Totale</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h2>12</h2>
                        <p class="mb-0">Stock Critique</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h2>45,250 DH</h2>
                        <p class="mb-0">Valeur Manquante</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h2>8</h2>
                        <p class="mb-0">Fournisseurs à Contacter</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des Articles -->
        <div class="row">
            @php
            $articles = [
                ['nom' => 'Couscous Grain Fin', 'famille' => 'Céréales', 'stock' => 0, 'min' => 50, 'prix' => 25, 'fournisseur' => 'Maroc Céréales', 'statut' => 'rupture'],
                ['nom' => 'Agneau Épaule', 'famille' => 'Viandes', 'stock' => 2, 'min' => 15, 'prix' => 180, 'fournisseur' => 'Boucherie Al Baraka', 'statut' => 'faible'],
                ['nom' => 'Huile d\'Olive Extra', 'famille' => 'Condiments', 'stock' => 0, 'min' => 20, 'prix' => 85, 'fournisseur' => 'Olive Morocco', 'statut' => 'rupture'],
                ['nom' => 'Tomates Fraîches', 'famille' => 'Légumes', 'stock' => 3, 'min' => 30, 'prix' => 12, 'fournisseur' => 'Ferme Atlas', 'statut' => 'faible'],
                ['nom' => 'Menthe Fraîche', 'famille' => 'Herbes', 'stock' => 0, 'min' => 10, 'prix' => 8, 'fournisseur' => 'Jardin Marrakech', 'statut' => 'rupture'],
                ['nom' => 'Lait Frais', 'famille' => 'Laiterie', 'stock' => 1, 'min' => 25, 'prix' => 6, 'fournisseur' => 'Laiterie Centrale', 'statut' => 'faible'],
                ['nom' => 'Pain Traditionnel', 'famille' => 'Boulangerie', 'stock' => 0, 'min' => 40, 'prix' => 3, 'fournisseur' => 'Four Traditionnel', 'statut' => 'rupture'],
                ['nom' => 'Épices Ras El Hanout', 'famille' => 'Épices', 'stock' => 2, 'min' => 12, 'prix' => 45, 'fournisseur' => 'Épices Fès', 'statut' => 'faible']
            ];
            @endphp
            
            @foreach($articles as $article)
            <div class="col-lg-6 col-md-12">
                <div class="stock-card {{ $article['statut'] === 'faible' ? 'warning' : '' }}">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1">{{ $article['nom'] }}</h5>
                            <small class="text-muted">{{ $article['famille'] }}</small>
                        </div>
                        <span class="stock-status status-{{ $article['statut'] }}">
                            {{ $article['statut'] === 'rupture' ? 'RUPTURE' : 'CRITIQUE' }}
                        </span>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">Stock Actuel</small>
                            <div class="h4 text-{{ $article['statut'] === 'rupture' ? 'danger' : 'warning' }}">
                                {{ $article['stock'] }}
                            </div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Stock Minimum</small>
                            <div class="h4 text-muted">{{ $article['min'] }}</div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Prix: {{ $article['prix'] }} DH</small><br>
                            <small class="text-primary">{{ $article['fournisseur'] }}</small>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-phone"></i>
                            </button>
                            <button class="btn btn-outline-success btn-sm">
                                <i class="fas fa-plus"></i> Commander
                            </button>
                        </div>
                    </div>
                    
                    @if($article['statut'] === 'rupture')
                    <div class="mt-2">
                        <div class="alert alert-danger alert-sm mb-0 py-2">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Manque à gagner estimé:</strong> {{ number_format(($article['min'] - $article['stock']) * $article['prix'], 2) }} DH
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Actions Rapides -->
        <div class="text-center mt-4">
            <a href="#" class="btn btn-danger btn-lg me-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Générer Commandes Urgentes
            </a>
            <a href="#" class="btn btn-warning btn-lg me-3">
                <i class="fas fa-envelope me-2"></i>
                Envoyer Alertes Fournisseurs
            </a>
            <a href="{{ route('admin.dashboard.export') }}?type=stock-rupture&format=pdf" class="btn btn-primary btn-lg">
                <i class="fas fa-download me-2"></i>
                Rapport PDF
            </a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Animation d'entrée
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stock-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateX(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
