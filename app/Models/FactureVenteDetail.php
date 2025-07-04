<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modèle pour FACTURE_VNT_DETAIL (Détails des Factures de Vente)
 */
class FactureVenteDetail extends Model
{
    use HasFactory;
    
    protected $connection = 'sqlsrv';
    protected $table = 'FACTURE_VNT_DETAIL';
    protected $primaryKey = 'FCTVD_ID';
    public $timestamps = false;
    
    protected $fillable = [
        'FCTVD_ID',
        'FCTV_REF',
        'ART_REF',
        'FCTVD_QUANTITE',
        'FCTVD_PRIX',
        'FCTVD_PRIX_TOTAL',
        'FCTVD_REMISE',
        'FCTVD_TVA',
        'FCTVD_ORDRE'
    ];
    
    protected $casts = [
        'FCTVD_QUANTITE' => 'decimal:3',
        'FCTVD_PRIX' => 'decimal:2',
        'FCTVD_PRIX_TOTAL' => 'decimal:2',
        'FCTVD_REMISE' => 'decimal:2',
        'FCTVD_TVA' => 'decimal:2'
    ];
    
    /**
     * Relation avec la facture de vente
     */
    public function factureVente()
    {
        return $this->belongsTo(Sale::class, 'FCTV_REF', 'FCTV_REF');
    }
    
    /**
     * Relation avec l'article
     */
    public function article()
    {
        return $this->belongsTo(Article::class, 'ART_REF', 'ART_REF');
    }
    
    /**
     * Calculer le montant total avec remise
     */
    public function getMontantTotalAttribute()
    {
        return ($this->FCTVD_QUANTITE * $this->FCTVD_PRIX) - $this->FCTVD_REMISE;
    }
    
    /**
     * Calculer le montant TVA
     */
    public function getMontantTvaAttribute()
    {
        return $this->getMontantTotalAttribute() * ($this->FCTVD_TVA / 100);
    }
}
