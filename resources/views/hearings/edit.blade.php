@extends('layouts.app')

@section('page-title', 'تعديل الجلسة')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('hearings.index') }}">الجلسات</a></li>
    <li class="breadcrumb-item"><a href="{{ route('hearings.show', $hearing) }}">تفاصيل الجلسة</a></li>
    <li class="breadcrumb-item active">تعديل</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i>
                        تعديل الجلسة
                    </h3>
                </div>
                
                <form action="{{ route('hearings.update', $hearing) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
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
                                                {{ old('case_id', $hearing->case_id) == $case->id ? 'selected' : '' }}
                                                data-client="{{ $case->client_name }}"
                                                data-court="{{ $case->court_name }}">
                                                {{ $case->case_number }} - {{ $case->client_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- عنوان الجلسة -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">عنوان الجلسة</label>
                                    <input type="text" name="title" id="title" class="form-control" 
                                           value="{{ old('title', $hearing->title) }}" placeholder="عنوان الجلسة">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- تاريخ الجلسة -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="hearing_date" class="required">تاريخ الجلسة</label>
                                    <input type="date" name="hearing_date" id="hearing_date" class="form-control" 
                                           value="{{ old('hearing_date', $hearing->hearing_date->format('Y-m-d')) }}" required>
                                </div>
                            </div>

                            <!-- وقت الجلسة -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="hearing_time" class="required">وقت الجلسة</label>
                                    <input type="time" name="hearing_time" id="hearing_time" class="form-control" 
                                           value="{{ old('hearing_time', substr($hearing->hearing_time, 0, 5)) }}" required>
                                </div>
                            </div>

                            <!-- نوع الجلسة -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="hearing_type">نوع الجلسة</label>
                                    <select name="hearing_type" id="hearing_type" class="form-control">
                                        <option value="">اختر نوع الجلسة</option>
                                        <option value="initial" {{ old('hearing_type', $hearing->hearing_type) == 'initial' ? 'selected' : '' }}>افتتاحية</option>
                                        <option value="evidence" {{ old('hearing_type', $hearing->hearing_type) == 'evidence' ? 'selected' : '' }}>جلسة أدلة</option>
                                        <option value="pleading" {{ old('hearing_type', $hearing->hearing_type) == 'pleading' ? 'selected' : '' }}>جلسة مرافعة</option>
                                        <option value="judgment" {{ old('hearing_type', $hearing->hearing_type) == 'judgment' ? 'selected' : '' }}>جلسة حكم</option>
                                        <option value="appeal" {{ old('hearing_type', $hearing->hearing_type) == 'appeal' ? 'selected' : '' }}>استئناف</option>
                                        <option value="execution" {{ old('hearing_type', $hearing->hearing_type) == 'execution' ? 'selected' : '' }}>تنفيذ</option>
                                        <option value="other" {{ old('hearing_type', $hearing->hearing_type) == 'other' ? 'selected' : '' }}>أخرى</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- اسم المحكمة -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="court_name">اسم المحكمة</label>
                                    <input type="text" name="court_name" id="court_name" class="form-control" 
                                           value="{{ old('court_name', $hearing->court_name) }}" placeholder="اسم المحكمة">
                                </div>
                            </div>

                            <!-- قاعة المحكمة -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="court_room">قاعة المحكمة</label>
                                    <input type="text" name="court_room" id="court_room" class="form-control" 
                                           value="{{ old('court_room', $hearing->court_room) }}" placeholder="رقم القاعة">
                                </div>
                            </div>

                            <!-- حالة الجلسة -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status" class="required">حالة الجلسة</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="scheduled" {{ old('status', $hearing->status) == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                                        <option value="completed" {{ old('status', $hearing->status) == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                        <option value="postponed" {{ old('status', $hearing->status) == 'postponed' ? 'selected' : '' }}>مؤجلة</option>
                                        <option value="cancelled" {{ old('status', $hearing->status) == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- وصف الجلسة -->
                        <div class="form-group">
                            <label for="description">وصف الجلسة</label>
                            <textarea name="description" id="description" class="form-control" rows="3" 
                                      placeholder="وصف موجز عن الجلسة">{{ old('description', $hearing->description) }}</textarea>
                        </div>

                        <!-- نتيجة الجلسة -->
                        <div class="form-group">
                            <label for="result">نتيجة الجلسة</label>
                            <textarea name="result" id="result" class="form-control" rows="3" 
                                      placeholder="نتيجة الجلسة (إن وجدت)">{{ old('result', $hearing->result) }}</textarea>
                        </div>

                        <!-- ملاحظات -->
                        <div class="form-group">
                            <label for="notes">ملاحظات</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" 
                                      placeholder="ملاحظات إضافية">{{ old('notes', $hearing->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    حفظ التعديلات
                                </button>
                                <a href="{{ route('hearings.show', $hearing) }}" class="btn btn-secondary">
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
    // تحديث بيانات المحكمة عند تغيير القضية
    $('#case_id').change(function() {
        var selectedOption = $(this).find('option:selected');
        var courtName = selectedOption.data('court');
        
        if (courtName && !$('#court_name').val()) {
            $('#court_name').val(courtName);
        }
    });
    
    // إظهار/إخفاء حقل النتيجة بناءً على حالة الجلسة
    $('#status').change(function() {
        var status = $(this).val();
        if (status === 'completed') {
            $('#result').prop('required', true);
            $('#result').closest('.form-group').show();
        } else {
            $('#result').prop('required', false);
            if (status !== 'completed') {
                $('#result').closest('.form-group').show();
            }
        }
    }).trigger('change');
});
</script>
@endpush

@push('styles')
<style>
.required::after {
    content: " *";
    color: red;
}
</style>
@endpush