@extends('layouts.sb-admin')

@section('title', 'Articles en Rupture - AccessPOS')

@section('page-heading')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-exclamation-triangle text-danger"></i>
            Articles en Rupture de Stock
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.tableau-de-bord-moderne') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Stock Rupture</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <a href="{{ url('/admin/tableau-de-bord-moderne') }}" class="btn-modern btn-secondary-modern">
            <i class="fas fa-arrow-left"></i>
            Retour
        </a>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Base card styles with beautiful gradients */
    .card {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border: 1px solid rgba(226, 230, 234, 0.6);
        border-radius: 0.35rem;
        overflow: hidden;
    }
    
    .card-header {
        background: linear-gradient(135deg, #f8f9fc 0%, #e2e6ea 100%);
        border-bottom: 1px solid #e3e6f0;
        font-weight: 600;
        color: #5a5c69;
    }
    
    /* Alert styles with modern gradients */
    .alert-emergency {
        background: linear-gradient(135deg, #e74a3b 0%, #dc143c 100%);
        color: white;
        border: none;
        border-radius: 0.35rem;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 0.5rem 2rem rgba(231, 74, 59, 0.4);
        position: relative;
        overflow: hidden;
    }
    
    .alert-emergency::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><pattern id="wave" x="0" y="0" width="100" height="20" patternUnits="userSpaceOnUse"><path d="M0,10 Q25,0 50,10 T100,10 L100,20 L0,20 Z" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="20" fill="url(%23wave)"/></svg>') repeat-x;
        animation: wave 3s linear infinite;
    }
    
    @keyframes wave {
        0% { background-position-x: 0; }
        100% { background-position-x: 100px; }
    }
    
    /* Stock card styles with modern design */
    .stock-card {
        background: white;
        border-radius: 0.35rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border-left: 5px solid;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .stock-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(78, 115, 223, 0.05) 0%, rgba(231, 74, 59, 0.05) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .stock-card:hover::before {
        opacity: 1;
    }
    
    .stock-card.rupture {
        border-left-color: #e74a3b;
    }
    
    .stock-card.critique {
        border-left-color: #f6c23e;
    }
    
    .stock-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
    }
    
    /* Status badges with gradients */
    .stock-status {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        overflow: hidden;
    }
    
    .status-rupture {
        background: linear-gradient(135deg, #e74a3b 0%, #dc143c 100%);
        color: white;
        box-shadow: 0 0.25rem 0.5rem rgba(231, 74, 59, 0.3);
    }
    
    .status-critique {
        background: linear-gradient(135deg, #f6c23e 0%, #e67e22 100%);
        color: white;
        box-shadow: 0 0.25rem 0.5rem rgba(246, 194, 62, 0.3);
    }
    
    /* Button styles with modern gradients */
    .btn-emergency {
        background: linear-gradient(135deg, #e74a3b 0%, #dc143c 100%);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 0.25rem 0.5rem rgba(231, 74, 59, 0.3);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .btn-emergency::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transition: all 0.3s ease;
        transform: translate(-50%, -50%);
    }
    
    .btn-emergency:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .btn-emergency:hover {
        color: white;
        text-decoration: none;
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(231, 74, 59, 0.4);
    }
    
    .btn-contact {
        background: linear-gradient(135deg, #36b9cc 0%, #2980b9 100%);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 0.25rem 0.5rem rgba(54, 185, 204, 0.3);
    }
    
    .btn-contact:hover {
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(54, 185, 204, 0.4);
    }
    
    .btn-order {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 0.25rem 0.5rem rgba(28, 200, 138, 0.3);
    }
    
    .btn-order:hover {
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(28, 200, 138, 0.4);
    }
    
    /* Animated statistics cards */
    .stat-card {
        background: white;
        border-radius: 0.35rem;
        border: none;
        overflow: hidden;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .stat-card-danger {
        background: linear-gradient(135deg, #e74a3b 0%, #dc143c 100%);
        color: white;
        box-shadow: 0 0.25rem 0.5rem rgba(231, 74, 59, 0.3);
    }
    
    .stat-card-warning {
        background: linear-gradient(135deg, #f6c23e 0%, #e67e22 100%);
        color: white;
        box-shadow: 0 0.25rem 0.5rem rgba(246, 194, 62, 0.3);
    }
    
    .stat-card-info {
        background: linear-gradient(135deg, #36b9cc 0%, #2980b9 100%);
        color: white;
        box-shadow: 0 0.25rem 0.5rem rgba(54, 185, 204, 0.3);
    }
    
    .stat-card-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        box-shadow: 0 0.25rem 0.5rem rgba(78, 115, 223, 0.3);
    }
    
    .stat-card:hover {
        transform: translateY(-5px) rotate(1deg);
        box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2) !important;
    }
    
    /* Loss indicator with pulsing effect */
    .loss-indicator {
        background: linear-gradient(135deg, rgba(231, 74, 59, 0.1) 0%, rgba(220, 20, 60, 0.1) 100%);
        border: 2px solid rgba(231, 74, 59, 0.3);
        border-radius: 0.35rem;
        padding: 1rem;
        margin-top: 0.5rem;
        color: #e74a3b;
        font-size: 0.9rem;
        position: relative;
        overflow: hidden;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            border-color: rgba(231, 74, 59, 0.3);
            background: linear-gradient(135deg, rgba(231, 74, 59, 0.1) 0%, rgba(220, 20, 60, 0.1) 100%);
        }
        50% {
            border-color: rgba(231, 74, 59, 0.6);
            background: linear-gradient(135deg, rgba(231, 74, 59, 0.2) 0%, rgba(220, 20, 60, 0.2) 100%);
        }
    }
    
    /* Fade in animations */
    .fade-in {
        animation: fadeIn 0.8s ease-in forwards;
        opacity: 0;
    }
    
    @keyframes fadeIn {
        from { 
            opacity: 0; 
            transform: translateY(30px) scale(0.95); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0) scale(1); 
        }
    }
    
    /* Supplier info with icons */
    .supplier-info {
        background: linear-gradient(135deg, #f8f9fc 0%, #e2e6ea 100%);
        border-radius: 0.25rem;
        padding: 0.75rem;
        margin-top: 0.5rem;
        border-left: 3px solid #36b9cc;
    }
    
    /* Stock level indicators */
    .stock-level {
        display: inline-block;
        width: 100%;
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 0.5rem;
        position: relative;
    }
    
    .stock-level-fill {
        height: 100%;
        border-radius: 4px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .stock-level-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    
    .stock-level-danger {
        background: linear-gradient(135deg, #e74a3b 0%, #dc143c 100%);
    }
    
    .stock-level-warning {
        background: linear-gradient(135deg, #f6c23e 0%, #e67e22 100%);
    }
    
    /* Floating action button */
    .fab {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e74a3b 0%, #dc143c 100%);
        color: white;
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(231, 74, 59, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        transition: all 0.3s ease;
        z-index: 1000;
        animation: bounce 2s infinite;
    }
    
    .fab:hover {
        transform: scale(1.1);
        box-shadow: 0 1rem 2rem rgba(231, 74, 59, 0.5);
        color: white;
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .stock-card {
            margin-bottom: 1rem;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
        }
        
        .fab {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
            bottom: 1rem;
            right: 1rem;
        }
    }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #224abe 0%, #1e3a8a 100%);
    }
</style>
@endsection
        text-transform: uppercase;
    }
    
    .status-rupture {
        background-color: rgba(231, 74, 59, 0.1);
        color: #e74a3b;
        border: 1px solid rgba(231, 74, 59, 0.2);
    }
    
    .status-critique {
        background-color: rgba(246, 194, 62, 0.1);
        color: #f6c23e;
        border: 1px solid rgba(246, 194, 62, 0.2);
    }
    
    .urgent-action {
        background: linear-gradient(135deg, #e74a3b, #ea4c89);
        color: white;
        border: none;
        border-radius: 0.35rem;
        padding: 0.5rem 1rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.3s ease;
    }
    
    .urgent-action:hover {
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
    }
    
    .btn-back {
        background: #6c757d;
        color: white;
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 0.35rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .btn-back:hover {
        background: #5a6268;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
        text-decoration: none;
    }
    
    .animated-card {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.5s ease forwards;
    }
    
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .stat-card-danger {
        background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);
        color: white;
    }
    
    .stat-card-warning {
        background: linear-gradient(135deg, #f6c23e 0%, #e67e22 100%);
        color: white;
    }
    
    .stat-card-info {
        background: linear-gradient(135deg, #36b9cc 0%, #2980b9 100%);
        color: white;
    }
    
    .stat-card-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
    }
    
    .manque-a-gagner {
        background: rgba(231, 74, 59, 0.1);
        border: 1px solid rgba(231, 74, 59, 0.2);
        border-radius: 0.25rem;
        padding: 0.5rem;
        margin-top: 0.5rem;
        color: #e74a3b;
        font-size: 0.85rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Alerte d'urgence principale -->
    <div class="alert-emergency fade-in" style="animation-delay: 0.1s;">
        <div class="row align-items-center position-relative">
            <div class="col-lg-8">
                <h3 class="mb-3 font-weight-bold">
                    <i class="fas fa-exclamation-triangle mr-3 fa-lg"></i>
                    🚨 Alerte Critique: 24 articles nécessitent une action immédiate
                </h3>
                <p class="mb-2 font-size-lg">
                    <i class="fas fa-clock mr-2"></i>
                    Des articles sont en rupture ou ont un stock critique. Contactez vos fournisseurs <strong>maintenant</strong>.
                </p>
                <p class="mb-0">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Dernière mise à jour: {{ date('d/m/Y à H:i') }}
                </p>
            </div>
            <div class="col-lg-4 text-center">
                <button class="btn-emergency mb-3" onclick="contactSuppliers()">
                    <i class="fas fa-phone-alt"></i>
                    Contacter Fournisseurs
                </button>
                <br>
                <button class="btn btn-outline-light" onclick="generateOrders()">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Commandes Automatiques
                </button>
            </div>
        </div>
    </div>

    <!-- Statistiques avec animations -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-card-danger h-100 py-3 fade-in" style="animation-delay: 0.2s;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                Rupture Totale
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-white" id="rupture-count">12</div>
                            <div class="mt-2 small text-white">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Action immédiate requise
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ban fa-3x text-white opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-card-warning h-100 py-3 fade-in" style="animation-delay: 0.3s;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                Stock Critique
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-white" id="critical-count">12</div>
                            <div class="mt-2 small text-white">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                Sous le seuil minimum
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-3x text-white opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-card-info h-100 py-3 fade-in" style="animation-delay: 0.4s;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                Valeur Manquante
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-white">45,250 DH</div>
                            <div class="mt-2 small text-white">
                                <i class="fas fa-chart-line mr-1"></i>
                                Perte potentielle
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-3x text-white opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-card-primary h-100 py-3 fade-in" style="animation-delay: 0.5s;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                Fournisseurs
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-white">8</div>
                            <div class="mt-2 small text-white">
                                <i class="fas fa-phone mr-1"></i>
                                À contacter aujourd'hui
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-3x text-white opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des articles avec design moderne -->
    <div class="card shadow mb-4 fade-in" style="animation-delay: 0.6s;">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-danger">
                <i class="fas fa-list-ul mr-2"></i>Articles Nécessitant une Action Urgente
            </h6>
            <div class="btn-group">
                <button class="btn btn-sm btn-outline-danger" onclick="selectAll()">
                    <i class="fas fa-check-square mr-1"></i>
                    Tout sélectionner
                </button>
                <button class="btn btn-sm btn-danger" onclick="bulkOrder()">
                    <i class="fas fa-shopping-cart mr-1"></i>
                    Commande groupée
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="row m-0">
                @php
                $articles = [
                    ['nom' => 'Couscous Grain Fin', 'famille' => 'Céréales', 'stock' => 0, 'min' => 50, 'prix' => 25, 'fournisseur' => 'Maroc Céréales', 'telephone' => '+212 5XX-XXXX-XX', 'statut' => 'rupture'],
                    ['nom' => 'Agneau Épaule', 'famille' => 'Viandes', 'stock' => 2, 'min' => 15, 'prix' => 180, 'fournisseur' => 'Boucherie Al Baraka', 'telephone' => '+212 6XX-XXXX-XX', 'statut' => 'critique'],
                    ['nom' => 'Huile d\'Olive Extra', 'famille' => 'Condiments', 'stock' => 0, 'min' => 20, 'prix' => 85, 'fournisseur' => 'Olive Morocco', 'telephone' => '+212 5XX-XXXX-XX', 'statut' => 'rupture'],
                    ['nom' => 'Tomates Fraîches', 'famille' => 'Légumes', 'stock' => 3, 'min' => 30, 'prix' => 12, 'fournisseur' => 'Ferme Atlas', 'telephone' => '+212 6XX-XXXX-XX', 'statut' => 'critique'],
                    ['nom' => 'Menthe Fraîche', 'famille' => 'Herbes', 'stock' => 0, 'min' => 10, 'prix' => 8, 'fournisseur' => 'Jardin Marrakech', 'telephone' => '+212 5XX-XXXX-XX', 'statut' => 'rupture'],
                    ['nom' => 'Lait Frais', 'famille' => 'Laiterie', 'stock' => 1, 'min' => 25, 'prix' => 6, 'fournisseur' => 'Laiterie Centrale', 'telephone' => '+212 5XX-XXXX-XX', 'statut' => 'critique'],
                    ['nom' => 'Pain Traditionnel', 'famille' => 'Boulangerie', 'stock' => 0, 'min' => 40, 'prix' => 3, 'fournisseur' => 'Four Traditionnel', 'telephone' => '+212 6XX-XXXX-XX', 'statut' => 'rupture'],
                    ['nom' => 'Épices Ras El Hanout', 'famille' => 'Épices', 'stock' => 2, 'min' => 12, 'prix' => 45, 'fournisseur' => 'Épices Fès', 'telephone' => '+212 5XX-XXXX-XX', 'statut' => 'critique']
                ];
                @endphp
                
                @foreach($articles as $index => $article)
                <div class="col-lg-6 col-md-12 p-3">
                    <div class="stock-card {{ $article['statut'] }} fade-in" style="animation-delay: {{ 0.7 + ($index * 0.1) }}s;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <input type="checkbox" class="mr-3" id="article-{{ $index }}">
                                    <div>
                                        <h6 class="mb-1 font-weight-bold text-gray-800">
                                            {{ $article['nom'] }}
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-tags mr-1"></i>
                                            {{ $article['famille'] }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <span class="stock-status status-{{ $article['statut'] }}">
                                @if($article['statut'] === 'rupture')
                                    <i class="fas fa-ban mr-1"></i>RUPTURE
                                @else
                                    <i class="fas fa-exclamation-triangle mr-1"></i>CRITIQUE
                                @endif
                            </span>
                        </div>
                        
                        <!-- Barre de progression du stock -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted font-weight-bold">Niveau de Stock</small>
                                <small class="text-{{ $article['statut'] === 'rupture' ? 'danger' : 'warning' }} font-weight-bold">
                                    {{ $article['stock'] }} / {{ $article['min'] }}
                                </small>
                            </div>
                            <div class="stock-level">
                                <div class="stock-level-fill stock-level-{{ $article['statut'] === 'rupture' ? 'danger' : 'warning' }}" 
                                     style="width: {{ $article['min'] > 0 ? ($article['stock'] / $article['min']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="text-xs font-weight-bold text-gray-600 text-uppercase mb-1">
                                    <i class="fas fa-warehouse mr-1"></i>Stock Actuel
                                </div>
                                <div class="h5 font-weight-bold text-{{ $article['statut'] === 'rupture' ? 'danger' : 'warning' }}">
                                    {{ $article['stock'] }}
                                    @if($article['statut'] === 'rupture')
                                        <i class="fas fa-exclamation-triangle text-danger ml-1"></i>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-xs font-weight-bold text-gray-600 text-uppercase mb-1">
                                    <i class="fas fa-chart-line mr-1"></i>Stock Minimum
                                </div>
                                <div class="h5 font-weight-bold text-gray-600">{{ $article['min'] }}</div>
                            </div>
                        </div>
                        
                        <!-- Informations fournisseur -->
                        <div class="supplier-info mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">Prix unitaire:</small>
                                    <strong class="text-primary d-block">{{ $article['prix'] }} DH</strong>
                                </div>
                                <div class="text-right">
                                    <small class="text-muted">Fournisseur:</small>
                                    <strong class="text-info d-block">{{ $article['fournisseur'] }}</strong>
                                    <small class="text-muted">{{ $article['telephone'] }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions rapides -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-contact" onclick="contactSupplier('{{ $article['telephone'] }}', '{{ $article['fournisseur'] }}')">
                                    <i class="fas fa-phone"></i>
                                    Appeler
                                </button>
                                <button class="btn btn-order" onclick="quickOrder('{{ $article['nom'] }}', {{ $article['min'] - $article['stock'] }})">
                                    <i class="fas fa-plus"></i>
                                    Commander
                                </button>
                            </div>
                            <div class="text-right">
                                <small class="text-muted">Qté suggérée:</small>
                                <strong class="text-success">{{ $article['min'] - $article['stock'] + 10 }}</strong>
                            </div>
                        </div>
                        
                        @if($article['statut'] === 'rupture')
                        <div class="loss-indicator mt-3">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Perte estimée:</strong> 
                            {{ number_format(($article['min'] - $article['stock']) * $article['prix'], 2) }} DH
                            <br>
                            <small>
                                <i class="fas fa-clock mr-1"></i>
                                Ventes perdues depuis la rupture
                            </small>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Actions rapides en bas -->
    <div class="text-center fade-in" style="animation-delay: 1.5s;">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="btn-group flex-wrap">
                    <button class="btn btn-danger btn-lg mr-2 mb-2" onclick="generateEmergencyOrders()">
                        <i class="fas fa-bolt mr-2"></i>
                        Commandes d'Urgence
                    </button>
                    <button class="btn btn-warning btn-lg mr-2 mb-2" onclick="sendSupplierAlerts()">
                        <i class="fas fa-envelope mr-2"></i>
                        Alertes Fournisseurs
                    </button>
                    <button class="btn btn-info btn-lg mr-2 mb-2" onclick="generateReport()">
                        <i class="fas fa-file-pdf mr-2"></i>
                        Rapport PDF
                    </button>
                    <button class="btn btn-success btn-lg mb-2" onclick="exportData()">
                        <i class="fas fa-download mr-2"></i>
                        Exporter Données
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bouton d'action flottant -->
<button class="fab" onclick="emergencyContact()" title="Contact d'urgence">
    <i class="fas fa-exclamation"></i>
</button>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des compteurs
    animateCounters();
    
    // Initialisation des tooltips
    initializeTooltips();
    
    // Auto-refresh des données
    setInterval(refreshStockData, 30000); // Refresh toutes les 30 secondes
    
    // Effet de pulsation pour les éléments critiques
    startCriticalPulse();
});

// Animation des compteurs avec effet compte-à-rebours
function animateCounters() {
    const counters = [
        { id: 'rupture-count', target: 12, duration: 2000 },
        { id: 'critical-count', target: 12, duration: 2500 }
    ];
    
    counters.forEach(counter => {
        const element = document.getElementById(counter.id);
        if (element) {
            let current = 0;
            const increment = counter.target / (counter.duration / 50);
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= counter.target) {
                    current = counter.target;
                    clearInterval(timer);
                    
                    // Effet de flash à la fin
                    element.style.animation = 'flash 0.5s ease-in-out';
                }
                element.textContent = Math.floor(current);
            }, 50);
        }
    });
}

// Fonction pour contacter un fournisseur
function contactSupplier(telephone, fournisseur) {
    Swal.fire({
        title: `📞 Contacter ${fournisseur}`,
        html: `
            <div class="text-left">
                <p><strong>Téléphone:</strong> ${telephone}</p>
                <p><strong>Actions disponibles:</strong></p>
                <div class="btn-group-vertical w-100">
                    <button class="btn btn-primary mb-2" onclick="window.open('tel:${telephone}')">
                        <i class="fas fa-phone mr-2"></i>Appeler maintenant
                    </button>
                    <button class="btn btn-info mb-2" onclick="sendSMS('${telephone}', '${fournisseur}')">
                        <i class="fas fa-sms mr-2"></i>Envoyer SMS
                    </button>
                    <button class="btn btn-success" onclick="sendEmail('${fournisseur}')">
                        <i class="fas fa-envelope mr-2"></i>Envoyer Email
                    </button>
                </div>
            </div>
        `,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: 'Fermer',
        width: 400,
        customClass: {
            container: 'swal-contact-container'
        }
    });
}

// Fonction pour commander rapidement
function quickOrder(article, quantite) {
    Swal.fire({
        title: `🛒 Commande Rapide`,
        html: `
            <div class="text-left">
                <div class="form-group">
                    <label><strong>Article:</strong></label>
                    <input type="text" class="form-control" value="${article}" readonly>
                </div>
                <div class="form-group">
                    <label><strong>Quantité suggérée:</strong></label>
                    <input type="number" id="order-quantity" class="form-control" value="${quantite + 10}" min="1">
                </div>
                <div class="form-group">
                    <label><strong>Priorité:</strong></label>
                    <select id="order-priority" class="form-control">
                        <option value="urgent">🔴 Urgent (Livraison immédiate)</option>
                        <option value="high">🟠 Élevée (Sous 24h)</option>
                        <option value="normal">🟡 Normale (2-3 jours)</option>
                    </select>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '✅ Confirmer Commande',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#28a745',
        preConfirm: () => {
            const quantity = document.getElementById('order-quantity').value;
            const priority = document.getElementById('order-priority').value;
            
            if (!quantity || quantity < 1) {
                Swal.showValidationMessage('Quantité invalide');
                return false;
            }
            
            return { quantity, priority };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            processOrder(article, result.value.quantity, result.value.priority);
        }
    });
}

// Traitement de la commande
function processOrder(article, quantity, priority) {
    // Simulation d'envoi de commande
    Swal.fire({
        title: 'Traitement de la commande...',
        html: 'Envoi en cours...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        Swal.fire({
            icon: 'success',
            title: '✅ Commande Envoyée!',
            html: `
                <div class="text-left">
                    <p><strong>Article:</strong> ${article}</p>
                    <p><strong>Quantité:</strong> ${quantity}</p>
                    <p><strong>Priorité:</strong> ${priority}</p>
                    <p><strong>Numéro de commande:</strong> CMD${Date.now()}</p>
                </div>
            `,
            confirmButtonText: 'OK',
            confirmButtonColor: '#28a745'
        });
    }, 2000);
}

// Sélectionner tous les articles
function selectAll() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"][id^="article-"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(cb => {
        cb.checked = !allChecked;
        animateSelection(cb);
    });
    
    updateBulkActionButton();
}

// Animation de sélection
function animateSelection(checkbox) {
    const card = checkbox.closest('.stock-card');
    if (checkbox.checked) {
        card.style.transform = 'scale(0.98)';
        card.style.boxShadow = '0 0 20px rgba(40, 167, 69, 0.4)';
        card.style.borderLeft = '5px solid #28a745';
    } else {
        card.style.transform = '';
        card.style.boxShadow = '';
        card.style.borderLeft = '';
    }
    
    setTimeout(() => {
        card.style.transform = '';
    }, 200);
}

// Commande groupée
function bulkOrder() {
    const selectedArticles = getSelectedArticles();
    
    if (selectedArticles.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Aucune sélection',
            text: 'Veuillez sélectionner au moins un article',
            confirmButtonColor: '#f6c23e'
        });
        return;
    }
    
    Swal.fire({
        title: `🛒 Commande Groupée (${selectedArticles.length} articles)`,
        html: generateBulkOrderHTML(selectedArticles),
        showCancelButton: true,
        confirmButtonText: '✅ Confirmer Toutes les Commandes',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#28a745',
        width: 600
    }).then((result) => {
        if (result.isConfirmed) {
            processBulkOrder(selectedArticles);
        }
    });
}

// Générer HTML pour commande groupée
function generateBulkOrderHTML(articles) {
    let html = '<div class="text-left"><table class="table table-sm">';
    html += '<thead><tr><th>Article</th><th>Quantité</th><th>Priorité</th></tr></thead><tbody>';
    
    articles.forEach((article, index) => {
        html += `
            <tr>
                <td>${article.name}</td>
                <td><input type="number" class="form-control form-control-sm" id="bulk-qty-${index}" value="${article.suggestedQty}" min="1"></td>
                <td>
                    <select class="form-control form-control-sm" id="bulk-priority-${index}">
                        <option value="urgent">🔴 Urgent</option>
                        <option value="high">🟠 Élevée</option>
                        <option value="normal">🟡 Normale</option>
                    </select>
                </td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    return html;
}

// Obtenir les articles sélectionnés
function getSelectedArticles() {
    const selected = [];
    const checkboxes = document.querySelectorAll('input[type="checkbox"][id^="article-"]:checked');
    
    checkboxes.forEach(cb => {
        const card = cb.closest('.stock-card');
        const name = card.querySelector('h6').textContent.trim();
        const stockInfo = card.querySelector('.h5').textContent.trim();
        selected.push({
            name: name,
            currentStock: parseInt(stockInfo),
            suggestedQty: parseInt(stockInfo) + 10
        });
    });
    
    return selected;
}

// Actions rapides
function contactSuppliers() {
    Swal.fire({
        title: '📞 Contact Fournisseurs',
        text: 'Voulez-vous contacter tous les fournisseurs concernés?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Oui, contacter tous',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            // Simulation d'envoi d'alertes
            showContactProgress();
        }
    });
}

function showContactProgress() {
    let progress = 0;
    const suppliers = ['Maroc Céréales', 'Boucherie Al Baraka', 'Olive Morocco', 'Ferme Atlas', 'Jardin Marrakech', 'Laiterie Centrale', 'Four Traditionnel', 'Épices Fès'];
    
    Swal.fire({
        title: 'Contact en cours...',
        html: `
            <div class="progress mb-3">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" 
                     id="contact-progress" style="width: 0%"></div>
            </div>
            <p id="current-supplier">Initialisation...</p>
        `,
        allowOutsideClick: false,
        showConfirmButton: false
    });
    
    const interval = setInterval(() => {
        progress += 12.5;
        const progressBar = document.getElementById('contact-progress');
        const currentSupplier = document.getElementById('current-supplier');
        
        if (progressBar && currentSupplier) {
            progressBar.style.width = progress + '%';
            const supplierIndex = Math.floor(progress / 12.5) - 1;
            if (supplierIndex >= 0 && supplierIndex < suppliers.length) {
                currentSupplier.textContent = `Contact: ${suppliers[supplierIndex]}...`;
            }
        }
        
        if (progress >= 100) {
            clearInterval(interval);
            Swal.fire({
                icon: 'success',
                title: '✅ Tous les fournisseurs contactés!',
                text: '8 SMS et emails envoyés avec succès',
                confirmButtonColor: '#28a745'
            });
        }
    }, 500);
}

function generateEmergencyOrders() {
    Swal.fire({
        title: '⚡ Génération des Commandes d\'Urgence',
        text: 'Cela va créer des commandes automatiques pour tous les articles en rupture',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Générer les commandes',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            showOrderGeneration();
        }
    });
}

function showOrderGeneration() {
    Swal.fire({
        title: 'Génération en cours...',
        html: 'Création des commandes d\'urgence...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        Swal.fire({
            icon: 'success',
            title: '✅ Commandes Générées!',
            html: `
                <div class="text-left">
                    <p><strong>12 commandes d'urgence créées:</strong></p>
                    <ul class="text-sm">
                        <li>Couscous Grain Fin - Qté: 60</li>
                        <li>Huile d'Olive Extra - Qté: 30</li>
                        <li>Menthe Fraîche - Qté: 20</li>
                        <li>Pain Traditionnel - Qté: 50</li>
                        <li>Et 8 autres articles...</li>
                    </ul>
                    <p><strong>Total estimé:</strong> 15,420 DH</p>
                </div>
            `,
            confirmButtonText: 'Voir détails',
            confirmButtonColor: '#28a745'
        });
    }, 3000);
}

// Effet de pulsation pour les éléments critiques
function startCriticalPulse() {
    const criticalElements = document.querySelectorAll('.status-rupture, .loss-indicator');
    
    criticalElements.forEach(element => {
        setInterval(() => {
            element.style.animation = 'pulse 1s ease-in-out';
            setTimeout(() => {
                element.style.animation = '';
            }, 1000);
        }, 3000);
    });
}

// Rafraîchissement automatique des données
function refreshStockData() {
    // Simulation de mise à jour en temps réel
    const stockElements = document.querySelectorAll('.h5.font-weight-bold');
    
    stockElements.forEach(element => {
        if (element.classList.contains('text-danger') || element.classList.contains('text-warning')) {
            // Effet de clignotement pour indiquer la mise à jour
            element.style.opacity = '0.5';
            setTimeout(() => {
                element.style.opacity = '1';
            }, 200);
        }
    });
}

// Actions des boutons principaux
function sendSupplierAlerts() {
    Swal.fire({
        icon: 'success',
        title: '📧 Alertes Envoyées!',
        text: 'Tous les fournisseurs ont été alertés par email et SMS',
        confirmButtonColor: '#f6c23e'
    });
}

function generateReport() {
    Swal.fire({
        title: 'Génération du rapport...',
        text: 'Création du PDF en cours...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        Swal.fire({
            icon: 'success',
            title: '📄 Rapport Généré!',
            text: 'Le rapport PDF a été téléchargé',
            confirmButtonColor: '#007bff'
        });
    }, 2000);
}

function exportData() {
    Swal.fire({
        icon: 'success',
        title: '📊 Données Exportées!',
        text: 'Le fichier Excel a été téléchargé',
        confirmButtonColor: '#28a745'
    });
}

function emergencyContact() {
    Swal.fire({
        title: '🚨 Contact d\'Urgence',
        html: `
            <div class="text-left">
                <p><strong>Numéros d'urgence:</strong></p>
                <div class="list-group">
                    <a href="tel:+212500000000" class="list-group-item list-group-item-action">
                        <i class="fas fa-phone text-danger mr-2"></i>
                        Directeur Commercial: +212 5XX-XXX-XXX
                    </a>
                    <a href="tel:+212600000000" class="list-group-item list-group-item-action">
                        <i class="fas fa-phone text-warning mr-2"></i>
                        Responsable Achats: +212 6XX-XXX-XXX
                    </a>
                    <a href="tel:+212700000000" class="list-group-item list-group-item-action">
                        <i class="fas fa-phone text-info mr-2"></i>
                        Fournisseur Principal: +212 7XX-XXX-XXX
                    </a>
                </div>
            </div>
        `,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: 'Fermer',
        width: 500
    });
}

// Initialisation des tooltips
function initializeTooltips() {
    // Si Bootstrap tooltips est disponible
    if (typeof $!== 'undefined' && $.fn.tooltip) {
        $('[title]').tooltip({
            placement: 'top',
            trigger: 'hover'
        });
    }
}

// Event listeners pour les checkboxes
document.addEventListener('change', function(e) {
    if (e.target.type === 'checkbox' && e.target.id.startsWith('article-')) {
        animateSelection(e.target);
        updateBulkActionButton();
    }
});

function updateBulkActionButton() {
    const selectedCount = document.querySelectorAll('input[type="checkbox"][id^="article-"]:checked').length;
    const bulkButton = document.querySelector('[onclick="bulkOrder()"]');
    
    if (bulkButton) {
        if (selectedCount > 0) {
            bulkButton.innerHTML = `<i class="fas fa-shopping-cart mr-1"></i>Commande groupée (${selectedCount})`;
            bulkButton.classList.remove('btn-outline-danger');
            bulkButton.classList.add('btn-danger');
        } else {
            bulkButton.innerHTML = '<i class="fas fa-shopping-cart mr-1"></i>Commande groupée';
            bulkButton.classList.remove('btn-danger');
            bulkButton.classList.add('btn-outline-danger');
        }
    }
}

// Style pour les animations
const style = document.createElement('style');
style.textContent = `
    @keyframes flash {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .swal-contact-container .swal2-html-container {
        text-align: left !important;
    }
`;
document.head.appendChild(style);
</script>
@endsection
