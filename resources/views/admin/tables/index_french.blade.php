@extends('layouts.sb-admin')

@section('title', 'Gestion des tables - AccessPos Pro')

@section('content')
<div class="container-fluid">
    
    {{-- Titre et boutons --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-utensils text-primary"></i>
                Gestion des tables
            </h1>
            <p class="mb-0 text-muted">Gestion complète des tables du restaurant et de leurs états</p>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-success btn-sm" onclick="refreshTables()">
                <i class="fas fa-sync-alt"></i>
                Actualiser
            </button>
            <a href="{{ route('admin.tables.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i>
                Ajouter une nouvelle table
            </a>
            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#layoutModal">
                <i class="fas fa-map"></i>
                Voir le plan
            </button>
        </div>
    </div>

    {{-- Statistiques rapides --}}
    <div class="row mb-4">
        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total des tables
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-total">
                                {{ $statistics['total'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-table fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tables libres
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-libres">
                                {{ $statistics['libres'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Tables occupées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-occupees">
                                {{ $statistics['occupees'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Tables réservées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-reservees">
                                {{ $statistics['reservees'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Hors service
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-hors-service">
                                {{ $statistics['hors_service'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total couverts
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-couverts">
                                {{ $statistics['total_couverts'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chair fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres de recherche --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtres de recherche et de tri</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="filter-zone">Zone</label>
                    <select class="form-control" id="filter-zone">
                        <option value="">Toutes les zones</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->ZON_REF }}">{{ $zone->ZON_LIB }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-status">État</label>
                    <select class="form-control" id="filter-status">
                        <option value="">Tous les états</option>
                        <option value="LIBRE">Libre</option>
                        <option value="OCCUPEE">Occupée</option>
                        <option value="RESERVEE">Réservée</option>
                        <option value="HORS_SERVICE">Hors service</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-search">Recherche</label>
                    <input type="text" class="form-control" id="filter-search" placeholder="Nom de la table ou description">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                        <i class="fas fa-times"></i>
                        Effacer les filtres
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau des tables --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des tables</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tablesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Zone</th>
                            <th>Couverts</th>
                            <th>État</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tables-tbody">
                        @foreach($tables as $table)
                        <tr data-zone="{{ $table->ZON_REF }}" data-status="{{ $table->ETT_ETAT }}">
                            <td>
                                <strong>{{ $table->TAB_LIB }}</strong><br>
                                <small class="text-muted">Réf: {{ $table->TAB_REF }}</small>
                            </td>
                            <td>{{ $table->zone->ZON_LIB ?? 'Non définie' }}</td>
                            <td>
                                <span class="badge badge-info">{{ $table->TAB_NBR_Couvert }} couverts</span>
                            </td>
                            <td>
                                @php
                                    $statusConfig = [
                                        'LIBRE' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Libre'],
                                        'OCCUPEE' => ['class' => 'danger', 'icon' => 'users', 'text' => 'Occupée'],
                                        'RESERVEE' => ['class' => 'warning', 'icon' => 'calendar-check', 'text' => 'Réservée'],
                                        'HORS_SERVICE' => ['class' => 'secondary', 'icon' => 'tools', 'text' => 'Hors service']
                                    ];
                                    $config = $statusConfig[$table->ETT_ETAT] ?? ['class' => 'light', 'icon' => 'question', 'text' => 'Non défini'];
                                @endphp
                                <span class="badge badge-{{ $config['class'] }}">
                                    <i class="fas fa-{{ $config['icon'] }}"></i>
                                    {{ $config['text'] }}
                                </span>
                            </td>
                            <td>
                                @if($table->TAB_DESCRIPT)
                                    {{ Str::limit($table->TAB_DESCRIPT, 50) }}
                                @else
                                    <span class="text-muted">Aucune description</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-info" 
                                            onclick="showTableDetails('{{ $table->TAB_REF }}')" 
                                            title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route('admin.tables.edit', $table->TAB_REF) }}" 
                                       class="btn btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" 
                                                data-toggle="dropdown" title="Changer l'état">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" 
                                                    onclick="changeTableStatus('{{ $table->TAB_REF }}', 'LIBRE')">
                                                <i class="fas fa-check-circle text-success"></i> Libre
                                            </button>
                                            <button class="dropdown-item" 
                                                    onclick="changeTableStatus('{{ $table->TAB_REF }}', 'OCCUPEE')">
                                                <i class="fas fa-users text-danger"></i> Occupée
                                            </button>
                                            <button class="dropdown-item" 
                                                    onclick="changeTableStatus('{{ $table->TAB_REF }}', 'RESERVEE')">
                                                <i class="fas fa-calendar-check text-warning"></i> Réservée
                                            </button>
                                            <button class="dropdown-item" 
                                                    onclick="changeTableStatus('{{ $table->TAB_REF }}', 'HORS_SERVICE')">
                                                <i class="fas fa-tools text-secondary"></i> Hors service
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteTable('{{ $table->TAB_REF }}', '{{ $table->TAB_LIB }}')" 
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal détails de la table --}}
<div class="modal fade" id="tableDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails de la table</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="tableDetailsContent">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2">Chargement en cours...</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal plan --}}
<div class="modal fade" id="layoutModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Plan des tables</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="layoutContainer" class="text-center" style="min-height: 400px;">
                    <p class="text-muted">Un plan interactif des tables sera ajouté ici</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Activer DataTable
    $('#tablesTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
        },
        "pageLength": 25,
        "responsive": true,
        "order": [[1, "asc"], [0, "asc"]]
    });

    // Filtres de recherche
    $('#filter-zone, #filter-status').change(function() {
        filterTables();
    });

    $('#filter-search').on('keyup', function() {
        filterTables();
    });
});

// Actualiser les données
function refreshTables() {
    $.get('{{ route("admin.tables.data") }}', function(response) {
        if (response.success) {
            updateStatistics(response.statistiques);
            location.reload(); // ou mise à jour dynamique du tableau
        }
    }).fail(function() {
        showToast('error', 'Erreur lors de l\'actualisation des données');
    });
}

// Mettre à jour les statistiques
function updateStatistics(stats) {
    $('#stat-total').text(stats.total);
    $('#stat-libres').text(stats.libres);
    $('#stat-occupees').text(stats.occupees);
    $('#stat-reservees').text(stats.reservees);
    $('#stat-hors-service').text(stats.hors_service);
    $('#stat-couverts').text(stats.total_couverts);
}

// Filtrer les tables
function filterTables() {
    const zone = $('#filter-zone').val();
    const status = $('#filter-status').val();
    const search = $('#filter-search').val().toLowerCase();

    $('#tables-tbody tr').each(function() {
        const $row = $(this);
        const rowZone = $row.data('zone');
        const rowStatus = $row.data('status');
        const rowText = $row.text().toLowerCase();

        let show = true;

        if (zone && rowZone !== zone) show = false;
        if (status && rowStatus !== status) show = false;
        if (search && !rowText.includes(search)) show = false;

        $row.toggle(show);
    });
}

// Effacer les filtres
function clearFilters() {
    $('#filter-zone, #filter-status').val('');
    $('#filter-search').val('');
    $('#tables-tbody tr').show();
}

// Changer l'état de la table
function changeTableStatus(tabRef, newStatus) {
    const statusNames = {
        'LIBRE': 'libre',
        'OCCUPEE': 'occupée', 
        'RESERVEE': 'réservée',
        'HORS_SERVICE': 'hors service'
    };
    
    if (confirm(`Êtes-vous sûr de vouloir changer l'état de la table en "${statusNames[newStatus]}" ?`)) {
        $.post('{{ url("admin/tables") }}/' + tabRef + '/status', {
            _token: '{{ csrf_token() }}',
            status: newStatus
        }).done(function(response) {
            if (response.success) {
                showToast('success', response.message);
                refreshTables();
            } else {
                showToast('error', response.error || 'Erreur lors du changement d\'état');
            }
        }).fail(function() {
            showToast('error', 'Erreur de connexion au serveur');
        });
    }
}

// Afficher les détails de la table
function showTableDetails(tabRef) {
    $('#tableDetailsModal').modal('show');
    $('#tableDetailsContent').html(`
        <div class="text-center">
            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
            <p class="mt-2">Chargement en cours...</p>
        </div>
    `);

    $.get('{{ url("admin/tables") }}/' + tabRef)
        .done(function(response) {
            $('#tableDetailsContent').html(response);
        })
        .fail(function() {
            $('#tableDetailsContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Erreur lors du chargement des détails
                </div>
            `);
        });
}

// Supprimer une table
function deleteTable(tabRef, tableName) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer la table "${tableName}" ?`)) {
        $.ajax({
            url: '{{ url("admin/tables") }}/' + tabRef,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', response.success);
                    location.reload();
                } else {
                    showToast('error', response.error);
                }
            },
            error: function() {
                showToast('error', 'Erreur lors de la suppression de la table');
            }
        });
    }
}

// Afficher les messages d'alerte
function showToast(type, message) {
    // Peut utiliser une bibliothèque Toast comme Toastr
    if (type === 'success') {
        alert('✅ ' + message);
    } else {
        alert('❌ ' + message);
    }
}
</script>
@endsection
