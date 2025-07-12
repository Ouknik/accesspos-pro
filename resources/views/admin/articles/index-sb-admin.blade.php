@extends('layouts.sb-admin')

@section('title', 'Gestion des Produits - AccessPos Pro')

@section('page-heading')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-boxes"></i>
            Gestion des Produits
        </h1>
        <p class="mb-0 text-muted">Gestion complète de tous les produits et articles</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.articles.analytics') }}" class="btn btn-info btn-sm">
            <i class="fas fa-chart-bar"></i>
            Analyses
        </a>
        <a href="{{ route('admin.articles.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i>
            Ajouter un Produit
        </a>
    </div>
</div>
@endsection

@section('content')

{{-- Statistiques Rapides --}}
<div class="row mb-4">
    
    {{-- Total Produits --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Produits
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $stats['total'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Produits Actifs --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Actifs
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $stats['actifs'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stock Faible --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Stock Faible
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $stats['stock_faible'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Rupture de Stock --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Rupture de Stock
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $stats['rupture'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Section de Recherche et Filtres --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-search"></i>
            Recherche et Filtres
        </h6>
    </div>
    <div class="card-body bg-light">
        <form method="GET" action="{{ route('admin.articles.index') }}" id="searchForm">
            <div class="row">
                {{-- Recherche par nom --}}
                <div class="col-md-4 mb-3">
                    <label for="search" class="form-label">Recherche par nom</label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Nom du produit...">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Filtre par catégorie --}}
                <div class="col-md-3 mb-3">
                    <label for="category_id" class="form-label">Catégorie</label>
                    <select class="form-control" id="category_id" name="category_id">
                        <option value="">Toutes les catégories</option>
                        @if(isset($categories))
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->nom }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Filtre par statut --}}
                <div class="col-md-2 mb-3">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">Tous</option>
                        <option value="actif" {{ request('status') == 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ request('status') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>

                {{-- Filtre par stock --}}
                <div class="col-md-2 mb-3">
                    <label for="stock_filter" class="form-label">Stock</label>
                    <select class="form-control" id="stock_filter" name="stock_filter">
                        <option value="">Tous</option>
                        <option value="faible" {{ request('stock_filter') == 'faible' ? 'selected' : '' }}>Stock faible</option>
                        <option value="rupture" {{ request('stock_filter') == 'rupture' ? 'selected' : '' }}>Rupture</option>
                    </select>
                </div>

                {{-- Boutons d'action --}}
                <div class="col-md-1 mb-3 d-flex align-items-end">
                    <div class="d-flex flex-column w-100">
                        <button type="submit" class="btn btn-primary btn-sm mb-1">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Actions en Lot --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-tasks"></i>
            Actions en Lot
        </h6>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-success" onclick="exportSelected('excel')">
                <i class="fas fa-file-excel"></i>
                Excel
            </button>
            <button type="button" class="btn btn-sm btn-danger" onclick="exportSelected('pdf')">
                <i class="fas fa-file-pdf"></i>
                PDF
            </button>
            <button type="button" class="btn btn-sm btn-warning" onclick="activateSelected()">
                <i class="fas fa-toggle-on"></i>
                Activer
            </button>
            <button type="button" class="btn btn-sm btn-secondary" onclick="deactivateSelected()">
                <i class="fas fa-toggle-off"></i>
                Désactiver
            </button>
        </div>
    </div>
    <div class="card-body">
        <div id="bulk-actions-info" class="alert alert-info" style="display: none;">
            <span id="selected-count">0</span> produit(s) sélectionné(s)
        </div>
    </div>
</div>

{{-- Tableau des Produits --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list"></i>
            Liste des Produits
        </h6>
        <div class="d-flex align-items-center">
            <span class="text-muted mr-3">
                @if(isset($articles) && is_object($articles) && method_exists($articles, 'total'))
                    {{ $articles->total() }} produit(s) au total
                @elseif(isset($articles) && is_array($articles))
                    {{ count($articles) }} produit(s) au total
                @else
                    0 produit(s) au total
                @endif
            </span>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Actions:</div>
                    <a class="dropdown-item" href="#" onclick="refreshTable()">
                        <i class="fas fa-sync mr-2"></i>
                        Actualiser
                    </a>
                    <a class="dropdown-item" href="#" onclick="exportAll('excel')">
                        <i class="fas fa-file-excel mr-2"></i>
                        Exporter Excel
                    </a>
                    <a class="dropdown-item" href="#" onclick="exportAll('pdf')">
                        <i class="fas fa-file-pdf mr-2"></i>
                        Exporter PDF
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" onclick="importModal()">
                        <i class="fas fa-upload mr-2"></i>
                        Importer
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="5%">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="selectAll">
                                <label class="custom-control-label" for="selectAll"></label>
                            </div>
                        </th>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($articles))
                        @php
                            $articlesArray = is_object($articles) && method_exists($articles, 'getIterator') ? $articles : (is_array($articles) ? $articles : []);
                            $hasArticles = is_object($articles) ? (method_exists($articles, 'count') ? $articles->count() > 0 : count($articles) > 0) : (is_array($articles) ? count($articles) > 0 : false);
                        @endphp
                        
                        @if($hasArticles)
                            @foreach($articlesArray as $article)
                            <tr>
                            <td>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input article-checkbox" 
                                           id="check{{ $article->id }}"
                                           value="{{ $article->id }}">
                                    <label class="custom-control-label" for="check{{ $article->id }}"></label>
                                </div>
                            </td>
                            <td>
                                @if($article->image)
                                    <img src="{{ asset('storage/' . $article->image) }}" 
                                         alt="{{ $article->nom }}" 
                                         class="img-thumbnail" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-gray-200 d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px; border-radius: 4px;">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $article->nom ?? 'N/A' }}</div>
                                @if($article->description)
                                    <div class="text-xs text-muted">
                                        {{ Str::limit($article->description, 50) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($article->category)
                                    <span class="badge badge-secondary">
                                        {{ $article->category->nom }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="font-weight-bold text-success">
                                    {{ number_format($article->prix ?? 0, 2) }} DH
                                </div>
                                @if($article->prix_achat)
                                    <div class="text-xs text-muted">
                                        Achat: {{ number_format($article->prix_achat, 2) }} DH
                                    </div>
                                @endif
                            </td>
                            <td>
                                @php
                                    $stock = $article->stock ?? 0;
                                    $seuil_alerte = $article->seuil_alerte ?? 10;
                                @endphp
                                
                                @if($stock <= 0)
                                    <span class="badge badge-danger">
                                        <i class="fas fa-times"></i>
                                        Rupture
                                    </span>
                                @elseif($stock <= $seuil_alerte)
                                    <span class="badge badge-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ $stock }} (Faible)
                                    </span>
                                @else
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i>
                                        {{ $stock }}
                                    </span>
                                @endif
                            </td>
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
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.articles.show', $article->id) }}" 
                                       class="btn btn-info btn-sm" 
                                       title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.articles.edit', $article->id) }}" 
                                       class="btn btn-warning btn-sm" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-danger btn-sm" 
                                            title="Supprimer"
                                            onclick="confirmDelete({{ $article->id }}, '{{ $article->nom }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-box fa-3x mb-3"></i>
                                        <p class="mb-0">Aucun produit trouvé</p>
                                        <a href="{{ route('admin.articles.create') }}" class="btn btn-primary btn-sm mt-2">
                                            <i class="fas fa-plus"></i>
                                            Ajouter le premier produit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @else
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-box fa-3x mb-3"></i>
                                    <p class="mb-0">Aucun produit trouvé</p>
                                    <a href="{{ route('admin.articles.create') }}" class="btn btn-primary btn-sm mt-2">
                                        <i class="fas fa-plus"></i>
                                        Ajouter le premier produit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(isset($articles) && is_object($articles) && method_exists($articles, 'hasPages') && $articles->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Affichage de {{ $articles->firstItem() }} à {{ $articles->lastItem() }} 
                    sur {{ $articles->total() }} résultats
                </div>
                <div>
                    {{ $articles->appends(request()->query())->links() }}
                </div>
            </div>
        @elseif(isset($articles) && is_array($articles) && count($articles) > 0)
            <div class="d-flex justify-content-center mt-3">
                <div class="text-muted">
                    {{ count($articles) }} produit(s) affiché(s)
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Modal de Confirmation de Suppression --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    Confirmer la Suppression
                </h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le produit <strong id="productName"></strong> ?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Cette action est irréversible !
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">
                    <i class="fas fa-times"></i>
                    Annuler
                </button>
                <form method="POST" id="deleteForm" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialisation DataTable avec configuration SB Admin
    $('#dataTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
        },
        "pageLength": 25,
        "order": [[ 2, "asc" ]],
        "columnDefs": [
            { "orderable": false, "targets": [0, 1, 7] }
        ]
    });

    // Gestion de la sélection multiple
    $('#selectAll').change(function() {
        $('.article-checkbox').prop('checked', this.checked);
        updateBulkActions();
    });

    $('.article-checkbox').change(function() {
        updateBulkActions();
    });

    // Recherche en temps réel
    $('#search').on('input', function() {
        var searchTerm = $(this).val();
        if (searchTerm.length >= 3 || searchTerm.length === 0) {
            $('#searchForm').submit();
        }
    });
});

function updateBulkActions() {
    var selected = $('.article-checkbox:checked').length;
    $('#selected-count').text(selected);
    
    if (selected > 0) {
        $('#bulk-actions-info').show();
    } else {
        $('#bulk-actions-info').hide();
    }
    
    // Mise à jour du checkbox "Tout sélectionner"
    var total = $('.article-checkbox').length;
    $('#selectAll').prop('indeterminate', selected > 0 && selected < total);
    $('#selectAll').prop('checked', selected === total && total > 0);
}

function confirmDelete(id, name) {
    $('#productName').text(name);
    $('#deleteForm').attr('action', '{{ route("admin.articles.index") }}/' + id);
    $('#deleteModal').modal('show');
}

function exportSelected(format) {
    var selectedIds = $('.article-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Aucune sélection',
            text: 'Veuillez sélectionner au moins un produit.',
            confirmButtonClass: 'btn btn-primary'
        });
        return;
    }
    
    // Logique d'export
    console.log('Export ' + format + ' for IDs:', selectedIds);
}

function exportAll(format) {
    // Logique d'export complet
    console.log('Export all in ' + format);
}

function activateSelected() {
    var selectedIds = $('.article-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Aucune sélection',
            text: 'Veuillez sélectionner au moins un produit.',
            confirmButtonClass: 'btn btn-primary'
        });
        return;
    }
    
    // Logique d'activation
    console.log('Activate IDs:', selectedIds);
}

function deactivateSelected() {
    var selectedIds = $('.article-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Aucune sélection',
            text: 'Veuillez sélectionner au moins un produit.',
            confirmButtonClass: 'btn btn-primary'
        });
        return;
    }
    
    // Logique de désactivation
    console.log('Deactivate IDs:', selectedIds);
}

function refreshTable() {
    window.location.reload();
}

function importModal() {
    // Modal d'import
    console.log('Import modal');
}
</script>
@endsection
