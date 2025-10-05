@extends('layouts.app')

@section('page-title', 'تعديل مستند')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">المستندات</a></li>
    <li class="breadcrumb-item active">تعديل مستند</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-edit me-2"></i>تعديل مستند</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('documents.update', $document->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">عنوان المستند</label>
                <input type="text" name="title" value="{{ old('title', $document->title) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">الوصف</label>
                <textarea name="description" class="form-control">{{ old('description', $document->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">النوع</label>
                <select name="type" class="form-select" required>
                    <option value="contract" {{ $document->type == 'contract' ? 'selected' : '' }}>عقد</option>
                    <option value="pleading" {{ $document->type == 'pleading' ? 'selected' : '' }}>مرافعة</option>
                    <option value="correspondence" {{ $document->type == 'correspondence' ? 'selected' : '' }}>مراسلة</option>
                    <option value="evidence" {{ $document->type == 'evidence' ? 'selected' : '' }}>دليل</option>
                    <option value="other" {{ $document->type == 'other' ? 'selected' : '' }}>أخرى</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">القضية</label>
                <select name="legal_case_id" class="form-select" required>
                    <option value="">-- اختر قضية --</option>
                    @foreach($cases as $case)
                        <option value="{{ $case->id }}" {{ $document->legal_case_id == $case->id ? 'selected' : '' }}>
                            {{ $case->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">القالب</label>
                <select name="template_id" class="form-select">
                    <option value="">-- بدون قالب --</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}" {{ $document->template_id == $template->id ? 'selected' : '' }}>
                            {{ $template->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">الملف الحالي</label><br>
                <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-file-alt me-2"></i>عرض الملف
                </a>
            </div>

            <div class="mb-3">
                <label class="form-label">استبدال الملف (اختياري)</label>
                <input type="file" name="file" class="form-control">
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="is_confidential" value="1" class="form-check-input" id="confidential" {{ $document->is_confidential ? 'checked' : '' }}>
                <label for="confidential" class="form-check-label">سري</label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>تحديث
            </button>
            <a href="{{ route('documents.index') }}" class="btn btn-secondary">إلغاء</a>
        </form>
    </div>
</div>
@endsection
