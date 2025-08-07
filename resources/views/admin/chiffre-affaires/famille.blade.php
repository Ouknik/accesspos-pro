@extends('layouts.sb-admin')

@section('title', 'Rapport CA par Famille - AccessPos Pro')

@section('page-heading')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tags text-success"></i>
            Rapport Chiffre d'Affaires par Famille
        </h1>
        <p class="mb-0 text-muted">Analyse performance familles - Période: {{ $dateDebut }} à {{ $dateFin }}</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.chiffre-affaires.index', request()->query()) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-right"></i>
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
        <h6 class="m-0 font-weight-bold text-success">
            <i class="fas fa-filter"></i>
            Filtres de Période
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.chiffre-affaires.famille') }}">
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
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-search"></i>
                        Afficher Rapport
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tableau rapport familles --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-success">
            <i class="fas fa-table mr-2"></i>
            Rapport Chiffre d'Affaires par Famille
        </h6>
        <div class="btn-group">
            <a href="{{ route('admin.chiffre-affaires.export', ['type' => 'pdf', 'report' => 'famille'] + request()->query()) }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('admin.chiffre-affaires.export', ['type' => 'excel', 'report' => 'famille'] + request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <button type="button" class="btn btn-info btn-sm" onclick="printTable('famillesTable')">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>
    <div class="card-body">
        @if(count($familles) > 0)
            <div class="table-responsive">
                <table class="table table-bordered" id="famillesTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Famille</th>
                            <th>Nb Factures</th>
                            <th>Quantité Vendue</th>
                            <th>Chiffre d'Affaires (DH)</th>
                            <th>Prix Moyen (DH)</th>
                            <th>Pourcentage %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_ca = collect($familles)->sum('chiffre_affaires');
                            $total_qte = collect($familles)->sum('quantite_vendue');
                        @endphp
                        
                        @foreach($familles as $index => $famille)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-tag text-{{ $index < 3 ? 'success' : ($index < 5 ? 'warning' : 'info') }} mr-2"></i>
                                        <strong>{{ $famille->famille }}</strong>
                                    </div>
                                </td>
                                <td class="text-center">
                                    {{ number_format($famille->nombre_factures) }}
                                </td>
                                <td class="text-center">
                                    {{ number_format($famille->quantite_vendue, 2) }}
                                </td>
                                <td class="text-success font-weight-bold">
                                    {{ number_format($famille->chiffre_affaires, 2) }}
                                </td>
                                <td class="text-info">
                                    {{ number_format($famille->prix_moyen, 2) }}
                                </td>
                                <td>
                                    @if($total_ca > 0)
                                        <div class="progress">
                                            <div class="progress-bar bg-{{ $index < 3 ? 'success' : ($index < 5 ? 'warning' : 'info') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ ($famille->chiffre_affaires / $total_ca) * 100 }}%">
                                                {{ number_format(($famille->chiffre_affaires / $total_ca) * 100, 1) }}%
                                            </div>
                                        </div>
                                    @else
                                        0.0%
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-success text-white">
                        <tr>
                            <th>Total Général</th>
                            <th class="text-center">-</th>
                            <th class="text-center">{{ number_format($total_qte, 2) }}</th>
                            <th>{{ number_format($total_ca, 2) }}</th>
                            <th>-</th>
                            <th>100.0%</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Graphique des familles --}}
            <div class="row mt-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-success">
                                <i class="fas fa-chart-bar mr-2"></i>
                                Graphique Chiffre d'Affaires par Famille
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="familleChart" width="100%" height="50"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    {{-- Statistiques supplémentaires --}}
                    <div class="card border-left-success mb-3">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Meilleure Famille
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $familles[0]->famille ?? 'Non défini' }}
                            </div>
                        </div>
                    </div>
                    <div class="card border-left-info mb-3">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Familles Actives
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ count($familles) }}
                            </div>
                        </div>
                    </div>
                    <div class="card border-left-warning">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                CA Moyenne par Famille
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ number_format(count($familles) > 0 ? $total_ca / count($familles) : 0, 2) }} DH
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <div class="text-center py-5">
                <i class="fas fa-tags fa-5x text-gray-300 mb-3"></i>
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
    $('#famillesTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
        },
        "pageLength": 25,
        "order": [[ 3, "desc" ]],
        "columnDefs": [
            { "orderable": false, "targets": [5] }
        ]
    });
});

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
    <title>تقرير فئات المنتجات - ${currentDate}</title>
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
        <h1>AccessPos Pro - تقرير أداء فئات المنتجات</h1>
        <p><strong>تاريخ الطباعة:</strong> ${currentDate} - ${currentTime}</p>
        <p>تقرير مفصل لأداء فئات المنتجات والمبيعات</p>
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

// Graphique des familles
@if(count($familles) > 0)
var ctx = document.getElementById('familleChart').getContext('2d');
var familleChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(collect($familles)->pluck('famille')->toArray()) !!},
        datasets: [{
            label: 'Chiffre d\'Affaires (DH)',
            data: {!! json_encode(collect($familles)->pluck('chiffre_affaires')->toArray()) !!},
            backgroundColor: 'rgba(28, 200, 138, 0.2)',
            borderColor: 'rgba(28, 200, 138, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
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
                        return 'Chiffre d\'Affaires: ' + context.parsed.y.toLocaleString() + ' DH';
                    }
                }
            }
        }
    }
});
@endif
</script>
@endsection
