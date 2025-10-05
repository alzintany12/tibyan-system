<?php

namespace App\Http\Controllers;

use App\Models\CaseModel;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CaseController extends Controller
{
    public function index(Request $request)
    {
        $query = CaseModel::with(['hearings', 'invoices', 'client']);
        
        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('case_title', 'like', "%{$search}%")
                  ->orWhere('case_summary', 'like', "%{$search}%");
            });
        }
        
        // تصفية حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // تصفية حسب نوع القضية
        if ($request->filled('case_type')) {
            $query->where('case_type', $request->case_type);
        }
        
        // تصفية حسب الأولوية
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        // ترتيب النتائج
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $cases = $query->paginate(15);
        
        $statistics = [
            'total' => CaseModel::count(),
            'active' => CaseModel::where('status', 'active')->count(),
            'completed' => CaseModel::where('status', 'completed')->count(),
            'pending' => CaseModel::where('status', 'pending')->count(),
            'postponed' => CaseModel::where('status', 'postponed')->count(),
        ];
        
        return view('cases.index', compact('cases', 'statistics'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        
        return view('cases.create', compact('clients', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'case_number' => 'required|string|unique:legal_cases,case_number',
            'case_title' => 'required|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'client_id_number' => 'nullable|string|max:50',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:20',
            'user_id' => 'nullable|exists:users,id',
            'opposing_party' => 'nullable|string|max:255',
            'court_name' => 'nullable|string|max:255',
            'court_type' => 'nullable|in:general,commercial,labor,administrative,criminal,family',
            'case_type' => 'required|in:civil,criminal,commercial,family,real_estate,labor,administrative,other',
            'case_category' => 'nullable|string|max:255',
            'status' => 'required|in:pending,active,completed,postponed,suspended,rejected',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'required|date',
            'expected_end_date' => 'nullable|date|after_or_equal:start_date',
            'next_hearing_date' => 'nullable|date',
            'next_hearing_time' => 'nullable|date_format:H:i',
            'description' => 'nullable|string',
            'case_summary' => 'nullable|string',
            'notes' => 'nullable|string',
            'fee_amount' => 'nullable|numeric|min:0',
            'fee_type' => 'required|in:fixed,hourly,percentage,mixed',
            'fee_percentage' => 'nullable|numeric|min:0|max:100',
            'total_fees' => 'nullable|numeric|min:0',
            'fees_received' => 'nullable|numeric|min:0',
            'estimated_hours' => 'nullable|integer|min:1',
            'case_value' => 'nullable|numeric|min:0',
            'opponent_name' => 'nullable|string|max:255',
            'opponent_info' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_archived' => 'nullable|boolean'
        ]);
        
        $validated['case_number'] = $validated['case_number'] ?: $this->generateCaseNumber();
        $validated['created_by'] = auth()->id() ?? 1;
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['is_archived'] = $validated['is_archived'] ?? false;
        
        // حساب الأتعاب المتبقية
        $validated['fees_pending'] = ($validated['total_fees'] ?? 0) - ($validated['fees_received'] ?? 0);
        
        $case = CaseModel::create($validated);
        
        return redirect()->route('cases.show', $case)
            ->with('success', 'تم إنشاء القضية بنجاح');
    }

    public function show(CaseModel $case)
    {
        $case->load(['hearings' => function($query) {
            $query->orderBy('hearing_date', 'desc');
        }, 'invoices' => function($query) {
            $query->orderBy('created_at', 'desc');
        }, 'documents' => function($query) {
            $query->orderBy('created_at', 'desc');
        }, 'client', 'user', 'createdBy']);
        
        return view('cases.show', compact('case'));
    }

    public function edit(CaseModel $case)
    {
        $clients = Client::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        
        return view('cases.edit', compact('case', 'clients', 'users'));
    }

    public function update(Request $request, CaseModel $case)
    {
        $validated = $request->validate([
            'case_number' => 'required|string|unique:legal_cases,case_number,' . $case->id,
            'case_title' => 'required|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:20',
            'user_id' => 'nullable|exists:users,id',
            'opposing_party' => 'nullable|string|max:255',
            'court_name' => 'nullable|string|max:255',
            'court_type' => 'nullable|in:general,commercial,labor,administrative,criminal,family',
            'case_type' => 'required|in:civil,criminal,commercial,family,real_estate,labor,administrative,other',
            'case_category' => 'nullable|string|max:255',
            'status' => 'required|in:pending,active,completed,postponed,suspended,rejected',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'required|date',
            'expected_end_date' => 'nullable|date|after_or_equal:start_date',
            'actual_end_date' => 'nullable|date',
            'next_hearing_date' => 'nullable|date',
            'next_hearing_time' => 'nullable|date_format:H:i',
            'description' => 'nullable|string',
            'case_summary' => 'nullable|string',
            'notes' => 'nullable|string',
            'result' => 'nullable|string',
            'fee_amount' => 'nullable|numeric|min:0',
            'fee_type' => 'required|in:fixed,hourly,percentage,mixed',
            'fee_percentage' => 'nullable|numeric|min:0|max:100',
            'total_fees' => 'nullable|numeric|min:0',
            'fees_received' => 'nullable|numeric|min:0',
            'estimated_hours' => 'nullable|integer|min:1',
            'actual_hours' => 'nullable|numeric|min:0',
            'case_value' => 'nullable|numeric|min:0',
            'opponent_name' => 'nullable|string|max:255',
            'opponent_info' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_archived' => 'nullable|boolean'
        ]);
        
        $validated['updated_by'] = auth()->user()->name ?? 'النظام';
        $validated['is_active'] = $validated['is_active'] ?? $case->is_active;
        $validated['is_archived'] = $validated['is_archived'] ?? $case->is_archived;
        
        // حساب الأتعاب المتبقية
        $validated['fees_pending'] = ($validated['total_fees'] ?? 0) - ($validated['fees_received'] ?? 0);
        
        // تحديث تاريخ الانتهاء إذا تم تغيير الحالة إلى مكتملة
        if ($validated['status'] == 'completed' && $case->status != 'completed') {
            $validated['actual_end_date'] = $validated['actual_end_date'] ?? now()->toDateString();
        }
        
        $case->update($validated);
        
        return redirect()->route('cases.show', $case)
            ->with('success', 'تم تحديث القضية بنجاح');
    }

    public function destroy(CaseModel $case)
    {
        // حذف القضية مع جميع البيانات المرتبطة بها (إجبار الحذف)
        
        // حذف جميع الجلسات المرتبطة وإزالتها من التقويم
        $hearings = $case->hearings;
        foreach ($hearings as $hearing) {
            // حذف التذكيرات والأحداث المرتبطة بالجلسة من التقويم
            $hearing->delete();
        }
        
        // حذف جميع الفواتير المرتبطة
        $invoices = $case->invoices;
        foreach ($invoices as $invoice) {
            // حذف بنود الفاتورة أولاً
            $invoice->items()->delete();
            $invoice->delete();
        }
        
        // حذف جميع المستندات المرتبطة
        $case->documents()->delete();
        
        // حذف جميع المهام المرتبطة
        $case->tasks()->delete();
        
        // حذف أي مدفوعات مرتبطة
        if (method_exists($case, 'payments')) {
            $case->payments()->delete();
        }
        
        // حذف أي مصروفات مرتبطة
        if (method_exists($case, 'expenses')) {
            $case->expenses()->delete();
        }
        
        // حذف القضية نفسها
        $case->delete();
        
        return redirect()->route('cases.index')
            ->with('success', 'تم حذف القضية وجميع البيانات المرتبطة بها بنجاح');
    }

    public function createInvoice(Request $request, CaseModel $case)
    {
        // التحقق من وجود مبلغ في الطلب أو استخدام مبلغ من بيانات القضية
        $amount = $request->input('amount');
        
        // إذا لم يتم تمرير مبلغ ، استخدم مبلغ من بيانات القضية
        if (empty($amount) || $amount <= 0) {
            $amount = $case->fee_amount ?? $case->total_fees ?? $case->case_value ?? 1000; // قيمة افتراضية
        }
        
        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'due_days' => 'nullable|integer|min:1|max:365'
        ]);
        
        // التأكد من المبلغ
        $finalAmount = $validated['amount'] ?? $amount;
        
        $invoiceData = [
            'case_id' => $case->id,
            'client_name' => $case->client_name,
            'invoice_number' => $this->generateInvoiceNumber(),
            'invoice_date' => now(),
            'due_date' => now()->addDays($validated['due_days'] ?? 30),
            'description' => $validated['description'] ?? "أتعاب قضية: {$case->case_number}",
            'amount' => $finalAmount,
            'tax_amount' => $finalAmount * 0.15, // 15% ضريبة
            'total_amount' => $finalAmount * 1.15,
            'status' => 'draft',
            'created_by' => auth()->user()->name ?? 'النظام'
        ];
        
        $invoice = Invoice::create($invoiceData);
        
        // تحديث أتعاب القضية
        $case->increment('fees_pending', $invoice->total_amount);
        $case->increment('total_fees', $invoice->total_amount);
        
        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'تم إنشاء الفاتورة بنجاح');
    }

    public function statistics()
    {
        $statistics = [
            'overview' => [
                'total_cases' => CaseModel::count(),
                'active_cases' => CaseModel::where('status', 'active')->count(),
                'completed_cases' => CaseModel::where('status', 'completed')->count(),
                'pending_cases' => CaseModel::where('status', 'pending')->count(),
            ],
            'financial' => [
                'total_fees' => CaseModel::sum('total_fees'),
                'received_fees' => CaseModel::sum('fees_received'),
                'pending_fees' => CaseModel::sum('fees_pending'),
            ],
            'case_types' => CaseModel::selectRaw('case_type, count(*) as count')
                ->groupBy('case_type')
                ->get()
                ->mapWithKeys(function($item) {
                    return [CaseModel::getCaseTypes()[$item->case_type] ?? $item->case_type => $item->count];
                }),
            'monthly_cases' => CaseModel::selectRaw('MONTH(created_at) as month, count(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->pluck('count', 'month')
        ];
        
        return response()->json($statistics);
    }

    protected function generateCaseNumber()
    {
        $year = date('Y');
        $lastCase = CaseModel::whereYear('created_at', $year)
                            ->orderBy('id', 'desc')
                            ->first();
        
        $nextNumber = $lastCase ? (int) substr($lastCase->case_number, -4) + 1 : 1;
        
        return "CASE-{$year}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    protected function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        
        $lastInvoice = Invoice::whereYear('created_at', $year)
                             ->whereMonth('created_at', $month)
                             ->orderBy('id', 'desc')
                             ->first();
        
        $nextNumber = $lastInvoice ? (int) substr($lastInvoice->invoice_number, -4) + 1 : 1;
        
        return "INV-{$year}-{$month}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}