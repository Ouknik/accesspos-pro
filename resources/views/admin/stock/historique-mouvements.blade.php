@extends('layouts.sb-admin')

@section('title', 'Historique des Mouvements - ' . ($article->ART_DESIGNATION ?? 'Article'))

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-history text-primary"></i> Historique des Mouvements
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.stock.mouvements') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour aux Mouvements
            </a>
            <a href="{{ route('admin.stock.inventaire') }}" class="btn btn-info btn-sm">
                <i class="fas fa-boxes"></i> Voir dans l'Inventaire
            </a>
        </div>
    </div>

    <!-- Informations Article -->
    @if($article)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-box"></i> Informations Article
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Référence:</strong></td>
                            <td>{{ $article->ART_REF }}</td>
                        </tr>
                        <tr>
                            <td><strong>Désignation:</strong></td>
                            <td>{{ $article->ART_DESIGNATION }}</td>
                        </tr>
                        <tr>
                            <td><strong>Code Barre:</strong></td>
                            <td>{{ $article->ART_CODEBARR ?: 'Non défini' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Prix d'Achat:</strong></td>
                            <td>{{ number_format($article->ART_PRIX_ACHAT, 2) }} €</td>
                        </tr>
                        <tr>
                            <td><strong>Prix de Vente:</strong></td>
                            <td>{{ number_format($article->ART_PRIX_VENTE, 2) }} €</td>
                        </tr>
                        <tr>
                            <td><strong>Stock Min/Max:</strong></td>
                            <td>{{ $article->ART_STOCK_MIN ?: '0' }} / {{ $article->ART_STOCK_MAX ?: '0' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Historique des Mouvements -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Historique des Mouvements - {{ $mouvements->count() }} mouvements
            </h6>
            <div class="btn-group">
                <button class="btn btn-sm btn-outline-primary" onclick="exportHistorique()">
                    <i class="fas fa-file-excel"></i> Exporter
                </button>
                <button class="btn btn-sm btn-outline-info" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimer
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($mouvements->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Libellé</th>
                                <th>Quantité</th>
                                <th>Référence Document</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mouvements as $mouvement)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($mouvement->date_mouvement)->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($mouvement->type_mouvement == 'ENTREE')
                                        <span class="badge badge-success">
                                            <i class="fas fa-arrow-up"></i> ENTRÉE
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            <i class="fas fa-arrow-down"></i> SORTIE
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $mouvement->libelle_mouvement }}</td>
                                <td class="text-right">
                                    <span class="h6 mb-0 font-weight-bold 
                                        {{ $mouvement->type_mouvement == 'ENTREE' ? 'text-success' : 'text-danger' }}">
                                        {{ $mouvement->type_mouvement == 'ENTREE' ? '+' : '' }}{{ number_format($mouvement->quantite, 2) }}
                                    </span>
                                </td>
                                <td>{{ $mouvement->reference_document }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info btn-sm" 
                                                onclick="showDetails('{{ $mouvement->reference_document }}', '{{ $mouvement->type_mouvement }}')"
                                                title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-info">
                                <th colspan="3">Total des Entrées:</th>
                                <th class="text-right text-success">
                                    +{{ number_format($mouvements->where('type_mouvement', 'ENTREE')->sum('quantite'), 2) }}
                                </th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="table-info">
                                <th colspan="3">Total des Sorties:</th>
                                <th class="text-right text-danger">
                                    {{ number_format($mouvements->where('type_mouvement', 'SORTIE')->sum('quantite'), 2) }}
                                </th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="table-warning">
                                <th colspan="3">Solde Net:</th>
                                <th class="text-right">
                                    @php
                                        $solde = $mouvements->where('type_mouvement', 'ENTREE')->sum('quantite') + $mouvements->where('type_mouvement', 'SORTIE')->sum('quantite');
                                    @endphp
                                    <span class="{{ $solde >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $solde >= 0 ? '+' : '' }}{{ number_format($solde, 2) }}
                                    </span>
                                </th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Statistiques -->
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                                            Entrées Totales
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold">
                                            {{ number_format($mouvements->where('type_mouvement', 'ENTREE')->sum('quantite'), 2) }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-arrow-up fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                                            Sorties Totales
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold">
                                            {{ number_format(abs($mouvements->where('type_mouvement', 'SORTIE')->sum('quantite')), 2) }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-arrow-down fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                                            Nb Mouvements
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold">
                                            {{ $mouvements->count() }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-exchange-alt fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Aucun mouvement -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-inbox fa-5x text-muted"></i>
                    </div>
                    <h4 class="text-muted">Aucun Mouvement</h4>
                    <p class="text-muted">Aucun mouvement de stock trouvé pour cet article.</p>
                    <div class="mt-4">
                        <a href="{{ route('admin.stock.mouvements') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Créer un Mouvement
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function showDetails(reference, type) {
    // Rediriger vers la page de détails du document
    if (type === 'ENTREE') {
        // Facture fournisseur - aller vers la page des achats avec recherche
        window.open(`{{ route('admin.stock.achats') }}?search=${reference}`, '_blank');
    } else {
        // Facture de vente - aller vers la page des factures avec recherche
        @if(Route::has('admin.factures.index'))
            window.open(`{{ route('admin.factures.index') }}?search=${reference}`, '_blank');
        @else
            // Si la route des factures n'existe pas, afficher les détails dans une modal
            showFactureDetails(reference);
        @endif
    }
}

function showFactureDetails(reference) {
    // Créer une modal pour afficher les détails de la facture
    const modalContent = `
        <div class="modal fade" id="factureModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Détails Facture de Vente</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Référence:</strong> ${reference}</p>
                        <p class="text-info">Les détails complets de la facture seront disponibles prochainement.</p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Pour voir les détails complets de cette facture, vous pouvez :
                            <ul class="mt-2">
                                <li>Consulter le module de gestion des factures</li>
                                <li>Rechercher par la référence : <strong>${reference}</strong></li>
                                <li>Vérifier dans les rapports de vente</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-primary" onclick="copyReference('${reference}')">
                            <i class="fas fa-copy"></i> Copier Référence
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Supprimer la modal existante si elle existe
    const existingModal = document.getElementById('factureModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Ajouter la nouvelle modal au DOM
    document.body.insertAdjacentHTML('beforeend', modalContent);
    
    // Afficher la modal
    $('#factureModal').modal('show');
}

function copyReference(reference) {
    // Copier la référence dans le presse-papiers
    navigator.clipboard.writeText(reference).then(function() {
        // Afficher un message de confirmation
        const toast = `
            <div class="toast" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                <div class="toast-header">
                    <i class="fas fa-check-circle text-success mr-2"></i>
                    <strong class="mr-auto">Succès</strong>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="toast-body">
                    Référence copiée : ${reference}
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', toast);
        $('.toast').toast({delay: 3000}).toast('show');
        
        // Fermer la modal
        $('#factureModal').modal('hide');
    }).catch(function(err) {
        console.error('Erreur lors de la copie: ', err);
        alert('Référence: ' + reference + '\n(Copiez manuellement)');
    });
}

function exportHistorique() {
    // Créer un tableau pour l'export
    const article = '{{ $article->ART_DESIGNATION ?? "Article" }}';
    const data = [];
    
    // En-têtes
    data.push(['Historique des Mouvements - ' + article]);
    data.push(['Date', 'Type', 'Libellé', 'Quantité', 'Référence Document']);
    
    // Données des mouvements
    @if($mouvements->count() > 0)
        @foreach($mouvements as $mouvement)
            data.push([
                '{{ \Carbon\Carbon::parse($mouvement->date_mouvement)->format("d/m/Y H:i") }}',
                '{{ $mouvement->type_mouvement }}',
                '{{ $mouvement->libelle_mouvement }}',
                '{{ number_format($mouvement->quantite, 2) }}',
                '{{ $mouvement->reference_document }}'
            ]);
        @endforeach
    @endif
    
    // Créer le fichier CSV
    let csvContent = "data:text/csv;charset=utf-8,";
    data.forEach(function(rowArray) {
        let row = rowArray.join(",");
        csvContent += row + "\r\n";
    });
    
    // Télécharger le fichier
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `historique_mouvements_{{ $article->ART_REF ?? 'article' }}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<style>
@media print {
    .btn, .btn-group {
        display: none !important;
    }
    
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
}
</style>
@endsection
