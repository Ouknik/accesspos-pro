<?php
/**
 * Rapport des articles - Analyse détaillée des ventes par article
 * Inclut les tendances, stock, et performances individuelles
 */

try {
    // Rapport détaillé des articles
    $articlesQuery = $pdo->prepare("
        SELECT 
            a.ART_REF as reference,
            a.ART_LIBELLE as nom_article,
            a.ART_PRIX_VNT as prix_vente,
            a.ART_PRIX_ACH as prix_achat,
            a.ART_STOCK_ACTUEL as stock_actuel,
            a.ART_STOCK_MIN as stock_minimum,
            ISNULL(a.ART_FAMILLE, 'Non définie') as famille,
            f.FAM_LIBELLE as nom_famille,
            COUNT(DISTINCT fv.FCTV_REF) as nombre_ventes,
            SUM(CASE WHEN fv.FCTV_MNT_TTC IS NOT NULL THEN fv.FCTV_MNT_TTC ELSE 0 END) as chiffre_affaires,
            AVG(CASE WHEN fv.FCTV_MNT_TTC IS NOT NULL THEN fv.FCTV_MNT_TTC ELSE 0 END) as prix_moyen_vente,
            COUNT(DISTINCT fv.CLT_REF) as clients_uniques,
            MIN(fv.FCTV_DATE) as premiere_vente,
            MAX(fv.FCTV_DATE) as derniere_vente,
            COUNT(DISTINCT fv.FCTV_SERVEUR) as serveurs_vendeurs
        FROM ARTICLE a
        LEFT JOIN FACTURE_VNT fv ON a.ART_REF = fv.ART_REF 
            AND fv.FCTV_DATE BETWEEN ? AND ? 
            AND fv.FCTV_VALIDE = 1
        LEFT JOIN FAMILLE f ON a.ART_FAMILLE = f.FAM_REF
        WHERE a.ART_REF IS NOT NULL
        GROUP BY a.ART_REF, a.ART_LIBELLE, a.ART_PRIX_VNT, a.ART_PRIX_ACH, 
                 a.ART_STOCK_ACTUEL, a.ART_STOCK_MIN, a.ART_FAMILLE, f.FAM_LIBELLE
        ORDER BY chiffre_affaires DESC
    ");
    $articlesQuery->execute([$dateFrom, $dateTo]);
    $articles = $articlesQuery->fetchAll(PDO::FETCH_ASSOC);

    // Articles avec stock faible
    $stockFaible = array_filter($articles, function($article) {
        return $article['stock_actuel'] !== null && 
               $article['stock_minimum'] !== null && 
               $article['stock_actuel'] <= $article['stock_minimum'];
    });

    // Articles les plus rentables
    $articlesRentables = array_filter($articles, function($article) {
        return $article['prix_achat'] !== null && 
               $article['prix_vente'] !== null && 
               $article['prix_achat'] > 0;
    });

    usort($articlesRentables, function($a, $b) {
        $margeA = (($a['prix_vente'] - $a['prix_achat']) / $a['prix_achat']) * 100;
        $margeB = (($b['prix_vente'] - $b['prix_achat']) / $b['prix_achat']) * 100;
        return $margeB <=> $margeA;
    });

    // Évolution des ventes des top articles
    $topArticlesEvolution = $pdo->prepare("
        SELECT 
            a.ART_REF as reference,
            a.ART_LIBELLE as nom_article,
            CAST(fv.FCTV_DATE as DATE) as date_vente,
            COUNT(DISTINCT fv.FCTV_REF) as nb_ventes,
            SUM(CASE WHEN fv.FCTV_MNT_TTC IS NOT NULL THEN fv.FCTV_MNT_TTC ELSE 0 END) as ca_jour
        FROM ARTICLE a
        INNER JOIN FACTURE_VNT fv ON a.ART_REF = fv.ART_REF
        WHERE fv.FCTV_DATE BETWEEN ? AND ? 
            AND fv.FCTV_VALIDE = 1
            AND a.ART_REF IN (
                SELECT TOP 5 a2.ART_REF 
                FROM ARTICLE a2
                INNER JOIN FACTURE_VNT fv2 ON a2.ART_REF = fv2.ART_REF
                WHERE fv2.FCTV_DATE BETWEEN ? AND ? AND fv2.FCTV_VALIDE = 1
                GROUP BY a2.ART_REF
                ORDER BY SUM(fv2.FCTV_MNT_TTC) DESC
            )
        GROUP BY a.ART_REF, a.ART_LIBELLE, CAST(fv.FCTV_DATE as DATE)
        ORDER BY a.ART_REF, date_vente
    ");
    $topArticlesEvolution->execute([$dateFrom, $dateTo, $dateFrom, $dateTo]);
    $evolutionData = $topArticlesEvolution->fetchAll(PDO::FETCH_ASSOC);

    // Calcul des totaux
    $totalCA = array_sum(array_column($articles, 'chiffre_affaires'));
    $articlesVendus = array_filter($articles, function($article) {
        return $article['nombre_ventes'] > 0;
    });

    ?>

    <div class="report-card animate-fadeIn">
        <div class="report-title">
            <span><i class="fas fa-boxes"></i> Rapport d'Analyse des Articles</span>
            <small class="text-muted">Période: <?php echo date('d/m/Y', strtotime($dateFrom)) . ' - ' . date('d/m/Y', strtotime($dateTo)); ?></small>
        </div>

        <!-- KPIs des articles -->
        <div class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-primary"><?php echo count($articles); ?></h4>
                        <p class="mb-0">Total Articles</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-success"><?php echo count($articlesVendus); ?></h4>
                        <p class="mb-0">Articles Vendus</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-warning"><?php echo count($stockFaible); ?></h4>
                        <p class="mb-0">Stock Faible</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-info"><?php echo formatCurrency($totalCA); ?></h4>
                        <p class="mb-0">CA Total</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes stock faible -->
        <?php if (!empty($stockFaible)): ?>
        <div class="alert alert-warning animate-fadeIn">
            <h5><i class="fas fa-exclamation-triangle"></i> Alertes Stock Faible (<?php echo count($stockFaible); ?> articles)</h5>
            <div class="row">
                <?php foreach (array_slice($stockFaible, 0, 6) as $article): ?>
                    <div class="col-md-4 mb-2">
                        <div class="d-flex justify-content-between">
                            <span><strong><?php echo htmlspecialchars($article['nom_article']); ?></strong></span>
                            <span class="text-danger">Stock: <?php echo $article['stock_actuel']; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Onglets pour différentes vues -->
        <ul class="nav nav-tabs" id="articlesTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="top-tab" data-bs-toggle="tab" data-bs-target="#top" 
                        type="button" role="tab">Top Ventes</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="rentables-tab" data-bs-toggle="tab" data-bs-target="#rentables" 
                        type="button" role="tab">Plus Rentables</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="stock-tab" data-bs-toggle="tab" data-bs-target="#stock" 
                        type="button" role="tab">Gestion Stock</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="evolution-tab" data-bs-toggle="tab" data-bs-target="#evolution" 
                        type="button" role="tab">Évolution</button>
            </li>
        </ul>

        <div class="tab-content" id="articlesTabContent">
            <!-- Top des ventes -->
            <div class="tab-pane fade show active" id="top" role="tabpanel">
                <div class="table-responsive mt-3">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Rang</th>
                                <th>Référence</th>
                                <th>Nom Article</th>
                                <th>Famille</th>
                                <th>Prix Vente</th>
                                <th>Nb Ventes</th>
                                <th>Chiffre d'Affaires</th>
                                <th>Clients Uniques</th>
                                <th>Stock Actuel</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($articlesVendus, 0, 20) as $index => $article): 
                                $pourcentageCA = $totalCA > 0 ? ($article['chiffre_affaires'] / $totalCA) * 100 : 0;
                                $performanceLevel = $index < 5 ? 'Excellent' : ($index < 10 ? 'Très Bon' : ($index < 15 ? 'Bon' : 'Moyen'));
                                $badgeClass = $index < 5 ? 'badge-success' : ($index < 10 ? 'badge-warning' : ($index < 15 ? 'badge-info' : 'badge-secondary'));
                            ?>
                                <tr class="<?php echo $article['stock_actuel'] !== null && $article['stock_minimum'] !== null && $article['stock_actuel'] <= $article['stock_minimum'] ? 'table-warning' : ''; ?>">
                                    <td>
                                        <span class="status-badge <?php echo $badgeClass; ?>">
                                            #<?php echo $index + 1; ?>
                                        </span>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($article['reference']); ?></strong></td>
                                    <td>
                                        <?php echo htmlspecialchars($article['nom_article']); ?>
                                        <?php if ($article['stock_actuel'] !== null && $article['stock_minimum'] !== null && $article['stock_actuel'] <= $article['stock_minimum']): ?>
                                            <i class="fas fa-exclamation-triangle text-warning" title="Stock faible"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($article['nom_famille'] ?: $article['famille']); ?></small>
                                    </td>
                                    <td class="text-info">
                                        <?php echo formatCurrency($article['prix_vente']); ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary"><?php echo number_format($article['nombre_ventes']); ?></span>
                                    </td>
                                    <td class="text-success">
                                        <strong><?php echo formatCurrency($article['chiffre_affaires']); ?></strong>
                                        <br><small class="text-muted"><?php echo number_format($pourcentageCA, 1); ?>% du total</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-info"><?php echo number_format($article['clients_uniques']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($article['stock_actuel'] !== null): ?>
                                            <span class="<?php echo $article['stock_actuel'] <= ($article['stock_minimum'] ?: 0) ? 'text-danger' : 'text-success'; ?>">
                                                <?php echo number_format($article['stock_actuel']); ?>
                                            </span>
                                            <?php if ($article['stock_minimum'] !== null): ?>
                                                <br><small class="text-muted">Min: <?php echo $article['stock_minimum']; ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo $badgeClass; ?>">
                                            <?php echo $performanceLevel; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Articles les plus rentables -->
            <div class="tab-pane fade" id="rentables" role="tabpanel">
                <div class="table-responsive mt-3">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Rang</th>
                                <th>Référence</th>
                                <th>Nom Article</th>
                                <th>Prix Achat</th>
                                <th>Prix Vente</th>
                                <th>Marge Unitaire</th>
                                <th>% Marge</th>
                                <th>CA Réalisé</th>
                                <th>Bénéfice Estimé</th>
                                <th>Rentabilité</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($articlesRentables, 0, 20) as $index => $article): 
                                $margeUnitaire = $article['prix_vente'] - $article['prix_achat'];
                                $pourcentageMarge = $article['prix_achat'] > 0 ? ($margeUnitaire / $article['prix_achat']) * 100 : 0;
                                $beneficeEstime = $margeUnitaire * $article['nombre_ventes'];
                                $rentabiliteLevel = $pourcentageMarge > 50 ? 'Excellent' : ($pourcentageMarge > 30 ? 'Très Bon' : ($pourcentageMarge > 15 ? 'Bon' : 'Faible'));
                                $badgeClass = $pourcentageMarge > 50 ? 'badge-success' : ($pourcentageMarge > 30 ? 'badge-warning' : ($pourcentageMarge > 15 ? 'badge-info' : 'badge-secondary'));
                            ?>
                                <tr>
                                    <td>
                                        <span class="status-badge <?php echo $badgeClass; ?>">
                                            #<?php echo $index + 1; ?>
                                        </span>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($article['reference']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($article['nom_article']); ?></td>
                                    <td class="text-danger"><?php echo formatCurrency($article['prix_achat']); ?></td>
                                    <td class="text-success"><?php echo formatCurrency($article['prix_vente']); ?></td>
                                    <td class="text-info">
                                        <strong><?php echo formatCurrency($margeUnitaire); ?></strong>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-<?php echo $pourcentageMarge > 30 ? 'success' : ($pourcentageMarge > 15 ? 'warning' : 'danger'); ?>" 
                                                 role="progressbar" style="width: <?php echo min($pourcentageMarge, 100); ?>%;">
                                                <?php echo number_format($pourcentageMarge, 1); ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-success"><?php echo formatCurrency($article['chiffre_affaires']); ?></td>
                                    <td class="text-primary">
                                        <strong><?php echo formatCurrency($beneficeEstime); ?></strong>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo $badgeClass; ?>">
                                            <?php echo $rentabiliteLevel; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Gestion du stock -->
            <div class="tab-pane fade" id="stock" role="tabpanel">
                <div class="mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-exclamation-triangle text-warning"></i> Articles en Stock Faible</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-warning">
                                    <thead>
                                        <tr>
                                            <th>Article</th>
                                            <th>Stock</th>
                                            <th>Minimum</th>
                                            <th>CA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($stockFaible, 0, 10) as $article): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($article['nom_article']); ?></strong>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($article['reference']); ?></small>
                                                </td>
                                                <td class="text-danger"><strong><?php echo $article['stock_actuel']; ?></strong></td>
                                                <td><?php echo $article['stock_minimum']; ?></td>
                                                <td class="text-success"><?php echo formatCurrency($article['chiffre_affaires']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-chart-pie text-info"></i> Répartition Stock</h5>
                            <canvas id="stockChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Évolution des ventes -->
            <div class="tab-pane fade" id="evolution" role="tabpanel">
                <div class="chart-container mt-3">
                    <h5><i class="fas fa-line-chart"></i> Évolution des Top 5 Articles</h5>
                    <canvas id="evolutionArticlesChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Graphique de répartition du stock
    const articlesStock = <?php echo json_encode(array_filter($articles, function($a) { return $a['stock_actuel'] !== null; })); ?>;
    
    if (articlesStock.length > 0) {
        const stockOk = articlesStock.filter(a => a.stock_actuel > (a.stock_minimum || 0)).length;
        const stockFaible = articlesStock.filter(a => a.stock_actuel <= (a.stock_minimum || 0)).length;
        const stockVide = articlesStock.filter(a => a.stock_actuel == 0).length;
        
        const stockCtx = document.getElementById('stockChart').getContext('2d');
        new Chart(stockCtx, {
            type: 'doughnut',
            data: {
                labels: ['Stock Normal', 'Stock Faible', 'Stock Vide'],
                datasets: [{
                    data: [stockOk, stockFaible - stockVide, stockVide],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'État du Stock par Articles'
                    }
                }
            }
        });
    }

    // Graphique d'évolution des top articles
    const evolutionData = <?php echo json_encode($evolutionData); ?>;
    
    if (evolutionData.length > 0) {
        const evolutionCtx = document.getElementById('evolutionArticlesChart').getContext('2d');
        
        // Groupement par article
        const articleGroups = {};
        const dates = [...new Set(evolutionData.map(d => d.date_vente))].sort();
        
        evolutionData.forEach(item => {
            if (!articleGroups[item.reference]) {
                articleGroups[item.reference] = {
                    name: item.nom_article,
                    data: dates.map(date => {
                        const dayData = evolutionData.find(d => d.reference === item.reference && d.date_vente === date);
                        return dayData ? parseFloat(dayData.ca_jour) : 0;
                    })
                };
            }
        });

        const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'];
        const datasets = Object.keys(articleGroups).slice(0, 5).map((ref, index) => ({
            label: articleGroups[ref].name,
            data: articleGroups[ref].data,
            borderColor: colors[index],
            backgroundColor: colors[index] + '20',
            tension: 0.4,
            fill: false
        }));

        new Chart(evolutionCtx, {
            type: 'line',
            data: {
                labels: dates.map(d => new Date(d).toLocaleDateString('fr-FR')),
                datasets: datasets
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Évolution du CA des Top 5 Articles'
                    },
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' DH';
                            }
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45
                        }
                    }
                }
            }
        });
    }
    </script>

    <?php

} catch (PDOException $e) {
    echo '<div class="alert alert-danger animate-fadeIn">';
    echo '<i class="fas fa-exclamation-triangle"></i> ';
    echo 'Erreur lors du chargement du rapport articles: ' . htmlspecialchars($e->getMessage());
    echo '</div>';
}
?>
