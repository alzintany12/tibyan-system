<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'version_number',
        'file_path',
        'file_size',
        'changes_description',
        'created_by'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'version_number' => 'integer'
    ];

    // العلاقات
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}