<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'legal_cases';

    protected $fillable = [
        'case_number',
        'case_title',
        'client_id',
        'client_id_number',
        'client_name',
        'client_phone',
        'user_id',
        'opposing_party',
        'court_name',
        'court_type',
        'case_type',
        'case_category',
        'status',
        'priority',
        'start_date',
        'end_date',
        'expected_end_date',
        'actual_end_date',
        'next_hearing_date',
        'next_hearing_time',
        'description',
        'case_summary',
        'notes',
        'result',
        'fee_amount',
        'fee_type',
        'fee_percentage',
        'total_fees',
        'fees_received',
        'fees_pending',
        'estimated_hours',
        'actual_hours',
        'case_value',
        'case_documents',
        'opponent_name',
        'opponent_info',
        'created_by',
        'updated_by',
        'is_archived',
        'is_active'
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'expected_end_date',
        'actual_end_date',
        'next_hearing_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'fees_received' => 'decimal:2',
        'fees_pending' => 'decimal:2',
        'total_fees' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'fee_percentage' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'case_value' => 'decimal:2',
        'case_documents' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'expected_end_date' => 'date',
        'actual_end_date' => 'date',
        'next_hearing_date' => 'date',
        'next_hearing_time' => 'datetime:H:i',
        'is_archived' => 'boolean',
        'is_active' => 'boolean'
    ];

    // العلاقات
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function hearings()
    {
        return $this->hasMany(Hearing::class, 'case_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'case_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'case_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'case_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'case_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'case_id');
    }

    // الحالات المتاحة للقضية
    public static function getStatuses()
    {
        return [
            'pending' => 'قيد الانتظار',
            'active' => 'نشطة',
            'completed' => 'مكتملة',
            'postponed' => 'مؤجلة',
            'suspended' => 'معلقة',
            'rejected' => 'مرفوضة'
        ];
    }

    // مستويات الأولوية
    public static function getPriorities()
    {
        return [
            'low' => 'منخفضة',
            'medium' => 'متوسطة',
            'high' => 'عالية',
            'urgent' => 'عاجلة'
        ];
    }

    // أنواع القضايا
    public static function getCaseTypes()
    {
        return [
            'civil' => 'مدنية',
            'commercial' => 'تجارية',
            'criminal' => 'جنائية',
            'family' => 'أحوال شخصية',
            'administrative' => 'إدارية',
            'labor' => 'عمالية',
            'real_estate' => 'عقارية',
            'other' => 'أخرى'
        ];
    }

    // أنواع المحاكم
    public static function getCourtTypes()
    {
        return [
            'general' => 'عامة',
            'commercial' => 'تجارية',
            'labor' => 'عمالية',
            'administrative' => 'إدارية',
            'criminal' => 'جنائية',
            'family' => 'أحوال شخصية'
        ];
    }

    // أنواع الرسوم
    public static function getFeeTypes()
    {
        return [
            'fixed' => 'ثابت',
            'hourly' => 'بالساعة',
            'percentage' => 'نسبة مئوية',
            'mixed' => 'مختلط'
        ];
    }

    // الحصول على الجلسة القادمة
    public function getNextHearingAttribute()
    {
        return $this->hearings()
            ->where('hearing_date', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('hearing_date')
            ->orderBy('hearing_time')
            ->first();
    }

    // الحصول على آخر جلسة
    public function getLastHearingAttribute()
    {
        return $this->hearings()
            ->orderBy('hearing_date', 'desc')
            ->orderBy('hearing_time', 'desc')
            ->first();
    }

    // حساب المبلغ المتبقي
    public function getRemainingFeesAttribute()
    {
        return $this->total_fees - $this->fees_received;
    }

    // التحقق من اكتمال الأتعاب
    public function getFeesCompletedAttribute()
    {
        return $this->fees_received >= $this->total_fees;
    }

    // الحصول على نسبة الإنجاز
    public function getProgressPercentageAttribute()
    {
        if ($this->total_fees <= 0) {
            return 0;
        }
        return round(($this->fees_received / $this->total_fees) * 100, 2);
    }

    // التحقق من تأخر الجلسة
    public function getIsOverdueAttribute()
    {
        return $this->next_hearing_date && $this->next_hearing_date->isPast();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_active', true);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('case_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeUpcoming($query)
    {
        return $query->whereNotNull('next_hearing_date')
            ->where('next_hearing_date', '>=', now());
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotNull('next_hearing_date')
            ->where('next_hearing_date', '<', now())
            ->where('status', '!=', 'completed');
    }
}