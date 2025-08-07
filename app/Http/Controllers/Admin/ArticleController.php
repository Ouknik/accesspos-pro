<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\ArticlesExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Contrôleur pour la gestion des articles/produits
 * Utilise les tables existantes: ARTICLE, FAMILLE, SOUS_FAMILLE, STOCK
 */
class ArticleController extends Controller
{
    /**
     * Affichage de la liste des articles
     */
    public function index(Request $request)
    {
        try {
            $search = $request->get('search') ?? null;
            $famille = $request->get('famille') ?? null;
            $statut = $request->get('statut') ?? null;
            $perPage = $request->get('per_page', 15);

            // Test de connexion à la base de données
            try {
                DB::connection()->getPdo();
                // Test simple pour vérifier que les tables existent
                $testCount = DB::table('ARTICLE')->count();
            } catch (\Exception $e) {
                throw new \Exception('Impossible de se connecter à la base de données: ' . $e->getMessage());
            }

            // Requête principale avec jointures - Version simplifiée pour éviter les problèmes SQL Server
            $query = DB::table('ARTICLE as a')
                ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
                ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
                ->select([
                    'a.ART_REF',
                    'a.ART_DESIGNATION',
                    'a.ART_PRIX_VENTE',
                    'a.ART_PRIX_ACHAT',
                    'a.ART_STOCK_MIN',
                    'a.ART_STOCK_MAX',
                    'a.ART_VENTE',
                    'a.ART_STOCKABLE',
                    'a.IsMenu',
                    'a.ART_DATE_CREATION',
                    'sf.SFM_LIB as sous_famille',
                    'f.FAM_LIB as famille',
                    'f.FAM_REF as famille_ref',
                    DB::raw('CASE 
                        WHEN a.ART_VENTE = 1 THEN \'Actif\' 
                        ELSE \'Inactif\' 
                    END as statut_display')
                ]);

            // Filtres
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('a.ART_DESIGNATION', 'LIKE', "%{$search}%")
                      ->orWhere('a.ART_REF', 'LIKE', "%{$search}%");
                });
            }

            if ($famille) {
                $query->where('f.FAM_REF', $famille);
            }

            if ($statut !== null) {
                $query->where('a.ART_VENTE', $statut);
            }

            // Ordonner et paginer
            $articles = $query->orderBy('a.ART_DESIGNATION')->paginate($perPage);

            // Ajouter le stock pour chaque article (requête séparée pour éviter les complications)
            foreach ($articles as $article) {
                $stock = DB::table('STOCK')
                    ->where('ART_REF', $article->ART_REF)
                    ->sum('STK_QTE');
                
                $article->stock_total = $stock ?? 0;
                
                // Déterminer le statut du stock
                if ($article->stock_total == 0) {
                    $article->statut_stock = 'Rupture';
                } elseif ($article->stock_total <= $article->ART_STOCK_MIN) {
                    $article->statut_stock = 'Stock Faible';
                } else {
                    $article->statut_stock = 'Normal';
                }
            }

            // Statistiques générales
            $stats = $this->getArticlesStats();

            // Liste des familles pour le filtre
            $familles = DB::table('FAMILLE')->orderBy('FAM_LIB')->get();

            return view('admin.articles.index', compact('articles', 'stats', 'familles', 'search', 'famille', 'statut'));

        } catch (\Exception $e) {
            // Log pour debugging
            Log::error('Erreur dans ArticleController@index: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()->with('error', 'Erreur lors du chargement des articles: ' . $e->getMessage());
        }
    }

    /**
     * Affichage des détails d'un article
     */
    public function show($artRef)
    {
        try {
            // Détails de l'article
            $article = DB::table('ARTICLE as a')
                ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
                ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
                ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
                ->select([
                    'a.ART_REF',
                    'a.SFM_REF', 
                    'a.ART_DESIGNATION', 
                    'a.ART_PRIX_VENTE',
                    'a.ART_PRIX_ACHAT', 
                    'a.ART_STOCK_MIN', 
                    'a.ART_STOCK_MAX', 
                    'a.ART_VENTE',
                    'a.ART_STOCKABLE', 
                    'a.IsMenu', 
                    'a.ART_DESCRIPTION', 
                    'a.ART_LIBELLE_TICKET',
                    'a.UNM_ABR',
                    'a.ART_PRIX_ACHAT_HT',
                    'a.ART_PRIX_VENTE_HT',
                    'a.ART_LIBELLE_CAISSE',                'a.ART_LIBELLE_ARABE',
                'a.ART_DATE_CREATION',
                'a.ART_DATE_MODIFICATION',
                'sf.SFM_LIB as sous_famille',
                    'f.FAM_LIB as famille',
                    DB::raw('ISNULL(SUM(s.STK_QTE), 0) as stock_total')
                ])
                ->where('a.ART_REF', $artRef)
                ->groupBy([
                    'a.ART_REF', 
                    'a.SFM_REF', 
                    'a.ART_DESIGNATION', 
                    'a.ART_PRIX_VENTE',
                    'a.ART_PRIX_ACHAT', 
                    'a.ART_STOCK_MIN', 
                    'a.ART_STOCK_MAX', 
                    'a.ART_VENTE',
                    'a.ART_STOCKABLE', 
                    'a.IsMenu', 
                    'a.ART_DESCRIPTION', 
                    'a.ART_LIBELLE_TICKET',
                    'a.UNM_ABR',
                    'a.ART_PRIX_ACHAT_HT',
                    'a.ART_PRIX_VENTE_HT',
                    'a.ART_LIBELLE_CAISSE',                'a.ART_LIBELLE_ARABE',
                'a.ART_DATE_CREATION',
                'a.ART_DATE_MODIFICATION',
                'sf.SFM_LIB', 
                    'f.FAM_LIB'
                ])
                ->first();

            if (!$article) {
                return redirect()->route('admin.articles.index')->with('error', 'Article non trouvé');
            }

            // Statistiques de vente
            $ventesStats = $this->getArticleVentesStats($artRef);

            // Historique des mouvements de stock (si disponible)
            $mouvementsStock = $this->getArticleStockMovements($artRef);

            return view('admin.articles.show', compact('article', 'ventesStats', 'mouvementsStock'));

        } catch (\Exception $e) {
            return redirect()->route('admin.articles.index')->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Affichage du formulaire de création
     */
    public function create()
    {
        try {
            // Vérifier les permissions admin
          /*  if (!$this->isAdmin()) {
                return redirect()->route('admin.articles.index')
                    ->with('error', 'Accès refusé. Seuls les administrateurs peuvent créer des articles.');
            }*/

            // Test de connexion à la base de données
            try {
                DB::connection()->getPdo();
            } catch (\Exception $e) {
                return redirect()->route('admin.articles.index')
                    ->with('error', 'Erreur de connexion à la base de données: ' . $e->getMessage());
            }

            // Liste des familles avec gestion d'erreur
            try {
                $familles = DB::table('FAMILLE')->orderBy('FAM_LIB')->get();
                if ($familles->isEmpty()) {
                    Log::warning('Aucune famille trouvée dans la base de données');
                }
            } catch (\Exception $e) {
                Log::error('Erreur lors de la récupération des familles: ' . $e->getMessage());
                $familles = collect([]);
            }

            // Liste des sous-familles avec gestion d'erreur
            try {
                $sousFamilles = DB::table('SOUS_FAMILLE')->orderBy('SFM_LIB')->get();
                if ($sousFamilles->isEmpty()) {
                    Log::warning('Aucune sous-famille trouvée dans la base de données');
                }
            } catch (\Exception $e) {
                Log::error('Erreur lors de la récupération des sous-familles: ' . $e->getMessage());
                $sousFamilles = collect([]);
            }

            // Liste des unités de mesure
            try {
                $unitesMesure = DB::table('UNITE_MESURE')->orderBy('UNM_LIB')->get();
                if ($unitesMesure->isEmpty()) {
                    Log::warning('Aucune unité de mesure trouvée dans la base de données');
                }
            } catch (\Exception $e) {
                Log::error('Erreur lors de la récupération des unités de mesure: ' . $e->getMessage());
                $unitesMesure = collect([]);
            }

            // Générer un nouvel ID d'article pour prévisualisation
            try {
                $nextArticleRef = $this->generateArticleRef();
            } catch (\Exception $e) {
                Log::error('Erreur lors de la génération de la référence article: ' . $e->getMessage());
                $nextArticleRef = 'ART000001'; // Valeur par défaut
            }

            // Vérifications supplémentaires pour le debugging
            Log::info("ArticleController@create - Familles: " . count($familles) . ", Sous-familles: " . count($sousFamilles));

            return view('admin.articles.create', compact('familles', 'sousFamilles', 'unitesMesure', 'nextArticleRef'));

        } catch (\Exception $e) {
            Log::error('Erreur dans ArticleController@create: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->route('admin.articles.index')
                ->with('error', 'Erreur lors du chargement du formulaire de création: ' . $e->getMessage());
        }
    }

    /**
     * Enregistrement d'un nouvel article
     */
    public function store(Request $request)
    {
        try {
            Log::info('ArticleController@store - Début de l\'enregistrement');
            Log::info('Données reçues: ' . json_encode($request->all()));
            Log::info('Method: ' . $request->method());
            Log::info('Headers: ' . json_encode($request->headers->all()));

            // Vérifier les permissions admin
           /* if (!$this->isAdmin()) {
                return redirect()->route('admin.articles.index')
                    ->with('error', 'Accès refusé. Seuls les administrateurs peuvent créer des articles.');
            }*/

            // Validation des données avec logging supplémentaire
            Log::info('Début de la validation des données');
            
            $validatedData = $request->validate([
                'ART_REF' => 'required|string|unique:ARTICLE,ART_REF|max:50',
                'ART_DESIGNATION' => 'required|string|max:200',
                'sous_famille' => 'nullable|string|exists:SOUS_FAMILLE,SFM_REF',
                'unite_mesure' => 'nullable|string|exists:UNITE_MESURE,UNM_ABR',
                'ART_PRIX_VENTE' => 'required|numeric|min:0',
                'ART_PRIX_ACHAT' => 'required|numeric|min:0',
                'ART_STOCK_MIN' => 'nullable|integer|min:0',
                'ART_STOCK_MAX' => 'nullable|integer|min:0',
                'initial_stock' => 'nullable|integer|min:0',
                'ART_DESCRIPTION' => 'nullable|string|max:500',
                'ART_LIBELLE_TICKET' => 'nullable|string|max:100',
            ], [
                'ART_REF.required' => 'رمز المنتج مطلوب',
                'ART_REF.unique' => 'رمز المنتج موجود مسبقاً',
                'ART_DESIGNATION.required' => 'اسم المنتج مطلوب',
                'ART_DESIGNATION.max' => 'اسم المنتج لا يجب أن يتجاوز 200 حرف',
                'sous_famille.exists' => 'الفئة الفرعية المحددة غير موجودة',
                'unite_mesure.exists' => 'وحدة القياس المحددة غير موجودة',
                'ART_PRIX_VENTE.required' => 'سعر البيع مطلوب',
                'ART_PRIX_VENTE.numeric' => 'سعر البيع يجب أن يكون رقم',
                'ART_PRIX_VENTE.min' => 'سعر البيع لا يمكن أن يكون سالب',
                'ART_PRIX_ACHAT.required' => 'سعر الشراء مطلوب',
                'ART_PRIX_ACHAT.numeric' => 'سعر الشراء يجب أن يكون رقم',
                'ART_PRIX_ACHAT.min' => 'سعر الشراء لا يمكن أن يكون سالب',
            ]);
            
            Log::info('Validation réussie');
            Log::info('Données validées: ' . json_encode($validatedData));
            
            // إذا لم يتم اختيار فئة فرعية، استخدم الأولى المتاحة
            if (empty($validatedData['sous_famille'])) {
                $firstSousFamille = DB::table('SOUS_FAMILLE')->first();
                if ($firstSousFamille) {
                    $validatedData['sous_famille'] = $firstSousFamille->SFM_REF;
                    Log::info('استخدام أول فئة فرعية متاحة: ' . $validatedData['sous_famille']);
                } else {
                    throw new \Exception('لا توجد فئات فرعية متاحة في قاعدة البيانات');
                }
            }
            
            // إذا لم يتم اختيار وحدة قياس، استخدم الأولى المتاحة
            if (empty($validatedData['unite_mesure'])) {
                $firstUnite = DB::table('UNITE_MESURE')->first();
                if ($firstUnite) {
                    $validatedData['unite_mesure'] = $firstUnite->UNM_ABR;
                    Log::info('استخدام أول وحدة قياس متاحة: ' . $validatedData['unite_mesure']);
                } else {
                    throw new \Exception('لا توجد وحدات قياس متاحة في قاعدة البيانات');
                }
            }

            // التحقق من أن الحد الأقصى للمخزون أكبر من الحد الأدنى
            if ($request->ART_STOCK_MAX && $request->ART_STOCK_MIN && 
                $request->ART_STOCK_MAX <= $request->ART_STOCK_MIN) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'الحد الأقصى للمخزون يجب أن يكون أكبر من الحد الأدنى');
            }

            // بدء المعاملة
            Log::info('بدء المعاملة');
            DB::beginTransaction();

            // استخدام المرجع المرسل من النموذج
            $artRef = $validatedData['ART_REF'];
            Log::info('مرجع المنتج: ' . $artRef);

            // Préparer les données d'insertion
            $insertData = [
                'ART_REF' => $artRef,
                'SFM_REF' => $validatedData['sous_famille'], // الآن مضمون أن يكون موجوداً
                'ART_DESIGNATION' => $validatedData['ART_DESIGNATION'],
                'ART_PRIX_VENTE' => $validatedData['ART_PRIX_VENTE'],
                'ART_PRIX_ACHAT' => $validatedData['ART_PRIX_ACHAT'], // مطلوب الآن
                'UNM_ABR' => $validatedData['unite_mesure'], // وحدة القياس المحددة
                'ART_STOCK_MIN' => $validatedData['ART_STOCK_MIN'] ?? 0,
                'ART_STOCK_MAX' => $validatedData['ART_STOCK_MAX'] ?? 100,
                'ART_DESCRIPTION' => $validatedData['ART_DESCRIPTION'],
                'ART_LIBELLE_TICKET' => $validatedData['ART_LIBELLE_TICKET'] ?? $validatedData['ART_DESIGNATION'],
                'ART_VENTE' => $request->has('ART_VENTE') ? 1 : 0,
                'ART_STOCKABLE' => $request->has('ART_STOCKABLE') ? 1 : 0,
                'IsMenu' => $request->has('IsMenu') ? 1 : 0,
                'ART_DATE_CREATION' => now(),
                
                // Champs obligatoires avec valeurs par défaut
                'ART_PRIX_ACHAT_HT' => $validatedData['ART_PRIX_ACHAT'],
                'ART_PRIX_VENTE_HT' => $validatedData['ART_PRIX_VENTE'],
                'ART_LIBELLE_CAISSE' => $validatedData['ART_DESIGNATION'],
                'ART_LIBELLE_ARABE' => $validatedData['ART_DESIGNATION'],
                'ART_ORDRE_AFFICHAGE' => 999,
                'base64' => '',
                'CF1' => '',
                'CF2' => '',
                'CF3' => '',
                'CF4' => '',
                'CF5' => '',
                'CF6' => '',
                'CF7' => '',
                'CF8' => '',
                'CF9' => '',
                'CF10' => '',
                'IsIngredient' => 0,
            ];

            Log::info('Données d\'insertion préparées: ' . json_encode($insertData));

            // Insérer l'article
            Log::info('Insertion de l\'article dans la base de données');
            $insertResult = DB::table('ARTICLE')->insert($insertData);
            Log::info('Résultat de l\'insertion: ' . ($insertResult ? 'SUCCESS' : 'FAILED'));

            // Créer un enregistrement de stock initial si stockable
            if ($request->has('ART_STOCKABLE')) {
                Log::info('Création du stock initial');
                $stockData = [
                    'ART_REF' => $artRef,
                    'ETP_REF' => '001', // Entrepôt par défaut (existant dans la DB)
                    'STK_QTE' => $validatedData['initial_stock'] ?? 0,
                ];
                
                Log::info('Données de stock: ' . json_encode($stockData));
                $stockResult = DB::table('STOCK')->insert($stockData);
                Log::info('Résultat de l\'insertion stock: ' . ($stockResult ? 'SUCCESS' : 'FAILED'));
            }

            // إتمام المعاملة
            DB::commit();
            Log::info('المعاملة تمت بنجاح');

            Log::info("Article créé avec succès: $artRef - " . $validatedData['ART_DESIGNATION']);

            // Redirection avec message de succès et save_and_new check
            if ($request->has('save_and_new')) {
                return redirect()->route('admin.articles.create')
                    ->with('success', 'تم إنشاء المنتج بنجاح: ' . $validatedData['ART_DESIGNATION'] . '. يمكنك إضافة منتج آخر.');
            }

            return redirect()->route('admin.articles.show', $artRef)
                ->with('success', 'تم إنشاء المنتج بنجاح: ' . $validatedData['ART_DESIGNATION']);;

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            Log::error('خطأ في التحقق من البيانات: ' . json_encode($e->errors()));
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'يرجى التحقق من البيانات المدخلة وإعادة المحاولة');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur dans ArticleController@store: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('Line: ' . $e->getLine());
            Log::error('File: ' . $e->getFile());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء المنتج: ' . $e->getMessage());
        }
    }

    /**
     * Affichage du formulaire d'édition
     */
    public function edit($artRef)
    {
        try {
            // Vérifier les permissions admin
         /*   if (!$this->isAdmin()) {
                return redirect()->route('admin.articles.index')->with('error', 'Accès refusé');
            }*/

            $article = DB::table('ARTICLE')->where('ART_REF', $artRef)->first();
            
            if (!$article) {
                return redirect()->route('admin.articles.index')->with('error', 'Article non trouvé');
            }

            $familles = DB::table('FAMILLE')->orderBy('FAM_LIB')->get();
            $sousFamilles = DB::table('SOUS_FAMILLE')->orderBy('SFM_LIB')->get();

            return view('admin.articles.edit', compact('article', 'familles', 'sousFamilles'));

        } catch (\Exception $e) {
            return redirect()->route('admin.articles.index')->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Mise à jour d'un article
     */
    public function update(Request $request, $artRef)
    {
        try {
            // Vérifier les permissions admin
        /*    if (!$this->isAdmin()) {
                return response()->json(['error' => 'Accès refusé'], 403);
            }
*/
            $request->validate([
                'art_designation' => 'required|string|max:200',
                'sfm_ref' => 'required|string',
                'art_prix_vente' => 'required|numeric|min:0',
                'art_prix_achat' => 'nullable|numeric|min:0',
                'art_stock_min' => 'nullable|integer|min:0',
                'art_stock_max' => 'nullable|integer|min:0',
            ]);

            // Mettre à jour l'article
            DB::table('ARTICLE')
                ->where('ART_REF', $artRef)
                ->update([
                    'SFM_REF' => $request->sfm_ref,
                    'ART_DESIGNATION' => $request->art_designation,
                    'ART_PRIX_VENTE' => $request->art_prix_vente,
                    'ART_PRIX_ACHAT' => $request->art_prix_achat ?? 0,
                    'ART_STOCK_MIN' => $request->art_stock_min ?? 0,
                    'ART_STOCK_MAX' => $request->art_stock_max ?? 0,
                    'ART_DESCRIPTION' => $request->art_description,
                    'ART_LIBELLE_TICKET' => $request->art_libelle_ticket,
                    'ART_VENTE' => $request->has('art_vente') ? 1 : 0,
                    'ART_STOCKABLE' => $request->has('art_stockable') ? 1 : 0,
                    'IsMenu' => $request->has('is_menu') ? 1 : 0,
                    'ART_DATE_MODIFICATION' => now(),
                ]);

            return redirect()->route('admin.articles.show', $artRef)
                ->with('success', 'Article mis à jour avec succès');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Suppression d'un article (désactivation)
     */
    public function destroy($artRef)
    {
        try {
            // Vérifier les permissions admin
            if (!$this->isAdmin()) {
                return response()->json(['error' => 'Accès refusé'], 403);
            }

            // Désactiver au lieu de supprimer
            DB::table('ARTICLE')
                ->where('ART_REF', $artRef)
                ->update(['ART_VENTE' => 0]);

            return response()->json(['success' => 'Article désactivé avec succès']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Activation/Désactivation d'un article
     */
    public function toggleStatus($artRef)
    {
        try {
            if (!$this->isAdmin()) {
                return response()->json(['error' => 'Accès refusé'], 403);
            }

            $article = DB::table('ARTICLE')->where('ART_REF', $artRef)->first();
            
            if (!$article) {
                return response()->json(['error' => 'Article non trouvé'], 404);
            }

            $newStatus = $article->ART_VENTE ? 0 : 1;
            
            DB::table('ARTICLE')
                ->where('ART_REF', $artRef)
                ->update(['ART_VENTE' => $newStatus]);

            $message = $newStatus ? 'Article activé' : 'Article désactivé';
            
            return response()->json(['success' => $message, 'new_status' => $newStatus]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export articles to Excel (Real Excel file)
     */
    public function exportExcel(Request $request)
    {
        try {
            // Prepare filters for the export
            $filters = [
                'search' => $request->get('search'),
                'famille' => $request->get('famille'),
                'statut' => $request->get('statut'),
                'stock_filter' => $request->get('stock_filter'),
            ];

            // Generate filename with timestamp
            $filename = 'articles_export_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Use Laravel Excel to create a proper Excel file
            return Excel::download(
                new ArticlesExport($filters), 
                $filename
            );

        } catch (\Exception $e) {
            Log::error('Error in exportExcel: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de l\'export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export articles to PDF
     */
    public function exportPDF(Request $request)
    {
        try {
            // Get the same data as exportExcel
            $search = $request->get('search');
            $famille = $request->get('famille');
            $statut = $request->get('statut');
            $stock_filter = $request->get('stock_filter');

            // Build the query
            $query = DB::table('ARTICLE as a')
                ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
                ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
                ->select([
                    'a.ART_REF',
                    'a.ART_DESIGNATION',
                    'a.ART_PRIX_VENTE',
                    'a.ART_PRIX_ACHAT',
                    'a.ART_STOCK_MIN',
                    'a.ART_VENTE',
                    'f.FAM_LIB as famille',
                    DB::raw('CASE 
                        WHEN a.ART_VENTE = 1 THEN \'Actif\' 
                        ELSE \'Inactif\' 
                    END as statut_display')
                ]);

            // Apply filters (same as exportExcel)
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('a.ART_DESIGNATION', 'LIKE', "%{$search}%")
                      ->orWhere('a.ART_REF', 'LIKE', "%{$search}%");
                });
            }

            if ($famille) {
                $query->where('f.FAM_REF', $famille);
            }

            if ($statut !== null) {
                $query->where('a.ART_VENTE', $statut);
            }

            $articles = $query->orderBy('a.ART_DESIGNATION')->get();

            // Add stock information
            foreach ($articles as $article) {
                $stock = DB::table('STOCK')
                    ->where('ART_REF', $article->ART_REF)
                    ->sum('STK_QTE');
                $article->stock_total = $stock ?? 0;
            }

            // Apply stock filter
            if ($stock_filter) {
                $articles = $articles->filter(function($article) use ($stock_filter) {
                    if ($stock_filter === 'faible') {
                        return $article->stock_total <= $article->ART_STOCK_MIN && $article->stock_total > 0;
                    } elseif ($stock_filter === 'rupture') {
                        return $article->stock_total <= 0;
                    }
                    return true;
                });
            }

            // Create HTML content for PDF
            $html = '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Liste des Articles - AccessPos Pro</title>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        margin: 20px; 
                        font-size: 12px;
                    }
                    .header { 
                        text-align: center; 
                        margin-bottom: 30px; 
                        border-bottom: 3px solid #007bff;
                        padding-bottom: 15px;
                    }
                    .header h1 { 
                        color: #333; 
                        margin: 0;
                        font-size: 24px;
                    }
                    .header-info { 
                        color: #666; 
                        margin-top: 10px;
                        background: #f8f9fa;
                        padding: 15px;
                        border-radius: 5px;
                    }
                    table { 
                        width: 100%; 
                        border-collapse: collapse; 
                        margin-top: 20px; 
                        font-size: 11px;
                    }
                    th, td { 
                        border: 1px solid #ddd; 
                        padding: 8px; 
                        text-align: left; 
                    }
                    th { 
                        background-color: #f8f9fa; 
                        font-weight: bold; 
                        text-align: center;
                        font-size: 10px;
                    }
                    tr:nth-child(even) { 
                        background-color: #f9f9f9; 
                    }
                    .footer {
                        margin-top: 20px;
                        text-align: center;
                        color: #666;
                        font-size: 10px;
                        border-top: 1px solid #ddd;
                        padding-top: 10px;
                    }
                    .ref-cell { font-weight: bold; color: #007bff; }
                    .price-cell { text-align: right; }
                    .stock-cell { text-align: center; }
                    .status-cell { text-align: center; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>📋 LISTE DES ARTICLES</h1>
                    <div class="header-info">
                        <p><strong>Date d\'export:</strong> ' . date('d/m/Y à H:i:s') . '</p>
                        <p><strong>Nombre d\'articles:</strong> ' . count($articles) . '</p>
                        <p><strong>AccessPos Pro</strong> - Système de Gestion</p>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th style="width: 15%;">Référence</th>
                            <th style="width: 30%;">Nom du Produit</th>
                            <th style="width: 15%;">Catégorie</th>
                            <th style="width: 12%;">Prix Vente</th>
                            <th style="width: 12%;">Prix Achat</th>
                            <th style="width: 8%;">Stock</th>
                            <th style="width: 8%;">Statut</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($articles as $article) {
                $html .= '<tr>
                    <td class="ref-cell">' . htmlspecialchars($article->ART_REF) . '</td>
                    <td><strong>' . htmlspecialchars($article->ART_DESIGNATION) . '</strong></td>
                    <td>' . htmlspecialchars($article->famille ?? 'Non définie') . '</td>
                    <td class="price-cell">' . number_format($article->ART_PRIX_VENTE ?? 0, 2) . ' DH</td>
                    <td class="price-cell">' . number_format($article->ART_PRIX_ACHAT ?? 0, 2) . ' DH</td>
                    <td class="stock-cell">' . $article->stock_total . '</td>
                    <td class="status-cell">' . htmlspecialchars($article->statut_display) . '</td>
                </tr>';
            }

            $html .= '</tbody>
                </table>
                
                <div class="footer">
                    <p><strong>AccessPos Pro</strong> - Système de Gestion Professionnel</p>
                    <p>Document généré le ' . date('d/m/Y à H:i:s') . '</p>
                    <p>Total: <strong>' . count($articles) . '</strong> articles listés</p>
                </div>
            </body>
            </html>';

            // Return HTML response that will open in a new window for printing
            return response($html)
                ->header('Content-Type', 'text/html; charset=UTF-8')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

        } catch (\Exception $e) {
            Log::error('Error in exportPDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de l\'export PDF: ' . $e->getMessage());
        }
    }

    /**
     * Print articles list
     */
    public function printArticles(Request $request)
    {
        try {
            // Get the same data as exportExcel
            $search = $request->get('search');
            $famille = $request->get('famille');
            $statut = $request->get('statut');
            $stock_filter = $request->get('stock_filter');

            // Build the query
            $query = DB::table('ARTICLE as a')
                ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
                ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
                ->select([
                    'a.ART_REF',
                    'a.ART_DESIGNATION',
                    'a.ART_PRIX_VENTE',
                    'a.ART_PRIX_ACHAT',
                    'a.ART_STOCK_MIN',
                    'a.ART_VENTE',
                    'f.FAM_LIB as famille',
                    DB::raw('CASE 
                        WHEN a.ART_VENTE = 1 THEN \'Actif\' 
                        ELSE \'Inactif\' 
                    END as statut_display')
                ]);

            // Apply filters
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('a.ART_DESIGNATION', 'LIKE', "%{$search}%")
                      ->orWhere('a.ART_REF', 'LIKE', "%{$search}%");
                });
            }

            if ($famille) {
                $query->where('f.FAM_REF', $famille);
            }

            if ($statut !== null) {
                $query->where('a.ART_VENTE', $statut);
            }

            $articles = $query->orderBy('a.ART_DESIGNATION')->get();

            // Add stock information
            foreach ($articles as $article) {
                $stock = DB::table('STOCK')
                    ->where('ART_REF', $article->ART_REF)
                    ->sum('STK_QTE');
                $article->stock_total = $stock ?? 0;
            }

            // Apply stock filter
            if ($stock_filter) {
                $articles = $articles->filter(function($article) use ($stock_filter) {
                    if ($stock_filter === 'faible') {
                        return $article->stock_total <= $article->ART_STOCK_MIN && $article->stock_total > 0;
                    } elseif ($stock_filter === 'rupture') {
                        return $article->stock_total <= 0;
                    }
                    return true;
                });
            }

            // Create print-optimized HTML
            $html = '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Impression - Articles</title>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        margin: 0; 
                        padding: 15px;
                        font-size: 11px;
                        background: white;
                    }
                    .header { 
                        text-align: center; 
                        margin-bottom: 25px; 
                        border-bottom: 2px solid #333;
                        padding-bottom: 15px;
                    }
                    .header h1 { 
                        color: #333; 
                        margin: 0;
                        font-size: 20px;
                        text-transform: uppercase;
                    }
                    .header-info { 
                        color: #666; 
                        margin-top: 10px;
                        background: #f8f9fa;
                        padding: 8px;
                        border-radius: 5px;
                        font-size: 10px;
                    }
                    table { 
                        width: 100%; 
                        border-collapse: collapse; 
                        margin-top: 15px;
                        font-size: 9px;
                    }
                    th, td { 
                        border: 1px solid #333; 
                        padding: 4px; 
                        text-align: left; 
                        vertical-align: top;
                    }
                    th { 
                        background-color: #f0f0f0; 
                        font-weight: bold;
                        text-align: center;
                        font-size: 8px;
                        text-transform: uppercase;
                    }
                    tr:nth-child(even) { 
                        background-color: #f9f9f9; 
                    }
                    .footer {
                        margin-top: 20px;
                        text-align: center;
                        font-size: 8px;
                        color: #666;
                        border-top: 1px solid #ccc;
                        padding-top: 10px;
                    }
                    .ref-cell { font-weight: bold; color: #007bff; }
                    .price-cell { text-align: right; font-weight: bold; }
                    .stock-cell { text-align: center; }
                    .status-cell { text-align: center; font-weight: bold; }
                    .no-print { display: none; }
                    @page { margin: 1cm; }
                    @media print {
                        body { margin: 0; padding: 10px; }
                        .no-print { display: none !important; }
                        table { font-size: 8px; }
                        th, td { padding: 3px; }
                        .header h1 { font-size: 16px; }
                    }
                </style>
                <script>
                    window.onload = function() {
                        setTimeout(function() {
                            window.print();
                        }, 1000);
                    };
                </script>
            </head>
            <body>
                <div class="header">
                    <h1>📋 LISTE COMPLÈTE DES ARTICLES</h1>
                    <div class="header-info">
                        <p><strong>Date d\'impression:</strong> ' . date('d/m/Y à H:i:s') . '</p>
                        <p><strong>Nombre d\'articles:</strong> ' . count($articles) . '</p>
                        <p><strong>AccessPos Pro</strong> - Système de Gestion des Stocks</p>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th style="width: 12%;">Référence</th>
                            <th style="width: 28%;">Nom du Produit</th>
                            <th style="width: 15%;">Catégorie</th>
                            <th style="width: 12%;">Prix Vente</th>
                            <th style="width: 12%;">Prix Achat</th>
                            <th style="width: 10%;">Stock</th>
                            <th style="width: 11%;">Statut</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($articles as $article) {
                $html .= '<tr>
                    <td class="ref-cell">' . htmlspecialchars($article->ART_REF) . '</td>
                    <td><strong>' . htmlspecialchars($article->ART_DESIGNATION) . '</strong></td>
                    <td>' . htmlspecialchars($article->famille ?? 'Non définie') . '</td>
                    <td class="price-cell">' . number_format($article->ART_PRIX_VENTE ?? 0, 2) . ' DH</td>
                    <td class="price-cell">' . number_format($article->ART_PRIX_ACHAT ?? 0, 2) . ' DH</td>
                    <td class="stock-cell">' . $article->stock_total . '</td>
                    <td class="status-cell">' . htmlspecialchars($article->statut_display) . '</td>
                </tr>';
            }

            $html .= '</tbody>
                </table>
                
                <div class="footer">
                    <p><strong>AccessPos Pro</strong> - Système de Gestion Professionnel</p>
                    <p>Document généré le ' . date('d/m/Y à H:i:s') . '</p>
                    <p>Total: <strong>' . count($articles) . '</strong> articles listés</p>
                </div>
            </body>
            </html>';

            // Return HTML response optimized for printing
            return response($html)
                ->header('Content-Type', 'text/html; charset=UTF-8')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

        } catch (\Exception $e) {
            Log::error('Error in printArticles: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de l\'impression: ' . $e->getMessage());
        }
    }

    /**
     * Analytics d'un article
     */
    /**
     * Page d'analytics générale pour tous les articles
     */
    public function analyticsDashboard(Request $request)
    {
        try {
            $period = $request->get('period', 30);
            $famille = $request->get('famille');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');

            // استخدام معايير البحث
            $dateFilter = $this->getDateFilter($period, $dateFrom, $dateTo);

            // إحصائيات عامة
            $analytics = [
                'total_products' => DB::table('ARTICLE')->count(),
                'active_products' => DB::table('ARTICLE')->where('ART_VENTE', 1)->count(),
                'new_products' => DB::table('ARTICLE')
                    ->whereDate('ART_DATE_CREATION', '>=', now()->subDays(30))
                    ->count(),
                'low_stock_products' => DB::table('STOCK as s')
                    ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                    ->whereRaw('s.STK_QTE <= a.ART_STOCK_MIN')
                    ->count(),
                'total_stock_value' => DB::table('STOCK as s')
                    ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                    ->sum(DB::raw('s.STK_QTE * a.ART_PRIX_VENTE')),
            ];

            // أفضل المنتجات مبيعاً
            $analytics['top_selling'] = DB::table('FACTURE_VNT_DETAIL as fvd')
                ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
                ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                ->whereBetween('fv.FCTV_DATE', $dateFilter)
                ->when($famille, function($query, $famille) {
                    return $query->join('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
                                 ->where('sf.FAM_REF', $famille);
                })
                ->select('a.ART_REF', 'a.ART_DESIGNATION')
                ->selectRaw('SUM(fvd.FVD_QTE) as total_sold')
                ->selectRaw('SUM(fvd.FVD_PRIX_VNT_TTC * fvd.FVD_QTE) as total_revenue')
                ->groupBy('a.ART_REF', 'a.ART_DESIGNATION')
                ->orderByDesc('total_sold')
                ->limit(10)
                ->get();

            // أقل المنتجات مبيعاً
            $analytics['low_selling'] = DB::table('ARTICLE as a')
                ->leftJoin('FACTURE_VNT_DETAIL as fvd', 'a.ART_REF', '=', 'fvd.ART_REF')
                ->leftJoin('FACTURE_VNT as fv', function($join) use ($dateFilter) {
                    $join->on('fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                         ->whereBetween('fv.FCTV_DATE', $dateFilter);
                })
                ->when($famille, function($query, $famille) {
                    return $query->join('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
                                 ->where('sf.FAM_REF', $famille);
                })
                ->select('a.ART_REF', 'a.ART_DESIGNATION')
                ->selectRaw('ISNULL(SUM(fvd.FVD_QTE), 0) as total_sold')
                ->selectRaw('ISNULL(SUM(fvd.FVD_PRIX_VNT_TTC * fvd.FVD_QTE), 0) as total_revenue')
                ->groupBy('a.ART_REF', 'a.ART_DESIGNATION')
                ->orderBy('total_sold')
                ->limit(10)
                ->get();

            // توزيع المنتجات حسب العائلة
            $analytics['family_distribution'] = DB::table('ARTICLE as a')
                ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
                ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
                ->select('f.FAM_LIB as famille')
                ->selectRaw('COUNT(a.ART_REF) as count')
                ->groupBy('f.FAM_REF', 'f.FAM_LIB')
                ->get();

            // تطور المبيعات
            $analytics['sales_trend'] = DB::table('FACTURE_VNT as fv')
                ->selectRaw('CAST(fv.FCTV_DATE as DATE) as date')
                ->selectRaw('SUM(fv.FCTV_MNT_TTC) as total')
                ->whereBetween('fv.FCTV_DATE', $dateFilter)
                ->groupByRaw('CAST(fv.FCTV_DATE as DATE)')
                ->orderBy('date')
                ->get();

            // حالة المخزون
            $analytics['stock_status'] = [
                'normal' => DB::table('STOCK as s')
                    ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                    ->whereRaw('s.STK_QTE > a.ART_STOCK_MIN AND s.STK_QTE < a.ART_STOCK_MAX')
                    ->count(),
                'low' => DB::table('STOCK as s')
                    ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                    ->whereRaw('s.STK_QTE <= a.ART_STOCK_MIN AND s.STK_QTE > 0')
                    ->count(),
                'out' => DB::table('STOCK as s')
                    ->where('s.STK_QTE', '<=', 0)
                    ->count(),
                'over' => DB::table('STOCK as s')
                    ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                    ->whereRaw('s.STK_QTE >= a.ART_STOCK_MAX')
                    ->count(),
            ];

            // النسب المئوية
            $analytics['active_percentage'] = $analytics['total_products'] > 0 ? 
                round(($analytics['active_products'] / $analytics['total_products']) * 100, 1) : 0;

            // الحصول على العائلات للفلتر
            $familles = DB::table('FAMILLE')->orderBy('FAM_LIB')->get();

            return view('admin.articles.analytics-sb-admin', compact('analytics', 'familles'));

        } catch (\Exception $e) {
            return redirect()->route('admin.articles.index')
                ->with('error', 'حدث خطأ في تحميل التحليلات: ' . $e->getMessage());
        }
    }

    // ============= MÉTHODES PRIVÉES =============

    /**
     * Vérifier si l'utilisateur est admin
     */
    private function isAdmin()
    {
        return Auth::check() && Auth::user()->isAdmin();
    }

    /**
     * Générer une nouvelle référence d'article
     */
    private function generateArticleRef()
    {
        try {
            // نهج بسيط وموثوق: استخدام التاريخ والوقت مع رقم عشوائي
            $timestamp = date('YmdHis');
            $random = rand(100, 999);
            $candidateRef = "ART{$timestamp}{$random}";
            
            // التحقق من عدم وجود المرجع (احتمال ضئيل جداً)
            $maxAttempts = 5;
            $attempts = 0;
            
            while ($attempts < $maxAttempts) {
                $exists = DB::table('ARTICLE')->where('ART_REF', $candidateRef)->exists();
                if (!$exists) {
                    Log::info("Generated new article reference: $candidateRef");
                    return $candidateRef;
                }
                
                // في حالة نادرة جداً من التكرار، زيادة الرقم العشوائي
                $random++;
                $candidateRef = "ART{$timestamp}{$random}";
                $attempts++;
            }
            
            // في أسوأ الحالات، استخدام microtime للتفرد التام
            $microRef = 'ART' . str_replace('.', '', microtime(true));
            Log::warning("Used microtime reference due to collision: $microRef");
            return $microRef;
            
        } catch (\Exception $e) {
            Log::error('Erreur dans generateArticleRef: ' . $e->getMessage());
            
            // En cas d'erreur complete, utiliser une methode de secours
            $fallbackRef = 'ART' . time() . rand(1000, 9999);
            Log::error("Used fallback reference: $fallbackRef");
            return $fallbackRef;
        }
    }

    /**
     * Statistiques générales des articles
     */
    private function getArticlesStats()
    {
        try {
            $total = DB::table('ARTICLE')->count();
            $active = DB::table('ARTICLE')->where('ART_VENTE', 1)->count();
            
            // Calculer stock faible et valeur du stock
            $lowStockCount = 0;
            $stockValue = 0;
            
            $articles = DB::table('ARTICLE')->where('ART_STOCKABLE', 1)->get();
            
            foreach ($articles as $article) {
                $stock = DB::table('STOCK')
                    ->where('ART_REF', $article->ART_REF)
                    ->sum('STK_QTE') ?? 0;
                
                if ($stock <= $article->ART_STOCK_MIN && $stock > 0) {
                    $lowStockCount++;
                }
                
                // Use prix_vente instead of prix_achat for stock value, and ensure positive values
                if ($article->ART_PRIX_VENTE && $stock > 0) {
                    $stockValue += $stock * $article->ART_PRIX_VENTE;
                }
            }
            
            return [
                'total' => $total,
                'active' => $active,
                'low_stock' => $lowStockCount,
                'stock_value' => max(0, $stockValue), // Ensure positive value
            ];
        } catch (\Exception $e) {
            Log::error('Erreur dans getArticlesStats: ' . $e->getMessage());
            return [
                'total' => 0,
                'active' => 0,
                'low_stock' => 0,
                'stock_value' => 0,
            ];
        }
    }

    /**
     * Statistiques de vente d'un article
     */
    private function getArticleVentesStats($artRef)
    {
        try {
            return [
                'total_ventes' => DB::table('FACTURE_VNT_DETAIL as fvd')
                    ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                    ->where('fvd.ART_REF', $artRef)
                    ->sum('fvd.FVD_QTE') ?? 0,

                'ca_total' => DB::table('FACTURE_VNT_DETAIL as fvd')
                    ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                    ->where('fvd.ART_REF', $artRef)
                    ->sum(DB::raw('fvd.FVD_QTE * fvd.FVD_PRIX_VNT_TTC')) ?? 0,

                'ventes_mois' => DB::table('FACTURE_VNT_DETAIL as fvd')
                    ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                    ->where('fvd.ART_REF', $artRef)
                    ->where('fv.FCTV_DATE', '>=', now()->startOfMonth())
                    ->sum('fvd.FVD_QTE') ?? 0,

                'derniere_vente' => DB::table('FACTURE_VNT_DETAIL as fvd')
                    ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                    ->where('fvd.ART_REF', $artRef)
                    ->orderBy('fv.FCTV_DATE', 'desc')
                    ->value('fv.FCTV_DATE'),
            ];
        } catch (\Exception $e) {
            return [
                'total_ventes' => 0,
                'ca_total' => 0,
                'ventes_mois' => 0,
                'derniere_vente' => null,
            ];
        }
    }

    /**
     * Mouvements de stock d'un article
     */
    private function getArticleStockMovements($artRef)
    {
        try {
            // Retourner les 10 dernières ventes
            return DB::table('FACTURE_VNT_DETAIL as fvd')
                ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                ->join('CLIENT as c', 'fv.CLT_REF', '=', 'c.CLT_REF')
                ->where('fvd.ART_REF', $artRef)
                ->select([
                    'fv.FCTV_DATE as date',
                    'fv.FCTV_NUMERO as numero',
                    'c.CLT_CLIENT as client',
                    'fvd.FVD_QTE as quantite',
                    'fvd.FVD_PRIX_VNT_TTC as prix',
                    DB::raw('"Vente" as type')
                ])
                ->orderBy('fv.FCTV_DATE', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Ventes sur 30 jours
     */
    private function getVentes30Jours($artRef)
    {
        try {
            return DB::table('FACTURE_VNT_DETAIL as fvd')
                ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                ->where('fvd.ART_REF', $artRef)
                ->where('fv.FCTV_DATE', '>=', now()->subDays(30))
                ->selectRaw('CAST(fv.FCTV_DATE as DATE) as date')
                ->selectRaw('SUM(fvd.FVD_QTE) as quantite')
                ->selectRaw('SUM(fvd.FVD_QTE * fvd.FVD_PRIX_VNT_TTC) as ca')
                ->groupByRaw('CAST(fv.FCTV_DATE as DATE)')
                ->orderBy('date')
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Top clients pour cet article
     */
    private function getTopClients($artRef)
    {
        try {
            return DB::table('FACTURE_VNT_DETAIL as fvd')
                ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                ->join('CLIENT as c', 'fv.CLT_REF', '=', 'c.CLT_REF')
                ->where('fvd.ART_REF', $artRef)
                ->select([
                    'c.CLT_CLIENT as nom',
                    DB::raw('SUM(fvd.FVD_QTE) as total_achete'),
                    DB::raw('SUM(fvd.FVD_QTE * fvd.FVD_PRIX_VNT_TTC) as ca_client')
                ])
                ->groupBy('c.CLT_REF', 'c.CLT_CLIENT')
                ->orderByDesc('total_achete')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Performance mensuelle
     */
    private function getPerformanceMensuelle($artRef)
    {
        try {
            return DB::table('FACTURE_VNT_DETAIL as fvd')
                ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                ->where('fvd.ART_REF', $artRef)
                ->where('fv.FCTV_DATE', '>=', now()->subMonths(12))
                ->selectRaw('YEAR(fv.FCTV_DATE) as annee')
                ->selectRaw('MONTH(fv.FCTV_DATE) as mois')
                ->selectRaw('SUM(fvd.FVD_QTE) as quantite')
                ->selectRaw('SUM(fvd.FVD_QTE * fvd.FVD_PRIX_VNT_TTC) as ca')
                ->groupByRaw('YEAR(fv.FCTV_DATE), MONTH(fv.FCTV_DATE)')
                ->orderByRaw('YEAR(fv.FCTV_DATE), MONTH(fv.FCTV_DATE)')
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Analyse des marges
     */
    private function getMarginAnalysis($artRef)
    {
        try {
            $article = DB::table('ARTICLE')->where('ART_REF', $artRef)->first();
            
            if (!$article || !$article->ART_PRIX_ACHAT || !$article->ART_PRIX_VENTE) {
                return null;
            }

            $marge = $article->ART_PRIX_VENTE - $article->ART_PRIX_ACHAT;
            $pourcentage = ($marge / $article->ART_PRIX_VENTE) * 100;

            return [
                'prix_achat' => $article->ART_PRIX_ACHAT,
                'prix_vente' => $article->ART_PRIX_VENTE,
                'marge' => $marge,
                'pourcentage' => round($pourcentage, 2),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Évolution du stock
     */
    private function getStockEvolution($artRef)
    {
        try {
            // Stock actuel
            $stockActuel = DB::table('STOCK')
                ->where('ART_REF', $artRef)
                ->sum('STK_QTE') ?? 0;

            return [
                'stock_actuel' => $stockActuel,
                'derniers_mouvements' => $this->getArticleStockMovements($artRef)
            ];
        } catch (\Exception $e) {
            return ['stock_actuel' => 0, 'derniers_mouvements' => collect([])];
        }
    }

    /**
     * Calculer les analytics générales pour tous les articles
     */
    private function getGeneralAnalytics(Request $request)
    {
        $period = $request->get('period', '30');
        $famille = $request->get('famille');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        // Définir la période d'analyse
        if ($period === 'custom' && $dateFrom && $dateTo) {
            $startDate = $dateFrom;
            $endDate = $dateTo;
        } else {
            $days = (int) $period;
            $startDate = now()->subDays($days)->format('Y-m-d');
            $endDate = now()->format('Y-m-d');
        }

        try {
            // Statistiques générales
            $totalProducts = DB::table('ARTICLE')->count();
            $activeProducts = DB::table('ARTICLE')->where('ART_VENTE', 1)->count();
            $newProducts = DB::table('ARTICLE')
                ->where('ART_DATE_CREATION', '>=', $startDate)
                ->count();
            
            // Produits avec stock faible
            $lowStockProducts = DB::table('STOCK as s')
                ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                ->where('a.ART_STOCKABLE', 1)
                ->whereRaw('s.STK_QTE <= a.ART_STOCK_MIN')
                ->count();
            
            // Valeur totale du stock
            $totalStockValue = DB::table('STOCK as s')
                ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                ->where('a.ART_STOCKABLE', 1)
                ->sum(DB::raw('s.STK_QTE * a.ART_PRIX_ACHAT')) ?? 0;
            
            // Top produits vendus
            $topSellingQuery = DB::table('FACTURE_VNT_DETAIL as fvd')
                ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
                ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                ->select('a.ART_REF', 'a.ART_DESIGNATION')
                ->selectRaw('SUM(fvd.FVD_QTE) as total_sold')
                ->selectRaw('SUM(fvd.FVD_QTE * fvd.FVD_PRIX_VNT_TTC) as total_revenue')
                ->whereBetween('fv.FCTV_DATE', [$startDate, $endDate]);
            
            if ($famille) {
                $topSellingQuery->join('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
                    ->where('sf.FAM_REF', $famille);
            }
            
            $topSelling = $topSellingQuery
                ->groupBy('a.ART_REF', 'a.ART_DESIGNATION')
                ->orderByDesc('total_sold')
                ->limit(10)
                ->get();
            
            // Produits peu vendus
            $lowSelling = $topSellingQuery
                ->groupBy('a.ART_REF', 'a.ART_DESIGNATION')
                ->orderBy('total_sold')
                ->limit(10)
                ->get();
            
            // Distribution par famille
            $familyDistribution = DB::table('ARTICLE as a')
                ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
                ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
                ->select('f.FAM_LIB as famille')
                ->selectRaw('COUNT(a.ART_REF) as count')
                ->groupBy('f.FAM_REF', 'f.FAM_LIB')
                ->orderByDesc('count')
                ->get();
            
            // Évolution des ventes
            $salesTrend = DB::table('FACTURE_VNT_DETAIL as fvd')
                ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                ->selectRaw('CAST(fv.FCTV_DATE as DATE) as date')
                ->selectRaw('SUM(fvd.FVD_QTE * fvd.FVD_PRIX_VNT_TTC) as total')
                ->whereBetween('fv.FCTV_DATE', [$startDate, $endDate])
                ->groupByRaw('CAST(fv.FCTV_DATE as DATE)')
                ->orderBy('date')
                ->get();
            
            // État du stock
            $stockStatus = [
                'normal' => DB::table('STOCK as s')
                    ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                    ->where('a.ART_STOCKABLE', 1)
                    ->whereRaw('s.STK_QTE > a.ART_STOCK_MIN')
                    ->whereRaw('s.STK_QTE < a.ART_STOCK_MAX')
                    ->count(),
                'low' => $lowStockProducts,
                'out' => DB::table('STOCK as s')
                    ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                    ->where('a.ART_STOCKABLE', 1)
                    ->where('s.STK_QTE', '<=', 0)
                    ->count(),
                'over' => DB::table('STOCK as s')
                    ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
                    ->where('a.ART_STOCKABLE', 1)
                    ->whereRaw('s.STK_QTE >= a.ART_STOCK_MAX')
                    ->count(),
            ];
            
            return [
                'total_products' => $totalProducts,
                'active_products' => $activeProducts,
                'new_products' => $newProducts,
                'active_percentage' => $totalProducts > 0 ? round(($activeProducts / $totalProducts) * 100, 1) : 0,
                'low_stock_products' => $lowStockProducts,
                'total_stock_value' => $totalStockValue,
                'top_selling' => $topSelling,
                'low_selling' => $lowSelling,
                'family_distribution' => $familyDistribution,
                'sales_trend' => $salesTrend,
                'stock_status' => $stockStatus,
                'normal_stock' => $stockStatus['normal'],
                'low_stock' => $stockStatus['low'],
                'out_of_stock' => $stockStatus['out'],
                'overstock' => $stockStatus['over'],
            ];
            
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des valeurs par défaut
            return [
                'total_products' => 0,
                'active_products' => 0,
                'new_products' => 0,
                'active_percentage' => 0,
                'low_stock_products' => 0,
                'total_stock_value' => 0,
                'top_selling' => collect([]),
                'low_selling' => collect([]),
                'family_distribution' => collect([]),
                'sales_trend' => collect([]),
                'stock_status' => ['normal' => 0, 'low' => 0, 'out' => 0, 'over' => 0],
                'normal_stock' => 0,
                'low_stock' => 0,
                'out_of_stock' => 0,
                'overstock' => 0,
            ];
        }
    }

    /**
     * Get date filter for analytics
     */
    private function getDateFilter($period, $dateFrom, $dateTo)
    {
        if ($period === 'custom' && $dateFrom && $dateTo) {
            return [$dateFrom, $dateTo];
        }

        $start = now()->subDays($period)->format('Y-m-d');
        $end = now()->format('Y-m-d');

        return [$start, $end];
    }
}
