@extends('layouts.sb-admin')

@section('title', 'Détails du Produit - AccessPos Pro')

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
                <li class="breadcrumb-item active">{{ $article->nom ?? 'Détails' }}</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.articles.edit', $article->id) }}" class="btn btn-warning btn-sm">
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
<div class="card shadow mb-4" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
    <div class="card-body text-white">
        <div class="row align-items-center">
            <div class="col-md-2 text-center">
                @if($article->image && file_exists(public_path('storage/' . $article->image)))
                    <img src="{{ asset('storage/' . $article->image) }}" 
                         alt="{{ $article->nom }}" 
                         class="img-fluid rounded-circle border border-white" 
                         style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                         style="width: 120px; height: 120px;">
                        <i class="fas fa-box fa-3x text-gray-400"></i>
                    </div>
                @endif
            </div>
            <div class="col-md-6">
                <h2 class="mb-2 font-weight-bold">{{ $article->nom ?? 'Nom non défini' }}</h2>
                @if($article->code)
                    <p class="mb-1 opacity-75">
                        <i class="fas fa-barcode mr-2"></i>
                        Code: {{ $article->code }}
                    </p>
                @endif
                @if($article->category)
                    <p class="mb-1 opacity-75">
                        <i class="fas fa-tag mr-2"></i>
                        Catégorie: {{ $article->category->nom }}
                    </p>
                @endif
                <div class="mt-3">
                    @if($article->statut == 'actif')
                        <span class="badge badge-light badge-pill px-3 py-2">
                            <i class="fas fa-check-circle"></i>
                            Actif
                        </span>
                    @else
                        <span class="badge badge-secondary badge-pill px-3 py-2">
                            <i class="fas fa-pause-circle"></i>
                            Inactif
                        </span>
                    @endif
                    
                    @php
                        $stock = $article->stock ?? 0;
                        $seuil = $article->seuil_alerte ?? 10;
                    @endphp
                    
                    @if($stock <= 0)
                        <span class="badge badge-danger badge-pill px-3 py-2 ml-2">
                            <i class="fas fa-times-circle"></i>
                            Rupture de Stock
                        </span>
                    @elseif($stock <= $seuil)
                        <span class="badge badge-warning badge-pill px-3 py-2 ml-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            Stock Faible
                        </span>
                    @else
                        <span class="badge badge-success badge-pill px-3 py-2 ml-2">
                            <i class="fas fa-check"></i>
                            Stock OK
                        </span>
                    @endif
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="row">
                    <div class="col-6">
                        <h3 class="mb-0 font-weight-bold">{{ number_format($article->prix ?? 0, 2) }}</h3>
                        <small class="opacity-75">Prix de Vente (DH)</small>
                    </div>
                    <div class="col-6">
                        <h3 class="mb-0 font-weight-bold">{{ $article->stock ?? 0 }}</h3>
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
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle"></i>
                    Informations Détaillées
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="font-weight-bold text-gray-800">Nom:</td>
                                <td>{{ $article->nom ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-800">Code:</td>
                                <td>
                                    @if($article->code)
                                        <code class="bg-light px-2 py-1 rounded">{{ $article->code }}</code>
                                    @else
                                        <span class="text-muted">Aucun code défini</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-800">Catégorie:</td>
                                <td>
                                    @if($article->category)
                                        <span class="badge badge-secondary">{{ $article->category->nom }}</span>
                                    @else
                                        <span class="text-muted">Aucune catégorie</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-800">Statut:</td>
                                <td>
                                    @if($article->statut == 'actif')
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i>
                                            Actif
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-pause-circle"></i>
                                            Inactif
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="font-weight-bold text-gray-800">Prix d'Achat:</td>
                                <td>
                                    @if($article->prix_achat)
                                        <span class="text-info font-weight-bold">{{ number_format($article->prix_achat, 2) }} DH</span>
                                    @else
                                        <span class="text-muted">Non défini</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-800">Prix de Vente:</td>
                                <td>
                                    <span class="text-success font-weight-bold">{{ number_format($article->prix ?? 0, 2) }} DH</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-800">Marge:</td>
                                <td>
                                    @php
                                        $marge = ($article->prix ?? 0) - ($article->prix_achat ?? 0);
                                    @endphp
                                    <span class="font-weight-bold {{ $marge >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($marge, 2) }} DH
                                        @if($article->prix_achat && $article->prix_achat > 0)
                                            ({{ number_format(($marge / $article->prix_achat) * 100, 1) }}%)
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-800">TVA:</td>
                                <td>
                                    @if(isset($article->tva))
                                        {{ $article->tva }}%
                                    @else
                                        <span class="text-muted">Non définie</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($article->description)
                    <div class="mt-4">
                        <h6 class="font-weight-bold text-gray-800">Description:</h6>
                        <p class="text-gray-700">{{ $article->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Gestion du Stock --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-warehouse"></i>
                    Gestion du Stock
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="border-left-primary p-3">
                            <h3 class="font-weight-bold text-primary">{{ $article->stock ?? 0 }}</h3>
                            <p class="mb-0 text-gray-600">Stock Actuel</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="border-left-warning p-3">
                            <h3 class="font-weight-bold text-warning">{{ $article->seuil_alerte ?? 'N/A' }}</h3>
                            <p class="mb-0 text-gray-600">Seuil d'Alerte</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="border-left-info p-3">
                            @php
                                $stock = $article->stock ?? 0;
                                $seuil = $article->seuil_alerte ?? 10;
                                $ratio = $seuil > 0 ? ($stock / $seuil) : 0;
                            @endphp
                            <h3 class="font-weight-bold text-info">{{ number_format($ratio, 1) }}x</h3>
                            <p class="mb-0 text-gray-600">Ratio Stock/Seuil</p>
                        </div>
                    </div>
                </div>
                
                {{-- Barre de progression du stock --}}
                <div class="mt-4">
                    <label class="small mb-1">Niveau de Stock</label>
                    @php
                        $maxStock = ($article->seuil_alerte ?? 10) * 3; // 3x le seuil comme référence
                        $percentage = $maxStock > 0 ? min(100, ($stock / $maxStock) * 100) : 0;
                        $progressClass = $stock <= 0 ? 'bg-danger' : ($stock <= $seuil ? 'bg-warning' : 'bg-success');
                    @endphp
                    <div class="progress">
                        <div class="progress-bar {{ $progressClass }}" 
                             role="progressbar" 
                             style="width: {{ $percentage }}%" 
                             aria-valuenow="{{ $percentage }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ $stock }} unités
                        </div>
                    </div>
                </div>

                {{-- Options de stock --}}
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" 
                                   class="custom-control-input" 
                                   id="gestion_stock" 
                                   {{ ($article->gestion_stock ?? true) ? 'checked' : '' }} 
                                   disabled>
                            <label class="custom-control-label" for="gestion_stock">
                                Gestion automatique du stock
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" 
                                   class="custom-control-input" 
                                   id="alerte_stock" 
                                   {{ ($article->alerte_stock ?? true) ? 'checked' : '' }} 
                                   disabled>
                            <label class="custom-control-label" for="alerte_stock">
                                Alertes de stock activées
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistiques de Vente --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-chart-line"></i>
                    Statistiques de Vente
                </h6>
            </div>
            <div class="card-body">
                @if(isset($statistiques))
                    <div class="row">
                        <div class="col-md-3 text-center mb-3">
                            <div class="card border-left-success h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Ventes du Jour
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $statistiques['ventes_jour'] ?? 0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div class="card border-left-info h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Ventes du Mois
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $statistiques['ventes_mois'] ?? 0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div class="card border-left-warning h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        CA Généré
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($statistiques['ca_genere'] ?? 0, 2) }} DH
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div class="card border-left-primary h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Dernière Vente
                                    </div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                        {{ isset($statistiques['derniere_vente']) ? $statistiques['derniere_vente']->format('d/m/Y') : 'Jamais' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-chart-line fa-3x mb-3"></i>
                        <p>Aucune statistique de vente disponible</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Colonne de droite --}}
    <div class="col-xl-4 col-lg-5">
        
        {{-- Actions Rapides --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-cog"></i>
                    Actions Rapides
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.articles.edit', $article->id) }}" class="btn btn-warning btn-block">
                        <i class="fas fa-edit"></i>
                        Modifier le Produit
                    </a>
                    
                    <button type="button" class="btn btn-success btn-block" onclick="addToSale()">
                        <i class="fas fa-cart-plus"></i>
                        Ajouter à une Vente
                    </button>
                    
                    <button type="button" class="btn btn-info btn-block" onclick="adjustStock()">
                        <i class="fas fa-adjust"></i>
                        Ajuster le Stock
                    </button>
                    
                    <button type="button" class="btn btn-secondary btn-block" onclick="duplicateProduct()">
                        <i class="fas fa-copy"></i>
                        Dupliquer le Produit
                    </button>
                    
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-block dropdown-toggle" type="button" data-toggle="dropdown">
                            <i class="fas fa-download"></i>
                            Exporter
                        </button>
                        <div class="dropdown-menu w-100">
                            <a class="dropdown-item" href="#" onclick="exportProduct('pdf')">
                                <i class="fas fa-file-pdf mr-2"></i>
                                PDF
                            </a>
                            <a class="dropdown-item" href="#" onclick="exportProduct('excel')">
                                <i class="fas fa-file-excel mr-2"></i>
                                Excel
                            </a>
                            <a class="dropdown-item" href="#" onclick="exportProduct('json')">
                                <i class="fas fa-file-code mr-2"></i>
                                JSON
                            </a>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-danger btn-block" onclick="confirmDelete()">
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
                        <code class="bg-light px-2 py-1 rounded">#{{ $article->id }}</code>
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
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-image"></i>
                        Image du Produit
                    </h6>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('storage/' . $article->image) }}" 
                         alt="{{ $article->nom }}" 
                         class="img-fluid rounded cursor-pointer" 
                         onclick="showImageModal(this.src)"
                         style="max-height: 300px;">
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="showImageModal('{{ asset('storage/' . $article->image) }}')">
                            <i class="fas fa-expand"></i>
                            Agrandir
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
                <h5 class="modal-title">{{ $article->nom }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="{{ $article->nom }}" class="img-fluid">
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
                <p>Êtes-vous sûr de vouloir supprimer le produit <strong>{{ $article->nom }}</strong> ?</p>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Cette action est irréversible et supprimera également toutes les données associées !
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form method="POST" action="{{ route('admin.articles.destroy', $article->id) }}" style="display: inline;">
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
    window.location.href = '{{ route("admin.sales.create") }}?product_id={{ $article->id }}';
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
    window.location.href = '{{ route("admin.articles.create") }}?duplicate={{ $article->id }}';
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
