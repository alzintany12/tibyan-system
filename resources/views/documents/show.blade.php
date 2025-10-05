@extends('layouts.app')

@section('page-title', 'عرض مستند')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">المستندات</a></li>
    <li class="breadcrumb-item active">عرض مستند</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="fas fa-file-alt me-2"></i>{{ $document->title }}</h5>
        <div>
            <a href="{{ route('documents.edit', $document->id) }}" class="btn btn-sm btn-warning">
                <i class="fas fa-edit me-1"></i> تعديل
            </a>
            <form action="{{ route('documents.destroy', $document->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash me-1"></i> حذف
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <p><strong>الوصف:</strong> {{ $document->description ?? 'لا يوجد' }}</p>
        <p><strong>النوع:</strong> 
            @switch($document->type)
                @case('contract') عقد @break
                @case('pleading') مرافعة @break
                @case('correspondence') مراسلة @break
                @case('evidence') دليل @break
                @default أخرى
            @endswitch
        </p>
        <p><strong>القضية:</strong> 
            @if($document->legalCase)
                <a href="{{ route('cases.show', $document->legalCase->id) }}">{{ $document->legalCase->title }}</a>
            @else
                غير مرتبط
            @endif
        </p>
        <p><strong>القالب:</strong> {{ $document->template ? $document->template->name : 'بدون' }}</p>
        <p><strong>الحجم:</strong> {{ number_format($document->size / 1024, 2) }} KB</p>
        <p><strong>المالك:</strong> {{ $document->owner->name ?? 'غير محدد' }}</p>
        <p><strong>الحالة:</strong> {{ $document->is_confidential ? 'سري' : 'عادي' }}</p>
        <p><strong>تاريخ الإنشاء:</strong> {{ $document->created_at->format('Y-m-d H:i') }}</p>

        <div class="mt-4">
            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="btn btn-primary">
                <i class="fas fa-download me-2"></i>عرض / تحميل الملف
            </a>
            <a href="{{ route('documents.index') }}" class="btn btn-secondary">رجوع</a>
        </div>
    </div>
</div>
@endsection
