@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">✏️ تعديل القالب</h1>

    <form action="{{ route('document-templates.update', $documentTemplate) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">اسم القالب</label>
            <input type="text" name="name" value="{{ old('name', $documentTemplate->name) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">محتوى القالب</label>
            <textarea id="templateContent" name="content" class="form-control" rows="6" required>{{ old('content', $documentTemplate->content) }}</textarea>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ $documentTemplate->is_active ? 'checked' : '' }}>
            <label class="form-check-label">نشط</label>
        </div>

        <button type="submit" class="btn btn-success">💾 تحديث</button>
        <a href="{{ route('document-templates.index') }}" class="btn btn-secondary">⬅️ رجوع</a>
    </form>
</div>
@endsection

@section('scripts')
<!-- ✅ TinyMCE -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#templateContent',
        directionality: 'rtl', // اتجاه عربي
        language: 'ar',
        height: 400,
        plugins: 'lists table code link',
        toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | table | link | code'
    });
</script>
@endsection
