@extends('layouts.sb-admin')

@section('title', 'Nouvelle Facture - AccessPos Pro')

@push('styles')
<style>
    .create-facture-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .form-section {
        background: white;
        border-radius: 15px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 2rem;
        overflow: hidden;
    }
    
    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 1.5rem;
        margin: 0;
    }
    
    .section-header h6 {
        margin: 0;
        font-weight: 600;
    }
    
    .client-search {
        position: relative;
    }
    
    .client-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }
    
    .client-suggestion {
        padding: 0.75rem;
        cursor: pointer;
        border-bottom: 1px solid #f8f9fc;
    }
    
    .client-suggestion:hover {
        background: #f8f9fc;
    }
    
    .client-suggestion:last-child {
        border-bottom: none;
    }
    
    .article-search {
        position: relative;
    }
    
    .article-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }
    
    .article-suggestion {
        padding: 0.75rem;
        cursor: pointer;
        border-bottom: 1px solid #f8f9fc;
        display: flex;
        justify-content: between;
        align-items: center;
    }
    
    .article-suggestion:hover {
        background: #f8f9fc;
    }
    
    .article-row {
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 0;
        margin-bottom: 1rem;
    }
    
    .article-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    .totaux-section {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        color: white;
        border-radius: 10px;
        padding: 1.5rem;
    }
    
    .total-ligne {
        font-size: 1.1em;
        font-weight: bold;
    }
    
    .mode-paiement-section {
        background: #f8f9fc;
        border-radius: 10px;
        padding: 1rem;
        margin: 1rem 0;
    }
    
    .btn-add-article {
        background: linear-gradient(45deg, #36b9cc, #258391);
        border: none;
        color: white;
        border-radius: 25px;
        padding: 0.5rem 1.5rem;
        transition: all 0.3s ease;
    }
    
    .btn-add-article:hover {
        background: linear-gradient(45deg, #258391, #1e6b77);
        transform: translateY(-1px);
        color: white;
    }
    
    .btn-remove-article {
        color: #e74a3b;
        border: 1px solid #e74a3b;
        background: transparent;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .btn-remove-article:hover {
        background: #e74a3b;
        color: white;
    }
    
    .alert-from-command {
        background: linear-gradient(45deg, #f6c23e, #dda20a);
        color: white;
        border: none;
        border-radius: 10px;
    }
    
    .client-info-display {
        background: #e8f5e8;
        border: 1px solid #1cc88a;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
        display: none;
    }
    
    .new-client-form {
        background: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 1.5rem;
        margin-top: 1rem;
        display: none;
        animation: slideDown 0.3s ease;
    }
    
    .new-client-form.show {
        display: block;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .btn-new-client {
        background: linear-gradient(45deg, #36b9cc, #258391);
        border: none;
        color: white;
        border-radius: 20px;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }
    
    .btn-new-client:hover {
        background: linear-gradient(45deg, #258391, #1e6b77);
        transform: translateY(-1px);
        color: white;
    }
    
    .form-switch {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }
    
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 24px;
    }
    
    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    
    input:checked + .slider {
        background-color: #1cc88a;
    }
    
    input:checked + .slider:before {
        transform: translateX(26px);
    }
    
    .article-info-preview {
        background: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
        display: none;
    }
    
    .stock-warning {
        color: #e74a3b;
        font-size: 0.875rem;
    }
    
    .stock-ok {
        color: #1cc88a;
        font-size: 0.875rem;
    }
    
    .quick-add-buttons {
        margin-top: 1rem;
    }
    
    .quick-add-btn {
        margin: 0.25rem;
        border-radius: 20px;
        font-size: 0.875rem;
        background: #f8f9fc;
        border: 1px solid #e3e6f0;
        color: #5a5c69;
        transition: all 0.3s ease;
    }
    
    .quick-add-btn:hover {
        background: #e3e6f0;
        border-color: #d1d3e2;
        color: #3a3b45;
    }
    
    .floating-total {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #1cc88a;
        color: white;
        padding: 1rem;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        display: none;
    }
    
    .validation-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
    }
    
    .is-invalid {
        border-color: #e74a3b;
    }
    
    .is-valid {
        border-color: #1cc88a;
    }
    
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3498db;
        border-radius: 50%;
        animation: spin 2s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush

@section('content')
<div class="container-fluid create-facture-container">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus text-primary"></i>
            Nouvelle Facture
        </h1>
        <div class="d-none d-lg-inline-block">
            <span class="badge badge-info p-2">
                N° {{ $nouveauNumero }}
            </span>
        </div>
    </div>

    <!-- Alert si création depuis une commande -->
    @if($commande)
    <div class="alert alert-from-command" role="alert">
        <i class="fas fa-info-circle"></i>
        <strong>Création depuis la commande #{{ $commande['commande']->DVS_NUMERO }}</strong>
        <br>
        Client: {{ $commande['commande']->CLIENT_NAME }} | 
        Montant: {{ number_format($commande['commande']->DVS_MONTANT_TTC, 2) }} DH | 
        {{ count($commande['details']) }} articles
    </div>
    @endif

    <!-- Messages d'erreur -->
    @if($errors->any())
        <div class="alert alert-danger">
            <h6><i class="fas fa-exclamation-triangle"></i> Erreurs de validation:</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.factures.store') }}" id="factureForm">
        @csrf
        
        @if($commande)
            <input type="hidden" name="cmd_ref" value="{{ $commande['commande']->CMD_REF }}">
        @endif

        <div class="row">
            <!-- Colonne principale -->
            <div class="col-lg-8">
                <!-- Informations client -->
                <div class="form-section">
                    <div class="section-header">
                        <h6><i class="fas fa-user"></i> Informations Client</h6>
                    </div>
                    <div class="p-4">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="client_search" class="form-label mb-0">Client *</label>
                                    <button type="button" class="btn btn-new-client btn-sm" id="toggleNewClientForm">
                                        <i class="fas fa-plus"></i> Nouveau Client
                                    </button>
                                </div>
                                
                                <div class="client-search">
                                    <input type="text" class="form-control" id="client_search" 
                                           placeholder="Rechercher un client par nom, téléphone ou code..." autocomplete="off"
                                           value="{{ $commande ? $commande['commande']->CLIENT_NAME : old('client_search') }}">
                                    <input type="hidden" name="client_ref" id="client_ref" 
                                           value="{{ $commande ? $commande['commande']->CLT_REF : old('client_ref') }}">
                                    <div class="client-suggestions" id="clientSuggestions"></div>
                                </div>
                                
                                <!-- نموذج إنشاء عميل جديد -->
                                <div class="new-client-form" id="newClientForm">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 text-primary">
                                            <i class="fas fa-user-plus"></i> Nouveau Client
                                        </h6>
                                        <button type="button" class="btn btn-link text-muted" id="closeNewClientForm">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Nom du client *</label>
                                                <input type="text" class="form-control" id="new_client_nom" placeholder="Nom complet">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Téléphone *</label>
                                                <input type="text" class="form-control" id="new_client_telephone" placeholder="Numéro de téléphone">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Email</label>
                                                <input type="email" class="form-control" id="new_client_email" placeholder="Adresse email">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Civilité</label>
                                                <select class="form-control" id="new_client_civilite">
                                                    <option value="">Sélectionner...</option>
                                                    <option value="Mr">M.</option>
                                                    <option value="Mme">Mme</option>
                                                    <option value="Mlle">Mlle</option>
                                                    <option value="Dr">Dr</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group mb-3">
                                                <label>Raison sociale</label>
                                                <input type="text" class="form-control" id="new_client_raison_sociale" placeholder="Nom de l'entreprise">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label class="form-switch">
                                                    <span style="margin-right: 10px;">Entreprise</span>
                                                    <label class="switch">
                                                        <input type="checkbox" id="new_client_est_entreprise">
                                                        <span class="slider"></span>
                                                    </label>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="button" class="btn btn-success" id="saveNewClient">
                                            <i class="fas fa-save"></i> Enregistrer le client
                                        </button>
                                        <button type="button" class="btn btn-secondary ms-2" id="cancelNewClient">
                                            Annuler
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Informations client sélectionné -->
                                <div class="client-info-display" id="clientInfoDisplay">
                                    <div class="row">
                                        <div class="col-6">
                                            <strong id="clientName"></strong><br>
                                            <small class="text-muted">Code: <span id="clientRef"></span></small>
                                        </div>
                                        <div class="col-6 text-right">
                                            <small id="clientPhone"></small><br>
                                            <small id="clientEmail"></small>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <span class="badge" id="clientCreditBadge">Crédit: 0 DH</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="table_ref" class="form-label">Table</label>
                                <input type="text" class="form-control" name="table_ref" id="table_ref" 
                                       placeholder="N° Table" value="{{ $commande ? $commande['commande']->TAB_REF : old('table_ref') }}">
                                
                                <!-- Boutons rapides pour les tables courantes -->
                                <div class="quick-add-buttons">
                                    <button type="button" class="btn btn-sm quick-add-btn" onclick="setTable('T01')">Table 1</button>
                                    <button type="button" class="btn btn-sm quick-add-btn" onclick="setTable('T02')">Table 2</button>
                                    <button type="button" class="btn btn-sm quick-add-btn" onclick="setTable('T03')">Table 3</button>
                                    <button type="button" class="btn btn-sm quick-add-btn" onclick="setTable('COMP')">Comptoir</button>
                                    <button type="button" class="btn btn-sm quick-add-btn" onclick="setTable('EMP')">À emporter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Articles -->
                <div class="form-section">
                    <div class="section-header">
                        <h6><i class="fas fa-list"></i> Articles de la Facture</h6>
                    </div>
                    <div class="p-4">
                        <!-- Zone d'ajout d'article -->
                        <div class="row mb-4">
                            <div class="col-md-5">
                                <label class="form-label">Ajouter un article</label>
                                <div class="article-search">
                                    <input type="text" class="form-control" id="article_search" 
                                           placeholder="Rechercher par nom ou référence..." autocomplete="off">
                                    <div class="article-suggestions" id="articleSuggestions"></div>
                                </div>
                                
                                <!-- Aperçu de l'article sélectionné -->
                                <div class="article-info-preview" id="articleInfoPreview">
                                    <div class="row">
                                        <div class="col-8">
                                            <strong id="previewName"></strong><br>
                                            <small class="text-muted">Réf: <span id="previewRef"></span></small>
                                        </div>
                                        <div class="col-4 text-right">
                                            <strong id="previewPrice"></strong><br>
                                            <span id="stockStatus"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Quantité</label>
                                <input type="number" class="form-control" id="article_quantite" 
                                       value="1" min="0.01" step="0.01" onchange="checkStock()">
                                <div class="validation-feedback" id="quantiteValidation"></div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Prix unitaire</label>
                                <input type="number" class="form-control" id="article_prix" 
                                       value="0" min="0" step="0.01">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Remise</label>
                                <input type="number" class="form-control" id="article_remise" 
                                       value="0" min="0" step="0.01">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-add-article" onclick="addArticle()" id="btnAddArticle">
                                    <i class="fas fa-plus"></i> Ajouter
                                </button>
                            </div>
                        </div>
                        
                        <!-- Boutons rapides pour articles populaires -->
                        <div class="quick-add-buttons mb-3">
                            <small class="text-muted">Articles populaires:</small><br>
                            <button type="button" class="btn btn-sm quick-add-btn" onclick="quickAddArticle('CAFE', 'Café', 15.00)">
                                <i class="fas fa-coffee"></i> Café
                            </button>
                            <button type="button" class="btn btn-sm quick-add-btn" onclick="quickAddArticle('THE', 'Thé', 12.00)">
                                <i class="fas fa-leaf"></i> Thé
                            </button>
                            <button type="button" class="btn btn-sm quick-add-btn" onclick="quickAddArticle('CROIS', 'Croissant', 8.00)">
                                <i class="fas fa-bread-slice"></i> Croissant
                            </button>
                            <button type="button" class="btn btn-sm quick-add-btn" onclick="quickAddArticle('SAND', 'Sandwich', 25.00)">
                                <i class="fas fa-hamburger"></i> Sandwich
                            </button>
                        </div>

                        <!-- Liste des articles -->
                        <div id="articlesContainer">
                            @if($commande)
                                @foreach($commande['details'] as $index => $detail)
                                <div class="article-row" data-index="{{ $index }}">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <strong>{{ $detail->ART_DESIGNATION }}</strong>
                                            <br>
                                            <small class="text-muted">Réf: {{ $detail->ART_REF }}</small>
                                            <input type="hidden" name="articles[{{ $index }}][art_ref]" value="{{ $detail->ART_REF }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Quantité</label>
                                            <input type="number" class="form-control quantite-input" 
                                                   name="articles[{{ $index }}][quantite]" 
                                                   value="{{ $detail->CVD_QTE }}" 
                                                   min="0.01" step="0.01" onchange="calculateLine({{ $index }})">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Prix Unit.</label>
                                            <input type="number" class="form-control prix-input" 
                                                   name="articles[{{ $index }}][prix]" 
                                                   value="{{ $detail->CVD_PRIX_TTC }}" 
                                                   min="0" step="0.01" onchange="calculateLine({{ $index }})">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Remise</label>
                                            <input type="number" class="form-control remise-input" 
                                                   name="articles[{{ $index }}][remise]" 
                                                   value="{{ $detail->CVD_REMISE ?? 0 }}" 
                                                   min="0" step="0.01" onchange="calculateLine({{ $index }})">
                                        </div>
                                        <div class="col-md-1 text-center">
                                            <div class="total-ligne" id="total_{{ $index }}">
                                                {{ number_format(($detail->CVD_QTE * $detail->CVD_PRIX_TTC) - ($detail->CVD_REMISE ?? 0), 2) }} DH
                                            </div>
                                        </div>
                                        <div class="col-md-1 text-center">
                                            <button type="button" class="btn btn-remove-article" onclick="removeArticle({{ $index }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Message si aucun article -->
                        <div id="noArticlesMessage" class="text-center py-4 text-muted" 
                             style="{{ ($commande && count($commande['details']) > 0) ? 'display: none;' : '' }}">
                            <i class="fas fa-box-open fa-3x mb-3"></i>
                            <p>Aucun article ajouté. Utilisez la recherche ci-dessus pour ajouter des articles.</p>
                        </div>
                    </div>
                </div>

                <!-- Remarques -->
                <div class="form-section">
                    <div class="section-header">
                        <h6><i class="fas fa-comment"></i> Remarques</h6>
                    </div>
                    <div class="p-4">
                        <textarea class="form-control" name="remarque" rows="3" 
                                  placeholder="Remarques ou notes particulières...">{{ old('remarque') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Colonne latérale -->
            <div class="col-lg-4">
                <!-- Totaux -->
                <div class="form-section">
                    <div class="totaux-section">
                        <h6 class="mb-3"><i class="fas fa-calculator"></i> Résumé Financier</h6>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Sous-total HT:</span>
                            <strong id="sousTotal">0.00 DH</strong>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-white">Remise globale:</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="remise_globale" 
                                       id="remiseGlobale" value="0" min="0" step="0.01" 
                                       onchange="calculateTotals()">
                                <div class="input-group-append">
                                    <span class="input-group-text">DH</span>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="border-light">
                        
                        <div class="d-flex justify-content-between">
                            <h5>TOTAL TTC:</h5>
                            <h5 id="totalTTC">0.00 DH</h5>
                        </div>
                    </div>
                </div>

                <!-- Mode de paiement -->
                <div class="form-section">
                    <div class="section-header">
                        <h6><i class="fas fa-credit-card"></i> Mode de Paiement</h6>
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <label class="form-label">Mode principal *</label>
                            <select class="form-control" name="mode_paiement" id="modePaiement" onchange="togglePaiementDetails()">
                                <option value="Espèces">Espèces</option>
                                <option value="Carte">Carte bancaire</option>
                                <option value="Chèque">Chèque</option>
                                <option value="Crédit">Crédit/À terme</option>
                                <option value="Mixte">Paiement mixte</option>
                            </select>
                        </div>

                        <div class="mode-paiement-section" id="paiementDetails" style="display: none;">
                            <h6 class="mb-3">Détails du paiement:</h6>
                            
                            <div class="mb-2">
                                <label class="form-label">Espèces</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="montant_espece" 
                                           value="0" min="0" step="0.01" onchange="calculateRendu()">
                                    <div class="input-group-append">
                                        <span class="input-group-text">DH</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <label class="form-label">Carte</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="montant_carte" 
                                           value="0" min="0" step="0.01" onchange="calculateRendu()">
                                    <div class="input-group-append">
                                        <span class="input-group-text">DH</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <label class="form-label">Crédit</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="montant_credit" 
                                           value="0" min="0" step="0.01" onchange="calculateRendu()">
                                    <div class="input-group-append">
                                        <span class="input-group-text">DH</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <label class="form-label">Chèque</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="montant_cheque" 
                                           value="0" min="0" step="0.01" onchange="calculateRendu()">
                                    <div class="input-group-append">
                                        <span class="input-group-text">DH</span>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between">
                                <strong>Rendu:</strong>
                                <strong id="montantRendu" class="text-warning">0.00 DH</strong>
                            </div>
                            <input type="hidden" name="montant_rendu" id="montantRenduInput">
                        </div>

                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="exonore" id="exonore">
                            <label class="form-check-label" for="exonore">
                                Facture exonérée de TVA
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-section">
                    <div class="section-header">
                        <h6><i class="fas fa-cog"></i> Actions</h6>
                    </div>
                    <div class="p-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save"></i> Créer la Facture
                            </button>
                            <button type="button" class="btn btn-warning" onclick="saveDraft()">
                                <i class="fas fa-edit"></i> Enregistrer en Brouillon
                            </button>
                            <a href="{{ route('admin.factures.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <!-- Total flottant pour les écrans mobiles -->
    <div class="floating-total d-md-none" id="floatingTotal">
        <div class="text-center">
            <small>Total TTC</small><br>
            <strong id="floatingTotalAmount">0.00 DH</strong>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let articleIndex = {{ $commande ? count($commande['details']) : 0 }};
let selectedArticle = null;
let stockData = {};

$(document).ready(function() {
    // Initialiser les calculs si on a une commande
    @if($commande)
        calculateTotals();
        // Afficher les infos client si présélectionné
        loadClientInfo('{{ $commande['commande']->CLT_REF }}');
    @endif
    
    // Afficher le total flottant sur mobile quand on scroll
    $(window).scroll(function() {
        if ($(window).width() < 768) {
            const shouldShow = $(window).scrollTop() > 300;
            $('#floatingTotal').toggle(shouldShow);
        }
    });
    
    // Recherche client avec appel API
    let clientSearchTimeout;
    $('#client_search').on('input', function() {
        const query = $(this).val();
        
        clearTimeout(clientSearchTimeout);
        
        if (query.length < 2) {
            $('#clientSuggestions').hide();
            $('#clientInfoDisplay').hide();
            $('#client_ref').val('');
            return;
        }
        
        // Ajouter un délai pour éviter trop d'appels API
        clientSearchTimeout = setTimeout(() => {
            searchClients(query);
        }, 300);
    });
    
    // Recherche article avec appel API
    let articleSearchTimeout;
    $('#article_search').on('input', function() {
        const query = $(this).val();
        
        clearTimeout(articleSearchTimeout);
        
        if (query.length < 2) {
            $('#articleSuggestions').hide();
            $('#articleInfoPreview').hide();
            selectedArticle = null;
            return;
        }
        
        // Ajouter un délai pour éviter trop d'appels API
        articleSearchTimeout = setTimeout(() => {
            searchArticles(query);
        }, 300);
    });
    
    // Fermer les suggestions en cliquant ailleurs
    $(document).click(function(e) {
        if (!$(e.target).closest('.client-search').length) {
            $('#clientSuggestions').hide();
        }
        if (!$(e.target).closest('.article-search').length) {
            $('#articleSuggestions').hide();
        }
    });
    
    // Raccourcis clavier
    $(document).keydown(function(e) {
        // Ctrl + S pour sauvegarder
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            $('#factureForm').submit();
        }
        
        // F2 pour focus sur recherche client
        if (e.key === 'F2') {
            e.preventDefault();
            $('#client_search').focus();
        }
        
        // F3 pour focus sur recherche article
        if (e.key === 'F3') {
            e.preventDefault();
            $('#article_search').focus();
        }
    });
});

// Recherche clients via API
function searchClients(query) {
    $.get(`/admin/api/factures/search-clients?q=${encodeURIComponent(query)}`)
        .done(function(data) {
            let html = '';
            data.forEach(client => {
                html += `
                    <div class="client-suggestion" onclick="selectClient('${client.id}', '${client.nom}', '${client.telephone || ''}', '${client.email || ''}')">
                        <strong>${client.nom}</strong>
                        ${client.telephone ? `<br><small class="text-muted"><i class="fas fa-phone"></i> ${client.telephone}</small>` : ''}
                        ${client.email ? `<br><small class="text-muted"><i class="fas fa-envelope"></i> ${client.email}</small>` : ''}
                    </div>
                `;
            });
            
            $('#clientSuggestions').html(html).toggle(data.length > 0);
        })
        .fail(function() {
            console.error('Erreur lors de la recherche de clients');
        });
}

// Recherche articles via API
function searchArticles(query) {
    $.get(`/admin/api/factures/search-articles?q=${encodeURIComponent(query)}`)
        .done(function(data) {
            let html = '';
            data.forEach(article => {
                html += `
                    <div class="article-suggestion" onclick="selectArticle('${article.id}', '${article.designation}', ${article.prix_ttc}, ${article.tva})">
                        <div style="flex: 1;">
                            <strong>${article.designation}</strong>
                            <br><small class="text-muted">Réf: ${article.id}</small>
                        </div>
                        <div class="text-right">
                            <strong>${article.prix_ttc.toFixed(2)} DH</strong>
                            <br><small class="text-muted">TVA ${article.tva}%</small>
                        </div>
                    </div>
                `;
            });
            
            $('#articleSuggestions').html(html).toggle(data.length > 0);
        })
        .fail(function() {
            console.error('Erreur lors de la recherche d\'articles');
        });
}

// Charger les informations détaillées du client
function loadClientInfo(clientRef) {
    if (!clientRef) return;
    
    $.get(`/admin/api/factures/client/${clientRef}`)
        .done(function(client) {
            $('#clientName').text(client.nom);
            $('#clientRef').text(client.id);
            $('#clientPhone').text(client.telephone ? `📞 ${client.telephone}` : '');
            $('#clientEmail').text(client.email ? `✉️ ${client.email}` : '');
            
            const credit = parseFloat(client.credit) || 0;
            const badgeClass = credit > 0 ? 'badge-warning' : 'badge-success';
            $('#clientCreditBadge').removeClass('badge-warning badge-success').addClass(badgeClass)
                                 .text(`Crédit: ${credit.toFixed(2)} DH`);
            
            $('#clientInfoDisplay').show();
        })
        .fail(function() {
            console.error('Erreur lors du chargement des informations client');
        });
}

function selectClient(ref, name, phone, email) {
    $('#client_ref').val(ref);
    $('#client_search').val(name).removeClass('is-invalid').addClass('is-valid');
    $('#clientSuggestions').hide();
    
    // Charger les informations détaillées
    loadClientInfo(ref);
}

function selectArticle(ref, designation, prix, tva) {
    selectedArticle = {
        ref: ref,
        designation: designation,
        prix: prix,
        tva: tva
    };
    
    $('#article_search').val(designation).removeClass('is-invalid').addClass('is-valid');
    $('#article_prix').val(prix.toFixed(2));
    $('#articleSuggestions').hide();
    
    // Afficher l'aperçu
    $('#previewName').text(designation);
    $('#previewRef').text(ref);
    $('#previewPrice').text(`${prix.toFixed(2)} DH`);
    $('#articleInfoPreview').show();
    
    // Vérifier le stock
    checkStock();
}

// Vérifier le stock disponible
function checkStock() {
    if (!selectedArticle) return;
    
    const quantite = parseFloat($('#article_quantite').val()) || 1;
    
    // Simuler vérification de stock via API
    $.post('/admin/api/factures/validate-stock', {
        articles: [{
            ref: selectedArticle.ref,
            quantite: quantite
        }],
        _token: $('meta[name="csrf-token"]').attr('content')
    })
    .done(function(response) {
        if (response.success && response.results.length > 0) {
            const result = response.results[0];
            const statusEl = $('#stockStatus');
            const quantiteInput = $('#article_quantite');
            const validationEl = $('#quantiteValidation');
            
            if (result.status === 'success') {
                statusEl.html(`<span class="stock-ok"><i class="fas fa-check"></i> Stock: ${result.stock_disponible}</span>`);
                quantiteInput.removeClass('is-invalid').addClass('is-valid');
                validationEl.removeClass('invalid-feedback').addClass('valid-feedback').text('Quantité disponible');
                $('#btnAddArticle').prop('disabled', false);
            } else if (result.status === 'warning') {
                statusEl.html(`<span class="stock-warning"><i class="fas fa-exclamation-triangle"></i> Stock limité: ${result.stock_disponible}</span>`);
                quantiteInput.removeClass('is-valid').addClass('is-invalid');
                validationEl.removeClass('valid-feedback').addClass('invalid-feedback').text(result.message);
                $('#btnAddArticle').prop('disabled', false); // Permettre quand même l'ajout
            } else {
                statusEl.html(`<span class="stock-warning"><i class="fas fa-times"></i> Indisponible</span>`);
                quantiteInput.removeClass('is-valid').addClass('is-invalid');
                validationEl.removeClass('valid-feedback').addClass('invalid-feedback').text(result.message);
                $('#btnAddArticle').prop('disabled', true);
            }
        }
    })
    .fail(function() {
        $('#stockStatus').html(`<span class="text-muted"><i class="fas fa-question"></i> Stock non vérifié</span>`);
    });
}

// Fonctions pour les boutons rapides
function setTable(tableRef) {
    $('#table_ref').val(tableRef);
}

function quickAddArticle(ref, designation, prix) {
    selectedArticle = {
        ref: ref,
        designation: designation,
        prix: prix,
        tva: 20
    };
    
    $('#article_search').val(designation);
    $('#article_prix').val(prix.toFixed(2));
    $('#article_quantite').val(1);
    
    // Afficher l'aperçu
    $('#previewName').text(designation);
    $('#previewRef').text(ref);
    $('#previewPrice').text(`${prix.toFixed(2)} DH`);
    $('#stockStatus').html(`<span class="text-muted"><i class="fas fa-info"></i> Article rapide</span>`);
    $('#articleInfoPreview').show();
    
    // Ajouter directement
    addArticle();
}

function addArticle() {
    if (!selectedArticle) {
        showToast('Veuillez sélectionner un article valide', 'error');
        $('#article_search').focus();
        return;
    }
    
    const quantite = parseFloat($('#article_quantite').val()) || 1;
    const prix = parseFloat($('#article_prix').val()) || 0;
    const remise = parseFloat($('#article_remise').val()) || 0;
    
    if (quantite <= 0) {
        showToast('La quantité doit être supérieure à 0', 'error');
        return;
    }
    
    if (prix <= 0) {
        showToast('Le prix doit être supérieur à 0', 'error');
        return;
    }
    
    // Vérifier si l'article existe déjà
    const existingIndex = findExistingArticle(selectedArticle.ref);
    if (existingIndex !== -1) {
        // Article existe, augmenter la quantité
        const existingQuantite = parseFloat($(`input[name="articles[${existingIndex}][quantite]"]`).val()) || 0;
        const newQuantite = existingQuantite + quantite;
        $(`input[name="articles[${existingIndex}][quantite]"]`).val(newQuantite);
        calculateLine(existingIndex);
        showToast(`Quantité mise à jour: ${newQuantite}`, 'success');
    } else {
        // Nouvel article
        const html = `
            <div class="article-row" data-index="${articleIndex}" style="opacity: 0; animation: fadeIn 0.5s forwards;">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <strong>${selectedArticle.designation}</strong>
                        <br>
                        <small class="text-muted">Réf: ${selectedArticle.ref}</small>
                        <input type="hidden" name="articles[${articleIndex}][art_ref]" value="${selectedArticle.ref}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quantité</label>
                        <input type="number" class="form-control quantite-input" 
                               name="articles[${articleIndex}][quantite]" 
                               value="${quantite}" 
                               min="0.01" step="0.01" onchange="calculateLine(${articleIndex})"
                               onkeyup="calculateLine(${articleIndex})">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Prix Unit.</label>
                        <input type="number" class="form-control prix-input" 
                               name="articles[${articleIndex}][prix]" 
                               value="${prix.toFixed(2)}" 
                               min="0" step="0.01" onchange="calculateLine(${articleIndex})"
                               onkeyup="calculateLine(${articleIndex})">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Remise</label>
                        <input type="number" class="form-control remise-input" 
                               name="articles[${articleIndex}][remise]" 
                               value="${remise.toFixed(2)}" 
                               min="0" step="0.01" onchange="calculateLine(${articleIndex})"
                               onkeyup="calculateLine(${articleIndex})">
                    </div>
                    <div class="col-md-1 text-center">
                        <div class="total-ligne" id="total_${articleIndex}">
                            ${((quantite * prix) - remise).toFixed(2)} DH
                        </div>
                    </div>
                    <div class="col-md-1 text-center">
                        <button type="button" class="btn btn-remove-article" onclick="removeArticle(${articleIndex})" 
                                title="Supprimer cet article">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        $('#articlesContainer').append(html);
        articleIndex++;
        showToast('Article ajouté avec succès', 'success');
    }
    
    $('#noArticlesMessage').hide();
    
    // Reset des champs
    resetArticleForm();
    calculateTotals();
    
    // Focus sur la recherche pour le prochain article
    $('#article_search').focus();
}

function findExistingArticle(ref) {
    let foundIndex = -1;
    $('#articlesContainer .article-row').each(function() {
        const index = $(this).data('index');
        const articleRef = $(`input[name="articles[${index}][art_ref]"]`).val();
        if (articleRef === ref) {
            foundIndex = index;
            return false; // Break the loop
        }
    });
    return foundIndex;
}

function resetArticleForm() {
    $('#article_search').val('').removeClass('is-valid is-invalid');
    $('#article_prix').val(0);
    $('#article_quantite').val(1);
    $('#article_remise').val(0);
    $('#articleInfoPreview').hide();
    selectedArticle = null;
    $('#btnAddArticle').prop('disabled', false);
}

function showToast(message, type = 'info') {
    const toastClass = type === 'error' ? 'bg-danger' : type === 'success' ? 'bg-success' : 'bg-info';
    const icon = type === 'error' ? 'fas fa-exclamation-circle' : type === 'success' ? 'fas fa-check-circle' : 'fas fa-info-circle';
    
    const toast = $(`
        <div class="toast align-items-center text-white ${toastClass} border-0" role="alert" 
             style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="${icon}"></i> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);
    
    $('body').append(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        toast.fadeOut(500, function() {
            $(this).remove();
        });
    }, 3000);
}

function removeArticle(index) {
    $(`div[data-index="${index}"]`).remove();
    calculateTotals();
    
    if ($('#articlesContainer .article-row').length === 0) {
        $('#noArticlesMessage').show();
    }
}

function calculateLine(index) {
    const quantite = parseFloat($(`input[name="articles[${index}][quantite]"]`).val()) || 0;
    const prix = parseFloat($(`input[name="articles[${index}][prix]"]`).val()) || 0;
    const remise = parseFloat($(`input[name="articles[${index}][remise]"]`).val()) || 0;
    
    const total = (quantite * prix) - remise;
    $(`#total_${index}`).text(total.toFixed(2) + ' DH');
    
    calculateTotals();
}

function calculateTotals() {
    let sousTotal = 0;
    let nbArticles = 0;
    
    $('#articlesContainer .article-row').each(function() {
        const index = $(this).data('index');
        const quantite = parseFloat($(`input[name="articles[${index}][quantite]"]`).val()) || 0;
        const prix = parseFloat($(`input[name="articles[${index}][prix]"]`).val()) || 0;
        const remise = parseFloat($(`input[name="articles[${index}][remise]"]`).val()) || 0;
        
        const totalLigne = (quantite * prix) - remise;
        sousTotal += totalLigne;
        nbArticles += quantite;
    });
    
    const remiseGlobale = parseFloat($('#remiseGlobale').val()) || 0;
    const totalTTC = Math.max(0, sousTotal - remiseGlobale);
    
    // Mise à jour des affichages
    $('#sousTotal').text(sousTotal.toFixed(2) + ' DH');
    $('#totalTTC').text(totalTTC.toFixed(2) + ' DH');
    $('#floatingTotalAmount').text(totalTTC.toFixed(2) + ' DH');
    
    // Mise à jour de l'en-tête avec le nombre d'articles
    if (nbArticles > 0) {
        $('.fa-list').parent().html(`<i class="fas fa-list"></i> Articles de la Facture (${Math.floor(nbArticles)} articles)`);
    } else {
        $('.fa-list').parent().html(`<i class="fas fa-list"></i> Articles de la Facture`);
    }
    
    // Recalculer le rendu si en mode mixte
    if ($('#modePaiement').val() === 'Mixte') {
        calculateRendu();
    }
    
    // Validation du formulaire
    validateForm();
}

function validateForm() {
    const hasClient = $('#client_ref').val() !== '';
    const hasArticles = $('#articlesContainer .article-row').length > 0;
    const totalTTC = parseFloat($('#totalTTC').text().replace(' DH', '')) || 0;
    
    // Validation client
    if (hasClient) {
        $('#client_search').removeClass('is-invalid').addClass('is-valid');
    } else {
        $('#client_search').removeClass('is-valid').addClass('is-invalid');
    }
    
    // Validation articles
    const submitBtn = $('#factureForm button[type="submit"]');
    if (hasClient && hasArticles && totalTTC > 0) {
        submitBtn.prop('disabled', false).removeClass('btn-secondary').addClass('btn-success');
    } else {
        submitBtn.prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
    }
}

function togglePaiementDetails() {
    const mode = $('#modePaiement').val();
    $('#paiementDetails').toggle(mode === 'Mixte');
    
    if (mode === 'Mixte') {
        // Préremplir avec le total
        const totalTTC = parseFloat($('#totalTTC').text().replace(' DH', '')) || 0;
        $('input[name="montant_espece"]').val(totalTTC.toFixed(2));
        calculateRendu();
    }
}

function calculateRendu() {
    const totalTTC = parseFloat($('#totalTTC').text().replace(' DH', '')) || 0;
    const especes = parseFloat($('input[name="montant_espece"]').val()) || 0;
    const carte = parseFloat($('input[name="montant_carte"]').val()) || 0;
    const credit = parseFloat($('input[name="montant_credit"]').val()) || 0;
    const cheque = parseFloat($('input[name="montant_cheque"]').val()) || 0;
    
    const totalPaye = especes + carte + credit + cheque;
    const rendu = Math.max(0, totalPaye - totalTTC);
    const manquant = Math.max(0, totalTTC - totalPaye);
    
    $('#montantRendu').text(rendu.toFixed(2) + ' DH');
    $('#montantRenduInput').val(rendu);
    
    // Afficher si il manque de l'argent
    if (manquant > 0) {
        $('#montantRendu').removeClass('text-warning text-success').addClass('text-danger')
                         .text(`Manquant: ${manquant.toFixed(2)} DH`);
    } else if (rendu > 0) {
        $('#montantRendu').removeClass('text-danger text-success').addClass('text-warning')
                         .text(`Rendu: ${rendu.toFixed(2)} DH`);
    } else {
        $('#montantRendu').removeClass('text-danger text-warning').addClass('text-success')
                         .text('Montant exact');
    }
}

function saveDraft() {
    // Validation minimale pour brouillon
    if (!$('#client_ref').val()) {
        showToast('Veuillez sélectionner un client pour sauvegarder le brouillon', 'error');
        return;
    }
    
    // Créer un champ hidden pour indiquer que c'est un brouillon
    $('<input>').attr({
        type: 'hidden',
        name: 'save_as_draft',
        value: '1'
    }).appendTo('#factureForm');
    
    showToast('Sauvegarde du brouillon...', 'info');
    $('#factureForm').submit();
}

// Validation avant soumission
$('#factureForm').on('submit', function(e) {
    const isValid = validateFormSubmission();
    
    if (!isValid) {
        e.preventDefault();
        return false;
    }
    
    // Désactiver le bouton pour éviter les soumissions multiples
    $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Création...');
    
    return true;
});

function validateFormSubmission() {
    const clientRef = $('#client_ref').val();
    const articlesCount = $('#articlesContainer .article-row').length;
    const totalTTC = parseFloat($('#totalTTC').text().replace(' DH', '')) || 0;
    
    if (!clientRef) {
        showToast('Veuillez sélectionner un client', 'error');
        $('#client_search').focus();
        return false;
    }
    
    if (articlesCount === 0) {
        showToast('Veuillez ajouter au moins un article', 'error');
        $('#article_search').focus();
        return false;
    }
    
    if (totalTTC <= 0) {
        showToast('Le montant total doit être supérieur à 0', 'error');
        return false;
    }
    
    // Validation du mode de paiement mixte
    if ($('#modePaiement').val() === 'Mixte') {
        const especes = parseFloat($('input[name="montant_espece"]').val()) || 0;
        const carte = parseFloat($('input[name="montant_carte"]').val()) || 0;
        const credit = parseFloat($('input[name="montant_credit"]').val()) || 0;
        const cheque = parseFloat($('input[name="montant_cheque"]').val()) || 0;
        const totalPaye = especes + carte + credit + cheque;
        
        if (totalPaye < totalTTC) {
            showToast('Le montant payé est insuffisant', 'error');
            return false;
        }
    }
    
    return true;
}

// Ajouter des animations CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideIn {
        from { transform: translateX(-100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    .article-row {
        transition: all 0.3s ease;
    }
    
    .article-row:hover {
        background-color: #f8f9fc;
        border-radius: 8px;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
`;
document.head.appendChild(style);

// إدارة نموذج العميل الجديد
$(document).ready(function() {
    // إظهار/إخفاء نموذج العميل الجديد
    $('#toggleNewClientForm').click(function() {
        $('#newClientForm').addClass('show');
        $('#client_search').prop('disabled', true);
    });

    $('#closeNewClientForm, #cancelNewClient').click(function() {
        $('#newClientForm').removeClass('show');
        $('#client_search').prop('disabled', false);
        clearNewClientForm();
    });

    // حفظ العميل الجديد
    $('#saveNewClient').click(function() {
        const nom = $('#new_client_nom').val().trim();
        const telephone = $('#new_client_telephone').val().trim();
        
        // التحقق من البيانات الأساسية
        if (!nom || !telephone) {
            Swal.fire({
                icon: 'warning',
                title: 'بيانات ناقصة',
                text: 'يرجى إدخال اسم العميل ورقم الهاتف على الأقل'
            });
            return;
        }

        // إعداد البيانات
        const clientData = {
            nom: nom,
            telephone: telephone,
            email: $('#new_client_email').val().trim() || null,
            civilite: $('#new_client_civilite').val() || null,
            raison_sociale: $('#new_client_raison_sociale').val().trim() || null,
            est_entreprise: $('#new_client_est_entreprise').is(':checked'),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        // إزالة القيم الفارغة
        Object.keys(clientData).forEach(key => {
            if (clientData[key] === '' || clientData[key] === null) {
                delete clientData[key];
            }
        });
        
        // إعادة إضافة _token
        clientData._token = $('meta[name="csrf-token"]').attr('content');

        // إظهار مؤشر التحميل
        const saveBtn = $('#saveNewClient');
        const originalText = saveBtn.html();
        saveBtn.html('<span class="loading-spinner"></span> جاري الحفظ...').prop('disabled', true);

        // إرسال البيانات
        $.ajax({
            url: '/admin/api/factures/create-client',
            method: 'POST',
            data: clientData,
            success: function(response) {
                if (response.success) {
                    // إضافة العميل إلى قائمة البحث
                    const newClient = response.client;
                    
                    // تحديث حقل البحث
                    $('#client_search').val(newClient.CLT_CLIENT);
                    $('#client_ref').val(newClient.CLT_REF);
                    
                    // إظهار معلومات العميل
                    updateClientDisplay(newClient);
                    
                    // إخفاء النموذج
                    $('#newClientForm').removeClass('show');
                    $('#client_search').prop('disabled', false);
                    clearNewClientForm();
                    
                    // رسالة نجاح
                    Swal.fire({
                        icon: 'success',
                        title: 'تم بنجاح!',
                        text: 'تم إنشاء العميل الجديد بنجاح',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // التحقق من صحة النموذج
                    validateForm();
                }
            },
            error: function(xhr) {
                let errorMessage = 'حدث خطأ أثناء إنشاء العميل';
                let errorDetails = '';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    // عرض تفاصيل أخطاء التحقق
                    if (xhr.responseJSON.errors) {
                        errorDetails = '<ul class="text-start">';
                        Object.values(xhr.responseJSON.errors).forEach(function(errors) {
                            errors.forEach(function(error) {
                                errorDetails += '<li>' + error + '</li>';
                            });
                        });
                        errorDetails += '</ul>';
                    }
                    
                    // إذا كان هناك عميل موجود، اقترح استخدامه
                    if (xhr.responseJSON.existing_client) {
                        const existingClient = xhr.responseJSON.existing_client;
                        Swal.fire({
                            icon: 'question',
                            title: 'عميل موجود',
                            html: `${errorMessage}<br><br>هل تريد استخدام العميل الموجود؟<br><strong>${existingClient.CLT_CLIENT}</strong><br>${existingClient.CLT_TELEPHONE}`,
                            showCancelButton: true,
                            confirmButtonText: 'نعم، استخدم العميل الموجود',
                            cancelButtonText: 'إلغاء',
                            confirmButtonColor: '#1cc88a'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // استخدام العميل الموجود
                                $('#client_search').val(existingClient.CLT_CLIENT);
                                $('#client_ref').val(existingClient.CLT_REF);
                                updateClientDisplay(existingClient);
                                $('#newClientForm').removeClass('show');
                                $('#client_search').prop('disabled', false);
                                clearNewClientForm();
                                validateForm();
                            }
                        });
                        return; // لا تعرض رسالة خطأ عادية
                    }
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في الحفظ',
                    html: errorMessage + (errorDetails ? '<br><br>' + errorDetails : '')
                });
            },
            complete: function() {
                // إعادة تعيين زر الحفظ
                saveBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    function clearNewClientForm() {
        $('#new_client_nom, #new_client_telephone, #new_client_email, #new_client_raison_sociale').val('');
        $('#new_client_civilite').val('');
        $('#new_client_est_entreprise').prop('checked', false);
    }

    function updateClientDisplay(client) {
        $('#clientName').text(client.CLT_CLIENT || client.nom || 'N/A');
        $('#clientRef').text(client.CLT_REF || client.id || 'N/A');
        $('#clientPhone').text(client.CLT_TELEPHONE || client.telephone || '');
        $('#clientEmail').text(client.CLT_EMAIL || client.email || '');
        $('#clientCreditBadge').text('Crédit: 0 DH').removeClass('badge-danger badge-warning').addClass('badge-info');
        $('#clientInfoDisplay').show();
    }
});
</script>
@endpush
