<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        @media print {
            @page {
                margin: 1cm;
                size: A4;
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            background-color: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #4CAF50;
            margin: 0;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .print-controls {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 5px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background-color: #45a049;
        }
        
        .btn-secondary {
            background-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .summary {
            background-color: #e8f5e8;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid #4CAF50;
        }
        
        .summary h3 {
            margin: 0 0 15px 0;
            color: #4CAF50;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .summary-item {
            text-align: center;
            padding: 10px;
            background-color: white;
            border-radius: 5px;
        }
        
        .summary-value {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .summary-label {
            font-size: 12px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #4CAF50 !important;
            color: white !important;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9 !important;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="print-controls no-print">
        <button onclick="window.print()" class="btn">
            🖨️ Imprimer
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            ✖️ Fermer
        </button>
    </div>

    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Rapport généré le {{ now()->format('d/m/Y à H:i') }}</p>
        @if(isset($dateDebut) && isset($dateFin))
            <p>Période: {{ $dateDebut }} au {{ $dateFin }}</p>
        @endif
    </div>

    @if(isset($summary))
        <div class="summary">
            <h3>Résumé Exécutif</h3>
            <div class="summary-grid">
                @foreach($summary as $key => $value)
                    <div class="summary-item">
                        <div class="summary-value">{{ $value }}</div>
                        <div class="summary-label">{{ $key }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                <tr>
                    @foreach($row as $key => $value)
                        <td class="{{ in_array($key, ['chiffre_affaires', 'moyenne_facture', 'prix_moyen', 'quantite_vendue']) ? 'text-right' : '' }}">
                            @if(in_array($key, ['chiffre_affaires', 'moyenne_facture', 'prix_moyen']))
                                {{ number_format($value, 2, ',', ' ') }} DH
                            @elseif(in_array($key, ['quantite_vendue', 'nombre_factures', 'nombre_ventes']))
                                {{ number_format($value, 0, ',', ' ') }}
                            @else
                                {{ $value }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="text-center">
                        Aucune donnée disponible pour cette période
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>© {{ date('Y') }} AccessPOS Pro - Système de gestion de point de vente</p>
        <p>Rapport généré automatiquement</p>
    </div>

    <script>
        // Auto-print si demandé via URL
        if (window.location.search.includes('auto_print=1')) {
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            };
        }
    </script>
</body>
</html>
