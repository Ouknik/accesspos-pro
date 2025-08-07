<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Export Factures - {{ date('d/m/Y') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #4472C4;
        }
        
        .header h1 {
            color: #4472C4;
            margin: 0;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid #4472C4;
        }
        
        .summary h3 {
            margin: 0 0 10px 0;
            color: #4472C4;
        }
        
        .summary-stats {
            display: flex;
            justify-content: space-between;
        }
        
        .stat-box {
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 3px;
            min-width: 120px;
        }
        
        .stat-box .number {
            font-size: 18px;
            font-weight: bold;
            color: #4472C4;
        }
        
        .stat-box .label {
            font-size: 11px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th {
            background-color: #4472C4;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e9ecef;
            font-size: 10px;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #e3f2fd;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 10px;
            color: #666;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Export des Factures</h1>
        <p>Généré le {{ date('d/m/Y à H:i') }}</p>
        <p>Période: {{ request('date_debut') ? date('d/m/Y', strtotime(request('date_debut'))) : 'Début' }} - {{ request('date_fin') ? date('d/m/Y', strtotime(request('date_fin'))) : 'Aujourd\'hui' }}</p>
    </div>

    @php
        $totalFactures = $factures->count();
        $totalHT = $factures->sum('FCTV_MNT_HT');
        $totalTTC = $factures->sum('FCTV_MNT_TTC');
        $totalRemise = $factures->sum('FCTV_REMISE');
        $factures_validees = $factures->where('statut', 'Validée')->count();
    @endphp

    <div class="summary">
        <h3>Résumé</h3>
        <div class="summary-stats">
            <div class="stat-box">
                <div class="number">{{ number_format($totalFactures) }}</div>
                <div class="label">Total Factures</div>
            </div>
            <div class="stat-box">
                <div class="number">{{ number_format($factures_validees) }}</div>
                <div class="label">Factures Validées</div>
            </div>
            <div class="stat-box">
                <div class="number">{{ number_format($totalHT, 2) }} DH</div>
                <div class="label">Total HT</div>
            </div>
            <div class="stat-box">
                <div class="number">{{ number_format($totalTTC, 2) }} DH</div>
                <div class="label">Total TTC</div>
            </div>
            <div class="stat-box">
                <div class="number">{{ number_format($totalRemise, 2) }} DH</div>
                <div class="label">Total Remises</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%">Référence</th>
                <th style="width: 8%">N°</th>
                <th style="width: 10%">Date</th>
                <th style="width: 20%">Client</th>
                <th style="width: 12%">Montant HT</th>
                <th style="width: 12%">Montant TTC</th>
                <th style="width: 10%">Remise</th>
                <th style="width: 8%">Statut</th>
                <th style="width: 8%">État</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factures as $index => $facture)
                <tr>
                    <td>{{ $facture->FCTV_REF }}</td>
                    <td class="text-center">{{ $facture->FCTV_NUM }}</td>
                    <td class="text-center">{{ $facture->FCTV_DATE ? date('d/m/Y', strtotime($facture->FCTV_DATE)) : '-' }}</td>
                    <td>{{ $facture->CLT_CLIENT ?? 'Client anonyme' }}</td>
                    <td class="text-right">{{ number_format($facture->FCTV_MNT_HT ?? 0, 2) }} DH</td>
                    <td class="text-right">{{ number_format($facture->FCTV_MNT_TTC ?? 0, 2) }} DH</td>
                    <td class="text-right">{{ number_format($facture->FCTV_REMISE ?? 0, 2) }} DH</td>
                    <td class="text-center">
                        <span class="badge {{ $facture->statut == 'Validée' ? 'badge-success' : 'badge-warning' }}">
                            {{ $facture->statut }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $facture->etat == 'Active' ? 'badge-success' : 'badge-danger' }}">
                            {{ $facture->etat }}
                        </span>
                    </td>
                </tr>
                
                @if(($index + 1) % 25 == 0 && $index + 1 < $totalFactures)
                    </tbody>
                    </table>
                    <div class="page-break"></div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 12%">Référence</th>
                                <th style="width: 8%">N°</th>
                                <th style="width: 10%">Date</th>
                                <th style="width: 20%">Client</th>
                                <th style="width: 12%">Montant HT</th>
                                <th style="width: 12%">Montant TTC</th>
                                <th style="width: 10%">Remise</th>
                                <th style="width: 8%">Statut</th>
                                <th style="width: 8%">État</th>
                            </tr>
                        </thead>
                        <tbody>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>AccessPos Pro - Système de Gestion des Factures</p>
        <p>Ce document contient {{ number_format($totalFactures) }} facture(s) pour un montant total TTC de {{ number_format($totalTTC, 2) }} DH</p>
    </div>
</body>
</html>
