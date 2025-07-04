<!-- filepath: c:\Users\OA\Desktop\isat mosstafa\accesspos-pro\resources\views\admin\reports\rapport-stock.blade.php -->
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $statistiques['type_rapport'] }} - AccessPOS Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        .header-section {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
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
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
        }
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .table th {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
            border: none;
            padding: 15px;
        }
        .btn-action {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            margin: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="header-section">
        <div class="container">
            <h1 class="mb-2">
                <i class="fas fa-boxes me-3"></i>
                {{ $statistiques['type_rapport'] }}
            </h1>
            <p class="mb-0 fs-5">
                Période: {{ $statistiques['periode'] }}
            </p>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Statistiques -->
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stat-value text-info">{{ number_format($statistiques['total_articles']) }}</div>
                    <div class="stat-label">Total Articles</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stat-value text-success">{{ number_format($statistiques['articles_en_stock']) }}</div>
                    <div class="stat-label">Articles en Stock</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stat-value text-danger">{{ number_format($statistiques['articles_rupture']) }}</div>
                    <div class="stat-label">Articles en Rupture</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stat-value text-warning">{{ number_format($statistiques['valeur_stock'], 2) }} €</div>
                    <div class="stat-label">Valeur du Stock</div>
                </div>
            </div>
        </div>

        <!-- Tableau des articles -->
        <div class="table-container">
            <h4 class="mb-3">
                <i class="fas fa-table me-2"></i>
                Détail du Stock
            </h4>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            @if(isset($statistiques['colonnes']['code']) && $statistiques['colonnes']['code'])
                                <th>Code Article</th>
                            @endif
                            @if(isset($statistiques['colonnes']['designation']) && $statistiques['colonnes']['designation'])
                                <th>Désignation</th>
                            @endif
                            @if(isset($statistiques['colonnes']['famille']) && $statistiques['colonnes']['famille'])
                                <th>Famille</th>
                            @endif
                            @if(isset($statistiques['colonnes']['stock']) && $statistiques['colonnes']['stock'])
                                <th>Quantité</th>
                            @endif
                            @if(isset($statistiques['colonnes']['prix_achat']) && $statistiques['colonnes']['prix_achat'])
                                <th>Prix Achat</th>
                            @endif
                            @if(isset($statistiques['colonnes']['prix_vente']) && $statistiques['colonnes']['prix_vente'])
                                <th>Prix Vente</th>
                            @endif
                            <th>Valeur</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articles as $article)
                        <tr>
                            @if(isset($statistiques['colonnes']['code']) && $statistiques['colonnes']['code'])
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $article->{$statistiques['colonnes']['code']} ?? 'N/A' }}
                                    </span>
                                </td>
                            @endif
                            
                            @if(isset($statistiques['colonnes']['designation']) && $statistiques['colonnes']['designation'])
                                <td>{{ $article->{$statistiques['colonnes']['designation']} ?? 'N/A' }}</td>
                            @endif
                            
                            @if(isset($statistiques['colonnes']['famille']) && $statistiques['colonnes']['famille'])
                                <td>{{ $article->{$statistiques['colonnes']['famille']} ?? 'N/A' }}</td>
                            @endif
                            
                            @if(isset($statistiques['colonnes']['stock']) && $statistiques['colonnes']['stock'])
                                <td class="text-center">
                                    @php
                                        $stock = $article->{$statistiques['colonnes']['stock']} ?? 0;
                                    @endphp
                                    <span class="badge {{ $stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ number_format($stock) }}
                                    </span>
                                </td>
                            @endif
                            
                            @if(isset($statistiques['colonnes']['prix_achat']) && $statistiques['colonnes']['prix_achat'])
                                <td class="text-end">
                                    {{ number_format($article->{$statistiques['colonnes']['prix_achat']} ?? 0, 2) }} €
                                </td>
                            @endif
                            
                            @if(isset($statistiques['colonnes']['prix_vente']) && $statistiques['colonnes']['prix_vente'])
                                <td class="text-end">
                                    {{ number_format($article->{$statistiques['colonnes']['prix_vente']} ?? 0, 2) }} €
                                </td>
                            @endif
                            
                            <td class="text-end">
                                @php
                                    $stock = $article->{$statistiques['colonnes']['stock']} ?? 0;
                                    $prixAchat = $article->{$statistiques['colonnes']['prix_achat']} ?? 0;
                                @endphp
                                <strong>{{ number_format($stock * $prixAchat, 2) }} €</strong>
                            </td>
                            
                            <td>
                                @php
                                    $stock = $article->{$statistiques['colonnes']['stock']} ?? 0;
                                @endphp
                                @if($stock <= 0)
                                    <span class="badge bg-danger">Rupture</span>
                                @elseif($stock <= 5)
                                    <span class="badge bg-warning">Stock Faible</span>
                                @else
                                    <span class="badge bg-success">En Stock</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Boutons d'actions -->
        <div class="text-center mb-4">
            <a href="{{ route('admin.reports.index') }}" class="btn-action">
                <i class="fas fa-arrow-left me-2"></i>
                Retour aux Rapports
            </a>
            <a href="{{ route('admin.tableau-de-bord-moderne') }}" class="btn-action">
                <i class="fas fa-home me-2"></i>
                Tableau de Bord
            </a>
        </div>
    </div>
</body>
</html>