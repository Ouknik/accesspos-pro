@extends('layouts.sb-admin')

@section('title', 'Rapport CA par Mode de Paiement - AccessPos Pro')

@section('page-heading')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-credit-card text-warning"></i>
            Rapport Chiffre d'Affaires par Mode de Paiement
        </h1>
        <p class="mb-0 text-muted">Distribution des modes de paiement - Période: {{ $dateDebut }} à {{ $dateFin }}</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.chiffre-affaires.index', request()->query()) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i>
            Retour aux Rapports
        </a>
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
            <i class="fas fa-print"></i>
            Imprimer
        </button>
    </div>
</div>
@endsection

@section('content')

{{-- Filtres de Période --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-warning">
            <i class="fas fa-filter"></i>
            Filtres de Période
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.chiffre-affaires.paiement') }}">
            <div class="row">
                <div class="col-md-4">
                    <label for="date_from" class="form-label">Date Début</label>
                    <input type="date" class="form-control" name="date_from" value="{{ $dateDebut }}" required>
                </div>
                <div class="col-md-4">
                    <label for="date_to" class="form-label">Date Fin</label>
                    <input type="date" class="form-control" name="date_to" value="{{ $dateFin }}" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-search"></i>
                        Afficher Rapport
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tableau Rapport Modes de Paiement --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-warning">
            <i class="fas fa-table mr-2"></i>
            Répartition CA par Mode de Paiement
        </h6>
        <div class="btn-group">
            <a href="{{ route('admin.chiffre-affaires.export', ['type' => 'pdf', 'report' => 'paiement'] + request()->query()) }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('admin.chiffre-affaires.export', ['type' => 'excel', 'report' => 'paiement'] + request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <button type="button" class="btn btn-info btn-sm" onclick="printTable('paiementsTable')">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>
    <div class="card-body">
        @if(count($paiements) > 0)
            <div class="table-responsive">
                <table class="table table-bordered" id="paiementsTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Mode de Paiement</th>
                            <th>Nb Factures</th>
                            <th>Chiffre d'Affaires (DH)</th>
                            <th>Moyenne Facture (DH)</th>
                            <th>Pourcentage</th>
                            <th>Répartition Visuelle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paiements as $paiement)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @switch($paiement->mode_paiement)
                                            @case('Espèces')
                                                <i class="fas fa-money-bill-wave text-success mr-2"></i>
                                                @break
                                            @case('Carte Bancaire')
                                                <i class="fas fa-credit-card text-primary mr-2"></i>
                                                @break
                                            @case('Chèque')
                                                <i class="fas fa-file-invoice text-info mr-2"></i>
                                                @break
                                            @case('Virement')
                                                <i class="fas fa-exchange-alt text-warning mr-2"></i>
                                                @break
                                            @default
                                                <i class="fas fa-money-check text-secondary mr-2"></i>
                                        @endswitch
                                        <strong>{{ $paiement->mode_paiement }}</strong>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info">{{ number_format($paiement->nombre_factures) }}</span>
                                </td>
                                <td class="text-success font-weight-bold">
                                    {{ number_format($paiement->chiffre_affaires, 2) }}
                                </td>
                                <td class="text-info">
                                    {{ number_format($paiement->moyenne_facture, 2) }}
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-warning">{{ number_format($paiement->pourcentage, 1) }}%</span>
                                </td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar 
                                            @if($paiement->mode_paiement == 'Espèces') bg-success 
                                            @elseif($paiement->mode_paiement == 'Carte Bancaire') bg-primary 
                                            @elseif($paiement->mode_paiement == 'Chèque') bg-info 
                                            @else bg-warning @endif" 
                                             role="progressbar" 
                                             style="width: {{ $paiement->pourcentage }}%">
                                            {{ number_format($paiement->pourcentage, 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- إحصائيات إضافية --}}
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card border-left-warning">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Mode de Paiement le Plus Utilisé
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $paiements[0]->mode_paiement ?? 'Non défini' }}
                            </div>
                            <div class="small text-muted">
                                {{ number_format($paiements[0]->pourcentage ?? 0, 1) }}% du CA total
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-primary">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                CA Total
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ number_format(collect($paiements)->sum('chiffre_affaires'), 2) }} DH
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-success">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Transactions
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ number_format(collect($paiements)->sum('nombre_factures')) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-info">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Types de Paiement Utilisés
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ count($paiements) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الرسم البياني الدائري --}}
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-warning">Répartition CA</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="paiementChart" width="100%" height="80"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-warning">Répartition Nombre de Transactions</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="transactionChart" width="100%" height="80"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <div class="text-center py-5">
                <i class="fas fa-credit-card fa-5x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">Aucune donnée pour la période sélectionnée</h5>
                <p class="text-muted">Veuillez changer la période ou vérifier l'existence de ventes</p>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#paiementsTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
        },
        "pageLength": 10,
        "order": [[ 2, "desc" ]],
        "columnDefs": [
            { "orderable": false, "targets": [5] }
        ]
    });
});

// Fonction d'impression pour les tableaux
// Fonction d'impression pour les tableaux - نسخة محسنة
function printTable(tableId) {
    console.log('printTable called with tableId:', tableId);
    
    // التحقق من وجود العنصر
    var tableElement = document.getElementById(tableId);
    if (!tableElement) {
        console.error('Table element not found:', tableId);
        alert('خطأ: الجدول غير موجود - ' + tableId);
        return false;
    }
    
    console.log('Table element found, proceeding with print...');
    
    try {
        // إنشاء نافذة الطباعة
        var printWindow = window.open('', 'PrintWindow', 'width=900,height=700,scrollbars=yes,resizable=yes');
        
        if (!printWindow) {
            alert('تعذر فتح نافذة الطباعة. يرجى السماح للنوافذ المنبثقة.');
            return false;
        }
        
        // الحصول على محتوى الجدول
        var tableHTML = tableElement.outerHTML;
        var currentDate = new Date().toLocaleDateString('ar-MA');
        var currentTime = new Date().toLocaleTimeString('ar-MA');
        
        // محتوى HTML للطباعة
        var printContent = `
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تقرير طرق الدفع - ${currentDate}</title>
    <style>
        @media screen {
            body { 
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                margin: 20px;
                direction: rtl;
                background-color: #f8f9fa;
            }
            .print-controls {
                text-align: center;
                margin: 20px 0;
                padding: 15px;
                background: white;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .btn {
                display: inline-block;
                padding: 10px 20px;
                margin: 0 10px;
                text-decoration: none;
                border-radius: 5px;
                font-weight: bold;
                cursor: pointer;
                border: none;
                font-size: 14px;
            }
            .btn-primary { background-color: #ffc107; color: #212529; }
            .btn-secondary { background-color: #6c757d; color: white; }
            .btn:hover { opacity: 0.8; }
        }
        
        @media print {
            .print-controls { display: none !important; }
            body { margin: 0; background: white; }
        }
        
        .report-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            border-bottom: 3px solid #ffc107;
        }
        .report-header h1 {
            color: #ffc107;
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .report-header p {
            margin: 5px 0;
            color: #666;
            font-size: 16px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        th, td {
            padding: 12px 8px;
            text-align: right;
            border: 1px solid #dee2e6;
            font-size: 14px;
        }
        
        th {
            background-color: #ffc107;
            color: #212529;
            font-weight: bold;
            text-align: center;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tbody tr:hover {
            background-color: #fff3cd;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <button class="btn btn-primary" onclick="window.print()">
            🖨️ طباعة
        </button>
        <button class="btn btn-secondary" onclick="window.close()">
            ❌ إغلاق
        </button>
    </div>
    
    <div class="report-header">
        <h1>AccessPos Pro - تقرير طرق الدفع</h1>
        <p><strong>تاريخ الطباعة:</strong> ${currentDate} - ${currentTime}</p>
        <p>تقرير مفصل لتوزيع المبيعات حسب طريقة الدفع</p>
    </div>
    
    ${tableHTML}
    
    <div class="footer">
        <p><strong>© AccessPos Pro</strong> - نظام إدارة المبيعات والمطاعم</p>
        <p>تم إنشاء هذا التقرير في ${currentDate} الساعة ${currentTime}</p>
    </div>
</body>
</html>`;

        // كتابة المحتوى في النافذة الجديدة
        printWindow.document.write(printContent);
        printWindow.document.close();
        
        // التركيز على النافذة الجديدة
        printWindow.focus();
        
        console.log('Print window created successfully');
        return true;
        
    } catch (error) {
        console.error('Error in printTable function:', error);
        alert('حدث خطأ أثناء تحضير الطباعة: ' + error.message);
        return false;
    }
}

@if(count($paiements) > 0)
// Graphique circulaire pour chiffre d'affaires
var ctx1 = document.getElementById('paiementChart').getContext('2d');
var paiementChart = new Chart(ctx1, {
    type: 'pie',
    data: {
        labels: {!! json_encode(collect($paiements)->pluck('mode_paiement')->toArray()) !!},
        datasets: [{
            data: {!! json_encode(collect($paiements)->pluck('chiffre_affaires')->toArray()) !!},
            backgroundColor: [
                'rgba(28, 200, 138, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(255, 99, 132, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)'
            ],
            borderColor: [
                'rgba(28, 200, 138, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    var total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                    var percentage = Math.round((data.datasets[0].data[tooltipItem.index] / total) * 100);
                    return data.labels[tooltipItem.index] + ': ' + 
                           data.datasets[0].data[tooltipItem.index].toLocaleString() + 
                           ' DH (' + percentage + '%)';
                }
            }
        }
    }
});

// Graphique circulaire nombre de transactions
var ctx2 = document.getElementById('transactionChart').getContext('2d');
var transactionChart = new Chart(ctx2, {
    type: 'pie',
    data: {
        labels: {!! json_encode(collect($paiements)->pluck('mode_paiement')->toArray()) !!},
        datasets: [{
            data: {!! json_encode(collect($paiements)->pluck('nombre_factures')->toArray()) !!},
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(28, 200, 138, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(28, 200, 138, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    var total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                    var percentage = Math.round((data.datasets[0].data[tooltipItem.index] / total) * 100);
                    return data.labels[tooltipItem.index] + ': ' + 
                           data.datasets[0].data[tooltipItem.index].toLocaleString() + 
                           ' transactions (' + percentage + '%)';
                }
            }
        }
    }
});
@endif
</script>
@endsection
