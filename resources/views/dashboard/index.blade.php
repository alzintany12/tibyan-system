@extends('layouts.app')

@section('title', 'لوحة التحكم - نظام تبيان')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-tachometer-alt ms-2"></i>
        لوحة التحكم
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshStats()">
                <i class="fas fa-sync-alt"></i> تحديث
            </button>
        </div>
    </div>
</div>

<!-- إحصائيات سريعة -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0">{{ $totalCases }}</h3>
                    <p class="mb-0">إجمالي القضايا</p>
                </div>
                <div class="ms-3">
                    <i class="fas fa-briefcase fa-2x"></i>
                </div>
            </div>
            <div class="mt-2">
                <small>نشطة: {{ $activeCases }} | مكتملة: {{ $completedCases }}</small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card success">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0">{{ $todayHearings }}</h3>
                    <p class="mb-0">جلسات اليوم</p>
                </div>
                <div class="ms-3">
                    <i class="fas fa-calendar-day fa-2x"></i>
                </div>
            </div>
            <div class="mt-2">
                <small>قادمة: {{ $upcomingHearings }} | فائتة: {{ $missedHearings }}</small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card warning">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0">{{ number_format($totalRevenue, 2) }}</h3>
                    <p class="mb-0">إجمالي الإيرادات</p>
                </div>
                <div class="ms-3">
                    <i class="fas fa-money-bill-wave fa-2x"></i>
                </div>
            </div>
            <div class="mt-2">
                <small>معلقة: {{ number_format($pendingRevenue, 2) }}</small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card danger">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0">{{ $overdueInvoices }}</h3>
                    <p class="mb-0">فواتير متأخرة</p>
                </div>
                <div class="ms-3">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
            </div>
            <div class="mt-2">
                <small>بقيمة: {{ number_format($overdueRevenue, 2) }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- الجلسات القادمة -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt ms-2"></i>
                    الجلسات القادمة
                </h5>
                <a href="{{ route('hearings.index') }}" class="btn btn-sm btn-light">
                    عرض الكل
                </a>
            </div>
            <div class="card-body">
                @forelse($upcomingHearingsData as $hearing)
                    <div class="d-flex align-items-center p-3 border-bottom">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $hearing->case->case_number }}</h6>
                            <p class="mb-1 text-muted">{{ $hearing->case->client_name }}</p>
                            <small class="text-muted">
                                <i class="fas fa-clock ms-1"></i>
                                {{ $hearing->hearing_date->format('Y-m-d') }} - {{ $hearing->hearing_time }}
                            </small>
                        </div>
                        <div class="ms-3">
                            <span class="badge bg-primary">{{ $hearing->court_name }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد جلسات قادمة</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- القضايا الحديثة -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-briefcase ms-2"></i>
                    القضايا النشطة الحديثة
                </h5>
                <a href="{{ route('cases.index') }}" class="btn btn-sm btn-light">
                    عرض الكل
                </a>
            </div>
            <div class="card-body">
                @forelse($recentCases as $case)
                    <div class="d-flex align-items-center p-3 border-bottom">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $case->case_number }}</h6>
                            <p class="mb-1 text-muted">{{ $case->client_name }}</p>
                            <small class="text-muted">
                                <i class="fas fa-calendar ms-1"></i>
                                {{ $case->created_at->format('Y-m-d') }}
                            </small>
                        </div>
                        <div class="ms-3">
                            <span class="badge bg-success">{{ \App\Models\CaseModel::getCaseTypes()[$case->case_type] }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد قضايا حديثة</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- الفواتير المتأخرة -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle ms-2"></i>
                    الفواتير المتأخرة
                </h5>
                <a href="{{ route('invoices.index', ['status' => 'overdue']) }}" class="btn btn-sm btn-light">
                    عرض الكل
                </a>
            </div>
            <div class="card-body">
                @forelse($overdueInvoicesData as $invoice)
                    <div class="d-flex align-items-center p-3 border-bottom">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $invoice->invoice_number }}</h6>
                            <p class="mb-1 text-muted">{{ $invoice->client_name }}</p>
                            <small class="text-danger">
                                <i class="fas fa-clock ms-1"></i>
                                متأخر {{ $invoice->days_overdue }} يوم
                            </small>
                        </div>
                        <div class="ms-3">
                            <span class="badge bg-danger">{{ number_format($invoice->total_amount, 2) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p class="text-muted">لا توجد فواتير متأخرة</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- الرسم البياني -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie ms-2"></i>
                    توزيع القضايا حسب النوع
                </h5>
            </div>
            <div class="card-body">
                <canvas id="caseTypesChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- جلسات الأسبوع -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-week ms-2"></i>
                    جلسات هذا الأسبوع
                </h5>
            </div>
            <div class="card-body">
                @if($weeklyHearings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>الوقت</th>
                                    <th>رقم القضية</th>
                                    <th>العميل</th>
                                    <th>المحكمة</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($weeklyHearings as $date => $dayHearings)
                                    @foreach($dayHearings as $hearing)
                                        <tr>
                                            <td>{{ $hearing->hearing_date->format('Y-m-d') }}</td>
                                            <td>{{ $hearing->hearing_time }}</td>
                                            <td>{{ $hearing->case->case_number }}</td>
                                            <td>{{ $hearing->case->client_name }}</td>
                                            <td>{{ $hearing->court_name }}</td>
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
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد جلسات مجدولة لهذا الأسبوع</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // رسم بياني لتوزيع القضايا
    const ctx = document.getElementById('caseTypesChart').getContext('2d');
    const caseTypesData = @json($casesByType);
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(caseTypesData),
            datasets: [{
                data: Object.values(caseTypesData),
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40',
                    '#FF6384',
                    '#C9CBCF'
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
    
    // تحديث الإحصائيات
    function refreshStats() {
        // إضافة منطق تحديث الإحصائيات هنا
        location.reload();
    }
</script>
@endpush