@extends('layouts.sb-admin')

@section('title', 'Ajouter un Produit - AccessPos Pro')

@section('page-heading')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle text-primary"></i>
            Ajouter un Nouveau Produit
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.tableau-de-bord-moderne') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.articles.index') }}">Produits</a></li>
                <li class="breadcrumb-item active">Nouveau</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i>
            Retour
        </a>
    </div>
</div>
@endsection

@section('styles')
<style>
    .form-section { 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px; 
        padding: 0; 
        margin-bottom: 25px; 
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        border: none;
    }
    
    .section-header {
        background: rgba(255, 255, 255, 0.95);
        padding: 20px 25px 15px;
        border-bottom: 3px solid #e3f2fd;
        margin-bottom: 0;
    }
    
    .section-title { 
        color: #2c3e50; 
        font-weight: 700; 
        margin-bottom: 5px; 
        display: flex; 
        align-items: center; 
        font-size: 1.1rem;
    }
    
    .section-title i { 
        margin-right: 12px; 
        padding: 8px;
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        border-radius: 8px;
        font-size: 0.9rem;
    }
    
    .section-subtitle {
        color: #6c757d;
        font-size: 0.875rem;
        margin: 0;
        font-weight: 400;
    }
    
    .section-body {
        background: white;
        padding: 25px;
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
        font-size: 0.75rem;
    }
    
    .form-control, .form-select {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }
    
    .input-group-text {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        border: none;
        font-weight: 600;
    }
    
    .btn-primary {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border: none;
        border-radius: 8px;
        padding: 12px 30px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }
    
    .btn-success {
        background: linear-gradient(45deg, #28a745, #20c997);
        border: none;
        border-radius: 8px;
        padding: 12px 30px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
    }
    
    .btn-secondary {
        background: linear-gradient(45deg, #6c757d, #495057);
        border: none;
        border-radius: 8px;
        padding: 12px 30px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }
    
    .form-switch .form-check-input {
        background-color: #e9ecef;
        border: none;
        width: 3rem;
        height: 1.5rem;
    }
    
    .form-switch .form-check-input:checked {
        background-color: #667eea;
    }
    
    .alert {
        border: none;
        border-radius: 10px;
        padding: 15px 20px;
        margin: 15px 0;
    }
    
    .alert-info {
        background: linear-gradient(45deg, #17a2b8, #138496);
        color: white;
    }
    
    .alert-success {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
    }
    
    .alert-warning {
        background: linear-gradient(45deg, #ffc107, #fd7e14);
        color: white;
    }
    
    .alert-danger {
        background: linear-gradient(45deg, #dc3545, #c82333);
        color: white;
    }
    
    .preview-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    .preview-card .card-body {
        background: rgba(255, 255, 255, 0.95);
        margin: 3px;
        border-radius: 12px;
    }
    
    .badge {
        border-radius: 20px;
        padding: 6px 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .bg-success {
        background: linear-gradient(45deg, #28a745, #20c997) !important;
    }
    
    .bg-danger {
        background: linear-gradient(45deg, #dc3545, #c82333) !important;
    }
    
    .bg-warning {
        background: linear-gradient(45deg, #ffc107, #fd7e14) !important;
    }
    
    .bg-info {
        background: linear-gradient(45deg, #17a2b8, #138496) !important;
    }
    
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: #6c757d;
    }
    
    .stock-alert { 
        background: linear-gradient(45deg, #fff3cd, #ffeaa7); 
        border: 2px solid #ffc107; 
        border-radius: 10px; 
        padding: 15px; 
        margin: 15px 0; 
        color: #856404;
        font-weight: 500;
    }
    
    .container-fluid {
        background: #f8f9fc;
        min-height: 100vh;
        padding: 20px;
    }
    
    /* Animation pour les sections */
    .form-section {
        animation: slideInUp 0.6s ease-out;
    }
    
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .section-body {
            padding: 20px 15px;
        }
        
        .btn-lg {
            padding: 10px 20px;
            font-size: 0.9rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
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

    {{-- Messages de session --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <form method="POST" action="{{ route('admin.articles.store') }}" id="productForm" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-md-8">
                <!-- المعلومات الأساسية -->
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="section-title">
                            <i class="fas fa-info-circle"></i>
                            Informations de Base
                        </h4>
                        <p class="section-subtitle">Renseignez les informations principales du produit</p>
                    </div>
                    <div class="section-body">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ART_REF" class="form-label">
                                    Code produit <span class="required">*</span>
                                </label>
                                <input type="text" class="form-control @error('ART_REF') is-invalid @enderror" 
                                       id="ART_REF" name="ART_REF" 
                                       value="{{ old('ART_REF', $nextArticleRef ?? 'ART' . date('YmdHis')) }}" 
                                       required readonly
                                       placeholder="Exemple : ART001">
                                <div class="help-text">
                                    <i class="fas fa-info-circle text-primary"></i>
                                    Un code unique sera généré automatiquement
                                </div>
                                @error('ART_REF')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ART_DESIGNATION" class="form-label">
                                    Nom du produit <span class="required">*</span>
                                </label>
                                <input type="text" class="form-control @error('ART_DESIGNATION') is-invalid @enderror" 
                                       id="ART_DESIGNATION" name="ART_DESIGNATION" value="{{ old('ART_DESIGNATION') }}" required
                                       placeholder="Saisissez le nom du produit">
                                @error('ART_DESIGNATION')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="famille" class="form-label">Famille</label>
                                <select class="form-select @error('famille') is-invalid @enderror" 
                                        id="famille" name="famille">
                                    <option value="">Choisir la famille</option>
                                    @foreach($familles as $famille)
                                        <option value="{{ $famille->FAM_REF }}" 
                                                {{ old('famille') == $famille->FAM_REF ? 'selected' : '' }}>
                                            {{ $famille->FAM_LIB }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('famille')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="sous_famille" class="form-label">Sous-catégorie</label>
                                <select class="form-select @error('sous_famille') is-invalid @enderror" 
                                        id="sous_famille" name="sous_famille">
                                    <option value="">Choisir la sous-catégorie</option>
                                    @foreach($sousFamilles as $sousFamille)
                                        <option value="{{ $sousFamille->SFM_REF }}" 
                                                data-famille="{{ $sousFamille->FAM_REF }}"
                                                {{ old('sous_famille') == $sousFamille->SFM_REF ? 'selected' : '' }}>
                                            {{ $sousFamille->SFM_LIB }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('sous_famille')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="unite_mesure" class="form-label">Unité de mesure</label>
                                <select class="form-select @error('unite_mesure') is-invalid @enderror" 
                                        id="unite_mesure" name="unite_mesure">
                                    <option value="">Choisir l'unité de mesure</option>
                                    @foreach($unitesMesure as $unite)
                                        <option value="{{ $unite->UNM_ABR }}" 
                                                {{ old('unite_mesure') == $unite->UNM_ABR ? 'selected' : '' }}>
                                            {{ $unite->UNM_LIB }} ({{ $unite->UNM_ABR }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('unite_mesure')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations de prix -->
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="section-title">
                            <i class="fas fa-dollar-sign"></i>
                            Informations de Prix
                        </h4>
                        <p class="section-subtitle">Définissez les prix d'achat et de vente</p>
                    </div>
                    <div class="section-body">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ART_PRIX_ACHAT" class="form-label">
                                    Prix d'achat <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('ART_PRIX_ACHAT') is-invalid @enderror" 
                                           id="ART_PRIX_ACHAT" name="ART_PRIX_ACHAT" value="{{ old('ART_PRIX_ACHAT') }}" 
                                           step="0.01" min="0" required placeholder="0.00">
                                    <span class="input-group-text">DA</span>
                                </div>
                                @error('ART_PRIX_ACHAT')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ART_PRIX_VENTE" class="form-label">
                                    Prix de vente <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('ART_PRIX_VENTE') is-invalid @enderror" 
                                           id="ART_PRIX_VENTE" name="ART_PRIX_VENTE" value="{{ old('ART_PRIX_VENTE') }}" 
                                           step="0.01" min="0" required placeholder="0.00">
                                    <span class="input-group-text">DA</span>
                                </div>
                                @error('ART_PRIX_VENTE')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Calcul automatique de la marge bénéficiaire -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info" id="profit-margin" style="display: none;">
                                <i class="fas fa-calculator me-2"></i>
                                <span id="margin-text"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations de stock -->
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="section-title">
                            <i class="fas fa-warehouse"></i>
                            Gestion du Stock
                        </h4>
                        <p class="section-subtitle">Configurez les paramètres de gestion du stock</p>
                    </div>
                    <div class="section-body">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="ART_STOCKABLE" 
                                       name="ART_STOCKABLE" value="1" 
                                       {{ old('ART_STOCKABLE') ? 'checked' : '' }}>
                                <label class="form-check-label" for="ART_STOCKABLE">
                                    Produit stockable (nécessite un suivi de stock)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="stock-fields" style="display: {{ old('ART_STOCKABLE') ? 'block' : 'none' }};">
                        <div class="stock-alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note :</strong> En activant le stock, un enregistrement de stock initial sera créé pour le produit.
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="initial_stock" class="form-label">Stock initial</label>
                                    <input type="number" class="form-control @error('initial_stock') is-invalid @enderror" 
                                           id="initial_stock" name="initial_stock" value="{{ old('initial_stock', 0) }}" 
                                           min="0" placeholder="0">
                                    @error('initial_stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ART_STOCK_MIN" class="form-label">Seuil minimum de stock</label>
                                    <input type="number" class="form-control @error('ART_STOCK_MIN') is-invalid @enderror" 
                                           id="ART_STOCK_MIN" name="ART_STOCK_MIN" value="{{ old('ART_STOCK_MIN', 5) }}" 
                                           min="0" placeholder="5">
                                    @error('ART_STOCK_MIN')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ART_STOCK_MAX" class="form-label">Seuil maximum de stock</label>
                                    <input type="number" class="form-control @error('ART_STOCK_MAX') is-invalid @enderror" 
                                           id="ART_STOCK_MAX" name="ART_STOCK_MAX" value="{{ old('ART_STOCK_MAX', 100) }}" 
                                           min="0" placeholder="100">
                                    @error('ART_STOCK_MAX')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paramètres latéraux -->
            <div class="col-md-4">
                <!-- Statut du produit -->
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="section-title">
                            <i class="fas fa-cog"></i>
                            Paramètres du Produit
                        </h4>
                        <p class="section-subtitle">Configuration des options du produit</p>
                    </div>
                    <div class="section-body">
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="ART_VENTE" 
                                   name="ART_VENTE" value="1" 
                                   {{ old('ART_VENTE', 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="ART_VENTE">
                                Produit actif (disponible à la vente)
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="IsMenu" 
                                   name="IsMenu" value="1" 
                                   {{ old('IsMenu') ? 'checked' : '' }}>
                            <label class="form-check-label" for="IsMenu">
                                Produit de menu (repas ou plat)
                            </label>
                        </div>
                        <div class="help-text">Produits dédiés aux restaurants ou menus</div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="section-title">
                            <i class="fas fa-sticky-note"></i>
                            Notes & Description
                        </h4>
                        <p class="section-subtitle">Informations complémentaires</p>
                    </div>
                    <div class="section-body">
                    
                    <div class="mb-3">
                        <label for="ART_DESCRIPTION" class="form-label">Description du produit</label>
                        <textarea class="form-control @error('ART_DESCRIPTION') is-invalid @enderror" 
                                  id="ART_DESCRIPTION" name="ART_DESCRIPTION" rows="4" 
                                  placeholder="Description détaillée du produit (optionnel)">{{ old('ART_DESCRIPTION') }}</textarea>
                        @error('ART_DESCRIPTION')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Aperçu rapide -->
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="section-title">
                            <i class="fas fa-eye"></i>
                            Aperçu Rapide
                        </h4>
                        <p class="section-subtitle">Prévisualisation du produit</p>
                    </div>
                    <div class="section-body">
                        <div class="preview-card card">
                            <div class="card-body">
                            <h6 class="card-title" id="preview-name">Nom du produit</h6>
                            <p class="card-text">
                                <small class="text-muted">Code : <span id="preview-ref">-</span></small><br>
                                <strong>Prix : <span id="preview-price">0.00</span> DA</strong>
                            </p>
                            <div id="preview-badges">                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons de sauvegarde -->
        <div class="row">
            <div class="col-12">
                <div class="form-section">
                    <div class="section-header">
                        <h4 class="section-title">
                            <i class="fas fa-save"></i>
                            Finalisation
                        </h4>
                        <p class="section-subtitle">Enregistrez le nouveau produit</p>
                    </div>
                    <div class="section-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <button type="submit" class="btn btn-primary btn-lg" id="saveBtn">
                                <i class="fas fa-save me-2"></i> Enregistrer le produit
                            </button>
                            <button type="submit" name="save_and_new" value="1" class="btn btn-success btn-lg" id="saveNewBtn">
                                <i class="fas fa-plus me-2"></i> Enregistrer et nouveau
                            </button>
                        </div>
                        <div>
                            <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times me-2"></i> Annuler
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تأثيرات الحركة للعناصر
        const sections = document.querySelectorAll('.form-section');
        sections.forEach((section, index) => {
            section.style.animationDelay = `${index * 0.1}s`;
        });

        // عرض/إخفاء حقول المخزون مع تأثيرات جميلة
        const stockableCheckbox = document.getElementById('ART_STOCKABLE');
        const stockFields = document.getElementById('stock-fields');
        
        stockableCheckbox.addEventListener('change', function() {
            if (this.checked) {
                stockFields.style.display = 'block';
                stockFields.style.animation = 'slideInUp 0.5s ease-out';
            } else {
                stockFields.style.animation = 'slideOutDown 0.3s ease-in';
                setTimeout(() => {
                    stockFields.style.display = 'none';
                }, 300);
            }
        });

        // فلترة الفئات الفرعية مع تأثيرات سلسة
        const familleSelect = document.getElementById('famille');
        const sousFamilleSelect = document.getElementById('sous_famille');
        const allSousFamilles = Array.from(sousFamilleSelect.options);
        
        familleSelect.addEventListener('change', function() {
            const selectedFamille = this.value;
            
            // إضافة تأثير تحميل
            sousFamilleSelect.style.opacity = '0.5';
            sousFamilleSelect.disabled = true;
            
            setTimeout(() => {
                sousFamilleSelect.innerHTML = '<option value="">Choisir la sous-catégorie</option>';
                
                allSousFamilles.forEach(function(option) {
                    if (option.value === '' || option.dataset.famille === selectedFamille) {
                        sousFamilleSelect.appendChild(option.cloneNode(true));
                    }
                });
                
                sousFamilleSelect.style.opacity = '1';
                sousFamilleSelect.disabled = false;
            }, 200);
        });

        // حساب هامش الربح مع تأثيرات بصرية محسنة
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
                
                marginText.innerHTML = `
                    <strong>Marge bénéficiaire:</strong> ${margin.toFixed(1)}% 
                    <span class="ms-2"><strong>Profit:</strong> ${profit.toFixed(2)} DA</span>
                `;
                
                marginDiv.style.display = 'block';
                marginDiv.style.animation = 'slideInUp 0.3s ease-out';
                
                if (margin < 0) {
                    marginDiv.className = 'alert alert-danger';
                    marginText.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>` + marginText.innerHTML;
                } else if (margin < 10) {
                    marginDiv.className = 'alert alert-warning';
                    marginText.innerHTML = `<i class="fas fa-exclamation-circle me-2"></i>` + marginText.innerHTML;
                } else {
                    marginDiv.className = 'alert alert-success';
                    marginText.innerHTML = `<i class="fas fa-check-circle me-2"></i>` + marginText.innerHTML;
                }
            } else {
                marginDiv.style.animation = 'slideOutUp 0.3s ease-in';
                setTimeout(() => {
                    marginDiv.style.display = 'none';
                }, 300);
            }
        }
        
        prixAchatInput.addEventListener('input', calculateMargin);
        prixVenteInput.addEventListener('input', calculateMargin);

        // معاينة محسنة مع تأثيرات
        const previewName = document.getElementById('preview-name');
        const previewRef = document.getElementById('preview-ref');
        const previewPrice = document.getElementById('preview-price');
        const previewBadges = document.getElementById('preview-badges');
        
        function updatePreview() {
            const name = document.getElementById('ART_DESIGNATION').value || 'Nom du produit';
            const ref = document.getElementById('ART_REF').value || '-';
            const price = document.getElementById('ART_PRIX_VENTE').value || '0.00';
            const isActive = document.getElementById('ART_VENTE').checked;
            const isMenu = document.getElementById('IsMenu').checked;
            const isStockable = document.getElementById('ART_STOCKABLE').checked;
            
            // تأثيرات تحديث سلسة
            [previewName, previewRef, previewPrice, previewBadges].forEach(el => {
                el.style.transition = 'all 0.3s ease';
            });
            
            previewName.textContent = name;
            previewRef.textContent = ref;
            previewPrice.textContent = parseFloat(price).toFixed(2);
            
            let badges = '';
            if (isActive) {
                badges += '<span class="badge bg-success me-1">Actif</span>';
            } else {
                badges += '<span class="badge bg-danger me-1">Inactif</span>';
            }
            
            if (isMenu) {
                badges += '<span class="badge bg-warning me-1">Menu</span>';
            }
            
            if (isStockable) {
                badges += '<span class="badge bg-info me-1">Stockable</span>';
            }
            
            previewBadges.innerHTML = badges;
        }
        
        // ربط أحداث التحديث مع تأثيرات
        ['ART_DESIGNATION', 'ART_REF', 'ART_PRIX_VENTE', 'ART_VENTE', 'IsMenu', 'ART_STOCKABLE'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener(element.type === 'checkbox' ? 'change' : 'input', updatePreview);
            }
        });

        // تشغيل المعاينة الأولى
        updatePreview();

        // تحسين التحقق من صحة النموذج
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const stockableCheckbox = document.getElementById('ART_STOCKABLE');
            const stockMin = parseInt(document.getElementById('ART_STOCK_MIN').value) || 0;
            const stockMax = parseInt(document.getElementById('ART_STOCK_MAX').value) || 0;
            
            if (stockableCheckbox.checked && stockMax > 0 && stockMin >= stockMax) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Attention!',
                    text: 'Le stock minimum doit être inférieur au stock maximum',
                    confirmButtonColor: '#667eea',
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    }
                });
                return false;
            }
            
            // التحقق من الحقول المطلوبة مع رسائل جميلة
            const artDesignation = document.getElementById('ART_DESIGNATION').value.trim();
            const artPrixVente = document.getElementById('ART_PRIX_VENTE').value;
            const artPrixAchat = document.getElementById('ART_PRIX_ACHAT').value;
            
            if (!artDesignation) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Champ requis!',
                    text: 'Le nom du produit est obligatoire',
                    confirmButtonColor: '#667eea'
                });
                document.getElementById('ART_DESIGNATION').focus();
                return false;
            }
            
            if (!artPrixVente || parseFloat(artPrixVente) <= 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Prix invalide!',
                    text: 'Le prix de vente doit être supérieur à zéro',
                    confirmButtonColor: '#667eea'
                });
                document.getElementById('ART_PRIX_VENTE').focus();
                return false;
            }
            
            if (!artPrixAchat || parseFloat(artPrixAchat) < 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Prix invalide!',
                    text: 'Le prix d\'achat ne peut pas être négatif',
                    confirmButtonColor: '#667eea'
                });
                document.getElementById('ART_PRIX_ACHAT').focus();
                return false;
            }
            
            // تأثير تحميل جميل للزر
            const submitBtns = this.querySelectorAll('button[type="submit"]');
            submitBtns.forEach(btn => {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enregistrement...';
                btn.style.transform = 'scale(0.98)';
            });
            
            // رسالة تأكيد جميلة
            Swal.fire({
                icon: 'success',
                title: 'En cours...',
                text: 'Enregistrement du produit en cours',
                timer: 1500,
                showConfirmButton: false,
                timerProgressBar: true
            });
        });

        // تأثيرات hover للأزرار
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });

    // إضافة تأثيرات CSS إضافية
    const additionalCSS = `
        @keyframes slideOutDown {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(30px);
            }
        }
        
        @keyframes slideOutUp {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-30px);
            }
        }
        
        .form-control:focus {
            transform: scale(1.02);
        }
        
        .form-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
    `;
    
    const style = document.createElement('style');
    style.textContent = additionalCSS;
    document.head.appendChild(style);
</script>
@endsection