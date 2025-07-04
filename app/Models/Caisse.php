<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modèle pour CAISSE (Gestion des Caisses)
 * 
 * @property int CSS_ID
 * @property string CSS_DESIGNATION
 */
class Caisse extends Model
{
    use HasFactory;
    
    protected $connection = 'sqlsrv';
    protected $table = 'CAISSE';
    protected $primaryKey = 'CSS_ID';
    public $timestamps = false;
    
    protected $fillable = [
        'CSS_ID',
        'CSS_DESIGNATION',
        'CSS_SOLDE_INITIAL',
        'CSS_SOLDE_ACTUEL',
        'CSS_DATE_OUVERTURE',
        'CSS_DATE_FERMETURE',
        'CSS_ETAT',
        'CSS_UTILISATEUR'
    ];
    
    protected $casts = [
        'CSS_SOLDE_INITIAL' => 'decimal:2',
        'CSS_SOLDE_ACTUEL' => 'decimal:2',
        'CSS_DATE_OUVERTURE' => 'datetime',
        'CSS_DATE_FERMETURE' => 'datetime',
        'CSS_ETAT' => 'boolean'
    ];
    
    /**
     * Relation avec les factures de vente
     */
    public function facturesVente()
    {
        return $this->hasMany(Sale::class, 'css_id_caisse', 'CSS_ID');
    }
    
    /**
     * Calculer le chiffre d'affaires de la caisse
     */
    public function chiffreAffaires($dateDebut = null, $dateFin = null)
    {
        $query = $this->facturesVente();
        
        if ($dateDebut) {
            $query->where('fctv_date', '>=', $dateDebut);
        }
        
        if ($dateFin) {
            $query->where('fctv_date', '<=', $dateFin);
        }
        
        return $query->sum('fctv_mnt_ttc');
    }
    
    /**
     * Obtenir le nombre de transactions
     */
    public function nombreTransactions($dateDebut = null, $dateFin = null)
    {
        $query = $this->facturesVente();
        
        if ($dateDebut) {
            $query->where('fctv_date', '>=', $dateDebut);
        }
        
        if ($dateFin) {
            $query->where('fctv_date', '<=', $dateFin);
        }
        
        return $query->count();
    }
}
