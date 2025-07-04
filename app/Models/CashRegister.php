<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model for CAISSE (صناديق المبيعات)
 * 
 * @property mixed CSS_ID_CAISSE
 */
class CashRegister extends Model
{
    use HasFactory;
    
    protected $connection = 'accesspos';
    protected $table = 'CAISSE';
    protected $primaryKey = 'CSS_ID_CAISSE';
    public $timestamps = false;
    
    protected $fillable = [
        'css_id_caisse',
        'css_libelle_caisse',
        'css_avec_afficheur',
        'css_num_cmd',
        'css_num_fact'
    ];
    
    protected $casts = [
        'css_avec_afficheur' => 'boolean'
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