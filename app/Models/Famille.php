<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modèle pour FAMILLE (Familles d'Articles)
 * 
 * @property string FAM_REF
 * @property string FAM_DESIGNATION
 */
class Famille extends Model
{
    use HasFactory;
    
    protected $connection = 'sqlsrv';
    protected $table = 'FAMILLE';
    protected $primaryKey = 'FAM_REF';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'FAM_REF',
        'FAM_DESIGNATION',
        'FAM_COULEUR',
        'FAM_ORDRE'
    ];
    
    /**
     * Relation avec les articles
     */
    public function articles()
    {
        return $this->hasMany(Article::class, 'fam_ref', 'FAM_REF');
    }
    
    /**
     * Obtenir les sous-familles
     */
    public function sousFamilles()
    {
        return $this->hasMany(SubCategory::class, 'FAM_REF', 'FAM_REF');
    }
    
    /**
     * Calculer la valeur totale des ventes pour cette famille
     */
    public function chiffreAffaires()
    {
        return $this->articles()
            ->join('FACTURE_VNT_DETAIL', 'ARTICLE.ART_REF', '=', 'FACTURE_VNT_DETAIL.ART_REF')
            ->sum('FACTURE_VNT_DETAIL.FCTVD_PRIX_TOTAL');
    }
}
