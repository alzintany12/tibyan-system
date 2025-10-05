<?php $__env->startSection('title', 'لوحة التحكم - نظام تبيان'); ?>

<?php $__env->startSection('content'); ?>
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
                    <h3 class="mb-0"><?php echo e($totalCases); ?></h3>
                    <p class="mb-0">إجمالي القضايا</p>
                </div>
                <div class="ms-3">
                    <i class="fas fa-briefcase fa-2x"></i>
                </div>
            </div>
            <div class="mt-2">
                <small>نشطة: <?php echo e($activeCases); ?> | مكتملة: <?php echo e($completedCases); ?></small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card success">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0"><?php echo e($todayHearings); ?></h3>
                    <p class="mb-0">جلسات اليوم</p>
                </div>
                <div class="ms-3">
                    <i class="fas fa-calendar-day fa-2x"></i>
                </div>
            </div>
            <div class="mt-2">
                <small>قادمة: <?php echo e($upcomingHearings); ?> | فائتة: <?php echo e($missedHearings); ?></small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card warning">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0"><?php echo e(number_format($totalRevenue, 2)); ?></h3>
                    <p class="mb-0">إجمالي الإيرادات</p>
                </div>
                <div class="ms-3">
                    <i class="fas fa-money-bill-wave fa-2x"></i>
                </div>
            </div>
            <div class="mt-2">
                <small>معلقة: <?php echo e(number_format($pendingRevenue, 2)); ?></small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card danger">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0"><?php echo e($overdueInvoices); ?></h3>
                    <p class="mb-0">فواتير متأخرة</p>
                </div>
                <div class="ms-3">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
            </div>
            <div class="mt-2">
                <small>بقيمة: <?php echo e(number_format($overdueRevenue, 2)); ?></small>
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
                <a href="<?php echo e(route('hearings.index')); ?>" class="btn btn-sm btn-light">
                    عرض الكل
                </a>
            </div>
            <div class="card-body">
                <?php $__empty_1 = true; $__currentLoopData = $upcomingHearingsData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hearing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex align-items-center p-3 border-bottom">
                        <div class="flex-grow-1">
                            <h6 class="mb-1"><?php echo e($hearing->case->case_number); ?></h6>
                            <p class="mb-1 text-muted"><?php echo e($hearing->case->client_name); ?></p>
                            <small class="text-muted">
                                <i class="fas fa-clock ms-1"></i>
                                <?php echo e($hearing->hearing_date->format('Y-m-d')); ?> - <?php echo e($hearing->hearing_time); ?>

                            </small>
                        </div>
                        <div class="ms-3">
                            <span class="badge bg-primary"><?php echo e($hearing->court_name); ?></span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد جلسات قادمة</p>
                    </div>
                <?php endif; ?>
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
                <a href="<?php echo e(route('cases.index')); ?>" class="btn btn-sm btn-light">
                    عرض الكل
                </a>
            </div>
            <div class="card-body">
                <?php $__empty_1 = true; $__currentLoopData = $recentCases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex align-items-center p-3 border-bottom">
                        <div class="flex-grow-1">
                            <h6 class="mb-1"><?php echo e($case->case_number); ?></h6>
                            <p class="mb-1 text-muted"><?php echo e($case->client_name); ?></p>
                            <small class="text-muted">
                                <i class="fas fa-calendar ms-1"></i>
                                <?php echo e($case->created_at->format('Y-m-d')); ?>

                            </small>
                        </div>
                        <div class="ms-3">
                            <span class="badge bg-success"><?php echo e(\App\Models\CaseModel::getCaseTypes()[$case->case_type]); ?></span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد قضايا حديثة</p>
                    </div>
                <?php endif; ?>
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
                <a href="<?php echo e(route('invoices.index', ['status' => 'overdue'])); ?>" class="btn btn-sm btn-light">
                    عرض الكل
                </a>
            </div>
            <div class="card-body">
                <?php $__empty_1 = true; $__currentLoopData = $overdueInvoicesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex align-items-center p-3 border-bottom">
                        <div class="flex-grow-1">
                            <h6 class="mb-1"><?php echo e($invoice->invoice_number); ?></h6>
                            <p class="mb-1 text-muted"><?php echo e($invoice->client_name); ?></p>
                            <small class="text-danger">
                                <i class="fas fa-clock ms-1"></i>
                                متأخر <?php echo e($invoice->days_overdue); ?> يوم
                            </small>
                        </div>
                        <div class="ms-3">
                            <span class="badge bg-danger"><?php echo e(number_format($invoice->total_amount, 2)); ?></span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p class="text-muted">لا توجد فواتير متأخرة</p>
                    </div>
                <?php endif; ?>
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
                <?php if($weeklyHearings->count() > 0): ?>
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
                                <?php $__currentLoopData = $weeklyHearings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $dayHearings): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $__currentLoopData = $dayHearings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hearing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($hearing->hearing_date->format('Y-m-d')); ?></td>
                                            <td><?php echo e($hearing->hearing_time); ?></td>
                                            <td><?php echo e($hearing->case->case_number); ?></td>
                                            <td><?php echo e($hearing->case->client_name); ?></td>
                                            <td><?php echo e($hearing->court_name); ?></td>
                                            <td>
                                                <?php
                                                    $statusColors = [
                                                        'scheduled' => 'primary',
                                                        'completed' => 'success',
                                                        'postponed' => 'warning',
                                                        'cancelled' => 'danger',
                                                        'missed' => 'secondary'
                                                    ];
                                                ?>
                                                <span class="badge bg-<?php echo e($statusColors[$hearing->status] ?? 'primary'); ?>">
                                                    <?php echo e(\App\Models\Hearing::getStatuses()[$hearing->status]); ?>

                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد جلسات مجدولة لهذا الأسبوع</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // رسم بياني لتوزيع القضايا
    const ctx = document.getElementById('caseTypesChart').getContext('2d');
    const caseTypesData = <?php echo json_encode($casesByType, 15, 512) ?>;
    
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tibyan-system\resources\views/dashboard/index.blade.php ENDPATH**/ ?>