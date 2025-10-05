<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Exception;

class Hearing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'case_id',
        'hearing_date',
        'hearing_time',
        'title',
        'description',
        'court_name',
        'court_room',
        'judge_name',
        'hearing_type',
        'status',
        'notes',
        'result',
        'next_hearing_date',
        'documents_required',
        'reminder_sent',
        'postponed_to',
        'postpone_reason',
        'completed_at',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'hearing_date',
        'next_hearing_date',
        'completed_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'hearing_date' => 'date',
        'next_hearing_date' => 'date',
        'completed_at' => 'datetime',
        'reminder_sent' => 'boolean'
    ];

    /* -------------------------------------------------------------
     | العلاقات
     |--------------------------------------------------------------
     */
    public function case()
    {
        // تأكد أن CaseModel هو الموديل الصحيح للقضايا
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    public function postponedFrom()
    {
        return $this->belongsTo(Hearing::class, 'postponed_to');
    }

    public function postponedTo()
    {
        return $this->hasOne(Hearing::class, 'postponed_to');
    }

    /* -------------------------------------------------------------
     | القوائم الثابتة (أنواع الجلسات وحالاتها)
     |--------------------------------------------------------------
     */
    public static function getHearingTypes()
    {
        return [
            'initial' => 'جلسة أولى',
            'evidence' => 'جلسة بينات',
            'pleading' => 'جلسة مرافعة',
            'judgment' => 'جلسة حكم',
            'appeal' => 'جلسة استئناف',
            'execution' => 'جلسة تنفيذ',
            'other' => 'أخرى'
        ];
    }

    public static function getStatuses()
    {
        return [
            'scheduled' => 'مجدولة',
            'completed' => 'مكتملة',
            'postponed' => 'مؤجلة',
            'cancelled' => 'ملغية'
        ];
    }

    /* -------------------------------------------------------------
     | Accessors
     |--------------------------------------------------------------
     */
    public function getFullDateTimeAttribute()
    {
        return ($this->hearing_date && $this->hearing_time)
            ? Carbon::parse($this->hearing_date . ' ' . $this->hearing_time)
            : null;
    }

    public function getFormattedDateAttribute()
    {
        return $this->hearing_date ? $this->hearing_date->format('Y-m-d') : null;
    }

    public function getFormattedTimeAttribute()
    {
        return $this->hearing_time
            ? Carbon::createFromFormat('H:i:s', $this->hearing_time)->format('H:i')
            : null;
    }

    public function getIsUpcomingAttribute()
    {
        return $this->hearing_date && $this->hearing_date->isFuture();
    }

    public function getIsTodayAttribute()
    {
        return $this->hearing_date && $this->hearing_date->isToday();
    }

    public function getIsPastAttribute()
    {
        return $this->hearing_date && $this->hearing_date->isPast();
    }

    public function getTimeUntilHearingAttribute()
    {
        if (!$this->hearing_date || !$this->hearing_time) {
            return null;
        }

        $hearingDateTime = Carbon::parse($this->hearing_date . ' ' . $this->hearing_time);
        return now()->diffInHours($hearingDateTime, false);
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'scheduled' => 'primary',
            'completed' => 'success',
            'postponed' => 'warning',
            'cancelled' => 'danger'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /* -------------------------------------------------------------
     | Scopes (استعلامات جاهزة)
     |--------------------------------------------------------------
     */
    public function scopeUpcoming($query)
    {
        return $query->where('hearing_date', '>=', now())
                    ->where('status', 'scheduled')
                    ->orderBy('hearing_date')
                    ->orderBy('hearing_time');
    }

    public function scopePast($query)
    {
        return $query->where('hearing_date', '<', now())
                    ->orderBy('hearing_date', 'desc')
                    ->orderBy('hearing_time', 'desc');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('hearing_date', today())
                    ->orderBy('hearing_time');
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('hearing_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->orderBy('hearing_date')->orderBy('hearing_time');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('hearing_date', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])->orderBy('hearing_date')->orderBy('hearing_time');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('hearing_type', $type);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePostponed($query)
    {
        return $query->where('status', 'postponed');
    }

    // 🧩 الجلسات الفائتة (المهمة لحساب missed hearings)
    public function scopeMissed($query)
    {
        return $query->where('hearing_date', '<', now())
                     ->whereNotIn('status', ['completed', 'cancelled', 'postponed']);
    }

    // 🕐 الجلسات المتأخرة عن وقتها اليوم
    public function scopeLate($query)
    {
        return $query->whereDate('hearing_date', today())
                     ->where('hearing_time', '<', now()->format('H:i:s'))
                     ->whereNotIn('status', ['completed', 'cancelled']);
    }

    /* -------------------------------------------------------------
     | Helper Methods
     |--------------------------------------------------------------
     */
    public function markAsCompleted($result = null, $notes = null)
    {
        $this->update([
            'status' => 'completed',
            'result' => $result,
            'notes' => $notes ?? $this->notes,
            'completed_at' => now()
        ]);
    }

    public function postpone($newDate, $newTime, $reason = null)
    {
        // إنشاء جلسة جديدة بعد التأجيل
        $newHearing = $this->replicate();
        $newHearing->hearing_date = $newDate;
        $newHearing->hearing_time = $newTime;
        $newHearing->status = 'scheduled';
        $newHearing->notes = ($this->notes ? $this->notes . "\n\n" : '') .
            "تم تأجيل الجلسة الأصلية من {$this->hearing_date} {$this->hearing_time}" .
            ($reason ? "\nسبب التأجيل: {$reason}" : '');
        $newHearing->save();

        $this->update([
            'status' => 'postponed',
            'postponed_to' => $newHearing->id,
            'postpone_reason' => $reason
        ]);

        return $newHearing;
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $this->notes . ($reason ? "\n\nسبب الإلغاء: " . $reason : '')
        ]);
    }

    public function getTimingInfo()
    {
        if (!$this->hearing_date || !$this->hearing_time) {
            return null;
        }

        try {
            // تنظيف وتهيئة التوقيت
            $timeString = $this->hearing_time;
            
            // إزالة أي تاريخ مدمج في التوقيت
            if (strpos($timeString, ' ') !== false) {
                $parts = explode(' ', $timeString);
                $timeString = end($parts);
            }
            
            // التأكد من صيغة الوقت
            if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $timeString)) {
                return null;
            }
            
            $hearingDateTime = Carbon::parse($this->hearing_date->format('Y-m-d') . ' ' . $timeString);
        } catch (\Exception $e) {
            return null;
        }
        $now = now();

        if ($hearingDateTime->isPast()) {
            return [
                'status' => 'past',
                'message' => 'انتهت منذ ' . $hearingDateTime->diffForHumans($now),
                'class' => 'text-muted'
            ];
        }

        if ($hearingDateTime->isToday()) {
            return [
                'status' => 'today',
                'message' => 'اليوم في ' . $hearingDateTime->format('H:i'),
                'class' => 'text-primary fw-bold'
            ];
        }

        if ($hearingDateTime->isTomorrow()) {
            return [
                'status' => 'tomorrow',
                'message' => 'غداً في ' . $hearingDateTime->format('H:i'),
                'class' => 'text-warning fw-bold'
            ];
        }

        return [
            'status' => 'upcoming',
            'message' => $hearingDateTime->diffForHumans($now),
            'class' => 'text-success'
        ];
    }

    /**
     * تحديد ما إذا كانت الجلسة يمكن تعديلها
     */
    public function canBeModified()
    {
        // يمكن تعديل الجلسة إذا كانت:
        // 1. مجدولة أو مؤجلة
        // 2. لم تنته بعد 
        return in_array($this->status, ['scheduled', 'postponed']) && 
               (!$this->hearing_date || $this->hearing_date >= now()->toDateString());
    }

    /**
     * إضافة دالة للحصول على قائمة النتائج الممكنة
     */
    public static function getResults()
    {
        return [
            'pending' => 'في الانتظار',
            'completed' => 'مكتملة',
            'postponed' => 'مؤجلة',
            'cancelled' => 'ملغاة',
            'judgment' => 'حكم',
            'settlement' => 'صلح',
            'referral' => 'إحالة'
        ];
    }
}
