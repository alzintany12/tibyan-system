@extends('layouts.app')

@section('page-title', 'إدارة العملاء')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">العملاء</li>
@endsection

@section('page-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('clients.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>عميل جديد
        </a>
        <a href="{{ route('clients.export') }}" class="btn btn-success">
            <i class="fas fa-file-excel me-2"></i>تصدير
        </a>
    </div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-users me-2"></i>قائمة العملاء
        </h5>
    </div>
    <div class="card-body">
        <!-- فلاتر البحث -->
        <div class="row mb-4">
            <div class="col-md-3">
                <label for="search" class="form-label">البحث</label>
                <input type="text" class="form-control" id="search" placeholder="اسم العميل، الهاتف، البريد...">
            </div>
            <div class="col-md-2">
                <label for="type_filter" class="form-label">النوع</label>
                <select class="form-select" id="type_filter">
                    <option value="">الكل</option>
                    <option value="individual">فرد</option>
                    <option value="company">شركة</option>
                    <option value="government">حكومي</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="status_filter" class="form-label">الحالة</label>
                <select class="form-select" id="status_filter">
                    <option value="">الكل</option>
                    <option value="1">نشط</option>
                    <option value="0">غير نشط</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="city_filter" class="form-label">المدينة</label>
                <select class="form-select" id="city_filter">
                    <option value="">الكل</option>
                    @foreach($clients->unique('city')->pluck('city')->filter() as $city)
                        <option value="{{ $city }}">{{ $city }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" id="apply_filters">
                        <i class="fas fa-search me-1"></i>بحث
                    </button>
                    <button type="button" class="btn btn-secondary" id="clear_filters">
                        <i class="fas fa-times me-1"></i>مسح
                    </button>
                </div>
            </div>
        </div>

        <!-- جدول العملاء -->
        <div class="table-responsive">
            <table class="table table-striped" id="clients-table">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>النوع</th>
                        <th>الهاتف</th>
                        <th>البريد الإلكتروني</th>
                        <th>المدينة</th>
                        <th>عدد القضايا</th>
                        <th>الحالة</th>
                        <th>تاريخ الإضافة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                    {{ substr($client->name, 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $client->name }}</h6>
                                    @if($client->company_name)
                                        <small class="text-muted">{{ $client->company_name }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $client->type === 'individual' ? 'info' : ($client->type === 'company' ? 'warning' : 'secondary') }}">
                                {{ $client->type === 'individual' ? 'فرد' : ($client->type === 'company' ? 'شركة' : 'حكومي') }}
                            </span>
                        </td>
                        <td>
                            @if($client->phone)
                                <a href="tel:{{ $client->phone }}" class="text-decoration-none">
                                    <i class="fas fa-phone me-1"></i>{{ $client->phone }}
                                </a>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </td>
                        <td>
                            @if($client->email)
                                <a href="mailto:{{ $client->email }}" class="text-decoration-none">
                                    <i class="fas fa-envelope me-1"></i>{{ $client->email }}
                                </a>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </td>
                        <td>{{ $client->city ?? 'غير محدد' }}</td>
                        <td>
                            <span class="badge bg-primary">{{ $client->legal_cases_count ?? 0 }}</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $client->is_active ? 'success' : 'danger' }}">
                                {{ $client->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $client->created_at->format('Y/m/d') }}</small>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('clients.show', $client) }}">
                                        <i class="fas fa-eye me-2"></i>عرض
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('clients.edit', $client) }}">
                                        <i class="fas fa-edit me-2"></i>تعديل
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('cases.create', ['client_id' => $client->id]) }}">
                                        <i class="fas fa-gavel me-2"></i>قضية جديدة
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('invoices.create', ['client_id' => $client->id]) }}">
                                        <i class="fas fa-file-invoice me-2"></i>فاتورة جديدة
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('clients.toggle-status', $client) }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-toggle-{{ $client->is_active ? 'off' : 'on' }} me-2"></i>
                                                {{ $client->is_active ? 'تعطيل' : 'تفعيل' }}
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirmDelete(this)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-trash me-2"></i>حذف
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                <h5>لا توجد عملاء</h5>
                                <p>ابدأ بإضافة أول عميل لك</p>
                                <a href="{{ route('clients.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>إضافة عميل
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($clients->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
</div>

<!-- إحصائيات سريعة -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $clients->where('is_active', true)->count() }}</h4>
                        <p class="card-text">عميل نشط</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $clients->where('type', 'individual')->count() }}</h4>
                        <p class="card-text">عميل فرد</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $clients->where('type', 'company')->count() }}</h4>
                        <p class="card-text">شركة</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-building fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $clients->filter(function($client) { return $client->created_at->isCurrentMonth(); })->count() }}</h4>
                        <p class="card-text">عميل جديد هذا الشهر</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-plus fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // تفعيل DataTable
    var table = $('#clients-table').DataTable({
        order: [[7, 'desc']],
        columnDefs: [
            { orderable: false, targets: [8] }
        ]
    });

    // فلاتر البحث
    $('#apply_filters').click(function() {
        var search = $('#search').val();
        var type = $('#type_filter').val();
        var status = $('#status_filter').val();
        var city = $('#city_filter').val();

        var url = new URL(window.location.href);
        
        if (search) url.searchParams.set('search', search);
        else url.searchParams.delete('search');
        
        if (type) url.searchParams.set('type', type);
        else url.searchParams.delete('type');
        
        if (status) url.searchParams.set('status', status);
        else url.searchParams.delete('status');
        
        if (city) url.searchParams.set('city', city);
        else url.searchParams.delete('city');

        window.location.href = url.toString();
    });

    // مسح الفلاتر
    $('#clear_filters').click(function() {
        $('#search, #type_filter, #status_filter, #city_filter').val('');
        window.location.href = window.location.pathname;
    });

    // البحث عند الضغط على Enter
    $('#search').keypress(function(e) {
        if (e.which == 13) {
            $('#apply_filters').click();
        }
    });
});
</script>
@endpush