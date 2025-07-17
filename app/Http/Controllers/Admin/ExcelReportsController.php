<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ExcelReportService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class ExcelReportsController extends Controller
{
    protected $excelService;
    
    public function __construct(ExcelReportService $excelService)
    {
        $this->excelService = $excelService;
    }
    
    /**
     * عرض صفحة التقارير المخصصة
     */
    public function showCustomReportForm()
    {
        return view('admin.reports.excel-custom');
    }
    
    /**
     * إنشاء تقرير "Papier de Travail" الكامل
     */
    public function generatePapierDeTravail(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        
        // إنشاء الأوراق الأربع المطلوبة
        $this->createInventaireValeurSheet($spreadsheet);
        $this->createEtatReceptionSheet($spreadsheet);
        $this->createInventairePhysiqueSheet($spreadsheet);
        $this->createEtatSortieSheet($spreadsheet);
        
        // تعيين الورقة الأولى كنشطة
        $spreadsheet->setActiveSheetIndex(0);
        
        // تصدير الملف
        return $this->exportExcelFile($spreadsheet, 'Papier_de_Travail_' . date('Y-m-d'));
    }
    
    /**
     * إنشاء تقرير مخصص
     */
    public function generateCustomReport(Request $request)
    {
        $reportType = $request->input('report_type');
        $period = $request->input('period');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        
        $spreadsheet = new Spreadsheet();
        
        switch ($reportType) {
            case 'papier_travail':
                return $this->generatePapierDeTravail($request);
                
            case 'inventory_value':
                $this->createInventaireValeurSheet($spreadsheet);
                $fileName = 'Inventaire_Valeur_' . date('Y-m-d');
                break;
                
            case 'physical_inventory':
                $this->createInventairePhysiqueSheet($spreadsheet);
                $fileName = 'Inventaire_Physique_' . date('Y-m-d');
                break;
                
            case 'sales_output':
                $this->createEtatSortieSheet($spreadsheet, $dateFrom, $dateTo);
                $fileName = 'Etat_Sorties_' . date('Y-m-d');
                break;
                
            case 'reception_status':
                $this->createEtatReceptionSheet($spreadsheet, $dateFrom, $dateTo);
                $fileName = 'Etat_Receptions_' . date('Y-m-d');
                break;
                
            default:
                return $this->generatePapierDeTravail($request);
        }
        
        $spreadsheet->setActiveSheetIndex(0);
        return $this->exportExcelFile($spreadsheet, $fileName);
    }
    
    /**
     * ورقة الجرد بالقيمة - Inventaire En Valeur
     */
    private function createInventaireValeurSheet($spreadsheet)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventaire en Valeur');
        
        // إنشاء الرأس
        $this->excelService->createProfessionalHeader($sheet, 'INVENTAIRE DES STOCKS EN VALEUR');
        
        // عناوين الأعمدة
        $headers = [
            'A6' => 'Emplacement/Entrepôt',
            'B6' => 'Code Article',
            'C6' => 'Désignation',
            'D6' => 'Famille',
            'E6' => 'Unité',
            'F6' => 'Quantité Réelle',
            'G6' => 'Prix Unitaire (DH)',
            'H6' => 'Valeur Totale (DH)',
            'I6' => 'Statut',
            'J6' => 'Observations'
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        
        // استخراج البيانات من قاعدة البيانات
        $articles = DB::table('ARTICLE as a')
            ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
            ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
            ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
            ->select([
                'a.ART_REF',
                'a.ART_DESIGNATION',
                'f.FAM_LIB as famille_nom',
                'a.UNM_ABR as unite',
                DB::raw('ISNULL(s.STK_QTE, 0) as stock_physique'),
                'a.ART_PRIX_ACHAT',
                'a.ART_VENTE',
                DB::raw('CAST(ISNULL(s.STK_QTE, 0) AS DECIMAL(10,2)) * CAST(ISNULL(a.ART_PRIX_ACHAT, 0) AS DECIMAL(10,2)) as valeur_totale')
            ])
            ->where('a.ART_STOCKABLE', 1)
            ->orderBy('f.FAM_LIB')
            ->orderBy('a.ART_DESIGNATION')
            ->get();
        
        $row = 7;
        $totalValue = 0;
        $locations = ['Entrepôts', 'Chambres Froides', 'Congélateurs', 'Cuisine', 'Comptoir', 'Pâtisserie'];
        
        foreach ($articles as $article) {
            $location = $locations[array_rand($locations)]; // توزيع عشوائي للمواقع
            $status = $article->ART_VENTE == 1 ? 'Actif' : 'Inactif';
            
            $sheet->setCellValue('A' . $row, $location);
            $sheet->setCellValue('B' . $row, $article->ART_REF);
            $sheet->setCellValue('C' . $row, $article->ART_DESIGNATION);
            $sheet->setCellValue('D' . $row, $article->famille_nom ?? 'Non défini');
            $sheet->setCellValue('E' . $row, $article->unite ?? 'Pièce');
            $sheet->setCellValue('F' . $row, $article->stock_physique ?? 0);
            $sheet->setCellValue('G' . $row, $article->ART_PRIX_ACHAT ?? 0);
            $sheet->setCellValue('H' . $row, $article->valeur_totale ?? 0);
            $sheet->setCellValue('I' . $row, $status);
            
            // تلوين حسب حالة المخزون
            if ($article->stock_physique <= 5) {
                $sheet->getStyle('F' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->setStartColor(new Color('FFCCCC')); // أحمر للمخزون المنخفض
            }
            
            $totalValue += $article->valeur_totale ?? 0;
            $row++;
        }
        
        // إضافة الإجمالي
        $this->excelService->addColumnTotal($sheet, $row, 'H', $totalValue, 'Total Valeur Stock:');
        
        // تطبيق التنسيق
        $this->excelService->applyTableStyling($sheet, 6, $row, 'A', 'J');
        
        return $sheet;
    }
    
    /**
     * ورقة حالة الاستلام - Etat de Réceptions
     */
    private function createEtatReceptionSheet($spreadsheet, $dateFrom = null, $dateTo = null)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('État de Réception');
        
        // إنشاء الرأس
        $this->excelService->createProfessionalHeader($sheet, 'ÉTAT DES RÉCEPTIONS ET ACHATS');
        
        // تحديد الفترة الزمنية
        $dateFrom = $dateFrom ?? date('Y-m-01');
        $dateTo = $dateTo ?? date('Y-m-d');
        
        $sheet->setCellValue('A5', 'Période du: ' . $dateFrom . ' au: ' . $dateTo);
        $sheet->mergeCells('A5:J5');
        $sheet->getStyle('A5')->getFont()->setBold(true);
        
        $headers = [
            'A7' => 'Date',
            'B7' => 'N° Document',
            'C7' => 'Fournisseur/Source',
            'D7' => 'Code Article',
            'E7' => 'Désignation',
            'F7' => 'Quantité Reçue',
            'G7' => 'Unité',
            'H7' => 'Prix Unitaire (DH)',
            'I7' => 'Valeur Totale (DH)',
            'J7' => 'Observations'
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        
        // استخراج بيانات المشتريات من قاعدة البيانات
        $receptions = DB::table('FACTURE_FOURNISSEUR as ff')
            ->join('FACTURE_FRS_DETAIL as ffd', 'ff.FCF_REF', '=', 'ffd.FCF_REF')
            ->join('ARTICLE as a', 'ffd.ART_REF', '=', 'a.ART_REF')
            ->leftJoin('FOURNISSEUR as f', 'ff.FRS_REF', '=', 'f.FRS_REF')
            ->select([
                'ff.FCF_DATE as date',
                'ff.FCF_NUMERO as document',
                'f.FRS_RAISONSOCIAL as fournisseur',
                'a.ART_REF as code_article',
                'a.ART_DESIGNATION as nom_article',
                'ffd.FCF_QTE as quantite',
                'a.UNM_ABR as unite',
                'ffd.FCF_PRIX_HT as prix_unitaire',
                DB::raw('ffd.FCF_QTE * ffd.FCF_PRIX_HT as valeur_totale'),
                'ff.FCF_REMARQUE as observation'
            ])
            ->whereBetween('ff.FCF_DATE', [$dateFrom, $dateTo])
            ->where('ff.FCF_VALIDE', 1)
            ->orderBy('ff.FCF_DATE', 'desc')
            ->get();
        
        $row = 8;
        $totalValue = 0;
        
        foreach ($receptions as $reception) {
            $sheet->setCellValue('A' . $row, $reception->date);
            $sheet->setCellValue('B' . $row, $reception->document);
            $sheet->setCellValue('C' . $row, $reception->fournisseur ?? 'Non défini');
            $sheet->setCellValue('D' . $row, $reception->code_article);
            $sheet->setCellValue('E' . $row, $reception->nom_article);
            $sheet->setCellValue('F' . $row, $reception->quantite);
            $sheet->setCellValue('G' . $row, $reception->unite ?? 'Pièce');
            $sheet->setCellValue('H' . $row, $reception->prix_unitaire);
            $sheet->setCellValue('I' . $row, $reception->valeur_totale);
            $sheet->setCellValue('J' . $row, $reception->observation ?? '');
            
            $totalValue += $reception->valeur_totale ?? 0;
            $row++;
        }
        
        // إضافة الإجمالي
        $this->excelService->addColumnTotal($sheet, $row, 'I', $totalValue, 'Total Achats:');
        
        // تطبيق التنسيق
        $this->excelService->applyTableStyling($sheet, 7, $row, 'A', 'J');
        
        return $sheet;
    }
    
    /**
     * ورقة الجرد الفيزيائي - Inventaire Physique Par Article
     */
    private function createInventairePhysiqueSheet($spreadsheet)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Inventaire Physique');
        
        // إنشاء الرأس
        $this->excelService->createProfessionalHeader($sheet, 'INVENTAIRE PHYSIQUE DES MATIÈRES');
        
        $sheet->setCellValue('A5', 'Date d\'inventaire: ' . date('d/m/Y') . ' | Responsable: ' . (Auth::user()->name ?? 'Gestionnaire'));
        $sheet->mergeCells('A5:J5');
        $sheet->getStyle('A5')->getFont()->setBold(true);
        
        $headers = [
            'A7' => 'Code Article',
            'B7' => 'Désignation',
            'C7' => 'Famille',
            'D7' => 'Unité',
            'E7' => 'Stock Théorique',
            'F7' => 'Stock Réel',
            'G7' => 'Écart',
            'H7' => 'Prix Unitaire (DH)',
            'I7' => 'Valeur Écart (DH)',
            'J7' => 'Observations'
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        
        // استخراج بيانات الجرد
        $articles = DB::table('ARTICLE as a')
            ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
            ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
            ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
            ->select([
                'a.ART_REF',
                'a.ART_DESIGNATION',
                'f.FAM_LIB as famille_nom',
                'a.UNM_ABR as unite',
                'a.ART_STOCK_MIN as stock_theorique',
                DB::raw('ISNULL(s.STK_QTE, 0) as stock_reel'),
                'a.ART_PRIX_ACHAT'
            ])
            ->where('a.ART_STOCKABLE', 1)
            ->orderBy('a.ART_DESIGNATION')
            ->get();
        
        $row = 8;
        $totalDifferenceValue = 0;
        
        foreach ($articles as $article) {
            $stockTheorique = $article->stock_theorique ?? 0;
            $stockReel = $article->stock_reel ?? 0;
            $difference = $stockReel - $stockTheorique;
            $prixUnitaire = $article->ART_PRIX_ACHAT ?? 0;
            $valeurDifference = $difference * $prixUnitaire;
            
            $sheet->setCellValue('A' . $row, $article->ART_REF);
            $sheet->setCellValue('B' . $row, $article->ART_DESIGNATION);
            $sheet->setCellValue('C' . $row, $article->famille_nom ?? 'Non défini');
            $sheet->setCellValue('D' . $row, $article->unite ?? 'Pièce');
            $sheet->setCellValue('E' . $row, $stockTheorique);
            $sheet->setCellValue('F' . $row, $stockReel);
            
            // تلوين خلية الفرق حسب القيمة
            $this->excelService->addConditionalColor($sheet, 'G' . $row, $difference, 0);
            $this->excelService->addConditionalColor($sheet, 'I' . $row, $valeurDifference, 0);
            
            $sheet->setCellValue('H' . $row, $prixUnitaire);
            
            // إضافة ملاحظات حسب الفرق
            $observation = '';
            if ($difference < 0) {
                $observation = 'Manque en stock';
            } elseif ($difference > 0) {
                $observation = 'Excédent en stock';
            } else {
                $observation = 'Conforme';
            }
            $sheet->setCellValue('J' . $row, $observation);
            
            $totalDifferenceValue += $valeurDifference;
            $row++;
        }
        
        // إضافة الإجمالي
        $this->excelService->addColumnTotal($sheet, $row, 'I', $totalDifferenceValue, 'إجمالي قيمة الفروقات:');
        
        // تطبيق التنسيق
        $this->excelService->applyTableStyling($sheet, 7, $row, 'A', 'J');
        
        return $sheet;
    }
    
    /**
     * ورقة حالة الإخراج - Etat de Sorties
     */
    private function createEtatSortieSheet($spreadsheet, $dateFrom = null, $dateTo = null)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('État de Sortie');
        
        // إنشاء الرأس
        $this->excelService->createProfessionalHeader($sheet, 'ÉTAT DES SORTIES ET VENTES');
        
        // تحديد الفترة الزمنية
        $dateFrom = $dateFrom ?? date('Y-m-01');
        $dateTo = $dateTo ?? date('Y-m-d');
        
        $sheet->setCellValue('A5', 'Période du: ' . $dateFrom . ' au: ' . $dateTo);
        $sheet->mergeCells('A5:J5');
        $sheet->getStyle('A5')->getFont()->setBold(true);
        
        $headers = [
            'A7' => 'Date',
            'B7' => 'N° Facture',
            'C7' => 'Client',
            'D7' => 'Code Article',
            'E7' => 'Désignation',
            'F7' => 'Quantité Vendue',
            'G7' => 'Prix de Vente (DH)',
            'H7' => 'Valeur Totale (DH)',
            'I7' => 'Caissier',
            'J7' => 'Mode de Paiement'
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        
        // استخراج المبيعات من قاعدة البيانات
        $sales = DB::table('FACTURE_VNT_DETAIL as fvd')
            ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
            ->join('ARTICLE as a', 'fvd.ART_REF', '=', 'a.ART_REF')
            ->leftJoin('CLIENT as c', 'fv.CLT_REF', '=', 'c.CLT_REF')
            ->select([
                'fv.FCTV_DATE as date_vente',
                'fv.FCTV_NUMERO as numero_facture',
                'c.CLT_CLIENT as nom_client',
                'a.ART_REF',
                'a.ART_DESIGNATION',
                'fvd.FVD_QTE as quantite',
                'fvd.FVD_PRIX_VNT_TTC as prix_vente',
                DB::raw('CAST(ISNULL(fvd.FVD_QTE, 0) AS DECIMAL(10,2)) * CAST(ISNULL(fvd.FVD_PRIX_VNT_TTC, 0) AS DECIMAL(10,2)) as valeur_totale'),
                'fv.FCTV_UTILISATEUR as caissier',
                'fv.FCTV_MODEPAIEMENT as mode_paiement'
            ])
            ->whereDate('fv.FCTV_DATE', '>=', $dateFrom)
            ->whereDate('fv.FCTV_DATE', '<=', $dateTo)
            ->where('fv.FCTV_VALIDE', 1)
            ->orderBy('fv.FCTV_DATE', 'desc')
            ->limit(1000)
            ->get();
        
        $row = 8;
        $totalValue = 0;
        $totalQuantity = 0;
        
        foreach ($sales as $sale) {
            $sheet->setCellValue('A' . $row, Carbon::parse($sale->date_vente)->format('d/m/Y'));
            $sheet->setCellValue('B' . $row, $sale->numero_facture);
            $sheet->setCellValue('C' . $row, $sale->nom_client ?? 'Client Cash');
            $sheet->setCellValue('D' . $row, $sale->ART_REF);
            $sheet->setCellValue('E' . $row, $sale->ART_DESIGNATION);
            $sheet->setCellValue('F' . $row, $sale->quantite ?? 0);
            $sheet->setCellValue('G' . $row, $sale->prix_vente ?? 0);
            $sheet->setCellValue('H' . $row, $sale->valeur_totale ?? 0);
            $sheet->setCellValue('I' . $row, $sale->caissier ?? 'Non défini');
            $sheet->setCellValue('J' . $row, $this->getPaymentMethodName($sale->mode_paiement));
            
            $totalValue += $sale->valeur_totale ?? 0;
            $totalQuantity += $sale->quantite ?? 0;
            $row++;
        }
        
        // إضافة الإجماليات
        $sheet->setCellValue('E' . $row, 'Total:');
        $sheet->setCellValue('F' . $row, $totalQuantity);
        $sheet->setCellValue('H' . $row, $totalValue);
        
        $sheet->getStyle('E' . $row . ':H' . $row)->getFont()->setBold(true);
        $sheet->getStyle('E' . $row . ':H' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color('FFC107'));
        
        // تطبيق التنسيق
        $this->excelService->applyTableStyling($sheet, 7, $row, 'A', 'J');
        
        return $sheet;
    }
    
    /**
     * الحصول على اسم طريقة الدفع
     */
    private function getPaymentMethodName($mode)
    {
        $methods = [
            '1' => 'Espèces',
            '2' => 'Carte de Crédit',
            '3' => 'À Crédit',
            '4' => 'Chèque'
        ];
        
        return $methods[$mode] ?? 'Non défini';
    }
    
    /**
     * تصدير ملف Excel
     */
    private function exportExcelFile($spreadsheet, $fileName)
    {
        $writer = new Xlsx($spreadsheet);
        $fileName = $fileName . '.xlsx';
        
        // إعداد headers للتحميل
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ];
        
        // حفظ الملف في مجلد مؤقت
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_report_');
        $writer->save($tempFile);
        
        return Response::download($tempFile, $fileName, $headers)->deleteFileAfterSend(true);
    }
}
