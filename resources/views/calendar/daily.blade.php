@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">📖 دفتر اليوم</h3>
            <small>
                التاريخ: {{ $gregorian }} م  
                @if($hijri) | {{ $hijri }} هـ @endif
            </small>
        </div>
        <div>
            <!-- زر رجوع للتقويم الشهري -->
            <a href="{{ route('calendar.index') }}" class="btn btn-outline-dark">⬅ رجوع للتقويم الشهري</a>

            <!-- التنقل بين الأيام -->
            <a href="{{ route('calendar.daily', $date->copy()->subDay()->toDateString()) }}" class="btn btn-outline-secondary">⬅ اليوم السابق</a>
            <a href="{{ route('calendar.daily', $date->copy()->addDay()->toDateString()) }}" class="btn btn-outline-secondary">اليوم التالي ➡</a>

            <!-- PDF + طباعة -->
            <a href="{{ route('calendar.daily.pdf', $date->toDateString()) }}" class="btn btn-danger">⬇ تحميل PDF</a>
            <button onclick="window.print()" class="btn btn-warning">🖨 طباعة مباشرة</button>
        </div>
    </div>

    <table class="table table-bordered text-center align-middle">
        <thead class="table-primary">
            <tr>
                <th>المحكمة - الدائرة / رقم الدعوى</th>
                <th>الموكل وصفته</th>
                <th>الخصم وصفته</th>
                <th>الجلسة السابقة</th>
                <th>القرار</th>
            </tr>
        </thead>
        <tbody>
            @forelse($hearings as $hearing)
                <tr>
                    <td>
                        {{ $hearing->court_name ?? '-' }} - {{ $hearing->court_room ?? '' }}<br>
                        دعوى رقم: {{ $hearing->legalCase->number ?? $hearing->legalCase->id ?? '-' }}
                    </td>
                    <td>{{ $hearing->legalCase->client->name ?? '-' }}</td>
                    <td>{{ $hearing->opponent ?? '-' }}</td>
                    <td>{{ $hearing->previous_session ?? '-' }}</td>
                    <td>{{ $hearing->decision ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-muted">لا توجد جلسات لهذا اليوم</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- زر إضافة جلسة -->
    <div class="text-center mt-4">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addHearingModal">
            ➕ إضافة جلسة
        </button>
    </div>
</div>

<!-- مودال إضافة جلسة -->
<div class="modal fade" id="addHearingModal" tabindex="-1" aria-labelledby="addHearingModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('hearings.store') }}" class="modal-content">
        @csrf
        <div class="modal-header">
            <h5 class="modal-title" id="addHearingModalLabel">إضافة جلسة جديدة</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="hearing_date" value="{{ $date->toDateString() }}">
            <div class="mb-3">
                <label class="form-label">المحكمة</label>
                <input type="text" name="court_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">الدائرة</label>
                <input type="text" name="court_room" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">رقم الدعوى</label>
                <input type="text" name="case_number" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">القرار</label>
                <textarea name="decision" class="form-control"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
            <button type="submit" class="btn btn-primary">حفظ</button>
        </div>
    </form>
  </div>
</div>
@endsection
