<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model for UTILISATEUR (المستخدمين والموظفين)
 * 
 * @property mixed USR_REF
 */
class Employee extends Model
{
    use HasFactory;
    
    protected $connection = 'accesspos';
    protected $table = 'UTILISATEUR';
    protected $primaryKey = 'USR_REF';
    public $timestamps = false;
    
    protected $fillable = [
        'usr_ref',
        'libelle_role',
        'usr_login',
        'usr_abrev',
        'usr_pass',
        'usr_style',
        'usr_prenom',
        'usr_nom',
        'usr_adresse',
        'usr_code_postal',
        'usr_ville',
        'usr_pays',
        'usr_tel_domicile',
        'usr_tel_portable',
        'usr_date_emb',
        'usr_date_naissance',
        'usr_cin',
        'usr_cnss',
        'usr_observation',
        'usr_fonction'
    ];
    
    protected $casts = [
        'usr_code_postal' => 'integer',
        'usr_date_emb' => 'datetime',
        'usr_date_naissance' => 'datetime'
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