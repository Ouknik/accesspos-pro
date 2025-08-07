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
        'ART_STOCK_MAX' => 'integer',
        'ART_STOCKABLE' => 'boolean',
        'ART_ACHAT' => 'boolean',
        'ART_VENTE' => 'boolean',
        'ART_PLU' => 'integer',
        'IsMenu' => 'boolean',
        'IsIngredient' => 'boolean'
    ];
    
    // تعطيل التحقق من التوقيتات التلقائية
    const CREATED_AT = null;
    const UPDATED_AT = null;

    /**
     * Relation avec les détails de factures
     */
    public function factureDetails()
    {
        return $this->hasMany(FactureDetail::class, 'FVD_ARTICLE', 'ART_REF');
    }

    /**
     * Relation avec les détails de commandes
     */
    public function commandeDetails()
    {
        return $this->hasMany(CommandeDetail::class, 'DVD_ARTICLE', 'ART_REF');
    }

    /**
     * Scope pour les articles vendables
     */
    public function scopeVendables($query)
    {
        return $query->where('ART_VENTE', 1);
    }

    /**
     * Scope pour les articles stockables
     */
    public function scopeStockables($query)
    {
        return $query->where('ART_STOCKABLE', 1);
    }

    /**
     * Scope pour rechercher par nom ou référence
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('ART_DESIGNATION', 'like', "%{$search}%")
              ->orWhere('ART_REF', 'like', "%{$search}%")
              ->orWhere('ART_LIBELLE_TICKET', 'like', "%{$search}%");
        });
    }

    /**
     * Scope pour les menus
     */
    public function scopeMenus($query)
    {
        return $query->where('IsMenu', 1);
    }

    /**
     * Accessor pour le prix de vente TTC
     */
    public function getPrixVenteTtcAttribute()
    {
        return $this->ART_PRIX_VENTE * (1 + $this->ART_TVA_VENTE / 100);
    }

    /**
     * Accessor pour la marge brute
     */
    public function getMargeAttribute()
    {
        if (!$this->ART_PRIX_ACHAT || $this->ART_PRIX_ACHAT <= 0) {
            return 0;
        }
        
        return $this->ART_PRIX_VENTE - $this->ART_PRIX_ACHAT;
    }

    /**
     * Accessor pour le pourcentage de marge
     */
    public function getPourcentageMargeAttribute()
    {
        if (!$this->ART_PRIX_ACHAT || $this->ART_PRIX_ACHAT <= 0) {
            return 0;
        }
        
        return ($this->marge / $this->ART_PRIX_ACHAT) * 100;
    }

    /**
     * Rechercher des articles pour l'autocomplétion
     */
    public static function searchForAutocomplete($query, $limit = 10)
    {
        return self::vendables()
                  ->search($query)
                  ->select('ART_REF', 'ART_DESIGNATION', 'ART_PRIX_VENTE', 'ART_TVA_VENTE', 'ART_LIBELLE_TICKET')
                  ->limit($limit)
                  ->get()
                  ->map(function($article) {
                      return [
                          'id' => $article->ART_REF,
                          'ref' => $article->ART_REF,
                          'designation' => $article->ART_DESIGNATION,
                          'prix_vente_ht' => $article->ART_PRIX_VENTE,
                          'prix_vente_ttc' => $article->prix_vente_ttc,
                          'tva' => $article->ART_TVA_VENTE,
                          'text' => $article->ART_DESIGNATION . ' - ' . $article->ART_REF
                      ];
                  });
    }

    /**
     * Obtenir la quantité vendue
     */
    public function getQuantiteVendueAttribute()
    {
        return $this->factureDetails()
                   ->whereHas('facture', function($query) {
                       $query->where('FCTV_VALIDE', 1)->where('FCTV_ETAT', 1);
                   })
                   ->sum('FVD_QTE');
    }

    /**
     * Obtenir le chiffre d'affaires généré
     */
    public function getChiffreAffairesAttribute()
    {
        return $this->factureDetails()
                   ->whereHas('facture', function($query) {
                       $query->where('FCTV_VALIDE', 1)->where('FCTV_ETAT', 1);
                   })
                   ->get()
                   ->sum(function($detail) {
                       return ($detail->FVD_QTE * $detail->FVD_PRIX_VNT_TTC) - $detail->FVD_REMISE;
                   });
    }

    /**
     * Vérifier si l'article est disponible à la vente
     */
    public function isDisponible()
    {
        return $this->ART_VENTE == 1;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return $this->primaryKey;
    }
}