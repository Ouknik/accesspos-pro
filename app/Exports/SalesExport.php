<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\SaleDetail;
use Carbon\Carbon;

class SalesExport
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Sale::with(['customer', 'details', 'user']);

        // Appliquer les filtres de date
        if (!empty($this->filters['date_debut'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_debut']);
        }

        if (!empty($this->filters['date_fin'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_fin']);
        }

        // Filtres additionnels
        if (!empty($this->filters['caissier'])) {
            $query->where('user_id', $this->filters['caissier']);
        }

        if (!empty($this->filters['type_paiement'])) {
            $query->where('payment_method', $this->filters['type_paiement']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'N° Vente',
            'Date/Heure',
            'Client',
            'Caissier',
            'Articles',
            'Quantité Totale',
            'Montant HT',
            'TVA',
            'Montant TTC',
            'Mode de Paiement',
            'Statut'
        ];
    }

    public function toArray(): array
    {
        $sales = $this->collection();
        $data = [];
        
        // Ajouter l'en-tête
        $data[] = $this->headings();
        
        // Ajouter les données
        foreach ($sales as $sale) {
            $totalQuantity = $sale->details->sum('quantity') ?? 0;
            $totalHT = $sale->details->sum(function($detail) {
                return $detail->quantity * $detail->unit_price;
            }) ?? 0;
            $tva = $totalHT * 0.20; // 20% TVA par défaut
            $totalTTC = $totalHT + $tva;

            $articles = $sale->details->count() . ' article(s)';

            $data[] = [
                'VTE-' . str_pad($sale->id, 6, '0', STR_PAD_LEFT),
                Carbon::parse($sale->created_at)->format('d/m/Y H:i'),
                $sale->customer->name ?? 'Client anonyme',
                $sale->user->name ?? 'Caissier inconnu',
                $articles,
                $totalQuantity,
                number_format($totalHT, 2, ',', ' ') . ' €',
                number_format($tva, 2, ',', ' ') . ' €',
                number_format($totalTTC, 2, ',', ' ') . ' €',
                ucfirst($sale->payment_method ?? 'Non spécifié'),
                $sale->status == 'completed' ? 'Terminée' : 'En cours'
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
