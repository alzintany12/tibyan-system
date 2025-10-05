@extends('layouts.app')

@section('title', 'إدارة الفواتير - نظام تبيان')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-file-invoice-dollar ms-2"></i>
        إدارة الفواتير
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة فاتورة جديدة
            </a>
        </div>
    </div>
</div>

<!-- إحصائيات سريعة -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center border-primary">
            <div class="card-body">
                <h4 class="text-primary">{{ $statistics['total'] }}</h4>
                <p class="card-text small">إجمالي الفواتير</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-success">
            <div class="card-body">
                <h4 class="text-success">{{ $statistics['paid'] }}</h4>
                <p class="card-text small">الفواتير المدفوعة</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-warning">
            <div class="card-body">
                <h4 class="text-warning">{{ $statistics['pending'] }}</h4>
                <p class="card-text small">في الانتظار</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-danger">
            <div class="card-body">
                <h4 class="text-danger">{{ $statistics['overdue'] }}</h4>
                <p class="card-text small">متأخرة الدفع</p>
            </div>
        </div>
    </div>
</div>

<!-- إحصائيات مالية -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="text-info">{{ number_format($statistics['total_amount'], 2) }} دينار ليبي</h5>
                <p class="card-text small">إجمالي المبالغ</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="text-success">{{ number_format($statistics['paid_amount'], 2) }} دينار ليبي</h5>
                <p class="card-text small">المبالغ المحصلة</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="text-warning">{{ number_format($statistics['pending_amount'], 2) }} دينار ليبي</h5>
                <p class="card-text small">المبالغ المعلقة</p>
            </div>
        </div>
    </div>
</div>

<!-- فلاتر البحث -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('invoices.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">البحث</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                       placeholder="رقم الفاتورة، العميل، رقم القضية">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">الحالة</label>
                <select class="form-select" name="status">
                    <option value="">جميع الحالات</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>مرسلة</option>
                    <option value="viewed" {{ request('status') == 'viewed' ? 'selected' : '' }}>تم الاطلاع</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>متأخرة</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغية</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">من تاريخ</label>
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">إلى تاريخ</label>
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
            </div>
            
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- جدول الفواتير -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>
                            <a href="{{ route('invoices.index', array_merge(request()->query(), ['sort_by' => 'invoice_number', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}" 
                               class="text-decoration-none text-dark">
                                رقم الفاتورة
                                @if(request('sort_by') === 'invoice_number')
                                    <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>العميل</th>
                        <th>القضية</th>
                        <th>
                            <a href="{{ route('invoices.index', array_merge(request()->query(), ['sort_by' => 'invoice_date', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}" 
                               class="text-decoration-none text-dark">
                                تاريخ الفاتورة
                                @if(request('sort_by') === 'invoice_date')
                                    <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>تاريخ الاستحقاق</th>
                        <th class="text-center">
                            <a href="{{ route('invoices.index', array_merge(request()->query(), ['sort_by' => 'total_amount', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}" 
                               class="text-decoration-none text-dark">
                                المبلغ الإجمالي
                                @if(request('sort_by') === 'total_amount')
                                    <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>
                                <a href="{{ route('invoices.show', $invoice) }}" class="text-decoration-none">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>
                            <td>{{ $invoice->client_name }}</td>
                            <td>
                                @if($invoice->case)
                                    <a href="{{ route('cases.show', $invoice->case) }}" class="text-decoration-none text-muted small">
                                        {{ $invoice->case->case_number }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $invoice->invoice_date->format('Y/m/d') }}</td>
                            <td>
                                <span class="{{ $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'text-danger' : '' }}">
                                    {{ $invoice->due_date->format('Y/m/d') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <strong>{{ number_format($invoice->total_amount, 2) }}</strong>
                            </td>
                            <td class="text-center">
                                @switch($invoice->status)
                                    @case('draft')
                                        <span class="badge bg-secondary">مسودة</span>
                                        @break
                                    @case('sent')
                                        <span class="badge bg-primary">مرسلة</span>
                                        @break
                                    @case('viewed')
                                        <span class="badge bg-info">تم الاطلاع</span>
                                        @break
                                    @case('paid')
                                        <span class="badge bg-success">مدفوعة</span>
                                        @break
                                    @case('overdue')
                                        <span class="badge bg-danger">متأخرة</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-warning">ملغية</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $invoice->status }}</span>
                                @endswitch
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($invoice->status === 'draft')
                                        <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" 
                                                type="button" data-bs-toggle="dropdown">
                                            <span class="visually-hidden">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('invoices.print', $invoice) }}" target="_blank">
                                                    <i class="fas fa-print"></i> طباعة
                                                </a>
                                            </li>
                                            
                                            @if($invoice->status !== 'paid')
                                                <li>
                                                    <form action="{{ route('invoices.mark-paid', $invoice) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="dropdown-item" onclick="return confirm('هل أنت متأكد؟')">
                                                            <i class="fas fa-check-circle text-success"></i> تحديد كمدفوعة
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            
                                            <li>
                                                <a class="dropdown-item" href="{{ route('invoices.duplicate', $invoice) }}">
                                                    <i class="fas fa-copy"></i> نسخ
                                                </a>
                                            </li>
                                            
                                            <li><hr class="dropdown-divider"></li>
                                            
                                            @if($invoice->status !== 'paid')
                                                <li>
                                                    <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('هل أنت متأكد من حذف الفاتورة؟')">
                                                            <i class="fas fa-trash"></i> حذف
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>لا توجد فواتير</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($invoices->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $invoices->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // تحديث تلقائي لحالة الفواتير المتأخرة
    function updateOverdueStatus() {
        $.get('{{ route("invoices.update-overdue") }}', function(data) {
            if (data.updated > 0) {
                console.log('تم تحديث ' + data.updated + ' فاتورة متأخرة');
                // يمكن إضافة تحديث للصفحة هنا إذا لزم الأمر
            }
        });
    }
    
    // تشغيل التحديث عند تحميل الصفحة
    updateOverdueStatus();
});
</script>
@endpush