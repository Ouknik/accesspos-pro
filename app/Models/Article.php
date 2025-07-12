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
    
    // استخدام قاعدة البيانات SQL Server الحقيقية فقط
    protected $connection = 'sqlsrv';
    protected $table = 'ARTICLE';
    protected $primaryKey = 'ART_REF';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'ART_REF',
        'SFM_REF',
        'FRN_REFERENCE',
        'ART_DESIGNATION',
        'ART_TVA_ACHAT',
        'ART_TVA_VENTE',
        'ART_PRIX_ACHAT',
        'ART_PRIX_VENTE',
        'ART_LIBELLE_TICKET',
        'ART_DESCRIPTION',
        'ART_STOCK_MIN',
        'ART_STOCK_MAX',
        'ART_STOCKABLE',
        'ART_ACHAT',
        'ART_VENTE',
        'ART_PLU',
        'IsMenu',
        'IsIngredient'
    ];
    
    protected $casts = [
        'ART_TVA_ACHAT' => 'integer',
        'ART_TVA_VENTE' => 'decimal:2',
        'ART_PRIX_ACHAT' => 'decimal:2',
        'ART_PRIX_VENTE' => 'decimal:2',
        'ART_STOCK_MIN' => 'integer',
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