<?php

namespace App\Http\Controllers;

use App\Models\CaseModel;
use App\Models\Hearing;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // إحصائيات عامة
        $totalCases = CaseModel::count();
        $activeCases = CaseModel::where('status', 'active')->count();
        $completedCases = CaseModel::where('status', 'completed')->count();
        
        // إحصائيات الجلسات
        $todayHearings = Hearing::today()->count();
        $upcomingHearings = Hearing::upcoming()->count();
        $missedHearings = Hearing::missed()->count();
        
        // إحصائيات الفواتير
        $pendingInvoices = Invoice::pending()->count();
        $overdueInvoices = Invoice::overdue()->count();
        $paidInvoices = Invoice::paid()->count();
        
        // إحصائيات مالية
        $totalRevenue = Invoice::paid()->sum('total_amount');
        $pendingRevenue = Invoice::pending()->sum('total_amount');
        $overdueRevenue = Invoice::overdue()->sum('total_amount');
        
        // الجلسات القادمة (أقرب 5 جلسات)
        $upcomingHearingsData = Hearing::upcoming()
            ->with('case')
            ->limit(5)
            ->get();
        
        // القضايا النشطة الحديثة
        $recentCases = CaseModel::active()
            ->with('hearings')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // الفواتير المتأخرة
        $overdueInvoicesData = Invoice::overdue()
            ->with('case')
            ->orderBy('due_date')
            ->limit(5)
            ->get();
        
        // بيانات الرسم البياني للقضايا حسب النوع
        $casesByType = CaseModel::selectRaw('case_type, count(*) as count')
            ->groupBy('case_type')
            ->get()
            ->pluck('count', 'case_type');
        
        // بيانات الرسم البياني للإيرادات الشهرية
        $monthlyRevenue = Invoice::paid()
            ->selectRaw('MONTH(payment_date) as month, SUM(total_amount) as revenue')
            ->whereYear('payment_date', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('revenue', 'month');
        
        // جلسات هذا الأسبوع
        $weeklyHearings = Hearing::thisWeek()
            ->with('case')
            ->get()
            ->groupBy(function($hearing) {
                return $hearing->hearing_date->format('Y-m-d');
            });

        return view('dashboard.index', compact(
            'totalCases', 'activeCases', 'completedCases',
            'todayHearings', 'upcomingHearings', 'missedHearings',
            'pendingInvoices', 'overdueInvoices', 'paidInvoices',
            'totalRevenue', 'pendingRevenue', 'overdueRevenue',
            'upcomingHearingsData', 'recentCases', 'overdueInvoicesData',
            'casesByType', 'monthlyRevenue', 'weeklyHearings'
        ));
    }

    public function getStatistics(Request $request)
    {
        $period = $request->get('period', '30'); // آخر 30 يوم افتراضياً
        $startDate = Carbon::now()->subDays($period);
        
        $statistics = [
            'cases' => [
                'total' => CaseModel::where('created_at', '>=', $startDate)->count(),
                'active' => CaseModel::where('status', 'active')
                    ->where('created_at', '>=', $startDate)->count(),
                'completed' => CaseModel::where('status', 'completed')
                    ->where('created_at', '>=', $startDate)->count(),
            ],
            'hearings' => [
                'scheduled' => Hearing::where('status', 'scheduled')
                    ->where('hearing_date', '>=', $startDate)->count(),
                'completed' => Hearing::where('status', 'completed')
                    ->where('hearing_date', '>=', $startDate)->count(),
                'missed' => Hearing::where('status', 'missed')
                    ->where('hearing_date', '>=', $startDate)->count(),
            ],
            'invoices' => [
                'paid' => Invoice::where('status', 'paid')
                    ->where('payment_date', '>=', $startDate)->count(),
                'pending' => Invoice::where('status', 'sent')
                    ->where('created_at', '>=', $startDate)->count(),
                'overdue' => Invoice::where('status', 'overdue')
                    ->where('created_at', '>=', $startDate)->count(),
            ],
            'revenue' => [
                'total' => Invoice::where('status', 'paid')
                    ->where('payment_date', '>=', $startDate)
                    ->sum('total_amount'),
                'pending' => Invoice::whereIn('status', ['sent', 'overdue'])
                    ->where('created_at', '>=', $startDate)
                    ->sum('total_amount'),
            ]
        ];

        return response()->json($statistics);
    }

    public function getCalendarEvents(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');
        
        $hearings = Hearing::with('case')
            ->whereBetween('hearing_date', [$start, $end])
            ->get();
        
        $events = $hearings->map(function($hearing) {
            return [
                'id' => $hearing->id,
                'title' => "جلسة: {$hearing->case->case_number}",
                'start' => $hearing->hearing_date->format('Y-m-d') . 'T' . $hearing->hearing_time,
                'description' => $hearing->case->case_summary,
                'location' => $hearing->court_name,
                'className' => 'hearing-event status-' . $hearing->status,
                'url' => route('hearings.show', $hearing->id)
            ];
        });
        
        return response()->json($events);
    }
}