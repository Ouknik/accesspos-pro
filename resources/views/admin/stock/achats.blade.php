@extends('layouts.sb-admin')

@section('title', 'Gestion des Achats')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-shopping-cart text-primary"></i> Gestion des Achats
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.stock.dashboard') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <a href="{{ route('admin.stock.achats.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nouvel Achat
            </a>
        </div>
    </div>

    <!-- Statistiques des achats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Factures
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statsAchats['total_factures'] ?? 0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Montant Total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statsAchats['montant_total'] ?? 0, 2) }} €
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Factures Validées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statsAchats['factures_validees'] ?? 0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En Attente
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statsAchats['factures_en_attente'] ?? 0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtres de recherche</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.stock.achats') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fournisseur</label>
                            <select name="fournisseur" class="form-control">
                                <option value="">Tous les fournisseurs</option>
                                @foreach($fournisseurs as $fournisseur)
                                    <option value="{{ $fournisseur->FRS_REF }}" 
                                            {{ request('fournisseur') == $fournisseur->FRS_REF ? 'selected' : '' }}>
                                        {{ $fournisseur->FRS_RAISONSOCIAL }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date Début</label>
                            <input type="date" name="date_debut" class="form-control" 
                                   value="{{ request('date_debut') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date Fin</label>
                            <input type="date" name="date_fin" class="form-control" 
                                   value="{{ request('date_fin') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Rechercher
                                </button>
                                <a href="{{ route('admin.stock.achats') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des achats -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Liste des Factures d'Achat - {{ $achats->total() }} factures
            </h6>
        </div>
        <div class="card-body">
            @if($achats->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>N° Facture</th>
                                <th>Date</th>
                                <th>Fournisseur</th>
                                <th>Montant HT</th>
                                <th>Montant TTC</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($achats as $achat)
                            <tr>
                                <td>
                                    <div class="font-weight-bold">{{ $achat->numero ?: 'N/A' }}</div>
                                    <small class="text-muted">Réf: {{ $achat->FCF_REF }}</small>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($achat->FCF_DATE)->format('d/m/Y') }}</td>
                                <td>{{ $achat->fournisseur ?: 'Non défini' }}</td>
                                <td class="text-right">{{ number_format($achat->montant_ht, 2) }} €</td>
                                <td class="text-right">{{ number_format($achat->montant_ttc, 2) }} €</td>
                                <td class="text-center">
                                    @if($achat->valide)
                                        <span class="badge badge-success">Validée</span>
                                    @else
                                        <span class="badge badge-warning">En attente</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info btn-sm" 
                                                onclick="showDetails('{{ $achat->FCF_REF }}')"
                                                title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if(!$achat->valide)
                                        <button class="btn btn-success btn-sm" 
                                                onclick="validateFacture('{{ $achat->FCF_REF }}')"
                                                title="Valider la facture">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        @endif
                                        <button class="btn btn-primary btn-sm" 
                                                onclick="printFacture('{{ $achat->FCF_REF }}')"
                                                title="Imprimer">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $achats->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-inbox fa-5x text-gray-300"></i>
                    </div>
                    <h4 class="text-gray-500">Aucune facture d'achat trouvée</h4>
                    <p class="text-muted">Aucune facture ne correspond aux critères de recherche.</p>
                    <div class="mt-4">
                        <a href="{{ route('admin.stock.achats.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Créer une facture d'achat
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Détails Facture -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails de la Facture</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalContent">
                <!-- Le contenu sera chargé via AJAX -->
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p>Chargement des détails...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
function showDetails(fcfRef) {
    $('#detailsModal').modal('show');
    
    fetch(`{{ url('admin/stock/achats') }}/${fcfRef}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('modalContent').innerHTML = data.html;
            } else {
                document.getElementById('modalContent').innerHTML = 
                    '<div class="alert alert-danger">Erreur lors du chargement des détails</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('modalContent').innerHTML = 
                '<div class="alert alert-danger">Erreur de connexion</div>';
        });
}

function validateFacture(fcfRef) {
    if (confirm('Êtes-vous sûr de vouloir valider cette facture ?')) {
        fetch(`{{ url('admin/stock/achats') }}/${fcfRef}/validate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la validation: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur de connexion');
        });
    }
}

function printFacture(fcfRef) {
    window.open(`{{ url('admin/stock/achats') }}/${fcfRef}/print`, '_blank');
}
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.table th {
    white-space: nowrap;
}

.btn-group-sm > .btn, .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
@endsection
