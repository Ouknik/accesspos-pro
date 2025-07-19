@extends('layouts.sb-admin')

@section('title', 'Modifier la table - AccessPos Pro')

@section('content')
<div class="container-fluid">
    
    {{-- Titre et bouton retour --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit text-info"></i>
                Modifier la table
            </h1>
            <p class="mb-0 text-muted">Mise à jour des données de la table : <strong>{{ $table->TAB_LIB }}</strong></p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.tables.show', $table->TAB_REF) }}" class="btn btn-outline-info btn-sm">
                <i class="fas fa-eye"></i>
                Voir les détails
            </a>
            <a href="{{ route('admin.tables.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i>
                Retour à la liste
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            
            {{-- Formulaire de modification de table --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-utensils"></i>
                        Mise à jour des données de la table
                    </h6>
                </div>
                <div class="card-body">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.tables.update', $table->TAB_REF) }}" method="POST" id="editTableForm">
                        @csrf
                        @method('PUT')
                        
                        {{-- Informations actuelles de la table --}}
                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Référence :</strong><br>
                                    <code>{{ $table->TAB_REF }}</code>
                                </div>
                                <div class="col-md-4">
                                    <strong>État actuel :</strong><br>
                                    @php
                                        $statusConfig = [
                                            'LIBRE' => ['class' => 'success', 'text' => 'Libre'],
                                            'OCCUPEE' => ['class' => 'danger', 'text' => 'Occupée'],
                                            'RESERVEE' => ['class' => 'warning', 'text' => 'Réservée'],
                                            'HORS_SERVICE' => ['class' => 'secondary', 'text' => 'Hors service']
                                        ];
                                        $config = $statusConfig[$table->ETT_ETAT] ?? ['class' => 'light', 'text' => 'Non défini'];
                                    @endphp
                                    <span class="badge badge-{{ $config['class'] }}">{{ $config['text'] }}</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Date de création :</strong><br>
                                    <small class="text-muted">Non disponible</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            {{-- Nom de la table --}}
                            <div class="col-md-6 mb-3">
                                <label for="tab_lib" class="form-label">
                                    <i class="fas fa-tag text-primary"></i>
                                    Nom/Numéro de la table <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="tab_lib" name="tab_lib" 
                                       value="{{ old('tab_lib', $table->TAB_LIB) }}" required>
                                <small class="form-text text-muted">Nom distinctif pour identifier facilement la table</small>
                            </div>

                            {{-- Zone --}}
                            <div class="col-md-6 mb-3">
                                <label for="zon_ref" class="form-label">
                                    <i class="fas fa-map-marker-alt text-info"></i>
                                    Zone <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="zon_ref" name="zon_ref" required>
                                    <option value="">Choisir la zone</option>
                                    @foreach($zones as $zone)
                                        <option value="{{ $zone->ZON_REF }}" 
                                                {{ old('zon_ref', $table->ZON_REF) == $zone->ZON_REF ? 'selected' : '' }}>
                                            {{ $zone->ZON_LIB }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Zone à laquelle appartient la table</small>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Nombre de couverts --}}
                            <div class="col-md-6 mb-3">
                                <label for="tab_nbr_couvert" class="form-label">
                                    <i class="fas fa-chair text-warning"></i>
                                    Nombre de couverts <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="tab_nbr_couvert" name="tab_nbr_couvert" 
                                       value="{{ old('tab_nbr_couvert', $table->TAB_NBR_Couvert) }}" required min="1" max="50">
                                <small class="form-text text-muted">Nombre de personnes pouvant être accueillies</small>
                            </div>

                            {{-- Aperçu de la table --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Aperçu de la table</label>
                                <div class="text-center p-3 border rounded" style="background-color: #f8f9fc;">
                                    <div id="table-preview" class="d-inline-block">
                                        <div class="table-visual" style="
                                            width: 80px; 
                                            height: 80px; 
                                            background: #4e73df; 
                                            border-radius: 50%; 
                                            display: flex; 
                                            align-items: center; 
                                            justify-content: center;
                                            color: white;
                                            font-weight: bold;
                                        ">
                                            <span id="preview-seats">{{ $table->TAB_NBR_Couvert }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted" id="preview-name">{{ $table->TAB_LIB }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label for="tab_descript" class="form-label">
                                <i class="fas fa-align-left text-secondary"></i>
                                Description (optionnel)
                            </label>
                            <textarea class="form-control" id="tab_descript" name="tab_descript" rows="3"
                                      placeholder="Description supplémentaire de la table (emplacement, caractéristiques, notes...)">{{ old('tab_descript', $table->TAB_DESCRIPT) }}</textarea>
                            <small class="form-text text-muted">Informations supplémentaires pour la gestion de la table</small>
                        </div>

                        {{-- Avertissement si la table est occupée --}}
                        @if($table->ETT_ETAT === 'OCCUPEE')
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Attention :</strong> Cette table est actuellement occupée. La modification n'affectera pas l'état.
                        </div>
                        @endif

                        {{-- Informations supplémentaires --}}
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note :</strong> La modification des données de la table n'affectera pas son état actuel ou les réservations existantes.
                        </div>

                        {{-- Boutons de sauvegarde et d'annulation --}}
                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg px-4">
                                <i class="fas fa-save"></i>
                                Enregistrer les modifications
                            </button>
                            <a href="{{ route('admin.tables.index') }}" class="btn btn-secondary btn-lg px-4 ml-2">
                                <i class="fas fa-times"></i>
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Actions rapides --}}
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-tools"></i>
                        Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-exchange-alt text-primary"></i> Changer l'état</h6>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-success" 
                                        onclick="changeTableStatus('{{ $table->TAB_REF }}', 'LIBRE')">
                                    Libre
                                </button>
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="changeTableStatus('{{ $table->TAB_REF }}', 'OCCUPEE')">
                                    Occupée
                                </button>
                                <button type="button" class="btn btn-outline-warning" 
                                        onclick="changeTableStatus('{{ $table->TAB_REF }}', 'RESERVEE')">
                                    Réservée
                                </button>
                                <button type="button" class="btn btn-outline-secondary" 
                                        onclick="changeTableStatus('{{ $table->TAB_REF }}', 'HORS_SERVICE')">
                                    Hors service
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-trash text-danger"></i> Supprimer la table</h6>
                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                    onclick="deleteTable('{{ $table->TAB_REF }}', '{{ $table->TAB_LIB }}')">
                                <i class="fas fa-trash"></i>
                                Supprimer cette table
                            </button>
                            <small class="form-text text-muted">Attention : cette action est irréversible</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Mise à jour de l'aperçu lors du changement de données
    $('#tab_lib').on('input', function() {
        const name = $(this).val() || 'Nouvelle table';
        $('#preview-name').text(name);
    });

    $('#tab_nbr_couvert').on('input', function() {
        const seats = $(this).val() || '4';
        $('#preview-seats').text(seats);
    });

    // Vérification du formulaire avant envoi
    $('#editTableForm').on('submit', function(e) {
        const name = $('#tab_lib').val().trim();
        const zone = $('#zon_ref').val();
        const seats = $('#tab_nbr_couvert').val();

        if (!name) {
            e.preventDefault();
            alert('Veuillez saisir le nom de la table');
            $('#tab_lib').focus();
            return false;
        }

        if (!zone) {
            e.preventDefault();
            alert('Veuillez choisir la zone');
            $('#zon_ref').focus();
            return false;
        }

        if (!seats || seats < 1) {
            e.preventDefault();
            alert('Veuillez saisir un nombre valide de couverts');
            $('#tab_nbr_couvert').focus();
            return false;
        }

        // Confirmation de la sauvegarde
        return confirm('Êtes-vous sûr de vouloir enregistrer les modifications ?');
    });
});

// Changer l'état de la table
function changeTableStatus(tabRef, newStatus) {
    if (confirm('Êtes-vous sûr de vouloir changer l\'état de la table ?')) {
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
</script>

<style>
.table-visual {
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.table-visual:hover {
    transform: scale(1.05);
}
</style>
@endsection
