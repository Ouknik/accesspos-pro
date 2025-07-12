@extends('admin.layouts.app')

@section('title', 'Détails du produit - ' . $article->ART_DESIGNATION)

@section('styles')
<style>
    .product-header { 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
    }
    .info-card { border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 10px; }
    .stat-box { 
        background: #f8f9fa; 
        border-radius: 10px; 
        padding: 20px; 
        text-align: center; 
        margin-bottom: 20px;
        transition: transform 0.2s;
    }
    .stat-box:hover { transform: translateY(-3px); }
    .badge-large { padding: 8px 16px; font-size: 14px; }
    .timeline { position: relative; padding-left: 30px; }
    .timeline::before { 
        content: ''; 
        position: absolute; 
        left: 15px; 
        top: 0; 
        bottom: 0; 
        width: 2px; 
        background: #dee2e6; 
    }
    .timeline-item { 
        position: relative; 
        padding: 15px 0; 
        border-left: 2px solid transparent; 
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -5px;
        top: 20px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #007bff;
    }
    .chart-container { height: 300px; margin: 20px 0; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Navigation breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.tableau-de-bord-moderne') }}">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.articles.index') }}">Gestion des produits</a></li>
            <li class="breadcrumb-item active">{{ $article->ART_DESIGNATION }}</li>
        </ol>
    </nav>

    <!-- En-tête du produit -->
    <div class="product-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-3">
                    @if($article->IsMenu)
                        <i class="fas fa-utensils fa-2x me-3"></i>
                    @else
                        <i class="fas fa-box fa-2x me-3"></i>
                    @endif
                    <div>
                        <h1 class="h2 mb-0">{{ $article->ART_DESIGNATION }}</h1>
                        <p class="mb-0 opacity-75">Code produit : <code>{{ $article->ART_REF }}</code></p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-auto">
                        @if($article->ART_VENTE)
                            <span class="badge bg-success badge-large">Produit actif</span>
                        @else
                            <span class="badge bg-danger badge-large">Produit inactif</span>
                        @endif
                    </div>
                    @if($article->IsMenu)
                        <div class="col-auto">
                            <span class="badge bg-warning badge-large">Produit de menu</span>
                        </div>
                    @endif
                    @if($article->ART_STOCKABLE)
                        <div class="col-auto">
                            <span class="badge bg-info badge-large">Produit stockable</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('admin.articles.edit', $article->ART_REF) }}" class="btn btn-light">
                        <i class="fas fa-edit"></i> Modifier le produit
                    </a>
                    @if(auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('admin.articles.toggle-status', $article->ART_REF) }}" 
                              class="d-inline" onsubmit="return confirm('Voulez-vous modifier le statut de ce produit ?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-light w-100">
                                <i class="fas fa-{{ $article->ART_VENTE ? 'times' : 'check' }}"></i>
                                {{ $article->ART_VENTE ? 'Désactiver le produit' : 'Activer le produit' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations de base -->
        <div class="col-md-8">
            <div class="card info-card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations de base</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold text-muted">Nom du produit :</td>
                                    <td>{{ $article->ART_DESIGNATION }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Code produit :</td>
                                    <td><code>{{ $article->ART_REF }}</code></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Famille :</td>
                                    <td>
                                        @if($article->famille)
                                            <span class="badge bg-secondary">{{ $article->famille }}</span>
                                        @else
                                            <span class="text-muted">Non spécifié</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Sous-catégorie :</td>
                                    <td>
                                        @if($article->sous_famille)
                                            <span class="badge bg-light text-dark">{{ $article->sous_famille }}</span>
                                        @else
                                            <span class="text-muted">Non spécifié</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold text-muted">Date de création :</td>
                                    <td>
                                        @if($article->ART_DATE_CREATION)
                                            {{ \Carbon\Carbon::parse($article->ART_DATE_CREATION)->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-muted">Non spécifié</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Dernière modification :</td>
                                    <td>
                                        @if($article->ART_DATE_MODIFICATION)
                                            {{ \Carbon\Carbon::parse($article->ART_DATE_MODIFICATION)->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-muted">لم يتم تحديثه</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">نوع المنتج:</td>
                                    <td>
                                        @if($article->IsMenu)
                                            <span class="badge bg-warning">منتج قائمة</span>
                                        @else
                                            <span class="badge bg-info">منتج عادي</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">مخزني:</td>
                                    <td>
                                        @if($article->ART_STOCKABLE)
                                            <span class="badge bg-success">نعم</span>
                                        @else
                                            <span class="badge bg-secondary">لا</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations de prix -->
            <div class="card info-card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Informations de prix</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stat-box">
                                <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                                <h4 class="text-primary">{{ number_format($article->ART_PRIX_ACHAT ?? 0, 2) }} DA</h4>
                                <p class="text-muted mb-0">Prix d'achat</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-box">
                                <i class="fas fa-tag fa-2x text-success mb-2"></i>
                                <h4 class="text-success">{{ number_format($article->ART_PRIX_VENTE, 2) }} DA</h4>
                                <p class="text-muted mb-0">Prix de vente</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-box">
                                <i class="fas fa-percentage fa-2x text-warning mb-2"></i>
                                <h4 class="text-warning">
                                    @if($article->ART_PRIX_ACHAT && $article->ART_PRIX_ACHAT > 0)
                                        {{ number_format((($article->ART_PRIX_VENTE - $article->ART_PRIX_ACHAT) / $article->ART_PRIX_ACHAT) * 100, 1) }}%
                                    @else
                                        <span class="text-muted">Non calculé</span>
                                    @endif
                                </h4>
                                <p class="text-muted mb-0">Marge bénéficiaire</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($article->ART_STOCKABLE)
            <!-- Informations de stock -->
            <div class="card info-card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-warehouse me-2"></i>Informations de stock</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-box">
                                <i class="fas fa-boxes fa-2x text-info mb-2"></i>
                                <h4 class="text-info">{{ $article->stock_total ?? 0 }}</h4>
                                <p class="text-muted mb-0">Stock actuel</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box">
                                <i class="fas fa-arrow-down fa-2x text-danger mb-2"></i>
                                <h4 class="text-danger">{{ $article->ART_STOCK_MIN ?? 0 }}</h4>
                                <p class="text-muted mb-0">Seuil minimum</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box">
                                <i class="fas fa-arrow-up fa-2x text-success mb-2"></i>
                                <h4 class="text-success">{{ $article->ART_STOCK_MAX ?? 0 }}</h4>
                                <p class="text-muted mb-0">Seuil maximum</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box">
                                <i class="fas fa-money-bill-wave fa-2x text-primary mb-2"></i>
                                <h4 class="text-primary">
                                    {{ number_format(($article->stock_total ?? 0) * ($article->ART_PRIX_ACHAT ?? 0), 2) }} DA
                                </h4>
                                <p class="text-muted mb-0">Valeur du stock</p>
                            </div>
                        </div>
                    </div>

                    <!-- Analyse de l'état du stock -->
                    @if($article->stock_total <= $article->ART_STOCK_MIN)
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Attention :</strong> Stock faible ! Il est recommandé de réapprovisionner.
                        </div>
                    @elseif($article->stock_total >= $article->ART_STOCK_MAX)
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note :</strong> Le stock a atteint le seuil maximum.
                        </div>
                    @else
                        <div class="alert alert-success mt-3">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Excellent :</strong> Le niveau de stock est dans la plage normale.
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Statistiques latérales -->
        <div class="col-md-4">
            <!-- Statistiques de ventes -->
            <div class="card info-card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistiques de ventes</h5>
                </div>
                <div class="card-body">
                    @if(isset($salesStats))
                        <div class="stat-box">
                            <i class="fas fa-shopping-bag fa-2x text-success mb-2"></i>
                            <h4 class="text-success">{{ $salesStats['total_quantity'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">Quantité totale vendue</p>
                        </div>
                        
                        <div class="stat-box">
                            <i class="fas fa-money-bill fa-2x text-primary mb-2"></i>
                            <h4 class="text-primary">{{ number_format($salesStats['total_revenue'] ?? 0, 2) }} DA</h4>
                            <p class="text-muted mb-0">Chiffre d'affaires total</p>
                        </div>

                        <div class="stat-box">
                            <i class="fas fa-calendar fa-2x text-warning mb-2"></i>
                            <h4 class="text-warning">{{ $salesStats['sales_this_month'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">Ventes ce mois-ci</p>
                        </div>
                    @else
                        <p class="text-muted text-center">Aucune donnée de vente disponible</p>
                    @endif
                </div>
            </div>

            <!-- Dernières opérations -->
            <div class="card info-card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Dernières opérations</h5>
                </div>
                <div class="card-body">
                    @if(isset($recentOperations) && count($recentOperations) > 0)
                        <div class="timeline">
                            @foreach($recentOperations as $operation)
                                <div class="timeline-item">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $operation->type }}</strong>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($operation->created_at)->diffForHumans() }}
                                        </small>
                                    </div>
                                    <p class="text-muted mb-0">{{ $operation->description }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">Aucune opération récente</p>
                    @endif
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card info-card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.articles.edit', $article->ART_REF) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Modifier le produit
                        </a>
                        
                        @if($article->ART_STOCKABLE)
                            <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#stockModal">
                                <i class="fas fa-plus me-2"></i>Ajouter au stock
                            </button>
                        @endif
                        
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                        </a>
                        
                        <button type="button" class="btn btn-outline-success" onclick="printProductDetails()">
                            <i class="fas fa-print me-2"></i>Imprimer les détails
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour ajouter du stock -->
@if($article->ART_STOCKABLE)
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter du stock - {{ $article->ART_DESIGNATION }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.articles.add-stock', $article->ART_REF) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Quantité ajoutée</label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prix d'achat (optionnel)</label>
                        <input type="number" name="cost_price" class="form-control" step="0.01" 
                               value="{{ $article->ART_PRIX_ACHAT }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Ajouter au stock</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    function printProductDetails() {
        window.print();
    }

    // Affichage des messages de succès
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Succès !',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erreur !',
            text: '{{ session('error') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
</script>
@endsection
