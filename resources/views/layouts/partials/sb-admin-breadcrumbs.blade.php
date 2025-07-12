{{-- Breadcrumbs pour SB Admin 2 AccessPos Pro --}}
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-light border rounded px-3 py-2 mb-4">
        {{-- Home/Dashboard toujours présent --}}
        <li class="breadcrumb-item">
            <a href="{{ route('admin.tableau-de-bord-moderne') }}" class="text-decoration-none">
                <i class="fas fa-home"></i> Accueil
            </a>
        </li>

        {{-- Dashboard --}}
        @if(request()->routeIs('admin.dashboard', 'admin.tableau-de-bord-moderne'))
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-tachometer-alt"></i> Tableau de bord
            </li>

        {{-- Articles/Produits --}}
        @elseif(request()->routeIs('admin.articles.*'))
            <li class="breadcrumb-item">
                <a href="{{ route('admin.articles.index') }}" class="text-decoration-none">
                    <i class="fas fa-box"></i> Produits
                </a>
            </li>
            @if(request()->routeIs('admin.articles.index'))
                <li class="breadcrumb-item active" aria-current="page">Liste des produits</li>
            @elseif(request()->routeIs('admin.articles.create'))
                <li class="breadcrumb-item active" aria-current="page">Ajouter un produit</li>
            @elseif(request()->routeIs('admin.articles.edit'))
                <li class="breadcrumb-item active" aria-current="page">Modifier le produit</li>
            @elseif(request()->routeIs('admin.articles.show'))
                <li class="breadcrumb-item active" aria-current="page">Détails du produit</li>
            @elseif(request()->routeIs('admin.articles.analytics'))
                <li class="breadcrumb-item active" aria-current="page">Analytiques des produits</li>
            @endif

        {{-- Rapports détaillés Dashboard --}}
        @elseif(request()->routeIs('admin.dashboard.*'))
            <li class="breadcrumb-item">
                <a href="{{ route('admin.tableau-de-bord-moderne') }}" class="text-decoration-none">
                    <i class="fas fa-chart-pie"></i> Analyses
                </a>
            </li>
            @if(request()->routeIs('admin.dashboard.chiffre-affaires'))
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-chart-line"></i> Chiffre d'affaires
                </li>
            @elseif(request()->routeIs('admin.dashboard.stock-rupture'))
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-exclamation-triangle"></i> Stock en rupture
                </li>
            @elseif(request()->routeIs('admin.dashboard.top-clients'))
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-users"></i> Top clients
                </li>
            @elseif(request()->routeIs('admin.dashboard.performance-horaire'))
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-clock"></i> Performance horaire
                </li>
            @elseif(request()->routeIs('admin.dashboard.modes-paiement'))
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-credit-card"></i> Modes de paiement
                </li>
            @elseif(request()->routeIs('admin.dashboard.etat-tables'))
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-table"></i> État des tables
                </li>
            @endif

        {{-- Rapports système --}}
        @elseif(request()->routeIs('admin.reports.*'))
            <li class="breadcrumb-item">
                <a href="{{ route('admin.reports.index') }}" class="text-decoration-none">
                    <i class="fas fa-chart-area"></i> Rapports
                </a>
            </li>
            @if(request()->routeIs('admin.reports.index'))
                <li class="breadcrumb-item active" aria-current="page">Tous les rapports</li>
            @elseif(request()->routeIs('admin.reports.complet'))
                <li class="breadcrumb-item active" aria-current="page">Rapport complet</li>
            @elseif(request()->routeIs('admin.reports.rapide'))
                <li class="breadcrumb-item active" aria-current="page">Rapport rapide</li>
            @endif

        {{-- Clients --}}
        @elseif(request()->routeIs('admin.clients.*'))
            <li class="breadcrumb-item">
                <a href="{{ route('admin.clients.index') }}" class="text-decoration-none">
                    <i class="fas fa-users"></i> Clients
                </a>
            </li>
            @if(request()->routeIs('admin.clients.index'))
                <li class="breadcrumb-item active" aria-current="page">Liste des clients</li>
            @elseif(request()->routeIs('admin.clients.create'))
                <li class="breadcrumb-item active" aria-current="page">Ajouter un client</li>
            @elseif(request()->routeIs('admin.clients.edit'))
                <li class="breadcrumb-item active" aria-current="page">Modifier le client</li>
            @elseif(request()->routeIs('admin.clients.show'))
                <li class="breadcrumb-item active" aria-current="page">Détails du client</li>
            @endif

        {{-- Ventes --}}
        @elseif(request()->routeIs('admin.ventes.*'))
            <li class="breadcrumb-item">
                <a href="{{ route('admin.ventes.index') }}" class="text-decoration-none">
                    <i class="fas fa-shopping-cart"></i> Ventes
                </a>
            </li>
            @if(request()->routeIs('admin.ventes.index'))
                <li class="breadcrumb-item active" aria-current="page">Liste des ventes</li>
            @elseif(request()->routeIs('admin.ventes.create'))
                <li class="breadcrumb-item active" aria-current="page">Nouvelle vente</li>
            @elseif(request()->routeIs('admin.ventes.show'))
                <li class="breadcrumb-item active" aria-current="page">Détails de la vente</li>
            @endif

        {{-- Achats --}}
        @elseif(request()->routeIs('admin.achats.*'))
            <li class="breadcrumb-item">
                <a href="{{ route('admin.achats.index') }}" class="text-decoration-none">
                    <i class="fas fa-shopping-bag"></i> Achats
                </a>
            </li>
            @if(request()->routeIs('admin.achats.index'))
                <li class="breadcrumb-item active" aria-current="page">Liste des achats</li>
            @elseif(request()->routeIs('admin.achats.create'))
                <li class="breadcrumb-item active" aria-current="page">Nouvel achat</li>
            @elseif(request()->routeIs('admin.achats.show'))
                <li class="breadcrumb-item active" aria-current="page">Détails de l'achat</li>
            @endif

        {{-- Fournisseurs --}}
        @elseif(request()->routeIs('admin.fournisseurs.*'))
            <li class="breadcrumb-item">
                <a href="{{ route('admin.fournisseurs.index') }}" class="text-decoration-none">
                    <i class="fas fa-truck"></i> Fournisseurs
                </a>
            </li>
            @if(request()->routeIs('admin.fournisseurs.index'))
                <li class="breadcrumb-item active" aria-current="page">Liste des fournisseurs</li>
            @elseif(request()->routeIs('admin.fournisseurs.create'))
                <li class="breadcrumb-item active" aria-current="page">Ajouter un fournisseur</li>
            @elseif(request()->routeIs('admin.fournisseurs.edit'))
                <li class="breadcrumb-item active" aria-current="page">Modifier le fournisseur</li>
            @elseif(request()->routeIs('admin.fournisseurs.show'))
                <li class="breadcrumb-item active" aria-current="page">Détails du fournisseur</li>
            @endif

        {{-- Restaurant/Tables --}}
        @elseif(request()->routeIs('admin.restaurant.*'))
            <li class="breadcrumb-item">
                <a href="{{ route('admin.restaurant.index') }}" class="text-decoration-none">
                    <i class="fas fa-utensils"></i> Restaurant
                </a>
            </li>
            @if(request()->routeIs('admin.restaurant.index'))
                <li class="breadcrumb-item active" aria-current="page">Gestion des tables</li>
            @elseif(request()->routeIs('admin.restaurant.reservations'))
                <li class="breadcrumb-item active" aria-current="page">Réservations</li>
            @endif

        {{-- Caisse --}}
        @elseif(request()->routeIs('admin.caisse.*'))
            <li class="breadcrumb-item">
                <a href="{{ route('admin.caisse.index') }}" class="text-decoration-none">
                    <i class="fas fa-cash-register"></i> Caisse
                </a>
            </li>
            @if(request()->routeIs('admin.caisse.index'))
                <li class="breadcrumb-item active" aria-current="page">Point de vente</li>
            @elseif(request()->routeIs('admin.caisse.historique'))
                <li class="breadcrumb-item active" aria-current="page">Historique des ventes</li>
            @endif

        {{-- Utilisateurs --}}
        @elseif(request()->routeIs('admin.users.*'))
            <li class="breadcrumb-item">
                <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                    <i class="fas fa-user-cog"></i> Utilisateurs
                </a>
            </li>
            @if(request()->routeIs('admin.users.index'))
                <li class="breadcrumb-item active" aria-current="page">Liste des utilisateurs</li>
            @elseif(request()->routeIs('admin.users.create'))
                <li class="breadcrumb-item active" aria-current="page">Ajouter un utilisateur</li>
            @elseif(request()->routeIs('admin.users.edit'))
                <li class="breadcrumb-item active" aria-current="page">Modifier l'utilisateur</li>
            @elseif(request()->routeIs('admin.users.profile'))
                <li class="breadcrumb-item active" aria-current="page">Mon profil</li>
            @endif

        {{-- Paramètres --}}
        @elseif(request()->routeIs('admin.settings.*'))
            <li class="breadcrumb-item">
                <a href="{{ route('admin.settings.index') }}" class="text-decoration-none">
                    <i class="fas fa-cog"></i> Paramètres
                </a>
            </li>
            @if(request()->routeIs('admin.settings.index'))
                <li class="breadcrumb-item active" aria-current="page">Configuration générale</li>
            @elseif(request()->routeIs('admin.settings.company'))
                <li class="breadcrumb-item active" aria-current="page">Informations entreprise</li>
            @elseif(request()->routeIs('admin.settings.taxes'))
                <li class="breadcrumb-item active" aria-current="page">Configuration des taxes</li>
            @endif

        {{-- Journal d'activité --}}
        @elseif(request()->routeIs('admin.logs.*'))
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-history"></i> Journal d'activité
            </li>

        {{-- Page par défaut --}}
        @else
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-file"></i> {{ $pageTitle ?? 'Page' }}
            </li>
        @endif
    </ol>
</nav>

{{-- Actions rapides selon la page --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        {{-- Actions contextuelles --}}
        @if(request()->routeIs('admin.articles.index'))
            <a href="{{ route('admin.articles.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Ajouter un produit
            </a>
            <button class="btn btn-success btn-sm" onclick="AccessPosArticles.exportArticles()">
                <i class="fas fa-file-excel"></i> Exporter
            </button>
        @elseif(request()->routeIs('admin.dashboard'))
            <button class="btn btn-info btn-sm" onclick="AccessPosDashboard.refreshLiveData()">
                <i class="fas fa-sync-alt"></i> Actualiser
            </button>
            <div class="btn-group btn-group-sm" role="group">
                <button class="btn btn-outline-secondary" onclick="AccessPosDashboard.exportData('excel')">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
                <button class="btn btn-outline-secondary" onclick="AccessPosDashboard.exportData('pdf')">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
            </div>
        @endif
    </div>
    
    {{-- Information de dernière mise à jour --}}
    <small class="text-muted">
        <i class="fas fa-clock"></i> 
        Dernière mise à jour: 
        <span id="last-update">{{ now()->format('H:i:s') }}</span>
    </small>
</div>

<style>
/* Styles personnalisés pour breadcrumbs */
.breadcrumb {
    background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
    border: 1px solid #e3e6f0;
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #858796;
    font-weight: bold;
}

.breadcrumb-item.active {
    color: #5a5c69;
    font-weight: 600;
}

.breadcrumb-item a {
    color: #4e73df;
    transition: color 0.15s ease-in-out;
}

.breadcrumb-item a:hover {
    color: #2e59d9;
    text-decoration: none;
}

/* Animation pour les actions rapides */
.btn-group .btn {
    transition: all 0.15s ease-in-out;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
}

/* Responsive pour mobile */
@media (max-width: 768px) {
    .breadcrumb {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn-group {
        width: 100%;
    }
    
    .btn-group .btn {
        flex: 1;
    }
}
</style>

<script>
// Mise à jour automatique de l'heure
setInterval(function() {
    const lastUpdate = document.getElementById('last-update');
    if (lastUpdate) {
        lastUpdate.textContent = new Date().toLocaleTimeString('fr-FR');
    }
}, 60000); // Mise à jour chaque minute
</script>
