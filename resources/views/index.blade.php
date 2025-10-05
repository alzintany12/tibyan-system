@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">📂 إدارة القضايا</h1>

    {{-- إحصائيات --}}
    <div class="row mb-4">
        <div class="col-md-2">إجمالي: {{ $stats['total'] }}</div>
        <div class="col-md-2">نشطة: {{ $stats['active'] }}</div>
        <div class="col-md-2">مكتملة: {{ $stats['completed'] }}</div>
        <div class="col-md-2">معلقة: {{ $stats['pending'] }}</div>
        <div class="col-md-2">مؤجلة: {{ $stats['postponed'] }}</div>
        <div class="col-md-2">بها جلسات قادمة: {{ $stats['upcoming_hearings'] }}</div>
    </div>

    {{-- جدول القضايا --}}
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>العنوان</th>
                <th>العميل</th>
                <th>المحامي المكلف</th>
                <th>الحالة</th>
                <th>تاريخ الإنشاء</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cases as $case)
                <tr>
                    <td>{{ $case->case_title ?? $case->title }}</td>
                    <td>{{ $case->client->name ?? '-' }}</td>
                    <td>{{ $case->user->name ?? '-' }}</td>
                    <td>{{ $case->status }}</td>
                    <td>{{ $case->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('cases.show', $case->id) }}" class="btn btn-sm btn-info">عرض</a>
                        <a href="{{ route('cases.edit', $case->id) }}" class="btn btn-sm btn-warning">تعديل</a>
                        <form action="{{ route('cases.destroy', $case->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- روابط التصفح --}}
    <div>
        {{ $cases->links() }}
    </div>
</div>
@endsection
