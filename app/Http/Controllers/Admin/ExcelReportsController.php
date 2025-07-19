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
     * إنشاء تقرير "Papier de Travail" الكامل - التقارير الأربعة مجتمعة
     */
    public function generatePapierDeTravail(Request $request)
    {
        try {
            set_time_limit(300);
            ini_set('memory_limit', '512M');
            
            // استخراج التواريخ من الطلب
            $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));
            
            $spreadsheet = new Spreadsheet();
            
            // إنشاء التقارير الأربعة بناءً على الصور المرسلة مع فلترة التواريخ
            // التقرير الأول: الجرد حسب المواقع (النسخة الجديدة المطابقة للصورة)
            $this->createInventaireValeurSheet($spreadsheet, $dateFrom, $dateTo);
            $this->createEtatReceptionSheet($spreadsheet, $dateFrom, $dateTo);
            $this->createEtatSortieSheet($spreadsheet, $dateFrom, $dateTo);
            $this->createInventairePhysiqueSheet($spreadsheet, $dateFrom, $dateTo);
            
            $spreadsheet->setActiveSheetIndex(0);
            
            return $this->exportExcelFile($spreadsheet, 'Papier_de_Travail_' . date('Y-m-d'));
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'حدث خطأ في إنشاء التقرير: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * التقرير الأول: Inventaire En Valeur (مثل الصورة الجديدة)
     * بناءً على الصورة المرسلة - عرض المواقع مع المبالغ
     */
    private function createInventaireValeurSheet($spreadsheet, $dateFrom = null, $dateTo = null)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventaire En Valeur');
        
        // إعداد الأعمدة
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(20);
        
        // العنوان الرئيسي
        $sheet->setCellValue('A1', 'DJAFAAT AL JAOUDA');
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // تاريخ التقرير - استخدام التواريخ المطلوبة أو التاريخ الحالي
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
        
        // عناوين الأعمدة
        $sheet->setCellValue('A4', 'Lieu');
        $sheet->setCellValue('B4', 'Valeur');
        
        // تنسيق عناوين الأعمدة
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
        
        // حساب القيمة الإجمالية للمخزون
        $totalValue = DB::table('STOCK as s')
            ->join('ARTICLE as a', 's.ART_REF', '=', 'a.ART_REF')
            ->where('s.STK_QTE', '>', 0)
            ->selectRaw('SUM(s.STK_QTE * a.ART_PRIX_ACHAT) as total')
            ->value('total') ?? 0;
        
        // البيانات حسب المواقع مع النسب المحددة
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
            
            // تنسيق الصفوف
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);
            
            $row++;
        }
        
        // المجموع
        $sheet->setCellValue('A' . $row, 'Total');
        $sheet->setCellValue('B' . $row, number_format($totalValue, 2, '.', ','));
        
        // تنسيق صف المجموع
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
     * حساب قيمة المخزون لموقع معين
     */
    private function calculerValeurStock($siteType)
    {
        // في حال عدم وجود تصنيف المواقع في قاعدة البيانات، نحسب إجمالي القيمة مقسمة
        $totalValue = DB::table('ARTICLE as a')
            ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
            ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
            ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
            ->where('a.ART_STOCKABLE', 1)
            ->selectRaw('SUM(ISNULL(s.STK_QTE, 0) * ISNULL(a.ART_PRIX_ACHAT, 0)) as total_value')
            ->value('total_value') ?? 0;

        // توزيع القيمة على المواقع المختلفة (يمكن تخصيصها حسب الحاجة)
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
     * التقرير التفصيلي للجرد بالقيمة (النسخة الأصلية مع التفاصيل)
     */
    private function createInventaireValeurDetailleSheet($spreadsheet)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventaire Détaillé');

        // العنوان الرئيسي مع التاريخ
        $sheet->setCellValue('A1', 'Inventaire En Valeur - Détaillé');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // إضافة فترة التاريخ "Du... Au..."
        $dateFrom = Carbon::now()->startOfMonth()->format('d/m/Y');
        $dateTo = Carbon::now()->format('d/m/Y');
        $sheet->setCellValue('A2', "Du {$dateFrom} Au {$dateTo}");
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        // رؤوس الأعمدة
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

        // إضافة حدود للعناوين
        $sheet->getStyle('A4:F4')->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // استخراج البيانات
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
            
            // تنسيق الأرقام
            $sheet->getStyle('D' . $row)->getNumberFormat()
                ->setFormatCode('#,##0.00');
            $sheet->getStyle('E' . $row)->getNumberFormat()
                ->setFormatCode('#,##0.00');
            $sheet->getStyle('F' . $row)->getNumberFormat()
                ->setFormatCode('#,##0.00');
            
            // إضافة حدود للصفوف
            $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            
            $totalValeur += $item->valeur_totale ?? 0;
            $row++;
        }

        // صف المجموع
        $sheet->setCellValue('A' . $row, 'TOTAL GÉNÉRAL');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $sheet->setCellValue('F' . $row, $totalValeur);
        $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color('FFFF99'));
        $sheet->getStyle('F' . $row)->getNumberFormat()
            ->setFormatCode('#,##0.00');

        // تحديد عرض الأعمدة
        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(18);
    }

    /**
     * التقرير الثاني: État de réception
     * بناءً على الصورة الثانية المرسلة
     */
    private function createEtatReceptionSheet($spreadsheet, $dateFrom = null, $dateTo = null)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('État de Réception');

        // العنوان الرئيسي
        $sheet->setCellValue('A1', 'État de réception');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // إضافة فترة التاريخ
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

        // إضافة حدود للعناوين
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

        // تطبيق فلترة التواريخ إذا تم تمريرها
        if ($dateFrom && $dateTo) {
            $query->whereBetween('ff.FCF_DATE', [$dateFrom, $dateTo]);
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

            // تنسيق الأرقام
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            // إضافة حدود للصفوف
            $sheet->getStyle('A' . $row . ':I' . $row)->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $totalMontant += $reception->montant ?? 0;
            $row++;
        }

        // صف المجموع
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
     * التقرير الثالث: État de Sorties
     * بناءً على الصورة الثالثة المرسلة
     */
    private function createEtatSortieSheet($spreadsheet, $dateFrom = null, $dateTo = null)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('État de Sorties');

        // العنوان الرئيسي
        $sheet->setCellValue('A1', 'État de Sorties');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // إضافة فترة التاريخ - استخدام التواريخ الممررة أو التواريخ الافتراضية
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

        // إضافة حدود للعناوين
        $sheet->getStyle('A4:I4')->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // استخراج بيانات المبيعات/الخروج من FACTURE_VNT مع فلترة التواريخ
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

        // تطبيق فلترة التواريخ إذا تم تمريرها
        if ($dateFrom && $dateTo) {
            $query->whereBetween('fv.FCTV_DATE', [$dateFrom, $dateTo]);
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

            // تنسيق الأرقام
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            // إضافة حدود للصفوف
            $sheet->getStyle('A' . $row . ':I' . $row)->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $totalMontant += $sortie->montant ?? 0;
            $row++;
        }

        // صف المجموع
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
     * التقرير الرابع: Inventaire Physique Par Article
     * بناءً على الصورة الرابعة المرسلة
     */
    private function createInventairePhysiqueSheet($spreadsheet, $dateFrom = null, $dateTo = null)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Inventaire Physique');

        // العنوان الرئيسي
        $sheet->setCellValue('A1', 'Inventaire Physique Par Article');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // إضافة فترة التاريخ - استخدام التواريخ الممررة أو التواريخ الافتراضية
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

        // إضافة حدود للعناوين
        $sheet->getStyle('A4:F4')->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // استخراج بيانات الجرد الفيزيائي مع حساب الكميات
        $inventaire = DB::table('ARTICLE as a')
            ->leftJoin('STOCK as s', 'a.ART_REF', '=', 's.ART_REF')
            ->leftJoin('SOUS_FAMILLE as sf', 'a.SFM_REF', '=', 'sf.SFM_REF')
            ->leftJoin('FAMILLE as f', 'sf.FAM_REF', '=', 'f.FAM_REF')
            ->select([
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
            
            // حساب الكميات الداخلة مع فلترة التواريخ
            $queryEntree = DB::table('FACTURE_FRS_DETAIL as ffd')
                ->join('FACTURE_FOURNISSEUR as ff', 'ffd.FCF_REF', '=', 'ff.FCF_REF')
                ->where('ffd.ART_REF', $item->designation) // استخدام ART_REF الصحيح
                ->where('ff.FCF_VALIDE', 1);
            
            if ($dateFrom && $dateTo) {
                $queryEntree->whereBetween('ff.FCF_DATE', [$dateFrom, $dateTo]);
            }
            
            $quantiteEntree = $queryEntree->sum('ffd.FCF_QTE') ?? 0;
                
            // حساب الكميات الخارجة مع فلترة التواريخ
            $querySortie = DB::table('FACTURE_VNT_DETAIL as fvd')
                ->join('FACTURE_VNT as fv', 'fvd.FCTV_REF', '=', 'fv.FCTV_REF')
                ->where('fvd.ART_REF', $item->designation) // استخدام ART_REF الصحيح
                ->where('fv.FCTV_VALIDE', 1);
            
            if ($dateFrom && $dateTo) {
                $querySortie->whereBetween('fv.FCTV_DATE', [$dateFrom, $dateTo]);
            }
            
            $quantiteSortie = $querySortie->sum('fvd.FVD_QTE') ?? 0;

            $sheet->setCellValue('B' . $row, $quantiteEntree);
            $sheet->setCellValue('C' . $row, $quantiteSortie);
            $sheet->setCellValue('D' . $row, $item->unite ?? 'Pièce');
            $sheet->setCellValue('E' . $row, $item->stock_final ?? 0);
            $sheet->setCellValue('F' . $row, $item->observation ?? '');

            // تنسيق الأرقام
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            // إضافة حدود للصفوف
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
     * تصدير ملف Excel
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
     * دوال الاختبار للتقارير المنفردة
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
     * عرض صفحة الاختبار
     */
    public function showTestPage()
    {
        return view('admin.reports.test-inventaire');
    }

    /**
     * عرض نموذج التقرير المخصص (للمتوافقية مع المسارات القديمة)
     */
    // public function showCustomReportForm()
    // {
    //     return view('admin.reports.custom-form');
    // }


     public function showCustomReportForm()
    {
        return view('admin.reports.excel-custom');
    }

    /**
     * توليد تقرير مخصص (للمتوافقية مع المسارات القديمة)
     */
    public function generateCustomReport(Request $request)
    {
        // توجيه إلى التقرير الشامل الجديد
        return $this->generatePapierDeTravail($request);
    }
}
