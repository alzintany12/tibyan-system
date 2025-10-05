<?php

namespace App\Http\Controllers;

use App\Models\LegalCase;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CasesExport;

class LegalCaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = LegalCase::with(['client', 'assignedTo', 'creator']);

        // البحث
        if ($request->filled('search')) {
            $query->search($request->get('search'));
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // فلترة حسب نوع القضية
        if ($request->filled('case_type')) {
            $query->where('case_type', $request->get('case_type'));
        }

        // فلترة حسب المحكمة
        if ($request->filled('court_type')) {
            $query->where('court_type', $request->get('court_type'));
        }

        // فلترة حسب الأولوية
        if ($request->filled('priority')) {
            $query->where('priority', $request->get('priority'));
        }

        // فلترة حسب المستخدم المعين
        if ($request->filled('assigned_to')) {
            $query->byUser($request->get('assigned_to'));
        }

        // فلترة حسب العميل
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->get('client_id'));
        }

        // فلترة القضايا المؤرشفة
        if ($request->filled('archived')) {
            $isArchived = $request->get('archived') === '1';
            $query->where('is_archived', $isArchived);
        }

        // الترتيب
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $cases = $query->paginate(20);

        // البيانات للفلاتر
        $clients = Client::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        // إحصائيات
        $stats = [
            'total' => LegalCase::count(),
            'active' => LegalCase::active()->count(),
            'completed' => LegalCase::completed()->count(),
            'pending' => LegalCase::where('status', 'pending')->count(),
            'postponed' => LegalCase::where('status', 'postponed')->count(),
            'upcoming_hearings' => LegalCase::withUpcomingHearings()->count(),
        ];

        return view('cases.index', compact('cases', 'clients', 'users', 'stats'));
    }

    public function create()
    {
        $clients = Client::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('cases.create', compact('clients', 'users'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'case_title' => 'required|string|max:500',
            'client_id' => 'required|exists:clients,id',
            'opposing_party' => 'required|string|max:255',
            'court_name' => 'required|string|max:255',
            'court_type' => 'required|in:general,commercial,labor,administrative,criminal,family',
            'case_type' => 'required|string|max:100',
            'case_category' => 'nullable|string|max:100',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'required|date',
            'next_hearing_date' => 'nullable|date|after:start_date',
            'next_hearing_time' => 'nullable|date_format:H:i',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'fee_type' => 'required|in:fixed,hourly,percentage,mixed',
            'fee_amount' => 'nullable|numeric|min:0',
            'fee_percentage' => 'nullable|numeric|min:0|max:100',
            'estimated_hours' => 'nullable|integer|min:0',
            'case_value' => 'nullable|numeric|min:0',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        // إنشاء رقم القضية
        $legalCase = new LegalCase();
        $validatedData['case_number'] = $legalCase->generateCaseNumber();
        $validatedData['status'] = LegalCase::STATUS_ACTIVE;
        $validatedData['created_by'] = auth()->id();

        $legalCase = LegalCase::create($validatedData);

        return redirect()->route('cases.show', $legalCase)
                        ->with('success', 'تم إنشاء القضية بنجاح');
    }

    public function show(LegalCase $case)
    {
        $case->load([
            'client',
            'assignedTo',
            'creator',
            'documents' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'hearings' => function ($query) {
                $query->orderBy('hearing_date', 'desc');
            },
            'tasks' => function ($query) {
                $query->orderBy('due_date', 'desc');
            },
            'invoices' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'expenses' => function ($query) {
                $query->orderBy('expense_date', 'desc');
            }
        ]);

        // إحصائيات القضية
        $caseStats = [
            'duration_days' => $case->duration_in_days,
            'total_expenses' => $case->total_expenses,
            'total_invoices' => $case->total_invoices,
            'paid_invoices' => $case->paid_invoices,
            'documents_count' => $case->documents->count(),
            'hearings_count' => $case->hearings->count(),
            'tasks_count' => $case->tasks->count(),
            'completed_tasks' => $case->tasks->where('status', 'completed')->count(),
        ];

        return view('cases.show', compact('case', 'caseStats'));
    }

    public function edit(LegalCase $case)
    {
        $clients = Client::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('cases.edit', compact('case', 'clients', 'users'));
    }

    public function update(Request $request, LegalCase $case)
    {
        $validatedData = $request->validate([
            'case_title' => 'required|string|max:500',
            'client_id' => 'required|exists:clients,id',
            'opposing_party' => 'required|string|max:255',
            'court_name' => 'required|string|max:255',
            'court_type' => 'required|in:general,commercial,labor,administrative,criminal,family',
            'case_type' => 'required|string|max:100',
            'case_category' => 'nullable|string|max:100',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'next_hearing_date' => 'nullable|date',
            'next_hearing_time' => 'nullable|date_format:H:i',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'fee_type' => 'required|in:fixed,hourly,percentage,mixed',
            'fee_amount' => 'nullable|numeric|min:0',
            'fee_percentage' => 'nullable|numeric|min:0|max:100',
            'estimated_hours' => 'nullable|integer|min:0',
            'actual_hours' => 'nullable|numeric|min:0',
            'case_value' => 'nullable|numeric|min:0',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $validatedData['updated_by'] = auth()->id();

        $case->update($validatedData);

        return redirect()->route('cases.show', $case)
                        ->with('success', 'تم تحديث القضية بنجاح');
    }

    public function destroy(LegalCase $case)
    {
        if (!$case->canBeDeleted()) {
            return redirect()->back()
                            ->with('error', 'لا يمكن حذف القضية لوجود فواتير غير مدفوعة أو كونها نشطة');
        }

        $case->delete();

        return redirect()->route('cases.index')
                        ->with('success', 'تم حذف القضية بنجاح');
    }

    public function updateStatus(Request $request, LegalCase $case)
    {
        $request->validate([
            'status' => 'required|in:active,completed,postponed,rejected,suspended,pending',
            'status_reason' => 'nullable|string|max:500'
        ]);

        $case->update([
            'status' => $request->status,
            'updated_by' => auth()->id()
        ]);

        // إذا تم اكتمال القضية، ضع تاريخ الانتهاء
        if ($request->status === LegalCase::STATUS_COMPLETED) {
            $case->update(['end_date' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة القضية بنجاح'
        ]);
    }

    public function assignTo(Request $request, LegalCase $case)
    {
        $request->validate([
            'assigned_to' => 'nullable|exists:users,id'
        ]);

        $case->update([
            'assigned_to' => $request->assigned_to,
            'updated_by' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تعيين القضية بنجاح'
        ]);
    }

    public function archive(LegalCase $case)
    {
        $case->update([
            'is_archived' => true,
            'updated_by' => auth()->id()
        ]);

        return redirect()->back()
                        ->with('success', 'تم أرشفة القضية بنجاح');
    }

    public function unarchive(LegalCase $case)
    {
        $case->update([
            'is_archived' => false,
            'updated_by' => auth()->id()
        ]);

        return redirect()->back()
                        ->with('success', 'تم إلغاء أرشفة القضية بنجاح');
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $cases = LegalCase::with('client')
                         ->search($query)
                         ->where('is_archived', false)
                         ->limit(10)
                         ->get(['id', 'case_number', 'case_title', 'client_id']);

        return response()->json($cases);
    }

    public function export(Request $request)
    {
        $filters = $request->only(['search', 'status', 'case_type', 'court_type', 'priority', 'assigned_to', 'client_id']);
        
        return Excel::download(new CasesExport($filters), 'cases-' . now()->format('Y-m-d') . '.xlsx');
    }
}