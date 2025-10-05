@extends('layouts.app')

@section('title', 'تعديل الفاتورة - نظام تبيان')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-edit ms-2"></i>
        تعديل الفاتورة: {{ $invoice->invoice_number }}
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> العودة للفاتورة
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('invoices.update', $invoice) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">رقم الفاتورة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" 
                                   name="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number) }}" required>
                            @error('invoice_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">الحالة <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="draft" {{ old('status', $invoice->status) == 'draft' ? 'selected' : '' }}>مسودة</option>
                                <option value="sent" {{ old('status', $invoice->status) == 'sent' ? 'selected' : '' }}>مرسلة</option>
                                <option value="viewed" {{ old('status', $invoice->status) == 'viewed' ? 'selected' : '' }}>تم الاطلاع</option>
                                <option value="paid" {{ old('status', $invoice->status) == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                                <option value="overdue" {{ old('status', $invoice->status) == 'overdue' ? 'selected' : '' }}>متأخرة</option>
                                <option value="cancelled" {{ old('status', $invoice->status) == 'cancelled' ? 'selected' : '' }}>ملغية</option>
                                <option value="pending" {{ old('status', $invoice->status) == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">القضية</label>
                            <select class="form-select @error('case_id') is-invalid @enderror" name="case_id" id="case_id">
                                <option value="">اختر القضية</option>
                                @foreach($cases as $case)
                                    <option value="{{ $case->id }}" 
                                            {{ (old('case_id', $invoice->case_id) == $case->id) ? 'selected' : '' }}
                                            data-client="{{ $case->client_name }}">
                                        {{ $case->case_number }} - {{ $case->client_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('case_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">اسم العميل <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('client_name') is-invalid @enderror" 
                                   name="client_name" value="{{ old('client_name', $invoice->client_name) }}" 
                                   id="client_name" required>
                            @error('client_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">تاريخ الفاتورة <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" 
                                   name="invoice_date" value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required>
                            @error('invoice_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">تاريخ الاستحقاق <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                   name="due_date" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label">وصف الخدمة</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      name="description" rows="3">{{ old('description', $invoice->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                @forelse($invoice->items as $index => $item)
                                    <div class="row invoice-item mb-3">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="items[{{ $index }}][description]" 
                                                   placeholder="وصف البند" value="{{ old('items.'.$index.'.description', $item->description) }}">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" class="form-control item-quantity" name="items[{{ $index }}][quantity]" 
                                                   placeholder="الكمية" value="{{ old('items.'.$index.'.quantity', $item->quantity) }}" min="1" step="1">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control item-price" name="items[{{ $index }}][unit_price]" 
                                                   placeholder="السعر" value="{{ old('items.'.$index.'.unit_price', $item->unit_price) }}" min="0" step="0.01">
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
                                @empty
                                    <div class="row invoice-item mb-3">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="items[0][description]" 
                                                   placeholder="وصف البند" value="{{ old('items.0.description') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" class="form-control item-quantity" name="items[0][quantity]" 
                                                   placeholder="الكمية" value="{{ old('items.0.quantity', 1) }}" min="1" step="1">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control item-price" name="items[0][unit_price]" 
                                                   placeholder="السعر" value="{{ old('items.0.unit_price', 0) }}" min="0" step="0.01">
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
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- حساب المبالغ -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ملاحظات</label>
                                <textarea class="form-control" name="notes" rows="3">{{ old('notes', $invoice->notes) }}</textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-6"><strong>المبلغ الأساسي:</strong></div>
                                        <div class="col-6 text-end">
                                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                                   name="amount" id="amount" value="{{ old('amount', $invoice->amount) }}" 
                                                   min="0" step="0.01" required>
                                            @error('amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-6">الضريبة:</div>
                                        <div class="col-6 text-end">
                                            <input type="number" class="form-control" name="tax_amount" id="tax_amount" 
                                                   value="{{ old('tax_amount', $invoice->tax_amount) }}" min="0" step="0.01">
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-6">الخصم:</div>
                                        <div class="col-6 text-end">
                                            <input type="number" class="form-control" name="discount_amount" id="discount_amount" 
                                                   value="{{ old('discount_amount', $invoice->discount_amount) }}" min="0" step="0.01">
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="row mb-2">
                                        <div class="col-6"><strong>المجموع الكلي:</strong></div>
                                        <div class="col-6 text-end">
                                            <input type="number" class="form-control @error('total_amount') is-invalid @enderror" 
                                                   name="total_amount" id="total_amount" value="{{ old('total_amount', $invoice->total_amount) }}" 
                                                   min="0" step="0.01" required readonly>
                                            @error('total_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-6">المبلغ المدفوع:</div>
                                        <div class="col-6 text-end">
                                            <input type="number" class="form-control" name="paid_amount" id="paid_amount" 
                                                   value="{{ old('paid_amount', $invoice->paid_amount) }}" min="0" step="0.01">
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
                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let itemIndex = {{ $invoice->items->count() > 0 ? $invoice->items->count() : 1 }};
    
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
@endpush