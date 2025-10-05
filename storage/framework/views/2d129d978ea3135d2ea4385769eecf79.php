<?php $__env->startSection('title', 'إضافة جلسة جديدة - نظام تبيان'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-calendar-plus ms-2"></i>
        إضافة جلسة جديدة
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="<?php echo e(route('hearings.index')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right"></i> العودة للقائمة
            </a>
            <?php if(request('case_id')): ?>
                <a href="<?php echo e(route('cases.show', request('case_id'))); ?>" class="btn btn-outline-primary">
                    <i class="fas fa-eye"></i> عرض القضية
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if($errors->any()): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <h6>يرجى تصحيح الأخطاء التالية:</h6>
    <ul class="mb-0">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form action="<?php echo e(route('hearings.store')); ?>" method="POST">
    <?php echo csrf_field(); ?>
    
    <?php if(request('case_id')): ?>
        <input type="hidden" name="case_id" value="<?php echo e(request('case_id')); ?>">
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle ms-2"></i>
                        معلومات الجلسة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if(!request('case_id')): ?>
                        <div class="col-md-12 mb-3">
                            <label for="case_id" class="form-label">القضية <span class="text-danger">*</span></label>
                            <select class="form-select <?php $__errorArgs = ['case_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="case_id" name="case_id" required>
                                <option value="">اختر القضية</option>
                                <?php $__currentLoopData = $cases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($case->id); ?>" <?php echo e(old('case_id') == $case->id ? 'selected' : ''); ?>>
                                        <?php echo e($case->case_number); ?> - <?php echo e($case->client_name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['case_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <?php else: ?>
                        <div class="col-md-12 mb-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle ms-2"></i>
                                <strong>القضية المحددة:</strong> <?php echo e($selectedCase->case_number); ?> - <?php echo e($selectedCase->client_name); ?>

                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="col-md-6 mb-3">
                            <label for="hearing_date" class="form-label">تاريخ الجلسة <span class="text-danger">*</span></label>
                            <input type="date" class="form-control <?php $__errorArgs = ['hearing_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="hearing_date" name="hearing_date" 
                                   value="<?php echo e(old('hearing_date', $selectedDate ?? request('date'))); ?>" required>
                            <?php $__errorArgs = ['hearing_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="hearing_time" class="form-label">وقت الجلسة <span class="text-danger">*</span></label>
                            <input type="time" class="form-control <?php $__errorArgs = ['hearing_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="hearing_time" name="hearing_time" value="<?php echo e(old('hearing_time')); ?>" required>
                            <?php $__errorArgs = ['hearing_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="court_name" class="form-label">اسم المحكمة</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['court_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="court_name" name="court_name" value="<?php echo e(old('court_name')); ?>">
                            <?php $__errorArgs = ['court_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="court_room" class="form-label">رقم القاعة</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['court_room'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="court_room" name="court_room" value="<?php echo e(old('court_room')); ?>">
                            <?php $__errorArgs = ['court_room'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="hearing_type" class="form-label">نوع الجلسة</label>
                            <select class="form-select <?php $__errorArgs = ['hearing_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="hearing_type" name="hearing_type">
                                <option value="">اختر نوع الجلسة</option>
                                <option value="initial" <?php echo e(old('hearing_type') == 'initial' ? 'selected' : ''); ?>>جلسة أولى</option>
                                <option value="evidence" <?php echo e(old('hearing_type') == 'evidence' ? 'selected' : ''); ?>>جلسة بينات</option>
                                <option value="pleading" <?php echo e(old('hearing_type') == 'pleading' ? 'selected' : ''); ?>>جلسة مرافعة</option>
                                <option value="judgment" <?php echo e(old('hearing_type') == 'judgment' ? 'selected' : ''); ?>>جلسة حكم</option>
                                <option value="appeal" <?php echo e(old('hearing_type') == 'appeal' ? 'selected' : ''); ?>>جلسة استئناف</option>
                                <option value="execution" <?php echo e(old('hearing_type') == 'execution' ? 'selected' : ''); ?>>جلسة تنفيذ</option>
                                <option value="other" <?php echo e(old('hearing_type') == 'other' ? 'selected' : ''); ?>>أخرى</option>
                            </select>
                            <?php $__errorArgs = ['hearing_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">حالة الجلسة</label>
                            <select class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="status" name="status">
                                <option value="scheduled" <?php echo e(old('status') == 'scheduled' ? 'selected' : ''); ?>>مجدولة</option>
                                <option value="completed" <?php echo e(old('status') == 'completed' ? 'selected' : ''); ?>>مكتملة</option>
                                <option value="postponed" <?php echo e(old('status') == 'postponed' ? 'selected' : ''); ?>>مؤجلة</option>
                                <option value="cancelled" <?php echo e(old('status') == 'cancelled' ? 'selected' : ''); ?>>ملغية</option>
                            </select>
                            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="title" class="form-label">عنوان الجلسة</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="title" name="title" value="<?php echo e(old('title')); ?>" placeholder="مثال: جلسة أولى للدعوى رقم...">
                            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">وصف الجلسة</label>
                            <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      id="description" name="description" rows="4" placeholder="تفاصيل الجلسة والأهداف المرجوة منها..."><?php echo e(old('description')); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      id="notes" name="notes" rows="3" placeholder="أي ملاحظات إضافية..."><?php echo e(old('notes')); ?></textarea>
                            <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- معلومات القضية (إذا تم تحديدها) -->
            <?php if(request('case_id')): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase ms-2"></i>
                        معلومات القضية
                    </h5>
                </div>
                <div class="card-body">
                    <h6><?php echo e($selectedCase->case_number); ?></h6>
                    <p class="text-muted"><?php echo e($selectedCase->case_title); ?></p>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">العميل:</small>
                            <br>
                            <strong><?php echo e($selectedCase->client_name); ?></strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">نوع القضية:</small>
                            <br>
                            <span class="badge bg-info"><?php echo e(\App\Models\CaseModel::getCaseTypes()[$selectedCase->case_type] ?? $selectedCase->case_type); ?></span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">الحالة:</small>
                            <br>
                            <span class="badge bg-success"><?php echo e(\App\Models\CaseModel::getStatuses()[$selectedCase->status] ?? $selectedCase->status); ?></span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">المحكمة:</small>
                            <br>
                            <small><?php echo e($selectedCase->court_name ?: 'غير محدد'); ?></small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- التذكيرات والإشعارات -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bell ms-2"></i>
                        التذكيرات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="send_notification" name="send_notification" value="1" 
                               <?php echo e(old('send_notification') ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="send_notification">
                            إرسال تذكير قبل الجلسة
                        </label>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reminder_minutes" class="form-label">التذكير قبل (بالدقائق)</label>
                        <select class="form-select" id="reminder_minutes" name="reminder_minutes">
                            <option value="30" <?php echo e(old('reminder_minutes') == '30' ? 'selected' : ''); ?>>30 دقيقة</option>
                            <option value="60" <?php echo e(old('reminder_minutes', '60') == '60' ? 'selected' : ''); ?>>ساعة واحدة</option>
                            <option value="120" <?php echo e(old('reminder_minutes') == '120' ? 'selected' : ''); ?>>ساعتان</option>
                            <option value="1440" <?php echo e(old('reminder_minutes') == '1440' ? 'selected' : ''); ?>>يوم كامل</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- التوقيتات السريعة -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clock ms-2"></i>
                        توقيتات سريعة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm quick-time" data-time="09:00">09:00 ص</button>
                        <button type="button" class="btn btn-outline-primary btn-sm quick-time" data-time="10:00">10:00 ص</button>
                        <button type="button" class="btn btn-outline-primary btn-sm quick-time" data-time="11:00">11:00 ص</button>
                        <button type="button" class="btn btn-outline-primary btn-sm quick-time" data-time="14:00">02:00 م</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- أزرار الحفظ -->
    <div class="d-flex justify-content-between">
        <a href="<?php echo e(route('hearings.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-times"></i> إلغاء
        </a>
        <div>
            <button type="submit" name="action" value="save" class="btn btn-primary me-2">
                <i class="fas fa-save"></i> حفظ الجلسة
            </button>
            <button type="submit" name="action" value="save_and_add" class="btn btn-success">
                <i class="fas fa-plus"></i> حفظ وإضافة أخرى
            </button>
        </div>
    </div>
</form>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // التوقيتات السريعة
    document.querySelectorAll('.quick-time').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('hearing_time').value = this.dataset.time;
        });
    });
    
    // تحديد القضية وملء البيانات
    const caseSelect = document.getElementById('case_id');
    if (caseSelect) {
        caseSelect.addEventListener('change', function() {
            if (this.value) {
                // يمكن إضافة AJAX هنا لجلب تفاصيل القضية
                console.log('Selected case:', this.value);
            }
        });
    }
    
    // تحقق من التاريخ
    const hearingDateInput = document.getElementById('hearing_date');
    hearingDateInput.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const today = new Date();
        
        if (selectedDate < today) {
            if (!confirm('التاريخ المحدد في الماضي. هل تريد المتابعة؟')) {
                this.value = '';
            }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tibyan-system\resources\views/hearings/create.blade.php ENDPATH**/ ?>