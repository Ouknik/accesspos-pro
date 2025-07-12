@extends('admin.layouts.app')

@section('title', 'Ajouter un nouveau produit')

@section('styles')
<style>
    .form-section { 
        background: #f8f9fa; 
        border-radius: 10px; 
        padding: 20px; 
        margin-bottom: 20px; 
        border-left: 4px solid #007bff;
    }
    .section-title { 
        color: #007bff; 
        font-weight: bold; 
        margin-bottom: 15px; 
        display: flex; 
        align-items: center; 
    }
    .section-title i { margin-right: 8px; }
    .required { color: #dc3545; }
    .help-text { font-size: 0.875rem; color: #6c757d; }
    .price-input { position: relative; }
    .price-input::after { 
        content: 'DA'; 
        position: absolute; 
        right: 10px; 
        top: 50%; 
        transform: translateY(-50%); 
        color: #6c757d; 
    }
    .stock-alert { 
        background: #fff3cd; 
        border: 1px solid #ffeaa7; 
        border-radius: 5px; 
        padding: 10px; 
        margin: 10px 0; 
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
            <li class="breadcrumb-item active">Ajouter un nouveau produit</li>
        </ol>
    </nav>

    <!-- Titre principal -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Ajouter un nouveau produit</h1>
            <p class="text-muted">Ajouter un nouveau produit à la base de données</p>
        </div>
        <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <form method="POST" action="{{ route('admin.articles.store') }}" id="productForm" enctype="multipart/form-data">
        @csrf
        
        <!-- Messages d'erreurs généraux -->
        @if (session('errors') && count(session('errors')) > 0)
            <div class="alert alert-danger">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Veuillez corriger les erreurs suivantes :</h6>
                <ul class="mb-0">
                    @foreach (session('errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif
        <div class="row">
            <div class="col-md-8">
                <!-- Informations de base -->
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
                    <h4 class="section-title">
                        <i class="fas fa-dollar-sign"></i>
                        Informations de prix
                    </h4>
                    
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
                    <h4 class="section-title">
                        <i class="fas fa-warehouse"></i>
                        Informations de stock
                    </h4>
                    
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
                    <h4 class="section-title">
                        <i class="fas fa-cog"></i>
                        Paramètres du produit
                    </h4>
                    
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
                    <h4 class="section-title">
                        <i class="fas fa-sticky-note"></i>
                        Notes supplémentaires
                    </h4>
                    
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
                    <h4 class="section-title">
                        <i class="fas fa-eye"></i>
                        Aperçu rapide
                    </h4>
                    
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title" id="preview-name">Nom du produit</h6>
                            <p class="card-text">
                                <small class="text-muted">Code : <span id="preview-ref">-</span></small><br>
                                <strong>Prix : <span id="preview-price">0.00</span> DA</strong>
                            </p>
                            <div id="preview-badges"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons de sauvegarde -->
        <div class="row">
            <div class="col-12">
                <div class="form-section">
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // عرض/إخفاء حقول المخزون
        const stockableCheckbox = document.getElementById('ART_STOCKABLE');
        const stockFields = document.getElementById('stock-fields');
        
        stockableCheckbox.addEventListener('change', function() {
            stockFields.style.display = this.checked ? 'block' : 'none';
        });

        // فلترة الفئات الفرعية حسب العائلة
        const familleSelect = document.getElementById('famille');
        const sousFamilleSelect = document.getElementById('sous_famille');
        const allSousFamilles = Array.from(sousFamilleSelect.options);
        
        familleSelect.addEventListener('change', function() {
            const selectedFamille = this.value;
            
            // إزالة جميع الخيارات عدا الأول
            sousFamilleSelect.innerHTML = '<option value="">اختر الفئة الفرعية</option>';
            
            // إضافة الخيارات المطابقة فقط
            allSousFamilles.forEach(function(option) {
                if (option.value === '' || option.dataset.famille === selectedFamille) {
                    sousFamilleSelect.appendChild(option.cloneNode(true));
                }
            });
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
                
                marginText.innerHTML = `هامش الربح: ${margin.toFixed(1)}% (${profit.toFixed(2)} دج)`;
                marginDiv.style.display = 'block';
                
                if (margin < 0) {
                    marginDiv.className = 'alert alert-danger';
                } else if (margin < 10) {
                    marginDiv.className = 'alert alert-warning';
                } else {
                    marginDiv.className = 'alert alert-success';
                }
            } else {
                marginDiv.style.display = 'none';
            }
        }
        
        prixAchatInput.addEventListener('input', calculateMargin);
        prixVenteInput.addEventListener('input', calculateMargin);

        // معاينة سريعة
        const previewName = document.getElementById('preview-name');
        const previewRef = document.getElementById('preview-ref');
        const previewPrice = document.getElementById('preview-price');
        const previewBadges = document.getElementById('preview-badges');
        
        function updatePreview() {
            const name = document.getElementById('ART_DESIGNATION').value || 'اسم المنتج';
            const ref = document.getElementById('ART_REF').value || '-';
            const price = document.getElementById('ART_PRIX_VENTE').value || '0.00';
            const isActive = document.getElementById('ART_VENTE').checked;
            const isMenu = document.getElementById('IsMenu').checked;
            const isStockable = document.getElementById('ART_STOCKABLE').checked;
            
            previewName.textContent = name;
            previewRef.textContent = ref;
            previewPrice.textContent = parseFloat(price).toFixed(2);
            
            let badges = '';
            if (isActive) {
                badges += '<span class="badge bg-success me-1">نشط</span>';
            } else {
                badges += '<span class="badge bg-danger me-1">غير نشط</span>';
            }
            
            if (isMenu) {
                badges += '<span class="badge bg-warning me-1">قائمة</span>';
            }
            
            if (isStockable) {
                badges += '<span class="badge bg-info me-1">مخزني</span>';
            }
            
            previewBadges.innerHTML = badges;
        }
        
        // ربط أحداث التحديث
        document.getElementById('ART_DESIGNATION').addEventListener('input', updatePreview);
        document.getElementById('ART_REF').addEventListener('input', updatePreview);
        document.getElementById('ART_PRIX_VENTE').addEventListener('input', updatePreview);
        document.getElementById('ART_VENTE').addEventListener('change', updatePreview);
        document.getElementById('IsMenu').addEventListener('change', updatePreview);
        document.getElementById('ART_STOCKABLE').addEventListener('change', updatePreview);

        // تشغيل المعاينة الأولى
        updatePreview();

        // التحقق من صحة النموذج وإرساله
        document.getElementById('productForm').addEventListener('submit', function(e) {
            console.log('تم إرسال النموذج');
            
            // التحقق من المخزون
            const stockableCheckbox = document.getElementById('ART_STOCKABLE');
            const stockMin = parseInt(document.getElementById('ART_STOCK_MIN').value) || 0;
            const stockMax = parseInt(document.getElementById('ART_STOCK_MAX').value) || 0;
            
            if (stockableCheckbox.checked && stockMax > 0 && stockMin >= stockMax) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'تحذير!',
                    text: 'الحد الأدنى للمخزون يجب أن يكون أقل من الحد الأقصى',
                });
                return false;
            }
            
            // التحقق من الحقول المطلوبة
            const artDesignation = document.getElementById('ART_DESIGNATION').value.trim();
            const artPrixVente = document.getElementById('ART_PRIX_VENTE').value;
            const artPrixAchat = document.getElementById('ART_PRIX_ACHAT').value;
            
            if (!artDesignation) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'تحذير!',
                    text: 'اسم المنتج مطلوب',
                });
                return false;
            }
            
            if (!artPrixVente || parseFloat(artPrixVente) <= 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'تحذير!',
                    text: 'سعر البيع مطلوب ويجب أن يكون أكبر من صفر',
                });
                return false;
            }
            
            if (!artPrixAchat || parseFloat(artPrixAchat) < 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'تحذير!',
                    text: 'سعر الشراء مطلوب ولا يمكن أن يكون سالباً',
                });
                return false;
            }
            
            // إظهار مؤشر التحميل
            const submitBtn = e.target.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الحفظ...';
            }
            
            console.log('النموذج صالح، يتم الإرسال...');
        });
    });
</script>
@endsection
