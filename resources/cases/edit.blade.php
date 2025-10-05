@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">✏️ تعديل القضية</h1>

    <form action="{{ route('cases.update', $case->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">عنوان القضية</label>
            <input type="text" name="case_title" class="form-control" value="{{ $case->case_title }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">العميل</label>
            <select name="client_id" class="form-control" required>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" @selected($case->client_id == $client->id)>
                        {{ $client->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">المحامي المكلف</label>
            <select name="user_id" class="form-control" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected($case->user_id == $user->id)>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">الحالة</label>
            <select name="status" class="form-control" required>
                <option value="pending" @selected($case->status == 'pending')>معلقة</option>
                <option value="active" @selected($case->status == 'active')>نشطة</option>
                <option value="completed" @selected($case->status == 'completed')>مكتملة</option>
                <option value="postponed" @selected($case->status == 'postponed')>مؤجلة</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">💾 تحديث</button>
        <a href="{{ route('cases.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection
