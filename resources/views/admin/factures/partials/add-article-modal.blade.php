<!-- Modal pour ajouter un article -->
<div class="modal fade" id="addArticleModal" tabindex="-1" role="dialog" aria-labelledby="addArticleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addArticleModalLabel">
                    <i class="fas fa-plus-circle"></i>
                    Ajouter un Article à la Facture
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Recherche d'article -->
                <div class="form-group">
                    <label for="article_search">Rechercher un Article</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" id="article_search" 
                               placeholder="Nom de l'article ou référence...">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="btn_scan_article">
                                <i class="fas fa-barcode"></i>
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        Tapez au moins 2 caractères pour rechercher
                    </small>
                </div>

                <!-- Résultats de recherche -->
                <div id="search_results" class="mb-3" style="display: none;">
                    <h6>Résultats de la recherche:</h6>
                    <div class="list-group" id="articles_list">
                        <!-- Les résultats apparaîtront ici -->
                    </div>
                </div>

                <!-- Article sélectionné -->
                <div id="selected_article" style="display: none;">
                    <div class="card bg-light">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-check-circle text-success"></i>
                                Article Sélectionné
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 id="selected_article_name"></h6>
                                    <p class="text-muted mb-1">
                                        <small>Référence: <span id="selected_article_ref"></span></small>
                                    </p>
                                    <p class="text-muted mb-1">
                                        <small>Catégorie: <span id="selected_article_category"></span></small>
                                    </p>
                                    <p class="text-info mb-0">
                                        <small>Stock disponible: <span id="selected_article_stock"></span></small>
                                    </p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <div class="text-lg font-weight-bold text-primary">
                                        <span id="selected_article_price"></span> DH
                                    </div>
                                    <small class="text-muted">Prix unitaire HT</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Détails de l'article à ajouter -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="article_quantity">Quantité <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="article_quantity" 
                                       value="1" min="0.01" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="article_price_ht">Prix Unitaire HT <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="article_price_ht" 
                                           step="0.01" min="0" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">DH</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="article_tva">TVA</label>
                                <select class="form-control" id="article_tva">
                                    <option value="0">0%</option>
                                    <option value="10">10%</option>
                                    <option value="20" selected>20%</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="article_price_ttc">Prix Unitaire TTC</label>
                                <input type="number" class="form-control" id="article_price_ttc" 
                                       readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="article_remise">Remise</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="article_remise" 
                                           value="0" min="0" step="0.01">
                                    <div class="input-group-append">
                                        <span class="input-group-text">DH</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Calcul total -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-8">
                                        <strong>Total de la ligne:</strong>
                                        <span class="float-right">
                                            <span id="line_total">0.00</span> DH
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Données cachées -->
                    <input type="hidden" id="selected_article_id">
                    <input type="hidden" id="selected_article_stock_qty">
                </div>

                <!-- Message si aucun article sélectionné -->
                <div id="no_article_selected" class="alert alert-warning">
                    <i class="fas fa-info-circle"></i>
                    Veuillez rechercher et sélectionner un article pour continuer.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i>
                    Annuler
                </button>
                <button type="button" class="btn btn-primary" id="btn_add_article_to_facture" disabled>
                    <i class="fas fa-plus"></i>
                    Ajouter à la Facture
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let searchTimeout;
    let selectedArticle = null;

    // Recherche d'articles
    $('#article_search').on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val().trim();
        
        if (query.length < 2) {
            $('#search_results').hide();
            resetArticleSelection();
            return;
        }
        
        searchTimeout = setTimeout(() => {
            searchArticles(query);
        }, 300);
    });

    // Fonction de recherche d'articles (simulation)
    function searchArticles(query) {
        // Ici, vous devriez faire un appel AJAX vers votre backend
        // Pour la démonstration, on simule des données
        const mockArticles = [
            {
                id: 1,
                ref: 'ART001',
                designation: 'Pizza Margherita',
                category: 'Pizzas',
                prix_vente_ht: 45.00,
                stock: 999,
                tva: 20
            },
            {
                id: 2,
                ref: 'ART002',
                designation: 'Coca Cola 33cl',
                category: 'Boissons',
                prix_vente_ht: 8.33,
                stock: 50,
                tva: 20
            },
            {
                id: 3,
                ref: 'ART003',
                designation: 'Salade César',
                category: 'Salades',
                prix_vente_ht: 35.00,
                stock: 25,
                tva: 20
            }
        ];

        // Filtrer les articles selon la recherche
        const filteredArticles = mockArticles.filter(article => 
            article.designation.toLowerCase().includes(query.toLowerCase()) ||
            article.ref.toLowerCase().includes(query.toLowerCase())
        );

        displaySearchResults(filteredArticles);
    }

    // Afficher les résultats de recherche
    function displaySearchResults(articles) {
        const resultsContainer = $('#articles_list');
        resultsContainer.empty();

        if (articles.length === 0) {
            resultsContainer.html(`
                <div class="list-group-item">
                    <div class="text-center text-muted">
                        <i class="fas fa-search"></i>
                        Aucun article trouvé
                    </div>
                </div>
            `);
        } else {
            articles.forEach(article => {
                const stockClass = article.stock > 10 ? 'text-success' : 
                                  article.stock > 0 ? 'text-warning' : 'text-danger';
                const stockIcon = article.stock > 10 ? 'fas fa-check-circle' : 
                                 article.stock > 0 ? 'fas fa-exclamation-triangle' : 'fas fa-times-circle';

                resultsContainer.append(`
                    <div class="list-group-item list-group-item-action article-item" 
                         data-article='${JSON.stringify(article)}'>
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">${article.designation}</h6>
                            <small class="text-primary font-weight-bold">${article.prix_vente_ht.toFixed(2)} DH</small>
                        </div>
                        <p class="mb-1">
                            <small class="text-muted">Réf: ${article.ref} | Catégorie: ${article.category}</small>
                        </p>
                        <small class="${stockClass}">
                            <i class="${stockIcon}"></i>
                            Stock: ${article.stock}
                        </small>
                    </div>
                `);
            });
        }

        $('#search_results').show();
    }

    // Sélectionner un article
    $(document).on('click', '.article-item', function() {
        const articleData = $(this).data('article');
        selectArticle(articleData);
    });

    // Fonction pour sélectionner un article
    function selectArticle(article) {
        selectedArticle = article;
        
        // Remplir les informations de l'article sélectionné
        $('#selected_article_name').text(article.designation);
        $('#selected_article_ref').text(article.ref);
        $('#selected_article_category').text(article.category);
        $('#selected_article_stock').text(article.stock);
        $('#selected_article_price').text(article.prix_vente_ht.toFixed(2));
        
        // Remplir les champs du formulaire
        $('#selected_article_id').val(article.id);
        $('#selected_article_stock_qty').val(article.stock);
        $('#article_price_ht').val(article.prix_vente_ht.toFixed(2));
        $('#article_tva').val(article.tva);
        
        // Calculer le prix TTC
        calculatePriceTTC();
        calculateLineTotal();
        
        // Afficher la section article sélectionné
        $('#selected_article').show();
        $('#no_article_selected').hide();
        $('#search_results').hide();
        $('#btn_add_article_to_facture').prop('disabled', false);
        
        // Effacer la recherche
        $('#article_search').val('');
    }

    // Calcul du prix TTC
    function calculatePriceTTC() {
        const prixHT = parseFloat($('#article_price_ht').val()) || 0;
        const tva = parseFloat($('#article_tva').val()) || 0;
        const prixTTC = prixHT * (1 + tva / 100);
        
        $('#article_price_ttc').val(prixTTC.toFixed(2));
        calculateLineTotal();
    }

    // Calcul du total de la ligne
    function calculateLineTotal() {
        const quantite = parseFloat($('#article_quantity').val()) || 0;
        const prixTTC = parseFloat($('#article_price_ttc').val()) || 0;
        const remise = parseFloat($('#article_remise').val()) || 0;
        
        const total = (quantite * prixTTC) - remise;
        $('#line_total').text(total.toFixed(2));
    }

    // Écouteurs pour les calculs
    $('#article_price_ht, #article_tva').on('input change', calculatePriceTTC);
    $('#article_quantity, #article_remise').on('input', calculateLineTotal);

    // Réinitialiser la sélection
    function resetArticleSelection() {
        selectedArticle = null;
        $('#selected_article').hide();
        $('#no_article_selected').show();
        $('#btn_add_article_to_facture').prop('disabled', true);
    }

    // Ajouter l'article à la facture
    $('#btn_add_article_to_facture').click(function() {
        if (!selectedArticle) return;
        
        const quantity = parseFloat($('#article_quantity').val());
        const stockAvailable = parseFloat($('#selected_article_stock_qty').val());
        
        // Vérifier le stock
        if (quantity > stockAvailable && stockAvailable > 0) {
            if (!confirm(`La quantité demandée (${quantity}) dépasse le stock disponible (${stockAvailable}). Continuer ?`)) {
                return;
            }
        }
        
        // Ajouter la ligne à la table
        addArticleToTable();
        
        // Fermer le modal
        $('#addArticleModal').modal('hide');
    });

    // Fonction pour ajouter l'article à la table
    function addArticleToTable() {
        const articleId = $('#selected_article_id').val();
        const designation = $('#selected_article_name').text();
        const ref = $('#selected_article_ref').text();
        const quantity = $('#article_quantity').val();
        const prixHT = $('#article_price_ht').val();
        const tva = $('#article_tva').val();
        const prixTTC = $('#article_price_ttc').val();
        const remise = $('#article_remise').val();
        const total = $('#line_total').text();
        
        const newRow = `
            <tr data-article-id="new">
                <td>
                    <div class="font-weight-bold">${designation}</div>
                    <small class="text-muted">Réf: ${ref}</small>
                    <input type="hidden" name="articles[${articleCounter}][id]" value="">
                    <input type="hidden" name="articles[${articleCounter}][article_id]" value="${articleId}">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm article-qte" 
                           name="articles[${articleCounter}][qte]" 
                           value="${quantity}" min="0" step="0.01">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm article-prix-ht" 
                           name="articles[${articleCounter}][prix_ht]" 
                           value="${prixHT}" min="0" step="0.01">
                </td>
                <td>
                    <select class="form-control form-control-sm article-tva" 
                            name="articles[${articleCounter}][tva]">
                        <option value="0" ${tva == 0 ? 'selected' : ''}>0%</option>
                        <option value="10" ${tva == 10 ? 'selected' : ''}>10%</option>
                        <option value="20" ${tva == 20 ? 'selected' : ''}>20%</option>
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm article-prix-ttc" 
                           value="${prixTTC}" readonly>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm article-remise" 
                           name="articles[${articleCounter}][remise]" 
                           value="${remise}" min="0" step="0.01">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm article-total" 
                           value="${total}" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger btn-remove-article">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $('#articlesTable tbody').append(newRow);
        articleCounter++;
        
        // Mettre à jour les calculs
        updateArticleCount();
        calculateTotalFacture();
    }

    // Réinitialiser le modal à la fermeture
    $('#addArticleModal').on('hidden.bs.modal', function() {
        $('#article_search').val('');
        $('#search_results').hide();
        $('#article_quantity').val(1);
        $('#article_remise').val(0);
        resetArticleSelection();
    });

    // Scanner de code-barres (simulation)
    $('#btn_scan_article').click(function() {
        alert('Fonctionnalité de scan de code-barres non implémentée dans cette démonstration.');
    });
});
</script>
