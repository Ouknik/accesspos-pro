@extends('layouts.sb-admin')

@section('title', 'Gestion des Factures - AccessPos Pro')

@push('styles')
<style>
    /* Styles personnalisés pour la gestion des factures */
    .facture-card {
        transition: all 0.3s ease;
        border-left: 4px solid #e3e6f0;
    }
    
    .facture-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .facture-card.valide {
        border-left-color: #1cc88a;
    }
    
    .facture-card.brouillon {
        border-left-color: #f6c23e;
    }
    
    .facture-card.annule {
        border-left-color: #e74a3b;
    }
    
    .status-valide {
        background: linear-gradient(45deg, #1cc88a, #13855c);
        color: white;
    }
    
    .status-brouillon {
        background: linear-gradient(45deg, #f6c23e, #dda20a);
        color: white;
    }
    
    .status-annule {
        background: linear-gradient(45deg, #e74a3b, #c0392b);
        color: white;
    }
    
    .filter-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .stats-card {
        border: none;
        border-radius: 15px;
    }
    
    .table-hover tbody tr:hover {
        transform: scale(1.02);
        transition: all 0.2s ease;
    }
    
    .facture-details-modal .modal-content {
        border-radius: 15px;
    }
    
    .print-facture {
        background: linear-gradient(45deg, #4e73df, #224abe);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }
    
    .print-facture:hover {
        background: linear-gradient(45deg, #224abe, #1e3a8a);
        transform: translateY(-1px);
        color: white;
    }
    
    .mode-paiement-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .facture-actions .btn-group {
        display: flex;
        gap: 2px;
    }
    
    .montant-highlight {
        font-size: 1.1em;
        font-weight: bold;
    }
    
    @media print {
        body * {
            visibility: hidden;
        }
        .print-area, .print-area * {
            visibility: visible;
        }
        .print-area {
            position: absolute;
            left: 0;
            top: 0;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête de page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-invoice text-primary"></i>
            Gestion des Factures
        </h1>
        <div class="d-none d-lg-inline-block">
            <a href="{{ route('admin.factures.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i>
                Nouvelle Facture
            </a>
        </div>
    </div>

    <!-- Messages de succès/erreur -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Factures
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats->total_factures ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                @if($filters['date'] === 'today')
                                    CA Journalier
                                @elseif($filters['date'] === 'week')
                                    CA Cette Semaine
                                @elseif($filters['date'] === 'month')
                                    CA Ce Mois
                                @elseif($filters['date'] === 'custom')
                                    CA Période Sélectionnée
                                @else
                                    CA Total
                                @endif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats->ca_total ?? 0, 2) }} DH
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Brouillons
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats->brouillons ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-edit fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Facture Moyenne
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats->facture_moyenne ?? 0, 2) }} DH
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow filter-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-filter"></i>
                        Filtres de recherche
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.factures.index') }}" class="row">
                        <div class="col-md-2 mb-3">
                            <label class="text-white mb-2">Statut:</label>
                            <select name="status" class="form-control">
                                <option value="all" {{ $filters['status'] === 'all' ? 'selected' : '' }}>Tous</option>
                                <option value="valide" {{ $filters['status'] === 'valide' ? 'selected' : '' }}>Validées</option>
                                <option value="brouillon" {{ $filters['status'] === 'brouillon' ? 'selected' : '' }}>Brouillons</option>
                                <option value="annule" {{ $filters['status'] === 'annule' ? 'selected' : '' }}>Annulées</option>
                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="text-white mb-2">Client:</label>
                            <select name="client" class="form-control">
                                <option value="all" {{ $filters['client'] === 'all' ? 'selected' : '' }}>Tous</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->CLT_REF }}" {{ $filters['client'] === $client->CLT_REF ? 'selected' : '' }}>
                                        {{ $client->CLT_CLIENT }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="text-white mb-2">Serveur:</label>
                            <select name="serveur" class="form-control">
                                <option value="all" {{ $filters['serveur'] === 'all' ? 'selected' : '' }}>Tous</option>
                                @foreach($serveurs as $serveur)
                                    <option value="{{ $serveur }}" {{ $filters['serveur'] === $serveur ? 'selected' : '' }}>
                                        {{ $serveur }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="text-white mb-2">Mode Paiement:</label>
                            <select name="mode_paiement" class="form-control">
                                <option value="all" {{ $filters['mode_paiement'] === 'all' ? 'selected' : '' }}>Tous</option>
                                @foreach($modesPaiement as $mode)
                                    <option value="{{ $mode }}" {{ $filters['mode_paiement'] === $mode ? 'selected' : '' }}>
                                        {{ $mode }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="text-white mb-2">Période:</label>
                            <select name="date" id="dateFilter" class="form-control">
                                <option value="today" {{ $filters['date'] === 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                                <option value="week" {{ $filters['date'] === 'week' ? 'selected' : '' }}>Cette semaine</option>
                                <option value="month" {{ $filters['date'] === 'month' ? 'selected' : '' }}>Ce mois</option>
                                <option value="custom" {{ $filters['date'] === 'custom' ? 'selected' : '' }}>Période personnalisée</option>
                                <option value="all" {{ $filters['date'] === 'all' ? 'selected' : '' }}>Toutes</option>
                            </select>
                        </div>

                        <!-- Période personnalisée -->
                        <div class="col-md-3 mb-3" id="customDateRange" style="display: {{ $filters['date'] === 'custom' ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-6">
                                    <label class="text-white mb-2">Du:</label>
                                    <input type="date" name="date_debut" class="form-control" value="{{ $filters['date_debut'] ?? '' }}">
                                </div>
                                <div class="col-6">
                                    <label class="text-white mb-2">Au:</label>
                                    <input type="date" name="date_fin" class="form-control" value="{{ $filters['date_fin'] ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="text-white mb-2">Recherche:</label>
                            <input type="text" name="search" class="form-control" placeholder="N° facture, client..." value="{{ $filters['search'] }}">
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-light mr-2">
                                <i class="fas fa-search"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.factures.index') }}" class="btn btn-outline-light">
                                <i class="fas fa-undo"></i> Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des factures -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Liste des Factures ({{ count($factures) }} résultats)
                        @if($filters['date'] === 'custom' && $filters['date_debut'] && $filters['date_fin'])
                            <small class="text-muted d-block">
                                <i class="fas fa-calendar"></i> 
                                Du {{ \Carbon\Carbon::parse($filters['date_debut'])->format('d/m/Y') }} 
                                au {{ \Carbon\Carbon::parse($filters['date_fin'])->format('d/m/Y') }}
                            </small>
                        @elseif($filters['date'] !== 'all')
                            <small class="text-muted d-block">
                                <i class="fas fa-calendar"></i> 
                                @if($filters['date'] === 'today')
                                    Aujourd'hui
                                @elseif($filters['date'] === 'week')
                                    Cette semaine
                                @elseif($filters['date'] === 'month')
                                    Ce mois
                                @endif
                            </small>
                        @endif
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                            <div class="dropdown-header">Actions:</div>
                            <a class="dropdown-item" href="#" onclick="window.print()">
                                <i class="fas fa-print fa-sm fa-fw mr-2 text-gray-400"></i>
                                Imprimer la liste
                            </a>
                            <a class="dropdown-item" href="#" onclick="exportFactures('excel')">
                                <i class="fas fa-file-excel fa-sm fa-fw mr-2 text-gray-400"></i>
                                Exporter Excel
                            </a>
                            <a class="dropdown-item" href="#" onclick="exportFactures('pdf')">
                                <i class="fas fa-file-pdf fa-sm fa-fw mr-2 text-gray-400"></i>
                                Exporter PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="facturesTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>N° Facture</th>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Serveur</th>
                                    <th>Articles</th>
                                    <th>Mode Paiement</th>
                                    <th>Montant TTC</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($factures as $facture)
                                <tr class="facture-row" data-facture-ref="{{ $facture->FCTV_REF }}">
                                    <td>
                                        <strong class="text-primary">#{{ $facture->FCTV_NUMERO }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $facture->FCTV_REF }}</small>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($facture->FCTV_DATE)->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        {{ $facture->CLIENT_NAME }}
                                        @if($facture->FCTV_EXONORE)
                                            <br><span class="badge badge-warning badge-sm">Exonérée</span>
                                        @endif
                                    </td>
                                    <td>{{ $facture->FCTV_SERVEUR ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-pill badge-secondary">
                                            {{ $facture->NB_ARTICLES ?? 0 }} articles
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge mode-paiement-badge
                                            @if($facture->FCTV_MODEPAIEMENT === 'Espèces') badge-success
                                            @elseif($facture->FCTV_MODEPAIEMENT === 'Carte') badge-info
                                            @elseif($facture->FCTV_MODEPAIEMENT === 'Chèque') badge-warning
                                            @else badge-secondary @endif
                                        ">
                                            {{ $facture->FCTV_MODEPAIEMENT }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="montant-highlight text-success">{{ number_format($facture->FCTV_MNT_TTC, 2) }} DH</span>
                                        @if($facture->FCTV_REMISE > 0)
                                            <br>
                                            <small class="text-warning">
                                                <i class="fas fa-tag"></i> -{{ number_format($facture->FCTV_REMISE, 2) }} DH
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($facture->FCTV_ETAT == 0)
                                            <span class="badge badge-pill status-annule">Annulée</span>
                                        @elseif($facture->FCTV_VALIDE == 0)
                                            <span class="badge badge-pill status-brouillon">Brouillon</span>
                                        @else
                                            <span class="badge badge-pill status-valide">Validée</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="facture-actions">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.factures.show', $facture->FCTV_REF) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.factures.print', $facture->FCTV_REF) }}" 
                                                   class="btn btn-sm btn-outline-success print-facture" 
                                                   target="_blank" title="Imprimer">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                @if($facture->FCTV_ETAT != 0)
                                                    @if($facture->FCTV_VALIDE == 0)
                                                        <a href="{{ route('admin.factures.edit', $facture->FCTV_REF) }}" 
                                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                                data-toggle="dropdown" title="Actions">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            @if($facture->FCTV_VALIDE == 0)
                                                            <a class="dropdown-item text-success" href="#" 
                                                               onclick="validateFacture('{{ $facture->FCTV_REF }}')">
                                                                <i class="fas fa-check"></i> Valider
                                                            </a>
                                                            @endif
                                                            <a class="dropdown-item text-info" href="#" 
                                                               onclick="duplicateFacture('{{ $facture->FCTV_REF }}')">
                                                                <i class="fas fa-copy"></i> Dupliquer
                                                            </a>
                                                            <div class="dropdown-divider"></div>
                                                            <form method="POST" action="{{ route('admin.factures.destroy', $facture->FCTV_REF) }}" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger" 
                                                                        onclick="return confirm('Êtes-vous sûr de vouloir annuler cette facture ?')">
                                                                    <i class="fas fa-times"></i> Annuler
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-gray-500">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>Aucune facture trouvée avec les critères sélectionnés.</p>
                                            <a href="{{ route('admin.factures.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Créer votre première facture
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialiser DataTables pour un meilleur tri/recherche
    if ($.fn.DataTable) {
        $('#facturesTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            },
            "order": [[ 1, "desc" ]],
            "pageLength": 25,
            "responsive": true,
            "columnDefs": [
                { "orderable": false, "targets": -1 }
            ]
        });
    }
});

// Fonction pour valider une facture
function validateFacture(factureRef) {
    if (confirm('Êtes-vous sûr de vouloir valider cette facture ?')) {
        // Implémentation de la validation
        alert('Fonctionnalité de validation en cours de développement');
    }
}

// Fonction pour dupliquer une facture
function duplicateFacture(factureRef) {
    if (confirm('Voulez-vous créer une nouvelle facture basée sur celle-ci ?')) {
        window.location.href = '{{ route("admin.factures.create") }}?duplicate=' + factureRef;
    }
}

// Fonction pour exporter les données
function exportFactures(format) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.factures.export") }}';
    
    // Ajouter le token CSRF
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    // Ajouter le format
    const formatInput = document.createElement('input');
    formatInput.type = 'hidden';
    formatInput.name = 'format';
    formatInput.value = format;
    form.appendChild(formatInput);
    
    // Ajouter les filtres actuels
    const urlParams = new URLSearchParams(window.location.search);
    for (const [key, value] of urlParams) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
    }
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Auto-refresh toutes les 60 secondes (moins fréquent que les tickets)
setInterval(function() {
    if (!$('.modal').hasClass('show')) {
        location.reload();
    }
}, 60000);

// Gestion de l'affichage des dates personnalisées
$(document).ready(function() {
    $('#dateFilter').change(function() {
        const selectedValue = $(this).val();
        const customDateRange = $('#customDateRange');
        
        if (selectedValue === 'custom') {
            customDateRange.show();
            // Définir les dates par défaut si vides
            const dateDebut = $('input[name="date_debut"]');
            const dateFin = $('input[name="date_fin"]');
            
            if (!dateDebut.val()) {
                // Début du mois actuel
                const today = new Date();
                const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                dateDebut.val(firstDay.toISOString().split('T')[0]);
            }
            
            if (!dateFin.val()) {
                // Aujourd'hui
                const today = new Date();
                dateFin.val(today.toISOString().split('T')[0]);
            }
        } else {
            customDateRange.hide();
            // Vider les dates personnalisées
            $('input[name="date_debut"]').val('');
            $('input[name="date_fin"]').val('');
        }
    });
    
    // Validation des dates
    $('input[name="date_debut"], input[name="date_fin"]').change(function() {
        const dateDebut = new Date($('input[name="date_debut"]').val());
        const dateFin = new Date($('input[name="date_fin"]').val());
        
        if (dateDebut && dateFin && dateDebut > dateFin) {
            alert('تاريخ البداية يجب أن يكون قبل تاريخ النهاية');
            $(this).val('');
        }
    });
});
</script>
@endpush
