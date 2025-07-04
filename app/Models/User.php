<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    // Use the existing table name
    protected $table = 'UTILISATEUR';
    
    // Set the primary key
    protected $primaryKey = 'USR_REF';
    
    // Disable timestamps since the table doesn't have created_at/updated_at
    public $timestamps = false;
    
    // Disable auto-incrementing for the primary key
    public $incrementing = false;
    
    // Set the key type
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'USR_REF',
        'USR_LOGIN', 
        'USR_PASS',
        'USR_PRENOM',
        'USR_NOM',
        'LIBELLE_ROLE',
        'USR_ABREV',
        'USR_ADRESSE',
        'USR_VILLE',
        'USR_TEL_PORTABLE',
        'USR_FONCTION'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'USR_PASS',
        'remember_token',
    ];

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->USR_PASS;
    }

    /**
     * Get the login username for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'USR_LOGIN';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->USR_LOGIN;
    }

    /**
     * Get the name to be used for user display
     */
    public function getNameAttribute()
    {
        $fullName = trim($this->USR_PRENOM . ' ' . $this->USR_NOM);
        return $fullName ?: $this->USR_LOGIN ?: $this->USR_REF;
    }

    /**
     * Get the email attribute (fallback for auth systems that require it)
     */
    public function getEmailAttribute()
    {
        return $this->USR_LOGIN . '@accesspos.local';
    }

    /**
     * Check if the user has admin role
     */
    public function isAdmin()
    {
        return strtolower($this->LIBELLE_ROLE) === 'admin' || 
               strtolower($this->LIBELLE_ROLE) === 'administrateur' ||
               strtolower($this->LIBELLE_ROLE) === 'default';
    }

    /**
     * Check if the user can access dashboard
     */
    public function canAccessDashboard()
    {
        return $this->isAdmin() || 
               strtolower($this->LIBELLE_ROLE) === 'manager' ||
               strtolower($this->LIBELLE_ROLE) === 'caissier' ||
               strtolower($this->LIBELLE_ROLE) === 'serveur';
    }

    /**
     * Get user's role in French
     */
    public function getRoleNameAttribute()
    {
        return $this->LIBELLE_ROLE ?: 'Utilisateur';
    }

    /**
     * Scope to get active users
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('USR_LOGIN')
                    ->where('USR_LOGIN', '!=', '')
                    ->where('USR_LOGIN', '!=', ' ');
    }

    /**
     * Scope to get users by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('LIBELLE_ROLE', $role);
    }
}
