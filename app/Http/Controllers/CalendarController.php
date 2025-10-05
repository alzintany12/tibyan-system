<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Models\Hearing;
use App\Models\Task;
use App\Models\LegalCase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * عرض صفحة التقويم الشهري.
     */
    public function index()
    {
        $users = User::where('is_active', true)->get();
        $cases = LegalCase::where('is_active', true)->get();

        return view('calendar.index', compact('users', 'cases'));
    }

    /**
     * جلب الأحداث (القضايا + الجلسات + المهام).
     */
    public function getEvents(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $userId = $request->input('user_id');
        $eventTypes = $request->input('event_types', ['events', 'hearings', 'tasks']);

        $events = collect();

        // الأحداث المخصصة
        if (in_array('events', $eventTypes)) {
            $calendarEvents = CalendarEvent::whereBetween('start_date', [$start, $end]);
            if ($userId && $userId !== 'all') {
                $calendarEvents->where('user_id', $userId);
            }
            $calendarEvents = $calendarEvents->get();

            foreach ($calendarEvents as $event) {
                $events->push([
                    'id' => 'event_' . $event->id,
                    'title' => $event->title,
                    'start' => $event->start_date instanceof Carbon ? $event->start_date->toIso8601String() : $event->start_date,
                    'end'   => $event->end_date ? ($event->end_date instanceof Carbon ? $event->end_date->toIso8601String() : $event->end_date) : null,
                    'allDay' => $event->is_all_day,
                    'color' => $event->color ?: '#007bff',
                    'url' => route('calendar.events.show', $event->id),
                    'extendedProps' => [
                        'type' => 'event',
                        'description' => $event->description,
                        'location' => $event->location,
                        'user' => $event->user->name ?? '',
                        'reminders' => $event->reminders
                    ]
                ]);
            }
        }

        // الجلسات
        if (in_array('hearings', $eventTypes)) {
            $hearings = Hearing::with(['legalCase.client', 'user'])
                ->whereBetween('hearing_date', [$start, $end]);

            if ($userId && $userId !== 'all') {
                $hearings->where('user_id', $userId);
            }

            $hearings = $hearings->get();

            foreach ($hearings as $hearing) {
                $events->push([
                    'id' => 'hearing_' . $hearing->id,
                    'title' => '⚖️ ' . ($hearing->title ?? ($hearing->legalCase->title ?? 'جلسة')),
                    'start' => $hearing->hearing_date instanceof Carbon ? $hearing->hearing_date->toIso8601String() : $hearing->hearing_date,
                    'color' => $this->getHearingColor($hearing->status ?? ''),
                    'url' => route('hearings.show', $hearing),
                    'extendedProps' => [
                        'type' => 'hearing',
                        'court' => $hearing->court_name,
                        'client' => $hearing->legalCase->client->name ?? '',
                        'case' => $hearing->legalCase->title ?? '',
                        'status' => $hearing->status ?? '',
                        'judge' => $hearing->judge_name ?? '',
                        'room' => $hearing->court_room ?? ''
                    ]
                ]);
            }
        }

        // المهام
        if (in_array('tasks', $eventTypes)) {
            $tasks = Task::with(['legalCase.client', 'assignedTo'])
                ->whereBetween('due_date', [$start, $end]);

            if ($userId && $userId !== 'all') {
                $tasks->where('assigned_to', $userId);
            }

            $tasks = $tasks->get();

            foreach ($tasks as $task) {
                $events->push([
                    'id' => 'task_' . $task->id,
                    'title' => '✅ ' . $task->title,
                    'start' => $task->due_date instanceof Carbon ? $task->due_date->toDateString() : $task->due_date,
                    'allDay' => true,
                    'color' => $this->getTaskColor($task->priority ?? null, $task->status ?? null),
                    'url' => route('tasks.show', $task),
                    'extendedProps' => [
                        'type' => 'task',
                        'priority' => $task->priority ?? '',
                        'status' => $task->status ?? '',
                        'progress' => $task->progress ?? 0,
                        'assigned_to' => $task->assignedTo->name ?? '',
                        'case' => $task->legalCase->title ?? ''
                    ]
                ]);
            }
        }

        return response()->json($events->toArray());
    }

    /**
     * صفحة دفتر اليوم (جدول يومي مثل الدفتر الورقي).
     */
    public function daily($date = null)
    {
        $date = $date ? Carbon::parse($date)->startOfDay() : Carbon::today();

        $hearings = Hearing::with(['legalCase.client'])
            ->whereDate('hearing_date', $date->toDateString())
            ->orderBy('hearing_date', 'asc')
            ->get();

        $gregorian = $date->locale('ar')->translatedFormat('l d F Y');
        $hijri = $this->toHijri($date);

        return view('calendar.daily', compact('date', 'hearings', 'gregorian', 'hijri'));
    }

    /**
     * طباعة دفتر اليوم كـ PDF.
     */
    public function dailyPdf($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        $hearings = Hearing::with(['legalCase.client'])
            ->whereDate('hearing_date', $date)
            ->get();

        $pdf = \PDF::loadView('calendar.daily-pdf', [
            'date' => $date,
            'hearings' => $hearings
        ])->setPaper('a4', 'portrait');

        return $pdf->download("دفتر-{$date->format('Y-m-d')}.pdf");
    }

    /**
     * تحويل التاريخ إلى هجري باستخدام intl (إن متاح).
     */
    private function toHijri(Carbon $date)
    {
        if (!class_exists('\IntlDateFormatter')) {
            return '';
        }

        try {
            $tz = $date->getTimezone()->getName() ?? 'UTC';
            $formatter = \IntlDateFormatter::create(
                'ar_SA@calendar=islamic',
                \IntlDateFormatter::LONG,
                \IntlDateFormatter::NONE,
                $tz,
                \IntlDateFormatter::TRADITIONAL,
                "d MMMM y"
            );

            $result = $formatter->format($date);
            return $result ?: '';
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * ألوان الجلسات حسب الحالة.
     */
    private function getHearingColor($status)
    {
        return match ($status) {
            'scheduled' => '#007bff',
            'completed' => '#28a745',
            'postponed' => '#ffc107',
            'cancelled' => '#dc3545',
            default => '#6c757d'
        };
    }

    /**
     * ألوان المهام حسب الأولوية والحالة.
     */
    private function getTaskColor($priority, $status)
    {
        if ($status === 'completed') {
            return '#28a745';
        }

        return match ($priority) {
            'urgent' => '#dc3545',
            'high' => '#fd7e14',
            'medium' => '#ffc107',
            'low' => '#6c757d',
            default => '#007bff'
        };
    }

    // ✨ باقي الميثودات: storeEvent, updateEvent, deleteEvent, createRecurringEvents, exportCalendar
    // اتركها كما هي في ملفك الأصلي (لم ألمسها).
}
