@extends('layouts.app')

@section('title', 'إضافة قضية جديدة - نظام تبيان')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-plus ms-2"></i>
        إضافة قضية جديدة
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('cases.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right"></i> العودة للقائمة
        </a>
    </div>
</div>

<form action="{{ route('cases.store') }}" method="POST" id="caseForm">
    @csrf
    
    <div class="row">
        <!-- معلومات العميل -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user ms-2"></i>
                        معلومات العميل
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="client_name" class="form-label">اسم العميل <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('client_name') is-invalid @enderror" 
                               id="client_name" name="client_name" value="{{ old('client_name') }}" required>
                        @error('client_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="client_id_number" class="form-label">رقم الهوية <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('client_id_number') is-invalid @enderror" 
                               id="client_id_number" name="client_id_number" value="{{ old('client_id_number') }}" required>
                        @error('client_id_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="client_id" class="form-label">العميل</label>
                        <select class="form-select @error('client_id') is-invalid @enderror" 
                                id="client_id" name="client_id">
                            <option value="">اختر عميل موجود (اختياري)</option>
                            @if(isset($clients))
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }} - {{ $client->phone ?? '' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('client_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="client_phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control @error('client_phone') is-invalid @enderror" 
                               id="client_phone" name="client_phone" value="{{ old('client_phone') }}" required>
                        @error('client_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        
        <!-- معلومات القضية -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase ms-2"></i>
                        معلومات القضية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="case_number" class="form-label">رقم القضية <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('case_number') is-invalid @enderror" 
                               id="case_number" name="case_number" value="{{ old('case_number') }}" required>
                        @error('case_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="case_title" class="form-label">عنوان القضية <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('case_title') is-invalid @enderror" 
                               id="case_title" name="case_title" value="{{ old('case_title') }}" required>
                        @error('case_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="case_type" class="form-label">نوع القضية <span class="text-danger">*</span></label>
                        <select class="form-select @error('case_type') is-invalid @enderror" 
                                id="case_type" name="case_type" required>
                            <option value="">اختر نوع القضية</option>
                            @foreach(\App\Models\CaseModel::getCaseTypes() as $key => $type)
                                <option value="{{ $key }}" {{ old('case_type') == $key ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                        @error('case_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="court_name" class="form-label">اسم المحكمة</label>
                        <input type="text" class="form-control @error('court_name') is-invalid @enderror" 
                               id="court_name" name="court_name" value="{{ old('court_name') }}">
                        @error('court_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="opponent_name" class="form-label">اسم الخصم</label>
                        <input type="text" class="form-control @error('opponent_name') is-invalid @enderror" 
                               id="opponent_name" name="opponent_name" value="{{ old('opponent_name') }}">
                        @error('opponent_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            <option value="">اختر الحالة</option>
                            @foreach(\App\Models\CaseModel::getStatuses() as $key => $status)
                                <option value="{{ $key }}" {{ old('status', 'active') == $key ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="priority" class="form-label">الأولوية <span class="text-danger">*</span></label>
                        <select class="form-select @error('priority') is-invalid @enderror" 
                                id="priority" name="priority" required>
                            <option value="">اختر الأولوية</option>
                            @foreach(\App\Models\CaseModel::getPriorities() as $key => $priority)
                                <option value="{{ $key }}" {{ old('priority', 'medium') == $key ? 'selected' : '' }}>
                                    {{ $priority }}
                                </option>
                            @endforeach
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- ملخص القضية -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt ms-2"></i>
                        تفاصيل القضية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="case_summary" class="form-label">ملخص القضية <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('case_summary') is-invalid @enderror" 
                                  id="case_summary" name="case_summary" rows="6" required>{{ old('case_summary') }}</textarea>
                        @error('case_summary')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظات إضافية</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        
        <!-- المعلومات المالية والتواريخ -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-money-bill-wave ms-2"></i>
                        الأتعاب والتواريخ
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="total_fees" class="form-label">إجمالي الأتعاب <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" 
                               class="form-control @error('total_fees') is-invalid @enderror" 
                               id="total_fees" name="total_fees" value="{{ old('total_fees') }}" required>
                        @error('total_fees')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="fee_type" class="form-label">نوع الأتعاب <span class="text-danger">*</span></label>
                        <select class="form-select @error('fee_type') is-invalid @enderror" 
                                id="fee_type" name="fee_type" required>
                            <option value="">اختر نوع الأتعاب</option>
                            @foreach(\App\Models\CaseModel::getFeeTypes() as $key => $type)
                                <option value="{{ $key }}" {{ old('fee_type', 'fixed') == $key ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                        @error('fee_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="fees_received" class="form-label">الأتعاب المستلمة حالياً</label>
                        <input type="number" step="0.01" min="0" 
                               class="form-control @error('fees_received') is-invalid @enderror" 
                               id="fees_received" name="fees_received" value="{{ old('fees_received', 0) }}">
                        @error('fees_received')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="start_date" class="form-label">تاريخ بدء القضية <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                               id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="expected_end_date" class="form-label">التاريخ المتوقع للانتهاء</label>
                        <input type="date" class="form-control @error('expected_end_date') is-invalid @enderror" 
                               id="expected_end_date" name="expected_end_date" value="{{ old('expected_end_date') }}">
                        @error('expected_end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- أزرار الحفظ -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('cases.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
                <button type="button" class="btn btn-outline-primary" onclick="previewCase()">
                    <i class="fas fa-eye"></i> معاينة
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ القضية
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Modal للمعاينة -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">معاينة القضية</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- سيتم ملؤها بـ JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">حفظ القضية</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // حساب الأتعاب المتبقية تلقائياً
    document.getElementById('total_fees').addEventListener('input', calculateRemainingFees);
    document.getElementById('fees_received').addEventListener('input', calculateRemainingFees);
    
    function calculateRemainingFees() {
        const totalFees = parseFloat(document.getElementById('total_fees').value) || 0;
        const feesReceived = parseFloat(document.getElementById('fees_received').value) || 0;
        const remaining = totalFees - feesReceived;
        
        // يمكن إضافة عرض للمبلغ المتبقي هنا
        if (remaining < 0) {
            document.getElementById('fees_received').setCustomValidity('الأتعاب المستلمة لا يمكن أن تزيد عن الإجمالي');
        } else {
            document.getElementById('fees_received').setCustomValidity('');
        }
    }
    
    // معاينة القضية
    function previewCase() {
        const formData = new FormData(document.getElementById('caseForm'));
        const caseTypes = @json(\App\Models\CaseModel::getCaseTypes());
        
        let previewHtml = `
            <div class="row">
                <div class="col-md-6">
                    <h6>معلومات العميل:</h6>
                    <p><strong>الاسم:</strong> ${formData.get('client_name') || 'غير محدد'}</p>
                    <p><strong>رقم الهوية:</strong> ${formData.get('client_id') || 'غير محدد'}</p>
                    <p><strong>الهاتف:</strong> ${formData.get('client_phone') || 'غير محدد'}</p>
                </div>
                <div class="col-md-6">
                    <h6>معلومات القضية:</h6>
                    <p><strong>النوع:</strong> ${caseTypes[formData.get('case_type')] || 'غير محدد'}</p>
                    <p><strong>المحكمة:</strong> ${formData.get('court_name') || 'غير محدد'}</p>
                    <p><strong>الخصم:</strong> ${formData.get('opponent_name') || 'غير محدد'}</p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <h6>ملخص القضية:</h6>
                    <p>${formData.get('case_summary') || 'غير محدد'}</p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <h6>المعلومات المالية:</h6>
                    <p><strong>إجمالي الأتعاب:</strong> ${formData.get('total_fees') || '0'}</p>
                    <p><strong>المستلم:</strong> ${formData.get('fees_received') || '0'}</p>
                    <p><strong>المتبقي:</strong> ${(parseFloat(formData.get('total_fees')) || 0) - (parseFloat(formData.get('fees_received')) || 0)}</p>
                </div>
                <div class="col-md-6">
                    <h6>التواريخ:</h6>
                    <p><strong>تاريخ البدء:</strong> ${formData.get('start_date') || 'غير محدد'}</p>
                    <p><strong>التاريخ المتوقع للانتهاء:</strong> ${formData.get('expected_end_date') || 'غير محدد'}</p>
                </div>
            </div>
        `;
        
        document.getElementById('previewContent').innerHTML = previewHtml;
        new bootstrap.Modal(document.getElementById('previewModal')).show();
    }
    
    function submitForm() {
        document.getElementById('caseForm').submit();
    }
    
    // التحقق من صحة النموذج قبل الإرسال
    document.getElementById('caseForm').addEventListener('submit', function(e) {
        const totalFees = parseFloat(document.getElementById('total_fees').value) || 0;
        const feesReceived = parseFloat(document.getElementById('fees_received').value) || 0;
        
        if (feesReceived > totalFees) {
            e.preventDefault();
            alert('الأتعاب المستلمة لا يمكن أن تزيد عن الإجمالي');
            return false;
        }
    });
</script>
@endpush