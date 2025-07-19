@extends('layouts.sb-admin')

@section('title', 'Ajouter une nouvelle table - AccessPos Pro')

@section('content')
<div class="container-fluid">
    
    {{-- Titre et bouton retour --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus-circle text-success"></i>
                Ajouter une nouvelle table
            </h1>
            <p class="mb-0 text-muted">Ajouter une nouvelle table à la liste des tables du restaurant</p>
        </div>
        <a href="{{ route('admin.tables.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i>
            Retour à la liste des tables
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            
            {{-- Formulaire d'ajout de table --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-utensils"></i>
                        Données de la nouvelle table
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

                    <form action="{{ route('admin.tables.store') }}" method="POST" id="createTableForm">
                        @csrf
                        
                        <div class="row">
                            {{-- Nom de la table --}}
                            <div class="col-md-6 mb-3">
                                <label for="tab_lib" class="form-label">
                                    <i class="fas fa-tag text-primary"></i>
                                    Nom/Numéro de la table <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="tab_lib" name="tab_lib" 
                                       value="{{ old('tab_lib') }}" required
                                       placeholder="Exemple: Table n°1, Table VIP">
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
                                                {{ old('zon_ref') == $zone->ZON_REF ? 'selected' : '' }}>
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
                                       value="{{ old('tab_nbr_couvert', 4) }}" required min="1" max="50">
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
                                            <span id="preview-seats">4</span>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted" id="preview-name">Nouvelle table</small>
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
                                      placeholder="Description supplémentaire de la table (emplacement, caractéristiques, notes...)">{{ old('tab_descript') }}</textarea>
                            <small class="form-text text-muted">Informations supplémentaires pour la gestion de la table</small>
                        </div>

                        {{-- Informations importantes --}}
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Informations importantes :</strong>
                            <ul class="mb-0 mt-2">
                                <li>La nouvelle table sera en état "libre" par défaut</li>
                                <li>Vous pouvez changer l'état de la table depuis la liste des tables</li>
                                <li>Le nom de la table doit être distinctif et facile à retenir</li>
                            </ul>
                        </div>

                        {{-- Boutons de sauvegarde et d'annulation --}}
                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg px-4">
                                <i class="fas fa-save"></i>
                                Enregistrer la table
                            </button>
                            <a href="{{ route('admin.tables.index') }}" class="btn btn-secondary btn-lg px-4 ml-2">
                                <i class="fas fa-times"></i>
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Conseils rapides --}}
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-lightbulb"></i>
                        Conseils pour une meilleure gestion
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-hashtag text-primary"></i> Nommage des tables</h6>
                            <ul class="text-sm text-muted">
                                <li>Utilisez des numéros consécutifs (Table 1, Table 2)</li>
                                <li>Ou utilisez des noms distinctifs (Table fenêtre, Table royale)</li>
                                <li>Évitez les noms similaires</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-users text-info"></i> Définir le nombre de couverts</h6>
                            <ul class="text-sm text-muted">
                                <li>Soyez précis dans la définition du nombre</li>
                                <li>Pensez à l'espace disponible</li>
                                <li>Le nombre peut être modifié ultérieurement</li>
                            </ul>
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
    $('#createTableForm').on('submit', function(e) {
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
        return confirm('Êtes-vous sûr de vouloir enregistrer les données de la table ?');
    });

    // Amélioration de l'expérience utilisateur
    $('input, select, textarea').on('focus', function() {
        $(this).closest('.form-group, .mb-3').addClass('focused');
    }).on('blur', function() {
        $(this).closest('.form-group, .mb-3').removeClass('focused');
    });
});
</script>

<style>
.focused {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
}

.table-visual {
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.table-visual:hover {
    transform: scale(1.05);
}

.text-sm {
    font-size: 0.875rem;
}
</style>
@endsection
