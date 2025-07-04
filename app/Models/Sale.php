<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model for FACTURE_VNT (فواتير المبيعات)
 * 
 * @property mixed FCTV_REF
 */
class Sale extends Model
{
    use HasFactory;
    
    protected $connection = 'sqlsrv';
    protected $table = 'FACTURE_VNT';
    protected $primaryKey = 'FCTV_REF';
    public $timestamps = false;
    
    protected $fillable = [
        'fctv_ref',
        'etp_ref',
        'clt_ref',
        'fctv_numero',
        'fctv_remise',
        'fctv_exonore',
        'fctv_valide',
        'fctv_utilisateur',
        'fctv_mnt_ht',
        'fctv_mnt_ttc',
        'fctv_date',
        'fctv_remarque',
        'fctv_modepaiement',
        'fct_mnt_total',
        'fct_mnt_rgl',
        'tab_ref',
        'fctv_serveur',
        'css_id_caisse',
        'montantcharte',
        'montantespece'
    ];
    
    protected $casts = [
        'fctv_remise' => 'decimal:2',
        'fctv_exonore' => 'boolean',
        'fctv_valide' => 'boolean',
        'fctv_mnt_ht' => 'decimal:2',
        'fctv_mnt_ttc' => 'decimal:2',
        'fctv_date' => 'datetime',
        'fct_mnt_total' => 'decimal:2',
        'fct_mnt_rgl' => 'decimal:2',
        'montantcharte' => 'decimal:2',
        'montantespece' => 'decimal:2'
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