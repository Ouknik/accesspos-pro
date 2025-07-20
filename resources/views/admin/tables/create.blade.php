@extends('layouts.sb-admin')

@section('title', 'Ajouter une Nouvelle Table - AccessPos Pro')

@section('page-heading')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle text-primary"></i>
            Ajouter une Nouvelle Table
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.tableau-de-bord-moderne') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.tables.index') }}">Tables</a></li>
                <li class="breadcrumb-item active">Nouvelle</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.tables.index') }}" class="btn-modern btn-secondary-modern">
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
    
    .border-left-danger {
        border-left: 0.25rem solid #e74a3b !important;
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
    
    .table-visual {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.2rem;
        box-shadow: 0 0.5rem 1rem rgba(78, 115, 223, 0.3);
        transition: all 0.3s ease;
        margin: 0 auto;
    }
    
    .table-visual:hover {
        transform: scale(1.05) rotate(5deg);
        box-shadow: 0 1rem 2rem rgba(78, 115, 223, 0.4);
    }
    
    .preview-container {
        background: linear-gradient(135deg, #f8f9fc 0%, #e2e6ea 100%);
        border-left: 0.25rem solid #1cc88a;
        padding: 1.5rem;
        border-radius: 0.35rem;
        text-align: center;
    }
    
    .preview-name {
        font-weight: 600;
        color: #5a5c69;
        margin-top: 1rem;
        font-size: 1.1rem;
    }
    
    .zone-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
        background-color: #36b9cc;
    }
    
    .info-box {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
        border-left: 0.25rem solid #36b9cc;
        padding: 1.5rem;
        border-radius: 0.35rem;
        margin-top: 1rem;
    }
    
    .tip-box {
        background: linear-gradient(135deg, #f3e5f5 0%, #e8f5e8 100%);
        border-left: 0.25rem solid #1cc88a;
        padding: 1rem;
        border-radius: 0.35rem;
        margin-bottom: 1rem;
    }
    
    .input-group-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Messages de validation --}}
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-left-danger" role="alert">
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

    <form method="POST" action="{{ route('admin.tables.store') }}" id="tableForm">
        @csrf
        
        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <!-- Informations de base -->
                <div class="card shadow mb-4 fade-in hover-shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-utensils"></i> Informations de la Table
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tab_lib" class="form-label font-weight-bold text-dark">
                                        Nom/Numéro de la table <span class="required">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('tab_lib') is-invalid @enderror" 
                                           id="tab_lib" name="tab_lib" 
                                           value="{{ old('tab_lib') }}" 
                                           required
                                           placeholder="Exemple: Table n°1, Table VIP">
                                    <small class="help-text">
                                        <i class="fas fa-info-circle text-primary"></i>
                                        Nom distinctif pour identifier facilement la table
                                    </small>
                                    @error('tab_lib')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="zon_ref" class="form-label font-weight-bold text-dark">
                                        Zone <span class="required">*</span>
                                    </label>
                                    <select class="form-control @error('zon_ref') is-invalid @enderror" 
                                            id="zon_ref" name="zon_ref" required>
                                        <option value="">Choisir la zone</option>
                                        @foreach($zones as $zone)
                                            <option value="{{ $zone->ZON_REF }}" 
                                                    {{ old('zon_ref') == $zone->ZON_REF ? 'selected' : '' }}>
                                                {{ $zone->ZON_LIB }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="help-text">
                                        <i class="fas fa-map-marker-alt text-info"></i>
                                        Zone à laquelle appartient la table
                                    </small>
                                    @error('zon_ref')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tab_nbr_couvert" class="form-label font-weight-bold text-dark">
                                        Nombre de couverts <span class="required">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('tab_nbr_couvert') is-invalid @enderror" 
                                               id="tab_nbr_couvert" name="tab_nbr_couvert" 
                                               value="{{ old('tab_nbr_couvert', 4) }}" 
                                               required min="1" max="50">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fas fa-chair"></i></span>
                                        </div>
                                    </div>
                                    <small class="help-text">
                                        <i class="fas fa-users text-warning"></i>
                                        Nombre de personnes pouvant être accueillies
                                    </small>
                                    @error('tab_nbr_couvert')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tab_descript" class="form-label font-weight-bold text-dark">
                                        Description
                                    </label>
                                    <textarea class="form-control @error('tab_descript') is-invalid @enderror" 
                                              id="tab_descript" name="tab_descript" rows="3"
                                              placeholder="Description supplémentaire de la table...">{{ old('tab_descript') }}</textarea>
                                    <small class="help-text">
                                        <i class="fas fa-align-left text-secondary"></i>
                                        Informations supplémentaires (optionnel)
                                    </small>
                                    @error('tab_descript')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations importantes -->
                <div class="card shadow mb-4 fade-in hover-shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle"></i> Informations Importantes
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="info-box">
                            <h6 class="font-weight-bold text-info mb-3">
                                <i class="fas fa-lightbulb"></i> À retenir
                            </h6>
                            <ul class="mb-0">
                                <li>La nouvelle table sera en état "libre" par défaut</li>
                                <li>Vous pouvez changer l'état de la table depuis la liste des tables</li>
                                <li>Le nom de la table doit être distinctif et facile à retenir</li>
                                <li>Le nombre de couverts peut être modifié ultérieurement</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panneau latéral -->
            <div class="col-xl-4 col-lg-5">
                <!-- Aperçu de la table -->
                <div class="card shadow mb-4 fade-in hover-shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-eye"></i> Aperçu de la Table
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="preview-container">
                            <div id="table-preview">
                                <div class="table-visual">
                                    <span id="preview-seats">4</span>
                                </div>
                            </div>
                            <div class="preview-name" id="preview-name">Nouvelle table</div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <span class="zone-indicator"></span>
                                    Zone: <span id="preview-zone">Non sélectionnée</span>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conseils -->
                <div class="card shadow mb-4 fade-in hover-shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-lightbulb"></i> Conseils de Gestion
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="tip-box">
                            <h6 class="font-weight-bold text-success mb-2">
                                <i class="fas fa-hashtag"></i> Nommage des tables
                            </h6>
                            <ul class="mb-0 small">
                                <li>Utilisez des numéros consécutifs (Table 1, Table 2)</li>
                                <li>Ou des noms distinctifs (Table fenêtre, Table royale)</li>
                                <li>Évitez les noms similaires</li>
                            </ul>
                        </div>
                        
                        <div class="tip-box">
                            <h6 class="font-weight-bold text-info mb-2">
                                <i class="fas fa-users"></i> Nombre de couverts
                            </h6>
                            <ul class="mb-0 small">
                                <li>Soyez précis dans la définition</li>
                                <li>Pensez à l'espace disponible</li>
                                <li>Peut être modifié ultérieurement</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons de sauvegarde -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4 fade-in">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-save"></i> Enregistrer la Table
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap">
                            <div class="mb-2">
                                <button type="submit" class="btn btn-success btn-lg mr-2" id="saveBtn">
                                    <i class="fas fa-save me-2"></i> Enregistrer la table
                                </button>
                                <button type="submit" name="save_and_new" value="1" class="btn btn-primary btn-lg mr-2" id="saveNewBtn">
                                    <i class="fas fa-plus me-2"></i> Enregistrer et créer une nouvelle
                                </button>
                            </div>
                            <div class="mb-2">
                                <a href="{{ route('admin.tables.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times me-2"></i> Annuler
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
        // معاينة محسنة مع تأثيرات
        const previewName = document.getElementById('preview-name');
        const previewSeats = document.getElementById('preview-seats');
        const previewZone = document.getElementById('preview-zone');
        
        function updatePreview() {
            const name = document.getElementById('tab_lib').value || 'Nouvelle table';
            const seats = document.getElementById('tab_nbr_couvert').value || '4';
            const zoneSelect = document.getElementById('zon_ref');
            const zoneName = zoneSelect.options[zoneSelect.selectedIndex].text || 'Non sélectionnée';
            
            previewName.textContent = name;
            previewSeats.textContent = seats;
            if (zoneSelect.value) {
                previewZone.textContent = zoneName;
                previewZone.parentElement.style.color = '#1cc88a';
            } else {
                previewZone.textContent = 'Non sélectionnée';
                previewZone.parentElement.style.color = '#858796';
            }
        }
        
        // ربط أحداث التحديث
        ['tab_lib', 'tab_nbr_couvert', 'zon_ref'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener(element.tagName === 'SELECT' ? 'change' : 'input', updatePreview);
            }
        });

        // تشغيل المعاينة الأولى
        updatePreview();

        // تحسين التحقق من صحة النموذج
        document.getElementById('tableForm').addEventListener('submit', function(e) {
            // التحقق من الحقول المطلوبة
            const tableName = document.getElementById('tab_lib').value.trim();
            const zone = document.getElementById('zon_ref').value;
            const seats = document.getElementById('tab_nbr_couvert').value;
            
            if (!tableName) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Champ requis',
                    text: 'Veuillez saisir le nom de la table',
                    confirmButtonText: 'Compris',
                    confirmButtonColor: '#4e73df'
                });
                document.getElementById('tab_lib').focus();
                return false;
            }
            
            if (!zone) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Champ requis',
                    text: 'Veuillez choisir la zone',
                    confirmButtonText: 'Compris',
                    confirmButtonColor: '#4e73df'
                });
                document.getElementById('zon_ref').focus();
                return false;
            }
            
            if (!seats || parseInt(seats) < 1) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Valeur invalide',
                    text: 'Veuillez saisir un nombre valide de couverts (minimum 1)',
                    confirmButtonText: 'Compris',
                    confirmButtonColor: '#4e73df'
                });
                document.getElementById('tab_nbr_couvert').focus();
                return false;
            }
            
            if (parseInt(seats) > 50) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Nombre trop élevé',
                    text: 'Le nombre de couverts ne peut pas dépasser 50',
                    confirmButtonText: 'Compris',
                    confirmButtonColor: '#4e73df'
                });
                document.getElementById('tab_nbr_couvert').focus();
                return false;
            }
            
            // تأثير تحميل للأزرار
            const submitBtns = this.querySelectorAll('button[type="submit"]');
            submitBtns.forEach((btn) => {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enregistrement...';
            });
            
            // رسالة تأكيد
            Swal.fire({
                title: 'Enregistrement de la table...',
                text: 'Veuillez patienter',
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

        // ربط الـ debounced functions
        ['tab_lib'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('input', debouncedUpdatePreview);
            }
        });

        // تحسين تفاعل الحقول
        $('input, select, textarea').on('focus', function() {
            $(this).closest('.mb-3').addClass('focused');
            $(this).parent().addClass('focused');
        }).on('blur', function() {
            $(this).closest('.mb-3').removeClass('focused');
            $(this).parent().removeClass('focused');
        });

        // تأثيرات hover للكروت
        $('.hover-shadow').hover(
            function() {
                $(this).addClass('shadow-lg');
            },
            function() {
                $(this).removeClass('shadow-lg');
            }
        );

        // تحسين حقل عدد الكراسي بالتحقق المباشر
        document.getElementById('tab_nbr_couvert').addEventListener('input', function() {
            const value = parseInt(this.value);
            const tableVisual = document.querySelector('.table-visual');
            
            if (value > 0 && value <= 50) {
                // تغيير لون الطاولة حسب العدد
                if (value <= 4) {
                    tableVisual.style.background = 'linear-gradient(135deg, #1cc88a 0%, #13855c 100%)';
                } else if (value <= 8) {
                    tableVisual.style.background = 'linear-gradient(135deg, #f6c23e 0%, #dda20a 100%)';
                } else {
                    tableVisual.style.background = 'linear-gradient(135deg, #e74a3b 0%, #c0392b 100%)';
                }
            } else {
                // اللون الافتراضي
                tableVisual.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            }
        });

        // تشغيل التحقق الأولي لعدد الكراسي
        document.getElementById('tab_nbr_couvert').dispatchEvent(new Event('input'));
    });
</script>

<style>
.focused {
    transform: translateY(-2px);
    transition: all 0.2s ease;
}

.focused .form-control,
.focused .form-select {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.shadow-lg {
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
}

.btn {
    transition: all 0.15s ease-in-out;
}

.btn:hover {
    transform: translateY(-2px);
}

/* Animation pour les éléments fade-in */
.fade-in {
    opacity: 0;
}

/* Amélioration des alertes */
.alert {
    border: none;
    border-radius: 0.35rem;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
}

/* Style pour les petits écrans */
@media (max-width: 768px) {
    .table-visual {
        width: 100px;
        height: 100px;
    }
    
    .btn-lg {
        padding: 0.5rem 1rem;
        font-size: 1rem;
    }
}
</style>
@endsection
