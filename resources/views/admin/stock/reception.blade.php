@extends('layouts.sb-admin')

@section('title', 'Réception de Marchandises')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-truck text-info"></i> Réception de Marchandises
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.stock.dashboard') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <button class="btn btn-primary btn-sm" onclick="showNewReception()">
                <i class="fas fa-plus"></i> Nouvelle réception
            </button>
        </div>
    </div>

    <!-- Résumé des réceptions en attente -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Bons de Livraison en Attente
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $blEnAttente->count() ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                Réceptions du Jour
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $receptionsJour->count() ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
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
                                Articles Reçus Aujourd'hui
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($articlesRecus ?? 0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Valeur Réceptionnée
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($valeurReceptionnee ?? 0, 2) }} €
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bons de livraison en attente -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-truck"></i> Bons de Livraison en Attente de Réception
            </h6>
        </div>
        <div class="card-body">
            @if(isset($blEnAttente) && $blEnAttente->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>N° BL</th>
                                <th>Fournisseur</th>
                                <th>Date Prévue</th>
                                <th>Articles</th>
                                <th>Montant HT</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($blEnAttente as $bl)
                            <tr>
                                <td>
                                    <div class="font-weight-bold">{{ $bl->BLF_NUMERO }}</div>
                                    <small class="text-muted">{{ $bl->BLF_REF }}</small>
                                </td>
                                <td>{{ $bl->fournisseur ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($bl->BLF_DATE)->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <span class="badge badge-info">{{ $bl->nb_articles ?? 0 }} articles</span>
                                </td>
                                <td class="text-right">{{ number_format($bl->BLF_MONTANT_HT, 2) }} €</td>
                                <td class="text-center">
                                    @if($bl->BLF_VALIDE)
                                        <span class="badge badge-success">Validé</span>
                                    @else
                                        <span class="badge badge-warning">En attente</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-primary btn-sm" 
                                                onclick="showReceptionDetails('{{ $bl->BLF_REF }}')"
                                                title="Réceptionner">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-info btn-sm" 
                                                onclick="showBLDetails('{{ $bl->BLF_REF }}')"
                                                title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-5x text-success"></i>
                    </div>
                    <h4 class="text-success">Parfait !</h4>
                    <p class="text-muted">Aucun bon de livraison en attente de réception.</p>
                    <div class="mt-4">
                        <a href="{{ route('admin.stock.achats') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-cart"></i> Voir les achats
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Historique des réceptions récentes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history"></i> Réceptions Récentes
            </h6>
        </div>
        <div class="card-body">
            @if(isset($receptionsRecentes) && $receptionsRecentes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>N° BL</th>
                                <th>Fournisseur</th>
                                <th>Articles</th>
                                <th>Montant</th>
                                <th>Utilisateur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receptionsRecentes as $reception)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($reception->BLF_DATE)->format('d/m/Y H:i') }}</td>
                                <td>{{ $reception->BLF_NUMERO }}</td>
                                <td>{{ $reception->fournisseur ?? 'N/A' }}</td>
                                <td class="text-center">{{ $reception->nb_articles ?? 0 }}</td>
                                <td class="text-right">{{ number_format($reception->BLF_MONTANT_TTC, 2) }} €</td>
                                <td>{{ $reception->BLF_UTILISATEUR ?? 'Système' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center">Aucune réception récente</p>
            @endif
        </div>
    </div>
</div>

<!-- Modal Détails Bon de Livraison -->
<div class="modal fade" id="blDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails du Bon de Livraison</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="blDetailsContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Réception -->
<div class="modal fade" id="receptionModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Réception de Marchandises</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="receptionForm">
                <div class="modal-body" id="receptionContent">
                    <!-- Contenu chargé dynamiquement -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Valider la Réception
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Nouvelle Réception -->
<div class="modal fade" id="newReceptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle Réception</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Choisissez le type de réception :</p>
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action" onclick="showReceptionFromBL()">
                        <i class="fas fa-clipboard-list text-primary"></i>
                        <strong>Réception depuis un BL</strong>
                        <small class="d-block text-muted">Réceptionner des articles depuis un bon de livraison existant</small>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action" onclick="showReceptionDirecte()">
                        <i class="fas fa-plus text-success"></i>
                        <strong>Réception directe</strong>
                        <small class="d-block text-muted">Réceptionner des articles sans bon de livraison préalable</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showNewReception() {
    $('#newReceptionModal').modal('show');
}

function showBLDetails(blRef) {
    $('#blDetailsContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Chargement...</div>');
    $('#blDetailsModal').modal('show');
    
    // Simulation du chargement des détails
    setTimeout(function() {
        $('#blDetailsContent').html(`
            <div class="row">
                <div class="col-md-6">
                    <h6>Informations du BL</h6>
                    <table class="table table-sm">
                        <tr><td>Référence:</td><td>${blRef}</td></tr>
                        <tr><td>Date:</td><td>10/08/2025</td></tr>
                        <tr><td>Fournisseur:</td><td>Fournisseur Test</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Articles du BL</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Article</th><th>Qté</th></tr></thead>
                            <tbody>
                                <tr><td>Article Test</td><td>10</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `);
    }, 1000);
}

function showReceptionDetails(blRef) {
    $('#receptionContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Chargement...</div>');
    $('#receptionModal').modal('show');
    
    // Simulation du chargement du formulaire de réception
    setTimeout(function() {
        $('#receptionContent').html(`
            <div class="row">
                <div class="col-md-12">
                    <h6>Réception du BL: ${blRef}</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Article</th>
                                    <th>Qté Commandée</th>
                                    <th>Qté Reçue</th>
                                    <th>État</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Article Test</td>
                                    <td>10</td>
                                    <td><input type="number" class="form-control form-control-sm" value="10" min="0"></td>
                                    <td>
                                        <select class="form-control form-control-sm">
                                            <option>Conforme</option>
                                            <option>Endommagé</option>
                                            <option>Manquant</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group">
                        <label>Commentaires de réception</label>
                        <textarea class="form-control" rows="3" placeholder="Remarques sur la réception..."></textarea>
                    </div>
                </div>
            </div>
        `);
    }, 1000);
}

function showReceptionFromBL() {
    $('#newReceptionModal').modal('hide');
    alert('Fonctionnalité en développement - Réception depuis BL');
}

function showReceptionDirecte() {
    $('#newReceptionModal').modal('hide');
    alert('Fonctionnalité en développement - Réception directe');
}

// Gestion du formulaire de réception
document.getElementById('receptionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validation...';
    submitBtn.disabled = true;
    
    // Simulation de la validation
    setTimeout(function() {
        alert('Réception validée avec succès !');
        $('#receptionModal').modal('hide');
        location.reload();
    }, 2000);
});
</script>

<style>
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.list-group-item:hover {
    background-color: #f8f9fc;
}

.btn-group-sm > .btn, .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
@endsection
