<?php

namespace App\Http\Controllers;

use App\Models\LegalCase;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Task;
use App\Models\Expense;
use App\Models\Hearing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Display the main reports page.
     */
    public function index()
    {
        // الإحصائيات السريعة
        $quickStats = [
            'total_cases' => LegalCase::count(),
            'total_clients' => Client::where('is_active', true)->count(),
            'total_revenue' => Payment::sum('amount'),
            'pending_tasks' => Task::where('status', '!=', 'completed')->count()
        ];

        return view('reports.index', compact('quickStats'));
    }

    /**
     * Generate cases report.
     */
    public function casesReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());
        
        $cases = LegalCase::with(['client', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        // فلترة حسب المحامي
        if ($request->filled('lawyer_id')) {
            $cases->where('user_id', $request->lawyer_id);
        }

        // فلترة حسب النوع
        if ($request->filled('case_type')) {
            $cases->where('type', $request->case_type);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $cases->where('status', $request->status);
        }

        $casesData = $cases->get();

        // الإحصائيات
        $statistics = [
            'total_cases' => $casesData->count(),
            'active_cases' => $casesData->where('is_active', true)->count(),
            'closed_cases' => $casesData->where('status', 'closed')->count(),
            'won_cases' => $casesData->where('status', 'won')->count(),
            'lost_cases' => $casesData->where('status', 'lost')->count(),
            'by_type' => $casesData->groupBy('type')->map->count(),
            'by_status' => $casesData->groupBy('status')->map->count(),
            'by_lawyer' => $casesData->groupBy('user.name')->map->count(),
            'monthly_trend' => $this->getCasesMonthlyTrend($startDate, $endDate)
        ];

        return view('reports.cases', compact('casesData', 'statistics', 'startDate', 'endDate'));
    }

    /**
     * Generate financial report.
     */
    public function financialReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        // الفواتير
        $invoices = Invoice::with(['client', 'legalCase', 'payments'])
            ->whereBetween('invoice_date', [$startDate, $endDate]);

        // المدفوعات
        $payments = Payment::with(['invoice.client'])
            ->whereBetween('payment_date', [$startDate, $endDate]);

        // المصروفات
        $expenses = Expense::with(['legalCase', 'user'])
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'approved');

        $invoicesData = $invoices->get();
        $paymentsData = $payments->get();
        $expensesData = $expenses->get();

        // الإحصائيات المالية
        $statistics = [
            'total_invoiced' => $invoicesData->sum('total_amount'),
            'total_paid' => $paymentsData->sum('amount'),
            'outstanding_amount' => $invoicesData->sum('total_amount') - $paymentsData->sum('amount'),
            'total_expenses' => $expensesData->sum('amount'),
            'net_profit' => $paymentsData->sum('amount') - $expensesData->sum('amount'),
            'invoices_by_status' => $invoicesData->groupBy('status')->map->sum('total_amount'),
            'payments_by_method' => $paymentsData->groupBy('payment_method')->map->sum('amount'),
            'expenses_by_category' => $expensesData->groupBy('category')->map->sum('amount'),
            'monthly_revenue' => $this->getMonthlyRevenue($startDate, $endDate),
            'monthly_expenses' => $this->getMonthlyExpenses($startDate, $endDate)
        ];

        return view('reports.financial', compact('invoicesData', 'paymentsData', 'expensesData', 'statistics', 'startDate', 'endDate'));
    }

    /**
     * Generate productivity report.
     */
    public function productivityReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        // المهام
        $tasks = Task::with(['user', 'assignedTo', 'legalCase'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        // الجلسات
        $hearings = Hearing::with(['legalCase', 'user'])
            ->whereBetween('hearing_date', [$startDate, $endDate]);

        $tasksData = $tasks->get();
        $hearingsData = $hearings->get();

        // إحصائيات الإنتاجية
        $statistics = [
            'total_tasks' => $tasksData->count(),
            'completed_tasks' => $tasksData->where('status', 'completed')->count(),
            'pending_tasks' => $tasksData->where('status', 'pending')->count(),
            'overdue_tasks' => $tasksData->where('due_date', '<', now())->where('status', '!=', 'completed')->count(),
            'total_hearings' => $hearingsData->count(),
            'completed_hearings' => $hearingsData->where('status', 'completed')->count(),
            'upcoming_hearings' => $hearingsData->where('hearing_date', '>', now())->count(),
            'tasks_by_user' => $tasksData->groupBy('assignedTo.name')->map->count(),
            'tasks_by_priority' => $tasksData->groupBy('priority')->map->count(),
            'hearings_by_user' => $hearingsData->groupBy('user.name')->map->count(),
            'completion_rate' => $this->getTaskCompletionRate($startDate, $endDate)
        ];

        return view('reports.productivity', compact('tasksData', 'hearingsData', 'statistics', 'startDate', 'endDate'));
    }

    /**
     * Generate clients report.
     */
    public function clientsReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $clients = Client::with(['legalCases', 'invoices'])
            ->where('is_active', true);

        // إحصائيات العملاء
        $clientsData = $clients->get();
        
        $statistics = [
            'total_clients' => $clientsData->count(),
            'new_clients' => Client::whereBetween('created_at', [$startDate, $endDate])->count(),
            'active_clients' => $clientsData->where('is_active', true)->count(),
            'clients_with_cases' => $clientsData->filter(function ($client) {
                return $client->legalCases->count() > 0;
            })->count(),
            'top_clients_by_revenue' => $this->getTopClientsByRevenue(),
            'clients_by_type' => $clientsData->groupBy('type')->map->count(),
            'geographic_distribution' => $clientsData->groupBy('city')->map->count(),
            'cases_per_client' => $clientsData->map(function ($client) {
                return [
                    'name' => $client->name,
                    'cases_count' => $client->legalCases->count(),
                    'total_revenue' => $client->invoices->sum('total_amount')
                ];
            })->sortByDesc('cases_count')->take(10)
        ];

        return view('reports.clients', compact('clientsData', 'statistics', 'startDate', 'endDate'));
    }

    /**
     * Generate custom report.
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:cases,financial,productivity,clients,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:html,pdf,excel',
            'filters' => 'nullable|array'
        ]);

        $data = $this->generateReportData($validated);

        switch ($validated['format']) {
            case 'pdf':
                return $this->generatePDF($data, $validated);
            case 'excel':
                return $this->generateExcel($data, $validated);
            default:
                return view('reports.generated', compact('data'));
        }
    }

    /**
     * Generate PDF report.
     */
    private function generatePDF($data, $config)
    {
        $pdf = Pdf::loadView('reports.pdf-template', compact('data', 'config'));
        
        $filename = $config['report_type'] . '_report_' . now()->format('Y_m_d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Generate Excel report.
     */
    private function generateExcel($data, $config)
    {
        // يمكن تنفيذ التصدير إلى Excel هنا
        return redirect()->back()->with('info', 'ميزة تصدير Excel قيد التطوير');
    }

    /**
     * Generate report data based on type.
     */
    private function generateReportData($config)
    {
        switch ($config['report_type']) {
            case 'cases':
                return $this->getCasesReportData($config['start_date'], $config['end_date'], $config['filters'] ?? []);
            case 'financial':
                return $this->getFinancialReportData($config['start_date'], $config['end_date'], $config['filters'] ?? []);
            case 'productivity':
                return $this->getProductivityReportData($config['start_date'], $config['end_date'], $config['filters'] ?? []);
            case 'clients':
                return $this->getClientsReportData($config['start_date'], $config['end_date'], $config['filters'] ?? []);
            default:
                return [];
        }
    }

    /**
     * Get cases monthly trend.
     */
    private function getCasesMonthlyTrend($startDate, $endDate)
    {
        return LegalCase::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('year', 'month')
            ->orderBy('year', 'month')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => Carbon::create($item->year, $item->month)->format('Y-m'),
                    'count' => $item->count
                ];
            });
    }

    /**
     * Get monthly revenue.
     */
    private function getMonthlyRevenue($startDate, $endDate)
    {
        return Payment::select(
                DB::raw('YEAR(payment_date) as year'),
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->groupBy('year', 'month')
            ->orderBy('year', 'month')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => Carbon::create($item->year, $item->month)->format('Y-m'),
                    'total' => $item->total
                ];
            });
    }

    /**
     * Get monthly expenses.
     */
    private function getMonthlyExpenses($startDate, $endDate)
    {
        return Expense::select(
                DB::raw('YEAR(expense_date) as year'),
                DB::raw('MONTH(expense_date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->groupBy('year', 'month')
            ->orderBy('year', 'month')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => Carbon::create($item->year, $item->month)->format('Y-m'),
                    'total' => $item->total
                ];
            });
    }

    /**
     * Get task completion rate by user.
     */
    private function getTaskCompletionRate($startDate, $endDate)
    {
        return User::with(['assignedTasks' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function ($user) {
                $totalTasks = $user->assignedTasks->count();
                $completedTasks = $user->assignedTasks->where('status', 'completed')->count();
                
                return [
                    'user' => $user->name,
                    'total_tasks' => $totalTasks,
                    'completed_tasks' => $completedTasks,
                    'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0
                ];
            })
            ->where('total_tasks', '>', 0)
            ->sortByDesc('completion_rate');
    }

    /**
     * Get top clients by revenue.
     */
    private function getTopClientsByRevenue()
    {
        return Client::select('clients.*')
            ->selectSub(function ($query) {
                $query->from('payments')
                    ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                    ->whereColumn('invoices.client_id', 'clients.id')
                    ->selectRaw('SUM(payments.amount)');
            }, 'total_revenue')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();
    }

    /**
     * Dashboard analytics data.
     */
    public function getDashboardAnalytics()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        return response()->json([
            'cases_by_month' => $this->getCasesMonthlyTrend(now()->startOfYear(), now()->endOfYear()),
            'revenue_by_month' => $this->getMonthlyRevenue(now()->startOfYear(), now()->endOfYear()),
            'task_completion' => $this->getTaskCompletionRate(now()->startOfMonth(), now()->endOfMonth()),
            'upcoming_hearings' => Hearing::where('hearing_date', '>', now())
                ->where('hearing_date', '<=', now()->addDays(7))
                ->count(),
            'overdue_tasks' => Task::where('due_date', '<', now())
                ->where('status', '!=', 'completed')
                ->count(),
            'pending_invoices' => Invoice::where('status', 'sent')
                ->sum('total_amount'),
            'monthly_revenue' => Payment::whereMonth('payment_date', $currentMonth)
                ->whereYear('payment_date', $currentYear)
                ->sum('amount')
        ]);
    }
}