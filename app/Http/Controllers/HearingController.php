<?php

namespace App\Http\Controllers;

use App\Models\Hearing;
use App\Models\CaseModel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HearingController extends Controller
{
    public function index(Request $request)
    {
        $query = Hearing::with('case');
        
        // تصفية حسب الحالة
        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        // تصفية حسب الفترة الزمنية
        $period = $request->get('period', 'upcoming');
        switch ($period) {
            case 'upcoming':
                $query->where('hearing_date', '>=', now())
                      ->orderBy('hearing_date', 'asc');
                break;
            case 'past':
                $query->where('hearing_date', '<', now())
                     ->orderBy('hearing_date', 'desc');
                break;
            case 'this_week':
                $query->whereBetween('hearing_date', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->orderBy('hearing_date', 'asc');
                break;
            case 'this_month':
                $query->whereBetween('hearing_date', [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ])->orderBy('hearing_date', 'asc');
                break;
        }
        
        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('court_name', 'like', "%{$search}%")
                  ->orWhereHas('case', function($caseQuery) use ($search) {
                      $caseQuery->where('case_number', 'like', "%{$search}%")
                               ->orWhere('client_name', 'like', "%{$search}%");
                  });
            });
        }
        
        $hearings = $query->paginate(15);
        
        // الإحصائيات
        $statistics = [
            'total' => Hearing::count(),
            'upcoming' => Hearing::where('hearing_date', '>=', now())->count(),
            'today' => Hearing::whereDate('hearing_date', today())->count(),
            'this_week' => Hearing::whereBetween('hearing_date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'completed' => Hearing::where('status', 'completed')->count(),
            'postponed' => Hearing::where('status', 'postponed')->count(),
            'missed' => Hearing::missed()->count(),
        ];
        
        return view('hearings.index', compact('hearings', 'statistics', 'status', 'period'));
    }

    public function create(Request $request)
    {
        $cases = CaseModel::where('is_active', true)->get();
        $selectedCaseId = $request->get('case_id');
        $selectedCase = null;
        $selectedDate = $request->get('date');
        
        if ($selectedCaseId) {
            $selectedCase = CaseModel::find($selectedCaseId);
        }
        
        return view('hearings.create', compact('cases', 'selectedCaseId', 'selectedCase', 'selectedDate'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'case_id' => 'required|exists:legal_cases,id',
            'hearing_date' => 'required|date',
            'hearing_time' => 'required|date_format:H:i',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'court_name' => 'nullable|string|max:255',
            'court_room' => 'nullable|string|max:100',
            'hearing_type' => 'nullable|in:initial,evidence,pleading,judgment,appeal,execution,other',
            'status' => 'required|in:scheduled,completed,postponed,cancelled',
            'notes' => 'nullable|string',
            'send_notification' => 'nullable|boolean',
            'reminder_minutes' => 'nullable|integer|min:1'
        ]);
        
        // تحويل التاريخ والوقت
        $validated['hearing_date'] = $validated['hearing_date'];
        $validated['hearing_time'] = $validated['hearing_time'] . ':00'; // إضافة الثواني
        $validated['created_by'] = auth()->user()->name ?? 'النظام';
        
        $hearing = Hearing::create($validated);
        
        // تحديث تاريخ الجلسة القادمة في القضية
        if ($hearing->case && $hearing->status == 'scheduled') {
            $hearing->case->update([
                'next_hearing_date' => $hearing->hearing_date,
                'next_hearing_time' => $hearing->hearing_time
            ]);
        }
        
        if ($request->input('action') == 'save_and_add') {
            return redirect()->route('hearings.create', ['case_id' => $hearing->case_id])
                ->with('success', 'تم إنشاء الجلسة بنجاح');
        }
        
        return redirect()->route('hearings.show', $hearing)
            ->with('success', 'تم إنشاء الجلسة بنجاح');
    }

    public function show(Hearing $hearing)
    {
        $hearing->load('case');
        
        return view('hearings.show', compact('hearing'));
    }

    public function edit(Hearing $hearing)
    {
        $cases = CaseModel::where('is_active', true)->get();
        $hearing->load('case');
        
        return view('hearings.edit', compact('hearing', 'cases'));
    }

    public function update(Request $request, Hearing $hearing)
    {
        $validated = $request->validate([
            'case_id' => 'required|exists:legal_cases,id',
            'hearing_date' => 'required|date',
            'hearing_time' => 'required|date_format:H:i',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'court_name' => 'nullable|string|max:255',
            'court_room' => 'nullable|string|max:100',
            'hearing_type' => 'nullable|in:initial,evidence,pleading,judgment,appeal,execution,other',
            'status' => 'required|in:scheduled,completed,postponed,cancelled',
            'notes' => 'nullable|string',
            'result' => 'nullable|string'
        ]);
        
        // تحويل التاريخ والوقت
        $validated['hearing_date'] = $validated['hearing_date'];
        $validated['hearing_time'] = $validated['hearing_time'] . ':00'; // إضافة الثواني
        $validated['updated_by'] = auth()->user()->name ?? 'النظام';
        
        $hearing->update($validated);
        
        // تحديث تاريخ الجلسة القادمة في القضية
        if ($hearing->case && $hearing->status == 'scheduled') {
            $nextHearing = $hearing->case->hearings()
                ->where('status', 'scheduled')
                ->where('hearing_date', '>=', now())
                ->orderBy('hearing_date')
                ->orderBy('hearing_time')
                ->first();
            
            if ($nextHearing) {
                $hearing->case->update([
                    'next_hearing_date' => $nextHearing->hearing_date,
                    'next_hearing_time' => $nextHearing->hearing_time
                ]);
            } else {
                $hearing->case->update([
                    'next_hearing_date' => null,
                    'next_hearing_time' => null
                ]);
            }
        }
        
        return redirect()->route('hearings.show', $hearing)
            ->with('success', 'تم تحديث الجلسة بنجاح');
    }

    public function destroy(Hearing $hearing)
    {
        // تحديث القضية إذا كانت هذه هي الجلسة القادمة
        if ($hearing->case && 
            $hearing->case->next_hearing_date == $hearing->hearing_date &&
            $hearing->case->next_hearing_time == $hearing->hearing_time) {
            
            $nextHearing = $hearing->case->hearings()
                ->where('id', '!=', $hearing->id)
                ->where('status', 'scheduled')
                ->where('hearing_date', '>=', now())
                ->orderBy('hearing_date')
                ->orderBy('hearing_time')
                ->first();
            
            $hearing->case->update([
                'next_hearing_date' => $nextHearing ? $nextHearing->hearing_date : null,
                'next_hearing_time' => $nextHearing ? $nextHearing->hearing_time : null
            ]);
        }
        
        $hearing->delete();
        
        return redirect()->route('hearings.index')
            ->with('success', 'تم حذف الجلسة بنجاح');
    }

    public function calendar(Request $request)
    {
        $currentDate = $request->get('date', now()->format('Y-m-d'));
        $viewDate = Carbon::parse($currentDate);
        
        // جلب الجلسات للشهر الحالي
        $hearings = Hearing::with('case')
            ->whereBetween('hearing_date', [
                $viewDate->copy()->startOfMonth(),
                $viewDate->copy()->endOfMonth()
            ])
            ->get();
        
        return view('hearings.calendar', compact('hearings', 'viewDate'));
    }

    public function getCalendarEvents(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');
        
        $hearings = Hearing::with('case')
            ->whereBetween('hearing_date', [$start, $end])
            ->get();
        
        $events = [];
        foreach ($hearings as $hearing) {
            $statusColors = [
                'scheduled' => '#007bff',
                'completed' => '#28a745',
                'postponed' => '#ffc107',
                'cancelled' => '#dc3545'
            ];
            
            // تنظيف التوقيت
            $timeString = $hearing->hearing_time;
            if (strpos($timeString, ' ') !== false) {
                $parts = explode(' ', $timeString);
                $timeString = end($parts);
            }
            // إضافة :00 إذا لم تكن موجودة
            if (substr_count($timeString, ':') === 1) {
                $timeString .= ':00';
            }
            
            $events[] = [
                'id' => $hearing->id,
                'title' => $hearing->title ?: ($hearing->case->case_number ?? 'جلسة'),
                'start' => $hearing->hearing_date->format('Y-m-d') . 'T' . $timeString,
                'color' => $statusColors[$hearing->status] ?? '#6c757d',
                'url' => route('hearings.show', $hearing),
                'extendedProps' => [
                    'case_number' => $hearing->case->case_number ?? '',
                    'client_name' => $hearing->case->client_name ?? '',
                    'court_name' => $hearing->court_name,
                    'hearing_type' => $hearing->hearing_type,
                    'status' => $hearing->status
                ]
            ];
        }
        
        return response()->json($events);
    }

    public function complete(Request $request, Hearing $hearing)
    {
        $validated = $request->validate([
            'result' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);
        
        $hearing->update([
            'status' => 'completed',
            'result' => $validated['result'] ?? null,
            'notes' => $validated['notes'] ?? $hearing->notes,
            'completed_at' => now(),
            'updated_by' => auth()->user()->name ?? 'النظام'
        ]);
        
        // تحديث الجلسة القادمة في القضية
        $nextHearing = $hearing->case->hearings()
            ->where('status', 'scheduled')
            ->where('hearing_date', '>=', now())
            ->orderBy('hearing_date')
            ->orderBy('hearing_time')
            ->first();
        
        if ($nextHearing) {
            $hearing->case->update([
                'next_hearing_date' => $nextHearing->hearing_date,
                'next_hearing_time' => $nextHearing->hearing_time
            ]);
        } else {
            $hearing->case->update([
                'next_hearing_date' => null,
                'next_hearing_time' => null
            ]);
        }
        
        return redirect()->back()
            ->with('success', 'تم تحديد الجلسة كمكتملة');
    }

    public function postpone(Request $request, Hearing $hearing)
    {
        $validated = $request->validate([
            'new_date' => 'required|date|after:today',
            'new_time' => 'required|date_format:H:i',
            'reason' => 'nullable|string'
        ]);
        
        // إنشاء جلسة جديدة
        $newHearing = $hearing->replicate();
        $newHearing->hearing_date = $validated['new_date'];
        $newHearing->hearing_time = $validated['new_time'];
        $newHearing->status = 'scheduled';
        $newHearing->notes = ($hearing->notes ? $hearing->notes . "\n\n" : '') . 
                            "تم تأجيل الجلسة الأصلية من " . $hearing->hearing_date . " " . $hearing->hearing_time .
                            (isset($validated['reason']) ? "\nسبب التأجيل: " . $validated['reason'] : '');
        $newHearing->created_at = now();
        $newHearing->updated_at = now();
        $newHearing->save();
        
        // تحديث الجلسة الحالية
        $hearing->update([
            'status' => 'postponed',
            'postponed_to' => $newHearing->id,
            'postpone_reason' => $validated['reason'] ?? null,
            'updated_by' => auth()->user()->name ?? 'النظام'
        ]);
        
        // تحديث الجلسة القادمة في القضية
        $hearing->case->update([
            'next_hearing_date' => $newHearing->hearing_date,
            'next_hearing_time' => $newHearing->hearing_time
        ]);
        
        return redirect()->route('hearings.show', $newHearing)
            ->with('success', 'تم تأجيل الجلسة وإنشاء جلسة جديدة');
    }

    public function quickStats()
    {
        $stats = [
            'today' => Hearing::whereDate('hearing_date', today())->count(),
            'this_week' => Hearing::whereBetween('hearing_date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month' => Hearing::whereBetween('hearing_date', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ])->count(),
            'upcoming' => Hearing::where('hearing_date', '>', now())->count(),
            'completed_today' => Hearing::whereDate('hearing_date', today())
                                      ->where('status', 'completed')->count()
        ];
        
        return response()->json($stats);
    }

    public function updateMissedHearings()
    {
        $missedHearings = Hearing::where('hearing_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled', 'postponed'])
            ->update(['status' => 'missed']);
        
        return response()->json(['updated' => $missedHearings]);
    }

    public function sendReminders()
    {
        $upcomingHearings = Hearing::where('hearing_date', '>=', now())
            ->where('hearing_date', '<=', now()->addDays(7))
            ->where('status', 'scheduled')
            ->where('reminder_sent', false)
            ->get();
        
        $sent = 0;
        foreach ($upcomingHearings as $hearing) {
            // تنفيذ إرسال التذكير (هنا يمكن إضافة Email/SMS)
            $hearing->update(['reminder_sent' => true]);
            $sent++;
        }
        
        return response()->json(['sent' => $sent]);
    }
}