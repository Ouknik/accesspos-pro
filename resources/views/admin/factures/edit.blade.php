@extends('admin.layout')

@section('title', 'Modifier la Facture #' . $facture['facture']->FCTV_NUMERO)

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit"></i>
            Modifier la Facture #{{ $facture['facture']->FCTV_NUMERO }}
        </h1>
        <div class="btn-group" role="group">
            <a href="{{ route('factures.show', $facture['facture']->FCTV_ID) }}" class="btn btn-secondary">
                <i class="fas fa-eye"></i> Voir
            </a>
            <a href="{{ route('factures.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Informations sur la facture -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-primary">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Statut de la Facture
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        @if($facture['facture']->FCTV_ETAT == 0)
                            <span class="badge badge-danger">Annulée</span>
                        @elseif($facture['facture']->FCTV_VALIDE == 0)
                            <span class="badge badge-warning">Brouillon</span>
                        @else
                            <span class="badge badge-success">Validée</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-info">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                        Date de Création
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ \Carbon\Carbon::parse($facture['facture']->FCTV_DATE)->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-success">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Montant Total
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ number_format($facture['facture']->FCTV_MNT_TTC, 2) }} DH
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages d'alerte -->
    @if($facture['facture']->FCTV_VALIDE == 1)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-exclamation-triangle"></i> Attention!</strong>
            Cette facture est validée. Toute modification nécessitera une nouvelle validation.
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if($facture['facture']->FCTV_ETAT == 0)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-ban"></i> Facture Annulée!</strong>
            Cette facture est annulée. Les modifications sont limitées.
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <!-- Formulaire de modification -->
    <form id="editFactureForm" action="{{ route('factures.update', $facture['facture']->FCTV_ID) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Informations générales -->
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle"></i>
                            Informations Générales
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="facture_numero">Numéro de Facture</label>
                                    <input type="text" class="form-control" id="facture_numero" 
                                           name="facture_numero" value="{{ $facture['facture']->FCTV_NUMERO }}" 
                                           readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="facture_ref">Référence</label>
                                    <input type="text" class="form-control" id="facture_ref" 
                                           name="facture_ref" value="{{ $facture['facture']->FCTV_REF }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="facture_date">Date de la Facture</label>
                                    <input type="datetime-local" class="form-control" id="facture_date" 
                                           name="facture_date" 
                                           value="{{ \Carbon\Carbon::parse($facture['facture']->FCTV_DATE)->format('Y-m-d\TH:i') }}"
                                           {{ $facture['facture']->FCTV_ETAT == 0 ? 'disabled' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="serveur">Serveur</label>
                                    <input type="text" class="form-control" id="serveur" 
                                           name="serveur" value="{{ $facture['facture']->FCTV_SERVEUR }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mode_paiement">Mode de Paiement</label>
                                    <select class="form-control" id="mode_paiement" name="mode_paiement" 
                                            {{ $facture['facture']->FCTV_ETAT == 0 ? 'disabled' : '' }}>
                                        <option value="Espece" {{ $facture['facture']->FCTV_MODEPAIEMENT == 'Espece' ? 'selected' : '' }}>Espèces</option>
                                        <option value="Charte" {{ $facture['facture']->FCTV_MODEPAIEMENT == 'Charte' ? 'selected' : '' }}>Carte Bancaire</option>
                                        <option value="Cheque" {{ $facture['facture']->FCTV_MODEPAIEMENT == 'Cheque' ? 'selected' : '' }}>Chèque</option>
                                        <option value="Credit" {{ $facture['facture']->FCTV_MODEPAIEMENT == 'Credit' ? 'selected' : '' }}>Crédit</option>
                                        <option value="Mixte" {{ $facture['facture']->FCTV_MODEPAIEMENT == 'Mixte' ? 'selected' : '' }}>Mixte</option>
                                        <option value="Virement" {{ $facture['facture']->FCTV_MODEPAIEMENT == 'Virement' ? 'selected' : '' }}>Virement</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="table_ref">Table</label>
                                    <input type="text" class="form-control" id="table_ref" 
                                           name="table_ref" value="{{ $facture['facture']->TAB_REF }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="remarque">Remarques</label>
                            <textarea class="form-control" id="remarque" name="remarque" 
                                      rows="3">{{ $facture['facture']->FCTV_RMARQUE }}</textarea>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="exonere" 
                                   name="exonere" value="1" 
                                   {{ $facture['facture']->FCTV_EXONORE ? 'checked' : '' }}
                                   {{ $facture['facture']->FCTV_ETAT == 0 ? 'disabled' : '' }}>
                            <label class="form-check-label" for="exonere">
                                Facture exonérée de TVA
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Détail du paiement mixte -->
                <div class="card shadow mb-4" id="paiement-mixte-card" 
                     style="display: {{ $facture['facture']->FCTV_MODEPAIEMENT == 'Mixte' ? 'block' : 'none' }};">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-money-bill-wave"></i>
                            Détail du Paiement Mixte
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="montant_espece">Espèces</label>
                                    <input type="number" class="form-control" id="montant_espece" 
                                           name="montant_espece" step="0.01" 
                                           value="{{ $facture['facture']->MontantEspece }}"
                                           {{ $facture['facture']->FCTV_ETAT == 0 ? 'disabled' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="montant_carte">Carte Bancaire</label>
                                    <input type="number" class="form-control" id="montant_carte" 
                                           name="montant_carte" step="0.01" 
                                           value="{{ $facture['facture']->MontantCharte }}"
                                           {{ $facture['facture']->FCTV_ETAT == 0 ? 'disabled' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="montant_cheque">Chèque</label>
                                    <input type="number" class="form-control" id="montant_cheque" 
                                           name="montant_cheque" step="0.01" 
                                           value="{{ $facture['facture']->MontantCheque }}"
                                           {{ $facture['facture']->FCTV_ETAT == 0 ? 'disabled' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="montant_credit">Crédit</label>
                                    <input type="number" class="form-control" id="montant_credit" 
                                           name="montant_credit" step="0.01" 
                                           value="{{ $facture['facture']->MontantCredit }}"
                                           {{ $facture['facture']->FCTV_ETAT == 0 ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="montant_rendu">Rendu</label>
                                    <input type="number" class="form-control" id="montant_rendu" 
                                           name="montant_rendu" step="0.01" 
                                           value="{{ $facture['facture']->FCTV_RENDU }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info mt-4">
                                    <small>
                                        <strong>Total des paiements:</strong> <span id="total-paiements">0.00</span> DH<br>
                                        <strong>Montant facture:</strong> {{ number_format($facture['facture']->FCTV_MNT_TTC, 2) }} DH
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations client -->
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-info">
                            <i class="fas fa-user"></i>
                            Informations Client
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="client_search">Rechercher un Client</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="client_search" 
                                       placeholder="Nom du client..." value="{{ $facture['facture']->CLIENT_NAME }}"
                                       {{ $facture['facture']->FCTV_ETAT == 0 ? 'disabled' : '' }}>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" 
                                            id="btn-clear-client"
                                            {{ $facture['facture']->FCTV_ETAT == 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" id="client_id" name="client_id" 
                                   value="{{ $facture['facture']->FCTV_CLIENT }}">
                        </div>

                        <div id="client-info" class="mt-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $facture['facture']->CLIENT_NAME }}</h6>
                                    @if(!empty($facture['facture']->CLT_ADRESSE))
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt"></i>
                                                {{ $facture['facture']->CLT_ADRESSE }}
                                            </small>
                                        </p>
                                    @endif
                                    @if(!empty($facture['facture']->CLT_TELEPHONE))
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="fas fa-phone"></i>
                                                {{ $facture['facture']->CLT_TELEPHONE }}
                                            </small>
                                        </p>
                                    @endif
                                    @if(!empty($facture['facture']->CLT_EMAIL))
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="fas fa-envelope"></i>
                                                {{ $facture['facture']->CLT_EMAIL }}
                                            </small>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Actions sur la facture -->
                        <div class="mt-4">
                            <h6 class="font-weight-bold">Actions:</h6>
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="valider_facture" 
                                           {{ $facture['facture']->FCTV_VALIDE == 1 ? 'checked' : '' }}
                                           {{ $facture['facture']->FCTV_ETAT == 0 ? 'disabled' : '' }}>
                                    <label class="form-check-label" for="valider_facture">
                                        Valider la facture
                                    </label>
                                </div>
                            </div>

                            @if($facture['facture']->FCTV_ETAT != 0)
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="annuler_facture">
                                    <label class="form-check-label text-danger" for="annuler_facture">
                                        Annuler la facture
                                    </label>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Remise globale -->
                        <div class="mt-3">
                            <div class="form-group">
                                <label for="remise_globale">Remise Globale</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="remise_globale" 
                                           name="remise_globale" step="0.01" 
                                           value="{{ $facture['facture']->FCTV_REMISE }}"
                                           {{ $facture['facture']->FCTV_ETAT == 0 ? 'disabled' : '' }}>
                                    <div class="input-group-append">
                                        <span class="input-group-text">DH</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Articles de la facture -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-list"></i>
                    Articles de la Facture
                    <span class="badge badge-success ml-2" id="article-count">{{ count($facture['details']) }}</span>
                </h6>
                @if($facture['facture']->FCTV_ETAT != 0)
                <button type="button" class="btn btn-sm btn-success float-right" id="btn-add-article">
                    <i class="fas fa-plus"></i> Ajouter un Article
                </button>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="articlesTable">
                        <thead>
                            <tr>
                                <th width="35%">Article</th>
                                <th width="10%">Quantité</th>
                                <th width="12%">Prix HT</th>
                                <th width="8%">TVA</th>
                                <th width="12%">Prix TTC</th>
                                <th width="10%">Remise</th>
                                <th width="13%">Total</th>
                                @if($facture['facture']->FCTV_ETAT != 0)
                                <th width="5%">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($facture['details'] as $index => $detail)
                            <tr data-article-id="{{ $detail->FVD_ID }}">
                                <td>
                                    <div class="font-weight-bold">{{ $detail->ART_DESIGNATION }}</div>
                                    <small class="text-muted">Réf: {{ $detail->ART_REF }}</small>
                                    <input type="hidden" name="articles[{{ $index }}][id]" value="{{ $detail->FVD_ID }}">
                                    <input type="hidden" name="articles[{{ $index }}][article_id]" value="{{ $detail->FVD_ARTICLE }}">
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm article-qte" 
                                           name="articles[{{ $index }}][qte]" 
                                           value="{{ $detail->FVD_QTE }}" min="0" step="0.01"
                                           {{ $facture['facture']->FCTV_ETAT == 0 ? 'disabled' : '' }}>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm article-prix-ht" 
                                           name="articles[{{ $index }}][prix_ht]" 
                                           value="{{ $detail->FVD_PRIX_VNT_HT }}" min="0" step="0.01"
                                           {{ $facture['facture']->FCTV_ETAT == 0 ? 'disabled' : '' }}>
                                </td>
                                <td>
                                    <select class="form-control form-control-sm article-tva" 
                                            name="articles[{{ $index }}][tva]"
                                            {{ $facture['facture']->FCTV_ETAT == 0 ? 'disabled' : '' }}>
                                        <option value="0" {{ $detail->FVD_TVA == 0 ? 'selected' : '' }}>0%</option>
                                        <option value="10" {{ $detail->FVD_TVA == 10 ? 'selected' : '' }}>10%</option>
                                        <option value="20" {{ $detail->FVD_TVA == 20 ? 'selected' : '' }}>20%</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm article-prix-ttc" 
                                           value="{{ $detail->FVD_PRIX_VNT_TTC }}" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm article-remise" 
                                           name="articles[{{ $index }}][remise]" 
                                           value="{{ $detail->FVD_REMISE }}" min="0" step="0.01"
                                           {{ $facture['facture']->FCTV_ETAT == 0 ? 'disabled' : '' }}>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm article-total" 
                                           value="{{ $detail->TOTAL_LIGNE }}" readonly>
                                </td>
                                @if($facture['facture']->FCTV_ETAT != 0)
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger btn-remove-article">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td colspan="{{ $facture['facture']->FCTV_ETAT != 0 ? '6' : '5' }}">Total:</td>
                                <td>
                                    <span id="total-facture">{{ number_format($facture['facture']->FCTV_MNT_TTC, 2) }}</span> DH
                                </td>
                                @if($facture['facture']->FCTV_ETAT != 0)
                                <td></td>
                                @endif
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center">
                        @if($facture['facture']->FCTV_ETAT != 0)
                        <button type="submit" class="btn btn-primary btn-lg mr-3">
                            <i class="fas fa-save"></i>
                            Enregistrer les Modifications
                        </button>
                        @endif
                        
                        <a href="{{ route('factures.print', $facture['facture']->FCTV_ID) }}" 
                           class="btn btn-success btn-lg mr-3" target="_blank">
                            <i class="fas fa-print"></i>
                            Imprimer
                        </a>
                        
                        <a href="{{ route('factures.show', $facture['facture']->FCTV_ID) }}" 
                           class="btn btn-info btn-lg mr-3">
                            <i class="fas fa-eye"></i>
                            Voir la Facture
                        </a>
                        
                        <a href="{{ route('factures.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left"></i>
                            Retour à la Liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@include('admin.factures.partials.add-article-modal')
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Variables globales
    let articleCounter = {{ count($facture['details']) }};
    const factureEtat = {{ $facture['facture']->FCTV_ETAT }};
    const montantFacture = {{ $facture['facture']->FCTV_MNT_TTC }};

    // Gestion du mode de paiement
    $('#mode_paiement').change(function() {
        if ($(this).val() === 'Mixte') {
            $('#paiement-mixte-card').show();
            calculatePaiementMixte();
        } else {
            $('#paiement-mixte-card').hide();
        }
    });

    // Calcul automatique pour paiement mixte
    function calculatePaiementMixte() {
        if (factureEtat === 0) return;
        
        const espece = parseFloat($('#montant_espece').val()) || 0;
        const carte = parseFloat($('#montant_carte').val()) || 0;
        const cheque = parseFloat($('#montant_cheque').val()) || 0;
        const credit = parseFloat($('#montant_credit').val()) || 0;
        
        const totalPaiements = espece + carte + cheque + credit;
        const rendu = totalPaiements > montantFacture ? totalPaiements - montantFacture : 0;
        
        $('#total-paiements').text(totalPaiements.toFixed(2));
        $('#montant_rendu').val(rendu.toFixed(2));
    }

    // Écouteurs pour le paiement mixte
    $('#montant_espece, #montant_carte, #montant_cheque, #montant_credit').on('input', calculatePaiementMixte);

    // Recherche de client
    let clientTimeout;
    $('#client_search').on('input', function() {
        if (factureEtat === 0) return;
        
        clearTimeout(clientTimeout);
        const query = $(this).val();
        
        if (query.length < 2) {
            $('#client_id').val('');
            return;
        }
        
        clientTimeout = setTimeout(() => {
            searchClients(query);
        }, 300);
    });

    function searchClients(query) {
        // Simulation d'une recherche client
        // Remplacer par un appel AJAX réel
        console.log('Recherche client:', query);
    }

    // Effacer le client
    $('#btn-clear-client').click(function() {
        if (factureEtat === 0) return;
        
        $('#client_search').val('Client de Passage');
        $('#client_id').val('1');
        $('#client-info').html('<div class="alert alert-info">Client de passage sélectionné</div>');
    });

    // Calculs automatiques pour les articles
    function calculateArticleLine(row) {
        if (factureEtat === 0) return;
        
        const qte = parseFloat(row.find('.article-qte').val()) || 0;
        const prixHT = parseFloat(row.find('.article-prix-ht').val()) || 0;
        const tva = parseFloat(row.find('.article-tva').val()) || 0;
        const remise = parseFloat(row.find('.article-remise').val()) || 0;
        
        const prixTTC = prixHT * (1 + tva / 100);
        const sousTotal = (qte * prixTTC) - remise;
        
        row.find('.article-prix-ttc').val(prixTTC.toFixed(2));
        row.find('.article-total').val(sousTotal.toFixed(2));
        
        calculateTotalFacture();
    }

    function calculateTotalFacture() {
        let total = 0;
        $('#articlesTable tbody tr').each(function() {
            const lineTotal = parseFloat($(this).find('.article-total').val()) || 0;
            total += lineTotal;
        });
        
        const remiseGlobale = parseFloat($('#remise_globale').val()) || 0;
        total -= remiseGlobale;
        
        $('#total-facture').text(total.toFixed(2));
        
        // Mettre à jour le calcul du paiement mixte si nécessaire
        if ($('#mode_paiement').val() === 'Mixte') {
            calculatePaiementMixte();
        }
    }

    // Écouteurs pour les calculs d'articles
    $(document).on('input', '.article-qte, .article-prix-ht, .article-remise', function() {
        calculateArticleLine($(this).closest('tr'));
    });

    $(document).on('change', '.article-tva', function() {
        calculateArticleLine($(this).closest('tr'));
    });

    $('#remise_globale').on('input', calculateTotalFacture);

    // Supprimer un article
    $(document).on('click', '.btn-remove-article', function() {
        if (factureEtat === 0) return;
        
        if (confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
            $(this).closest('tr').remove();
            updateArticleCount();
            calculateTotalFacture();
        }
    });

    // Ajouter un article
    $('#btn-add-article').click(function() {
        if (factureEtat === 0) return;
        $('#addArticleModal').modal('show');
    });

    function updateArticleCount() {
        const count = $('#articlesTable tbody tr').length;
        $('#article-count').text(count);
    }

    // Validation du formulaire
    $('#editFactureForm').submit(function(e) {
        if (factureEtat === 0) {
            e.preventDefault();
            alert('Impossible de modifier une facture annulée.');
            return false;
        }

        // Vérifier si on annule la facture
        if ($('#annuler_facture').is(':checked')) {
            if (!confirm('Êtes-vous sûr de vouloir annuler cette facture ? Cette action est irréversible.')) {
                e.preventDefault();
                return false;
            }
        }

        // Vérifier le total des paiements mixtes
        if ($('#mode_paiement').val() === 'Mixte') {
            const totalPaiements = parseFloat($('#total-paiements').text()) || 0;
            const montantFacture = parseFloat($('#total-facture').text().replace(',', '')) || 0;
            
            if (totalPaiements < montantFacture) {
                alert('Le total des paiements est inférieur au montant de la facture.');
                e.preventDefault();
                return false;
            }
        }

        return true;
    });

    // Initialisation
    calculateTotalFacture();
    if ($('#mode_paiement').val() === 'Mixte') {
        calculatePaiementMixte();
    }
});
</script>
@endsection
