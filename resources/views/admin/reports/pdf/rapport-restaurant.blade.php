
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rapport Restaurant</title>
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
            width: 33.33%;
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
        <h1>Rapport Restaurant</h1>
        <p><strong>Période:</strong> {{ $periode ?? 'N/A' }}</p>
        <p><strong>Généré le:</strong> {{ date('d/m/Y à H:i') }}</p>
    </div>
    
    <div class="stats">
        <h3>Statistiques Restaurant</h3>
        <div class="stats-grid">
            <div class="stats-item">
                <div class="stats-value">{{ number_format($statistiques['total_tables'] ?? 0) }}</div>
                <div class="stats-label">Total Tables</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ number_format($statistiques['tables_actives'] ?? 0) }}</div>
                <div class="stats-label">Tables Actives</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ number_format($statistiques['total_reservations'] ?? 0) }}</div>
                <div class="stats-label">Réservations</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Rapport généré automatiquement par AccessPOS Pro - {{ date('d/m/Y à H:i:s') }}</p>
    </div>
</body>
</html>