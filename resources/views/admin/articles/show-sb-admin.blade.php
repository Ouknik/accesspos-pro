@extends('layouts.sb-admin')

@section('title', 'Détails du Produit - AccessPos Pro')

@section('styles')
<style>
    .product-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        color: white;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .product-image {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .info-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 25px;
        overflow: hidden;
    }

    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .card-header {
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        border-bottom: 3px solid #dee2e6;
        padding: 15px 20px;
    }

    .card-header h6 {
        margin: 0;
        font-weight: 700;
        font-size: 1rem;
    }

    .status-badge {
        font-size: 0.875rem;
        padding: 8px 16px;
        border-radius: 25px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .price-display {
        font-size: 2rem;
        font-weight: 800;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .stock-indicator {
        background: linear-gradient(45deg, #f8f9fa, #ffffff);
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        margin: 10px 0;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .action-btn {
        border-radius: 25px;
        padding: 12px 24px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        margin-bottom: 10px;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .alert-modern {
        border: none;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
    }

    .table-modern {
        border: none;
    }

    .table-modern td {
        border: none;
        padding: 12px 0;
        border-bottom: 1px solid #f1f3f4;
    }

    .table-modern td:first-child {
        font-weight: 600;
        color: #495057;
        width: 40%;
    }

    .progress-modern {
        height: 8px;
        border-radius: 10px;
        overflow: hidden;
        background: #e9ecef;
    }

    .progress-bar-modern {
        border-radius: 10px;
        transition: width 0.6s ease;
    }

    .stat-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .image-preview {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        cursor: pointer;
    }

    .image-preview:hover {
        transform: scale(1.05);
    }
</style>
@endsection

@section('page-heading')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-box"></i>
            Détails du Produit
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.tableau-de-bord-moderne') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.articles.index') }}">Produits</a></li>
                <li class="breadcrumb-item active">{{ $article->ART_DESIGNATION ?? 'Détails' }}</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.articles.edit', $article->ART_REF) }}" class="btn btn-warning btn-sm">
            <i class="fas fa-edit"></i>
            Modifier
        </a>
        <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i>
            Retour
        </a>
    </div>
</div>
@endsection

@section('content')

{{-- En-tête du Produit --}}
<div class="product-header">
    <div class="card-body text-white">
        <div class="row align-items-center">
            <div class="col-md-2 text-center">
                @if($article->image && file_exists(public_path('storage/' . $article->image)))
                    <img src="{{ asset('storage/' . $article->image) }}" 
                         alt="{{ $article->ART_DESIGNATION }}" 
                         class="product-image">
                @else
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mx-auto product-image">
                        <i class="fas fa-box fa-3x text-gray-400"></i>
                    </div>
                @endif
            </div>
            <div class="col-md-6">
                <h2 class="mb-2 font-weight-bold">{{ $article->ART_DESIGNATION ?? 'Nom non défini' }}</h2>
                @if($article->ART_REF)
                    <p class="mb-1 opacity-75">
                        <i class="fas fa-barcode mr-2"></i>
                        Code: {{ $article->ART_REF }}
                    </p>
                @endif
                @if($article->famille)
                    <p class="mb-1 opacity-75">
                        <i class="fas fa-tags mr-2"></i>
                        Catégorie: {{ $article->famille }}
                    </p>
                @endif
                <div class="mt-3">
                    @if($article->ART_VENTE == 1)
                        <span class="status-badge badge-success">
                            <i class="fas fa-check-circle"></i>
                            Actif
                        </span>
                    @else
                        <span class="status-badge badge-secondary">
                            <i class="fas fa-pause-circle"></i>
                            Inactif
                        </span>
                    @endif
                    
                    @php
                        $stock = $article->stock_total ?? 0;
                        $seuil = $article->ART_STOCK_MIN ?? 10;
                    @endphp
                    
                    @if($stock <= 0)
                        <span class="status-badge badge-danger ml-2">
                            <i class="fas fa-times-circle"></i>
                            Rupture de Stock
                        </span>
                    @elseif($stock <= $seuil)
                        <span class="status-badge badge-warning ml-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            Stock Faible
                        </span>
                    @else
                        <span class="status-badge badge-success ml-2">
                            <i class="fas fa-check"></i>
                            Stock OK
                        </span>
                    @endif
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="row">
                    <div class="col-6">
                        <h3 class="price-display mb-0">{{ number_format($article->ART_PRIX_VENTE ?? 0, 2) }}</h3>
                        <small class="opacity-75">Prix de Vente (DH)</small>
                    </div>
                    <div class="col-6">
                        <h3 class="price-display mb-0">{{ $article->stock_total ?? 0 }}</h3>
                        <small class="opacity-75">Stock Disponible</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    
    {{-- Informations Générales --}}
    <div class="col-xl-8 col-lg-7">
        
        {{-- Informations Détaillées --}}
        <div class="info-card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle"></i>
                    Informations Détaillées
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table-modern table">
                            <tr>
                                <td class="font-weight-bold text-gray-800">Nom:</td>
                                <td>{{ $article->ART_DESIGNATION ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-800">Code:</td>
                                <td>
                                    @if($article->ART_REF)
                                        <code class="bg-light px-2 py-1 rounded">{{ $article->ART_REF }}</code>
                                    @else
                                        <span class="text-muted">Aucun code défini</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-800">Référence Famille:</td>
                                <td>
                                    @if($article->SFM_REF)
                                        <span class="badge badge-secondary">{{ $article->SFM_REF }}</span>
                                    @else
                                        <span class="text-muted">Aucune famille</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-800">Statut Vente:</td>
                                <td>
                                    @if($article->ART_VENTE == 1)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i>
                                            Autorisé
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-ban"></i>
                                            Non autorisé
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table-modern table">
                            <tr>
                                <td class="font-weight-bold text-gray-800">Prix d'Achat:</td>
                                <td>
                                    @if($article->ART_PRIX_ACHAT)
                                        <span class="text-info font-weight-bold">{{ number_format($article->ART_PRIX_ACHAT, 2) }} DH</span>
                                    @else
                                        <span class="text-muted">Non défini</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-800">Prix de Vente:</td>
                                <td>
                                    <span class="text-success font-weight-bold">{{ number_format($article->ART_PRIX_VENTE ?? 0, 2) }} DH</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-800">Marge:</td>
                                <td>
                                    @php
                                        $marge = ($article->ART_PRIX_VENTE ?? 0) - ($article->ART_PRIX_ACHAT ?? 0);
                                    @endphp
                                    <span class="font-weight-bold {{ $marge >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($marge, 2) }} DH
                                        @if($article->ART_PRIX_ACHAT && $article->ART_PRIX_ACHAT > 0)
                                            ({{ number_format(($marge / $article->ART_PRIX_ACHAT) * 100, 1) }}%)
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-800">TVA Vente:</td>
                                <td>
                                    @if($article->ART_TVA_VENTE)
                                        {{ $article->ART_TVA_VENTE }}%
                                    @else
                                        <span class="text-muted">Non définie</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($article->ART_DESCRIPTION)
                    <div class="mt-4">
                        <h6 class="font-weight-bold text-gray-800">Description:</h6>
                        <p class="text-gray-700">{{ $article->ART_DESCRIPTION }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Gestion du Stock --}}
        <div class="info-card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-warehouse"></i>
                    Gestion du Stock
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stock-indicator border-left-primary">
                            <h3 class="font-weight-bold text-primary">{{ $article->stock_total ?? 0 }}</h3>
                            <p class="mb-0 text-gray-600">Stock Actuel</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stock-indicator border-left-warning">
                            <h3 class="font-weight-bold text-warning">{{ $article->ART_STOCK_MIN ?? 'N/A' }}</h3>
                            <p class="mb-0 text-gray-600">Stock Minimum</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stock-indicator border-left-info">
                            <h3 class="font-weight-bold text-info">{{ $article->ART_STOCK_MAX ?? 'N/A' }}</h3>
                            <p class="mb-0 text-gray-600">Stock Maximum</p>
                        </div>
                    </div>
                </div>
                
                {{-- Barre de progression du stock --}}
                <div class="mt-4">
                    <label class="small mb-1 font-weight-bold">Niveau de Stock</label>
                    @php
                        $stockActuel = $article->stock_total ?? 0;
                        $stockMin = $article->ART_STOCK_MIN ?? 10;
                        $stockMax = $article->ART_STOCK_MAX ?? 100;
                        $percentage = $stockMax > 0 ? min(100, ($stockActuel / $stockMax) * 100) : 0;
                        $progressClass = $stockActuel <= 0 ? 'bg-danger' : ($stockActuel <= $stockMin ? 'bg-warning' : 'bg-success');
                    @endphp
                    <div class="progress progress-modern">
                        <div class="progress-bar progress-bar-modern {{ $progressClass }}" 
                             role="progressbar" 
                             style="width: {{ $percentage }}%" 
                             aria-valuenow="{{ $percentage }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <small class="text-muted">{{ $stockActuel }} unités</small>
                        <small class="text-muted">{{ $percentage }}%</small>
                    </div>
                </div>

                {{-- Options de stock --}}
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" 
                                   class="custom-control-input" 
                                   id="stockable" 
                                   {{ ($article->ART_STOCKABLE ?? true) ? 'checked' : '' }} 
                                   disabled>
                            <label class="custom-control-label" for="stockable">
                                Produit stockable
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" 
                                   class="custom-control-input" 
                                   id="achat_autorise" 
                                   {{ ($article->ART_ACHAT ?? true) ? 'checked' : '' }} 
                                   disabled>
                            <label class="custom-control-label" for="achat_autorise">
                                Achat autorisé
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistiques de Vente --}}
        <div class="info-card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-chart-line"></i>
                    Statistiques de Vente
                </h6>
            </div>
            <div class="card-body">
                @if(isset($statistiques))
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="stat-card border-left-success">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Ventes du Jour
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $statistiques['ventes_jour'] ?? 0 }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="stat-card border-left-info">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Ventes du Mois
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $statistiques['ventes_mois'] ?? 0 }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="stat-card border-left-warning">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    CA Généré
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($statistiques['ca_genere'] ?? 0, 2) }} DH
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="stat-card border-left-primary">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Dernière Vente
                                </div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                    {{ isset($statistiques['derniere_vente']) ? $statistiques['derniere_vente']->format('d/m/Y') : 'Jamais' }}
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-chart-line fa-4x mb-3 text-gray-300"></i>
                        <h5 class="text-gray-400">Aucune statistique disponible</h5>
                        <p class="text-gray-400">Les statistiques de vente s'afficheront ici une fois que des ventes seront enregistrées.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Colonne de droite --}}
    <div class="col-xl-4 col-lg-5">
        
        {{-- Actions Rapides --}}
        <div class="info-card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-cog"></i>
                    Actions Rapides
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.articles.edit', $article->ART_REF) }}" class="btn btn-warning action-btn">
                        <i class="fas fa-edit"></i>
                        Modifier le Produit
                    </a>
                    
                    <button type="button" class="btn btn-success action-btn" onclick="addToSale()">
                        <i class="fas fa-cart-plus"></i>
                        Ajouter à une Vente
                    </button>
                    
                    <button type="button" class="btn btn-info action-btn" onclick="adjustStock()">
                        <i class="fas fa-adjust"></i>
                        Ajuster le Stock
                    </button>
                    
                    <button type="button" class="btn btn-secondary action-btn" onclick="duplicateProduct()">
                        <i class="fas fa-copy"></i>
                        Dupliquer le Produit
                    </button>
                    
                    <div class="dropdown">
                        <button class="btn btn-outline-primary action-btn dropdown-toggle w-100" type="button" data-toggle="dropdown">
                            <i class="fas fa-download"></i>
                            Exporter
                        </button>
                        <div class="dropdown-menu w-100">
                            <a class="dropdown-item" href="#" onclick="exportProduct('pdf')">
                                <i class="fas fa-file-pdf mr-2 text-danger"></i>
                                PDF
                            </a>
                            <a class="dropdown-item" href="#" onclick="exportProduct('excel')">
                                <i class="fas fa-file-excel mr-2 text-success"></i>
                                Excel
                            </a>
                            <a class="dropdown-item" href="#" onclick="exportProduct('json')">
                                <i class="fas fa-file-code mr-2 text-info"></i>
                                JSON
                            </a>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-danger action-btn" onclick="confirmDelete()">
                        <i class="fas fa-trash"></i>
                        Supprimer le Produit
                    </button>
                </div>
            </div>
        </div>

        {{-- Informations Système --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-secondary">
                    <i class="fas fa-info"></i>
                    Informations Système
                </h6>
            </div>
            <div class="card-body">
                <div class="small">
                    <div class="mb-3">
                        <strong>ID du Produit:</strong><br>
                        <code class="bg-light px-2 py-1 rounded">#{{ $article->ART_REF }}</code>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Créé le:</strong><br>
                        {{ $article->created_at ? $article->created_at->format('d/m/Y à H:i') : 'N/A' }}
                    </div>
                    
                    <div class="mb-3">
                        <strong>Dernière modification:</strong><br>
                        {{ $article->updated_at ? $article->updated_at->format('d/m/Y à H:i') : 'N/A' }}
                    </div>
                    
                    @if(isset($article->user))
                        <div class="mb-3">
                            <strong>Créé par:</strong><br>
                            {{ $article->user->name ?? 'N/A' }}
                        </div>
                    @endif
                    
                    @if($article->updated_at && $article->created_at && $article->updated_at != $article->created_at)
                        <div>
                            <strong>Nombre de modifications:</strong><br>
                            <span class="badge badge-info">{{ $article->updated_at->diffInDays($article->created_at) + 1 }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Image Agrandie --}}
        @if($article->image && file_exists(public_path('storage/' . $article->image)))
            <div class="info-card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-image"></i>
                        Image du Produit
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="image-preview">
                        <img src="{{ asset('storage/' . $article->image) }}" 
                             alt="{{ $article->ART_DESIGNATION }}" 
                             class="img-fluid rounded" 
                             onclick="showImageModal(this.src)"
                             style="max-height: 300px;">
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-primary action-btn" onclick="showImageModal('{{ asset('storage/' . $article->image) }}')">
                            <i class="fas fa-expand"></i>
                            Agrandir l'Image
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Alertes --}}
        @php
            $alerts = [];
            
            if(($article->stock ?? 0) <= 0) {
                $alerts[] = ['type' => 'danger', 'icon' => 'fas fa-times-circle', 'message' => 'Produit en rupture de stock'];
            } elseif(($article->stock ?? 0) <= ($article->seuil_alerte ?? 10)) {
                $alerts[] = ['type' => 'warning', 'icon' => 'fas fa-exclamation-triangle', 'message' => 'Stock faible - Réapprovisionnement recommandé'];
            }
            
            if($article->statut == 'inactif') {
                $alerts[] = ['type' => 'info', 'icon' => 'fas fa-pause-circle', 'message' => 'Produit inactif - Non visible dans les ventes'];
            }
            
            $marge = ($article->prix ?? 0) - ($article->prix_achat ?? 0);
            if($article->prix_achat && $marge < 0) {
                $alerts[] = ['type' => 'danger', 'icon' => 'fas fa-exclamation-triangle', 'message' => 'Marge négative - Vente à perte'];
            }
        @endphp

        @if(count($alerts) > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-bell"></i>
                        Alertes ({{ count($alerts) }})
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($alerts as $alert)
                        <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
                            <i class="{{ $alert['icon'] }} mr-2"></i>
                            {{ $alert['message'] }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Modal d'image --}}
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $article->ART_DESIGNATION }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="{{ $article->ART_DESIGNATION }}" class="img-fluid">
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmation de suppression --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                    Confirmer la Suppression
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le produit <strong>{{ $article->ART_DESIGNATION }}</strong> ?</p>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Cette action est irréversible et supprimera également toutes les données associées !
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form method="POST" action="{{ route('admin.articles.destroy', $article->ART_REF) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer Définitivement</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function showImageModal(imageSrc) {
    $('#modalImage').attr('src', imageSrc);
    $('#imageModal').modal('show');
}

function addToSale() {
    // Redirection vers la création d'une vente avec ce produit pré-sélectionné
    window.location.href = '{{ route("admin.sales.create") }}?product_id={{ $article->ART_REF }}';
}

function adjustStock() {
    Swal.fire({
        title: 'Ajuster le Stock',
        html: `
            <div class="text-left">
                <p>Stock actuel: <strong>{{ $article->stock ?? 0 }}</strong> unités</p>
                <div class="form-group">
                    <label for="swal-adjustment-type">Type d'ajustement:</label>
                    <select class="form-control" id="swal-adjustment-type">
                        <option value="add">Ajouter au stock</option>
                        <option value="remove">Retirer du stock</option>
                        <option value="set">Définir le stock à</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="swal-adjustment-quantity">Quantité:</label>
                    <input type="number" class="form-control" id="swal-adjustment-quantity" min="0" value="0">
                </div>
                <div class="form-group">
                    <label for="swal-adjustment-reason">Raison (optionnel):</label>
                    <textarea class="form-control" id="swal-adjustment-reason" rows="2" placeholder="Ex: Inventaire, Retour client, etc."></textarea>
                </div>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Appliquer',
        cancelButtonText: 'Annuler',
        confirmButtonClass: 'btn btn-primary',
        cancelButtonClass: 'btn btn-secondary',
        preConfirm: () => {
            const type = document.getElementById('swal-adjustment-type').value;
            const quantity = parseInt(document.getElementById('swal-adjustment-quantity').value) || 0;
            const reason = document.getElementById('swal-adjustment-reason').value;
            
            if (quantity <= 0) {
                Swal.showValidationMessage('Veuillez saisir une quantité valide');
                return false;
            }
            
            return { type, quantity, reason };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Ici vous implémenteriez la logique AJAX pour ajuster le stock
            console.log('Stock adjustment:', result.value);
            
            Swal.fire({
                icon: 'success',
                title: 'Stock Ajusté',
                text: 'Le stock a été ajusté avec succès.',
                confirmButtonClass: 'btn btn-success'
            }).then(() => {
                // Recharger la page pour voir les changements
                window.location.reload();
            });
        }
    });
}

function duplicateProduct() {
    window.location.href = '{{ route("admin.articles.create") }}?duplicate={{ $article->ART_REF }}';
}

function exportProduct(format) {
    // Logique d'export selon le format
    console.log('Export product in ' + format + ' format');
    
    // Exemple d'URL d'export
    // window.open('{{ route("admin.articles.export", $article->id) }}?format=' + format, '_blank');
    
    Swal.fire({
        icon: 'info',
        title: 'Export en cours',
        text: 'Le fichier ' + format.toUpperCase() + ' sera téléchargé dans quelques instants.',
        confirmButtonClass: 'btn btn-primary'
    });
}

function confirmDelete() {
    $('#deleteModal').modal('show');
}

// Auto-refresh des statistiques (optionnel)
setInterval(function() {
    // Ici vous pourriez actualiser les statistiques via AJAX
    // sans recharger toute la page
}, 60000); // Toutes les minutes
</script>
@endsection
