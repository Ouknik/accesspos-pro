<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Génération de Rapports - AccessPOS</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #3730a3;
            --success-color: #059669;
            --success-light: #10b981;
            --warning-color: #d97706;
            --warning-light: #f59e0b;
            --danger-color: #dc2626;
            --danger-light: #ef4444;
            --info-color: #2563eb;
            --info-light: #3b82f6;
            --dark-color: #111827;
            --dark-secondary: #1f2937;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --light-color: #ffffff;
            --border-light: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius-lg: 0.5rem;
            --radius-xl: 1rem;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: var(--gray-800);
            line-height: 1.6;
            overflow-x: hidden;
            min-height: 100vh;
        }
        
        .dashboard-container {
            min-height: 100vh;
            padding: 0;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            padding: 2rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 300"><path d="M0,150 C300,50 700,250 1000,150 L1000,300 L0,300 Z" fill="rgba(255,255,255,0.1)"/></svg>') no-repeat;
            background-size: cover;
            background-position: bottom;
        }
        
        .dashboard-header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .dashboard-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .dashboard-subtitle {
            font-size: 1.125rem;
            opacity: 0.9;
            font-weight: 400;
        }
        
        .btn-retour {
            background: rgba(255,255,255,0.1);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-lg);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-retour:hover {
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
        }
        
        /* Form Styles */
        .reports-section {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .form-section {
            background: white;
            border-radius: var(--radius-xl);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-light);
        }
        
        .section-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-light);
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }
        
        .section-subtitle {
            color: var(--gray-600);
            font-size: 0.875rem;
        }
        
        .rapport-types-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .rapport-type-card {
            border: 2px solid var(--border-light);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            position: relative;
            overflow: hidden;
        }
        
        .rapport-type-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .rapport-type-card.selected {
            border-color: var(--primary-color);
            background: rgba(79, 70, 229, 0.05);
        }
        
        .rapport-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            color: white;
            font-size: 1.25rem;
        }
        
        .rapport-icon.ventes { background: linear-gradient(135deg, var(--success-color), var(--success-light)); }
        .rapport-icon.stock { background: linear-gradient(135deg, var(--info-color), var(--info-light)); }
        .rapport-icon.clients { background: linear-gradient(135deg, var(--warning-color), var(--warning-light)); }
        .rapport-icon.financier { background: linear-gradient(135deg, var(--danger-color), var(--danger-light)); }
        .rapport-icon.restaurant { background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); }
        
        .rapport-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }
        
        .rapport-description {
            color: var(--gray-600);
            font-size: 0.875rem;
            line-height: 1.5;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-lg);
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-secondary {
            background: var(--gray-100);
            color: var(--gray-700);
        }
        
        .btn-secondary:hover {
            background: var(--gray-200);
        }
        
        .btn-outline {
            background: transparent;
            color: var(--gray-600);
            border: 1px solid var(--border-light);
        }
        
        .btn-outline:hover {
            background: var(--gray-100);
            color: var(--gray-700);
            text-decoration: none;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        
        /* Alert styles */
        .alert-moderne {
            padding: 1rem 1.5rem;
            border-radius: var(--radius-lg);
            margin-bottom: 1.5rem;
            border: 1px solid;
        }
        
        .alert-moderne.success {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.2);
            color: var(--success-color);
        }
        
        .alert-moderne.error {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.2);
            color: var(--danger-color);
        }

        .periode-options {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .radio-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .radio-card {
            border: 2px solid var(--border-light);
            border-radius: var(--radius-lg);
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: var(--light-color);
            position: relative;
        }

        .radio-card input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .radio-card:hover {
            border-color: var(--primary-color);
            box-shadow: var(--shadow-md);
        }

        .radio-card input[type="radio"]:checked + .radio-content {
            color: var(--primary-color);
        }

        .radio-card:has(input[type="radio"]:checked) {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.05), rgba(99, 102, 241, 0.02));
        }

        .radio-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .radio-content i {
            font-size: 1.25rem;
        }

        .radio-title {
            font-weight: 600;
            color: var(--gray-900);
            font-size: 0.95rem;
        }

        .radio-description {
            color: var(--gray-600);
            font-size: 0.8rem;
        }

        .date-inputs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .input-label {
            font-weight: 500;
            color: var(--gray-700);
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .form-input {
            padding: 0.75rem;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            transition: all 0.3s ease;
            background: var(--light-color);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .date-shortcuts {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .shortcut-btn {
            background: var(--gray-100);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-lg);
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
            color: var(--gray-700);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .shortcut-btn:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .format-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }

        .format-card {
            border: 2px solid var(--border-light);
            border-radius: var(--radius-lg);
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: var(--light-color);
            text-align: center;
        }

        .format-card input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .format-card:hover {
            border-color: var(--primary-color);
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .format-card:has(input[type="radio"]:checked) {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.05), rgba(99, 102, 241, 0.02));
        }

        .format-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .format-icon {
            width: 50px;
            height: 50px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
            margin-bottom: 0.5rem;
        }

        .format-icon.view { background: linear-gradient(135deg, var(--info-color), var(--info-light)); }
        .format-icon.pdf { background: linear-gradient(135deg, var(--danger-color), var(--danger-light)); }
        .format-icon.excel { background: linear-gradient(135deg, var(--success-color), var(--success-light)); }
        .format-icon.csv { background: linear-gradient(135deg, var(--warning-color), var(--warning-light)); }

        .format-title {
            font-weight: 600;
            color: var(--gray-900);
            font-size: 0.9rem;
        }

        .format-description {
            color: var(--gray-600);
            font-size: 0.75rem;
        }

        .rapport-features {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .rapport-features li {
            font-size: 0.8rem;
            color: var(--gray-600);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filtres-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .filtres-content .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--gray-500);
        }

        .filtres-content .empty-state i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--gray-300);
        }

        .history-section {
            padding: 0 2rem 2rem;
        }

        .history-grid {
            background: var(--light-color);
            border-radius: var(--radius-xl);
            padding: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-light);
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--gray-500);
        }

        .empty-state i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--gray-300);
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: 1px solid #e9ecef;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .rapport-types-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }

            .reports-section {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
<div class="dashboard-container">
    <!-- En-tête des Rapports -->
    <div class="dashboard-header">
        <div class="dashboard-header-content">
            <div>
                <h1 class="dashboard-title">
                    <i class="fas fa-chart-bar me-2"></i>
                    Génération de Rapports
                </h1>
                <p class="dashboard-subtitle">
                    Créez et téléchargez vos rapports personnalisés - Analyses détaillées
                </p>
            </div>
            <div class="dashboard-meta">
                <div class="live-indicator">
                    <div class="live-dot"></div>
                    <span>Système en temps réel</span>
                </div>
                <div class="user-info" style="color: rgba(255,255,255,0.9); margin-right: 1rem;">
                    <i class="fas fa-user-circle me-1"></i>
                    Bienvenue, {{ Auth::user()->name ?? 'Utilisateur' }}
                </div>
                <a href="{{ route('admin.tableau-de-bord-moderne') }}" class="btn-retour">
                    <i class="fas fa-arrow-left me-1"></i>
                    Retour au Tableau de Bord
                </a>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert-moderne error">
            <i class="fas fa-exclamation-triangle"></i>
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert-moderne success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(isset($tablesInfo))
    <div class="reports-section">
        <div class="form-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="fas fa-database me-2 text-info"></i>
                    État des Données Disponibles
                </h3>
                <p class="section-subtitle">Vérifiez les données avant de générer un rapport</p>
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="stat-value {{ $tablesInfo['ventes']['count'] > 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($tablesInfo['ventes']['count']) }}
                        </div>
                        <div class="stat-label">Factures de Vente</div>
                        @if($tablesInfo['ventes']['count'] > 0 && $tablesInfo['ventes']['date_range'])
                            <small class="text-muted">
                                Du {{ \Carbon\Carbon::parse($tablesInfo['ventes']['date_range']['min'])->format('d/m/Y') }}
                                au {{ \Carbon\Carbon::parse($tablesInfo['ventes']['date_range']['max'])->format('d/m/Y') }}
                            </small>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="stat-value {{ $tablesInfo['articles']['count'] > 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($tablesInfo['articles']['count']) }}
                        </div>
                        <div class="stat-label">Articles</div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="stat-value {{ $tablesInfo['clients']['count'] > 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($tablesInfo['clients']['count']) }}
                        </div>
                        <div class="stat-label">Clients</div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="stat-value {{ $tablesInfo['tables']['count'] > 0 ? 'text-success' : 'text-warning' }}">
                            {{ number_format($tablesInfo['tables']['count']) }}
                        </div>
                        <div class="stat-label">Tables Restaurant</div>
                    </div>
                </div>
            </div>
            
            @if($tablesInfo['ventes']['count'] == 0 && $tablesInfo['articles']['count'] == 0 && $tablesInfo['clients']['count'] == 0)
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Attention:</strong> Aucune donnée n'est disponible dans la base de données. 
                    Veuillez d'abord ajouter des articles, des clients et effectuer des ventes pour générer des rapports.
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Suggestions de Rapports -->
    @if(isset($suggestions) && count($suggestions) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        Suggestions de Rapports
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($suggestions as $suggestion)
                        <div class="col-md-6">
                            <div class="alert alert-{{ $suggestion['type'] }} border-0 shadow-sm mb-0">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="alert-heading mb-1">{{ $suggestion['title'] }}</h6>
                                        <p class="mb-0 small">{{ $suggestion['message'] }}</p>
                                    </div>
                                    <a href="{{ $suggestion['action']['url'] }}" class="btn btn-outline-{{ $suggestion['type'] }} btn-sm">
                                        {{ $suggestion['action']['text'] }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Formulaire de Génération -->
    <div class="reports-section">
        <form action="{{ route('admin.reports.generate') }}" method="POST" id="reportForm" class="report-form">
            @csrf
            
            <!-- Sélection du Type de Rapport -->
            <div class="form-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-file-chart me-2 text-primary"></i>
                        Type de Rapport
                    </h3>
                    <p class="section-subtitle">Choisissez le type d'analyse que vous souhaitez générer</p>
                </div>
                
                <div class="rapport-types-grid">
                    <div class="rapport-type-card" data-type="ventes">
                        <div class="rapport-icon ventes">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h4 class="rapport-title">Rapport des Ventes</h4>
                        <p class="rapport-description">Analyse détaillée des ventes, CA, tickets moyens et tendances</p>
                        <ul class="rapport-features">
                            <li><i class="fas fa-check text-success"></i> Chiffre d'affaires par période</li>
                            <li><i class="fas fa-check text-success"></i> Articles les plus vendus</li>
                            <li><i class="fas fa-check text-success"></i> Modes de paiement</li>
                            <li><i class="fas fa-check text-success"></i> Performance par heure</li>
                        </ul>
                    </div>

                    <div class="rapport-type-card" data-type="stock">
                        <div class="rapport-icon stock">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <h4 class="rapport-title">Rapport du Stock</h4>
                        <p class="rapport-description">État des stocks, mouvements et valorisation des articles</p>
                        <ul class="rapport-features">
                            <li><i class="fas fa-check text-success"></i> Inventaire détaillé</li>
                            <li><i class="fas fa-check text-success"></i> Articles en rupture</li>
                            <li><i class="fas fa-check text-success"></i> Valorisation du stock</li>
                            <li><i class="fas fa-check text-success"></i> Mouvements par période</li>
                        </ul>
                    </div>

                    <div class="rapport-type-card" data-type="clients">
                        <div class="rapport-icon clients">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4 class="rapport-title">Rapport des Clients</h4>
                        <p class="rapport-description">Analyse de la clientèle et programme de fidélité</p>
                        <ul class="rapport-features">
                            <li><i class="fas fa-check text-success"></i> Top clients</li>
                            <li><i class="fas fa-check text-success"></i> Comportement d'achat</li>
                            <li><i class="fas fa-check text-success"></i> Points de fidélité</li>
                            <li><i class="fas fa-check text-success"></i> Segmentation clientèle</li>
                        </ul>
                    </div>

                    <div class="rapport-type-card" data-type="financier">
                        <div class="rapport-icon financier">
                            <i class="fas fa-euro-sign"></i>
                        </div>
                        <h4 class="rapport-title">Rapport Financier</h4>
                        <p class="rapport-description">Analyse financière complète et rentabilité</p>
                        <ul class="rapport-features">
                            <li><i class="fas fa-check text-success"></i> Encaissements vs Dépenses</li>
                            <li><i class="fas fa-check text-success"></i> Évolution du CA</li>
                            <li><i class="fas fa-check text-success"></i> État des caisses</li>
                            <li><i class="fas fa-check text-success"></i> Indicateurs de rentabilité</li>
                        </ul>
                    </div>

                    <div class="rapport-type-card" data-type="restaurant">
                        <div class="rapport-icon restaurant">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h4 class="rapport-title">Rapport Restaurant</h4>
                        <p class="rapport-description">Gestion des tables, réservations et services</p>
                        <ul class="rapport-features">
                            <li><i class="fas fa-check text-success"></i> Occupation des tables</li>
                            <li><i class="fas fa-check text-success"></i> Réservations</li>
                            <li><i class="fas fa-check text-success"></i> CA par table</li>
                            <li><i class="fas fa-check text-success"></i> Temps de service</li>
                        </ul>
                    </div>
                </div>
                
                <input type="hidden" name="type_rapport" id="type_rapport" required>
            </div>

            <!-- Configuration de la Période -->
            <div class="form-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                        Période d'Analyse
                    </h3>
                    <p class="section-subtitle">Définissez la période pour votre rapport</p>
                </div>

                <div class="periode-options">
                    <div class="radio-group">
                        <label class="radio-card">
                            <input type="radio" name="periode_type" value="jour" checked>
                            <div class="radio-content">
                                <i class="fas fa-calendar-day text-info"></i>
                                <span class="radio-title">Jour Spécifique</span>
                                <span class="radio-description">Rapport pour une date précise</span>
                            </div>
                        </label>

                        <label class="radio-card">
                            <input type="radio" name="periode_type" value="periode">
                            <div class="radio-content">
                                <i class="fas fa-calendar-week text-warning"></i>
                                <span class="radio-title">Période Personnalisée</span>
                                <span class="radio-description">Du ... au ... (plage de dates)</span>
                            </div>
                        </label>
                    </div>

                    <div class="date-inputs">
                        <div class="input-group">
                            <label for="date_debut" class="input-label">
                                <i class="fas fa-calendar me-1"></i>
                                Date de Début
                            </label>
                            <input type="date" 
                                   id="date_debut" 
                                   name="date_debut" 
                                   class="form-input" 
                                   value="{{ date('Y-m-d') }}" 
                                   required>
                        </div>

                        <div class="input-group" id="date_fin_group" style="display: none;">
                            <label for="date_fin" class="input-label">
                                <i class="fas fa-calendar me-1"></i>
                                Date de Fin
                            </label>
                            <input type="date" 
                                   id="date_fin" 
                                   name="date_fin" 
                                   class="form-input" 
                                   value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <!-- Raccourcis de Dates -->
                    <div class="date-shortcuts">
                        <button type="button" class="shortcut-btn" data-period="today">
                            <i class="fas fa-calendar-day"></i>
                            Aujourd'hui
                        </button>
                        <button type="button" class="shortcut-btn" data-period="yesterday">
                            <i class="fas fa-calendar-minus"></i>
                            Hier
                        </button>
                        <button type="button" class="shortcut-btn" data-period="week">
                            <i class="fas fa-calendar-week"></i>
                            Cette Semaine
                        </button>
                        <button type="button" class="shortcut-btn" data-period="month">
                            <i class="fas fa-calendar"></i>
                            Ce Mois
                        </button>
                        <button type="button" class="shortcut-btn" data-period="lastMonth">
                            <i class="fas fa-calendar-times"></i>
                            Mois Dernier
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filtres Avancés -->
            <div class="form-section" id="filtres-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-filter me-2 text-primary"></i>
                        Filtres Avancés
                        <span class="badge badge-info ms-2">Optionnel</span>
                    </h3>
                    <p class="section-subtitle">Affinez votre rapport avec des critères spécifiques</p>
                </div>

                <div class="filtres-content" id="filtres-content">
                    <div class="empty-state">
                        <i class="fas fa-info-circle"></i>
                        <h3>Sélectionnez un type de rapport</h3>
                        <p>Les filtres apparaîtront selon votre choix</p>
                    </div>
                </div>
            </div>

            <!-- Options d'Export -->
            <div class="form-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-download me-2 text-primary"></i>
                        Format d'Export
                    </h3>
                    <p class="section-subtitle">Choisissez le format de téléchargement</p>
                </div>

                <div class="format-options">
                    <label class="format-card">
                        <input type="radio" name="format" value="view" checked>
                        <div class="format-content">
                            <div class="format-icon view">
                                <i class="fas fa-eye"></i>
                            </div>
                            <span class="format-title">Affichage Web</span>
                            <span class="format-description">Visualiser dans le navigateur</span>
                        </div>
                    </label>

                    <label class="format-card">
                        <input type="radio" name="format" value="pdf">
                        <div class="format-content">
                            <div class="format-icon pdf">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <span class="format-title">PDF</span>
                            <span class="format-description">Document formaté et imprimable</span>
                        </div>
                    </label>

                    <label class="format-card">
                        <input type="radio" name="format" value="excel">
                        <div class="format-content">
                            <div class="format-icon excel">
                                <i class="fas fa-file-excel"></i>
                            </div>
                            <span class="format-title">Excel</span>
                            <span class="format-description">Feuille de calcul pour analyse</span>
                        </div>
                    </label>

                    <label class="format-card">
                        <input type="radio" name="format" value="csv">
                        <div class="format-content">
                            <div class="format-icon csv">
                                <i class="fas fa-file-csv"></i>
                            </div>
                            <span class="format-title">CSV</span>
                            <span class="format-description">Données brutes pour import</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Boutons d'Action -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-generate" id="btn-generate">
                    <i class="fas fa-chart-line me-2"></i>
                    <span class="btn-text">Générer le Rapport</span>
                    <div class="loading-spinner" style="display: none;"></div>
                </button>

                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                    <i class="fas fa-undo me-2"></i>
                    Réinitialiser
                </button>

                <a href="{{ route('admin.tableau-de-bord-moderne') }}" class="btn btn-outline">
                    <i class="fas fa-times me-2"></i>
                    Annuler
                </a>
            </div>
        </form>
    </div>

    <!-- Historique des Rapports -->
    <div class="history-section">
        <div class="section-header">
            <h3 class="section-title">
                <i class="fas fa-history me-2 text-primary"></i>
                Rapports Récents
            </h3>
            <p class="section-subtitle">Accédez rapidement à vos derniers rapports générés</p>
        </div>

        <div class="history-grid" id="history-grid">
            <div class="empty-state">
                <i class="fas fa-file-alt"></i>
                <h3>Aucun Rapport Récent</h3>
                <p>Commencez par générer votre premier rapport ci-dessus</p>
            </div>
        </div>
    </div>
</div>

<!-- Scripts JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sélection du type de rapport
    const rapportCards = document.querySelectorAll('.rapport-type-card');
    const typeRapportInput = document.getElementById('type_rapport');
    
    rapportCards.forEach(card => {
        card.addEventListener('click', function() {
            rapportCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            typeRapportInput.value = this.dataset.type;
            updateFiltres(this.dataset.type);
        });
    });

    // Gestion des périodes
    const periodeRadios = document.querySelectorAll('input[name="periode_type"]');
    const dateFinGroup = document.getElementById('date_fin_group');
    
    periodeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'periode') {
                dateFinGroup.style.display = 'block';
                document.getElementById('date_fin').required = true;
            } else {
                dateFinGroup.style.display = 'none';
                document.getElementById('date_fin').required = false;
            }
        });
    });

    // Raccourcis de dates
    const shortcutBtns = document.querySelectorAll('.shortcut-btn');
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    
    shortcutBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const period = this.dataset.period;
            const today = new Date();
            
            switch(period) {
                case 'today':
                    dateDebut.value = formatDate(today);
                    document.querySelector('input[name="periode_type"][value="jour"]').checked = true;
                    dateFinGroup.style.display = 'none';
                    break;
                    
                case 'yesterday':
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    dateDebut.value = formatDate(yesterday);
                    document.querySelector('input[name="periode_type"][value="jour"]').checked = true;
                    dateFinGroup.style.display = 'none';
                    break;
                    
                case 'week':
                    const startOfWeek = new Date(today);
                    startOfWeek.setDate(today.getDate() - today.getDay() + 1);
                    dateDebut.value = formatDate(startOfWeek);
                    dateFin.value = formatDate(today);
                    document.querySelector('input[name="periode_type"][value="periode"]').checked = true;
                    dateFinGroup.style.display = 'block';
                    document.getElementById('date_fin').required = true;
                    break;
                    
                case 'month':
                    const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                    dateDebut.value = formatDate(startOfMonth);
                    dateFin.value = formatDate(today);
                    document.querySelector('input[name="periode_type"][value="periode"]').checked = true;
                    dateFinGroup.style.display = 'block';
                    document.getElementById('date_fin').required = true;
                    break;
                    
                case 'lastMonth':
                    const startOfLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    const endOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                    dateDebut.value = formatDate(startOfLastMonth);
                    dateFin.value = formatDate(endOfLastMonth);
                    document.querySelector('input[name="periode_type"][value="periode"]').checked = true;
                    dateFinGroup.style.display = 'block';
                    document.getElementById('date_fin').required = true;
                    break;
            }
        });
    });

    // Soumission du formulaire
    const form = document.getElementById('reportForm');
    const btnGenerate = document.getElementById('btn-generate');
    
    form.addEventListener('submit', function(e) {
        if (!typeRapportInput.value) {
            e.preventDefault();
            alert('Veuillez sélectionner un type de rapport');
            return;
        }

        // Animation de chargement
        const btnText = btnGenerate.querySelector('.btn-text');
        const spinner = btnGenerate.querySelector('.loading-spinner');
        
        btnText.textContent = 'Génération en cours...';
        spinner.style.display = 'inline-block';
        btnGenerate.disabled = true;
    });
});

function formatDate(date) {
    return date.toISOString().split('T')[0];
}

function updateFiltres(typeRapport) {
    const filtresContent = document.getElementById('filtres-content');
    
    switch(typeRapport) {
        case 'ventes':
            filtresContent.innerHTML = `
                <div class="filtres-grid">
                    <div class="input-group">
                        <label class="input-label">
                            <i class="fas fa-user-tie me-1"></i>
                            Caissier
                        </label>
                        <select name="caissier" class="form-input">
                            <option value="">Tous les caissiers</option>
                            <option value="admin">Administrateur</option>
                            <option value="caissier1">Caissier 1</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label class="input-label">
                            <i class="fas fa-credit-card me-1"></i>
                            Mode de Paiement
                        </label>
                        <select name="mode_paiement" class="form-input">
                            <option value="">Tous les modes</option>
                            <option value="especes">Espèces</option>
                            <option value="carte">Carte Bancaire</option>
                            <option value="cheque">Chèque</option>
                        </select>
                    </div>
                </div>
            `;
            break;
            
        case 'stock':
            filtresContent.innerHTML = `
                <div class="filtres-grid">
                    <div class="input-group">
                        <label class="input-label">
                            <i class="fas fa-tags me-1"></i>
                            Famille d'Articles
                        </label>
                        <select name="famille" class="form-input">
                            <option value="">Toutes les familles</option>
                            <option value="boissons">Boissons</option>
                            <option value="plats">Plats</option>
                            <option value="desserts">Desserts</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label class="input-label">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Stock Minimum
                        </label>
                        <input type="number" name="stock_minimum" class="form-input" placeholder="Seuil d'alerte">
                    </div>
                </div>
            `;
            break;
            
        case 'clients':
            filtresContent.innerHTML = `
                <div class="filtres-grid">
                    <div class="input-group">
                        <label class="input-label">
                            <i class="fas fa-star me-1"></i>
                            Clients Fidèles Uniquement
                        </label>
                        <select name="fideles_only" class="form-input">
                            <option value="">Tous les clients</option>
                            <option value="1">Clients fidèles seulement</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label class="input-label">
                            <i class="fas fa-euro-sign me-1"></i>
                            CA Minimum
                        </label>
                        <input type="number" name="ca_minimum" class="form-input" placeholder="Montant minimum">
                    </div>
                </div>
            `;
            break;
            
        default:
            filtresContent.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-info-circle"></i>
                    <p>Aucun filtre spécifique disponible pour ce type de rapport</p>
                </div>
            `;
    }
}

function resetForm() {
    document.getElementById('reportForm').reset();
    document.querySelectorAll('.rapport-type-card').forEach(card => {
        card.classList.remove('selected');
    });
    document.getElementById('type_rapport').value = '';
    document.getElementById('date_fin_group').style.display = 'none';
    document.getElementById('filtres-content').innerHTML = `
        <div class="empty-state">
            <i class="fas fa-info-circle"></i>
            <h3>Sélectionnez un type de rapport</h3>
            <p>Les filtres apparaîtront selon votre choix</p>
        </div>
    `;
}
</script>
</body>
</html>
