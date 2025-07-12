<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.tableau-de-bord-moderne') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-cash-register"></i>
        </div>
        <div class="sidebar-brand-text mx-3">AccessPos <sup>Pro</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('admin.dashboard', 'admin.tableau-de-bord-moderne') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.tableau-de-bord-moderne') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Tableau de bord</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Gestion
    </div>

    <!-- Nav Item - Produits -->
    <li class="nav-item {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProducts"
            aria-expanded="{{ request()->routeIs('admin.articles.*') ? 'true' : 'false' }}" aria-controls="collapseProducts">
            <i class="fas fa-fw fa-box"></i>
            <span>Produits</span>
        </a>
        <div id="collapseProducts" class="collapse {{ request()->routeIs('admin.articles.*') ? 'show' : '' }}" aria-labelledby="headingProducts" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Gestion des produits:</h6>
                <a class="collapse-item {{ request()->routeIs('admin.articles.index') ? 'active' : '' }}" href="{{ route('admin.articles.index') }}">
                    <i class="fas fa-list"></i> Liste des produits
                </a>
                <a class="collapse-item {{ request()->routeIs('admin.articles.create') ? 'active' : '' }}" href="{{ route('admin.articles.create') }}">
                    <i class="fas fa-plus"></i> Ajouter un produit
                </a>
                @if(Route::has('admin.articles.analytics'))
                <a class="collapse-item {{ request()->routeIs('admin.articles.analytics') ? 'active' : '' }}" href="{{ route('admin.articles.analytics') }}">
                    <i class="fas fa-chart-bar"></i> Analytiques
                </a>
                @endif
                <div class="collapse-divider"></div>
                <h6 class="collapse-header">Gestion du stock:</h6>
                @if(Route::has('admin.stock.index'))
                <a class="collapse-item" href="{{ route('admin.stock.index') }}">
                    <i class="fas fa-warehouse"></i> Gestion du stock
                </a>
                @endif
                @if(Route::has('admin.stock.mouvement'))
                <a class="collapse-item" href="{{ route('admin.stock.mouvement') }}">
                    <i class="fas fa-exchange-alt"></i> Mouvements de stock
                </a>
                @endif
            </div>
        </div>
    </li>

    <!-- Nav Item - Ventes -->
    @if(Route::has('admin.ventes.index'))
    <li class="nav-item {{ request()->routeIs('admin.ventes.*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSales"
            aria-expanded="{{ request()->routeIs('admin.ventes.*') ? 'true' : 'false' }}" aria-controls="collapseSales">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>Ventes</span>
        </a>
        <div id="collapseSales" class="collapse {{ request()->routeIs('admin.ventes.*') ? 'show' : '' }}" aria-labelledby="headingSales" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Gestion des ventes:</h6>
                <a class="collapse-item" href="{{ route('admin.ventes.index') }}">
                    <i class="fas fa-list"></i> Liste des ventes
                </a>
                <a class="collapse-item" href="{{ route('admin.ventes.create') }}">
                    <i class="fas fa-plus"></i> Nouvelle vente
                </a>
                <a class="collapse-item" href="{{ route('admin.ventes.pos') }}">
                    <i class="fas fa-cash-register"></i> Point de vente
                </a>
            </div>
        </div>
    </li>
    @endif

    <!-- Nav Item - Clients -->
    @if(Route::has('admin.clients.index'))
    <li class="nav-item {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.clients.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Clients</span>
        </a>
    </li>
    @endif

    <!-- Nav Item - Fournisseurs -->
    @if(Route::has('admin.fournisseurs.index'))
    <li class="nav-item {{ request()->routeIs('admin.fournisseurs.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.fournisseurs.index') }}">
            <i class="fas fa-fw fa-truck"></i>
            <span>Fournisseurs</span>
        </a>
    </li>
    @endif

    <!-- Nav Item - Rapports Détaillés -->
    <li class="nav-item {{ request()->routeIs('admin.dashboard.*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDetails"
            aria-expanded="{{ request()->routeIs('admin.dashboard.*') ? 'true' : 'false' }}" aria-controls="collapseDetails">
            <i class="fas fa-fw fa-chart-pie"></i>
            <span>Analyses Détaillées</span>
        </a>
        <div id="collapseDetails" class="collapse {{ request()->routeIs('admin.dashboard.*') ? 'show' : '' }}" aria-labelledby="headingDetails" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Analyses financières:</h6>
                <a class="collapse-item {{ request()->routeIs('admin.dashboard.chiffre-affaires') ? 'active' : '' }}" href="{{ route('admin.dashboard.chiffre-affaires') }}">
                    <i class="fas fa-chart-line"></i> Chiffre d'affaires
                </a>
                <a class="collapse-item {{ request()->routeIs('admin.dashboard.modes-paiement') ? 'active' : '' }}" href="{{ route('admin.dashboard.modes-paiement') }}">
                    <i class="fas fa-credit-card"></i> Modes de paiement
                </a>
                <div class="collapse-divider"></div>
                <h6 class="collapse-header">Analyses opérationnelles:</h6>
                <a class="collapse-item {{ request()->routeIs('admin.dashboard.stock-rupture') ? 'active' : '' }}" href="{{ route('admin.dashboard.stock-rupture') }}">
                    <i class="fas fa-exclamation-triangle"></i> Stock en rupture
                </a>
                <a class="collapse-item {{ request()->routeIs('admin.dashboard.performance-horaire') ? 'active' : '' }}" href="{{ route('admin.dashboard.performance-horaire') }}">
                    <i class="fas fa-clock"></i> Performance horaire
                </a>
                <a class="collapse-item {{ request()->routeIs('admin.dashboard.etat-tables') ? 'active' : '' }}" href="{{ route('admin.dashboard.etat-tables') }}">
                    <i class="fas fa-table"></i> État des tables
                </a>
                <div class="collapse-divider"></div>
                <h6 class="collapse-header">Analyses client:</h6>
                <a class="collapse-item {{ request()->routeIs('admin.dashboard.top-clients') ? 'active' : '' }}" href="{{ route('admin.dashboard.top-clients') }}">
                    <i class="fas fa-users"></i> Top clients
                </a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Système
    </div>

    <!-- Nav Item - Paramètres -->
    @if(Route::has('admin.parametres.index'))
    <li class="nav-item {{ request()->routeIs('admin.parametres.*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSettings"
            aria-expanded="{{ request()->routeIs('admin.parametres.*') ? 'true' : 'false' }}" aria-controls="collapseSettings">
            <i class="fas fa-fw fa-cog"></i>
            <span>Paramètres</span>
        </a>
        <div id="collapseSettings" class="collapse {{ request()->routeIs('admin.parametres.*') ? 'show' : '' }}" aria-labelledby="headingSettings" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Configuration:</h6>
                <a class="collapse-item" href="{{ route('admin.parametres.index') }}">
                    <i class="fas fa-cogs"></i> Paramètres généraux
                </a>
                <a class="collapse-item" href="{{ route('admin.parametres.entreprise') }}">
                    <i class="fas fa-building"></i> Informations entreprise
                </a>
                <a class="collapse-item" href="{{ route('admin.parametres.taxes') }}">
                    <i class="fas fa-percentage"></i> Taxes et TVA
                </a>
            </div>
        </div>
    </li>
    @endif

    <!-- Nav Item - Utilisateurs -->
    @if(Route::has('admin.users.index'))
    <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.users.index') }}">
            <i class="fas fa-fw fa-users-cog"></i>
            <span>Utilisateurs</span>
        </a>
    </li>
    @endif

    <!-- Nav Item - Sauvegardes -->
    @if(Route::has('admin.backup.index'))
    <li class="nav-item {{ request()->routeIs('admin.backup.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.backup.index') }}">
            <i class="fas fa-fw fa-database"></i>
            <span>Sauvegardes</span>
        </a>
    </li>
    @endif

    <!-- Nav Item - Journal d'activité -->
    @if(Route::has('admin.logs.index'))
    <li class="nav-item {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.logs.index') }}">
            <i class="fas fa-fw fa-history"></i>
            <span>Journal d'activité</span>
        </a>
    </li>
    @endif

    <!-- Nav Item - Tests & QA (TASK 13) -->
    <li class="nav-item {{ request()->routeIs('admin.*-test') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTesting"
            aria-expanded="{{ request()->routeIs('admin.*-test') ? 'true' : 'false' }}" aria-controls="collapseTesting">
            <i class="fas fa-fw fa-flask"></i>
            <span>Tests & Qualité</span>
        </a>
        <div id="collapseTesting" class="collapse {{ request()->routeIs('admin.*-test') ? 'show' : '' }}" aria-labelledby="headingTesting" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Tests Fonctionnels:</h6>
                {{-- <a class="collapse-item {{ request()->routeIs('admin.test-pages') ? 'active' : '' }}" href="{{ route('admin.test-pages') }}">
                    <i class="fas fa-globe"></i> Test des Pages
                </a> --}}
                {{-- <a class="collapse-item {{ request()->routeIs('admin.responsive-test') ? 'active' : '' }}" href="{{ route('admin.responsive-test') }}">
                    <i class="fas fa-mobile-alt"></i> Test Responsive
                </a> --}}
                {{-- <a class="collapse-item {{ request()->routeIs('admin.forms-test') ? 'active' : '' }}" href="{{ route('admin.forms-test') }}">
                    <i class="fas fa-clipboard-list"></i> Test des Forms
                </a> --}}
                <div class="collapse-divider"></div>
                <h6 class="collapse-header">Tests Techniques:</h6>
                {{-- <a class="collapse-item {{ request()->routeIs('admin.javascript-test') ? 'active' : '' }}" href="{{ route('admin.javascript-test') }}">
                    <i class="fab fa-js-square"></i> Test JavaScript
                </a>
                <a class="collapse-item {{ request()->routeIs('admin.console-errors-test') ? 'active' : '' }}" href="{{ route('admin.console-errors-test') }}">
                    <i class="fas fa-bug"></i> Erreurs Console
                </a> --}}
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

    <!-- Sidebar Message -->
    <div class="sidebar-card d-none d-lg-flex">
        <img class="sidebar-card-illustration mb-2" src="{{ asset('startbootstrap-sb-admin-2-gh-pages/img/undraw_rocket.svg') }}" alt="...">
        <p class="text-center mb-2"><strong>AccessPos Pro</strong> est votre solution complète de gestion!</p>
        <a class="btn btn-success btn-sm" href="#" data-toggle="modal" data-target="#supportModal">Support technique</a>
    </div>

</ul>

<!-- Support Modal -->
<div class="modal fade" id="supportModal" tabindex="-1" role="dialog" aria-labelledby="supportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supportModalLabel">Support technique</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Besoin d'aide avec AccessPos Pro ?</p>
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-book mr-2"></i> Documentation
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-video mr-2"></i> Tutoriels vidéo
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-envelope mr-2"></i> Contacter le support
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-phone mr-2"></i> Assistance téléphonique
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
