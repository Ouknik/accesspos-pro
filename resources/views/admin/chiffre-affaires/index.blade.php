@extends('layouts.sb-admin')

@section('title', 'Rapports Chiffre d\'Affaires - AccessPos Pro')

@section('page-heading')
<!-- Sélecteur de langue -->
@include('partials.language-selector')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line text-primary"></i>
            Rapports Chiffre d'Affaires
        </h1>
        <p class="mb-0 text-muted">Analyse complète des performances de ventes et revenus</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.chiffre-affaires.export', ['type' => 'pdf', 'report' => 'dashboard'] + request()->query()) }}" class="btn btn-danger btn-sm">
            <i class="fas fa-file-pdf"></i>
            Export PDF
        </a>
        <a href="{{ route('admin.chiffre-affaires.export', ['type' => 'excel', 'report' => 'dashboard'] + request()->query()) }}" class="btn btn-success btn-sm">
            <i class="fas fa-file-excel"></i>
            Export Excel
        </a>
        <a href="{{ route('admin.chiffre-affaires.export', ['type' => 'print', 'report' => 'dashboard'] + request()->query()) }}" class="btn btn-info btn-sm" target="_blank">
            <i class="fas fa-print"></i>
            Imprimer
        </a>
        <button type="button" class="btn btn-primary btn-sm" onclick="refreshData()">
            <i class="fas fa-sync"></i>
            Actualiser
        </button>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown" title="اختبار وظائف الطباعة">
                <i class="fas fa-print"></i>
                Test Print
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="javascript:void(0)" onclick="testPrintFunction()">
                    <i class="fas fa-print"></i> اختبار طباعة متقدمة
                </a>
                <a class="dropdown-item" href="javascript:void(0)" onclick="testSimplePrint()">
                    <i class="fas fa-print"></i> اختبار طباعة مبسطة
                </a>
                <a class="dropdown-item" href="javascript:void(0)" onclick="window.print()">
                    <i class="fas fa-print"></i> طباعة الصفحة مباشرة
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')

@if(isset($error))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i>
        {{ $error }}
    </div>
@endif

{{-- Filtres de Période --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter"></i>
            Filtres de Période
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.chiffre-affaires.index') }}" id="filterForm">
            <input type="hidden" name="lang" value="{{ request('lang', 'ar') }}">
            <div class="row">
                <div class="col-md-4">
                    <label for="date_from" class="form-label">Date Début</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateDebut }}" required>
                </div>
                <div class="col-md-4">
                    <label for="date_to" class="form-label">Date Fin</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateFin }}" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Afficher Rapports
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Indicateurs Principaux --}}
@if(isset($kpis) && $kpis)
<div class="row">
    {{-- Total Chiffre d'Affaires --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Chiffre d'Affaires Total
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($kpis->chiffre_affaires_total ?? 0, 2) }} DH
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Nombre de Factures --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Nombre de Factures
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($kpis->total_factures ?? 0) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-receipt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Moyenne Facture --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Moyenne Facture
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($kpis->moyenne_facture ?? 0, 2) }} DH
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calculator fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Nombre de Clients --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Nombre de Clients
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($kpis->nombre_clients_uniques ?? 0) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Graphiques --}}
<div class="row">
    {{-- Évolution Chiffre d'Affaires --}}
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-area mr-2"></i>
                    Évolution Chiffre d'Affaires Quotidien
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="chartEvolutionCA" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Répartition Modes de Paiement --}}
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-credit-card mr-2"></i>
                    Répartition Modes de Paiement
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="chartPaiements"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    @if(isset($repartitionPaiements) && isset($repartitionPaiements['labels']))
                        @foreach($repartitionPaiements['labels'] as $index => $label)
                            <span class="mr-2">
                                <i class="fas fa-circle" style="color: {{ ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'][$index % 5] }}"></i>
                                {{ $label }}
                            </span>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- التقارير السريعة --}}
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    Rapports Rapides
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.chiffre-affaires.serveur', request()->query()) }}" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-user-tie"></i>
                            Rapport Serveurs
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.chiffre-affaires.famille', request()->query()) }}" class="btn btn-outline-success btn-block">
                            <i class="fas fa-tags"></i>
                            Rapport Familles
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.chiffre-affaires.article', request()->query()) }}" class="btn btn-outline-info btn-block">
                            <i class="fas fa-box"></i>
                            Rapport Produits
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.chiffre-affaires.paiement', request()->query()) }}" class="btn btn-outline-warning btn-block">
                            <i class="fas fa-credit-card"></i>
                            Rapport Modes de Paiement
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.chiffre-affaires.client', request()->query()) }}" class="btn btn-outline-danger btn-block">
                            <i class="fas fa-user-friends"></i>
                            Rapport Clients
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.chiffre-affaires.caissier', request()->query()) }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-cash-register"></i>
                            Rapport Caissiers
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Meilleurs Produits et Familles --}}
<div class="row">
    {{-- Meilleurs Produits --}}
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-trophy mr-2"></i>
                    Top 10 Produits
                </h6>
            </div>
            <div class="card-body">
                @if(isset($topProduits) && count($topProduits) > 0)
                    @foreach($topProduits as $index => $produit)
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                <span class="badge badge-{{ $index < 3 ? 'success' : 'secondary' }} badge-pill">
                                    {{ $index + 1 }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="h6 mb-0">{{ $produit->ART_DESIGNATION }}</div>
                                <div class="small text-muted">
                                    Quantité: {{ number_format($produit->quantite_vendue) }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="h6 mb-0 text-success">
                                    {{ number_format($produit->chiffre_affaires, 2) }} DH
                                </div>
                            </div>
                        </div>
                        @if($index < 9 && $index < count($topProduits) - 1)
                            <hr class="my-2">
                        @endif
                    @endforeach
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-box fa-3x mb-3"></i>
                        <p>Aucune donnée pour la période sélectionnée</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Meilleures Familles --}}
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tags mr-2"></i>
                    Meilleures Familles
                </h6>
            </div>
            <div class="card-body">
                @if(isset($topCategories) && count($topCategories) > 0)
                    @foreach($topCategories as $index => $categorie)
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                <span class="badge badge-{{ $index < 3 ? 'primary' : 'secondary' }} badge-pill">
                                    {{ $index + 1 }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="h6 mb-0">{{ $categorie->famille }}</div>
                            </div>
                            <div class="text-right">
                                <div class="h6 mb-0 text-primary">
                                    {{ number_format($categorie->chiffre_affaires, 2) }} DH
                                </div>
                            </div>
                        </div>
                        @if($index < count($topCategories) - 1)
                            <hr class="my-2">
                        @endif
                    @endforeach
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-tags fa-3x mb-3"></i>
                        <p>Aucune donnée pour la période sélectionnée</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Tableau Détails des Ventes --}}
@if(isset($ventesDetails) && count($ventesDetails) > 0)
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table mr-2"></i>
            Dernières Ventes
        </h6>
        <div class="btn-group">
            <a href="{{ route('admin.chiffre-affaires.export', ['type' => 'pdf', 'report' => 'ventes-details'] + request()->query()) }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('admin.chiffre-affaires.export', ['type' => 'excel', 'report' => 'ventes-details'] + request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="ventesTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>N° Facture</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Montant</th>
                        <th>Mode de Paiement</th>
                        <th>Caissier</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventesDetails as $vente)
                        <tr>
                            <td>{{ $vente->numero_facture }}</td>
                            <td>{{ \Carbon\Carbon::parse($vente->date_vente)->format('d/m/Y H:i') }}</td>
                            <td>{{ $vente->client ?? 'Non défini' }}</td>
                            <td class="text-success font-weight-bold">{{ number_format($vente->montant, 2) }} DH</td>
                            <td>
                                <span class="badge badge-info">{{ $vente->mode_paiement }}</span>
                            </td>
                            <td>{{ $vente->caissier }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Configuration des polices et couleurs
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Graphique évolution chiffre d'affaires
var ctx1 = document.getElementById("chartEvolutionCA");
if (ctx1) {
    var chartEvolutionCA = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: {!! json_encode($evolutionCA['labels'] ?? []) !!},
            datasets: [{
                label: "Chiffre d'Affaires (DH)",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: {!! json_encode($evolutionCA['data'] ?? []) !!},
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 7
                    }
                }],
                yAxes: [{
                    ticks: {
                        padding: 10,
                        callback: function(value, index, values) {
                            return value.toLocaleString() + ' DH';
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        return 'Chiffre d\'Affaires: ' + tooltipItem.yLabel.toLocaleString() + ' DH';
                    }
                }
            }
        }
    });
}

// Graphique répartition modes de paiement
var ctx2 = document.getElementById("chartPaiements");
if (ctx2) {
    var chartPaiements = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($repartitionPaiements['labels'] ?? []) !!},
            datasets: [{
                data: {!! json_encode($repartitionPaiements['data'] ?? []) !!},
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#e02d1b'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var label = chart.labels[tooltipItem.index];
                        var value = chart.datasets[0].data[tooltipItem.index];
                        return label + ': ' + value.toLocaleString() + ' DH';
                    }
                }
            },
            legend: {
                display: false
            },
            cutoutPercentage: 80,
        },
    });
}

// DataTable
$(document).ready(function() {
    $('#ventesTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
        },
        "pageLength": 25,
        "order": [[ 1, "desc" ]],
        "columnDefs": [
            { "orderable": false, "targets": [] }
        ]
    });
});

// Fonctions utilitaires
function refreshData() {
    window.location.reload();
}

// دالة طباعة مبسطة وموثوقة
function printTable(tableId) {
    console.log('بدء عملية الطباعة للجدول:', tableId);
    
    // البحث عن الجدول
    var table = document.getElementById(tableId);
    if (!table) {
        console.error('الجدول غير موجود:', tableId);
        alert('خطأ: لم يتم العثور على الجدول');
        return;
    }
    
    // إنشاء محتوى الطباعة
    var printContent = `
    <!DOCTYPE html>
    <html dir="rtl">
    <head>
        <meta charset="UTF-8">
        <title>تقرير المبيعات</title>
        <style>
            * { box-sizing: border-box; }
            body { 
                font-family: Arial, sans-serif; 
                direction: rtl; 
                margin: 0; 
                padding: 20px; 
            }
            .header { 
                text-align: center; 
                margin-bottom: 30px; 
                border-bottom: 2px solid #333; 
                padding-bottom: 15px; 
            }
            .header h1 { 
                color: #333; 
                margin: 0; 
                font-size: 24px; 
            }
            .header p { 
                margin: 5px 0; 
                color: #666; 
            }
            table { 
                width: 100%; 
                border-collapse: collapse; 
                margin: 20px 0; 
            }
            th, td { 
                border: 1px solid #333; 
                padding: 8px; 
                text-align: right; 
                font-size: 12px;
            }
            th { 
                background-color: #f0f0f0; 
                font-weight: bold; 
                text-align: center;
            }
            .footer { 
                margin-top: 30px; 
                text-align: center; 
                font-size: 10px; 
                color: #666; 
            }
            @media print {
                body { margin: 0; }
                .no-print { display: none !important; }
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>AccessPos Pro - تقرير المبيعات</h1>
            <p>تاريخ الطباعة: ${new Date().toLocaleDateString('ar-SA')}</p>
        </div>
        ${table.outerHTML}
        <div class="footer">
            <p>© AccessPos Pro - نظام إدارة المبيعات</p>
        </div>
    </body>
    </html>`;
    
    // فتح نافذة طباعة جديدة
    var printWindow = window.open('', '_blank');
    if (!printWindow) {
        alert('يرجى السماح للنوافذ المنبثقة في المتصفح');
        return;
    }
    
    // كتابة المحتوى
    printWindow.document.write(printContent);
    printWindow.document.close();
    
    // انتظار تحميل المحتوى ثم الطباعة
    setTimeout(function() {
        printWindow.focus();
        printWindow.print();
        // إغلاق النافذة بعد الطباعة (اختياري)
        setTimeout(function() {
            printWindow.close();
        }, 1000);
    }, 500);
}

// دالة طباعة الصفحة الحالية
function printCurrentPage() {
    window.print();
}

// دالة اختبار الطباعة
function testPrintFunction() {
    // البحث عن أي جدول في الصفحة
    var tables = document.querySelectorAll('table[id]');
    if (tables.length > 0) {
        printTable(tables[0].id);
    } else {
        alert('لا توجد جداول للطباعة في هذه الصفحة');
    }
}

// دالة اختبار الطباعة المبسطة
function testSimplePrint() {
    var tables = document.querySelectorAll('table[id]');
    if (tables.length > 0) {
        simplePrint(tables[0].id);
    } else {
        alert('لا توجد جداول للطباعة في هذه الصفحة');
    }
}

// دالة طباعة بديلة مبسطة جداً
function simplePrint(tableId) {
    var table = document.getElementById(tableId);
    if (!table) {
        alert('الجدول غير موجود');
        return;
    }
    
    var originalContents = document.body.innerHTML;
    var printContents = `
        <div style="direction: rtl; font-family: Arial;">
            <h2 style="text-align: center;">تقرير AccessPos Pro</h2>
            <p style="text-align: center;">تاريخ: ${new Date().toLocaleDateString('ar-SA')}</p>
            <hr>
            ${table.outerHTML}
            <hr>
            <p style="text-align: center; font-size: 12px;">© AccessPos Pro</p>
        </div>
    `;
    
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload(); // إعادة تحميل الصفحة لاستعادة الحالة الأصلية
}

// Format des nombres français
function formatFrenchNumber(num) {
    return num.toLocaleString('fr-FR');
}
</script>
@endsection
