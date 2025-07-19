@extends('layouts.sb-admin')

@section('title', 'Détails de la table - AccessPos Pro')

@section('content')
<div class="container-fluid">
    
    {{-- Titre et boutons --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-utensils text-primary"></i>
                Détails de la table : {{ $table->TAB_LIB }}
            </h1>
            <p class="mb-0 text-muted">Informations détaillées et gestion de la table</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.tables.edit', $table->TAB_REF) }}" class="btn btn-info btn-sm">
                <i class="fas fa-edit"></i>
                Modifier
            </a>
            <a href="{{ route('admin.tables.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i>
                Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Informations de base --}}
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i>
                        Informations de base
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Aperçu de la table --}}
                    <div class="text-center mb-4">
                        <div class="table-visual-large mx-auto" style="
                            width: 120px; 
                            height: 120px; 
                            @php
                                $statusColors = [
                                    'LIBRE' => '#28a745',
                                    'OCCUPEE' => '#dc3545',
                                    'RESERVEE' => '#ffc107',
                                    'HORS_SERVICE' => '#6c757d'
                                ];
                                $bgColor = $statusColors[$table->ETT_ETAT] ?? '#007bff';
                            @endphp
                            background: {{ $bgColor }}; 
                            border-radius: 50%; 
                            display: flex; 
                            align-items: center; 
                            justify-content: center;
                            color: white;
                            font-weight: bold;
                            font-size: 1.5rem;
                            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
                        ">
                            {{ $table->TAB_NBR_Couvert }}
                        </div>
                        <h5 class="mt-3 mb-1">{{ $table->TAB_LIB }}</h5>
                        <p class="text-muted">{{ $table->ZON_LIB ?? 'Zone non définie' }}</p>
                    </div>

                    {{-- Détails de la table --}}
                    <table class="table table-borderless">
                        <tr>
                            <td><strong><i class="fas fa-hashtag text-primary"></i> Référence :</strong></td>
                            <td><code>{{ $table->TAB_REF }}</code></td>
                        </tr>
                        <tr>
                            <td><strong><i class="fas fa-map-marker-alt text-info"></i> Zone :</strong></td>
                            <td>{{ $table->ZON_LIB ?? 'Non définie' }}</td>
                        </tr>
                        <tr>
                            <td><strong><i class="fas fa-chair text-warning"></i> Nombre de couverts :</strong></td>
                            <td>{{ $table->TAB_NBR_Couvert }} couverts</td>
                        </tr>
                        <tr>
                            <td><strong><i class="fas fa-info text-secondary"></i> Description :</strong></td>
                            <td>{{ $table->TAB_DESCRIPT ?: 'Aucune description' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- État et actions --}}
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tachometer-alt"></i>
                        État de la table et actions
                    </h6>
                </div>
                <div class="card-body">
                    
                    {{-- État actuel --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>État actuel</h6>
                            @php
                                $statusConfig = [
                                    'LIBRE' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Libre', 'desc' => 'La table est prête à accueillir des clients'],
                                    'OCCUPEE' => ['class' => 'danger', 'icon' => 'users', 'text' => 'Occupée', 'desc' => 'Il y a actuellement des clients à la table'],
                                    'RESERVEE' => ['class' => 'warning', 'icon' => 'calendar-check', 'text' => 'Réservée', 'desc' => 'La table est réservée à l\'avance'],
                                    'HORS_SERVICE' => ['class' => 'secondary', 'icon' => 'tools', 'text' => 'Hors service', 'desc' => 'La table n\'est pas disponible (maintenance ou nettoyage)']
                                ];
                                $config = $statusConfig[$table->ETT_ETAT] ?? ['class' => 'light', 'icon' => 'question', 'text' => 'Non défini', 'desc' => 'État inconnu'];
                            @endphp
                            <div class="p-3 border rounded bg-light">
                                <div class="d-flex align-items-center">
                                    <div class="status-icon mr-3">
                                        <i class="fas fa-{{ $config['icon'] }} fa-2x text-{{ $config['class'] }}"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1 text-{{ $config['class'] }}">{{ $config['text'] }}</h5>
                                        <small class="text-muted">{{ $config['desc'] }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Dernière mise à jour</h6>
                            <div class="p-3 border rounded bg-light">
                                <p class="mb-1"><i class="fas fa-clock text-muted"></i> Heure : <strong>{{ now()->format('H:i') }}</strong></p>
                                <p class="mb-1"><i class="fas fa-calendar text-muted"></i> Date : <strong>{{ now()->format('d/m/Y') }}</strong></p>
                                <small class="text-muted">Mise à jour automatique toutes les 30 secondes</small>
                            </div>
                        </div>
                    </div>

                    {{-- Changer l'état --}}
                    <div class="mb-4">
                        <h6>Changer l'état de la table</h6>
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <button type="button" class="btn btn-success btn-block" 
                                        onclick="changeTableStatus('{{ $table->TAB_REF }}', 'LIBRE')">
                                    <i class="fas fa-check-circle"></i><br>Libre
                                </button>
                            </div>
                            <div class="col-md-3 mb-2">
                                <button type="button" class="btn btn-danger btn-block" 
                                        onclick="changeTableStatus('{{ $table->TAB_REF }}', 'OCCUPEE')">
                                    <i class="fas fa-users"></i><br>Occupée
                                </button>
                            </div>
                            <div class="col-md-3 mb-2">
                                <button type="button" class="btn btn-warning btn-block" 
                                        onclick="changeTableStatus('{{ $table->TAB_REF }}', 'RESERVEE')">
                                    <i class="fas fa-calendar-check"></i><br>Réservée
                                </button>
                            </div>
                            <div class="col-md-3 mb-2">
                                <button type="button" class="btn btn-secondary btn-block" 
                                        onclick="changeTableStatus('{{ $table->TAB_REF }}', 'HORS_SERVICE')">
                                    <i class="fas fa-tools"></i><br>Hors service
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Actions supplémentaires --}}
                    <div class="border-top pt-3">
                        <h6>Actions supplémentaires</h6>
                        <div class="btn-group">
                            <a href="{{ route('admin.tables.edit', $table->TAB_REF) }}" class="btn btn-outline-info">
                                <i class="fas fa-edit"></i>
                                Modifier les données
                            </a>
                            <button type="button" class="btn btn-outline-primary" onclick="printTable()">
                                <i class="fas fa-print"></i>
                                Imprimer QR Code
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="exportTable()">
                                <i class="fas fa-download"></i>
                                Exporter les données
                            </button>
                            <button type="button" class="btn btn-outline-danger" 
                                    onclick="deleteTable('{{ $table->TAB_REF }}', '{{ $table->TAB_LIB }}')">
                                <i class="fas fa-trash"></i>
                                Supprimer la table
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Journal des activités --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history"></i>
                        Journal des activités de la table
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($activities) && count($activities) > 0)
                        <div class="timeline">
                            @foreach($activities as $activity)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <div class="timeline-title">{{ $activity->title ?? 'Activité' }}</div>
                                    <p class="timeline-text text-muted">{{ $activity->description ?? 'Description de l\'activité' }}</p>
                                    <small class="text-muted">{{ $activity->created_at ?? now() }}</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-info-circle fa-3x mb-3"></i>
                            <p>Aucune activité enregistrée pour cette table pour le moment</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Mise à jour automatique toutes les 30 secondes
setInterval(function() {
    // Peut être développé pour récupérer l'état mis à jour de la table
    console.log('Mise à jour automatique...');
}, 30000);

// Changer l'état de la table
function changeTableStatus(tabRef, newStatus) {
    const statusNames = {
        'LIBRE': 'libre',
        'OCCUPEE': 'occupée', 
        'RESERVEE': 'réservée',
        'HORS_SERVICE': 'hors service'
    };
    
    if (confirm(`Êtes-vous sûr de vouloir changer l'état de la table en "${statusNames[newStatus]}" ?`)) {
        $.post('/admin/tables/' + tabRef + '/status', {
            _token: '{{ csrf_token() }}',
            status: newStatus
        }).done(function(response) {
            if (response.success) {
                alert('✅ ' + response.message);
                location.reload();
            } else {
                alert('❌ ' + (response.error || 'Erreur lors du changement d\'état'));
            }
        }).fail(function() {
            alert('❌ Erreur de connexion au serveur');
        });
    }
}

// Supprimer une table
function deleteTable(tabRef, tableName) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer la table "${tableName}" ?\n\nCette action est irréversible !`)) {
        $.ajax({
            url: '/admin/tables/' + tabRef,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('✅ ' + response.success);
                    window.location.href = '{{ route("admin.tables.index") }}';
                } else {
                    alert('❌ ' + response.error);
                }
            },
            error: function() {
                alert('❌ Erreur lors de la suppression de la table');
            }
        });
    }
}

// Imprimer QR Code
function printTable() {
    alert('La fonctionnalité d\'impression de QR Code sera développée prochainement');
}

// Exporter les données de la table
function exportTable() {
    const tableData = {
        name: '{{ $table->TAB_LIB }}',
        ref: '{{ $table->TAB_REF }}',
        zone: '{{ $table->ZON_LIB }}',
        seats: '{{ $table->TAB_NBR_Couvert }}',
        status: '{{ $table->ETT_ETAT }}',
        description: '{{ $table->TAB_DESCRIPT }}'
    };
    
    const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(tableData, null, 2));
    const downloadAnchor = document.createElement('a');
    downloadAnchor.setAttribute("href", dataStr);
    downloadAnchor.setAttribute("download", "table_{{ $table->TAB_REF }}.json");
    document.body.appendChild(downloadAnchor);
    downloadAnchor.click();
    downloadAnchor.remove();
}
</script>

<style>
.table-visual-large {
    transition: transform 0.3s ease;
}

.table-visual-large:hover {
    transform: scale(1.05);
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -25px;
    top: 17px;
    width: 2px;
    height: calc(100% + 5px);
    background: #e3e6f0;
}

.timeline-title {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 0;
    font-size: 0.875rem;
}

.status-icon {
    width: 60px;
    text-align: center;
}
</style>
@endsection
