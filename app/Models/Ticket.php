<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Ticket extends Model
{
    protected $table = 'CMD_VENTE';
    protected $primaryKey = 'CMD_REF';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'CMD_REF',
        'DVS_NUMERO',
        'DVS_DATE',
        'DVS_MONTANT_HT',
        'DVS_MONTANT_TTC',
        'DVS_REMISE',
        'DVS_SERVEUR',
        'DVS_ETAT',
        'TAB_REF',
        'CSS_ID_CAISSE',
        'CLT_REF',
        'DVS_NBR_COUVERT',
        'DVS_RMARQUE',
        'DVS_VALIDE',
        'DVS_EXONORE',
        'TAB_LIB'
    ];

    protected $casts = [
        'DVS_DATE' => 'datetime',
        'DVS_MONTANT_HT' => 'decimal:2',
        'DVS_MONTANT_TTC' => 'decimal:2',
        'DVS_REMISE' => 'decimal:2',
        'DVS_NBR_COUVERT' => 'integer',
        'DVS_VALIDE' => 'boolean',
        'DVS_EXONORE' => 'boolean'
    ];

    /**
     * Relation avec les détails du ticket
     */
    public function details()
    {
        return $this->hasMany(TicketDetail::class, 'CMD_REF', 'CMD_REF');
    }

    /**
     * Relation avec le client
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'CLT_REF', 'CLT_REF');
    }

    /**
     * Relation avec les paramètres de caisse
     */
    public function caisse()
    {
        return $this->belongsTo(ParametreCaisse::class, 'CSS_ID_CAISSE', 'NumCaisse');
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeByStatus($query, $status)
    {
        if ($status !== 'all') {
            return $query->where('DVS_ETAT', $status);
        }
        return $query;
    }

    /**
     * Scope pour filtrer par serveur
     */
    public function scopeByServeur($query, $serveur)
    {
        if ($serveur !== 'all') {
            return $query->where('DVS_SERVEUR', $serveur);
        }
        return $query;
    }

    /**
     * Scope pour filtrer par date
     */
    public function scopeByDate($query, $dateFilter)
    {
        switch ($dateFilter) {
            case 'today':
                return $query->whereDate('DVS_DATE', Carbon::today());
            case 'week':
                return $query->where('DVS_DATE', '>=', Carbon::now()->subWeek());
            case 'month':
                return $query->where('DVS_DATE', '>=', Carbon::now()->subMonth());
            default:
                return $query;
        }
    }

    /**
     * Scope pour filtrer par type de service
     */
    public function scopeByType($query, $type)
    {
        if ($type === 'sur_place') {
            return $query->whereNotNull('TAB_REF')->where('TAB_REF', '!=', '');
        } elseif ($type === 'emporter') {
            return $query->where(function($q) {
                $q->whereNull('TAB_REF')->orWhere('TAB_REF', '');
            });
        }
        return $query;
    }

    /**
     * Accesseur pour le type de service
     */
    public function getTypeServiceAttribute()
    {
        return $this->TAB_REF ? 'Sur Place' : 'À Emporter';
    }

    /**
     * Accesseur pour le nom du client
     */
    public function getClientNameAttribute()
    {
        if ($this->CLT_REF && $this->CLT_REF !== '0') {
            return $this->client ? $this->client->CLT_CLIENT : 'Client #' . $this->CLT_REF;
        }
        return 'Client Anonyme';
    }

    /**
     * Accesseur pour formater la date
     */
    public function getFormattedDateAttribute()
    {
        return $this->DVS_DATE ? $this->DVS_DATE->format('d/m/Y H:i') : null;
    }

    /**
     * Méthode pour obtenir le badge de statut
     */
    public function getStatusBadgeAttribute()
    {
        $class = 'badge badge-pill ';
        switch (strtolower($this->DVS_ETAT)) {
            case 'en cours':
                $class .= 'status-en-cours';
                break;
            case 'terminé':
                $class .= 'status-termine';
                break;
            case 'en attente':
                $class .= 'status-en-attente';
                break;
            case 'annulé':
                $class .= 'status-annule';
                break;
            default:
                $class .= 'badge-secondary';
        }
        return $class;
    }

    /**
     * Méthode statique pour obtenir les statistiques des tickets
     */
    public static function getStats()
    {
        return self::selectRaw('
            COUNT(*) as total_tickets,
            SUM(CASE WHEN DVS_ETAT = "En cours" THEN 1 ELSE 0 END) as en_cours,
            SUM(CASE WHEN DVS_ETAT = "Terminé" THEN 1 ELSE 0 END) as termines,
            SUM(CASE WHEN DVS_ETAT = "En attente" THEN 1 ELSE 0 END) as en_attente,
            SUM(CASE WHEN DVS_ETAT = "Annulé" THEN 1 ELSE 0 END) as annules,
            SUM(CASE WHEN DATE(DVS_DATE) = CURDATE() THEN DVS_MONTANT_TTC ELSE 0 END) as ca_journalier,
            SUM(CASE WHEN DATE(DVS_DATE) = CURDATE() THEN DVS_REMISE ELSE 0 END) as remise_journaliere,
            AVG(DVS_MONTANT_TTC) as ticket_moyen,
            SUM(CASE WHEN TAB_REF IS NOT NULL THEN 1 ELSE 0 END) as sur_place,
            SUM(CASE WHEN TAB_REF IS NULL THEN 1 ELSE 0 END) as a_emporter,
            SUM(CASE WHEN DVS_EXONORE = 1 THEN 1 ELSE 0 END) as exoneres
        ')->first();
    }

    /**
     * Méthode pour obtenir la liste des serveurs
     */
    public static function getServeurs()
    {
        return self::whereNotNull('DVS_SERVEUR')
                   ->distinct()
                   ->pluck('DVS_SERVEUR')
                   ->sort()
                   ->values();
    }
}
