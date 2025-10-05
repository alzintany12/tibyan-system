<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\LegalCase;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the expenses.
     */
    public function index(Request $request)
    {
        $query = Expense::with(['legalCase.client', 'user'])
            ->latest('expense_date');

        // فلترة حسب المستخدم
        if ($request->filled('user_id')) {
            if ($request->user_id === 'me') {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('user_id', $request->user_id);
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

        // فلترة حسب الفئة
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب تاريخ المصروف
        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhereHas('legalCase', function ($caseQuery) use ($search) {
                      $caseQuery->where('title', 'like', "%{$search}%");
                  });
            });
        }

        $expenses = $query->paginate(15);
        $cases = LegalCase::where('is_active', true)->get();
        $clients = Client::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();

        // إحصائيات سريعة
        $totalExpenses = $query->sum('amount');
        $pendingCount = Expense::where('status', 'pending')->count();
        $approvedCount = Expense::where('status', 'approved')->count();

        return view('expenses.index', compact('expenses', 'cases', 'clients', 'users', 'totalExpenses', 'pendingCount', 'approvedCount'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create(Request $request)
    {
        $cases = LegalCase::where('is_active', true)->get();
        $clients = Client::where('is_active', true)->get();
        
        $selectedCase = null;
        if ($request->filled('case_id')) {
            $selectedCase = LegalCase::find($request->case_id);
        }

        return view('expenses.create', compact('cases', 'clients', 'selectedCase'));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'legal_case_id' => 'nullable|exists:legal_cases,id',
            'description' => 'required|string|max:255',
            'category' => 'required|string|in:court_fees,travel,accommodation,meals,documents,communication,expert_fees,translation,other',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date|before_or_equal:today',
            'vendor' => 'nullable|string|max:255',
            'payment_method' => 'required|string|in:cash,credit_card,bank_transfer,check',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'is_billable' => 'boolean',
            'tax_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3'
        ]);

        $expense = new Expense($validated);
        $expense->user_id = Auth::id();
        $expense->status = 'pending';
        $expense->is_billable = $request->boolean('is_billable');

        // رفع إيصال الاستلام
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $filename = time() . '_' . Str::slug($expense->description) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('expenses/receipts', $filename, 'public');
            
            $expense->receipt_path = $path;
            $expense->receipt_filename = $file->getClientOriginalName();
        }

        $expense->save();

        return redirect()->route('expenses.index')
            ->with('success', 'تم إضافة المصروف بنجاح');
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense)
    {
        $expense->load(['legalCase.client', 'user']);
        
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Expense $expense)
    {
        // لا يمكن تعديل المصروفات المعتمدة
        if ($expense->status === 'approved') {
            return redirect()->route('expenses.show', $expense)
                ->with('error', 'لا يمكن تعديل مصروف معتمد');
        }

        $cases = LegalCase::where('is_active', true)->get();
        $clients = Client::where('is_active', true)->get();

        return view('expenses.edit', compact('expense', 'cases', 'clients'));
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        // لا يمكن تعديل المصروفات المعتمدة
        if ($expense->status === 'approved') {
            return redirect()->route('expenses.show', $expense)
                ->with('error', 'لا يمكن تعديل مصروف معتمد');
        }

        $validated = $request->validate([
            'legal_case_id' => 'nullable|exists:legal_cases,id',
            'description' => 'required|string|max:255',
            'category' => 'required|string|in:court_fees,travel,accommodation,meals,documents,communication,expert_fees,translation,other',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date|before_or_equal:today',
            'vendor' => 'nullable|string|max:255',
            'payment_method' => 'required|string|in:cash,credit_card,bank_transfer,check',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'is_billable' => 'boolean',
            'tax_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3'
        ]);

        $expense->fill($validated);
        $expense->is_billable = $request->boolean('is_billable');

        // رفع إيصال جديد إذا تم تحديده
        if ($request->hasFile('receipt')) {
            // حذف الإيصال القديم
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }

            $file = $request->file('receipt');
            $filename = time() . '_' . Str::slug($expense->description) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('expenses/receipts', $filename, 'public');
            
            $expense->receipt_path = $path;
            $expense->receipt_filename = $file->getClientOriginalName();
        }

        $expense->save();

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'تم تحديث المصروف بنجاح');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense)
    {
        // لا يمكن حذف المصروفات المعتمدة
        if ($expense->status === 'approved') {
            return redirect()->route('expenses.index')
                ->with('error', 'لا يمكن حذف مصروف معتمد');
        }

        // حذف الإيصال إذا كان موجوداً
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'تم حذف المصروف بنجاح');
    }

    /**
     * Approve the expense.
     */
    public function approve(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'approval_notes' => 'nullable|string'
        ]);

        $expense->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes']
        ]);

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'تم اعتماد المصروف بنجاح');
    }

    /**
     * Reject the expense.
     */
    public function reject(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        $expense->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason']
        ]);

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'تم رفض المصروف');
    }

    /**
     * Export expenses to Excel.
     */
    public function export(Request $request)
    {
        // يمكن تنفيذ التصدير هنا باستخدام Laravel Excel
        return redirect()->route('expenses.index')
            ->with('info', 'ميزة التصدير قيد التطوير');
    }

    /**
     * Download expense receipt.
     */
    public function downloadReceipt(Expense $expense)
    {
        if (!$expense->receipt_path || !Storage::disk('public')->exists($expense->receipt_path)) {
            abort(404, 'الإيصال غير موجود');
        }

        return Storage::disk('public')->download($expense->receipt_path, $expense->receipt_filename);
    }

    /**
     * Get expenses summary for reports.
     */
    public function getSummary(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'approved');

        $summary = [
            'total_amount' => $expenses->sum('amount'),
            'total_count' => $expenses->count(),
            'by_category' => $expenses->selectRaw('category, SUM(amount) as total, COUNT(*) as count')
                ->groupBy('category')
                ->get(),
            'by_month' => $expenses->selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
                ->groupByRaw('MONTH(expense_date)')
                ->get(),
            'billable_amount' => $expenses->where('is_billable', true)->sum('amount'),
            'non_billable_amount' => $expenses->where('is_billable', false)->sum('amount')
        ];

        return response()->json($summary);
    }

    /**
     * Get my expenses (for current user).
     */
    public function myExpenses(Request $request)
    {
        $query = Expense::where('user_id', Auth::id())
            ->with(['legalCase.client'])
            ->latest('expense_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $expenses = $query->paginate(10);

        return view('expenses.my-expenses', compact('expenses'));
    }
}