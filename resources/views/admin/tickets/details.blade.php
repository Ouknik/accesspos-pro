<div class="row">
    <!-- معلومات التذكرة الأساسية -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-gradient-primary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle"></i>
                    Informations Générales
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>N° Ticket:</strong></td>
                        <td>{{ $ticketInfo['ticket']->DVS_NUMERO }}</td>
                    </tr>
                    <tr>
                        <td><strong>Référence:</strong></td>
                        <td><code>{{ $ticketInfo['ticket']->CMD_REF }}</code></td>
                    </tr>
                    <tr>
                        <td><strong>Date/Heure:</strong></td>
                        <td>{{ \Carbon\Carbon::parse($ticketInfo['ticket']->DVS_DATE)->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Type de service:</strong></td>
                        <td>
                            @if($ticketInfo['ticket']->TAB_REF)
                                <span class="badge badge-success">{{ $ticketInfo['ticket']->TYPE_SERVICE }}</span>
                                <br><small>Table: {{ $ticketInfo['ticket']->TAB_LIB }}</small>
                            @else
                                <span class="badge badge-info">{{ $ticketInfo['ticket']->TYPE_SERVICE }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Client:</strong></td>
                        <td>{{ $ticketInfo['ticket']->CLIENT_NAME }}</td>
                    </tr>
                    <tr>
                        <td><strong>Serveur:</strong></td>
                        <td>{{ $ticketInfo['ticket']->DVS_SERVEUR ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nb Couverts:</strong></td>
                        <td>{{ $ticketInfo['ticket']->DVS_NBR_COUVERT ?? 1 }}</td>
                    </tr>
                    <tr>
                        <td><strong>Statut:</strong></td>
                        <td>
                            <span class="badge badge-pill status-{{ strtolower(str_replace(' ', '-', $ticketInfo['ticket']->DVS_ETAT)) }}">
                                {{ $ticketInfo['ticket']->DVS_ETAT }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Résumé financier -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-gradient-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-calculator"></i>
                    Résumé Financier
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>Montant HT:</strong></td>
                        <td class="text-right">{{ number_format($ticketInfo['ticket']->DVS_MONTANT_HT, 2) }} DH</td>
                    </tr>
                    <tr>
                        <td><strong>Montant TTC:</strong></td>
                        <td class="text-right text-success font-weight-bold">{{ number_format($ticketInfo['ticket']->DVS_MONTANT_TTC, 2) }} DH</td>
                    </tr>
                    @if($ticketInfo['ticket']->DVS_REMISE > 0)
                    <tr>
                        <td><strong>Remise globale:</strong></td>
                        <td class="text-right text-warning">-{{ number_format($ticketInfo['ticket']->DVS_REMISE, 2) }} DH</td>
                    </tr>
                    @endif
                    @if($ticketInfo['ticket']->DVS_EXONORE)
                    <tr>
                        <td colspan="2" class="text-center">
                            <span class="badge badge-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Ticket Exonéré
                            </span>
                        </td>
                    </tr>
                    @endif
                    @if($ticketInfo['ticket']->DVS_RMARQUE)
                    <tr>
                        <td><strong>Remarque:</strong></td>
                        <td><em>{{ $ticketInfo['ticket']->DVS_RMARQUE }}</em></td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Détails des articles -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-gradient-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-list"></i>
                    Détails des Articles ({{ count($ticketInfo['details']) }} articles)
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Article</th>
                                <th class="text-center">Qté</th>
                                <th class="text-right">Prix Unit.</th>
                                <th class="text-right">Remise</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total_general = 0; @endphp
                            @foreach($ticketInfo['details'] as $detail)
                            @php $total_general += $detail->TOTAL_LIGNE; @endphp
                            <tr>
                                <td>
                                    <strong>{{ $detail->ART_DESIGNATION }}</strong>
                                    <br>
                                    <small class="text-muted">Réf: {{ $detail->ART_REF }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-secondary">{{ $detail->CVD_QTE }}</span>
                                </td>
                                <td class="text-right">{{ number_format($detail->CVD_PRIX_TTC, 2) }} DH</td>
                                <td class="text-right">
                                    @if($detail->CVD_REMISE > 0)
                                        <span class="text-warning">-{{ number_format($detail->CVD_REMISE, 2) }} DH</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-right font-weight-bold">{{ number_format($detail->TOTAL_LIGNE, 2) }} DH</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th colspan="4" class="text-right">Total Général:</th>
                                <th class="text-right text-success">{{ number_format($total_general, 2) }} DH</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions -->
<div class="row mt-3">
    <div class="col-12 text-center">
        <button type="button" class="btn btn-success mr-2" onclick="printTicket('{{ $ticketInfo['ticket']->CMD_REF }}')">
            <i class="fas fa-print"></i>
            Imprimer le Ticket
        </button>
        
        @if($ticketInfo['ticket']->DVS_ETAT !== 'Terminé' && $ticketInfo['ticket']->DVS_ETAT !== 'Annulé')
        <form method="POST" action="{{ route('admin.tickets.update-status') }}" style="display: inline;">
            @csrf
            <input type="hidden" name="cmd_ref" value="{{ $ticketInfo['ticket']->CMD_REF }}">
            <input type="hidden" name="new_status" value="Terminé">
            <button type="submit" class="btn btn-primary mr-2">
                <i class="fas fa-check"></i>
                Marquer Terminé
            </button>
        </form>
        
        <form method="POST" action="{{ route('admin.tickets.delete') }}" style="display: inline;" 
              onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce ticket ?')">
            @csrf
            <input type="hidden" name="cmd_ref" value="{{ $ticketInfo['ticket']->CMD_REF }}">
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-times"></i>
                Annuler le Ticket
            </button>
        </form>
        @endif
        
        <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">
            <i class="fas fa-times"></i>
            Fermer
        </button>
    </div>
</div>
