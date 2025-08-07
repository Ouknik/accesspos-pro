<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Facture extends Model
{
    protected $table = 'FACTURE_VNT';
    protected $primaryKey = 'FCTV_ID';
    public $timestamps = false;

    protected $fillable = [
        'FCTV_NUMERO',
        'FCTV_REF',
        'FCTV_DATE',
        'FCTV_CLIENT',
        'FCTV_MNT_HT',
        'FCTV_MNT_TTC',
        'FCTV_REMISE',
        'FCTV_EXONORE',
        'FCTV_MODEPAIEMENT',
        'FCTV_SERVEUR',
        'FCTV_VALIDE',
        'FCTV_ETAT',
        'FCTV_RMARQUE',
        'FCTV_RENDU',
        'TAB_REF',
        'CSS_ID_CAISSE',
        'MontantEspece',
        'MontantCharte',
        'MontantCheque',
        'MontantCredit'
    ];

    protected $casts = [
        'FCTV_DATE' => 'datetime',
        'FCTV_MNT_HT' => 'decimal:2',
        'FCTV_MNT_TTC' => 'decimal:2',
        'FCTV_REMISE' => 'decimal:2',
        'FCTV_RENDU' => 'decimal:2',
        'MontantEspece' => 'decimal:2',
        'MontantCharte' => 'decimal:2',
        'MontantCheque' => 'decimal:2',
        'MontantCredit' => 'decimal:2',
        'FCTV_EXONORE' => 'boolean',
        'FCTV_VALIDE' => 'boolean',
        'FCTV_ETAT' => 'integer'
    ];

    /**
     * Relation avec les détails de la facture
     */
    public function details()
    {
        return $this->hasMany(FactureDetail::class, 'FVD_FACTURE', 'FCTV_ID');
    }

    /**
     * Relation avec le client
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'FCTV_CLIENT', 'CLT_ID');
    }

    /**
     * Relation avec les commandes associées
     */
    public function commandes()
    {
        return $this->hasMany(CommandeFacture::class, 'CMD_FACTURE', 'FCTV_ID');
    }

    /**
     * Scope pour les factures validées
     */
    public function scopeValidees($query)
    {
        return $query->where('FCTV_VALIDE', 1)->where('FCTV_ETAT', 1);
    }

    /**
     * Scope pour les brouillons
     */
    public function scopeBrouillons($query)
    {
        return $query->where('FCTV_VALIDE', 0)->where('FCTV_ETAT', 1);
    }

    /**
     * Scope pour les factures annulées
     */
    public function scopeAnnulees($query)
    {
        return $query->where('FCTV_ETAT', 0);
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopePeriode($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('FCTV_DATE', [$dateDebut, $dateFin]);
    }

    /**
     * Scope pour filtrer par client
     */
    public function scopeClient($query, $clientId)
    {
        return $query->where('FCTV_CLIENT', $clientId);
    }

    /**
     * Scope pour filtrer par mode de paiement
     */
    public function scopeModePaiement($query, $mode)
    {
        return $query->where('FCTV_MODEPAIEMENT', $mode);
    }

    /**
     * Scope pour filtrer par serveur
     */
    public function scopeServeur($query, $serveur)
    {
        return $query->where('FCTV_SERVEUR', $serveur);
    }

    /**
     * Accessor pour le statut de la facture
     */
    public function getStatutAttribute()
    {
        if ($this->FCTV_ETAT == 0) {
            return 'Annulée';
        } elseif ($this->FCTV_VALIDE == 0) {
            return 'Brouillon';
        } else {
            return 'Validée';
        }
    }

    /**
     * Accessor pour la classe CSS du statut
     */
    public function getStatutClassAttribute()
    {
        if ($this->FCTV_ETAT == 0) {
            return 'badge-danger';
        } elseif ($this->FCTV_VALIDE == 0) {
            return 'badge-warning';
        } else {
            return 'badge-success';
        }
    }

    /**
     * Accessor pour le nom du client
     */
    public function getClientNameAttribute()
    {
        return $this->client ? $this->client->CLT_NOM : 'Client de Passage';
    }

    /**
     * Accessor pour le total des articles
     */
    public function getTotalArticlesAttribute()
    {
        return $this->details()->count();
    }

    /**
     * Accessor pour vérifier si c'est un paiement mixte
     */
    public function getIsPaiementMixteAttribute()
    {
        return $this->FCTV_MODEPAIEMENT === 'Mixte';
    }

    /**
     * Accessor pour le total des paiements (paiement mixte)
     */
    public function getTotalPaiementsAttribute()
    {
        if (!$this->is_paiement_mixte) {
            return $this->FCTV_MNT_TTC;
        }

        return ($this->MontantEspece ?? 0) + 
               ($this->MontantCharte ?? 0) + 
               ($this->MontantCheque ?? 0) + 
               ($this->MontantCredit ?? 0);
    }

    /**
     * Calculer le montant HT à partir du TTC
     */
    public function calculateMontantHT()
    {
        $totalHT = 0;
        foreach ($this->details as $detail) {
            $totalHT += ($detail->FVD_QTE * $detail->FVD_PRIX_VNT_HT) - $detail->FVD_REMISE;
        }
        return $totalHT - $this->FCTV_REMISE;
    }

    /**
     * Calculer le montant TTC
     */
    public function calculateMontantTTC()
    {
        $totalTTC = 0;
        foreach ($this->details as $detail) {
            $totalTTC += ($detail->FVD_QTE * $detail->FVD_PRIX_VNT_TTC) - $detail->FVD_REMISE;
        }
        return $totalTTC - $this->FCTV_REMISE;
    }

    /**
     * Calculer le montant de la TVA
     */
    public function getMontantTvaAttribute()
    {
        if ($this->FCTV_EXONORE) {
            return 0;
        }
        return $this->FCTV_MNT_TTC - $this->FCTV_MNT_HT;
    }

    /**
     * Valider la facture
     */
    public function valider()
    {
        $this->FCTV_VALIDE = 1;
        $this->save();
        
        // Mettre à jour le stock des articles
        foreach ($this->details as $detail) {
            $article = $detail->article;
            if ($article) {
                $article->ART_QTE_STOCK -= $detail->FVD_QTE;
                $article->save();
            }
        }
    }

    /**
     * Annuler la facture
     */
    public function annuler()
    {
        $wasValidated = $this->FCTV_VALIDE == 1;
        
        $this->FCTV_ETAT = 0;
        $this->save();
        
        // Si la facture était validée, restaurer le stock
        if ($wasValidated) {
            foreach ($this->details as $detail) {
                $article = $detail->article;
                if ($article) {
                    $article->ART_QTE_STOCK += $detail->FVD_QTE;
                    $article->save();
                }
            }
        }
    }

    /**
     * Générer un nouveau numéro de facture
     */
    public static function generateNumero()
    {
        $year = date('Y');
        $month = date('m');
        
        // Chercher le dernier numéro de facture pour ce mois
        $lastFacture = self::where('FCTV_NUMERO', 'like', "FCT-{$year}{$month}-%")
                          ->orderBy('FCTV_NUMERO', 'desc')
                          ->first();
        
        if ($lastFacture) {
            // Extraire le numéro séquentiel
            $parts = explode('-', $lastFacture->FCTV_NUMERO);
            $sequence = intval(end($parts)) + 1;
        } else {
            $sequence = 1;
        }
        
        return sprintf('FCT-%s%s-%04d', $year, $month, $sequence);
    }

    /**
     * Créer une facture depuis une commande
     */
    public static function createFromCommande($commandeId, $clientId = null)
    {
        // Cette méthode sera implémentée selon votre logique métier
        // pour convertir une commande en facture
    }

    /**
     * Exporter la facture en PDF
     */
    public function exportToPdf()
    {
        // Logique d'export PDF
        // Peut utiliser une librairie comme DomPDF ou wkhtmltopdf
    }

    /**
     * Envoyer la facture par email
     */
    public function sendByEmail($email, $message = null)
    {
        // Logique d'envoi par email
        // Utiliser la classe Mail de Laravel
    }

    /**
     * Obtenir les statistiques des factures
     */
    public static function getStatistiques($dateDebut = null, $dateFin = null)
    {
        $query = self::query();
        
        if ($dateDebut && $dateFin) {
            $query->periode($dateDebut, $dateFin);
        }
        
        return [
            'total_factures' => $query->count(),
            'factures_validees' => $query->validees()->count(),
            'factures_brouillons' => $query->brouillons()->count(),
            'factures_annulees' => $query->annulees()->count(),
            'chiffre_affaire' => $query->validees()->sum('FCTV_MNT_TTC'),
            'montant_ht' => $query->validees()->sum('FCTV_MNT_HT'),
            'montant_tva' => $query->validees()->sum('FCTV_MNT_TTC') - $query->validees()->sum('FCTV_MNT_HT'),
            'remises_accordees' => $query->validees()->sum('FCTV_REMISE')
        ];
    }

    /**
     * Obtenir les top clients
     */
    public static function getTopClients($limit = 10, $dateDebut = null, $dateFin = null)
    {
        $query = self::select('FCTV_CLIENT')
                    ->selectRaw('COUNT(*) as nb_factures')
                    ->selectRaw('SUM(FCTV_MNT_TTC) as ca_total')
                    ->validees()
                    ->with('client')
                    ->groupBy('FCTV_CLIENT')
                    ->orderBy('ca_total', 'desc')
                    ->limit($limit);
        
        if ($dateDebut && $dateFin) {
            $query->periode($dateDebut, $dateFin);
        }
        
        return $query->get();
    }
}
