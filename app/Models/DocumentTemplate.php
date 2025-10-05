<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentTemplate extends Model
{
    use HasFactory;

    /**
     * الحقول المسموح تعبئتها
     */
    protected $fillable = [
        'name',
        'description',
        'type',
        'category',
        'content',
        'variables',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'is_active',
        'is_system_default',
        'user_id',
        'created_by',
        'usage_count',
    ];

    /**
     * التحويلات (casts)
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_system_default' => 'boolean',
        'usage_count' => 'integer',
        'variables' => 'array',
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 🔗 المستخدم الذي أنشأ القالب
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * 🔗 المستندات المرتبطة بهذا القالب
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'template_id');
    }

    /**
     * 🔍 سكوب: القوالب النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 🔍 سكوب: القوالب حسب التصنيف
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * ⬆️ زيادة عدّاد الاستخدام
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }
}
