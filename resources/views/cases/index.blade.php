@extends('layouts.app')

@section('title', 'إدارة القضايا - نظام تبيان')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-briefcase ms-2"></i>
        إدارة القضايا
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('cases.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة قضية جديدة
            </a>
        </div>
    </div>
</div>

<!-- إحصائيات سريعة -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-primary">{{ $statistics['total'] }}</h3>
                <p class="card-text">إجمالي القضايا</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-success">{{ $statistics['active'] }}</h3>
                <p class="card-text">القضايا النشطة</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-info">{{ $statistics['completed'] }}</h3>
                <p class="card-text">القضايا المكتملة</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-warning">{{ $statistics['postponed'] }}</h3>
                <p class="card-text">القضايا المؤجلة</p>
            </div>
        </div>
    </div>
</div>

<!-- فلاتر البحث -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('cases.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">البحث</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                       placeholder="رقم القضية، اسم العميل، أو ملخص القضية">
            </div>
            <div class="col-md-3">
                <label class="form-label">الحالة</label>
                <select class="form-select" name="status">
                    <option value="">جميع الحالات</option>
                    @foreach(\App\Models\CaseModel::getStatuses() as $key => $status)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">نوع القضية</label>
                <select class="form-select" name="case_type">
                    <option value="">جميع الأنواع</option>
                    @foreach(\App\Models\CaseModel::getCaseTypes() as $key => $type)
                        <option value="{{ $key }}" {{ request('case_type') == $key ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- جدول القضايا -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">قائمة القضايا</h5>
        <small class="text-muted">إجمالي {{ $cases->total() }} قضية</small>
    </div>
    <div class="card-body">
        @if($cases->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>رقم القضية</th>
                            <th>العميل</th>
                            <th>نوع القضية</th>
                            <th>الحالة</th>
                            <th>الأتعاب</th>
                            <th>الجلسة القادمة</th>
                            <th>تاريخ البدء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cases as $case)
                            <tr>
                                <td>
                                    <strong>{{ $case->case_number }}</strong>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $case->client_name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $case->client_phone }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ \App\Models\CaseModel::getCaseTypes()[$case->case_type] }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'active' => 'success',
                                            'completed' => 'primary',
                                            'postponed' => 'warning',
                                            'cancelled' => 'danger'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$case->status] ?? 'secondary' }}">
                                        {{ \App\Models\CaseModel::getStatuses()[$case->status] }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ number_format($case->total_fees, 2) }}</strong>
                                        <br>
                                        <small class="text-success">مدفوع: {{ number_format($case->fees_received, 2) }}</small>
                                        <br>
                                        <small class="text-danger">متبقي: {{ number_format($case->fees_pending, 2) }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if($case->next_hearing)
                                        <div>
                                            <strong>{{ $case->next_hearing->hearing_date->format('Y-m-d') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $case->next_hearing->hearing_time }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">لا توجد جلسة</span>
                                    @endif
                                </td>
                                <td>{{ $case->start_date->format('Y-m-d') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('cases.show', $case) }}" class="btn btn-sm btn-outline-primary" 
                                           data-bs-toggle="tooltip" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('cases.edit', $case) }}" class="btn btn-sm btn-outline-warning"
                                           data-bs-toggle="tooltip" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('hearings.create', ['case_id' => $case->id]) }}">
                                                        <i class="fas fa-calendar-plus ms-2"></i>
                                                        إضافة جلسة
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('cases.create-invoice', $case) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-file-invoice ms-2"></i>
                                                            إنشاء فاتورة
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('cases.destroy', $case) }}" method="POST" 
                                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه القضية؟')" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash ms-2"></i>
                                                            حذف
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $cases->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">لا توجد قضايا</h5>
                <p class="text-muted">لم يتم العثور على أي قضايا تطابق معايير البحث</p>
                <a href="{{ route('cases.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> إضافة قضية جديدة
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // تفعيل الـ tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
@endpush