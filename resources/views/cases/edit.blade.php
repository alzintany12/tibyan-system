@extends('layouts.app')

@section('title', 'تعديل القضية - نظام تبيان')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-edit ms-2"></i>
        تعديل القضية: {{ $case->case_number }}
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('cases.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right"></i> العودة للقائمة
            </a>
            <a href="{{ route('cases.show', $case) }}" class="btn btn-outline-primary">
                <i class="fas fa-eye"></i> عرض القضية
            </a>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <h6>يرجى تصحيح الأخطاء التالية:</h6>
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form action="{{ route('cases.update', $case) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
        <!-- المعلومات الأساسية -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle ms-2"></i>
                        المعلومات الأساسية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="case_number" class="form-label">رقم القضية <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('case_number') is-invalid @enderror" 
                                   id="case_number" name="case_number" value="{{ old('case_number', $case->case_number) }}" required>
                            @error('case_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="case_title" class="form-label">عنوان القضية <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('case_title') is-invalid @enderror" 
                                   id="case_title" name="case_title" value="{{ old('case_title', $case->case_title) }}" required>
                            @error('case_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="client_name" class="form-label">اسم العميل <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('client_name') is-invalid @enderror" 
                                   id="client_name" name="client_name" value="{{ old('client_name', $case->client_name) }}" required>
                            @error('client_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="client_phone" class="form-label">رقم هاتف العميل</label>
                            <input type="text" class="form-control @error('client_phone') is-invalid @enderror" 
                                   id="client_phone" name="client_phone" value="{{ old('client_phone', $case->client_phone) }}">
                            @error('client_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="case_type" class="form-label">نوع القضية <span class="text-danger">*</span></label>
                            <select class="form-select @error('case_type') is-invalid @enderror" id="case_type" name="case_type" required>
                                <option value="">اختر نوع القضية</option>
                                @foreach(\App\Models\CaseModel::getCaseTypes() as $key => $type)
                                    <option value="{{ $key }}" {{ old('case_type', $case->case_type) == $key ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('case_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">حالة القضية <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                @foreach(\App\Models\CaseModel::getStatuses() as $key => $status)
                                    <option value="{{ $key }}" {{ old('status', $case->status) == $key ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label">الأولوية</label>
                            <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority">
                                @foreach(\App\Models\CaseModel::getPriorities() as $key => $priority)
                                    <option value="{{ $key }}" {{ old('priority', $case->priority) == $key ? 'selected' : '' }}>
                                        {{ $priority }}
                                    </option>
                                @endforeach
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="court_name" class="form-label">اسم المحكمة</label>
                            <input type="text" class="form-control @error('court_name') is-invalid @enderror" 
                                   id="court_name" name="court_name" value="{{ old('court_name', $case->court_name) }}">
                            @error('court_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="court_type" class="form-label">نوع المحكمة</label>
                            <select class="form-select @error('court_type') is-invalid @enderror" id="court_type" name="court_type">
                                <option value="">اختر نوع المحكمة</option>
                                @foreach(\App\Models\CaseModel::getCourtTypes() as $key => $type)
                                    <option value="{{ $key }}" {{ old('court_type', $case->court_type) == $key ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('court_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="opposing_party" class="form-label">الطرف المقابل</label>
                            <input type="text" class="form-control @error('opposing_party') is-invalid @enderror" 
                                   id="opposing_party" name="opposing_party" value="{{ old('opposing_party', $case->opposing_party) }}">
                            @error('opposing_party')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="case_summary" class="form-label">ملخص القضية</label>
                        <textarea class="form-control @error('case_summary') is-invalid @enderror" 
                                  id="case_summary" name="case_summary" rows="3">{{ old('case_summary', $case->case_summary) }}</textarea>
                        @error('case_summary')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">وصف تفصيلي</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4">{{ old('description', $case->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3">{{ old('notes', $case->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- التواريخ والجلسات -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar ms-2"></i>
                        التواريخ والجلسات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">تاريخ بداية القضية <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date', $case->start_date?->format('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="expected_end_date" class="form-label">التاريخ المتوقع للانتهاء</label>
                            <input type="date" class="form-control @error('expected_end_date') is-invalid @enderror" 
                                   id="expected_end_date" name="expected_end_date" value="{{ old('expected_end_date', $case->expected_end_date?->format('Y-m-d')) }}">
                            @error('expected_end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="next_hearing_date" class="form-label">تاريخ الجلسة القادمة</label>
                            <input type="date" class="form-control @error('next_hearing_date') is-invalid @enderror" 
                                   id="next_hearing_date" name="next_hearing_date" value="{{ old('next_hearing_date', $case->next_hearing_date?->format('Y-m-d')) }}">
                            @error('next_hearing_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="next_hearing_time" class="form-label">وقت الجلسة القادمة</label>
                            <input type="time" class="form-control @error('next_hearing_time') is-invalid @enderror" 
                                   id="next_hearing_time" name="next_hearing_time" 
                                   value="{{ old('next_hearing_time', $case->next_hearing_time ? $case->next_hearing_time->format('H:i') : '') }}">
                            @error('next_hearing_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- المعلومات المالية والإضافية -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-dollar-sign ms-2"></i>
                        المعلومات المالية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="fee_type" class="form-label">نوع الرسوم</label>
                        <select class="form-select @error('fee_type') is-invalid @enderror" id="fee_type" name="fee_type">
                            @foreach(\App\Models\CaseModel::getFeeTypes() as $key => $type)
                                <option value="{{ $key }}" {{ old('fee_type', $case->fee_type) == $key ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                        @error('fee_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="total_fees" class="form-label">إجمالي الأتعاب</label>
                        <input type="number" step="0.01" class="form-control @error('total_fees') is-invalid @enderror" 
                               id="total_fees" name="total_fees" value="{{ old('total_fees', $case->total_fees) }}">
                        @error('total_fees')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="fees_received" class="form-label">الأتعاب المدفوعة</label>
                        <input type="number" step="0.01" class="form-control @error('fees_received') is-invalid @enderror" 
                               id="fees_received" name="fees_received" value="{{ old('fees_received', $case->fees_received) }}">
                        @error('fees_received')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="case_value" class="form-label">قيمة القضية</label>
                        <input type="number" step="0.01" class="form-control @error('case_value') is-invalid @enderror" 
                               id="case_value" name="case_value" value="{{ old('case_value', $case->case_value) }}">
                        @error('case_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="estimated_hours" class="form-label">الساعات المقدرة</label>
                        <input type="number" class="form-control @error('estimated_hours') is-invalid @enderror" 
                               id="estimated_hours" name="estimated_hours" value="{{ old('estimated_hours', $case->estimated_hours) }}">
                        @error('estimated_hours')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cog ms-2"></i>
                        إعدادات إضافية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                               {{ old('is_active', $case->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            القضية نشطة
                        </label>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_archived" name="is_archived" value="1" 
                               {{ old('is_archived', $case->is_archived) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_archived">
                            أرشفة القضية
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- أزرار الحفظ -->
    <div class="d-flex justify-content-between">
        <a href="{{ route('cases.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i> إلغاء
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> حفظ التعديلات
        </button>
    </div>
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحديث حساب الأتعاب المتبقية
    const totalFeesInput = document.getElementById('total_fees');
    const feesReceivedInput = document.getElementById('fees_received');
    
    function updateRemainingFees() {
        const total = parseFloat(totalFeesInput.value) || 0;
        const received = parseFloat(feesReceivedInput.value) || 0;
        const remaining = total - received;
        
        // يمكن إضافة عرض للمبلغ المتبقي هنا
    }
    
    totalFeesInput.addEventListener('input', updateRemainingFees);
    feesReceivedInput.addEventListener('input', updateRemainingFees);
});
</script>
@endpush