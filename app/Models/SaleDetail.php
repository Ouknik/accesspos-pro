<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model for FACTURE_VNT_DETAIL (تفاصيل فواتير المبيعات)
 * 
 * @property mixed FCTV_REF
 */
class SaleDetail extends Model
{
    use HasFactory;
    
    protected $connection = 'accesspos';
    protected $table = 'FACTURE_VNT_DETAIL';
    protected $primaryKey = 'FCTV_REF';
    public $timestamps = false;
    
    protected $fillable = [
        'fctv_ref',
        'art_ref',
        'fvd_colisage',
        'fvd_remise',
        'fvd_nbr_colis',
        'fvd_prix_vnt_ht',
        'fvd_prix_vnt_ttc',
        'fvd_tva',
        'fvd_nbr__gratuite',
        'fvd_qte',
        'fvd_numbl',
        'trf_libelle',
        'fvd_id',
        'ismenu',
        'namemenu'
    ];
    
    protected $casts = [
        'fvd_colisage' => 'decimal:2',
        'fvd_remise' => 'decimal:2',
        'fvd_nbr_colis' => 'decimal:2',
        'fvd_prix_vnt_ht' => 'decimal:2',
        'fvd_prix_vnt_ttc' => 'decimal:2',
        'fvd_tva' => 'decimal:2',
        'fvd_nbr__gratuite' => 'decimal:2',
        'fvd_qte' => 'decimal:2',
        'fvd_id' => 'integer',
        'ismenu' => 'boolean'
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