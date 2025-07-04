
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rapport Financier</title>
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
        <h1>Rapport Financier</h1>
        <p><strong>Période:</strong> {{ $periode ?? 'N/A' }}</p>
        <p><strong>Généré le:</strong> {{ date('d/m/Y à H:i') }}</p>
    </div>
    
    <div class="stats">
        <h3>Résumé Financier</h3>
        <div class="stats-grid">
            <div class="stats-item">
                <div class="stats-value">{{ number_format($statistiques['chiffre_affaires'] ?? 0, 2) }} €</div>
                <div class="stats-label">Chiffre d'Affaires TTC</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ number_format($statistiques['chiffre_affaires_ht'] ?? 0, 2) }} €</div>
                <div class="stats-label">Chiffre d'Affaires HT</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ number_format($statistiques['tva_collectee'] ?? 0, 2) }} €</div>
                <div class="stats-label">TVA Collectée</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ number_format($statistiques['nombre_transactions'] ?? 0) }}</div>
                <div class="stats-label">Nombre Transactions</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Rapport généré automatiquement par AccessPOS Pro - {{ date('d/m/Y à H:i:s') }}</p>
    </div>
</body>
</html>