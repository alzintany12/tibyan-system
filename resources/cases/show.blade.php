@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">📄 تفاصيل القضية</h1>

    <div class="card">
        <div class="card-body">
            <p><strong>عنوان القضية:</strong> {{ $case->case_title }}</p>
            <p><strong>العميل:</strong> {{ $case->client->name ?? '-' }}</p>
            <p><strong>المحامي المكلف:</strong> {{ $case->user->name ?? '-' }}</p>
            <p><strong>الحالة:</strong> {{ $case->status }}</p>
            <p><strong>تاريخ الإنشاء:</strong> {{ $case->created_at->format('Y-m-d H:i') }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('cases.edit', $case->id) }}" class="btn btn-warning">✏️ تعديل</a>
        <a href="{{ route('cases.index') }}" class="btn btn-secondary">🔙 رجوع</a>
    </div>
</div>
@endsection
