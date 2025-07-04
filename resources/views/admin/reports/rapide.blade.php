
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Rapide - AccessPOS Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            padding: 2rem 0;
        }

        .header-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-3px);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #6c757d;
            font-size: 1rem;
        }

        .no-sales {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .action-buttons {
            text-align: center;
            margin-top: 2rem;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header-card">
            <h1 class="mb-3">
                <i class="fas fa-bolt text-warning me-3"></i>
                Rapport Rapide
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar me-2"></i>
                Ventes d'aujourd'hui - {{ $statistiques['periode'] }}
            </p>
        </div>

        @if($sales->count() > 0)
        <!-- Statistiques -->
        <div class="row">
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <div class="stat-value text-success">{{ number_format($statistiques['total_ventes'], 2) }}</div>
                    <div class="stat-label">Total des Ventes (€)</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <div class="stat-value text-info">{{ number_format($statistiques['nombre_factures']) }}</div>
                    <div class="stat-label">Nombre de Factures</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <div class="stat-value text-warning">{{ number_format($statistiques['ticket_moyen'], 2) }}</div>
                    <div class="stat-label">Ticket Moyen (€)</div>
                </div>
            </div>
        </div>

        <!-- Tableau des ventes -->
        <div class="stats-card">
            <h4 class="mb-3">
                <i class="fas fa-list me-2"></i>
                Ventes d'Aujourd'hui
            </h4>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Référence</th>
                            <th>Heure</th>
                            <th>Montant</th>
                            <th>Mode de Paiement</th>
                            <th>Caissier</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                        <tr>
                            <td><span class="badge bg-primary">{{ $sale->FCTV_REF }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($sale->fctv_date)->format('H:i') }}</td>
                            <td><strong>{{ number_format($sale->fctv_mnt_ttc, 2) }} €</strong></td>
                            <td><span class="badge bg-success">{{ $sale->fctv_modepaiement }}</span></td>
                            <td><span class="badge bg-info">{{ $sale->fctv_utilisateur }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <!-- Aucune vente -->
        <div class="no-sales">
            <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
            <h3 class="text-muted">Aucune vente aujourd'hui</h3>
            <p class="text-muted">Il n'y a pas encore de ventes enregistrées pour aujourd'hui.</p>
        </div>
        @endif

        <!-- Boutons d'action -->
        <div class="action-buttons">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-gradient me-3">
                <i class="fas fa-arrow-left me-2"></i>
                Retour aux Rapports
            </a>
            <a href="{{ route('admin.tableau-de-bord-moderne') }}" class="btn btn-outline-light">
                <i class="fas fa-home me-2"></i>
                Tableau de Bord
            </a>
        </div>
    </div>
</body>
</html>