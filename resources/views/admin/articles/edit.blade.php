

@extends('layouts.sb-admin')

@section('title', 'Modifier le produit - ' . $article->ART_DESIGNATION)

@section('page-heading')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit text-primary"></i>
            Modifier le Produit
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.tableau-de-bord-moderne') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.articles.index') }}">Produits</a></li>
                <li class="breadcrumb-item active">Modifier - {{ $article->ART_DESIGNATION }}</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.articles.show', $article->ART_REF) }}" class="btn btn-info btn-sm">
            <i class="fas fa-eye"></i> Détails
        </a>
        <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>
@endsection

@section('styles')
<style>
    .form-section { 
        background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 15px; 
        padding: 0; 
        margin-bottom: 25px; 
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid #e3ebf0;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .form-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }
    
    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px 25px;
        margin: 0;
        border-bottom: none;
    }
    
    .section-title { 
        color: white;
        font-weight: 700; 
        margin-bottom: 5px; 
        display: flex; 
        align-items: center; 
        font-size: 1.1rem;
    }
    
    .section-title i { 
        margin-right: 12px; 
        padding: 10px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        font-size: 1rem;
    }
    
    .section-subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.875rem;
        margin: 0;
        font-weight: 400;
    }
    
    .section-body {
        padding: 25px;
        background: white;
    }
    
    .required { 
        color: #e74c3c; 
        font-weight: bold;
    }
    
    .help-text { 
        font-size: 0.8rem; 
        color: #6c757d; 
        margin-top: 5px;
        display: flex;
        align-items: center;
    }
    
    .help-text i {
        margin-right: 5px;
        color: #007bff;
    }
    
    .stock-alert { 
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border: 1px solid #f0ad4e; 
        border-radius: 10px; 
        padding: 15px; 
        margin: 15px 0;
        box-shadow: 0 5px 15px rgba(240, 173, 78, 0.2);
    }
    
    .change-indicator {
        position: relative;
        transition: all 0.3s ease;
        border-radius: 8px;
        padding: 10px;
        margin: 5px 0;
    }
    
    .change-indicator.modified {
        border-left: 4px solid #ffc107;
        background: linear-gradient(135deg, #fff8e1 0%, #fffbee 100%);
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.2);
        transform: translateX(3px);
    }
    
    .original-value {
        font-size: 0.8rem;
        color: #6c757d;
        font-style: italic;
        margin-top: 5px;
        padding: 5px 10px;
        background: #f8f9fa;
        border-radius: 5px;
        border-left: 3px solid #dee2e6;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        border: 2px solid #e3ebf0;
        padding: 12px 15px;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        transform: translateY(-1px);
    }
    
    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }
    
    .input-group-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        font-weight: 600;
    }
    
    .btn {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
    }
    
    .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        border: none;
    }
    
    .btn-info {
        background: linear-gradient(135deg, #17a2b8 0%, #007bff 100%);
        border: none;
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        border: none;
    }
    
    .alert {
        border-radius: 10px;
        border: none;
        padding: 15px 20px;
        margin: 15px 0;
    }
    
    .alert-info {
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        color: #0c5460;
    }
    
    .alert-success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
    }
    
    .alert-warning {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        color: #856404;
    }
    
    .alert-danger {
        background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
        color: #721c24;
    }
    
    .card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
    }
    
    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        color: #667eea;
    }
    
    .breadcrumb-item a {
        color: #667eea;
        text-decoration: none;
    }
    
    .breadcrumb-item a:hover {
        color: #764ba2;
        text-decoration: underline;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    {{-- رسائل التحقق --}}
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h6><i class="fas fa-exclamation-triangle"></i> أخطاء في التحقق:</h6>
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

    {{-- رسالة النجاح --}}
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
                    <div class="section-header">
                        <h4 class="section-title">
                            <i class="fas fa-info-circle"></i>
                            Informations de base
                        </h4>
                        <p class="section-subtitle">Informations principales du produit</p>
                    </div>
                    <div class="section-body">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ART_REF" class="form-label">
                                    Code produit <span class="required">*</span>
                                </label>
                                <input type="text" class="form-control @error('ART_REF') is-invalid @enderror" 
                                       id="ART_REF" name="ART_REF" value="{{ old('ART_REF', $article->ART_REF) }}" 
                                       required readonly>
                                <div class="help-text">
                                    <i class="fas fa-lock"></i>
                                    Le code produit ne peut pas être modifié
                                </div>
                                @error('ART_REF')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 change-indicator" data-original="{{ $article->ART_DESIGNATION }}">
                                <label for="art_designation" class="form-label">
                                    Nom du produit <span class="required">*</span>
                                </label>
                                <input type="text" class="form-control @error('art_designation') is-invalid @enderror" 
                                       id="art_designation" name="art_designation" 
                                       value="{{ old('art_designation', $article->ART_DESIGNATION) }}" required>
                                <div class="original-value">Valeur originale : {{ $article->ART_DESIGNATION }}</div>
                                @error('art_designation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3 change-indicator" data-original="{{ $article->famille_ref ?? '' }}">
                                <label for="famille" class="form-label">Famille</label>
                                <select class="form-select @error('famille') is-invalid @enderror" 
                                        id="famille" name="famille">
                                    <option value="">Choisir une famille</option>
                                    @foreach($familles as $famille)
                                        <option value="{{ $famille->FAM_REF }}" 
                                                {{ old('famille', $article->famille_ref ?? '') == $famille->FAM_REF ? 'selected' : '' }}>
                                            {{ $famille->FAM_LIB }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="original-value">
                                    Valeur originale : {{ $article->famille ?? 'Non défini' }}
                                </div>
                                @error('famille')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 change-indicator" data-original="{{ $article->SFM_REF }}">
                                <label for="sfm_ref" class="form-label">
                                    Sous-famille <span class="required">*</span>
                                </label>
                                <select class="form-select @error('sfm_ref') is-invalid @enderror" 
                                        id="sfm_ref" name="sfm_ref" required>
                                    <option value="">Choisir une sous-famille</option>
                                    @foreach($sousFamilles as $sousFamille)
                                        <option value="{{ $sousFamille->SFM_REF }}" 
                                                data-famille="{{ $sousFamille->FAM_REF }}"
                                                {{ old('sfm_ref', $article->SFM_REF) == $sousFamille->SFM_REF ? 'selected' : '' }}>
                                            {{ $sousFamille->SFM_LIB }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="original-value">
                                    Valeur originale : {{ $article->sous_famille ?? 'Non défini' }}
                                </div>
                                @error('sfm_ref')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- معلومات الأسعار -->
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="section-title">
                            <i class="fas fa-dollar-sign"></i>
                            Informations de prix
                        </h4>
                        <p class="section-subtitle">Configuration des prix d'achat et de vente</p>
                    </div>
                    <div class="section-body">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3 change-indicator" data-original="{{ $article->ART_PRIX_ACHAT }}">
                                <label for="art_prix_achat" class="form-label">Prix d'achat</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('art_prix_achat') is-invalid @enderror" 
                                           id="art_prix_achat" name="art_prix_achat" 
                                           value="{{ old('art_prix_achat', $article->ART_PRIX_ACHAT) }}" 
                                           step="0.01" min="0">
                                    <span class="input-group-text">DH</span>
                                </div>
                                <div class="original-value">
                                    Valeur originale : {{ number_format($article->ART_PRIX_ACHAT ?? 0, 2) }} DH
                                </div>
                                @error('art_prix_achat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 change-indicator" data-original="{{ $article->ART_PRIX_VENTE }}">
                                <label for="art_prix_vente" class="form-label">
                                    Prix de vente <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('art_prix_vente') is-invalid @enderror" 
                                           id="art_prix_vente" name="art_prix_vente" 
                                           value="{{ old('art_prix_vente', $article->ART_PRIX_VENTE) }}" 
                                           step="0.01" min="0" required>
                                    <span class="input-group-text">DH</span>
                                </div>
                                <div class="original-value">
                                    Valeur originale : {{ number_format($article->ART_PRIX_VENTE, 2) }} DH
                                </div>
                                @error('art_prix_vente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
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
                    <!-- حساب هامش الربح التلقائي -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info" id="profit-margin">
                                <i class="fas fa-calculator me-2"></i>
                                <span id="margin-text">Entrez des prix valides pour calculer la marge bénéficiaire</span>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>

                <!-- معلومات المخزون -->
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="section-title">
                            <i class="fas fa-warehouse"></i>
                            Informations de stock
                        </h4>
                        <p class="section-subtitle">Gestion des stocks et seuils d'alerte</p>
                    </div>
                    <div class="section-body">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="art_stockable" 
                                       name="art_stockable" value="1" 
                                       {{ old('art_stockable', $article->ART_STOCKABLE) ? 'checked' : '' }}>
                                <label class="form-check-label" for="art_stockable">
                                    Produit stockable (nécessite un suivi de stock)
                                </label>
                                <div class="original-value">
                                    État original : {{ $article->ART_STOCKABLE ? 'Stockable' : 'Non stockable' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="stock-fields" style="display: {{ old('art_stockable', $article->ART_STOCKABLE) ? 'block' : 'none' }};">
                        @if($article->ART_STOCKABLE)
                            <div class="stock-alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Stock actuel :</strong> {{ $article->stock_total ?? 0 }} unités
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 change-indicator" data-original="{{ $article->ART_STOCK_MIN }}">
                                    <label for="art_stock_min" class="form-label">Stock minimum</label>
                                    <input type="number" class="form-control @error('art_stock_min') is-invalid @enderror" 
                                           id="art_stock_min" name="art_stock_min" 
                                           value="{{ old('art_stock_min', $article->ART_STOCK_MIN) }}" 
                                           min="0">
                                    <div class="original-value">
                                        Valeur originale : {{ $article->ART_STOCK_MIN ?? 0 }}
                                    </div>
                                    @error('art_stock_min')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 change-indicator" data-original="{{ $article->ART_STOCK_MAX }}">
                                    <label for="art_stock_max" class="form-label">Stock maximum</label>
                                    <input type="number" class="form-control @error('art_stock_max') is-invalid @enderror" 
                                           id="art_stock_max" name="art_stock_max" 
                                           value="{{ old('art_stock_max', $article->ART_STOCK_MAX) }}" 
                                           min="0">
                                    <div class="original-value">
                                        Valeur originale : {{ $article->ART_STOCK_MAX ?? 0 }}
                                    </div>
                                    @error('art_stock_max')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الإعدادات الجانبية -->
                <div class="col-md-4">
                    <!-- حالة المنتج -->
                    <div class="form-section">
                        <div class="section-header">
                            <h4 class="section-title">
                                <i class="fas fa-cog"></i>
                                Paramètres du produit
                            </h4>
                            <p class="section-subtitle">Configuration et état du produit</p>
                        </div>
                        <div class="section-body">
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="art_vente" 
                                   name="art_vente" value="1" 
                                   {{ old('art_vente', $article->ART_VENTE) ? 'checked' : '' }}>
                            <label class="form-check-label" for="art_vente">
                                Produit actif (disponible à la vente)
                            </label>
                        </div>
                        <div class="original-value">
                            État original : {{ $article->ART_VENTE ? 'Actif' : 'Inactif' }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_menu" 
                                   name="is_menu" value="1" 
                                   {{ old('is_menu', $article->IsMenu) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_menu">
                                Produit menu (plat ou repas)
                            </label>
                        </div>
                                <div class="help-text">
                                    <i class="fas fa-info-circle"></i>
                                    Produits dédiés aux restaurants ou menus
                                </div>
                        <div class="original-value">
                            État original : {{ $article->IsMenu ? 'Produit menu' : 'Produit normal' }}
                        </div>                        </div>
                    </div>

                    <!-- ملاحظات -->
                    <div class="form-section">
                        <div class="section-header">
                            <h4 class="section-title">
                                <i class="fas fa-sticky-note"></i>
                                Notes supplémentaires
                            </h4>
                            <p class="section-subtitle">Description et informations additionnelles</p>
                        </div>
                        <div class="section-body">
                    
                    <div class="mb-3 change-indicator" data-original="{{ $article->ART_DESCRIPTION }}">
                        <label for="art_description" class="form-label">Description du produit</label>
                        <textarea class="form-control @error('art_description') is-invalid @enderror" 
                                  id="art_description" name="art_description" rows="4" 
                                  placeholder="Description détaillée du produit (facultatif)">{{ old('art_description', $article->ART_DESCRIPTION) }}</textarea>
                        <div class="original-value">
                            Description originale : {{ $article->ART_DESCRIPTION ?: 'Pas de description' }}
                        </div>
                        @error('art_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3 change-indicator" data-original="{{ $article->ART_LIBELLE_TICKET }}">
                        <label for="art_libelle_ticket" class="form-label">Texte d'impression</label>
                        <input type="text" class="form-control @error('art_libelle_ticket') is-invalid @enderror" 
                               id="art_libelle_ticket" name="art_libelle_ticket" 
                               value="{{ old('art_libelle_ticket', $article->ART_LIBELLE_TICKET) }}" 
                               placeholder="Texte qui apparaîtra sur la facture (facultatif)">
                        <div class="original-value">
                            Texte original : {{ $article->ART_LIBELLE_TICKET ?: 'Pas de texte' }}
                        </div>
                        @error('art_libelle_ticket')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror                        </div>
                    </div>

                    <!-- معلومات التعديل -->
                    <div class="form-section">
                        <div class="section-header">
                            <h4 class="section-title">
                                <i class="fas fa-clock"></i>
                                Informations de modification
                            </h4>
                            <p class="section-subtitle">Historique des modifications</p>
                        </div>
                        <div class="section-body">
                    
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text">
                                <small class="text-muted">
                                    <strong>Date de création :</strong><br>
                                    {{ $article->ART_DATE_CREATION ? \Carbon\Carbon::parse($article->ART_DATE_CREATION)->format('d/m/Y H:i') : 'Non défini' }}
                                </small>
                            </p>
                            @if(isset($article->ART_DATE_MODIFICATION) && $article->ART_DATE_MODIFICATION)
                                <p class="card-text">
                                    <small class="text-muted">
                                        <strong>Dernière modification :</strong><br>
                                        {{ \Carbon\Carbon::parse($article->ART_DATE_MODIFICATION)->format('d/m/Y H:i') }}
                                    </small>
                                </p>
                            @endif
                            <p class="card-text">
                                <small class="text-muted">
                                    <strong>Utilisateur actuel :</strong><br>
                                    {{ auth()->user()->name }}
                                </small>
                            </p>
                        </div>                        </div>
                    </div>

                    <!-- معاينة التغييرات -->
                    <div class="form-section">
                        <div class="section-header">
                            <h4 class="section-title">
                                <i class="fas fa-eye"></i>
                                Aperçu des modifications
                            </h4>
                            <p class="section-subtitle">Suivi des changements effectués</p>
                        </div>
                        <div class="section-body">
                    
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
                    <div class="section-header">
                        <h4 class="section-title">
                            <i class="fas fa-save"></i>
                            Actions de sauvegarde
                        </h4>
                        <p class="section-subtitle">Enregistrer ou annuler les modifications</p>
                    </div>
                    <div class="section-body">
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
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let hasChanges = false;
        
        // عرض/إخفاء حقول المخزون
        const stockableCheckbox = document.getElementById('art_stockable');
        const stockFields = document.getElementById('stock-fields');
        
        stockableCheckbox.addEventListener('change', function() {
            stockFields.style.display = this.checked ? 'block' : 'none';
            checkForChanges();
        });

        // فلترة الفئات الفرعية حسب العائلة
        const familleSelect = document.getElementById('famille');
        const sousFamilleSelect = document.getElementById('sfm_ref');
        const allSousFamilles = Array.from(sousFamilleSelect.options);
        
        familleSelect.addEventListener('change', function() {
            const selectedFamille = this.value;
            const currentValue = sousFamilleSelect.value;
            
            // إزالة جميع الخيارات عدا الأول
            sousFamilleSelect.innerHTML = '<option value="">Choisir une sous-famille</option>';
            
            // إضافة الخيارات المطابقة فقط
            allSousFamilles.forEach(function(option) {
                if (option.value === '' || option.dataset.famille === selectedFamille) {
                    const newOption = option.cloneNode(true);
                    if (newOption.value === currentValue) {
                        newOption.selected = true;
                    }
                    sousFamilleSelect.appendChild(newOption);
                }
            });
            
            checkForChanges();
        });

        // حساب هامش الربح التلقائي
        const prixAchatInput = document.getElementById('art_prix_achat');
        const prixVenteInput = document.getElementById('art_prix_vente');
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
                    const originalValue = indicator.dataset.original || '';
                    const currentValue = input.type === 'checkbox' ? (input.checked ? '1' : '0') : input.value;
                    
                    if (currentValue !== originalValue) {
                        indicator.classList.add('modified');
                        const label = indicator.querySelector('label').textContent.replace('*', '').trim();
                        changes.push(label);
                    } else {
                        indicator.classList.remove('modified');
                    }
                }
            });
            
            hasChanges = changes.length > 0;
            
            if (hasChanges) {
                changesSummary.innerHTML = `
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-edit"></i> Modifications effectuées sur :</h6>
                        <ul class="list-unstyled mb-0">
                            ${changes.map(change => `<li><i class="fas fa-arrow-right text-warning me-2"></i>${change}</li>`).join('')}
                        </ul>
                    </div>
                `;
                saveBtn.classList.remove('btn-success');
                saveBtn.classList.add('btn-warning');
                saveBtn.innerHTML = '<i class="fas fa-save me-2"></i>Enregistrer les modifications (' + changes.length + ')';
            } else {
                changesSummary.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> Aucune modification effectuée</div>';
                saveBtn.classList.remove('btn-warning');
                saveBtn.classList.add('btn-success');
                saveBtn.innerHTML = '<i class="fas fa-save me-2"></i>Enregistrer les modifications';
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
                return;
            }
            document.getElementById('productForm').reset();
            checkForChanges();
        };

        // التحقق من صحة النموذج
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const stockMin = parseInt(document.getElementById('art_stock_min').value) || 0;
            const stockMax = parseInt(document.getElementById('art_stock_max').value) || 0;
            
            if (stockableCheckbox.checked && stockMax > 0 && stockMin >= stockMax) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Attention !',
                        text: 'Le stock minimum doit être inférieur au stock maximum',
                        confirmButtonClass: 'btn btn-warning'
                    });
                } else {
                    alert('Le stock minimum doit être inférieur au stock maximum');
                }
                return false;
            }
            
            if (hasChanges) {
                return confirm('Voulez-vous enregistrer les modifications ?');
            }
        });

        // تحذير عند مغادرة الصفحة مع وجود تغييرات غير محفوظة
        window.addEventListener('beforeunload', function(e) {
            if (hasChanges) {
                e.preventDefault();
                e.returnValue = '';
                return 'Vous avez des modifications non enregistrées. Voulez-vous quitter ?';
            }
        });

        // تشغيل التحقق الأولي
        calculateMargin();
        checkForChanges();
    });
</script>
@endsection