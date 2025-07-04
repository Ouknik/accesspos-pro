<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model for REGLEMENT (المدفوعات)
 * 
 * @property mixed REG_REF
 */
class Payment extends Model
{
    use HasFactory;
    
    protected $connection = 'accesspos';
    protected $table = 'REGLEMENT';
    protected $primaryKey = 'REG_REF';
    public $timestamps = false;
    
    protected $fillable = [
        'reg_ref',
        'clt_ref',
        'type_reglement',
        'reg_date',
        'reg_montant',
        'reg_remarque'
    ];
    
    protected $casts = [
        'reg_date' => 'datetime',
        'reg_montant' => 'decimal:2'
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