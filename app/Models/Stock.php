<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model for STOCK (المخزون)
 * 
 * @property mixed ART_REF
 */
class Stock extends Model
{
    use HasFactory;
    
    protected $connection = 'sqlsrv';
    protected $table = 'STOCK';
    protected $primaryKey = 'ART_REF';
    public $timestamps = false;
    
    protected $fillable = [
        'art_ref',
        'etp_ref',
        'stk_qte'
    ];
    
    protected $casts = [
        'stk_qte' => 'decimal:2'
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