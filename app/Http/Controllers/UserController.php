<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // فلترة حسب الدور
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } else {
                $query->where('is_active', false);
            }
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,lawyer,assistant,secretary',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'hire_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string'
        ]);

        $user = new User($validated);
        $user->password = Hash::make($validated['password']);
        $user->is_active = true;

        // رفع الصورة الشخصية
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . Str::slug($user->name) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['legalCases', 'tasks', 'expenses']);
        
        // إحصائيات المستخدم
        $stats = [
            'total_cases' => $user->legalCases()->count(),
            'active_cases' => $user->legalCases()->where('is_active', true)->count(),
            'total_tasks' => $user->tasks()->count(),
            'completed_tasks' => $user->tasks()->where('status', 'completed')->count(),
            'total_expenses' => $user->expenses()->where('status', 'approved')->sum('amount'),
            'pending_expenses' => $user->expenses()->where('status', 'pending')->count()
        ];

        return view('users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string|in:admin,lawyer,assistant,secretary',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'hire_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string'
        ]);

        $user->fill($validated);

        // رفع صورة شخصية جديدة
        if ($request->hasFile('avatar')) {
            // حذف الصورة القديمة
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $file = $request->file('avatar');
            $filename = time() . '_' . Str::slug($user->name) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->route('users.show', $user)
            ->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // لا يمكن حذف المستخدم الحالي
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'لا يمكن حذف حسابك الشخصي');
        }

        // تعطيل بدلاً من الحذف للحفاظ على السجلات
        $user->update(['is_active' => false]);

        return redirect()->route('users.index')
            ->with('success', 'تم تعطيل المستخدم بنجاح');
    }

    /**
     * Toggle user status (active/inactive).
     */
    public function toggleStatus(User $user)
    {
        // لا يمكن تعطيل المستخدم الحالي
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'لا يمكن تعطيل حسابك الشخصي');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'تم تفعيل' : 'تم تعطيل';
        
        return redirect()->route('users.show', $user)
            ->with('success', $status . ' المستخدم بنجاح');
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->route('users.show', $user)
            ->with('success', 'تم تحديث كلمة المرور بنجاح');
    }

    /**
     * Show user profile.
     */
    public function profile()
    {
        $user = Auth::user();
        $user->load(['legalCases', 'tasks', 'expenses']);
        
        // إحصائيات المستخدم
        $stats = [
            'total_cases' => $user->legalCases()->count(),
            'active_cases' => $user->legalCases()->where('is_active', true)->count(),
            'total_tasks' => $user->assignedTasks()->count(),
            'completed_tasks' => $user->assignedTasks()->where('status', 'completed')->count(),
            'pending_tasks' => $user->assignedTasks()->where('status', 'pending')->count(),
            'total_expenses' => $user->expenses()->where('status', 'approved')->sum('amount'),
            'pending_expenses' => $user->expenses()->where('status', 'pending')->count()
        ];

        return view('profile.index', compact('user', 'stats'));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20'
        ]);

        $user->update($validated);

        return redirect()->route('profile.index')
            ->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    /**
     * Update profile avatar.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = Auth::user();

        // حذف الصورة القديمة
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $file = $request->file('avatar');
        $filename = time() . '_' . Str::slug($user->name) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('avatars', $filename, 'public');
        
        $user->update(['avatar' => $path]);

        return redirect()->route('profile.index')
            ->with('success', 'تم تحديث الصورة الشخصية بنجاح');
    }

    /**
     * Search users (API endpoint).
     */
    public function search(Request $request)
    {
        $search = $request->input('q');
        
        $users = User::where('is_active', true)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->select('id', 'name', 'email', 'role')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    /**
     * Get user dashboard data.
     */
    public function getDashboardData()
    {
        $user = Auth::user();
        
        $data = [
            'upcoming_hearings' => $user->hearings()
                ->where('hearing_date', '>=', now())
                ->orderBy('hearing_date')
                ->limit(5)
                ->get(),
            'pending_tasks' => $user->assignedTasks()
                ->where('status', 'pending')
                ->orderBy('due_date')
                ->limit(5)
                ->get(),
            'recent_cases' => $user->legalCases()
                ->where('is_active', true)
                ->latest()
                ->limit(5)
                ->get(),
            'expense_summary' => [
                'pending' => $user->expenses()->where('status', 'pending')->sum('amount'),
                'approved' => $user->expenses()->where('status', 'approved')->sum('amount'),
                'this_month' => $user->expenses()
                    ->whereMonth('expense_date', now()->month)
                    ->where('status', 'approved')
                    ->sum('amount')
            ]
        ];

        return response()->json($data);
    }
}