@extends('layouts.sb-admin')

@section('title', 'Détails Facture #' . $facture['facture']->FCTV_NUMERO . ' - AccessPos Pro')

@push('styles')
<style>
    .facture-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .facture-info-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .status-badge {
        font-size: 1rem;
        padding: 0.5rem 1rem;
        border-radius: 25px;
    }
    
    .montant-total {
        font-size: 2rem;
        font-weight: bold;
        color: #1cc88a;
    }
    
    .article-item {
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 0;
        transition: background-color 0.3s ease;
    }
    
    .article-item:hover {
        background-color: #f8f9fc;
        border-radius: 8px;
        margin: 0 -10px;
        padding: 1rem 10px;
    }
    
    .article-item:last-child {
        border-bottom: none;
    }
    
    .print-section {
        background: #f8f9fc;
        border-radius: 10px;
        padding: 1.5rem;
        margin: 2rem 0;
    }
    
    .mode-paiement-detail {
        background: linear-gradient(45deg, #36b9cc, #258391);
        color: white;
        border-radius: 10px;
        padding: 1rem;
        margin: 1rem 0;
    }
    
    .timeline-item {
        border-left: 3px solid #4e73df;
        padding-left: 1rem;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
    }
    
    .btn-action {
        transition: all 0.3s ease;
        border-radius: 25px;
        padding: 0.5rem 1.5rem;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.15);
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        
        .facture-header {
            background: #4e73df !important;
            -webkit-print-color-adjust: exact;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête de la facture -->
    <div class="facture-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-2">
                    <i class="fas fa-file-invoice"></i>
                    Facture #{{ $facture['facture']->FCTV_NUMERO }}
                </h1>
                <p class="mb-0 opacity-75">
                    Référence: {{ $facture['facture']->FCTV_REF }} | 
                    Date: {{ \Carbon\Carbon::parse($facture['facture']->FCTV_DATE)->format('d/m/Y à H:i') }}
                </p>
            </div>
            <div class="col-md-4 text-right">
                <div class="montant-total">
                    {{ number_format($facture['facture']->FCTV_MNT_TTC, 2) }} DH
                </div>
                @if($facture['facture']->FCTV_ETAT == 0)
                    <span class="status-badge bg-danger">ANNULÉE</span>
                @elseif($facture['facture']->FCTV_VALIDE == 0)
                    <span class="status-badge bg-warning">BROUILLON</span>
                @else
                    <span class="status-badge bg-success">VALIDÉE</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Boutons d'actions principaux -->
    <div class="row mb-4 no-print">
        <div class="col-12">
            <div class="btn-toolbar justify-content-between" role="toolbar">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.factures.index') }}" class="btn btn-secondary btn-action">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                    <a href="{{ route('admin.factures.print', $facture['facture']->FCTV_REF) }}" 
                       class="btn btn-primary btn-action" target="_blank">
                        <i class="fas fa-print"></i> Imprimer
                    </a>
                    @if($facture['facture']->FCTV_ETAT != 0 && $facture['facture']->FCTV_VALIDE == 0)
                    <a href="{{ route('admin.factures.edit', $facture['facture']->FCTV_REF) }}" 
                       class="btn btn-warning btn-action">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    @endif
                </div>
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.factures.create') }}?duplicate={{ $facture['facture']->FCTV_REF }}" 
                       class="btn btn-info btn-action">
                        <i class="fas fa-copy"></i> Dupliquer
                    </a>
                    @if($facture['facture']->FCTV_ETAT != 0)
                    <form method="POST" action="{{ route('admin.factures.destroy', $facture['facture']->FCTV_REF) }}" 
                          style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette facture ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-action">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-md-8">
            <!-- Informations client et facture -->
            <div class="card facture-info-card mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle"></i>
                        Informations de la Facture
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Client</h6>
                            <p class="mb-1"><strong>{{ $facture['facture']->CLIENT_NAME }}</strong></p>
                            @if(!empty($facture['facture']->CLT_ADRESSE))
                            <p class="mb-1 text-muted">{{ $facture['facture']->CLT_ADRESSE }}</p>
                            @endif
                            @if(!empty($facture['facture']->CLT_TELEPHONE))
                            <p class="mb-1 text-muted">
                                <i class="fas fa-phone"></i> {{ $facture['facture']->CLT_TELEPHONE }}
                            </p>
                            @endif
                            @if(!empty($facture['facture']->CLT_EMAIL))
                            <p class="mb-1 text-muted">
                                <i class="fas fa-envelope"></i> {{ $facture['facture']->CLT_EMAIL }}
                            </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Détails Facture</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Serveur:</strong></td>
                                    <td>{{ $facture['facture']->FCTV_SERVEUR ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Table:</strong></td>
                                    <td>{{ $facture['facture']->TAB_REF ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Caisse:</strong></td>
                                    <td>N° {{ $facture['facture']->CSS_ID_CAISSE ?? 1 }}</td>
                                </tr>
                                @if($facture['facture']->FCTV_EXONORE)
                                <tr>
                                    <td colspan="2">
                                        <span class="badge badge-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Facture Exonérée
                                        </span>
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Articles de la facture -->
            <div class="card facture-info-card mb-4">
                <div class="card-header bg-gradient-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-list"></i>
                        Articles Facturés ({{ count($facture['details']) }} articles)
                    </h6>
                </div>
                <div class="card-body p-0">
                    @php $totalGeneral = 0; @endphp
                    @foreach($facture['details'] as $detail)
                        @php $totalGeneral += $detail->TOTAL_LIGNE; @endphp
                        <div class="article-item px-3">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-1">{{ $detail->ART_DESIGNATION }}</h6>
                                    <small class="text-muted">Réf: {{ $detail->ART_REF }}</small>
                                    @if($detail->IsMenu)
                                        <br><span class="badge badge-info badge-sm">Menu: {{ $detail->NameMenu }}</span>
                                    @endif
                                </div>
                                <div class="col-md-2 text-center">
                                    <span class="badge badge-pill badge-secondary">
                                        {{ $detail->FVD_QTE }}
                                    </span>
                                </div>
                                <div class="col-md-2 text-right">
                                    <strong>{{ number_format($detail->FVD_PRIX_VNT_TTC, 2) }} DH</strong>
                                    <br>
                                    <small class="text-muted">{{ number_format($detail->FVD_PRIX_VNT_HT, 2) }} HT</small>
                                </div>
                                <div class="col-md-2 text-right">
                                    @if($detail->FVD_REMISE > 0)
                                        <span class="text-warning d-block">
                                            -{{ number_format($detail->FVD_REMISE, 2) }} DH
                                        </span>
                                    @endif
                                    <strong class="text-success">
                                        {{ number_format($detail->TOTAL_LIGNE, 2) }} DH
                                    </strong>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Commandes liées (si applicable) -->
            @if(count($facture['commandes']) > 0)
            <div class="card facture-info-card mb-4">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-link"></i>
                        Commandes Associées ({{ count($facture['commandes']) }})
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($facture['commandes'] as $commande)
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3">
                                <h6 class="text-primary">Ticket #{{ $commande->DVS_NUMERO }}</h6>
                                <p class="mb-1">
                                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($commande->DVS_DATE)->format('d/m/Y H:i') }}
                                </p>
                                <p class="mb-1">
                                    <strong>Montant:</strong> {{ number_format($commande->DVS_MONTANT_TTC, 2) }} DH
                                </p>
                                <p class="mb-0">
                                    <strong>Statut:</strong> 
                                    <span class="badge badge-success">{{ $commande->DVS_ETAT }}</span>
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Panneau latéral -->
        <div class="col-md-4">
            <!-- Résumé financier -->
            <div class="card facture-info-card mb-4">
                <div class="card-header bg-gradient-warning text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-calculator"></i>
                        Résumé Financier
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Sous-total HT:</strong></td>
                            <td class="text-right">{{ number_format($facture['facture']->FCTV_MNT_HT, 2) }} DH</td>
                        </tr>
                        @if($facture['facture']->FCTV_REMISE > 0)
                        <tr>
                            <td><strong>Remise globale:</strong></td>
                            <td class="text-right text-warning">-{{ number_format($facture['facture']->FCTV_REMISE, 2) }} DH</td>
                        </tr>
                        @endif
                        <tr class="table-active">
                            <td><strong>TOTAL TTC:</strong></td>
                            <td class="text-right"><strong class="text-success">{{ number_format($facture['facture']->FCTV_MNT_TTC, 2) }} DH</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Mode de paiement -->
            <div class="card facture-info-card mb-4">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-credit-card"></i>
                        Mode de Paiement
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mode-paiement-detail">
                        <h6 class="mb-3">{{ $facture['facture']->FCTV_MODEPAIEMENT }}</h6>
                        
                        @if($facture['facture']->MontantEspece > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-money-bill"></i> Espèces:</span>
                            <strong>{{ number_format($facture['facture']->MontantEspece, 2) }} DH</strong>
                        </div>
                        @endif
                        
                        @if($facture['facture']->MontantCharte > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-credit-card"></i> Carte:</span>
                            <strong>{{ number_format($facture['facture']->MontantCharte, 2) }} DH</strong>
                        </div>
                        @endif
                        
                        @if($facture['facture']->MontantCredit > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-clock"></i> Crédit:</span>
                            <strong>{{ number_format($facture['facture']->MontantCredit, 2) }} DH</strong>
                        </div>
                        @endif
                        
                        @if($facture['facture']->MontantCheque > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-money-check"></i> Chèque:</span>
                            <strong>{{ number_format($facture['facture']->MontantCheque, 2) }} DH</strong>
                        </div>
                        @endif
                        
                        @if($facture['facture']->FCTV_RENDU > 0)
                        <div class="d-flex justify-content-between mt-3 pt-3 border-top border-light">
                            <span><i class="fas fa-undo"></i> Rendu:</span>
                            <strong>{{ number_format($facture['facture']->FCTV_RENDU, 2) }} DH</strong>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Remarques -->
            @if(!empty($facture['facture']->FCTV_RMARQUE))
            <div class="card facture-info-card mb-4">
                <div class="card-header bg-gradient-secondary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-comment"></i>
                        Remarques
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0 font-italic">{{ $facture['facture']->FCTV_RMARQUE }}</p>
                </div>
            </div>
            @endif

            <!-- Actions rapides -->
            <div class="card facture-info-card no-print">
                <div class="card-header bg-gradient-dark text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt"></i>
                        Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.factures.print', $facture['facture']->FCTV_REF) }}" 
                           class="btn btn-primary btn-sm" target="_blank">
                            <i class="fas fa-print"></i> Imprimer Facture
                        </a>
                        <a href="#" class="btn btn-info btn-sm" onclick="sendByEmail()">
                            <i class="fas fa-envelope"></i> Envoyer par Email
                        </a>
                        <a href="#" class="btn btn-success btn-sm" onclick="exportToPDF()">
                            <i class="fas fa-file-pdf"></i> Exporter PDF
                        </a>
                        @if($facture['facture']->FCTV_VALIDE == 1 && $facture['facture']->FCTV_ETAT == 1)
                        <a href="#" class="btn btn-warning btn-sm" onclick="createAvoir()">
                            <i class="fas fa-undo"></i> Créer un Avoir
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function sendByEmail() {
    alert('Fonctionnalité d\'envoi par email en cours de développement');
}

function exportToPDF() {
    window.open('{{ route("admin.factures.print", $facture["facture"]->FCTV_REF) }}?format=pdf', '_blank');
}

function createAvoir() {
    if (confirm('Voulez-vous créer un avoir pour cette facture ?')) {
        alert('Fonctionnalité de création d\'avoir en cours de développement');
    }
}
</script>
@endpush
