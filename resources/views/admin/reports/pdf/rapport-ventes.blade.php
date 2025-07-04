
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rapport des Ventes</title>
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
        .total { font-weight: bold; }
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
        <h1>Rapport des Ventes</h1>
        <p><strong>Période:</strong> {{ $periode ?? 'N/A' }}</p>
        <p><strong>Généré le:</strong> {{ date('d/m/Y à H:i') }}</p>
    </div>
    
    <div class="stats">
        <h3>Statistiques Générales</h3>
        <div class="stats-grid">
            <div class="stats-item">
                <div class="stats-value">{{ number_format($statistiques['total_ventes'] ?? 0, 2) }} €</div>
                <div class="stats-label">Total Ventes TTC</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ number_format($statistiques['total_ventes_ht'] ?? 0, 2) }} €</div>
                <div class="stats-label">Total Ventes HT</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ number_format($statistiques['nombre_factures'] ?? 0) }}</div>
                <div class="stats-label">Nombre Factures</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ number_format($statistiques['ticket_moyen'] ?? 0, 2) }} €</div>
                <div class="stats-label">Ticket Moyen</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                @if(isset($statistiques['colonnes']['ref']) && $statistiques['colonnes']['ref'])
                    <th>Référence</th>
                @endif
                @if(isset($statistiques['colonnes']['date']) && $statistiques['colonnes']['date'])
                    <th>Date</th>
                @endif
                @if(isset($statistiques['colonnes']['montant_ht']) && $statistiques['colonnes']['montant_ht'])
                    <th class="text-right">Montant HT</th>
                @endif
                @if(isset($statistiques['colonnes']['montant_ttc']) && $statistiques['colonnes']['montant_ttc'])
                    <th class="text-right">Montant TTC</th>
                @endif
                @if(isset($statistiques['colonnes']['mode_paiement']) && $statistiques['colonnes']['mode_paiement'])
                    <th>Mode Paiement</th>
                @endif
                @if(isset($statistiques['colonnes']['utilisateur']) && $statistiques['colonnes']['utilisateur'])
                    <th>Caissier</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                @if(isset($statistiques['colonnes']['ref']) && $statistiques['colonnes']['ref'])
                    <td>{{ $sale->{$statistiques['colonnes']['ref']} ?? 'N/A' }}</td>
                @endif
                
                @if(isset($statistiques['colonnes']['date']) && $statistiques['colonnes']['date'])
                    <td>{{ isset($statistiques['colonnes']['date']) ? \Carbon\Carbon::parse($sale->{$statistiques['colonnes']['date']})->format('d/m/Y H:i') : 'N/A' }}</td>
                @endif
                
                @if(isset($statistiques['colonnes']['montant_ht']) && $statistiques['colonnes']['montant_ht'])
                    <td class="text-right">{{ number_format($sale->{$statistiques['colonnes']['montant_ht']} ?? 0, 2) }} €</td>
                @endif
                
                @if(isset($statistiques['colonnes']['montant_ttc']) && $statistiques['colonnes']['montant_ttc'])
                    <td class="text-right total">{{ number_format($sale->{$statistiques['colonnes']['montant_ttc']} ?? 0, 2) }} €</td>
                @endif
                
                @if(isset($statistiques['colonnes']['mode_paiement']) && $statistiques['colonnes']['mode_paiement'])
                    <td>{{ $sale->{$statistiques['colonnes']['mode_paiement']} ?? 'N/A' }}</td>
                @endif
                
                @if(isset($statistiques['colonnes']['utilisateur']) && $statistiques['colonnes']['utilisateur'])
                    <td>{{ $sale->{$statistiques['colonnes']['utilisateur']} ?? 'N/A' }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Rapport généré automatiquement par AccessPOS Pro - {{ date('d/m/Y à H:i:s') }}</p>
    </div>
</body>
</html>