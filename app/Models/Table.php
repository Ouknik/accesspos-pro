<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model for TABLE (طاولات المطعم)
 * 
 * @property mixed TAB_REF
 */
class Table extends Model
{
    use HasFactory;
    
    protected $connection = 'accesspos';
    protected $table = 'TABLE';
    protected $primaryKey = 'TAB_REF';
    public $timestamps = false;
    
    protected $fillable = [
        'tab_ref',
        'zon_ref',
        'ett_etat',
        'tab_lib',
        'tab_descript',
        'tab_nbr_couvert'
    ];
    
    protected $casts = [
        'tab_nbr_couvert' => 'integer'
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