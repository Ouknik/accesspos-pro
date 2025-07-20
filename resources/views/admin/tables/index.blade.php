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
        @php
            // تأكد من وجود البيانات أو استخدم قيم افتراضية
            $stats = [
                'total' => isset($statistiques['total']) ? $statistiques['total'] : (isset($tables) ? $tables->count() : 0),
                'libres' => isset($statistiques['libres']) ? $statistiques['libres'] : (isset($tables) ? $tables->where('ETT_ETAT', 'LIBRE')->count() : 0),
                'occupees' => isset($statistiques['occupees']) ? $statistiques['occupees'] : (isset($tables) ? $tables->where('ETT_ETAT', 'OCCUPEE')->count() : 0),
                'reservees' => isset($statistiques['reservees']) ? $statistiques['reservees'] : (isset($tables) ? $tables->where('ETT_ETAT', 'RESERVEE')->count() : 0),
                'hors_service' => isset($statistiques['hors_service']) ? $statistiques['hors_service'] : (isset($tables) ? $tables->where('ETT_ETAT', 'HORS_SERVICE')->count() : 0),
                'total_couverts' => isset($statistiques['total_couverts']) ? $statistiques['total_couverts'] : (isset($tables) ? $tables->sum('TAB_NBR_Couvert') : 0)
            ];
        @endphp
        
        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card stat-card border-left-primary shadow h-100 py-2 fade-in" style="animation-delay: 0.1s;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Tables
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-total">
                                {{ $stats['total'] }}
                            </div>
                            <div class="text-xs text-muted">Tables disponibles</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-table fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card stat-card border-left-success shadow h-100 py-2 fade-in" style="animation-delay: 0.2s;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tables Libres
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-libres">
                                {{ $stats['libres'] }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ $stats['total'] > 0 ? round(($stats['libres'] / $stats['total']) * 100, 1) : 0 }}% du total
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
            <div class="card stat-card border-left-danger shadow h-100 py-2 fade-in" style="animation-delay: 0.3s;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Tables Occupées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-occupees">
                                {{ $stats['occupees'] }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ $stats['total'] > 0 ? round(($stats['occupees'] / $stats['total']) * 100, 1) : 0 }}% du total
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
            <div class="card stat-card border-left-warning shadow h-100 py-2 fade-in" style="animation-delay: 0.4s;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Tables Réservées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-reservees">
                                {{ $stats['reservees'] }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ $stats['total'] > 0 ? round(($stats['reservees'] / $stats['total']) * 100, 1) : 0 }}% du total
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
            <div class="card stat-card border-left-secondary shadow h-100 py-2 fade-in" style="animation-delay: 0.5s;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Hors Service
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-hors-service">
                                {{ $stats['hors_service'] }}
                            </div>
                            <div class="text-xs text-muted">Tables indisponibles</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card stat-card border-left-info shadow h-100 py-2 fade-in" style="animation-delay: 0.6s;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Couverts
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-couverts">
                                {{ $stats['total_couverts'] }}
                            </div>
                            <div class="text-xs text-muted">Capacité totale</div>
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
    <div class="card shadow mb-4 fade-in" style="animation-delay: 0.7s;">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-2"></i>Filtres et Recherche
            </h6>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
                <i class="fas fa-times mr-1"></i>
                Réinitialiser
            </button>
        </div>
        <div class="card-body filter-card">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="filter-zone" class="font-weight-bold text-dark">Zone</label>
                    <select class="form-control" id="filter-zone">
                        <option value="">Toutes les zones</option>
                        @if(isset($zones) && $zones->count() > 0)
                            @foreach($zones as $zone)
                                <option value="{{ $zone->ZON_REF }}">{{ $zone->ZON_LIB ?? 'Zone sans nom' }}</option>
                            @endforeach
                        @else
                            <option value="" disabled>Aucune zone disponible</option>
                        @endif
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="filter-status" class="font-weight-bold text-dark">État</label>
                    <select class="form-control" id="filter-status">
                        <option value="">Tous les états</option>
                        <option value="LIBRE">🟢 Libre</option>
                        <option value="OCCUPEE">🔴 Occupée</option>
                        <option value="RESERVEE">🟡 Réservée</option>
                        <option value="HORS_SERVICE">⚫ Hors service</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="filter-search" class="font-weight-bold text-dark">Recherche</label>
                    <input type="text" class="form-control" id="filter-search" 
                           placeholder="Nom de table, zone ou description...">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="filter-couverts" class="font-weight-bold text-dark">Couverts</label>
                    <select class="form-control" id="filter-couverts">
                        <option value="">Tous</option>
                        <option value="1-4">1-4 couverts</option>
                        <option value="5-8">5-8 couverts</option>
                        <option value="9+">9+ couverts</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau des tables --}}
    <div class="card shadow mb-4 fade-in" style="animation-delay: 0.8s;">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-2"></i>Liste des Tables
                @if(isset($tables))
                    <span class="badge badge-primary ml-2">{{ $tables->count() }} table(s)</span>
                @endif
            </h6>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportTables()">
                    <i class="fas fa-download mr-1"></i>
                    Exporter
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(isset($tables) && $tables->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tablesTable" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center">
                                    <i class="fas fa-table mr-1"></i>Table
                                </th>
                                <th class="text-center">
                                    <i class="fas fa-map-marker-alt mr-1"></i>Zone
                                </th>
                                <th class="text-center">
                                    <i class="fas fa-chair mr-1"></i>Couverts
                                </th>
                                <th class="text-center">
                                    <i class="fas fa-info-circle mr-1"></i>État
                                </th>
                                <th class="text-center">
                                    <i class="fas fa-align-left mr-1"></i>Description
                                </th>
                                <th class="text-center">
                                    <i class="fas fa-cogs mr-1"></i>Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tables-tbody">
                            @foreach($tables as $table)
                            <tr data-zone="{{ $table->ZON_REF ?? '' }}" 
                                data-status="{{ $table->ETT_ETAT ?? 'UNKNOWN' }}" 
                                data-couverts="{{ $table->TAB_NBR_Couvert ?? 0 }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <div class="icon-circle bg-primary">
                                                <i class="fas fa-utensils text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <strong class="text-dark">{{ $table->TAB_LIB ?? 'Sans nom' }}</strong><br>
                                            <small class="text-muted">
                                                <i class="fas fa-hashtag mr-1"></i>{{ $table->TAB_REF ?? 'REF_INCONNUE' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if(isset($table->ZON_LIB) && !empty($table->ZON_LIB))
                                        <span class="badge badge-info">
                                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $table->ZON_LIB }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-question mr-1"></i>Non définie
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $couverts = $table->TAB_NBR_Couvert ?? 0;
                                        $badgeClass = $couverts <= 4 ? 'success' : ($couverts <= 8 ? 'warning' : 'danger');
                                    @endphp
                                    <span class="badge badge-{{ $badgeClass }} badge-pill">
                                        <i class="fas fa-chair mr-1"></i>{{ $couverts }} couvert{{ $couverts > 1 ? 's' : '' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $status = $table->ETT_ETAT ?? 'UNKNOWN';
                                        $statusConfig = [
                                            'LIBRE' => ['class' => 'table-status-libre', 'icon' => 'check-circle', 'text' => 'Libre'],
                                            'OCCUPEE' => ['class' => 'table-status-occupee', 'icon' => 'users', 'text' => 'Occupée'],
                                            'RESERVEE' => ['class' => 'table-status-reservee', 'icon' => 'calendar-check', 'text' => 'Réservée'],
                                            'HORS_SERVICE' => ['class' => 'table-status-hors-service', 'icon' => 'tools', 'text' => 'Hors service']
                                        ];
                                        $config = $statusConfig[$status] ?? ['class' => 'badge-secondary', 'icon' => 'question', 'text' => 'Non défini'];
                                    @endphp
                                    <span class="badge {{ $config['class'] }} badge-pill px-3 py-2">
                                        <i class="fas fa-{{ $config['icon'] }} mr-1"></i>
                                        {{ $config['text'] }}
                                    </span>
                                </td>
                                <td>
                                    @if(isset($table->TAB_DESCRIPT) && !empty(trim($table->TAB_DESCRIPT)))
                                        <span class="text-dark" title="{{ $table->TAB_DESCRIPT }}">
                                            {{ Str::limit($table->TAB_DESCRIPT, 50) }}
                                        </span>
                                    @else
                                        <span class="text-muted font-italic">
                                            <i class="fas fa-minus mr-1"></i>Aucune description
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-info btn-action" 
                                                onclick="showTableDetails('{{ $table->TAB_REF }}')" 
                                                title="Voir détails" data-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('admin.tables.edit', $table->TAB_REF) }}" 
                                           class="btn btn-outline-warning btn-action" 
                                           title="Modifier" data-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle btn-action" 
                                                    data-toggle="dropdown" title="Changer état" data-toggle="tooltip">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <button class="dropdown-item" 
                                                        onclick="changeTableStatus('{{ $table->TAB_REF }}', 'LIBRE')">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>Libre
                                                </button>
                                                <button class="dropdown-item" 
                                                        onclick="changeTableStatus('{{ $table->TAB_REF }}', 'OCCUPEE')">
                                                    <i class="fas fa-users text-danger mr-2"></i>Occupée
                                                </button>
                                                <button class="dropdown-item" 
                                                        onclick="changeTableStatus('{{ $table->TAB_REF }}', 'RESERVEE')">
                                                    <i class="fas fa-calendar-check text-warning mr-2"></i>Réservée
                                                </button>
                                                <button class="dropdown-item" 
                                                        onclick="changeTableStatus('{{ $table->TAB_REF }}', 'HORS_SERVICE')">
                                                    <i class="fas fa-tools text-secondary mr-2"></i>Hors service
                                                </button>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-danger btn-action" 
                                                onclick="deleteTable('{{ $table->TAB_REF }}', '{{ $table->TAB_LIB ?? 'Table sans nom' }}')" 
                                                title="Supprimer" data-toggle="tooltip">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                {{-- Message si aucune table --}}
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-utensils fa-4x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Aucune table trouvée</h5>
                    <p class="text-muted mb-4">Commencez par ajouter votre première table au restaurant.</p>
                    <a href="{{ route('admin.tables.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i>Ajouter une Table
                    </a>
                </div>
            @endif
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

@push('styles')
<style>
    .card {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border: 1px solid rgba(226, 230, 234, 0.6);
    }
    
    .card-header {
        background: linear-gradient(135deg, #f8f9fc 0%, #e2e6ea 100%);
        border-bottom: 1px solid #e3e6f0;
        font-weight: 600;
        color: #5a5c69;
    }
    
    .stat-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
    }
    
    .table-status-libre {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        color: white;
    }
    
    .table-status-occupee {
        background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);
        color: white;
    }
    
    .table-status-reservee {
        background: linear-gradient(135deg, #f6c23e 0%, #e67e22 100%);
        color: white;
    }
    
    .table-status-hors-service {
        background: linear-gradient(135deg, #858796 0%, #5a5c69 100%);
        color: white;
    }
    
    .fade-in {
        animation: fadeIn 0.6s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .filter-card {
        background: linear-gradient(135deg, #f8f9fc 0%, #e2e6ea 100%);
    }
    
    .btn-action {
        transition: all 0.3s ease;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
    }

    /* تحسينات CSS إضافية */
    .fade-in {
        animation: fadeInUp 0.6s ease-out forwards;
        opacity: 0;
        transform: translateY(20px);
    }

    .table-row-animation {
        animation: slideInLeft 0.4s ease-out forwards;
    }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .updating {
        position: relative;
        overflow: hidden;
    }

    .updating::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        animation: shimmer 1s ease-in-out;
    }

    @keyframes shimmer {
        to {
            left: 100%;
        }
    }

    .btn-pulse {
        animation: pulse 0.6s ease-in-out;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .icon-circle {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-action {
        transition: all 0.2s ease;
    }

    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Custom status badges */
    .table-status-libre {
        background: linear-gradient(45deg, #28a745, #34ce57);
        color: white;
        animation: pulse-success 2s infinite;
    }

    .table-status-occupee {
        background: linear-gradient(45deg, #dc3545, #e85d75);
        color: white;
        animation: pulse-danger 2s infinite;
    }

    .table-status-reservee {
        background: linear-gradient(45deg, #ffc107, #ffcd39);
        color: #212529;
        animation: pulse-warning 2s infinite;
    }

    .table-status-hors-service {
        background: linear-gradient(45deg, #6c757d, #7c8591);
        color: white;
    }

    @keyframes pulse-success {
        0%, 100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
    }

    @keyframes pulse-danger {
        0%, 100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
    }

    @keyframes pulse-warning {
        0%, 100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
    }

    /* DataTables customization */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }

    .dataTables_wrapper .dataTables_info {
        padding-top: 1rem;
    }

    .dt-buttons {
        margin-bottom: 1rem;
    }

    .dt-button {
        margin-right: 0.5rem !important;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // تهيئة DataTable مع خيارات متقدمة
    let table = $('#tablesTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Tout"]],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: 'Liste des Tables - {{ date("Y-m-d") }}'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Liste des Tables - {{ date("Y-m-d") }}'
            }
        ],
        order: [[0, 'asc']],
        columnDefs: [
            { 
                targets: [-1], 
                orderable: false,
                searchable: false 
            }
        ],
        drawCallback: function() {
            // Réinitialiser les tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
            // Animation des lignes
            $('#tablesTable tbody tr').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
                $(this).addClass('table-row-animation');
            });
        }
    });

    // تهيئة Tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // تطبيق الفلاتر
    $('#filter-zone').on('change', function() {
        let zone = $(this).val();
        if (zone === '') {
            table.column(1).search('').draw();
        } else {
            table.column(1).search(zone).draw();
        }
        animateFilterChange();
    });

    $('#filter-status').on('change', function() {
        let status = $(this).val();
        if (status === '') {
            table.column(3).search('').draw();
        } else {
            table.column(3).search(status).draw();
        }
        animateFilterChange();
    });

    $('#filter-search').on('keyup', function() {
        table.search($(this).val()).draw();
    });

    // Clear filters
    window.clearFilters = function() {
        $('#filter-zone, #filter-status, #filter-couverts').val('');
        $('#filter-search').val('');
        table.search('').columns().search('').draw();
    };
});

// Animation for filter changes
function animateFilterChange() {
    $('.table-responsive').addClass('updating');
    setTimeout(() => {
        $('.table-responsive').removeClass('updating');
    }, 300);
}

// Actualiser les données
function refreshTables() {
    location.reload();
}

// تفاصيل الطاولة
function showTableDetails(tableRef) {
    $('#tableDetailsModal').modal('show');
    $('#tableDetailsContent').html(`
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <p class="mt-2">Chargement des détails...</p>
        </div>
    `);

    $.get('{{ url("admin/tables") }}/' + tableRef)
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

// تغيير حالة الطاولة
function changeTableStatus(tableRef, newStatus) {
    const statusNames = {
        'LIBRE': 'Libre',
        'OCCUPEE': 'Occupée',
        'RESERVEE': 'Réservée',
        'HORS_SERVICE': 'Hors service'
    };

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Confirmer le changement',
            text: `Changer l'état de la table en "${statusNames[newStatus]}" ?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Oui, changer',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                updateTableStatus(tableRef, newStatus);
            }
        });
    } else {
        if (confirm(`Changer l'état de la table en "${statusNames[newStatus]}" ?`)) {
            updateTableStatus(tableRef, newStatus);
        }
    }
}

function updateTableStatus(tableRef, newStatus) {
    $.ajax({
        url: `/admin/tables/${tableRef}/status`,
        method: 'PATCH',
        data: {
            status: newStatus,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            showToast('success', 'État de la table mis à jour.');
            setTimeout(() => {
                location.reload();
            }, 1500);
        },
        error: function(xhr) {
            showToast('error', 'Erreur lors de la mise à jour de l\'état.');
        }
    });
}

// حذف الطاولة
function deleteTable(tableRef, tableName) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Confirmer la suppression',
            text: `Supprimer définitivement la table "${tableName}" ?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6'
        }).then((result) => {
            if (result.isConfirmed) {
                performDeleteTable(tableRef);
            }
        });
    } else {
        if (confirm(`Supprimer définitivement la table "${tableName}" ?`)) {
            performDeleteTable(tableRef);
        }
    }
}

function performDeleteTable(tableRef) {
    $.ajax({
        url: `/admin/tables/${tableRef}`,
        method: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            showToast('success', 'Table supprimée avec succès.');
            setTimeout(() => {
                location.reload();
            }, 1500);
        },
        error: function(xhr) {
            showToast('error', 'Erreur lors de la suppression.');
        }
    });
}

// تصدير البيانات
function exportTables() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Choisir le format d\'export',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Excel',
            denyButtonText: 'PDF',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                $('.buttons-excel').click();
            } else if (result.isDenied) {
                $('.buttons-pdf').click();
            }
        });
    } else {
        // Fallback sans SweetAlert
        let format = prompt('Choisir le format (excel/pdf):');
        if (format === 'excel') {
            $('.buttons-excel').click();
        } else if (format === 'pdf') {
            $('.buttons-pdf').click();
        }
    }
}

// Afficher les messages d'alerte
function showToast(type, message) {
    if (typeof Swal !== 'undefined') {
        if (type === 'success') {
            Swal.fire({
                title: 'Succès!',
                text: message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                title: 'Erreur!',
                text: message,
                icon: 'error'
            });
        }
    } else {
        // Fallback sans SweetAlert
        if (type === 'success') {
            alert('✅ ' + message);
        } else {
            alert('❌ ' + message);
        }
    }
}
</script>
@endpush
