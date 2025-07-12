@extends('layouts.sb-admin')

@section('title', 'Ajouter un Produit - AccessPos Pro')

@section('page-heading')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle"></i>
            Ajouter un Nouveau Produit
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.tableau-de-bord-moderne') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.articles.index') }}">Produits</a></li>
                <li class="breadcrumb-item active">Ajouter</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i>
            Retour à la Liste
        </a>
    </div>
</div>
@endsection

@section('content')

{{-- Messages de validation --}}
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <h6><i class="fas fa-exclamation-triangle"></i> Erreurs de validation:</h6>
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" id="articleForm">
    @csrf
    
    <div class="row">
        
        {{-- Informations Générales --}}
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i>
                        Informations Générales
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Nom du produit --}}
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">
                                Nom du Produit <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" 
                                   name="nom" 
                                   value="{{ old('nom') }}" 
                                   placeholder="Ex: Pizza Margherita"
                                   required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Nom du produit tel qu'il apparaîtra sur les factures
                            </small>
                        </div>

                        {{-- Code/Référence --}}
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">
                                Code/Référence
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code') }}" 
                                       placeholder="Ex: PIZ-001">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            onclick="generateCode()"
                                            title="Générer automatiquement">
                                        <i class="fas fa-magic"></i>
                                    </button>
                                </div>
                            </div>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Code unique pour identifier le produit (optionnel)
                            </small>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="Description détaillée du produit...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Catégorie --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">
                                Catégorie <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <select class="form-control @error('category_id') is-invalid @enderror" 
                                        id="category_id" 
                                        name="category_id" 
                                        required>
                                    <option value="">Sélectionner une catégorie</option>
                                    @if(isset($categories))
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->nom }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" 
                                            type="button" 
                                            onclick="showAddCategoryModal()"
                                            title="Ajouter une nouvelle catégorie">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Statut --}}
                        <div class="col-md-6 mb-3">
                            <label for="statut" class="form-label">Statut</label>
                            <select class="form-control @error('statut') is-invalid @enderror" 
                                    id="statut" 
                                    name="statut">
                                <option value="actif" {{ old('statut', 'actif') == 'actif' ? 'selected' : '' }}>
                                    Actif
                                </option>
                                <option value="inactif" {{ old('statut') == 'inactif' ? 'selected' : '' }}>
                                    Inactif
                                </option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informations de Prix --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-euro-sign"></i>
                        Informations de Prix
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Prix d'achat --}}
                        <div class="col-md-4 mb-3">
                            <label for="prix_achat" class="form-label">Prix d'Achat (DH)</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('prix_achat') is-invalid @enderror" 
                                       id="prix_achat" 
                                       name="prix_achat" 
                                       value="{{ old('prix_achat') }}" 
                                       step="0.01" 
                                       min="0"
                                       placeholder="0.00">
                                <div class="input-group-append">
                                    <span class="input-group-text">DH</span>
                                </div>
                            </div>
                            @error('prix_achat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Prix de vente --}}
                        <div class="col-md-4 mb-3">
                            <label for="prix" class="form-label">
                                Prix de Vente (DH) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('prix') is-invalid @enderror" 
                                       id="prix" 
                                       name="prix" 
                                       value="{{ old('prix') }}" 
                                       step="0.01" 
                                       min="0"
                                       placeholder="0.00"
                                       required>
                                <div class="input-group-append">
                                    <span class="input-group-text">DH</span>
                                </div>
                            </div>
                            @error('prix')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Marge calculée --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Marge Calculée</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="marge_calculee" 
                                       readonly 
                                       placeholder="0.00">
                                <div class="input-group-append">
                                    <span class="input-group-text">DH</span>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Marge = Prix de vente - Prix d'achat
                            </small>
                        </div>
                    </div>

                    {{-- Alertes de prix --}}
                    <div id="price-alerts" class="mt-3" style="display: none;">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span id="price-alert-text"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Gestion du Stock --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-warehouse"></i>
                        Gestion du Stock
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Stock initial --}}
                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label">
                                Stock Initial <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('stock') is-invalid @enderror" 
                                   id="stock" 
                                   name="stock" 
                                   value="{{ old('stock', 0) }}" 
                                   min="0"
                                   required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Seuil d'alerte --}}
                        <div class="col-md-6 mb-3">
                            <label for="seuil_alerte" class="form-label">Seuil d'Alerte</label>
                            <input type="number" 
                                   class="form-control @error('seuil_alerte') is-invalid @enderror" 
                                   id="seuil_alerte" 
                                   name="seuil_alerte" 
                                   value="{{ old('seuil_alerte', 10) }}" 
                                   min="0">
                            @error('seuil_alerte')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Vous serez alerté quand le stock atteint ce niveau
                            </small>
                        </div>
                    </div>

                    {{-- Options de stock --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="gestion_stock" 
                                       name="gestion_stock" 
                                       value="1" 
                                       {{ old('gestion_stock', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="gestion_stock">
                                    Gérer le stock automatiquement
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Le stock sera décrementé automatiquement lors des ventes
                            </small>
                        </div>
                        <div class="col-md-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="alerte_stock" 
                                       name="alerte_stock" 
                                       value="1" 
                                       {{ old('alerte_stock', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="alerte_stock">
                                    Activer les alertes de stock
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Colonne de droite --}}
        <div class="col-lg-4">
            
            {{-- Image du produit --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-image"></i>
                        Image du Produit
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div id="image-preview" class="border rounded p-4 mb-3" style="min-height: 200px; background: #f8f9fa;">
                            <i class="fas fa-image fa-4x text-gray-300 mb-3"></i>
                            <p class="text-muted">Aucune image sélectionnée</p>
                        </div>
                        
                        <input type="file" 
                               class="form-control-file @error('image') is-invalid @enderror" 
                               id="image" 
                               name="image" 
                               accept="image/*" 
                               onchange="previewImage(this)">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <small class="form-text text-muted">
                            Formats acceptés: JPG, PNG, GIF. Taille max: 2MB
                        </small>
                    </div>
                </div>
            </div>

            {{-- Résumé des informations --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">
                        <i class="fas fa-clipboard-list"></i>
                        Résumé
                    </h6>
                </div>
                <div class="card-body">
                    <div id="summary-content">
                        <p class="text-muted text-center">
                            <i class="fas fa-info-circle"></i>
                            Remplissez les informations pour voir le résumé
                        </p>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-cog"></i>
                        Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Enregistrer le Produit
                        </button>
                        
                        <button type="button" class="btn btn-success" onclick="saveAndContinue()">
                            <i class="fas fa-plus"></i>
                            Enregistrer et Ajouter un Autre
                        </button>
                        
                        <button type="button" class="btn btn-info" onclick="previewProduct()">
                            <i class="fas fa-eye"></i>
                            Aperçu
                        </button>
                        
                        <button type="reset" class="btn btn-warning">
                            <i class="fas fa-undo"></i>
                            Réinitialiser
                        </button>
                        
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Annuler
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Modal d'ajout de catégorie --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus"></i>
                    Ajouter une Nouvelle Catégorie
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addCategoryForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="category_nom">Nom de la Catégorie</label>
                        <input type="text" class="form-control" id="category_nom" name="nom" required>
                    </div>
                    <div class="form-group">
                        <label for="category_description">Description</label>
                        <textarea class="form-control" id="category_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Calcul automatique de la marge
    $('#prix_achat, #prix').on('input', function() {
        calculateMargin();
    });
    
    // Validation en temps réel
    $('#nom').on('input', function() {
        updateSummary();
    });
    
    $('#prix, #stock, #category_id').on('change', function() {
        updateSummary();
    });
    
    // Génération automatique du code
    $('#nom').on('blur', function() {
        if (!$('#code').val()) {
            generateCode();
        }
    });
});

function calculateMargin() {
    var prixAchat = parseFloat($('#prix_achat').val()) || 0;
    var prixVente = parseFloat($('#prix').val()) || 0;
    var marge = prixVente - prixAchat;
    
    $('#marge_calculee').val(marge.toFixed(2));
    
    // Alertes de prix
    var alertDiv = $('#price-alerts');
    var alertText = $('#price-alert-text');
    
    if (prixAchat > 0 && prixVente > 0) {
        if (marge < 0) {
            alertText.text('Attention: La marge est négative! Vous vendez à perte.');
            alertDiv.removeClass('alert-warning').addClass('alert-danger').show();
        } else if (marge < prixAchat * 0.1) {
            alertText.text('Attention: Marge très faible (moins de 10% du prix d\'achat).');
            alertDiv.removeClass('alert-danger').addClass('alert-warning').show();
        } else {
            alertDiv.hide();
        }
    } else {
        alertDiv.hide();
    }
}

function generateCode() {
    var nom = $('#nom').val().trim();
    if (nom) {
        // Génération simple du code basée sur le nom
        var code = nom.toUpperCase()
                      .replace(/[^A-Z0-9]/g, '')
                      .substring(0, 6) + 
                   '-' + 
                   Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        $('#code').val(code);
    }
}

function previewImage(input) {
    var preview = $('#image-preview');
    
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            preview.html('<img src="' + e.target.result + '" class="img-fluid rounded" style="max-height: 200px;">');
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.html('<i class="fas fa-image fa-4x text-gray-300 mb-3"></i><p class="text-muted">Aucune image sélectionnée</p>');
    }
}

function updateSummary() {
    var nom = $('#nom').val();
    var prix = $('#prix').val();
    var stock = $('#stock').val();
    var categoryText = $('#category_id option:selected').text();
    
    var summaryHtml = '';
    
    if (nom || prix || stock) {
        summaryHtml += '<div class="list-group list-group-flush">';
        
        if (nom) {
            summaryHtml += '<div class="list-group-item px-0 py-2"><strong>Nom:</strong> ' + nom + '</div>';
        }
        
        if (categoryText && categoryText !== 'Sélectionner une catégorie') {
            summaryHtml += '<div class="list-group-item px-0 py-2"><strong>Catégorie:</strong> ' + categoryText + '</div>';
        }
        
        if (prix) {
            summaryHtml += '<div class="list-group-item px-0 py-2"><strong>Prix:</strong> ' + prix + ' DH</div>';
        }
        
        if (stock) {
            summaryHtml += '<div class="list-group-item px-0 py-2"><strong>Stock:</strong> ' + stock + ' unités</div>';
        }
        
        summaryHtml += '</div>';
    } else {
        summaryHtml = '<p class="text-muted text-center"><i class="fas fa-info-circle"></i> Remplissez les informations pour voir le résumé</p>';
    }
    
    $('#summary-content').html(summaryHtml);
}

function showAddCategoryModal() {
    $('#addCategoryModal').modal('show');
}

$('#addCategoryForm').on('submit', function(e) {
    e.preventDefault();
    
    // Simulation d'ajout de catégorie
    var nom = $('#category_nom').val();
    
    if (nom) {
        // Ici vous ajouteriez la logique AJAX pour sauvegarder la catégorie
        var newOption = '<option value="new" selected>' + nom + '</option>';
        $('#category_id').append(newOption);
        
        $('#addCategoryModal').modal('hide');
        $('#addCategoryForm')[0].reset();
        
        Swal.fire({
            icon: 'success',
            title: 'Catégorie ajoutée',
            text: 'La nouvelle catégorie a été créée avec succès.',
            confirmButtonClass: 'btn btn-primary'
        });
    }
});

function saveAndContinue() {
    // Ajouter un champ hidden pour indiquer qu'on veut continuer
    $('<input>').attr({
        type: 'hidden',
        name: 'save_and_continue',
        value: '1'
    }).appendTo('#articleForm');
    
    $('#articleForm').submit();
}

function previewProduct() {
    // Modal de prévisualisation
    var nom = $('#nom').val() || 'Nom du produit';
    var prix = $('#prix').val() || '0.00';
    var description = $('#description').val() || 'Aucune description';
    
    Swal.fire({
        title: nom,
        html: '<div class="text-left">' +
              '<p><strong>Prix:</strong> ' + prix + ' DH</p>' +
              '<p><strong>Description:</strong> ' + description + '</p>' +
              '</div>',
        icon: 'info',
        confirmButtonText: 'Fermer',
        confirmButtonClass: 'btn btn-primary'
    });
}

// Validation du formulaire
$('#articleForm').on('submit', function(e) {
    var isValid = true;
    var errors = [];
    
    // Validation du nom
    if (!$('#nom').val().trim()) {
        errors.push('Le nom du produit est requis');
        isValid = false;
    }
    
    // Validation du prix
    if (!$('#prix').val() || parseFloat($('#prix').val()) <= 0) {
        errors.push('Le prix de vente doit être supérieur à 0');
        isValid = false;
    }
    
    // Validation de la catégorie
    if (!$('#category_id').val()) {
        errors.push('Veuillez sélectionner une catégorie');
        isValid = false;
    }
    
    if (!isValid) {
        e.preventDefault();
        
        Swal.fire({
            icon: 'error',
            title: 'Erreurs de validation',
            html: errors.join('<br>'),
            confirmButtonClass: 'btn btn-danger'
        });
    }
});
</script>
@endsection
