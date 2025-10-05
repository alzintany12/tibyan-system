<?php $__env->startSection('title', 'تفاصيل القضية - نظام تبيان'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-briefcase ms-2"></i>
        تفاصيل القضية: <?php echo e($case->case_number); ?>

    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="<?php echo e(route('cases.edit', $case)); ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="<?php echo e(route('hearings.create', ['case_id' => $case->id])); ?>" class="btn btn-success">
                <i class="fas fa-calendar-plus"></i> إضافة جلسة
            </a>
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
                <i class="fas fa-file-invoice"></i> إنشاء فاتورة
            </button>
        </div>
        <a href="<?php echo e(route('cases.index')); ?>" class="btn btn-outline-secondary">
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
                                <td><?php echo e($case->case_number); ?></td>
                            </tr>
                            <tr>
                                <td><strong>نوع القضية:</strong></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php echo e(\App\Models\CaseModel::getCaseTypes()[$case->case_type]); ?>

                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>الحالة:</strong></td>
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
                            </tr>
                            <tr>
                                <td><strong>تاريخ البدء:</strong></td>
                                <td><?php echo e($case->start_date->format('Y-m-d')); ?></td>
                            </tr>
                            <tr>
                                <td><strong>التاريخ المتوقع للانتهاء:</strong></td>
                                <td><?php echo e($case->expected_end_date ? $case->expected_end_date->format('Y-m-d') : 'غير محدد'); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>المحكمة:</strong></td>
                                <td><?php echo e($case->court_name ?? 'غير محدد'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>الخصم:</strong></td>
                                <td><?php echo e($case->opponent_name ?? 'غير محدد'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>تاريخ الإنشاء:</strong></td>
                                <td><?php echo e($case->created_at->format('Y-m-d H:i')); ?></td>
                            </tr>
                            <tr>
                                <td><strong>آخر تحديث:</strong></td>
                                <td><?php echo e($case->updated_at->format('Y-m-d H:i')); ?></td>
                            </tr>
                            <tr>
                                <td><strong>منشئ القضية:</strong></td>
                                <td><?php echo e($case->created_by ?? 'غير محدد'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6><strong>ملخص القضية:</strong></h6>
                    <p class="text-muted"><?php echo e($case->case_summary); ?></p>
                </div>
                
                <?php if($case->notes): ?>
                    <div class="mt-3">
                        <h6><strong>ملاحظات:</strong></h6>
                        <p class="text-muted"><?php echo e($case->notes); ?></p>
                    </div>
                <?php endif; ?>
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
                <p><strong>الاسم:</strong> <?php echo e($case->client_name); ?></p>
                <p><strong>رقم الهوية:</strong> <?php echo e($case->client_id); ?></p>
                <p><strong>الهاتف:</strong> 
                    <a href="tel:<?php echo e($case->client_phone); ?>" class="text-decoration-none">
                        <?php echo e($case->client_phone); ?>

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
                        <h4 class="text-primary"><?php echo e(number_format($case->total_fees, 2)); ?></h4>
                        <small class="text-muted">إجمالي الأتعاب</small>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-6">
                        <h5 class="text-success"><?php echo e(number_format($case->fees_received, 2)); ?></h5>
                        <small class="text-muted">مدفوع</small>
                    </div>
                    <div class="col-6">
                        <h5 class="text-danger"><?php echo e(number_format($case->fees_pending, 2)); ?></h5>
                        <small class="text-muted">متبقي</small>
                    </div>
                </div>
                
                <?php if($case->fees_pending > 0): ?>
                    <div class="mt-3">
                        <button type="button" class="btn btn-warning btn-sm w-100" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
                            <i class="fas fa-file-invoice"></i> إنشاء فاتورة للمتبقي
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- الجلسة القادمة -->
        <?php if($case->next_hearing): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar-check ms-2"></i>
                        الجلسة القادمة
                    </h6>
                </div>
                <div class="card-body">
                    <p><strong>التاريخ:</strong> <?php echo e($case->next_hearing->hearing_date->format('Y-m-d')); ?></p>
                    <p><strong>الوقت:</strong> <?php echo e($case->next_hearing->hearing_time); ?></p>
                    <p><strong>المحكمة:</strong> <?php echo e($case->next_hearing->court_name); ?></p>
                    <p><strong>النوع:</strong> 
                        <span class="badge bg-info">
                            <?php echo e(\App\Models\Hearing::getHearingTypes()[$case->next_hearing->hearing_type]); ?>

                        </span>
                    </p>
                    <div class="mt-3">
                        <a href="<?php echo e(route('hearings.show', $case->next_hearing)); ?>" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-eye"></i> عرض تفاصيل الجلسة
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
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
                <a href="<?php echo e(route('hearings.create', ['case_id' => $case->id])); ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> إضافة جلسة
                </a>
            </div>
            <div class="card-body">
                <?php if($case->hearings->count() > 0): ?>
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
                                <?php $__currentLoopData = $case->hearings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hearing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo e($hearing->hearing_date->format('Y-m-d')); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo e($hearing->hearing_time); ?></small>
                                        </td>
                                        <td><?php echo e($hearing->court_name); ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo e(\App\Models\Hearing::getHearingTypes()[$hearing->hearing_type]); ?>

                                            </span>
                                        </td>
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
                                        <td>
                                            <?php if($hearing->result): ?>
                                                <?php
                                                    $resultColors = [
                                                        'won' => 'success',
                                                        'lost' => 'danger',
                                                        'settlement' => 'info',
                                                        'postponed' => 'warning',
                                                        'referral' => 'secondary',
                                                        'pending' => 'light'
                                                    ];
                                                ?>
                                                <span class="badge bg-<?php echo e($resultColors[$hearing->result] ?? 'light'); ?>">
                                                    <?php echo e(\App\Models\Hearing::getResults()[$hearing->result]); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('hearings.show', $hearing)); ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if($hearing->canBeModified()): ?>
                                                <a href="<?php echo e(route('hearings.edit', $hearing)); ?>" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد جلسات لهذه القضية</p>
                        <a href="<?php echo e(route('hearings.create', ['case_id' => $case->id])); ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> إضافة جلسة جديدة
                        </a>
                    </div>
                <?php endif; ?>
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
                <form action="<?php echo e(route('cases.create-invoice', $case)); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> إنشاء فاتورة
                    </button>
                </form>
            </div>
            <div class="card-body">
                <?php if($case->invoices->count() > 0): ?>
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
                                <?php $__currentLoopData = $case->invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><strong><?php echo e($invoice->invoice_number); ?></strong></td>
                                        <td><?php echo e($invoice->invoice_date->format('Y-m-d')); ?></td>
                                        <td><?php echo e($invoice->due_date->format('Y-m-d')); ?></td>
                                        <td><strong><?php echo e(number_format($invoice->total_amount, 2)); ?></strong></td>
                                        <td>
                                            <?php
                                                $statusColors = [
                                                    'draft' => 'secondary',
                                                    'sent' => 'primary',
                                                    'paid' => 'success',
                                                    'overdue' => 'danger',
                                                    'partially_paid' => 'warning',
                                                    'cancelled' => 'dark'
                                                ];
                                            ?>
                                            <span class="badge bg-<?php echo e($statusColors[$invoice->status] ?? 'secondary'); ?>">
                                                <?php echo e(\App\Models\Invoice::getStatuses()[$invoice->status]); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('invoices.show', $invoice)); ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('invoices.print', $invoice)); ?>" class="btn btn-sm btn-outline-info" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد فواتير لهذه القضية</p>
                        <form action="<?php echo e(route('cases.create-invoice', $case)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> إنشاء فاتورة جديدة
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
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
            <form action="<?php echo e(route('cases.create-invoice', $case)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="amount" class="form-label">المبلغ <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="amount" id="amount" 
                               value="<?php echo e($case->fees_pending > 0 ? $case->fees_pending : $case->total_fees); ?>" 
                               step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">وصف الفاتورة</label>
                        <textarea class="form-control" name="description" id="description" rows="3"
                                  placeholder="أتعاب قضية: <?php echo e($case->case_number); ?>"><?php echo e("أتعاب قضية: " . $case->case_number); ?></textarea>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tibyan-system\resources\views/cases/show.blade.php ENDPATH**/ ?>