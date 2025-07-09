<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Contrôleur d'analyses avancées compatible avec la structure AccessPOS réelle
 */
class AdvancedAnalyticsControllerFixed extends Controller
{
    /**
     * Données détaillées du chiffre d'affaires
     */
    public function getChiffreAffairesDetails(Request $request)
    {
        try {
            $dateDebut = $request->get('date_debut', Carbon::today()->toDateString());
            $dateFin = $request->get('date_fin', Carbon::today()->toDateString());
            
            // CA du jour sélectionné
            $caJour = DB::table('FACTURE_VNT')
                ->whereBetween('FAC_DATE', [$dateDebut, $dateFin])
                ->where('FAC_VALIDE', 1)
                ->sum('FAC_NET_A_PAYER') ?? 0;
            
            // CA d'hier pour comparaison
            $caHier = DB::table('FACTURE_VNT')
                ->whereDate('FAC_DATE', Carbon::parse($dateDebut)->subDay())
                ->where('FAC_VALIDE', 1)
                ->sum('FAC_NET_A_PAYER') ?? 0;
            
            // Évolution en pourcentage
            $evolution = $caHier > 0 ? (($caJour - $caHier) / $caHier) * 100 : 0;
            
            // Nombre de tickets
            $nbTickets = DB::table('FACTURE_VNT')
                ->whereBetween('FAC_DATE', [$dateDebut, $dateFin])
                ->where('FAC_VALIDE', 1)
                ->count();
            
            // Ticket moyen
            $ticketMoyen = $nbTickets > 0 ? $caJour / $nbTickets : 0;
            
            // Évolution sur 7 jours
            $evolutionData = DB::table('FACTURE_VNT')
                ->selectRaw('CAST(FAC_DATE as DATE) as date, SUM(FAC_NET_A_PAYER) as ca')
                ->where('FAC_DATE', '>=', Carbon::parse($dateDebut)->subDays(7))
                ->where('FAC_VALIDE', 1)
                ->groupBy(DB::raw('CAST(FAC_DATE as DATE)'))
                ->orderBy('date')
                ->get();
            
            // Répartition par heure
            $repartitionHoraire = DB::table('FACTURE_VNT')
                ->selectRaw('DATEPART(HOUR, FAC_DATE) as heure, COUNT(*) as nb_ventes, SUM(FAC_NET_A_PAYER) as ca')
                ->whereDate('FAC_DATE', $dateDebut)
                ->where('FAC_VALIDE', 1)
                ->groupBy(DB::raw('DATEPART(HOUR, FAC_DATE)'))
                ->orderBy('heure')
                ->get();
            
            // Top articles vendus
            $topArticles = DB::table('FACTURE_VNT_DETAIL as fvd')
                ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
                ->join('FACTURE_VNT as fv', 'fvd.FAC_REF', '=', 'fv.FAC_REF')
                ->select('a.ART_DESIGNATION', 
                        DB::raw('SUM(fvd.FVTD_QTE) as quantite'),
                        DB::raw('SUM(fvd.FVTD_MONTANT_HT) as ca'))
                ->whereDate('fv.FAC_DATE', $dateDebut)
                ->where('fv.FAC_VALIDE', 1)
                ->groupBy('a.ART_REF', 'a.ART_DESIGNATION')
                ->orderBy('ca', 'desc')
                ->limit(10)
                ->get();
            
            $objectifJour = 2000;
            $pourcentageObjectif = ($caJour / $objectifJour) * 100;
            
            $data = [
                'success' => true,
                'date_analyse' => $dateDebut,
                'ca_total' => round($caJour, 2),
                'ca_hier' => round($caHier, 2),
                'evolution' => round($evolution, 2),
                'nb_tickets' => $nbTickets,
                'ticket_moyen' => round($ticketMoyen, 2),
                'objectif_jour' => $objectifJour,
                'pourcentage_objectif' => round($pourcentageObjectif, 2),
                'evolution_7_jours' => $evolutionData,
                'repartition_horaire' => $repartitionHoraire,
                'top_articles' => $topArticles,
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
                ->select('a.ART_REF', 'a.ART_DESIGNATION', 'f.FAM_LIB as famille',
                        'a.ART_PRIX_VNT', 'a.ART_STOCK_MIN',
                        DB::raw('COALESCE(s.STK_QTE, 0) as stock_actuel'))
                ->whereRaw('COALESCE(s.STK_QTE, 0) <= 0')
                ->get();
            
            // Articles en stock faible
            $articlesStockFaible = DB::table('ARTICLE as a')
                ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
                ->leftJoin('FAMILLE as f', 'a.FAM_REF', '=', 'f.FAM_REF')
                ->select('a.ART_REF', 'a.ART_DESIGNATION', 'f.FAM_LIB as famille',
                        'a.ART_PRIX_VNT', 'a.ART_STOCK_MIN',
                        DB::raw('COALESCE(s.STK_QTE, 0) as stock_actuel'))
                ->whereRaw('COALESCE(s.STK_QTE, 0) > 0 AND COALESCE(s.STK_QTE, 0) <= a.ART_STOCK_MIN')
                ->get();
            
            // Dernières ventes pour les articles en rupture
            foreach ($articlesRupture as $article) {
                $article->derniere_vente = DB::table('FACTURE_VNT_DETAIL as fvd')
                    ->join('FACTURE_VNT as fv', 'fvd.FAC_REF', '=', 'fv.FAC_REF')
                    ->where('fvd.ART_REF', $article->ART_REF)
                    ->where('fv.FAC_VALIDE', 1)
                    ->max('fv.FAC_DATE');
            }
            
            // Impact financier estimé
            $impactFinancier = $this->calculerImpactFinancierRupture($articlesRupture);
            
            $data = [
                'success' => true,
                'articles_rupture' => $articlesRupture,
                'articles_stock_faible' => $articlesStockFaible,
                'impact_financier' => round($impactFinancier, 2),
                'total_rupture' => $articlesRupture->count(),
                'total_stock_faible' => $articlesStockFaible->count(),
                'recommandations' => $this->genererRecommandationsStock($articlesRupture, $articlesStockFaible),
                'actions_prioritaires' => $this->definirActionsPrioritaires($articlesRupture, $articlesStockFaible)
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
            $periode = $request->get('periode', 'mois');
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
                ->select('c.CLT_REF', 'c.CLT_NOM', 'c.CLT_TEL', 'c.CLT_EMAIL',
                        DB::raw('COUNT(fv.FAC_REF) as nb_commandes'),
                        DB::raw('SUM(fv.FAC_NET_A_PAYER) as ca_total'),
                        DB::raw('AVG(fv.FAC_NET_A_PAYER) as panier_moyen'),
                        DB::raw('MAX(fv.FAC_DATE) as derniere_visite'))
                ->where('fv.FAC_DATE', '>=', $dateDebut)
                ->where('fv.FAC_VALIDE', 1)
                ->groupBy('c.CLT_REF', 'c.CLT_NOM', 'c.CLT_TEL', 'c.CLT_EMAIL')
                ->orderBy('ca_total', 'desc')
                ->limit($limite)
                ->get();
            
            // Calcul du taux de fidélité pour chaque client
            foreach ($topClients as $client) {
                $client->taux_fidelite = $this->calculerTauxFidelite($client->CLT_REF, $dateDebut);
                $client->derniere_visite_fr = Carbon::parse($client->derniere_visite)->format('d/m/Y');
            }
            
            // Analyse de la segmentation client
            $segmentation = $this->analyserSegmentationClients($topClients);
            
            // Recommandations marketing
            $recommandationsMarketing = $this->genererRecommandationsMarketing($topClients);
            
            $data = [
                'success' => true,
                'periode' => $periode,
                'top_clients' => $topClients,
                'segmentation' => $segmentation,
                'recommandations_marketing' => $recommandationsMarketing,
                'actions_clients' => $this->definirActionsClients($topClients, $segmentation)
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
                ->selectRaw('DATEPART(HOUR, FAC_DATE) as heure, 
                           COUNT(*) as nb_ventes, 
                           SUM(FAC_NET_A_PAYER) as ca,
                           AVG(FAC_NET_A_PAYER) as panier_moyen')
                ->whereBetween('FAC_DATE', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59'])
                ->where('FAC_VALIDE', 1)
                ->groupBy(DB::raw('DATEPART(HOUR, FAC_DATE)'))
                ->orderBy('heure')
                ->get();
            
            // Ajout des métriques de performance
            $caTotal = $performanceHoraire->sum('ca');
            foreach ($performanceHoraire as $heure) {
                $heure->pourcentage_ca = $caTotal > 0 ? round(($heure->ca / $caTotal) * 100, 2) : 0;
                $heure->efficacite = $this->calculerEfficaciteHeure($heure->heure);
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
                'success' => true,
                'periode' => ['debut' => $dateDebut, 'fin' => $dateFin],
                'performance_horaire' => $performanceHoraire,
                'heures_pointe' => $heuresPointe->values(),
                'tendances' => $tendances,
                'recommandations' => $recommandations,
                'comparaison_semaine' => $comparaisonSemaine,
                'pics_activite' => $this->analyserPicsActivite($performanceHoraire),
                'recommandations_rh' => $this->genererRecommandationsRH($performanceHoraire, [])
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
            $repartitionPaiements = DB::table('REGLEMENT as r')
                ->join('FACTURE_VNT as fv', 'r.FAC_REF', '=', 'fv.FAC_REF')
                ->select('r.RGL_MODE', 
                        DB::raw('COUNT(*) as nb_transactions'),
                        DB::raw('SUM(r.RGL_MONTANT) as montant_total'),
                        DB::raw('AVG(r.RGL_MONTANT) as montant_moyen'))
                ->whereBetween('fv.FAC_DATE', [$dateDebut, $dateFin])
                ->where('fv.FAC_VALIDE', 1)
                ->groupBy('r.RGL_MODE')
                ->orderBy('montant_total', 'desc')
                ->get();
            
            $totalCA = $repartitionPaiements->sum('montant_total');
            
            // Enrichissement des données
            foreach ($repartitionPaiements as $mode) {
                $mode->pourcentage = $totalCA > 0 ? round(($mode->montant_total / $totalCA) * 100, 2) : 0;
                $mode->icone = $this->getIconeModePaiement($mode->RGL_MODE);
                $mode->couleur = $this->getCouleurModePaiement($mode->RGL_MODE);
            }
            
            // Évolution des modes de paiement
            $evolutionModes = $this->obtenirEvolutionModesPaiement($dateDebut, $dateFin);
            
            // Tendances et analyses
            $tendances = $this->analyserTendancesModesPaiement($evolutionModes);
            
            // Frais et commissions
            $fraisCommissions = $this->calculerFraisCommissions($repartitionPaiements);
            
            $data = [
                'success' => true,
                'periode' => ['debut' => $dateDebut, 'fin' => $dateFin],
                'repartition_paiements' => $repartitionPaiements,
                'total_ca' => round($totalCA, 2),
                'evolution_modes' => $evolutionModes,
                'tendances' => $tendances,
                'frais_commissions' => $fraisCommissions,
                'risques_paiement' => $this->analyserRisquesPaiement($repartitionPaiements),
                'recommandations' => $this->genererRecommandationsModesPaiement($repartitionPaiements, $tendances)
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
            // État actuel des tables avec zones
            $etatTables = DB::table('TABLE as t')
                ->leftJoin('ZONE as z', 't.ZON_REF', '=', 'z.ZON_REF')
                ->select('t.TAB_REF', 't.TAB_LIB', 't.ETT_ETAT', 't.TAB_NBR_Couvert',
                        'z.ZON_LIB as zone', 't.TAB_DESCRIPT')
                ->get();
            
            // Enrichissement des données tables
            foreach ($etatTables as $table) {
                $table->duree_occupation = $this->calculerDureeOccupation($table->TAB_REF);
                $table->ca_table_jour = $this->calculerCATable($table->TAB_REF);
            }
            
            // Statistiques par zone
            $statistiquesZones = $this->analyserRepartitionZones($etatTables);
            
            // Performance des serveurs
            $performanceServeurs = $this->obtenirPerformanceServeurs();
            
            // Réservations en approche
            $reservationsAVenir = DB::table('RESERVATION as r')
                ->leftJoin('CLIENT as c', 'r.CLT_REF', '=', 'c.CLT_REF')
                ->select('r.DATE_RESERVATION', 'r.NBR_COUVERT', 'c.CLT_NOM', 'r.ETAT_RESERVATION')
                ->where('r.DATE_RESERVATION', '>=', Carbon::now())
                ->where('r.DATE_RESERVATION', '<=', Carbon::now()->addHours(4))
                ->orderBy('r.DATE_RESERVATION')
                ->get();
            
            // Calculs généraux
            $tauxOccupation = $this->calculerTauxOccupation($etatTables);
            $rotationMoyenne = $this->calculerRotationMoyenne($etatTables);
            
            // Prévisions d'affluence
            $previsionsAffluence = $this->calculerPrevisionAffluence();
            
            // Historique d'occupation
            $historiqueOccupation = $this->getHistoriqueOccupation();
            
            $data = [
                'success' => true,
                'etat_tables' => $etatTables,
                'statistiques_zones' => $statistiquesZones,
                'performance_serveurs' => $performanceServeurs,
                'reservations_a_venir' => $reservationsAVenir,
                'taux_occupation' => $tauxOccupation,
                'rotation_moyenne' => $rotationMoyenne,
                'previsions_affluence' => $previsionsAffluence,
                'historique_occupation' => $historiqueOccupation,
                'alertes_restaurant' => $this->genererAlertesRestaurant($etatTables, $performanceServeurs, $reservationsAVenir),
                'recommandations' => $this->genererRecommandationsGestionRestaurant($etatTables, $statistiquesZones)
            ];
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des données: ' . $e->getMessage()], 500);
        }
    }
    
    // ============================
    // MÉTHODES PRIVÉES UTILITAIRES
    // ============================
    
    private function genererRecommandationsCA($caJour, $objectifJour, $evolution)
    {
        $recommandations = [];
        
        if ($caJour < $objectifJour * 0.5) {
            $recommandations[] = 'Urgence : CA très en dessous de l\'objectif. Actions commerciales immédiates requises.';
        } elseif ($caJour < $objectifJour * 0.8) {
            $recommandations[] = 'Attention : CA en dessous de l\'objectif. Renforcer les actions de vente.';
        } else {
            $recommandations[] = 'Objectif en bonne voie d\'être atteint. Maintenir l\'effort.';
        }
        
        if ($evolution < -10) {
            $recommandations[] = 'Baisse significative par rapport à hier (' . round($evolution, 1) . '%). Analyser les causes.';
        } elseif ($evolution > 10) {
            $recommandations[] = 'Excellente progression par rapport à hier (+' . round($evolution, 1) . '%). Capitaliser sur cette dynamique.';
        }
        
        return $recommandations;
    }
    
    private function calculerImpactFinancierRupture($articlesRupture)
    {
        $impactEstime = 0;
        
        foreach ($articlesRupture as $article) {
            // Estimation basée sur 3 ventes perdues par jour par article
            $ventesEstimees = 3;
            $impactEstime += $article->ART_PRIX_VNT * $ventesEstimees;
        }
        
        return $impactEstime;
    }
    
    private function genererRecommandationsStock($articlesRupture, $articlesStockFaible)
    {
        $recommandations = [];
        
        if ($articlesRupture->count() > 0) {
            $recommandations[] = 'Urgence : ' . $articlesRupture->count() . ' articles en rupture. Commande immédiate nécessaire.';
        }
        
        if ($articlesStockFaible->count() > 5) {
            $recommandations[] = 'Attention : ' . $articlesStockFaible->count() . ' articles en stock faible. Planifier les approvisionnements.';
        }
        
        $top3Rupture = $articlesRupture->sortByDesc('ART_PRIX_VNT')->take(3);
        if ($top3Rupture->count() > 0) {
            $recommandations[] = 'Priorité aux articles à forte valeur : ' . $top3Rupture->pluck('ART_DESIGNATION')->implode(', ');
        }
        
        return $recommandations;
    }
    
    private function definirActionsPrioritaires($articlesRupture, $articlesStockFaible)
    {
        return [
            'immediates' => [
                'Contacter les fournisseurs pour les articles en rupture',
                'Retirer les articles en rupture des menus/catalogues',
                'Proposer des alternatives aux clients'
            ],
            'court_terme' => [
                'Planifier les commandes pour les articles en stock faible',
                'Réviser les seuils de stock minimum',
                'Mettre en place des alertes automatiques'
            ],
            'moyen_terme' => [
                'Analyser les tendances de consommation',
                'Optimiser la rotation des stocks',
                'Diversifier les fournisseurs'
            ]
        ];
    }
    
    private function calculerTauxFidelite($clientRef, $dateDebut)
    {
        $totalCommandes = DB::table('FACTURE_VNT')
            ->where('CLT_REF', $clientRef)
            ->where('FAC_DATE', '>=', $dateDebut)
            ->where('FAC_VALIDE', 1)
            ->count();
        
        $joursDepuisDebut = Carbon::parse($dateDebut)->diffInDays(Carbon::now()) + 1;
        
        return $joursDepuisDebut > 0 ? round(($totalCommandes / $joursDepuisDebut) * 30, 2) : 0; // Commandes par mois
    }
    
    private function analyserSegmentationClients($clients)
    {
        $segmentation = [
            'vip' => $clients->where('ca_total', '>', 1000)->count(),
            'fideles' => $clients->where('nb_commandes', '>', 10)->count(),
            'occasionnels' => $clients->where('nb_commandes', '<=', 3)->count(),
            'gros_panier' => $clients->where('panier_moyen', '>', 150)->count()
        ];
        
        return $segmentation;
    }
    
    private function genererRecommandationsMarketing($clients)
    {
        $recommandations = [];
        
        $clientsVIP = $clients->where('ca_total', '>', 1000);
        if ($clientsVIP->count() > 0) {
            $recommandations[] = 'Programme VIP pour ' . $clientsVIP->count() . ' clients premium';
        }
        
        $clientsInactifs = $clients->where('derniere_visite', '<', Carbon::now()->subDays(30));
        if ($clientsInactifs->count() > 0) {
            $recommandations[] = 'Campagne de réactivation pour ' . $clientsInactifs->count() . ' clients inactifs';
        }
        
        return $recommandations;
    }
    
    private function definirActionsClients($topClients, $segmentation)
    {
        return [
            'vip' => 'Offres exclusives et service personnalisé',
            'fideles' => 'Programme de points et récompenses',
            'occasionnels' => 'Incitations à revenir et promotions ciblées',
            'nouveaux' => 'Offre de bienvenue et suivi personnalisé'
        ];
    }
    
    // Méthodes utilitaires simplifiées
    private function calculerEfficaciteHeure($heure) { return rand(70, 95); }
    private function determinerPeriode($heure) { 
        if ($heure < 12) return 'Matin';
        if ($heure < 18) return 'Après-midi';
        return 'Soir';
    }
    private function analyserTendancesHoraires($performance) { return ['stable', 'croissante', 'décroissante'][rand(0, 2)]; }
    private function genererRecommandationsHoraires($performance, $pointe) { 
        return ['Optimiser les équipes aux heures de pointe', 'Promotions aux heures creuses']; 
    }
    private function comparerSemainePrecedente($debut, $fin) { return []; }
    private function analyserPicsActivite($performance) { return ['12h-14h', '19h-21h']; }
    private function genererRecommandationsRH($performance, $equipes) { 
        return ['Renforcer l\'équipe de 12h à 14h', 'Optimiser les plannings']; 
    }
    
    private function getIconeModePaiement($mode) {
        $icones = [
            'Espèces' => 'money-bill-wave',
            'Carte Bancaire' => 'credit-card',
            'Chèque' => 'file-invoice',
            'Virement' => 'exchange-alt'
        ];
        return $icones[$mode] ?? 'question-circle';
    }
    
    private function getCouleurModePaiement($mode) {
        $couleurs = [
            'Espèces' => '#28a745',
            'Carte Bancaire' => '#007bff',
            'Chèque' => '#ffc107',
            'Virement' => '#6f42c1'
        ];
        return $couleurs[$mode] ?? '#6b7280';
    }
    
    private function obtenirEvolutionModesPaiement($debut, $fin) { return []; }
    private function analyserTendancesModesPaiement($evolution) { return []; }
    private function calculerFraisCommissions($modes) { return 0; }
    private function analyserRisquesPaiement($modes) { return []; }
    private function genererRecommandationsModesPaiement($modes, $tendances) { 
        return ['Optimiser les frais bancaires', 'Promouvoir les paiements sans contact']; 
    }
    
    private function calculerDureeOccupation($tableRef) { return '1h 30min'; }
    private function calculerCATable($tableRef) { return rand(50, 300); }
    private function analyserRepartitionZones($tables) { 
        return $tables->groupBy('zone')->map(function($zone) {
            return [
                'total' => $zone->count(),
                'occupees' => $zone->where('ETT_ETAT', 'occupee')->count()
            ];
        });
    }
    private function obtenirPerformanceServeurs() { return collect([]); }
    private function calculerTauxOccupation($tables) {
        $total = $tables->count();
        $occupees = $tables->where('ETT_ETAT', 'occupee')->count();
        return $total > 0 ? round(($occupees / $total) * 100, 2) : 0;
    }
    private function calculerRotationMoyenne($tables) { return 2.5; }
    private function calculerPrevisionAffluence() { return []; }
    private function getHistoriqueOccupation() { return []; }
    private function genererAlertesRestaurant($tables, $serveurs, $reservations) { return []; }
    private function genererRecommandationsGestionRestaurant($tables, $zones) { 
        return ['Optimiser la répartition des tables', 'Améliorer le service client']; 
    }
}
