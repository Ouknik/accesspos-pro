<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modèle pour REGLEMENT (Règlements et Paiements)
 */
class Reglement extends Model
{
    use HasFactory;
    
    protected $connection = 'sqlsrv';
    protected $table = 'REGLEMENT';
    protected $primaryKey = 'RGL_ID';
    public $timestamps = false;
    
    protected $fillable = [
        'RGL_ID',
        'FCTV_REF',
        'CLT_REF',
        'RGL_MONTANT',
        'RGL_DATE',
        'RGL_MODE_PAIEMENT',
        'RGL_REFERENCE',
        'RGL_BANQUE',
        'RGL_NUMERO_CHEQUE',
        'RGL_ECHEANCE',
        'RGL_STATUT'
    ];
    
    protected $casts = [
        'RGL_MONTANT' => 'decimal:2',
        'RGL_DATE' => 'datetime',
        'RGL_ECHEANCE' => 'date'
    ];
    
    /**
     * Relation avec la facture de vente
     */
    public function factureVente()
    {
        return $this->belongsTo(Sale::class, 'FCTV_REF', 'FCTV_REF');
    }
    
    /**
     * Relation avec le client
     */
    public function client()
    {
        return $this->belongsTo(Customer::class, 'CLT_REF', 'CLT_REF');
    }
    
    /**
     * Obtenir les règlements par mode de paiement
     */
    public static function statistiquesParModePaiement($dateDebut = null, $dateFin = null)
    {
        $query = self::query();
        
        if ($dateDebut) {
            $query->where('RGL_DATE', '>=', $dateDebut);
        }
        
        if ($dateFin) {
            $query->where('RGL_DATE', '<=', $dateFin);
        }
        
        return $query->selectRaw('RGL_MODE_PAIEMENT, SUM(RGL_MONTANT) as total_montant, COUNT(*) as nombre_transactions')
                    ->groupBy('RGL_MODE_PAIEMENT')
                    ->orderByDesc('total_montant')
                    ->get();
    }
}
