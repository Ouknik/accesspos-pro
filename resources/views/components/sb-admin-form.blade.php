{{--
Component Form unifié pour AccessPos Pro avec SB Admin 2
Usage:
@include('components.sb-admin-form', [
    'formId' => 'example-form',
    'title' => 'Ajouter un élément',
    'action' => route('admin.items.store'),
    'method' => 'POST',
    'fields' => [...],
    'submitText' => 'Enregistrer'
])
--}}

@php
    $formId = $formId ?? 'form-' . Str::random(8);
    $title = $title ?? 'Formulaire';
    $description = $description ?? '';
    $action = $action ?? '#';
    $method = $method ?? 'POST';
    $enctype = $enctype ?? 'application/x-www-form-urlencoded';
    $fields = $fields ?? [];
    $submitText = $submitText ?? 'Enregistrer';
    $cancelUrl = $cancelUrl ?? null;
    $cardClass = $cardClass ?? 'shadow mb-4';
    $headerClass = $headerClass ?? 'py-3';
    $ajaxSubmit = $ajaxSubmit ?? false;
    $validation = $validation ?? true;
    $showProgress = $showProgress ?? true;
    $cols = $cols ?? 1; // Nombre de colonnes pour organiser les champs
@endphp

<div class="card {{ $cardClass }}">
    <div class="card-header {{ $headerClass }}">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-edit mr-2"></i>{{ $title }}
        </h6>
        @if($description)
            <p class="text-muted mb-0 mt-2">{{ $description }}</p>
        @endif
    </div>
    
    <div class="card-body">
        <form id="{{ $formId }}" 
              action="{{ $action }}" 
              method="{{ strtoupper($method) === 'GET' ? 'GET' : 'POST' }}"
              @if($enctype !== 'application/x-www-form-urlencoded') enctype="{{ $enctype }}" @endif
              @if($validation) data-validate="true" @endif
              @if($ajaxSubmit) data-ajax="true" @endif
              novalidate>
            
            @if(strtoupper($method) !== 'GET')
                @csrf
            @endif
            
            @if(!in_array(strtoupper($method), ['GET', 'POST']))
                @method($method)
            @endif
            
            {{-- Barre de progression pour soumission --}}
            @if($showProgress)
                <div class="progress mb-3" id="{{ $formId }}-progress" style="display: none; height: 4px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                         role="progressbar" style="width: 0%"></div>
                </div>
            @endif
            
            {{-- Organisation des champs en colonnes --}}
            <div class="row">
                @php
                    $fieldsPerCol = ceil(count($fields) / $cols);
                    $colClass = $cols == 1 ? 'col-12' : 'col-md-' . (12 / $cols);
                @endphp
                
                @for($colIndex = 0; $colIndex < $cols; $colIndex++)
                    <div class="{{ $colClass }}">
                        @for($fieldIndex = $colIndex * $fieldsPerCol; $fieldIndex < min(($colIndex + 1) * $fieldsPerCol, count($fields)); $fieldIndex++)
                            @php $field = $fields[$fieldIndex]; @endphp
                            
                            <div class="form-group mb-3">
                                {{-- Label --}}
                                @if($field['type'] !== 'hidden')
                                    <label for="{{ $field['id'] ?? $field['name'] }}" 
                                           class="form-label text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        {{ $field['label'] }}
                                        @if($field['required'] ?? false)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                @endif
                                
                                {{-- Champ selon le type --}}
                                @if($field['type'] === 'text' || $field['type'] === 'email' || $field['type'] === 'password' || $field['type'] === 'number' || $field['type'] === 'tel' || $field['type'] === 'url')
                                    <input type="{{ $field['type'] }}" 
                                           class="form-control {{ $field['class'] ?? '' }}"
                                           id="{{ $field['id'] ?? $field['name'] }}"
                                           name="{{ $field['name'] }}"
                                           value="{{ old($field['name'], $field['value'] ?? '') }}"
                                           placeholder="{{ $field['placeholder'] ?? '' }}"
                                           @if($field['required'] ?? false) required @endif
                                           @if($field['readonly'] ?? false) readonly @endif
                                           @if($field['min'] ?? false) min="{{ $field['min'] }}" @endif
                                           @if($field['max'] ?? false) max="{{ $field['max'] }}" @endif
                                           @if($field['step'] ?? false) step="{{ $field['step'] }}" @endif
                                           @foreach($field['attributes'] ?? [] as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach>
                                
                                @elseif($field['type'] === 'textarea')
                                    <textarea class="form-control {{ $field['class'] ?? '' }}"
                                              id="{{ $field['id'] ?? $field['name'] }}"
                                              name="{{ $field['name'] }}"
                                              rows="{{ $field['rows'] ?? 3 }}"
                                              placeholder="{{ $field['placeholder'] ?? '' }}"
                                              @if($field['required'] ?? false) required @endif
                                              @if($field['readonly'] ?? false) readonly @endif
                                              @foreach($field['attributes'] ?? [] as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach>{{ old($field['name'], $field['value'] ?? '') }}</textarea>
                                
                                @elseif($field['type'] === 'select')
                                    <select class="form-control {{ $field['class'] ?? '' }}"
                                            id="{{ $field['id'] ?? $field['name'] }}"
                                            name="{{ $field['name'] }}"
                                            @if($field['required'] ?? false) required @endif
                                            @if($field['multiple'] ?? false) multiple @endif
                                            @foreach($field['attributes'] ?? [] as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach>
                                        @if($field['placeholder'] ?? false)
                                            <option value="">{{ $field['placeholder'] }}</option>
                                        @endif
                                        @foreach($field['options'] ?? [] as $value => $label)
                                            <option value="{{ $value }}" 
                                                    @if(old($field['name'], $field['value'] ?? '') == $value) selected @endif>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                
                                @elseif($field['type'] === 'checkbox')
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               class="form-check-input {{ $field['class'] ?? '' }}"
                                               id="{{ $field['id'] ?? $field['name'] }}"
                                               name="{{ $field['name'] }}"
                                               value="{{ $field['checkValue'] ?? '1' }}"
                                               @if(old($field['name'], $field['value'] ?? false)) checked @endif
                                               @if($field['required'] ?? false) required @endif
                                               @foreach($field['attributes'] ?? [] as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach>
                                        <label class="form-check-label" for="{{ $field['id'] ?? $field['name'] }}">
                                            {{ $field['checkLabel'] ?? $field['label'] }}
                                        </label>
                                    </div>
                                
                                @elseif($field['type'] === 'radio')
                                    @foreach($field['options'] ?? [] as $value => $label)
                                        <div class="form-check">
                                            <input type="radio" 
                                                   class="form-check-input {{ $field['class'] ?? '' }}"
                                                   id="{{ $field['id'] ?? $field['name'] }}_{{ $value }}"
                                                   name="{{ $field['name'] }}"
                                                   value="{{ $value }}"
                                                   @if(old($field['name'], $field['value'] ?? '') == $value) checked @endif
                                                   @if($field['required'] ?? false) required @endif
                                                   @foreach($field['attributes'] ?? [] as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach>
                                            <label class="form-check-label" for="{{ $field['id'] ?? $field['name'] }}_{{ $value }}">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    @endforeach
                                
                                @elseif($field['type'] === 'file')
                                    <input type="file" 
                                           class="form-control-file {{ $field['class'] ?? '' }}"
                                           id="{{ $field['id'] ?? $field['name'] }}"
                                           name="{{ $field['name'] }}"
                                           @if($field['required'] ?? false) required @endif
                                           @if($field['multiple'] ?? false) multiple @endif
                                           @if($field['accept'] ?? false) accept="{{ $field['accept'] }}" @endif
                                           @foreach($field['attributes'] ?? [] as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach>
                                
                                @elseif($field['type'] === 'date' || $field['type'] === 'datetime-local' || $field['type'] === 'time')
                                    <input type="{{ $field['type'] }}" 
                                           class="form-control {{ $field['class'] ?? '' }}"
                                           id="{{ $field['id'] ?? $field['name'] }}"
                                           name="{{ $field['name'] }}"
                                           value="{{ old($field['name'], $field['value'] ?? '') }}"
                                           @if($field['required'] ?? false) required @endif
                                           @if($field['min'] ?? false) min="{{ $field['min'] }}" @endif
                                           @if($field['max'] ?? false) max="{{ $field['max'] }}" @endif
                                           @foreach($field['attributes'] ?? [] as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach>
                                
                                @elseif($field['type'] === 'hidden')
                                    <input type="hidden" 
                                           name="{{ $field['name'] }}"
                                           value="{{ old($field['name'], $field['value'] ?? '') }}">
                                
                                @elseif($field['type'] === 'custom')
                                    {{-- Pour des champs personnalisés --}}
                                    {!! $field['html'] ?? '' !!}
                                @endif
                                
                                {{-- Texte d'aide --}}
                                @if($field['help'] ?? false)
                                    <small class="form-text text-muted">{{ $field['help'] }}</small>
                                @endif
                                
                                {{-- Message d'erreur --}}
                                @if($field['type'] !== 'hidden')
                                    <div class="invalid-feedback">
                                        {{ $field['errorMessage'] ?? 'Ce champ est requis.' }}
                                    </div>
                                    @error($field['name'])
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                        @endfor
                    </div>
                @endfor
            </div>
            
            {{-- Slot pour contenu personnalisé --}}
            {{ $slot ?? '' }}
            
            {{-- Boutons d'action --}}
            <div class="form-group">
                <hr class="my-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        @if($cancelUrl)
                            <a href="{{ $cancelUrl }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-2"></i>Annuler
                            </a>
                        @endif
                    </div>
                    <div>
                        <button type="reset" class="btn btn-outline-secondary mr-2">
                            <i class="fas fa-undo mr-2"></i>Réinitialiser
                        </button>
                        <button type="submit" class="btn btn-primary" id="{{ $formId }}-submit">
                            <i class="fas fa-save mr-2"></i>{{ $submitText }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Styles CSS --}}
<style>
#{{ $formId }} .form-control {
    border-radius: 0.35rem;
    border: 2px solid #e3e6f0;
    transition: all 0.15s ease-in-out;
}

#{{ $formId }} .form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

#{{ $formId }} .form-control.is-valid {
    border-color: #1cc88a;
    box-shadow: 0 0 0 0.2rem rgba(28, 200, 138, 0.25);
}

#{{ $formId }} .form-control.is-invalid {
    border-color: #e74a3b;
    box-shadow: 0 0 0 0.2rem rgba(231, 74, 59, 0.25);
}

#{{ $formId }} .form-label {
    color: #5a5c69;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

#{{ $formId }} .form-check-input:checked {
    background-color: #4e73df;
    border-color: #4e73df;
}

#{{ $formId }} .btn {
    border-radius: 0.35rem;
    font-weight: 600;
    transition: all 0.15s ease-in-out;
}

#{{ $formId }} .btn:hover {
    transform: translateY(-1px);
}

#{{ $formId }} .progress {
    border-radius: 0;
    background-color: rgba(0, 0, 0, 0.05);
}

@media (max-width: 768px) {
    #{{ $formId }} .d-flex.justify-content-between {
        flex-direction: column-reverse;
        gap: 1rem;
    }
    
    #{{ $formId }} .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>

{{-- JavaScript --}}
<script>
$(document).ready(function() {
    const form = $('#{{ $formId }}');
    const submitButton = $('#{{ $formId }}-submit');
    const progressBar = $('#{{ $formId }}-progress');
    
    // Validation en temps réel
    @if($validation)
    form.find('input, select, textarea').on('blur', function() {
        validateField($(this));
    });
    
    form.find('input, select, textarea').on('input change', function() {
        const field = $(this);
        if (field.hasClass('is-invalid')) {
            validateField(field);
        }
    });
    @endif
    
    // Soumission du formulaire
    form.on('submit', function(e) {
        @if($validation)
        // Validation avant soumission
        let isValid = true;
        form.find('input, select, textarea').each(function() {
            if (!validateField($(this))) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            AccessPos.utils.showToast('Veuillez corriger les erreurs dans le formulaire', 'warning');
            return;
        }
        @endif
        
        @if($ajaxSubmit)
        e.preventDefault();
        submitFormAjax();
        @else
        // Soumission normale - afficher l'état de chargement
        showLoadingState();
        @endif
    });
    
    @if($validation)
    // Fonction de validation d'un champ
    function validateField(field) {
        const isValid = field[0].checkValidity();
        
        field.removeClass('is-valid is-invalid');
        field.addClass(isValid ? 'is-valid' : 'is-invalid');
        
        return isValid;
    }
    @endif
    
    // Afficher l'état de chargement
    function showLoadingState() {
        submitButton.prop('disabled', true);
        submitButton.html('<i class="fas fa-spinner fa-spin mr-2"></i>{{ $submitText }}...');
        
        @if($showProgress)
        progressBar.show();
        let progress = 0;
        const progressInterval = setInterval(function() {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            progressBar.find('.progress-bar').css('width', progress + '%');
        }, 200);
        
        // Nettoyer l'intervalle après 10 secondes
        setTimeout(() => clearInterval(progressInterval), 10000);
        @endif
    }
    
    // Masquer l'état de chargement
    function hideLoadingState() {
        submitButton.prop('disabled', false);
        submitButton.html('<i class="fas fa-save mr-2"></i>{{ $submitText }}');
        
        @if($showProgress)
        progressBar.hide();
        progressBar.find('.progress-bar').css('width', '0%');
        @endif
    }
    
    @if($ajaxSubmit)
    // Soumission AJAX
    function submitFormAjax() {
        showLoadingState();
        
        const formData = new FormData(form[0]);
        
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': AccessPos.config.csrfToken
            }
        })
        .done(function(response) {
            AccessPos.utils.showToast(response.message || 'Formulaire soumis avec succès', 'success');
            
            // Callback personnalisé si défini
            if (typeof window.onFormSuccess{{ Str::studly($formId) }} === 'function') {
                window.onFormSuccess{{ Str::studly($formId) }}(response);
            }
            
            // Redirection si spécifiée
            if (response.redirect) {
                setTimeout(() => {
                    window.location.href = response.redirect;
                }, 1500);
            }
        })
        .fail(function(xhr) {
            let errorMessage = 'Une erreur est survenue';
            
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                // Afficher les erreurs de validation
                const errors = xhr.responseJSON.errors;
                Object.keys(errors).forEach(function(field) {
                    const fieldElement = form.find(`[name="${field}"]`);
                    if (fieldElement.length) {
                        fieldElement.addClass('is-invalid');
                        fieldElement.siblings('.invalid-feedback').text(errors[field][0]);
                    }
                });
                errorMessage = 'Veuillez corriger les erreurs dans le formulaire';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            AccessPos.utils.showToast(errorMessage, 'danger');
        })
        .always(function() {
            hideLoadingState();
        });
    }
    @endif
    
    // Réinitialisation du formulaire
    form.find('button[type="reset"]').on('click', function() {
        form.find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
        setTimeout(() => {
            AccessPos.utils.showToast('Formulaire réinitialisé', 'info');
        }, 100);
    });
});

// Fonction globale pour accéder au formulaire depuis l'extérieur
window.form{{ Str::studly($formId) }} = {
    validate: function() {
        let isValid = true;
        $('#{{ $formId }}').find('input, select, textarea').each(function() {
            if (!$(this)[0].checkValidity()) {
                isValid = false;
                $(this).addClass('is-invalid');
            }
        });
        return isValid;
    },
    reset: function() {
        $('#{{ $formId }}')[0].reset();
        $('#{{ $formId }}').find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
    },
    getData: function() {
        return new FormData($('#{{ $formId }}')[0]);
    }
};
</script>
