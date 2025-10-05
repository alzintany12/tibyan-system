<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'phone2',
        'address',
        'city',
        'id_number',
        'id_type',
        'company_name',
        'tax_number',
        'client_type',
        'notes',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // أنواع العملاء
    const TYPE_INDIVIDUAL = 'individual';
    const TYPE_COMPANY = 'company';

    // أنواع الهوية
    const ID_TYPE_NATIONAL = 'national_id';
    const ID_TYPE_PASSPORT = 'passport';
    const ID_TYPE_IQAMA = 'iqama';
    const ID_TYPE_COMMERCIAL = 'commercial_register';

    // العلاقات
    public function cases()
    {
        return $this->hasMany(LegalCase::class);
    }

    /**
     * Alias for backward compatibility:
     * بعض الأماكن في الكود تستدعي legalCases بدلاً من cases.
     */
    public function legalCases()
    {
        return $this->cases();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    // الخصائص المحسوبة
    public function getFullAddressAttribute()
    {
        return trim($this->address . ', ' . $this->city, ', ');
    }

    public function getActiveCasesAttribute()
    {
        return $this->cases()->whereIn('status', ['active', 'pending'])->count();
    }

    public function getTotalInvoicesAttribute()
    {
        return $this->invoices()->sum('total_amount');
    }

    public function getPaidInvoicesAttribute()
    {
        return $this->invoices()->where('status', 'paid')->sum('total_amount');
    }

    public function getUnpaidInvoicesAttribute()
    {
        return $this->invoices()->whereIn('status', ['pending', 'overdue'])->sum('total_amount');
    }

    // الفلاتر والنطاقات
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeIndividuals($query)
    {
        return $query->where('client_type', self::TYPE_INDIVIDUAL);
    }

    public function scopeCompanies($query)
    {
        return $query->where('client_type', self::TYPE_COMPANY);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('phone', 'LIKE', "%{$search}%")
              ->orWhere('id_number', 'LIKE', "%{$search}%")
              ->orWhere('company_name', 'LIKE', "%{$search}%");
        });
    }

    // دالة للحصول على نوع الهوية مترجم
    public function getIdTypeNameAttribute()
    {
        $types = [
            self::ID_TYPE_NATIONAL => 'هوية وطنية',
            self::ID_TYPE_PASSPORT => 'جواز سفر',
            self::ID_TYPE_IQAMA => 'إقامة',
            self::ID_TYPE_COMMERCIAL => 'سجل تجاري'
        ];

        return $types[$this->id_type] ?? $this->id_type;
    }

    // دالة للحصول على نوع العميل مترجم
    public function getClientTypeNameAttribute()
    {
        return $this->client_type === self::TYPE_INDIVIDUAL ? 'فرد' : 'شركة';
    }
}
