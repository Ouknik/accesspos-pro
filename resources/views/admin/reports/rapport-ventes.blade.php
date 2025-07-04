

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                @if(isset($statistiques['colonnes']['ref']) && $statistiques['colonnes']['ref'])
                    <th>Référence</th>
                @endif
                @if(isset($statistiques['colonnes']['date']) && $statistiques['colonnes']['date'])
                    <th>Date</th>
                @endif
                @if(isset($statistiques['colonnes']['montant_ht']) && $statistiques['colonnes']['montant_ht'])
                    <th>Montant HT</th>
                @endif
                @if(isset($statistiques['colonnes']['montant_ttc']) && $statistiques['colonnes']['montant_ttc'])
                    <th>Montant TTC</th>
                @endif
                @if(isset($statistiques['colonnes']['mode_paiement']) && $statistiques['colonnes']['mode_paiement'])
                    <th>Mode de Paiement</th>
                @endif
                @if(isset($statistiques['colonnes']['utilisateur']) && $statistiques['colonnes']['utilisateur'])
                    <th>Caissier</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                @if(isset($statistiques['colonnes']['ref']) && $statistiques['colonnes']['ref'])
                    <td>
                        <span class="badge bg-primary">
                            {{ $sale->{$statistiques['colonnes']['ref']} ?? 'N/A' }}
                        </span>
                    </td>
                @endif
                @if(isset($statistiques['colonnes']['date']) && $statistiques['colonnes']['date'])
                    <td>{{ \Carbon\Carbon::parse($sale->{$statistiques['colonnes']['date']})->format('d/m/Y H:i') }}</td>
                @endif
                @if(isset($statistiques['colonnes']['montant_ht']) && $statistiques['colonnes']['montant_ht'])
                    <td class="text-end">{{ number_format($sale->{$statistiques['colonnes']['montant_ht']} ?? 0, 2) }} €</td>
                @endif
                @if(isset($statistiques['colonnes']['montant_ttc']) && $statistiques['colonnes']['montant_ttc'])
                    <td class="text-end">
                        <strong>{{ number_format($sale->{$statistiques['colonnes']['montant_ttc']} ?? 0, 2) }} €</strong>
                    </td>
                @endif
                @if(isset($statistiques['colonnes']['mode_paiement']) && $statistiques['colonnes']['mode_paiement'])
                    <td>
                        <span class="badge 
                            @if(($sale->{$statistiques['colonnes']['mode_paiement']} ?? '') == 'Espèces') bg-success
                            @elseif(($sale->{$statistiques['colonnes']['mode_paiement']} ?? '') == 'Carte') bg-info
                            @elseif(($sale->{$statistiques['colonnes']['mode_paiement']} ?? '') == 'Chèque') bg-warning
                            @else bg-secondary
                            @endif
                        ">
                            {{ $sale->{$statistiques['colonnes']['mode_paiement']} ?? 'N/A' }}
                        </span>
                    </td>
                @endif
                @if(isset($statistiques['colonnes']['utilisateur']) && $statistiques['colonnes']['utilisateur'])
                    <td>{{ $sale->{$statistiques['colonnes']['utilisateur']} ?? 'N/A' }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>