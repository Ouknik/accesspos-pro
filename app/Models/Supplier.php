<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model for FOURNISSEUR (الموردين)
 * 
 * @property mixed FRS_REF
 */
class Supplier extends Model
{
    use HasFactory;
    
    protected $connection = 'accesspos';
    protected $table = 'FOURNISSEUR';
    protected $primaryKey = 'FRS_REF';
    public $timestamps = false;
    
    protected $fillable = [
        'frs_ref',
        'frs_raisonsocial',
        'frs_compt_compta',
        'frs_pays',
        'frs_ville',
        'frs_adress',
        'frs_website',
        'frs_codepostal',
        'frs_tel',
        'frs_fax',
        'frs_email',
        'frs_bloque',
        'frs_peutlivr_ssbc',
        'frs_rc',
        'frs_if',
        'frs_cnss',
        'frs_patente',
        'frs_numcomptb',
        'frs_agenceb',
        'frs_banque'
    ];
    
    protected $casts = [
        'frs_codepostal' => 'integer',
        'frs_bloque' => 'boolean',
        'frs_peutlivr_ssbc' => 'boolean',
        'frs_delaipaie' => 'integer'
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