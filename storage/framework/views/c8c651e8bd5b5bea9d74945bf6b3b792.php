<?php $__env->startSection('title', 'إدارة القضايا - نظام تبيان'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-briefcase ms-2"></i>
        إدارة القضايا
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="<?php echo e(route('cases.create')); ?>" class="btn btn-primary">
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
                <h3 class="text-primary"><?php echo e($statistics['total']); ?></h3>
                <p class="card-text">إجمالي القضايا</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-success"><?php echo e($statistics['active']); ?></h3>
                <p class="card-text">القضايا النشطة</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-info"><?php echo e($statistics['completed']); ?></h3>
                <p class="card-text">القضايا المكتملة</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-warning"><?php echo e($statistics['postponed']); ?></h3>
                <p class="card-text">القضايا المؤجلة</p>
            </div>
        </div>
    </div>
</div>

<!-- فلاتر البحث -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('cases.index')); ?>" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">البحث</label>
                <input type="text" class="form-control" name="search" value="<?php echo e(request('search')); ?>" 
                       placeholder="رقم القضية، اسم العميل، أو ملخص القضية">
            </div>
            <div class="col-md-3">
                <label class="form-label">الحالة</label>
                <select class="form-select" name="status">
                    <option value="">جميع الحالات</option>
                    <?php $__currentLoopData = \App\Models\CaseModel::getStatuses(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(request('status') == $key ? 'selected' : ''); ?>>
                            <?php echo e($status); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">نوع القضية</label>
                <select class="form-select" name="case_type">
                    <option value="">جميع الأنواع</option>
                    <?php $__currentLoopData = \App\Models\CaseModel::getCaseTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(request('case_type') == $key ? 'selected' : ''); ?>>
                            <?php echo e($type); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
        <small class="text-muted">إجمالي <?php echo e($cases->total()); ?> قضية</small>
    </div>
    <div class="card-body">
        <?php if($cases->count() > 0): ?>
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
                        <?php $__currentLoopData = $cases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($case->case_number); ?></strong>
                                </td>
                                <td>
                                    <div>
                                        <strong><?php echo e($case->client_name); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo e($case->client_phone); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php echo e(\App\Models\CaseModel::getCaseTypes()[$case->case_type]); ?>

                                    </span>
                                </td>
                                <td>
                                    <?php
                                        $statusColors = [
                                            'active' => 'success',
                                            'completed' => 'primary',
                                            'postponed' => 'warning',
                                            'cancelled' => 'danger'
                                        ];
                                    ?>
                                    <span class="badge bg-<?php echo e($statusColors[$case->status] ?? 'secondary'); ?>">
                                        <?php echo e(\App\Models\CaseModel::getStatuses()[$case->status]); ?>

                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <strong><?php echo e(number_format($case->total_fees, 2)); ?></strong>
                                        <br>
                                        <small class="text-success">مدفوع: <?php echo e(number_format($case->fees_received, 2)); ?></small>
                                        <br>
                                        <small class="text-danger">متبقي: <?php echo e(number_format($case->fees_pending, 2)); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <?php if($case->next_hearing): ?>
                                        <div>
                                            <strong><?php echo e($case->next_hearing->hearing_date->format('Y-m-d')); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo e($case->next_hearing->hearing_time); ?></small>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">لا توجد جلسة</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($case->start_date->format('Y-m-d')); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('cases.show', $case)); ?>" class="btn btn-sm btn-outline-primary" 
                                           data-bs-toggle="tooltip" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('cases.edit', $case)); ?>" class="btn btn-sm btn-outline-warning"
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
                                                    <a class="dropdown-item" href="<?php echo e(route('hearings.create', ['case_id' => $case->id])); ?>">
                                                        <i class="fas fa-calendar-plus ms-2"></i>
                                                        إضافة جلسة
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="<?php echo e(route('cases.create-invoice', $case)); ?>" method="POST" class="d-inline">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-file-invoice ms-2"></i>
                                                            إنشاء فاتورة
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="<?php echo e(route('cases.destroy', $case)); ?>" method="POST" 
                                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه القضية؟')" class="d-inline">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
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
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                <?php echo e($cases->appends(request()->query())->links()); ?>

            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">لا توجد قضايا</h5>
                <p class="text-muted">لم يتم العثور على أي قضايا تطابق معايير البحث</p>
                <a href="<?php echo e(route('cases.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> إضافة قضية جديدة
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // تفعيل الـ tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tibyan-system\resources\views/cases/index.blade.php ENDPATH**/ ?>