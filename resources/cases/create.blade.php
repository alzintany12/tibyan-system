@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">➕ إضافة قضية جديدة</h1>

    <form action="{{ route('cases.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">عنوان القضية</label>
            <input type="text" name="case_title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">العميل</label>
            <select name="client_id" class="form-control" required>
                <option value="">-- اختر العميل --</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">المحامي المكلف</label>
            <select name="user_id" class="form-control" required>
                <option value="">-- اختر المحامي --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">الحالة</label>
            <select name="status" class="form-control" required>
                <option value="pending">معلقة</option>
                <option value="active">نشطة</option>
                <option value="completed">مكتملة</option>
                <option value="postponed">مؤجلة</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">💾 حفظ</button>
        <a href="{{ route('cases.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection
