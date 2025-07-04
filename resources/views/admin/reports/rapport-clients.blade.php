
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
            background: linear-gradient(135deg, #fd7e14 0%, #e67e22 100%);
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
            background: linear-gradient(135deg, #fd7e14 0%, #e67e22 100%);
            color: white;
            border: none;
            padding: 15px;
        }
        .btn-action {
            background: linear-gradient(135deg, #fd7e14 0%, #e67e22 100%);
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
                <i class="fas fa-users me-3"></i>
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
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <div class="stat-value text-warning">{{ number_format($statistiques['total_clients']) }}</div>
                    <div class="stat-label">Total Clients</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <div class="stat-value text-success">{{ number_format($statistiques['clients_actifs']) }}</div>
                    <div class="stat-label">Clients Actifs</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <div class="stat-value text-info">{{ number_format($statistiques['clients_fideles']) }}</div>
                    <div class="stat-label">Clients Fidèles</div>
                </div>
            </div>
        </div>

        <!-- Tableau des clients -->
        <div class="table-container">
            <h4 class="mb-3">
                <i class="fas fa-table me-2"></i>
                Liste des Clients
            </h4>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            @if(isset($statistiques['colonnes']['code']) && $statistiques['colonnes']['code'])
                                <th>Code Client</th>
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
                                <th>Fidèle</th>
                            @endif
                            @if(isset($statistiques['colonnes']['actif']) && $statistiques['colonnes']['actif'])
                                <th>Statut</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clients as $client)
                        <tr>
                            @if(isset($statistiques['colonnes']['code']) && $statistiques['colonnes']['code'])
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $client->{$statistiques['colonnes']['code']} ?? 'N/A' }}
                                    </span>
                                </td>
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
                                <td>
                                    @php
                                        $fidele = $client->{$statistiques['colonnes']['fidele']} ?? 0;
                                    @endphp
                                    <span class="badge {{ $fidele == 1 ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $fidele == 1 ? 'Oui' : 'Non' }}
                                    </span>
                                </td>
                            @endif
                            
                            @if(isset($statistiques['colonnes']['actif']) && $statistiques['colonnes']['actif'])
                                <td>
                                    @php
                                        $actif = $client->{$statistiques['colonnes']['actif']} ?? 0;
                                    @endphp
                                    <span class="badge {{ $actif == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $actif == 1 ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                            @endif
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