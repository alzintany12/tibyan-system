<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; direction: rtl; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; font-size: 12px; }
        th { background: #f2f2f2; }
        h3 { margin: 0; }
    </style>
</head>
<body>
    <h3>📖 دفتر يوم {{ $date->format('Y-m-d') }}</h3>
    <table>
        <thead>
            <tr>
                <th>المحكمة - الدائرة / رقم الدعوى</th>
                <th>الموكل</th>
                <th>الخصم</th>
                <th>الجلسة السابقة</th>
                <th>القرار</th>
            </tr>
        </thead>
        <tbody>
            @forelse($hearings as $hearing)
                <tr>
                    <td>
                        {{ $hearing->court_name ?? '-' }} - {{ $hearing->court_room ?? '' }}<br>
                        {{ $hearing->legalCase->number ?? $hearing->legalCase->id ?? '-' }}
                    </td>
                    <td>{{ $hearing->legalCase->client->name ?? '-' }}</td>
                    <td>{{ $hearing->opponent ?? '-' }}</td>
                    <td>{{ $hearing->previous_session ?? '-' }}</td>
                    <td>{{ $hearing->decision ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5">لا توجد جلسات</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
