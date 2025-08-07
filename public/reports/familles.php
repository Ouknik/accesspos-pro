<?php
/**
 * Rapport des familles d'articles - Analyse détaillée des ventes par famille
 * Inclut les tendances, parts de marché et performances
 */

try {
    // Rapport détaillé des familles
    $famillesQuery = $pdo->prepare("
        SELECT 
            ISNULL(a.ART_FAMILLE, 'Non définie') as famille,
            f.FAM_LIBELLE as nom_famille,
            f.FAM_DESCRIPTION as description_famille,
            COUNT(DISTINCT fv.FCTV_REF) as nombre_factures,
            SUM(CASE WHEN fv.FCTV_MNT_TTC IS NOT NULL THEN fv.FCTV_MNT_TTC ELSE 0 END) as chiffre_affaires,
            AVG(CASE WHEN fv.FCTV_MNT_TTC IS NOT NULL THEN fv.FCTV_MNT_TTC ELSE 0 END) as panier_moyen,
            COUNT(DISTINCT a.ART_REF) as nombre_articles,
            MIN(fv.FCTV_DATE) as premiere_vente,
            MAX(fv.FCTV_DATE) as derniere_vente,
            COUNT(DISTINCT fv.CLT_REF) as clients_uniques
        FROM FACTURE_VNT fv
        LEFT JOIN ARTICLE a ON fv.ART_REF = a.ART_REF
        LEFT JOIN FAMILLE f ON a.ART_FAMILLE = f.FAM_REF
        WHERE fv.FCTV_DATE BETWEEN ? AND ? 
            AND fv.FCTV_VALIDE = 1
        GROUP BY a.ART_FAMILLE, f.FAM_LIBELLE, f.FAM_DESCRIPTION
        ORDER BY chiffre_affaires DESC
    ");
    $famillesQuery->execute([$dateFrom, $dateTo]);
    $familles = $famillesQuery->fetchAll(PDO::FETCH_ASSOC);

    // Articles les plus vendus par famille
    $topArticles = $pdo->prepare("
        SELECT 
            ISNULL(a.ART_FAMILLE, 'Non définie') as famille,
            a.ART_REF as reference_article,
            a.ART_LIBELLE as nom_article,
            a.ART_PRIX_VNT as prix_vente,
            COUNT(DISTINCT fv.FCTV_REF) as nombre_ventes,
            SUM(CASE WHEN fv.FCTV_MNT_TTC IS NOT NULL THEN fv.FCTV_MNT_TTC ELSE 0 END) as ca_article
        FROM FACTURE_VNT fv
        LEFT JOIN ARTICLE a ON fv.ART_REF = a.ART_REF
        WHERE fv.FCTV_DATE BETWEEN ? AND ? 
            AND fv.FCTV_VALIDE = 1
            AND a.ART_REF IS NOT NULL
        GROUP BY a.ART_FAMILLE, a.ART_REF, a.ART_LIBELLE, a.ART_PRIX_VNT
        ORDER BY a.ART_FAMILLE, ca_article DESC
    ");
    $topArticles->execute([$dateFrom, $dateTo]);
    $articlesData = $topArticles->fetchAll(PDO::FETCH_ASSOC);

    // Évolution des ventes par famille sur la période
    $evolutionFamilles = $pdo->prepare("
        SELECT 
            ISNULL(a.ART_FAMILLE, 'Non définie') as famille,
            CAST(fv.FCTV_DATE as DATE) as date_vente,
            SUM(CASE WHEN fv.FCTV_MNT_TTC IS NOT NULL THEN fv.FCTV_MNT_TTC ELSE 0 END) as ca_jour
        FROM FACTURE_VNT fv
        LEFT JOIN ARTICLE a ON fv.ART_REF = a.ART_REF
        WHERE fv.FCTV_DATE BETWEEN ? AND ? 
            AND fv.FCTV_VALIDE = 1
        GROUP BY a.ART_FAMILLE, CAST(fv.FCTV_DATE as DATE)
        ORDER BY famille, date_vente
    ");
    $evolutionFamilles->execute([$dateFrom, $dateTo]);
    $evolutionData = $evolutionFamilles->fetchAll(PDO::FETCH_ASSOC);

    // Calcul des totaux pour les pourcentages
    $totalCA = array_sum(array_column($familles, 'chiffre_affaires'));
    $totalFactures = array_sum(array_column($familles, 'nombre_factures'));

    // Groupement des articles par famille
    $articlesByFamille = [];
    foreach ($articlesData as $article) {
        $articlesByFamille[$article['famille']][] = $article;
    }

    ?>

    <div class="report-card animate-fadeIn">
        <div class="report-title">
            <span><i class="fas fa-layer-group"></i> Rapport d'Analyse des Familles d'Articles</span>
            <small class="text-muted">Période: <?php echo date('d/m/Y', strtotime($dateFrom)) . ' - ' . date('d/m/Y', strtotime($dateTo)); ?></small>
        </div>

        <!-- Résumé des performances par famille -->
        <div class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-primary"><?php echo count($familles); ?></h4>
                        <p class="mb-0">Familles Actives</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-success"><?php echo formatCurrency($totalCA); ?></h4>
                        <p class="mb-0">CA Total</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-info"><?php echo array_sum(array_column($familles, 'nombre_articles')); ?></h4>
                        <p class="mb-0">Articles Vendus</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-warning"><?php echo formatCurrency($totalCA / max(count($familles), 1)); ?></h4>
                        <p class="mb-0">CA Moyen/Famille</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique en secteurs des parts de marché -->
        <div class="chart-container mb-4">
            <h5><i class="fas fa-pie-chart"></i> Répartition du Chiffre d'Affaires par Famille</h5>
            <div class="row">
                <div class="col-md-8">
                    <canvas id="famillesChart"></canvas>
                </div>
                <div class="col-md-4">
                    <div class="legend-container">
                        <?php foreach (array_slice($familles, 0, 8) as $index => $famille): 
                            $pourcentage = $totalCA > 0 ? ($famille['chiffre_affaires'] / $totalCA) * 100 : 0;
                        ?>
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: <?php echo ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'][$index % 8]; ?>;"></span>
                                <span class="legend-text">
                                    <?php echo htmlspecialchars($famille['nom_famille'] ?: $famille['famille']); ?>
                                    <small class="text-muted">(<?php echo number_format($pourcentage, 1); ?>%)</small>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau détaillé des familles -->
        <?php if (!empty($familles)): ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Rang</th>
                            <th>Code Famille</th>
                            <th>Nom Famille</th>
                            <th>Description</th>
                            <th>Nb Articles</th>
                            <th>Nb Factures</th>
                            <th>Chiffre d'Affaires</th>
                            <th>% du CA Total</th>
                            <th>Panier Moyen</th>
                            <th>Clients Uniques</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($familles as $index => $famille): 
                            $pourcentageCA = $totalCA > 0 ? ($famille['chiffre_affaires'] / $totalCA) * 100 : 0;
                            $performanceLevel = $pourcentageCA > 20 ? 'Excellent' : ($pourcentageCA > 10 ? 'Très Bon' : ($pourcentageCA > 5 ? 'Bon' : 'Moyen'));
                            $badgeClass = $pourcentageCA > 20 ? 'badge-success' : ($pourcentageCA > 10 ? 'badge-warning' : ($pourcentageCA > 5 ? 'badge-info' : 'badge-secondary'));
                        ?>
                            <tr>
                                <td>
                                    <span class="status-badge <?php echo $badgeClass; ?>">
                                        #<?php echo $index + 1; ?>
                                    </span>
                                </td>
                                <td><strong><?php echo htmlspecialchars($famille['famille']); ?></strong></td>
                                <td><?php echo htmlspecialchars($famille['nom_famille'] ?: 'Non définie'); ?></td>
                                <td>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars(substr($famille['description_famille'] ?: '', 0, 50) . (strlen($famille['description_famille'] ?: '') > 50 ? '...' : '')); ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge badge-info"><?php echo number_format($famille['nombre_articles']); ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-primary"><?php echo number_format($famille['nombre_factures']); ?></span>
                                </td>
                                <td class="text-success">
                                    <strong><?php echo formatCurrency($famille['chiffre_affaires']); ?></strong>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: <?php echo min($pourcentageCA, 100); ?>%;">
                                            <?php echo number_format($pourcentageCA, 1); ?>%
                                        </div>
                                    </div>
                                </td>
                                <td class="text-info">
                                    <?php echo formatCurrency($famille['panier_moyen']); ?>
                                </td>
                                <td>
                                    <span class="badge badge-warning"><?php echo number_format($famille['clients_uniques']); ?></span>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $badgeClass; ?>">
                                        <?php echo $performanceLevel; ?>
                                    </span>
                                </td>
                            </tr>
                            
                            <!-- Détail des meilleurs articles par famille -->
                            <?php if (isset($articlesByFamille[$famille['famille']])): ?>
                                <tr class="details-row">
                                    <td colspan="11">
                                        <div class="details-panel">
                                            <h6><i class="fas fa-star"></i> Top Articles de cette famille:</h6>
                                            <div class="row">
                                                <?php foreach (array_slice($articlesByFamille[$famille['famille']], 0, 3) as $article): ?>
                                                    <div class="col-md-4">
                                                        <div class="article-card">
                                                            <strong><?php echo htmlspecialchars($article['nom_article']); ?></strong>
                                                            <br><small class="text-muted">Réf: <?php echo htmlspecialchars($article['reference_article']); ?></small>
                                                            <br><span class="text-success"><?php echo formatCurrency($article['ca_article']); ?></span>
                                                            <br><small><?php echo number_format($article['nombre_ventes']); ?> ventes</small>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Graphique d'évolution des Top 5 familles -->
            <div class="chart-container mt-4">
                <h5><i class="fas fa-line-chart"></i> Évolution des Top 5 Familles</h5>
                <canvas id="evolutionFamillesChart" height="80"></canvas>
            </div>

        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Aucune donnée de famille disponible pour cette période.
            </div>
        <?php endif; ?>
    </div>

    <style>
    .legend-container {
        padding: 20px;
    }
    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 3px;
        margin-right: 10px;
        flex-shrink: 0;
    }
    .legend-text {
        font-size: 0.9em;
    }
    .details-row {
        background-color: #f8f9fa;
        border-top: none !important;
    }
    .details-panel {
        padding: 15px;
        margin: 5px 0;
    }
    .article-card {
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 10px;
        text-align: center;
    }
    </style>

    <script>
    <?php if (!empty($familles)): ?>
        // Graphique en secteurs des familles
        const famillesCtx = document.getElementById('famillesChart').getContext('2d');
        const famillesData = <?php echo json_encode(array_slice($familles, 0, 8)); ?>;
        
        new Chart(famillesCtx, {
            type: 'doughnut',
            data: {
                labels: famillesData.map(f => f.nom_famille || f.famille),
                datasets: [{
                    data: famillesData.map(f => parseFloat(f.chiffre_affaires)),
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
                        '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Répartition du CA par Famille d\'Articles'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.raw / total) * 100).toFixed(1);
                                return context.label + ': ' + context.raw.toLocaleString() + ' DH (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Graphique d'évolution des Top 5 familles
        const evolutionCtx = document.getElementById('evolutionFamillesChart').getContext('2d');
        const evolutionData = <?php echo json_encode($evolutionData); ?>;
        const top5Familles = famillesData.slice(0, 5);
        
        // Préparation des données par famille et par date
        const evolutionByFamille = {};
        const dates = [...new Set(evolutionData.map(d => d.date_vente))].sort();
        
        top5Familles.forEach(famille => {
            evolutionByFamille[famille.famille] = dates.map(date => {
                const dayData = evolutionData.find(d => d.famille === famille.famille && d.date_vente === date);
                return dayData ? parseFloat(dayData.ca_jour) : 0;
            });
        });

        const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'];
        
        new Chart(evolutionCtx, {
            type: 'line',
            data: {
                labels: dates.map(d => new Date(d).toLocaleDateString('fr-FR')),
                datasets: top5Familles.map((famille, index) => ({
                    label: famille.nom_famille || famille.famille,
                    data: evolutionByFamille[famille.famille],
                    borderColor: colors[index],
                    backgroundColor: colors[index] + '20',
                    tension: 0.4,
                    fill: false
                }))
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Évolution du CA des Top 5 Familles'
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
    <?php endif; ?>
    </script>

    <?php

} catch (PDOException $e) {
    echo '<div class="alert alert-danger animate-fadeIn">';
    echo '<i class="fas fa-exclamation-triangle"></i> ';
    echo 'Erreur lors du chargement du rapport familles: ' . htmlspecialchars($e->getMessage());
    echo '</div>';
}
?>
