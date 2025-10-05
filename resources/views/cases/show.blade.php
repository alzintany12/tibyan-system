@extends('layouts.app')

@section('title', 'تفاصيل القضية - نظام تبيان')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-briefcase ms-2"></i>
        تفاصيل القضية: {{ $case->case_number }}
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('cases.edit', $case) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="{{ route('hearings.create', ['case_id' => $case->id]) }}" class="btn btn-success">
                <i class="fas fa-calendar-plus"></i> إضافة جلسة
            </a>
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
                <i class="fas fa-file-invoice"></i> إنشاء فاتورة
            </button>
        </div>
        <a href="{{ route('cases.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right"></i> العودة للقائمة
        </a>
    </div>
</div>

<div class="row">
    <!-- معلومات القضية الأساسية -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle ms-2"></i>
                    معلومات القضية
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>رقم القضية:</strong></td>
                                <td>{{ $case->case_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>نوع القضية:</strong></td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ \App\Models\CaseModel::getCaseTypes()[$case->case_type] }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>الحالة:</strong></td>
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
                            </tr>
                            <tr>
                                <td><strong>تاريخ البدء:</strong></td>
                                <td>{{ $case->start_date->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <td><strong>التاريخ المتوقع للانتهاء:</strong></td>
                                <td>{{ $case->expected_end_date ? $case->expected_end_date->format('Y-m-d') : 'غير محدد' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>المحكمة:</strong></td>
                                <td>{{ $case->court_name ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <td><strong>الخصم:</strong></td>
                                <td>{{ $case->opponent_name ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <td><strong>تاريخ الإنشاء:</strong></td>
                                <td>{{ $case->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>آخر تحديث:</strong></td>
                                <td>{{ $case->updated_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>منشئ القضية:</strong></td>
                                <td>{{ $case->created_by ?? 'غير محدد' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6><strong>ملخص القضية:</strong></h6>
                    <p class="text-muted">{{ $case->case_summary }}</p>
                </div>
                
                @if($case->notes)
                    <div class="mt-3">
                        <h6><strong>ملاحظات:</strong></h6>
                        <p class="text-muted">{{ $case->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- الشريط الجانبي -->
    <div class="col-lg-4">
        <!-- معلومات العميل -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-user ms-2"></i>
                    معلومات العميل
                </h6>
            </div>
            <div class="card-body">
                <p><strong>الاسم:</strong> {{ $case->client_name }}</p>
                <p><strong>رقم الهوية:</strong> {{ $case->client_id }}</p>
                <p><strong>الهاتف:</strong> 
                    <a href="tel:{{ $case->client_phone }}" class="text-decoration-none">
                        {{ $case->client_phone }}
                    </a>
                </p>
            </div>
        </div>
        
        <!-- المعلومات المالية -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-money-bill-wave ms-2"></i>
                    المعلومات المالية
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12 mb-3">
                        <h4 class="text-primary">{{ number_format($case->total_fees, 2) }}</h4>
                        <small class="text-muted">إجمالي الأتعاب</small>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-6">
                        <h5 class="text-success">{{ number_format($case->fees_received, 2) }}</h5>
                        <small class="text-muted">مدفوع</small>
                    </div>
                    <div class="col-6">
                        <h5 class="text-danger">{{ number_format($case->fees_pending, 2) }}</h5>
                        <small class="text-muted">متبقي</small>
                    </div>
                </div>
                
                @if($case->fees_pending > 0)
                    <div class="mt-3">
                        <button type="button" class="btn btn-warning btn-sm w-100" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
                            <i class="fas fa-file-invoice"></i> إنشاء فاتورة للمتبقي
                        </button>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- الجلسة القادمة -->
        @if($case->next_hearing)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar-check ms-2"></i>
                        الجلسة القادمة
                    </h6>
                </div>
                <div class="card-body">
                    <p><strong>التاريخ:</strong> {{ $case->next_hearing->hearing_date->format('Y-m-d') }}</p>
                    <p><strong>الوقت:</strong> {{ $case->next_hearing->hearing_time }}</p>
                    <p><strong>المحكمة:</strong> {{ $case->next_hearing->court_name }}</p>
                    <p><strong>النوع:</strong> 
                        <span class="badge bg-info">
                            {{ \App\Models\Hearing::getHearingTypes()[$case->next_hearing->hearing_type] }}
                        </span>
                    </p>
                    <div class="mt-3">
                        <a href="{{ route('hearings.show', $case->next_hearing) }}" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-eye"></i> عرض تفاصيل الجلسة
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- الجلسات -->
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt ms-2"></i>
                    جلسات القضية
                </h5>
                <a href="{{ route('hearings.create', ['case_id' => $case->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> إضافة جلسة
                </a>
            </div>
            <div class="card-body">
                @if($case->hearings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>التاريخ والوقت</th>
                                    <th>المحكمة</th>
                                    <th>نوع الجلسة</th>
                                    <th>الحالة</th>
                                    <th>النتيجة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($case->hearings as $hearing)
                                    <tr>
                                        <td>
                                            <strong>{{ $hearing->hearing_date->format('Y-m-d') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $hearing->hearing_time }}</small>
                                        </td>
                                        <td>{{ $hearing->court_name }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ \App\Models\Hearing::getHearingTypes()[$hearing->hearing_type] }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'scheduled' => 'primary',
                                                    'completed' => 'success',
                                                    'postponed' => 'warning',
                                                    'cancelled' => 'danger',
                                                    'missed' => 'secondary'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$hearing->status] ?? 'primary' }}">
                                                {{ \App\Models\Hearing::getStatuses()[$hearing->status] }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($hearing->result)
                                                @php
                                                    $resultColors = [
                                                        'won' => 'success',
                                                        'lost' => 'danger',
                                                        'settlement' => 'info',
                                                        'postponed' => 'warning',
                                                        'referral' => 'secondary',
                                                        'pending' => 'light'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $resultColors[$hearing->result] ?? 'light' }}">
                                                    {{ \App\Models\Hearing::getResults()[$hearing->result] }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('hearings.show', $hearing) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($hearing->canBeModified())
                                                <a href="{{ route('hearings.edit', $hearing) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد جلسات لهذه القضية</p>
                        <a href="{{ route('hearings.create', ['case_id' => $case->id]) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> إضافة جلسة جديدة
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- الفواتير -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-file-invoice-dollar ms-2"></i>
                    فواتير القضية
                </h5>
                <form action="{{ route('cases.create-invoice', $case) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> إنشاء فاتورة
                    </button>
                </form>
            </div>
            <div class="card-body">
                @if($case->invoices->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>رقم الفاتورة</th>
                                    <th>تاريخ الفاتورة</th>
                                    <th>تاريخ الاستحقاق</th>
                                    <th>المبلغ</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($case->invoices as $invoice)
                                    <tr>
                                        <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                        <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                        <td>{{ $invoice->due_date->format('Y-m-d') }}</td>
                                        <td><strong>{{ number_format($invoice->total_amount, 2) }}</strong></td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'draft' => 'secondary',
                                                    'sent' => 'primary',
                                                    'paid' => 'success',
                                                    'overdue' => 'danger',
                                                    'partially_paid' => 'warning',
                                                    'cancelled' => 'dark'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$invoice->status] ?? 'secondary' }}">
                                                {{ \App\Models\Invoice::getStatuses()[$invoice->status] }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('invoices.print', $invoice) }}" class="btn btn-sm btn-outline-info" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد فواتير لهذه القضية</p>
                        <form action="{{ route('cases.create-invoice', $case) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> إنشاء فاتورة جديدة
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal إنشاء فاتورة -->
<div class="modal fade" id="createInvoiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إنشاء فاتورة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('cases.create-invoice', $case) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="amount" class="form-label">المبلغ <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="amount" id="amount" 
                               value="{{ $case->fees_pending > 0 ? $case->fees_pending : $case->total_fees }}" 
                               step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">وصف الفاتورة</label>
                        <textarea class="form-control" name="description" id="description" rows="3"
                                  placeholder="أتعاب قضية: {{ $case->case_number }}">{{ "أتعاب قضية: " . $case->case_number }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="due_days" class="form-label">تاريخ الاستحقاق (بالأيام)</label>
                        <select class="form-select" name="due_days" id="due_days">
                            <option value="7">7 أيام</option>
                            <option value="15">15 يوم</option>
                            <option value="30" selected>30 يوم</option>
                            <option value="60">60 يوم</option>
                            <option value="90">90 يوم</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إنشاء الفاتورة</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection