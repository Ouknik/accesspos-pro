<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ArticlesExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        // Build the same query as in the controller
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
                'f.FAM_LIB as famille',
                DB::raw('CASE 
                    WHEN a.ART_VENTE = 1 THEN \'Actif\' 
                    ELSE \'Inactif\' 
                END as statut_display')
            ]);

        // Apply filters
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('a.ART_DESIGNATION', 'LIKE', "%{$search}%")
                  ->orWhere('a.ART_REF', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($this->filters['famille'])) {
            $query->where('f.FAM_REF', $this->filters['famille']);
        }

        if (isset($this->filters['statut']) && $this->filters['statut'] !== '') {
            $query->where('a.ART_VENTE', $this->filters['statut']);
        }

        $articles = $query->orderBy('a.ART_DESIGNATION')->get();

        // Add stock information for each article
        $articlesWithStock = $articles->map(function($article) {
            $stock = DB::table('STOCK')
                ->where('ART_REF', $article->ART_REF)
                ->sum('STK_QTE') ?? 0;

            // Apply stock filter if needed
            if (!empty($this->filters['stock_filter'])) {
                $stockFilter = $this->filters['stock_filter'];
                if ($stockFilter === 'faible' && !($stock <= $article->ART_STOCK_MIN && $stock > 0)) {
                    return null;
                }
                if ($stockFilter === 'rupture' && !($stock <= 0)) {
                    return null;
                }
            }

            return [
                'reference' => $article->ART_REF,
                'nom' => $article->ART_DESIGNATION,
                'famille' => $article->famille ?? 'Non définie',
                'prix_vente' => number_format($article->ART_PRIX_VENTE ?? 0, 2),
                'prix_achat' => number_format($article->ART_PRIX_ACHAT ?? 0, 2),
                'stock_min' => $article->ART_STOCK_MIN ?? 0,
                'stock_max' => $article->ART_STOCK_MAX ?? 0,
                'stock_actuel' => $stock,
                'statut' => $article->statut_display,
                'date_creation' => now()->format('d/m/Y H:i:s')
            ];
        })->filter(); // Remove null values from stock filtering

        return collect($articlesWithStock);
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Nom du Produit',
            'Famille/Catégorie',
            'Prix de Vente (DH)',
            'Prix d\'Achat (DH)',
            'Stock Min',
            'Stock Max',
            'Stock Actuel',
            'Statut',
            'Date Export'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '366092'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Référence
            'B' => 35, // Nom du Produit
            'C' => 20, // Famille
            'D' => 15, // Prix Vente
            'E' => 15, // Prix Achat
            'F' => 12, // Stock Min
            'G' => 12, // Stock Max
            'H' => 12, // Stock Actuel
            'I' => 12, // Statut
            'J' => 20, // Date Export
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Apply borders to all data
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                // Center align numeric columns
                $sheet->getStyle('D2:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I2:I' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Add alternating row colors
                for ($row = 2; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'F8F9FA'],
                            ],
                        ]);
                    }
                }

                // Auto-fit all columns
                foreach (range('A', $highestColumn) as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(false);
                }

                // Add title and info at the top
                $sheet->insertNewRowBefore(1, 3);
                
                // Main title
                $sheet->setCellValue('A1', 'LISTE DES ARTICLES - ACCESSPOS PRO');
                $sheet->mergeCells('A1:J1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['argb' => '366092'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Export info
                $sheet->setCellValue('A2', 'Date d\'export: ' . now()->format('d/m/Y H:i:s'));
                $sheet->setCellValue('F2', 'Nombre d\'articles: ' . ($highestRow - 4));
                $sheet->getStyle('A2:J2')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 10,
                        'color' => ['argb' => '666666'],
                    ],
                ]);

                // Empty row
                $sheet->setCellValue('A3', '');

                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(25); // Header row
            },
        ];
    }
}
