<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ReportExport;

class ReportController extends Controller
{
    /**
     * Page d'index avec gestion des raccourcis
     */
    public function index(Request $request)
    {
        // Gestion des raccourcis depuis le dashboard
        if ($request->has('quick')) {
            switch ($request->quick) {
                case 'today':
                    return $this->generateQuickReport('ventes', now(), now());
                case 'week':
                    return $this->generateQuickReport('ventes', now()->startOfWeek(), now()->endOfWeek());
                case 'month':
                    return $this->generateQuickReport('ventes', now()->startOfMonth(), now()->endOfMonth());
            }
        }
        
        // Récupération des informations sur les tables disponibles
        $tablesInfo = $this->getAvailableData();
        
        // Ajout d'informations supplémentaires pour l'interface
        $suggestions = $this->getReportSuggestions();
        
        return view('admin.reports.index', compact('tablesInfo', 'suggestions'));
    }

    /**
     * Vérification des données disponibles et des colonnes réelles
     */
    private function getAvailableData()
    {
        try {
            $info = [];
            
            // Vérification du tableau des factures et colonnes
            $ventesCount = 0;
            $ventesDateRange = null;
            $ventesColumns = [];
            
            if (Schema::hasTable('FACTURE_VNT')) {
                $ventesColumns = Schema::getColumnListing('FACTURE_VNT');
                $ventesCount = DB::table('FACTURE_VNT')->count();
                
                if ($ventesCount > 0) {
                    // Recherche de la colonne de date
                    $dateColumn = null;
                    $possibleDateColumns = ['fctv_date', 'FCTV_DATE', 'DATE_FACTURE', 'created_at', 'date_creation'];
                    
                    foreach ($possibleDateColumns as $col) {
                        if (in_array($col, $ventesColumns)) {
                            $dateColumn = $col;
                            break;
                        }
                    }
                    
                    if ($dateColumn) {
                        $minDate = DB::table('FACTURE_VNT')->min($dateColumn);
                        $maxDate = DB::table('FACTURE_VNT')->max($dateColumn);
                        $ventesDateRange = ['min' => $minDate, 'max' => $maxDate, 'column' => $dateColumn];
                    }
                }
            }
            
            // Vérification du tableau des articles
            $articlesCount = 0;
            $articlesColumns = [];
            if (Schema::hasTable('ARTICLE')) {
                $articlesColumns = Schema::getColumnListing('ARTICLE');
                $articlesCount = DB::table('ARTICLE')->count();
            }
            
            // Vérification du tableau des clients
            $clientsCount = 0;
            $clientsColumns = [];
            if (Schema::hasTable('CLIENT')) {
                $clientsColumns = Schema::getColumnListing('CLIENT');
                $clientsCount = DB::table('CLIENT')->count();
            }
            
            // Vérification du tableau des tables
            $tablesCount = 0;
            $tablesColumns = [];
            if (Schema::hasTable('TABLE_RESTAURANT')) {
                $tablesColumns = Schema::getColumnListing('TABLE_RESTAURANT');
                $tablesCount = DB::table('TABLE_RESTAURANT')->count();
            }
            
            $info = [
                'ventes' => [
                    'count' => $ventesCount,
                    'date_range' => $ventesDateRange,
                    'columns' => $ventesColumns
                ],
                'articles' => [
                    'count' => $articlesCount,
                    'columns' => $articlesColumns
                ],
                'clients' => [
                    'count' => $clientsCount,
                    'columns' => $clientsColumns
                ],
                'tables' => [
                    'count' => $tablesCount,
                    'columns' => $tablesColumns
                ]
            ];
            
            return $info;
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du contrôle des données: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Génération du rapport selon les paramètres
     */
    public function generate(Request $request)
    {
        try {
            // Validation des données
            $request->validate([
                'type_rapport' => 'required|in:ventes,stock,clients,financier,restaurant',
                'periode_type' => 'required|in:jour,periode',
                'date_debut' => 'required|date',
                'date_fin' => 'nullable|date|after_or_equal:date_debut',
                'format' => 'required|in:view,pdf,excel,csv'
            ]);

            $dateDebut = Carbon::parse($request->date_debut);
            $dateFin = $request->periode_type === 'periode' ? Carbon::parse($request->date_fin) : $dateDebut;

            // Redirection selon le format demandé
            switch($request->format) {
                case 'pdf':
                    return $this->generatePDF($request, $dateDebut, $dateFin);
                case 'excel':
                    return $this->generateExcel($request, $dateDebut, $dateFin);
                case 'csv':
                    return $this->generateCSV($request, $dateDebut, $dateFin);
                default:
                    return $this->generateView($request, $dateDebut, $dateFin);
            }

        } catch (\Exception $e) {
            Log::error('Erreur génération rapport: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la génération du rapport: ' . $e->getMessage());
        }
    }

    /**
     * Génération de la vue du rapport
     */
    protected function generateView(Request $request, $dateDebut, $dateFin)
    {
        $typeRapport = $request->type_rapport;
        
        // Récupération des données selon le type
        switch ($typeRapport) {
            case 'ventes':
                return $this->generateVentesView($request, $dateDebut, $dateFin);
            case 'stock':
                return $this->generateStockView($request, $dateDebut, $dateFin);
            case 'clients':
                return $this->generateClientsView($request, $dateDebut, $dateFin);
            case 'financier':
                return $this->generateFinancierView($request, $dateDebut, $dateFin);
            case 'restaurant':
                return $this->generateRestaurantView($request, $dateDebut, $dateFin);
            default:
                return back()->with('error', 'Type de rapport non reconnu');
        }
    }

    /**
     * Génération du rapport des ventes
     */
    protected function generateVentesView(Request $request, $dateDebut, $dateFin)
    {
        // Vérification des colonnes disponibles
        $columns = Schema::getColumnListing('FACTURE_VNT');
        
        // Recherche des noms de colonnes corrects
        $dateColumn = $this->findColumn($columns, ['fctv_date', 'FCTV_DATE', 'DATE_FACTURE', 'created_at']);
        $refColumn = $this->findColumn($columns, ['FCTV_REF', 'fctv_ref', 'REF_FACTURE', 'numero_facture']);
        $montantTTCColumn = $this->findColumn($columns, ['fctv_mnt_ttc', 'FCTV_MNT_TTC', 'montant_ttc', 'total_ttc']);
        $montantHTColumn = $this->findColumn($columns, ['fctv_mnt_ht', 'FCTV_MNT_HT', 'montant_ht', 'total_ht']);
        $modePayementColumn = $this->findColumn($columns, ['fctv_modepaiement', 'FCTV_MODEPAIEMENT', 'mode_paiement', 'type_paiement']);
        $utilisateurColumn = $this->findColumn($columns, ['fctv_utilisateur', 'FCTV_UTILISATEUR', 'utilisateur', 'caissier']);
        
        if (!$dateColumn) {
            return back()->with('error', 'Impossible de trouver la colonne de date dans le tableau FACTURE_VNT. Colonnes disponibles: ' . implode(', ', $columns));
        }

        // Construction de la requête
        $query = DB::table('FACTURE_VNT');
        
        // Filtrage par date
        if ($dateColumn) {
            $query->whereBetween($dateColumn, [$dateDebut->format('Y-m-d 00:00:00'), $dateFin->format('Y-m-d 23:59:59')]);
        }

        // Filtres additionnels
        if ($request->filled('caissier') && $utilisateurColumn) {
            $query->where($utilisateurColumn, $request->caissier);
        }
        if ($request->filled('mode_paiement') && $modePayementColumn) {
            $query->where($modePayementColumn, $request->mode_paiement);
        }

        // Sélection des colonnes
        $selectColumns = ['*'];
        if ($dateColumn) {
            $query->orderBy($dateColumn, 'desc');
        }

        $sales = $query->get();

        // Si aucune donnée n'est trouvée
        if ($sales->isEmpty()) {
            return back()->with('error', 
                'Aucune donnée trouvée pour la période sélectionnée (' . 
                $dateDebut->format('d/m/Y') . 
                ($dateDebut->ne($dateFin) ? ' - ' . $dateFin->format('d/m/Y') : '') . 
                '). Veuillez vérifier vos données ou choisir une autre période.'
            );
        }

        // Calculs statistiques - utilisation des noms de colonnes dynamiques
        $totalTTC = 0;
        $totalHT = 0;

        foreach ($sales as $sale) {
            if ($montantTTCColumn && property_exists($sale, $montantTTCColumn)) {
                $totalTTC += $sale->{$montantTTCColumn} ?? 0;
            }
            if ($montantHTColumn && property_exists($sale, $montantHTColumn)) {
                $totalHT += $sale->{$montantHTColumn} ?? 0;
            }
        }

        $statistiques = [
            'total_ventes' => $totalTTC,
            'total_ventes_ht' => $totalHT,
            'nombre_factures' => $sales->count(),
            'ticket_moyen' => $sales->count() > 0 ? ($totalTTC / $sales->count()) : 0,
            'periode' => $dateDebut->format('d/m/Y') . ($dateDebut->eq($dateFin) ? '' : ' - ' . $dateFin->format('d/m/Y')),
            'type_rapport' => 'Rapport des Ventes',
            'colonnes' => [
                'date' => $dateColumn,
                'ref' => $refColumn,
                'montant_ttc' => $montantTTCColumn,
                'montant_ht' => $montantHTColumn,
                'mode_paiement' => $modePayementColumn,
                'utilisateur' => $utilisateurColumn
            ]
        ];

        // Analyses avec gestion des colonnes dynamiques
        $modesPayement = collect();
        $ventesParJour = collect();
        $ventesParCaissier = collect();

        if ($modePayementColumn) {
            $modesPayement = $sales->groupBy($modePayementColumn)
                ->map(function($group) use ($montantTTCColumn) {
                    $montant = 0;
                    foreach ($group as $item) {
                        if ($montantTTCColumn && property_exists($item, $montantTTCColumn)) {
                            $montant += $item->{$montantTTCColumn} ?? 0;
                        }
                    }
                    return [
                        'count' => $group->count(),
                        'montant' => $montant
                    ];
                });
        }

        if ($dateColumn) {
            $ventesParJour = $sales->groupBy(function($sale) use ($dateColumn) {
                $date = $sale->{$dateColumn} ?? null;
                return $date ? Carbon::parse($date)->format('Y-m-d') : 'unknown';
            })->map(function($group) use ($montantTTCColumn) {
                $montant = 0;
                foreach ($group as $item) {
                    if ($montantTTCColumn && property_exists($item, $montantTTCColumn)) {
                        $montant += $item->{$montantTTCColumn} ?? 0;
                    }
                }
                return [
                    'count' => $group->count(),
                    'montant' => $montant
                ];
            });
        }

        if ($utilisateurColumn) {
            $ventesParCaissier = $sales->groupBy($utilisateurColumn)
                ->map(function($group) use ($montantTTCColumn) {
                    $montant = 0;
                    foreach ($group as $item) {
                        if ($montantTTCColumn && property_exists($item, $montantTTCColumn)) {
                            $montant += $item->{$montantTTCColumn} ?? 0;
                        }
                    }
                    return [
                        'count' => $group->count(),
                        'montant' => $montant
                    ];
                });
        }

        return view('admin.reports.rapport-ventes', compact(
            'sales', 
            'statistiques', 
            'modesPayement', 
            'ventesParJour', 
            'ventesParCaissier',
            'dateDebut',
            'dateFin'
        ));
    }

    /**
     * Recherche du nom de colonne correct
     */
    private function findColumn($columns, $possibleNames)
    {
        foreach ($possibleNames as $name) {
            if (in_array($name, $columns)) {
                return $name;
            }
        }
        return null;
    }

    /**
     * Génération du rapport de stock
     */
    protected function generateStockView(Request $request, $dateDebut, $dateFin)
    {
        // Vérification des colonnes disponibles
        $columns = Schema::getColumnListing('ARTICLE');
        
        $query = DB::table('ARTICLE');

        // Filtres additionnels
        if ($request->filled('famille')) {
            $familleColumn = $this->findColumn($columns, ['ART_FAMILLE', 'art_famille', 'famille', 'categorie']);
            if ($familleColumn) {
                $query->where($familleColumn, $request->famille);
            }
        }
        
        if ($request->filled('stock_minimum')) {
            $stockColumn = $this->findColumn($columns, ['ART_QTE_STOCK', 'art_qte_stock', 'quantite_stock', 'stock']);
            if ($stockColumn) {
                $query->where($stockColumn, '<=', $request->stock_minimum);
            }
        }

        $articles = $query->get();

        // Si aucune donnée n'est trouvée
        if ($articles->isEmpty()) {
            return back()->with('error', 'Aucun article trouvé dans la base de données. Veuillez ajouter des articles d\'abord.');
        }

        // Recherche des noms de colonnes
        $codeColumn = $this->findColumn($columns, ['ART_CODE', 'art_code', 'code_article', 'code']);
        $designationColumn = $this->findColumn($columns, ['ART_DESIGNATION', 'art_designation', 'designation', 'nom']);
        $familleColumn = $this->findColumn($columns, ['ART_FAMILLE', 'art_famille', 'famille', 'categorie']);
        $stockColumn = $this->findColumn($columns, ['ART_QTE_STOCK', 'art_qte_stock', 'quantite_stock', 'stock']);
        $prixAchatColumn = $this->findColumn($columns, ['ART_PRIX_ACHAT', 'art_prix_achat', 'prix_achat', 'cout']);
        $prixVenteColumn = $this->findColumn($columns, ['ART_PRIX_VNT', 'art_prix_vnt', 'prix_vente', 'prix']);

        // Calculs avec gestion des colonnes dynamiques
        $articlesEnStock = 0;
        $articlesRupture = 0;
        $valeurStock = 0;

        foreach ($articles as $article) {
            $stock = 0;
            $prixAchat = 0;
            
            if ($stockColumn && property_exists($article, $stockColumn)) {
                $stock = $article->{$stockColumn} ?? 0;
            }
            
            if ($prixAchatColumn && property_exists($article, $prixAchatColumn)) {
                $prixAchat = $article->{$prixAchatColumn} ?? 0;
            }
            
            if ($stock > 0) {
                $articlesEnStock++;
            } else {
                $articlesRupture++;
            }
            
            $valeurStock += $stock * $prixAchat;
        }

        $statistiques = [
            'total_articles' => $articles->count(),
            'articles_en_stock' => $articlesEnStock,
            'articles_rupture' => $articlesRupture,
            'valeur_stock' => $valeurStock,
            'periode' => $dateDebut->format('d/m/Y') . ($dateDebut->eq($dateFin) ? '' : ' - ' . $dateFin->format('d/m/Y')),
            'type_rapport' => 'Rapport du Stock',
            'colonnes' => [
                'code' => $codeColumn,
                'designation' => $designationColumn,
                'famille' => $familleColumn,
                'stock' => $stockColumn,
                'prix_achat' => $prixAchatColumn,
                'prix_vente' => $prixVenteColumn
            ]
        ];

        return view('admin.reports.rapport-stock', compact(
            'articles', 
            'statistiques',
            'dateDebut',
            'dateFin'
        ));
    }

    /**
     * Génération du rapport des clients
     */
    protected function generateClientsView(Request $request, $dateDebut, $dateFin)
    {
        // Vérification des colonnes disponibles
        $columns = Schema::getColumnListing('CLIENT');
        
        $query = DB::table('CLIENT');

        // Filtres additionnels
        if ($request->filled('fideles_only')) {
            $fideleColumn = $this->findColumn($columns, ['CLT_FIDELE', 'clt_fidele', 'fidele', 'is_fidele']);
            if ($fideleColumn) {
                $query->where($fideleColumn, 1);
            }
        }

        $clients = $query->get();

        // Si aucune donnée n'est trouvée
        if ($clients->isEmpty()) {
            return back()->with('error', 'Aucun client trouvé dans la base de données. Veuillez ajouter des clients d\'abord.');
        }

        // Recherche des noms de colonnes
        $codeColumn = $this->findColumn($columns, ['CLT_CODE', 'clt_code', 'code_client', 'code']);
        $nomColumn = $this->findColumn($columns, ['CLT_NOM', 'clt_nom', 'nom', 'nom_client']);
        $telColumn = $this->findColumn($columns, ['CLT_TEL', 'clt_tel', 'telephone', 'tel']);
        $emailColumn = $this->findColumn($columns, ['CLT_EMAIL', 'clt_email', 'email', 'mail']);
        $adresseColumn = $this->findColumn($columns, ['CLT_ADRESSE', 'clt_adresse', 'adresse', 'address']);
        $fideleColumn = $this->findColumn($columns, ['CLT_FIDELE', 'clt_fidele', 'fidele', 'is_fidele']);
        $actifColumn = $this->findColumn($columns, ['CLT_ACTIF', 'clt_actif', 'actif', 'is_active']);

        // Calculs
        $clientsActifs = 0;
        $clientsFideles = 0;

        foreach ($clients as $client) {
            if ($actifColumn && property_exists($client, $actifColumn) && $client->{$actifColumn} == 1) {
                $clientsActifs++;
            }
            if ($fideleColumn && property_exists($client, $fideleColumn) && $client->{$fideleColumn} == 1) {
                $clientsFideles++;
            }
        }

        $statistiques = [
            'total_clients' => $clients->count(),
            'clients_actifs' => $clientsActifs,
            'clients_fideles' => $clientsFideles,
            'periode' => $dateDebut->format('d/m/Y') . ($dateDebut->eq($dateFin) ? '' : ' - ' . $dateFin->format('d/m/Y')),
            'type_rapport' => 'Rapport des Clients',
            'colonnes' => [
                'code' => $codeColumn,
                'nom' => $nomColumn,
                'tel' => $telColumn,
                'email' => $emailColumn,
                'adresse' => $adresseColumn,
                'fidele' => $fideleColumn,
                'actif' => $actifColumn
            ]
        ];

        return view('admin.reports.rapport-clients', compact(
            'clients', 
            'statistiques',
            'dateDebut',
            'dateFin'
        ));
    }

    /**
     * Génération du rapport financier
     */
    protected function generateFinancierView(Request $request, $dateDebut, $dateFin)
    {
        // Vérification des colonnes disponibles
        $columns = Schema::getColumnListing('FACTURE_VNT');
        
        $dateColumn = $this->findColumn($columns, ['fctv_date', 'FCTV_DATE', 'DATE_FACTURE', 'created_at']);
        $montantTTCColumn = $this->findColumn($columns, ['fctv_mnt_ttc', 'FCTV_MNT_TTC', 'montant_ttc', 'total_ttc']);
        $montantHTColumn = $this->findColumn($columns, ['fctv_mnt_ht', 'FCTV_MNT_HT', 'montant_ht', 'total_ht']);

        if (!$dateColumn) {
            return back()->with('error', 'Impossible de trouver la colonne de date dans le tableau FACTURE_VNT pour le rapport financier.');
        }

        $ventes = DB::table('FACTURE_VNT')
            ->whereBetween($dateColumn, [$dateDebut->format('Y-m-d 00:00:00'), $dateFin->format('Y-m-d 23:59:59')])
            ->get();

        // Si aucune donnée n'est trouvée
        if ($ventes->isEmpty()) {
            return back()->with('error', 
                'Aucune donnée financière trouvée pour la période sélectionnée (' . 
                $dateDebut->format('d/m/Y') . 
                ($dateDebut->ne($dateFin) ? ' - ' . $dateFin->format('d/m/Y') : '') . 
                '). Veuillez vérifier vos données ou choisir une autre période.'
            );
        }

        // Calculs
        $chiffreAffaires = 0;
        $chiffreAffairesHT = 0;

        foreach ($ventes as $vente) {
            if ($montantTTCColumn && property_exists($vente, $montantTTCColumn)) {
                $chiffreAffaires += $vente->{$montantTTCColumn} ?? 0;
            }
            if ($montantHTColumn && property_exists($vente, $montantHTColumn)) {
                $chiffreAffairesHT += $vente->{$montantHTColumn} ?? 0;
            }
        }

        $statistiques = [
            'chiffre_affaires' => $chiffreAffaires,
            'chiffre_affaires_ht' => $chiffreAffairesHT,
            'nombre_transactions' => $ventes->count(),
            'tva_collectee' => $chiffreAffaires - $chiffreAffairesHT,
            'periode' => $dateDebut->format('d/m/Y') . ($dateDebut->eq($dateFin) ? '' : ' - ' . $dateFin->format('d/m/Y')),
            'type_rapport' => 'Rapport Financier'
        ];

        return view('admin.reports.rapport-financier', compact(
            'ventes',
            'statistiques',
            'dateDebut',
            'dateFin'
        ));
    }

    /**
     * Génération du rapport restaurant
     */
    protected function generateRestaurantView(Request $request, $dateDebut, $dateFin)
    {
        try {
            // Tentative de récupération des données des tables
            $tables = DB::table('TABLE_RESTAURANT')->get();
            
            $reservations = collect();
            if (Schema::hasTable('RESERVATION')) {
                $reservationColumns = Schema::getColumnListing('RESERVATION');
                $dateColumn = $this->findColumn($reservationColumns, ['RESERVATION_DATE', 'reservation_date', 'date_reservation', 'date']);
                
                if ($dateColumn) {
                    $reservations = DB::table('RESERVATION')
                        ->whereBetween($dateColumn, [$dateDebut->format('Y-m-d'), $dateFin->format('Y-m-d')])
                        ->get();
                }
            }

            // Calculs
            $tablesActives = 0;
            $activeColumn = $this->findColumn(Schema::getColumnListing('TABLE_RESTAURANT'), ['TABLE_ACTIVE', 'table_active', 'active', 'is_active']);
            
            if ($activeColumn) {
                foreach ($tables as $table) {
                    if (property_exists($table, $activeColumn) && $table->{$activeColumn} == 1) {
                        $tablesActives++;
                    }
                }
            }

            $statistiques = [
                'total_tables' => $tables->count(),
                'tables_actives' => $tablesActives,
                'total_reservations' => $reservations->count(),
                'periode' => $dateDebut->format('d/m/Y') . ($dateDebut->eq($dateFin) ? '' : ' - ' . $dateFin->format('d/m/Y')),
                'type_rapport' => 'Rapport Restaurant'
            ];

            return view('admin.reports.rapport-restaurant', compact(
                'tables',
                'reservations',
                'statistiques',
                'dateDebut',
                'dateFin'
            ));

        } catch (\Exception $e) {
            return back()->with('error', 'Le module Restaurant n\'est pas encore configuré. Veuillez configurer les tables et réservations d\'abord.');
        }
    }

    /**
     * Génération PDF
     */
    protected function generatePDF($request, $dateDebut, $dateFin)
    {
        try {
            $data = $this->getReportData($request, $dateDebut, $dateFin);
            
            if ($this->isDataEmpty($data)) {
                return back()->with('error', 'Aucune donnée trouvée pour générer le PDF');
            }

            $pdf = PDF::loadView('admin.reports.pdf.rapport-' . $request->type_rapport, $data);
            $filename = 'rapport_' . $request->type_rapport . '_' . $dateDebut->format('Y-m-d') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Erreur génération PDF: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
        }
    }

    /**
     * Génération Excel
     */
    protected function generateExcel($request, $dateDebut, $dateFin)
    {
        try {
            $data = $this->getReportData($request, $dateDebut, $dateFin);
            
            if ($this->isDataEmpty($data)) {
                return back()->with('error', 'Aucune donnée trouvée pour générer l\'Excel');
            }

            $filename = 'rapport_' . $request->type_rapport . '_' . $dateDebut->format('Y-m-d') . '.xlsx';
            
            return Excel::download(new ReportExport($data, $request->type_rapport), $filename);

        } catch (\Exception $e) {
            Log::error('Erreur génération Excel: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la génération de l\'Excel: ' . $e->getMessage());
        }
    }

    /**
     * Génération CSV
     */
    protected function generateCSV($request, $dateDebut, $dateFin)
    {
        try {
            $data = $this->getReportData($request, $dateDebut, $dateFin);
            
            if ($this->isDataEmpty($data)) {
                return back()->with('error', 'Aucune donnée trouvée pour générer le CSV');
            }

            $filename = 'rapport_' . $request->type_rapport . '_' . $dateDebut->format('Y-m-d') . '.csv';
            
            return Excel::download(new ReportExport($data, $request->type_rapport), $filename, \Maatwebsite\Excel\Excel::CSV);

        } catch (\Exception $e) {
            Log::error('Erreur génération CSV: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la génération du CSV: ' . $e->getMessage());
        }
    }

    /**
     * Récupération des données du rapport selon le type
     */
    private function getReportData($request, $dateDebut, $dateFin)
    {
        switch ($request->type_rapport) {
            case 'ventes':
                return $this->getVentesDataForExport($request, $dateDebut, $dateFin);
            case 'stock':
                return $this->getStockDataForExport($request, $dateDebut, $dateFin);
            case 'clients':
                return $this->getClientsDataForExport($request, $dateDebut, $dateFin);
            default:
                return ['sales' => collect(), 'articles' => collect(), 'clients' => collect()];
        }
    }

    /**
     * Vérification si les données sont vides
     */
    private function isDataEmpty($data)
    {
        return ($data['sales'] ?? collect())->isEmpty() && 
               ($data['articles'] ?? collect())->isEmpty() && 
               ($data['clients'] ?? collect())->isEmpty();
    }

    /**
     * Récupération des données de stock pour l'export
     */
    private function getStockDataForExport($request, $dateDebut, $dateFin)
    {
        $columns = Schema::getColumnListing('ARTICLE');
        $articles = DB::table('ARTICLE')->get();
        
        $statistiques = [
            'colonnes' => [
                'code' => $this->findColumn($columns, ['ART_CODE', 'art_code', 'code_article', 'code']),
                'designation' => $this->findColumn($columns, ['ART_DESIGNATION', 'art_designation', 'designation', 'nom']),
                'famille' => $this->findColumn($columns, ['ART_FAMILLE', 'art_famille', 'famille', 'categorie']),
                'stock' => $this->findColumn($columns, ['ART_QTE_STOCK', 'art_qte_stock', 'quantite_stock', 'stock']),
                'prix_achat' => $this->findColumn($columns, ['ART_PRIX_ACHAT', 'art_prix_achat', 'prix_achat', 'cout']),
                'prix_vente' => $this->findColumn($columns, ['ART_PRIX_VNT', 'art_prix_vnt', 'prix_vente', 'prix'])
            ]
        ];
        
        return ['articles' => $articles, 'statistiques' => $statistiques];
    }

    /**
     * Récupération des données clients pour l'export
     */
    private function getClientsDataForExport($request, $dateDebut, $dateFin)
    {
        $columns = Schema::getColumnListing('CLIENT');
        $clients = DB::table('CLIENT')->get();
        
        $statistiques = [
            'colonnes' => [
                'code' => $this->findColumn($columns, ['CLT_CODE', 'clt_code', 'code_client', 'code']),
                'nom' => $this->findColumn($columns, ['CLT_NOM', 'clt_nom', 'nom', 'nom_client']),
                'tel' => $this->findColumn($columns, ['CLT_TEL', 'clt_tel', 'telephone', 'tel']),
                'email' => $this->findColumn($columns, ['CLT_EMAIL', 'clt_email', 'email', 'mail']),
                'fidele' => $this->findColumn($columns, ['CLT_FIDELE', 'clt_fidele', 'fidele', 'is_fidele']),
                'actif' => $this->findColumn($columns, ['CLT_ACTIF', 'clt_actif', 'actif', 'is_active'])
            ]
        ];
        
        return ['clients' => $clients, 'statistiques' => $statistiques];
    }

    /**
     * Récupération des données des ventes pour l'export
     */
    private function getVentesDataForExport($request, $dateDebut, $dateFin)
    {
        $columns = Schema::getColumnListing('FACTURE_VNT');
        
        // Recherche des noms de colonnes corrects
        $dateColumn = $this->findColumn($columns, ['fctv_date', 'FCTV_DATE', 'DATE_FACTURE', 'created_at']);
        $refColumn = $this->findColumn($columns, ['FCTV_REF', 'fctv_ref', 'REF_FACTURE', 'numero_facture']);
        $montantTTCColumn = $this->findColumn($columns, ['fctv_mnt_ttc', 'FCTV_MNT_TTC', 'montant_ttc', 'total_ttc']);
        $montantHTColumn = $this->findColumn($columns, ['fctv_mnt_ht', 'FCTV_MNT_HT', 'montant_ht', 'total_ht']);
        $modePayementColumn = $this->findColumn($columns, ['fctv_modepaiement', 'FCTV_MODEPAIEMENT', 'mode_paiement', 'type_paiement']);
        $utilisateurColumn = $this->findColumn($columns, ['fctv_utilisateur', 'FCTV_UTILISATEUR', 'utilisateur', 'caissier']);
        
        if (!$dateColumn) {
            return ['sales' => collect(), 'statistiques' => []];
        }

        $query = DB::table('FACTURE_VNT')
            ->whereBetween($dateColumn, [$dateDebut->format('Y-m-d 00:00:00'), $dateFin->format('Y-m-d 23:59:59')]);

        // Filtres additionnels
        if ($request->filled('caissier') && $utilisateurColumn) {
            $query->where($utilisateurColumn, $request->caissier);
        }
        if ($request->filled('mode_paiement') && $modePayementColumn) {
            $query->where($modePayementColumn, $request->mode_paiement);
        }

        $sales = $query->orderBy($dateColumn, 'desc')->get();

        // Calculs statistiques
        $totalTTC = 0;
        $totalHT = 0;
        foreach ($sales as $sale) {
            if ($montantTTCColumn && property_exists($sale, $montantTTCColumn)) {
                $totalTTC += $sale->{$montantTTCColumn} ?? 0;
            }
            if ($montantHTColumn && property_exists($sale, $montantHTColumn)) {
                $totalHT += $sale->{$montantHTColumn} ?? 0;
            }
        }

        $statistiques = [
            'total_ventes' => $totalTTC,
            'total_ventes_ht' => $totalHT,
            'nombre_factures' => $sales->count(),
            'ticket_moyen' => $sales->count() > 0 ? ($totalTTC / $sales->count()) : 0,
            'periode' => $dateDebut->format('d/m/Y') . ($dateDebut->eq($dateFin) ? '' : ' - ' . $dateFin->format('d/m/Y')),
            'colonnes' => [
                'date' => $dateColumn,
                'ref' => $refColumn,
                'montant_ttc' => $montantTTCColumn,
                'montant_ht' => $montantHTColumn,
                'mode_paiement' => $modePayementColumn,
                'utilisateur' => $utilisateurColumn
            ]
        ];

        return [
            'sales' => $sales,
            'statistiques' => $statistiques,
            'periode' => $dateDebut->format('d/m/Y') . ($dateDebut->eq($dateFin) ? '' : ' - ' . $dateFin->format('d/m/Y')),
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ];
    }

    /**
     * Génération rapide de rapport
     */
    private function generateQuickReport($type, $dateDebut, $dateFin)
    {
        $request = new Request([
            'type_rapport' => $type,
            'periode_type' => 'periode',
            'date_debut' => $dateDebut->format('Y-m-d'),
            'date_fin' => $dateFin->format('Y-m-d'),
            'format' => 'view'
        ]);
        
        return $this->generate($request);
    }

    /**
     * Suggestions de rapports basées sur les données disponibles
     */
    private function getReportSuggestions()
    {
        $suggestions = [];
        
        try {
            // Vérification des ventes récentes
            if (Schema::hasTable('FACTURE_VNT')) {
                $columns = Schema::getColumnListing('FACTURE_VNT');
                $dateColumn = $this->findColumn($columns, ['fctv_date', 'FCTV_DATE', 'DATE_FACTURE', 'created_at']);
                
                if ($dateColumn) {
                    $ventesAujourdhui = DB::table('FACTURE_VNT')
                        ->whereDate($dateColumn, today())
                        ->count();
                    
                    if ($ventesAujourdhui > 0) {
                        $suggestions[] = [
                            'type' => 'success',
                            'title' => 'Ventes du jour disponibles',
                            'message' => "$ventesAujourdhui transactions enregistrées aujourd'hui",
                            'action' => [
                                'url' => route('admin.reports.generate') . '?type_rapport=ventes&periode_type=jour&date_debut=' . today()->format('Y-m-d') . '&format=view',
                                'text' => 'Voir le rapport'
                            ]
                        ];
                    }
                }
            }
            
            // Vérification des articles en rupture
            if (Schema::hasTable('ARTICLE')) {
                $columns = Schema::getColumnListing('ARTICLE');
                $stockColumn = $this->findColumn($columns, ['ART_QTE_STOCK', 'art_qte_stock', 'quantite_stock', 'stock']);
                
                if ($stockColumn) {
                    $articlesRupture = DB::table('ARTICLE')
                        ->where($stockColumn, '<=', 0)
                        ->count();
                    
                    if ($articlesRupture > 0) {
                        $suggestions[] = [
                            'type' => 'warning',
                            'title' => 'Articles en rupture détectés',
                            'message' => "$articlesRupture articles nécessitent un réapprovisionnement",
                            'action' => [
                                'url' => route('admin.reports.generate') . '?type_rapport=stock&periode_type=jour&date_debut=' . today()->format('Y-m-d') . '&format=view&stock_minimum=0',
                                'text' => 'Voir les ruptures'
                            ]
                        ];
                    }
                }
            }
            
        } catch (\Exception $e) {
            Log::warning('Erreur lors de la génération des suggestions: ' . $e->getMessage());
        }
        
        return $suggestions;
    }

    /**
     * Export générique des données des modals
     */
    public function exportModalData(Request $request)
    {
        try {
            $type = $request->get('type');
            $format = $request->get('format', 'pdf'); // pdf, excel, csv
            
            // Récupérer les données selon le type
            $data = $this->getDataForExport($type, $request);
            
            switch ($format) {
                case 'pdf':
                    return $this->exportToPDF($type, $data);
                case 'excel':
                    return $this->exportToExcel($type, $data);
                case 'csv':
                    return $this->exportToCSV($type, $data);
                default:
                    return response()->json(['error' => 'Format non supporté'], 400);
            }
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de l\'export: ' . $e->getMessage()], 500);
        }
    }
    
    private function getDataForExport($type, $request)
    {
        $controller = new AdvancedAnalyticsController();
        
        switch ($type) {
            case 'chiffre-affaires':
                return $controller->getChiffreAffairesDetails($request)->getData();
            case 'stock-rupture':
                return $controller->getArticlesRuptureDetails($request)->getData();
            case 'top-clients':
                return $controller->getTopClientsDetails($request)->getData();
            case 'performance-horaire':
                return $controller->getPerformanceHoraireDetails($request)->getData();
            case 'modes-paiement':
                return $controller->getModesPaiementDetails($request)->getData();
            case 'etat-tables':
                return $controller->getEtatTablesDetails($request)->getData();
            default:
                throw new \Exception('Type d\'export non reconnu');
        }
    }
    
    private function exportToPDF($type, $data)
    {
        $pdf = new \TCPDF();
        $pdf->SetCreator('AccessPOS Pro');
        $pdf->SetAuthor('Système AccessPOS');
        $pdf->SetTitle('Rapport ' . ucfirst(str_replace('-', ' ', $type)));
        
        // Configuration du PDF
        $pdf->SetMargins(15, 27, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(TRUE, 25);
        
        // Ajouter une page
        $pdf->AddPage();
        
        // En-tête personnalisé
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'AccessPOS Pro - Rapport Détaillé', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 5, 'Type: ' . ucfirst(str_replace('-', ' ', $type)), 0, 1, 'C');
        $pdf->Cell(0, 5, 'Généré le: ' . date('d/m/Y à H:i'), 0, 1, 'C');
        $pdf->Ln(10);
        
        // Contenu selon le type
        $this->addPDFContent($pdf, $type, $data);
        
        // Output
        $filename = 'accesspos_' . $type . '_' . date('Y-m-d_H-i-s') . '.pdf';
        return $pdf->Output($filename, 'D');
    }
    
    private function addPDFContent($pdf, $type, $data)
    {
        switch ($type) {
            case 'chiffre-affaires':
                $this->addChiffreAffairesPDFContent($pdf, $data);
                break;
            case 'stock-rupture':
                $this->addStockRupturePDFContent($pdf, $data);
                break;
            case 'top-clients':
                $this->addTopClientsPDFContent($pdf, $data);
                break;
            case 'performance-horaire':
                $this->addPerformanceHorairePDFContent($pdf, $data);
                break;
            case 'modes-paiement':
                $this->addModesPaiementPDFContent($pdf, $data);
                break;
            case 'etat-tables':
                $this->addEtatTablesPDFContent($pdf, $data);
                break;
        }
    }
    
    private function addChiffreAffairesPDFContent($pdf, $data)
    {
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 8, 'Analyse du Chiffre d\'Affaires', 0, 1, 'L');
        $pdf->Ln(5);
        
        // KPIs principaux
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(45, 6, 'CA du jour:', 0, 0, 'L');
        $pdf->Cell(0, 6, number_format($data->ca_jour ?? 0, 2) . ' €', 0, 1, 'L');
        
        $pdf->Cell(45, 6, 'CA d\'hier:', 0, 0, 'L');
        $pdf->Cell(0, 6, number_format($data->ca_hier ?? 0, 2) . ' €', 0, 1, 'L');
        
        $pdf->Cell(45, 6, 'Évolution:', 0, 0, 'L');
        $evolutionColor = ($data->evolution ?? 0) >= 0 ? [0, 128, 0] : [255, 0, 0];
        $pdf->SetTextColor($evolutionColor[0], $evolutionColor[1], $evolutionColor[2]);
        $pdf->Cell(0, 6, ($data->evolution >= 0 ? '+' : '') . number_format($data->evolution ?? 0, 2) . ' %', 0, 1, 'L');
        $pdf->SetTextColor(0, 0, 0);
        
        $pdf->Cell(45, 6, 'Nb tickets:', 0, 0, 'L');
        $pdf->Cell(0, 6, number_format($data->nb_tickets ?? 0), 0, 1, 'L');
        
        $pdf->Cell(45, 6, 'Ticket moyen:', 0, 0, 'L');
        $pdf->Cell(0, 6, number_format($data->ticket_moyen ?? 0, 2) . ' €', 0, 1, 'L');
        
        $pdf->Ln(10);
        
        // Recommandations
        if (isset($data->recommandations) && count($data->recommandations) > 0) {
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 8, 'Recommandations', 0, 1, 'L');
            $pdf->SetFont('helvetica', '', 9);
            
            foreach ($data->recommandations as $recommandation) {
                $pdf->Cell(5, 5, '•', 0, 0, 'C');
                $pdf->Cell(0, 5, $recommandation['title'] . ': ' . $recommandation['description'], 0, 1, 'L');
            }
        }
    }
    
    private function addStockRupturePDFContent($pdf, $data)
    {
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 8, 'Articles en Rupture de Stock', 0, 1, 'L');
        $pdf->Ln(5);
        
        // Statistiques
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, 'Nombre d\'articles en rupture: ' . count($data->articles_rupture ?? []), 0, 1, 'L');
        $pdf->Cell(0, 6, 'Articles en stock faible: ' . count($data->articles_stock_faible ?? []), 0, 1, 'L');
        $pdf->Ln(5);
        
        // Tableau des articles en rupture
        if (isset($data->articles_rupture) && count($data->articles_rupture) > 0) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(80, 6, 'Article', 1, 0, 'C');
            $pdf->Cell(30, 6, 'Stock Actuel', 1, 0, 'C');
            $pdf->Cell(30, 6, 'Stock Min', 1, 0, 'C');
            $pdf->Cell(40, 6, 'Dernière Vente', 1, 1, 'C');
            
            $pdf->SetFont('helvetica', '', 8);
            foreach ($data->articles_rupture as $article) {
                $pdf->Cell(80, 5, substr($article->designation ?? '', 0, 35), 1, 0, 'L');
                $pdf->Cell(30, 5, $article->stock_actuel ?? '0', 1, 0, 'C');
                $pdf->Cell(30, 5, $article->stock_minimum ?? '0', 1, 0, 'C');
                $pdf->Cell(40, 5, substr($article->derniere_vente ?? 'N/A', 0, 15), 1, 1, 'C');
            }
        }
    }
    
    private function addTopClientsPDFContent($pdf, $data)
    {
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 8, 'Top Clients du Restaurant', 0, 1, 'L');
        $pdf->Ln(5);
        
        // Statistiques globales
        $pdf->SetFont('helvetica', '', 10);
        $stats = $data->statistiques_globales ?? [];
        $pdf->Cell(0, 6, 'CA total des top clients: ' . number_format($stats['ca_total_top_clients'] ?? 0, 2) . ' €', 0, 1, 'L');
        $pdf->Cell(0, 6, 'Nombre total de visites: ' . number_format($stats['nb_visites_total'] ?? 0), 0, 1, 'L');
        $pdf->Cell(0, 6, 'Ticket moyen global: ' . number_format($stats['ticket_moyen_global'] ?? 0, 2) . ' €', 0, 1, 'L');
        $pdf->Ln(5);
        
        // Tableau des top clients
        if (isset($data->top_clients) && count($data->top_clients) > 0) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(50, 6, 'Client', 1, 0, 'C');
            $pdf->Cell(30, 6, 'CA Total', 1, 0, 'C');
            $pdf->Cell(25, 6, 'Visites', 1, 0, 'C');
            $pdf->Cell(30, 6, 'Ticket Moyen', 1, 0, 'C');
            $pdf->Cell(25, 6, 'Fidélité', 1, 1, 'C');
            
            $pdf->SetFont('helvetica', '', 8);
            foreach (array_slice($data->top_clients, 0, 15) as $client) {
                $pdf->Cell(50, 5, substr($client->nom . ' ' . $client->prenom, 0, 20), 1, 0, 'L');
                $pdf->Cell(30, 5, number_format($client->ca_total, 2) . '€', 1, 0, 'R');
                $pdf->Cell(25, 5, $client->nb_visites, 1, 0, 'C');
                $pdf->Cell(30, 5, number_format($client->ticket_moyen, 2) . '€', 1, 0, 'R');
                $pdf->Cell(25, 5, number_format($client->taux_fidelite, 1) . '%', 1, 1, 'C');
            }
        }
    }
    
    private function addPerformanceHorairePDFContent($pdf, $data)
    {
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 8, 'Performance par Heure', 0, 1, 'L');
        $pdf->Ln(5);
        
        // Statistiques
        $stats = $data->statistiques ?? [];
        $pdf->SetFont('helvetica', '', 10);
        if (isset($stats['heure_plus_performante'])) {
            $pdf->Cell(0, 6, 'Heure la plus performante: ' . $stats['heure_plus_performante']->heure . 'h (' . 
                      number_format($stats['heure_plus_performante']->ca, 2) . ' €)', 0, 1, 'L');
        }
        $pdf->Cell(0, 6, 'Amplitude CA: ' . number_format($stats['amplitude_ca'] ?? 0, 2) . ' €', 0, 1, 'L');
        $pdf->Cell(0, 6, 'Heures d\'activité: ' . ($stats['heures_activite'] ?? 0), 0, 1, 'L');
        $pdf->Ln(5);
        
        // Tableau performance horaire
        if (isset($data->performance_horaire) && count($data->performance_horaire) > 0) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(25, 6, 'Heure', 1, 0, 'C');
            $pdf->Cell(35, 6, 'CA (€)', 1, 0, 'C');
            $pdf->Cell(25, 6, 'Tickets', 1, 0, 'C');
            $pdf->Cell(35, 6, 'Ticket Moyen', 1, 0, 'C');
            $pdf->Cell(30, 6, '% du CA', 1, 1, 'C');
            
            $pdf->SetFont('helvetica', '', 8);
            foreach ($data->performance_horaire as $heure) {
                $pdf->Cell(25, 5, sprintf('%02d:00', $heure->heure), 1, 0, 'C');
                $pdf->Cell(35, 5, number_format($heure->ca, 2), 1, 0, 'R');
                $pdf->Cell(25, 5, $heure->nb_tickets, 1, 0, 'C');
                $pdf->Cell(35, 5, number_format($heure->ticket_moyen, 2), 1, 0, 'R');
                $pdf->Cell(30, 5, number_format($heure->pourcentage_ca, 1) . '%', 1, 1, 'R');
            }
        }
    }
    
    private function addModesPaiementPDFContent($pdf, $data)
    {
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 8, 'Modes de Paiement Détaillés', 0, 1, 'L');
        $pdf->Ln(5);
        
        // Statistiques
        $stats = $data->statistiques ?? [];
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, 'Montant total: ' . number_format($stats['montant_total'] ?? 0, 2) . ' €', 0, 1, 'L');
        $pdf->Cell(0, 6, 'Modes utilisés: ' . ($stats['nb_modes_utilises'] ?? 0), 0, 1, 'L');
        $pdf->Cell(0, 6, 'Transaction moyenne: ' . number_format($stats['transaction_moyenne'] ?? 0, 2) . ' €', 0, 1, 'L');
        $pdf->Ln(5);
        
        // Tableau des modes de paiement
        if (isset($data->modes_paiement) && count($data->modes_paiement) > 0) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(50, 6, 'Mode de Paiement', 1, 0, 'C');
            $pdf->Cell(40, 6, 'Montant Total', 1, 0, 'C');
            $pdf->Cell(30, 6, 'Transactions', 1, 0, 'C');
            $pdf->Cell(30, 6, 'Pourcentage', 1, 1, 'C');
            
            $pdf->SetFont('helvetica', '', 8);
            foreach ($data->modes_paiement as $mode) {
                $pdf->Cell(50, 5, $mode->mode, 1, 0, 'L');
                $pdf->Cell(40, 5, number_format($mode->montant_total, 2) . ' €', 1, 0, 'R');
                $pdf->Cell(30, 5, $mode->nb_transactions, 1, 0, 'C');
                $pdf->Cell(30, 5, number_format($mode->pourcentage, 1) . '%', 1, 1, 'R');
            }
        }
    }
    
    private function addEtatTablesPDFContent($pdf, $data)
    {
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 8, 'État des Tables en Temps Réel', 0, 1, 'L');
        $pdf->Ln(5);
        
        // Statistiques
        $stats = $data->stats ?? [];
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, 'Taux d\'occupation: ' . ($stats['taux_occupation'] ?? 0) . '%', 0, 1, 'L');
        $pdf->Cell(0, 6, 'Temps moyen de service: ' . ($stats['temps_moyen_service'] ?? 0) . ' min', 0, 1, 'L');
        $pdf->Cell(0, 6, 'CA des tables: ' . number_format($stats['chiffre_affaires_tables'] ?? 0, 2) . ' €', 0, 1, 'L');
        $pdf->Ln(5);
        
        // Tableau des tables
        if (isset($data->tables) && count($data->tables) > 0) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(30, 6, 'Table', 1, 0, 'C');
            $pdf->Cell(25, 6, 'Zone', 1, 0, 'C');
            $pdf->Cell(20, 6, 'Statut', 1, 0, 'C');
            $pdf->Cell(25, 6, 'Couverts', 1, 0, 'C');
            $pdf->Cell(30, 6, 'CA Jour', 1, 0, 'C');
            $pdf->Cell(30, 6, 'Services', 1, 1, 'C');
            
            $pdf->SetFont('helvetica', '', 8);
            foreach ($data->tables as $table) {
                $pdf->Cell(30, 5, $table->nom, 1, 0, 'L');
                $pdf->Cell(25, 5, substr($table->zone ?? '', 0, 10), 1, 0, 'L');
                $pdf->Cell(20, 5, substr($table->statut, 0, 8), 1, 0, 'C');
                $pdf->Cell(25, 5, $table->nb_couverts, 1, 0, 'C');
                $pdf->Cell(30, 5, number_format($table->ca_jour ?? 0, 2) . '€', 1, 0, 'R');
                $pdf->Cell(30, 5, $table->nb_services ?? 0, 1, 1, 'C');
            }
        }
    }
    
    private function exportToExcel($type, $data)
    {
        // Ici, on utiliserait PHPSpreadsheet ou une autre librairie Excel
        // Pour simplifier, on retourne un CSV avec en-têtes Excel
        return $this->exportToCSV($type, $data, true);
    }
    
    private function exportToCSV($type, $data, $isExcel = false)
    {
        $filename = 'accesspos_' . $type . '_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($type, $data) {
            $file = fopen('php://output', 'w');
            
            // BOM pour Excel UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            switch ($type) {
                case 'chiffre-affaires':
                    $this->writeChiffreAffairesCSV($file, $data);
                    break;
                case 'stock-rupture':
                    $this->writeStockRuptureCSV($file, $data);
                    break;
                case 'top-clients':
                    $this->writeTopClientsCSV($file, $data);
                    break;
                case 'performance-horaire':
                    $this->writePerformanceHoraireCSV($file, $data);
                    break;
                case 'modes-paiement':
                    $this->writeModesPaiementCSV($file, $data);
                    break;
                case 'etat-tables':
                    $this->writeEtatTablesCSV($file, $data);
                    break;
            }
            
            fclose($file);
        };
        
        return response()->streamDownload($callback, $filename, $headers);
    }
    
    private function writeChiffreAffairesCSV($file, $data)
    {
        fputcsv($file, ['Rapport Chiffre d\'Affaires - AccessPOS Pro'], ';');
        fputcsv($file, ['Généré le', date('d/m/Y à H:i')], ';');
        fputcsv($file, [], ';');
        
        fputcsv($file, ['Métrique', 'Valeur'], ';');
        fputcsv($file, ['CA du jour', number_format($data->ca_jour ?? 0, 2) . ' €'], ';');
        fputcsv($file, ['CA d\'hier', number_format($data->ca_hier ?? 0, 2) . ' €'], ';');
        fputcsv($file, ['Évolution', number_format($data->evolution ?? 0, 2) . ' %'], ';');
        fputcsv($file, ['Nombre de tickets', $data->nb_tickets ?? 0], ';');
        fputcsv($file, ['Ticket moyen', number_format($data->ticket_moyen ?? 0, 2) . ' €'], ';');
    }
    
    private function writeStockRuptureCSV($file, $data)
    {
        fputcsv($file, ['Articles en Rupture de Stock - AccessPOS Pro'], ';');
        fputcsv($file, ['Généré le', date('d/m/Y à H:i')], ';');
        fputcsv($file, [], ';');
        
        fputcsv($file, ['Article', 'Référence', 'Stock Actuel', 'Stock Minimum', 'Prix Vente', 'Dernière Vente'], ';');
        
        if (isset($data->articles_rupture)) {
            foreach ($data->articles_rupture as $article) {
                fputcsv($file, [
                    $article->designation ?? '',
                    $article->reference ?? '',
                    $article->stock_actuel ?? 0,
                    $article->stock_minimum ?? 0,
                    number_format($article->prix_vente ?? 0, 2) . ' €',
                    $article->derniere_vente ?? 'N/A'
                ], ';');
            }
        }
    }
    
    private function writeTopClientsCSV($file, $data)
    {
        fputcsv($file, ['Top Clients - AccessPOS Pro'], ';');
        fputcsv($file, ['Généré le', date('d/m/Y à H:i')], ';');
        fputcsv($file, [], ';');
        
        fputcsv($file, ['Nom', 'Prénom', 'Téléphone', 'Email', 'CA Total', 'Nb Visites', 'Ticket Moyen', 'Taux Fidélité', 'Dernière Visite'], ';');
        
        if (isset($data->top_clients)) {
            foreach ($data->top_clients as $client) {
                fputcsv($file, [
                    $client->nom ?? '',
                    $client->prenom ?? '',
                    $client->telephone ?? '',
                    $client->email ?? '',
                    number_format($client->ca_total ?? 0, 2) . ' €',
                    $client->nb_visites ?? 0,
                    number_format($client->ticket_moyen ?? 0, 2) . ' €',
                    number_format($client->taux_fidelite ?? 0, 1) . '%',
                    $client->derniere_visite ?? 'N/A'
                ], ';');
            }
        }
    }
    
    private function writePerformanceHoraireCSV($file, $data)
    {
        fputcsv($file, ['Performance par Heure - AccessPOS Pro'], ';');
        fputcsv($file, ['Généré le', date('d/m/Y à H:i')], ';');
        fputcsv($file, [], ';');
        
        fputcsv($file, ['Heure', 'CA (€)', 'Nb Tickets', 'Ticket Moyen (€)', '% du CA Total', 'Articles Vendus'], ';');
        
        if (isset($data->performance_horaire)) {
            foreach ($data->performance_horaire as $heure) {
                fputcsv($file, [
                    sprintf('%02d:00', $heure->heure),
                    number_format($heure->ca ?? 0, 2),
                    $heure->nb_tickets ?? 0,
                    number_format($heure->ticket_moyen ?? 0, 2),
                    number_format($heure->pourcentage_ca ?? 0, 1) . '%',
                    $heure->articles_vendus ?? 0
                ], ';');
            }
        }
    }
    
    private function writeModesPaiementCSV($file, $data)
    {
        fputcsv($file, ['Modes de Paiement - AccessPOS Pro'], ';');
        fputcsv($file, ['Généré le', date('d/m/Y à H:i')], ';');
        fputcsv($file, [], ';');
        
        fputcsv($file, ['Mode de Paiement', 'Montant Total (€)', 'Nb Transactions', 'Montant Moyen (€)', 'Pourcentage'], ';');
        
        if (isset($data->modes_paiement)) {
            foreach ($data->modes_paiement as $mode) {
                fputcsv($file, [
                    $mode->mode ?? '',
                    number_format($mode->montant_total ?? 0, 2),
                    $mode->nb_transactions ?? 0,
                    number_format($mode->montant_moyen ?? 0, 2),
                    number_format($mode->pourcentage ?? 0, 1) . '%'
                ], ';');
            }
        }
    }
    
    private function writeEtatTablesCSV($file, $data)
    {
        fputcsv($file, ['État des Tables - AccessPOS Pro'], ';');
        fputcsv($file, ['Généré le', date('d/m/Y à H:i')], ';');
        fputcsv($file, [], ';');
        
        fputcsv($file, ['Table', 'Zone', 'Statut', 'Nb Couverts', 'CA du Jour (€)', 'Nb Services', 'Durée Occupation'], ';');
        
        if (isset($data->tables)) {
            foreach ($data->tables as $table) {
                fputcsv($file, [
                    $table->nom ?? '',
                    $table->zone ?? '',
                    $table->statut ?? '',
                    $table->nb_couverts ?? 0,
                    number_format($table->ca_jour ?? 0, 2),
                    $table->nb_services ?? 0,
                    $table->duree_occupation ?? 'N/A'
                ], ';');
            }
        }
    }
}
