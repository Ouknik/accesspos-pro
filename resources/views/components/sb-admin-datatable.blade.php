{{--
Component DataTable unifié pour AccessPos Pro avec SB Admin 2
Usage:
@include('components.sb-admin-datatable', [
    'tableId' => 'example-table',
    'title' => 'Liste des données',
    'columns' => [...],
    'ajaxUrl' => '/api/data',
    'buttons' => ['excel', 'pdf', 'print']
])
--}}

@php
    $tableId = $tableId ?? 'datatable-' . Str::random(8);
    $title = $title ?? 'Table de données';
    $description = $description ?? '';
    $columns = $columns ?? [];
    $ajaxUrl = $ajaxUrl ?? null;
    $buttons = $buttons ?? ['excel', 'pdf', 'print'];
    $searchable = $searchable ?? true;
    $sortable = $sortable ?? true;
    $responsive = $responsive ?? true;
    $pageLength = $pageLength ?? 25;
    $showLength = $showLength ?? true;
    $showInfo = $showInfo ?? true;
    $showPaging = $showPaging ?? true;
    $customActions = $customActions ?? null;
    $filters = $filters ?? [];
    $cardClass = $cardClass ?? 'shadow mb-4';
    $headerClass = $headerClass ?? 'py-3 d-flex flex-row align-items-center justify-content-between';
@endphp

<div class="card {{ $cardClass }}">
    <div class="card-header {{ $headerClass }}">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table mr-2"></i>{{ $title }}
        </h6>
        
        {{-- Actions personnalisées --}}
        @if($customActions)
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink-{{ $tableId }}"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="dropdownMenuLink-{{ $tableId }}">
                    {!! $customActions !!}
                </div>
            </div>
        @endif
    </div>
    
    @if($description)
        <div class="card-body border-bottom">
            <p class="text-muted mb-0">{{ $description }}</p>
        </div>
    @endif
    
    {{-- Filtres personnalisés --}}
    @if(!empty($filters))
        <div class="card-body border-bottom bg-light">
            <div class="row">
                @foreach($filters as $filter)
                    <div class="col-md-{{ $filter['col'] ?? '3' }}">
                        <div class="form-group mb-2">
                            <label for="{{ $filter['id'] }}" class="form-label text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ $filter['label'] }}
                            </label>
                            @if($filter['type'] === 'select')
                                <select class="form-control form-control-sm" id="{{ $filter['id'] }}" name="{{ $filter['name'] }}">
                                    <option value="">{{ $filter['placeholder'] ?? 'Tous' }}</option>
                                    @foreach($filter['options'] as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            @elseif($filter['type'] === 'date')
                                <input type="date" class="form-control form-control-sm" id="{{ $filter['id'] }}" name="{{ $filter['name'] }}" placeholder="{{ $filter['placeholder'] ?? '' }}">
                            @else
                                <input type="{{ $filter['type'] ?? 'text' }}" class="form-control form-control-sm" id="{{ $filter['id'] }}" name="{{ $filter['name'] }}" placeholder="{{ $filter['placeholder'] ?? '' }}">
                            @endif
                        </div>
                    </div>
                @endforeach
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-primary btn-sm" onclick="applyFilters{{ Str::studly($tableId) }}()">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm ml-2" onclick="clearFilters{{ Str::studly($tableId) }}()">
                        <i class="fas fa-times"></i> Effacer
                    </button>
                </div>
            </div>
        </div>
    @endif
    
    <div class="card-body">
        {{-- Toolbar avec boutons d'export --}}
        @if(!empty($buttons))
            <div class="row mb-3">
                <div class="col-12">
                    <div class="btn-toolbar justify-content-between" role="toolbar">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Export buttons">
                            @foreach($buttons as $button)
                                @if($button === 'excel')
                                    <button type="button" class="btn btn-success" onclick="exportTable{{ Str::studly($tableId) }}('excel')">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </button>
                                @elseif($button === 'pdf')
                                    <button type="button" class="btn btn-danger" onclick="exportTable{{ Str::studly($tableId) }}('pdf')">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                @elseif($button === 'print')
                                    <button type="button" class="btn btn-info" onclick="exportTable{{ Str::studly($tableId) }}('print')">
                                        <i class="fas fa-print"></i> Imprimer
                                    </button>
                                @elseif($button === 'csv')
                                    <button type="button" class="btn btn-secondary" onclick="exportTable{{ Str::studly($tableId) }}('csv')">
                                        <i class="fas fa-file-csv"></i> CSV
                                    </button>
                                @endif
                            @endforeach
                        </div>
                        
                        {{-- Recherche rapide --}}
                        @if($searchable)
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" class="form-control" placeholder="Recherche rapide..." id="quick-search-{{ $tableId }}">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
        
        {{-- Table principale --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="{{ $tableId }}" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        @foreach($columns as $column)
                            <th>{{ $column['title'] ?? $column['data'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    {{-- Données chargées via AJAX ou slot --}}
                    {{ $slot ?? '' }}
                </tbody>
            </table>
        </div>
        
        {{-- Informations et pagination --}}
        <div class="row mt-3">
            <div class="col-sm-12 col-md-5">
                <div class="dataTables_info" id="{{ $tableId }}_info" role="status" aria-live="polite"></div>
            </div>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers" id="{{ $tableId }}_paginate"></div>
            </div>
        </div>
    </div>
</div>

{{-- Styles CSS personnalisés --}}
<style>
#{{ $tableId }} {
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 0.5rem;
    overflow: hidden;
}

#{{ $tableId }} thead th {
    background: linear-gradient(135deg, #4e73df, #2e59d9);
    color: white;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
    padding: 1rem 0.75rem;
}

#{{ $tableId }} tbody tr {
    transition: all 0.15s ease-in-out;
}

#{{ $tableId }} tbody tr:hover {
    background-color: rgba(78, 115, 223, 0.05);
    transform: scale(1.01);
}

#{{ $tableId }} tbody td {
    padding: 0.75rem;
    vertical-align: middle;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
}

/* Style pour les boutons d'action dans les cellules */
#{{ $tableId }} .btn-action {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    border-radius: 0.25rem;
    margin-right: 0.25rem;
}

/* Pagination personnalisée */
#{{ $tableId }}_paginate .paginate_button {
    border-radius: 0.35rem;
    margin: 0 2px;
    transition: all 0.15s ease-in-out;
}

#{{ $tableId }}_paginate .paginate_button:hover {
    background: #4e73df;
    border-color: #4e73df;
    color: white !important;
}

#{{ $tableId }}_paginate .paginate_button.current {
    background: #4e73df !important;
    border-color: #4e73df !important;
    color: white !important;
}

/* Loading overlay */
#{{ $tableId }}_wrapper .loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

/* Responsive design */
@media (max-width: 768px) {
    #{{ $tableId }} {
        font-size: 0.875rem;
    }
    
    .btn-toolbar {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn-group {
        width: 100%;
    }
    
    .input-group {
        width: 100% !important;
    }
}
</style>

{{-- JavaScript pour initialisation --}}
<script>
$(document).ready(function() {
    // Configuration de base DataTable
    var tableConfig = {
        @if($ajaxUrl)
        ajax: {
            url: '{{ $ajaxUrl }}',
            type: 'GET',
            data: function(d) {
                // Ajouter les filtres personnalisés
                @foreach($filters as $filter)
                    d.{{ $filter['name'] }} = $('#{{ $filter['id'] }}').val();
                @endforeach
                return d;
            }
        },
        @endif
        
        columns: @json($columns),
        
        // Configuration générale
        responsive: {{ $responsive ? 'true' : 'false' }},
        pageLength: {{ $pageLength }},
        lengthChange: {{ $showLength ? 'true' : 'false' }},
        searching: {{ $searchable ? 'true' : 'false' }},
        ordering: {{ $sortable ? 'true' : 'false' }},
        info: {{ $showInfo ? 'true' : 'false' }},
        paging: {{ $showPaging ? 'true' : 'false' }},
        
        // Langue française
        language: {
            url: '/vendor/datatables/fr-FR.json'
        },
        
        // DOM layout
        dom: 'rt<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        
        // Callbacks
        drawCallback: function(settings) {
            // Réinitialiser tooltips après redraw
            $('[data-toggle="tooltip"]').tooltip();
        },
        
        initComplete: function() {
            console.log('DataTable {{ $tableId }} initialisée avec succès');
        }
    };
    
    // Initialiser la DataTable
    window.dataTable{{ Str::studly($tableId) }} = $('#{{ $tableId }}').DataTable(tableConfig);
    
    @if($searchable)
    // Recherche rapide
    let searchTimeout;
    $('#quick-search-{{ $tableId }}').on('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = this.value;
        searchTimeout = setTimeout(function() {
            window.dataTable{{ Str::studly($tableId) }}.search(searchTerm).draw();
        }, 300);
    });
    @endif
});

{{-- Fonctions d'export --}}
@if(!empty($buttons))
function exportTable{{ Str::studly($tableId) }}(format) {
    const table = window.dataTable{{ Str::studly($tableId) }};
    
    switch(format) {
        case 'excel':
            table.button('.buttons-excel').trigger();
            break;
        case 'pdf':
            table.button('.buttons-pdf').trigger();
            break;
        case 'print':
            table.button('.buttons-print').trigger();
            break;
        case 'csv':
            table.button('.buttons-csv').trigger();
            break;
    }
    
    AccessPos.utils.showToast(`Export ${format.toUpperCase()} en cours...`, 'info');
}
@endif

{{-- Fonctions de filtrage --}}
@if(!empty($filters))
function applyFilters{{ Str::studly($tableId) }}() {
    window.dataTable{{ Str::studly($tableId) }}.ajax.reload();
    AccessPos.utils.showToast('Filtres appliqués', 'success');
}

function clearFilters{{ Str::studly($tableId) }}() {
    @foreach($filters as $filter)
        $('#{{ $filter['id'] }}').val('');
    @endforeach
    $('#quick-search-{{ $tableId }}').val('');
    window.dataTable{{ Str::studly($tableId) }}.search('').ajax.reload();
    AccessPos.utils.showToast('Filtres effacés', 'info');
}
@endif

{{-- Fonction pour recharger les données --}}
function reloadTable{{ Str::studly($tableId) }}() {
    @if($ajaxUrl)
        window.dataTable{{ Str::studly($tableId) }}.ajax.reload(null, false);
    @else
        window.dataTable{{ Str::studly($tableId) }}.draw();
    @endif
}

{{-- Fonction pour obtenir les données sélectionnées --}}
function getSelectedRows{{ Str::studly($tableId) }}() {
    const selectedData = [];
    window.dataTable{{ Str::studly($tableId) }}.$('input[type="checkbox"]:checked').each(function() {
        const row = window.dataTable{{ Str::studly($tableId) }}.row($(this).closest('tr'));
        selectedData.push(row.data());
    });
    return selectedData;
}
</script>
