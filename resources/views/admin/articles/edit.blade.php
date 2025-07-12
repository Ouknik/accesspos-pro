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
                    <h4 class="section-title">
                        <i class="fas fa-dollar-sign"></i>
                        Informations de prix
                    </h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3 change-indicator" data-original="{{ $article->ART_PRIX_ACHAT }}">
                                <label for="art_prix_achat" class="form-label">Prix d'achat</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('art_prix_achat') is-invalid @enderror" 
                                           id="art_prix_achat" name="art_prix_achat" 
                                           value="{{ old('art_prix_achat', $article->ART_PRIX_ACHAT) }}" 
                                           step="0.01" min="0">
                                    <span class="input-group-text">DA</span>
                                </div>
                                <div class="original-value">
                                    Valeur originale : {{ number_format($article->ART_PRIX_ACHAT ?? 0, 2) }} DA
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
                                    <span class="input-group-text">DA</span>
                                </div>
                                <div class="original-value">
                                    Valeur originale : {{ number_format($article->ART_PRIX_VENTE, 2) }} DA
                                </div>
                                @error('art_prix_vente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                                    <span class="input-group-text">دج</span>
                                </div>
                                <div class="original-value">
                                    القيمة الأصلية: {{ number_format($article->ART_PRIX_VENTE, 2) }} دج
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
                
                marginText.innerHTML = `Marge bénéficiaire : ${margin.toFixed(1)}% (${profit.toFixed(2)} DA)`;
                
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
                    <p class="text-warning"><strong>Modifications effectuées sur :</strong></p>
                    <ul class="list-unstyled">
                        ${changes.map(change => `<li><i class="fas fa-edit text-warning me-2"></i>${change}</li>`).join('')}
                    </ul>
                `;
                saveBtn.classList.remove('btn-success');
                saveBtn.classList.add('btn-warning');
                saveBtn.innerHTML = '<i class="fas fa-save me-2"></i>Enregistrer les modifications (' + changes.length + ')';
            } else {
                changesSummary.innerHTML = '<p class="text-muted">Aucune modification effectuée</p>';
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
                Swal.fire({
                    icon: 'warning',
                    title: 'Attention !',
                    text: 'Le stock minimum doit être inférieur au stock maximum',
                });
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
