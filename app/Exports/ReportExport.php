<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $data;
    protected $type;

    public function __construct($data, $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function collection()
    {
        switch ($this->type) {
            case 'ventes':
                return $this->data['sales'] ?? collect();
            case 'stock':
                return $this->data['articles'] ?? collect();
            case 'clients':
                return $this->data['clients'] ?? collect();
            default:
                return collect();
        }
    }

    public function headings(): array
    {
        switch ($this->type) {
            case 'ventes':
                return ['Référence', 'Date', 'Montant HT', 'Montant TTC', 'Mode Paiement', 'Caissier'];
            case 'stock':
                return ['Code', 'Désignation', 'Famille', 'Stock', 'Prix Achat', 'Prix Vente', 'Valeur'];
            case 'clients':
                return ['Code', 'Nom', 'Téléphone', 'Email', 'Fidèle', 'Actif'];
            default:
                return [];
        }
    }

    public function map($row): array
    {
        switch ($this->type) {
            case 'stock':
                $columns = $this->data['statistiques']['colonnes'] ?? [];
                return [
                    $this->getValue($row, $columns['code'] ?? null),
                    $this->getValue($row, $columns['designation'] ?? null),
                    $this->getValue($row, $columns['famille'] ?? null),
                    $this->getValue($row, $columns['stock'] ?? null, 'number'),
                    $this->getValue($row, $columns['prix_achat'] ?? null, 'currency'),
                    $this->getValue($row, $columns['prix_vente'] ?? null, 'currency'),
                    $this->calculateValue($row, $columns)
                ];
            case 'clients':
                $columns = $this->data['statistiques']['colonnes'] ?? [];
                return [
                    $this->getValue($row, $columns['code'] ?? null),
                    $this->getValue($row, $columns['nom'] ?? null),
                    $this->getValue($row, $columns['tel'] ?? null),
                    $this->getValue($row, $columns['email'] ?? null),
                    $this->getValue($row, $columns['fidele'] ?? null, 'boolean'),
                    $this->getValue($row, $columns['actif'] ?? null, 'boolean')
                ];
            default:
                return [];
        }
    }

    private function getValue($row, $column, $type = 'string')
    {
        if (!$column || !property_exists($row, $column)) {
            return $type === 'number' ? 0 : 'N/A';
        }

        $value = $row->{$column} ?? null;

        switch ($type) {
            case 'number':
                return floatval($value);
            case 'currency':
                return number_format(floatval($value), 2) . ' €';
            case 'boolean':
                return $value == 1 ? 'Oui' : 'Non';
            default:
                return $value ?? 'N/A';
        }
    }

    private function calculateValue($row, $columns)
    {
        $stock = $this->getValue($row, $columns['stock'] ?? null, 'number');
        $prix = $this->getValue($row, $columns['prix_achat'] ?? null, 'number');
        return number_format($stock * $prix, 2) . ' €';
    }

    public function title(): string
    {
        return 'Rapport ' . ucfirst($this->type);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}