@extends('layouts.sb-admin')

@section('title', 'Rapport CA par Caissier - AccessPos Pro')

@section('page-heading')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cash-register text-secondary"></i>
            Rapport Chiffre d'Affaires par Caissier
        </h1>
        <p class="mb-0 text-muted">Analyse performance caissiers - Période: {{ $dateDebut }} à {{ $dateFin }}</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.chiffre-affaires.index', request()->query()) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i>
            Retour aux rapports
        </a>
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
            <i class="fas fa-print"></i>
            Imprimer
        </button>
    </div>
</div>
@endsection

@section('content')

@php
    // تعريف المتغيرات المطلوبة
    $total_ca = collect($caissiers)->sum('chiffre_affaires');
    $total_factures = collect($caissiers)->sum('nombre_factures');
@endphp

{{-- Filtres période --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-secondary">
            <i class="fas fa-filter"></i>
            Filtres de Période
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.chiffre-affaires.caissier') }}">
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
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-search"></i>
                        Afficher Rapport
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Statistiques rapides --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            CA Total Caissiers
                        </div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($total_ca, 2) }} DH
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Factures
                        </div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($total_factures) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-receipt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            CA Moy. par Caissier
                        </div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                            {{ number_format(count($caissiers) > 0 ? $total_ca / count($caissiers) : 0, 2) }} DH
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calculator fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Factures Moy. par Caissier
                        </div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                            {{ number_format(count($caissiers) > 0 ? $total_factures / count($caissiers) : 0) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-cash-register fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tableau rapport caissiers --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-secondary">
            <i class="fas fa-table mr-2"></i>
            Rapport Chiffre d'Affaires par Caissier
        </h6>
        <div class="btn-group">
            <a href="{{ route('admin.chiffre-affaires.export', ['type' => 'pdf', 'report' => 'caissier'] + request()->query()) }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('admin.chiffre-affaires.export', ['type' => 'excel', 'report' => 'caissier'] + request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <button type="button" class="btn btn-info btn-sm" onclick="printTable('caissiersTable')">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>
    <div class="card-body">
        @if(count($caissiers) > 0)
            <div class="table-responsive">
                <table class="table table-bordered" id="caissiersTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Code Caissier</th>
                            <th>Nom Caissier</th>
                            <th>Nb Factures</th>
                            <th>Chiffre d'Affaires (DH)</th>
                            <th>Moyenne Facture (DH)</th>
                            <th>Première Vente</th>
                            <th>Dernière Vente</th>
                            <th>Pourcentage %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($caissiers as $index => $caissier)
                            <tr>
                                <td>
                                    <span class="badge badge-secondary">{{ $caissier->code_caissier }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-secondary rounded-circle mr-2 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <strong>{{ $caissier->nom_caissier }}</strong>
                                    </div>
                                </td>
                                <td class="text-center">
                                    {{ number_format($caissier->nombre_factures) }}
                                </td>
                                <td class="text-success font-weight-bold">
                                    {{ number_format($caissier->chiffre_affaires, 2) }}
                                </td>
                                <td class="text-info">
                                    {{ number_format($caissier->moyenne_facture, 2) }}
                                </td>
                                <td class="text-muted">
                                    {{ \Carbon\Carbon::parse($caissier->premiere_vente)->format('d/m/Y') }}
                                </td>
                                <td class="text-muted">
                                    {{ \Carbon\Carbon::parse($caissier->derniere_vente)->format('d/m/Y') }}
                                </td>
                                <td>
                                    @if($total_ca > 0)
                                        <div class="progress">
                                            <div class="progress-bar bg-{{ $index < 3 ? 'success' : ($index < 5 ? 'warning' : 'secondary') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ ($caissier->chiffre_affaires / $total_ca) * 100 }}%">
                                                {{ number_format(($caissier->chiffre_affaires / $total_ca) * 100, 1) }}%
                                            </div>
                                        </div>
                                    @else
                                        0.0%
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-secondary text-white">
                        <tr>
                            <th colspan="2">Total Général</th>
                            <th class="text-center">{{ number_format($total_factures) }}</th>
                            <th>{{ number_format($total_ca, 2) }}</th>
                            <th>{{ number_format($total_factures > 0 ? $total_ca / $total_factures : 0, 2) }}</th>
                            <th>-</th>
                            <th>-</th>
                            <th>100.0%</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Statistiques supplémentaires --}}
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card border-left-success">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Meilleur Caissier
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $caissiers[0]->nom_caissier ?? 'Non défini' }}
                            </div>
                            <div class="small text-muted">
                                {{ number_format($caissiers[0]->chiffre_affaires ?? 0, 2) }} DH
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-primary">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Caissiers Actifs
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ count($caissiers) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-info">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                CA Moyenne par Caissier
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ number_format(count($caissiers) > 0 ? $total_ca / count($caissiers) : 0, 2) }} درهم
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-warning">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                متוسط الفواتير لكل صراف
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ number_format(count($caissiers) > 0 ? $total_factures / count($caissiers) : 0) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Graphique de performance --}}
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-secondary">
                                <i class="fas fa-chart-bar mr-2"></i>
                                Comparaison Performance Caissiers
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="caissierChart" width="100%" height="50"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <div class="text-center py-5">
                <i class="fas fa-cash-register fa-5x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">Aucune donnée pour la période sélectionnée</h5>
                <p class="text-muted">Veuillez modifier la période ou vérifier l'existence de ventes</p>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    $('#caissiersTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
        },
        "pageLength": 25,
        "order": [[ 3, "desc" ]],
        "columnDefs": [
            { "orderable": false, "targets": [7] }
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
    <title>تقرير أداء الصرافين - ${currentDate}</title>
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
            .btn-primary { background-color: #007bff; color: white; }
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
            border-bottom: 3px solid #007bff;
        }
        .report-header h1 {
            color: #007bff;
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
            background-color: #007bff;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tbody tr:hover {
            background-color: #e3f2fd;
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
        <h1>AccessPos Pro - تقرير أداء الصرافين</h1>
        <p><strong>تاريخ الطباعة:</strong> ${currentDate} - ${currentTime}</p>
        <p>تقرير مفصل لأداء الصرافين والمبيعات</p>
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

// Graphique pour les caissiers
@if(count($caissiers) > 0)
var ctx = document.getElementById('caissierChart').getContext('2d');
var caissierChart = new Chart(ctx, {
    type: 'horizontalBar',
    data: {
        labels: {!! json_encode(collect($caissiers)->pluck('nom_caissier')->toArray()) !!},
        datasets: [{
            label: 'Chiffre d\'Affaires (DH)',
            data: {!! json_encode(collect($caissiers)->pluck('chiffre_affaires')->toArray()) !!},
            backgroundColor: 'rgba(108, 117, 125, 0.2)',
            borderColor: 'rgba(108, 117, 125, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    callback: function(value, index, values) {
                        return value.toLocaleString() + ' DH';
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Chiffre d\'Affaires: ' + context.parsed.x.toLocaleString() + ' DH';
                    }
                }
            }
        }
    }
});
@endif
</script>

<style>
.avatar-sm {
    width: 2rem;
    height: 2rem;
}
</style>
@endsection
