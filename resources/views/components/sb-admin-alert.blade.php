{{--
Component Alert unifié pour AccessPos Pro avec SB Admin 2
Usage:
@include('components.sb-admin-alert', [
    'type' => 'success',
    'title' => 'Succès!',
    'message' => 'Opération réalisée avec succès',
    'dismissible' => true
])
--}}

@php
    $type = $type ?? 'info'; // success, danger, warning, info, primary, secondary
    $title = $title ?? '';
    $message = $message ?? '';
    $dismissible = $dismissible ?? true;
    $icon = $icon ?? null;
    $autoHide = $autoHide ?? false;
    $delay = $delay ?? 5000;
    $alertId = $alertId ?? 'alert-' . Str::random(8);
    
    // Icônes par défaut selon le type
    $defaultIcons = [
        'success' => 'fas fa-check-circle',
        'danger' => 'fas fa-exclamation-circle',
        'warning' => 'fas fa-exclamation-triangle',
        'info' => 'fas fa-info-circle',
        'primary' => 'fas fa-info-circle',
        'secondary' => 'fas fa-info-circle'
    ];
    
    $iconClass = $icon ?? $defaultIcons[$type] ?? 'fas fa-info-circle';
@endphp

<div class="alert alert-{{ $type }} {{ $dismissible ? 'alert-dismissible' : '' }} fade show" 
     role="alert" 
     id="{{ $alertId }}"
     @if($autoHide) data-auto-hide="{{ $delay }}" @endif>
    
    {{-- Icône et contenu --}}
    <div class="d-flex align-items-start">
        <div class="alert-icon mr-3">
            <i class="{{ $iconClass }} fa-lg"></i>
        </div>
        <div class="alert-content flex-grow-1">
            @if($title)
                <h6 class="alert-heading mb-1">{{ $title }}</h6>
            @endif
            <div class="alert-message">
                {{ $message }}
                {{ $slot ?? '' }}
            </div>
        </div>
        
        {{-- Bouton de fermeture --}}
        @if($dismissible)
            <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        @endif
    </div>
    
    {{-- Barre de progression pour auto-hide --}}
    @if($autoHide)
        <div class="alert-progress">
            <div class="progress-bar"></div>
        </div>
    @endif
</div>

<style>
#{{ $alertId }} {
    border: none;
    border-radius: 0.5rem;
    border-left: 4px solid;
    position: relative;
    overflow: hidden;
}

#{{ $alertId }}.alert-success {
    background: linear-gradient(135deg, rgba(28, 200, 138, 0.1), rgba(28, 200, 138, 0.05));
    border-left-color: #1cc88a;
    color: #0c5460;
}

#{{ $alertId }}.alert-danger {
    background: linear-gradient(135deg, rgba(231, 74, 59, 0.1), rgba(231, 74, 59, 0.05));
    border-left-color: #e74a3b;
    color: #721c24;
}

#{{ $alertId }}.alert-warning {
    background: linear-gradient(135deg, rgba(246, 194, 62, 0.1), rgba(246, 194, 62, 0.05));
    border-left-color: #f6c23e;
    color: #856404;
}

#{{ $alertId }}.alert-info {
    background: linear-gradient(135deg, rgba(54, 185, 204, 0.1), rgba(54, 185, 204, 0.05));
    border-left-color: #36b9cc;
    color: #055160;
}

#{{ $alertId }}.alert-primary {
    background: linear-gradient(135deg, rgba(78, 115, 223, 0.1), rgba(78, 115, 223, 0.05));
    border-left-color: #4e73df;
    color: #1e2e6e;
}

#{{ $alertId }} .alert-icon {
    opacity: 0.7;
}

#{{ $alertId }} .alert-heading {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

#{{ $alertId }} .close {
    padding: 0.5rem;
    margin: -0.5rem -0.5rem -0.5rem auto;
    opacity: 0.7;
    transition: opacity 0.15s ease-in-out;
}

#{{ $alertId }} .close:hover {
    opacity: 1;
}

@if($autoHide)
#{{ $alertId }} .alert-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: rgba(0, 0, 0, 0.1);
}

#{{ $alertId }} .alert-progress .progress-bar {
    height: 100%;
    background: currentColor;
    opacity: 0.3;
    animation: progressBar{{ Str::studly($alertId) }} {{ $delay }}ms linear;
}

@keyframes progressBar{{ Str::studly($alertId) }} {
    from { width: 100%; }
    to { width: 0%; }
}
@endif

/* Animation d'entrée */
#{{ $alertId }} {
    animation: slideInAlert 0.3s ease-out;
}

@keyframes slideInAlert {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Animation de sortie */
#{{ $alertId }}.fade-out {
    animation: slideOutAlert 0.3s ease-in;
}

@keyframes slideOutAlert {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(100%);
    }
}

/* Responsive */
@media (max-width: 768px) {
    #{{ $alertId }} {
        margin-bottom: 1rem;
        font-size: 0.875rem;
    }
    
    #{{ $alertId }} .alert-icon {
        margin-right: 0.75rem;
    }
    
    #{{ $alertId }} .alert-heading {
        font-size: 1rem;
    }
}
</style>

@if($autoHide)
<script>
$(document).ready(function() {
    const alert = $('#{{ $alertId }}');
    
    setTimeout(function() {
        alert.addClass('fade-out');
        setTimeout(function() {
            alert.alert('close');
        }, 300);
    }, {{ $delay }});
});
</script>
@endif
