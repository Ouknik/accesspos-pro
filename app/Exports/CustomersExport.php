<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\Sale;
use Carbon\Carbon;

class CustomersExport
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Customer::with(['sales']);

        // Filtres de date pour les achats
        if (!empty($this->filters['date_debut']) || !empty($this->filters['date_fin'])) {
            $query->whereHas('sales', function($q) {
                if (!empty($this->filters['date_debut'])) {
                    $q->whereDate('created_at', '>=', $this->filters['date_debut']);
                }
                if (!empty($this->filters['date_fin'])) {
                    $q->whereDate('created_at', '<=', $this->filters['date_fin']);
                }
            });
        }

        // Filtre par type de client
        if (!empty($this->filters['type_client'])) {
            $query->where('type', $this->filters['type_client']);
        }

        return $query->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'Code Client',
            'Nom',
            'Email',
            'Téléphone',
            'Adresse',
            'Type',
            'Nb Achats',
            'Total Acheté',
            'Dernier Achat',
            'Date Création',
            'Statut'
        ];
    }

    public function toArray(): array
    {
        $customers = $this->collection();
        $data = [];
        
        // Ajouter l'en-tête
        $data[] = $this->headings();
        
        // Ajouter les données
        foreach ($customers as $customer) {
            $salesCount = $customer->sales->count();
            $totalSpent = $customer->sales->sum('total_amount') ?? 0;
            $lastSale = $customer->sales->sortByDesc('created_at')->first();

            $data[] = [
                'CLI-' . str_pad($customer->id, 4, '0', STR_PAD_LEFT),
                $customer->name,
                $customer->email ?? 'Non renseigné',
                $customer->phone ?? 'Non renseigné',
                $customer->address ?? 'Non renseignée',
                ucfirst($customer->type ?? 'Particulier'),
                $salesCount,
                number_format($totalSpent, 2, ',', ' ') . ' €',
                $lastSale ? Carbon::parse($lastSale->created_at)->format('d/m/Y') : 'Jamais',
                Carbon::parse($customer->created_at)->format('d/m/Y'),
                $customer->is_active ? 'Actif' : 'Inactif'
            ];
        }
        
        return $data;
    }

    public function toCsv(): string
    {
        $data = $this->toArray();
        $output = '';
        
        foreach ($data as $row) {
            $output .= '"' . implode('","', $row) . '"' . "\n";
        }
        
        return $output;
    }
}
