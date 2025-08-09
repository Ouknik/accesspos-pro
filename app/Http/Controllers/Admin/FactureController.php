<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\FacturesExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class FactureController extends Controller
{
    /**
     * Afficher la liste des factures avec filtres
     */
    public function index(Request $request)
    {
        try {
            $filters = [
                'status' => $request->get('status', 'all'),
                'client' => $request->get('client', 'all'),
                'serveur' => $request->get('serveur', 'all'),
                'mode_paiement' => $request->get('mode_paiement', 'all'),
                'date' => $request->get('date', 'today'),
                'date_debut' => $request->get('date_debut'),
                'date_fin' => $request->get('date_fin'),
                'search' => $request->get('search', '')
            ];

            $factures = $this->getFactures($filters);
            $clients = $this->getClients();
            $serveurs = $this->getServeurs();
            $modesPaiement = $this->getModesPaiement();
            $stats = $this->getFactureStats($filters);

            return view('admin.factures.index', compact('factures', 'clients', 'serveurs', 'modesPaiement', 'stats', 'filters'));

        } catch (Exception $e) {
            return back()->with('error', 'Erreur lors du chargement des factures: ' . $e->getMessage());
        }
    }

    /**
     * Afficher le formulaire de création d'une nouvelle facture
     */
    public function create(Request $request)
    {
        try {
            $cmdRef = $request->get('cmd_ref'); // Si création depuis une commande
            $commande = null;
            
            if ($cmdRef) {
                $commande = $this->getCommandeDetails($cmdRef);
            }

            $clients = $this->getClients();
            $articles = $this->getArticles();
            $nouveauNumero = $this->generateNewFactureNumber();

            return view('admin.factures.create', compact('commande', 'clients', 'articles', 'nouveauNumero'));

        } catch (Exception $e) {
            return back()->with('error', 'Erreur lors de l\'ouverture du formulaire: ' . $e->getMessage());
        }
    }

    /**
     * Enregistrer une nouvelle facture
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'client_ref' => 'required',
                'articles' => 'required|array|min:1',
                'articles.*.art_ref' => 'required',
                'articles.*.quantite' => 'required|numeric|min:0.01',
                'articles.*.prix' => 'required|numeric|min:0',
                'mode_paiement' => 'required',
                'montant_espece' => 'nullable|numeric|min:0',
                'montant_carte' => 'nullable|numeric|min:0',
                'montant_credit' => 'nullable|numeric|min:0'
            ]);

            DB::beginTransaction();

            // Vérifier si c'est un brouillon
            $isDraft = $request->has('save_as_draft');
            $isValidated = !$isDraft;

            // Générer référence facture unique
            $factureRef = $this->generateFactureRef();
            $numeroFacture = $this->generateNewFactureNumber();

            // Calculer les totaux
            $montantHT = 0;
            $montantTTC = 0;
            
            foreach ($request->articles as $article) {
                $totalLigne = $article['quantite'] * $article['prix'];
                $montantTTC += $totalLigne;
                $montantHT += $totalLigne / 1.2; // Supposer TVA 20%
            }

            // Appliquer remise globale si présente
            $remiseGlobale = $request->input('remise_globale', 0);
            $montantTTC -= $remiseGlobale;
            $montantHT -= $remiseGlobale / 1.2;

            // Insérer la facture principale
            DB::table('FACTURE_VNT')->insert([
                'FCTV_REF' => $factureRef,
                'ETP_REF' => 'ETP001', // Valeur par défaut
                'CLT_REF' => $request->client_ref,
                'FCTV_NUMERO' => $numeroFacture,
                'FCTV_DATE' => now(),
                'FCTV_MNT_HT' => $montantHT,
                'FCTV_MNT_TTC' => $montantTTC,
                'FCTV_REMISE' => $remiseGlobale,
                'FCTV_MODEPAIEMENT' => $request->mode_paiement,
                'FCTV_SERVEUR' => auth()->user()->name ?? 'Admin',
                'TAB_REF' => $this->getValidTableRef($request->table_ref), // Vérifier la table
                'FCTV_VALIDE' => $isValidated ? 1 : 0, // 0 pour brouillon
                'FCTV_EXONORE' => $request->has('exonore') ? 1 : 0,
                'FCTV_ETAT' => 1,
                'FCT_MNT_TOTAL' => $montantTTC,
                'FCT_MNT_RGL' => $montantTTC,
                'MontantEspece' => $request->input('montant_espece', 0),
                'MontantCharte' => $request->input('montant_carte', 0),
                'MontantCredit' => $request->input('montant_credit', 0),
                'MontantCheque' => $request->input('montant_cheque', 0),
                'FCTV_RENDU' => $request->input('montant_rendu', 0),
                'FCTV_REMARQUE' => $request->input('remarque', '') . ($isDraft ? ' [BROUILLON]' : ''),
                'CSS_ID_CAISSE' => $this->getValidCaisseRef()
            ]);

            // Insérer les détails de la facture
            foreach ($request->articles as $index => $article) {
                DB::table('FACTURE_VNT_DETAIL')->insert([
                    'FCTV_REF' => $factureRef,
                    'ART_REF' => $this->getValidArticleRef($article['art_ref']),
                    'FVD_QTE' => $article['quantite'],
                    'FVD_PRIX_VNT_HT' => $article['prix'] / 1.2,
                    'FVD_PRIX_VNT_TTC' => $article['prix'],
                    'FVD_REMISE' => $article['remise'] ?? 0,
                    'FVD_TVA' => 20,
                    'FVD_COLISAGE' => 1,
                    'FVD_NBR_COLIS' => $article['quantite'],
                    'FVD_NBR__GRATUITE' => 0,
                    'FVD_NUMBL' => '',
                    'TRF_LIBELLE' => 'Normal',
                    'IsMenu' => 0,
                    'NameMenu' => ''
                ]);
            }

            // Si création depuis une commande, créer le lien
            if ($request->filled('cmd_ref')) {
                DB::table('CMD_OF_FACTURE')->insert([
                    'FCTV_REF' => $factureRef,
                    'CMD_REF' => $request->cmd_ref
                ]);

                // Marquer la commande comme facturée seulement si validée
                if ($isValidated) {
                    DB::table('CMD_VENTE')
                        ->where('CMD_REF', $request->cmd_ref)
                        ->update(['DVS_ETAT' => 'Facturé']);
                }
            }

            DB::commit();

            $message = $isDraft 
                ? 'Brouillon sauvegardé avec succès! N° ' . $numeroFacture
                : 'Facture créée avec succès! N° ' . $numeroFacture;

            return redirect()->route('admin.factures.show', $factureRef)
                           ->with('success', $message);

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'une facture
     */
    public function show($factureRef)
    {
        try {
            $facture = $this->getFactureDetails($factureRef);
            
            if (!$facture) {
                return redirect()->route('admin.factures.index')
                               ->with('error', 'Facture introuvable avec la référence: ' . $factureRef);
            }

            return view('admin.factures.show', compact('facture'));

        } catch (Exception $e) {
            \Log::error('Erreur lors du chargement de la facture ' . $factureRef . ': ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du chargement: ' . $e->getMessage());
        }
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit($factureRef)
    {
        try {
            $facture = $this->getFactureDetails($factureRef);
            
            if (!$facture || $facture['facture']->FCTV_VALIDE != 1) {
                return redirect()->route('admin.factures.index')
                               ->with('error', 'Facture non modifiable');
            }

            $clients = $this->getClients();
            $articles = $this->getArticles();

            return view('admin.factures.edit', compact('facture', 'clients', 'articles'));

        } catch (Exception $e) {
            return back()->with('error', 'Erreur lors du chargement: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour une facture
     */
    public function update(Request $request, $factureRef)
    {
        try {
            // Validation similaire à store()
            $request->validate([
                'client_ref' => 'required',
                'articles' => 'required|array|min:1',
                'mode_paiement' => 'required'
            ]);

            DB::beginTransaction();

            // Vérifier que la facture peut être modifiée
            $facture = DB::table('FACTURE_VNT')->where('FCTV_REF', $factureRef)->first();
            if (!$facture || $facture->FCTV_VALIDE != 1) {
                throw new Exception('Facture non modifiable');
            }

            // Supprimer les anciens détails
            DB::table('FACTURE_VNT_DETAIL')->where('FCTV_REF', $factureRef)->delete();

            // Recalculer les totaux
            $montantHT = 0;
            $montantTTC = 0;
            
            foreach ($request->articles as $article) {
                $totalLigne = $article['quantite'] * $article['prix'];
                $montantTTC += $totalLigne;
                $montantHT += $totalLigne / 1.2;
            }

            $remiseGlobale = $request->input('remise_globale', 0);
            $montantTTC -= $remiseGlobale;
            $montantHT -= $remiseGlobale / 1.2;

            // Mettre à jour la facture
            DB::table('FACTURE_VNT')->where('FCTV_REF', $factureRef)->update([
                'FCTV_MNT_HT' => $montantHT,
                'FCTV_MNT_TTC' => $montantTTC,
                'FCT_MNT_TOTAL' => $montantTTC,
                'FCT_MNT_RGL' => $montantTTC,
                'FCTV_REMISE' => $remiseGlobale,
                'FCTV_MODEPAIEMENT' => $request->mode_paiement,
                'CLT_REF' => $request->client_ref,
                'TAB_REF' => $this->getValidTableRef($request->table_ref),
                'FCTV_EXONORE' => $request->has('exonore') ? 1 : 0,
                'MontantEspece' => $request->input('montant_espece', 0),
                'MontantCharte' => $request->input('montant_carte', 0),
                'MontantCredit' => $request->input('montant_credit', 0),
                'FCTV_RENDU' => $request->input('montant_rendu', 0),
                'FCTV_REMARQUE' => $request->input('remarque', '')
            ]);

            // Réinsérer les nouveaux détails
            foreach ($request->articles as $index => $article) {
                DB::table('FACTURE_VNT_DETAIL')->insert([
                    'FCTV_REF' => $factureRef,
                    'ART_REF' => $this->getValidArticleRef($article['art_ref']),
                    'FVD_QTE' => $article['quantite'],
                    'FVD_PRIX_VNT_HT' => $article['prix'] / 1.2,
                    'FVD_PRIX_VNT_TTC' => $article['prix'],
                    'FVD_REMISE' => $article['remise'] ?? 0,
                    'FVD_TVA' => 20,
                    'FVD_COLISAGE' => 1,
                    'FVD_NBR_COLIS' => $article['quantite'],
                    'FVD_NBR__GRATUITE' => 0,
                    'FVD_NUMBL' => '',
                    'TRF_LIBELLE' => 'Normal',
                    'IsMenu' => 0,
                    'NameMenu' => ''
                ]);
            }

            DB::commit();

            return redirect()->route('admin.factures.show', $factureRef)
                           ->with('success', 'Facture mise à jour avec succès!');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer (annuler) une facture
     */
    public function destroy($factureRef)
    {
        try {
            DB::beginTransaction();

            // Marquer la facture comme annulée
            $updated = DB::table('FACTURE_VNT')
                ->where('FCTV_REF', $factureRef)
                ->update([
                    'FCTV_ETAT' => 0,
                    'FCTV_REMARQUE' => DB::raw("COALESCE(FCTV_REMARQUE, '') + ' [ANNULÉE LE " . now()->format('d/m/Y H:i') . "]'")
                ]);

            if (!$updated) {
                throw new Exception('Facture introuvable');
            }

            // Si liée à une commande, remettre la commande en état initial
            $cmdRefs = DB::table('CMD_OF_FACTURE')
                ->where('FCTV_REF', $factureRef)
                ->pluck('CMD_REF');

            foreach ($cmdRefs as $cmdRef) {
                DB::table('CMD_VENTE')
                    ->where('CMD_REF', $cmdRef)
                    ->update(['DVS_ETAT' => 'Terminé']);
            }

            DB::commit();

            return redirect()->route('admin.factures.index')
                           ->with('success', 'Facture annulée avec succès!');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'annulation: ' . $e->getMessage());
        }
    }

    /**
     * Imprimer une facture
     */
    public function print($factureRef)
    {
        try {
            $facture = $this->getFactureDetails($factureRef);
            
            if (!$facture) {
                return view('admin.factures.print-error', compact('factureRef'));
            }

            return view('admin.factures.print', compact('facture'));

        } catch (Exception $e) {
            return view('admin.factures.print-error', ['factureRef' => $factureRef, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Convertir une commande en facture
     */
    public function convertFromCommand($cmdRef)
    {
        try {
            // Vérifier si la commande existe et n'est pas déjà facturée
            $commande = DB::table('CMD_VENTE')->where('CMD_REF', $cmdRef)->first();
            
            if (!$commande) {
                return redirect()->route('admin.factures.index')
                               ->with('error', 'Commande introuvable');
            }

            if ($commande->DVS_ETAT === 'Facturé') {
                return redirect()->route('admin.factures.index')
                               ->with('error', 'Cette commande est déjà facturée');
            }

            return redirect()->route('admin.factures.create', ['cmd_ref' => $cmdRef]);

        } catch (Exception $e) {
            return back()->with('error', 'Erreur lors de la conversion: ' . $e->getMessage());
        }
    }

    /**
     * Exporter les factures
     */
    public function export(Request $request)
    {
        try {
            $format = $request->get('format', 'excel');
            $filters = $request->only(['status', 'client', 'date', 'date_debut', 'date_fin', 'serveur', 'mode_paiement', 'search']);
            
            // Implémentation de l'export selon le format demandé
            // TODO: Implémenter avec Maatwebsite\Excel ou DomPDF
            
            return back()->with('success', 'Export en cours de développement');

        } catch (Exception $e) {
            return back()->with('error', 'Erreur lors de l\'export: ' . $e->getMessage());
        }
    }

    // =================== MÉTHODES PRIVÉES ===================

    /**
     * Récupérer les factures avec filtres
     */
    private function getFactures($filters = [])
    {
        $query = DB::table('FACTURE_VNT as fv')
            ->leftJoin('CLIENT as cl', 'fv.CLT_REF', '=', 'cl.CLT_REF')
            ->leftJoin('FACTURE_VNT_DETAIL as fvd', 'fv.FCTV_REF', '=', 'fvd.FCTV_REF')
            ->select([
                'fv.FCTV_REF',
                'fv.ETP_REF', 
                'fv.CLT_REF',
                'fv.FCTV_NUMERO',
                'fv.FCTV_DATE',
                'fv.FCTV_MNT_HT',
                'fv.FCTV_MNT_TTC',
                'fv.FCTV_REMISE',
                'fv.FCTV_SERVEUR',
                'fv.FCTV_ETAT',
                'fv.FCTV_VALIDE',
                'fv.FCTV_MODEPAIEMENT',
                'fv.TAB_REF',
                'fv.CSS_ID_CAISSE',
                'fv.MontantEspece',
                'fv.MontantCharte',
                'fv.MontantCredit',
                'fv.MontantCheque',
                'fv.FCTV_RENDU',
                'fv.FCTV_REMARQUE',
                'fv.FCTV_EXONORE',
                DB::raw("COALESCE(cl.CLT_CLIENT, 'Client #' + fv.CLT_REF) as CLIENT_NAME"),
                DB::raw('COUNT(fvd.ART_REF) as NB_ARTICLES'),
                DB::raw('SUM(COALESCE(fvd.FVD_REMISE, 0)) as TOTAL_REMISE_ARTICLES')
            ]);

        // Application des filtres
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            if ($filters['status'] === 'valide') {
                $query->where('fv.FCTV_VALIDE', 1)->where('fv.FCTV_ETAT', 1);
            } elseif ($filters['status'] === 'annule') {
                $query->where('fv.FCTV_ETAT', 0);
            } elseif ($filters['status'] === 'brouillon') {
                $query->where('fv.FCTV_VALIDE', 0);
            }
        }

        if (isset($filters['client']) && $filters['client'] !== 'all') {
            $query->where('fv.CLT_REF', $filters['client']);
        }

        if (isset($filters['serveur']) && $filters['serveur'] !== 'all') {
            $query->where('fv.FCTV_SERVEUR', $filters['serveur']);
        }

        if (isset($filters['mode_paiement']) && $filters['mode_paiement'] !== 'all') {
            $query->where('fv.FCTV_MODEPAIEMENT', $filters['mode_paiement']);
        }

        if (isset($filters['date'])) {
            switch ($filters['date']) {
                case 'today':
                    $query->whereDate('fv.FCTV_DATE', Carbon::today());
                    break;
                case 'week':
                    $query->where('fv.FCTV_DATE', '>=', Carbon::now()->subWeek());
                    break;
                case 'month':
                    $query->where('fv.FCTV_DATE', '>=', Carbon::now()->subMonth());
                    break;
                case 'custom':
                    // الفلترة المخصصة بتاريخين محددين
                    if (isset($filters['date_debut']) && !empty($filters['date_debut'])) {
                        $query->whereDate('fv.FCTV_DATE', '>=', $filters['date_debut']);
                    }
                    if (isset($filters['date_fin']) && !empty($filters['date_fin'])) {
                        $query->whereDate('fv.FCTV_DATE', '<=', $filters['date_fin']);
                    }
                    break;
            }
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('fv.FCTV_NUMERO', 'like', "%{$search}%")
                  ->orWhere('fv.FCTV_REF', 'like', "%{$search}%")
                  ->orWhere('cl.CLT_CLIENT', 'like', "%{$search}%");
            });
        }

        return $query->groupBy([
                'fv.FCTV_REF', 'fv.ETP_REF', 'fv.CLT_REF', 'fv.FCTV_NUMERO', 'fv.FCTV_DATE', 
                'fv.FCTV_MNT_HT', 'fv.FCTV_MNT_TTC', 'fv.FCTV_REMISE', 'fv.FCTV_SERVEUR', 
                'fv.FCTV_ETAT', 'fv.FCTV_VALIDE', 'fv.FCTV_MODEPAIEMENT', 'fv.TAB_REF',
                'fv.CSS_ID_CAISSE', 'fv.MontantEspece', 'fv.MontantCharte', 'fv.MontantCredit',
                'fv.MontantCheque', 'fv.FCTV_RENDU', 'fv.FCTV_REMARQUE', 'fv.FCTV_EXONORE', 'cl.CLT_CLIENT'
            ])
            ->orderBy('fv.FCTV_DATE', 'desc')
            ->get();
    }

    /**
     * Récupérer les détails complets d'une facture
     */
    private function getFactureDetails($factureRef)
    {
        // Informations de la facture principale avec paramètres de caisse
        $facture = DB::table('FACTURE_VNT as fv')
            ->leftJoin('CLIENT as cl', 'fv.CLT_REF', '=', 'cl.CLT_REF')
            ->leftJoin('PARAMETRECAISSE as pc', 'fv.CSS_ID_CAISSE', '=', 'pc.NumCaisse')
            ->select([
                'fv.*',
                DB::raw("COALESCE(cl.CLT_CLIENT, 'Client #' + fv.CLT_REF) as CLIENT_NAME"),
                'cl.CLT_TELEPHONE', 
                'cl.CLT_EMAIL',
                'cl.CLT_RAISONSOCIAL',
                'cl.CLT_CREDIT',
                // Paramètres de caisse pour l'impression
                'pc.Enteteticket1',
                'pc.Enteteticket2',
                'pc.Adresse',
                'pc.Telephone',
                'pc.PiedPage',
                'pc.PiedPage2',
                'pc.RC',
                'pc.IFE',
                'pc.ICE'
            ])
            ->where(function($query) use ($factureRef) {
                // Chercher par FCTV_REF ou par FCTV_NUMERO
                $query->where('fv.FCTV_REF', $factureRef)
                      ->orWhere('fv.FCTV_NUMERO', $factureRef);
            })
            ->first();

        if (!$facture) {
            return null;
        }

        // Utiliser FCTV_REF de la facture trouvée pour les détails
        $factureRefToUse = $facture->FCTV_REF;

        // Détails des articles
        $details = DB::table('FACTURE_VNT_DETAIL as fvd')
            ->leftJoin('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
            ->select([
                'fvd.*',
                DB::raw("COALESCE(a.ART_DESIGNATION, 'Article ' + fvd.ART_REF) as ART_DESIGNATION"),
                DB::raw('(fvd.FVD_QTE * fvd.FVD_PRIX_VNT_TTC) - fvd.FVD_REMISE as TOTAL_LIGNE')
            ])
            ->where('fvd.FCTV_REF', $factureRefToUse)
            ->orderBy('fvd.FVD_ID')
            ->get();

        // Commandes liées (si applicable) - vérifier si des tables de liaison existent
        $commandes = collect(); // Collection vide par défaut
        
        // Vérifier s'il y a des commandes liées via CMD_OF_FACTURE
        try {
            $commandes = DB::table('CMD_OF_FACTURE as cof')
                ->leftJoin('CMD_VENTE as cv', 'cof.CMD_REF', '=', 'cv.CMD_REF')
                ->where('cof.FCTV_REF', $factureRefToUse)
                ->select('cv.*')
                ->get();
        } catch (\Exception $e) {
            // Si les tables n'existent pas, utiliser une collection vide
            $commandes = collect();
        }

        return [
            'facture' => $facture,
            'details' => $details,
            'commandes' => $commandes
        ];
    }

    /**
     * Récupérer les détails d'une commande pour conversion
     */
    private function getCommandeDetails($cmdRef)
    {
        $commande = DB::table('CMD_VENTE as cv')
            ->leftJoin('CLIENT as cl', 'cv.CLT_REF', '=', 'cl.CLT_REF')
            ->select([
                'cv.*',
                DB::raw("COALESCE(cl.CLT_CLIENT, 'Client #' + cv.CLT_REF) as CLIENT_NAME")
            ])
            ->where('cv.CMD_REF', $cmdRef)
            ->first();

        if (!$commande) {
            return null;
        }

        $details = DB::table('CMD_VENTE_DETAIL as cvd')
            ->leftJoin('ARTICLE as a', 'cvd.ART_REF', '=', 'a.ART_REF')
            ->select([
                'cvd.*',
                DB::raw("COALESCE(a.ART_DESIGNATION, 'Article ' + cvd.ART_REF) as ART_DESIGNATION")
            ])
            ->where('cvd.CMD_REF', $cmdRef)
            ->get();

        return [
            'commande' => $commande,
            'details' => $details
        ];
    }

    /**
     * Récupérer la liste des clients
     */
    private function getClients()
    {
        return DB::table('CLIENT')
            ->select('CLT_REF', 'CLT_CLIENT', 'CLT_TELEPHONE')
            ->whereNotNull('CLT_CLIENT')
            ->where('CLT_CLIENT', '!=', '')
            ->orderBy('CLT_CLIENT')
            ->get();
    }

    /**
     * Récupérer la liste des serveurs
     */
    private function getServeurs()
    {
        return DB::table('FACTURE_VNT')
            ->whereNotNull('FCTV_SERVEUR')
            ->distinct()
            ->pluck('FCTV_SERVEUR')
            ->sort()
            ->values();
    }

    /**
     * Récupérer les modes de paiement
     */
    private function getModesPaiement()
    {
        return DB::table('FACTURE_VNT')
            ->whereNotNull('FCTV_MODEPAIEMENT')
            ->distinct()
            ->pluck('FCTV_MODEPAIEMENT')
            ->sort()
            ->values();
    }

    /**
     * Récupérer la liste des articles
     */
    private function getArticles()
    {
        return DB::table('ARTICLE')
            ->select('ART_REF', 'ART_DESIGNATION', 'ART_PRIX_VENTE')
            ->where('ART_VENTE', 1)
            ->whereNotNull('ART_DESIGNATION')
            ->where('ART_DESIGNATION', '!=', '')
            ->orderBy('ART_DESIGNATION')
            ->get();
    }

    /**
     * Générer une nouvelle référence de facture
     */
    private function generateFactureRef()
    {
        $prefix = 'FCTV';
        $date = now()->format('Ymd');
        
        // Trouver le dernier numéro pour aujourd'hui
        $lastRef = DB::table('FACTURE_VNT')
            ->where('FCTV_REF', 'like', $prefix . $date . '%')
            ->orderBy('FCTV_REF', 'desc')
            ->value('FCTV_REF');

        if ($lastRef) {
            $lastNumber = intval(substr($lastRef, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Générer un nouveau numéro de facture séquentiel
     */
    private function generateNewFactureNumber()
    {
        $lastNumber = DB::table('FACTURE_VNT')
            ->whereNotNull('FCTV_NUMERO')
            ->orderBy('FCTV_NUMERO', 'desc')
            ->value('FCTV_NUMERO');

        if ($lastNumber && is_numeric($lastNumber)) {
            return intval($lastNumber) + 1;
        }
        
        // Si pas de numéro trouvé ou non numérique, compter les factures
        $count = DB::table('FACTURE_VNT')->count();
        return $count + 1;
    }

    /**
     * Générer une nouvelle référence de client
     */
    private function generateClientRef()
    {
        $prefix = 'CLT';
        $date = now()->format('Ymd');
        
        // Trouver le dernier numéro pour aujourd'hui
        $lastRef = DB::table('CLIENT')
            ->where('CLT_REF', 'like', $prefix . $date . '%')
            ->orderBy('CLT_REF', 'desc')
            ->value('CLT_REF');

        if ($lastRef) {
            $lastNumber = intval(substr($lastRef, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * التحقق من صحة مرجع الطاولة
     */
    private function getValidTableRef($tableRef)
    {
        if (empty($tableRef)) {
            return null; // إذا لم يتم تحديد طاولة
        }

        // التحقق من وجود الطاولة في قاعدة البيانات
        $exists = DB::table('TABLE')
            ->where('TAB_REF', $tableRef)
            ->exists();

        if ($exists) {
            return $tableRef;
        }

        // إذا لم توجد الطاولة، إنشاؤها تلقائياً أو إرجاع null
        try {
            // محاولة إنشاء طاولة بسيطة حسب بنية الجدول الصحيحة
            DB::table('TABLE')->insert([
                'TAB_REF' => $tableRef,
                'ZON_REF' => 'ZONE01', // منطقة افتراضية
                'ETT_ETAT' => 'Libre', // حالة افتراضية
                'TAB_LIB' => 'Table ' . str_replace('T', '', $tableRef),
                'TAB_DESCRIPT' => 'Table créée automatiquement',
                'TAB_NBR_Couvert' => 4 // عدد افتراضي للمقاعد
            ]);
            return $tableRef;
        } catch (Exception $e) {
            // إذا فشل إنشاء الطاولة، إرجاع null
            \Log::warning('Impossible de créer la table automatiquement: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * التحقق من صحة مرجع الصندوق والحصول على قيمة صالحة
     */
    private function getValidCaisseRef()
    {
        // البحث عن أول صندوق متاح
        $caisse = DB::table('CAISSE')
            ->select('CSS_ID_CAISSE')
            ->first();

        if ($caisse) {
            return $caisse->CSS_ID_CAISSE;
        }

        // إذا لم يوجد صندوق، إنشاء صندوق افتراضي
        try {
            $caisseRef = 'CAISSE001';
            DB::table('CAISSE')->insert([
                'CSS_ID_CAISSE' => $caisseRef,
                'CSS_LIBELLE_CAISSE' => 'Caisse Principale',
                'CSS_AVEC_AFFICHEUR' => 0,
                'CSS_NUM_CMD' => '1',
                'CSS_NUM_FACT' => '1'
            ]);
            return $caisseRef;
        } catch (Exception $e) {
            // إذا فشل إنشاء الصندوق، إرجاع null
            \Log::warning('Impossible de créer la caisse automatiquement: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * التحقق من صحة مرجع المقال والحصول على قيمة صالحة
     */
    private function getValidArticleRef($articleRef)
    {
        if (empty($articleRef)) {
            return null;
        }

        // التحقق من وجود المقال في قاعدة البيانات
        $exists = DB::table('ARTICLE')
            ->where('ART_REF', $articleRef)
            ->exists();

        if ($exists) {
            return $articleRef;
        }

        // إذا لم يوجد المقال، إنشاؤه تلقائياً
        try {
            // التأكد من وجود الحقول المطلوبة أولاً
            $this->ensureRequiredTablesExist();

            // إنشاء مقال بسيط حسب بنية الجدول الصحيحة
            DB::table('ARTICLE')->insert([
                'ART_REF' => $articleRef,
                'SFM_REF' => $this->getValidSousFamilleRef(),
                'ART_DESIGNATION' => 'Article ' . $articleRef,
                'ART_TVA_VENTE' => 20,
                'ART_PRIX_ACHAT' => 10.00,
                'ART_PRIX_VENTE' => 12.00,
                'ART_VENTE' => 1,
                'ART_ACHAT' => 1,
                'ART_STOCKABLE' => 1,
                'UNM_ABR' => $this->getValidUniteRef(),
                'ART_PRIX_ACHAT_HT' => 10.00,
                'ART_PRIX_VENTE_HT' => 10.00,
                'ART_LIBELLE_CAISSE' => 'Article ' . $articleRef,
                'ART_LIBELLE_ARABE' => 'مقال ' . $articleRef,
                'ART_PRIX_EMPORTER' => 12.00,
                'ART_ORDRE_AFFICHAGE' => 999,
                'base64' => '',
                'CF1' => '', 'CF2' => '', 'CF3' => '', 'CF4' => '', 'CF5' => '',
                'CF6' => '', 'CF7' => '', 'CF8' => '', 'CF9' => '', 'CF10' => '',
                'IsIngredient' => 0
            ]);
            return $articleRef;
        } catch (Exception $e) {
            // إذا فشل إنشاء المقال، إرجاع null
            \Log::warning('Impossible de créer l\'article automatiquement: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * التأكد من وجود الجداول المطلوبة لإنشاء المقالات
     */
    private function ensureRequiredTablesExist()
    {
        // التأكد من وجود عائلة فرعية
        $this->getValidSousFamilleRef();
        // التأكد من وجود وحدة قياس
        $this->getValidUniteRef();
    }

    /**
     * الحصول على مرجع عائلة فرعية صالح
     */
    private function getValidSousFamilleRef()
    {
        $sousFamille = DB::table('SOUS_FAMILLE')->first();
        if ($sousFamille) {
            return $sousFamille->SFM_REF;
        }

        // إنشاء عائلة فرعية افتراضية
        try {
            $familleRef = $this->getValidFamilleRef();
            $sfmRef = 'SFM001';
            DB::table('SOUS_FAMILLE')->insert([
                'SFM_REF' => $sfmRef,
                'FAM_REF' => $familleRef,
                'SFM_LIB' => 'Famille Générale',
                'SFM_ORDRE_AFFICHAGE' => 1
            ]);
            return $sfmRef;
        } catch (Exception $e) {
            \Log::warning('Impossible de créer la sous-famille: ' . $e->getMessage());
            return 'SFM001'; // valeur par défaut
        }
    }

    /**
     * الحصول على مرجع عائلة صالح
     */
    private function getValidFamilleRef()
    {
        $famille = DB::table('FAMILLE')->first();
        if ($famille) {
            return $famille->FAM_REF;
        }

        // إنشاء عائلة افتراضية
        try {
            $famRef = 'FAM001';
            DB::table('FAMILLE')->insert([
                'FAM_REF' => $famRef,
                'FAM_LIB' => 'Famille Générale',
                'FAM_ORDRE_AFFICHAGE' => 1
            ]);
            return $famRef;
        } catch (Exception $e) {
            \Log::warning('Impossible de créer la famille: ' . $e->getMessage());
            return 'FAM001'; // valeur par défaut
        }
    }

    /**
     * الحصول على مرجع وحدة قياس صالح
     */
    private function getValidUniteRef()
    {
        $unite = DB::table('UNITE_MESURE')->first();
        if ($unite) {
            return $unite->UNM_ABR;
        }

        // إنشاء وحدة قياس افتراضية
        try {
            $unmRef = 'U';
            DB::table('UNITE_MESURE')->insert([
                'UNM_ABR' => $unmRef,
                'UNM_LIB' => 'Unité'
            ]);
            return $unmRef;
        } catch (Exception $e) {
            \Log::warning('Impossible de créer l\'unité de mesure: ' . $e->getMessage());
            return 'U'; // valeur par défaut
        }
    }

    /**
     * Récupérer les statistiques des factures avec filtres
     */
    private function getFactureStats($filters = [])
    {
        $query = DB::table('FACTURE_VNT');
        
        // Appliquer les filtres de date
        if (isset($filters['date'])) {
            switch ($filters['date']) {
                case 'today':
                    $query->whereDate('FCTV_DATE', Carbon::today());
                    break;
                case 'week':
                    $query->where('FCTV_DATE', '>=', Carbon::now()->subWeek());
                    break;
                case 'month':
                    $query->where('FCTV_DATE', '>=', Carbon::now()->subMonth());
                    break;
                case 'custom':
                    if (isset($filters['date_debut']) && !empty($filters['date_debut'])) {
                        $query->whereDate('FCTV_DATE', '>=', $filters['date_debut']);
                    }
                    if (isset($filters['date_fin']) && !empty($filters['date_fin'])) {
                        $query->whereDate('FCTV_DATE', '<=', $filters['date_fin']);
                    }
                    break;
            }
        }
        
        // Appliquer les autres filtres
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            if ($filters['status'] === 'valide') {
                $query->where('FCTV_VALIDE', 1)->where('FCTV_ETAT', 1);
            } elseif ($filters['status'] === 'annule') {
                $query->where('FCTV_ETAT', 0);
            } elseif ($filters['status'] === 'brouillon') {
                $query->where('FCTV_VALIDE', 0);
            }
        }
        
        if (isset($filters['client']) && $filters['client'] !== 'all') {
            $query->where('CLT_REF', $filters['client']);
        }
        
        if (isset($filters['serveur']) && $filters['serveur'] !== 'all') {
            $query->where('FCTV_SERVEUR', $filters['serveur']);
        }
        
        if (isset($filters['mode_paiement']) && $filters['mode_paiement'] !== 'all') {
            $query->where('FCTV_MODEPAIEMENT', $filters['mode_paiement']);
        }

        return $query->select([
                DB::raw('COUNT(*) as total_factures'),
                DB::raw("SUM(CASE WHEN FCTV_VALIDE = 1 AND FCTV_ETAT = 1 THEN 1 ELSE 0 END) as factures_valides"),
                DB::raw("SUM(CASE WHEN FCTV_ETAT = 0 THEN 1 ELSE 0 END) as factures_annulees"),
                DB::raw("SUM(CASE WHEN FCTV_VALIDE = 0 THEN 1 ELSE 0 END) as brouillons"),
                DB::raw("SUM(CASE WHEN FCTV_VALIDE = 1 AND FCTV_ETAT = 1 THEN COALESCE(FCTV_MNT_TTC, 0) ELSE 0 END) as ca_total"),
                DB::raw("SUM(CASE WHEN FCTV_VALIDE = 1 AND FCTV_ETAT = 1 THEN COALESCE(FCTV_REMISE, 0) ELSE 0 END) as remise_totale"),
                DB::raw('AVG(CASE WHEN FCTV_VALIDE = 1 AND FCTV_ETAT = 1 THEN COALESCE(FCTV_MNT_TTC, 0) END) as facture_moyenne'),
                DB::raw('SUM(CASE WHEN FCTV_VALIDE = 1 AND FCTV_ETAT = 1 THEN COALESCE(MontantEspece, 0) ELSE 0 END) as total_especes'),
                DB::raw('SUM(CASE WHEN FCTV_VALIDE = 1 AND FCTV_ETAT = 1 THEN COALESCE(MontantCharte, 0) ELSE 0 END) as total_cartes'),
                DB::raw('SUM(CASE WHEN FCTV_VALIDE = 1 AND FCTV_ETAT = 1 THEN COALESCE(MontantCredit, 0) ELSE 0 END) as total_credits'),
                DB::raw('SUM(CASE WHEN COALESCE(FCTV_EXONORE, 0) = 1 AND FCTV_VALIDE = 1 AND FCTV_ETAT = 1 THEN 1 ELSE 0 END) as factures_exonerees')
            ])
            ->first();
    }

    // =================== API METHODS ===================

    /**
     * API: Rechercher des clients
     */
    public function searchClients(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $clients = DB::table('CLIENT')
            ->select('CLT_REF', 'CLT_CLIENT', 'CLT_TELEPHONE', 'CLT_EMAIL')
            ->where(function($q) use ($query) {
                $q->where('CLT_CLIENT', 'like', "%{$query}%")
                  ->orWhere('CLT_REF', 'like', "%{$query}%")
                  ->orWhere('CLT_TELEPHONE', 'like', "%{$query}%");
            })
            ->whereNotNull('CLT_CLIENT')
            ->where('CLT_CLIENT', '!=', '')
            ->limit(10)
            ->get();

        return response()->json($clients->map(function($client) {
            return [
                'id' => $client->CLT_REF,
                'text' => $client->CLT_CLIENT . ' - ' . ($client->CLT_TELEPHONE ?? 'N/A'),
                'nom' => $client->CLT_CLIENT,
                'telephone' => $client->CLT_TELEPHONE,
                'email' => $client->CLT_EMAIL
            ];
        }));
    }

    /**
     * API: Créer un nouveau client depuis la facture
     */
    public function createClient(Request $request)
    {
        try {
            // تحقق أبسط من البيانات
            $request->validate([
                'nom' => 'required|string|min:2|max:100',
                'telephone' => 'required|string|min:8|max:20',
                'email' => 'nullable|email|max:100',
                'raison_sociale' => 'nullable|string|max:100',
                'civilite' => 'nullable|string|max:10'
            ], [
                'nom.required' => 'اسم العميل مطلوب',
                'nom.min' => 'اسم العميل يجب أن يكون أكثر من حرفين',
                'telephone.required' => 'رقم الهاتف مطلوب',
                'telephone.min' => 'رقم الهاتف يجب أن يكون 8 أرقام على الأقل',
                'email.email' => 'صيغة البريد الإلكتروني غير صحيحة'
            ]);

            DB::beginTransaction();

            // تنظيف البيانات
            $nom = trim($request->nom);
            $telephone = trim($request->telephone);
            $email = $request->email ? trim($request->email) : null;

            // التحقق من وجود عميل مشابه
            $existingClient = DB::table('CLIENT')
                ->where('CLT_CLIENT', $nom)
                ->where('CLT_TELEPHONE', $telephone)
                ->first();

            if ($existingClient) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'عميل بنفس الاسم ورقم الهاتف موجود بالفعل',
                    'existing_client' => [
                        'CLT_REF' => $existingClient->CLT_REF,
                        'CLT_CLIENT' => $existingClient->CLT_CLIENT,
                        'CLT_TELEPHONE' => $existingClient->CLT_TELEPHONE,
                        'CLT_EMAIL' => $existingClient->CLT_EMAIL
                    ]
                ], 409);
            }

            // إنشاء مرجع فريد للعميل
            $clientRef = $this->generateClientRef();

            // إعداد بيانات العميل - الأعمدة الموجودة فقط في قاعدة البيانات
            $clientData = [
                'CLT_REF' => $clientRef,
                'CLTCAT_REF' => 'CAT001', // مطلوب - قيمة افتراضية
                'CLT_CLIENT' => $nom,
                'CLT_TELEPHONE' => $telephone,
                'CLT_EMAIL' => $email,
                'CLT_RAISONSOCIAL' => $request->raison_sociale ?? '',
                'CLT_CIVILITE' => $request->civilite ?? 'Mr',
                'CLT_ISENTREPRISE' => $request->boolean('est_entreprise', false) ? 1 : 0,
                'CLT_BLOQUE' => 0,
                'CLT_FIDELE' => 0,
                'CLT_POINTFIDILIO' => 0,
                'CLT_CREDIT' => 0.00,
                'CLT_ENVOISMS' => 1,
                'CLT_ENVOIMMS' => 0,
                'CLT_ENVOIEMAIL' => 1,
                'CLT_ENVOICOURIER' => 0,
                'CLT_COMMANTAIRE' => 'Créé depuis facture le ' . now()->format('d/m/Y H:i')
            ];

            // إدراج العميل الجديد
            DB::table('CLIENT')->insert($clientData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء العميل بنجاح',
                'client' => [
                    'CLT_REF' => $clientRef,
                    'CLT_CLIENT' => $nom,
                    'CLT_TELEPHONE' => $telephone,
                    'CLT_EMAIL' => $email,
                    'CLT_RAISONSOCIAL' => $request->raison_sociale ?? ''
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('خطأ في إنشاء العميل: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'خطأ في إنشاء العميل: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Rechercher des articles
     */
    public function searchArticles(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $articles = DB::table('ARTICLE')
            ->select('ART_REF', 'ART_DESIGNATION', 'ART_PRIX_VENTE', 'ART_TVA_VENTE')
            ->where(function($q) use ($query) {
                $q->where('ART_DESIGNATION', 'like', "%{$query}%")
                  ->orWhere('ART_REF', 'like', "%{$query}%");
            })
            ->where('ART_VENTE', 1)
            ->whereNotNull('ART_DESIGNATION')
            ->where('ART_DESIGNATION', '!=', '')
            ->limit(10)
            ->get();

        return response()->json($articles->map(function($article) {
            $prixTTC = $article->ART_PRIX_VENTE * (1 + ($article->ART_TVA_VENTE ?? 20) / 100);
            return [
                'id' => $article->ART_REF,
                'text' => $article->ART_DESIGNATION . ' - ' . number_format($prixTTC, 2) . ' DH',
                'designation' => $article->ART_DESIGNATION,
                'prix_ht' => $article->ART_PRIX_VENTE,
                'prix_ttc' => $prixTTC,
                'tva' => $article->ART_TVA_VENTE ?? 20
            ];
        }));
    }

    /**
     * API: Obtenir les détails d'un client
     */
    public function getClientDetails($clientRef)
    {
        $client = DB::table('CLIENT')
            ->where('CLT_REF', $clientRef)
            ->first();

        if (!$client) {
            return response()->json(['error' => 'Client non trouvé'], 404);
        }

        return response()->json([
            'id' => $client->CLT_REF,
            'nom' => $client->CLT_CLIENT,
            'telephone' => $client->CLT_TELEPHONE,
            'email' => $client->CLT_EMAIL,
            'credit' => $client->CLT_CREDIT ?? 0
        ]);
    }

    /**
     * API: Obtenir les détails d'un article
     */
    public function getArticleDetails($articleRef)
    {
        $article = DB::table('ARTICLE')
            ->where('ART_REF', $articleRef)
            ->first();

        if (!$article) {
            return response()->json(['error' => 'Article non trouvé'], 404);
        }

        $prixTTC = $article->ART_PRIX_VENTE * (1 + ($article->ART_TVA_VENTE ?? 20) / 100);

        return response()->json([
            'id' => $article->ART_REF,
            'designation' => $article->ART_DESIGNATION,
            'prix_ht' => $article->ART_PRIX_VENTE,
            'prix_ttc' => $prixTTC,
            'tva' => $article->ART_TVA_VENTE ?? 20
        ]);
    }

    /**
     * API: Obtenir les statistiques en temps réel
     */
    public function getStatistics()
    {
        $stats = $this->getFactureStats();
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * API: Valider le stock disponible
     */
    public function validateStock(Request $request)
    {
        $articles = $request->input('articles', []);
        $results = [];

        foreach ($articles as $article) {
            $articleRef = $article['ref'];
            $quantite = $article['quantite'];

            // Vérifier le stock disponible
            $stockData = DB::table('STOCK')
                ->where('ART_REF', $articleRef)
                ->sum('STK_QTE');

            if ($stockData === null) {
                $results[] = [
                    'ref' => $articleRef,
                    'status' => 'error',
                    'message' => 'Article non trouvé en stock'
                ];
                continue;
            }

            $stockDisponible = $stockData;

            if ($stockDisponible < $quantite) {
                $results[] = [
                    'ref' => $articleRef,
                    'status' => 'warning',
                    'message' => "Stock insuffisant (disponible: {$stockDisponible})",
                    'stock_disponible' => $stockDisponible
                ];
            } else {
                $results[] = [
                    'ref' => $articleRef,
                    'status' => 'success',
                    'message' => 'Stock suffisant',
                    'stock_disponible' => $stockDisponible
                ];
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }

    /**
     * API: Résumé journalier
     */
    public function getDailySummary()
    {
        $today = Carbon::today();
        
        $summary = DB::table('FACTURE_VNT')
            ->whereDate('FCTV_DATE', $today)
            ->where('FCTV_VALIDE', 1)
            ->where('FCTV_ETAT', 1)
            ->select([
                DB::raw('COUNT(*) as nb_factures'),
                DB::raw('SUM(COALESCE(FCTV_MNT_TTC, 0)) as ca_total'),
                DB::raw('AVG(COALESCE(FCTV_MNT_TTC, 0)) as ticket_moyen'),
                DB::raw('SUM(COALESCE(MontantEspece, 0)) as especes'),
                DB::raw('SUM(COALESCE(MontantCharte, 0)) as cartes'),
                DB::raw('SUM(COALESCE(MontantCredit, 0)) as credits')
            ])
            ->first();

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }
}
