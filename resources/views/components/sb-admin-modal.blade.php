{{--
Component Modal unifié pour AccessPos Pro avec SB Admin 2
Usage:
@include('components.sb-admin-modal', [
    'modalId' => 'example-modal',
    'title' => 'Titre du modal',
    'size' => 'lg',
    'content' => 'Contenu du modal'
])
--}}

@php
    $modalId = $modalId ?? 'modal-' . Str::random(8);
    $title = $title ?? 'Modal';
    $size = $size ?? ''; // sm, lg, xl
    $content = $content ?? '';
    $footer = $footer ?? true;
    $closeButton = $closeButton ?? true;
    $backdrop = $backdrop ?? true;
    $keyboard = $keyboard ?? true;
    $centered = $centered ?? false;
    $scrollable = $scrollable ?? false;
    $ajaxUrl = $ajaxUrl ?? null;
    $submitUrl = $submitUrl ?? null;
    $submitMethod = $submitMethod ?? 'POST';
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" 
     aria-labelledby="{{ $modalId }}Label" aria-hidden="true"
     @if(!$backdrop) data-backdrop="static" @endif
     @if(!$keyboard) data-keyboard="false" @endif>
    <div class="modal-dialog {{ $size ? 'modal-' . $size : '' }} {{ $centered ? 'modal-dialog-centered' : '' }} {{ $scrollable ? 'modal-dialog-scrollable' : '' }}" role="document">
        <div class="modal-content">
            {{-- Header --}}
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="{{ $modalId }}Label">
                    <i class="fas fa-window-maximize mr-2"></i>{{ $title }}
                </h5>
                @if($closeButton)
                    <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                @endif
            </div>
            
            {{-- Body --}}
            <div class="modal-body" id="{{ $modalId }}-body">
                @if($ajaxUrl)
                    {{-- Contenu chargé via AJAX --}}
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Chargement...</span>
                        </div>
                        <p class="mt-2 text-muted">Chargement en cours...</p>
                    </div>
                @else
                    {{-- Contenu statique --}}
                    {{ $content }}
                    {{ $slot ?? '' }}
                @endif
            </div>
            
            {{-- Footer --}}
            @if($footer)
                <div class="modal-footer" id="{{ $modalId }}-footer">
                    @if($submitUrl)
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i>Annuler
                        </button>
                        <button type="button" class="btn btn-primary" onclick="submitModal{{ Str::studly($modalId) }}()">
                            <i class="fas fa-save mr-2"></i>Enregistrer
                        </button>
                    @else
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i>Fermer
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<style>
#{{ $modalId }} .modal-content {
    border: none;
    border-radius: 0.5rem;
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
    overflow: hidden;
}

#{{ $modalId }} .modal-header {
    background: linear-gradient(135deg, #4e73df, #2e59d9);
    border-bottom: none;
    padding: 1.5rem;
}

#{{ $modalId }} .modal-title {
    font-weight: 600;
    font-size: 1.1rem;
}

#{{ $modalId }} .modal-body {
    padding: 2rem;
    min-height: 100px;
}

#{{ $modalId }} .modal-footer {
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    padding: 1.5rem 2rem;
    background-color: #f8f9fc;
}

#{{ $modalId }} .close {
    color: white;
    opacity: 0.8;
    transition: opacity 0.15s ease-in-out;
    text-shadow: none;
}

#{{ $modalId }} .close:hover {
    opacity: 1;
    color: white;
}

#{{ $modalId }} .btn {
    border-radius: 0.35rem;
    font-weight: 600;
    transition: all 0.15s ease-in-out;
}

#{{ $modalId }} .btn:hover {
    transform: translateY(-1px);
}

/* Animation d'entrée */
#{{ $modalId }}.fade .modal-dialog {
    transition: transform 0.3s ease-out;
    transform: translate(0, -50px);
}

#{{ $modalId }}.show .modal-dialog {
    transform: none;
}

/* Responsive */
@media (max-width: 768px) {
    #{{ $modalId }} .modal-dialog {
        margin: 1rem;
        max-width: calc(100% - 2rem);
    }
    
    #{{ $modalId }} .modal-body {
        padding: 1.5rem;
    }
    
    #{{ $modalId }} .modal-footer {
        padding: 1rem 1.5rem;
        flex-direction: column-reverse;
    }
    
    #{{ $modalId }} .modal-footer .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    #{{ $modalId }} .modal-footer .btn:last-child {
        margin-bottom: 0;
    }
}
</style>

<script>
$(document).ready(function() {
    const modal = $('#{{ $modalId }}');
    
    @if($ajaxUrl)
    // Charger le contenu via AJAX lors de l'ouverture
    modal.on('show.bs.modal', function() {
        const modalBody = $('#{{ $modalId }}-body');
        
        $.ajax({
            url: '{{ $ajaxUrl }}',
            method: 'GET',
            success: function(response) {
                modalBody.html(response);
                // Réinitialiser les composants Bootstrap dans le modal
                modalBody.find('[data-toggle="tooltip"]').tooltip();
                modalBody.find('[data-toggle="popover"]').popover();
            },
            error: function() {
                modalBody.html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Erreur lors du chargement du contenu.
                    </div>
                `);
            }
        });
    });
    
    // Nettoyer le contenu lors de la fermeture
    modal.on('hidden.bs.modal', function() {
        $('#{{ $modalId }}-body').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Chargement...</span>
                </div>
                <p class="mt-2 text-muted">Chargement en cours...</p>
            </div>
        `);
    });
    @endif
    
    // Events personnalisés
    modal.on('show.bs.modal', function() {
        console.log('Modal {{ $modalId }} s\'ouvre');
        // Callback personnalisé
        if (typeof window.onModalShow{{ Str::studly($modalId) }} === 'function') {
            window.onModalShow{{ Str::studly($modalId) }}();
        }
    });
    
    modal.on('hidden.bs.modal', function() {
        console.log('Modal {{ $modalId }} fermé');
        // Callback personnalisé
        if (typeof window.onModalHidden{{ Str::studly($modalId) }} === 'function') {
            window.onModalHidden{{ Str::studly($modalId) }}();
        }
    });
});

// Fonctions utilitaires globales
window.modal{{ Str::studly($modalId) }} = {
    show: function() {
        $('#{{ $modalId }}').modal('show');
    },
    hide: function() {
        $('#{{ $modalId }}').modal('hide');
    },
    toggle: function() {
        $('#{{ $modalId }}').modal('toggle');
    },
    setTitle: function(title) {
        $('#{{ $modalId }}Label').html('<i class="fas fa-window-maximize mr-2"></i>' + title);
    },
    setContent: function(content) {
        $('#{{ $modalId }}-body').html(content);
    },
    showLoading: function() {
        $('#{{ $modalId }}-body').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Chargement...</span>
                </div>
                <p class="mt-2 text-muted">Chargement en cours...</p>
            </div>
        `);
    }
};

@if($submitUrl)
// Fonction de soumission pour modals avec formulaire
function submitModal{{ Str::studly($modalId) }}() {
    const form = $('#{{ $modalId }} form');
    const submitBtn = $('#{{ $modalId }}-footer .btn-primary');
    
    if (form.length === 0) {
        console.error('Aucun formulaire trouvé dans le modal');
        return;
    }
    
    // État de chargement
    submitBtn.prop('disabled', true);
    submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Enregistrement...');
    
    // Soumission AJAX
    const formData = new FormData(form[0]);
    
    $.ajax({
        url: '{{ $submitUrl }}',
        method: '{{ $submitMethod }}',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': AccessPos.config.csrfToken
        }
    })
    .done(function(response) {
        AccessPos.utils.showToast(response.message || 'Enregistrement réussi', 'success');
        
        // Fermer le modal
        $('#{{ $modalId }}').modal('hide');
        
        // Callback de succès
        if (typeof window.onModalSubmitSuccess{{ Str::studly($modalId) }} === 'function') {
            window.onModalSubmitSuccess{{ Str::studly($modalId) }}(response);
        }
        
        // Redirection si spécifiée
        if (response.redirect) {
            setTimeout(() => {
                window.location.href = response.redirect;
            }, 1000);
        }
    })
    .fail(function(xhr) {
        let errorMessage = 'Une erreur est survenue';
        
        if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
        }
        
        AccessPos.utils.showToast(errorMessage, 'danger');
    })
    .always(function() {
        // Restaurer le bouton
        submitBtn.prop('disabled', false);
        submitBtn.html('<i class="fas fa-save mr-2"></i>Enregistrer');
    });
}
@endif
</script>
