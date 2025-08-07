<?php
/**
 * Rapport des serveurs - Analyse détaillée des performances par serveur
 * Inclut les statistiques de vente, commissions et comparaisons
 */

try {
    // Rapport détaillé des serveurs
    $serveursQuery = $pdo->prepare("
        SELECT 
            ISNULL(fv.FCTV_SERVEUR, 'Non défini') as code_serveur,
            CASE 
                WHEN u.USR_PRENOM IS NOT NULL AND u.USR_NOM IS NOT NULL 
                THEN CONCAT(u.USR_PRENOM, ' ', u.USR_NOM)
                ELSE ISNULL(fv.FCTV_SERVEUR, 'Non défini')
            END as nom_serveur,
            u.USR_EMAIL as email_serveur,
            u.USR_TELEPHONE as telephone_serveur,
            COUNT(DISTINCT fv.FCTV_REF) as nombre_factures,
            SUM(fv.FCTV_MNT_TTC) as chiffre_affaires,
            AVG(fv.FCTV_MNT_TTC) as panier_moyen,
            MIN(fv.FCTV_MNT_TTC) as vente_min,
            MAX(fv.FCTV_MNT_TTC) as vente_max,
            COUNT(DISTINCT fv.CLT_REF) as clients_uniques,
            MIN(fv.FCTV_DATE) as premiere_vente,
            MAX(fv.FCTV_DATE) as derniere_vente
        FROM FACTURE_VNT fv
        LEFT JOIN UTILISATEUR u ON (fv.FCTV_SERVEUR = u.USR_LOGIN OR fv.FCTV_SERVEUR = u.USR_REF)
        WHERE fv.FCTV_DATE BETWEEN ? AND ? 
            AND fv.FCTV_VALIDE = 1
        GROUP BY fv.FCTV_SERVEUR, u.USR_PRENOM, u.USR_NOM, u.USR_EMAIL, u.USR_TELEPHONE
        ORDER BY chiffre_affaires DESC
    ");
    $serveursQuery->execute([$dateFrom, $dateTo]);
    $serveurs = $serveursQuery->fetchAll(PDO::FETCH_ASSOC);

    // Calcul des totaux pour les pourcentages
    $totalCA = array_sum(array_column($serveurs, 'chiffre_affaires'));
    $totalFactures = array_sum(array_column($serveurs, 'nombre_factures'));

    // Performance par jour de la semaine
    $performanceJour = $pdo->prepare("
        SELECT 
            ISNULL(fv.FCTV_SERVEUR, 'Non défini') as serveur,
            CASE DATEPART(WEEKDAY, fv.FCTV_DATE)
                WHEN 1 THEN 'Dimanche'
                WHEN 2 THEN 'Lundi'
                WHEN 3 THEN 'Mardi'
                WHEN 4 THEN 'Mercredi'
                WHEN 5 THEN 'Jeudi'
                WHEN 6 THEN 'Vendredi'
                WHEN 7 THEN 'Samedi'
            END as jour_semaine,
            SUM(fv.FCTV_MNT_TTC) as ca_jour,
            COUNT(DISTINCT fv.FCTV_REF) as nb_factures
        FROM FACTURE_VNT fv
        WHERE fv.FCTV_DATE BETWEEN ? AND ? 
            AND fv.FCTV_VALIDE = 1
        GROUP BY fv.FCTV_SERVEUR, DATEPART(WEEKDAY, fv.FCTV_DATE)
        ORDER BY fv.FCTV_SERVEUR, DATEPART(WEEKDAY, fv.FCTV_DATE)
    ");
    $performanceJour->execute([$dateFrom, $dateTo]);
    $performanceJourData = $performanceJour->fetchAll(PDO::FETCH_ASSOC);

    ?>

    <div class="report-card animate-fadeIn">
        <div class="report-title">
            <span><i class="fas fa-user-tie"></i> Rapport de Performance des Serveurs</span>
            <small class="text-muted">Période: <?php echo date('d/m/Y', strtotime($dateFrom)) . ' - ' . date('d/m/Y', strtotime($dateTo)); ?></small>
        </div>

        <!-- Résumé des performances -->
        <div class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-primary"><?php echo count($serveurs); ?></h4>
                        <p class="mb-0">Serveurs Actifs</p>
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
                        <h4 class="text-info"><?php echo number_format($totalFactures); ?></h4>
                        <p class="mb-0">Total Factures</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-warning"><?php echo formatCurrency($totalCA / max(count($serveurs), 1)); ?></h4>
                        <p class="mb-0">CA Moyen/Serveur</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau détaillé des serveurs -->
        <?php if (!empty($serveurs)): ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Rang</th>
                            <th>Code Serveur</th>
                            <th>Nom Complet</th>
                            <th>Contact</th>
                            <th>Nb Factures</th>
                            <th>Chiffre d'Affaires</th>
                            <th>% du CA Total</th>
                            <th>Panier Moyen</th>
                            <th>Clients Uniques</th>
                            <th>Période d'Activité</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($serveurs as $index => $serveur): 
                            $pourcentageCA = $totalCA > 0 ? ($serveur['chiffre_affaires'] / $totalCA) * 100 : 0;
                            $performanceLevel = $index < 2 ? 'Excellent' : ($index < 5 ? 'Très Bon' : ($index < 10 ? 'Bon' : 'Moyen'));
                            $badgeClass = $index < 2 ? 'badge-success' : ($index < 5 ? 'badge-warning' : ($index < 10 ? 'badge-info' : 'badge-secondary'));
                        ?>
                            <tr>
                                <td>
                                    <span class="status-badge <?php echo $badgeClass; ?>">
                                        #<?php echo $index + 1; ?>
                                    </span>
                                </td>
                                <td><strong><?php echo htmlspecialchars($serveur['code_serveur']); ?></strong></td>
                                <td><?php echo htmlspecialchars($serveur['nom_serveur']); ?></td>
                                <td>
                                    <?php if ($serveur['email_serveur']): ?>
                                        <small class="d-block"><?php echo htmlspecialchars($serveur['email_serveur']); ?></small>
                                    <?php endif; ?>
                                    <?php if ($serveur['telephone_serveur']): ?>
                                        <small class="text-muted"><?php echo htmlspecialchars($serveur['telephone_serveur']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-primary"><?php echo number_format($serveur['nombre_factures']); ?></span>
                                </td>
                                <td class="text-success">
                                    <strong><?php echo formatCurrency($serveur['chiffre_affaires']); ?></strong>
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
                                    <?php echo formatCurrency($serveur['panier_moyen']); ?>
                                </td>
                                <td>
                                    <span class="badge badge-info"><?php echo number_format($serveur['clients_uniques']); ?></span>
                                </td>
                                <td>
                                    <small class="d-block">Du: <?php echo date('d/m/Y', strtotime($serveur['premiere_vente'])); ?></small>
                                    <small class="text-muted">Au: <?php echo date('d/m/Y', strtotime($serveur['derniere_vente'])); ?></small>
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

            <!-- Graphique de comparaison des serveurs (Top 10) -->
            <div class="chart-container mt-4">
                <h5><i class="fas fa-chart-bar"></i> Comparaison des Performances (Top 10)</h5>
                <canvas id="serveursChart" height="80"></canvas>
            </div>

            <!-- Analyse par jour de la semaine pour le top serveur -->
            <?php if (!empty($performanceJourData)): ?>
                <div class="chart-container mt-4">
                    <h5><i class="fas fa-calendar-week"></i> Performance par Jour de la Semaine</h5>
                    <canvas id="jourSemaineChart" height="60"></canvas>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Aucune donnée de serveur disponible pour cette période.
            </div>
        <?php endif; ?>
    </div>

    <script>
    <?php if (!empty($serveurs)): ?>
        // Graphique de comparaison des serveurs (Top 10)
        const serveursCtx = document.getElementById('serveursChart').getContext('2d');
        const top10Serveurs = <?php echo json_encode(array_slice($serveurs, 0, 10)); ?>;
        
        new Chart(serveursCtx, {
            type: 'bar',
            data: {
                labels: top10Serveurs.map(s => s.nom_serveur),
                datasets: [{
                    label: 'Chiffre d\'Affaires (DH)',
                    data: top10Serveurs.map(s => parseFloat(s.chiffre_affaires)),
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
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
                        text: 'Top 10 Serveurs par Chiffre d\'Affaires'
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

        <?php if (!empty($performanceJourData)): ?>
            // Graphique par jour de la semaine
            const jourSemaineCtx = document.getElementById('jourSemaineChart').getContext('2d');
            
            // Groupement des données par jour
            const joursData = {};
            const performanceData = <?php echo json_encode($performanceJourData); ?>;
            
            performanceData.forEach(item => {
                if (!joursData[item.jour_semaine]) {
                    joursData[item.jour_semaine] = 0;
                }
                joursData[item.jour_semaine] += parseFloat(item.ca_jour);
            });

            const joursOrdre = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
            
            new Chart(jourSemaineCtx, {
                type: 'line',
                data: {
                    labels: joursOrdre,
                    datasets: [{
                        label: 'CA par Jour (DH)',
                        data: joursOrdre.map(jour => joursData[jour] || 0),
                        borderColor: '#e74c3c',
                        backgroundColor: 'rgba(231, 76, 60, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Évolution du CA par Jour de la Semaine'
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
        <?php endif; ?>
    <?php endif; ?>
    </script>

    <?php

} catch (PDOException $e) {
    echo '<div class="alert alert-danger animate-fadeIn">';
    echo '<i class="fas fa-exclamation-triangle"></i> ';
    echo 'Erreur lors du chargement du rapport serveurs: ' . htmlspecialchars($e->getMessage());
    echo '</div>';
}
?>
