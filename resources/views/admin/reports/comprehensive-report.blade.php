@extends('layouts.app')

@section('title', 'التقارير الشاملة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h2 class="card-title mb-0">
                        <i class="fas fa-chart-line mr-2"></i>
                        التقارير الشاملة - AccessPOS Pro
                    </h2>
                </div>
                
                <div class="card-body">
                    <!-- نموذج الفلترة -->
                    <form method="GET" action="{{ route('admin.reports.comprehensive') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">تاريخ البداية</label>
                                <input type="date" name="date_debut" class="form-control" 
                                       value="{{ request('date_debut', date('Y-m-d', strtotime('-30 days'))) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">تاريخ النهاية</label>
                                <input type="date" name="date_fin" class="form-control" 
                                       value="{{ request('date_fin', date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">الكاشير</label>
                                <select name="caissier" class="form-control">
                                    <option value="">جميع الكاشيرين</option>
                                    @foreach($caissiers as $caissier)
                                        <option value="{{ $caissier->id }}" 
                                                {{ request('caissier') == $caissier->id ? 'selected' : '' }}>
                                            {{ $caissier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> عرض التقارير
                                    </button>
                                    <a href="{{ route('admin.reports.comprehensive.pdf', request()->all()) }}" 
                                       class="btn btn-danger">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if(isset($reportData))
                        <!-- الإحصائيات الأساسية -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="stat-card bg-gradient-success">
                                    <div class="stat-icon">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-value">{{ number_format($reportData['basic_stats']['total_sales'], 2) }}</div>
                                        <div class="stat-label">إجمالي المبيعات</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card bg-gradient-info">
                                    <div class="stat-icon">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-value">{{ $reportData['basic_stats']['total_invoices'] }}</div>
                                        <div class="stat-label">عدد الفواتير</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card bg-gradient-warning">
                                    <div class="stat-icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-value">{{ number_format($reportData['basic_stats']['avg_ticket'], 2) }}</div>
                                        <div class="stat-label">متوسط قيمة الفاتورة</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card bg-gradient-danger">
                                    <div class="stat-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-value">{{ $reportData['basic_stats']['total_customers'] }}</div>
                                        <div class="stat-label">عدد العملاء</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- تقارير المبيعات اليومية -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-calendar-day"></i> تقرير المبيعات اليومية</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>التاريخ</th>
                                                        <th>عدد الفواتير</th>
                                                        <th>إجمالي المبيعات</th>
                                                        <th>متوسط قيمة الفاتورة</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($reportData['daily_sales'] as $day)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($day['date'])->format('d/m/Y') }}</td>
                                                        <td>{{ $day['invoices_count'] }}</td>
                                                        <td>{{ number_format($day['total_sales'], 2) }}</td>
                                                        <td>{{ number_format($day['avg_ticket'], 2) }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- تقرير أداء الكاشيرين -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-user-tie"></i> تقرير أداء الكاشيرين</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>الكاشير</th>
                                                        <th>عدد الفواتير</th>
                                                        <th>إجمالي المبيعات</th>
                                                        <th>متوسط قيمة الفاتورة</th>
                                                        <th>النسبة المئوية</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($reportData['cashier_performance'] as $cashier)
                                                    <tr>
                                                        <td>{{ $cashier['name'] }}</td>
                                                        <td>{{ $cashier['invoices_count'] }}</td>
                                                        <td>{{ number_format($cashier['total_sales'], 2) }}</td>
                                                        <td>{{ number_format($cashier['avg_ticket'], 2) }}</td>
                                                        <td>
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar" role="progressbar" 
                                                                     style="width: {{ $cashier['percentage'] }}%">
                                                                    {{ number_format($cashier['percentage'], 1) }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- تقرير وسائل الدفع -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-credit-card"></i> تقرير وسائل الدفع</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>وسيلة الدفع</th>
                                                        <th>عدد المعاملات</th>
                                                        <th>إجمالي المبلغ</th>
                                                        <th>النسبة المئوية</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($reportData['payment_methods'] as $payment)
                                                    <tr>
                                                        <td>{{ $payment['method'] }}</td>
                                                        <td>{{ $payment['count'] }}</td>
                                                        <td>{{ number_format($payment['total'], 2) }}</td>
                                                        <td>
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar bg-info" role="progressbar" 
                                                                     style="width: {{ $payment['percentage'] }}%">
                                                                    {{ number_format($payment['percentage'], 1) }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- أفضل المنتجات مبيعاً -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-trophy"></i> أفضل المنتجات مبيعاً</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>المنتج</th>
                                                        <th>الكمية المباعة</th>
                                                        <th>إجمالي المبيعات</th>
                                                        <th>متوسط السعر</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($reportData['top_products'] as $product)
                                                    <tr>
                                                        <td>{{ $product['name'] }}</td>
                                                        <td>{{ $product['quantity'] }}</td>
                                                        <td>{{ number_format($product['total_sales'], 2) }}</td>
                                                        <td>{{ number_format($product['avg_price'], 2) }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- تقرير المقارنة -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-chart-bar"></i> تقرير المقارنة</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="comparison-card">
                                                    <h6>مقارنة مع الفترة السابقة</h6>
                                                    <div class="comparison-value {{ $reportData['comparison']['growth_percentage'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                        <i class="fas fa-{{ $reportData['comparison']['growth_percentage'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                                        {{ number_format(abs($reportData['comparison']['growth_percentage']), 1) }}%
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ $reportData['comparison']['growth_percentage'] >= 0 ? 'زيادة' : 'نقصان' }} في المبيعات
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="comparison-card">
                                                    <h6>أفضل يوم</h6>
                                                    <div class="comparison-value text-primary">
                                                        {{ number_format($reportData['comparison']['best_day']['sales'], 2) }}
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($reportData['comparison']['best_day']['date'])->format('d/m/Y') }}
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="comparison-card">
                                                    <h6>أسوأ يوم</h6>
                                                    <div class="comparison-value text-warning">
                                                        {{ number_format($reportData['comparison']['worst_day']['sales'], 2) }}
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($reportData['comparison']['worst_day']['date'])->format('d/m/Y') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 20px;
    color: white;
    text-align: center;
    margin-bottom: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-card.bg-gradient-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.stat-card.bg-gradient-info {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.stat-card.bg-gradient-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.stat-card.bg-gradient-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.stat-icon {
    font-size: 2rem;
    margin-bottom: 10px;
}

.stat-value {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.comparison-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    height: 100%;
}

.comparison-value {
    font-size: 1.5rem;
    font-weight: bold;
    margin: 10px 0;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.table th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-weight: 600;
}

.table td {
    border-color: #e2e8f0;
    vertical-align: middle;
}

.btn {
    border-radius: 10px;
    font-weight: 600;
    padding: 10px 20px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    border: none;
}
</style>
@endsection
