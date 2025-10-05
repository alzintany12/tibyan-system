@extends('layouts.app')

@section('title', 'تفاصيل الجلسة - نظام تبيان')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-calendar-check ms-2"></i>
        تفاصيل الجلسة
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2" role="group">
            <a href="{{ route('hearings.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right"></i> العودة للقائمة
            </a>
            @if($hearing->canBeModified())
                <a href="{{ route('hearings.edit', $hearing) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit"></i> تعديل
                </a>
            @endif
            @if($hearing->status == 'scheduled')
                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#completeModal">
                    <i class="fas fa-check"></i> إكمال الجلسة
                </button>
                <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#postponeModal">
                    <i class="fas fa-clock"></i> تأجيل
                </button>
            @endif
        </div>
    </div>
</div>

<!-- معلومات الجلسة الأساسية -->
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle ms-2"></i>
                    معلومات الجلسة
                </h5>
                <span class="badge bg-{{ $hearing->status_color }} fs-6">
                    {{ \App\Models\Hearing::getStatuses()[$hearing->status] ?? $hearing->status }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" width="30%">التاريخ:</td>
                                <td>{{ $hearing->hearing_date ? $hearing->hearing_date->format('Y/m/d') : 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">الوقت:</td>
                                <td>{{ $hearing->formatted_time ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">نوع الجلسة:</td>
                                <td>{{ \App\Models\Hearing::getHearingTypes()[$hearing->hearing_type] ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">المحكمة:</td>
                                <td>{{ $hearing->court_name ?? 'غير محدد' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" width="30%">القاعة:</td>
                                <td>{{ $hearing->court_room ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">القاضي:</td>
                                <td>{{ $hearing->judge_name ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">إنشأ بواسطة:</td>
                                <td>{{ $hearing->created_by ?? 'النظام' }}</td>
                            </tr>
                            @if($hearing->updated_by)
                                <tr>
                                    <td class="fw-bold">آخر تحديث:</td>
                                    <td>{{ $hearing->updated_by }} ({{ $hearing->updated_at ? $hearing->updated_at->format('Y/m/d H:i') : '' }})</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($hearing->title)
                    <div class="mt-3">
                        <h6 class="fw-bold">العنوان:</h6>
                        <p class="mb-0">{{ $hearing->title }}</p>
                    </div>
                @endif

                @if($hearing->description)
                    <div class="mt-3">
                        <h6 class="fw-bold">الوصف:</h6>
                        <p class="mb-0">{{ $hearing->description }}</p>
                    </div>
                @endif

                @if($hearing->notes)
                    <div class="mt-3">
                        <h6 class="fw-bold">ملاحظات:</h6>
                        <p class="mb-0">{{ $hearing->notes }}</p>
                    </div>
                @endif

                @if($hearing->result)
                    <div class="mt-3">
                        <h6 class="fw-bold">النتيجة:</h6>
                        <p class="mb-0">{{ $hearing->result }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- معلومات القضية -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-briefcase ms-2"></i>
                    معلومات القضية
                </h5>
            </div>
            <div class="card-body">
                @if($hearing->case)
                    <p><strong>رقم القضية:</strong><br>
                    <a href="{{ route('cases.show', $hearing->case) }}" class="text-primary">
                        {{ $hearing->case->case_number }}
                    </a></p>
                    
                    @if($hearing->case->case_title)
                        <p><strong>عنوان القضية:</strong><br>{{ $hearing->case->case_title }}</p>
                    @endif
                    
                    <p><strong>العميل:</strong><br>{{ $hearing->case->client_name ?? 'غير محدد' }}</p>
                    
                    @if($hearing->case->client_phone)
                        <p><strong>هاتف العميل:</strong><br>{{ $hearing->case->client_phone }}</p>
                    @endif
                    
                    <p><strong>نوع القضية:</strong><br>
                    {{ \App\Models\CaseModel::getCaseTypes()[$hearing->case->case_type] ?? $hearing->case->case_type }}
                    </p>
                    
                    <p><strong>حالة القضية:</strong><br>
                    <span class="badge bg-{{ $hearing->case->status == 'active' ? 'success' : 'secondary' }}">
                        {{ \App\Models\CaseModel::getStatuses()[$hearing->case->status] ?? $hearing->case->status }}
                    </span>
                    </p>
                @else
                    <p class="text-muted">لا توجد قضية مرتبطة</p>
                @endif
            </div>
        </div>

        <!-- معلومات التوقيت -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-clock ms-2"></i>
                    معلومات التوقيت
                </h5>
            </div>
            <div class="card-body">
                @php
                    $timingInfo = $hearing->getTimingInfo();
                @endphp
                
                @if($timingInfo)
                    <p class="{{ $timingInfo['class'] }}">
                        <i class="fas fa-info-circle ms-1"></i>
                        {{ $timingInfo['message'] }}
                    </p>
                @endif
                
                <p><strong>تاريخ الإنشاء:</strong><br>
                {{ $hearing->created_at ? $hearing->created_at->format('Y/m/d H:i') : 'غير محدد' }}</p>
                
                @if($hearing->completed_at)
                    <p><strong>تاريخ الإكمال:</strong><br>
                    {{ $hearing->completed_at->format('Y/m/d H:i') }}</p>
                @endif
                
                @if($hearing->postponed_to)
                    <p><strong>تم التأجيل إلى:</strong><br>
                    <a href="{{ route('hearings.show', $hearing->postponed_to) }}" class="text-primary">
                        الجلسة الجديدة
                    </a></p>
                @endif
                
                @if($hearing->postpone_reason)
                    <p><strong>سبب التأجيل:</strong><br>{{ $hearing->postpone_reason }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal لإكمال الجلسة -->
<div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completeModalLabel">إكمال الجلسة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('hearings.complete', $hearing) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="result" class="form-label">نتيجة الجلسة:</label>
                        <textarea class="form-control" id="result" name="result" rows="3" 
                                  placeholder="اكتب نتيجة الجلسة..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظات إضافية:</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="أي ملاحظات إضافية...">{{ $hearing->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> إكمال الجلسة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal لتأجيل الجلسة -->
<div class="modal fade" id="postponeModal" tabindex="-1" aria-labelledby="postponeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="postponeModalLabel">تأجيل الجلسة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('hearings.postpone', $hearing) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_date" class="form-label">التاريخ الجديد:</label>
                        <input type="date" class="form-control" id="new_date" name="new_date" 
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_time" class="form-label">الوقت الجديد:</label>
                        <input type="time" class="form-control" id="new_time" name="new_time" required>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">سبب التأجيل:</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" 
                                  placeholder="اكتب سبب التأجيل..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-clock"></i> تأجيل الجلسة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // تعيين قيم افتراضية للتأجيل
    document.getElementById('new_date').addEventListener('change', function() {
        if (this.value && !document.getElementById('new_time').value) {
            document.getElementById('new_time').value = '{{ $hearing->formatted_time ?? "10:00" }}';
        }
    });
</script>
@endpush