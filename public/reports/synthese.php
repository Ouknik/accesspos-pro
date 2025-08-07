<?php
/**
 * Rapport de synthèse - Vue d'ensemble complète et recommandations
 * Consolidation de tous les indicateurs et analyse stratégique
 */

try {
    // KPIs globaux de synthèse
    $syntheseGlobale = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT FCTV_REF) as total_factures,
            SUM(FCTV_MNT_TTC) as chiffre_affaires_total,
            AVG(FCTV_MNT_TTC) as panier_moyen,
            COUNT(DISTINCT CLT_REF) as clients_uniques,
            COUNT(DISTINCT FCTV_SERVEUR) as serveurs_actifs,
            COUNT(DISTINCT FCTV_UTILISATEUR) as caissiers_actifs,
            MIN(FCTV_DATE) as premiere_vente,
            MAX(FCTV_DATE) as derniere_vente
        FROM FACTURE_VNT
        WHERE FCTV_DATE BETWEEN ? AND ?
            AND FCTV_VALIDE = 1
    ");
    $syntheseGlobale->execute([$dateFrom, $dateTo]);
    $kpisGlobaux = $syntheseGlobale->fetch(PDO::FETCH_ASSOC);

    // Comparaison avec période précédente
    $datePrecedenteFrom = date('Y-m-d', strtotime($dateFrom . ' -' . ((strtotime($dateTo) - strtotime($dateFrom)) / 86400) . ' days'));
    $datePrecedenteTo = date('Y-m-d', strtotime($dateFrom . ' -1 day'));
    
    $synthesePrecedente = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT FCTV_REF) as total_factures,
            SUM(FCTV_MNT_TTC) as chiffre_affaires_total,
            AVG(FCTV_MNT_TTC) as panier_moyen,
            COUNT(DISTINCT CLT_REF) as clients_uniques
        FROM FACTURE_VNT
        WHERE FCTV_DATE BETWEEN ? AND ?
            AND FCTV_VALIDE = 1
    ");
    $synthesePrecedente->execute([$datePrecedenteFrom, $datePrecedenteTo]);
    $kpisPrecedents = $synthesePrecedente->fetch(PDO::FETCH_ASSOC);

    // Top performers
    $topServeur = $pdo->prepare("
        SELECT TOP 1
            ISNULL(fv.FCTV_SERVEUR, 'Non défini') as serveur,
            CASE 
                WHEN u.USR_PRENOM IS NOT NULL AND u.USR_NOM IS NOT NULL 
                THEN CONCAT(u.USR_PRENOM, ' ', u.USR_NOM)
                ELSE ISNULL(fv.FCTV_SERVEUR, 'Non défini')
            END as nom_serveur,
            SUM(fv.FCTV_MNT_TTC) as chiffre_affaires
        FROM FACTURE_VNT fv
        LEFT JOIN UTILISATEUR u ON (fv.FCTV_SERVEUR = u.USR_LOGIN OR fv.FCTV_SERVEUR = u.USR_REF)
        WHERE fv.FCTV_DATE BETWEEN ? AND ? AND fv.FCTV_VALIDE = 1
        GROUP BY fv.FCTV_SERVEUR, u.USR_PRENOM, u.USR_NOM
        ORDER BY chiffre_affaires DESC
    ");
    $topServeur->execute([$dateFrom, $dateTo]);
    $meilleurServeur = $topServeur->fetch(PDO::FETCH_ASSOC);

    $topFamille = $pdo->prepare("
        SELECT TOP 1
            ISNULL(a.ART_FAMILLE, 'Non définie') as famille,
            f.FAM_LIBELLE as nom_famille,
            SUM(CASE WHEN fv.FCTV_MNT_TTC IS NOT NULL THEN fv.FCTV_MNT_TTC ELSE 0 END) as chiffre_affaires
        FROM FACTURE_VNT fv
        LEFT JOIN ARTICLE a ON fv.ART_REF = a.ART_REF
        LEFT JOIN FAMILLE f ON a.ART_FAMILLE = f.FAM_REF
        WHERE fv.FCTV_DATE BETWEEN ? AND ? 
            AND fv.FCTV_VALIDE = 1
        GROUP BY a.ART_FAMILLE, f.FAM_LIBELLE
        ORDER BY chiffre_affaires DESC
    ");
    $topFamille->execute([$dateFrom, $dateTo]);
    $meilleureFamille = $topFamille->fetch(PDO::FETCH_ASSOC);

    $topClient = $pdo->prepare("
        SELECT TOP 1
            c.CLT_REF as reference_client,
            CASE 
                WHEN c.CLT_PRENOM IS NOT NULL AND c.CLT_NOM IS NOT NULL 
                THEN CONCAT(c.CLT_PRENOM, ' ', c.CLT_NOM)
                ELSE 'Client #' + c.CLT_REF
            END as nom_client,
            SUM(fv.FCTV_MNT_TTC) as chiffre_affaires,
            COUNT(DISTINCT fv.FCTV_REF) as nombre_achats
        FROM CLIENT c
        INNER JOIN FACTURE_VNT fv ON c.CLT_REF = fv.CLT_REF
        WHERE fv.FCTV_DATE BETWEEN ? AND ?
            AND fv.FCTV_VALIDE = 1
        GROUP BY c.CLT_REF, c.CLT_PRENOM, c.CLT_NOM
        ORDER BY chiffre_affaires DESC
    ");
    $topClient->execute([$dateFrom, $dateTo]);
    $meilleurClient = $topClient->fetch(PDO::FETCH_ASSOC);

    // Tendances et évolution
    $tendancesHebdo = $pdo->prepare("
        SELECT 
            DATEPART(WEEK, FCTV_DATE) as semaine,
            YEAR(FCTV_DATE) as annee,
            SUM(FCTV_MNT_TTC) as ca_semaine,
            COUNT(DISTINCT FCTV_REF) as nb_factures_semaine
        FROM FACTURE_VNT
        WHERE FCTV_DATE BETWEEN ? AND ?
            AND FCTV_VALIDE = 1
        GROUP BY DATEPART(WEEK, FCTV_DATE), YEAR(FCTV_DATE)
        ORDER BY annee, semaine
    ");
    $tendancesHebdo->execute([$dateFrom, $dateTo]);
    $tendancesData = $tendancesHebdo->fetchAll(PDO::FETCH_ASSOC);

    // Analyse des heures de pointe
    $heuresPointe = $pdo->prepare("
        SELECT 
            DATEPART(HOUR, FCTV_DATE) as heure,
            SUM(FCTV_MNT_TTC) as ca_heure,
            COUNT(DISTINCT FCTV_REF) as nb_transactions
        FROM FACTURE_VNT
        WHERE FCTV_DATE BETWEEN ? AND ?
            AND FCTV_VALIDE = 1
        GROUP BY DATEPART(HOUR, FCTV_DATE)
        ORDER BY ca_heure DESC
    ");
    $heuresPointe->execute([$dateFrom, $dateTo]);
    $heuresData = $heuresPointe->fetchAll(PDO::FETCH_ASSOC);

    // Calculs d'évolution
    function calculerEvolution($actuel, $precedent) {
        if ($precedent > 0) {
            return (($actuel - $precedent) / $precedent) * 100;
        }
        return $actuel > 0 ? 100 : 0;
    }

    $evolutionCA = calculerEvolution($kpisGlobaux['chiffre_affaires_total'], $kpisPrecedents['chiffre_affaires_total']);
    $evolutionFactures = calculerEvolution($kpisGlobaux['total_factures'], $kpisPrecedents['total_factures']);
    $evolutionPanier = calculerEvolution($kpisGlobaux['panier_moyen'], $kpisPrecedents['panier_moyen']);
    $evolutionClients = calculerEvolution($kpisGlobaux['clients_uniques'], $kpisPrecedents['clients_uniques']);

    // Alertes et recommandations
    $alertes = [];
    $recommandations = [];

    // Analyse et génération d'alertes
    if ($evolutionCA < -10) {
        $alertes[] = ['type' => 'danger', 'message' => 'Baisse significative du chiffre d\'affaires (-' . number_format(abs($evolutionCA), 1) . '%)'];
        $recommandations[] = 'Analyser les causes de la baisse du CA et mettre en place des actions correctives.';
    } elseif ($evolutionCA > 20) {
        $alertes[] = ['type' => 'success', 'message' => 'Excellente croissance du chiffre d\'affaires (+' . number_format($evolutionCA, 1) . '%)'];
        $recommandations[] = 'Capitaliser sur cette croissance en identifiant les facteurs de succès.';
    }

    if ($kpisGlobaux['panier_moyen'] < 100) {
        $alertes[] = ['type' => 'warning', 'message' => 'Panier moyen faible (' . formatCurrency($kpisGlobaux['panier_moyen']) . ')'];
        $recommandations[] = 'Développer des stratégies de vente additionnelle pour augmenter le panier moyen.';
    }

    // Heures de pointe
    $heurePlusFort = $heuresData[0] ?? null;
    if ($heurePlusFort) {
        $recommandations[] = 'Optimiser les ressources humaines autour de ' . $heurePlusFort['heure'] . 'h (heure de pointe).';
    }

    ?>

    <div class="report-card animate-fadeIn">
        <div class="report-title">
            <span><i class="fas fa-chart-line"></i> Rapport de Synthèse Exécutive</span>
            <small class="text-muted">Période: <?php echo date('d/m/Y', strtotime($dateFrom)) . ' - ' . date('d/m/Y', strtotime($dateTo)); ?></small>
        </div>

        <!-- Résumé exécutif -->
        <div class="executive-summary mb-4">
            <div class="row">
                <div class="col-md-8">
                    <h5><i class="fas fa-clipboard-list"></i> Résumé Exécutif</h5>
                    <div class="summary-content">
                        <p class="lead">
                            Au cours de la période analysée, l'entreprise a réalisé un chiffre d'affaires de 
                            <strong class="text-success"><?php echo formatCurrency($kpisGlobaux['chiffre_affaires_total']); ?></strong>
                            <?php if ($evolutionCA != 0): ?>
                                <?php if ($evolutionCA > 0): ?>
                                    <span class="text-success">(+<?php echo number_format($evolutionCA, 1); ?>%)</span>
                                <?php else: ?>
                                    <span class="text-danger">(<?php echo number_format($evolutionCA, 1); ?>%)</span>
                                <?php endif; ?>
                            <?php endif; ?>
                            sur <strong><?php echo number_format($kpisGlobaux['total_factures']); ?></strong> transactions.
                        </p>
                        <p>
                            Le panier moyen s'établit à <strong><?php echo formatCurrency($kpisGlobaux['panier_moyen']); ?></strong>
                            avec <strong><?php echo number_format($kpisGlobaux['clients_uniques']); ?></strong> clients uniques servis
                            par <strong><?php echo number_format($kpisGlobaux['serveurs_actifs']); ?></strong> serveurs actifs.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="performance-gauge">
                        <h6>Performance Globale</h6>
                        <div class="gauge-container">
                            <canvas id="performanceGauge" width="200" height="120"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPIs avec évolution -->
        <div class="kpis-evolution mb-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="kpi-evolution-card">
                        <div class="kpi-header">
                            <h3 class="text-success"><?php echo formatCurrency($kpisGlobaux['chiffre_affaires_total']); ?></h3>
                            <div class="evolution-badge <?php echo $evolutionCA >= 0 ? 'positive' : 'negative'; ?>">
                                <i class="fas fa-arrow-<?php echo $evolutionCA >= 0 ? 'up' : 'down'; ?>"></i>
                                <?php echo number_format(abs($evolutionCA), 1); ?>%
                            </div>
                        </div>
                        <p class="mb-0">Chiffre d'Affaires</p>
                        <small class="text-muted">vs période précédente</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-evolution-card">
                        <div class="kpi-header">
                            <h3 class="text-primary"><?php echo number_format($kpisGlobaux['total_factures']); ?></h3>
                            <div class="evolution-badge <?php echo $evolutionFactures >= 0 ? 'positive' : 'negative'; ?>">
                                <i class="fas fa-arrow-<?php echo $evolutionFactures >= 0 ? 'up' : 'down'; ?>"></i>
                                <?php echo number_format(abs($evolutionFactures), 1); ?>%
                            </div>
                        </div>
                        <p class="mb-0">Total Factures</p>
                        <small class="text-muted">vs période précédente</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-evolution-card">
                        <div class="kpi-header">
                            <h3 class="text-info"><?php echo formatCurrency($kpisGlobaux['panier_moyen']); ?></h3>
                            <div class="evolution-badge <?php echo $evolutionPanier >= 0 ? 'positive' : 'negative'; ?>">
                                <i class="fas fa-arrow-<?php echo $evolutionPanier >= 0 ? 'up' : 'down'; ?>"></i>
                                <?php echo number_format(abs($evolutionPanier), 1); ?>%
                            </div>
                        </div>
                        <p class="mb-0">Panier Moyen</p>
                        <small class="text-muted">vs période précédente</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-evolution-card">
                        <div class="kpi-header">
                            <h3 class="text-warning"><?php echo number_format($kpisGlobaux['clients_uniques']); ?></h3>
                            <div class="evolution-badge <?php echo $evolutionClients >= 0 ? 'positive' : 'negative'; ?>">
                                <i class="fas fa-arrow-<?php echo $evolutionClients >= 0 ? 'up' : 'down'; ?>"></i>
                                <?php echo number_format(abs($evolutionClients), 1); ?>%
                            </div>
                        </div>
                        <p class="mb-0">Clients Uniques</p>
                        <small class="text-muted">vs période précédente</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes et notifications -->
        <?php if (!empty($alertes)): ?>
        <div class="alerts-section mb-4">
            <h5><i class="fas fa-bell"></i> Alertes et Notifications</h5>
            <?php foreach ($alertes as $alerte): ?>
                <div class="alert alert-<?php echo $alerte['type']; ?> alert-dismissible">
                    <i class="fas fa-<?php echo $alerte['type'] === 'success' ? 'check-circle' : ($alerte['type'] === 'warning' ? 'exclamation-triangle' : 'exclamation-circle'); ?>"></i>
                    <?php echo htmlspecialchars($alerte['message']); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Top Performers -->
        <div class="top-performers mb-4">
            <h5><i class="fas fa-trophy"></i> Top Performers de la Période</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="performer-card">
                        <div class="performer-icon">
                            <i class="fas fa-user-tie text-primary"></i>
                        </div>
                        <div class="performer-info">
                            <h6>Meilleur Serveur</h6>
                            <strong><?php echo htmlspecialchars($meilleurServeur['nom_serveur'] ?? 'N/A'); ?></strong>
                            <br><small class="text-success"><?php echo formatCurrency($meilleurServeur['chiffre_affaires'] ?? 0); ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="performer-card">
                        <div class="performer-icon">
                            <i class="fas fa-layer-group text-warning"></i>
                        </div>
                        <div class="performer-info">
                            <h6>Meilleure Famille</h6>
                            <strong><?php echo htmlspecialchars($meilleureFamille['nom_famille'] ?? $meilleureFamille['famille'] ?? 'N/A'); ?></strong>
                            <br><small class="text-success"><?php echo formatCurrency($meilleureFamille['chiffre_affaires'] ?? 0); ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="performer-card">
                        <div class="performer-icon">
                            <i class="fas fa-user text-info"></i>
                        </div>
                        <div class="performer-info">
                            <h6>Meilleur Client</h6>
                            <strong><?php echo htmlspecialchars($meilleurClient['nom_client'] ?? 'N/A'); ?></strong>
                            <br><small class="text-success"><?php echo formatCurrency($meilleurClient['chiffre_affaires'] ?? 0); ?></small>
                            <br><small class="text-muted"><?php echo number_format($meilleurClient['nombre_achats'] ?? 0); ?> achats</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques de tendances -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="chart-container">
                    <h5><i class="fas fa-chart-line"></i> Évolution Hebdomadaire</h5>
                    <canvas id="tendancesChart" height="100"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h5><i class="fas fa-clock"></i> Analyse des Heures de Pointe</h5>
                    <canvas id="heuresChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Recommandations stratégiques -->
        <div class="recommendations-section">
            <h5><i class="fas fa-lightbulb"></i> Recommandations Stratégiques</h5>
            <div class="recommendations-grid">
                <?php foreach ($recommandations as $index => $recommandation): ?>
                    <div class="recommendation-item">
                        <div class="recommendation-number"><?php echo $index + 1; ?></div>
                        <div class="recommendation-content">
                            <p><?php echo htmlspecialchars($recommandation); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <!-- Recommandations additionnelles basées sur l'analyse -->
                <div class="recommendation-item">
                    <div class="recommendation-number"><?php echo count($recommandations) + 1; ?></div>
                    <div class="recommendation-content">
                        <p>Mettre en place un système de fidélisation client pour augmenter la récurrence d'achat.</p>
                    </div>
                </div>
                
                <div class="recommendation-item">
                    <div class="recommendation-number"><?php echo count($recommandations) + 2; ?></div>
                    <div class="recommendation-content">
                        <p>Former les équipes sur les techniques de vente croisée et de montée en gamme.</p>
                    </div>
                </div>
                
                <div class="recommendation-item">
                    <div class="recommendation-number"><?php echo count($recommandations) + 3; ?></div>
                    <div class="recommendation-content">
                        <p>Analyser régulièrement les performances pour identifier rapidement les tendances.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .executive-summary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
    }
    .summary-content .lead {
        font-size: 1.1em;
        margin-bottom: 15px;
    }
    .performance-gauge {
        text-align: center;
        background-color: rgba(255,255,255,0.1);
        padding: 20px;
        border-radius: 8px;
    }
    .kpi-evolution-card {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .kpi-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 10px;
    }
    .evolution-badge {
        font-size: 0.8em;
        padding: 4px 8px;
        border-radius: 20px;
        font-weight: bold;
    }
    .evolution-badge.positive {
        background-color: #d4edda;
        color: #155724;
    }
    .evolution-badge.negative {
        background-color: #f8d7da;
        color: #721c24;
    }
    .performer-card {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .performer-icon {
        font-size: 2em;
        margin-bottom: 15px;
    }
    .recommendations-grid {
        display: grid;
        gap: 15px;
    }
    .recommendation-item {
        display: flex;
        align-items: flex-start;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
    }
    .recommendation-number {
        background: #007bff;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 15px;
        flex-shrink: 0;
    }
    .recommendation-content {
        flex: 1;
    }
    .recommendation-content p {
        margin: 0;
        font-size: 0.95em;
        line-height: 1.4;
    }
    </style>

    <script>
    // Graphique de tendances hebdomadaires
    const tendancesData = <?php echo json_encode($tendancesData); ?>;
    if (tendancesData.length > 0) {
        const tendancesCtx = document.getElementById('tendancesChart').getContext('2d');
        
        new Chart(tendancesCtx, {
            type: 'line',
            data: {
                labels: tendancesData.map(d => `S${d.semaine}`),
                datasets: [{
                    label: 'CA Hebdomadaire (DH)',
                    data: tendancesData.map(d => parseFloat(d.ca_semaine)),
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
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
                    }
                }
            }
        });
    }

    // Graphique des heures de pointe
    const heuresData = <?php echo json_encode(array_slice($heuresData, 0, 12)); ?>;
    if (heuresData.length > 0) {
        const heuresCtx = document.getElementById('heuresChart').getContext('2d');
        
        new Chart(heuresCtx, {
            type: 'bar',
            data: {
                labels: heuresData.map(d => d.heure + 'h'),
                datasets: [{
                    label: 'CA par Heure (DH)',
                    data: heuresData.map(d => parseFloat(d.ca_heure)),
                    backgroundColor: 'rgba(255, 193, 7, 0.8)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
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
                    }
                }
            }
        });
    }

    // Jauge de performance (simulation)
    const performanceCtx = document.getElementById('performanceGauge').getContext('2d');
    const performanceScore = Math.min(100, Math.max(0, 
        50 + (<?php echo $evolutionCA; ?> * 2) + 
        (<?php echo $kpisGlobaux['panier_moyen']; ?> / 10)
    ));

    new Chart(performanceCtx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [performanceScore, 100 - performanceScore],
                backgroundColor: [
                    performanceScore >= 75 ? '#28a745' : (performanceScore >= 50 ? '#ffc107' : '#dc3545'),
                    '#e9ecef'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: false,
            circumference: Math.PI,
            rotation: Math.PI,
            cutout: '75%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: false
                }
            }
        },
        plugins: [{
            beforeDraw: function(chart) {
                const width = chart.width;
                const height = chart.height;
                const ctx = chart.ctx;
                
                ctx.restore();
                const fontSize = (height / 114).toFixed(2);
                ctx.font = fontSize + "em Arial";
                ctx.textBaseline = "middle";
                
                const text = Math.round(performanceScore) + "%";
                const textX = Math.round((width - ctx.measureText(text).width) / 2);
                const textY = height / 1.4;
                
                ctx.fillStyle = '#495057';
                ctx.fillText(text, textX, textY);
                ctx.save();
            }
        }]
    });
    </script>

    <?php

} catch (PDOException $e) {
    echo '<div class="alert alert-danger animate-fadeIn">';
    echo '<i class="fas fa-exclamation-triangle"></i> ';
    echo 'Erreur lors du chargement du rapport de synthèse: ' . htmlspecialchars($e->getMessage());
    echo '</div>';
}
?>
