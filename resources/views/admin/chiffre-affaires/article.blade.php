@extends('layouts.sb-admin')

@section('title', 'Rapport CA par Article - AccessPos Pro')

@section('page-heading')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-box text-info"></i>
            Rapport Chiffre d'Affaires par Article
        </h1>
        <p class="mb-0 text-muted">Top 20 articles - Période: {{ $dateDebut }} à {{ $dateFin }}</p>
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
        <h6 class="m-0 font-weight-bold text-info">
            <i class="fas fa-filter"></i>
            Filtres de Période
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.chiffre-affaires.article') }}">
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
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-search"></i>
                        Afficher Rapport
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tableau rapport articles --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-info">
            <i class="fas fa-table mr-2"></i>
            Top 20 Articles par Chiffre d'Affaires
        </h6>
        <div class="btn-group">
            <a href="{{ route('admin.chiffre-affaires.export', ['type' => 'pdf', 'report' => 'article'] + request()->query()) }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('admin.chiffre-affaires.export', ['type' => 'excel', 'report' => 'article'] + request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <button type="button" class="btn btn-info btn-sm" onclick="printTable('articlesTable')">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>
    <div class="card-body">
        @if(count($articles) > 0)
            <div class="table-responsive">
                <table class="table table-bordered" id="articlesTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Référence</th>
                            <th>Nom Article</th>
                            <th>Catégorie</th>
                            <th>Quantité Vendue</th>
                            <th>Chiffre d'Affaires (DH)</th>
                            <th>Prix Moyen (DH)</th>
                            <th>Nb Ventes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articles as $index => $article)
                            <tr>
                                <td class="text-center">
                                    @if($index < 3)
                                        <span class="badge badge-gold">
                                            <i class="fas fa-trophy"></i>
                                            {{ $index + 1 }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td>
                                    <code>{{ $article->reference }}</code>
                                </td>
                                <td>
                                    <strong>{{ $article->designation }}</strong>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $article->famille }}</span>
                                </td>
                                <td class="text-center">
                                    {{ number_format($article->quantite_vendue, 2) }}
                                </td>
                                <td class="text-success font-weight-bold">
                                    {{ number_format($article->chiffre_affaires, 2) }}
                                </td>
                                <td class="text-info">
                                    {{ number_format($article->prix_moyen, 2) }}
                                </td>
                                <td class="text-center">
                                    {{ number_format($article->nombre_ventes) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Statistiques additionnelles --}}
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card border-left-success">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Meilleur Article
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $articles[0]->designation ?? 'Non défini' }}
                            </div>
                            <div class="small text-muted">
                                {{ number_format($articles[0]->chiffre_affaires ?? 0, 2) }} DH
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-primary">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                CA Total Articles
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ number_format(collect($articles)->sum('chiffre_affaires'), 2) }} DH
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-info">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Quantité Totale
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ number_format(collect($articles)->sum('quantite_vendue'), 2) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-warning">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Ventes Moyennes
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ number_format(collect($articles)->avg('nombre_ventes'), 0) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <div class="text-center py-5">
                <i class="fas fa-box fa-5x text-gray-300 mb-3"></i>
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
    $('#articlesTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
        },
        "pageLength": 20,
        "order": [[ 5, "desc" ]],
        "columnDefs": [
            { "orderable": false, "targets": [0] }
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
    <title>تقرير أفضل المنتجات - ${currentDate}</title>
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
        <h1>AccessPos Pro - تقرير أفضل 20 منتج</h1>
        <p><strong>تاريخ الطباعة:</strong> ${currentDate} - ${currentTime}</p>
        <p>تقرير مفصل لأفضل المنتجات حسب رقم الأعمال</p>
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
</script>

<style>
.badge-gold {
    background: linear-gradient(45deg, #FFD700, #FFA500);
    color: #000;
    font-weight: bold;
}
</style>
@endsection
