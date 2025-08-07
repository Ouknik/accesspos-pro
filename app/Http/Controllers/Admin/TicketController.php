<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class TicketController extends Controller
{
    /**
     * Afficher la liste des tickets avec filtres
     */
    public function index(Request $request)
    {
        try {
            $filters = [
                'type' => $request->get('type', 'all'),
                'status' => $request->get('status', 'all'),
                'serveur' => $request->get('serveur', 'all'),
                'date' => $request->get('date', 'today')
            ];

            $tickets = $this->getTickets($filters);
            $serveurs = $this->getServeurs();
            $stats = $this->getTicketStats();

            return view('admin.tickets.index', compact('tickets', 'serveurs', 'stats', 'filters'));

        } catch (Exception $e) {
            return back()->with('error', 'Erreur lors du chargement des tickets: ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'un ticket en AJAX
     */
    public function details(Request $request)
    {
        try {
            $cmdRef = $request->get('cmd_ref');
            
            if (empty($cmdRef)) {
                return response()->json(['error' => 'Référence de ticket manquante'], 400);
            }

            $ticketInfo = $this->getFullTicketInfo($cmdRef);
            
            if ($ticketInfo) {
                return view('admin.tickets.details', compact('ticketInfo'))->render();
            } else {
                return response()->json(['error' => 'Ticket introuvable'], 404);
            }

        } catch (Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des détails'], 500);
        }
    }

    /**
     * Imprimer un ticket au format thermique
     */
    public function print(Request $request)
    {
        try {
            $cmdRef = $request->get('cmd_ref');
            
            if (empty($cmdRef)) {
                abort(400, 'Référence de ticket manquante');
            }

            $ticketInfo = $this->getFullTicketInfo($cmdRef);
            
            if ($ticketInfo) {
                return view('admin.tickets.print', compact('ticketInfo'));
            } else {
                return view('admin.tickets.print-error', compact('cmdRef'));
            }

        } catch (Exception $e) {
            return view('admin.tickets.print-error', ['cmdRef' => $cmdRef ?? 'inconnu']);
        }
    }

    /**
     * Mettre à jour le statut d'un ticket
     */
    public function updateStatus(Request $request)
    {
        try {
            $request->validate([
                'cmd_ref' => 'required',
                'new_status' => 'required|string'
            ]);

            DB::table('CMD_VENTE')
                ->where('CMD_REF', $request->cmd_ref)
                ->update(['DVS_ETAT' => $request->new_status]);

            return redirect()->route('admin.tickets.index')
                           ->with('success', 'Statut du ticket mis à jour avec succès!');

        } catch (Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer (annuler) un ticket
     */
    public function delete(Request $request)
    {
        try {
            $request->validate([
                'cmd_ref' => 'required'
            ]);

            DB::table('CMD_VENTE')
                ->where('CMD_REF', $request->cmd_ref)
                ->update(['DVS_ETAT' => 'Annulé']);

            return redirect()->route('admin.tickets.index')
                           ->with('success', 'Ticket annulé avec succès!');

        } catch (Exception $e) {
            return back()->with('error', 'Erreur lors de l\'annulation: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer la liste des tickets avec filtres
     */
    private function getTickets($filters = [])
    {
        $query = DB::table('CMD_VENTE as cv')
            ->leftJoin('CMD_VENTE_DETAIL as cvd', 'cv.CMD_REF', '=', 'cvd.CMD_REF')
            ->select([
                'cv.CMD_REF',
                'cv.DVS_NUMERO',
                'cv.DVS_DATE',
                'cv.DVS_MONTANT_HT',
                'cv.DVS_MONTANT_TTC',
                'cv.DVS_REMISE',
                'cv.DVS_SERVEUR',
                'cv.DVS_ETAT',
                'cv.TAB_REF',
                'cv.CSS_ID_CAISSE',
                'cv.CLT_REF',
                'cv.DVS_NBR_COUVERT',
                'cv.DVS_RMARQUE',
                'cv.DVS_VALIDE',
                'cv.DVS_EXONORE',
                'cv.TAB_LIB',
                DB::raw('COUNT(cvd.ART_REF) as NB_ARTICLES'),
                DB::raw('SUM(COALESCE(cvd.CVD_REMISE, 0)) as TOTAL_REMISE_ARTICLES')
            ]);

        // Application des filtres
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->where('cv.DVS_ETAT', $filters['status']);
        }

        if (isset($filters['serveur']) && $filters['serveur'] !== 'all') {
            $query->where('cv.DVS_SERVEUR', $filters['serveur']);
        }

        if (isset($filters['date'])) {
            switch ($filters['date']) {
                case 'today':
                    $query->whereDate('cv.DVS_DATE', Carbon::today());
                    break;
                case 'week':
                    $query->where('cv.DVS_DATE', '>=', Carbon::now()->subWeek());
                    break;
                case 'month':
                    $query->where('cv.DVS_DATE', '>=', Carbon::now()->subMonth());
                    break;
            }
        }

        if (isset($filters['type'])) {
            if ($filters['type'] === 'sur_place') {
                $query->whereNotNull('cv.TAB_REF')->where('cv.TAB_REF', '!=', '');
            } elseif ($filters['type'] === 'emporter') {
                $query->where(function($q) {
                    $q->whereNull('cv.TAB_REF')->orWhere('cv.TAB_REF', '');
                });
            }
        }

        return $query->groupBy([
                'cv.CMD_REF', 'cv.DVS_NUMERO', 'cv.DVS_DATE', 'cv.DVS_MONTANT_HT', 
                'cv.DVS_MONTANT_TTC', 'cv.DVS_REMISE', 'cv.DVS_SERVEUR', 'cv.DVS_ETAT', 
                'cv.TAB_REF', 'cv.CSS_ID_CAISSE', 'cv.CLT_REF', 'cv.DVS_NBR_COUVERT', 
                'cv.DVS_RMARQUE', 'cv.DVS_VALIDE', 'cv.DVS_EXONORE', 'cv.TAB_LIB'
            ])
            ->orderBy('cv.DVS_DATE', 'desc')
            ->get();
    }

    /**
     * Récupérer les informations complètes d'un ticket
     */
    private function getFullTicketInfo($cmdRef)
    {
        // Informations du ticket principal
        $ticket = DB::table('CMD_VENTE as cv')
            ->leftJoin('PARAMETRECAISSE as pc', 'cv.CSS_ID_CAISSE', '=', 'pc.NumCaisse')
            ->leftJoin('CLIENT as cl', 'cv.CLT_REF', '=', 'cl.CLT_REF')
            ->select([
                'cv.*',
                'pc.Enteteticket1', 'pc.Enteteticket2', 'pc.Adresse', 'pc.Telephone',
                'pc.RC', 'pc.ICE', 'pc.PiedPage', 'pc.PiedPage2',
                DB::raw("CASE 
                    WHEN cv.TAB_REF IS NOT NULL AND cv.TAB_REF != '0' THEN 'Sur Place'
                    ELSE 'À Emporter'
                END as TYPE_SERVICE"),
                DB::raw("CASE 
                    WHEN cv.CLT_REF != '0' THEN COALESCE(cl.CLT_CLIENT, 'Client #' + cv.CLT_REF)
                    ELSE 'Client Anonyme'
                END as CLIENT_NAME")
            ])
            ->where('cv.CMD_REF', $cmdRef)
            ->first();

        if (!$ticket) {
            return null;
        }

        // Détails des articles
        $details = DB::table('CMD_VENTE_DETAIL as cvd')
            ->leftJoin('ARTICLE as a', 'cvd.ART_REF', '=', 'a.ART_REF')
            ->select([
                'cvd.*',
                DB::raw("COALESCE(a.ART_DESIGNATION, 'Article ' + cvd.ART_REF) as ART_DESIGNATION"),
                DB::raw('(cvd.CVD_QTE * cvd.CVD_PRIX_TTC) as TOTAL_LIGNE')
            ])
            ->where('cvd.CMD_REF', $cmdRef)
            ->orderBy('cvd.CMD_ID')
            ->get();

        // Valeurs par défaut pour les paramètres de caisse
        if (empty($ticket->Enteteticket1)) {
            $ticket->Enteteticket1 = 'RESTAURANT';
        }
        if (empty($ticket->PiedPage)) {
            $ticket->PiedPage = 'Merci de votre visite';
        }

        return [
            'ticket' => $ticket,
            'details' => $details
        ];
    }

    /**
     * Récupérer la liste des serveurs
     */
    private function getServeurs()
    {
        return DB::table('CMD_VENTE')
            ->whereNotNull('DVS_SERVEUR')
            ->distinct()
            ->pluck('DVS_SERVEUR')
            ->sort()
            ->values();
    }

    /**
     * Récupérer les statistiques des tickets
     */
    private function getTicketStats()
    {
        return DB::table('CMD_VENTE')
            ->select([
                DB::raw('COUNT(*) as total_tickets'),
                DB::raw("SUM(CASE WHEN DVS_ETAT = 'En cours' THEN 1 ELSE 0 END) as en_cours"),
                DB::raw("SUM(CASE WHEN DVS_ETAT = 'Terminé' THEN 1 ELSE 0 END) as termines"),
                DB::raw("SUM(CASE WHEN DVS_ETAT = 'En attente' THEN 1 ELSE 0 END) as en_attente"),
                DB::raw("SUM(CASE WHEN DVS_ETAT = 'Annulé' THEN 1 ELSE 0 END) as annules"),
                DB::raw("SUM(CASE WHEN CAST(DVS_DATE AS DATE) = CAST(GETDATE() AS DATE) THEN DVS_MONTANT_TTC ELSE 0 END) as ca_journalier"),
                DB::raw("SUM(CASE WHEN CAST(DVS_DATE AS DATE) = CAST(GETDATE() AS DATE) THEN DVS_REMISE ELSE 0 END) as remise_journaliere"),
                DB::raw('AVG(DVS_MONTANT_TTC) as ticket_moyen'),
                DB::raw('SUM(CASE WHEN TAB_REF IS NOT NULL THEN 1 ELSE 0 END) as sur_place'),
                DB::raw('SUM(CASE WHEN TAB_REF IS NULL THEN 1 ELSE 0 END) as a_emporter'),
                DB::raw('SUM(CASE WHEN DVS_EXONORE = 1 THEN 1 ELSE 0 END) as exoneres')
            ])
            ->first();
    }
}
