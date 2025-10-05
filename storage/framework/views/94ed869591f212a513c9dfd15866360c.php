<?php $__env->startSection('title', 'إدارة الجلسات - نظام تبيان'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-calendar-alt ms-2"></i>
        إدارة الجلسات
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="<?php echo e(route('hearings.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة جلسة جديدة
            </a>
            <a href="<?php echo e(route('hearings.calendar')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-calendar"></i> عرض التقويم
            </a>
        </div>
    </div>
</div>

<!-- إحصائيات سريعة -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-primary"><?php echo e($statistics['today']); ?></h4>
                <p class="card-text small">جلسات اليوم</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-success"><?php echo e($statistics['upcoming']); ?></h4>
                <p class="card-text small">الجلسات القادمة</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-info"><?php echo e($statistics['completed']); ?></h4>
                <p class="card-text small">المكتملة</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-warning"><?php echo e($statistics['postponed']); ?></h4>
                <p class="card-text small">المؤجلة</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-danger"><?php echo e($statistics['missed']); ?></h4>
                <p class="card-text small">الفائتة</p>
            </div>
        </div>
    </div>
</div>

<!-- فلاتر وبحث -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('hearings.index')); ?>" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">البحث</label>
                <input type="text" class="form-control" name="search" value="<?php echo e(request('search')); ?>" 
                       placeholder="رقم القضية، العميل، المحكمة">
            </div>
            <div class="col-md-2">
                <label class="form-label">الحالة</label>
                <select class="form-select" name="status">
                    <option value="all" <?php echo e($status == 'all' ? 'selected' : ''); ?>>جميع الحالات</option>
                    <?php $__currentLoopData = \App\Models\Hearing::getStatuses(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $statusName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e($status == $key ? 'selected' : ''); ?>>
                            <?php echo e($statusName); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">الفترة</label>
                <select class="form-select" name="period">
                    <option value="upcoming" <?php echo e($period == 'upcoming' ? 'selected' : ''); ?>>القادمة</option>
                    <option value="today" <?php echo e($period == 'today' ? 'selected' : ''); ?>>اليوم</option>
                    <option value="this_week" <?php echo e($period == 'this_week' ? 'selected' : ''); ?>>هذا الأسبوع</option>
                    <option value="this_month" <?php echo e($period == 'this_month' ? 'selected' : ''); ?>>هذا الشهر</option>
                    <option value="past" <?php echo e($period == 'past' ? 'selected' : ''); ?>>السابقة</option>
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
            <div class="col-md-3">
                <label class="form-label">إجراءات سريعة</label>
                <div class="d-grid">
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="updateOverdueHearings()">
                            <i class="fas fa-clock"></i> تحديث الفائتة
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="sendReminders()">
                            <i class="fas fa-bell"></i> إرسال تذكيرات
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- جدول الجلسات -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">قائمة الجلسات</h5>
        <small class="text-muted">إجمالي <?php echo e($hearings->total()); ?> جلسة</small>
    </div>
    <div class="card-body">
        <?php if($hearings->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>التاريخ والوقت</th>
                            <th>رقم القضية</th>
                            <th>العميل</th>
                            <th>المحكمة</th>
                            <th>نوع الجلسة</th>
                            <th>الحالة</th>
                            <th>النتيجة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $hearings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hearing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="<?php echo e($hearing->hearing_date->isToday() ? 'table-warning' : ''); ?>">
                                <td>
                                    <div>
                                        <strong><?php echo e($hearing->hearing_date->format('Y-m-d')); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo e($hearing->hearing_time); ?></small>
                                        <?php if($hearing->hearing_date->isToday()): ?>
                                            <span class="badge bg-warning ms-1">اليوم</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('cases.show', $hearing->case)); ?>" class="text-decoration-none">
                                        <strong><?php echo e($hearing->case->case_number); ?></strong>
                                    </a>
                                </td>
                                <td><?php echo e($hearing->case->client_name); ?></td>
                                <td>
                                    <div>
                                        <strong><?php echo e($hearing->court_name); ?></strong>
                                        <?php if($hearing->courtroom): ?>
                                            <br>
                                            <small class="text-muted"><?php echo e($hearing->courtroom); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
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
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('hearings.show', $hearing)); ?>" 
                                           class="btn btn-sm btn-outline-primary"
                                           data-bs-toggle="tooltip" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <?php if($hearing->canBeModified()): ?>
                                            <a href="<?php echo e(route('hearings.edit', $hearing)); ?>" 
                                               class="btn btn-sm btn-outline-warning"
                                               data-bs-toggle="tooltip" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <?php if($hearing->status === 'scheduled'): ?>
                                                    <li>
                                                        <form action="<?php echo e(route('hearings.complete', $hearing)); ?>" method="POST" class="d-inline">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="dropdown-item text-success">
                                                                <i class="fas fa-check ms-2"></i>
                                                                تحديد كمكتملة
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <button type="button" class="dropdown-item text-warning" 
                                                                onclick="showPostponeModal(<?php echo e($hearing->id); ?>)">
                                                            <i class="fas fa-clock ms-2"></i>
                                                            تأجيل الجلسة
                                                        </button>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                <?php endif; ?>
                                                
                                                <li>
                                                    <a class="dropdown-item" href="<?php echo e(route('hearings.calendar')); ?>?hearing=<?php echo e($hearing->id); ?>">
                                                        <i class="fas fa-calendar ms-2"></i>
                                                        عرض في التقويم
                                                    </a>
                                                </li>
                                                
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="<?php echo e(route('hearings.destroy', $hearing)); ?>" method="POST" 
                                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الجلسة؟')" class="d-inline">
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
                <?php echo e($hearings->appends(request()->query())->links()); ?>

            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">لا توجد جلسات</h5>
                <p class="text-muted">لم يتم العثور على أي جلسات تطابق معايير البحث</p>
                <a href="<?php echo e(route('hearings.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> إضافة جلسة جديدة
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal لتأجيل الجلسة -->
<div class="modal fade" id="postponeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأجيل الجلسة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="postponeForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_date" class="form-label">التاريخ الجديد</label>
                        <input type="date" class="form-control" name="new_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_time" class="form-label">الوقت الجديد</label>
                        <input type="time" class="form-control" name="new_time" required>
                    </div>
                    <div class="mb-3">
                        <label for="postpone_reason" class="form-label">سبب التأجيل</label>
                        <textarea class="form-control" name="postpone_reason" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning">تأجيل الجلسة</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function showPostponeModal(hearingId) {
        const form = document.getElementById('postponeForm');
        form.action = '/hearings/' + hearingId + '/postpone';
        new bootstrap.Modal(document.getElementById('postponeModal')).show();
    }
    
    function updateOverdueHearings() {
        // تحديث الجلسات الفائتة
        fetch('/hearings/api/update-missed', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert('تم تحديث ' + data.updated + ' جلسة فائتة');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء التحديث');
        });
    }
    
    function sendReminders() {
        // إرسال تذكيرات الجلسات
        fetch('/hearings/api/send-reminders', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert('تم إرسال ' + data.sent + ' تذكير');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء إرسال التذكيرات');
        });
    }
    
    // تفعيل الـ tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tibyan-system\resources\views/hearings/index.blade.php ENDPATH**/ ?>