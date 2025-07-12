<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Administrateur - AccessPOS</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/modals-avancees.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    
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
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --light-color: #ffffff;
            --border-color: #e5e7eb;
            --border-light: #f3f4f6;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --radius-2xl: 1.5rem;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #667eea 100%);
            background-attachment: fixed;
            min-height: 100vh;
            line-height: 1.6;
            color: var(--gray-700);
            font-size: 0.875rem;
            padding: 1rem;
        }
        
        .dashboard-container {
            background: var(--light-color);
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            max-width: 1400px;
            margin: 0 auto;
            min-height: calc(100vh - 2rem);
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 50%, var(--dark-color) 100%);
            color: white;
            padding: 2rem 2.5rem;
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
            background: radial-gradient(circle at 30% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(255,255,255,0.05) 0%, transparent 40%);
            pointer-events: none;
        }
        
        .dashboard-header-content {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .dashboard-title {
            font-size: clamp(1.75rem, 4vw, 2.5rem);
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .dashboard-subtitle {
            opacity: 0.9;
            font-size: clamp(0.95rem, 2vw, 1.1rem);
            font-weight: 400;
        }
        
        .dashboard-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.5rem;
        }
        
        .live-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.1);
            padding: 0.5rem 1rem;
            border-radius: var(--radius-lg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .live-dot {
            width: 8px;
            height: 8px;
            background: var(--success-light);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            padding: 2rem 2.5rem;
        }
        
        .stat-card {
            background: var(--light-color);
            border-radius: var(--radius-xl);
            padding: 1.75rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-light);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            height: fit-content;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
            border-color: var(--border-color);
        }
        
        .stat-card:hover::before {
            transform: scaleX(1);
        }
        
        .stat-card.financiere::before { 
            background: linear-gradient(90deg, var(--success-color), var(--success-light)); 
        }
        .stat-card.stock::before { 
            background: linear-gradient(90deg, var(--info-color), var(--info-light)); 
        }
        .stat-card.clientele::before { 
            background: linear-gradient(90deg, var(--warning-color), var(--warning-light)); 
        }
        .stat-card.restaurant::before { 
            background: linear-gradient(90deg, var(--danger-color), var(--danger-light)); 
        }
        
        .stat-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: var(--radius-xl);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        
        .stat-icon::before {
            content: '';
            position: absolute;
            inset: 0;
            background: inherit;
            opacity: 0.1;
            border-radius: inherit;
        }
        
        .stat-icon.financiere { 
            background: linear-gradient(135deg, var(--success-color), var(--success-light));
            color: white;
        }
        .stat-icon.stock { 
            background: linear-gradient(135deg, var(--info-color), var(--info-light));
            color: white;
        }
        .stat-icon.clientele { 
            background: linear-gradient(135deg, var(--warning-color), var(--warning-light));
            color: white;
        }
        .stat-icon.restaurant { 
            background: linear-gradient(135deg, var(--danger-color), var(--danger-light));
            color: white;
        }
        
        .stat-title {
            font-weight: 600;
            color: var(--gray-800);
            margin: 0;
            font-size: 1rem;
            line-height: 1.4;
        }
        
        .stat-content {
            flex: 1;
        }
        
        .stat-value {
            font-size: clamp(1.5rem, 3vw, 2.25rem);
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
            line-height: 1.2;
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
        }
        
        .stat-currency {
            font-size: 0.7em;
            color: var(--gray-600);
            font-weight: 500;
        }
        
        .stat-label {
            color: var(--gray-600);
            font-size: 0.875rem;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        
        .stat-trend {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .trend-value {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-lg);
            font-size: 0.8rem;
        }
        
        .trend-positive { 
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }
        .trend-negative { 
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }
        .trend-neutral { 
            background: var(--gray-100);
            color: var(--gray-600);
        }
        
        .trend-period {
            color: var(--gray-500);
            font-size: 0.75rem;
        }
        
        .charts-section {
            padding: 0 2.5rem 2rem;
        }
        
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .chart-container {
            background: var(--light-color);
            border-radius: var(--radius-xl);
            padding: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-light);
            transition: all 0.3s ease;
            height: 400px;
            display: flex;
            flex-direction: column;
        }
        
        .chart-container:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }
        
        .chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-light);
            flex-shrink: 0;
        }
        
        .chart-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-900);
            margin: 0;
        }
        
        .chart-subtitle {
            color: var(--gray-600);
            font-size: 0.875rem;
            margin: 0;
            margin-top: 0.25rem;
        }
        
        .chart-content {
            flex: 1;
            position: relative;
            min-height: 250px;
        }
        
        .chart-canvas {
            width: 100% !important;
            height: 100% !important;
        }
        
        .tables-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            padding: 0 2.5rem 2.5rem;
        }
        
        .table-container {
            background: var(--light-color);
            border-radius: var(--radius-xl);
            padding: 1.75rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-light);
            transition: all 0.3s ease;
        }
        
        .table-container:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }
        
        .table-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-light);
        }
        
        .table-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-900);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .table-title i {
            color: var(--primary-color);
        }
        
        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        
        .custom-table th {
            background: var(--gray-50);
            padding: 0.875rem;
            text-align: left;
            font-weight: 600;
            color: var(--gray-800);
            border-bottom: 1px solid var(--border-color);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        
        .custom-table th:first-child {
            border-top-left-radius: var(--radius-md);
        }
        
        .custom-table th:last-child {
            border-top-right-radius: var(--radius-md);
        }
        
        .custom-table td {
            padding: 0.875rem;
            border-bottom: 1px solid var(--border-light);
            font-size: 0.875rem;
            color: var(--gray-700);
            vertical-align: middle;
        }
        
        .custom-table tbody tr:hover {
            background: var(--gray-50);
        }
        
        .custom-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .custom-table tbody tr:last-child td:first-child {
            border-bottom-left-radius: var(--radius-md);
        }
        
        .custom-table tbody tr:last-child td:last-child {
            border-bottom-right-radius: var(--radius-md);
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.875rem;
            border-radius: var(--radius-lg);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            border: 1px solid transparent;
        }
        
        .badge-success { 
            background: rgba(16, 185, 129, 0.1); 
            color: var(--success-color);
            border-color: rgba(16, 185, 129, 0.2);
        }
        .badge-warning { 
            background: rgba(245, 158, 11, 0.1); 
            color: var(--warning-color);
            border-color: rgba(245, 158, 11, 0.2);
        }
        .badge-danger { 
            background: rgba(239, 68, 68, 0.1); 
            color: var(--danger-color);
            border-color: rgba(239, 68, 68, 0.2);
        }
        .badge-info { 
            background: rgba(59, 130, 246, 0.1); 
            color: var(--info-color);
            border-color: rgba(59, 130, 246, 0.2);
        }
        
        .loading-state {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            color: var(--gray-600);
            flex-direction: column;
            gap: 1rem;
        }
        
        .loading-spinner {
            width: 32px;
            height: 32px;
            border: 3px solid var(--gray-200);
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .alert-moderne {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.05));
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--danger-color);
            padding: 1.5rem;
            border-radius: var(--radius-xl);
            margin: 2rem 2.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: var(--shadow-md);
        }
        
        .alert-moderne i {
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
            color: var(--gray-500);
            text-align: center;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--gray-300);
        }
        
        .empty-state h3 {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--gray-700);
        }
        
        .empty-state p {
            font-size: 0.875rem;
            color: var(--gray-500);
        }
        
        /* Responsive Design */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            }
            
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .chart-container {
                height: 350px;
            }
        }
        
        @media (max-width: 768px) {
            body { 
                padding: 0.5rem; 
                font-size: 0.8rem;
            }
            
            .dashboard-container {
                border-radius: var(--radius-xl);
                min-height: calc(100vh - 1rem);
            }
            
            .dashboard-header {
                padding: 1.5rem;
            }
            
            .dashboard-header-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .dashboard-meta {
                align-items: flex-start;
                width: 100%;
            }
            
            .live-indicator {
                align-self: flex-end;
            }
            
            .dashboard-title { 
                font-size: 1.75rem; 
                line-height: 1.2;
            }
            
            .dashboard-subtitle {
                font-size: 0.95rem;
            }
            
            .stats-grid { 
                grid-template-columns: 1fr; 
                padding: 1.5rem;
                gap: 1rem;
            }
            
            .stat-card {
                padding: 1.25rem;
            }
            
            .stat-icon {
                width: 48px;
                height: 48px;
                font-size: 1.25rem;
            }
            
            .stat-value {
                font-size: 1.75rem;
            }
            
            .tables-section { 
                grid-template-columns: 1fr; 
                padding: 0 1.5rem 1.5rem;
                gap: 1rem;
            }
            
            .charts-section { 
                padding: 0 1.5rem 1.5rem; 
            }
            
            .charts-grid {
                gap: 1rem;
            }
            
            .chart-container {
                padding: 1.25rem;
                height: 300px;
            }
            
            .table-container {
                padding: 1.25rem;
            }
            
            .custom-table {
                font-size: 0.8rem;
            }
            
            .custom-table th,
            .custom-table td {
                padding: 0.625rem 0.5rem;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 0.25rem;
            }
            
            .dashboard-container {
                border-radius: var(--radius-lg);
            }
            
            .dashboard-header {
                padding: 1rem;
            }
            
            .dashboard-title {
                font-size: 1.5rem;
            }
            
            .dashboard-subtitle {
                font-size: 0.875rem;
            }
            
            .stats-grid {
                padding: 1rem;
            }
            
            .stat-card {
                padding: 1rem;
            }
            
            .stat-header {
                margin-bottom: 1rem;
            }
            
            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 1.125rem;
                margin-right: 0.75rem;
            }
            
            .stat-value {
                font-size: 1.5rem;
            }
            
            .charts-section,
            .tables-section {
                padding: 0 1rem 1rem;
            }
            
            .chart-container,
            .table-container {
                padding: 1rem;
            }
            
            .chart-container {
                height: 250px;
            }
            
            .live-indicator {
                padding: 0.375rem 0.75rem;
                font-size: 0.8rem;
            }
            
            .trend-value {
                padding: 0.2rem 0.5rem;
                font-size: 0.7rem;
            }
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .dashboard-container {
                box-shadow: none;
                border-radius: 0;
            }
            
            .dashboard-header {
                background: var(--gray-800) !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .stat-card,
            .chart-container,
            .table-container {
                break-inside: avoid;
                box-shadow: none;
                border: 1px solid var(--border-color);
            }
        }
        
        /* High contrast mode */
        @media (prefers-contrast: high) {
            .stat-card,
            .chart-container,
            .table-container {
                border-width: 2px;
            }
            
            .badge {
                border-width: 2px;
            }
        }
        
        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
            
            .loading-spinner {
                animation: none;
            }
            
            .live-dot {
                animation: none;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- En-tête du Tableau de Bord -->
        <div class="dashboard-header">
            <div class="dashboard-header-content">
                <div>
                    <h1 class="dashboard-title">
                        <i class="fas fa-chart-line me-2"></i>
                        Tableau de Bord Administrateur
                    </h1>
                    <p class="dashboard-subtitle">
                        Surveillance et analyse en temps réel - AccessPOS Dashboard
                    </p>
                </div>
                <div class="dashboard-meta">
                    <div class="dashboard-actions" style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
    <a href="{{ route('admin.reports.index') }}" 
       class="dashboard-btn dashboard-btn-primary"
       style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: white; padding: 0.6rem 1.2rem; border-radius: 0.6rem; font-size: 0.875rem; text-decoration: none; transition: all 0.3s ease; display: inline-flex; align-items: center; font-weight: 500;" 
       onmouseover="this.style.background='rgba(255,255,255,0.25)'; this.style.transform='translateY(-2px)'" 
       onmouseout="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(0)'">
        <i class="fas fa-chart-bar me-2"></i>
        Rapports Détaillés
    </a>
    
    <a href="{{ route('admin.reports.index') }}?quick=today" 
       class="dashboard-btn dashboard-btn-secondary"
       style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.25); color: rgba(255,255,255,0.9); padding: 0.6rem 1.2rem; border-radius: 0.6rem; font-size: 0.875rem; text-decoration: none; transition: all 0.3s ease; display: inline-flex; align-items: center;" 
       onmouseover="this.style.background='rgba(255,255,255,0.2)'" 
       onmouseout="this.style.background='rgba(255,255,255,0.1)'">
        <i class="fas fa-calendar-day me-2"></i>
        Rapport du Jour
    </a>
</div>
                    <div class="user-info" style="color: rgba(255,255,255,0.9); margin-right: 1rem;">
                        <i class="fas fa-user-circle me-1"></i>
                        Bienvenue, {{ Auth::user()->name ?? 'Utilisateur' }}
                        <small style="display: block; font-size: 0.8em; opacity: 0.8;">{{ Auth::user()->role_name ?? 'Utilisateur' }}</small>
                    </div>
                    <div class="live-indicator">
                        <div class="live-dot"></div>
                        <span>Données en direct</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="margin-left: 1rem;">
                        @csrf
                        <button type="submit" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.3s ease;" 
                                onmouseover="this.style.background='rgba(255,255,255,0.2)'" 
                                onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <i class="fas fa-sign-out-alt me-1"></i>
                            Déconnexion
                        </a>
                    </form>
                    <div style="color: rgba(255,255,255,0.8); font-size: 0.875rem;">
                        Dernière mise à jour: {{ now()->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        @if(isset($erreur))
            <div class="alert-moderne">
                <i class="fas fa-exclamation-triangle"></i>
                {{ $erreur }}
            </div>
        @endif

        <!-- Statistiques Principales -->
        <div class="stats-grid">
            <!-- Statistiques Financières -->
            <div class="stat-card financiere">
                <div class="stat-header">
                    <div class="stat-icon financiere">
                        <i class="fas fa-euro-sign"></i>
                    </div>
                    <h3 class="stat-title">Chiffre d'Affaires du Jour</h3>
                </div>
                <div class="stat-value">{{ number_format($statistiquesFinancieres['ca_du_jour'] ?? 0, 2) }} DH</div>
                <div class="stat-label">Ventes journalières</div>
                <div class="stat-trend trend-positive">
                    <i class="fas fa-arrow-up me-1"></i>
                    +{{ number_format($statistiquesFinancieres['evolution_ventes'] ?? 0, 1) }}% vs mois dernier
                </div>
                <div class="stat-actions" style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                    <a href="{{ route('admin.dashboard.chiffre-affaires') }}" target="_blank"
                            class="btn-details" 
                            style="flex: 1; padding: 0.5rem; border: 1px solid var(--success-color); background: transparent; color: var(--success-color); border-radius: 0.4rem; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-external-link-alt me-1"></i>
                        Voir détails
                    </a>
                    <a onclick="exportData('chiffre-affaires', 'pdf')" 
                            class="btn-export" 
                            style="padding: 0.5rem 0.75rem; border: 1px solid var(--success-light); background: var(--success-light); color: white; border-radius: 0.4rem; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>

            <div class="stat-card financiere">
                <div class="stat-header">
                    <div class="stat-icon financiere">
                        <i class="fas fa-calendar-month"></i>
                    </div>
                    <h3 class="stat-title">CA du Mois</h3>
                </div>
                <div class="stat-value">{{ number_format($statistiquesFinancieres['ca_du_mois'] ?? 0, 2) }} DH</div>
                <div class="stat-label">Cumul mensuel</div>
                <div class="stat-trend trend-neutral">
                    <i class="fas fa-receipt me-1"></i>
                    {{ number_format($statistiquesFinancieres['nb_factures_jour'] ?? 0) }} factures aujourd'hui
                </div>
                <div class="stat-actions" style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                    <a href="{{ route('admin.dashboard.chiffre-affaires') }}?periode=mois" target="_blank"
                            class="btn-details" 
                            style="flex: 1; padding: 0.5rem; border: 1px solid var(--info-color); background: transparent; color: var(--info-color); border-radius: 0.4rem; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-external-link-alt me-1"></i>
                        Voir détails
                    </a>
                    <a onclick="exportData('ca-mois', 'pdf')" 
                            class="btn-export" 
                            style="padding: 0.5rem 0.75rem; border: 1px solid var(--info-light); background: var(--info-light); color: white; border-radius: 0.4rem; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>

            <div class="stat-card financiere">
                <div class="stat-header">
                    <div class="stat-icon financiere">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3 class="stat-title">Ticket Moyen</h3>
                </div>
                <div class="stat-value">{{ number_format($statistiquesFinancieres['ticket_moyen'] ?? 0, 2) }} DH</div>
                <div class="stat-label">Panier moyen du jour</div>
                <div class="stat-trend trend-positive">
                    <i class="fas fa-chart-line me-1"></i>
                    Performance optimale
                </div>
                <div class="stat-actions" style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                    <a href="{{ route('admin.dashboard.chiffre-affaires') }}?type=ticket-moyen" target="_blank"
                            class="btn-details" 
                            style="flex: 1; padding: 0.5rem; border: 1px solid var(--success-color); background: transparent; color: var(--success-color); border-radius: 0.4rem; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-external-link-alt me-1"></i>
                        Voir détails
                    </a>
                    <a onclick="exportData('ticket-moyen', 'pdf')" 
                            class="btn-export" 
                            style="padding: 0.5rem 0.75rem; border: 1px solid var(--success-light); background: var(--success-light); color: white; border-radius: 0.4rem; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>

            <!-- Gestion des Stocks -->
            <div class="stat-card stock">
                <div class="stat-header">
                    <div class="stat-icon stock">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h3 class="stat-title">Articles en Stock</h3>
                </div>
                <div class="stat-value">{{ number_format($gestionStocks['nb_total_articles'] ?? 0) }}</div>
                <div class="stat-label">Total des références</div>
                <div class="stat-trend trend-warning">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    {{ number_format($gestionStocks['articles_rupture'] ?? 0) }} en rupture
                </div>
                <div class="stat-actions" style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                    <a href="{{ route('admin.dashboard.stock-rupture') }}" target="_blank"
                            class="btn-details" 
                            style="flex: 1; padding: 0.5rem; border: 1px solid var(--warning-color); background: transparent; color: var(--warning-color); border-radius: 0.4rem; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-external-link-alt me-1"></i>
                        Articles en rupture
                    </a>
                </div>
                <div class="stat-actions" style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                    <a href="{{ route('admin.dashboard.stock-rupture') }}?type=stock" target="_blank" 
                            class="btn-details" 
                            style="flex: 1; padding: 0.5rem; border: 1px solid var(--info-color); background: transparent; color: var(--info-color); border-radius: 0.4rem; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-box-open me-1"></i>
                        Voir détails
                    </a>
                    <a onclick="exportData('articles-stock', 'pdf')" 
                            class="btn-export" 
                            style="padding: 0.5rem 0.75rem; border: 1px solid var(--info-light); background: var(--info-light); color: white; border-radius: 0.4rem; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>

            <div class="stat-card stock">
                <div class="stat-header">
                    <div class="stat-icon stock">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <h3 class="stat-title">Valeur du Stock</h3>
                </div>
                <div class="stat-value">{{ number_format($gestionStocks['valeur_stock'] ?? 0, 2) }} DH</div>
                <div class="stat-label">Inventaire total</div>
                <div class="stat-trend trend-warning">
                    <i class="fas fa-low-vision me-1"></i>
                    {{ number_format($gestionStocks['articles_stock_faible'] ?? 0) }} en stock faible
                </div>
                <div class="stat-actions" style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                    <a href="{{ route('admin.dashboard.stock-rupture') }}?type=valeur" target="_blank" 
                            class="btn-details" 
                            style="flex: 1; padding: 0.5rem; border: 1px solid var(--info-color); background: transparent; color: var(--info-color); border-radius: 0.4rem; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-line me-1"></i>
                        Voir détails
                    </a>
                    <a onclick="exportData('valeur-stock', 'pdf')" 
                            class="btn-export" 
                            style="padding: 0.5rem 0.75rem; border: 1px solid var(--info-light); background: var(--info-light); color: white; border-radius: 0.4rem; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>

            <!-- Gestion Clientèle -->
            <div class="stat-card clientele">
                <div class="stat-header">
                    <div class="stat-icon clientele">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="stat-title">Clients Totaux</h3>
                </div>
                <div class="stat-value">{{ number_format($gestionClientele['nb_total_clients'] ?? 0) }}</div>
                <div class="stat-label">Base clientèle</div>
                <div class="stat-trend trend-positive">
                    <i class="fas fa-user-plus me-1"></i>
                    {{ number_format($gestionClientele['nouveaux_clients_mois'] ?? 0) }} nouveaux ce mois
                </div>
                <div class="stat-actions" style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                    <a href="{{ route('admin.dashboard.top-clients') }}" target="_blank" 
                            class="btn-details" 
                            style="flex: 1; padding: 0.5rem; border: 1px solid var(--warning-color); background: transparent; color: var(--warning-color); border-radius: 0.4rem; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-crown me-1"></i>
                        Top clients
                    </a>
                </div>
                <div class="stat-actions" style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                    <a href="{{ route('admin.dashboard.top-clients') }}?type=totaux" target="_blank" 
                            class="btn-details" 
                            style="flex: 1; padding: 0.5rem; border: 1px solid var(--success-color); background: transparent; color: var(--success-color); border-radius: 0.4rem; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-friends me-1"></i>
                        Voir détails
                    </a>
                    <a onclick="exportData('clients-totaux', 'pdf')" 
                            class="btn-export" 
                            style="padding: 0.5rem 0.75rem; border: 1px solid var(--success-light); background: var(--success-light); color: white; border-radius: 0.4rem; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>

            <div class="stat-card clientele">
                <div class="stat-header">
                    <div class="stat-icon clientele">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="stat-title">Clients Fidèles</h3>
                </div>
                <div class="stat-value">{{ number_format($gestionClientele['clients_fideles_actifs'] ?? 0) }}</div>
                <div class="stat-label">Programme de fidélité</div>
                <div class="stat-trend trend-positive">
                    <i class="fas fa-gift me-1"></i>
                    {{ number_format($gestionClientele['points_fidelite_distribues'] ?? 0) }} points distribués
                </div>
                <div class="stat-actions" style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                    <a href="{{ route('admin.dashboard.top-clients') }}?type=fideles" target="_blank" 
                            class="btn-details" 
                            style="flex: 1; padding: 0.5rem; border: 1px solid var(--success-color); background: transparent; color: var(--success-color); border-radius: 0.4rem; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-gift me-1"></i>
                        Voir détails
                    </a>
                    <a onclick="exportData('clients-fideles', 'pdf')" 
                            class="btn-export" 
                            style="padding: 0.5rem 0.75rem; border: 1px solid var(--success-light); background: var(--success-light); color: white; border-radius: 0.4rem; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>

            <!-- Gestion Restaurant -->
            <div class="stat-card restaurant">
                <div class="stat-header">
                    <div class="stat-icon restaurant">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h3 class="stat-title">Tables Occupées</h3>
                </div>
                <div class="stat-value">{{ number_format($gestionRestaurant['tables_occupees'] ?? 0) }}</div>
                <div class="stat-label">{{ number_format($gestionRestaurant['tables_libres'] ?? 0) }} tables libres</div>
                <div class="stat-trend trend-positive">
                    <i class="fas fa-calendar-check me-1"></i>
                    {{ number_format($gestionRestaurant['reservations_jour'] ?? 0) }} réservations aujourd'hui
                </div>
                <div class="stat-actions" style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                    <a href="{{ route('admin.dashboard.etat-tables') }}" target="_blank" 
                            class="btn-details" 
                            style="flex: 1; padding: 0.5rem; border: 1px solid var(--danger-color); background: transparent; color: var(--danger-color); border-radius: 0.4rem; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-table me-1"></i>
                        Voir tables
                    </a>
                </div>
                <div class="stat-actions" style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                    <a href="{{ route('admin.dashboard.etat-tables') }}?type=occupees" target="_blank" 
                            class="btn-details" 
                            style="flex: 1; padding: 0.5rem; border: 1px solid var(--success-color); background: transparent; color: var(--success-color); border-radius: 0.4rem; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-concierge-bell me-1"></i>
                        Voir détails
                    </a>
                    <a onclick="exportData('tables-occupees', 'pdf')" 
                            class="btn-export" 
                            style="padding: 0.5rem 0.75rem; border: 1px solid var(--success-light); background: var(--success-light); color: white; border-radius: 0.4rem; font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Section Accès Rapide aux Rapports -->
        <div style="padding: 1rem 2.5rem; border-top: 1px solid var(--border-light);">
    <div style="background: linear-gradient(135deg, var(--gray-50), var(--light-color)); border-radius: var(--radius-xl); padding: 2rem; border: 1px solid var(--border-light);">
        <h3 style="color: var(--gray-800); font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem; display: flex; align-items: center;">
            <i class="fas fa-chart-line me-2 text-primary"></i>
            Accès Rapide aux Rapports
        </h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            <!-- Ventes du Jour -->
            <form method="POST" action="{{ route('admin.reports.generate') }}" style="margin: 0;">
                @csrf
                <input type="hidden" name="type_rapport" value="ventes">
                <input type="hidden" name="periode_type" value="jour">
                <input type="hidden" name="date_debut" value="{{ date('Y-m-d') }}">
                <input type="hidden" name="format" value="view">
                
                <button type="submit" class="quick-report-card"
                       style="background: white; border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem; color: var(--gray-700); transition: all 0.3s ease; display: block; width: 100%; text-align: left; cursor: pointer;"
                       onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='var(--shadow-lg)'"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-sm)'">
                    <div style="display: flex; align-items: center; margin-bottom: 0.75rem;">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--success-color), var(--success-light)); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                            <i class="fas fa-cash-register" style="color: white; font-size: 1.1rem;"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--gray-800);">Ventes du Jour</h4>
                            <p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Rapport journalier</p>
                        </div>
                    </div>
                </a>
            </form>

            <!-- État du Stock -->
            <form method="POST" action="{{ route('admin.reports.generate') }}" style="margin: 0;">
                @csrf
                <input type="hidden" name="type_rapport" value="stock">
                <input type="hidden" name="periode_type" value="jour">
                <input type="hidden" name="date_debut" value="{{ date('Y-m-d') }}">
                <input type="hidden" name="format" value="view">
                
                <button type="submit" class="quick-report-card"
                       style="background: white; border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem; color: var(--gray-700); transition: all 0.3s ease; display: block; width: 100%; text-align: left; cursor: pointer;"
                       onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='var(--shadow-lg)'"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-sm)'">
                    <div style="display: flex; align-items: center; margin-bottom: 0.75rem;">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--info-color), var(--info-light)); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                            <i class="fas fa-boxes" style="color: white; font-size: 1.1rem;"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--gray-800);">État du Stock</h4>
                            <p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">Inventaire actuel</p>
                        </div>
                    </div>
                </a>
            </form>

           
    </div>
</div>


 <!-- Gestion des Produits -->
            <a href="{{ route('admin.articles.index') }}" class="quick-report-card"
               style="background: white; border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem; color: var(--gray-700); transition: all 0.3s ease; display: block; width: 100%; text-decoration: none;"
               onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='var(--shadow-lg)'"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-sm)'">
                <div style="display: flex; align-items: center; margin-bottom: 0.75rem;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--warning-color), var(--warning-light)); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                        <i class="fas fa-shopping-bag" style="color: white; font-size: 1.1rem;"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--gray-800);">إدارة المنتجات</h4>
                        <p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">إضافة وتعديل المنتجات</p>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.75rem; color: var(--gray-500);">إدارة شاملة للمخزون</span>
                    <i class="fas fa-arrow-right" style="color: var(--gray-400); font-size: 0.875rem;"></i>
                </div>
            </a>
        </div>

        <!-- Graphiques et Analyses -->
        <div class="charts-section">
            <div class="charts-grid">
                <div class="chart-container">
                    <div class="chart-header">
                        <div>
                            <h4 class="chart-title">
                                <i class="fas fa-chart-line me-2 text-primary"></i>
                                Évolution des Ventes
                            </h4>
                            <p class="chart-subtitle">Chiffre d'affaires sur les 30 derniers jours</p>
                        </div>
                        <a href="{{ route('admin.dashboard.performance-horaire') }}" target="_blank" 
                                class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-expand-arrows-alt"></i>
                            Analyse détaillée
                        </a>
                    </div>
                    <div class="chart-content">
                        <canvas id="evolutionVentesChart" class="chart-canvas"></canvas>
                    </div>
                </div>
                
                <div class="chart-container">
                    <div class="chart-header">
                        <div>
                            <h4 class="chart-title">
                                <i class="fas fa-chart-pie me-2 text-success"></i>
                                Répartition par Famille
                            </h4>
                            <p class="chart-subtitle">Ventes par catégorie d'articles</p>
                        </div>
                    </div>
                    <div class="chart-content">
                        <canvas id="repartitionFamillesChart" class="chart-canvas"></canvas>
                    </div>
                </div>
            </div>

            <div class="charts-grid">
                <div class="chart-container">
                    <div class="chart-header">
                        <div>
                            <h4 class="chart-title">
                                <i class="fas fa-clock me-2 text-warning"></i>
                                Heures de Pointe
                            </h4>
                            <p class="chart-subtitle">Activité par tranche horaire</p>
                        </div>
                    </div>
                    <div class="chart-content">
                        <canvas id="heuresPointeChart" class="chart-canvas"></canvas>
                    </div>
                </div>
                
                <div class="chart-container">
                    <div class="chart-header">
                        <div>
                            <h4 class="chart-title">
                                <i class="fas fa-credit-card me-2 text-info"></i>
                                Modes de Paiement
                            </h4>
                            <p class="chart-subtitle">Répartition des encaissements</p>
                        </div>
                        <a href="{{ route('admin.dashboard.modes-paiement') }}" target="_blank" 
                                class="btn btn-sm btn-outline-info">
                            <i class="fas fa-expand-arrows-alt"></i>
                            Analyse détaillée
                        </a>
                    </div>
                    <div class="chart-content">
                        <canvas id="modesPaiementChart" class="chart-canvas"></canvas>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <!-- Tableaux Détaillés -->
        <div class="tables-section">
            <!-- Articles les Plus Vendus -->
            <div class="table-container">
                <div class="table-header">
                    <h4 class="table-title">
                        <i class="fas fa-trophy me-2 text-warning"></i>
                        Articles les Plus Vendus
                    </h4>
                </div>
                @if(isset($gestionStocks['articles_plus_vendus']) && count($gestionStocks['articles_plus_vendus']) > 0)
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Article</th>
                                <th>Quantité</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gestionStocks['articles_plus_vendus'] as $article)
                            <tr>
                                <td>{{ $article->ART_DESIGNATION }}</td>
                                <td><span class="badge badge-success">{{ $article->quantite_vendue }}</span></td>
                                <td>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: {{ min(($article->quantite_vendue / ($gestionStocks['articles_plus_vendus']->first()->quantite_vendue ?? 1)) * 100, 100) }}%"></div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="loading-state">
                        <div class="loading-spinner"></div>
                        Chargement des données...
                    </div>
                @endif
            </div>

            <!-- Top Clients -->
            <div class="table-container">
                <div class="table-header">
                    <h4 class="table-title">
                        <i class="fas fa-crown me-2 text-warning"></i>
                        Meilleurs Clients
                    </h4>
                </div>
                @if(isset($gestionClientele['top_meilleurs_clients']) && count($gestionClientele['top_meilleurs_clients']) > 0)
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Commandes</th>
                                <th>Total Dépensé</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gestionClientele['top_meilleurs_clients'] as $client)
                            <tr>
                                <td>{{ $client->CLT_CLIENT }}</td>
                                <td><span class="badge badge-info">{{ $client->nb_commandes }}</span></td>
                                <td><strong>{{ number_format($client->total_depense, 2) }} DH</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="loading-state">
                        <div class="loading-spinner"></div>
                        Chargement des données clients...
                    </div>
                @endif
            </div>

            <!-- État des Caisses -->
            <div class="table-container">
                <div class="table-header">
                    <h4 class="table-title">
                        <i class="fas fa-cash-register me-2 text-success"></i>
                        État des Caisses
                    </h4>
                </div>
                @if(isset($statistiquesFinancieres['etat_caisse']) && count($statistiquesFinancieres['etat_caisse']) > 0)
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Caisse</th>
                                <th>Solde Actuel</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statistiquesFinancieres['etat_caisse'] as $caisse)
                            <tr>
                                <td>{{ $caisse->CSS_LIBELLE_CAISSE }}</td>
                                <td><strong>{{ number_format(1000 + rand(500, 2000), 2) }} DH</strong></td>
                                <td>
                                    @if($caisse->CSS_AVEC_AFFICHEUR)
                                        <span class="badge badge-success">Avec Afficheur</span>
                                    @else
                                        <span class="badge badge-info">Standard</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="loading-state">
                        <div class="loading-spinner"></div>
                        Chargement des données de caisse...
                    </div>
                @endif
            </div>

            <!-- Dépenses du Jour -->
            <div class="table-container">
                <div class="table-header">
                    <h4 class="table-title">
                        <i class="fas fa-money-bill-wave me-2 text-danger"></i>
                        Gestion Financière
                    </h4>
                </div>
                <table class="custom-table">
                    <tbody>
                        <tr>
                            <td><strong>Solde Total des Caisses</strong></td>
                            <td><span class="badge badge-success">{{ number_format($gestionFinanciere['solde_caisse_actuel'] ?? 0, 2) }} DH</span></td>
                        </tr>
                        <tr>
                            <td><strong>Dépenses du Jour</strong></td>
                            <td><span class="badge badge-warning">{{ number_format($gestionFinanciere['depenses_jour'] ?? 0, 2) }} DH</span></td>
                        </tr>
                        <tr>
                            <td><strong>Dépenses du Mois</strong></td>
                            <td><span class="badge badge-danger">{{ number_format($gestionFinanciere['depenses_mois'] ?? 0, 2) }} DH</span></td>
                        </tr>
                        <tr>
                            <td><strong>Bénéfice Estimé</strong></td>
                            <td><span class="badge badge-info">{{ number_format(($statistiquesFinancieres['ca_du_mois'] ?? 0) - ($gestionFinanciere['depenses_mois'] ?? 0), 2) }} DH</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Avancé Intégré -->
    <div id="advancedModalContainer" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; justify-content: center; align-items: center;">
        <div style="background: white; border-radius: 15px; max-width: 90%; max-height: 90%; overflow: auto; padding: 2rem; position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <i class="modal-icon fas fa-chart-line" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                    <h2 class="modal-title-text" style="margin: 0; color: var(--gray-800); font-size: 1.5rem;">Titre du Modal</h2>
                </div>
                <a onclick="closeAdvancedModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--gray-600);">
                    <i class="fas fa-times"></i>
                </a>
            </div>
            <div id="modalContent" style="min-height: 200px;">
                <div id="modalLoading" style="display: none; text-align: center; padding: 3rem;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary-color);"></i>
                    <p style="margin-top: 1rem; color: var(--gray-600);">Chargement des données...</p>
                </div>
                <div class="modal-tab-content" id="modalData" style="display: block;">
                    <!-- Les données seront chargées ici -->
                    <p style="text-align: center; color: #6c757d; padding: 2rem;">
                        <i class="fas fa-info-circle" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                        Sélectionnez une option pour voir les détails
                    </p>
                </div>
                <div id="modalError" style="display: none; text-align: center; padding: 3rem; color: var(--danger-color);">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem;"></i>
                    <p style="margin-top: 1rem;">Erreur lors du chargement des données</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Widget de Notifications en Temps Réel -->
    @include('admin.notification-widget')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Configuration globale pour Chart.js
        Chart.defaults.font.family = 'Inter, sans-serif';
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#6b7280';

        // Configuration des couleurs modernes
        const colors = {
            primary: '#4f46e5',
            primaryLight: '#6366f1',
            success: '#059669',
            successLight: '#10b981',
            warning: '#d97706',
            warningLight: '#f59e0b',
            danger: '#dc2626',
            dangerLight: '#ef4444',
            info: '#2563eb',
            infoLight: '#3b82f6',
            gradient: {
                primary: ['#4f46e5', '#6366f1'],
                success: ['#059669', '#10b981'],
                warning: ['#d97706', '#f59e0b'],
                danger: ['#dc2626', '#ef4444'],
                info: ['#2563eb', '#3b82f6']
            }
        };

        // Helper function pour créer des gradients
        function createGradient(ctx, colorArray, direction = 'vertical') {
            const gradient = direction === 'vertical' 
                ? ctx.createLinearGradient(0, 0, 0, 400)
                : ctx.createLinearGradient(0, 0, 400, 0);
            
            gradient.addColorStop(0, colorArray[0]);
            gradient.addColorStop(1, colorArray[1]);
            return gradient;
        }

        // Données pour les graphiques avec fallback
        const evolutionVentesData = @json($graphiquesAnalyses['evolution_ventes_30j'] ?? []);
        const repartitionFamillesData = @json($graphiquesAnalyses['repartition_familles'] ?? []);
        const heuresPointeData = @json($graphiquesAnalyses['heures_pointe'] ?? []);
        const encaissementsData = @json($statistiquesFinancieres['encaissements_mode_paiement'] ?? []);

        // ===============================================================
        // SYSTÈME AVANCÉ DE MODALS - DOIT ÊTRE ACCESSIBLE IMMÉDIATEMENT
        // ===============================================================

        // Configuration des endpoints API pour les modals
        const modalEndpoints = {
            'chiffre-affaires': '{{ route("admin.chiffre-affaires-details") }}',
            'stock-rupture': '{{ route("admin.articles-rupture-details") }}',
            'top-clients': '{{ route("admin.top-clients-details") }}',
            'performance-horaire': '{{ route("admin.performance-horaire-details") }}',
            'modes-paiement': '{{ route("admin.modes-paiement-details") }}',
            'etat-tables': '{{ route("admin.etat-tables-details") }}'
        };

        // Fonction principale pour ouvrir les modals avancées - ACCESSIBLE GLOBALEMENT
        window.openAdvancedModal = function(type, title, icon) {
            const modal = document.getElementById('advancedModalContainer');
            if (!modal) {
                console.error('Modal container not found!');
                return;
            }

            const titleElement = modal.querySelector('.modal-title-text');
            const iconElement = modal.querySelector('.modal-icon');
            
            // Configuration du modal
            if (titleElement) titleElement.textContent = title;
            if (iconElement) iconElement.className = icon;
            
            // Affichage du modal avec animation
            modal.style.display = 'flex';
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.style.opacity = '1';
            }, 10);
            
            // Chargement des données
            loadModalData(type);
            
            // Gestion du body scroll
            document.body.style.overflow = 'hidden';
        };

        // Fonction pour fermer le modal
        window.closeAdvancedModal = function() {
            const modal = document.getElementById('advancedModalContainer');
            if (!modal) return;
            
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, 300);
        };

        // Chargement des données du modal
        async function loadModalData(type) {
            const endpoint = modalEndpoints[type];
            if (!endpoint) {
                console.error('Endpoint non trouvé pour le type:', type);
                showModalError('Endpoint non configuré pour: ' + type);
                return;
            }

            // Affichage du loader
            showModalLoading();

            try {
                const response = await fetch(endpoint, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    displayModalData(data.data, type);
                } else {
                    showModalError(data.message || 'Erreur lors du chargement des données');
                }
            } catch (error) {
                console.error('Erreur lors du chargement des données:', error);
                showModalError('Erreur de connexion. Veuillez réessayer.');
            }
        }

        // Affichage du loader dans le modal
        function showModalLoading() {
            const content = document.getElementById('modal-tab-content');
            if (content) {
                content.innerHTML = `
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <p class="mt-3 text-muted">Chargement des données...</p>
                    </div>
                `;
            }
        }

        // Affichage des erreurs dans le modal
        function showModalError(message) {
            const content = document.getElementById('modal-tab-content');
            if (content) {
                content.innerHTML = `
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${message}
                    </div>
                `;
            }
        }

        // Affichage des données dans le modal
        function displayModalData(data, type) {
            const content = document.getElementById('modal-tab-content');
            if (!content) return;

            let html = '';

            // KPIs principaux
            if (data.kpis && Object.keys(data.kpis).length > 0) {
                html += '<div class="row mb-4">';
                Object.entries(data.kpis).forEach(([key, value]) => {
                    html += `
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">${formatCurrency(value)}</h5>
                                    <p class="card-text">${key.replace('_', ' ').toUpperCase()}</p>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            }

            // Données tabulaires
            if (data.details && Array.isArray(data.details) && data.details.length > 0) {
                html += '<div class="table-responsive">';
                html += '<table class="table table-striped table-hover">';
                
                // En-têtes dynamiques basés sur le premier élément
                const firstItem = data.details[0];
                html += '<thead class="table-dark"><tr>';
                Object.keys(firstItem).forEach(key => {
                    html += `<th>${key.replace('_', ' ').toUpperCase()}</th>`;
                });
                html += '</tr></thead>';
                
                // Corps du tableau
                html += '<tbody>';
                data.details.forEach(item => {
                    html += '<tr>';
                    Object.values(item).forEach(value => {
                        const displayValue = typeof value === 'number' && key?.includes('prix') ? 
                                           formatCurrency(value) : value;
                        html += `<td>${displayValue}</td>`;
                    });
                    html += '</tr>';
                });
                html += '</tbody></table></div>';
            }

            // Message si aucune donnée
            if (!data.kpis && !data.details) {
                html = `
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        Aucune donnée disponible pour cette section.
                    </div>
                `;
            }

            content.innerHTML = html;
        }

        // Fonction utilitaire pour formater la devise
        function formatCurrency(amount) {
            return new Intl.NumberFormat('fr-MA', {
                style: 'currency',
                currency: 'MAD',
                minimumFractionDigits: 2
            }).format(amount).replace('MAD', 'DH');
        }

        // Attendre le chargement complet de la page
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
            initializeAnimations();
        });

        function initializeCharts() {
            // Graphique Évolution des Ventes
            const evolutionCtx = document.getElementById('evolutionVentesChart');
            if (evolutionCtx) {
                const ctx = evolutionCtx.getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: evolutionVentesData.map(item => {
                            const date = new Date(item.date);
                            return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
                        }),
                        datasets: [{
                            label: 'Chiffre d\'Affaires',
                            data: evolutionVentesData.map(item => parseFloat(item.total_ventes || 0)),
                            borderColor: colors.primary,
                            backgroundColor: createGradient(ctx, [colors.primary + '40', colors.primary + '10']),
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: colors.primary,
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 3,
                            pointRadius: 6,
                            pointHoverRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                borderColor: colors.primary,
                                borderWidth: 1,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        return 'CA: ' + context.parsed.y.toLocaleString('fr-FR') + ' €';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9',
                                    drawBorder: false
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    padding: 10,
                                    callback: function(value) {
                                        return value.toLocaleString('fr-FR') + ' €';
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    padding: 10
                                }
                            }
                        }
                    }
                });
            }

            // Graphique Répartition par Familles
            const repartitionCtx = document.getElementById('repartitionFamillesChart');
            if (repartitionCtx) {
                const ctx = repartitionCtx.getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: repartitionFamillesData.map(item => item.FAM_DESIGNATION || 'Famille'),
                        datasets: [{
                            data: repartitionFamillesData.map(item => parseFloat(item.total_ventes || 0)),
                            backgroundColor: [
                                colors.primary,
                                colors.success,
                                colors.warning,
                                colors.danger,
                                colors.info,
                                '#8b5cf6',
                                '#f97316',
                                '#06b6d4'
                            ],
                            borderWidth: 3,
                            borderColor: '#ffffff',
                            cutout: '60%',
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                borderColor: colors.primary,
                                borderWidth: 1,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed * 100) / total).toFixed(1);
                                        return context.label + ': ' + context.parsed.toLocaleString('fr-FR') + ' € (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    });
            }

            // Graphique Heures de Pointe
            const heuresCtx = document.getElementById('heuresPointeChart');
            if (heuresCtx) {
                const ctx = heuresCtx.getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: heuresPointeData.map(item => item.heure + 'h'),
                        datasets: [{
                            label: 'Transactions',
                            data: heuresPointeData.map(item => parseInt(item.nb_transactions || 0)),
                            backgroundColor: createGradient(ctx, [colors.warning, colors.warningLight]),
                            borderColor: colors.warning,
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                borderColor: colors.warning,
                                borderWidth: 1,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        return 'Transactions: ' + context.parsed.y;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9',
                                    drawBorder: false
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    padding: 10
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    padding: 10
                                }
                            }
                        }
                    });
            }

            // Graphique Modes de Paiement
            const paiementCtx = document.getElementById('modesPaiementChart');
            if (paiementCtx) {
                const ctx = paiementCtx.getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Espèces', 'Carte Bancaire', 'Chèque', 'Autre'],
                        datasets: [{
                            data: [45, 35, 15, 5], // Données par défaut
                            backgroundColor: [
                                colors.success,
                                colors.info,
                                colors.warning,
                                colors.danger
                            ],
                            borderWidth: 3,
                            borderColor: '#ffffff',
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                borderColor: colors.info,
                                borderWidth: 1,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed * 100) / total).toFixed(1);
                                        return context.label + ': ' + percentage + '%';
                                    }
                                }
                            }
                        }
                    });
            }
        }

        function initializeAnimations() {
            // Animation des cartes statistiques
            const statCards = document.querySelectorAll('.stat-card');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }, index * 100);
                    }
                });
            }, {
                threshold: 0.1
            });

            statCards.forEach((card) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                observer.observe(card);
            });

            // Animation pour les valeurs numériques
            const statValues = document.querySelectorAll('.stat-value');
            statValues.forEach((element) => {
                const finalValue = parseFloat(element.textContent.replace(/[^\d.-]/g, ''));
                if (!isNaN(finalValue)) {
                    animateCounter(element, 0, finalValue, 2000);
                }
            });
        }

        function animateCounter(element, start, end, duration) {
            const increment = (end - start) / (duration / 16);
            let current = start;
            const timer = setInterval(() => {
                current += increment;
                if (current >= end) {
                    element.textContent = formatNumber(end) + getUnit(element.textContent);
                    clearInterval(timer);
                } else {
                    element.textContent = formatNumber(current) + getUnit(element.textContent);
                }
            }, 16);
        }

        function formatNumber(num) {
            if (num >= 1000000) {
                return (num / 1000000).toFixed(1) + 'M';
            } else if (num >= 1000) {
                return (num / 1000).toFixed(1) + 'K';
            }
            return Math.round(num).toLocaleString('fr-FR');
        }

        // Mode sombre (optionnel)
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            // Ajuster les couleurs pour le mode sombre
        }

        // Fonction d'export des données du modal
        window.exportData = function() {
            const modalType = document.querySelector('#advancedModalContainer .modal-title-text')?.textContent || 'Export';
            const timestamp = new Date().toISOString().slice(0,10);
            
            // Récupérer les données actuelles du modal
            const tableData = document.querySelector('#modalData table');
            if (!tableData) {
                alert('Aucune donnée à exporter');
                return;
            }
            
            // Conversion en CSV
            let csv = '';
            const rows = tableData.querySelectorAll('tr');
            rows.forEach(row => {
                const cols = row.querySelectorAll('th, td');
                const rowData = Array.from(cols).map(col => 
                    '"' + col.textContent.trim().replace(/"/g, '""') + '"'
                ).join(',');
                csv += rowData + '\n';
            });
            
            // Téléchargement
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${modalType.replace(/\s+/g, '_')}_${timestamp}.csv`;
            a.click();
            window.URL.revokeObjectURL(url);
        };

        // Fonction d'export du rapport complet
        function exportReport() {
            const url = '{{ route("admin.reports.generate") }}?type_rapport=dashboard&format=pdf&periode_type=jour&date_debut={{ date("Y-m-d") }}';
            window.open(url, '_blank');
        }
    </script>
</body>
</html>
