<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class LegalCase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'case_number',
        'case_title',
        'client_id',
        'opposing_party',
        'court_name',
        'court_type',
        'case_type',
        'case_category',
        'status',
        'priority',
        'start_date',
        'end_date',
        'next_hearing_date',
        'next_hearing_time',
        'description',
        'notes',
        'fee_amount',
        'fee_type',
        'fee_percentage',
        'estimated_hours',
        'actual_hours',
        'assigned_to',
        'created_by',
        'case_value',
        'is_archived'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_hearing_date' => 'date',
        'fee_amount' => 'decimal:2',
        'fee_percentage' => 'decimal:2',
        'case_value' => 'decimal:2',
        'estimated_hours' => 'integer',
        'actual_hours' => 'decimal:2',
        'is_archived' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // حالات القضية
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_POSTPONED = 'postponed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_PENDING = 'pending';

    // أولويات القضية
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // أنواع الأتعاب
    const FEE_TYPE_FIXED = 'fixed';
    const FEE_TYPE_HOURLY = 'hourly';
    const FEE_TYPE_PERCENTAGE = 'percentage';
    const FEE_TYPE_MIXED = 'mixed';

    // أنواع المحاكم
    const COURT_TYPE_GENERAL = 'general';
    const COURT_TYPE_COMMERCIAL = 'commercial';
    const COURT_TYPE_LABOR = 'labor';
    const COURT_TYPE_ADMINISTRATIVE = 'administrative';
    const COURT_TYPE_CRIMINAL = 'criminal';
    const COURT_TYPE_FAMILY = 'family';

    // العلاقات
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function hearings()
    {
        return $this->hasMany(Hearing::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // الخصائص المحسوبة
    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_ACTIVE => 'success',
            self::STATUS_COMPLETED => 'primary',
            self::STATUS_POSTPONED => 'warning',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_SUSPENDED => 'secondary',
            self::STATUS_PENDING => 'info'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getPriorityColorAttribute()
    {
        $colors = [
            self::PRIORITY_LOW => 'success',
            self::PRIORITY_MEDIUM => 'warning',
            self::PRIORITY_HIGH => 'danger',
            self::PRIORITY_URGENT => 'dark'
        ];

        return $colors[$this->priority] ?? 'secondary';
    }

    public function getDurationInDaysAttribute()
    {
        if (!$this->start_date) return 0;
        
        $endDate = $this->end_date ?? now();
        return $this->start_date->diffInDays($endDate);
    }

    public function getTotalExpensesAttribute()
    {
        return $this->expenses()->sum('amount');
    }

    public function getTotalInvoicesAttribute()
    {
        return $this->invoices()->sum('total_amount');
    }

    public function getPaidInvoicesAttribute()
    {
        return $this->invoices()->where('status', 'paid')->sum('total_amount');
    }

    public function getNextHearingInDaysAttribute()
    {
        if (!$this->next_hearing_date) return null;
        
        return now()->diffInDays($this->next_hearing_date, false);
    }

    // الفلاتر والنطاقات
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeWithUpcomingHearings($query)
    {
        return $query->where('next_hearing_date', '>=', now())
                    ->orderBy('next_hearing_date');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('case_number', 'LIKE', "%{$search}%")
              ->orWhere('case_title', 'LIKE', "%{$search}%")
              ->orWhere('opposing_party', 'LIKE', "%{$search}%")
              ->orWhereHas('client', function ($clientQuery) use ($search) {
                  $clientQuery->where('name', 'LIKE', "%{$search}%");
              });
        });
    }

    // دوال المساعدة
    public function generateCaseNumber()
    {
        $year = now()->year;
        $count = static::whereYear('created_at', $year)->count() + 1;
        
        return sprintf('CASE-%d-%04d', $year, $count);
    }

    public function canBeDeleted()
    {
        return $this->status !== self::STATUS_ACTIVE && 
               $this->invoices()->where('status', '!=', 'paid')->count() === 0;
    }

    public function isOverdue()
    {
        return $this->next_hearing_date && 
               $this->next_hearing_date->isPast() && 
               $this->status === self::STATUS_ACTIVE;
    }

    public function getStatusNameAttribute()
    {
        $statuses = [
            self::STATUS_ACTIVE => 'نشطة',
            self::STATUS_COMPLETED => 'مكتملة',
            self::STATUS_POSTPONED => 'مؤجلة',
            self::STATUS_REJECTED => 'مرفوضة',
            self::STATUS_SUSPENDED => 'معلقة',
            self::STATUS_PENDING => 'قيد الانتظار'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getPriorityNameAttribute()
    {
        $priorities = [
            self::PRIORITY_LOW => 'منخفضة',
            self::PRIORITY_MEDIUM => 'متوسطة',
            self::PRIORITY_HIGH => 'عالية',
            self::PRIORITY_URGENT => 'عاجلة'
        ];

        return $priorities[$this->priority] ?? $this->priority;
    }
}