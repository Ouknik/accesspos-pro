<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model for ARTICLE (المنتجات والمواد)
 * 
 * @property mixed ART_REF
 */
class Article extends Model
{
    use HasFactory;
    
    protected $connection = 'sqlsrv';
    protected $table = 'ARTICLE';
    protected $primaryKey = 'ART_REF';
    public $timestamps = false;
    
    protected $fillable = [
        'art_ref',
        'sfm_ref',
        'frn_reference',
        'art_designation',
        'art_tva_achat',
        'art_tva_vente',
        'art_prix_achat',
        'art_prix_vente',
        'art_libelle_ticket',
        'art_style',
        'art_description',
        'art_stock_min',
        'art_stock_max',
        'art_stockable',
        'art_achat',
        'art_vente',
        'art_plu',
        'art_dernier_pa',
        'art_date_creation',
        'art_date_modification'
    ];
    
    protected $casts = [
        'art_tva_achat' => 'integer',
        'art_tva_vente' => 'decimal:2',
        'art_prix_achat' => 'decimal:2',
        'art_prix_vente' => 'decimal:2',
        'art_stock_min' => 'integer',
        'art_stock_max' => 'integer',
        'art_stockable' => 'boolean',
        'art_achat' => 'boolean',
        'art_vente' => 'boolean',
        'art_plu' => 'integer'
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