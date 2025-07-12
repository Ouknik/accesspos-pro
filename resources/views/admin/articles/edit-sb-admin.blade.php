@extends('layouts.sb-admin')

@section('title', 'Modifier le Produit - AccessPos Pro')

@section('page-heading')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit"></i>
            Modifier le Produit
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.tableau-de-bord-moderne') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.articles.index') }}">Produits</a></li>
                <li class="breadcrumb-item active">Modifier - {{ $article->nom ?? 'N/A' }}</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.articles.show', $article->id) }}" class="btn btn-info btn-sm">
            <i class="fas fa-eye"></i>
            Voir
        </a>
        <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i>
            Retour
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

{{-- Message de succès --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<form action="{{ route('admin.articles.update', $article->id) }}" method="POST" enctype="multipart/form-data" id="articleForm">
    @csrf
    @method('PUT')
    
    <div class="row">
        
        {{-- Informations Générales --}}
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i>
                        Informations Générales
                    </h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-warning" onclick="resetChanges()">
                            <i class="fas fa-undo"></i>
                            Réinitialiser
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="showChangeHistory()">
                            <i class="fas fa-history"></i>
                            Historique
                        </button>
                    </div>
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
                                   value="{{ old('nom', $article->nom) }}" 
                                   data-original="{{ $article->nom }}"
                                   placeholder="Ex: Pizza Margherita"
                                   required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <span class="original-value">Valeur originale: {{ $article->nom }}</span>
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
                                       value="{{ old('code', $article->code) }}" 
                                       data-original="{{ $article->code }}"
                                       placeholder="Ex: PIZ-001">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            onclick="generateCode()"
                                            title="Régénérer le code">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </div>
                            </div>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($article->code)
                                <small class="form-text text-muted">
                                    <span class="original-value">Code original: {{ $article->code }}</span>
                                </small>
                            @endif
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3" 
                                  data-original="{{ $article->description }}"
                                  placeholder="Description détaillée du produit...">{{ old('description', $article->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($article->description)
                            <small class="form-text text-muted">
                                <span class="original-value">Description originale: {{ Str::limit($article->description, 50) }}</span>
                            </small>
                        @endif
                    </div>

                    {{-- Catégorie et Statut --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">
                                Catégorie <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <select class="form-control @error('category_id') is-invalid @enderror" 
                                        id="category_id" 
                                        name="category_id" 
                                        data-original="{{ $article->category_id }}"
                                        required>
                                    <option value="">Sélectionner une catégorie</option>
                                    @if(isset($categories))
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                    {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>
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
                            @if($article->category)
                                <small class="form-text text-muted">
                                    <span class="original-value">Catégorie originale: {{ $article->category->nom }}</span>
                                </small>
                            @endif
                        </div>

                        {{-- Statut --}}
                        <div class="col-md-6 mb-3">
                            <label for="statut" class="form-label">Statut</label>
                            <select class="form-control @error('statut') is-invalid @enderror" 
                                    id="statut" 
                                    name="statut"
                                    data-original="{{ $article->statut }}">
                                <option value="actif" {{ old('statut', $article->statut) == 'actif' ? 'selected' : '' }}>
                                    Actif
                                </option>
                                <option value="inactif" {{ old('statut', $article->statut) == 'inactif' ? 'selected' : '' }}>
                                    Inactif
                                </option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <span class="original-value">Statut original: 
                                    <span class="badge badge-{{ $article->statut == 'actif' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($article->statut) }}
                                    </span>
                                </span>
                            </small>
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
                                       value="{{ old('prix_achat', $article->prix_achat) }}" 
                                       data-original="{{ $article->prix_achat }}"
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
                            @if($article->prix_achat)
                                <small class="form-text text-muted">
                                    <span class="original-value">Prix d'achat original: {{ number_format($article->prix_achat, 2) }} DH</span>
                                </small>
                            @endif
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
                                       value="{{ old('prix', $article->prix) }}" 
                                       data-original="{{ $article->prix }}"
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
                            <small class="form-text text-muted">
                                <span class="original-value">Prix de vente original: {{ number_format($article->prix, 2) }} DH</span>
                            </small>
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
                                Marge originale: {{ number_format(($article->prix ?? 0) - ($article->prix_achat ?? 0), 2) }} DH
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
                        {{-- Stock actuel --}}
                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label">
                                Stock Actuel <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('stock') is-invalid @enderror" 
                                       id="stock" 
                                       name="stock" 
                                       value="{{ old('stock', $article->stock) }}" 
                                       data-original="{{ $article->stock }}"
                                       min="0"
                                       required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-info" 
                                            type="button" 
                                            onclick="showStockAdjustment()"
                                            title="Ajustement de stock">
                                        <i class="fas fa-adjust"></i>
                                    </button>
                                </div>
                            </div>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <span class="original-value">Stock original: {{ $article->stock ?? 0 }} unités</span>
                            </small>
                        </div>

                        {{-- Seuil d'alerte --}}
                        <div class="col-md-6 mb-3">
                            <label for="seuil_alerte" class="form-label">Seuil d'Alerte</label>
                            <input type="number" 
                                   class="form-control @error('seuil_alerte') is-invalid @enderror" 
                                   id="seuil_alerte" 
                                   name="seuil_alerte" 
                                   value="{{ old('seuil_alerte', $article->seuil_alerte) }}" 
                                   data-original="{{ $article->seuil_alerte }}"
                                   min="0">
                            @error('seuil_alerte')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <span class="original-value">Seuil original: {{ $article->seuil_alerte ?? 10 }}</span>
                            </small>
                        </div>
                    </div>

                    {{-- Alertes de stock --}}
                    @php
                        $stockActuel = $article->stock ?? 0;
                        $seuilAlerte = $article->seuil_alerte ?? 10;
                    @endphp
                    
                    @if($stockActuel <= 0)
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Rupture de stock!</strong> Ce produit n'est plus disponible.
                        </div>
                    @elseif($stockActuel <= $seuilAlerte)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Stock faible!</strong> Le stock est en dessous du seuil d'alerte.
                        </div>
                    @endif

                    {{-- Options de stock --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="gestion_stock" 
                                       name="gestion_stock" 
                                       value="1" 
                                       {{ old('gestion_stock', $article->gestion_stock ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="gestion_stock">
                                    Gérer le stock automatiquement
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="alerte_stock" 
                                       name="alerte_stock" 
                                       value="1" 
                                       {{ old('alerte_stock', $article->alerte_stock ?? true) ? 'checked' : '' }}>
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
                            @if($article->image && file_exists(public_path('storage/' . $article->image)))
                                <img src="{{ asset('storage/' . $article->image) }}" 
                                     alt="{{ $article->nom }}" 
                                     class="img-fluid rounded" 
                                     style="max-height: 200px;">
                            @else
                                <i class="fas fa-image fa-4x text-gray-300 mb-3"></i>
                                <p class="text-muted">Image actuelle non disponible</p>
                            @endif
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
                            Laissez vide pour conserver l'image actuelle<br>
                            Formats acceptés: JPG, PNG, GIF. Taille max: 2MB
                        </small>
                        
                        @if($article->image)
                            <div class="mt-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="remove_image" 
                                           name="remove_image" 
                                           value="1">
                                    <label class="custom-control-label" for="remove_image">
                                        Supprimer l'image actuelle
                                    </label>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Informations de modification --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">
                        <i class="fas fa-info"></i>
                        Informations de Modification
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="mb-2">
                            <strong>Créé le:</strong><br>
                            {{ $article->created_at ? $article->created_at->format('d/m/Y à H:i') : 'N/A' }}
                        </div>
                        
                        <div class="mb-2">
                            <strong>Dernière modification:</strong><br>
                            {{ $article->updated_at ? $article->updated_at->format('d/m/Y à H:i') : 'N/A' }}
                        </div>
                        
                        @if(isset($article->user))
                            <div class="mb-2">
                                <strong>Créé par:</strong><br>
                                {{ $article->user->name ?? 'N/A' }}
                            </div>
                        @endif
                        
                        <div>
                            <strong>ID:</strong> #{{ $article->id }}
                        </div>
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
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i>
                            Enregistrer les Modifications
                        </button>
                        
                        <button type="button" class="btn btn-info" onclick="previewChanges()">
                            <i class="fas fa-eye"></i>
                            Aperçu des Changements
                        </button>
                        
                        <button type="button" class="btn btn-warning" onclick="resetForm()">
                            <i class="fas fa-undo"></i>
                            Annuler les Modifications
                        </button>
                        
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle w-100" type="button" data-toggle="dropdown">
                                <i class="fas fa-ellipsis-h"></i>
                                Plus d'Actions
                            </button>
                            <div class="dropdown-menu w-100">
                                <a class="dropdown-item" href="{{ route('admin.articles.show', $article->id) }}">
                                    <i class="fas fa-eye mr-2"></i>
                                    Voir le Produit
                                </a>
                                <a class="dropdown-item" href="#" onclick="duplicateProduct()">
                                    <i class="fas fa-copy mr-2"></i>
                                    Dupliquer
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="#" onclick="confirmDelete()">
                                    <i class="fas fa-trash mr-2"></i>
                                    Supprimer
                                </a>
                            </div>
                        </div>
                        
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-light">
                            <i class="fas fa-times"></i>
                            Annuler et Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Modal d'ajustement de stock --}}
<div class="modal fade" id="stockAdjustmentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-adjust"></i>
                    Ajustement de Stock
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Stock actuel: <strong>{{ $article->stock ?? 0 }}</strong> unités</p>
                
                <div class="form-group">
                    <label for="adjustment_type">Type d'ajustement</label>
                    <select class="form-control" id="adjustment_type">
                        <option value="add">Ajouter au stock</option>
                        <option value="remove">Retirer du stock</option>
                        <option value="set">Définir le stock à</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="adjustment_quantity">Quantité</label>
                    <input type="number" class="form-control" id="adjustment_quantity" min="0">
                </div>
                
                <div class="form-group">
                    <label for="adjustment_reason">Raison (optionnel)</label>
                    <textarea class="form-control" id="adjustment_reason" rows="2" placeholder="Ex: Inventaire, Retour client, etc."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="applyStockAdjustment()">Appliquer</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmation de suppression --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                    Confirmer la Suppression
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le produit <strong>{{ $article->nom }}</strong> ?</p>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Cette action est irréversible !
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form method="POST" action="{{ route('admin.articles.destroy', $article->id) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer Définitivement</button>
                </form>
            </div>
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
    
    // Détection des changements
    $('input, select, textarea').on('change input', function() {
        detectChanges($(this));
    });
    
    // Calcul initial de la marge
    calculateMargin();
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

function detectChanges(element) {
    var originalValue = element.data('original');
    var currentValue = element.val();
    
    if (String(currentValue) !== String(originalValue)) {
        element.addClass('changed');
        element.closest('.form-group, .mb-3').addClass('change-indicator modified');
    } else {
        element.removeClass('changed');
        element.closest('.form-group, .mb-3').removeClass('change-indicator modified');
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
        // Remettre l'image originale
        @if($article->image)
            preview.html('<img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->nom }}" class="img-fluid rounded" style="max-height: 200px;">');
        @else
            preview.html('<i class="fas fa-image fa-4x text-gray-300 mb-3"></i><p class="text-muted">Aucune image</p>');
        @endif
    }
}

function generateCode() {
    var nom = $('#nom').val().trim();
    if (nom) {
        var code = nom.toUpperCase()
                      .replace(/[^A-Z0-9]/g, '')
                      .substring(0, 6) + 
                   '-' + 
                   Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        $('#code').val(code);
        detectChanges($('#code'));
    }
}

function showStockAdjustment() {
    $('#stockAdjustmentModal').modal('show');
}

function applyStockAdjustment() {
    var type = $('#adjustment_type').val();
    var quantity = parseInt($('#adjustment_quantity').val()) || 0;
    var currentStock = parseInt($('#stock').val()) || 0;
    var newStock = currentStock;
    
    switch(type) {
        case 'add':
            newStock = currentStock + quantity;
            break;
        case 'remove':
            newStock = Math.max(0, currentStock - quantity);
            break;
        case 'set':
            newStock = quantity;
            break;
    }
    
    $('#stock').val(newStock).trigger('change');
    $('#stockAdjustmentModal').modal('hide');
    
    // Reset form
    $('#adjustment_quantity').val('');
    $('#adjustment_reason').val('');
}

function previewChanges() {
    var changes = [];
    
    $('.changed').each(function() {
        var field = $(this).closest('.form-group, .mb-3').find('label').text().replace('*', '').trim();
        var original = $(this).data('original');
        var current = $(this).val();
        
        changes.push({
            field: field,
            original: original,
            current: current
        });
    });
    
    if (changes.length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Aucune modification',
            text: 'Aucune modification n\'a été détectée.',
            confirmButtonClass: 'btn btn-primary'
        });
        return;
    }
    
    var changesHtml = '<div class="text-left">';
    changes.forEach(function(change) {
        changesHtml += '<div class="mb-2">';
        changesHtml += '<strong>' + change.field + ':</strong><br>';
        changesHtml += '<small class="text-muted">Avant: ' + (change.original || 'Vide') + '</small><br>';
        changesHtml += '<span class="text-success">Après: ' + (change.current || 'Vide') + '</span>';
        changesHtml += '</div>';
    });
    changesHtml += '</div>';
    
    Swal.fire({
        title: 'Aperçu des Modifications',
        html: changesHtml,
        icon: 'info',
        confirmButtonText: 'Fermer',
        confirmButtonClass: 'btn btn-primary'
    });
}

function resetForm() {
    if ($('.changed').length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Aucune modification',
            text: 'Le formulaire n\'a pas été modifié.',
            confirmButtonClass: 'btn btn-primary'
        });
        return;
    }
    
    Swal.fire({
        title: 'Confirmer la réinitialisation',
        text: 'Êtes-vous sûr de vouloir annuler toutes les modifications ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-warning',
        cancelButtonClass: 'btn btn-secondary',
        confirmButtonText: 'Oui, réinitialiser',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            // Reset all fields to original values
            $('input, select, textarea').each(function() {
                var original = $(this).data('original');
                if (original !== undefined) {
                    $(this).val(original);
                }
            });
            
            // Remove change indicators
            $('.changed').removeClass('changed');
            $('.change-indicator').removeClass('modified');
            
            // Recalculate margin
            calculateMargin();
            
            Swal.fire({
                icon: 'success',
                title: 'Réinitialisé',
                text: 'Le formulaire a été réinitialisé.',
                confirmButtonClass: 'btn btn-success'
            });
        }
    });
}

function confirmDelete() {
    $('#deleteModal').modal('show');
}

function duplicateProduct() {
    window.location.href = '{{ route("admin.articles.create") }}?duplicate={{ $article->id }}';
}

function resetChanges() {
    resetForm();
}

function showChangeHistory() {
    // Afficher l'historique des modifications (à implémenter selon vos besoins)
    Swal.fire({
        title: 'Historique des Modifications',
        text: 'Fonctionnalité à implémenter selon vos besoins.',
        icon: 'info',
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
