<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model for CLIENT (العملاء)
 * 
 * @property mixed CLT_REF
 */
class Customer extends Model
{
    use HasFactory;
    
    protected $connection = 'sqlsrv';
    protected $table = 'CLIENT';
    protected $primaryKey = 'CLT_REF';
    public $timestamps = false;
    
    protected $fillable = [
        'clt_ref',
        'cltcat_ref',
        'clt_client',
        'clt_telephone',
        'clt_email',
        'clt_site_web',
        'clt_fax',
        'clt_bloque',
        'clt_pointfidilio',
        'clt_fidele',
        'clt_commantaire',
        'clt_isentreprise',
        'clt_cnss',
        'clt_rc',
        'clt_patente',
        'clt_representant',
        'clt_envoisms',
        'clt_envoimms',
        'clt_envoiemail',
        'clt_envoicourier'
    ];
    
    protected $casts = [
        'clt_bloque' => 'boolean',
        'clt_pointfidilio' => 'integer',
        'clt_fidele' => 'boolean',
        'clt_isentreprise' => 'boolean',
        'clt_envoisms' => 'boolean',
        'clt_envoimms' => 'boolean',
        'clt_envoiemail' => 'boolean',
        'clt_envoicourier' => 'boolean',
        'clt_credit' => 'decimal:2'
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