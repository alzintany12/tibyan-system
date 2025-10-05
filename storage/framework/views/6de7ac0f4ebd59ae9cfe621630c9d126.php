<?php $__env->startSection('title', 'عرض الفاتورة - نظام تبيان'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-file-invoice-dollar ms-2"></i>
        الفاتورة رقم: <?php echo e($invoice->invoice_number); ?>

    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <?php if($invoice->status === 'draft'): ?>
                <a href="<?php echo e(route('invoices.edit', $invoice)); ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> تعديل
                </a>
            <?php endif; ?>
            
            <a href="<?php echo e(route('invoices.print', $invoice)); ?>" class="btn btn-outline-secondary" target="_blank">
                <i class="fas fa-print"></i> طباعة
            </a>
            
            <div class="btn-group">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-cog"></i> إجراءات
                </button>
                <ul class="dropdown-menu">
                    <?php if($invoice->status !== 'paid'): ?>
                        <li>
                            <form action="<?php echo e(route('invoices.mark-paid', $invoice)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <button type="submit" class="dropdown-item" onclick="return confirm('هل أنت متأكد من وضع علامة مدفوع؟')">
                                    <i class="fas fa-check-circle text-success"></i> تحديد كمدفوعة
                                </button>
                            </form>
                        </li>
                    <?php endif; ?>
                    
                    <?php if($invoice->status === 'draft'): ?>
                        <li>
                            <form action="<?php echo e(route('invoices.send', $invoice)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-paper-plane text-primary"></i> إرسال الفاتورة
                                </button>
                            </form>
                        </li>
                    <?php endif; ?>
                    
                    <li>
                        <a class="dropdown-item" href="<?php echo e(route('invoices.duplicate', $invoice)); ?>">
                            <i class="fas fa-copy text-info"></i> نسخ الفاتورة
                        </a>
                    </li>
                    
                    <li><hr class="dropdown-divider"></li>
                    
                    <?php if($invoice->status !== 'paid'): ?>
                        <li>
                            <form action="<?php echo e(route('invoices.cancel', $invoice)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <button type="submit" class="dropdown-item text-warning" onclick="return confirm('هل أنت متأكد من إلغاء الفاتورة؟')">
                                    <i class="fas fa-ban"></i> إلغاء الفاتورة
                                </button>
                            </form>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
        <a href="<?php echo e(route('invoices.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> العودة للفواتير
        </a>
    </div>
</div>

<!-- معلومات الحالة -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="alert alert-<?php echo e($invoice->status === 'paid' ? 'success' : ($invoice->status === 'overdue' ? 'danger' : 'info')); ?>">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <strong>الحالة:</strong> 
                    <?php switch($invoice->status):
                        case ('draft'): ?>
                            <span class="badge bg-secondary">مسودة</span>
                            <?php break; ?>
                        <?php case ('sent'): ?>
                            <span class="badge bg-primary">مرسلة</span>
                            <?php break; ?>
                        <?php case ('viewed'): ?>
                            <span class="badge bg-info">تم الاطلاع</span>
                            <?php break; ?>
                        <?php case ('paid'): ?>
                            <span class="badge bg-success">مدفوعة</span>
                            <?php break; ?>
                        <?php case ('overdue'): ?>
                            <span class="badge bg-danger">متأخرة</span>
                            <?php break; ?>
                        <?php case ('cancelled'): ?>
                            <span class="badge bg-warning">ملغية</span>
                            <?php break; ?>
                        <?php default: ?>
                            <span class="badge bg-secondary"><?php echo e($invoice->status); ?></span>
                    <?php endswitch; ?>
                </div>
                <div class="col-md-6 text-end">
                    <strong>المبلغ الإجمالي:</strong> 
                    <span class="h5 text-primary"><?php echo e(number_format($invoice->total_amount, 2)); ?> دينار ليبي</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- تفاصيل الفاتورة -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>تفاصيل الفاتورة</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>رقم الفاتورة:</strong> <?php echo e($invoice->invoice_number); ?>

                    </div>
                    <div class="col-md-6">
                        <strong>تاريخ الفاتورة:</strong> <?php echo e($invoice->invoice_date->format('Y/m/d')); ?>

                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>تاريخ الاستحقاق:</strong> <?php echo e($invoice->due_date->format('Y/m/d')); ?>

                    </div>
                    <div class="col-md-6">
                        <strong>اسم العميل:</strong> <?php echo e($invoice->client_name); ?>

                    </div>
                </div>
                
                <?php if($invoice->case): ?>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>القضية المرتبطة:</strong> 
                            <a href="<?php echo e(route('cases.show', $invoice->case)); ?>" class="text-decoration-none">
                                <?php echo e($invoice->case->case_number); ?> - <?php echo e($invoice->case->client_name); ?>

                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>وصف الخدمة:</strong>
                        <p class="mt-2"><?php echo e($invoice->description); ?></p>
                    </div>
                </div>
                
                <?php if($invoice->notes): ?>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>ملاحظات:</strong>
                            <p class="mt-2"><?php echo e($invoice->notes); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- بنود الفاتورة -->
        <?php if($invoice->items->count() > 0): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h5>بنود الفاتورة</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الوصف</th>
                                    <th class="text-center">الكمية</th>
                                    <th class="text-center">السعر</th>
                                    <th class="text-center">المجموع</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($item->description); ?></td>
                                        <td class="text-center"><?php echo e($item->quantity); ?></td>
                                        <td class="text-center"><?php echo e(number_format($item->unit_price, 2)); ?></td>
                                        <td class="text-center"><?php echo e(number_format($item->total_price, 2)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- معلومات إضافية -->
    <div class="col-md-4">
        <!-- ملخص المبالغ -->
        <div class="card">
            <div class="card-header">
                <h5>ملخص المبالغ</h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-8">المبلغ الأساسي:</div>
                    <div class="col-4 text-end"><?php echo e(number_format($invoice->amount, 2)); ?></div>
                </div>
                
                <?php if($invoice->tax_amount > 0): ?>
                    <div class="row mb-2">
                        <div class="col-8">الضريبة:</div>
                        <div class="col-4 text-end"><?php echo e(number_format($invoice->tax_amount, 2)); ?></div>
                    </div>
                <?php endif; ?>
                
                <?php if($invoice->discount_amount > 0): ?>
                    <div class="row mb-2">
                        <div class="col-8">الخصم:</div>
                        <div class="col-4 text-end text-danger">-<?php echo e(number_format($invoice->discount_amount, 2)); ?></div>
                    </div>
                <?php endif; ?>
                
                <hr>
                
                <div class="row">
                    <div class="col-8"><strong>المجموع الكلي:</strong></div>
                    <div class="col-4 text-end"><strong><?php echo e(number_format($invoice->total_amount, 2)); ?></strong></div>
                </div>
                
                <?php if($invoice->paid_amount > 0): ?>
                    <div class="row mt-2">
                        <div class="col-8">المبلغ المدفوع:</div>
                        <div class="col-4 text-end text-success"><?php echo e(number_format($invoice->paid_amount, 2)); ?></div>
                    </div>
                    
                    <?php if($invoice->total_amount - $invoice->paid_amount > 0): ?>
                        <div class="row">
                            <div class="col-8">المبلغ المتبقي:</div>
                            <div class="col-4 text-end text-danger"><?php echo e(number_format($invoice->total_amount - $invoice->paid_amount, 2)); ?></div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- تواريخ مهمة -->
        <div class="card mt-4">
            <div class="card-header">
                <h5>تواريخ مهمة</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">تاريخ الإنشاء</small><br>
                    <span><?php echo e($invoice->created_at->format('Y/m/d H:i')); ?></span>
                </div>
                
                <?php if($invoice->sent_at): ?>
                    <div class="mb-3">
                        <small class="text-muted">تاريخ الإرسال</small><br>
                        <span><?php echo e($invoice->sent_at->format('Y/m/d H:i')); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if($invoice->paid_at): ?>
                    <div class="mb-3">
                        <small class="text-muted">تاريخ الدفع</small><br>
                        <span><?php echo e($invoice->paid_at->format('Y/m/d H:i')); ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <small class="text-muted">تاريخ آخر تحديث</small><br>
                    <span><?php echo e($invoice->updated_at->format('Y/m/d H:i')); ?></span>
                </div>
                
                <?php if($invoice->created_by): ?>
                    <div class="mb-3">
                        <small class="text-muted">تم الإنشاء بواسطة</small><br>
                        <span><?php echo e($invoice->created_by); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if($invoice->updated_by): ?>
                    <div>
                        <small class="text-muted">تم التحديث بواسطة</small><br>
                        <span><?php echo e($invoice->updated_by); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tibyan-system\resources\views/invoices/show.blade.php ENDPATH**/ ?>