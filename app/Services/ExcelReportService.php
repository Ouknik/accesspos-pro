<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExcelReportService
{
    /**
     * إنشاء رأس احترافي للتقرير
     */
    public function createProfessionalHeader($sheet, $title, $company = 'DIAFAT AL JAOUDA')
    {
        // دمج الخلايا للرأس
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');
        $sheet->mergeCells('A4:J4');
        
        // إضافة النصوص
        $sheet->setCellValue('A1', $company);
        $sheet->setCellValue('A2', 'شركة الضيافة الجودة');
        $sheet->setCellValue('A3', $title);
        $sheet->setCellValue('A4', 'Date de création: ' . date('d/m/Y H:i'));
        
        // تنسيق الخطوط والألوان
        $sheet->getStyle('A1:A4')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(18);
        $sheet->getStyle('A2')->getFont()->setSize(14);
        $sheet->getStyle('A3')->getFont()->setSize(16);
        $sheet->getStyle('A4')->getFont()->setSize(10);
        
        // محاذاة وسط
        $sheet->getStyle('A1:A4')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        
        // لون الخلفية للرأس
        $sheet->getStyle('A1:J4')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color('E3F2FD'));
        
        return $sheet;
    }
    
    /**
     * تطبيق تنسيق الجدول
     */
    public function applyTableStyling($sheet, $startRow, $endRow, $startCol, $endCol)
    {
        $range = $startCol . $startRow . ':' . $endCol . $endRow;
        
        // حدود الجدول
        $sheet->getStyle($range)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
        
        // تنسيق الرأس
        $headerRange = $startCol . $startRow . ':' . $endCol . $startRow;
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color('2196F3'));
        
        $sheet->getStyle($headerRange)->getFont()
            ->setBold(true)
            ->setColor(new Color('FFFFFF'));
        
        // محاذاة النص
        $sheet->getStyle($range)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        
        // تغيير لون الصفوف المتناوبة
        for ($row = $startRow + 1; $row <= $endRow; $row += 2) {
            $rowRange = $startCol . $row . ':' . $endCol . $row;
            $sheet->getStyle($rowRange)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor(new Color('F5F5F5'));
        }
        
        // ضبط عرض الأعمدة تلقائياً
        for ($col = ord($startCol); $col <= ord($endCol); $col++) {
            $sheet->getColumnDimension(chr($col))->setAutoSize(true);
        }
        
        return $sheet;
    }
    
    /**
     * إضافة مجموع للعمود
     */
    public function addColumnTotal($sheet, $row, $col, $formula, $label = 'الإجمالي:')
    {
        $prevCol = chr(ord($col) - 1);
        $sheet->setCellValue($prevCol . $row, $label);
        $sheet->setCellValue($col . $row, $formula);
        
        // تنسيق خاص للمجموع
        $sheet->getStyle($prevCol . $row . ':' . $col . $row)->getFont()->setBold(true);
        $sheet->getStyle($prevCol . $row . ':' . $col . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color('FFC107'));
        
        return $sheet;
    }
    
    /**
     * إضافة خلية ملونة حسب الحالة
     */
    public function addConditionalColor($sheet, $cell, $value, $condition = 0)
    {
        $sheet->setCellValue($cell, $value);
        
        if ($value < $condition) {
            // أحمر للقيم السالبة
            $sheet->getStyle($cell)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor(new Color('FFCCCC'));
        } elseif ($value > $condition) {
            // أخضر للقيم الموجبة
            $sheet->getStyle($cell)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor(new Color('CCFFCC'));
        }
        
        return $sheet;
    }
}
