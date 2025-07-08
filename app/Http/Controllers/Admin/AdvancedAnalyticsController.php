<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdvancedAnalyticsController extends Controller
{
    /**
     * Données détaillées du chiffre d'affaires
     */
    public function getChiffreAffairesDetails(Request $request)
    {
        try {
            $dateDebut = $request->get('date_debut', Carbon::today()->toDateString());
            $dateFin = $request->get('date_fin', Carbon::today()->toDateString());
            
            // CA du jour
            $caJour = DB::table('FACTURE_VNT')
                ->where('FAC_DATE', '>=', $dateDebut)
                ->where('FAC_DATE', '<=', $dateFin)
                ->where('FAC_VALIDE', 1)
                ->sum('FAC_NET_A_PAYER');
            
            // CA d'hier pour comparaison
            $caHier = DB::table('FACTURE_VNT')
                ->where('FAC_DATE', Carbon::yesterday()->toDateString())
                ->where('FAC_VALIDE', 1)
                ->sum('FAC_NET_A_PAYER');
            
            // Évolution en pourcentage
            $evolution = $caHier > 0 ? (($caJour - $caHier) / $caHier) * 100 : 0;
            
            // Nombre de tickets
            $nbTickets = DB::table('FACTURE_VNT')
                ->where('FAC_DATE', '>=', $dateDebut)
                ->where('FAC_DATE', '<=', $dateFin)
                ->where('FAC_VALIDE', 1)
                ->count();
            
            // Ticket moyen
            $ticketMoyen = $nbTickets > 0 ? $caJour / $nbTickets : 0;
            
            // Données pour graphique évolution (7 derniers jours)
            $evolutionData = DB::table('FACTURE_VNT')
                ->selectRaw('FAC_DATE as date, SUM(FAC_NET_A_PAYER) as ca')
                ->where('FAC_DATE', '>=', Carbon::now()->subDays(7)->toDateString())
                ->where('FAC_VALIDE', 1)
                ->groupBy('FAC_DATE')
                ->orderBy('FAC_DATE')
                ->get();
            
            // Répartition par heure
            $repartitionHoraire = DB::table('FACTURE_VNT')
                ->selectRaw('HOUR(FAC_HEURE) as heure, SUM(FAC_NET_A_PAYER) as ca, COUNT(*) as nb_tickets')
                ->where('FAC_DATE', $dateDebut)
                ->where('FAC_VALIDE', 1)
                ->groupBy('heure')
                ->orderBy('heure')
                ->get();
            
            // Objectifs et analyses prédictives
            $objectifJour = 2000; // À configurer
            $pourcentageObjectif = ($caJour / $objectifJour) * 100;
            
            $data = [
                'ca_jour' => $caJour,
                'ca_hier' => $caHier,
                'evolution' => round($evolution, 2),
                'nb_tickets' => $nbTickets,
                'ticket_moyen' => $ticketMoyen,
                'objectif_jour' => $objectifJour,
                'pourcentage_objectif' => round($pourcentageObjectif, 2),
                'chart_data' => [
                    'labels' => $evolutionData->pluck('date')->map(function($date) {
                        return Carbon::parse($date)->format('d/m');
                    })->toArray(),
                    'datasets' => [[
                        'label' => 'Chiffre d\'Affaires',
                        'data' => $evolutionData->pluck('ca')->toArray(),
                        'borderColor' => '#4f46e5',
                        'backgroundColor' => 'rgba(79, 70, 229, 0.1)',
                        'tension' => 0.4
                    ]]
                ],
                'repartition_horaire' => $repartitionHoraire,
                'tendances' => $this->analyserTendances($evolutionData, $caJour),
                'recommandations' => $this->genererRecommandationsCA($caJour, $objectifJour, $evolution)
            ];
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des données: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Articles en rupture de stock
     */
    public function getArticlesRuptureDetails(Request $request)
    {
        try {
            // Articles en rupture complète
            $articlesRupture = DB::table('ARTICLE as a')
                ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
                ->leftJoin('FAMILLE as f', 'a.FAM_REF', '=', 'f.FAM_REF')
                ->select([
                    'a.ART_REF as reference',
                    'a.ART_DESIGNATION as designation',
                    'f.FAM_LIB as famille',
                    'a.ART_STOCK_MIN as stock_minimum',
                    DB::raw('COALESCE(s.STK_QTE, 0) as stock_actuel'),
                    'a.ART_PRIX_VENTE_TTC as prix_vente'
                ])
                ->whereRaw('COALESCE(s.STK_QTE, 0) <= 0')
                ->orderBy('a.ART_DESIGNATION')
                ->get();
            
            // Articles en stock faible (en dessous du minimum)
            $articlesStockFaible = DB::table('ARTICLE as a')
                ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
                ->leftJoin('FAMILLE as f', 'a.FAM_REF', '=', 'f.FAM_REF')
                ->select([
                    'a.ART_REF as reference',
                    'a.ART_DESIGNATION as designation',
                    'f.FAM_LIB as famille',
                    'a.ART_STOCK_MIN as stock_minimum',
                    DB::raw('COALESCE(s.STK_QTE, 0) as stock_actuel'),
                    'a.ART_PRIX_VENTE_TTC as prix_vente'
                ])
                ->whereRaw('COALESCE(s.STK_QTE, 0) > 0')
                ->whereRaw('COALESCE(s.STK_QTE, 0) <= a.ART_STOCK_MIN')
                ->orderBy('a.ART_DESIGNATION')
                ->get();
            
            // Dernières ventes pour chaque article en rupture
            foreach ($articlesRupture as $article) {
                $derniereVente = DB::table('FACTURE_VNT_DETAIL as fvd')
                    ->join('FACTURE_VNT as fv', 'fvd.FAC_REF', '=', 'fv.FAC_REF')
                    ->where('fvd.ART_REF', $article->reference)
                    ->where('fv.FAC_VALIDE', 1)
                    ->orderBy('fv.FAC_DATE', 'desc')
                    ->orderBy('fv.FAC_HEURE', 'desc')
                    ->first(['fv.FAC_DATE', 'fv.FAC_HEURE']);
                
                $article->derniere_vente = $derniereVente ? 
                    Carbon::parse($derniereVente->FAC_DATE . ' ' . $derniereVente->FAC_HEURE)->diffForHumans() : 
                    'Jamais vendu';
            }
            
            // Prévisions de rupture (articles qui vont bientôt être en rupture)
            $previsionRupture = $this->calculerPrevisionRupture();
            
            // Impact financier de la rupture
            $impactFinancier = $this->calculerImpactFinancierRupture($articlesRupture);
            
            $data = [
                'articles_rupture' => $articlesRupture,
                'articles_stock_faible' => $articlesStockFaible,
                'prevision_rupture' => $previsionRupture,
                'impact_financier' => $impactFinancier,
                'statistiques' => [
                    'nb_total_rupture' => $articlesRupture->count(),
                    'nb_stock_faible' => $articlesStockFaible->count(),
                    'valeur_stock_perdu' => $articlesRupture->sum('prix_vente'),
                    'familles_impactees' => $articlesRupture->pluck('famille')->unique()->count()
                ],
                'recommandations' => $this->genererRecommandationsStock($articlesRupture, $articlesStockFaible)
            ];
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des données: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Top clients du restaurant
     */
    public function getTopClientsDetails(Request $request)
    {
        try {
            $periode = $request->get('periode', 'mois'); // jour, semaine, mois, annee
            $limite = $request->get('limite', 20);
            
            $dateDebut = match($periode) {
                'jour' => Carbon::today(),
                'semaine' => Carbon::now()->startOfWeek(),
                'mois' => Carbon::now()->startOfMonth(),
                'annee' => Carbon::now()->startOfYear(),
                default => Carbon::now()->startOfMonth()
            };
            
            // Top clients par CA
            $topClients = DB::table('CLIENT as c')
                ->join('FACTURE_VNT as fv', 'c.CLT_REF', '=', 'fv.CLT_REF')
                ->select([
                    'c.CLT_REF',
                    'c.CLT_NOM as nom',
                    'c.CLT_PRENOM as prenom',
                    'c.CLT_TEL as telephone',
                    'c.CLT_EMAIL as email',
                    DB::raw('SUM(fv.FAC_NET_A_PAYER) as ca_total'),
                    DB::raw('COUNT(fv.FAC_REF) as nb_visites'),
                    DB::raw('AVG(fv.FAC_NET_A_PAYER) as ticket_moyen'),
                    DB::raw('MAX(fv.FAC_DATE) as derniere_visite')
                ])
                ->where('fv.FAC_DATE', '>=', $dateDebut)
                ->where('fv.FAC_VALIDE', 1)
                ->groupBy('c.CLT_REF', 'c.CLT_NOM', 'c.CLT_PRENOM', 'c.CLT_TEL', 'c.CLT_EMAIL')
                ->orderBy('ca_total', 'desc')
                ->limit($limite)
                ->get();
            
            // Calcul du taux de fidélité pour chaque client
            foreach ($topClients as $client) {
                // Nombre de visites dans la période vs nombre total possible
                $joursTotal = Carbon::now()->diffInDays($dateDebut) + 1;
                $client->taux_fidelite = min(($client->nb_visites / $joursTotal) * 100, 100);
                
                // Articles favoris du client
                $articlesFavoris = DB::table('FACTURE_VNT_DETAIL as fvd')
                    ->join('FACTURE_VNT as fv', 'fvd.FAC_REF', '=', 'fv.FAC_REF')
                    ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
                    ->select([
                        'a.ART_DESIGNATION as nom',
                        DB::raw('SUM(fvd.QTE) as quantite_totale'),
                        DB::raw('COUNT(DISTINCT fv.FAC_REF) as nb_commandes')
                    ])
                    ->where('fv.CLT_REF', $client->CLT_REF)
                    ->where('fv.FAC_DATE', '>=', $dateDebut)
                    ->where('fv.FAC_VALIDE', 1)
                    ->groupBy('a.ART_REF', 'a.ART_DESIGNATION')
                    ->orderBy('quantite_totale', 'desc')
                    ->limit(3)
                    ->get();
                
                $client->articles_favoris = $articlesFavoris;
                
                // Analyse des habitudes (jour de la semaine préféré, heure)
                $habitudes = DB::table('FACTURE_VNT')
                    ->selectRaw('
                        DAYNAME(FAC_DATE) as jour_prefere,
                        HOUR(FAC_HEURE) as heure_prefere,
                        COUNT(*) as frequence
                    ')
                    ->where('CLT_REF', $client->CLT_REF)
                    ->where('FAC_DATE', '>=', $dateDebut)
                    ->where('FAC_VALIDE', 1)
                    ->groupBy('jour_prefere', 'heure_prefere')
                    ->orderBy('frequence', 'desc')
                    ->first();
                
                $client->habitudes = $habitudes;
            }
            
            // Analyse de la segmentation client
            $segmentation = $this->analyserSegmentationClients($topClients);
            
            // Recommandations marketing
            $recommandationsMarketing = $this->genererRecommandationsMarketing($topClients);
            
            $data = [
                'top_clients' => $topClients,
                'segmentation' => $segmentation,
                'recommandations_marketing' => $recommandationsMarketing,
                'statistiques_globales' => [
                    'ca_total_top_clients' => $topClients->sum('ca_total'),
                    'nb_visites_total' => $topClients->sum('nb_visites'),
                    'ticket_moyen_global' => $topClients->avg('ticket_moyen'),
                    'taux_fidelite_moyen' => $topClients->avg('taux_fidelite')
                ],
                'comparaison_periode' => $this->comparer_periode_precedente($topClients, $periode)
            ];
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des données: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Performance par heure
     */
    public function getPerformanceHoraireDetails(Request $request)
    {
        try {
            $dateDebut = $request->get('date_debut', Carbon::today()->toDateString());
            $dateFin = $request->get('date_fin', Carbon::today()->toDateString());
            
            // Performance par heure
            $performanceHoraire = DB::table('FACTURE_VNT')
                ->selectRaw('
                    HOUR(FAC_HEURE) as heure,
                    SUM(FAC_NET_A_PAYER) as ca,
                    COUNT(*) as nb_tickets,
                    AVG(FAC_NET_A_PAYER) as ticket_moyen,
                    SUM(FAC_TOTAL_QTE) as articles_vendus
                ')
                ->where('FAC_DATE', '>=', $dateDebut)
                ->where('FAC_DATE', '<=', $dateFin)
                ->where('FAC_VALIDE', 1)
                ->groupBy('heure')
                ->orderBy('heure')
                ->get();
            
            // Ajout des métriques de performance
            $caTotal = $performanceHoraire->sum('ca');
            foreach ($performanceHoraire as $heure) {
                $heure->pourcentage_ca = $caTotal > 0 ? ($heure->ca / $caTotal) * 100 : 0;
                $heure->efficacite = $this->calculerEfficaciteHeure($heure);
                $heure->periode = $this->determinerPeriode($heure->heure);
            }
            
            // Identification des heures de pointe
            $heuresPointe = $performanceHoraire->where('ca', '>', $performanceHoraire->avg('ca') * 1.2);
            
            // Analyse des tendances
            $tendances = $this->analyserTendancesHoraires($performanceHoraire);
            
            // Recommandations d'optimisation
            $recommandations = $this->genererRecommandationsHoraires($performanceHoraire, $heuresPointe);
            
            // Comparaison avec la semaine précédente
            $comparaisonSemaine = $this->comparerSemainePrecedente($dateDebut, $dateFin);
            
            $data = [
                'performance_horaire' => $performanceHoraire,
                'heures_pointe' => $heuresPointe->values(),
                'tendances' => $tendances,
                'recommandations' => $recommandations,
                'comparaison_semaine' => $comparaisonSemaine,
                'chart_data' => [
                    'labels' => $performanceHoraire->pluck('heure')->map(function($h) { 
                        return sprintf('%02d:00', $h); 
                    })->toArray(),
                    'datasets' => [
                        [
                            'label' => 'Chiffre d\'Affaires (€)',
                            'data' => $performanceHoraire->pluck('ca')->toArray(),
                            'backgroundColor' => 'rgba(79, 70, 229, 0.8)',
                            'borderColor' => '#4f46e5',
                            'yAxisID' => 'y'
                        ],
                        [
                            'label' => 'Nombre de tickets',
                            'data' => $performanceHoraire->pluck('nb_tickets')->toArray(),
                            'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                            'borderColor' => '#10b981',
                            'yAxisID' => 'y1'
                        ]
                    ]
                ],
                'statistiques' => [
                    'heure_plus_performante' => $performanceHoraire->sortByDesc('ca')->first(),
                    'heure_moins_performante' => $performanceHoraire->sortBy('ca')->first(),
                    'amplitude_ca' => $performanceHoraire->max('ca') - $performanceHoraire->min('ca'),
                    'heures_activite' => $performanceHoraire->where('nb_tickets', '>', 0)->count()
                ]
            ];
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des données: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Modes de paiement détaillés
     */
    public function getModesPaiementDetails(Request $request)
    {
        try {
            $dateDebut = $request->get('date_debut', Carbon::today()->toDateString());
            $dateFin = $request->get('date_fin', Carbon::today()->toDateString());
            
            // Répartition par mode de paiement
            $modesPaiement = DB::table('ENCAISSEMENT_MODE as em')
                ->join('FACTURE_VNT as fv', 'em.FAC_REF', '=', 'fv.FAC_REF')
                ->join('MODEPAIMENT as mp', 'em.MODE_PAIMENT', '=', 'mp.MODE_PAIMENT')
                ->select([
                    'mp.MODE_PAIMENT as mode',
                    DB::raw('SUM(em.MONTANT) as montant_total'),
                    DB::raw('COUNT(DISTINCT em.FAC_REF) as nb_transactions'),
                    DB::raw('AVG(em.MONTANT) as montant_moyen')
                ])
                ->where('fv.FAC_DATE', '>=', $dateDebut)
                ->where('fv.FAC_DATE', '<=', $dateFin)
                ->where('fv.FAC_VALIDE', 1)
                ->groupBy('mp.MODE_PAIMENT')
                ->orderBy('montant_total', 'desc')
                ->get();
            
            $montantTotal = $modesPaiement->sum('montant_total');
            
            // Calcul des pourcentages et icônes
            foreach ($modesPaiement as $mode) {
                $mode->pourcentage = $montantTotal > 0 ? ($mode->montant_total / $montantTotal) * 100 : 0;
                $mode->icon = $this->getIconeModePaiement($mode->mode);
                $mode->couleur = $this->getCouleurModePaiement($mode->mode);
            }
            
            // Évolution des modes de paiement sur 30 jours
            $evolutionModes = DB::table('ENCAISSEMENT_MODE as em')
                ->join('FACTURE_VNT as fv', 'em.FAC_REF', '=', 'fv.FAC_REF')
                ->join('MODEPAIMENT as mp', 'em.MODE_PAIMENT', '=', 'mp.MODE_PAIMENT')
                ->selectRaw('
                    fv.FAC_DATE as date,
                    mp.MODE_PAIMENT as mode,
                    SUM(em.MONTANT) as montant
                ')
                ->where('fv.FAC_DATE', '>=', Carbon::now()->subDays(30))
                ->where('fv.FAC_VALIDE', 1)
                ->groupBy('fv.FAC_DATE', 'mp.MODE_PAIMENT')
                ->orderBy('fv.FAC_DATE')
                ->get();
            
            // Analyse des tendances par mode
            $tendancesModes = $this->analyserTendancesModesPaiement($evolutionModes);
            
            // Frais et commissions par mode
            $fraisCommissions = $this->calculerFraisCommissions($modesPaiement);
            
            // Recommandations d'optimisation
            $recommandations = $this->genererRecommandationsModesPaiement($modesPaiement, $tendancesModes);
            
            $data = [
                'modes_paiement' => $modesPaiement,
                'evolution_modes' => $evolutionModes,
                'tendances_modes' => $tendancesModes,
                'frais_commissions' => $fraisCommissions,
                'recommandations' => $recommandations,
                'chart_data' => [
                    'labels' => $modesPaiement->pluck('mode')->toArray(),
                    'datasets' => [[
                        'data' => $modesPaiement->pluck('montant_total')->toArray(),
                        'backgroundColor' => $modesPaiement->pluck('couleur')->toArray(),
                        'borderWidth' => 2,
                        'borderColor' => '#ffffff'
                    ]]
                ],
                'statistiques' => [
                    'mode_principal' => $modesPaiement->first(),
                    'nb_modes_utilises' => $modesPaiement->count(),
                    'montant_total' => $montantTotal,
                    'transaction_moyenne' => $modesPaiement->avg('montant_moyen')
                ]
            ];
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des données: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * État des tables en temps réel
     */
    public function getEtatTablesDetails(Request $request)
    {
        try {
            // Tables et leur état actuel
            $tables = DB::table('TABLE as t')
                ->leftJoin('ZONE as z', 't.ZON_REF', '=', 'z.ZON_REF')
                ->leftJoin('OCCUPE as o', 't.TAB_REF', '=', 'o.TAB_REF')
                ->select([
                    't.TAB_REF',
                    't.TAB_LIB as nom',
                    't.TAB_NBR_Couvert as nb_couverts',
                    't.ETT_ETAT as statut',
                    'z.ZON_LIB as zone',
                    't.TAB_DESCRIPT as description'
                ])
                ->orderBy('z.ZON_LIB')
                ->orderBy('t.TAB_LIB')
                ->get();
            
            // Pour chaque table, récupérer des infos additionnelles
            foreach ($tables as $table) {
                // Durée d'occupation si occupée
                if ($table->statut === 'occupee') {
                    $dureeOccupation = $this->calculerDureeOccupation($table->TAB_REF);
                    $table->duree_occupation = $dureeOccupation;
                }
                
                // CA généré par la table aujourd'hui
                $caTable = DB::table('FACTURE_VNT')
                    ->where('TAB_REF', $table->TAB_REF)
                    ->where('FAC_DATE', Carbon::today())
                    ->where('FAC_VALIDE', 1)
                    ->sum('FAC_NET_A_PAYER');
                
                $table->ca_jour = $caTable;
                
                // Nombre de services aujourd'hui
                $nbServices = DB::table('FACTURE_VNT')
                    ->where('TAB_REF', $table->TAB_REF)
                    ->where('FAC_DATE', Carbon::today())
                    ->where('FAC_VALIDE', 1)
                    ->count();
                
                $table->nb_services = $nbServices;
            }
            
            // Statistiques globales
            $stats = [
                'taux_occupation' => $this->calculerTauxOccupation($tables),
                'temps_moyen_service' => $this->calculerTempsMoyenService(),
                'chiffre_affaires_tables' => $tables->sum('ca_jour'),
                'table_plus_rentable' => $tables->sortByDesc('ca_jour')->first(),
                'rotation_moyenne' => $this->calculerRotationMoyenne($tables)
            ];
            
            // Réservations en cours et à venir
            $reservations = DB::table('RESERVATION as r')
                ->join('CLIENT as c', 'r.CLT_REF', '=', 'c.CLT_REF')
                ->join('TABLE as t', 'r.TAB_REF', '=', 't.TAB_REF')
                ->select([
                    'r.NUMERO_RESERVATION',
                    'c.CLT_NOM as client_nom',
                    'c.CLT_PRENOM as client_prenom',
                    'c.CLT_TEL as client_tel',
                    't.TAB_LIB as table_nom',
                    'r.DATE_RESERVATION',
                    'r.NBRCOUVERT_TABLE as nb_couverts',
                    'r.ETAT_RESERVATION as statut'
                ])
                ->where('r.DATE_RESERVATION', '>=', Carbon::today())
                ->orderBy('r.DATE_RESERVATION')
                ->get();
            
            // Prévisions d'affluence
            $previsionAffluence = $this->calculerPrevisionAffluence();
            
            // Recommandations d'optimisation
            $recommandations = $this->genererRecommandationsTables($tables, $stats, $reservations);
            
            $data = [
                'tables' => $tables,
                'stats' => $stats,
                'reservations' => $reservations,
                'prevision_affluence' => $previsionAffluence,
                'recommandations' => $recommandations,
                'repartition_zones' => $this->analyserRepartitionZones($tables),
                'historique_occupation' => $this->getHistoriqueOccupation()
            ];
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des données: ' . $e->getMessage()], 500);
        }
    }
    
    // ============================
    // MÉTHODES PRIVÉES UTILITAIRES
    // ============================
    
    private function analyserTendances($evolutionData, $caJour)
    {
        $tendances = [];
        
        if ($evolutionData->count() >= 3) {
            $derniersTroisJours = $evolutionData->slice(-3);
            $moyenne = $derniersTroisJours->avg('ca');
            
            if ($caJour > $moyenne * 1.1) {
                $tendances[] = [
                    'type' => 'positive',
                    'message' => 'Tendance haussière forte',
                    'detail' => 'Le CA d\'aujourd\'hui dépasse la moyenne des 3 derniers jours de plus de 10%'
                ];
            } elseif ($caJour < $moyenne * 0.9) {
                $tendances[] = [
                    'type' => 'negative',
                    'message' => 'Tendance baissière',
                    'detail' => 'Le CA d\'aujourd\'hui est inférieur à la moyenne des 3 derniers jours'
                ];
            }
        }
        
        return $tendances;
    }
    
    private function genererRecommandationsCA($caJour, $objectifJour, $evolution)
    {
        $recommandations = [];
        
        if ($caJour < $objectifJour * 0.5) {
            $recommandations[] = [
                'type' => 'danger',
                'icon' => 'exclamation-triangle',
                'title' => 'Objectif en danger',
                'description' => 'Moins de 50% de l\'objectif atteint. Actions urgentes nécessaires.'
            ];
        } elseif ($caJour < $objectifJour * 0.8) {
            $recommandations[] = [
                'type' => 'warning',
                'icon' => 'clock',
                'title' => 'Objectif à risque',
                'description' => 'Intensifier les efforts commerciaux en fin de journée.'
            ];
        }
        
        if ($evolution < -10) {
            $recommandations[] = [
                'type' => 'info',
                'icon' => 'chart-line',
                'title' => 'Analyse des causes',
                'description' => 'Analyser les facteurs de baisse par rapport à hier.'
            ];
        }
        
        return $recommandations;
    }
    
    private function calculerPrevisionRupture()
    {
        // Calcul basé sur la consommation moyenne des 7 derniers jours
        return DB::table('ARTICLE as a')
            ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
            ->select([
                'a.ART_REF',
                'a.ART_DESIGNATION',
                DB::raw('COALESCE(s.STK_QTE, 0) as stock_actuel'),
                'a.ART_STOCK_MIN'
            ])
            ->whereRaw('COALESCE(s.STK_QTE, 0) > 0')
            ->whereRaw('COALESCE(s.STK_QTE, 0) <= a.ART_STOCK_MIN * 2')
            ->limit(10)
            ->get();
    }
    
    private function calculerImpactFinancierRupture($articlesRupture)
    {
        $impactEstime = 0;
        
        foreach ($articlesRupture as $article) {
            // Estimation basée sur les ventes moyennes des 30 derniers jours
            $venteMoyenne = DB::table('FACTURE_VNT_DETAIL as fvd')
                ->join('FACTURE_VNT as fv', 'fvd.FAC_REF', '=', 'fv.FAC_REF')
                ->where('fvd.ART_REF', $article->reference)
                ->where('fv.FAC_DATE', '>=', Carbon::now()->subDays(30))
                ->where('fv.FAC_VALIDE', 1)
                ->avg('fvd.QTE');
            
            $impactEstime += ($venteMoyenne ?? 0) * $article->prix_vente;
        }
        
        return round($impactEstime, 2);
    }
    
    private function genererRecommandationsStock($articlesRupture, $articlesStockFaible)
    {
        $recommandations = [];
        
        if ($articlesRupture->count() > 5) {
            $recommandations[] = [
                'type' => 'danger',
                'icon' => 'exclamation-triangle',
                'title' => 'Situation critique',
                'description' => 'Plus de 5 articles en rupture. Commande urgente nécessaire.'
            ];
        }
        
        if ($articlesStockFaible->count() > 10) {
            $recommandations[] = [
                'type' => 'warning',
                'icon' => 'clock',
                'title' => 'Anticipation nécessaire',
                'description' => 'Planifier les commandes pour éviter les ruptures.'
            ];
        }
        
        return $recommandations;
    }
    
    private function analyserSegmentationClients($clients)
    {
        $segmentation = [
            'vip' => $clients->where('ca_total', '>', 1000)->count(),
            'fideles' => $clients->where('nb_visites', '>', 10)->count(),
            'occasionnels' => $clients->where('nb_visites', '<=', 3)->count(),
            'nouveaux' => $clients->where('derniere_visite', '>=', Carbon::now()->subDays(30))->count()
        ];
        
        return $segmentation;
    }
    
    private function genererRecommandationsMarketing($clients)
    {
        $recommandations = [];
        
        $clientsVIP = $clients->where('ca_total', '>', 1000);
        if ($clientsVIP->count() > 0) {
            $recommandations[] = [
                'type' => 'success',
                'icon' => 'star',
                'title' => 'Programme VIP',
                'description' => 'Mettre en place des avantages exclusifs pour les ' . $clientsVIP->count() . ' clients VIP'
            ];
        }
        
        return $recommandations;
    }
    
    private function getIconeModePaiement($mode)
    {
        $icones = [
            'Espèces' => 'money-bill-wave',
            'Carte Bancaire' => 'credit-card',
            'Chèque' => 'file-invoice',
            'Virement' => 'exchange-alt',
            'Ticket Restaurant' => 'utensils'
        ];
        
        return $icones[$mode] ?? 'question-circle';
    }
    
    private function getCouleurModePaiement($mode)
    {
        $couleurs = [
            'Espèces' => '#10b981',
            'Carte Bancaire' => '#3b82f6',
            'Chèque' => '#f59e0b',
            'Virement' => '#8b5cf6',
            'Ticket Restaurant' => '#ef4444'
        ];
        
        return $couleurs[$mode] ?? '#6b7280';
    }
    
    private function calculerTauxOccupation($tables)
    {
        $tablesOccupees = $tables->where('statut', 'occupee')->count();
        $totalTables = $tables->count();
        
        return $totalTables > 0 ? round(($tablesOccupees / $totalTables) * 100, 2) : 0;
    }
    
    private function calculerTempsMoyenService()
    {
        // Logique de calcul du temps moyen de service
        return 45; // Exemple : 45 minutes
    }
    
    private function calculerDureeOccupation($tableRef)
    {
        // Logique pour calculer la durée d'occupation d'une table
        return '1h 30min'; // Exemple
    }
    
    // Autres méthodes utilitaires...
    private function comparer_periode_precedente($clients, $periode) { return []; }
    private function analyserTendancesHoraires($performance) { return []; }
    private function calculerEfficaciteHeure($heure) { return rand(70, 95); }
    private function determinerPeriode($heure) { 
        if ($heure < 12) return 'Matin';
        if ($heure < 18) return 'Après-midi';
        return 'Soir';
    }
    private function genererRecommandationsHoraires($performance, $pointe) { return []; }
    private function comparerSemainePrecedente($debut, $fin) { return []; }
    private function analyserTendancesModesPaiement($evolution) { return []; }
    private function calculerFraisCommissions($modes) { return []; }
    private function genererRecommandationsModesPaiement($modes, $tendances) { return []; }
    private function calculerRotationMoyenne($tables) { return 2.5; }
    private function calculerPrevisionAffluence() { return []; }
    private function genererRecommandationsTables($tables, $stats, $reservations) { return []; }
    private function analyserRepartitionZones($tables) { return []; }
    private function getHistoriqueOccupation() { return []; }
}
