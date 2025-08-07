<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket - {{ $ticketInfo['ticket']->DVS_NUMERO }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.2;
            color: #000;
            background: white;
            width: 80mm;
            margin: 0 auto;
            padding: 5px;
        }
        
        .ticket {
            width: 100%;
            max-width: 80mm;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
        }
        
        .restaurant-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .restaurant-info {
            font-size: 10px;
            margin-bottom: 1px;
        }
        
        .ticket-info {
            margin: 8px 0;
            font-size: 11px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        
        .separator {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        
        .items {
            margin: 8px 0;
        }
        
        .item {
            margin: 3px 0;
            font-size: 11px;
        }
        
        .item-name {
            font-weight: bold;
        }
        
        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            margin-left: 10px;
        }
        
        .total-section {
            margin: 8px 0;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
            font-size: 11px;
        }
        
        .total-final {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 3px 0;
            margin: 5px 0;
        }
        
        .footer {
            text-align: center;
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 8px;
            font-size: 10px;
        }
        
        .status {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            margin: 5px 0;
            padding: 2px;
            border: 1px solid #000;
        }
        
        @media print {
            body {
                width: 80mm;
            }
            
            .no-print {
                display: none;
            }
        }
        
        .print-controls {
            text-align: center;
            margin: 10px 0;
            background: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
        }
        
        .btn {
            padding: 8px 15px;
            margin: 0 5px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <div class="print-controls no-print">
        <button class="btn btn-primary" onclick="window.print()">
            🖨️ Imprimer
        </button>
        <button class="btn btn-secondary" onclick="window.close()">
            ❌ Fermer
        </button>
    </div>

    <div class="ticket">
        <!-- En-tête du restaurant -->
        <div class="header">
            <div class="restaurant-name">{{ $ticketInfo['ticket']->Enteteticket1 ?? 'RESTAURANT' }}</div>
            @if($ticketInfo['ticket']->Enteteticket2)
            <div class="restaurant-info">{{ $ticketInfo['ticket']->Enteteticket2 }}</div>
            @endif
            @if($ticketInfo['ticket']->Adresse)
            <div class="restaurant-info">{{ $ticketInfo['ticket']->Adresse }}</div>
            @endif
            @if($ticketInfo['ticket']->Telephone)
            <div class="restaurant-info">Tél: {{ $ticketInfo['ticket']->Telephone }}</div>
            @endif
            @if($ticketInfo['ticket']->RC)
            <div class="restaurant-info">RC: {{ $ticketInfo['ticket']->RC }}</div>
            @endif
            @if($ticketInfo['ticket']->ICE)
            <div class="restaurant-info">ICE: {{ $ticketInfo['ticket']->ICE }}</div>
            @endif
        </div>

        <!-- Informations du ticket -->
        <div class="ticket-info">
            <div class="info-row">
                <span>N° Ticket:</span>
                <span><strong>{{ $ticketInfo['ticket']->DVS_NUMERO }}</strong></span>
            </div>
            <div class="info-row">
                <span>Date:</span>
                <span>{{ \Carbon\Carbon::parse($ticketInfo['ticket']->DVS_DATE)->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span>Type:</span>
                <span>{{ $ticketInfo['ticket']->TYPE_SERVICE }}</span>
            </div>
            @if($ticketInfo['ticket']->TAB_LIB)
            <div class="info-row">
                <span>Table:</span>
                <span>{{ $ticketInfo['ticket']->TAB_LIB }}</span>
            </div>
            @endif
            @if($ticketInfo['ticket']->DVS_SERVEUR)
            <div class="info-row">
                <span>Serveur:</span>
                <span>{{ $ticketInfo['ticket']->DVS_SERVEUR }}</span>
            </div>
            @endif
            @if($ticketInfo['ticket']->DVS_NBR_COUVERT > 1)
            <div class="info-row">
                <span>Couverts:</span>
                <span>{{ $ticketInfo['ticket']->DVS_NBR_COUVERT }}</span>
            </div>
            @endif
            <div class="info-row">
                <span>Client:</span>
                <span>{{ $ticketInfo['ticket']->CLIENT_NAME }}</span>
            </div>
        </div>

        <div class="separator"></div>

        <!-- Articles -->
        <div class="items">
            @foreach($ticketInfo['details'] as $detail)
            <div class="item">
                <div class="item-name">{{ $detail->ART_DESIGNATION }}</div>
                <div class="item-details">
                    <span>{{ $detail->CVD_QTE }} x {{ number_format($detail->CVD_PRIX_TTC, 2) }}</span>
                    <span>{{ number_format($detail->TOTAL_LIGNE, 2) }} DH</span>
                </div>
                @if($detail->CVD_REMISE > 0)
                <div class="item-details" style="color: #666;">
                    <span>Remise article:</span>
                    <span>-{{ number_format($detail->CVD_REMISE, 2) }} DH</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Totaux -->
        <div class="total-section">
            <div class="total-row">
                <span>Sous-total HT:</span>
                <span>{{ number_format($ticketInfo['ticket']->DVS_MONTANT_HT, 2) }} DH</span>
            </div>
            @if($ticketInfo['ticket']->DVS_REMISE > 0)
            <div class="total-row" style="color: #666;">
                <span>Remise globale:</span>
                <span>-{{ number_format($ticketInfo['ticket']->DVS_REMISE, 2) }} DH</span>
            </div>
            @endif
            <div class="total-row total-final">
                <span>TOTAL TTC:</span>
                <span>{{ number_format($ticketInfo['ticket']->DVS_MONTANT_TTC, 2) }} DH</span>
            </div>
        </div>

        <!-- Statut -->
        <div class="status">
            STATUT: {{ strtoupper($ticketInfo['ticket']->DVS_ETAT) }}
        </div>

        @if($ticketInfo['ticket']->DVS_RMARQUE)
        <div class="separator"></div>
        <div style="text-align: center; font-size: 10px; font-style: italic;">
            Remarque: {{ $ticketInfo['ticket']->DVS_RMARQUE }}
        </div>
        @endif

        @if($ticketInfo['ticket']->DVS_EXONORE)
        <div class="separator"></div>
        <div style="text-align: center; font-weight: bold; font-size: 11px;">
            *** TICKET EXONÉRÉ ***
        </div>
        @endif

        <!-- Pied de page -->
        <div class="footer">
            @if($ticketInfo['ticket']->PiedPage)
            <div>{{ $ticketInfo['ticket']->PiedPage }}</div>
            @endif
            @if($ticketInfo['ticket']->PiedPage2)
            <div>{{ $ticketInfo['ticket']->PiedPage2 }}</div>
            @endif
            <div style="margin-top: 5px; font-size: 9px;">
                Imprimé le {{ now()->format('d/m/Y à H:i:s') }}
            </div>
            <div style="margin-top: 3px; font-size: 9px;">
                Réf: {{ $ticketInfo['ticket']->CMD_REF }}
            </div>
        </div>

        <!-- QR Code ou code-barres (optionnel) -->
        <div style="text-align: center; margin-top: 10px; font-size: 9px;">
            ═══════════════════════════════
            <br>
            AccessPos Pro - Système POS
            <br>
            ═══════════════════════════════
        </div>
    </div>

    <script>
        // Auto-print quand la page se charge
        window.onload = function() {
            // Attendre un petit délai pour que le contenu se charge complètement
            setTimeout(function() {
                window.print();
            }, 500);
        };

        // Fermer la fenêtre après impression
        window.onafterprint = function() {
            setTimeout(function() {
                window.close();
            }, 1000);
        };
    </script>
</body>
</html>
