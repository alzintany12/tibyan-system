<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\LegalCase;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     */
    public function index(Request $request)
    {
        $query = Task::with(['legalCase.client', 'user', 'assignedTo'])
            ->latest('created_at');

        // فلترة حسب المستخدم المسند إليه
        if ($request->filled('assigned_to')) {
            if ($request->assigned_to === 'me') {
                $query->where('assigned_to', Auth::id());
            } else {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        // فلترة حسب القضية
        if ($request->filled('case_id')) {
            $query->where('legal_case_id', $request->case_id);
        }

        // فلترة حسب العميل
        if ($request->filled('client_id')) {
            $query->whereHas('legalCase', function ($q) use ($request) {
                $q->where('client_id', $request->client_id);
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب الأولوية
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // فلترة حسب تاريخ الاستحقاق
        if ($request->filled('due_date_filter')) {
            switch ($request->due_date_filter) {
                case 'overdue':
                    $query->whereDate('due_date', '<', now())
                          ->where('status', '!=', 'completed');
                    break;
                case 'today':
                    $query->whereDate('due_date', now());
                    break;
                case 'this_week':
                    $query->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'next_week':
                    $query->whereBetween('due_date', [now()->addWeek()->startOfWeek(), now()->addWeek()->endOfWeek()]);
                    break;
            }
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('legalCase', function ($caseQuery) use ($search) {
                      $caseQuery->where('title', 'like', "%{$search}%");
                  });
            });
        }

        $tasks = $query->paginate(15);
        $cases = LegalCase::where('is_active', true)->get();
        $clients = Client::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();

        // إحصائيات المهام
        $taskStats = [
            'total' => Task::count(),
            'pending' => Task::where('status', 'pending')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'completed' => Task::where('status', 'completed')->count()
        ];

        return view('tasks.index', compact('tasks', 'cases', 'clients', 'users', 'taskStats'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create(Request $request)
    {
        $cases = LegalCase::where('is_active', true)->get();
        $clients = Client::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();
        
        $selectedCase = null;
        if ($request->filled('case_id')) {
            $selectedCase = LegalCase::find($request->case_id);
        }

        return view('tasks.create', compact('cases', 'clients', 'users', 'selectedCase'));
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'legal_case_id' => 'nullable|exists:legal_cases,id',
            'assigned_to' => 'required|exists:users,id',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'due_date' => 'required|date|after_or_equal:today',
            'estimated_hours' => 'nullable|numeric|min:0.5|max:100',
            'tags' => 'nullable|string',
            'is_billable' => 'boolean',
            'hourly_rate' => 'nullable|numeric|min:0'
        ]);

        $task = new Task($validated);
        $task->user_id = Auth::id();
        $task->status = 'pending';
        $task->progress = 0;
        $task->is_billable = $request->boolean('is_billable');

        // معالجة التاجز
        if ($request->filled('tags')) {
            $task->tags = array_map('trim', explode(',', $request->tags));
        }

        $task->save();

        // إشعار المستخدم المُكلف بالمهمة
        if ($task->assigned_to !== Auth::id()) {
            // يمكن إضافة نظام إشعارات هنا
        }

        return redirect()->route('tasks.index')
            ->with('success', 'تم إنشاء المهمة بنجاح');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        $task->load(['legalCase.client', 'user', 'assignedTo']);
        
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        $cases = LegalCase::where('is_active', true)->get();
        $clients = Client::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();

        return view('tasks.edit', compact('task', 'cases', 'clients', 'users'));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'legal_case_id' => 'nullable|exists:legal_cases,id',
            'assigned_to' => 'required|exists:users,id',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'due_date' => 'required|date',
            'estimated_hours' => 'nullable|numeric|min:0.5|max:100',
            'tags' => 'nullable|string',
            'is_billable' => 'boolean',
            'hourly_rate' => 'nullable|numeric|min:0'
        ]);

        $oldAssignedTo = $task->assigned_to;
        
        $task->fill($validated);
        $task->is_billable = $request->boolean('is_billable');

        // معالجة التاجز
        if ($request->filled('tags')) {
            $task->tags = array_map('trim', explode(',', $request->tags));
        } else {
            $task->tags = null;
        }

        $task->save();

        // إشعار في حالة تغيير المكلف بالمهمة
        if ($oldAssignedTo !== $task->assigned_to && $task->assigned_to !== Auth::id()) {
            // يمكن إضافة نظام إشعارات هنا
        }

        return redirect()->route('tasks.show', $task)
            ->with('success', 'تم تحديث المهمة بنجاح');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'تم حذف المهمة بنجاح');
    }

    /**
     * Update task status.
     */
    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,on_hold,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        $task->status = $validated['status'];
        
        if ($validated['status'] === 'completed') {
            $task->progress = 100;
            $task->completed_at = now();
        } elseif ($validated['status'] === 'in_progress' && $task->progress === 0) {
            $task->progress = 10; // بداية العمل
            $task->started_at = now();
        }

        if ($request->filled('notes')) {
            $task->notes = $validated['notes'];
        }

        $task->save();

        return redirect()->route('tasks.show', $task)
            ->with('success', 'تم تحديث حالة المهمة بنجاح');
    }

    /**
     * Update task progress.
     */
    public function updateProgress(Request $request, Task $task)
    {
        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string'
        ]);

        $task->progress = $validated['progress'];

        if ($validated['progress'] === 100) {
            $task->status = 'completed';
            $task->completed_at = now();
        } elseif ($validated['progress'] > 0 && $task->status === 'pending') {
            $task->status = 'in_progress';
            $task->started_at = $task->started_at ?: now();
        }

        if ($request->filled('notes')) {
            $task->notes = $validated['notes'];
        }

        $task->save();

        return redirect()->route('tasks.show', $task)
            ->with('success', 'تم تحديث تقدم المهمة بنجاح');
    }

    /**
     * Mark task as completed.
     */
    public function complete(Request $request, Task $task)
    {
        $validated = $request->validate([
            'completion_notes' => 'nullable|string',
            'actual_hours' => 'nullable|numeric|min:0'
        ]);

        $task->update([
            'status' => 'completed',
            'progress' => 100,
            'completed_at' => now(),
            'completion_notes' => $validated['completion_notes'],
            'actual_hours' => $validated['actual_hours']
        ]);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'تم إكمال المهمة بنجاح');
    }

    /**
     * Get tasks for calendar view.
     */
    public function getCalendarEvents(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $tasks = Task::with(['legalCase.client', 'assignedTo'])
            ->whereBetween('due_date', [$start, $end])
            ->get();

        $events = $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->due_date->toDateString(),
                'url' => route('tasks.show', $task),
                'color' => $this->getTaskColor($task->priority, $task->status),
                'extendedProps' => [
                    'priority' => $task->priority,
                    'status' => $task->status,
                    'progress' => $task->progress,
                    'assigned_to' => $task->assignedTo->name,
                    'case' => $task->legalCase?->title
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * Get my tasks (for current user).
     */
    public function myTasks(Request $request)
    {
        $query = Task::where('assigned_to', Auth::id())
            ->with(['legalCase.client', 'user'])
            ->latest('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->paginate(10);

        return view('tasks.my-tasks', compact('tasks'));
    }

    /**
     * Get color for task based on priority and status.
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
}