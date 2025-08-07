<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketDetail extends Model
{
    protected $table = 'CMD_VENTE_DETAIL';
    protected $primaryKey = 'CMD_ID';
    public $timestamps = false;

    protected $fillable = [
        'CMD_REF',
        'ART_REF',
        'CVD_QTE',
        'CVD_PRIX_TTC',
        'CVD_REMISE'
    ];

    protected $casts = [
        'CVD_QTE' => 'decimal:2',
        'CVD_PRIX_TTC' => 'decimal:2',
        'CVD_REMISE' => 'decimal:2'
    ];

    /**
     * Relation avec le ticket principal
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'CMD_REF', 'CMD_REF');
    }

    /**
     * Relation avec l'article
     */
    public function article()
    {
        return $this->belongsTo(Article::class, 'ART_REF', 'ART_REF');
    }

    /**
     * Accesseur pour le nom de l'article
     */
    public function getArtDesignationAttribute()
    {
        return $this->article ? $this->article->ART_DESIGNATION : 'Article ' . $this->ART_REF;
    }

    /**
     * Accesseur pour le total de la ligne
     */
    public function getTotalLigneAttribute()
    {
        return $this->CVD_QTE * $this->CVD_PRIX_TTC;
    }
}
