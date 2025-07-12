@extends('admin.layouts.app')

@section('title', 'Gestion des produits')

@section('styles')
<style>
    .badge-status-active { background-color: #28a745; }
    .badge-status-inactive { background-color: #dc3545; }
    .badge-stock-low { background-color: #ffc107; color: #000; }
    .badge-stock-ok { background-color: #28a745; }
    .card-stats { transition: transform 0.2s; }
    .card-stats:hover { transform: translateY(-5px); }
    .table-actions .btn { margin: 0 2px; }
    .search-section { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Gestion des produits</h1>
            <p class="text-muted">Gestion complète de tous les produits et articles</p>
        </div>
        <div>
            <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un nouveau produit
            </a>
            <a href="{{ route('admin.articles.analytics') }}" class="btn btn-info">
                <i class="fas fa-chart-bar"></i> Analyses
            </a>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="stats-grid">
        <div class="card text-white bg-primary card-stats">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total produits</h5>
                        <h2 class="mb-0">{{ $stats['total'] ?? 0 }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-boxes fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card text-white bg-success card-stats">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Produits actifs</h5>
                        <h2 class="mb-0">{{ $stats['active'] ?? 0 }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card text-white bg-warning card-stats">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Stock faible</h5>
                        <h2 class="mb-0">{{ $stats['low_stock'] ?? 0 }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card text-white bg-info card-stats">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Valeur du stock</h5>
                        <h2 class="mb-0">{{ number_format($stats['stock_value'] ?? 0, 2) }} DA</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-money-bill fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- البحث والفلاتر -->
    <div class="search-section">
        <form method="GET" action="{{ route('admin.articles.index') }}" class="row">
            <div class="col-md-3">
                <label class="form-label">Recherche</label>
                <input type="text" name="search" class="form-control" placeholder="Nom du produit ou code..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Famille</label>
                <select name="famille" class="form-select">
                    <option value="">Toutes les familles</option>
                    @foreach($familles as $famille)
                        <option value="{{ $famille->FAM_REF }}" 
                                {{ request('famille') == $famille->FAM_REF ? 'selected' : '' }}>
                            {{ $famille->FAM_LIB }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">État</label>
                <select name="statut" class="form-select">
                    <option value="">Tous les états</option>
                    <option value="1" {{ request('statut') == '1' ? 'selected' : '' }}>Actif</option>
                    <option value="0" {{ request('statut') == '0' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Nombre de résultats</label>
                <select name="per_page" class="form-select">
                    <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15</option>
                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search"></i> Rechercher
                </button>
                <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Tableau des produits -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Liste des produits ({{ $articles->total() }} produits)</h5>
            <div>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                    <i class="fas fa-download"></i> Exporter
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Code produit</th>
                            <th>Nom du produit</th>
                            <th>Famille</th>
                            <th>Sous-catégorie</th>
                            <th>Prix d'achat</th>
                            <th>Prix de vente</th>
                            <th>Stock</th>
                            <th>Statut</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $article)
                        <tr>
                            <td><code>{{ $article->ART_REF }}</code></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($article->IsMenu)
                                        <i class="fas fa-utensils text-warning me-2" title="Produit de menu"></i>
                                    @endif
                                    <strong>{{ $article->ART_DESIGNATION }}</strong>
                                </div>
                            </td>
                            <td>
                                @if($article->famille)
                                    <span class="badge bg-secondary">{{ $article->famille }}</span>
                                @else
                                    <span class="text-muted">Non spécifié</span>
                                @endif
                            </td>
                            <td>
                                @if($article->sous_famille)
                                    <span class="badge bg-light text-dark">{{ $article->sous_famille }}</span>
                                @else
                                    <span class="text-muted">Non spécifié</span>
                                @endif
                            </td>
                            <td>
                                @if($article->ART_PRIX_ACHAT)
                                    <span class="text-success">{{ number_format($article->ART_PRIX_ACHAT, 2) }} DA</span>
                                @else
                                    <span class="text-muted">Non spécifié</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-primary fw-bold">{{ number_format($article->ART_PRIX_VENTE, 2) }} DA</span>
                            </td>
                            <td>
                                @if($article->ART_STOCKABLE)
                                    @if($article->stock_total <= $article->ART_STOCK_MIN)
                                        <span class="badge badge-stock-low">
                                            {{ $article->stock_total }} (faible)
                                        </span>
                                    @else
                                        <span class="badge badge-stock-ok">
                                            {{ $article->stock_total }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted">Non stockable</span>
                                @endif
                            </td>
                            <td>
                                @if($article->ART_VENTE)
                                    <span class="badge badge-status-active">Actif</span>
                                @else
                                    <span class="badge badge-status-inactive">Inactif</span>
                                @endif
                            </td>
                            <td>
                                @if($article->ART_DATE_CREATION)
                                    {{ \Carbon\Carbon::parse($article->ART_DATE_CREATION)->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">Non spécifié</span>
                                @endif
                            </td>
                            <td class="table-actions">
                                <a href="{{ route('admin.articles.show', $article->ART_REF) }}" 
                                   class="btn btn-sm btn-outline-info" title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.articles.edit', $article->ART_REF) }}" 
                                   class="btn btn-sm btn-outline-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(auth()->user()->isAdmin())
                                    <form method="POST" action="{{ route('admin.articles.toggle-status', $article->ART_REF) }}" 
                                          class="d-inline" onsubmit="return confirm('Voulez-vous modifier le statut de ce produit ?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" 
                                                title="{{ $article->ART_VENTE ? 'Désactiver' : 'Activer' }}">
                                            <i class="fas fa-{{ $article->ART_VENTE ? 'times' : 'check' }}"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-box-open fa-3x mb-3"></i>
                                    <p>Aucun produit ne correspond aux critères de recherche</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $articles->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Modal pour l'exportation -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exporter les produits</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="GET" action="{{ route('admin.articles.export') }}">
                    <!-- Transfert des critères de recherche actuels -->
                    @foreach(request()->query() as $key => $value)
                        @if($key !== 'page')
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    
                    <div class="mb-3">
                        <label class="form-label">Type d'exportation</label>
                        <select name="format" class="form-select" required>
                            <option value="excel">Excel (.xlsx)</option>
                            <option value="csv">CSV</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Données requises</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="fields[]" value="basic" checked>
                            <label class="form-check-label">Informations de base</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="fields[]" value="stock">
                            <label class="form-check-label">Informations de stock</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="fields[]" value="prices">
                            <label class="form-check-label">Prix</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download"></i> Exporter
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Mise à jour automatique de la page toutes les 5 minutes pour afficher le stock mis à jour
    setTimeout(function() {
        location.reload();
    }, 300000);
    
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
