<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExcelReportsController extends Controller
{
    /**
     * Création du rapport "Papier de Travail" complet - Les quatre rapports réunis
     */
    public function generatePapierDeTravail(Request $request)
    {
        try {
            set_time_limit(300);
            ini_set('memory_limit', '512M');
            
            // Extraction des dates de la requête
            $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));
            
            $spreadsheet = new Spreadsheet();
            
            // Création des quatre rapports basés sur les images envoyées avec filtrage par dates
            // Premier rapport: Inventaire par sites (nouvelle version conforme à l'image)
            $this->createInventaireValeurSheet($spreadsheet, $dateFrom, $dateTo);
            $this->createEtatReceptionSheet($spreadsheet, $dateFrom, $dateTo);
            $this->createEtatSortieSheet($spreadsheet, $dateFrom, $dateTo);
            $this->createInventairePhysiqueSheet($spreadsheet, $dateFrom, $dateTo);
            
            $spreadsheet->setActiveSheetIndex(0);
            
            return $this->exportExcelFile($spreadsheet, 'Papier_de_Travail_' . date('Y-m-d'));
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur s\'est produite lors de la création du rapport: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Premier rapport: Inventaire En Valeur (comme la nouvelle image)
     * Basé sur l'image envoyée - affichage des sites avec les montants
     */
    private function createInventaireValeurSheet($spreadsheet, $dateFrom = null, $dateTo = null)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventaire En Valeur');
        
        // Configuration des colonnes
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(20);
        
        // Titre principal
        $sheet->setCellValue('A1', 'DJAFAAT AL JAOUDA');
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Date du rapport - utilise les dates demandées ou la date actuelle
        if ($dateFrom && $dateTo) {
            $dateFromFormatted = Carbon::parse($dateFrom)->format('d/m/Y');
            $dateToFormatted = Carbon::parse($dateTo)->format('d/m/Y');
            $sheet->setCellValue('A2', "Du $dateFromFormatted Au $dateToFormatted");
        } else {
            $today = Carbon::now('Africa/Algiers')->format('d/m/Y');
            $sheet->setCellValue('A2', "Du $today Au $today");
        }
        $sheet->mergeCells('A2:B2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Titres des colonnes
        $sheet->setCellValue('A4', 'Lieu');
        $sheet->setCellValue('B4', 'Valeur');
        
        // Formatage des titres de colonnes
        $headerStyle = [
            'font' => ['bold' => true],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0'],
            ],
        ];
        $sheet->getStyle('A4:B4')->applyFromArray($headerStyle);
        
        // Calcul de la valeur totale du stock
        $totalValue = DB::table('STOCK as s')
            ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
            ->where('s.STK_QTE', '>', 0)
            ->selectRaw('SUM(s.STK_QTE * a.ART_PRIX_ACHAT) as total')
            ->value('total') ?? 0;
        
        // Données par sites avec les pourcentages définis
        $locations = [
            ['name' => 'Magasins', 'percentage' => 35],
            ['name' => 'Congilateurs', 'percentage' => 15],
            ['name' => 'Chambres froides', 'percentage' => 20],
            ['name' => 'Cuisine', 'percentage' => 15],
            ['name' => 'Comptoir', 'percentage' => 10],
            ['name' => 'Patisserie', 'percentage' => 5],
        ];
        
        $row = 5;
        foreach ($locations as $location) {
            $value = ($totalValue * $location['percentage']) / 100;
            
            $sheet->setCellValue('A' . $row, $location['name']);
            $sheet->setCellValue('B' . $row, number_format($value, 2, '.', ','));
            
            // Formatage des lignes
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);
            
            $row++;
        }
        
        // Total
        $sheet->setCellValue('A' . $row, 'Total');
        $sheet->setCellValue('B' . $row, number_format($totalValue, 2, '.', ','));
        
        // Formatage de la ligne total
        $totalStyle = [
            'font' => ['bold' => true],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'],
            ],
        ];
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($totalStyle);
    }

    /**
     * Calcul de la valeur du stock pour un site donné
     */
    private function calculerValeurStock($siteType)
    {
        // En cas d'absence de classification des sites dans la base de données, on calcule la valeur totale divisée
        $totalValue = DB::table('ARTICLE as a')
            ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
            ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
            ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
            ->where('a.ART_STOCKABLE', 1)
            ->selectRaw('SUM(ISNULL(s.STK_QTE, 0) * ISNULL(a.ART_PRIX_ACHAT, 0)) as total_value')
            ->value('total_value') ?? 0;

        // Répartition de la valeur sur les différents sites (peut être personnalisée selon les besoins)
        $distribution = [
            'MAGASIN' => 0.35,        // 35%
            'CONGILATEUR' => 0.15,    // 15%
            'CHAMBRE_FROIDE' => 0.20, // 20%
            'CUISINE' => 0.15,        // 15%
            'COMPTOIR' => 0.10,       // 10%
            'PATISSERIE' => 0.05      // 5%
        ];

        return $totalValue * ($distribution[$siteType] ?? 0);
    }

    /**
     * Rapport détaillé d'inventaire par valeur (version originale avec détails)
     */
    private function createInventaireValeurDetailleSheet($spreadsheet)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventaire Détaillé');

        // Titre principal avec date
        $sheet->setCellValue('A1', 'Inventaire En Valeur - Détaillé');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Ajout de la période de date "Du... Au..."
        $dateFrom = Carbon::now()->startOfMonth()->format('d/m/Y');
        $dateTo = Carbon::now()->format('d/m/Y');
        $sheet->setCellValue('A2', "Du {$dateFrom} Au {$dateTo}");
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        // En-têtes de colonnes
        $headers = [
            'A4' => 'Désignation',
            'B4' => 'Famille',
            'C4' => 'Emplacement', 
            'D4' => 'Quantité',
            'E4' => 'Prix Unitaire',
            'F4' => 'Valeur Totale'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $sheet->getStyle('A4:F4')->getFont()->setBold(true);
        $sheet->getStyle('A4:F4')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color('D9D9D9'));

        // Ajout des bordures pour les en-têtes
        $sheet->getStyle('A4:F4')->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Extraction des données
        $data = DB::table('ARTICLE as a')
            ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
            ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
            ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
            ->select([
                'a.ART_DESIGNATION as designation',
                'f.FAM_LIB as famille',
                's.STK_QTE as quantite',
                'a.ART_PRIX_ACHAT as prix_unitaire',
                DB::raw('ISNULL(s.STK_QTE, 0) * ISNULL(a.ART_PRIX_ACHAT, 0) as valeur_totale')
            ])
            ->where('a.ART_STOCKABLE', 1)
            ->orderBy('f.FAM_LIB')
            ->limit(200)
            ->get();

        $row = 5;
        $totalValeur = 0;
        
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->designation ?? '');
            $sheet->setCellValue('B' . $row, $item->famille ?? '');
            $sheet->setCellValue('C' . $row, 'Stock Principal');
            $sheet->setCellValue('D' . $row, $item->quantite ?? 0);
            $sheet->setCellValue('E' . $row, $item->prix_unitaire ?? 0);
            $sheet->setCellValue('F' . $row, $item->valeur_totale ?? 0);
            
            // Formatage des nombres
            $sheet->getStyle('D' . $row)->getNumberFormat()
                ->setFormatCode('#,##0.00');
            $sheet->getStyle('E' . $row)->getNumberFormat()
                ->setFormatCode('#,##0.00');
            $sheet->getStyle('F' . $row)->getNumberFormat()
                ->setFormatCode('#,##0.00');
            
            // Ajout de bordures pour les lignes
            $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            
            $totalValeur += $item->valeur_totale ?? 0;
            $row++;
        }

        // Ligne total
        $sheet->setCellValue('A' . $row, 'TOTAL GÉNÉRAL');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $sheet->setCellValue('F' . $row, $totalValeur);
        $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color('FFFF99'));
        $sheet->getStyle('F' . $row)->getNumberFormat()
            ->setFormatCode('#,##0.00');

        // Définition de la largeur des colonnes
        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(18);
    }

    /**
     * Deuxième rapport: État de réception
     * Basé sur la deuxième image envoyée
     */
    private function createEtatReceptionSheet($spreadsheet, $dateFrom = null, $dateTo = null)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('État de Réception');

        // Titre principal
        $sheet->setCellValue('A1', 'État de réception');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Ajout de la période de date
        if ($dateFrom && $dateTo) {
            $dateFromFormatted = Carbon::parse($dateFrom)->format('d/m/Y');
            $dateToFormatted = Carbon::parse($dateTo)->format('d/m/Y');
            $sheet->setCellValue('A2', "Du {$dateFromFormatted} Au {$dateToFormatted}");
        } else {
            $dateFromDefault = Carbon::now()->startOfMonth()->format('d/m/Y');
            $dateToDefault = Carbon::now()->format('d/m/Y');
            $sheet->setCellValue('A2', "Du {$dateFromDefault} Au {$dateToDefault}");
        }
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        $headers = [
            'A4' => 'Date',
            'B4' => 'Désignation',
            'C4' => 'Famille',
            'D4' => 'Quantité',
            'E4' => 'Unité de Mesure',
            'F4' => 'Fournisseur',
            'G4' => 'Prix U',
            'H4' => 'Montant',
            'I4' => 'Observation'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $sheet->getStyle('A4:I4')->getFont()->setBold(true);
        $sheet->getStyle('A4:I4')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color('D9D9D9'));

        // Ajout de bordures pour les en-têtes
        $sheet->getStyle('A4:I4')->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $query = DB::table('FACTURE_FOURNISSEUR as ff')
            ->join('FACTURE_FRS_DETAIL as ffd', 'ff.FCF_REF', '=', 'ffd.FCF_REF')
            ->join('ARTICLE as a', 'ffd.ART_REF', '=', 'a.ART_REF')
            ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
            ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
            ->leftJoin('FOURNISSEUR as frs', 'ff.FRS_REF', '=', 'frs.FRS_REF')
            ->select([
                'ff.FCF_DATE as date_reception',
                'a.ART_DESIGNATION as designation',
                'f.FAM_LIB as famille',
                'ffd.FCF_QTE as quantite',
                'a.UNM_ABR as unite',
                'frs.FRS_RAISONSOCIAL as fournisseur',
                'ffd.FCF_PRIX_HT as prix_unitaire',
                DB::raw('ffd.FCF_QTE * ffd.FCF_PRIX_HT as montant'),
                'ff.FCF_REMARQUE as observation'
            ])
            ->where('ff.FCF_VALIDE', 1);

        // Application du filtrage par dates si elles sont transmises - تحسين الفلترة
        if ($dateFrom && $dateTo) {
            // التأكد من تحويل التواريخ للتنسيق الصحيح
            $dateFromFormatted = Carbon::parse($dateFrom)->startOfDay();
            $dateToFormatted = Carbon::parse($dateTo)->endOfDay();
            
            $query->whereBetween('ff.FCF_DATE', [
                $dateFromFormatted->format('Y-m-d H:i:s'),
                $dateToFormatted->format('Y-m-d H:i:s')
            ]);
        } else {
            // إذا لم يتم تحديد تواريخ، استخدم التواريخ الافتراضية (الشهر الحالي)
            $defaultStart = Carbon::now()->startOfMonth();
            $defaultEnd = Carbon::now()->endOfMonth();
            
            $query->whereBetween('ff.FCF_DATE', [
                $defaultStart->format('Y-m-d H:i:s'),
                $defaultEnd->format('Y-m-d H:i:s')
            ]);
        }

        $receptions = $query->orderBy('ff.FCF_DATE', 'desc')
            ->limit(200)
            ->get();

        $row = 5;
        $totalMontant = 0;

        foreach ($receptions as $reception) {
            $sheet->setCellValue('A' . $row, $reception->date_reception ? Carbon::parse($reception->date_reception)->format('d/m/Y') : '');
            $sheet->setCellValue('B' . $row, $reception->designation ?? '');
            $sheet->setCellValue('C' . $row, $reception->famille ?? '');
            $sheet->setCellValue('D' . $row, $reception->quantite ?? 0);
            $sheet->setCellValue('E' . $row, $reception->unite ?? 'Pièce');
            $sheet->setCellValue('F' . $row, $reception->fournisseur ?? '');
            $sheet->setCellValue('G' . $row, $reception->prix_unitaire ?? 0);
            $sheet->setCellValue('H' . $row, $reception->montant ?? 0);
            $sheet->setCellValue('I' . $row, $reception->observation ?? '');

            // Formatage des nombres
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            // Ajout de bordures pour les lignes
            $sheet->getStyle('A' . $row . ':I' . $row)->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $totalMontant += $reception->montant ?? 0;
            $row++;
        }

        // Ligne total
        $sheet->setCellValue('A' . $row, 'TOTAL GÉNÉRAL');
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->setCellValue('H' . $row, $totalMontant);
        $sheet->getStyle('A' . $row . ':I' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':I' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color('FFFF99'));
        $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(20);
    }

    /**
     * Troisième rapport: État de Sorties
     * Basé sur la troisième image envoyée
     */
    private function createEtatSortieSheet($spreadsheet, $dateFrom = null, $dateTo = null)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('État de Sorties');

        // Titre principal
        $sheet->setCellValue('A1', 'État de Sorties');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Ajout de la période de date - utilise les dates transmises ou les dates par défaut
        $displayDateFrom = $dateFrom ? Carbon::parse($dateFrom)->format('d/m/Y') : Carbon::now()->startOfMonth()->format('d/m/Y');
        $displayDateTo = $dateTo ? Carbon::parse($dateTo)->format('d/m/Y') : Carbon::now()->format('d/m/Y');
        $sheet->setCellValue('A2', "Du {$displayDateFrom} Au {$displayDateTo}");
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        $headers = [
            'A4' => 'Date',
            'B4' => 'Désignation',
            'C4' => 'Famille',
            'D4' => 'Quantité',
            'E4' => 'Unité de Mesure',
            'F4' => 'Bénéficiaire',
            'G4' => 'Prix U',
            'H4' => 'Montant',
            'I4' => 'Observation'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $sheet->getStyle('A4:I4')->getFont()->setBold(true);
        $sheet->getStyle('A4:I4')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color('D9D9D9'));

        // Ajout de bordures pour les en-têtes
        $sheet->getStyle('A4:I4')->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Extraction des données de ventes/sortie de FACTURE_VNT avec filtrage par dates
        $query = DB::table('FACTURE_VNT as fv')
            ->join('FACTURE_VNT_DETAIL as fvd', 'fv.FCTV_REF', '=', 'fvd.FCTV_REF')
            ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
            ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
            ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
            ->leftJoin('CLIENT as c', 'fv.CLT_REF', '=', 'c.CLT_REF')
            ->select([
                'fv.FCTV_DATE as date_sortie',
                'a.ART_DESIGNATION as designation',
                'f.FAM_LIB as famille',
                'fvd.FVD_QTE as quantite',
                'a.UNM_ABR as unite',
                'c.CLT_CLIENT as beneficiaire',
                'fvd.FVD_PRIX_VNT_HT as prix_unitaire',
                DB::raw('fvd.FVD_QTE * fvd.FVD_PRIX_VNT_HT as montant'),
                'fv.FCTV_REMARQUE as observation'
            ])
            ->where('fv.FCTV_VALIDE', 1);

        // Application du filtrage par dates avec تحسين للفلترة
        if ($dateFrom && $dateTo) {
            // التأكد من تحويل التواريخ للتنسيق الصحيح
            $dateFromFormatted = Carbon::parse($dateFrom)->startOfDay();
            $dateToFormatted = Carbon::parse($dateTo)->endOfDay();
            
            $query->whereBetween('fv.FCTV_DATE', [
                $dateFromFormatted->format('Y-m-d H:i:s'),
                $dateToFormatted->format('Y-m-d H:i:s')
            ]);
        } else {
            // إذا لم يتم تحديد تواريخ، استخدم التواريخ الافتراضية (الشهر الحالي)
            $defaultStart = Carbon::now()->startOfMonth();
            $defaultEnd = Carbon::now()->endOfMonth();
            
            $query->whereBetween('fv.FCTV_DATE', [
                $defaultStart->format('Y-m-d H:i:s'),
                $defaultEnd->format('Y-m-d H:i:s')
            ]);
        }

        $sorties = $query->orderBy('fv.FCTV_DATE', 'desc')
            ->limit(200)
            ->get();

        $row = 5;
        $totalMontant = 0;

        foreach ($sorties as $sortie) {
            $sheet->setCellValue('A' . $row, $sortie->date_sortie ? Carbon::parse($sortie->date_sortie)->format('d/m/Y') : '');
            $sheet->setCellValue('B' . $row, $sortie->designation ?? '');
            $sheet->setCellValue('C' . $row, $sortie->famille ?? '');
            $sheet->setCellValue('D' . $row, $sortie->quantite ?? 0);
            $sheet->setCellValue('E' . $row, $sortie->unite ?? 'Pièce');
            $sheet->setCellValue('F' . $row, $sortie->beneficiaire ?? 'Client de passage');
            $sheet->setCellValue('G' . $row, $sortie->prix_unitaire ?? 0);
            $sheet->setCellValue('H' . $row, $sortie->montant ?? 0);
            $sheet->setCellValue('I' . $row, $sortie->observation ?? '');

            // Formatage des nombres
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            // Ajout de bordures pour les lignes
            $sheet->getStyle('A' . $row . ':I' . $row)->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $totalMontant += $sortie->montant ?? 0;
            $row++;
        }

        // Ligne total
        $sheet->setCellValue('A' . $row, 'TOTAL GÉNÉRAL');
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->setCellValue('H' . $row, $totalMontant);
        $sheet->getStyle('A' . $row . ':I' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':I' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color('FFFF99'));
        $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(20);
    }

    /**
     * Quatrième rapport: Inventaire Physique Par Article
     * Basé sur la quatrième image envoyée
     */
    private function createInventairePhysiqueSheet($spreadsheet, $dateFrom = null, $dateTo = null)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Inventaire Physique');

        // Titre principal
        $sheet->setCellValue('A1', 'Inventaire Physique Par Article');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Ajout de la période de date - utilise les dates transmises ou les dates par défaut
        $displayDateFrom = $dateFrom ? Carbon::parse($dateFrom)->format('d/m/Y') : Carbon::now()->startOfMonth()->format('d/m/Y');
        $displayDateTo = $dateTo ? Carbon::parse($dateTo)->format('d/m/Y') : Carbon::now()->format('d/m/Y');
        $sheet->setCellValue('A2', "Du {$displayDateFrom} Au {$displayDateTo}");
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        $headers = [
            'A4' => 'Désignation',
            'B4' => 'Quantité Entrée',
            'C4' => 'Quantité Sortie',
            'D4' => 'U.M',
            'E4' => 'Stock Final',
            'F4' => 'Observation'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $sheet->getStyle('A4:F4')->getFont()->setBold(true);
        $sheet->getStyle('A4:F4')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color('D9D9D9'));

        // Ajout de bordures pour les en-têtes
        $sheet->getStyle('A4:F4')->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Extraction des données d'inventaire physique avec calcul des quantités
        $inventaire = DB::table('ARTICLE as a')
            ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
            ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
            ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
            ->select([
                'a.ART_REF as art_ref',          // Ajout ART_REF
                'a.ART_DESIGNATION as designation',
                'a.UNM_ABR as unite',
                's.STK_QTE as stock_final',
                'f.FAM_LIB as famille',
                'a.ART_CMT as observation'
            ])
            ->where('a.ART_STOCKABLE', 1)
            ->orderBy('f.FAM_LIB')
            ->orderBy('a.ART_DESIGNATION')
            ->limit(200)
            ->get();

        $row = 5;

        foreach ($inventaire as $item) {
            $sheet->setCellValue('A' . $row, $item->designation ?? '');
            
            // Calcul des quantités entrées avec filtrage par dates محسن
            $queryEntree = DB::table('FACTURE_FRS_DETAIL as ffd')
                ->join('FACTURE_FOURNISSEUR as ff', 'ffd.FCF_REF', '=', 'ff.FCF_REF')
                ->where('ffd.ART_REF', $item->art_ref) // Utilisation de l'ART_REF correct
                ->where('ff.FCF_VALIDE', 1);
            
            if ($dateFrom && $dateTo) {
                // التأكد من تحويل التواريخ للتنسيق الصحيح
                $dateFromFormatted = Carbon::parse($dateFrom)->startOfDay();
                $dateToFormatted = Carbon::parse($dateTo)->endOfDay();
                
                $queryEntree->whereBetween('ff.FCF_DATE', [
                    $dateFromFormatted->format('Y-m-d H:i:s'),
                    $dateToFormatted->format('Y-m-d H:i:s')
                ]);
            }
            
            try {
                $quantiteEntree = $queryEntree->sum('ffd.FCF_QTE') ?? 0;
            } catch (\Exception $e) {
                $quantiteEntree = 0;
            }
                
            // Calcul des quantités sorties avec filtrage par dates محسن
            $querySortie = DB::table('FACTURE_VNT_DETAIL as fvd')
                ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                ->where('fvd.ART_REF', $item->art_ref) // Utilisation de l'ART_REF correct
                ->where('fv.FCTV_VALIDE', 1);
            
            if ($dateFrom && $dateTo) {
                // التأكد من تحويل التواريخ للتنسيق الصحيح
                $dateFromFormatted = Carbon::parse($dateFrom)->startOfDay();
                $dateToFormatted = Carbon::parse($dateTo)->endOfDay();
                
                $querySortie->whereBetween('fv.FCTV_DATE', [
                    $dateFromFormatted->format('Y-m-d H:i:s'),
                    $dateToFormatted->format('Y-m-d H:i:s')
                ]);
            }
            
            try {
                $quantiteSortie = $querySortie->sum('fvd.FVD_QTE') ?? 0;
            } catch (\Exception $e) {
                $quantiteSortie = 0;
            }

            $sheet->setCellValue('B' . $row, $quantiteEntree);
            $sheet->setCellValue('C' . $row, $quantiteSortie);
            $sheet->setCellValue('D' . $row, $item->unite ?? 'Pièce');
            $sheet->setCellValue('E' . $row, $item->stock_final ?? 0);
            $sheet->setCellValue('F' . $row, $item->observation ?? '');

            // Formatage des nombres
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            // Ajout de bordures pour les lignes
            $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(25);
    }

    /**
     * Export du fichier Excel
     */
    private function exportExcelFile($spreadsheet, $fileName)
    {
        $writer = new Xlsx($spreadsheet);
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="' . $fileName . '.xlsx"',
            'Cache-Control' => 'max-age=0',
        ];

        return response()->stream(function() use ($writer) {
            $writer->save('php://output');
        }, 200, $headers);
    }

    /**
     * Fonctions de test pour les rapports individuels
     */
    public function testInventaireValeur()
    {
        $spreadsheet = new Spreadsheet();
        $this->createInventaireValeurSheet($spreadsheet);
        return $this->exportExcelFile($spreadsheet, 'Inventaire_Physique_Sites_' . date('Y-m-d'));
    }

    public function testInventaireValeurDetaille()
    {
        $spreadsheet = new Spreadsheet();
        $this->createInventaireValeurDetailleSheet($spreadsheet);
        return $this->exportExcelFile($spreadsheet, 'Inventaire_Valeur_Detaille_' . date('Y-m-d'));
    }

    public function testEtatReception()
    {
        $spreadsheet = new Spreadsheet();
        $this->createEtatReceptionSheet($spreadsheet);
        $spreadsheet->setActiveSheetIndex(0);
        return $this->exportExcelFile($spreadsheet, 'Test_Etat_Reception_' . date('Y-m-d'));
    }

    public function testEtatSortie()
    {
        $spreadsheet = new Spreadsheet();
        $this->createEtatSortieSheet($spreadsheet);
        $spreadsheet->setActiveSheetIndex(0);
        return $this->exportExcelFile($spreadsheet, 'Test_Etat_Sortie_' . date('Y-m-d'));
    }

    public function testInventairePhysique()
    {
        $spreadsheet = new Spreadsheet();
        $this->createInventairePhysiqueSheet($spreadsheet);
        $spreadsheet->setActiveSheetIndex(0);
        return $this->exportExcelFile($spreadsheet, 'Test_Inventaire_Physique_' . date('Y-m-d'));
    }

    /**
     * Affichage de la page de test
     */
    public function showTestPage()
    {
        return view('admin.reports.test-inventaire');
    }

    /**
     * Affichage du formulaire de rapport personnalisé (pour compatibilité avec les anciens routes)
     */
    // public function showCustomReportForm()
    // {
    //     return view('admin.reports.custom-form');
    // }


    /**
     * Calcul de la plage de dates basé sur la période choisie - تحسين منطق التواريخ
     */
    private function calculateDateRange($period, $dateFrom = null, $dateTo = null)
    {
        $now = Carbon::now();
        
        switch ($period) {
            case 'today':
                return [$now->format('Y-m-d'), $now->format('Y-m-d')];
                
            case 'this_week':
                return [
                    $now->copy()->startOfWeek()->format('Y-m-d'), 
                    $now->copy()->endOfWeek()->format('Y-m-d')
                ];
                
            case 'this_month':
                return [
                    $now->copy()->startOfMonth()->format('Y-m-d'), 
                    $now->copy()->endOfMonth()->format('Y-m-d')
                ];
                
            case 'last_month':
                $lastMonth = $now->copy()->subMonth();
                return [
                    $lastMonth->startOfMonth()->format('Y-m-d'), 
                    $lastMonth->endOfMonth()->format('Y-m-d')
                ];
                
            case 'custom':
                // التأكد من وجود التواريخ المخصصة
                if ($dateFrom && $dateTo) {
                    return [
                        Carbon::parse($dateFrom)->format('Y-m-d'),
                        Carbon::parse($dateTo)->format('Y-m-d')
                    ];
                } else {
                    // قيم افتراضية إذا لم يتم تحديد التواريخ
                    return [
                        $now->copy()->startOfMonth()->format('Y-m-d'),
                        $now->format('Y-m-d')
                    ];
                }
                
            default:
                // قيم افتراضية للحالات غير المعرّفة
                return [
                    $now->copy()->startOfMonth()->format('Y-m-d'),
                    $now->format('Y-m-d')
                ];
        }
    }

    public function showCustomReportForm()
    {
        return view('admin.reports.excel-custom');
    }

    /**
     * Génération de rapport personnalisé basé sur les paramètres saisis
     */
    public function generateCustomReport(Request $request)
    {
        try {
            // Configuration du temps et de la mémoire
            set_time_limit(600);
            ini_set('memory_limit', '1024M');
            
            // Validation des données
            $request->validate([
                'report_type' => 'required|string|in:papier_travail,inventory_value,physical_inventory,sales_output,reception_status',
                'period' => 'required|string|in:today,this_week,this_month,last_month,custom',
                'date_from' => 'nullable|date|before_or_equal:date_to',
                'date_to' => 'nullable|date|after_or_equal:date_from',
            ], [
                'report_type.required' => 'Vous devez choisir le type de rapport',
                'report_type.in' => 'Le type de rapport sélectionné n\'est pas valide',
                'period.required' => 'Vous devez choisir la période',
                'date_from.before_or_equal' => 'La date de début doit être antérieure ou égale à la date de fin',
                'date_to.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début',
            ]);
            
            // Calcul de la plage de dates - تحسين منطق التواريخ
            [$dateFrom, $dateTo] = $this->calculateDateRange(
                $request->period,
                $request->date_from,
                $request->date_to
            );
            
            // التأكد من أن التواريخ في الشكل الصحيح
            $dateFromFormatted = Carbon::parse($dateFrom)->format('Y-m-d');
            $dateToFormatted = Carbon::parse($dateTo)->format('Y-m-d');
            
            $spreadsheet = new Spreadsheet();
            $reportType = $request->report_type;
            
            // Création du rapport demandé
            switch ($reportType) {
                case 'papier_travail':
                    // Création du rapport complet des quatre
                    $this->createInventaireValeurSheet($spreadsheet, $dateFromFormatted, $dateToFormatted);
                    $this->createEtatReceptionSheet($spreadsheet, $dateFromFormatted, $dateToFormatted);
                    $this->createEtatSortieSheet($spreadsheet, $dateFromFormatted, $dateToFormatted);
                    $this->createInventairePhysiqueSheet($spreadsheet, $dateFromFormatted, $dateToFormatted);
                    $fileName = 'Papier_de_Travail_' . $dateFromFormatted . '_' . $dateToFormatted;
                    break;
                    
                case 'inventory_value':
                    $this->createInventaireValeurSheet($spreadsheet, $dateFromFormatted, $dateToFormatted);
                    $fileName = 'Inventaire_Valeur_' . $dateFromFormatted . '_' . $dateToFormatted;
                    break;
                    
                case 'physical_inventory':
                    $this->createInventairePhysiqueSheet($spreadsheet, $dateFromFormatted, $dateToFormatted);
                    $fileName = 'Inventaire_Physique_' . $dateFromFormatted . '_' . $dateToFormatted;
                    break;
                    
                case 'sales_output':
                    $this->createEtatSortieSheet($spreadsheet, $dateFromFormatted, $dateToFormatted);
                    $fileName = 'Etat_Sortie_' . $dateFromFormatted . '_' . $dateToFormatted;
                    break;
                    
                case 'reception_status':
                    $this->createEtatReceptionSheet($spreadsheet, $dateFromFormatted, $dateToFormatted);
                    $fileName = 'Etat_Reception_' . $dateFromFormatted . '_' . $dateToFormatted;
                    break;
                    
                default:
                    throw new \Exception('Type de rapport non supporté');
            }
            
            $spreadsheet->setActiveSheetIndex(0);
            
            return $this->exportExcelFile($spreadsheet, $fileName);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur s\'est produite lors de la création du rapport: ' . $e->getMessage())->withInput();
        }
    }
}
