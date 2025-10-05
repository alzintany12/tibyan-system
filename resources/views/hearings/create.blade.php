@extends('layouts.app')

@section('title', 'إضافة جلسة جديدة - نظام تبيان')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-calendar-plus ms-2"></i>
        إضافة جلسة جديدة
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('hearings.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right"></i> العودة للقائمة
            </a>
            @if(request('case_id'))
                <a href="{{ route('cases.show', request('case_id')) }}" class="btn btn-outline-primary">
                    <i class="fas fa-eye"></i> عرض القضية
                </a>
            @endif
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

<form action="{{ route('hearings.store') }}" method="POST">
    @csrf
    
    @if(request('case_id'))
        <input type="hidden" name="case_id" value="{{ request('case_id') }}">
    @endif
    
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
                        @if(!request('case_id'))
                        <div class="col-md-12 mb-3">
                            <label for="case_id" class="form-label">القضية <span class="text-danger">*</span></label>
                            <select class="form-select @error('case_id') is-invalid @enderror" id="case_id" name="case_id" required>
                                <option value="">اختر القضية</option>
                                @foreach($cases as $case)
                                    <option value="{{ $case->id }}" {{ old('case_id') == $case->id ? 'selected' : '' }}>
                                        {{ $case->case_number }} - {{ $case->client_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('case_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @else
                        <div class="col-md-12 mb-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle ms-2"></i>
                                <strong>القضية المحددة:</strong> {{ $selectedCase->case_number }} - {{ $selectedCase->client_name }}
                            </div>
                        </div>
                        @endif
                        
                        <div class="col-md-6 mb-3">
                            <label for="hearing_date" class="form-label">تاريخ الجلسة <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('hearing_date') is-invalid @enderror" 
                                   id="hearing_date" name="hearing_date" 
                                   value="{{ old('hearing_date', $selectedDate ?? request('date')) }}" required>
                            @error('hearing_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="hearing_time" class="form-label">وقت الجلسة <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('hearing_time') is-invalid @enderror" 
                                   id="hearing_time" name="hearing_time" value="{{ old('hearing_time') }}" required>
                            @error('hearing_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="court_name" class="form-label">اسم المحكمة</label>
                            <input type="text" class="form-control @error('court_name') is-invalid @enderror" 
                                   id="court_name" name="court_name" value="{{ old('court_name') }}">
                            @error('court_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="court_room" class="form-label">رقم القاعة</label>
                            <input type="text" class="form-control @error('court_room') is-invalid @enderror" 
                                   id="court_room" name="court_room" value="{{ old('court_room') }}">
                            @error('court_room')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="hearing_type" class="form-label">نوع الجلسة</label>
                            <select class="form-select @error('hearing_type') is-invalid @enderror" id="hearing_type" name="hearing_type">
                                <option value="">اختر نوع الجلسة</option>
                                <option value="initial" {{ old('hearing_type') == 'initial' ? 'selected' : '' }}>جلسة أولى</option>
                                <option value="evidence" {{ old('hearing_type') == 'evidence' ? 'selected' : '' }}>جلسة بينات</option>
                                <option value="pleading" {{ old('hearing_type') == 'pleading' ? 'selected' : '' }}>جلسة مرافعة</option>
                                <option value="judgment" {{ old('hearing_type') == 'judgment' ? 'selected' : '' }}>جلسة حكم</option>
                                <option value="appeal" {{ old('hearing_type') == 'appeal' ? 'selected' : '' }}>جلسة استئناف</option>
                                <option value="execution" {{ old('hearing_type') == 'execution' ? 'selected' : '' }}>جلسة تنفيذ</option>
                                <option value="other" {{ old('hearing_type') == 'other' ? 'selected' : '' }}>أخرى</option>
                            </select>
                            @error('hearing_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">حالة الجلسة</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                <option value="postponed" {{ old('status') == 'postponed' ? 'selected' : '' }}>مؤجلة</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغية</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="title" class="form-label">عنوان الجلسة</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" placeholder="مثال: جلسة أولى للدعوى رقم...">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">وصف الجلسة</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" placeholder="تفاصيل الجلسة والأهداف المرجوة منها...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" placeholder="أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- معلومات القضية (إذا تم تحديدها) -->
            @if(request('case_id'))
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase ms-2"></i>
                        معلومات القضية
                    </h5>
                </div>
                <div class="card-body">
                    <h6>{{ $selectedCase->case_number }}</h6>
                    <p class="text-muted">{{ $selectedCase->case_title }}</p>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">العميل:</small>
                            <br>
                            <strong>{{ $selectedCase->client_name }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">نوع القضية:</small>
                            <br>
                            <span class="badge bg-info">{{ \App\Models\CaseModel::getCaseTypes()[$selectedCase->case_type] ?? $selectedCase->case_type }}</span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">الحالة:</small>
                            <br>
                            <span class="badge bg-success">{{ \App\Models\CaseModel::getStatuses()[$selectedCase->status] ?? $selectedCase->status }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">المحكمة:</small>
                            <br>
                            <small>{{ $selectedCase->court_name ?: 'غير محدد' }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
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
                               {{ old('send_notification') ? 'checked' : '' }}>
                        <label class="form-check-label" for="send_notification">
                            إرسال تذكير قبل الجلسة
                        </label>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reminder_minutes" class="form-label">التذكير قبل (بالدقائق)</label>
                        <select class="form-select" id="reminder_minutes" name="reminder_minutes">
                            <option value="30" {{ old('reminder_minutes') == '30' ? 'selected' : '' }}>30 دقيقة</option>
                            <option value="60" {{ old('reminder_minutes', '60') == '60' ? 'selected' : '' }}>ساعة واحدة</option>
                            <option value="120" {{ old('reminder_minutes') == '120' ? 'selected' : '' }}>ساعتان</option>
                            <option value="1440" {{ old('reminder_minutes') == '1440' ? 'selected' : '' }}>يوم كامل</option>
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
        <a href="{{ route('hearings.index') }}" class="btn btn-secondary">
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

@endsection

@push('scripts')
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
@endpush