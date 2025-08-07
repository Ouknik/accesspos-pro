<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class ChiffreAffairesExport implements 
    FromCollection, 
    WithHeadings, 
    WithStyles, 
    WithTitle, 
    WithColumnFormatting,
    WithColumnWidths,
    WithProperties,
    WithEvents
{
    private $data;
    private $type;
    private $dateDebut;
    private $dateFin;

    public function __construct($data, $type, $dateDebut = null, $dateFin = null)
    {
        // التأكد من أن البيانات هي collection وتحويل stdClass إلى array إذا لزم الأمر
        if (is_array($data)) {
            $this->data = collect($data);
        } elseif ($data instanceof \Illuminate\Support\Collection) {
            $this->data = $data;
        } else {
            $this->data = collect([$data]);
        }
        
        $this->type = $type;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->data->isEmpty()) {
            return collect([]);
        }
        
        return $this->data->map(function ($item, $index) {
            try {
                // تحويل stdClass إلى array بشكل آمن
                $row = [];
                
                if (is_object($item)) {
                    foreach ($item as $key => $value) {
                        $row[$key] = $value;
                    }
                } elseif (is_array($item)) {
                    $row = $item;
                } else {
                    // في حالة كان المتغير من نوع آخر
                    $row = ['data' => $item];
                }
                
                // إضافة رقم السطر
                $numberedRow = ['N°' => $index + 1];
                
                // دمج البيانات مع رقم السطر
                foreach ($row as $key => $value) {
                    $numberedRow[$key] = $value ?? '';
                }
                
                // تنسيق البيانات حسب نوع التقرير
                return $this->formatRowData($numberedRow);
                
            } catch (\Exception $e) {
                // في حالة حدوث خطأ، إرجاع صف فارغ مع رقم السطر
                return ['N°' => $index + 1, 'error' => 'Erreur de traitement: ' . $e->getMessage()];
            }
        });
    }

    /**
     * تنسيق بيانات الصف حسب نوع التقرير
     */
    private function formatRowData($row)
    {
        switch ($this->type) {
            case 'serveur':
                return $this->formatServeurData($row);
                
            case 'famille':
                return $this->formatFamilleData($row);
                
            case 'article':
                return $this->formatArticleData($row);
                
            case 'caissier':
                return $this->formatCaissierData($row);
                
            case 'paiement':
                return $this->formatPaiementData($row);
                
            case 'client':
                return $this->formatClientData($row);
                
            case 'ventes-details':
                return $this->formatVentesDetailsData($row);
                
            default:
                return $row;
        }
    }

    private function formatServeurData($row)
    {
        if (isset($row['chiffre_affaires']) && is_numeric($row['chiffre_affaires'])) {
            $row['chiffre_affaires'] = number_format($row['chiffre_affaires'], 2);
        }
        if (isset($row['moyenne_facture']) && is_numeric($row['moyenne_facture'])) {
            $row['moyenne_facture'] = number_format($row['moyenne_facture'], 2);
        }
        return $row;
    }

    private function formatFamilleData($row)
    {
        if (isset($row['chiffre_affaires']) && is_numeric($row['chiffre_affaires'])) {
            $row['chiffre_affaires'] = number_format($row['chiffre_affaires'], 2);
        }
        if (isset($row['prix_moyen']) && is_numeric($row['prix_moyen'])) {
            $row['prix_moyen'] = number_format($row['prix_moyen'], 2);
        }
        if (isset($row['quantite_vendue']) && is_numeric($row['quantite_vendue'])) {
            $row['quantite_vendue'] = number_format($row['quantite_vendue']);
        }
        return $row;
    }

    private function formatArticleData($row)
    {
        if (isset($row['chiffre_affaires']) && is_numeric($row['chiffre_affaires'])) {
            $row['chiffre_affaires'] = number_format($row['chiffre_affaires'], 2);
        }
        if (isset($row['prix_moyen']) && is_numeric($row['prix_moyen'])) {
            $row['prix_moyen'] = number_format($row['prix_moyen'], 2);
        }
        if (isset($row['quantite_vendue']) && is_numeric($row['quantite_vendue'])) {
            $row['quantite_vendue'] = number_format($row['quantite_vendue']);
        }
        return $row;
    }

    private function formatCaissierData($row)
    {
        if (isset($row['chiffre_affaires']) && is_numeric($row['chiffre_affaires'])) {
            $row['chiffre_affaires'] = number_format($row['chiffre_affaires'], 2);
        }
        if (isset($row['moyenne_facture']) && is_numeric($row['moyenne_facture'])) {
            $row['moyenne_facture'] = number_format($row['moyenne_facture'], 2);
        }
        if (isset($row['premiere_vente']) && $row['premiere_vente']) {
            try {
                $row['premiere_vente'] = Carbon::parse($row['premiere_vente'])->format('d/m/Y H:i');
            } catch (\Exception $e) {
                $row['premiere_vente'] = $row['premiere_vente'];
            }
        }
        if (isset($row['derniere_vente']) && $row['derniere_vente']) {
            try {
                $row['derniere_vente'] = Carbon::parse($row['derniere_vente'])->format('d/m/Y H:i');
            } catch (\Exception $e) {
                $row['derniere_vente'] = $row['derniere_vente'];
            }
        }
        return $row;
    }

    private function formatPaiementData($row)
    {
        if (isset($row['chiffre_affaires']) && is_numeric($row['chiffre_affaires'])) {
            $row['chiffre_affaires'] = number_format($row['chiffre_affaires'], 2);
        }
        if (isset($row['moyenne_facture']) && is_numeric($row['moyenne_facture'])) {
            $row['moyenne_facture'] = number_format($row['moyenne_facture'], 2);
        }
        if (isset($row['pourcentage']) && is_numeric($row['pourcentage'])) {
            $row['pourcentage'] = number_format($row['pourcentage'], 1) . '%';
        }
        return $row;
    }

    private function formatClientData($row)
    {
        if (isset($row['chiffre_affaires']) && is_numeric($row['chiffre_affaires'])) {
            $row['chiffre_affaires'] = number_format($row['chiffre_affaires'], 2);
        }
        if (isset($row['moyenne_facture']) && is_numeric($row['moyenne_facture'])) {
            $row['moyenne_facture'] = number_format($row['moyenne_facture'], 2);
        }
        if (isset($row['premiere_visite']) && $row['premiere_visite']) {
            try {
                $row['premiere_visite'] = Carbon::parse($row['premiere_visite'])->format('d/m/Y');
            } catch (\Exception $e) {
                $row['premiere_visite'] = $row['premiere_visite'];
            }
        }
        if (isset($row['derniere_visite']) && $row['derniere_visite']) {
            try {
                $row['derniere_visite'] = Carbon::parse($row['derniere_visite'])->format('d/m/Y');
            } catch (\Exception $e) {
                $row['derniere_visite'] = $row['derniere_visite'];
            }
        }
        return $row;
    }

    private function formatVentesDetailsData($row)
    {
        if (isset($row['date_vente']) && $row['date_vente']) {
            try {
                $row['date_vente'] = Carbon::parse($row['date_vente'])->format('d/m/Y H:i');
            } catch (\Exception $e) {
                $row['date_vente'] = $row['date_vente'];
            }
        }
        if (isset($row['montant']) && is_numeric($row['montant'])) {
            $row['montant'] = number_format($row['montant'], 2);
        }
        return $row;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        switch ($this->type) {
            case 'serveur':
                return [
                    'N°',
                    'Code Serveur',
                    'Nom Serveur', 
                    'Nombre Factures',
                    'Chiffre d\'Affaires (DH)',
                    'Moyenne Facture (DH)'
                ];
            case 'famille':
                return [
                    'N°',
                    'Famille',
                    'Nombre Factures',
                    'Quantité Vendue',
                    'Chiffre d\'Affaires (DH)',
                    'Prix Moyen (DH)'
                ];
            case 'article':
                return [
                    'N°',
                    'Référence',
                    'Désignation',
                    'Famille',
                    'Quantité Vendue',
                    'Chiffre d\'Affaires (DH)',
                    'Prix Moyen (DH)',
                    'Nombre Ventes'
                ];
            case 'caissier':
                return [
                    'N°',
                    'Code Caissier',
                    'Nom Caissier',
                    'Nombre Factures',
                    'Chiffre d\'Affaires (DH)',
                    'Moyenne Facture (DH)',
                    'Première Vente',
                    'Dernière Vente'
                ];
            case 'paiement':
                return [
                    'N°',
                    'Mode de Paiement',
                    'Nombre Factures',
                    'Chiffre d\'Affaires (DH)',
                    'Moyenne Facture (DH)',
                    'Pourcentage (%)'
                ];
            case 'client':
                return [
                    'N°',
                    'Réf. Client',
                    'Nom Client',
                    'Téléphone',
                    'Email',
                    'Adresse',
                    'Ville',
                    'Nb Factures',
                    'Chiffre d\'Affaires (DH)',
                    'Moyenne Facture (DH)',
                    'Première Visite',
                    'Dernière Visite'
                ];
            case 'ventes-details':
                return [
                    'N°',
                    'N° Facture',
                    'Date Vente',
                    'Client',
                    'Montant (DH)',
                    'Mode de Paiement',
                    'Caissier'
                ];
            default:
                return ['N°', 'Données Export'];
        }
    }

    /**
     * @return string
     */
    public function title(): string
    {
        $titles = [
            'serveur' => 'Rapport CA par Serveur',
            'famille' => 'Rapport CA par Famille', 
            'article' => 'Rapport CA par Article',
            'caissier' => 'Rapport CA par Caissier',
            'paiement' => 'Rapport CA par Mode de Paiement',
            'client' => 'Rapport CA par Client',
            'ventes-details' => 'Détails des Ventes',
            'dashboard' => 'Rapport Général CA'
        ];
        
        return $titles[$this->type] ?? 'Rapport CA';
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        switch ($this->type) {
            case 'serveur':
                return [
                    'A' => 5,   // N°
                    'B' => 15,  // Code
                    'C' => 25,  // Nom
                    'D' => 15,  // Nb Factures
                    'E' => 20,  // CA
                    'F' => 20,  // Moyenne
                ];
            case 'famille':
                return [
                    'A' => 5,   // N°
                    'B' => 30,  // Famille
                    'C' => 15,  // Nb Factures
                    'D' => 15,  // Quantité
                    'E' => 20,  // CA
                    'F' => 15,  // Prix Moyen
                ];
            case 'article':
                return [
                    'A' => 5,   // N°
                    'B' => 15,  // Référence
                    'C' => 35,  // Désignation
                    'D' => 20,  // Famille
                    'E' => 15,  // Quantité
                    'F' => 20,  // CA
                    'G' => 15,  // Prix Moyen
                    'H' => 12,  // Nb Ventes
                ];
            case 'caissier':
                return [
                    'A' => 5,   // N°
                    'B' => 15,  // Code
                    'C' => 25,  // Nom
                    'D' => 15,  // Nb Factures
                    'E' => 20,  // CA
                    'F' => 20,  // Moyenne
                    'G' => 18,  // Première Vente
                    'H' => 18,  // Dernière Vente
                ];
            case 'paiement':
                return [
                    'A' => 5,   // N°
                    'B' => 20,  // Mode
                    'C' => 15,  // Nb Factures
                    'D' => 20,  // CA
                    'E' => 20,  // Moyenne
                    'F' => 15,  // Pourcentage
                ];
            case 'client':
                return [
                    'A' => 5,   // N°
                    'B' => 12,  // Réf
                    'C' => 25,  // Nom
                    'D' => 15,  // Téléphone
                    'E' => 25,  // Email
                    'F' => 30,  // Adresse
                    'G' => 15,  // Ville
                    'H' => 12,  // Nb Factures
                    'I' => 18,  // CA
                    'J' => 18,  // Moyenne
                    'K' => 15,  // Première Visite
                    'L' => 15,  // Dernière Visite
                ];
            default:
                return ['A' => 15];
        }
    }

    /**
     * @return array
     */
    public function properties(): array
    {
        return [
            'creator'        => 'AccessPos Pro',
            'lastModifiedBy' => 'AccessPos Pro',
            'title'          => $this->title(),
            'description'    => 'Rapport généré automatiquement - ' . 
                               ($this->dateDebut ? "Période: {$this->dateDebut} - {$this->dateFin}" : ''),
            'subject'        => 'Rapport Chiffre d\'Affaires',
            'keywords'       => 'AccessPos, CA, Rapport, Ventes',
            'category'       => 'Rapports',
            'manager'        => 'AccessPos Pro',
            'company'        => 'AccessPos Pro',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->data->count() + 1;
        $lastColumn = $this->getLastColumn();
        
        return [
            // Style pour les en-têtes
            1 => [
                'font' => [
                    'bold' => true, 
                    'size' => 12,
                    'color' => ['argb' => 'FFFFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2E7D32'], // Vert foncé
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ],
            
            // Style pour les données
            "A2:{$lastColumn}{$lastRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFE0E0E0'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            
            // Style pour les colonnes numériques
            $this->getNumericColumnsRange($lastRow) => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
                'font' => [
                    'bold' => true,
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        $formats = ['A' => NumberFormat::FORMAT_NUMBER]; // N° ligne
        
        switch ($this->type) {
            case 'serveur':
                $formats['E'] = '_-* #,##0.00_-;-* #,##0.00_-;_-* "-"??_-;_-@_-'; // CA
                $formats['F'] = '_-* #,##0.00_-;-* #,##0.00_-;_-* "-"??_-;_-@_-'; // Moyenne
                break;
            case 'famille':
                $formats['D'] = '_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-'; // Quantité
                $formats['E'] = '_-* #,##0.00_-;-* #,##0.00_-;_-* "-"??_-;_-@_-'; // CA
                $formats['F'] = '_-* #,##0.00_-;-* #,##0.00_-;_-* "-"??_-;_-@_-'; // Prix moyen
                break;
            case 'article':
                $formats['E'] = '_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-'; // Quantité
                $formats['F'] = '_-* #,##0.00_-;-* #,##0.00_-;_-* "-"??_-;_-@_-'; // CA
                $formats['G'] = '_-* #,##0.00_-;-* #,##0.00_-;_-* "-"??_-;_-@_-'; // Prix moyen
                break;
            case 'caissier':
                $formats['E'] = '_-* #,##0.00_-;-* #,##0.00_-;_-* "-"??_-;_-@_-'; // CA
                $formats['F'] = '_-* #,##0.00_-;-* #,##0.00_-;_-* "-"??_-;_-@_-'; // Moyenne
                break;
            case 'paiement':
                $formats['D'] = '_-* #,##0.00_-;-* #,##0.00_-;_-* "-"??_-;_-@_-'; // CA
                $formats['E'] = '_-* #,##0.00_-;-* #,##0.00_-;_-* "-"??_-;_-@_-'; // Moyenne
                break;
            case 'client':
                $formats['I'] = '_-* #,##0.00_-;-* #,##0.00_-;_-* "-"??_-;_-@_-'; // CA
                $formats['J'] = '_-* #,##0.00_-;-* #,##0.00_-;_-* "-"??_-;_-@_-'; // Moyenne
                break;
        }
        
        return $formats;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $this->data->count() + 1;
                
                // Ajouter un résumé si les données existent
                if ($this->data->count() > 0) {
                    $this->addSummary($sheet, $lastRow);
                }
                
                // Ajouter l'en-tête du rapport
                $this->addReportHeader($sheet);
                
                // Figer les en-têtes
                $sheet->freezePane('A2');
                
                // Auto-ajuster la hauteur des lignes
                $sheet->getDefaultRowDimension()->setRowHeight(-1);
                
                // Alternance de couleurs pour les lignes
                $this->addAlternatingRowColors($sheet, $lastRow);
            },
        ];
    }

    /**
     * Ajouter un résumé en bas du tableau
     */
    private function addSummary(Worksheet $sheet, int $lastRow): void
    {
        $summaryRow = $lastRow + 3;
        
        // Titre du résumé
        $sheet->setCellValue("A{$summaryRow}", "RÉSUMÉ STATISTIQUE");
        $sheet->mergeCells("A{$summaryRow}:F{$summaryRow}");
        
        // Style du titre
        $sheet->getStyle("A{$summaryRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF2E7D32']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF1F8E9'],
            ],
        ]);
        
        // Statistiques
        $summaryRow++;
        $sheet->setCellValue("A{$summaryRow}", "Nombre total d'enregistrements:");
        $sheet->setCellValue("B{$summaryRow}", $this->data->count());
        
        // CA total si disponible
        if ($this->data->isNotEmpty()) {
            $totalCA = 0;
            $hasCA = false;
            
            foreach ($this->data as $item) {
                // Convertir stdClass en array pour accéder aux propriétés
                $itemArray = is_object($item) ? (array) $item : $item;
                
                if (isset($itemArray['chiffre_affaires']) && is_numeric($itemArray['chiffre_affaires'])) {
                    $totalCA += floatval($itemArray['chiffre_affaires']);
                    $hasCA = true;
                }
            }
            
            if ($hasCA && $totalCA > 0) {
                $summaryRow++;
                $sheet->setCellValue("A{$summaryRow}", "Chiffre d'affaires total:");
                $sheet->setCellValue("B{$summaryRow}", number_format($totalCA, 2) . ' DH');
                
                $summaryRow++;
                $sheet->setCellValue("A{$summaryRow}", "Chiffre d'affaires moyen:");
                $sheet->setCellValue("B{$summaryRow}", number_format($totalCA / $this->data->count(), 2) . ' DH');
            }
        }
        
        // Date de génération
        $summaryRow++;
        $sheet->setCellValue("A{$summaryRow}", "Date de génération:");
        $sheet->setCellValue("B{$summaryRow}", Carbon::now()->format('d/m/Y H:i:s'));
        
        // Période si disponible
        if ($this->dateDebut && $this->dateFin) {
            $summaryRow++;
            $sheet->setCellValue("A{$summaryRow}", "Période analysée:");
            $sheet->setCellValue("B{$summaryRow}", "{$this->dateDebut} au {$this->dateFin}");
        }
    }

    /**
     * Ajouter l'en-tête du rapport
     */
    private function addReportHeader(Worksheet $sheet): void
    {
        // Insérer des lignes en haut
        $sheet->insertNewRowBefore(1, 3);
        
        // Titre principal
        $sheet->setCellValue('A1', 'ACCESSPOS PRO - ' . strtoupper($this->title()));
        $lastColumn = $this->getLastColumn();
        $sheet->mergeCells("A1:{$lastColumn}1");
        
        // Style du titre principal
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1976D2'], // Bleu
            ],
        ]);
        
        // Date de génération
        $sheet->setCellValue('A2', 'Généré le: ' . Carbon::now()->format('d/m/Y à H:i:s'));
        $sheet->mergeCells("A2:{$lastColumn}2");
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        
        // Ligne vide
        $sheet->setCellValue('A3', '');
    }

    /**
     * Ajouter l'alternance de couleurs pour les lignes
     */
    private function addAlternatingRowColors(Worksheet $sheet, int $lastRow): void
    {
        $lastColumn = $this->getLastColumn();
        
        for ($row = 5; $row <= $lastRow + 3; $row += 2) { // Commencer à la ligne 5 (après l'en-tête)
            $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFF8F9FA'], // Gris très clair
                ],
            ]);
        }
    }

    /**
     * Obtenir la dernière colonne selon le type de rapport
     */
    private function getLastColumn(): string
    {
        $columnCounts = [
            'serveur' => 'F',
            'famille' => 'F',
            'article' => 'H',
            'caissier' => 'H',
            'paiement' => 'F',
            'client' => 'L',
            'ventes-details' => 'G',
        ];
        
        return $columnCounts[$this->type] ?? 'F';
    }

    /**
     * Obtenir la plage des colonnes numériques
     */
    private function getNumericColumnsRange(int $lastRow): string
    {
        switch ($this->type) {
            case 'serveur':
                return "D2:F{$lastRow}"; // Nb Factures, CA, Moyenne
            case 'famille':
                return "C2:F{$lastRow}"; // Nb Factures, Quantité, CA, Prix Moyen
            case 'article':
                return "E2:H{$lastRow}"; // Quantité, CA, Prix Moyen, Nb Ventes
            case 'caissier':
                return "D2:F{$lastRow}"; // Nb Factures, CA, Moyenne
            case 'paiement':
                return "C2:F{$lastRow}"; // Nb Factures, CA, Moyenne, Pourcentage
            case 'client':
                return "H2:J{$lastRow}"; // Nb Factures, CA, Moyenne
            default:
                return "A2:A{$lastRow}";
        }
    }
}
