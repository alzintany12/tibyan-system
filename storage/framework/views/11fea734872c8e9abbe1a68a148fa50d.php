<?php $__env->startSection('title', 'تعديل الفاتورة - نظام تبيان'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-edit ms-2"></i>
        تعديل الفاتورة: <?php echo e($invoice->invoice_number); ?>

    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?php echo e(route('invoices.show', $invoice)); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> العودة للفاتورة
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="<?php echo e(route('invoices.update', $invoice)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">رقم الفاتورة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['invoice_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   name="invoice_number" value="<?php echo e(old('invoice_number', $invoice->invoice_number)); ?>" required>
                            <?php $__errorArgs = ['invoice_number'];
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
                        
                        <div class="col-md-6">
                            <label class="form-label">الحالة <span class="text-danger">*</span></label>
                            <select class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="status" required>
                                <option value="draft" <?php echo e(old('status', $invoice->status) == 'draft' ? 'selected' : ''); ?>>مسودة</option>
                                <option value="sent" <?php echo e(old('status', $invoice->status) == 'sent' ? 'selected' : ''); ?>>مرسلة</option>
                                <option value="viewed" <?php echo e(old('status', $invoice->status) == 'viewed' ? 'selected' : ''); ?>>تم الاطلاع</option>
                                <option value="paid" <?php echo e(old('status', $invoice->status) == 'paid' ? 'selected' : ''); ?>>مدفوعة</option>
                                <option value="overdue" <?php echo e(old('status', $invoice->status) == 'overdue' ? 'selected' : ''); ?>>متأخرة</option>
                                <option value="cancelled" <?php echo e(old('status', $invoice->status) == 'cancelled' ? 'selected' : ''); ?>>ملغية</option>
                                <option value="pending" <?php echo e(old('status', $invoice->status) == 'pending' ? 'selected' : ''); ?>>في الانتظار</option>
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
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">القضية</label>
                            <select class="form-select <?php $__errorArgs = ['case_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="case_id" id="case_id">
                                <option value="">اختر القضية</option>
                                <?php $__currentLoopData = $cases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($case->id); ?>" 
                                            <?php echo e((old('case_id', $invoice->case_id) == $case->id) ? 'selected' : ''); ?>

                                            data-client="<?php echo e($case->client_name); ?>">
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
                        
                        <div class="col-md-6">
                            <label class="form-label">اسم العميل <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['client_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   name="client_name" value="<?php echo e(old('client_name', $invoice->client_name)); ?>" 
                                   id="client_name" required>
                            <?php $__errorArgs = ['client_name'];
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

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">تاريخ الفاتورة <span class="text-danger">*</span></label>
                            <input type="date" class="form-control <?php $__errorArgs = ['invoice_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   name="invoice_date" value="<?php echo e(old('invoice_date', $invoice->invoice_date->format('Y-m-d'))); ?>" required>
                            <?php $__errorArgs = ['invoice_date'];
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
                        
                        <div class="col-md-6">
                            <label class="form-label">تاريخ الاستحقاق <span class="text-danger">*</span></label>
                            <input type="date" class="form-control <?php $__errorArgs = ['due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   name="due_date" value="<?php echo e(old('due_date', $invoice->due_date->format('Y-m-d'))); ?>" required>
                            <?php $__errorArgs = ['due_date'];
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

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label">وصف الخدمة</label>
                            <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      name="description" rows="3"><?php echo e(old('description', $invoice->description)); ?></textarea>
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
                    </div>

                    <!-- بنود الفاتورة -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">بنود الفاتورة</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-item">
                                <i class="fas fa-plus"></i> إضافة بند
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="invoice-items">
                                <?php $__empty_1 = true; $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="row invoice-item mb-3">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="items[<?php echo e($index); ?>][description]" 
                                                   placeholder="وصف البند" value="<?php echo e(old('items.'.$index.'.description', $item->description)); ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" class="form-control item-quantity" name="items[<?php echo e($index); ?>][quantity]" 
                                                   placeholder="الكمية" value="<?php echo e(old('items.'.$index.'.quantity', $item->quantity)); ?>" min="1" step="1">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control item-price" name="items[<?php echo e($index); ?>][unit_price]" 
                                                   placeholder="السعر" value="<?php echo e(old('items.'.$index.'.unit_price', $item->unit_price)); ?>" min="0" step="0.01">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control item-total" placeholder="المجموع" readonly>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="row invoice-item mb-3">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="items[0][description]" 
                                                   placeholder="وصف البند" value="<?php echo e(old('items.0.description')); ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" class="form-control item-quantity" name="items[0][quantity]" 
                                                   placeholder="الكمية" value="<?php echo e(old('items.0.quantity', 1)); ?>" min="1" step="1">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control item-price" name="items[0][unit_price]" 
                                                   placeholder="السعر" value="<?php echo e(old('items.0.unit_price', 0)); ?>" min="0" step="0.01">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control item-total" placeholder="المجموع" readonly>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-item" disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- حساب المبالغ -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ملاحظات</label>
                                <textarea class="form-control" name="notes" rows="3"><?php echo e(old('notes', $invoice->notes)); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-6"><strong>المبلغ الأساسي:</strong></div>
                                        <div class="col-6 text-end">
                                            <input type="number" class="form-control <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   name="amount" id="amount" value="<?php echo e(old('amount', $invoice->amount)); ?>" 
                                                   min="0" step="0.01" required>
                                            <?php $__errorArgs = ['amount'];
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
                                    
                                    <div class="row mb-2">
                                        <div class="col-6">الضريبة:</div>
                                        <div class="col-6 text-end">
                                            <input type="number" class="form-control" name="tax_amount" id="tax_amount" 
                                                   value="<?php echo e(old('tax_amount', $invoice->tax_amount)); ?>" min="0" step="0.01">
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-6">الخصم:</div>
                                        <div class="col-6 text-end">
                                            <input type="number" class="form-control" name="discount_amount" id="discount_amount" 
                                                   value="<?php echo e(old('discount_amount', $invoice->discount_amount)); ?>" min="0" step="0.01">
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="row mb-2">
                                        <div class="col-6"><strong>المجموع الكلي:</strong></div>
                                        <div class="col-6 text-end">
                                            <input type="number" class="form-control <?php $__errorArgs = ['total_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   name="total_amount" id="total_amount" value="<?php echo e(old('total_amount', $invoice->total_amount)); ?>" 
                                                   min="0" step="0.01" required readonly>
                                            <?php $__errorArgs = ['total_amount'];
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
                                    
                                    <div class="row">
                                        <div class="col-6">المبلغ المدفوع:</div>
                                        <div class="col-6 text-end">
                                            <input type="number" class="form-control" name="paid_amount" id="paid_amount" 
                                                   value="<?php echo e(old('paid_amount', $invoice->paid_amount)); ?>" min="0" step="0.01">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> حفظ التغييرات
                        </button>
                        <a href="<?php echo e(route('invoices.show', $invoice)); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    let itemIndex = <?php echo e($invoice->items->count() > 0 ? $invoice->items->count() : 1); ?>;
    
    // إضافة بند جديد
    $('#add-item').click(function() {
        let newItem = `
            <div class="row invoice-item mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="items[${itemIndex}][description]" 
                           placeholder="وصف البند">
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control item-quantity" name="items[${itemIndex}][quantity]" 
                           placeholder="الكمية" value="1" min="1" step="1">
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control item-price" name="items[${itemIndex}][unit_price]" 
                           placeholder="السعر" value="0" min="0" step="0.01">
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control item-total" placeholder="المجموع" readonly>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $('#invoice-items').append(newItem);
        itemIndex++;
        updateRemoveButtons();
        calculateTotals();
    });
    
    // حذف بند
    $(document).on('click', '.remove-item', function() {
        $(this).closest('.invoice-item').remove();
        updateRemoveButtons();
        calculateTotals();
    });
    
    // تحديث أزرار الحذف
    function updateRemoveButtons() {
        let itemCount = $('.invoice-item').length;
        if (itemCount > 1) {
            $('.remove-item').prop('disabled', false);
        } else {
            $('.remove-item').prop('disabled', true);
        }
    }
    
    // حساب المجاميع
    $(document).on('input', '.item-quantity, .item-price, #amount, #tax_amount, #discount_amount', function() {
        calculateTotals();
    });
    
    function calculateTotals() {
        let totalItemsAmount = 0;
        
        $('.invoice-item').each(function() {
            let quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
            let price = parseFloat($(this).find('.item-price').val()) || 0;
            let total = quantity * price;
            
            $(this).find('.item-total').val(total.toFixed(2));
            totalItemsAmount += total;
        });
        
        let amount = parseFloat($('#amount').val()) || 0;
        let taxAmount = parseFloat($('#tax_amount').val()) || 0;
        let discountAmount = parseFloat($('#discount_amount').val()) || 0;
        
        // إذا كان هناك بنود، استخدم مجموع البنود كمبلغ أساسي
        if (totalItemsAmount > 0) {
            amount = totalItemsAmount;
            $('#amount').val(amount.toFixed(2));
        }
        
        let totalAmount = amount + taxAmount - discountAmount;
        $('#total_amount').val(totalAmount.toFixed(2));
    }
    
    // تحديث بيانات العميل عند اختيار القضية
    $('#case_id').change(function() {
        let selectedOption = $(this).find(':selected');
        let clientName = selectedOption.data('client');
        
        if (clientName) {
            $('#client_name').val(clientName);
        }
    });
    
    // حساب أولي
    updateRemoveButtons();
    calculateTotals();
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tibyan-system\resources\views/invoices/edit.blade.php ENDPATH**/ ?>