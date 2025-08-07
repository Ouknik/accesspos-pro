<?php
/**
 * Rapport des modes de paiement - Analyse des transactions et préférences
 * Inclut les tendances de paiement et les statistiques financières
 */

try {
    // Analyse détaillée des modes de paiement
    $paiementsQuery = $pdo->prepare("
        SELECT 
            CASE 
                WHEN FCTV_MODEPAIEMENT = 'ESP' OR FCTV_MODEPAIEMENT IS NULL THEN 'Espèces'
                WHEN FCTV_MODEPAIEMENT = 'CHQ' THEN 'Chèque'
                WHEN FCTV_MODEPAIEMENT = 'CB' THEN 'Carte Bancaire'
                WHEN FCTV_MODEPAIEMENT = 'VIR' THEN 'Virement'
                WHEN FCTV_MODEPAIEMENT = 'TPE' THEN 'Terminal de Paiement'
                ELSE FCTV_MODEPAIEMENT
            END as mode_paiement,
            COUNT(DISTINCT FCTV_REF) as nombre_transactions,
            SUM(FCTV_MNT_TTC) as montant_total,
            AVG(FCTV_MNT_TTC) as ticket_moyen,
            MIN(FCTV_MNT_TTC) as montant_min,
            MAX(FCTV_MNT_TTC) as montant_max,
            COUNT(DISTINCT CLT_REF) as clients_uniques,
            COUNT(DISTINCT FCTV_SERVEUR) as serveurs_utilisateurs
        FROM FACTURE_VNT
        WHERE FCTV_DATE BETWEEN ? AND ?
            AND FCTV_VALIDE = 1
        GROUP BY CASE 
                WHEN FCTV_MODEPAIEMENT = 'ESP' OR FCTV_MODEPAIEMENT IS NULL THEN 'Espèces'
                WHEN FCTV_MODEPAIEMENT = 'CHQ' THEN 'Chèque'
                WHEN FCTV_MODEPAIEMENT = 'CB' THEN 'Carte Bancaire'
                WHEN FCTV_MODEPAIEMENT = 'VIR' THEN 'Virement'
                WHEN FCTV_MODEPAIEMENT = 'TPE' THEN 'Terminal de Paiement'
                ELSE FCTV_MODEPAIEMENT
            END
        ORDER BY montant_total DESC
    ");
    $paiementsQuery->execute([$dateFrom, $dateTo]);
    $paiements = $paiementsQuery->fetchAll(PDO::FETCH_ASSOC);

    // Évolution des paiements par jour
    $evolutionPaiements = $pdo->prepare("
        SELECT 
            CAST(FCTV_DATE as DATE) as date_paiement,
            CASE 
                WHEN FCTV_MODEPAIEMENT = 'ESP' OR FCTV_MODEPAIEMENT IS NULL THEN 'Espèces'
                WHEN FCTV_MODEPAIEMENT = 'CHQ' THEN 'Chèque'
                WHEN FCTV_MODEPAIEMENT = 'CB' THEN 'Carte Bancaire'
                WHEN FCTV_MODEPAIEMENT = 'VIR' THEN 'Virement'
                WHEN FCTV_MODEPAIEMENT = 'TPE' THEN 'Terminal de Paiement'
                ELSE FCTV_MODEPAIEMENT
            END as mode_paiement,
            SUM(FCTV_MNT_TTC) as montant_jour,
            COUNT(DISTINCT FCTV_REF) as nb_transactions
        FROM FACTURE_VNT
        WHERE FCTV_DATE BETWEEN ? AND ?
            AND FCTV_VALIDE = 1
        GROUP BY CAST(FCTV_DATE as DATE),
                CASE 
                    WHEN FCTV_MODEPAIEMENT = 'ESP' OR FCTV_MODEPAIEMENT IS NULL THEN 'Espèces'
                    WHEN FCTV_MODEPAIEMENT = 'CHQ' THEN 'Chèque'
                    WHEN FCTV_MODEPAIEMENT = 'CB' THEN 'Carte Bancaire'
                    WHEN FCTV_MODEPAIEMENT = 'VIR' THEN 'Virement'
                    WHEN FCTV_MODEPAIEMENT = 'TPE' THEN 'Terminal de Paiement'
                    ELSE FCTV_MODEPAIEMENT
                END
        ORDER BY date_paiement, mode_paiement
    ");
    $evolutionPaiements->execute([$dateFrom, $dateTo]);
    $evolutionData = $evolutionPaiements->fetchAll(PDO::FETCH_ASSOC);

    // Analyse par tranche horaire
    $paiementsHoraires = $pdo->prepare("
        SELECT 
            CASE 
                WHEN DATEPART(HOUR, FCTV_DATE) BETWEEN 8 AND 11 THEN 'Matinée (8h-12h)'
                WHEN DATEPART(HOUR, FCTV_DATE) BETWEEN 12 AND 13 THEN 'Déjeuner (12h-14h)'
                WHEN DATEPART(HOUR, FCTV_DATE) BETWEEN 14 AND 17 THEN 'Après-midi (14h-18h)'
                WHEN DATEPART(HOUR, FCTV_DATE) BETWEEN 18 AND 21 THEN 'Soirée (18h-22h)'
                ELSE 'Autres heures'
            END as tranche_horaire,
            CASE 
                WHEN FCTV_MODEPAIEMENT = 'ESP' OR FCTV_MODEPAIEMENT IS NULL THEN 'Espèces'
                WHEN FCTV_MODEPAIEMENT = 'CHQ' THEN 'Chèque'
                WHEN FCTV_MODEPAIEMENT = 'CB' THEN 'Carte Bancaire'
                WHEN FCTV_MODEPAIEMENT = 'VIR' THEN 'Virement'
                WHEN FCTV_MODEPAIEMENT = 'TPE' THEN 'Terminal de Paiement'
                ELSE FCTV_MODEPAIEMENT
            END as mode_paiement,
            COUNT(DISTINCT FCTV_REF) as nb_transactions,
            SUM(FCTV_MNT_TTC) as montant_total
        FROM FACTURE_VNT
        WHERE FCTV_DATE BETWEEN ? AND ?
            AND FCTV_VALIDE = 1
        GROUP BY CASE 
                WHEN DATEPART(HOUR, FCTV_DATE) BETWEEN 8 AND 11 THEN 'Matinée (8h-12h)'
                WHEN DATEPART(HOUR, FCTV_DATE) BETWEEN 12 AND 13 THEN 'Déjeuner (12h-14h)'
                WHEN DATEPART(HOUR, FCTV_DATE) BETWEEN 14 AND 17 THEN 'Après-midi (14h-18h)'
                WHEN DATEPART(HOUR, FCTV_DATE) BETWEEN 18 AND 21 THEN 'Soirée (18h-22h)'
                ELSE 'Autres heures'
            END,
            CASE 
                WHEN FCTV_MODEPAIEMENT = 'ESP' OR FCTV_MODEPAIEMENT IS NULL THEN 'Espèces'
                WHEN FCTV_MODEPAIEMENT = 'CHQ' THEN 'Chèque'
                WHEN FCTV_MODEPAIEMENT = 'CB' THEN 'Carte Bancaire'
                WHEN FCTV_MODEPAIEMENT = 'VIR' THEN 'Virement'
                WHEN FCTV_MODEPAIEMENT = 'TPE' THEN 'Terminal de Paiement'
                ELSE FCTV_MODEPAIEMENT
            END
        ORDER BY tranche_horaire, montant_total DESC
    ");
    $paiementsHoraires->execute([$dateFrom, $dateTo]);
    $horaireData = $paiementsHoraires->fetchAll(PDO::FETCH_ASSOC);

    // Calculs des totaux
    $totalMontant = array_sum(array_column($paiements, 'montant_total'));
    $totalTransactions = array_sum(array_column($paiements, 'nombre_transactions'));

    ?>

    <div class="report-card animate-fadeIn">
        <div class="report-title">
            <span><i class="fas fa-credit-card"></i> Rapport d'Analyse des Modes de Paiement</span>
            <small class="text-muted">Période: <?php echo date('d/m/Y', strtotime($dateFrom)) . ' - ' . date('d/m/Y', strtotime($dateTo)); ?></small>
        </div>

        <!-- KPIs des paiements -->
        <div class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-primary"><?php echo count($paiements); ?></h4>
                        <p class="mb-0">Modes de Paiement</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-success"><?php echo formatCurrency($totalMontant); ?></h4>
                        <p class="mb-0">Montant Total</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-info"><?php echo number_format($totalTransactions); ?></h4>
                        <p class="mb-0">Total Transactions</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-warning"><?php echo formatCurrency($totalMontant / max($totalTransactions, 1)); ?></h4>
                        <p class="mb-0">Ticket Moyen</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Répartition principale -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="chart-container">
                    <h5><i class="fas fa-pie-chart"></i> Répartition par Montant</h5>
                    <canvas id="paiementMontantChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h5><i class="fas fa-chart-bar"></i> Répartition par Nombre de Transactions</h5>
                    <canvas id="paiementTransactionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tableau détaillé -->
        <div class="table-responsive mb-4">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Mode de Paiement</th>
                        <th>Nb Transactions</th>
                        <th>% Transactions</th>
                        <th>Montant Total</th>
                        <th>% Montant</th>
                        <th>Ticket Moyen</th>
                        <th>Montant Min</th>
                        <th>Montant Max</th>
                        <th>Clients Uniques</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paiements as $index => $paiement): 
                        $pourcentageTransactions = $totalTransactions > 0 ? ($paiement['nombre_transactions'] / $totalTransactions) * 100 : 0;
                        $pourcentageMontant = $totalMontant > 0 ? ($paiement['montant_total'] / $totalMontant) * 100 : 0;
                        $performanceLevel = $pourcentageMontant > 40 ? 'Dominant' : ($pourcentageMontant > 20 ? 'Important' : ($pourcentageMontant > 10 ? 'Modéré' : 'Faible'));
                        $badgeClass = $pourcentageMontant > 40 ? 'badge-success' : ($pourcentageMontant > 20 ? 'badge-warning' : ($pourcentageMontant > 10 ? 'badge-info' : 'badge-secondary'));
                    ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($paiement['mode_paiement']); ?></strong>
                                <?php 
                                $icon = '';
                                switch($paiement['mode_paiement']) {
                                    case 'Espèces': $icon = 'fas fa-money-bill-wave text-success'; break;
                                    case 'Carte Bancaire': $icon = 'fas fa-credit-card text-primary'; break;
                                    case 'Chèque': $icon = 'fas fa-money-check text-info'; break;
                                    case 'Virement': $icon = 'fas fa-university text-warning'; break;
                                    case 'Terminal de Paiement': $icon = 'fas fa-credit-card text-danger'; break;
                                }
                                if ($icon) echo " <i class='$icon'></i>";
                                ?>
                            </td>
                            <td>
                                <span class="badge badge-primary"><?php echo number_format($paiement['nombre_transactions']); ?></span>
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: <?php echo $pourcentageTransactions; ?>%;">
                                        <?php echo number_format($pourcentageTransactions, 1); ?>%
                                    </div>
                                </div>
                            </td>
                            <td class="text-success">
                                <strong><?php echo formatCurrency($paiement['montant_total']); ?></strong>
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?php echo $pourcentageMontant; ?>%;">
                                        <?php echo number_format($pourcentageMontant, 1); ?>%
                                    </div>
                                </div>
                            </td>
                            <td class="text-info">
                                <?php echo formatCurrency($paiement['ticket_moyen']); ?>
                            </td>
                            <td><?php echo formatCurrency($paiement['montant_min']); ?></td>
                            <td><?php echo formatCurrency($paiement['montant_max']); ?></td>
                            <td>
                                <span class="badge badge-info"><?php echo number_format($paiement['clients_uniques']); ?></span>
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

        <!-- Évolution temporelle -->
        <div class="chart-container mb-4">
            <h5><i class="fas fa-line-chart"></i> Évolution des Paiements dans le Temps</h5>
            <canvas id="evolutionPaiementsChart" height="80"></canvas>
        </div>

        <!-- Analyse par tranche horaire -->
        <div class="mb-4">
            <h5><i class="fas fa-clock"></i> Préférences de Paiement par Tranche Horaire</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Tranche Horaire</th>
                            <th>Mode Préféré</th>
                            <th>Montant</th>
                            <th>Transactions</th>
                            <th>Répartition</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $horaireGroupe = [];
                        foreach ($horaireData as $data) {
                            if (!isset($horaireGroupe[$data['tranche_horaire']])) {
                                $horaireGroupe[$data['tranche_horaire']] = [];
                            }
                            $horaireGroupe[$data['tranche_horaire']][] = $data;
                        }
                        
                        foreach ($horaireGroupe as $tranche => $modes): 
                            usort($modes, function($a, $b) { return $b['montant_total'] <=> $a['montant_total']; });
                            $modePrefere = $modes[0];
                            $totalTranche = array_sum(array_column($modes, 'montant_total'));
                        ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($tranche); ?></strong></td>
                                <td>
                                    <?php echo htmlspecialchars($modePrefere['mode_paiement']); ?>
                                    <small class="text-muted">(<?php echo number_format(($modePrefere['montant_total'] / max($totalTranche, 1)) * 100, 1); ?>%)</small>
                                </td>
                                <td class="text-success"><?php echo formatCurrency($totalTranche); ?></td>
                                <td><?php echo number_format(array_sum(array_column($modes, 'nb_transactions'))); ?></td>
                                <td>
                                    <?php foreach (array_slice($modes, 0, 3) as $mode): ?>
                                        <small class="d-block">
                                            <?php echo htmlspecialchars($mode['mode_paiement']); ?>: 
                                            <?php echo formatCurrency($mode['montant_total']); ?>
                                        </small>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    // Graphique répartition par montant
    const paiementsData = <?php echo json_encode($paiements); ?>;
    
    const montantCtx = document.getElementById('paiementMontantChart').getContext('2d');
    new Chart(montantCtx, {
        type: 'doughnut',
        data: {
            labels: paiementsData.map(p => p.mode_paiement),
            datasets: [{
                data: paiementsData.map(p => parseFloat(p.montant_total)),
                backgroundColor: [
                    '#28a745', '#007bff', '#ffc107', '#dc3545', '#6f42c1'
                ],
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

    // Graphique répartition par transactions
    const transactionCtx = document.getElementById('paiementTransactionChart').getContext('2d');
    new Chart(transactionCtx, {
        type: 'bar',
        data: {
            labels: paiementsData.map(p => p.mode_paiement),
            datasets: [{
                label: 'Nombre de Transactions',
                data: paiementsData.map(p => parseInt(p.nombre_transactions)),
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
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Graphique d'évolution
    const evolutionData = <?php echo json_encode($evolutionData); ?>;
    
    if (evolutionData.length > 0) {
        const evolutionCtx = document.getElementById('evolutionPaiementsChart').getContext('2d');
        
        // Groupement des données
        const dates = [...new Set(evolutionData.map(d => d.date_paiement))].sort();
        const modes = [...new Set(evolutionData.map(d => d.mode_paiement))];
        const colors = ['#28a745', '#007bff', '#ffc107', '#dc3545', '#6f42c1'];
        
        const datasets = modes.map((mode, index) => ({
            label: mode,
            data: dates.map(date => {
                const dayData = evolutionData.find(d => d.mode_paiement === mode && d.date_paiement === date);
                return dayData ? parseFloat(dayData.montant_jour) : 0;
            }),
            borderColor: colors[index % colors.length],
            backgroundColor: colors[index % colors.length] + '20',
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
                        text: 'Évolution des Modes de Paiement'
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
    echo 'Erreur lors du chargement du rapport paiements: ' . htmlspecialchars($e->getMessage());
    echo '</div>';
}
?>
