@extends('layouts.app')

@section('page-title', 'إضافة فاتورة جديدة')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">الفواتير</a></li>
    <li class="breadcrumb-item active">إضافة فاتورة جديدة</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus"></i>
                        إضافة فاتورة جديدة
                    </h3>
                </div>
                
                <form action="{{ route('invoices.store') }}" method="POST">
                    @csrf
                    
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <!-- القضية -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="case_id" class="required">القضية</label>
                                    <select name="case_id" id="case_id" class="form-control" required>
                                        <option value="">اختر القضية</option>
                                        @foreach($cases as $case)
                                            <option value="{{ $case->id }}" 
                                                {{ old('case_id', $selectedCaseId) == $case->id ? 'selected' : '' }}
                                                data-client="{{ $case->client_name }}"
                                                data-client-phone="{{ $case->client_phone }}"
                                                data-fee-amount="{{ $case->fee_amount }}"
                                                data-case-value="{{ $case->case_value }}">
                                                {{ $case->case_number }} - {{ $case->client_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- اسم العميل -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="client_name" class="required">اسم العميل</label>
                                    <input type="text" name="client_name" id="client_name" class="form-control" 
                                           value="{{ old('client_name', $selectedCase->client_name ?? '') }}" 
                                           placeholder="اسم العميل" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- تاريخ الفاتورة -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="invoice_date" class="required">تاريخ الفاتورة</label>
                                    <input type="date" name="invoice_date" id="invoice_date" class="form-control" 
                                           value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                                </div>
                            </div>

                            <!-- تاريخ الاستحقاق -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="due_date" class="required">تاريخ الاستحقاق</label>
                                    <input type="date" name="due_date" id="due_date" class="form-control" 
                                           value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                                </div>
                            </div>

                            <!-- المبلغ -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="amount" class="required">المبلغ (دينار ليبي)</label>
                                    <input type="number" name="amount" id="amount" class="form-control" 
                                           value="{{ old('amount') }}" 
                                           step="0.01" min="0.01" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- مبلغ الضريبة -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tax_amount">مبلغ الضريبة (دينار ليبي)</label>
                                    <input type="number" name="tax_amount" id="tax_amount" class="form-control" 
                                           value="{{ old('tax_amount', '0') }}" 
                                           step="0.01" min="0" placeholder="0.00" readonly>
                                </div>
                            </div>

                            <!-- الإجمالي -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="total_amount" class="required">المبلغ الإجمالي (دينار ليبي)</label>
                                    <input type="number" name="total_amount" id="total_amount" class="form-control" 
                                           value="{{ old('total_amount') }}" 
                                           step="0.01" min="0.01" placeholder="0.00" required readonly>
                                </div>
                            </div>

                            <!-- نسبة الضريبة -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tax_rate">نسبة الضريبة (%)</label>
                                    <input type="number" name="tax_rate" id="tax_rate" class="form-control" 
                                           value="15" step="0.01" min="0" max="100" placeholder="15">
                                </div>
                            </div>
                        </div>

                        <!-- وصف الفاتورة -->
                        <div class="form-group">
                            <label for="description" class="required">وصف الفاتورة</label>
                            <textarea name="description" id="description" class="form-control" rows="3" 
                                      placeholder="وصف الخدمات أو المنتجات" required>{{ old('description') }}</textarea>
                        </div>

                        <!-- ملاحظات -->
                        <div class="form-group">
                            <label for="notes">ملاحظات</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" 
                                      placeholder="ملاحظات إضافية">{{ old('notes') }}</textarea>
                        </div>

                        <!-- بنود الفاتورة -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3">بنود الفاتورة (اختياري)</h5>
                                <div id="invoice-items">
                                    <div class="invoice-item mb-3">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <input type="text" name="items[0][description]" class="form-control" 
                                                       placeholder="وصف البند" value="{{ old('items.0.description') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" name="items[0][quantity]" class="form-control item-quantity" 
                                                       placeholder="الكمية" value="{{ old('items.0.quantity', 1) }}" min="1">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" name="items[0][unit_price]" class="form-control item-price" 
                                                       placeholder="سعر الوحدة" value="{{ old('items.0.unit_price', 0) }}" step="0.01" min="0">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger remove-item" disabled>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="add-item" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i>
                                    إضافة بند
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    إنشاء الفاتورة
                                </button>
                                <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                    إلغاء
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let itemCount = 1;
    
    // تحديث بيانات العميل عند تغيير القضية
    $('#case_id').change(function() {
        var selectedOption = $(this).find('option:selected');
        var clientName = selectedOption.data('client');
        var feeAmount = selectedOption.data('fee-amount');
        var caseValue = selectedOption.data('case-value');
        
        if (clientName) {
            $('#client_name').val(clientName);
        }
        
        // تحديث وصف الفاتورة
        if (selectedOption.val()) {
            var caseNumber = selectedOption.text().split(' - ')[0];
            $('#description').val('أتعاب قضية: ' + caseNumber);
        }
        
        // تحديث المبلغ المقترح
        var suggestedAmount = feeAmount || caseValue || 0;
        if (suggestedAmount > 0 && !$('#amount').val()) {
            $('#amount').val(suggestedAmount);
            calculateTotal();
        }
    });
    
    // حساب الإجمالي
    function calculateTotal() {
        var amount = parseFloat($('#amount').val()) || 0;
        var taxRate = parseFloat($('#tax_rate').val()) || 0;
        var taxAmount = (amount * taxRate) / 100;
        var totalAmount = amount + taxAmount;
        
        $('#tax_amount').val(taxAmount.toFixed(2));
        $('#total_amount').val(totalAmount.toFixed(2));
    }
    
    // حساب الإجمالي عند تغيير المبلغ أو نسبة الضريبة
    $('#amount, #tax_rate').on('input', calculateTotal);
    
    // إضافة بند جديد
    $('#add-item').click(function() {
        var newItem = `
            <div class="invoice-item mb-3">
                <div class="row">
                    <div class="col-md-5">
                        <input type="text" name="items[${itemCount}][description]" class="form-control" 
                               placeholder="وصف البند">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[${itemCount}][quantity]" class="form-control item-quantity" 
                               placeholder="الكمية" value="1" min="1">
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="items[${itemCount}][unit_price]" class="form-control item-price" 
                               placeholder="سعر الوحدة" value="0" step="0.01" min="0">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#invoice-items').append(newItem);
        itemCount++;
        updateRemoveButtons();
    });
    
    // حذف بند
    $(document).on('click', '.remove-item', function() {
        $(this).closest('.invoice-item').remove();
        updateRemoveButtons();
    });
    
    // تحديث أزرار الحذف
    function updateRemoveButtons() {
        var itemCount = $('.invoice-item').length;
        $('.remove-item').prop('disabled', itemCount <= 1);
    }
    
    // تحديث تاريخ الاستحقاق عند تغيير تاريخ الفاتورة
    $('#invoice_date').change(function() {
        var invoiceDate = new Date($(this).val());
        if (invoiceDate) {
            invoiceDate.setDate(invoiceDate.getDate() + 30);
            var dueDate = invoiceDate.toISOString().split('T')[0];
            $('#due_date').val(dueDate);
        }
    });
    
    // التأكد من وجود قيمة في المبلغ
    $('form').submit(function(e) {
        var amount = parseFloat($('#amount').val()) || 0;
        if (amount <= 0) {
            e.preventDefault();
            alert('يرجى إدخال مبلغ صحيح للفاتورة');
            $('#amount').focus();
            return false;
        }
    });
    
    // حساب الإجمالي عند تحميل الصفحة
    calculateTotal();
    updateRemoveButtons();
});
</script>
@endpush

@push('styles')
<style>
.required::after {
    content: " *";
    color: red;
}

.invoice-item {
    border-left: 3px solid #007bff;
    padding-left: 15px;
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
}

.invoice-item:hover {
    background-color: #e9ecef;
}
</style>
@endpush