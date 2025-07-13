@extends('layouts.sb-admin')

@section('title', 'Modifier le produit - ' . $article->ART_DESIGNATION)

@section('styles')
<style>
    .form-section { 
        background: #f8f9fa; 
        border-radius: 10px; 
        padding: 20px; 
        margin-bottom: 20px; 
        border-left: 4px solid #28a745;
    }
    .section-title { 
        color: #28a745; 
        font-weight: bold; 
        margin-bottom: 15px; 
        display: flex; 
        align-items: center; 
    }
    .section-title i { margin-right: 8px; }
    .required { color: #dc3545; }
    .help-text { font-size: 0.875rem; color: #6c757d; }
    .stock-alert { 
        background: #fff3cd; 
        border: 1px solid #ffeaa7; 
        border-radius: 5px; 
        padding: 10px; 
        margin: 10px 0; 
    }
    .change-indicator {
        position: relative;
    }
    .change-indicator.modified {
        border-left: 3px solid #ffc107;
        background-color: #fff8e1;
    }
    .original-value {
        font-size: 0.875rem;
        color: #6c757d;
        font-style: italic;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Navigation breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.tableau-de-bord-moderne') }}">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.articles.index') }}">Gestion des produits</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.articles.show', $article->ART_REF) }}">{{ $article->ART_DESIGNATION }}</a></li>
            <li class="breadcrumb-item active">Modifier</li>
        </ol>
    </nav>

    <!-- العنوان الرئيسي -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Modifier le produit</h1>
            <p class="text-muted">Modification des informations du produit : <strong>{{ $article->ART_DESIGNATION }}</strong></p>
        </div>
        <div>
            <a href="{{ route('admin.articles.show', $article->ART_REF) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> Voir les détails
            </a>
            <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

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

    <form method="POST" action="{{ route('admin.articles.update', $article->ART_REF) }}" id="productForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-8">
                <!-- المعلومات الأساسية -->
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Informations de base
                    </h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ART_REF" class="form-label">
                                    Code produit <span class="required">*</span>
                                </label>
                                <input type="text" class="form-control @error('ART_REF') is-invalid @enderror" 
                                       id="ART_REF" name="ART_REF" value="{{ old('ART_REF', $article->ART_REF) }}" 
                                       required readonly>
                                <div class="help-text">Le code produit ne peut pas être modifié</div>
                                @error('ART_REF')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 change-indicator" data-original="{{ $article->ART_DESIGNATION }}">
                                <label for="ART_DESIGNATION" class="form-label">
                                    Nom du produit <span class="required">*</span>
                                </label>
                                <input type="text" class="form-control @error('ART_DESIGNATION') is-invalid @enderror" 
                                       id="ART_DESIGNATION" name="ART_DESIGNATION" 
                                       value="{{ old('ART_DESIGNATION', $article->ART_DESIGNATION) }}" required>
                                <div class="original-value">Valeur originale : {{ $article->ART_DESIGNATION }}</div>
                                @error('ART_DESIGNATION')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3 change-indicator" data-original="{{ $article->SFM_REF ?? '' }}">
                                <label for="SFM_REF" class="form-label">Référence Famille</label>
                                <input type="text" class="form-control @error('SFM_REF') is-invalid @enderror" 
                                       id="SFM_REF" name="SFM_REF" 
                                       value="{{ old('SFM_REF', $article->SFM_REF) }}" 
                                       placeholder="Ex: FAM001">
                                <div class="original-value">
                                    Valeur originale : {{ $article->SFM_REF ?? 'Non défini' }}
                                </div>
                                @error('SFM_REF')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 change-indicator" data-original="{{ $article->ART_VENTE }}">
                                <label for="ART_VENTE" class="form-label">
                                    Autorisé à la Vente
                                </label>
                                <select class="form-control @error('ART_VENTE') is-invalid @enderror" 
                                        id="ART_VENTE" name="ART_VENTE">
                                    <option value="1" {{ old('ART_VENTE', $article->ART_VENTE) == '1' ? 'selected' : '' }}>
                                        Oui
                                    </option>
                                    <option value="0" {{ old('ART_VENTE', $article->ART_VENTE) == '0' ? 'selected' : '' }}>
                                        Non
                                    </option>
                                </select>
                                <div class="original-value">
                                    Statut original : {{ $article->ART_VENTE ? 'Autorisé' : 'Non autorisé' }}
                                </div>
                                @error('ART_VENTE')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- معلومات الأسعار -->
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-dollar-sign"></i>
                        Informations de prix
                    </h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3 change-indicator" data-original="{{ $article->ART_PRIX_ACHAT }}">
                                <label for="ART_PRIX_ACHAT" class="form-label">Prix d'achat</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('ART_PRIX_ACHAT') is-invalid @enderror" 
                                           id="ART_PRIX_ACHAT" name="ART_PRIX_ACHAT" 
                                           value="{{ old('ART_PRIX_ACHAT', $article->ART_PRIX_ACHAT) }}"
                                           step="0.01" min="0">
                                    <span class="input-group-text">DH</span>
                                </div>
                                <div class="original-value">
                                    Valeur originale : {{ number_format($article->ART_PRIX_ACHAT ?? 0, 2) }} DH
                                </div>
                                @error('ART_PRIX_ACHAT')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 change-indicator" data-original="{{ $article->ART_PRIX_VENTE }}">
                                <label for="ART_PRIX_VENTE" class="form-label">
                                    Prix de vente <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('ART_PRIX_VENTE') is-invalid @enderror" 
                                           id="ART_PRIX_VENTE" name="ART_PRIX_VENTE" 
                                           value="{{ old('ART_PRIX_VENTE', $article->ART_PRIX_VENTE) }}"
                                           step="0.01" min="0" required>
                                    <span class="input-group-text">DH</span>
                                </div>
                                <div class="original-value">
                                    Valeur originale : {{ number_format($article->ART_PRIX_VENTE, 2) }} DH
                                </div>
                                @error('ART_PRIX_VENTE')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- حساب هامش الربح التلقائي -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info" id="profit-margin">
                                <i class="fas fa-calculator me-2"></i>
                                <span id="margin-text"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- معلومات المخزون -->
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-warehouse"></i>
                        Informations de stock
                    </h4>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="ART_STOCKABLE" 
                                       name="ART_STOCKABLE" value="1" 
                                       {{ old('ART_STOCKABLE', $article->ART_STOCKABLE) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ART_STOCKABLE">
                                    Produit stockable (nécessite un suivi de stock)
                                </label>
                                <div class="original-value">
                                    État original : {{ $article->ART_STOCKABLE ? 'Stockable' : 'Non stockable' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="stock-fields" style="display: {{ old('ART_STOCKABLE', $article->ART_STOCKABLE) ? 'block' : 'none' }};">
                        @if($article->ART_STOCKABLE)
                            <div class="stock-alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Stock actuel :</strong> {{ $article->stock_total ?? 0 }} unités
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 change-indicator" data-original="{{ $article->ART_STOCK_MIN }}">
                                    <label for="ART_STOCK_MIN" class="form-label">Stock minimum</label>
                                    <input type="number" class="form-control @error('ART_STOCK_MIN') is-invalid @enderror" 
                                           id="ART_STOCK_MIN" name="ART_STOCK_MIN" 
                                           value="{{ old('ART_STOCK_MIN', $article->ART_STOCK_MIN) }}"
                                           min="0">
                                    <div class="original-value">
                                        Valeur originale : {{ $article->ART_STOCK_MIN ?? 0 }} unités
                                    </div>
                                    @error('ART_STOCK_MIN')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 change-indicator" data-original="{{ $article->ART_STOCK_MAX }}">
                                    <label for="ART_STOCK_MAX" class="form-label">Stock maximum</label>
                                    <input type="number" class="form-control @error('ART_STOCK_MAX') is-invalid @enderror" 
                                           id="ART_STOCK_MAX" name="ART_STOCK_MAX" 
                                           value="{{ old('ART_STOCK_MAX', $article->ART_STOCK_MAX) }}"
                                           min="0">
                                    <div class="original-value">
                                        Valeur originale : {{ $article->ART_STOCK_MAX ?? 0 }} unités
                                    </div>
                                    @error('ART_STOCK_MAX')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الإعدادات الجانبية -->
            <div class="col-md-4">
                <!-- حالة المنتج -->
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-cog"></i>
                        Paramètres du produit
                    </h4>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="ART_ACHAT" 
                                   name="ART_ACHAT" value="1" 
                                   {{ old('ART_ACHAT', $article->ART_ACHAT) ? 'checked' : '' }}>
                            <label class="form-check-label" for="ART_ACHAT">
                                Autorisé à l'achat
                            </label>
                        </div>
                        <div class="original-value">
                            État original : {{ $article->ART_ACHAT ? 'Autorisé' : 'Non autorisé' }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="IsMenu" 
                                   name="IsMenu" value="1" 
                                   {{ old('IsMenu', $article->IsMenu) ? 'checked' : '' }}>
                            <label class="form-check-label" for="IsMenu">
                                Produit menu (plat ou repas)
                            </label>
                        </div>
                        <div class="help-text">Produits dédiés aux restaurants ou menus</div>
                        <div class="original-value">
                            État original : {{ $article->IsMenu ? 'Produit menu' : 'Produit normal' }}
                        </div>
                    </div>
                </div>

                <!-- ملاحظات -->
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-sticky-note"></i>
                        Notes supplémentaires
                    </h4>
                    
                    <div class="mb-3 change-indicator" data-original="{{ $article->ART_DESCRIPTION }}">
                        <label for="ART_DESCRIPTION" class="form-label">Description du produit</label>
                        <textarea class="form-control @error('ART_DESCRIPTION') is-invalid @enderror" 
                                  id="ART_DESCRIPTION" name="ART_DESCRIPTION" rows="4" 
                                  placeholder="Description détaillée du produit (facultatif)">{{ old('ART_DESCRIPTION', $article->ART_DESCRIPTION) }}</textarea>
                        <div class="original-value">
                            Description originale : {{ $article->ART_DESCRIPTION ?: 'Pas de description' }}
                        </div>
                        @error('ART_DESCRIPTION')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3 change-indicator" data-original="{{ $article->ART_LIBELLE_TICKET }}">
                        <label for="ART_LIBELLE_TICKET" class="form-label">Texte d'impression</label>
                        <input type="text" class="form-control @error('ART_LIBELLE_TICKET') is-invalid @enderror" 
                               id="ART_LIBELLE_TICKET" name="ART_LIBELLE_TICKET" 
                               value="{{ old('ART_LIBELLE_TICKET', $article->ART_LIBELLE_TICKET) }}" 
                               placeholder="Texte qui apparaîtra sur la facture (facultatif)">
                        <div class="original-value">
                            Texte original : {{ $article->ART_LIBELLE_TICKET ?: 'Pas de texte' }}
                        </div>
                        @error('ART_LIBELLE_TICKET')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- معلومات التعديل -->
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-clock"></i>
                        Informations de modification
                    </h4>
                    
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text">
                                <small class="text-muted">
                                    <strong>Référence:</strong> {{ $article->ART_REF }}
                                </small>
                            </p>
                            <p class="card-text">
                                <small class="text-muted">
                                    <strong>Famille:</strong> {{ $article->SFM_REF ?? 'N/A' }}
                                </small>
                            </p>
                            <p class="card-text">
                                <small class="text-muted">
                                    <strong>PLU:</strong> {{ $article->ART_PLU ?? 'N/A' }}
                                </small>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- معاينة التغييرات -->
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-eye"></i>
                        Aperçu des modifications
                    </h4>
                    
                    <div class="card">
                        <div class="card-body">
                            <div id="changes-summary">
                                <p class="text-muted">Aucune modification effectuée</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- أزرار الحفظ -->
        <div class="row">
            <div class="col-12">
                <div class="form-section">
                    <div class="d-flex justify-content-between">
                        <div>
                            <button type="submit" class="btn btn-success btn-lg" id="saveBtn">
                                <i class="fas fa-save me-2"></i>Enregistrer les modifications
                            </button>
                            <button type="button" class="btn btn-warning btn-lg" onclick="resetForm()">
                                <i class="fas fa-undo me-2"></i>Réinitialiser
                            </button>
                        </div>
                        <div>
                            <a href="{{ route('admin.articles.show', $article->ART_REF) }}" class="btn btn-info btn-lg">
                                <i class="fas fa-eye me-2"></i>Voir les détails
                            </a>
                            <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
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
                            <label for="ART_DESIGNATION" class="form-label">
                                Nom du Produit <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('ART_DESIGNATION') is-invalid @enderror" 
                                   id="ART_DESIGNATION" 
                                   name="ART_DESIGNATION" 
                                   value="{{ old('ART_DESIGNATION', $article->ART_DESIGNATION) }}" 
                                   data-original="{{ $article->ART_DESIGNATION }}"
                                   placeholder="Ex: Pizza Margherita"
                                   required>
                            @error('ART_DESIGNATION')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <span class="original-value">Valeur originale: {{ $article->ART_DESIGNATION }}</span>
                            </small>
                        </div>

                        {{-- Code/Référence --}}
                        <div class="col-md-6 mb-3">
                            <label for="ART_REF" class="form-label">
                                Code/Référence
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control @error('ART_REF') is-invalid @enderror" 
                                       id="ART_REF" 
                                       name="ART_REF" 
                                       value="{{ old('ART_REF', $article->ART_REF) }}" 
                                       data-original="{{ $article->ART_REF }}"
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
                            @error('ART_REF')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($article->ART_REF)
                                <small class="form-text text-muted">
                                    <span class="original-value">Code original: {{ $article->ART_REF }}</span>
                                </small>
                            @endif
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label for="ART_DESCRIPTION" class="form-label">Description</label>
                        <textarea class="form-control @error('ART_DESCRIPTION') is-invalid @enderror" 
                                  id="ART_DESCRIPTION" 
                                  name="ART_DESCRIPTION" 
                                  rows="3" 
                                  data-original="{{ $article->ART_DESCRIPTION }}"
                                  placeholder="Description détaillée du produit...">{{ old('ART_DESCRIPTION', $article->ART_DESCRIPTION) }}</textarea>
                        @error('ART_DESCRIPTION')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($article->ART_DESCRIPTION)
                            <small class="form-text text-muted">
                                <span class="original-value">Description originale: {{ Str::limit($article->ART_DESCRIPTION, 50) }}</span>
                            </small>
                        @endif
                    </div>

                    {{-- Famille et Statut de Vente --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="SFM_REF" class="form-label">
                                Référence Famille
                            </label>
                            <input type="text" 
                                   class="form-control @error('SFM_REF') is-invalid @enderror" 
                                   id="SFM_REF" 
                                   name="SFM_REF" 
                                   value="{{ old('SFM_REF', $article->SFM_REF) }}" 
                                   data-original="{{ $article->SFM_REF }}"
                                   placeholder="Ex: FAM001">
                            @error('SFM_REF')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($article->SFM_REF)
                                <small class="form-text text-muted">
                                    <span class="original-value">Famille originale: {{ $article->SFM_REF }}</span>
                                </small>
                            @endif
                        </div>

                        {{-- Statut Vente --}}
                        <div class="col-md-6 mb-3">
                            <label for="ART_VENTE" class="form-label">Autorisé à la Vente</label>
                            <select class="form-control @error('ART_VENTE') is-invalid @enderror" 
                                    id="ART_VENTE" 
                                    name="ART_VENTE"
                                    data-original="{{ $article->ART_VENTE }}">
                                <option value="1" {{ old('ART_VENTE', $article->ART_VENTE) == '1' ? 'selected' : '' }}>
                                    Oui
                                </option>
                                <option value="0" {{ old('ART_VENTE', $article->ART_VENTE) == '0' ? 'selected' : '' }}>
                                    Non
                                </option>
                            </select>
                            @error('ART_VENTE')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <span class="original-value">Statut original: 
                                    <span class="badge badge-{{ $article->ART_VENTE ? 'success' : 'secondary' }}">
                                        {{ $article->ART_VENTE ? 'Autorisé' : 'Non autorisé' }}
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
                            <label for="ART_PRIX_ACHAT" class="form-label">Prix d'Achat (DH)</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('ART_PRIX_ACHAT') is-invalid @enderror" 
                                       id="ART_PRIX_ACHAT" 
                                       name="ART_PRIX_ACHAT" 
                                       value="{{ old('ART_PRIX_ACHAT', $article->ART_PRIX_ACHAT) }}" 
                                       data-original="{{ $article->ART_PRIX_ACHAT }}"
                                       step="0.01" 
                                       min="0"
                                       placeholder="0.00">
                                <div class="input-group-append">
                                    <span class="input-group-text">DH</span>
                                </div>
                            </div>
                            @error('ART_PRIX_ACHAT')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($article->ART_PRIX_ACHAT)
                                <small class="form-text text-muted">
                                    <span class="original-value">Prix d'achat original: {{ number_format($article->ART_PRIX_ACHAT, 2) }} DH</span>
                                </small>
                            @endif
                        </div>

                        {{-- Prix de vente --}}
                        <div class="col-md-4 mb-3">
                            <label for="ART_PRIX_VENTE" class="form-label">
                                Prix de Vente (DH) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('ART_PRIX_VENTE') is-invalid @enderror" 
                                       id="ART_PRIX_VENTE" 
                                       name="ART_PRIX_VENTE" 
                                       value="{{ old('ART_PRIX_VENTE', $article->ART_PRIX_VENTE) }}" 
                                       data-original="{{ $article->ART_PRIX_VENTE }}"
                                       step="0.01" 
                                       min="0"
                                       placeholder="0.00"
                                       required>
                                <div class="input-group-append">
                                    <span class="input-group-text">DH</span>
                                </div>
                            </div>
                            @error('ART_PRIX_VENTE')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <span class="original-value">Prix de vente original: {{ number_format($article->ART_PRIX_VENTE, 2) }} DH</span>
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
                                Marge originale: {{ number_format(($article->ART_PRIX_VENTE ?? 0) - ($article->ART_PRIX_ACHAT ?? 0), 2) }} DH
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
                        {{-- Stock minimum --}}
                        <div class="col-md-6 mb-3">
                            <label for="ART_STOCK_MIN" class="form-label">
                                Stock Minimum
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('ART_STOCK_MIN') is-invalid @enderror" 
                                       id="ART_STOCK_MIN" 
                                       name="ART_STOCK_MIN" 
                                       value="{{ old('ART_STOCK_MIN', $article->ART_STOCK_MIN) }}" 
                                       data-original="{{ $article->ART_STOCK_MIN }}"
                                       min="0">
                                <div class="input-group-append">
                                    <span class="input-group-text">unités</span>
                                </div>
                            </div>
                            @error('ART_STOCK_MIN')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <span class="original-value">Stock minimum original: {{ $article->ART_STOCK_MIN ?? 0 }} unités</span>
                            </small>
                        </div>

                        {{-- Stock maximum --}}
                        <div class="col-md-6 mb-3">
                            <label for="ART_STOCK_MAX" class="form-label">Stock Maximum</label>
                            <input type="number" 
                                   class="form-control @error('ART_STOCK_MAX') is-invalid @enderror" 
                                   id="ART_STOCK_MAX" 
                                   name="ART_STOCK_MAX" 
                                   value="{{ old('ART_STOCK_MAX', $article->ART_STOCK_MAX) }}" 
                                   data-original="{{ $article->ART_STOCK_MAX }}"
                                   min="0">
                            @error('ART_STOCK_MAX')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <span class="original-value">Seuil original: {{ $article->seuil_alerte ?? 10 }}</span>
                            </small>
                        </div>
                    </div>

                    {{-- Options de stock --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="ART_STOCKABLE" 
                                       name="ART_STOCKABLE" 
                                       value="1" 
                                       {{ old('ART_STOCKABLE', $article->ART_STOCKABLE ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="ART_STOCKABLE">
                                    Article stockable
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="ART_ACHAT" 
                                       name="ART_ACHAT" 
                                       value="1" 
                                       {{ old('ART_ACHAT', $article->ART_ACHAT ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="ART_ACHAT">
                                    Autorisé à l'achat
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
                            <p class="text-muted">Aucune image configurée pour cet article</p>
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
                        
                        @if(false)
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
                            <strong>Référence:</strong><br>
                            {{ $article->ART_REF }}
                        </div>
                        
                        <div class="mb-2">
                            <strong>Famille:</strong><br>
                            {{ $article->SFM_REF ?? 'N/A' }}
                        </div>
                        
                        <div class="mb-2">
                            <strong>Autorisé Vente:</strong><br>
                            <span class="badge badge-{{ $article->ART_VENTE ? 'success' : 'secondary' }}">
                                {{ $article->ART_VENTE ? 'Oui' : 'Non' }}
                            </span>
                        </div>
                        
                        <div>
                            <strong>PLU:</strong> {{ $article->ART_PLU ?? 'N/A' }}
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
                                <a class="dropdown-item" href="{{ route('admin.articles.show', $article->ART_REF) }}">
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
                <p>Stock actuel: <strong>{{ $article->stock_total ?? 0 }}</strong> unités</p>
                
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
                <p>Êtes-vous sûr de vouloir supprimer le produit <strong>{{ $article->ART_DESIGNATION }}</strong> ?</p>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Cette action est irréversible !
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form method="POST" action="{{ route('admin.articles.edit', $article->ART_REF) }}" style="display: inline;">
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
    document.addEventListener('DOMContentLoaded', function() {
        let hasChanges = false;
        
        // عرض/إخفاء حقول المخزون
        const stockableCheckbox = document.getElementById('ART_STOCKABLE');
        const stockFields = document.getElementById('stock-fields');
        
        stockableCheckbox.addEventListener('change', function() {
            stockFields.style.display = this.checked ? 'block' : 'none';
            checkForChanges();
        });

        // حساب هامش الربح التلقائي
        const prixAchatInput = document.getElementById('ART_PRIX_ACHAT');
        const prixVenteInput = document.getElementById('ART_PRIX_VENTE');
        const marginDiv = document.getElementById('profit-margin');
        const marginText = document.getElementById('margin-text');
        
        function calculateMargin() {
            const prixAchat = parseFloat(prixAchatInput.value) || 0;
            const prixVente = parseFloat(prixVenteInput.value) || 0;
            
            if (prixAchat > 0 && prixVente > 0) {
                const margin = ((prixVente - prixAchat) / prixAchat) * 100;
                const profit = prixVente - prixAchat;
                
                marginText.innerHTML = `Marge bénéficiaire : ${margin.toFixed(1)}% (${profit.toFixed(2)} DH)`;
                
                if (margin < 0) {
                    marginDiv.className = 'alert alert-danger';
                } else if (margin < 10) {
                    marginDiv.className = 'alert alert-warning';
                } else {
                    marginDiv.className = 'alert alert-success';
                }
            } else {
                marginText.innerHTML = 'Entrez des prix valides pour calculer la marge bénéficiaire';
                marginDiv.className = 'alert alert-info';
            }
        }
        
        prixAchatInput.addEventListener('input', function() {
            calculateMargin();
            checkForChanges();
        });
        
        prixVenteInput.addEventListener('input', function() {
            calculateMargin();
            checkForChanges();
        });

        // متابعة التغييرات
        function checkForChanges() {
            const changeIndicators = document.querySelectorAll('.change-indicator');
            const changesSummary = document.getElementById('changes-summary');
            const saveBtn = document.getElementById('saveBtn');
            let changes = [];
            
            changeIndicators.forEach(function(indicator) {
                const input = indicator.querySelector('input, select, textarea');
                if (input) {
                    const original = indicator.getAttribute('data-original');
                    const current = input.value;
                    
                    if (String(current) !== String(original)) {
                        const label = indicator.querySelector('label').textContent.replace('*', '').trim();
                        changes.push({
                            field: label,
                            original: original,
                            current: current
                        });
                        indicator.classList.add('modified');
                    } else {
                        indicator.classList.remove('modified');
                    }
                }
            });
            
            hasChanges = changes.length > 0;
            
            if (hasChanges) {
                saveBtn.innerHTML = '<i class="fas fa-save me-2"></i>Enregistrer les modifications (' + changes.length + ')';
                saveBtn.className = 'btn btn-success btn-lg';
                
                let changesHtml = '<div class="small">';
                changes.forEach(function(change) {
                    changesHtml += `<div class="mb-1">
                        <strong>${change.field}:</strong><br>
                        <span class="text-muted">${change.original || 'Vide'}</span> → 
                        <span class="text-success">${change.current || 'Vide'}</span>
                    </div>`;
                });
                changesHtml += '</div>';
                
                changesSummary.innerHTML = changesHtml;
            } else {
                saveBtn.innerHTML = '<i class="fas fa-save me-2"></i>Enregistrer les modifications';
                saveBtn.className = 'btn btn-success btn-lg';
                changesSummary.innerHTML = '<p class="text-muted">Aucune modification effectuée</p>';
            }
        }
        
        // ربط أحداث التحقق من التغييرات
        document.querySelectorAll('input, select, textarea').forEach(function(element) {
            element.addEventListener('input', checkForChanges);
            element.addEventListener('change', checkForChanges);
        });

        // إعادة تعيين النموذج
        window.resetForm = function() {
            if (hasChanges && !confirm('Voulez-vous réinitialiser toutes les modifications ?')) {
                return false;
            }
            document.getElementById('productForm').reset();
            document.querySelectorAll('.change-indicator').forEach(function(indicator) {
                indicator.classList.remove('modified');
            });
            checkForChanges();
        };

        // التحقق من صحة النموذج
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const stockMin = parseInt(document.getElementById('ART_STOCK_MIN').value) || 0;
            const stockMax = parseInt(document.getElementById('ART_STOCK_MAX').value) || 0;
            
            if (stockableCheckbox.checked && stockMax > 0 && stockMin >= stockMax) {
                e.preventDefault();
                alert('Le stock minimum ne peut pas être supérieur ou égal au stock maximum');
                return false;
            }
        });

        // تحذير عند مغادرة الصفحة مع وجود تغييرات غير محفوظة
        window.addEventListener('beforeunload', function(e) {
            if (hasChanges) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // تشغيل التحقق الأولي
        calculateMargin();
        checkForChanges();
    });
</script>
@endsection
