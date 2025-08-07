<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #{{ $facture['facture']->FCTV_NUMERO }} - {{ $facture['facture']->CLIENT_NAME }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: white;
            padding: 20px;
        }
        
        .facture-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border: 2px solid #000;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #000;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .company-details {
            font-size: 11px;
            line-height: 1.3;
        }
        
        .facture-info {
            flex: 1;
            text-align: right;
        }
        
        .facture-title {
            font-size: 28px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
        }
        
        .facture-number {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .facture-date {
            font-size: 12px;
            margin-bottom: 10px;
        }
        
        .client-section {
            margin: 30px 0;
            display: flex;
            justify-content: space-between;
        }
        
        .client-info {
            flex: 1;
            padding: 15px;
            border: 1px solid #000;
            background: #f9f9f9;
        }
        
        .client-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            text-decoration: underline;
        }
        
        .payment-info {
            flex: 1;
            margin-left: 20px;
            padding: 15px;
            border: 1px solid #000;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }
        
        .items-table th {
            background: #000;
            color: white;
            padding: 10px 5px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #000;
        }
        
        .items-table td {
            padding: 8px 5px;
            border: 1px solid #000;
            text-align: center;
        }
        
        .items-table td:first-child {
            text-align: left;
        }
        
        .items-table td:last-child {
            text-align: right;
            font-weight: bold;
        }
        
        .item-description {
            font-weight: bold;
        }
        
        .item-ref {
            font-size: 9px;
            color: #666;
            font-style: italic;
        }
        
        .totals-section {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        
        .totals-table td {
            padding: 5px 10px;
            border: 1px solid #000;
        }
        
        .totals-table .total-label {
            text-align: right;
            font-weight: bold;
            background: #f0f0f0;
        }
        
        .totals-table .total-value {
            text-align: right;
            font-weight: bold;
        }
        
        .total-final {
            background: #000 !important;
            color: white !important;
            font-size: 14px !important;
            font-weight: bold !important;
        }
        
        .footer {
            clear: both;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #000;
            font-size: 10px;
            text-align: center;
        }
        
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 200px;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        
        .status-stamp {
            position: absolute;
            top: 100px;
            right: 50px;
            transform: rotate(-15deg);
            border: 3px solid;
            padding: 10px 20px;
            font-size: 20px;
            font-weight: bold;
            border-radius: 10px;
        }
        
        .status-valide {
            color: #1cc88a;
            border-color: #1cc88a;
        }
        
        .status-brouillon {
            color: #f6c23e;
            border-color: #f6c23e;
        }
        
        .status-annule {
            color: #e74a3b;
            border-color: #e74a3b;
        }
        
        .exonere-stamp {
            background: #f6c23e;
            color: white;
            padding: 5px 15px;
            font-weight: bold;
            border-radius: 5px;
            display: inline-block;
            margin: 10px 0;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .facture-container {
                border: none;
                padding: 0;
                max-width: none;
            }
            
            .no-print {
                display: none !important;
            }
            
            @page {
                margin: 1cm;
                size: A4;
            }
        }
        
        .print-controls {
            text-align: center;
            margin: 20px 0;
            background: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
        }
        
        .btn {
            padding: 10px 20px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .remarques-section {
            margin: 20px 0;
            padding: 10px;
            background: #f9f9f9;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="print-controls no-print">
        <button class="btn btn-primary" onclick="window.print()">
            🖨️ Imprimer la Facture
        </button>
        <button class="btn btn-secondary" onclick="window.close()">
            ❌ Fermer
        </button>
    </div>

    <div class="facture-container">
        <!-- Statut de la facture -->
        @if($facture['facture']->FCTV_ETAT == 0)
            <div class="status-stamp status-annule">ANNULÉE</div>
        @elseif($facture['facture']->FCTV_VALIDE == 0)
            <div class="status-stamp status-brouillon">BROUILLON</div>
        @else
            <div class="status-stamp status-valide">VALIDÉE</div>
        @endif

        <!-- En-tête -->
        <div class="header">
            <div class="company-info">
                <div class="company-name">
                    {{ $facture['facture']->Enteteticket1 ?? 'RESTAURANT' }}
                </div>
                <div class="company-details">
                    @if($facture['facture']->Enteteticket2)
                        <div>{{ $facture['facture']->Enteteticket2 }}</div>
                    @endif
                    @if($facture['facture']->Adresse)
                        <div>{{ $facture['facture']->Adresse }}</div>
                    @endif
                    @if($facture['facture']->Telephone)
                        <div>Tél: {{ $facture['facture']->Telephone }}</div>
                    @endif
                    @if($facture['facture']->RC)
                        <div>RC: {{ $facture['facture']->RC }}</div>
                    @endif
                    @if($facture['facture']->ICE)
                        <div>ICE: {{ $facture['facture']->ICE }}</div>
                    @endif
                    @if($facture['facture']->IFE)
                        <div>IFE: {{ $facture['facture']->IFE }}</div>
                    @endif
                </div>
            </div>
            <div class="facture-info">
                <div class="facture-title">FACTURE</div>
                <div class="facture-number">N° {{ $facture['facture']->FCTV_NUMERO }}</div>
                <div class="facture-date">
                    Date: {{ \Carbon\Carbon::parse($facture['facture']->FCTV_DATE)->format('d/m/Y') }}
                </div>
                <div class="facture-date">
                    Heure: {{ \Carbon\Carbon::parse($facture['facture']->FCTV_DATE)->format('H:i:s') }}
                </div>
                <div style="margin-top: 10px; font-size: 10px;">
                    Réf: {{ $facture['facture']->FCTV_REF }}
                </div>
            </div>
        </div>

        <!-- Informations client et paiement -->
        <div class="client-section">
            <div class="client-info">
                <div class="client-title">FACTURÉ À:</div>
                <div><strong>{{ $facture['facture']->CLIENT_NAME }}</strong></div>
                @if(!empty($facture['facture']->CLT_RAISONSOCIAL))
                    <div>{{ $facture['facture']->CLT_RAISONSOCIAL }}</div>
                @endif
                @if(!empty($facture['facture']->CLT_TELEPHONE))
                    <div>Tél: {{ $facture['facture']->CLT_TELEPHONE }}</div>
                @endif
                @if(!empty($facture['facture']->CLT_EMAIL))
                    <div>Email: {{ $facture['facture']->CLT_EMAIL }}</div>
                @endif
                @if($facture['facture']->CLT_REF && $facture['facture']->CLT_REF !== '0')
                    <div style="margin-top: 5px; font-size: 10px;">
                        Code client: {{ $facture['facture']->CLT_REF }}
                    </div>
                @endif
            </div>
            
            <div class="payment-info">
                <div class="client-title">INFORMATIONS:</div>
                <div><strong>Serveur:</strong> {{ $facture['facture']->FCTV_SERVEUR ?? 'N/A' }}</div>
                @if($facture['facture']->TAB_REF)
                    <div><strong>Table:</strong> {{ $facture['facture']->TAB_REF }}</div>
                @endif
                <div><strong>Caisse:</strong> N° {{ $facture['facture']->CSS_ID_CAISSE ?? 1 }}</div>
                <div><strong>Mode paiement:</strong> {{ $facture['facture']->FCTV_MODEPAIEMENT }}</div>
                
                @if($facture['facture']->FCTV_EXONORE)
                    <div class="exonere-stamp">EXONÉRÉE TVA</div>
                @endif
            </div>
        </div>

        <!-- Tableau des articles -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="40%">DÉSIGNATION</th>
                    <th width="10%">QTÉ</th>
                    <th width="15%">PRIX UNIT. HT</th>
                    <th width="10%">TVA</th>
                    <th width="15%">PRIX UNIT. TTC</th>
                    <th width="10%">TOTAL TTC</th>
                </tr>
            </thead>
            <tbody>
                @php $totalGeneral = 0; @endphp
                @foreach($facture['details'] as $detail)
                    @php $totalGeneral += $detail->TOTAL_LIGNE; @endphp
                    <tr>
                        <td>
                            <div class="item-description">{{ $detail->ART_DESIGNATION }}</div>
                            <div class="item-ref">Réf: {{ $detail->ART_REF }}</div>
                            @if($detail->IsMenu && $detail->NameMenu)
                                <div class="item-ref">Menu: {{ $detail->NameMenu }}</div>
                            @endif
                        </td>
                        <td>{{ $detail->FVD_QTE }}</td>
                        <td>{{ number_format($detail->FVD_PRIX_VNT_HT, 2) }}</td>
                        <td>{{ $detail->FVD_TVA }}%</td>
                        <td>{{ number_format($detail->FVD_PRIX_VNT_TTC, 2) }}</td>
                        <td>{{ number_format($detail->TOTAL_LIGNE, 2) }}</td>
                    </tr>
                    @if($detail->FVD_REMISE > 0)
                    <tr>
                        <td colspan="5" style="text-align: right; font-style: italic;">
                            Remise sur article:
                        </td>
                        <td style="color: #e74a3b;">-{{ number_format($detail->FVD_REMISE, 2) }}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <!-- Totaux -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="total-label">Total HT:</td>
                    <td class="total-value">{{ number_format($facture['facture']->FCTV_MNT_HT, 2) }} DH</td>
                </tr>
                @if(!$facture['facture']->FCTV_EXONORE)
                <tr>
                    <td class="total-label">TVA (20%):</td>
                    <td class="total-value">{{ number_format($facture['facture']->FCTV_MNT_TTC - $facture['facture']->FCTV_MNT_HT, 2) }} DH</td>
                </tr>
                @endif
                @if($facture['facture']->FCTV_REMISE > 0)
                <tr>
                    <td class="total-label">Remise globale:</td>
                    <td class="total-value" style="color: #e74a3b;">-{{ number_format($facture['facture']->FCTV_REMISE, 2) }} DH</td>
                </tr>
                @endif
                <tr class="total-final">
                    <td class="total-label total-final">TOTAL TTC:</td>
                    <td class="total-value total-final">{{ number_format($facture['facture']->FCTV_MNT_TTC, 2) }} DH</td>
                </tr>
            </table>
        </div>

        <!-- Détails du paiement -->
        @if($facture['facture']->FCTV_MODEPAIEMENT === 'Mixte' || $facture['facture']->MontantEspece > 0 || $facture['facture']->MontantCharte > 0 || $facture['facture']->MontantCredit > 0)
        <div style="clear: both; margin-top: 30px;">
            <table class="totals-table" style="width: 300px; float: right;">
                <tr style="background: #f0f0f0;">
                    <td colspan="2" style="text-align: center; font-weight: bold;">DÉTAIL DU PAIEMENT</td>
                </tr>
                @if($facture['facture']->MontantEspece > 0)
                <tr>
                    <td class="total-label">Espèces:</td>
                    <td class="total-value">{{ number_format($facture['facture']->MontantEspece, 2) }} DH</td>
                </tr>
                @endif
                @if($facture['facture']->MontantCharte > 0)
                <tr>
                    <td class="total-label">Carte bancaire:</td>
                    <td class="total-value">{{ number_format($facture['facture']->MontantCharte, 2) }} DH</td>
                </tr>
                @endif
                @if($facture['facture']->MontantCredit > 0)
                <tr>
                    <td class="total-label">Crédit:</td>
                    <td class="total-value">{{ number_format($facture['facture']->MontantCredit, 2) }} DH</td>
                </tr>
                @endif
                @if($facture['facture']->MontantCheque > 0)
                <tr>
                    <td class="total-label">Chèque:</td>
                    <td class="total-value">{{ number_format($facture['facture']->MontantCheque, 2) }} DH</td>
                </tr>
                @endif
                @if($facture['facture']->FCTV_RENDU > 0)
                <tr style="background: #fff3cd;">
                    <td class="total-label">Rendu:</td>
                    <td class="total-value">{{ number_format($facture['facture']->FCTV_RENDU, 2) }} DH</td>
                </tr>
                @endif
            </table>
        </div>
        @endif

        <!-- Remarques -->
        @if(!empty($facture['facture']->FCTV_REMARQUE))
        <div class="remarques-section" style="clear: both;">
            <strong>Remarques:</strong><br>
            {{ $facture['facture']->FCTV_REMARQUE }}
        </div>
        @endif

        <!-- Commandes liées -->
        @if(count($facture['commandes']) > 0)
        <div style="clear: both; margin-top: 20px; font-size: 10px;">
            <strong>Tickets associés:</strong>
            @foreach($facture['commandes'] as $commande)
                Ticket #{{ $commande->DVS_NUMERO }} ({{ \Carbon\Carbon::parse($commande->DVS_DATE)->format('d/m/Y H:i') }}){{ !$loop->last ? ', ' : '' }}
            @endforeach
        </div>
        @endif

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div>Signature du Client</div>
            </div>
            <div class="signature-box">
                <div>Cachet et Signature</div>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            @if($facture['facture']->PiedPage)
                <div>{{ $facture['facture']->PiedPage }}</div>
            @endif
            @if($facture['facture']->PiedPage2)
                <div>{{ $facture['facture']->PiedPage2 }}</div>
            @endif
            <div style="margin-top: 10px;">
                Facture générée le {{ now()->format('d/m/Y à H:i:s') }} par AccessPos Pro
            </div>
            <div style="margin-top: 5px; font-style: italic;">
                {{ count($facture['details']) }} article(s) - Facture {{ $facture['facture']->FCTV_VALIDE ? 'validée' : 'en brouillon' }}
            </div>
        </div>
    </div>

    <script>
        // Auto-print quand la page se charge
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };

        // Optionnel: fermer la fenêtre après impression
        window.onafterprint = function() {
            // Décommenter si vous voulez fermer automatiquement
            // setTimeout(function() {
            //     window.close();
            // }, 1000);
        };
    </script>
</body>
</html>
