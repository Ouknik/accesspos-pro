@extends('layouts.sb-admin')

@section('title', 'Gestion des Tickets - AccessPos Pro')

@push('styles')
<style>
    /* Styles personnalisés pour la gestion des tickets */
    .ticket-card {
        transition: all 0.3s ease;
        border-left: 4px solid #e3e6f0;
    }
    
    .ticket-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .ticket-card.en-cours {
        border-left-color: #f6c23e;
    }
    
    .ticket-card.termine {
        border-left-color: #1cc88a;
    }
    
    .ticket-card.en-attente {
        border-left-color: #36b9cc;
    }
    
    .ticket-card.annule {
        border-left-color: #e74a3b;
    }
    
    .status-en-cours {
        background: linear-gradient(45deg, #f6c23e, #dda20a);
        color: white;
    }
    
    .status-termine {
        background: linear-gradient(45deg, #1cc88a, #13855c);
        color: white;
    }
    
    .status-en-attente {
        background: linear-gradient(45deg, #36b9cc, #258391);
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
    
    .ticket-details-modal .modal-content {
        border-radius: 15px;
    }
    
    .print-ticket {
        background: linear-gradient(45deg, #4e73df, #224abe);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }
    
    .print-ticket:hover {
        background: linear-gradient(45deg, #224abe, #1e3a8a);
        transform: translateY(-1px);
        color: white;
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
            <i class="fas fa-ticket-alt text-primary"></i>
            Gestion des Tickets
        </h1>
        <div class="d-none d-lg-inline-block">
            <span class="badge badge-info p-2">
                <i class="fas fa-clock"></i>
                Dernière mise à jour: {{ now()->format('H:i:s') }}
            </span>
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
                                Total Tickets
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats->total_tickets ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
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
                                CA Journalier
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats->ca_journalier ?? 0, 2) }} DH
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
                                En Cours
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats->en_cours ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                Ticket Moyen
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats->ticket_moyen ?? 0, 2) }} DH
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
                    <form method="GET" action="{{ route('admin.tickets.index') }}" class="row">
                        <div class="col-md-3 mb-3">
                            <label class="text-white mb-2">Type de service:</label>
                            <select name="type" class="form-control">
                                <option value="all" {{ $filters['type'] === 'all' ? 'selected' : '' }}>Tous</option>
                                <option value="sur_place" {{ $filters['type'] === 'sur_place' ? 'selected' : '' }}>Sur Place</option>
                                <option value="emporter" {{ $filters['type'] === 'emporter' ? 'selected' : '' }}>À Emporter</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="text-white mb-2">Statut:</label>
                            <select name="status" class="form-control">
                                <option value="all" {{ $filters['status'] === 'all' ? 'selected' : '' }}>Tous</option>
                                <option value="En cours" {{ $filters['status'] === 'En cours' ? 'selected' : '' }}>En cours</option>
                                <option value="Terminé" {{ $filters['status'] === 'Terminé' ? 'selected' : '' }}>Terminé</option>
                                <option value="En attente" {{ $filters['status'] === 'En attente' ? 'selected' : '' }}>En attente</option>
                                <option value="Annulé" {{ $filters['status'] === 'Annulé' ? 'selected' : '' }}>Annulé</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
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

                        <div class="col-md-3 mb-3">
                            <label class="text-white mb-2">Période:</label>
                            <select name="date" class="form-control">
                                <option value="today" {{ $filters['date'] === 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                                <option value="week" {{ $filters['date'] === 'week' ? 'selected' : '' }}>Cette semaine</option>
                                <option value="month" {{ $filters['date'] === 'month' ? 'selected' : '' }}>Ce mois</option>
                                <option value="all" {{ $filters['date'] === 'all' ? 'selected' : '' }}>Toutes</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-light mr-2">
                                <i class="fas fa-search"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-light">
                                <i class="fas fa-undo"></i> Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des tickets -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Liste des Tickets ({{ count($tickets) }} résultats)
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
                            <a class="dropdown-item" href="#" onclick="exportTickets('excel')">
                                <i class="fas fa-file-excel fa-sm fa-fw mr-2 text-gray-400"></i>
                                Exporter Excel
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="ticketsTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>N° Ticket</th>
                                    <th>Date/Heure</th>
                                    <th>Type</th>
                                    <th>Table</th>
                                    <th>Serveur</th>
                                    <th>Articles</th>
                                    <th>Montant TTC</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                <tr class="ticket-row" data-cmd-ref="{{ $ticket->CMD_REF }}">
                                    <td>
                                        <strong class="text-primary">#{{ $ticket->DVS_NUMERO }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $ticket->CMD_REF }}</small>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($ticket->DVS_DATE)->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        @if($ticket->TAB_REF)
                                            <span class="badge badge-success">Sur Place</span>
                                        @else
                                            <span class="badge badge-info">À Emporter</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $ticket->TAB_LIB ?? 'N/A' }}
                                    </td>
                                    <td>{{ $ticket->DVS_SERVEUR ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-pill badge-secondary">
                                            {{ $ticket->NB_ARTICLES ?? 0 }} articles
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-success">{{ number_format($ticket->DVS_MONTANT_TTC, 2) }} DH</strong>
                                        @if($ticket->DVS_REMISE > 0)
                                            <br>
                                            <small class="text-warning">
                                                <i class="fas fa-tag"></i> -{{ number_format($ticket->DVS_REMISE, 2) }} DH
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-pill status-{{ strtolower(str_replace(' ', '-', $ticket->DVS_ETAT)) }}">
                                            {{ $ticket->DVS_ETAT }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="showTicketDetails('{{ $ticket->CMD_REF }}')"
                                                    title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success print-ticket" 
                                                    onclick="printTicket('{{ $ticket->CMD_REF }}')"
                                                    title="Imprimer">
                                                <i class="fas fa-print"></i>
                                            </button>
                                            @if($ticket->DVS_ETAT !== 'Annulé' && $ticket->DVS_ETAT !== 'Terminé')
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                        data-toggle="dropdown" title="Actions">
                                                    <i class="fas fa-cog"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @if($ticket->DVS_ETAT !== 'Terminé')
                                                    <form method="POST" action="{{ route('admin.tickets.update-status') }}" style="display: inline;">
                                                        @csrf
                                                        <input type="hidden" name="cmd_ref" value="{{ $ticket->CMD_REF }}">
                                                        <input type="hidden" name="new_status" value="Terminé">
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="fas fa-check"></i> Marquer terminé
                                                        </button>
                                                    </form>
                                                    @endif
                                                    <form method="POST" action="{{ route('admin.tickets.delete') }}" style="display: inline;">
                                                        @csrf
                                                        <input type="hidden" name="cmd_ref" value="{{ $ticket->CMD_REF }}">
                                                        <button type="submit" class="dropdown-item text-danger" 
                                                                onclick="return confirm('Êtes-vous sûr de vouloir annuler ce ticket ?')">
                                                            <i class="fas fa-times"></i> Annuler
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-gray-500">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>Aucun ticket trouvé avec les critères sélectionnés.</p>
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

<!-- Modal pour les détails du ticket -->
<div class="modal fade ticket-details-modal" id="ticketDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-ticket-alt"></i>
                    Détails du Ticket
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="ticketDetailsContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Chargement...</span>
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
        $('#ticketsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            },
            "order": [[ 1, "desc" ]],
            "pageLength": 25,
            "responsive": true
        });
    }
});

// Fonction pour afficher les détails d'un ticket
function showTicketDetails(cmdRef) {
    $('#ticketDetailsModal').modal('show');
    $('#ticketDetailsContent').html('<div class="text-center py-4"><div class="spinner-border text-primary"><span class="sr-only">Chargement...</span></div></div>');
    
    $.get('{{ route("admin.tickets.details") }}', { cmd_ref: cmdRef })
        .done(function(data) {
            $('#ticketDetailsContent').html(data);
        })
        .fail(function() {
            $('#ticketDetailsContent').html('<div class="alert alert-danger">Erreur lors du chargement des détails</div>');
        });
}

// Fonction pour imprimer un ticket
function printTicket(cmdRef) {
    window.open('{{ route("admin.tickets.print") }}?cmd_ref=' + cmdRef, '_blank', 'width=300,height=600');
}

// Fonction pour exporter les données
function exportTickets(format) {
    // Implémentation de l'export selon le format
    alert('Fonctionnalité d\'export en cours de développement');
}

// Auto-refresh toutes les 30 secondes
setInterval(function() {
    if (!$('.modal').hasClass('show')) {
        location.reload();
    }
}, 30000);
</script>
@endpush
