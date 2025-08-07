<?php
/**
 * Rapport des caissiers - Analyse des performances et productivité
 * Inclut les statistiques de vente et la gestion d'équipe
 */

try {
    // Analyse détaillée des caissiers
    $caissiersQuery = $pdo->prepare("
        SELECT 
            ISNULL(fv.FCTV_UTILISATEUR, 'Non défini') as code_caissier,
            CASE 
                WHEN u.USR_PRENOM IS NOT NULL AND u.USR_NOM IS NOT NULL 
                THEN CONCAT(u.USR_PRENOM, ' ', u.USR_NOM)
                ELSE ISNULL(fv.FCTV_UTILISATEUR, 'Non défini')
            END as nom_caissier,
            u.USR_EMAIL as email_caissier,
            u.USR_TELEPHONE as telephone_caissier,
            u.USR_POSTE as poste,
            COUNT(DISTINCT fv.FCTV_REF) as nombre_transactions,
            SUM(fv.FCTV_MNT_TTC) as chiffre_affaires,
            AVG(fv.FCTV_MNT_TTC) as ticket_moyen,
            MIN(fv.FCTV_MNT_TTC) as ticket_min,
            MAX(fv.FCTV_MNT_TTC) as ticket_max,
            COUNT(DISTINCT fv.CLT_REF) as clients_servis,
            COUNT(DISTINCT CAST(fv.FCTV_DATE as DATE)) as jours_travailles,
            MIN(fv.FCTV_DATE) as premiere_vente,
            MAX(fv.FCTV_DATE) as derniere_vente,
            COUNT(DISTINCT fv.FCTV_MODEPAIEMENT) as modes_paiement_utilises
        FROM FACTURE_VNT fv
        LEFT JOIN UTILISATEUR u ON (fv.FCTV_UTILISATEUR = u.USR_LOGIN OR fv.FCTV_UTILISATEUR = u.USR_REF)
        WHERE fv.FCTV_DATE BETWEEN ? AND ? 
            AND fv.FCTV_VALIDE = 1
        GROUP BY fv.FCTV_UTILISATEUR, u.USR_PRENOM, u.USR_NOM, u.USR_EMAIL, u.USR_TELEPHONE, u.USR_POSTE
        ORDER BY chiffre_affaires DESC
    ");
    $caissiersQuery->execute([$dateFrom, $dateTo]);
    $caissiers = $caissiersQuery->fetchAll(PDO::FETCH_ASSOC);

    // Performance par heure de la journée
    $performanceHoraire = $pdo->prepare("
        SELECT 
            ISNULL(fv.FCTV_UTILISATEUR, 'Non défini') as caissier,
            DATEPART(HOUR, fv.FCTV_DATE) as heure,
            COUNT(DISTINCT fv.FCTV_REF) as nb_transactions,
            SUM(fv.FCTV_MNT_TTC) as ca_heure
        FROM FACTURE_VNT fv
        WHERE fv.FCTV_DATE BETWEEN ? AND ? 
            AND fv.FCTV_VALIDE = 1
        GROUP BY fv.FCTV_UTILISATEUR, DATEPART(HOUR, fv.FCTV_DATE)
        ORDER BY caissier, heure
    ");
    $performanceHoraire->execute([$dateFrom, $dateTo]);
    $horaireData = $performanceHoraire->fetchAll(PDO::FETCH_ASSOC);

    // Performance par jour de la semaine
    $performanceJourSemaine = $pdo->prepare("
        SELECT 
            ISNULL(fv.FCTV_UTILISATEUR, 'Non défini') as caissier,
            CASE DATEPART(WEEKDAY, fv.FCTV_DATE)
                WHEN 1 THEN 'Dimanche'
                WHEN 2 THEN 'Lundi'
                WHEN 3 THEN 'Mardi'
                WHEN 4 THEN 'Mercredi'
                WHEN 5 THEN 'Jeudi'
                WHEN 6 THEN 'Vendredi'
                WHEN 7 THEN 'Samedi'
            END as jour_semaine,
            COUNT(DISTINCT fv.FCTV_REF) as nb_transactions,
            SUM(fv.FCTV_MNT_TTC) as ca_jour,
            AVG(fv.FCTV_MNT_TTC) as ticket_moyen_jour
        FROM FACTURE_VNT fv
        WHERE fv.FCTV_DATE BETWEEN ? AND ? 
            AND fv.FCTV_VALIDE = 1
        GROUP BY fv.FCTV_UTILISATEUR, DATEPART(WEEKDAY, fv.FCTV_DATE)
        ORDER BY caissier, DATEPART(WEEKDAY, fv.FCTV_DATE)
    ");
    $performanceJourSemaine->execute([$dateFrom, $dateTo]);
    $jourSemaineData = $performanceJourSemaine->fetchAll(PDO::FETCH_ASSOC);

    // Analyse des erreurs/annulations (si disponible)
    $erreurs = $pdo->prepare("
        SELECT 
            ISNULL(fv.FCTV_UTILISATEUR, 'Non défini') as caissier,
            COUNT(*) as nb_annulations
        FROM FACTURE_VNT fv
        WHERE fv.FCTV_DATE BETWEEN ? AND ? 
            AND fv.FCTV_VALIDE = 0
        GROUP BY fv.FCTV_UTILISATEUR
        ORDER BY nb_annulations DESC
    ");
    $erreurs->execute([$dateFrom, $dateTo]);
    $erreursData = $erreurs->fetchAll(PDO::FETCH_ASSOC);

    // Calculs des totaux et moyennes
    $totalCA = array_sum(array_column($caissiers, 'chiffre_affaires'));
    $totalTransactions = array_sum(array_column($caissiers, 'nombre_transactions'));

    ?>

    <div class="report-card animate-fadeIn">
        <div class="report-title">
            <span><i class="fas fa-cash-register"></i> Rapport de Performance des Caissiers</span>
            <small class="text-muted">Période: <?php echo date('d/m/Y', strtotime($dateFrom)) . ' - ' . date('d/m/Y', strtotime($dateTo)); ?></small>
        </div>

        <!-- KPIs des caissiers -->
        <div class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-primary"><?php echo count($caissiers); ?></h4>
                        <p class="mb-0">Caissiers Actifs</p>
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
                        <h4 class="text-info"><?php echo number_format($totalTransactions); ?></h4>
                        <p class="mb-0">Total Transactions</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card">
                        <h4 class="text-warning"><?php echo formatCurrency($totalCA / max(count($caissiers), 1)); ?></h4>
                        <p class="mb-0">CA Moyen/Caissier</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique de comparaison des performances -->
        <div class="chart-container mb-4">
            <h5><i class="fas fa-chart-bar"></i> Comparaison des Performances (Top 10)</h5>
            <canvas id="caissiersChart" height="80"></canvas>
        </div>

        <!-- Onglets pour différentes analyses -->
        <ul class="nav nav-tabs" id="caissiersTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="performance-tab" data-bs-toggle="tab" data-bs-target="#performance" 
                        type="button" role="tab">Performance Individuelle</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="horaires-tab" data-bs-toggle="tab" data-bs-target="#horaires" 
                        type="button" role="tab">Analyse Horaire</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="productivite-tab" data-bs-toggle="tab" data-bs-target="#productivite" 
                        type="button" role="tab">Productivité</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="qualite-tab" data-bs-toggle="tab" data-bs-target="#qualite" 
                        type="button" role="tab">Qualité Service</button>
            </li>
        </ul>

        <div class="tab-content" id="caissiersTabContent">
            <!-- Performance individuelle -->
            <div class="tab-pane fade show active" id="performance" role="tabpanel">
                <div class="table-responsive mt-3">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Rang</th>
                                <th>Code Caissier</th>
                                <th>Nom Complet</th>
                                <th>Poste</th>
                                <th>Contact</th>
                                <th>Nb Transactions</th>
                                <th>Chiffre d'Affaires</th>
                                <th>% du CA Total</th>
                                <th>Ticket Moyen</th>
                                <th>Clients Servis</th>
                                <th>Jours Travaillés</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($caissiers as $index => $caissier): 
                                $pourcentageCA = $totalCA > 0 ? ($caissier['chiffre_affaires'] / $totalCA) * 100 : 0;
                                $performanceLevel = $index < 2 ? 'Excellent' : ($index < 5 ? 'Très Bon' : ($index < 8 ? 'Bon' : 'Moyen'));
                                $badgeClass = $index < 2 ? 'badge-success' : ($index < 5 ? 'badge-warning' : ($index < 8 ? 'badge-info' : 'badge-secondary'));
                                $productivite = $caissier['jours_travailles'] > 0 ? $caissier['nombre_transactions'] / $caissier['jours_travailles'] : 0;
                            ?>
                                <tr>
                                    <td>
                                        <span class="status-badge <?php echo $badgeClass; ?>">
                                            #<?php echo $index + 1; ?>
                                        </span>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($caissier['code_caissier']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($caissier['nom_caissier']); ?></td>
                                    <td>
                                        <?php if ($caissier['poste']): ?>
                                            <small class="badge badge-secondary"><?php echo htmlspecialchars($caissier['poste']); ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">-</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($caissier['email_caissier']): ?>
                                            <small class="d-block"><?php echo htmlspecialchars($caissier['email_caissier']); ?></small>
                                        <?php endif; ?>
                                        <?php if ($caissier['telephone_caissier']): ?>
                                            <small class="text-muted"><?php echo htmlspecialchars($caissier['telephone_caissier']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary"><?php echo number_format($caissier['nombre_transactions']); ?></span>
                                        <br><small class="text-muted"><?php echo number_format($productivite, 1); ?>/jour</small>
                                    </td>
                                    <td class="text-success">
                                        <strong><?php echo formatCurrency($caissier['chiffre_affaires']); ?></strong>
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
                                        <?php echo formatCurrency($caissier['ticket_moyen']); ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-info"><?php echo number_format($caissier['clients_servis']); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning"><?php echo number_format($caissier['jours_travailles']); ?></span>
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

            <!-- Analyse horaire -->
            <div class="tab-pane fade" id="horaires" role="tabpanel">
                <div class="chart-container mt-3">
                    <h5><i class="fas fa-clock"></i> Performance par Heure de la Journée</h5>
                    <canvas id="horaireChart" height="80"></canvas>
                </div>
                
                <div class="mt-4">
                    <h6>Répartition par Jour de la Semaine</h6>
                    <canvas id="jourSemaineChart" height="60"></canvas>
                </div>
            </div>

            <!-- Productivité -->
            <div class="tab-pane fade" id="productivite" role="tabpanel">
                <div class="mt-3">
                    <div class="row">
                        <?php 
                        // Calcul des KPIs de productivité
                        $meilleurProductivite = max(array_map(function($c) { 
                            return $c['jours_travailles'] > 0 ? $c['nombre_transactions'] / $c['jours_travailles'] : 0; 
                        }, $caissiers));
                        
                        $meilleurTicket = max(array_column($caissiers, 'ticket_moyen'));
                        $moyenneEquipe = $totalTransactions / max(array_sum(array_column($caissiers, 'jours_travailles')), 1);
                        ?>
                        
                        <div class="col-md-4">
                            <div class="productivity-card">
                                <h6><i class="fas fa-trophy text-warning"></i> Meilleure Productivité</h6>
                                <h4 class="text-primary"><?php echo number_format($meilleurProductivite, 1); ?></h4>
                                <p class="mb-0">Transactions/jour</p>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="productivity-card">
                                <h6><i class="fas fa-chart-line text-success"></i> Meilleur Ticket Moyen</h6>
                                <h4 class="text-success"><?php echo formatCurrency($meilleurTicket); ?></h4>
                                <p class="mb-0">Par transaction</p>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="productivity-card">
                                <h6><i class="fas fa-users text-info"></i> Moyenne Équipe</h6>
                                <h4 class="text-info"><?php echo number_format($moyenneEquipe, 1); ?></h4>
                                <p class="mb-0">Transactions/jour</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Graphique de productivité individuelle -->
                    <div class="chart-container mt-4">
                        <h6>Productivité Individuelle (Transactions/Jour)</h6>
                        <canvas id="productiviteChart" height="60"></canvas>
                    </div>
                </div>
            </div>

            <!-- Qualité de service -->
            <div class="tab-pane fade" id="qualite" role="tabpanel">
                <div class="mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-exclamation-triangle text-warning"></i> Analyse des Erreurs/Annulations</h6>
                            <?php if (!empty($erreursData)): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Caissier</th>
                                                <th>Nb Annulations</th>
                                                <th>Taux d'Erreur</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($erreursData as $erreur): 
                                                $caissierInfo = array_filter($caissiers, function($c) use ($erreur) {
                                                    return $c['code_caissier'] === $erreur['caissier'];
                                                });
                                                $caissierInfo = reset($caissierInfo);
                                                $tauxErreur = $caissierInfo ? ($erreur['nb_annulations'] / max($caissierInfo['nombre_transactions'], 1)) * 100 : 0;
                                            ?>
                                                <tr class="<?php echo $tauxErreur > 5 ? 'table-warning' : ''; ?>">
                                                    <td><?php echo htmlspecialchars($erreur['caissier']); ?></td>
                                                    <td>
                                                        <span class="badge badge-danger"><?php echo $erreur['nb_annulations']; ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="<?php echo $tauxErreur > 5 ? 'text-danger' : 'text-success'; ?>">
                                                            <?php echo number_format($tauxErreur, 2); ?>%
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> Aucune annulation détectée pour cette période.
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6">
                            <h6><i class="fas fa-award text-success"></i> Indicateurs de Qualité</h6>
                            <div class="quality-metrics">
                                <?php foreach (array_slice($caissiers, 0, 5) as $caissier): 
                                    $versatilite = $caissier['modes_paiement_utilises'];
                                    $fideliteClient = $caissier['clients_servis'] > 0 ? $caissier['nombre_transactions'] / $caissier['clients_servis'] : 0;
                                ?>
                                    <div class="quality-item mb-3">
                                        <div class="d-flex justify-content-between">
                                            <strong><?php echo htmlspecialchars($caissier['nom_caissier']); ?></strong>
                                            <span class="quality-score">
                                                <?php 
                                                $score = min(100, ($versatilite * 20) + ($fideliteClient * 10));
                                                echo number_format($score, 0) . '/100';
                                                ?>
                                            </span>
                                        </div>
                                        <div class="quality-details">
                                            <small class="d-block">Polyvalence: <?php echo $versatilite; ?> modes de paiement</small>
                                            <small class="text-muted">Fidélisation: <?php echo number_format($fideliteClient, 1); ?> transactions/client</small>
                                        </div>
                                        <div class="progress mt-1" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: <?php echo min($score, 100); ?>%;"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .productivity-card {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
    }
    .quality-metrics {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
    }
    .quality-item {
        background-color: white;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #dee2e6;
    }
    .quality-score {
        font-weight: bold;
        color: #28a745;
    }
    </style>

    <script>
    // Graphique de comparaison des caissiers (Top 10)
    const caissiersData = <?php echo json_encode(array_slice($caissiers, 0, 10)); ?>;
    
    const caissiersCtx = document.getElementById('caissiersChart').getContext('2d');
    new Chart(caissiersCtx, {
        type: 'bar',
        data: {
            labels: caissiersData.map(c => c.nom_caissier),
            datasets: [{
                label: 'Chiffre d\'Affaires (DH)',
                data: caissiersData.map(c => parseFloat(c.chiffre_affaires)),
                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                borderColor: 'rgba(40, 167, 69, 1)',
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
                    text: 'Top 10 Caissiers par Chiffre d\'Affaires'
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

    // Graphique horaire
    const horaireData = <?php echo json_encode($horaireData); ?>;
    if (horaireData.length > 0) {
        const horaireCtx = document.getElementById('horaireChart').getContext('2d');
        
        // Groupement par heure
        const heuresStats = {};
        for (let h = 8; h <= 22; h++) {
            heuresStats[h] = 0;
        }
        
        horaireData.forEach(item => {
            if (heuresStats.hasOwnProperty(item.heure)) {
                heuresStats[item.heure] += parseFloat(item.ca_heure);
            }
        });

        new Chart(horaireCtx, {
            type: 'line',
            data: {
                labels: Object.keys(heuresStats).map(h => h + 'h'),
                datasets: [{
                    label: 'CA par Heure (DH)',
                    data: Object.values(heuresStats),
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
                        text: 'Performance par Heure de la Journée'
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

    // Graphique par jour de la semaine
    const jourSemaineData = <?php echo json_encode($jourSemaineData); ?>;
    if (jourSemaineData.length > 0) {
        const jourSemaineCtx = document.getElementById('jourSemaineChart').getContext('2d');
        
        const joursStats = {};
        const joursOrdre = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        
        joursOrdre.forEach(jour => {
            joursStats[jour] = 0;
        });
        
        jourSemaineData.forEach(item => {
            if (joursStats.hasOwnProperty(item.jour_semaine)) {
                joursStats[item.jour_semaine] += parseFloat(item.ca_jour);
            }
        });

        new Chart(jourSemaineCtx, {
            type: 'bar',
            data: {
                labels: joursOrdre,
                datasets: [{
                    label: 'CA par Jour (DH)',
                    data: joursOrdre.map(jour => joursStats[jour]),
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

    // Graphique de productivité
    const productiviteCtx = document.getElementById('productiviteChart').getContext('2d');
    const productiviteData = caissiersData.map(c => 
        c.jours_travailles > 0 ? c.nombre_transactions / c.jours_travailles : 0
    );

    new Chart(productiviteCtx, {
        type: 'bar',
        data: {
            labels: caissiersData.map(c => c.nom_caissier),
            datasets: [{
                label: 'Transactions/Jour',
                data: productiviteData,
                backgroundColor: 'rgba(23, 162, 184, 0.8)',
                borderColor: 'rgba(23, 162, 184, 1)',
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
                            return value.toFixed(1);
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
    </script>

    <?php

} catch (PDOException $e) {
    echo '<div class="alert alert-danger animate-fadeIn">';
    echo '<i class="fas fa-exclamation-triangle"></i> ';
    echo 'Erreur lors du chargement du rapport caissiers: ' . htmlspecialchars($e->getMessage());
    echo '</div>';
}
?>
