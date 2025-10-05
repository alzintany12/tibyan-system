@extends('layouts.app')

@section('page-title', 'التقارير والإحصائيات')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">التقارير</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-chart-bar"></i>
                    التقارير والإحصائيات
                </h4>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">
                    اختر نوع التقرير الذي تريد إنشاؤه من الخيارات التالية:
                </p>

                <div class="row">
                    <!-- تقرير القضايا -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 border-left-primary">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <i class="fas fa-gavel fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title">تقرير القضايا</h5>
                                        <p class="card-text text-muted">
                                            إحصائيات شاملة عن القضايا وحالاتها ونتائجها
                                        </p>
                                        <a href="{{ route('reports.cases') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-chart-line"></i> عرض التقرير
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- التقرير المالي -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 border-left-success">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <i class="fas fa-dollar-sign fa-2x text-success"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title">التقرير المالي</h5>
                                        <p class="card-text text-muted">
                                            الإيرادات والمصروفات والأرباح والفواتير
                                        </p>
                                        <a href="{{ route('reports.financial') }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-chart-pie"></i> عرض التقرير
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تقرير الإنتاجية -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 border-left-info">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <i class="fas fa-tasks fa-2x text-info"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title">تقرير الإنتاجية</h5>
                                        <p class="card-text text-muted">
                                            أداء الموظفين وإنجاز المهام والأنشطة
                                        </p>
                                        <a href="{{ route('reports.productivity') }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-chart-area"></i> عرض التقرير
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تقرير العملاء -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 border-left-warning">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <i class="fas fa-users fa-2x text-warning"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title">تقرير العملاء</h5>
                                        <p class="card-text text-muted">
                                            إحصائيات العملاء وقضاياهم ومعاملاتهم
                                        </p>
                                        <a href="{{ route('reports.clients') }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-user-chart"></i> عرض التقرير
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تقرير مخصص -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 border-left-secondary">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <i class="fas fa-cog fa-2x text-secondary"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title">تقرير مخصص</h5>
                                        <p class="card-text text-muted">
                                            إنشاء تقرير مخصص حسب معايير محددة
                                        </p>
                                        <button type="button" class="btn btn-secondary btn-sm" 
                                                data-toggle="modal" data-target="#customReportModal">
                                            <i class="fas fa-plus"></i> إنشاء تقرير
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- إحصائيات سريعة -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 border-left-danger">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <i class="fas fa-tachometer-alt fa-2x text-danger"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title">إحصائيات سريعة</h5>
                                        <p class="card-text text-muted">
                                            ملخص سريع للأرقام والإحصائيات المهمة
                                        </p>
                                        <button type="button" class="btn btn-danger btn-sm" id="quickStatsBtn">
                                            <i class="fas fa-bolt"></i> عرض الإحصائيات
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الإحصائيات السريعة -->
                <div id="quickStatsSection" style="display: none;">
                    <hr>
                    <h5 class="mb-3">الإحصائيات السريعة</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $quickStats['total_cases'] ?? 0 }}</h3>
                                    <small>إجمالي القضايا</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $quickStats['total_clients'] ?? 0 }}</h3>
                                    <small>إجمالي العملاء</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ number_format($quickStats['total_revenue'] ?? 0, 2) }}</h3>
                                    <small>إجمالي الإيرادات (ر.س)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $quickStats['pending_tasks'] ?? 0 }}</h3>
                                    <small>المهام المعلقة</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal للتقرير المخصص -->
<div class="modal fade" id="customReportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إنشاء تقرير مخصص</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('reports.generate') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>نوع التقرير</label>
                                <select name="report_type" class="form-control" required>
                                    <option value="">اختر نوع التقرير</option>
                                    <option value="cases">القضايا</option>
                                    <option value="financial">المالي</option>
                                    <option value="clients">العملاء</option>
                                    <option value="productivity">الإنتاجية</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>صيغة التقرير</label>
                                <select name="format" class="form-control" required>
                                    <option value="pdf">PDF</option>
                                    <option value="excel">Excel</option>
                                    <option value="html">HTML</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>من تاريخ</label>
                                <input type="date" name="date_from" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>إلى تاريخ</label>
                                <input type="date" name="date_to" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>معايير إضافية</label>
                        <div class="form-check">
                            <input type="checkbox" name="include_charts" class="form-check-input" id="includeCharts">
                            <label class="form-check-label" for="includeCharts">
                                تضمين الرسوم البيانية
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="include_details" class="form-check-input" id="includeDetails">
                            <label class="form-check-label" for="includeDetails">
                                تضمين التفاصيل الكاملة
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3" 
                                placeholder="أي ملاحظات خاصة بالتقرير..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-alt"></i> إنشاء التقرير
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.border-left-primary {
    border-left: 4px solid #007bff !important;
}
.border-left-success {
    border-left: 4px solid #28a745 !important;
}
.border-left-info {
    border-left: 4px solid #17a2b8 !important;
}
.border-left-warning {
    border-left: 4px solid #ffc107 !important;
}
.border-left-secondary {
    border-left: 4px solid #6c757d !important;
}
.border-left-danger {
    border-left: 4px solid #dc3545 !important;
}
.card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // إظهار/إخفاء الإحصائيات السريعة
    $('#quickStatsBtn').click(function() {
        $('#quickStatsSection').slideToggle();
        var icon = $(this).find('i');
        if (icon.hasClass('fa-bolt')) {
            icon.removeClass('fa-bolt').addClass('fa-eye-slash');
            $(this).html('<i class="fas fa-eye-slash"></i> إخفاء الإحصائيات');
        } else {
            icon.removeClass('fa-eye-slash').addClass('fa-bolt');
            $(this).html('<i class="fas fa-bolt"></i> عرض الإحصائيات');
        }
    });

    // تحديث التاريخ الافتراضي
    var today = new Date();
    var firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    $('input[name="date_from"]').val(firstDay.toISOString().split('T')[0]);
    $('input[name="date_to"]').val(today.toISOString().split('T')[0]);
});
</script>
@endpush