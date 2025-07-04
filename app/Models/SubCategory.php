<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model for SOUS_FAMILLE (الفئات الفرعية)
 * 
 * @property mixed SFM_REF
 */
class SubCategory extends Model
{
    use HasFactory;
    
    protected $connection = 'accesspos';
    protected $table = 'SOUS_FAMILLE';
    protected $primaryKey = 'SFM_REF';
    public $timestamps = false;
    
    protected $fillable = [
        'sfm_ref',
        'fam_ref',
        'sfm_lib',
        'sfm_ordre_affichage'
    ];
    
    protected $casts = [
        'sfm_ordre_affichage' => 'integer'
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