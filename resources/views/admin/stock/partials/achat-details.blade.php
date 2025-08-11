<div class="row">
    <div class="col-md-6">
        <h6 class="text-primary">Informations Facture</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Numéro:</strong></td>
                <td>{{ $facture->FCF_NUMERO ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Date:</strong></td>
                <td>{{ \Carbon\Carbon::parse($facture->FCF_DATE)->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Montant HT:</strong></td>
                <td>{{ number_format($facture->FCF_MONTANT_HT_, 2) }} €</td>
            </tr>
            <tr>
                <td><strong>Montant TTC:</strong></td>
                <td><strong>{{ number_format($facture->FCF_MONTANT_TTC, 2) }} €</strong></td>
            </tr>
            <tr>
                <td><strong>Statut:</strong></td>
                <td>
                    @if($facture->FCF_VALIDE)
                        <span class="badge badge-success">Validée</span>
                    @else
                        <span class="badge badge-warning">En attente</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h6 class="text-primary">Fournisseur</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Raison sociale:</strong></td>
                <td>{{ $facture->fournisseur ?: 'Non défini' }}</td>
            </tr>
            <tr>
                <td><strong>Adresse:</strong></td>
                <td>{{ $facture->adresse_fournisseur ?: 'Non définie' }}</td>
            </tr>
            <tr>
                <td><strong>Téléphone:</strong></td>
                <td>{{ $facture->tel_fournisseur ?: 'Non défini' }}</td>
            </tr>
        </table>
    </div>
</div>

@if($facture->FCF_REMARQUE)
<div class="row mt-3">
    <div class="col-12">
        <h6 class="text-primary">Remarques</h6>
        <div class="alert alert-info">
            {{ $facture->FCF_REMARQUE }}
        </div>
    </div>
</div>
@endif

<div class="row mt-3">
    <div class="col-12">
        <h6 class="text-primary">Détails des Articles</h6>
        @if($details->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Article</th>
                            <th>Quantité</th>
                            <th>Prix HT</th>
                            <th>TVA %</th>
                            <th>Remise %</th>
                            <th>Prix TTC</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($details as $detail)
                        <tr>
                            <td>
                                <div class="font-weight-bold">{{ $detail->designation ?: 'Article supprimé' }}</div>
                                <small class="text-muted">{{ $detail->ART_REF }}</small>
                            </td>
                            <td class="text-center">{{ number_format($detail->quantite, 2) }}</td>
                            <td class="text-right">{{ number_format($detail->prix_ht, 2) }} €</td>
                            <td class="text-center">{{ number_format($detail->tva, 1) }}%</td>
                            <td class="text-center">{{ number_format($detail->remise, 1) }}%</td>
                            <td class="text-right">{{ number_format($detail->prix_ttc, 2) }} €</td>
                            <td class="text-right">
                                <strong>{{ number_format($detail->quantite * $detail->prix_ttc, 2) }} €</strong>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-info">
                            <th colspan="6" class="text-right">Total Facture:</th>
                            <th class="text-right">{{ number_format($facture->FCF_MONTANT_TTC, 2) }} €</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Aucun détail disponible pour cette facture
            </div>
        @endif
    </div>
</div>
