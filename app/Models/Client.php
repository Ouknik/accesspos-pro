<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'CLIENT';
    protected $primaryKey = 'CLT_ID';
    public $timestamps = false;

    protected $fillable = [
        'CLT_REF',
        'CLT_NOM',
        'CLT_PRENOM',
        'CLT_ADRESSE',
        'CLT_TELEPHONE',
        'CLT_EMAIL',
        'CLT_VILLE',
        'CLT_CODE_POSTAL',
        'CLT_REMISE',
        'CLT_CREDIT_LIMIT',
        'CLT_SOLDE',
        'CLT_ETAT',
        'CLT_DATE_CREATION',
        'CLT_ICE',
        'CLT_RC'
    ];

    protected $casts = [
        'CLT_REMISE' => 'decimal:2',
        'CLT_CREDIT_LIMIT' => 'decimal:2',
        'CLT_SOLDE' => 'decimal:2',
        'CLT_ETAT' => 'integer',
        'CLT_DATE_CREATION' => 'datetime'
    ];

    /**
     * Relation avec les factures
     */
    public function factures()
    {
        return $this->hasMany(Facture::class, 'FCTV_CLIENT', 'CLT_ID');
    }

    /**
     * Relation avec les commandes
     */
    public function commandes()
    {
        return $this->hasMany(Commande::class, 'DVS_CLIENT', 'CLT_ID');
    }

    /**
     * Scope pour les clients actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('CLT_ETAT', 1);
    }

    /**
     * Scope pour rechercher par nom ou référence
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('CLT_NOM', 'like', "%{$search}%")
              ->orWhere('CLT_PRENOM', 'like', "%{$search}%")
              ->orWhere('CLT_REF', 'like', "%{$search}%")
              ->orWhere('CLT_TELEPHONE', 'like', "%{$search}%");
        });
    }

    /**
     * Accessor pour le nom complet
     */
    public function getNomCompletAttribute()
    {
        return trim($this->CLT_NOM . ' ' . $this->CLT_PRENOM);
    }

    /**
     * Accessor pour l'adresse complète
     */
    public function getAdresseCompleteAttribute()
    {
        $adresse = $this->CLT_ADRESSE;
        if ($this->CLT_VILLE) {
            $adresse .= ', ' . $this->CLT_VILLE;
        }
        if ($this->CLT_CODE_POSTAL) {
            $adresse .= ' ' . $this->CLT_CODE_POSTAL;
        }
        return $adresse;
    }

    /**
     * Accessor pour le statut du client
     */
    public function getStatutAttribute()
    {
        return $this->CLT_ETAT == 1 ? 'Actif' : 'Inactif';
    }

    /**
     * Accessor pour la classe CSS du statut
     */
    public function getStatutClassAttribute()
    {
        return $this->CLT_ETAT == 1 ? 'badge-success' : 'badge-danger';
    }

    /**
     * Calculer le chiffre d'affaires du client
     */
    public function getChiffreAffairesAttribute()
    {
        return $this->factures()->validees()->sum('FCTV_MNT_TTC');
    }

    /**
     * Calculer le nombre de factures du client
     */
    public function getNombreFacturesAttribute()
    {
        return $this->factures()->validees()->count();
    }

    /**
     * Calculer le panier moyen du client
     */
    public function getPanierMoyenAttribute()
    {
        $nbFactures = $this->nombre_factures;
        return $nbFactures > 0 ? $this->chiffre_affaires / $nbFactures : 0;
    }

    /**
     * Vérifier si le client a du crédit disponible
     */
    public function hasCreditDisponible($montant = 0)
    {
        if ($this->CLT_CREDIT_LIMIT <= 0) {
            return true; // Pas de limite de crédit
        }
        
        return ($this->CLT_SOLDE + $montant) <= $this->CLT_CREDIT_LIMIT;
    }

    /**
     * Calculer le crédit restant
     */
    public function getCreditRestantAttribute()
    {
        if ($this->CLT_CREDIT_LIMIT <= 0) {
            return null; // Pas de limite
        }
        
        return $this->CLT_CREDIT_LIMIT - $this->CLT_SOLDE;
    }

    /**
     * Obtenir la dernière facture du client
     */
    public function getDerniereFactureAttribute()
    {
        return $this->factures()->orderBy('FCTV_DATE', 'desc')->first();
    }

    /**
     * Générer une nouvelle référence client
     */
    public static function generateRef()
    {
        $year = date('Y');
        $lastClient = self::where('CLT_REF', 'like', "CLT-{$year}-%")
                         ->orderBy('CLT_REF', 'desc')
                         ->first();
        
        if ($lastClient) {
            $parts = explode('-', $lastClient->CLT_REF);
            $sequence = intval(end($parts)) + 1;
        } else {
            $sequence = 1;
        }
        
        return sprintf('CLT-%s-%04d', $year, $sequence);
    }

    /**
     * Obtenir les statistiques des clients
     */
    public static function getStatistiques()
    {
        return [
            'total_clients' => self::count(),
            'clients_actifs' => self::actifs()->count(),
            'clients_avec_credit' => self::where('CLT_SOLDE', '>', 0)->count(),
            'credit_total' => self::sum('CLT_SOLDE'),
            'ca_moyen_par_client' => self::actifs()->get()->avg('chiffre_affaires')
        ];
    }

    /**
     * Obtenir les meilleurs clients par CA
     */
    public static function getTopClients($limit = 10)
    {
        return self::actifs()
                  ->withSum(['factures' => function($query) {
                      $query->validees();
                  }], 'FCTV_MNT_TTC')
                  ->orderBy('factures_sum_fctv_mnt_ttc', 'desc')
                  ->limit($limit)
                  ->get();
    }

    /**
     * Rechercher des clients pour l'autocomplétion
     */
    public static function searchForAutocomplete($query, $limit = 10)
    {
        return self::actifs()
                  ->search($query)
                  ->select('CLT_ID', 'CLT_REF', 'CLT_NOM', 'CLT_PRENOM', 'CLT_TELEPHONE', 'CLT_ADRESSE')
                  ->limit($limit)
                  ->get()
                  ->map(function($client) {
                      return [
                          'id' => $client->CLT_ID,
                          'ref' => $client->CLT_REF,
                          'nom' => $client->nom_complet,
                          'telephone' => $client->CLT_TELEPHONE,
                          'adresse' => $client->CLT_ADRESSE,
                          'text' => $client->nom_complet . ' - ' . $client->CLT_TELEPHONE
                      ];
                  });
    }

    /**
     * Mettre à jour le solde client
     */
    public function updateSolde($montant, $operation = 'add')
    {
        if ($operation === 'add') {
            $this->CLT_SOLDE += $montant;
        } else {
            $this->CLT_SOLDE -= $montant;
        }
        
        $this->save();
    }

    /**
     * Calculer l'ancienneté du client
     */
    public function getAncienneteAttribute()
    {
        if (!$this->CLT_DATE_CREATION) {
            return null;
        }
        
        return $this->CLT_DATE_CREATION->diffForHumans();
    }

    /**
     * Vérifier si le client est nouveau (moins de 30 jours)
     */
    public function getIsNouveauClientAttribute()
    {
        if (!$this->CLT_DATE_CREATION) {
            return false;
        }
        
        return $this->CLT_DATE_CREATION->diffInDays() <= 30;
    }

    /**
     * Obtenir l'historique des achats du client
     */
    public function getHistoriqueAchats($limit = 10)
    {
        return $this->factures()
                   ->validees()
                   ->with('details.article')
                   ->orderBy('FCTV_DATE', 'desc')
                   ->limit($limit)
                   ->get();
    }

    /**
     * Calculer la fréquence d'achat (jours entre achats)
     */
    public function getFrequenceAchatAttribute()
    {
        $factures = $this->factures()->validees()->orderBy('FCTV_DATE')->get();
        
        if ($factures->count() < 2) {
            return null;
        }
        
        $totalJours = 0;
        for ($i = 1; $i < $factures->count(); $i++) {
            $totalJours += $factures[$i]->FCTV_DATE->diffInDays($factures[$i-1]->FCTV_DATE);
        }
        
        return round($totalJours / ($factures->count() - 1));
    }
}
