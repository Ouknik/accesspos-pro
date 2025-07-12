@extends('layouts.sb-admin')

@section('title', 'État des Tables - AccessPos Pro')

@section('page-heading')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-utensils"></i>
            État des Tables
        </h1>
        <p class="mb-0 text-muted">Gestion et suivi en temps réel des tables du restaurant</p>
    </div>
    <div class="btn-group">
        <button type="button" class="btn btn-success btn-sm" onclick="refreshTables()">
            <i class="fas fa-sync"></i>
            Actualiser
        </button>
        <button type="button" class="btn btn-primary btn-sm" onclick="viewFullScreen()">
            <i class="fas fa-expand"></i>
            Plein Écran
        </button>
        <button type="button" class="btn btn-info btn-sm" onclick="printLayout()">
            <i class="fas fa-print"></i>
            Imprimer
        </button>
    </div>
</div>
@endsection

@section('content')

{{-- Statistiques Rapides --}}
<div class="row mb-4">
    
    {{-- Total Tables --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Tables
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $statistiques['total'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-table fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tables Libres --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Tables Libres
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $statistiques['libres'] ?? 0 }}
                        </div>
                        @if(isset($statistiques['total']) && $statistiques['total'] > 0)
                            <div class="text-xs text-muted">
                                {{ number_format(($statistiques['libres'] / $statistiques['total']) * 100, 1) }}% disponibles
                            </div>
                        @endif
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tables Occupées --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Tables Occupées
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $statistiques['occupees'] ?? 0 }}
                        </div>
                        @if(isset($statistiques['total']) && $statistiques['total'] > 0)
                            <div class="text-xs text-muted">
                                {{ number_format(($statistiques['occupees'] / $statistiques['total']) * 100, 1) }}% occupées
                            </div>
                        @endif
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tables Réservées --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Tables Réservées
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $statistiques['reservees'] ?? 0 }}
                        </div>
                        @if(isset($statistiques['total']) && $statistiques['total'] > 0)
                            <div class="text-xs text-muted">
                                {{ number_format(($statistiques['reservees'] / $statistiques['total']) * 100, 1) }}% réservées
                            </div>
                        @endif
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Barre de Progression Globale --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-chart-bar mr-2"></i>
            Taux d'Occupation Global
        </h6>
    </div>
    <div class="card-body">
        @php
            $total = $statistiques['total'] ?? 1;
            $libres = $statistiques['libres'] ?? 0;
            $occupees = $statistiques['occupees'] ?? 0;
            $reservees = $statistiques['reservees'] ?? 0;
            
            $pourcentageLibres = ($libres / $total) * 100;
            $pourcentageOccupees = ($occupees / $total) * 100;
            $pourcentageReservees = ($reservees / $total) * 100;
        @endphp
        
        <div class="mb-3">
            <div class="small mb-1">
                Répartition des Tables 
                <span class="float-right">{{ $total }} tables au total</span>
            </div>
            <div class="progress">
                <div class="progress-bar bg-success" 
                     role="progressbar" 
                     style="width: {{ $pourcentageLibres }}%" 
                     title="Tables Libres">
                </div>
                <div class="progress-bar bg-warning" 
                     role="progressbar" 
                     style="width: {{ $pourcentageOccupees }}%" 
                     title="Tables Occupées">
                </div>
                <div class="progress-bar bg-info" 
                     role="progressbar" 
                     style="width: {{ $pourcentageReservees }}%" 
                     title="Tables Réservées">
                </div>
            </div>
        </div>
        
        <div class="row text-center">
            <div class="col-4">
                <div class="text-success">
                    <i class="fas fa-circle"></i>
                    Libres: {{ number_format($pourcentageLibres, 1) }}%
                </div>
            </div>
            <div class="col-4">
                <div class="text-warning">
                    <i class="fas fa-circle"></i>
                    Occupées: {{ number_format($pourcentageOccupees, 1) }}%
                </div>
            </div>
            <div class="col-4">
                <div class="text-info">
                    <i class="fas fa-circle"></i>
                    Réservées: {{ number_format($pourcentageReservees, 1) }}%
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Plan des Tables --}}
<div class="row">
    
    {{-- Vue en Grille --}}
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-th mr-2"></i>
                    Plan des Tables
                </h6>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-primary active" onclick="changeView('grid')">
                        <i class="fas fa-th"></i>
                        Grille
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="changeView('list')">
                        <i class="fas fa-list"></i>
                        Liste
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="tablesGrid" class="tables-grid">
                    @if(isset($tables) && count($tables) > 0)
                        <div class="row">
                            @foreach($tables as $table)
                                @php
                                    $statut = $table['statut'] ?? 'libre';
                                    $badgeClass = $statut == 'libre' ? 'success' : ($statut == 'occupee' ? 'warning' : 'info');
                                    $iconClass = $statut == 'libre' ? 'check-circle' : ($statut == 'occupee' ? 'users' : 'calendar-check');
                                @endphp
                                
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                    <div class="card border-left-{{ $badgeClass }} h-100 table-card" 
                                         data-table-id="{{ $table['id'] ?? '' }}"
                                         onclick="selectTable({{ $table['id'] ?? 0 }})">
                                        <div class="card-body text-center">
                                            <div class="mb-2">
                                                <i class="fas fa-{{ $iconClass }} fa-2x text-{{ $badgeClass }}"></i>
                                            </div>
                                            <h6 class="font-weight-bold">{{ $table['numero'] ?? 'N/A' }}</h6>
                                            <p class="text-muted small mb-2">
                                                {{ $table['capacite'] ?? 0 }} places
                                            </p>
                                            <span class="badge badge-{{ $badgeClass }}">
                                                {{ ucfirst($statut) }}
                                            </span>
                                            
                                            @if($statut == 'occupee' && isset($table['client']))
                                                <div class="mt-2">
                                                    <small class="text-muted">{{ $table['client'] }}</small>
                                                </div>
                                            @endif
                                            
                                            @if($statut == 'reservee' && isset($table['reservation_heure']))
                                                <div class="mt-2">
                                                    <small class="text-muted">{{ $table['reservation_heure'] }}</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-table fa-3x mb-3"></i>
                            <p>Aucune table configurée</p>
                            <button type="button" class="btn btn-primary" onclick="addTable()">
                                <i class="fas fa-plus"></i>
                                Ajouter une Table
                            </button>
                        </div>
                    @endif
                </div>
                
                {{-- Vue en Liste (cachée par défaut) --}}
                <div id="tablesList" class="tables-list" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>N° Table</th>
                                    <th>Capacité</th>
                                    <th>Statut</th>
                                    <th>Client/Réservation</th>
                                    <th>Heure</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($tables) && count($tables) > 0)
                                    @foreach($tables as $table)
                                        @php
                                            $statut = $table['statut'] ?? 'libre';
                                            $badgeClass = $statut == 'libre' ? 'success' : ($statut == 'occupee' ? 'warning' : 'info');
                                        @endphp
                                        <tr>
                                            <td class="font-weight-bold">{{ $table['numero'] ?? 'N/A' }}</td>
                                            <td>{{ $table['capacite'] ?? 0 }} places</td>
                                            <td>
                                                <span class="badge badge-{{ $badgeClass }}">
                                                    {{ ucfirst($statut) }}
                                                </span>
                                            </td>
                                            <td>{{ $table['client'] ?? $table['reservation_nom'] ?? '-' }}</td>
                                            <td>{{ $table['heure_occupation'] ?? $table['reservation_heure'] ?? '-' }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-info" onclick="viewTable({{ $table['id'] ?? 0 }})">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-warning" onclick="editTable({{ $table['id'] ?? 0 }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions et Informations --}}
    <div class="col-xl-4 col-lg-5">
        
        {{-- Actions Rapides --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-bolt mr-2"></i>
                    Actions Rapides
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success btn-block" onclick="addTable()">
                        <i class="fas fa-plus"></i>
                        Ajouter une Table
                    </button>
                    
                    <button type="button" class="btn btn-primary btn-block" onclick="addReservation()">
                        <i class="fas fa-calendar-plus"></i>
                        Nouvelle Réservation
                    </button>
                    
                    <button type="button" class="btn btn-warning btn-block" onclick="liberateAllTables()">
                        <i class="fas fa-broom"></i>
                        Libérer Toutes les Tables
                    </button>
                    
                    <button type="button" class="btn btn-info btn-block" onclick="generateReport()">
                        <i class="fas fa-chart-bar"></i>
                        Générer un Rapport
                    </button>
                </div>
            </div>
        </div>

        {{-- Table Sélectionnée --}}
        <div class="card shadow mb-4" id="selectedTableCard" style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-crosshairs mr-2"></i>
                    Table Sélectionnée
                </h6>
            </div>
            <div class="card-body" id="selectedTableContent">
                <!-- Contenu dynamique -->
            </div>
        </div>

        {{-- Réservations du Jour --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar-day mr-2"></i>
                    Réservations du Jour
                </h6>
            </div>
            <div class="card-body">
                @if(isset($reservations) && count($reservations) > 0)
                    @foreach($reservations as $reservation)
                        <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                            <div class="mr-3">
                                <i class="fas fa-clock text-info"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">{{ $reservation['heure'] ?? 'N/A' }}</div>
                                <div class="text-muted small">
                                    Table {{ $reservation['table_numero'] ?? 'N/A' }} - {{ $reservation['client'] ?? 'Client inconnu' }}
                                </div>
                            </div>
                            <div>
                                <span class="badge badge-{{ $reservation['statut'] == 'confirmee' ? 'success' : 'warning' }}">
                                    {{ ucfirst($reservation['statut'] ?? 'En attente') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                        <p class="mb-0">Aucune réservation aujourd'hui</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Activité Récente --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-secondary">
                    <i class="fas fa-history mr-2"></i>
                    Activité Récente
                </h6>
            </div>
            <div class="card-body">
                @if(isset($activiteRecente) && count($activiteRecente) > 0)
                    @foreach($activiteRecente as $activite)
                        <div class="d-flex align-items-start mb-3">
                            <div class="mr-3">
                                @php
                                    $iconClass = $activite['type'] == 'occupation' ? 'user-plus text-warning' : 
                                                ($activite['type'] == 'liberation' ? 'user-minus text-success' : 'calendar-check text-info');
                                @endphp
                                <i class="fas fa-{{ $iconClass }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small font-weight-bold">{{ $activite['action'] ?? 'Action inconnue' }}</div>
                                <div class="text-xs text-muted">
                                    {{ $activite['heure'] ?? 'N/A' }} - Table {{ $activite['table'] ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-history fa-2x mb-2"></i>
                        <p class="mb-0 small">Aucune activité récente</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- Modal de Gestion de Table --}}
<div class="modal fade" id="tableModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tableModalTitle">
                    <i class="fas fa-table"></i>
                    Gestion de Table
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="tableModalContent">
                <!-- Contenu dynamique -->
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let selectedTableId = null;

$(document).ready(function() {
    // Auto-refresh toutes les 30 secondes
    setInterval(refreshTables, 30000);
});

function refreshTables() {
    // Recharger la page ou faire un appel AJAX
    window.location.reload();
}

function changeView(viewType) {
    const gridView = document.getElementById('tablesGrid');
    const listView = document.getElementById('tablesList');
    const buttons = document.querySelectorAll('[onclick^="changeView"]');
    
    buttons.forEach(btn => btn.classList.remove('active'));
    
    if (viewType === 'grid') {
        gridView.style.display = 'block';
        listView.style.display = 'none';
        event.target.classList.add('active');
    } else {
        gridView.style.display = 'none';
        listView.style.display = 'block';
        event.target.classList.add('active');
    }
}

function selectTable(tableId) {
    selectedTableId = tableId;
    
    // Retirer la sélection précédente
    document.querySelectorAll('.table-card').forEach(card => {
        card.classList.remove('border-primary');
    });
    
    // Ajouter la sélection à la nouvelle table
    const selectedCard = document.querySelector(`[data-table-id="${tableId}"]`);
    if (selectedCard) {
        selectedCard.classList.add('border-primary');
    }
    
    // Afficher les informations de la table sélectionnée
    showTableDetails(tableId);
}

function showTableDetails(tableId) {
    // Ici vous feriez un appel AJAX pour récupérer les détails de la table
    const selectedTableCard = document.getElementById('selectedTableCard');
    const selectedTableContent = document.getElementById('selectedTableContent');
    
    // Simulation des données de la table
    const tableData = {
        numero: 'Table ' + tableId,
        capacite: 4,
        statut: 'libre',
        client: null,
        heure: null
    };
    
    selectedTableContent.innerHTML = `
        <div class="text-center mb-3">
            <h5 class="font-weight-bold">${tableData.numero}</h5>
            <p class="text-muted">${tableData.capacite} places</p>
        </div>
        
        <div class="mb-3">
            <strong>Statut:</strong>
            <span class="badge badge-success ml-2">${tableData.statut}</span>
        </div>
        
        <div class="d-grid gap-2">
            <button class="btn btn-warning btn-sm" onclick="changeTableStatus(${tableId}, 'occupee')">
                <i class="fas fa-user-plus"></i>
                Marquer Occupée
            </button>
            <button class="btn btn-info btn-sm" onclick="addReservationToTable(${tableId})">
                <i class="fas fa-calendar-plus"></i>
                Réserver
            </button>
            <button class="btn btn-success btn-sm" onclick="changeTableStatus(${tableId}, 'libre')">
                <i class="fas fa-check"></i>
                Libérer
            </button>
            <button class="btn btn-danger btn-sm" onclick="deleteTable(${tableId})">
                <i class="fas fa-trash"></i>
                Supprimer
            </button>
        </div>
    `;
    
    selectedTableCard.style.display = 'block';
}

function changeTableStatus(tableId, newStatus) {
    // Appel AJAX pour changer le statut
    console.log(`Changement statut table ${tableId} vers ${newStatus}`);
    
    Swal.fire({
        icon: 'success',
        title: 'Statut Modifié',
        text: `Le statut de la table a été changé vers "${newStatus}".`,
        confirmButtonClass: 'btn btn-success'
    }).then(() => {
        refreshTables();
    });
}

function addTable() {
    $('#tableModalTitle').html('<i class="fas fa-plus"></i> Ajouter une Table');
    $('#tableModalContent').html(`
        <form id="addTableForm">
            <div class="form-group">
                <label for="table_numero">Numéro de Table</label>
                <input type="text" class="form-control" id="table_numero" required>
            </div>
            <div class="form-group">
                <label for="table_capacite">Capacité</label>
                <input type="number" class="form-control" id="table_capacite" min="1" max="20" required>
            </div>
            <div class="form-group">
                <label for="table_zone">Zone</label>
                <select class="form-control" id="table_zone">
                    <option value="salle_principale">Salle Principale</option>
                    <option value="terrasse">Terrasse</option>
                    <option value="vip">Zone VIP</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </div>
        </form>
    `);
    
    $('#tableModal').modal('show');
    
    $('#addTableForm').on('submit', function(e) {
        e.preventDefault();
        // Logique d'ajout de table
        $('#tableModal').modal('hide');
        Swal.fire({
            icon: 'success',
            title: 'Table Ajoutée',
            text: 'La nouvelle table a été ajoutée avec succès.',
            confirmButtonClass: 'btn btn-success'
        });
    });
}

function addReservation() {
    $('#tableModalTitle').html('<i class="fas fa-calendar-plus"></i> Nouvelle Réservation');
    $('#tableModalContent').html(`
        <form id="addReservationForm">
            <div class="form-group">
                <label for="reservation_table">Table</label>
                <select class="form-control" id="reservation_table" required>
                    <option value="">Sélectionner une table...</option>
                    <!-- Options dynamiques -->
                </select>
            </div>
            <div class="form-group">
                <label for="reservation_client">Nom du Client</label>
                <input type="text" class="form-control" id="reservation_client" required>
            </div>
            <div class="form-group">
                <label for="reservation_date">Date</label>
                <input type="date" class="form-control" id="reservation_date" required>
            </div>
            <div class="form-group">
                <label for="reservation_heure">Heure</label>
                <input type="time" class="form-control" id="reservation_heure" required>
            </div>
            <div class="form-group">
                <label for="reservation_personnes">Nombre de Personnes</label>
                <input type="number" class="form-control" id="reservation_personnes" min="1" max="20" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Réserver</button>
            </div>
        </form>
    `);
    
    $('#tableModal').modal('show');
}

function liberateAllTables() {
    Swal.fire({
        title: 'Confirmer la Libération',
        text: 'Êtes-vous sûr de vouloir libérer toutes les tables occupées ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-warning',
        cancelButtonClass: 'btn btn-secondary',
        confirmButtonText: 'Oui, libérer tout',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            // Logique de libération
            Swal.fire({
                icon: 'success',
                title: 'Tables Libérées',
                text: 'Toutes les tables ont été libérées avec succès.',
                confirmButtonClass: 'btn btn-success'
            }).then(() => {
                refreshTables();
            });
        }
    });
}

function generateReport() {
    window.open('/admin/reports/tables', '_blank');
}

function viewFullScreen() {
    if (document.documentElement.requestFullscreen) {
        document.documentElement.requestFullscreen();
    }
}

function printLayout() {
    window.print();
}

function viewTable(tableId) {
    selectTable(tableId);
}

function editTable(tableId) {
    selectTable(tableId);
    // Ouvrir le modal d'édition
}

function addReservationToTable(tableId) {
    addReservation();
    // Pré-sélectionner la table
}

function deleteTable(tableId) {
    Swal.fire({
        title: 'Confirmer la Suppression',
        text: 'Êtes-vous sûr de vouloir supprimer cette table ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-danger',
        cancelButtonClass: 'btn btn-secondary',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            // Logique de suppression
            Swal.fire({
                icon: 'success',
                title: 'Table Supprimée',
                text: 'La table a été supprimée avec succès.',
                confirmButtonClass: 'btn btn-success'
            }).then(() => {
                refreshTables();
            });
        }
    });
}
</script>

<style>
.table-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.table-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.table-card.border-primary {
    border-width: 3px !important;
}

.tables-grid .col-lg-3:nth-child(4n+1) .table-card {
    margin-right: auto;
}

@media print {
    .btn, .card-header {
        display: none !important;
    }
}
</style>
@endsection
