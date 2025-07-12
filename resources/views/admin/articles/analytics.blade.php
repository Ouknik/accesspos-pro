@extends('admin.layouts.app')

@section('title', 'تحليلات المنتجات')

@section('styles')
<style>
    .analytics-card { 
        border: none; 
        box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
        border-radius: 15px; 
        margin-bottom: 20px;
        transition: transform 0.2s;
    }
    .analytics-card:hover { transform: translateY(-5px); }
    .stat-icon { 
        font-size: 3rem; 
        opacity: 0.8; 
    }
    .chart-container { 
        height: 400px; 
        position: relative; 
    }
    .filter-section { 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 15px;
        margin-bottom: 30px;
    }
    .metric-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .trend-up { color: #28a745; }
    .trend-down { color: #dc3545; }
    .trend-neutral { color: #6c757d; }
    .top-products-list { max-height: 400px; overflow-y: auto; }
    .product-item {
        border-bottom: 1px solid #eee;
        padding: 10px 0;
    }
    .product-item:last-child { border-bottom: none; }
    .export-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Navigation breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.tableau-de-bord-moderne') }}">لوحة التحكم</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.articles.index') }}">إدارة المنتجات</a></li>
            <li class="breadcrumb-item active">التحليلات</li>
        </ol>
    </nav>

    <!-- عنوان الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">تحليلات المنتجات</h1>
            <p class="text-muted">تحليل شامل لأداء المنتجات والمبيعات</p>
        </div>
        <div>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-download"></i> تصدير التقرير
            </button>
            <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> العودة للقائمة
            </a>
        </div>
    </div>

    <!-- فلاتر التحليل -->
    <div class="filter-section">
        <h4 class="mb-3"><i class="fas fa-filter me-2"></i>فلاتر التحليل</h4>
        <form method="GET" action="{{ route('admin.articles.analytics') }}" class="row">
            <div class="col-md-3">
                <label class="form-label">فترة التحليل</label>
                <select name="period" class="form-select">
                    <option value="7" {{ request('period') == '7' ? 'selected' : '' }}>آخر 7 أيام</option>
                    <option value="30" {{ request('period', '30') == '30' ? 'selected' : '' }}>آخر 30 يوم</option>
                    <option value="90" {{ request('period') == '90' ? 'selected' : '' }}>آخر 3 أشهر</option>
                    <option value="365" {{ request('period') == '365' ? 'selected' : '' }}>آخر سنة</option>
                    <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>فترة مخصصة</option>
                </select>
            </div>
            <div class="col-md-2" id="date-from" style="display: {{ request('period') == 'custom' ? 'block' : 'none' }};">
                <label class="form-label">من تاريخ</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2" id="date-to" style="display: {{ request('period') == 'custom' ? 'block' : 'none' }};">
                <label class="form-label">إلى تاريخ</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">العائلة</label>
                <select name="famille" class="form-select">
                    <option value="">جميع العائلات</option>
                    @foreach($familles as $famille)
                        <option value="{{ $famille->FAM_REF }}" 
                                {{ request('famille') == $famille->FAM_REF ? 'selected' : '' }}>
                            {{ $famille->FAM_LIB }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-light">
                    <i class="fas fa-search"></i> تطبيق الفلاتر
                </button>
            </div>
        </form>
    </div>

    <!-- مقاييس رئيسية -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">إجمالي المنتجات</h6>
                        <h3 class="text-primary">{{ $analytics['total_products'] ?? 0 }}</h3>
                        <small class="trend-up">
                            <i class="fas fa-arrow-up"></i> +{{ $analytics['new_products'] ?? 0 }} جديد
                        </small>
                    </div>
                    <i class="fas fa-boxes stat-icon text-primary"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">المنتجات النشطة</h6>
                        <h3 class="text-success">{{ $analytics['active_products'] ?? 0 }}</h3>
                        <small class="text-muted">
                            {{ $analytics['active_percentage'] ?? 0 }}% من الإجمالي
                        </small>
                    </div>
                    <i class="fas fa-check-circle stat-icon text-success"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">مخزون منخفض</h6>
                        <h3 class="text-warning">{{ $analytics['low_stock_products'] ?? 0 }}</h3>
                        <small class="text-muted">
                            يحتاج إعادة تموين
                        </small>
                    </div>
                    <i class="fas fa-exclamation-triangle stat-icon text-warning"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">قيمة المخزون</h6>
                        <h3 class="text-info">{{ number_format($analytics['total_stock_value'] ?? 0, 0) }}</h3>
                        <small class="text-muted">دج</small>
                    </div>
                    <i class="fas fa-money-bill stat-icon text-info"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- أفضل المنتجات مبيعاً -->
        <div class="col-md-6">
            <div class="analytics-card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>أفضل المنتجات مبيعاً</h5>
                </div>
                <div class="card-body">
                    @if(isset($analytics['top_selling']) && count($analytics['top_selling']) > 0)
                        <div class="top-products-list">
                            @foreach($analytics['top_selling'] as $index => $product)
                                <div class="product-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-success me-2">{{ $index + 1 }}</span>
                                            <div>
                                                <strong>{{ $product->ART_DESIGNATION }}</strong>
                                                <br><small class="text-muted">{{ $product->ART_REF }}</small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="text-success fw-bold">{{ $product->total_sold ?? 0 }} وحدة</div>
                                            <small class="text-muted">{{ number_format($product->total_revenue ?? 0, 2) }} دج</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-chart-bar fa-3x mb-3"></i>
                            <p>لا توجد بيانات مبيعات للفترة المحددة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- أقل المنتجات مبيعاً -->
        <div class="col-md-6">
            <div class="analytics-card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-arrow-down me-2"></i>أقل المنتجات مبيعاً</h5>
                </div>
                <div class="card-body">
                    @if(isset($analytics['low_selling']) && count($analytics['low_selling']) > 0)
                        <div class="top-products-list">
                            @foreach($analytics['low_selling'] as $index => $product)
                                <div class="product-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-warning me-2">{{ $index + 1 }}</span>
                                            <div>
                                                <strong>{{ $product->ART_DESIGNATION }}</strong>
                                                <br><small class="text-muted">{{ $product->ART_REF }}</small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="text-warning fw-bold">{{ $product->total_sold ?? 0 }} وحدة</div>
                                            <small class="text-muted">{{ number_format($product->total_revenue ?? 0, 2) }} دج</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-chart-line fa-3x mb-3"></i>
                            <p>لا توجد بيانات مبيعات للمقارنة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- توزيع المنتجات حسب العائلة -->
        <div class="col-md-6">
            <div class="analytics-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>توزيع المنتجات حسب العائلة</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="familyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- تطور المبيعات -->
        <div class="col-md-6">
            <div class="analytics-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>تطور المبيعات</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="salesTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- تحليل المخزون -->
    <div class="row">
        <div class="col-12">
            <div class="analytics-card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-warehouse me-2"></i>تحليل المخزون</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="chart-container">
                                <canvas id="stockChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6>حالة المخزون</h6>
                            <div class="list-group">
                                <div class="list-group-item d-flex justify-content-between">
                                    <span>مخزون طبيعي</span>
                                    <span class="badge bg-success">{{ $analytics['normal_stock'] ?? 0 }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between">
                                    <span>مخزون منخفض</span>
                                    <span class="badge bg-warning">{{ $analytics['low_stock'] ?? 0 }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between">
                                    <span>مخزون نفذ</span>
                                    <span class="badge bg-danger">{{ $analytics['out_of_stock'] ?? 0 }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between">
                                    <span>مخزون مرتفع</span>
                                    <span class="badge bg-info">{{ $analytics['overstock'] ?? 0 }}</span>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <h6>إجراءات مقترحة</h6>
                            <div class="list-group">
                                @if(($analytics['low_stock'] ?? 0) > 0)
                                    <div class="list-group-item list-group-item-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        إعادة تموين {{ $analytics['low_stock'] }} منتج
                                    </div>
                                @endif
                                @if(($analytics['overstock'] ?? 0) > 0)
                                    <div class="list-group-item list-group-item-info">
                                        <i class="fas fa-tags me-2"></i>
                                        ترويج {{ $analytics['overstock'] }} منتج
                                    </div>
                                @endif
                                @if(($analytics['out_of_stock'] ?? 0) > 0)
                                    <div class="list-group-item list-group-item-danger">
                                        <i class="fas fa-times-circle me-2"></i>
                                        طلب عاجل لـ {{ $analytics['out_of_stock'] }} منتج
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- التصدير -->
    <div class="export-section">
        <h5><i class="fas fa-download me-2"></i>تصدير التقارير</h5>
        <p class="text-muted">اختر نوع التقرير المطلوب تصديره</p>
        <div class="row">
            <div class="col-md-3">
                <a href="{{ route('admin.articles.export', array_merge(request()->query(), ['type' => 'products'])) }}" 
                   class="btn btn-outline-primary w-100">
                    <i class="fas fa-table me-2"></i>قائمة المنتجات
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.articles.export', array_merge(request()->query(), ['type' => 'sales'])) }}" 
                   class="btn btn-outline-success w-100">
                    <i class="fas fa-chart-bar me-2"></i>تقرير المبيعات
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.articles.export', array_merge(request()->query(), ['type' => 'stock'])) }}" 
                   class="btn btn-outline-warning w-100">
                    <i class="fas fa-warehouse me-2"></i>تقرير المخزون
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.articles.export', array_merge(request()->query(), ['type' => 'complete'])) }}" 
                   class="btn btn-outline-info w-100">
                    <i class="fas fa-file-alt me-2"></i>تقرير شامل
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal للتصدير المخصص -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تصدير تقرير مخصص</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="GET" action="{{ route('admin.articles.export') }}">
                    <!-- نقل معايير البحث الحالية -->
                    @foreach(request()->query() as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">نوع التقرير</label>
                                <select name="type" class="form-select" required>
                                    <option value="products">قائمة المنتجات</option>
                                    <option value="sales">تقرير المبيعات</option>
                                    <option value="stock">تقرير المخزون</option>
                                    <option value="analytics">تقرير التحليلات</option>
                                    <option value="complete">تقرير شامل</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">تنسيق الملف</label>
                                <select name="format" class="form-select" required>
                                    <option value="excel">Excel (.xlsx)</option>
                                    <option value="csv">CSV</option>
                                    <option value="pdf">PDF</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">البيانات المطلوبة</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fields[]" value="basic" checked>
                                    <label class="form-check-label">المعلومات الأساسية</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fields[]" value="prices" checked>
                                    <label class="form-check-label">الأسعار</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fields[]" value="stock">
                                    <label class="form-check-label">معلومات المخزون</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fields[]" value="sales">
                                    <label class="form-check-label">بيانات المبيعات</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fields[]" value="categories">
                                    <label class="form-check-label">العائلات والفئات</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fields[]" value="dates">
                                    <label class="form-check-label">تواريخ الإنشاء والتعديل</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="include_charts" value="1">
                            <label class="form-check-label">تضمين الرسوم البيانية (PDF فقط)</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>تصدير التقرير
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // التحكم في إظهار/إخفاء تواريخ الفترة المخصصة
        const periodSelect = document.querySelector('select[name="period"]');
        const dateFromDiv = document.getElementById('date-from');
        const dateToDiv = document.getElementById('date-to');
        
        periodSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                dateFromDiv.style.display = 'block';
                dateToDiv.style.display = 'block';
            } else {
                dateFromDiv.style.display = 'none';
                dateToDiv.style.display = 'none';
            }
        });

        // رسم بياني لتوزيع المنتجات حسب العائلة
        const familyCtx = document.getElementById('familyChart').getContext('2d');
        const familyData = @json($analytics['family_distribution'] ?? []);
        
        new Chart(familyCtx, {
            type: 'doughnut',
            data: {
                labels: familyData.map(item => item.famille || 'غير محدد'),
                datasets: [{
                    data: familyData.map(item => item.count),
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                        '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // رسم بياني لتطور المبيعات
        const salesCtx = document.getElementById('salesTrendChart').getContext('2d');
        const salesData = @json($analytics['sales_trend'] ?? []);
        
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: salesData.map(item => item.date),
                datasets: [{
                    label: 'المبيعات اليومية',
                    data: salesData.map(item => item.total),
                    borderColor: '#36A2EB',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // رسم بياني لحالة المخزون
        const stockCtx = document.getElementById('stockChart').getContext('2d');
        const stockData = @json($analytics['stock_status'] ?? []);
        
        new Chart(stockCtx, {
            type: 'bar',
            data: {
                labels: ['مخزون طبيعي', 'مخزون منخفض', 'نفذ المخزون', 'مخزون مرتفع'],
                datasets: [{
                    label: 'عدد المنتجات',
                    data: [
                        stockData.normal || 0,
                        stockData.low || 0,
                        stockData.out || 0,
                        stockData.over || 0
                    ],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#17a2b8']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection
