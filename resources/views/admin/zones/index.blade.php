@extends('layouts.sb-admin')

@section('title', 'إدارة المناطق - AccessPos Pro')

@section('content')
<div class="container-fluid">
    
    {{-- العنوان والأزرار --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-map-marker-alt text-info"></i>
                إدارة المناطق
            </h1>
            <p class="mb-0 text-muted">إدارة مناطق المطعم وتوزيع الطاولات</p>
        </div>
        <a href="{{ route('admin.zones.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i>
            إضافة منطقة جديدة
        </a>
    </div>

    {{-- قائمة المناطق --}}
    <div class="row">
        @forelse($zones as $zone)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                {{ $zone->ZON_LIB }}
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        {{ $zone->total_tables }} طاولة
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-success"><i class="fas fa-check-circle"></i> {{ $zone->tables_libres }} متاحة</small>
                                <small class="text-danger ml-2"><i class="fas fa-users"></i> {{ $zone->tables_occupees }} مشغولة</small>
                                <small class="text-warning ml-2"><i class="fas fa-calendar"></i> {{ $zone->tables_reservees }} محجوزة</small>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted"><i class="fas fa-chair"></i> {{ $zone->total_couverts }} مقعد إجمالي</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin.zones.show', $zone->ZON_REF) }}" class="btn btn-outline-info">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                            <a href="{{ route('admin.zones.edit', $zone->ZON_REF) }}" class="btn btn-outline-primary">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <button type="button" class="btn btn-outline-danger" 
                                    onclick="deleteZone('{{ $zone->ZON_REF }}', '{{ $zone->ZON_LIB }}', {{ $zone->total_tables }})">
                                <i class="fas fa-trash"></i> حذف
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center">
                    <i class="fas fa-map-marker-alt fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">لا توجد مناطق محددة</h5>
                    <p class="text-muted">ابدأ بإضافة منطقة جديدة لتنظيم طاولات المطعم</p>
                    <a href="{{ route('admin.zones.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        إضافة منطقة جديدة
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    {{-- نصائح سريعة --}}
    <div class="card shadow mt-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-lightbulb"></i>
                نصائح لتنظيم المناطق
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <h6><i class="fas fa-layer-group text-primary"></i> تقسيم منطقي</h6>
                    <p class="text-muted">قسم المطعم إلى مناطق حسب الموقع (داخلي، خارجي، VIP)</p>
                </div>
                <div class="col-md-4">
                    <h6><i class="fas fa-users text-info"></i> تجربة العملاء</h6>
                    <p class="text-muted">فكر في راحة العملاء وسهولة الوصول للطاولات</p>
                </div>
                <div class="col-md-4">
                    <h6><i class="fas fa-cogs text-warning"></i> إدارة الطاقم</h6>
                    <p class="text-muted">اجعل المناطق تسهل على النادلين خدمة الطاولات</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// حذف منطقة
function deleteZone(zonRef, zoneName, tablesCount) {
    if (tablesCount > 0) {
        alert(`❌ لا يمكن حذف المنطقة "${zoneName}" لأنها تحتوي على ${tablesCount} طاولة.\n\nيجب نقل أو حذف الطاولات أولاً.`);
        return;
    }
    
    if (confirm(`هل أنت متأكد من حذف المنطقة "${zoneName}"؟\n\nهذا الإجراء لا يمكن التراجع عنه!`)) {
        $.ajax({
            url: '/admin/zones/' + zonRef,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('✅ ' + response.success);
                    location.reload();
                } else {
                    alert('❌ ' + response.error);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert('❌ ' + (response ? response.error : 'خطأ في حذف المنطقة'));
            }
        });
    }
}
</script>
@endsection
