
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rapport des Clients</title>
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
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success { background-color: #28a745; color: white; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }
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
        <h1>Rapport des Clients</h1>
        <p><strong>Période:</strong> {{ $periode ?? 'N/A' }}</p>
        <p><strong>Généré le:</strong> {{ date('d/m/Y à H:i') }}</p>
    </div>
    
    <div class="stats">
        <h3>Statistiques Générales</h3>
        <div class="stats-grid">
            <div class="stats-item">
                <div class="stats-value">{{ number_format($statistiques['total_clients'] ?? 0) }}</div>
                <div class="stats-label">Total Clients</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ number_format($statistiques['clients_actifs'] ?? 0) }}</div>
                <div class="stats-label">Clients Actifs</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ number_format($statistiques['clients_fideles'] ?? 0) }}</div>
                <div class="stats-label">Clients Fidèles</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                @if(isset($statistiques['colonnes']['code']) && $statistiques['colonnes']['code'])
                    <th>Code</th>
                @endif
                @if(isset($statistiques['colonnes']['nom']) && $statistiques['colonnes']['nom'])
                    <th>Nom</th>
                @endif
                @if(isset($statistiques['colonnes']['tel']) && $statistiques['colonnes']['tel'])
                    <th>Téléphone</th>
                @endif
                @if(isset($statistiques['colonnes']['email']) && $statistiques['colonnes']['email'])
                    <th>Email</th>
                @endif
                @if(isset($statistiques['colonnes']['fidele']) && $statistiques['colonnes']['fidele'])
                    <th class="text-center">Fidèle</th>
                @endif
                @if(isset($statistiques['colonnes']['actif']) && $statistiques['colonnes']['actif'])
                    <th class="text-center">Statut</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $client)
            <tr>
                @if(isset($statistiques['colonnes']['code']) && $statistiques['colonnes']['code'])
                    <td>{{ $client->{$statistiques['colonnes']['code']} ?? 'N/A' }}</td>
                @endif
                
                @if(isset($statistiques['colonnes']['nom']) && $statistiques['colonnes']['nom'])
                    <td>{{ $client->{$statistiques['colonnes']['nom']} ?? 'N/A' }}</td>
                @endif
                
                @if(isset($statistiques['colonnes']['tel']) && $statistiques['colonnes']['tel'])
                    <td>{{ $client->{$statistiques['colonnes']['tel']} ?? 'N/A' }}</td>
                @endif
                
                @if(isset($statistiques['colonnes']['email']) && $statistiques['colonnes']['email'])
                    <td>{{ $client->{$statistiques['colonnes']['email']} ?? 'N/A' }}</td>
                @endif
                
                @if(isset($statistiques['colonnes']['fidele']) && $statistiques['colonnes']['fidele'])
                    <td class="text-center">
                        @php
                            $fidele = $client->{$statistiques['colonnes']['fidele']} ?? 0;
                        @endphp
                        <span class="badge {{ $fidele == 1 ? 'badge-success' : 'badge-secondary' }}">
                            {{ $fidele == 1 ? 'Oui' : 'Non' }}
                        </span>
                    </td>
                @endif
                
                @if(isset($statistiques['colonnes']['actif']) && $statistiques['colonnes']['actif'])
                    <td class="text-center">
                        @php
                            $actif = $client->{$statistiques['colonnes']['actif']} ?? 0;
                        @endphp
                        <span class="badge {{ $actif == 1 ? 'badge-success' : 'badge-danger' }}">
                            {{ $actif == 1 ? 'Actif' : 'Inactif' }}
                        </span>
                    </td>
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