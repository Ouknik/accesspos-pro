<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model for COMMANDE_ACHAT_DETAIL (تفاصيل أوامر الشراء)
 * 
 * @property mixed ART_REF
 */
class PurchaseDetail extends Model
{
    use HasFactory;
    
    protected $connection = 'accesspos';
    protected $table = 'COMMANDE_ACHAT_DETAIL';
    protected $primaryKey = 'ART_REF';
    public $timestamps = false;
    
    protected $fillable = [
        'art_ref',
        'ach_ref',
        'cad_colisage',
        'cad_remise',
        'cad_qte',
        'cad_prixv_ht',
        'cad_tva',
        'cad_prix_ttc',
        'cad_qte_gratuit',
        'cad_nbr_colis'
    ];
    
    protected $casts = [
        'cad_colisage' => 'decimal:2',
        'cad_remise' => 'decimal:2',
        'cad_qte' => 'decimal:2',
        'cad_prixv_ht' => 'decimal:2',
        'cad_tva' => 'decimal:2',
        'cad_prix_ttc' => 'decimal:2',
        'cad_qte_gratuit' => 'decimal:2',
        'cad_nbr_colis' => 'decimal:2'
    ];
    
    // تعطيل التحقق من التوقيتات التلقائية
    const CREATED_AT = null;
    const UPDATED_AT = null;
    
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return $this->primaryKey;
    }
}