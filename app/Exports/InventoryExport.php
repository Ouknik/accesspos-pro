<?php

namespace App\Exports;

use App\Models\Stock;
use App\Models\Article;
use Carbon\Carbon;

class InventoryExport
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Article::with(['stock', 'category', 'subCategory']);

        // Filtres
        if (!empty($this->filters['famille'])) {
            $query->where('category_id', $this->filters['famille']);
        }

        if (!empty($this->filters['sous_famille'])) {
            $query->where('sub_category_id', $this->filters['sous_famille']);
        }

        if (!empty($this->filters['statut_stock'])) {
            if ($this->filters['statut_stock'] === 'rupture') {
                $query->whereHas('stock', function($q) {
                    $q->where('quantity', '<=', 0);
                });
            } elseif ($this->filters['statut_stock'] === 'faible') {
                $query->whereHas('stock', function($q) {
                    $q->where('quantity', '>', 0)->where('quantity', '<=', 10);
                });
            }
        }

        return $query->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'Code Article',
            'Nom Article',
            'Famille',
            'Sous-Famille',
            'Stock Actuel',
            'Stock Minimum',
            'Prix d\'Achat',
            'Prix de Vente',
            'Marge (%)',
            'Valeur Stock',
            'Statut',
            'Dernière MAJ'
        ];
    }

    public function toArray(): array
    {
        $articles = $this->collection();
        $data = [];
        
        // Ajouter l'en-tête
        $data[] = $this->headings();
        
        // Ajouter les données
        foreach ($articles as $article) {
            $stock = $article->stock ?? null;
            $currentStock = $stock ? $stock->quantity : 0;
            $minStock = $stock ? $stock->min_quantity : 0;
            
            $purchasePrice = $article->purchase_price ?? 0;
            $salePrice = $article->sale_price ?? 0;
            $margin = $purchasePrice > 0 ? (($salePrice - $purchasePrice) / $purchasePrice) * 100 : 0;
            $stockValue = $currentStock * $purchasePrice;

            // Déterminer le statut
            $status = 'Normal';
            if ($currentStock <= 0) {
                $status = 'Rupture';
            } elseif ($currentStock <= $minStock) {
                $status = 'Stock faible';
            }

            $data[] = [
                $article->code ?? 'ART-' . str_pad($article->id, 4, '0', STR_PAD_LEFT),
                $article->name,
                $article->category->name ?? 'Non classé',
                $article->subCategory->name ?? 'Non classé',
                $currentStock,
                $minStock,
                number_format($purchasePrice, 2, ',', ' ') . ' €',
                number_format($salePrice, 2, ',', ' ') . ' €',
                number_format($margin, 1) . '%',
                number_format($stockValue, 2, ',', ' ') . ' €',
                $status,
                $stock ? Carbon::parse($stock->updated_at)->format('d/m/Y H:i') : 'N/A'
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
