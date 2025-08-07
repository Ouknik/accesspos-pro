<?php
/**
 * Dashboard principal - Vue d'ensemble des KPIs
 * Affichage des métriques principales avec graphiques
 */

try {
    // Requête pour les KPIs principaux
    $kpis = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT FCTV_REF) as total_factures,
            SUM(FCTV_MNT_TTC) as chiffre_affaires_total,
            AVG(FCTV_MNT_TTC) as moyenne_facture,
            MIN(FCTV_MNT_TTC) as plus_petite_facture,
            MAX(FCTV_MNT_TTC) as plus_grande_facture,
            COUNT(DISTINCT CLT_REF) as nombre_clients_uniques,
            COUNT(DISTINCT FCTV_UTILISATEUR) as nombre_caissiers
        FROM FACTURE_VNT
        WHERE FCTV_DATE BETWEEN ? AND ?
            AND FCTV_VALIDE = 1
    ");
    $kpis->execute([$dateFrom, $dateTo]);
    $kpiData = $kpis->fetch(PDO::FETCH_ASSOC);

    // Evolution du CA par jour
    $evolution = $pdo->prepare("
        SELECT 
            CAST(FCTV_DATE as DATE) as date_vente,
            SUM(FCTV_MNT_TTC) as ca_jour,
            COUNT(DISTINCT FCTV_REF) as nb_factures
        FROM FACTURE_VNT
        WHERE FCTV_DATE BETWEEN ? AND ?
            AND FCTV_VALIDE = 1
        GROUP BY CAST(FCTV_DATE as DATE)
        ORDER BY date_vente
    ");
    $evolution->execute([$dateFrom, $dateTo]);
    $evolutionData = $evolution->fetchAll(PDO::FETCH_ASSOC);

    // Top serveurs
    $topServeurs = $pdo->prepare("
        SELECT TOP 5
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
    $topServeurs->execute([$dateFrom, $dateTo]);
    $serveursData = $topServeurs->fetchAll(PDO::FETCH_ASSOC);

    // Répartition par mode de paiement
    $paiements = $pdo->prepare("
        SELECT 
            CASE 
                WHEN FCTV_MODEPAIEMENT = 'ESP' THEN 'Espèces'
                WHEN FCTV_MODEPAIEMENT = 'CHQ' THEN 'Chèque'
                WHEN FCTV_MODEPAIEMENT = 'CB' THEN 'Carte Bancaire'
                ELSE ISNULL(FCTV_MODEPAIEMENT, 'Espèces')
            END as mode_paiement,
            SUM(FCTV_MNT_TTC) as montant,
            COUNT(DISTINCT FCTV_REF) as nb_transactions
        FROM FACTURE_VNT
        WHERE FCTV_DATE BETWEEN ? AND ? AND FCTV_VALIDE = 1
        GROUP BY FCTV_MODEPAIEMENT
        ORDER BY montant DESC
    ");
    $paiements->execute([$dateFrom, $dateTo]);
    $paiementsData = $paiements->fetchAll(PDO::FETCH_ASSOC);

    ?>

    <!-- KPIs principaux -->
    <div class="report-card animate-fadeIn">
        <div class="report-title">
            <span><i class="fas fa-chart-bar"></i> Indicateurs Clés de Performance (KPIs)</span>
        </div>
        
        <div class="stats-grid">
            <div class="kpi-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-primary mb-1"><?php echo number_format($kpiData['total_factures'] ?? 0); ?></h3>
                        <p class="mb-0 text-muted">Total Factures</p>
                    </div>
                    <i class="fas fa-receipt fa-2x text-primary opacity-75"></i>
                </div>
            </div>
            
            <div class="kpi-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-success mb-1"><?php echo formatCurrency($kpiData['chiffre_affaires_total'] ?? 0); ?></h3>
                        <p class="mb-0 text-muted">Chiffre d'Affaires Total</p>
                    </div>
                    <i class="fas fa-coins fa-2x text-success opacity-75"></i>
                </div>
            </div>
            
            <div class="kpi-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-info mb-1"><?php echo formatCurrency($kpiData['moyenne_facture'] ?? 0); ?></h3>
                        <p class="mb-0 text-muted">Panier Moyen</p>
                    </div>
                    <i class="fas fa-shopping-cart fa-2x text-info opacity-75"></i>
                </div>
            </div>
            
            <div class="kpi-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-warning mb-1"><?php echo number_format($kpiData['nombre_clients_uniques'] ?? 0); ?></h3>
                        <p class="mb-0 text-muted">Clients Uniques</p>
                    </div>
                    <i class="fas fa-users fa-2x text-warning opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique d'évolution du CA -->
    <div class="chart-container animate-fadeIn">
        <h4><i class="fas fa-line-chart"></i> Evolution du Chiffre d'Affaires</h4>
        <canvas id="evolutionChart" height="100"></canvas>
    </div>

    <!-- Top serveurs -->
    <div class="report-card animate-fadeIn">
        <div class="report-title">
            <span><i class="fas fa-trophy"></i> Top 5 Serveurs par Performance</span>
        </div>
        
        <?php if (!empty($serveursData)): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Rang</th>
                        <th>Code Serveur</th>
                        <th>Nom Serveur</th>
                        <th>Chiffre d'Affaires</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($serveursData as $index => $serveur): ?>
                        <tr>
                            <td>
                                <span class="status-badge badge-<?php echo $index < 3 ? 'success' : 'info'; ?>">
                                    #<?php echo $index + 1; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($serveur['serveur']); ?></td>
                            <td><strong><?php echo htmlspecialchars($serveur['nom_serveur']); ?></strong></td>
                            <td class="text-success"><strong><?php echo formatCurrency($serveur['chiffre_affaires']); ?></strong></td>
                            <td>
                                <?php
                                $performance = $index < 2 ? 'Excellent' : ($index < 4 ? 'Bon' : 'Moyen');
                                $badgeClass = $index < 2 ? 'badge-success' : ($index < 4 ? 'badge-warning' : 'badge-info');
                                echo "<span class='status-badge $badgeClass'>$performance</span>";
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Aucune donnée de serveur disponible pour cette période.
            </div>
        <?php endif; ?>
    </div>

    <!-- Répartition des paiements -->
    <div class="chart-container animate-fadeIn">
        <h4><i class="fas fa-pie-chart"></i> Répartition des Modes de Paiement</h4>
        <div class="row">
            <div class="col-md-6">
                <canvas id="paiementChart"></canvas>
            </div>
            <div class="col-md-6">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Mode de Paiement</th>
                                <th>Montant</th>
                                <th>Transactions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paiementsData as $paiement): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($paiement['mode_paiement']); ?></td>
                                    <td class="text-success"><?php echo formatCurrency($paiement['montant']); ?></td>
                                    <td><?php echo number_format($paiement['nb_transactions']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Graphique d'évolution du CA
    const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
    new Chart(evolutionCtx, {
        type: 'line',
        data: {
            labels: [<?php echo "'" . implode("','", array_map(function($d) { return date('d/m', strtotime($d['date_vente'])); }, $evolutionData)) . "'"; ?>],
            datasets: [{
                label: 'Chiffre d\'Affaires (DH)',
                data: [<?php echo implode(',', array_column($evolutionData, 'ca_jour')); ?>],
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Evolution quotidienne du Chiffre d\'Affaires'
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

    // Graphique des modes de paiement
    const paiementCtx = document.getElementById('paiementChart').getContext('2d');
    new Chart(paiementCtx, {
        type: 'doughnut',
        data: {
            labels: [<?php echo "'" . implode("','", array_column($paiementsData, 'mode_paiement')) . "'"; ?>],
            datasets: [{
                data: [<?php echo implode(',', array_column($paiementsData, 'montant')); ?>],
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB', 
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF'
                ]
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
                    text: 'Répartition par Mode de Paiement'
                }
            }
        }
    });
    </script>

    <?php

} catch (PDOException $e) {
    echo '<div class="alert alert-danger animate-fadeIn">';
    echo '<i class="fas fa-exclamation-triangle"></i> ';
    echo 'Erreur lors du chargement du dashboard: ' . htmlspecialchars($e->getMessage());
    echo '</div>';
}
?>
