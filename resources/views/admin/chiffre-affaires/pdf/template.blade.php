<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
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
        .info-section {
            margin-bottom: 20px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }
        .info-row {
            display: inline-block;
            width: 48%;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
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
        .summary {
            background-color: #e8f5e8;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid #4CAF50;
        }
        .summary h3 {
            margin: 0 0 10px 0;
            color: #4CAF50;
        }
        .summary-item {
            display: inline-block;
            width: 32%;
            text-align: center;
            margin-bottom: 10px;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .summary-label {
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Rapport généré le {{ now()->format('d/m/Y à H:i') }}</p>
        @if(isset($dateDebut) && isset($dateFin))
            <p>Période: {{ $dateDebut }} au {{ $dateFin }}</p>
        @endif
    </div>

    @if(isset($summary))
        <div class="summary">
            <h3>Résumé</h3>
            @foreach($summary as $key => $value)
                <div class="summary-item">
                    <div class="summary-value">{{ $value }}</div>
                    <div class="summary-label">{{ $key }}</div>
                </div>
            @endforeach
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
        <p>Page {{ $page ?? 1 }} / {{ $totalPages ?? 1 }}</p>
    </div>
</body>
</html>
