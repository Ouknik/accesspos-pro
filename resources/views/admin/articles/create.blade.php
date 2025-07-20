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
        <a href="{{ route('admin.articles.index') }}" class="btn-modern btn-secondary-modern">
            <i class="fas fa-arrow-left"></i>
            Retour
        </a>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border: 1px solid rgba(226, 230, 234, 0.6);
    }
    
    .card-header {
        background: linear-gradient(135deg, #f8f9fc 0%, #e2e6ea 100%);
        border-bottom: 1px solid #e3e6f0;
        font-weight: 600;
        color: #5a5c69;
    }
    
    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        transform: translateY(-2px);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        border: none;
    }
    
    .btn-success:hover {
        background: linear-gradient(135deg, #17a673 0%, #0f6848 100%);
        transform: translateY(-2px);
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, #858796 0%, #5a5c69 100%);
        border: none;
    }
    
    .btn-secondary:hover {
        background: linear-gradient(135deg, #717384 0%, #484a54 100%);
        transform: translateY(-2px);
    }
    
    .form-check-input:checked {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    
    .text-primary {
        color: #4e73df !important;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .shadow {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }
    
    .small-box {
        border-radius: 0.35rem;
        position: relative;
        display: block;
        margin-bottom: 20px;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    }
    
    .small-box > .inner {
        padding: 10px;
    }
    
    .small-box > .small-box-footer {
        position: relative;
        text-align: center;
        padding: 3px 0;
        color: #fff;
        color: rgba(255, 255, 255, 0.8);
        display: block;
        z-index: 10;
        background: rgba(0, 0, 0, 0.1);
        text-decoration: none;
    }
    
    .required {
        color: #e74a3b;
        font-weight: bold;
    }
    
    .help-text {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }
    
    .fade-in {
        animation: fadeIn 0.6s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .hover-shadow:hover {
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
        transition: all 0.15s ease-in-out;
    }
    
    .form-floating {
        position: relative;
    }
    
    .price-preview {
        background: linear-gradient(135deg, #f8f9fc 0%, #e2e6ea 100%);
        border-left: 0.25rem solid #1cc88a;
        padding: 1rem;
        border-radius: 0.35rem;
        margin-top: 1rem;
    }
    
    .stock-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
    }
    
    .stock-indicator.active {
        background-color: #1cc88a;
    }
    
    .stock-indicator.inactive {
        background-color: #e74a3b;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Messages de validation --}}
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-left-danger" role="alert">
        <h6><i class="fas fa-exclamation-triangle"></i> أخطاء في التحقق من البيانات:</h6>
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
        <div class="alert alert-danger alert-dismissible fade show border-left-danger" role="alert">
            <i class="fas fa-times-circle me-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-left-success" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.articles.store') }}" id="productForm" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <!-- المعلومات الأساسية -->
                <div class="card shadow mb-4 fade-in hover-shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle"></i> المعلومات الأساسية
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ART_REF" class="form-label font-weight-bold text-dark">
                                        كود المنتج <span class="required">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('ART_REF') is-invalid @enderror" 
                                           id="ART_REF" name="ART_REF" 
                                           value="{{ old('ART_REF', $nextArticleRef ?? 'ART' . date('YmdHis')) }}" 
                                           required readonly
                                           placeholder="مثال: ART001">
                                    <small class="help-text">
                                        <i class="fas fa-info-circle text-primary"></i>
                                        سيتم إنشاء كود فريد تلقائياً
                                    </small>
                                    @error('ART_REF')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ART_DESIGNATION" class="form-label font-weight-bold text-dark">
                                        اسم المنتج <span class="required">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('ART_DESIGNATION') is-invalid @enderror" 
                                           id="ART_DESIGNATION" name="ART_DESIGNATION" value="{{ old('ART_DESIGNATION') }}" required
                                           placeholder="أدخل اسم المنتج">
                                    @error('ART_DESIGNATION')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="famille" class="form-label font-weight-bold text-dark">العائلة</label>
                                    <select class="form-control @error('famille') is-invalid @enderror" 
                                            id="famille" name="famille">
                                        <option value="">اختر العائلة</option>
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
                                    <label for="sous_famille" class="form-label font-weight-bold text-dark">الفئة الفرعية</label>
                                    <select class="form-control @error('sous_famille') is-invalid @enderror" 
                                            id="sous_famille" name="sous_famille">
                                        <option value="">اختر الفئة الفرعية</option>
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
                                    <label for="unite_mesure" class="form-label font-weight-bold text-dark">وحدة القياس</label>
                                    <select class="form-control @error('unite_mesure') is-invalid @enderror" 
                                            id="unite_mesure" name="unite_mesure">
                                        <option value="">اختر وحدة القياس</option>
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
                </div>

                <!-- معلومات الأسعار -->
                <div class="card shadow mb-4 fade-in hover-shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-dollar-sign"></i> معلومات الأسعار
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ART_PRIX_ACHAT" class="form-label font-weight-bold text-dark">
                                        سعر الشراء <span class="required">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('ART_PRIX_ACHAT') is-invalid @enderror" 
                                               id="ART_PRIX_ACHAT" name="ART_PRIX_ACHAT" value="{{ old('ART_PRIX_ACHAT') }}" 
                                               step="0.01" min="0" required placeholder="0.00">
                                        <div class="input-group-append">
                                            <span class="input-group-text bg-primary text-white">دج</span>
                                        </div>
                                    </div>
                                    @error('ART_PRIX_ACHAT')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ART_PRIX_VENTE" class="form-label font-weight-bold text-dark">
                                        سعر البيع <span class="required">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('ART_PRIX_VENTE') is-invalid @enderror" 
                                               id="ART_PRIX_VENTE" name="ART_PRIX_VENTE" value="{{ old('ART_PRIX_VENTE') }}" 
                                               step="0.01" min="0" required placeholder="0.00">
                                        <div class="input-group-append">
                                            <span class="input-group-text bg-primary text-white">دج</span>
                                        </div>
                                    </div>
                                    @error('ART_PRIX_VENTE')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- حساب الهامش تلقائياً -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="price-preview" id="profit-margin" style="display: none;">
                                    <i class="fas fa-calculator me-2"></i>
                                    <span id="margin-text"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إدارة المخزون -->
                <div class="card shadow mb-4 fade-in hover-shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-warehouse"></i> إدارة المخزون
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="ART_STOCKABLE" 
                                           name="ART_STOCKABLE" value="1" 
                                           {{ old('ART_STOCKABLE') ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-bold" for="ART_STOCKABLE">
                                        منتج قابل للتخزين (يتطلب متابعة المخزون)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="stock-fields" style="display: {{ old('ART_STOCKABLE') ? 'block' : 'none' }};">
                            <div class="alert alert-warning border-left-warning">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>ملاحظة:</strong> عند تفعيل المخزون، سيتم إنشاء سجل مخزون أولي للمنتج.
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="initial_stock" class="form-label font-weight-bold text-dark">المخزون الأولي</label>
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
                                        <label for="ART_STOCK_MIN" class="form-label font-weight-bold text-dark">الحد الأدنى للمخزون</label>
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
                                        <label for="ART_STOCK_MAX" class="form-label font-weight-bold text-dark">الحد الأقصى للمخزون</label>
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
            </div>

            <!-- الشريط الجانبي -->
            <div class="col-xl-4 col-lg-5">
                <!-- إعدادات المنتج -->
                <div class="card shadow mb-4 fade-in hover-shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-cog"></i> إعدادات المنتج
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="ART_VENTE" 
                                       name="ART_VENTE" value="1" 
                                       {{ old('ART_VENTE', 1) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="ART_VENTE">
                                    منتج نشط (متاح للبيع)
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="IsMenu" 
                                       name="IsMenu" value="1" 
                                       {{ old('IsMenu') ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="IsMenu">
                                    منتج قائمة طعام (وجبة أو طبق)
                                </label>
                            </div>
                            <small class="help-text">منتجات مخصصة للمطاعم أو القوائم</small>
                        </div>
                    </div>
                </div>

                <!-- الملاحظات والوصف -->
                <div class="card shadow mb-4 fade-in hover-shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-sticky-note"></i> الملاحظات والوصف
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="ART_DESCRIPTION" class="form-label font-weight-bold text-dark">وصف المنتج</label>
                            <textarea class="form-control @error('ART_DESCRIPTION') is-invalid @enderror" 
                                      id="ART_DESCRIPTION" name="ART_DESCRIPTION" rows="4" 
                                      placeholder="وصف تفصيلي للمنتج (اختياري)">{{ old('ART_DESCRIPTION') }}</textarea>
                            @error('ART_DESCRIPTION')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- معاينة سريعة -->
                <div class="card shadow mb-4 fade-in hover-shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-eye"></i> معاينة سريعة
                        </h6>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title font-weight-bold" id="preview-name">اسم المنتج</h6>
                        <p class="card-text">
                            <small class="text-muted">الكود: <span id="preview-ref">-</span></small><br>
                            <strong>السعر: <span id="preview-price">0.00</span> دج</strong>
                        </p>
                        <div id="preview-badges"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- أزرار الحفظ -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4 fade-in">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-save"></i> حفظ المنتج
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap">
                            <div class="mb-2">
                                <button type="submit" class="btn btn-primary btn-lg mr-2" id="saveBtn">
                                    <i class="fas fa-save me-2"></i> حفظ المنتج
                                </button>
                                <button type="submit" name="save_and_new" value="1" class="btn btn-success btn-lg mr-2" id="saveNewBtn">
                                    <i class="fas fa-plus me-2"></i> حفظ وإضافة جديد
                                </button>
                            </div>
                            <div class="mb-2">
                                <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times me-2"></i> إلغاء
                                </a>
                            </div>
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
        // تحسين عرض/إخفاء حقول المخزون
        const stockableCheckbox = document.getElementById('ART_STOCKABLE');
        const stockFields = document.getElementById('stock-fields');
        
        function toggleStockFields(show, animate = true) {
            if (show) {
                stockFields.style.display = 'block';
                if (animate) {
                    $(stockFields).fadeIn(400);
                }
            } else {
                if (animate) {
                    $(stockFields).fadeOut(300);
                } else {
                    stockFields.style.display = 'none';
                }
            }
        }
        
        stockableCheckbox.addEventListener('change', function() {
            toggleStockFields(this.checked, true);
        });

        // تشغيل التحقق الأولي
        toggleStockFields(stockableCheckbox.checked, false);

        // تحسين فلترة الفئات الفرعية
        const familleSelect = document.getElementById('famille');
        const sousFamilleSelect = document.getElementById('sous_famille');
        const allSousFamilles = Array.from(sousFamilleSelect.options);
        
        familleSelect.addEventListener('change', function() {
            const selectedFamille = this.value;
            
            sousFamilleSelect.disabled = true;
            
            setTimeout(() => {
                sousFamilleSelect.innerHTML = '<option value="">اختر الفئة الفرعية</option>';
                
                allSousFamilles.forEach(function(option) {
                    if (option.value === '' || option.dataset.famille === selectedFamille) {
                        sousFamilleSelect.appendChild(option.cloneNode(true));
                    }
                });
                
                sousFamilleSelect.disabled = false;
            }, 200);
        });

        // حساب هامش الربح المحسن
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
                    <div class="d-flex align-items-center">
                        <div class="me-4">
                            <strong>الهامش:</strong> <span class="badge badge-primary">${margin.toFixed(1)}%</span>
                        </div>
                        <div>
                            <strong>الربح:</strong> <span class="badge badge-success">${profit.toFixed(2)} دج</span>
                        </div>
                    </div>
                `;
                
                if (marginDiv.style.display === 'none' || !marginDiv.style.display) {
                    $(marginDiv).fadeIn(400);
                }
                
                // تغيير اللون حسب الهامش
                marginDiv.className = 'price-preview border-left-' + (margin < 0 ? 'danger' : margin < 10 ? 'warning' : 'success');
            } else {
                if (marginDiv.style.display === 'block') {
                    $(marginDiv).fadeOut(300);
                }
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
                badges += '<span class="badge badge-success mr-1 mb-1"><span class="stock-indicator active"></span>نشط</span>';
            } else {
                badges += '<span class="badge badge-danger mr-1 mb-1"><span class="stock-indicator inactive"></span>غير نشط</span>';
            }
            
            if (isMenu) {
                badges += '<span class="badge badge-warning mr-1 mb-1">قائمة طعام</span>';
            }
            
            if (isStockable) {
                badges += '<span class="badge badge-info mr-1 mb-1">قابل للتخزين</span>';
            }
            
            previewBadges.innerHTML = badges;
        }
        
        // ربط أحداث التحديث
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
                    title: 'خطأ في البيانات',
                    text: 'الحد الأدنى للمخزون يجب أن يكون أقل من الحد الأقصى',
                    confirmButtonText: 'فهمت',
                    confirmButtonColor: '#4e73df'
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
                    icon: 'error',
                    title: 'حقل مطلوب',
                    text: 'يرجى إدخال اسم المنتج',
                    confirmButtonText: 'فهمت',
                    confirmButtonColor: '#4e73df'
                });
                document.getElementById('ART_DESIGNATION').focus();
                return false;
            }
            
            if (!artPrixVente || parseFloat(artPrixVente) <= 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'حقل مطلوب',
                    text: 'يرجى إدخال سعر البيع',
                    confirmButtonText: 'فهمت',
                    confirmButtonColor: '#4e73df'
                });
                document.getElementById('ART_PRIX_VENTE').focus();
                return false;
            }
            
            if (!artPrixAchat || parseFloat(artPrixAchat) < 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'حقل مطلوب',
                    text: 'يرجى إدخال سعر الشراء',
                    confirmButtonText: 'فهمت',
                    confirmButtonColor: '#4e73df'
                });
                document.getElementById('ART_PRIX_ACHAT').focus();
                return false;
            }
            
            // تأثير تحميل للأزرار
            const submitBtns = this.querySelectorAll('button[type="submit"]');
            submitBtns.forEach((btn) => {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الحفظ...';
            });
            
            // رسالة تأكيد
            Swal.fire({
                title: 'جاري حفظ المنتج...',
                text: 'يرجى الانتظار',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        });

        // تأثيرات fade-in للكروت
        $('.fade-in').each(function(i) {
            $(this).delay(i * 100).animate({opacity: 1}, 600);
        });

        // تحسين أداء الحقول النصية مع debouncing
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        const debouncedUpdatePreview = debounce(updatePreview, 300);
        const debouncedCalculateMargin = debounce(calculateMargin, 300);

        // ربط الـ debounced functions
        ['ART_DESIGNATION', 'ART_REF'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('input', debouncedUpdatePreview);
            }
        });

        ['ART_PRIX_ACHAT', 'ART_PRIX_VENTE'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('input', debouncedCalculateMargin);
            }
        });
    });
</script>
@endsection