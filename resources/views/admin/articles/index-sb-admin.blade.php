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
                            Produits Actifs
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $stats['active'] ?? 0 }}
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
                            {{ $stats['low_stock'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Valeur du Stock --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Valeur du Stock
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['stock_value'] ?? 0, 2) }} DA
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                    <label for="famille" class="form-label">Famille</label>
                    <select class="form-control" id="famille" name="famille">
                        <option value="">Toutes les familles</option>
                        @if(isset($familles))
                            @foreach($familles as $famille)
                                <option value="{{ $famille->FAM_REF }}" 
                                        {{ request('famille') == $famille->FAM_REF ? 'selected' : '' }}>
                                    {{ $famille->FAM_LIB }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Filtre par statut --}}
                <div class="col-md-2 mb-3">
                    <label for="statut" class="form-label">Statut</label>
                    <select class="form-control" id="statut" name="statut">
                        <option value="">Tous</option>
                        <option value="1" {{ request('statut') == '1' ? 'selected' : '' }}>Actif</option>
                        <option value="0" {{ request('statut') == '0' ? 'selected' : '' }}>Inactif</option>
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


{{-- Tableau des Produits --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list"></i>
            Liste des Produits
        </h6>
        <div class="d-flex align-items-center">
            <span class="text-muted mr-3">
                @if(isset($articles))
                    @if(is_object($articles) && method_exists($articles, 'total'))
                        {{ $articles->total() }} produit(s) au total
                    @elseif(is_countable($articles))
                        {{ count($articles) }} produit(s) au total
                    @else
                        0 produit(s) au total
                    @endif
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
                    @if(isset($articles) && count($articles) > 0)
                        @foreach($articles as $article)
                            <tr>
                            <td>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input article-checkbox" 
                                           id="check{{ $article->ART_REF }}"
                                           value="{{ $article->ART_REF }}">
                                    <label class="custom-control-label" for="check{{ $article->ART_REF }}"></label>
                                </div>
                            </td>
                            <td>
                                {{-- Pas d'images dans la structure actuelle --}}
                                <div class="bg-gray-200 d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px; border-radius: 4px;">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $article->ART_DESIGNATION ?? 'N/A' }}</div>
                                <div class="text-xs text-muted">
                                    Réf: {{ $article->ART_REF }}
                                </div>
                            </td>
                            <td>
                                @if($article->famille)
                                    <span class="badge badge-secondary">
                                        {{ $article->famille }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="font-weight-bold text-success">
                                    {{ number_format($article->ART_PRIX_VENTE ?? 0, 2) }} DA
                                </div>
                                @if($article->ART_PRIX_ACHAT)
                                    <div class="text-xs text-muted">
                                        Achat: {{ number_format($article->ART_PRIX_ACHAT, 2) }} DA
                                    </div>
                                @endif
                            </td>
                            <td>
                                @php
                                    $stock = $article->stock_total ?? 0;
                                    $seuil_alerte = $article->ART_STOCK_MIN ?? 10;
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
                                @if($article->ART_VENTE == 1)
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
                                    <a href="{{ route('admin.articles.show', $article->ART_REF) }}" 
                                       class="btn btn-info btn-sm" 
                                       title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.articles.edit', $article->ART_REF) }}" 
                                       class="btn btn-warning btn-sm" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-danger btn-sm" 
                                            title="Supprimer"
                                            onclick="confirmDelete('{{ $article->ART_REF }}', '{{ $article->ART_DESIGNATION }}')">
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
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(isset($articles) && is_object($articles) && method_exists($articles, 'hasPages') && $articles->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    <small>
                        Affichage de {{ $articles->firstItem() }} à {{ $articles->lastItem() }} 
                        sur {{ $articles->total() }} résultats
                    </small>
                </div>
                <div>
                    {{ $articles->appends(request()->query())->links('pagination.sb-admin') }}
                </div>
            </div>
        @elseif(isset($articles) && count($articles) > 0)
            <div class="d-flex justify-content-center mt-4">
                <div class="text-muted">
                    <small>{{ count($articles) }} produit(s) affiché(s)</small>
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

function confirmDelete(artRef, name) {
    $('#productName').text(name);
    $('#deleteForm').attr('action', '/admin/articles/' + artRef);
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
