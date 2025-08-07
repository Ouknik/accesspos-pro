@extends('layouts.sb-admin')

@section('title', 'Rapport CA par Serveur - AccessPos Pro')

@section('page-heading')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-tie text-primary"></i>
            Rapport Chiffre d'Affaires par Serveur
        </h1>
        <p class="mb-0 text-muted">Analyse performance serveurs - Période: {{ $dateDebut }} à {{ $dateFin }}</p>
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

{{-- Filtres période --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter"></i>
            Filtres de Période
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.chiffre-affaires.serveur') }}">
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
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Afficher Rapport
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tableau rapport serveurs --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table mr-2"></i>
            Rapport Chiffre d'Affaires par Serveur
        </h6>
        <div class="btn-group">
            <a href="{{ route('admin.chiffre-affaires.export', ['type' => 'pdf', 'report' => 'serveur'] + request()->query()) }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('admin.chiffre-affaires.export', ['type' => 'excel', 'report' => 'serveur'] + request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <button type="button" class="btn btn-info btn-sm" onclick="printTable('serveursTable')">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>
    <div class="card-body">
        @if(count($serveurs) > 0)
            <div class="table-responsive">
                <table class="table table-bordered" id="serveursTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Code Serveur</th>
                            <th>Nom Serveur</th>
                            <th>Nb Factures</th>
                            <th>Chiffre d'Affaires (DH)</th>
                            <th>Moyenne Facture (DH)</th>
                            <th>Pourcentage %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_ca = collect($serveurs)->sum('chiffre_affaires');
                            $total_factures = collect($serveurs)->sum('nombre_factures');
                        @endphp
                        
                        @foreach($serveurs as $index => $serveur)
                            <tr>
                                <td>
                                    <span class="badge badge-info">{{ $serveur->code_serveur }}</span>
                                </td>
                                <td>
                                    <strong>{{ $serveur->nom_serveur }}</strong>
                                </td>
                                <td class="text-center">
                                    {{ number_format($serveur->nombre_factures) }}
                                </td>
                                <td class="text-success font-weight-bold">
                                    {{ number_format($serveur->chiffre_affaires, 2) }}
                                </td>
                                <td class="text-info">
                                    {{ number_format($serveur->moyenne_facture, 2) }}
                                </td>
                                <td>
                                    @if($total_ca > 0)
                                        <div class="progress">
                                            <div class="progress-bar bg-{{ $index < 3 ? 'success' : ($index < 5 ? 'warning' : 'info') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ ($serveur->chiffre_affaires / $total_ca) * 100 }}%">
                                                {{ number_format(($serveur->chiffre_affaires / $total_ca) * 100, 1) }}%
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
                            <th colspan="2">Total Général</th>
                            <th class="text-center">{{ number_format($total_factures) }}</th>
                            <th>{{ number_format($total_ca, 2) }}</th>
                            <th>{{ number_format($total_factures > 0 ? $total_ca / $total_factures : 0, 2) }}</th>
                            <th>100.0%</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Statistiques additionnelles --}}
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card border-left-success">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Meilleur Serveur
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $serveurs[0]->nom_serveur ?? 'Non défini' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-primary">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Serveurs Actifs
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ count($serveurs) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-info">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                CA Moyen par Serveur
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ number_format(count($serveurs) > 0 ? $total_ca / count($serveurs) : 0, 2) }} DH
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-warning">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Factures Moy. par Serveur
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ number_format(count($serveurs) > 0 ? $total_factures / count($serveurs) : 0) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <div class="text-center py-5">
                <i class="fas fa-user-tie fa-5x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">Aucune donnée pour la période sélectionnée</h5>
                <p class="text-muted">Veuillez modifier la période ou vérifier l'existence de ventes</p>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#serveursTable').DataTable({
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
        <title>تقرير الخوادم</title>
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
            <h1>AccessPos Pro - تقرير أداء الخوادم</h1>
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
</script>
@endsection
