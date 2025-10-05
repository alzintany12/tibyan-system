@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">➕ إضافة قالب جديد</h1>

    <form action="{{ route('document-templates.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">اسم القالب</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">محتوى القالب</label>
            <textarea name="content" class="form-control" rows="6" required>{{ old('content') }}</textarea>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" checked>
            <label class="form-check-label">نشط</label>
        </div>

        <button type="submit" class="btn btn-success">💾 حفظ</button>
        <a href="{{ route('document-templates.index') }}" class="btn btn-secondary">⬅️ رجوع</a>
    </form>
</div>
@endsection
