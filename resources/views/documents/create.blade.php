@extends('layouts.app')

@section('page-title', 'إضافة مستند جديد')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">المستندات</a></li>
    <li class="breadcrumb-item active">إضافة مستند</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-plus me-2"></i>إضافة مستند جديد</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">عنوان المستند</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">الوصف</label>
                <textarea name="description" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">النوع</label>
                <select name="type" class="form-select" required>
                    <option value="contract">عقد</option>
                    <option value="pleading">مرافعة</option>
                    <option value="correspondence">مراسلة</option>
                    <option value="evidence">دليل</option>
                    <option value="other">أخرى</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">القضية</label>
                <select name="legal_case_id" class="form-select" required>
                    <option value="">-- اختر قضية --</option>
                    @foreach($cases as $case)
                        <option value="{{ $case->id }}" {{ $selectedCase && $selectedCase->id == $case->id ? 'selected' : '' }}>
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
                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">الملف</label>
                <input type="file" name="file" class="form-control" required>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="is_confidential" value="1" class="form-check-input" id="confidential">
                <label for="confidential" class="form-check-label">سري</label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>حفظ
            </button>
            <a href="{{ route('documents.index') }}" class="btn btn-secondary">إلغاء</a>
        </form>
    </div>
</div>
@endsection
