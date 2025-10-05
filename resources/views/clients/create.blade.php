@extends('layouts.app')

@section('page-title', 'إضافة عميل جديد')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">العملاء</a></li>
    <li class="breadcrumb-item active">إضافة عميل جديد</li>
@endsection

@section('content')
<form method="POST" action="{{ route('clients.store') }}" enctype="multipart/form-data">
    @csrf
    
    <div class="row">
        <div class="col-lg-8">
            <!-- المعلومات الأساسية -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>المعلومات الأساسية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="type" class="form-label">نوع العميل <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">اختر النوع</option>
                                <option value="individual" {{ old('type') === 'individual' ? 'selected' : '' }}>فرد</option>
                                <option value="company" {{ old('type') === 'company' ? 'selected' : '' }}>شركة</option>
                                <option value="government" {{ old('type') === 'government' ? 'selected' : '' }}>جهة حكومية</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mt-3" id="company-fields" style="display: none;">
                        <div class="col-md-6">
                            <label for="company_name" class="form-label">اسم الشركة</label>
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                   id="company_name" name="company_name" value="{{ old('company_name') }}">
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="commercial_register" class="form-label">السجل التجاري</label>
                            <input type="text" class="form-control @error('commercial_register') is-invalid @enderror" 
                                   id="commercial_register" name="commercial_register" value="{{ old('commercial_register') }}">
                            @error('commercial_register')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">رقم الهاتف</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mt-3" id="individual-fields">
                        <div class="col-md-6">
                            <label for="national_id" class="form-label">رقم الهوية الوطنية</label>
                            <input type="text" class="form-control @error('national_id') is-invalid @enderror" 
                                   id="national_id" name="national_id" value="{{ old('national_id') }}">
                            @error('national_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="birth_date" class="form-label">تاريخ الميلاد</label>
                            <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                                   id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                            @error('birth_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- معلومات الاتصال والعنوان -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>معلومات الاتصال والعنوان
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="address" class="form-label">العنوان</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label for="city" class="form-label">المدينة</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                   id="city" name="city" value="{{ old('city') }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="postal_code" class="form-label">الرمز البريدي</label>
                            <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                   id="postal_code" name="postal_code" value="{{ old('postal_code') }}">
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="country" class="form-label">الدولة</label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                   id="country" name="country" value="{{ old('country', 'السعودية') }}">
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="secondary_phone" class="form-label">هاتف إضافي</label>
                            <input type="tel" class="form-control @error('secondary_phone') is-invalid @enderror" 
                                   id="secondary_phone" name="secondary_phone" value="{{ old('secondary_phone') }}">
                            @error('secondary_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="website" class="form-label">الموقع الإلكتروني</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                   id="website" name="website" value="{{ old('website') }}" placeholder="https://example.com">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ملاحظات إضافية -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-sticky-note me-2"></i>ملاحظات إضافية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="4" placeholder="أي ملاحظات مهمة حول العميل...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- اللوحة الجانبية -->
        <div class="col-lg-4">
            <!-- الحالة والإعدادات -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog me-2"></i>الإعدادات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                عميل نشط
                            </label>
                        </div>
                        <small class="form-text text-muted">إلغاء التحديد سيخفي العميل من القوائم الرئيسية</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="priority" class="form-label">مستوى الأولوية</label>
                        <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority">
                            <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>عادي</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>مهم</option>
                            <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>عاجل</option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="source" class="form-label">مصدر العميل</label>
                        <select class="form-select @error('source') is-invalid @enderror" id="source" name="source">
                            <option value="">اختر المصدر</option>
                            <option value="referral" {{ old('source') === 'referral' ? 'selected' : '' }}>إحالة</option>
                            <option value="website" {{ old('source') === 'website' ? 'selected' : '' }}>الموقع الإلكتروني</option>
                            <option value="social_media" {{ old('source') === 'social_media' ? 'selected' : '' }}>وسائل التواصل</option>
                            <option value="advertisement" {{ old('source') === 'advertisement' ? 'selected' : '' }}>إعلان</option>
                            <option value="other" {{ old('source') === 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                        @error('source')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- الصورة الشخصية -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-image me-2"></i>الصورة الشخصية
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img id="avatar-preview" src="{{ asset('images/default-avatar.png') }}" 
                             alt="معاينة الصورة" class="rounded-circle" width="100" height="100">
                    </div>
                    
                    <div class="mb-3">
                        <input type="file" class="form-control @error('avatar') is-invalid @enderror" 
                               id="avatar" name="avatar" accept="image/*">
                        @error('avatar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">اختياري - JPG, PNG (حد أقصى 2MB)</small>
                    </div>
                </div>
            </div>
            
            <!-- أزرار الحفظ -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>حفظ العميل
                        </button>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>إلغاء
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // إظهار/إخفاء الحقول حسب نوع العميل
    $('#type').change(function() {
        var type = $(this).val();
        
        if (type === 'company' || type === 'government') {
            $('#company-fields').show();
            $('#individual-fields').hide();
        } else if (type === 'individual') {
            $('#company-fields').hide();
            $('#individual-fields').show();
        } else {
            $('#company-fields, #individual-fields').hide();
        }
    });
    
    // تفعيل التغيير عند التحميل
    $('#type').trigger('change');
    
    // معاينة الصورة
    $('#avatar').change(function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#avatar-preview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // تنسيق رقم الهاتف
    $('#phone, #secondary_phone').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.startsWith('966')) {
            value = '+' + value;
        } else if (value.startsWith('05')) {
            value = '+966' + value.substring(1);
        } else if (value.length === 9 && value.startsWith('5')) {
            value = '+966' + value;
        }
        $(this).val(value);
    });
    
    // تنسيق رقم الهوية
    $('#national_id').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length > 10) {
            value = value.substring(0, 10);
        }
        $(this).val(value);
    });
    
    // تنسيق السجل التجاري
    $('#commercial_register').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length > 10) {
            value = value.substring(0, 10);
        }
        $(this).val(value);
    });
});
</script>
@endpush