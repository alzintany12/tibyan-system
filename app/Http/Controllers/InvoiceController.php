<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\CaseModel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('case');
        
        // تصفية حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhereHas('case', function($caseQuery) use ($search) {
                      $caseQuery->where('case_number', 'like', "%{$search}%");
                  });
            });
        }
        
        // تصفية حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }
        
        // ترتيب النتائج
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $invoices = $query->paginate(15);
        
        // الإحصائيات
        $statistics = [
            'total' => Invoice::count(),
            'paid' => Invoice::where('status', 'paid')->count(),
            'pending' => Invoice::whereIn('status', ['sent', 'viewed', 'pending'])->count(),
            'overdue' => Invoice::where('status', 'overdue')->count(),
            'total_amount' => Invoice::sum('total_amount'),
            'paid_amount' => Invoice::where('status', 'paid')->sum('total_amount'),
            'pending_amount' => Invoice::whereIn('status', ['sent', 'overdue', 'viewed', 'pending'])->sum('total_amount'),
        ];
        
        return view('invoices.index', compact('invoices', 'statistics'));
    }

    public function create(Request $request)
    {
        $cases = CaseModel::where('status', 'active')->get();
        $selectedCaseId = $request->get('case_id');
        $selectedCase = null;
        
        if ($selectedCaseId) {
            $selectedCase = CaseModel::find($selectedCaseId);
        }
        
        return view('invoices.create', compact('cases', 'selectedCaseId', 'selectedCase'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'case_id' => 'required|exists:legal_cases,id',
            'client_name' => 'required|string|max:255',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'nullable|numeric|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0'
        ]);
        
        $validated['invoice_number'] = $this->generateInvoiceNumber();
        $validated['status'] = 'draft';
        $validated['created_by'] = auth()->user()->name ?? 'النظام';
        
        $invoice = Invoice::create($validated);
        
        // إضافة البنود إذا وجدت
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                if (!empty($item['description'])) {
                    $invoice->items()->create([
                        'description' => $item['description'],
                        'quantity' => $item['quantity'] ?? 1,
                        'unit_price' => $item['unit_price'] ?? 0,
                        'total_price' => ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0)
                    ]);
                }
            }
        }
        
        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'تم إنشاء الفاتورة بنجاح');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('case', 'items');
        
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $cases = CaseModel::where('is_active', true)->get();
        $invoice->load('items');
        
        return view('invoices.edit', compact('invoice', 'cases'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . $invoice->id,
            'case_id' => 'nullable|exists:legal_cases,id',
            'client_name' => 'required|string|max:255',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0.01',
            'paid_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,sent,viewed,paid,overdue,cancelled,pending',
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'nullable|numeric|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0'
        ]);
        
        $validated['updated_by'] = auth()->user()->name ?? 'النظام';
        
        $invoice->update($validated);
        
        // تحديث البنود
        $invoice->items()->delete(); // حذف البنود الحالية
        
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                if (!empty($item['description'])) {
                    $invoice->items()->create([
                        'description' => $item['description'],
                        'quantity' => $item['quantity'] ?? 1,
                        'unit_price' => $item['unit_price'] ?? 0,
                        'total_price' => ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0)
                    ]);
                }
            }
        }
        
        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'تم تحديث الفاتورة بنجاح');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        
        return redirect()->route('invoices.index')
            ->with('success', 'تم حذف الفاتورة بنجاح');
    }

    public function print(Invoice $invoice)
    {
        $invoice->load('case', 'items');
        
        try {
            $pdf = Pdf::loadView('invoices.print', compact('invoice'))
                      ->setPaper('a4', 'portrait')
                      ->setOptions([
                          'isHtml5ParserEnabled' => true,
                          'isPhpEnabled' => true,
                          'defaultFont' => 'DejaVu Sans'
                      ]);
            
            return $pdf->download("فاتورة-{$invoice->invoice_number}.pdf");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ في طباعة الفاتورة: ' . $e->getMessage());
        }
    }

    public function markAsPaid(Request $request, Invoice $invoice)
    {        
        $invoice->update([
            'status' => 'paid',
            'paid_amount' => $invoice->total_amount,
            'paid_at' => now(),
            'updated_by' => auth()->user()->name ?? 'النظام'
        ]);
        
        // تحديث أتعاب القضية
        if ($invoice->case) {
            $invoice->case->increment('fees_received', $invoice->total_amount);
            $invoice->case->decrement('fees_pending', $invoice->total_amount);
        }
        
        return redirect()->back()
            ->with('success', 'تم تحديد الفاتورة كمدفوعة');
    }

    public function send(Invoice $invoice)
    {
        $invoice->update([
            'status' => 'sent',
            'sent_at' => now(),
            'updated_by' => auth()->user()->name ?? 'النظام'
        ]);
        
        return redirect()->back()
            ->with('success', 'تم إرسال الفاتورة');
    }

    public function cancel(Invoice $invoice)
    {
        $invoice->update([
            'status' => 'cancelled',
            'updated_by' => auth()->user()->name ?? 'النظام'
        ]);
        
        return redirect()->back()
            ->with('success', 'تم إلغاء الفاتورة');
    }

    public function duplicate(Invoice $invoice)
    {
        $newInvoice = $invoice->replicate();
        $newInvoice->invoice_number = $this->generateInvoiceNumber();
        $newInvoice->status = 'draft';
        $newInvoice->paid_amount = 0;
        $newInvoice->paid_at = null;
        $newInvoice->sent_at = null;
        $newInvoice->invoice_date = now();
        $newInvoice->due_date = now()->addDays(30);
        $newInvoice->created_by = auth()->user()->name ?? 'النظام';
        $newInvoice->save();
        
        // نسخ البنود
        foreach ($invoice->items as $item) {
            $newInvoice->items()->create([
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price
            ]);
        }
        
        return redirect()->route('invoices.show', $newInvoice)
            ->with('success', 'تم نسخ الفاتورة بنجاح');
    }

    public function statistics()
    {
        $statistics = [
            'overview' => [
                'total_invoices' => Invoice::count(),
                'paid_invoices' => Invoice::where('status', 'paid')->count(),
                'pending_invoices' => Invoice::whereIn('status', ['sent', 'viewed', 'pending'])->count(),
                'overdue_invoices' => Invoice::where('status', 'overdue')->count(),
            ],
            'financial' => [
                'total_amount' => Invoice::sum('total_amount'),
                'paid_amount' => Invoice::where('status', 'paid')->sum('total_amount'),
                'pending_amount' => Invoice::whereIn('status', ['sent', 'overdue', 'viewed', 'pending'])->sum('total_amount'),
            ],
            'monthly_revenue' => Invoice::where('status', 'paid')
                ->whereNotNull('paid_at')
                ->selectRaw('MONTH(paid_at) as month, SUM(total_amount) as revenue')
                ->whereYear('paid_at', date('Y'))
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->pluck('revenue', 'month')
        ];
        
        return response()->json($statistics);
    }

    public function updateOverdueStatus()
    {
        $overdueInvoices = Invoice::where('due_date', '<', now())
                                 ->whereIn('status', ['sent', 'viewed', 'pending'])
                                 ->get();
        
        foreach ($overdueInvoices as $invoice) {
            $invoice->update(['status' => 'overdue']);
        }
        
        return response()->json([
            'updated' => $overdueInvoices->count(),
            'message' => 'تم تحديث حالة الفواتير المتأخرة'
        ]);
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