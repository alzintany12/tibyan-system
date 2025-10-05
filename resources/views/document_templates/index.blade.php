@extends('layouts.app')

@section('page-title', 'قوالب المستندات')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">قوالب المستندات</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-file-alt"></i>
                        قوالب المستندات
                    </h4>
                    <a href="{{ route('document-templates.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i>
                        إنشاء قالب جديد
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- فلاتر البحث -->
                <form method="GET" action="{{ route('document-templates.index') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>البحث</label>
                                <input type="text" name="search" class="form-control" 
                                    placeholder="اسم القالب أو الوصف..." 
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>النوع</label>
                                <select name="type" class="form-control">
                                    <option value="">جميع الأنواع</option>
                                    <option value="contract" {{ request('type') == 'contract' ? 'selected' : '' }}>عقد</option>
                                    <option value="pleading" {{ request('type') == 'pleading' ? 'selected' : '' }}>مرافعة</option>
                                    <option value="correspondence" {{ request('type') == 'correspondence' ? 'selected' : '' }}>مراسلة</option>
                                    <option value="evidence" {{ request('type') == 'evidence' ? 'selected' : '' }}>دليل</option>
                                    <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>أخرى</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>الفئة</label>
                                <select name="category" class="form-control">
                                    <option value="">جميع الفئات</option>
                                    <option value="legal" {{ request('category') == 'legal' ? 'selected' : '' }}>قانونية</option>
                                    <option value="administrative" {{ request('category') == 'administrative' ? 'selected' : '' }}>إدارية</option>
                                    <option value="financial" {{ request('category') == 'financial' ? 'selected' : '' }}>مالية</option>
                                    <option value="evidence" {{ request('category') == 'evidence' ? 'selected' : '' }}>أدلة</option>
                                    <option value="correspondence" {{ request('category') == 'correspondence' ? 'selected' : '' }}>مراسلات</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                                <a href="{{ route('document-templates.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times"></i> إلغاء
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- جدول القوالب -->
                @if($templates->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>اسم القالب</th>
                                <th>النوع</th>
                                <th>الفئة</th>
                                <th>الوصف</th>
                                <th>المنشئ</th>
                                <th>تاريخ الإنشاء</th>
                                <th>قالب النظام</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templates as $template)
                            <tr>
                                <td>
                                    <strong>{{ $template->name }}</strong>
                                    @if($template->file_path)
                                        <br><small class="text-muted">
                                            <i class="fas fa-paperclip"></i> ملف مرفق
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $typeNames = [
                                            'contract' => 'عقد',
                                            'pleading' => 'مرافعة',
                                            'correspondence' => 'مراسلة',
                                            'evidence' => 'دليل',
                                            'other' => 'أخرى'
                                        ];
                                    @endphp
                                    <span class="badge badge-info">
                                        {{ $typeNames[$template->type] ?? $template->type }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $categoryNames = [
                                            'legal' => 'قانونية',
                                            'administrative' => 'إدارية',
                                            'financial' => 'مالية',
                                            'evidence' => 'أدلة',
                                            'correspondence' => 'مراسلات'
                                        ];
                                    @endphp
                                    <span class="badge badge-secondary">
                                        {{ $categoryNames[$template->category] ?? $template->category }}
                                    </span>
                                </td>
                                <td>
                                    @if($template->description)
                                        {{ Str::limit($template->description, 50) }}
                                    @else
                                        <span class="text-muted">لا يوجد وصف</span>
                                    @endif
                                </td>
                                <td>
                                    @if($template->creator)
                                        {{ $template->creator->name }}
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </td>
                                <td>{{ $template->created_at->format('Y-m-d') }}</td>
                                <td>
                                    @if($template->is_system_default)
                                        <span class="badge badge-success">نعم</span>
                                    @else
                                        <span class="badge badge-light">لا</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('document-templates.show', $template) }}" 
                                            class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('document-templates.edit', $template) }}" 
                                            class="btn btn-sm btn-outline-secondary" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($template->file_path)
                                        <a href="{{ route('document-templates.download', $template) }}" 
                                            class="btn btn-sm btn-outline-success" title="تحميل الملف">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                data-toggle="modal" data-target="#generateModal{{ $template->id }}"
                                                title="إنشاء مستند">
                                            <i class="fas fa-magic"></i>
                                        </button>
                                        <form method="POST" action="{{ route('document-templates.duplicate', $template) }}" 
                                              style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning" 
                                                    title="نسخ القالب">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- الصفحات -->
                <div class="d-flex justify-content-center">
                    {{ $templates->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد قوالب</h5>
                    <p class="text-muted">لم يتم العثور على أي قوالب مستندات</p>
                    <a href="{{ route('document-templates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إنشاء قالب جديد
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal إنشاء مستند من القالب -->
@foreach($templates as $template)
<div class="modal fade" id="generateModal{{ $template->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إنشاء مستند من القالب: {{ $template->name }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('document-templates.generate', $template) }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>القضية (اختياري)</label>
                        <select name="case_id" class="form-control">
                            <option value="">اختر القضية</option>
                            @foreach(\App\Models\LegalCase::where('is_active', true)->get() as $case)
                                <option value="{{ $case->id }}">{{ $case->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    @if($template->variables && count($template->variables) > 0)
                        <h6>المتغيرات:</h6>
                        @foreach($template->variables as $variable)
                        <div class="form-group">
                            <label>{{ $variable }}</label>
                            <input type="text" name="variables[{{ $variable }}]" class="form-control" 
                                   placeholder="أدخل قيمة {{ $variable }}">
                        </div>
                        @endforeach
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-magic"></i> إنشاء المستند
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('styles')
<style>
.card {
    border: none;
    border-radius: 10px;
}
.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.9rem;
}
.btn-group .btn {
    border-radius: 0;
}
.btn-group .btn:first-child {
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px;
}
.btn-group .btn:last-child {
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // إضافة تأثيرات على الجدول
    $('tbody tr').hover(function() {
        $(this).addClass('table-active');
    }, function() {
        $(this).removeClass('table-active');
    });
});
</script>
@endpush