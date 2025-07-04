<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model for FAMILLE (فئات المنتجات)
 * 
 * @property mixed FAM_REF
 */
class Category extends Model
{
    use HasFactory;
    
    protected $connection = 'accesspos';
    protected $table = 'FAMILLE';
    protected $primaryKey = 'FAM_REF';
    public $timestamps = false;
    
    protected $fillable = [
        'fam_ref',
        'fam_lib',
        'fam_ordre_affichage'
    ];
    
    protected $casts = [
        'fam_ordre_affichage' => 'integer'
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