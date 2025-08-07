<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FacturesExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting, ShouldAutoSize
{
    protected $factures;

    public function __construct($factures)
    {
        $this->factures = $factures;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->factures->map(function ($facture) {
            return [
                'reference' => $facture->FCTV_REF,
                'numero' => $facture->FCTV_NUM,
                'date' => $facture->FCTV_DATE ? date('d/m/Y', strtotime($facture->FCTV_DATE)) : '',
                'client' => $facture->CLT_CLIENT ?? 'Client anonyme',
                'montant_ht' => number_format($facture->FCTV_MNT_HT ?? 0, 2, '.', ''),
                'montant_tva' => number_format($facture->FCTV_MNT_TVA ?? 0, 2, '.', ''),
                'montant_ttc' => number_format($facture->FCTV_MNT_TTC ?? 0, 2, '.', ''),
                'remise' => number_format($facture->FCTV_REMISE ?? 0, 2, '.', ''),
                'statut' => $facture->statut ?? 'N/A',
                'etat' => $facture->etat ?? 'N/A'
            ];
        });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Référence',
            'Numéro',
            'Date',
            'Client',
            'Montant HT',
            'Montant TVA',
            'Montant TTC',
            'Remise',
            'Statut',
            'État'
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style pour l'en-tête
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ],
            // Style pour toutes les cellules
            'A:J' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_00, // Montant HT
            'F' => NumberFormat::FORMAT_NUMBER_00, // Montant TVA
            'G' => NumberFormat::FORMAT_NUMBER_00, // Montant TTC
            'H' => NumberFormat::FORMAT_NUMBER_00, // Remise
        ];
    }
}
