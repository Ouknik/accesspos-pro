@extends('layouts.sb-admin')

@section('title', 'Articles en Rupture - AccessPOS')

@section('page-heading')
<h1 class="h3 mb-2 text-gray-800">
    <i class="fas fa-exclamation-triangle mr-2 text-danger"></i>Articles en Rupture de Stock
</h1>
<p class="mb-4 text-danger font-weight-bold">⚠️ Action urgente requise - {{ date('d/m/Y') }}</p>
@endsection

@section('styles')
<style>
    .alert-card {
        background: linear-gradient(135deg, #e74a3b 0%, #ea4c89 100%);
        color: white;
        border-radius: 0.35rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(231, 74, 59, 0.3);
    }
    
    .stock-card {
        background: white;
        border-radius: 0.35rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border-left: 5px solid;
        transition: all 0.3s ease;
    }
    
    .stock-card.rupture {
        border-left-color: #e74a3b;
    }
    
    .stock-card.critique {
        border-left-color: #f6c23e;
    }
    
    .stock-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.3rem 3rem 0 rgba(58, 59, 69, 0.25);
    }
    
    .stock-status {
        padding: 0.25rem 0.75rem;
        border-radius: 10rem;
        font-size: 0.8rem;
        font-weight: 700;
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
<!-- Navigation Actions -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ url('/admin/tableau-de-bord-moderne') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i>
        Retour au Tableau de Bord
    </a>
</div>

<!-- Alerte Principale -->
<div class="alert-card animated-card">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h4 class="mb-2 font-weight-bold">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Attention: 24 articles nécessitent une action immédiate
            </h4>
            <p class="mb-0">Des articles sont en rupture ou ont un stock critique. Contactez vos fournisseurs rapidement.</p>
        </div>
        <div class="col-lg-4 text-right">
            <a href="#" class="urgent-action">
                <i class="fas fa-phone"></i>
                Contacter Fournisseurs
            </a>
        </div>
    </div>
</div>

<!-- Statistiques Rapides -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card-danger h-100 py-2 animated-card" style="animation-delay: 0.1s;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Rupture Totale
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-white">12</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card-warning h-100 py-2 animated-card" style="animation-delay: 0.2s;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Stock Critique
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-white">12</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-circle fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card-info h-100 py-2 animated-card" style="animation-delay: 0.3s;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Valeur Manquante
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-white">45,250 DH</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-coins fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card-primary h-100 py-2 animated-card" style="animation-delay: 0.4s;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Fournisseurs à Contacter
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-white">8</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des Articles -->
<div class="card shadow mb-4 animated-card" style="animation-delay: 0.5s;">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-danger">
            <i class="fas fa-list mr-2"></i>Articles Nécessitant une Action Urgente
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            @php
            $articles = [
                ['nom' => 'Couscous Grain Fin', 'famille' => 'Céréales', 'stock' => 0, 'min' => 50, 'prix' => 25, 'fournisseur' => 'Maroc Céréales', 'statut' => 'rupture'],
                ['nom' => 'Agneau Épaule', 'famille' => 'Viandes', 'stock' => 2, 'min' => 15, 'prix' => 180, 'fournisseur' => 'Boucherie Al Baraka', 'statut' => 'critique'],
                ['nom' => 'Huile d\'Olive Extra', 'famille' => 'Condiments', 'stock' => 0, 'min' => 20, 'prix' => 85, 'fournisseur' => 'Olive Morocco', 'statut' => 'rupture'],
                ['nom' => 'Tomates Fraîches', 'famille' => 'Légumes', 'stock' => 3, 'min' => 30, 'prix' => 12, 'fournisseur' => 'Ferme Atlas', 'statut' => 'critique'],
                ['nom' => 'Menthe Fraîche', 'famille' => 'Herbes', 'stock' => 0, 'min' => 10, 'prix' => 8, 'fournisseur' => 'Jardin Marrakech', 'statut' => 'rupture'],
                ['nom' => 'Lait Frais', 'famille' => 'Laiterie', 'stock' => 1, 'min' => 25, 'prix' => 6, 'fournisseur' => 'Laiterie Centrale', 'statut' => 'critique'],
                ['nom' => 'Pain Traditionnel', 'famille' => 'Boulangerie', 'stock' => 0, 'min' => 40, 'prix' => 3, 'fournisseur' => 'Four Traditionnel', 'statut' => 'rupture'],
                ['nom' => 'Épices Ras El Hanout', 'famille' => 'Épices', 'stock' => 2, 'min' => 12, 'prix' => 45, 'fournisseur' => 'Épices Fès', 'statut' => 'critique']
            ];
            @endphp
            
            @foreach($articles as $index => $article)
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="stock-card {{ $article['statut'] }} animated-card" style="animation-delay: {{ 0.6 + ($index * 0.1) }}s;">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="mb-1 font-weight-bold text-gray-800">{{ $article['nom'] }}</h6>
                            <small class="text-muted">{{ $article['famille'] }}</small>
                        </div>
                        <span class="stock-status status-{{ $article['statut'] }}">
                            {{ $article['statut'] === 'rupture' ? 'RUPTURE' : 'CRITIQUE' }}
                        </span>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-gray-600 text-uppercase mb-1">
                                Stock Actuel
                            </div>
                            <div class="h5 font-weight-bold text-{{ $article['statut'] === 'rupture' ? 'danger' : 'warning' }}">
                                {{ $article['stock'] }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-gray-600 text-uppercase mb-1">
                                Stock Minimum
                            </div>
                            <div class="h5 font-weight-bold text-gray-600">{{ $article['min'] }}</div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <small class="text-muted">Prix: <strong>{{ $article['prix'] }} DH</strong></small><br>
                            <small class="text-primary font-weight-bold">{{ $article['fournisseur'] }}</small>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-outline-primary btn-sm" title="Contacter fournisseur">
                                <i class="fas fa-phone"></i>
                            </button>
                            <button class="btn btn-outline-success btn-sm" title="Passer commande">
                                <i class="fas fa-plus"></i> Commander
                            </button>
                        </div>
                    </div>
                    
                    @if($article['statut'] === 'rupture')
                    <div class="manque-a-gagner">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Manque à gagner estimé:</strong> {{ number_format(($article['min'] - $article['stock']) * $article['prix'], 2) }} DH
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Actions Rapides -->
<div class="text-center animated-card" style="animation-delay: 1.4s;">
    <a href="#" class="btn btn-danger btn-lg mr-3 mb-2">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        Générer Commandes Urgentes
    </a>
    <a href="#" class="btn btn-warning btn-lg mr-3 mb-2">
        <i class="fas fa-envelope mr-2"></i>
        Envoyer Alertes Fournisseurs
    </a>
    <a href="#" class="btn btn-primary btn-lg mb-2">
        <i class="fas fa-download mr-2"></i>
        Rapport PDF
    </a>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pas de scripts supplémentaires nécessaires pour cette page
    console.log('Page Stock Rupture chargée avec succès');
});
</script>
@endsection
