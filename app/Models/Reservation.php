<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model for RESERVATION (الحجوزات)
 * 
 * @property mixed RES_REF
 */
class Reservation extends Model
{
    use HasFactory;
    
    protected $connection = 'accesspos';
    protected $table = 'RESERVATION';
    protected $primaryKey = 'RES_REF';
    public $timestamps = false;
    
    protected $fillable = [
        'res_ref',
        'clt_ref',
        'numero_reservation',
        'nbrcouvert_table',
        'date_reservation',
        'etat_reservation',
        'delai_reservation'
    ];
    
    protected $casts = [
        'nbrcouvert_table' => 'integer',
        'date_reservation' => 'datetime',
        'delai_reservation' => 'integer'
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