<?php
/**
 * Rapport des clients - Analyse de la clientèle et fidélisation
 * Inclut les comportements d'achat et la segmentation client
 */

try {
    // Analyse détaillée des clients
    $clientsQuery = $pdo->prepare("
        SELECT 
            c.CLT_REF as reference_client,
            c.CLT_NOM as nom_client,
            c.CLT_PRENOM as prenom_client,
            c.CLT_TELEPHONE as telephone,
            c.CLT_EMAIL as email,
            c.CLT_ADRESSE as adresse,
            COUNT(DISTINCT fv.FCTV_REF) as nombre_achats,
            SUM(fv.FCTV_MNT_TTC) as chiffre_affaires_client,
            AVG(fv.FCTV_MNT_TTC) as panier_moyen,
            MIN(fv.FCTV_DATE) as premier_achat,
            MAX(fv.FCTV_DATE) as dernier_achat,
            DATEDIFF(day, MIN(fv.FCTV_DATE), MAX(fv.FCTV_DATE)) as duree_relation,
            COUNT(DISTINCT CAST(fv.FCTV_DATE as DATE)) as jours_activite,
            COUNT(DISTINCT fv.FCTV_SERVEUR) as serveurs_contactes
        FROM CLIENT c
        LEFT JOIN FACTURE_VNT fv ON c.CLT_REF = fv.CLT_REF
            AND fv.FCTV_DATE BETWEEN ? AND ?
            AND fv.FCTV_VALIDE = 1
        WHERE c.CLT_REF IS NOT NULL
        GROUP BY c.CLT_REF, c.CLT_NOM, c.CLT_PRENOM, c.CLT_TELEPHONE, c.CLT_EMAIL, c.CLT_ADRESSE
        ORDER BY chiffre_affaires_client DESC
    ");
    $clientsQuery->execute([$dateFrom, $dateTo]);
    $clients = $clientsQuery->fetchAll(PDO::FETCH_ASSOC);

    // Clients actifs (avec achats dans la période)
    $clientsActifs = array_filter($clients, function($client) {
        return $client['nombre_achats'] > 0;
    });

    // Segmentation RFM (Récence, Fréquence, Montant)
    $clientsSegmentes = [];
    foreach ($clientsActifs as $client) {
        $recence = $client['dernier_achat'] ? 
            (new DateTime())->diff(new DateTime($client['dernier_achat']))->days : 9999;
        $frequence = $client['nombre_achats'];
        $montant = $client['chiffre_affaires_client'];
        
        // Scoring simple RFM (1-5)
        $scoreRecence = $recence <= 7 ? 5 : ($recence <= 30 ? 4 : ($recence <= 90 ? 3 : ($recence <= 180 ? 2 : 1)));
        $scoreFrequence = $frequence >= 10 ? 5 : ($frequence >= 5 ? 4 : ($frequence >= 3 ? 3 : ($frequence >= 2 ? 2 : 1)));
        $scoreMontant = $montant >= 5000 ? 5 : ($montant >= 2000 ? 4 : ($montant >= 1000 ? 3 : ($montant >= 500 ? 2 : 1)));
        
        $scoreTotal = $scoreRecence + $scoreFrequence + $scoreMontant;
        
        $segment = 'Bronze';
        if ($scoreTotal >= 13) $segment = 'Platine';
        elseif ($scoreTotal >= 10) $segment = 'Or';
        elseif ($scoreTotal >= 7) $segment = 'Argent';
        
        $client['score_recence'] = $scoreRecence;
        $client['score_frequence'] = $scoreFrequence;
        $client['score_montant'] = $scoreMontant;
        $client['score_total'] = $scoreTotal;
        $client['segment'] = $segment;
        $client['recence_jours'] = $recence;
        
        $clientsSegmentes[] = $client;
    }

    // Évolution des nouveaux clients
    $nouveauxClients = $pdo->prepare("
        SELECT 
            CAST(MIN(fv.FCTV_DATE) as DATE) as date_premier_achat,
            COUNT(DISTINCT c.CLT_REF) as nouveaux_clients,
            SUM(fv.FCTV_MNT_TTC) as ca_nouveaux_clients
        FROM CLIENT c
        INNER JOIN FACTURE_VNT fv ON c.CLT_REF = fv.CLT_REF
        WHERE fv.FCTV_VALIDE = 1
        GROUP BY c.CLT_REF
        HAVING MIN(fv.FCTV_DATE) BETWEEN ? AND ?
        ORDER BY date_premier_achat
    ");
    $nouveauxClients->execute([$dateFrom, $dateTo]);
    $nouveauxData = $nouveauxClients->fetchAll(PDO::FETCH_ASSOC);

    // Groupement par date pour le graphique
    $evolutionNouveaux = [];
    foreach ($nouveauxData as $data) {
        $date = $data['date_premier_achat'];
        if (!isset($evolutionNouveaux[$date])) {
            $evolutionNouveaux[$date] = ['count' => 0, 'ca' => 0];
        }
        $evolutionNouveaux[$date]['count'] += $data['nouveaux_clients'];
        $evolutionNouveaux[$date]['ca'] += $data['ca_nouveaux_clients'];
    }

    // Statistiques globales
    $totalCA = array_sum(array_column($clientsActifs, 'chiffre_affaires_client'));
    $totalClients = count($clients);
    $clientsActifsCount = count($clientsActifs);

    ?>

    <div class="report-card animate-fadeIn">
        <div class="report-title">
            <span><i class="fas fa-users"></i> Rapport d'Analyse de la Clientèle</span>
            <small class="text-muted">Période: <?php echo date('d/m/Y', strtotime($dateFrom)) . ' - ' . date('d/m/Y', strtotime($dateTo)); ?></small>
        </div>

        <!-- KPIs clients -->
        <div class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-primary"><?php echo number_format($totalClients); ?></h4>
                        <p class="mb-0">Total Clients</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-success"><?php echo number_format($clientsActifsCount); ?></h4>
                        <p class="mb-0">Clients Actifs</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-info"><?php echo formatCurrency($totalCA / max($clientsActifsCount, 1)); ?></h4>
                        <p class="mb-0">CA Moyen/Client</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-warning"><?php echo count($nouveauxData); ?></h4>
                        <p class="mb-0">Nouveaux Clients</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segmentation RFM -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="chart-container">
                    <h5><i class="fas fa-chart-pie"></i> Segmentation Clientèle (RFM)</h5>
                    <canvas id="segmentationChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="segment-stats">
                    <h5><i class="fas fa-medal"></i> Répartition par Segment</h5>
                    <?php 
                    $segments = array_count_values(array_column($clientsSegmentes, 'segment'));
                    $segmentColors = ['Platine' => '#e5e7eb', 'Or' => '#fbbf24', 'Argent' => '#9ca3af', 'Bronze' => '#cd7c2f'];
                    foreach ($segments as $segment => $count): 
                        $pourcentage = ($count / max(count($clientsSegmentes), 1)) * 100;
                    ?>
                        <div class="segment-item mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="segment-name">
                                    <i class="fas fa-medal" style="color: <?php echo $segmentColors[$segment] ?? '#6b7280'; ?>"></i>
                                    <?php echo $segment; ?>
                                </span>
                                <span class="segment-count">
                                    <strong><?php echo $count; ?></strong> clients
                                    <small class="text-muted">(<?php echo number_format($pourcentage, 1); ?>%)</small>
                                </span>
                            </div>
                            <div class="progress mt-1" style="height: 8px;">
                                <div class="progress-bar" style="width: <?php echo $pourcentage; ?>%; background-color: <?php echo $segmentColors[$segment] ?? '#6b7280'; ?>;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Onglets pour différentes analyses -->
        <ul class="nav nav-tabs" id="clientsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="top-clients-tab" data-bs-toggle="tab" data-bs-target="#top-clients" 
                        type="button" role="tab">Top Clients</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="fidelisation-tab" data-bs-toggle="tab" data-bs-target="#fidelisation" 
                        type="button" role="tab">Fidélisation</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="nouveaux-tab" data-bs-toggle="tab" data-bs-target="#nouveaux" 
                        type="button" role="tab">Nouveaux Clients</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="comportement-tab" data-bs-toggle="tab" data-bs-target="#comportement" 
                        type="button" role="tab">Comportements</button>
            </li>
        </ul>

        <div class="tab-content" id="clientsTabContent">
            <!-- Top clients par CA -->
            <div class="tab-pane fade show active" id="top-clients" role="tabpanel">
                <div class="table-responsive mt-3">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Rang</th>
                                <th>Client</th>
                                <th>Contact</th>
                                <th>Nb Achats</th>
                                <th>CA Total</th>
                                <th>Panier Moyen</th>
                                <th>Premier Achat</th>
                                <th>Dernier Achat</th>
                                <th>Segment</th>
                                <th>Fidélité</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($clientsSegmentes, 0, 20) as $index => $client): 
                                $segmentColors = ['Platine' => 'badge-secondary', 'Or' => 'badge-warning', 'Argent' => 'badge-info', 'Bronze' => 'badge-dark'];
                                $fidelite = $client['nombre_achats'] >= 10 ? 'Très Fidèle' : ($client['nombre_achats'] >= 5 ? 'Fidèle' : ($client['nombre_achats'] >= 3 ? 'Régulier' : 'Occasionnel'));
                                $fideliteColor = $client['nombre_achats'] >= 10 ? 'badge-success' : ($client['nombre_achats'] >= 5 ? 'badge-warning' : ($client['nombre_achats'] >= 3 ? 'badge-info' : 'badge-secondary'));
                            ?>
                                <tr>
                                    <td>
                                        <span class="status-badge badge-primary">
                                            #<?php echo $index + 1; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars(trim(($client['prenom_client'] ?: '') . ' ' . ($client['nom_client'] ?: 'Client #' . $client['reference_client']))); ?></strong>
                                        <br><small class="text-muted">Réf: <?php echo htmlspecialchars($client['reference_client']); ?></small>
                                    </td>
                                    <td>
                                        <?php if ($client['telephone']): ?>
                                            <small class="d-block"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($client['telephone']); ?></small>
                                        <?php endif; ?>
                                        <?php if ($client['email']): ?>
                                            <small class="text-muted"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($client['email']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary"><?php echo number_format($client['nombre_achats']); ?></span>
                                    </td>
                                    <td class="text-success">
                                        <strong><?php echo formatCurrency($client['chiffre_affaires_client']); ?></strong>
                                    </td>
                                    <td class="text-info">
                                        <?php echo formatCurrency($client['panier_moyen']); ?>
                                    </td>
                                    <td>
                                        <?php echo $client['premier_achat'] ? date('d/m/Y', strtotime($client['premier_achat'])) : '-'; ?>
                                    </td>
                                    <td>
                                        <?php echo $client['dernier_achat'] ? date('d/m/Y', strtotime($client['dernier_achat'])) : '-'; ?>
                                        <?php if (isset($client['recence_jours']) && $client['recence_jours'] <= 7): ?>
                                            <br><small class="text-success">Récent</small>
                                        <?php elseif (isset($client['recence_jours']) && $client['recence_jours'] > 90): ?>
                                            <br><small class="text-warning">Inactif</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo $segmentColors[$client['segment']] ?? 'badge-secondary'; ?>">
                                            <?php echo $client['segment']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo $fideliteColor; ?>">
                                            <?php echo $fidelite; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Analyse de fidélisation -->
            <div class="tab-pane fade" id="fidelisation" role="tabpanel">
                <div class="mt-3">
                    <div class="row">
                        <div class="col-md-8">
                            <canvas id="fidelisationChart" height="60"></canvas>
                        </div>
                        <div class="col-md-4">
                            <h6>Analyse de Fidélité</h6>
                            <?php 
                            $fideliteStats = [
                                'Très Fidèle (≥10)' => count(array_filter($clientsActifs, fn($c) => $c['nombre_achats'] >= 10)),
                                'Fidèle (5-9)' => count(array_filter($clientsActifs, fn($c) => $c['nombre_achats'] >= 5 && $c['nombre_achats'] < 10)),
                                'Régulier (3-4)' => count(array_filter($clientsActifs, fn($c) => $c['nombre_achats'] >= 3 && $c['nombre_achats'] < 5)),
                                'Occasionnel (1-2)' => count(array_filter($clientsActifs, fn($c) => $c['nombre_achats'] < 3))
                            ];
                            foreach ($fideliteStats as $type => $count): 
                                $pourcentage = ($count / max($clientsActifsCount, 1)) * 100;
                            ?>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span><?php echo $type; ?></span>
                                        <span><strong><?php echo $count; ?></strong></span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar" style="width: <?php echo $pourcentage; ?>%;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nouveaux clients -->
            <div class="tab-pane fade" id="nouveaux" role="tabpanel">
                <div class="chart-container mt-3">
                    <h5><i class="fas fa-user-plus"></i> Évolution des Nouveaux Clients</h5>
                    <canvas id="nouveauxClientsChart" height="80"></canvas>
                </div>
            </div>

            <!-- Comportements d'achat -->
            <div class="tab-pane fade" id="comportement" role="tabpanel">
                <div class="mt-3">
                    <div class="row">
                        <?php 
                        $comportements = [
                            'Gros Paniers' => array_filter($clientsActifs, fn($c) => $c['panier_moyen'] > 500),
                            'Acheteurs Fréquents' => array_filter($clientsActifs, fn($c) => $c['nombre_achats'] >= 5),
                            'Clients Récents' => array_filter($clientsActifs, fn($c) => isset($c['recence_jours']) && $c['recence_jours'] <= 30),
                            'Relations Longues' => array_filter($clientsActifs, fn($c) => $c['duree_relation'] > 90)
                        ];
                        foreach ($comportements as $type => $clients_type): 
                        ?>
                            <div class="col-md-6 mb-3">
                                <div class="behavior-card">
                                    <h6><?php echo $type; ?></h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="behavior-count"><?php echo count($clients_type); ?> clients</span>
                                        <span class="behavior-percentage">
                                            <?php echo number_format((count($clients_type) / max($clientsActifsCount, 1)) * 100, 1); ?>%
                                        </span>
                                    </div>
                                    <div class="progress mt-2" style="height: 8px;">
                                        <div class="progress-bar" 
                                             style="width: <?php echo (count($clients_type) / max($clientsActifsCount, 1)) * 100; ?>%;"></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .segment-stats {
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }
    .segment-item {
        padding: 10px;
        background-color: white;
        border-radius: 5px;
        border: 1px solid #dee2e6;
    }
    .behavior-card {
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    .behavior-count {
        font-weight: bold;
        color: #495057;
    }
    .behavior-percentage {
        color: #6c757d;
        font-size: 0.9em;
    }
    </style>

    <script>
    // Graphique de segmentation
    const segments = <?php echo json_encode($segments ?? []); ?>;
    if (Object.keys(segments).length > 0) {
        const segmentCtx = document.getElementById('segmentationChart').getContext('2d');
        new Chart(segmentCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(segments),
                datasets: [{
                    data: Object.values(segments),
                    backgroundColor: ['#e5e7eb', '#fbbf24', '#9ca3af', '#cd7c2f'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Graphique de fidélisation
    const fideliteData = <?php echo json_encode($fideliteStats ?? []); ?>;
    const fidelisationCtx = document.getElementById('fidelisationChart').getContext('2d');
    new Chart(fidelisationCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(fideliteData),
            datasets: [{
                label: 'Nombre de Clients',
                data: Object.values(fideliteData),
                backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#6c757d'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Répartition par Niveau de Fidélité'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Graphique nouveaux clients
    const nouveauxData = <?php echo json_encode($evolutionNouveaux); ?>;
    if (Object.keys(nouveauxData).length > 0) {
        const nouveauxCtx = document.getElementById('nouveauxClientsChart').getContext('2d');
        const dates = Object.keys(nouveauxData).sort();
        
        new Chart(nouveauxCtx, {
            type: 'line',
            data: {
                labels: dates.map(d => new Date(d).toLocaleDateString('fr-FR')),
                datasets: [{
                    label: 'Nouveaux Clients',
                    data: dates.map(d => nouveauxData[d].count),
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Évolution des Nouveaux Clients'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
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
    echo 'Erreur lors du chargement du rapport clients: ' . htmlspecialchars($e->getMessage());
    echo '</div>';
}
?>
