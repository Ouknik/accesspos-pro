@extends('layouts.sb-admin')

@section('title', 'Mouvements de Stock')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-exchange-alt text-warning"></i> Mouvements de Stock
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.stock.dashboard') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#addMouvementModal">
                <i class="fas fa-plus"></i> Nouveau mouvement
            </button>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtres de recherche</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.stock.mouvements') }}">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Date début</label>
                            <input type="date" name="date_debut" class="form-control" 
                                   value="{{ request('date_debut') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Date fin</label>
                            <input type="date" name="date_fin" class="form-control" 
                                   value="{{ request('date_fin') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Type de mouvement</label>
                            <select name="type_mouvement" class="form-control">
                                <option value="">Tous les types</option>
                                <option value="ENTREE" {{ request('type_mouvement') == 'ENTREE' ? 'selected' : '' }}>
                                    Entrées
                                </option>
                                <option value="SORTIE" {{ request('type_mouvement') == 'SORTIE' ? 'selected' : '' }}>
                                    Sorties
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Article</label>
                            <input type="text" name="article" class="form-control" 
                                   placeholder="Nom ou référence article..." 
                                   value="{{ request('article') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Rechercher
                                </button>
                                <a href="{{ route('admin.stock.mouvements') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques des mouvements -->
    @if(isset($statsMouvements))
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-success">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Total Entrées
                    </div>
                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                        {{ number_format($statsMouvements['total_entrees'] ?? 0) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-danger">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                        Total Sorties
                    </div>
                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                        {{ number_format($statsMouvements['total_sorties'] ?? 0) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                        Mouvements Aujourd'hui
                    </div>
                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                        {{ number_format($statsMouvements['mouvements_aujourdhui'] ?? 0) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        Articles Différents
                    </div>
                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                        {{ number_format($statsMouvements['articles_differents'] ?? 0) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Liste des mouvements -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Historique des Mouvements - {{ $mouvements->total() }} mouvements trouvés
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Article</th>
                            <th>Quantité</th>
                            <th>Libellé</th>
                            <th>Référence</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mouvements as $mouvement)
                        <tr>
                            <td>{{ Carbon\Carbon::parse($mouvement->date_mouvement)->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                @if($mouvement->type_mouvement == 'ENTREE')
                                    <span class="badge badge-success">
                                        <i class="fas fa-arrow-up"></i> Entrée
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="fas fa-arrow-down"></i> Sortie
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ Str::limit($mouvement->article_designation, 40) }}</div>
                                <small class="text-muted">Réf: {{ $mouvement->article_ref }}</small>
                            </td>
                            <td class="text-center">
                                <span class="font-weight-bold 
                                    {{ $mouvement->quantite > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $mouvement->quantite > 0 ? '+' : '' }}{{ number_format($mouvement->quantite, 2) }}
                                </span>
                            </td>
                            <td>{{ $mouvement->libelle_mouvement }}</td>
                            <td>
                                <small class="text-muted">{{ $mouvement->reference_document }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-info btn-sm" 
                                            onclick="showDetails('{{ $mouvement->reference_document }}')"
                                            title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-exchange-alt fa-2x mb-2"></i><br>
                                Aucun mouvement trouvé avec les critères de recherche
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $mouvements->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouveau Mouvement -->
<div class="modal fade" id="addMouvementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouveau Mouvement de Stock</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="mouvementForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type de mouvement</label>
                                <select id="type_mouvement" class="form-control" required>
                                    <option value="">Sélectionner un type</option>
                                    <option value="ENTREE">Entrée de stock</option>
                                    <option value="SORTIE">Sortie de stock</option>
                                    <option value="TRANSFERT">Transfert</option>
                                    <option value="AJUSTEMENT">Ajustement</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="datetime-local" id="date_mouvement" class="form-control" 
                                       value="{{ now()->format('Y-m-d\TH:i') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Article</label>
                        <select id="article_ref" class="form-control" required>
                            <option value="">Rechercher un article...</option>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Quantité</label>
                                <input type="number" id="quantite" class="form-control" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Unité</label>
                                <input type="text" id="unite" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Motif</label>
                        <select id="motif" class="form-control" required>
                            <option value="">Sélectionner un motif</option>
                            <option value="Correction manuelle">Correction manuelle</option>
                            <option value="Perte/Vol">Perte/Vol</option>
                            <option value="Avarie">Avarie</option>
                            <option value="Retour client">Retour client</option>
                            <option value="Retour fournisseur">Retour fournisseur</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Commentaire</label>
                        <textarea id="commentaire" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer le mouvement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Initialiser Select2 pour la recherche d'articles
$(document).ready(function() {
    $('#article_ref').select2({
        ajax: {
            url: '{{ route("admin.stock.api.articles.search") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    term: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        placeholder: 'Rechercher un article...',
        minimumInputLength: 2
    });
    
    // Mettre à jour l'unité quand un article est sélectionné
    $('#article_ref').on('select2:select', function (e) {
        const data = e.params.data;
        $('#unite').val(data.unite || 'Pce');
    });
});

// Gestion du formulaire de mouvement
document.getElementById('mouvementForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('type_mouvement', document.getElementById('type_mouvement').value);
    formData.append('date_mouvement', document.getElementById('date_mouvement').value);
    formData.append('article_ref', document.getElementById('article_ref').value);
    formData.append('quantite', document.getElementById('quantite').value);
    formData.append('motif', document.getElementById('motif').value);
    formData.append('commentaire', document.getElementById('commentaire').value);
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch('{{ route("admin.stock.mouvements.create") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#addMouvementModal').modal('hide');
            location.reload();
        } else {
            alert('Erreur lors de l\'enregistrement: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de l\'enregistrement');
    });
});

function showDetails(reference) {
    // Rediriger vers la page de détail du document
    window.open(`/admin/documents/${reference}`, '_blank');
}

// Mettre le signe de la quantité selon le type de mouvement
document.getElementById('type_mouvement').addEventListener('change', function() {
    const quantiteInput = document.getElementById('quantite');
    const type = this.value;
    
    if (type === 'SORTIE') {
        quantiteInput.addEventListener('input', function() {
            if (this.value > 0) {
                this.value = -Math.abs(this.value);
            }
        });
    }
});
</script>

<style>
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.select2-container {
    width: 100% !important;
}
</style>
@endsection
