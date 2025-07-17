@extends('layouts.sb-admin')

@section('title', 'Création de Rapport Excel Personnalisé - Papier de Travail')

@section('content')
<div class="container-fluid">
    <!-- Titre de la page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-excel text-success"></i>
            Rapports Excel Personnalisés
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.tableau-de-bord-moderne') }}">Tableau de Bord</a></li>
                <li class="breadcrumb-item active">Rapports Excel</li>
            </ol>
        </nav>
    </div>

    <!-- Cartes de rapports rapides -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Papier de Travail
                            </div>
                            <div class="text-xs text-gray-600 mb-2">Rapport complet d'inventaire et mouvement</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('admin.excel-reports.papier-de-travail') }}" class="btn btn-primary btn-sm btn-block">
                            <i class="fas fa-download"></i> Téléchargement Direct
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Inventaire en Valeur
                            </div>
                            <div class="text-xs text-gray-600 mb-2">Inventaire des stocks par valeur financière</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-warehouse fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button class="btn btn-success btn-sm btn-block" onclick="generateReport('inventory_value')">
                            <i class="fas fa-chart-bar"></i> Générer le Rapport
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                État de Sortie
                            </div>
                            <div class="text-xs text-gray-600 mb-2">Rapport des ventes et sorties</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button class="btn btn-info btn-sm btn-block" onclick="generateReport('sales_output')">
                            <i class="fas fa-chart-line"></i> Générer le Rapport
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Inventaire Physique
                            </div>
                            <div class="text-xs text-gray-600 mb-2">Comparaison stock théorique et réel</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button class="btn btn-warning btn-sm btn-block" onclick="generateReport('physical_inventory')">
                            <i class="fas fa-balance-scale"></i> Générer le Rapport
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire personnalisé pour les rapports -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs"></i>
                        Création de Rapport Personnalisé
                    </h6>
                    <span class="badge badge-info">Papier de Travail Custom</span>
                </div>
                <div class="card-body">
                    <form id="customReportForm" method="POST" action="{{ route('admin.excel-reports.generate') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-file-alt"></i>
                                        Type de Rapport:
                                    </label>
                                    <select name="report_type" class="form-control form-control-lg" required>
                                        <option value="">-- Choisir le type de rapport --</option>
                                        <option value="papier_travail">📋 Papier de Travail Complet</option>
                                        <option value="inventory_value">💰 Inventaire en Valeur Seulement</option>
                                        <option value="physical_inventory">📦 Inventaire Physique Seulement</option>
                                        <option value="sales_output">🛒 État de Sortie Seulement</option>
                                        <option value="reception_status">📥 État de Réception Seulement</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-calendar-alt"></i>
                                        Période:
                                    </label>
                                    <select name="period" id="period" class="form-control form-control-lg">
                                        <option value="today">📅 Aujourd'hui</option>
                                        <option value="this_week">📅 Cette Semaine</option>
                                        <option value="this_month" selected>📅 Ce Mois</option>
                                        <option value="last_month">📅 Mois Dernier</option>
                                        <option value="custom">📅 Période Personnalisée</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row" id="custom_dates" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-calendar-check"></i>
                                        Date de Début:
                                    </label>
                                    <input type="date" name="date_from" class="form-control form-control-lg" value="{{ date('Y-m-01') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-calendar-times"></i>
                                        Date de Fin:
                                    </label>
                                    <input type="date" name="date_to" class="form-control form-control-lg" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-sliders-h"></i>
                                        Options Supplémentaires:
                                    </label>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="include_images" class="custom-control-input" id="include_images">
                                                        <label class="custom-control-label" for="include_images">
                                                            <i class="fas fa-image text-primary"></i>
                                                            Inclure les Images des Produits
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="include_charts" class="custom-control-input" id="include_charts">
                                                        <label class="custom-control-label" for="include_charts">
                                                            <i class="fas fa-chart-pie text-success"></i>
                                                            Inclure les Graphiques
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="detailed_analysis" class="custom-control-input" id="detailed_analysis" checked>
                                                        <label class="custom-control-label" for="detailed_analysis">
                                                            <i class="fas fa-microscope text-info"></i>
                                                            Analyse Détaillée des Données
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="generateBtn">
                                <i class="fas fa-download"></i>
                                Générer et Télécharger le Rapport
                            </button>
                            <button type="button" class="btn btn-secondary btn-lg px-5 ml-2" onclick="resetForm()">
                                <i class="fas fa-undo"></i>
                                Réinitialiser
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations supplémentaires -->
    <div class="row">
        <div class="col-md-6">
            <div class="card border-left-success shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-info-circle"></i>
                        Informations sur les Rapports
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-clipboard-list text-primary"></i> Papier de Travail</span>
                            <span class="badge badge-primary badge-pill">4 feuilles</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-warehouse text-success"></i> Inventaire en Valeur</span>
                            <span class="badge badge-success badge-pill">1 feuille</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-boxes text-warning"></i> Inventaire Physique</span>
                            <span class="badge badge-warning badge-pill">1 feuille</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-shopping-cart text-info"></i> État de Sortie</span>
                            <span class="badge badge-info badge-pill">1 feuille</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-left-info shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-lightbulb"></i>
                        Conseils d'Utilisation
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small text-gray-600">
                        <p><i class="fas fa-check text-success"></i> Les rapports sont générés au format Excel professionnel</p>
                        <p><i class="fas fa-check text-success"></i> Données mises à jour en temps réel</p>
                        <p><i class="fas fa-check text-success"></i> Période personnalisable</p>
                        <p><i class="fas fa-check text-success"></i> Rapports prêts pour l'impression et le partage</p>
                        <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> Les gros rapports peuvent prendre plus de temps</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Afficher/masquer les dates personnalisées
document.getElementById('period').addEventListener('change', function() {
    const customDates = document.getElementById('custom_dates');
    if (this.value === 'custom') {
        customDates.style.display = 'block';
    } else {
        customDates.style.display = 'none';
    }
});

// Fonction de génération de rapport rapide
function generateReport(type) {
    const form = document.getElementById('customReportForm');
    form.querySelector('select[name="report_type"]').value = type;
    
    // Afficher le chargement
    const generateBtn = document.getElementById('generateBtn');
    generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Génération en cours...';
    generateBtn.disabled = true;
    
    form.submit();
}

// Réinitialiser le formulaire
function resetForm() {
    document.getElementById('customReportForm').reset();
    document.getElementById('custom_dates').style.display = 'none';
}

// Gestion de la soumission du formulaire
document.getElementById('customReportForm').addEventListener('submit', function() {
    const generateBtn = document.getElementById('generateBtn');
    generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Génération en cours...';
    generateBtn.disabled = true;
    
    // Réinitialiser le bouton après 5 secondes
    setTimeout(function() {
        generateBtn.innerHTML = '<i class="fas fa-download"></i> Générer et Télécharger le Rapport';
        generateBtn.disabled = false;
    }, 5000);
});
</script>
@endsection
