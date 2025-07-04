
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
            background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
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
        .btn-action {
            background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
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
                <i class="fas fa-utensils me-3"></i>
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
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stat-value text-primary">{{ number_format($statistiques['total_tables']) }}</div>
                    <div class="stat-label">Total Tables</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stat-value text-success">{{ number_format($statistiques['tables_actives']) }}</div>
                    <div class="stat-label">Tables Actives</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stat-value text-info">{{ number_format($statistiques['total_reservations']) }}</div>
                    <div class="stat-label">Réservations</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stat-value text-warning">{{ number_format(($statistiques['tables_actives'] / $statistiques['total_tables']) * 100, 1) }}%</div>
                    <div class="stat-label">Taux d'Occupation</div>
                </div>
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