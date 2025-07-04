<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model for COMMANDE_ACHAT (أوامر الشراء)
 * 
 * @property mixed ACH_REF
 */
class Purchase extends Model
{
    use HasFactory;
    
    protected $connection = 'accesspos';
    protected $table = 'COMMANDE_ACHAT';
    protected $primaryKey = 'ACH_REF';
    public $timestamps = false;
    
    protected $fillable = [
        'ach_ref',
        'frs_ref',
        'etp_ref',
        'ach_numero',
        'ach_remise',
        'ach_valide',
        'ach_transformer',
        'ach_dateliv',
        'ach_utilisateur',
        'ach_dateplustard',
        'ach_montant_ht',
        'ach_montant_ttc',
        'ach_datecommande',
        'ach_remarque'
    ];
    
    protected $casts = [
        'ach_remise' => 'decimal:2',
        'ach_valide' => 'boolean',
        'ach_transformer' => 'boolean',
        'ach_dateliv' => 'datetime',
        'ach_dateplustard' => 'datetime',
        'ach_montant_ht' => 'decimal:2',
        'ach_montant_ttc' => 'decimal:2',
        'ach_datecommande' => 'datetime'
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