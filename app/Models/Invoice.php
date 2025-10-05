<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'case_id',
        'client_name',
        'client_id',
        'invoice_date',
        'due_date',
        'description',
        'amount',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'status',
        'payment_method',
        'payment_date',
        'paid_at',
        'sent_at',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'invoice_date',
        'due_date',
        'payment_date',
        'paid_at',
        'sent_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'paid_at' => 'datetime',
        'sent_at' => 'datetime'
    ];

    // العلاقات
    public function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // الحالات المتاحة للفاتورة
    public static function getStatuses()
    {
        return [
            'draft' => 'مسودة',
            'sent' => 'مرسلة',
            'viewed' => 'تم الاطلاع',
            'paid' => 'مدفوعة',
            'overdue' => 'متأخرة',
            'cancelled' => 'ملغية',
            'pending' => 'معلقة'
        ];
    }

    // طرق الدفع المتاحة
    public static function getPaymentMethods()
    {
        return [
            'cash' => 'نقداً',
            'bank_transfer' => 'تحويل بنكي',
            'check' => 'شيك',
            'credit_card' => 'بطاقة ائتمان',
            'other' => 'أخرى'
        ];
    }

    // حساب المبلغ المتبقي
    public function getRemainingAmountAttribute()
    {
        return $this->total_amount - ($this->paid_amount ?? 0);
    }

    // التحقق من اكتمال الدفع
    public function getIsFullyPaidAttribute()
    {
        return $this->paid_amount >= $this->total_amount;
    }

    // التحقق من التأخير
    public function getIsOverdueAttribute()
    {
        return $this->due_date && $this->due_date->isPast() && $this->status != 'paid';
    }

    // حساب نسبة الدفع
    public function getPaymentPercentageAttribute()
    {
        if ($this->total_amount <= 0) {
            return 0;
        }
        return round((($this->paid_amount ?? 0) / $this->total_amount) * 100, 2);
    }

    // الحصول على عدد الأيام المتبقية للاستحقاق
    public function getDaysUntilDueAttribute()
    {
        if (!$this->due_date) {
            return null;
        }
        return now()->diffInDays($this->due_date, false);
    }

    // الحصول على حالة الدفع
    public function getPaymentStatusAttribute()
    {
        if ($this->status == 'paid') {
            return 'مدفوعة بالكامل';
        }
        
        if ($this->paid_amount > 0) {
            return 'مدفوعة جزئياً';
        }
        
        if ($this->is_overdue) {
            return 'متأخرة';
        }
        
        return 'غير مدفوعة';
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['draft', 'sent', 'viewed', 'pending']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
                    ->orWhere(function($q) {
                        $q->where('due_date', '<', now())
                          ->whereNotIn('status', ['paid', 'cancelled']);
                    });
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['sent', 'viewed', 'pending']);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('invoice_date', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('invoice_date', now()->year);
    }

    // Helper Methods
    public function markAsPaid($amount = null, $paymentMethod = null, $paymentDate = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_amount' => $amount ?? $this->total_amount,
            'payment_method' => $paymentMethod,
            'payment_date' => $paymentDate ?? now(),
            'paid_at' => now()
        ]);
    }

    public function markAsSent()
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $this->notes . ($reason ? "\n\nسبب الإلغاء: " . $reason : '')
        ]);
    }

    // إنشاء رقم فاتورة تلقائي
    public static function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        
        $lastInvoice = static::whereYear('created_at', $year)
                             ->whereMonth('created_at', $month)
                             ->orderBy('id', 'desc')
                             ->first();
        
        $nextNumber = $lastInvoice ? (int) substr($lastInvoice->invoice_number, -4) + 1 : 1;
        
        return "INV-{$year}-{$month}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}