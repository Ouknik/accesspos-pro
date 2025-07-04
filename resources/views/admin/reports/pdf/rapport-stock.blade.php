
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rapport du Stock</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .stats {
            background: #f8f9fa;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .stats h3 {
            margin-top: 0;
            color: #495057;
        }
        .stats-grid {
            display: table;
            width: 100%;
        }
        .stats-item {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
        }
        .stats-value {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
        .stats-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success { background-color: #28a745; color: white; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-warning { background-color: #ffc107; color: black; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport du Stock</h1>
        <p><strong>Période:</strong> {{ $periode ?? 'N/A' }}</p>
        <p><strong>Généré le:</strong> {{ date('d/m/Y à H:i') }}</p>
    </div>
    
    <div class="stats">
        <h3>Statistiques Générales</h3>
        <div class="stats-grid">
            <div class="stats-item">
                <div class="stats-value">{{ number_format($statistiques['total_articles'] ?? 0) }}</div>
                <div class="stats-label">Total Articles</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ number_format($statistiques['articles_en_stock'] ?? 0) }}</div>
                <div class="stats-label">En Stock</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ number_format($statistiques['articles_rupture'] ?? 0) }}</div>
                <div class="stats-label">En Rupture</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ number_format($statistiques['valeur_stock'] ?? 0, 2) }} €</div>
                <div class="stats-label">Valeur Stock</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                @if(isset($statistiques['colonnes']['code']) && $statistiques['colonnes']['code'])
                    <th>Code</th>
                @endif
                @if(isset($statistiques['colonnes']['designation']) && $statistiques['colonnes']['designation'])
                    <th>Désignation</th>
                @endif
                @if(isset($statistiques['colonnes']['famille']) && $statistiques['colonnes']['famille'])
                    <th>Famille</th>
                @endif
                @if(isset($statistiques['colonnes']['stock']) && $statistiques['colonnes']['stock'])
                    <th class="text-center">Stock</th>
                @endif
                @if(isset($statistiques['colonnes']['prix_achat']) && $statistiques['colonnes']['prix_achat'])
                    <th class="text-right">Prix Achat</th>
                @endif
                @if(isset($statistiques['colonnes']['prix_vente']) && $statistiques['colonnes']['prix_vente'])
                    <th class="text-right">Prix Vente</th>
                @endif
                <th class="text-right">Valeur</th>
                <th class="text-center">Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($articles as $article)
            <tr>
                @if(isset($statistiques['colonnes']['code']) && $statistiques['colonnes']['code'])
                    <td>{{ $article->{$statistiques['colonnes']['code']} ?? 'N/A' }}</td>
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
                        {{ number_format($stock) }}
                    </td>
                @endif
                
                @if(isset($statistiques['colonnes']['prix_achat']) && $statistiques['colonnes']['prix_achat'])
                    <td class="text-right">{{ number_format($article->{$statistiques['colonnes']['prix_achat']} ?? 0, 2) }} €</td>
                @endif
                
                @if(isset($statistiques['colonnes']['prix_vente']) && $statistiques['colonnes']['prix_vente'])
                    <td class="text-right">{{ number_format($article->{$statistiques['colonnes']['prix_vente']} ?? 0, 2) }} €</td>
                @endif
                
                <td class="text-right">
                    @php
                        $stock = $article->{$statistiques['colonnes']['stock']} ?? 0;
                        $prixAchat = $article->{$statistiques['colonnes']['prix_achat']} ?? 0;
                    @endphp
                    <strong>{{ number_format($stock * $prixAchat, 2) }} €</strong>
                </td>
                
                <td class="text-center">
                    @php
                        $stock = $article->{$statistiques['colonnes']['stock']} ?? 0;
                    @endphp
                    @if($stock <= 0)
                        <span class="badge badge-danger">Rupture</span>
                    @elseif($stock <= 5)
                        <span class="badge badge-warning">Stock Faible</span>
                    @else
                        <span class="badge badge-success">En Stock</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Rapport généré automatiquement par AccessPOS Pro - {{ date('d/m/Y à H:i:s') }}</p>
    </div>
</body>
</html>