<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Search -->
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" placeholder="Rechercher des produits, clients..." 
                   aria-label="Search" aria-describedby="basic-addon2" id="globalSearch">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button" id="searchButton">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" 
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small" 
                               placeholder="Rechercher..." aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <!-- Nav Item - Alerts -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" 
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                <span class="badge badge-danger badge-counter" id="alertsCount">3+</span>
            </a>
            <!-- Dropdown - Alerts -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" 
                 aria-labelledby="alertsDropdown" style="max-width: 350px;">
                <h6 class="dropdown-header bg-primary text-white">
                    <i class="fas fa-bell mr-2"></i>
                    Centre d'alertes
                </h6>
                <div id="alertsList">
                    <!-- Stock faible -->
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.articles.index', ['filter' => 'low_stock']) }}">
                        <div class="mr-3">
                            <div class="icon-circle bg-warning">
                                <i class="fas fa-exclamation-triangle text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">{{ now()->format('d/m/Y') }}</div>
                            <span class="font-weight-bold">Stock faible détecté</span>
                            <div class="small text-gray-500">3 produits nécessitent un réapprovisionnement</div>
                        </div>
                    </a>
                    
                    <!-- Rupture de stock -->
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.articles.index', ['filter' => 'out_of_stock']) }}">
                        <div class="mr-3">
                            <div class="icon-circle bg-danger">
                                <i class="fas fa-times-circle text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">{{ now()->format('d/m/Y') }}</div>
                            <span class="font-weight-bold">Rupture de stock</span>
                            <div class="small text-gray-500">2 produits en rupture de stock</div>
                        </div>
                    </a>

                    <!-- Nouvelle commande -->
                    @if(Route::has('admin.commandes.index'))
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.commandes.index') }}">
                        <div class="mr-3">
                            <div class="icon-circle bg-success">
                                <i class="fas fa-shopping-cart text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">{{ now()->format('d/m/Y H:i') }}</div>
                            <span class="font-weight-bold">Nouvelle commande</span>
                            <div class="small text-gray-500">Commande #{{ str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) }} reçue</div>
                        </div>
                    </a>
                    @endif
                </div>
                <a class="dropdown-item text-center small text-gray-500" href="#" id="viewAllAlerts">
                    Voir toutes les alertes
                </a>
            </div>
        </li>

        <!-- Nav Item - Messages -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" 
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-envelope fa-fw"></i>
                <!-- Counter - Messages -->
                <span class="badge badge-primary badge-counter" id="messagesCount">7</span>
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" 
                 aria-labelledby="messagesDropdown" style="max-width: 350px;">
                <h6 class="dropdown-header bg-primary text-white">
                    <i class="fas fa-envelope mr-2"></i>
                    Centre de messages
                </h6>
                <div id="messagesList">
                    <a class="dropdown-item d-flex align-items-center" href="#">
                        <div class="dropdown-list-image mr-3">
                            <img class="rounded-circle" src="{{ asset('startbootstrap-sb-admin-2-gh-pages/img/undraw_profile_1.svg') }}" alt="...">
                            <div class="status-indicator bg-success"></div>
                        </div>
                        <div class="font-weight-bold">
                            <div class="text-truncate">Message du système : Mise à jour disponible</div>
                            <div class="small text-gray-500">AccessPos Team · {{ now()->subMinutes(15)->format('H:i') }}</div>
                        </div>
                    </a>
                    <a class="dropdown-item d-flex align-items-center" href="#">
                        <div class="dropdown-list-image mr-3">
                            <img class="rounded-circle" src="{{ asset('startbootstrap-sb-admin-2-gh-pages/img/undraw_profile_2.svg') }}" alt="...">
                            <div class="status-indicator"></div>
                        </div>
                        <div>
                            <div class="text-truncate">Sauvegarde automatique effectuée avec succès</div>
                            <div class="small text-gray-500">Système · {{ now()->subHours(1)->format('H:i') }}</div>
                        </div>
                    </a>
                </div>
                <a class="dropdown-item text-center small text-gray-500" href="#" id="viewAllMessages">
                    Lire tous les messages
                </a>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    {{ Auth::check() ? Auth::user()->name : 'Administrateur' }}
                </span>
                <img class="img-profile rounded-circle" 
                     src="{{ Auth::check() && Auth::user()->avatar ? asset(Auth::user()->avatar) : asset('startbootstrap-sb-admin-2-gh-pages/img/undraw_profile.svg') }}"
                     alt="Profile">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <div class="dropdown-header bg-primary text-white">
                    <i class="fas fa-user mr-2"></i>
                    {{ Auth::check() ? Auth::user()->name : 'Administrateur' }}
                </div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#profileModal">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Mon profil
                </a>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#settingsModal">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                    Préférences
                </a>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#activityModal">
                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                    Activité récente
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Déconnexion
                </a>
            </div>
        </li>

    </ul>

</nav>

<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="profileModalLabel">
                    <i class="fas fa-user mr-2"></i>Mon profil
                </h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img class="img-profile rounded-circle mb-3" style="width: 120px; height: 120px;"
                             src="{{ Auth::check() && Auth::user()->avatar ? asset(Auth::user()->avatar) : asset('startbootstrap-sb-admin-2-gh-pages/img/undraw_profile.svg') }}"
                             alt="Profile">
                        <h5>{{ Auth::check() ? Auth::user()->name : 'Administrateur' }}</h5>
                        <p class="text-muted">{{ Auth::check() ? Auth::user()->email : 'admin@accesspos.com' }}</p>
                    </div>
                    <div class="col-md-8">
                        <form>
                            <div class="form-group">
                                <label>Nom complet</label>
                                <input type="text" class="form-control" value="{{ Auth::check() ? Auth::user()->name : 'Administrateur' }}">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" value="{{ Auth::check() ? Auth::user()->email : 'admin@accesspos.com' }}">
                            </div>
                            <div class="form-group">
                                <label>Téléphone</label>
                                <input type="tel" class="form-control" value="{{ Auth::check() && Auth::user()->phone ? Auth::user()->phone : '+213 XXX XXX XXX' }}">
                            </div>
                            <div class="form-group">
                                <label>Poste</label>
                                <input type="text" class="form-control" value="{{ Auth::check() && Auth::user()->role ? Auth::user()->role : 'Administrateur système' }}">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Fermer</button>
                <button class="btn btn-primary" type="button">Sauvegarder les modifications</button>
            </div>
        </div>
    </div>
</div>

<!-- Settings Modal -->
<div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="settingsModalLabel">
                    <i class="fas fa-cogs mr-2"></i>Préférences
                </h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Langue d'interface</label>
                    <select class="form-control">
                        <option value="fr" selected>Français</option>
                        <option value="ar">العربية</option>
                        <option value="en">English</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fuseau horaire</label>
                    <select class="form-control">
                        <option value="Africa/Algiers" selected>Alger (GMT+1)</option>
                        <option value="Europe/Paris">Paris (GMT+1)</option>
                        <option value="UTC">UTC (GMT+0)</option>
                    </select>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="darkMode">
                        <label class="custom-control-label" for="darkMode">Mode sombre</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="notifications" checked>
                        <label class="custom-control-label" for="notifications">Notifications push</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
                <button class="btn btn-primary" type="button">Sauvegarder</button>
            </div>
        </div>
    </div>
</div>

<!-- Activity Modal -->
<div class="modal fade" id="activityModal" tabindex="-1" role="dialog" aria-labelledby="activityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="activityModalLabel">
                    <i class="fas fa-list mr-2"></i>Activité récente
                </h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Connexion au système</h6>
                            <p class="timeline-text">Connexion réussie depuis l'adresse IP 192.168.1.100</p>
                            <small class="text-muted">{{ now()->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Produit ajouté</h6>
                            <p class="timeline-text">Nouveau produit "Café Arabica" ajouté au catalogue</p>
                            <small class="text-muted">{{ now()->subMinutes(30)->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Stock mis à jour</h6>
                            <p class="timeline-text">Stock du produit "Thé vert" mis à jour (50 unités)</p>
                            <small class="text-muted">{{ now()->subHours(1)->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Recherche globale
    $('#globalSearch').on('keyup', function(e) {
        if (e.keyCode === 13) { // Enter key
            performGlobalSearch();
        }
    });
    
    $('#searchButton').on('click', function() {
        performGlobalSearch();
    });
    
    function performGlobalSearch() {
        const query = $('#globalSearch').val();
        if (query.length > 2) {
            // Effectuer la recherche
            console.log('Recherche: ' + query);
            // Ici vous pouvez ajouter votre logique de recherche AJAX
        }
    }
    
    // Mise à jour du compteur d'alertes
    function updateAlertsCount() {
        // Logique pour mettre à jour le nombre d'alertes
        // Cette fonction peut être appelée périodiquement
    }
    
    // Mise à jour du compteur de messages
    function updateMessagesCount() {
        // Logique pour mettre à jour le nombre de messages
    }
    
    // Actualisation automatique des alertes toutes les 5 minutes
    setInterval(function() {
        updateAlertsCount();
        updateMessagesCount();
    }, 300000);
});
</script>
