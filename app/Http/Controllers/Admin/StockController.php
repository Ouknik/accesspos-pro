<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockController extends Controller
{
    /**
     * Tableau de bord du stock - Aperçu général
     */
    public function dashboard()
    {
        // Statistiques générales du stock
        $stats = $this->getStockStatistics();
        
        // Derniers mouvements de stock
        $recentMouvements = $this->getRecentMouvements(10);
        
        // Alertes de stock
        $alertes = $this->getStockAlertes();
        
        // Articles les plus vendus ce mois
        $topArticles = $this->getTopArticlesByMonth();
        
        // Évolution du stock par famille
        $stockParFamille = $this->getStockParFamille();
        
        return view('admin.stock.dashboard', compact(
            'stats', 
            'recentMouvements', 
            'alertes', 
            'topArticles', 
            'stockParFamille'
        ));
    }

    /**
     * Gestion de l'inventaire actuel
     */
    public function inventaire(Request $request)
    {
        // Récupérer l'entrepôt par défaut
        $entrepot = DB::table('ENTREPOT')->first();
        if (!$entrepot) {
            // إنشاء إنتريبو افتراضي إذا لم يكن موجوداً
            $entrepotRef = 'ETP001';
            DB::table('ENTREPOT')->insert([
                'ETP_REF' => $entrepotRef,
                'ETP_LIBELLE' => 'Entrepôt Principal',
                'ETP_PAYS' => 'Maroc',
                'ETP_VILLE' => 'Casablanca',
                'ETP_ADRESS' => 'Adresse principale',
                'ETP_TELEPHONE' => '',
                'ETP_EMAIL' => '',
                'ETP_DESCRIPTION' => 'Entrepôt principal créé automatiquement'
            ]);
            $entrepot = DB::table('ENTREPOT')->where('ETP_REF', $entrepotRef)->first();
        }

        $query = DB::table('STOCK as s')
            ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
            ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
            ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
            ->leftJoin('UNITE_MESURE as um', 'a.UNM_ABR', '=', 'um.UNM_ABR')
            ->select([
                'a.ART_REF',
                'a.ART_DESIGNATION',
                'a.ART_CODEBARR',
                's.STK_QTE as quantite_stock',
                'a.ART_STOCK_MIN as stock_minimum',
                'a.ART_STOCK_MAX as stock_maximum',
                'a.ART_PRIX_ACHAT as prix_achat',
                'a.ART_PRIX_VENTE as prix_vente',
                'um.UNM_ABR as unite_mesure',
                'f.FAM_LIB as famille',
                'sf.SFM_LIB as sous_famille',
                DB::raw('(s.STK_QTE * a.ART_PRIX_ACHAT) as valeur_stock'),
                DB::raw('CASE 
                    WHEN s.STK_QTE = 0 THEN \'rupture\'
                    WHEN s.STK_QTE <= a.ART_STOCK_MIN THEN \'alerte\'
                    WHEN s.STK_QTE >= a.ART_STOCK_MAX THEN \'surplus\'
                    ELSE \'normal\'
                END as statut_stock')
            ])
            ->where('a.ART_STOCKABLE', 1)
            ->where('s.ETP_REF', $entrepot->ETP_REF);

        // Filtres
        if ($request->filled('famille')) {
            $query->where('f.FAM_REF', $request->famille);
        }

        if ($request->filled('statut')) {
            switch ($request->statut) {
                case 'rupture':
                    $query->where('s.STK_QTE', 0);
                    break;
                case 'alerte':
                    $query->whereRaw('s.STK_QTE > 0 AND s.STK_QTE <= a.ART_STOCK_MIN');
                    break;
                case 'surplus':
                    $query->whereRaw('s.STK_QTE >= a.ART_STOCK_MAX');
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('a.ART_DESIGNATION', 'LIKE', "%{$search}%")
                  ->orWhere('a.ART_CODEBARR', 'LIKE', "%{$search}%")
                  ->orWhere('a.ART_REF', 'LIKE', "%{$search}%");
            });
        }

        $inventaire = $query->orderBy('f.FAM_LIB')
                           ->orderBy('a.ART_DESIGNATION')
                           ->paginate(50);

        // Liste des familles pour le filtre
        $familles = DB::table('FAMILLE')
            ->orderBy('FAM_LIB')
            ->get(['FAM_REF', 'FAM_LIB']);

        return view('admin.stock.inventaire', compact('inventaire', 'familles'));
    }

    /**
     * Mouvements de stock
     */
    public function mouvements(Request $request)
    {
        // Construction de l'historique des mouvements depuis les différentes tables
        $query = $this->buildMouvementsQuery();

        // Filtres
        if ($request->filled('date_debut')) {
            $query->whereDate('date_mouvement', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_mouvement', '<=', $request->date_fin);
        }

        if ($request->filled('type_mouvement')) {
            $query->where('type_mouvement', $request->type_mouvement);
        }

        if ($request->filled('article')) {
            $query->where('article_ref', 'LIKE', "%{$request->article}%");
        }

        $mouvements = $query->orderBy('date_mouvement', 'desc')
                           ->paginate(50);

        // Statistiques des mouvements
        $statsMouvements = $this->getStatsMouvements($request);

        return view('admin.stock.mouvements', compact('mouvements', 'statsMouvements'));
    }

    /**
     * Gestion des achats et approvisionnement
     */
    public function achats(Request $request)
    {
        $query = DB::table('FACTURE_FOURNISSEUR as ff')
            ->leftJoin('FOURNISSEUR as f', 'ff.FRS_REF', '=', 'f.FRS_REF')
            ->select([
                'ff.FCF_REF',
                'ff.FCF_NUMERO as numero',
                'ff.FCF_DATE',
                'f.FRS_RAISONSOCIAL as fournisseur',
                'ff.FCF_MONTANT_HT_ as montant_ht',
                'ff.FCF_MONTANT_TTC as montant_ttc',
                'ff.FCF_VALIDE as valide',
                'ff.FCF_REMARQUE as remarque'
            ]);

        // Filtres
        if ($request->filled('fournisseur')) {
            $query->where('ff.FRS_REF', $request->fournisseur);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('ff.FCF_DATE', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('ff.FCF_DATE', '<=', $request->date_fin);
        }

        if ($request->filled('statut')) {
            $query->where('ff.FCF_VALIDE', $request->statut);
        }

        $achats = $query->orderBy('ff.FCF_DATE', 'desc')
                       ->paginate(30);

        // Liste des fournisseurs pour le filtre
        $fournisseurs = DB::table('FOURNISSEUR')
            ->select('FRS_REF', 'FRS_RAISONSOCIAL')
            ->orderBy('FRS_RAISONSOCIAL')
            ->get();

        // Statistiques des achats
        $statsAchats = $this->getStatsAchats($request);

        return view('admin.stock.achats', compact('achats', 'fournisseurs', 'statsAchats'));
    }

    /**
     * Réception de marchandises
     */
    public function reception()
    {
        try {
            // Récupérer l'entrepôt par défaut
            $entrepotDefaut = DB::table('ENTREPOT')->first();
            $etpRef = $entrepotDefaut ? $entrepotDefaut->ETP_REF : 'ETP001';

            // Bons de livraison en attente de réception
            $blEnAttente = DB::table('BL_FOURNISSEUR as blf')
                ->leftJoin('FOURNISSEUR as f', 'blf.FRS_REF', '=', 'f.FRS_REF')
                ->leftJoin(DB::raw('(SELECT BLF_REF, COUNT(*) as nb_articles FROM BLFOURNISSEUR_DETAIL GROUP BY BLF_REF) as details'), 'blf.BLF_REF', '=', 'details.BLF_REF')
                ->select([
                    'blf.*',
                    'f.FRS_RAISONSOCIAL as fournisseur',
                    'details.nb_articles'
                ])
                ->where('blf.ETP_REF', $etpRef)
                ->where('blf.BLF_VALIDE', 0) // En attente
                ->orderBy('blf.BLF_DATE', 'desc')
                ->get();

            // Réceptions du jour
            $today = Carbon::today();
            $receptionsJour = DB::table('BL_FOURNISSEUR')
                ->where('ETP_REF', $etpRef)
                ->where('BLF_VALIDE', 1)
                ->whereDate('BLF_DATE', $today)
                ->get();

            // Articles reçus aujourd'hui
            $articlesRecus = DB::table('BL_FOURNISSEUR as blf')
                ->join('BLFOURNISSEUR_DETAIL as bld', 'blf.BLF_REF', '=', 'bld.BLF_REF')
                ->where('blf.ETP_REF', $etpRef)
                ->where('blf.BLF_VALIDE', 1)
                ->whereDate('blf.BLF_DATE', $today)
                ->sum('bld.ABF_QTE_RECUS');

            // Valeur réceptionnée aujourd'hui
            $valeurReceptionnee = DB::table('BL_FOURNISSEUR')
                ->where('ETP_REF', $etpRef)
                ->where('BLF_VALIDE', 1)
                ->whereDate('BLF_DATE', $today)
                ->sum('BLF_MONTANT_TTC');

            // Réceptions récentes (7 derniers jours)
            $receptionsRecentes = DB::table('BL_FOURNISSEUR as blf')
                ->leftJoin('FOURNISSEUR as f', 'blf.FRS_REF', '=', 'f.FRS_REF')
                ->leftJoin(DB::raw('(SELECT BLF_REF, COUNT(*) as nb_articles FROM BLFOURNISSEUR_DETAIL GROUP BY BLF_REF) as details'), 'blf.BLF_REF', '=', 'details.BLF_REF')
                ->select([
                    'blf.*',
                    'f.FRS_RAISONSOCIAL as fournisseur',
                    'details.nb_articles'
                ])
                ->where('blf.ETP_REF', $etpRef)
                ->where('blf.BLF_VALIDE', 1)
                ->where('blf.BLF_DATE', '>=', Carbon::now()->subDays(7))
                ->orderBy('blf.BLF_DATE', 'desc')
                ->limit(10)
                ->get();

            return view('admin.stock.reception', compact(
                'blEnAttente',
                'receptionsJour',
                'articlesRecus',
                'valeurReceptionnee',
                'receptionsRecentes'
            ));

        } catch (\Exception $e) {
            \Log::error('Erreur page réception: ' . $e->getMessage());
            
            return view('admin.stock.reception', [
                'blEnAttente' => collect(),
                'receptionsJour' => collect(),
                'articlesRecus' => 0,
                'valeurReceptionnee' => 0,
                'receptionsRecentes' => collect()
            ]);
        }
    }

    /**
     * Rapports de stock
     */
    public function rapports()
    {
        // Rapport de valorisation du stock
        $valorisationStock = $this->getValorisationStock();
        
        // Articles en rupture
        $articlesRupture = $this->getArticlesRupture();
        
        // Articles en alerte
        $articlesAlerte = $this->getArticlesAlerte();
        
        // Top 10 des articles les plus vendus
        $topVentes = $this->getTopVentesArticles();
        
        // Articles à rotation lente
        $rotationLente = $this->getArticlesRotationLente();

        return view('admin.stock.rapports', compact(
            'valorisationStock',
            'articlesRupture', 
            'articlesAlerte',
            'topVentes',
            'rotationLente'
        ));
    }

    /**
     * Alertes de stock
     */
    public function alertes()
    {
        // الحصول على الإنتريبو الافتراضي
        $entrepot = DB::table('ENTREPOT')->first();
        if (!$entrepot) {
            // إنشاء إنتريبو افتراضي إذا لم يكن موجوداً
            $entrepotRef = 'ETP001';
            try {
                DB::table('ENTREPOT')->insert([
                    'ETP_REF' => $entrepotRef,
                    'ETP_LIBELLE' => 'Entrepôt Principal',
                    'ETP_PAYS' => 'Maroc',
                    'ETP_VILLE' => 'Casablanca',
                    'ETP_ADRESS' => 'Adresse principale',
                    'ETP_TELEPHONE' => '',
                    'ETP_EMAIL' => '',
                    'ETP_DESCRIPTION' => 'Entrepôt principal créé automatiquement'
                ]);
                $entrepot = DB::table('ENTREPOT')->where('ETP_REF', $entrepotRef)->first();
            } catch (\Exception $e) {
                $alertes = collect();
                return view('admin.stock.alertes', compact('alertes'));
            }
        }

        $alertes = collect();

        // Articles en rupture de stock
        $ruptureStock = DB::table('STOCK as s')
            ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
            ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
            ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
            ->where('s.STK_QTE', 0)
            ->where('s.ETP_REF', $entrepot->ETP_REF)
            ->where('a.ART_STOCKABLE', 1)
            ->select([
                'a.ART_REF',
                'a.ART_DESIGNATION',
                's.STK_QTE',
                'a.ART_STOCK_MIN',
                'a.ART_STOCK_MAX',
                DB::raw("'rupture' as type_alerte"),
                DB::raw("'Article en rupture de stock' as message")
            ])
            ->get();

        // Articles en stock minimum
        $stockMinimum = DB::table('STOCK as s')
            ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
            ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
            ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
            ->whereRaw('s.STK_QTE > 0 AND s.STK_QTE <= a.ART_STOCK_MIN')
            ->where('s.ETP_REF', $entrepot->ETP_REF)
            ->where('a.ART_STOCKABLE', 1)
            ->select([
                'a.ART_REF',
                'a.ART_DESIGNATION',
                's.STK_QTE',
                'a.ART_STOCK_MIN',
                'a.ART_STOCK_MAX',
                DB::raw("'minimum' as type_alerte"),
                DB::raw("'Stock en dessous du seuil minimum' as message")
            ])
            ->get();

        $alertes = $ruptureStock->merge($stockMinimum);

        return view('admin.stock.alertes', compact('alertes'));
    }

    /**
     * Méthodes utilitaires
     */
    private function getStockStatistics()
    {
        return [
            'total_articles' => DB::table('ARTICLE')->where('ART_STOCKABLE', 1)->count(),
            'valeur_totale_stock' => DB::table('STOCK as s')
                ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                ->sum(DB::raw('s.STK_QTE * a.ART_PRIX_ACHAT')),
            'articles_rupture' => DB::table('STOCK as s')
                ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                ->where('s.STK_QTE', 0)
                ->where('a.ART_STOCKABLE', 1)
                ->count(),
            'articles_alerte' => DB::table('STOCK as s')
                ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                ->whereRaw('s.STK_QTE > 0 AND s.STK_QTE <= a.ART_STOCK_MIN')
                ->where('a.ART_STOCKABLE', 1)
                ->count(),
            'articles_surplus' => DB::table('STOCK as s')
                ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                ->whereRaw('s.STK_QTE >= a.ART_STOCK_MAX')
                ->where('a.ART_STOCKABLE', 1)
                ->count()
        ];
    }

    private function getRecentMouvements($limit = 10)
    {
        return $this->buildMouvementsQuery()
            ->limit($limit)
            ->orderBy('date_mouvement', 'desc')
            ->get();
    }

    private function buildMouvementsQuery()
    {
        // Union des différents types de mouvements
        $entrees = DB::table('FACTURE_FRS_DETAIL as ffd')
            ->join('FACTURE_FOURNISSEUR as ff', 'ffd.FCF_REF', '=', 'ff.FCF_REF')
            ->join('ARTICLE as a', 'ffd.ART_REF', '=', 'a.ART_REF')
            ->select([
                'ffd.ART_REF as article_ref',
                'a.ART_DESIGNATION as article_designation',
                'ffd.FCF_QTE as quantite',
                'ff.FCF_DATE as date_mouvement',
                DB::raw("'ENTREE' as type_mouvement"),
                DB::raw("'Achat fournisseur' as libelle_mouvement"),
                'ff.FCF_REF as reference_document'
            ])
            ->where('ff.FCF_VALIDE', 1);

        $sorties = DB::table('FACTURE_VNT_DETAIL as fvd')
            ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
            ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
            ->select([
                'fvd.ART_REF as article_ref',
                'a.ART_DESIGNATION as article_designation',
                DB::raw('-fvd.FVD_QTE as quantite'),
                'fv.FCTV_DATE as date_mouvement',
                DB::raw("'SORTIE' as type_mouvement"),
                DB::raw("'Vente client' as libelle_mouvement"),
                'fv.FCTV_REF as reference_document'
            ])
            ->where('fv.FCTV_VALIDE', 1);

        return $entrees->union($sorties);
    }

    private function getStockAlertes()
    {
        // Récupérer l'entrepôt par défaut
        $entrepot = DB::table('ENTREPOT')->first();
        if (!$entrepot) {
            // إنشاء إنتريبو افتراضي إذا لم يكن موجوداً
            $entrepotRef = 'ETP001';
            try {
                DB::table('ENTREPOT')->insert([
                    'ETP_REF' => $entrepotRef,
                    'ETP_LIBELLE' => 'Entrepôt Principal',
                    'ETP_PAYS' => 'Maroc',
                    'ETP_VILLE' => 'Casablanca',
                    'ETP_ADRESS' => 'Adresse principale',
                    'ETP_TELEPHONE' => '',
                    'ETP_EMAIL' => '',
                    'ETP_DESCRIPTION' => 'Entrepôt principal créé automatiquement'
                ]);
                $entrepot = DB::table('ENTREPOT')->where('ETP_REF', $entrepotRef)->first();
            } catch (\Exception $e) {
                return collect(); // Retourner une collection vide en cas d'erreur
            }
        }

        $alertes = collect();

        // Articles en rupture de stock
        $ruptureStock = DB::table('STOCK as s')
            ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
            ->where('s.STK_QTE', 0)
            ->where('s.ETP_REF', $entrepot->ETP_REF)
            ->where('a.ART_STOCKABLE', 1)
            ->select([
                'a.ART_REF',
                'a.ART_DESIGNATION',
                's.STK_QTE',
                'a.ART_STOCK_MIN',
                'a.ART_STOCK_MAX',
                DB::raw("'rupture' as type_alerte"),
                DB::raw("'Article en rupture de stock' as message")
            ])
            ->limit(5)
            ->get();

        // Articles en stock minimum
        $stockMinimum = DB::table('STOCK as s')
            ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
            ->whereRaw('s.STK_QTE > 0 AND s.STK_QTE <= a.ART_STOCK_MIN')
            ->where('s.ETP_REF', $entrepot->ETP_REF)
            ->where('a.ART_STOCKABLE', 1)
            ->select([
                'a.ART_REF',
                'a.ART_DESIGNATION',
                's.STK_QTE',
                'a.ART_STOCK_MIN',
                'a.ART_STOCK_MAX',
                DB::raw("'minimum' as type_alerte"),
                DB::raw("'Stock en dessous du seuil minimum' as message")
            ])
            ->limit(5)
            ->get();

        return $ruptureStock->merge($stockMinimum);
    }

    private function getValorisationStock()
    {
        return [
            'total' => DB::table('STOCK as s')
                ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                ->sum(DB::raw('s.STK_QTE * a.ART_PRIX_ACHAT')),
            'nombre_articles' => DB::table('STOCK as s')
                ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                ->where('a.ART_STOCKABLE', 1)
                ->count(),
            'valeur_moyenne' => DB::table('STOCK as s')
                ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                ->where('s.STK_QTE', '>', 0)
                ->avg(DB::raw('s.STK_QTE * a.ART_PRIX_ACHAT')),
            'familles_actives' => DB::table('STOCK as s')
                ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
                ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
                ->where('s.STK_QTE', '>', 0)
                ->distinct()
                ->count('f.FAM_REF')
        ];
    }

    private function getArticlesRupture()
    {
        return DB::table('STOCK as s')
            ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
            ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
            ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
            ->where('s.STK_QTE', 0)
            ->where('a.ART_STOCKABLE', 1)
            ->select([
                'a.ART_REF',
                'a.ART_DESIGNATION',
                'a.ART_PRIX_ACHAT',
                'a.ART_STOCK_MIN',
                'f.FAM_LIB as famille'
            ])
            ->orderBy('a.ART_PRIX_ACHAT', 'desc')
            ->get();
    }

    private function getArticlesAlerte()
    {
        return DB::table('STOCK as s')
            ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
            ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
            ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
            ->whereRaw('s.STK_QTE > 0 AND s.STK_QTE <= a.ART_STOCK_MIN')
            ->where('a.ART_STOCKABLE', 1)
            ->select([
                'a.ART_REF',
                'a.ART_DESIGNATION',
                's.STK_QTE',
                'a.ART_STOCK_MIN',
                'a.ART_STOCK_MAX',
                'f.FAM_LIB as famille'
            ])
            ->orderBy('s.STK_QTE', 'asc')
            ->get();
    }

    private function getTopVentesArticles()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        
        return DB::table('FACTURE_VNT_DETAIL as fvd')
            ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
            ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
            ->select([
                'a.ART_DESIGNATION as designation',
                DB::raw('SUM(fvd.FVD_QTE) as quantite_vendue'),
                DB::raw('SUM(fvd.FVD_QTE * fvd.FVD_PRIX_VNT_HT) as chiffre_affaires')
            ])
            ->where('fv.FCTV_VALIDE', 1)
            ->where('fv.FCTV_DATE', '>=', $currentMonth)
            ->groupBy('fvd.ART_REF', 'a.ART_DESIGNATION')
            ->orderBy('quantite_vendue', 'desc')
            ->limit(10)
            ->get();
    }

    private function getArticlesRotationLente()
    {
        return DB::table('STOCK as s')
            ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
            ->leftJoin(
                DB::raw('(SELECT ART_REF, MAX(FCTV_DATE) as derniere_vente 
                         FROM FACTURE_VNT_DETAIL fvd 
                         JOIN FACTURE_VNT fv ON fvd.FCTV_REF = fv.FCTV_REF 
                         WHERE fv.FCTV_VALIDE = 1 
                         GROUP BY ART_REF) as last_sales'),
                'a.ART_REF', '=', 'last_sales.ART_REF'
            )
            ->select([
                'a.ART_REF',
                'a.ART_DESIGNATION',
                's.STK_QTE',
                DB::raw('s.STK_QTE * a.ART_PRIX_ACHAT as valeur_stock'),
                'last_sales.derniere_vente'
            ])
            ->where('s.STK_QTE', '>', 0)
            ->where('a.ART_STOCKABLE', 1)
            ->where(function($query) {
                $query->whereNull('last_sales.derniere_vente')
                      ->orWhere('last_sales.derniere_vente', '<', Carbon::now()->subMonths(3));
            })
            ->orderBy('valeur_stock', 'desc')
            ->get();
    }

    private function getStatsMouvements($request)
    {
        $query = $this->buildMouvementsQuery();
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date_mouvement', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date_mouvement', '<=', $request->date_fin);
        }
        
        $mouvements = $query->get();
        
        return [
            'total_entrees' => $mouvements->where('type_mouvement', 'ENTREE')->sum('quantite'),
            'total_sorties' => abs($mouvements->where('type_mouvement', 'SORTIE')->sum('quantite')),
            'mouvements_aujourdhui' => $mouvements->where('date_mouvement', '>=', Carbon::today())->count(),
            'articles_differents' => $mouvements->unique('article_ref')->count()
        ];
    }

    private function getStatsAchats($request)
    {
        $query = DB::table('FACTURE_FOURNISSEUR');
        
        if ($request->filled('date_debut')) {
            $query->whereDate('FCF_DATE', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('FCF_DATE', '<=', $request->date_fin);
        }
        
        return [
            'total_factures' => $query->count(),
            'montant_total' => $query->sum('FCF_MONTANT_TTC'),
            'factures_validees' => $query->where('FCF_VALIDE', 1)->count(),
            'factures_en_attente' => $query->where('FCF_VALIDE', 0)->count()
        ];
    }

    // Nouvelles méthodes pour les fonctionnalités manquantes
    public function createAjustement(Request $request)
    {
        try {
            $request->validate([
                'art_ref' => 'required',
                'nouveau_stock' => 'required|numeric|min:0',
                'motif' => 'required',
            ]);

            $artRef = $request->art_ref;
            $nouveauStock = (float)$request->nouveau_stock;
            $motif = $request->motif;
            $commentaire = $request->commentaire ?? '';
            
            // Récupérer l'article et le stock actuel  
            $article = DB::table('ARTICLE')->where('ART_REF', $artRef)->first();
            if (!$article) {
                return response()->json(['success' => false, 'message' => 'Article non trouvé']);
            }

            // Récupérer l'entrepôt par défaut
            $entrepot = DB::table('ENTREPOT')->first();
            if (!$entrepot) {
                // إنشاء إنتريبو افتراضي إذا لم يكن موجوداً
                $entrepotRef = 'ETP001';
                DB::table('ENTREPOT')->insert([
                    'ETP_REF' => $entrepotRef,
                    'ETP_LIBELLE' => 'Entrepôt Principal',
                    'ETP_PAYS' => 'Maroc',
                    'ETP_VILLE' => 'Casablanca',
                    'ETP_ADRESS' => 'Adresse principale',
                    'ETP_TELEPHONE' => '',
                    'ETP_EMAIL' => '',
                    'ETP_DESCRIPTION' => 'Entrepôt principal créé automatiquement'
                ]);
                $entrepot = DB::table('ENTREPOT')->where('ETP_REF', $entrepotRef)->first();
            }

            $stockActuel = DB::table('STOCK')
                ->where('ART_REF', $artRef)
                ->where('ETP_REF', $entrepot->ETP_REF)
                ->value('STK_QTE') ?? 0;
            
            // S'assurer qu'un enregistrement de stock existe
            $stockRecord = DB::table('STOCK')
                ->where('ART_REF', $artRef)
                ->where('ETP_REF', $entrepot->ETP_REF)
                ->first();
            
            if (!$stockRecord) {
                // Créer un enregistrement de stock s'il n'existe pas
                DB::table('STOCK')->insert([
                    'ART_REF' => $artRef,
                    'ETP_REF' => $entrepot->ETP_REF,
                    'STK_QTE' => 0
                ]);
                $stockActuel = 0;
            }
            
            $difference = $nouveauStock - $stockActuel;
            
            // Mettre à jour le stock
            DB::table('STOCK')->updateOrInsert(
                [
                    'ART_REF' => $artRef,
                    'ETP_REF' => $entrepot->ETP_REF
                ],
                ['STK_QTE' => $nouveauStock]
            );
            
            // Enregistrer le mouvement d'ajustement
            $this->enregistrerMouvement(
                $artRef,
                'Ajustement manuel',
                $difference > 0 ? 'Entrée' : 'Sortie',
                abs($difference),
                $motif . ($commentaire ? ' - ' . $commentaire : ''),
                'Ajustement'
            );
            
            return response()->json([
                'success' => true, 
                'message' => 'Stock ajusté avec succès',
                'ancien_stock' => $stockActuel,
                'nouveau_stock' => $nouveauStock,
                'difference' => $difference
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erreur ajustement stock: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'ajustement: ' . $e->getMessage()]);
        }
    }

    public function createMouvement(Request $request)
    {
        try {
            $typeMouvement = $request->type_mouvement;
            $artRef = $request->article_ref;
            $quantite = $request->quantite;
            $motif = $request->motif;
            $commentaire = $request->commentaire;
            
            // Récupérer le stock actuel
            $stockActuel = DB::table('STOCK')->where('ART_REF', $artRef)->value('STK_QTE') ?? 0;
            
            // Calculer le nouveau stock
            if ($typeMouvement == 'SORTIE') {
                $nouveauStock = $stockActuel - abs($quantite);
            } else {
                $nouveauStock = $stockActuel + abs($quantite);
            }
            
            // Vérifier que le stock ne devient pas négatif
            if ($nouveauStock < 0) {
                return response()->json(['success' => false, 'message' => 'Stock insuffisant']);
            }
            
            // Mettre à jour le stock
            DB::table('STOCK')->updateOrInsert(
                ['ART_REF' => $artRef],
                ['STK_QTE' => $nouveauStock]
            );
            
            return response()->json(['success' => true, 'message' => 'Mouvement enregistré avec succès']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function historiqueMouvements($articleRef)
    {
        $mouvements = $this->buildMouvementsQueryForArticle($articleRef)
            ->orderBy('date_mouvement', 'desc')
            ->get();
            
        $article = DB::table('ARTICLE')->where('ART_REF', $articleRef)->first();
        
        return view('admin.stock.historique-mouvements', compact('mouvements', 'article'));
    }

    private function buildMouvementsQueryForArticle($articleRef)
    {
        // Union des différents types de mouvements pour un article spécifique
        $entrees = DB::table('FACTURE_FRS_DETAIL as ffd')
            ->join('FACTURE_FOURNISSEUR as ff', 'ffd.FCF_REF', '=', 'ff.FCF_REF')
            ->join('ARTICLE as a', 'ffd.ART_REF', '=', 'a.ART_REF')
            ->select([
                'ffd.ART_REF as article_ref',
                'a.ART_DESIGNATION as article_designation',
                'ffd.FCF_QTE as quantite',
                'ff.FCF_DATE as date_mouvement',
                DB::raw("'ENTREE' as type_mouvement"),
                DB::raw("'Achat fournisseur' as libelle_mouvement"),
                'ff.FCF_REF as reference_document'
            ])
            ->where('ff.FCF_VALIDE', 1)
            ->where('ffd.ART_REF', $articleRef);

        $sorties = DB::table('FACTURE_VNT_DETAIL as fvd')
            ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
            ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
            ->select([
                'fvd.ART_REF as article_ref',
                'a.ART_DESIGNATION as article_designation',
                DB::raw('-fvd.FVD_QTE as quantite'),
                'fv.FCTV_DATE as date_mouvement',
                DB::raw("'SORTIE' as type_mouvement"),
                DB::raw("'Vente client' as libelle_mouvement"),
                'fv.FCTV_REF as reference_document'
            ])
            ->where('fv.FCTV_VALIDE', 1)
            ->where('fvd.ART_REF', $articleRef);

        return $entrees->union($sorties);
    }

    private function getTopArticlesByMonth()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        
        return DB::table('FACTURE_VNT_DETAIL as fvd')
            ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
            ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
            ->select([
                'a.ART_DESIGNATION as designation',
                DB::raw('SUM(fvd.FVD_QTE) as quantite_vendue'),
                DB::raw('SUM(fvd.FVD_QTE * fvd.FVD_PRIX_VNT_HT) as chiffre_affaires')
            ])
            ->where('fv.FCTV_VALIDE', 1)
            ->where('fv.FCTV_DATE', '>=', $currentMonth)
            ->groupBy('fvd.ART_REF', 'a.ART_DESIGNATION')
            ->orderBy('quantite_vendue', 'desc')
            ->limit(10)
            ->get();
    }

    private function getStockParFamille()
    {
        return DB::table('STOCK as s')
            ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
            ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
            ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
            ->select([
                'f.FAM_LIB as famille',
                DB::raw('COUNT(DISTINCT a.ART_REF) as nombre_articles'),
                DB::raw('SUM(s.STK_QTE) as quantite_totale'),
                DB::raw('SUM(s.STK_QTE * a.ART_PRIX_ACHAT) as valeur_stock'),
                DB::raw('SUM(CASE WHEN s.STK_QTE = 0 THEN 1 ELSE 0 END) as articles_rupture'),
                DB::raw('SUM(CASE WHEN s.STK_QTE <= a.ART_STOCK_MIN AND s.STK_QTE > 0 THEN 1 ELSE 0 END) as articles_alerte')
            ])
            ->where('a.ART_STOCKABLE', 1)
            ->whereNotNull('f.FAM_LIB')
            ->groupBy('f.FAM_REF', 'f.FAM_LIB')
            ->orderBy('valeur_stock', 'desc')
            ->get();
    }

    // Méthodes API pour AJAX
    public function getStockStats()
    {
        return response()->json($this->getStockStatistics());
    }

    public function searchArticles(Request $request)
    {
        $term = $request->get('term', '');
        
        // Récupérer l'entrepôt par défaut
        $entrepot = DB::table('ENTREPOT')->first();
        if (!$entrepot) {
            // إنشاء إنتريبو افتراضي إذا لم يكن موجوداً
            $entrepotRef = 'ETP001';
            try {
                DB::table('ENTREPOT')->insert([
                    'ETP_REF' => $entrepotRef,
                    'ETP_LIBELLE' => 'Entrepôt Principal',
                    'ETP_PAYS' => 'Maroc',
                    'ETP_VILLE' => 'Casablanca',
                    'ETP_ADRESS' => 'Adresse principale',
                    'ETP_TELEPHONE' => '',
                    'ETP_EMAIL' => '',
                    'ETP_DESCRIPTION' => 'Entrepôt principal créé automatiquement'
                ]);
                $entrepot = DB::table('ENTREPOT')->where('ETP_REF', $entrepotRef)->first();
            } catch (\Exception $e) {
                return response()->json([]);
            }
        }
        
        $articles = DB::table('ARTICLE as a')
            ->leftJoin('STOCK as s', function($join) use ($entrepot) {
                $join->on('a.ART_REF', '=', 's.ART_REF')
                     ->where('s.ETP_REF', '=', $entrepot->ETP_REF);
            })
            ->select([
                'a.ART_REF as id',
                'a.ART_DESIGNATION as text',
                'a.ART_CODEBARR as code_barre',
                's.STK_QTE as stock'
            ])
            ->where('a.ART_STOCKABLE', 1)
            ->where(function($query) use ($term) {
                $query->where('a.ART_DESIGNATION', 'LIKE', "%{$term}%")
                      ->orWhere('a.ART_CODEBARR', 'LIKE', "%{$term}%")
                      ->orWhere('a.ART_REF', 'LIKE', "%{$term}%");
            })
            ->limit(20)
            ->get();

        return response()->json($articles);
    }

    public function getAlertesCount()
    {
        $count = DB::table('STOCK as s')
            ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
            ->where(function($query) {
                $query->where('s.STK_QTE', 0)
                      ->orWhereRaw('s.STK_QTE <= a.ART_STOCK_MIN');
            })
            ->where('a.ART_STOCKABLE', 1)
            ->count();

        return response()->json(['count' => $count]);
    }

    // Méthodes manquantes pour les routes définies

    public function ajustements()
    {
        return redirect()->route('admin.stock.inventaire')->with('info', 'Les ajustements sont gérés depuis l\'inventaire');
    }

    public function historiqueAjustements()
    {
        return redirect()->route('admin.stock.mouvements')->with('info', 'Historique des ajustements disponible dans les mouvements');
    }

    public function createAchat()
    {
        return redirect()->route('admin.stock.achats')->with('info', 'Création d\'achat depuis la liste des achats');
    }

    public function storeAchat(Request $request)
    {
        return response()->json(['success' => false, 'message' => 'Fonctionnalité en développement']);
    }

    public function showAchat($facture)
    {
        return redirect()->route('admin.stock.achats')->with('info', 'Détails d\'achat disponibles');
    }

    public function confirmReception(Request $request)
    {
        return response()->json(['success' => false, 'message' => 'Fonctionnalité en développement']);
    }

    public function showBonLivraison($bl)
    {
        return redirect()->route('admin.stock.reception')->with('info', 'Bon de livraison disponible');
    }

    public function rapportValorisation()
    {
        return redirect()->route('admin.stock.rapports')->with('info', 'Rapport de valorisation dans les rapports');
    }

    public function rapportMouvementsPeriode()
    {
        return redirect()->route('admin.stock.rapports')->with('info', 'Rapport des mouvements dans les rapports');
    }

    public function rapportRupture()
    {
        return redirect()->route('admin.stock.rapports')->with('info', 'Rapport de rupture dans les rapports');
    }

    public function rapportRotation()
    {
        return redirect()->route('admin.stock.rapports')->with('info', 'Rapport de rotation dans les rapports');
    }

    public function markAlerteRead(Request $request)
    {
        return response()->json(['success' => true, 'message' => 'Alerte marquée comme lue']);
    }

    public function getNotificationsAlertes()
    {
        $alertes = $this->getStockAlertes();
        return response()->json($alertes);
    }

    public function updateInventaire(Request $request)
    {
        return response()->json(['success' => false, 'message' => 'Fonctionnalité en développement']);
    }

    public function exportInventaire()
    {
        return redirect()->route('admin.stock.inventaire')->with('info', 'Export disponible prochainement');
    }

    public function getRecentMouvementsApi()
    {
        $mouvements = $this->getRecentMouvements(5);
        return response()->json($mouvements);
    }

    public function getFournisseurArticles($fournisseur)
    {
        $articles = DB::table('FOURNISSEUR_OF_ARTICLE as foa')
            ->join('ARTICLE as a', 'foa.ART_REF', '=', 'a.ART_REF')
            ->where('foa.FRS_REF', $fournisseur)
            ->select(['a.ART_REF', 'a.ART_DESIGNATION', 'foa.PRIX_ACHAT'])
            ->get();

        return response()->json($articles);
    }

    /**
     * Enregistrer un mouvement de stock
     */
    private function enregistrerMouvement($artRef, $typeDocument, $typeMouvement, $quantite, $motif, $origine = 'Manuel')
    {
        try {
            // Vérifier s'il existe une table de mouvements
            if (DB::getSchemaBuilder()->hasTable('MOUVEMENT_STOCK')) {
                DB::table('MOUVEMENT_STOCK')->insert([
                    'ART_REF' => $artRef,
                    'TYPE_DOCUMENT' => $typeDocument,
                    'TYPE_MOUVEMENT' => $typeMouvement,
                    'QUANTITE' => $quantite,
                    'MOTIF' => $motif,
                    'ORIGINE' => $origine,
                    'DATE_MOUVEMENT' => now(),
                    'UTILISATEUR' => auth()->user()->name ?? 'Système'
                ]);
            }
            
            // Log pour traçabilité
            \Log::info("Mouvement de stock enregistré", [
                'article' => $artRef,
                'type' => $typeMouvement,
                'quantite' => $quantite,
                'motif' => $motif
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Erreur enregistrement mouvement: " . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'une facture d'achat
     */
    public function showAchatDetails($fcfRef)
    {
        try {
            $facture = DB::table('FACTURE_FOURNISSEUR as ff')
                ->leftJoin('FOURNISSEUR as f', 'ff.FRS_REF', '=', 'f.FRS_REF')
                ->where('ff.FCF_REF', $fcfRef)
                ->select([
                    'ff.*',
                    'f.FRS_RAISONSOCIAL as fournisseur',
                    'f.FRS_ADRESS as adresse_fournisseur',
                    'f.FRS_TEL as tel_fournisseur'
                ])
                ->first();

            if (!$facture) {
                return response()->json(['success' => false, 'message' => 'Facture non trouvée']);
            }

            $details = DB::table('FACTURE_FRS_DETAIL as fd')
                ->leftJoin('ARTICLE as a', 'fd.ART_REF', '=', 'a.ART_REF')
                ->where('fd.FCF_REF', $fcfRef)
                ->select([
                    'a.ART_DESIGNATION as designation',
                    'a.ART_REF',
                    'fd.FCF_QTE as quantite',
                    'fd.FCF_PRIX_HT as prix_ht',
                    'fd.FCF_PRIX_TTC as prix_ttc',
                    'fd.FCF_TVA as tva',
                    'fd.FCF_REMISE as remise'
                ])
                ->get();

            $html = view('admin.stock.partials.achat-details', compact('facture', 'details'))->render();
            
            return response()->json(['success' => true, 'html' => $html]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Valider une facture d'achat
     */
    public function validateAchat($fcfRef)
    {
        try {
            $updated = DB::table('FACTURE_FOURNISSEUR')
                ->where('FCF_REF', $fcfRef)
                ->update(['FCF_VALIDE' => 1]);

            if ($updated) {
                return response()->json(['success' => true, 'message' => 'Facture validée avec succès']);
            } else {
                return response()->json(['success' => false, 'message' => 'Facture non trouvée']);
            }
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Imprimer une facture d'achat
     */
    public function printAchat($fcfRef)
    {
        $facture = DB::table('FACTURE_FOURNISSEUR as ff')
            ->leftJoin('FOURNISSEUR as f', 'ff.FRS_REF', '=', 'f.FRS_REF')
            ->where('ff.FCF_REF', $fcfRef)
            ->select([
                'ff.*',
                'f.FRS_RAISONSOCIAL as fournisseur',
                'f.FRS_ADRESS as adresse_fournisseur'
            ])
            ->first();

        $details = DB::table('FACTURE_FRS_DETAIL as fd')
            ->leftJoin('ARTICLE as a', 'fd.ART_REF', '=', 'a.ART_REF')
            ->where('fd.FCF_REF', $fcfRef)
            ->select([
                'a.ART_DESIGNATION as designation',
                'fd.FCF_QTE as quantite',
                'fd.FCF_PRIX_HT as prix_ht',
                'fd.FCF_PRIX_TTC as prix_ttc'
            ])
            ->get();

        return view('admin.stock.print.facture-achat', compact('facture', 'details'));
    }
}
