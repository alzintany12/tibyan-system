<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\LegalCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientsExport;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Client::with(['legalCases']);

        // البحث
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('national_id', 'LIKE', "%{$search}%");
            });
        }

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->where('client_type', $request->get('type'));
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $isActive = $request->get('status') === 'active';
            $query->where('is_active', $isActive);
        }

        // الترتيب
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $clients = $query->paginate(20);

        // إحصائيات
        $stats = [
            'total' => Client::count(),
            'active' => Client::where('is_active', true)->count(),
            'inactive' => Client::where('is_active', false)->count(),
            'individuals' => Client::where('client_type', 'individual')->count(),
            'companies' => Client::where('client_type', 'company')->count(),
        ];

        return view('clients.index', compact('clients', 'stats'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'client_type' => 'required|in:individual,company',
            'email' => 'nullable|email|unique:clients,email',
            'phone' => 'required|string|max:20',
            'secondary_phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:20|unique:clients,national_id',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'birth_date' => 'nullable|date',
            'occupation' => 'nullable|string|max:255',
            'company_registration' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'preferred_language' => 'nullable|in:ar,en',
            'communication_method' => 'nullable|in:phone,email,sms,whatsapp',
        ]);

        $validatedData['created_by'] = auth()->id();
        $validatedData['is_active'] = true;

        $client = Client::create($validatedData);

        return redirect()->route('clients.show', $client)
                        ->with('success', 'تم إنشاء العميل بنجاح');
    }

    public function show(Client $client)
    {
        $client->load([
            'legalCases' => function ($query) {
                $query->with(['assignedTo'])->orderBy('created_at', 'desc');
            },
            'invoices' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'creator'
        ]);

        // إحصائيات العميل
        $clientStats = [
            'total_cases' => $client->legalCases->count(),
            'active_cases' => $client->legalCases->where('status', 'active')->count(),
            'completed_cases' => $client->legalCases->where('status', 'completed')->count(),
            'total_invoices' => $client->invoices->sum('total_amount'),
            'paid_invoices' => $client->invoices->where('status', 'paid')->sum('total_amount'),
            'pending_invoices' => $client->invoices->where('status', 'pending')->sum('total_amount'),
        ];

        return view('clients.show', compact('client', 'clientStats'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'client_type' => 'required|in:individual,company',
            'email' => 'nullable|email|unique:clients,email,' . $client->id,
            'phone' => 'required|string|max:20',
            'secondary_phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:20|unique:clients,national_id,' . $client->id,
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'birth_date' => 'nullable|date',
            'occupation' => 'nullable|string|max:255',
            'company_registration' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'preferred_language' => 'nullable|in:ar,en',
            'communication_method' => 'nullable|in:phone,email,sms,whatsapp',
        ]);

        $validatedData['updated_by'] = auth()->id();

        $client->update($validatedData);

        return redirect()->route('clients.show', $client)
                        ->with('success', 'تم تحديث بيانات العميل بنجاح');
    }

    public function destroy(Client $client)
    {
        // التحقق من وجود قضايا نشطة
        if ($client->legalCases()->whereIn('status', ['active', 'pending'])->exists()) {
            return redirect()->back()
                            ->with('error', 'لا يمكن حذف العميل لوجود قضايا نشطة مرتبطة به');
        }

        $client->delete();

        return redirect()->route('clients.index')
                        ->with('success', 'تم حذف العميل بنجاح');
    }

    public function toggleStatus(Client $client)
    {
        $client->update([
            'is_active' => !$client->is_active,
            'updated_by' => auth()->id()
        ]);

        $status = $client->is_active ? 'تفعيل' : 'إلغاء تفعيل';
        
        return redirect()->back()
                        ->with('success', "تم {$status} العميل بنجاح");
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $clients = Client::where('name', 'LIKE', "%{$query}%")
                        ->orWhere('email', 'LIKE', "%{$query}%")
                        ->orWhere('phone', 'LIKE', "%{$query}%")
                        ->where('is_active', true)
                        ->limit(10)
                        ->get(['id', 'name', 'email', 'phone']);

        return response()->json($clients);
    }

    public function export(Request $request)
    {
        $filters = $request->only(['search', 'type', 'status']);
        
        return Excel::download(new ClientsExport($filters), 'clients-' . now()->format('Y-m-d') . '.xlsx');
    }
}