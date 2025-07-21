@extends('layouts.sb-admin')

@section('title', 'Détails de la table - AccessPos Pro')

@section('content')
<div class="container-fluid">
    
    {{-- Titre et boutons --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-info-circle text-info"></i>
                Détails de la table
            </h1>
            <p class="mb-0 text-muted">Informations complètes de la table : <strong>{{ $table->TAB_LIB }}</strong></p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.tables.edit', $table->TAB_REF) }}" class="btn btn-warning btn-sm">
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
        {{-- Informations principales --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-utensils"></i>
                        Informations de la table
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="font-weight-bold text-gray-800">
                                        <i class="fas fa-hashtag text-primary"></i>
                                        Référence :
                                    </td>
                                    <td><code class="bg-light p-1 rounded">{{ $table->TAB_REF }}</code></td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-gray-800">
                                        <i class="fas fa-tag text-success"></i>
                                        Nom/Numéro :
                                    </td>
                                    <td><strong>{{ $table->TAB_LIB }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-gray-800">
                                        <i class="fas fa-map-marker-alt text-info"></i>
                                        Zone :
                                    </td>
                                    <td>
                                        @if(isset($table->zone) && $table->zone)
                                            <span class="badge badge-info">{{ $table->zone->ZON_LIB }}</span>
                                        @else
                                            <span class="badge badge-secondary">Non définie</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-gray-800">
                                        <i class="fas fa-chair text-warning"></i>
                                        Couverts :
                                    </td>
                                    <td>
                                        <span class="badge badge-warning badge-pill">
                                            {{ $table->TAB_NBR_Couvert }} personne{{ $table->TAB_NBR_Couvert > 1 ? 's' : '' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <div class="table-preview-large mb-3">
                                    <div style="
                                        width: 120px; 
                                        height: 120px; 
                                        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
                                        border-radius: 50%; 
                                        display: flex; 
                                        align-items: center; 
                                        justify-content: center;
                                        color: white;
                                        font-size: 1.5rem;
                                        font-weight: bold;
                                        margin: 0 auto;
                                        box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.1);
                                    ">
                                        {{ $table->TAB_NBR_Couvert }}
                                    </div>
                                </div>
                                <h5 class="text-primary">{{ $table->TAB_LIB }}</h5>
                            </div>
                        </div>
                    </div>
                    
                    @if($table->TAB_DESCRIPT)
                        <div class="mt-4">
                            <h6 class="font-weight-bold text-gray-800">
                                <i class="fas fa-align-left text-secondary"></i>
                                Description :
                            </h6>
                            <div class="p-3 bg-light rounded">
                                {{ $table->TAB_DESCRIPT }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- État et actions --}}
        <div class="col-xl-4 col-lg-5">
            {{-- État actuel --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-info-circle"></i>
                        État actuel
                    </h6>
                </div>
                <div class="card-body text-center">
                    @php
                        $statusConfig = [
                            'LIBRE' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Libre'],
                            'OCCUPEE' => ['class' => 'danger', 'icon' => 'users', 'text' => 'Occupée'],
                            'RESERVEE' => ['class' => 'warning', 'icon' => 'calendar-check', 'text' => 'Réservée'],
                            'HORS_SERVICE' => ['class' => 'secondary', 'icon' => 'tools', 'text' => 'Hors service']
                        ];
                        $config = $statusConfig[$table->ETT_ETAT] ?? ['class' => 'light', 'icon' => 'question', 'text' => 'Non défini'];
                    @endphp
                    
                    <div class="mb-3">
                        <i class="fas fa-{{ $config['icon'] }} fa-3x text-{{ $config['class'] }}"></i>
                    </div>
                    <h4>
                        <span class="badge badge-{{ $config['class'] }} badge-pill px-3 py-2">
                            {{ $config['text'] }}
                        </span>
                    </h4>
                    <p class="text-muted">État actuel de la table</p>
                </div>
            </div>

            {{-- Actions rapides --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tools"></i>
                        Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6><i class="fas fa-exchange-alt text-info"></i> Changer l'état</h6>
                        <div class="btn-group-vertical w-100">
                            <button type="button" class="btn btn-outline-success btn-sm mb-1" 
                                    onclick="changeTableStatus('{{ $table->TAB_REF }}', 'LIBRE')">
                                <i class="fas fa-check-circle"></i> Libre
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm mb-1" 
                                    onclick="changeTableStatus('{{ $table->TAB_REF }}', 'OCCUPEE')">
                                <i class="fas fa-users"></i> Occupée
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm mb-1" 
                                    onclick="changeTableStatus('{{ $table->TAB_REF }}', 'RESERVEE')">
                                <i class="fas fa-calendar-check"></i> Réservée
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" 
                                    onclick="changeTableStatus('{{ $table->TAB_REF }}', 'HORS_SERVICE')">
                                <i class="fas fa-tools"></i> Hors service
                            </button>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <h6><i class="fas fa-edit text-warning"></i> Modifier</h6>
                        <a href="{{ route('admin.tables.edit', $table->TAB_REF) }}" class="btn btn-warning btn-sm w-100">
                            <i class="fas fa-edit"></i> Modifier cette table
                        </a>
                    </div>

                    <hr>

                    <div>
                        <h6><i class="fas fa-trash text-danger"></i> Supprimer</h6>
                        <button type="button" class="btn btn-outline-danger btn-sm w-100" 
                                onclick="deleteTable('{{ $table->TAB_REF }}', '{{ $table->TAB_LIB }}')">
                            <i class="fas fa-trash"></i> Supprimer cette table
                        </button>
                        <small class="form-text text-muted">Attention : cette action est irréversible</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Changer l'état de la table
function changeTableStatus(tabRef, newStatus) {
    const statusNames = {
        'LIBRE': 'libre',
        'OCCUPEE': 'occupée', 
        'RESERVEE': 'réservée',
        'HORS_SERVICE': 'hors service'
    };
    
    if (confirm(`Êtes-vous sûr de vouloir changer l'état de la table en "${statusNames[newStatus]}" ?`)) {
        $.ajax({
            url: '/admin/tables/' + tabRef + '/status',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                if (response.success) {
                    alert('✅ ' + response.message);
                    location.reload();
                } else {
                    alert('❌ ' + (response.error || 'Erreur lors du changement d\'état'));
                }
            },
            error: function(xhr) {
                alert('❌ Erreur de connexion au serveur');
            }
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
</script>
@endpush
