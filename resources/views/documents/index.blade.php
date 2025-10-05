@extends('layouts.app')

@section('page-title', 'إدارة المستندات')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">المستندات</li>
@endsection

@section('page-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('documents.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>مستند جديد
        </a>
        <a href="{{ route('documents.templates') }}" class="btn btn-info">
            <i class="fas fa-file-contract me-2"></i>القوالب
        </a>
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-download me-2"></i>تصدير
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
            </ul>
        </div>
    </div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-file-alt me-2"></i>قائمة المستندات
        </h5>
    </div>
    <div class="card-body">
        <!-- فلاتر البحث -->
        <div class="row mb-4">
            <div class="col-md-3">
                <label for="search" class="form-label">البحث</label>
                <input type="text" class="form-control" id="search" placeholder="عنوان المستند...">
            </div>
            <div class="col-md-2">
                <label for="case_filter" class="form-label">القضية</label>
                <select class="form-select select2" id="case_filter">
                    <option value="">جميع القضايا</option>
                    @foreach($cases as $case)
                        <option value="{{ $case->id }}">{{ $case->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="type_filter" class="form-label">النوع</label>
                <select class="form-select" id="type_filter">
                    <option value="">جميع الأنواع</option>
                    <option value="contract">عقد</option>
                    <option value="pleading">مرافعة</option>
                    <option value="correspondence">مراسلة</option>
                    <option value="evidence">دليل</option>
                    <option value="other">أخرى</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_filter" class="form-label">التاريخ</label>
                <input type="date" class="form-control" id="date_filter">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" id="apply_filters">
                        <i class="fas fa-search me-1"></i>بحث
                    </button>
                    <button type="button" class="btn btn-secondary" id="clear_filters">
                        <i class="fas fa-times me-1"></i>مسح
                    </button>
                </div>
            </div>
        </div>

        <!-- جدول المستندات -->
        <div class="table-responsive">
            <table class="table table-striped" id="documents-table">
                <thead>
                    <tr>
                        <th>العنوان</th>
                        <th>النوع</th>
                        <th>القضية</th>
                        <th>الحجم</th>
                        <th>المالك</th>
                        <th>تاريخ الإنشاء</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $document)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="file-icon me-2">
                                    @if($document->mime_type && str_contains($document->mime_type, 'pdf'))
                                        <i class="fas fa-file-pdf text-danger fa-lg"></i>
                                    @elseif($document->mime_type && str_contains($document->mime_type, 'word'))
                                        <i class="fas fa-file-word text-primary fa-lg"></i>
                                    @elseif($document->mime_type && str_contains($document->mime_type, 'image'))
                                        <i class="fas fa-file-image text-success fa-lg"></i>
                                    @else
                                        <i class="fas fa-file text-muted fa-lg"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $document->title }}</h6>
                                    @if($document->description)
                                        <small class="text-muted">{{ Str::limit($document->description, 50) }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $document->type === 'contract' ? 'primary' : ($document->type === 'pleading' ? 'success' : 'info') }}">
                                @switch($document->type)
                                    @case('contract') عقد @break
                                    @case('pleading') مرافعة @break
                                    @case('correspondence') مراسلة @break
                                    @case('evidence') دليل @break
                                    @default أخرى
                                @endswitch
                            </span>
                        </td>
                        <td>
                            @if($document->legalCase)
                                <a href="{{ route('cases.show', $document->legalCase) }}" class="text-decoration-none">
                                    {{ $document->legalCase->title }}
                                </a>
                            @else
                                <span class="text-muted">غير مرتبط</span>
                            @endif
                        </td>
                        <td>
                            @if($document->file_size)
                                <small class="text-muted">{{ number_format($document->file_size / 1024, 1) }} KB</small>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </td>
                        <td>{{ $document->user->name ?? 'غير محدد' }}</td>
                        <td>
                            <small class="text-muted">{{ $document->created_at->format('Y/m/d H:i') }}</small>
                        </td>
                        <td>
                            @if($document->is_confidential)
                                <span class="badge bg-warning">
                                    <i class="fas fa-lock me-1"></i>سري
                                </span>
                            @else
                                <span class="badge bg-success">عام</span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('documents.show', $document) }}">
                                        <i class="fas fa-eye me-2"></i>عرض
                                    </a></li>
                                    @if($document->file_path)
                                        <li><a class="dropdown-item" href="{{ route('documents.download', $document) }}">
                                            <i class="fas fa-download me-2"></i>تحميل
                                        </a></li>
                                    @endif
                                    <li><a class="dropdown-item" href="{{ route('documents.edit', $document) }}">
                                        <i class="fas fa-edit me-2"></i>تعديل
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('documents.duplicate', $document) }}">
                                        <i class="fas fa-copy me-2"></i>نسخ
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('documents.destroy', $document) }}" onsubmit="return confirmDelete(this)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-trash me-2"></i>حذف
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-file-alt fa-3x mb-3 d-block"></i>
                                <h5>لا توجد مستندات</h5>
                                <p>ابدأ بإضافة أول مستند</p>
                                <a href="{{ route('documents.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>إضافة مستند
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($documents->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
</div>

<!-- إحصائيات سريعة -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $documents->count() }}</h4>
                        <p class="card-text">إجمالي المستندات</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $documents->where('is_confidential', true)->count() }}</h4>
                        <p class="card-text">مستند سري</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-lock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $documents->where('type', 'contract')->count() }}</h4>
                        <p class="card-text">عقد</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-contract fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $documents->filter(function($doc) { return $doc->created_at->isCurrentMonth(); })->count() }}</h4>
                        <p class="card-text">هذا الشهر</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-plus fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // تفعيل DataTable
    var table = $('#documents-table').DataTable({
        order: [[5, 'desc']],
        columnDefs: [
            { orderable: false, targets: [7] }
        ]
    });

    // فلاتر البحث
    $('#apply_filters').click(function() {
        var search = $('#search').val();
        var caseId = $('#case_filter').val();
        var type = $('#type_filter').val();
        var date = $('#date_filter').val();

        var url = new URL(window.location.href);
        
        if (search) url.searchParams.set('search', search);
        else url.searchParams.delete('search');
        
        if (caseId) url.searchParams.set('case_id', caseId);
        else url.searchParams.delete('case_id');
        
        if (type) url.searchParams.set('type', type);
        else url.searchParams.delete('type');
        
        if (date) url.searchParams.set('date', date);
        else url.searchParams.delete('date');

        window.location.href = url.toString();
    });

    // مسح الفلاتر
    $('#clear_filters').click(function() {
        $('#search, #case_filter, #type_filter, #date_filter').val('');
        $('.select2').val(null).trigger('change');
        window.location.href = window.location.pathname;
    });

    // البحث عند الضغط على Enter
    $('#search').keypress(function(e) {
        if (e.which == 13) {
            $('#apply_filters').click();
        }
    });
});
</script>
@endpush
