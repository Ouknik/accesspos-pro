<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactureDetail extends Model
{
    protected $table = 'FACTURE_VNT_DETAIL';
    protected $primaryKey = 'FVD_ID';
    public $timestamps = false;

    protected $fillable = [
        'FVD_FACTURE',
        'FVD_ARTICLE',
        'FVD_QTE',
        'FVD_PRIX_VNT_HT',
        'FVD_PRIX_VNT_TTC',
        'FVD_TVA',
        'FVD_REMISE',
        'IsMenu',
        'NameMenu'
    ];

    protected $casts = [
        'FVD_QTE' => 'decimal:2',
        'FVD_PRIX_VNT_HT' => 'decimal:2',
        'FVD_PRIX_VNT_TTC' => 'decimal:2',
        'FVD_TVA' => 'decimal:2',
        'FVD_REMISE' => 'decimal:2',
        'IsMenu' => 'boolean'
    ];

    /**
     * Relation avec la facture
     */
    public function facture()
    {
        return $this->belongsTo(Facture::class, 'FVD_FACTURE', 'FCTV_ID');
    }

    /**
     * Relation avec l'article
     */
    public function article()
    {
        return $this->belongsTo(Article::class, 'FVD_ARTICLE', 'ART_ID');
    }

    /**
     * Accessor pour le total de la ligne
     */
    public function getTotalLigneAttribute()
    {
        return ($this->FVD_QTE * $this->FVD_PRIX_VNT_TTC) - $this->FVD_REMISE;
    }

    /**
     * Accessor pour le total HT de la ligne
     */
    public function getTotalLigneHtAttribute()
    {
        return ($this->FVD_QTE * $this->FVD_PRIX_VNT_HT) - $this->FVD_REMISE;
    }

    /**
     * Accessor pour le montant de la TVA de la ligne
     */
    public function getMontantTvaLigneAttribute()
    {
        return $this->total_ligne - $this->total_ligne_ht;
    }

    /**
     * Accessor pour la désignation de l'article
     */
    public function getArtDesignationAttribute()
    {
        return $this->article ? $this->article->ART_DESIGNATION : 'Article supprimé';
    }

    /**
     * Accessor pour la référence de l'article
     */
    public function getArtRefAttribute()
    {
        return $this->article ? $this->article->ART_REF : 'N/A';
    }

    /**
     * Accessor pour la catégorie de l'article
     */
    public function getArtCategorieAttribute()
    {
        return $this->article && $this->article->category ? 
               $this->article->category->CAT_DESIGNATION : 'Non catégorisé';
    }

    /**
     * Calculer le prix TTC à partir du prix HT et de la TVA
     */
    public function calculatePrixTTC()
    {
        return $this->FVD_PRIX_VNT_HT * (1 + $this->FVD_TVA / 100);
    }

    /**
     * Calculer le prix HT à partir du prix TTC et de la TVA
     */
    public function calculatePrixHT()
    {
        return $this->FVD_PRIX_VNT_TTC / (1 + $this->FVD_TVA / 100);
    }

    /**
     * Mettre à jour les prix automatiquement
     */
    public function updatePrices()
    {
        if ($this->FVD_PRIX_VNT_HT > 0) {
            $this->FVD_PRIX_VNT_TTC = $this->calculatePrixTTC();
        } elseif ($this->FVD_PRIX_VNT_TTC > 0) {
            $this->FVD_PRIX_VNT_HT = $this->calculatePrixHT();
        }
    }

    /**
     * Scope pour filtrer par article
     */
    public function scopeArticle($query, $articleId)
    {
        return $query->where('FVD_ARTICLE', $articleId);
    }

    /**
     * Scope pour les lignes avec remise
     */
    public function scopeAvecRemise($query)
    {
        return $query->where('FVD_REMISE', '>', 0);
    }

    /**
     * Scope pour les menus
     */
    public function scopeMenus($query)
    {
        return $query->where('IsMenu', 1);
    }

    /**
     * Mutateur pour s'assurer que les prix sont cohérents
     */
    public function setFvdPrixVntHtAttribute($value)
    {
        $this->attributes['FVD_PRIX_VNT_HT'] = $value;
        if ($value > 0 && isset($this->attributes['FVD_TVA'])) {
            $this->attributes['FVD_PRIX_VNT_TTC'] = $value * (1 + $this->attributes['FVD_TVA'] / 100);
        }
    }

    /**
     * Mutateur pour la TVA
     */
    public function setFvdTvaAttribute($value)
    {
        $this->attributes['FVD_TVA'] = $value;
        if (isset($this->attributes['FVD_PRIX_VNT_HT']) && $this->attributes['FVD_PRIX_VNT_HT'] > 0) {
            $this->attributes['FVD_PRIX_VNT_TTC'] = $this->attributes['FVD_PRIX_VNT_HT'] * (1 + $value / 100);
        }
    }

    /**
     * Vérifier si la quantité est disponible en stock
     */
    public function checkStock()
    {
        if (!$this->article) {
            return false;
        }

        return $this->article->ART_QTE_STOCK >= $this->FVD_QTE;
    }

    /**
     * Obtenir les statistiques des ventes par article
     */
    public static function getVentesParArticle($dateDebut = null, $dateFin = null)
    {
        $query = self::select('FVD_ARTICLE')
                    ->selectRaw('SUM(FVD_QTE) as qte_vendue')
                    ->selectRaw('SUM((FVD_QTE * FVD_PRIX_VNT_TTC) - FVD_REMISE) as ca_article')
                    ->selectRaw('COUNT(DISTINCT FVD_FACTURE) as nb_factures')
                    ->with(['article' => function($query) {
                        $query->select('ART_ID', 'ART_DESIGNATION', 'ART_REF');
                    }])
                    ->whereHas('facture', function($query) {
                        $query->validees();
                    })
                    ->groupBy('FVD_ARTICLE')
                    ->orderBy('ca_article', 'desc');

        if ($dateDebut && $dateFin) {
            $query->whereHas('facture', function($q) use ($dateDebut, $dateFin) {
                $q->periode($dateDebut, $dateFin);
            });
        }

        return $query->get();
    }

    /**
     * Obtenir les articles les plus vendus
     */
    public static function getTopArticles($limit = 10, $dateDebut = null, $dateFin = null)
    {
        return self::getVentesParArticle($dateDebut, $dateFin)->take($limit);
    }

    /**
     * Calculer la marge sur la ligne
     */
    public function getMargeAttribute()
    {
        if (!$this->article || !$this->article->ART_PRIX_ACHAT) {
            return 0;
        }

        $prixAchat = $this->article->ART_PRIX_ACHAT * $this->FVD_QTE;
        $prixVente = $this->total_ligne_ht;
        
        return $prixVente - $prixAchat;
    }

    /**
     * Calculer le pourcentage de marge
     */
    public function getPourcentageMargeAttribute()
    {
        if (!$this->article || !$this->article->ART_PRIX_ACHAT || $this->total_ligne_ht == 0) {
            return 0;
        }

        return ($this->marge / $this->total_ligne_ht) * 100;
    }

    /**
     * Événement avant sauvegarde
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // S'assurer que les prix sont cohérents
            $model->updatePrices();
        });
    }
}
