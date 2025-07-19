<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model for ZONE (مناطق المطعم)
 * 
 * @property mixed ZON_REF
 * @property mixed ZON_LIB
 */
class Zone extends Model
{
    use HasFactory;
    
    protected $connection = 'accesspos';
    protected $table = 'ZONE';
    protected $primaryKey = 'ZON_REF';
    public $timestamps = false;
    
    protected $fillable = [
        'zon_ref',
        'zon_lib'
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

    /**
     * العلاقة مع الطاولات
     */
    public function tables()
    {
        return $this->hasMany(Table::class, 'ZON_REF', 'ZON_REF');
    }
}
