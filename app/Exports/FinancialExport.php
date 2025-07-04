<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Depense;
use App\Models\Payment;
use Carbon\Carbon;

class FinancialExport
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function getSalesData()
    {
        $query = Sale::query();

        if (!empty($this->filters['date_debut'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_debut']);
        }

        if (!empty($this->filters['date_fin'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_fin']);
        }

        return $query->where('status', 'completed')->get();
    }

    public function getPurchasesData()
    {
        $query = Purchase::query();

        if (!empty($this->filters['date_debut'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_debut']);
        }

        if (!empty($this->filters['date_fin'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_fin']);
        }

        return $query->get();
    }

    public function getExpensesData()
    {
        $query = Depense::query();

        if (!empty($this->filters['date_debut'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_debut']);
        }

        if (!empty($this->filters['date_fin'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_fin']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Type Opération',
            'Description',
            'Recettes',
            'Dépenses',
            'Solde',
            'Mode Paiement',
            'Référence'
        ];
    }

    public function toArray(): array
    {
        $data = [];
        $runningBalance = 0;
        
        // Ajouter l'en-tête
        $data[] = $this->headings();
        
        $operations = collect();

        // Ajouter les ventes
        foreach ($this->getSalesData() as $sale) {
            $amount = $sale->total_amount ?? 0;
            $runningBalance += $amount;
            
            $operations->push([
                'date' => Carbon::parse($sale->created_at),
                'type' => 'Vente',
                'description' => 'Vente N° VTE-' . str_pad($sale->id, 6, '0', STR_PAD_LEFT),
                'recettes' => $amount,
                'depenses' => 0,
                'solde' => $runningBalance,
                'mode_paiement' => ucfirst($sale->payment_method ?? 'Non spécifié'),
                'reference' => 'VTE-' . str_pad($sale->id, 6, '0', STR_PAD_LEFT)
            ]);
        }

        // Ajouter les achats
        foreach ($this->getPurchasesData() as $purchase) {
            $amount = $purchase->total_amount ?? 0;
            $runningBalance -= $amount;
            
            $operations->push([
                'date' => Carbon::parse($purchase->created_at),
                'type' => 'Achat',
                'description' => 'Achat N° ACH-' . str_pad($purchase->id, 6, '0', STR_PAD_LEFT),
                'recettes' => 0,
                'depenses' => $amount,
                'solde' => $runningBalance,
                'mode_paiement' => ucfirst($purchase->payment_method ?? 'Non spécifié'),
                'reference' => 'ACH-' . str_pad($purchase->id, 6, '0', STR_PAD_LEFT)
            ]);
        }

        // Ajouter les dépenses
        foreach ($this->getExpensesData() as $expense) {
            $amount = $expense->amount ?? 0;
            $runningBalance -= $amount;
            
            $operations->push([
                'date' => Carbon::parse($expense->created_at),
                'type' => 'Dépense',
                'description' => $expense->description ?? 'Dépense diverse',
                'recettes' => 0,
                'depenses' => $amount,
                'solde' => $runningBalance,
                'mode_paiement' => ucfirst($expense->payment_method ?? 'Non spécifié'),
                'reference' => 'DEP-' . str_pad($expense->id, 6, '0', STR_PAD_LEFT)
            ]);
        }

        // Trier par date
        $operations = $operations->sortBy('date');

        // Recalculer les soldes
        $balance = 0;
        foreach ($operations as $operation) {
            $balance += ($operation['recettes'] - $operation['depenses']);
            
            $data[] = [
                $operation['date']->format('d/m/Y H:i'),
                $operation['type'],
                $operation['description'],
                $operation['recettes'] > 0 ? number_format($operation['recettes'], 2, ',', ' ') . ' €' : '',
                $operation['depenses'] > 0 ? number_format($operation['depenses'], 2, ',', ' ') . ' €' : '',
                number_format($balance, 2, ',', ' ') . ' €',
                $operation['mode_paiement'],
                $operation['reference']
            ];
        }

        // Ajouter une ligne de total
        $totalRecettes = $operations->sum('recettes');
        $totalDepenses = $operations->sum('depenses');
        $soldeFinal = $totalRecettes - $totalDepenses;

        $data[] = ['', '', '', '', '', '', '', ''];
        $data[] = [
            '',
            'TOTAUX',
            '',
            number_format($totalRecettes, 2, ',', ' ') . ' €',
            number_format($totalDepenses, 2, ',', ' ') . ' €',
            number_format($soldeFinal, 2, ',', ' ') . ' €',
            '',
            ''
        ];
        
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
