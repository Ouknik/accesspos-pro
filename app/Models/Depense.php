<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modèle pour DEPENSE (Dépenses et Sorties de Caisse)
 */
class Depense extends Model
{
    use HasFactory;
    
    protected $connection = 'sqlsrv';
    protected $table = 'DEPENSE';
    protected $primaryKey = 'DEP_ID';
    public $timestamps = false;
    
    protected $fillable = [
        'DEP_ID',
        'DEP_MOTIF',
        'DEP_MONTANT',
        'DEP_DATE',
        'DEP_DESCRIPTION',
        'DEP_BENEFICIAIRE',
        'DEP_MODE_PAIEMENT',
        'DEP_UTILISATEUR',
        'CSS_ID',
        'DEP_REFERENCE'
    ];
    
    protected $casts = [
        'DEP_MONTANT' => 'decimal:2',
        'DEP_DATE' => 'datetime'
    ];
    
    /**
     * Relation avec la caisse
     */
    public function caisse()
    {
        return $this->belongsTo(Caisse::class, 'CSS_ID', 'CSS_ID');
    }
    
    /**
     * Obtenir les dépenses par motif
     */
    public static function statistiquesParMotif($dateDebut = null, $dateFin = null)
    {
        $query = self::query();
        
        if ($dateDebut) {
            $query->where('DEP_DATE', '>=', $dateDebut);
        }
        
        if ($dateFin) {
            $query->where('DEP_DATE', '<=', $dateFin);
        }
        
        return $query->selectRaw('DEP_MOTIF, SUM(DEP_MONTANT) as total_montant, COUNT(*) as nombre_depenses')
                    ->groupBy('DEP_MOTIF')
                    ->orderByDesc('total_montant')
                    ->get();
    }
    
    /**
     * Obtenir le total des dépenses pour une période
     */
    public static function totalDepensesPeriode($dateDebut, $dateFin)
    {
        return self::whereBetween('DEP_DATE', [$dateDebut, $dateFin])
                  ->sum('DEP_MONTANT');
    }
    
    /**
     * Obtenir les dépenses par utilisateur
     */
    public static function depensesParUtilisateur($dateDebut = null, $dateFin = null)
    {
        $query = self::query();
        
        if ($dateDebut) {
            $query->where('DEP_DATE', '>=', $dateDebut);
        }
        
        if ($dateFin) {
            $query->where('DEP_DATE', '<=', $dateFin);
        }
        
        return $query->selectRaw('DEP_UTILISATEUR, SUM(DEP_MONTANT) as total_depenses, COUNT(*) as nombre_operations')
                    ->groupBy('DEP_UTILISATEUR')
                    ->orderByDesc('total_depenses')
                    ->get();
    }
}
