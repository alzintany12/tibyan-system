<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();

            // 📌 الحقول الأساسية
            $table->string('name'); // اسم القالب
            $table->string('description')->nullable(); // وصف القالب
            $table->string('category')->nullable(); // تصنيف (مثلاً: عقود، مذكرات...)

            // 📌 محتوى القالب (HTML أو نص منسق)
            $table->longText('content')->nullable();

            // 📌 معلومات عن الملفات المرتبطة بالقالب (اختياري)
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable();

            // 📌 الحالة
            $table->boolean('is_active')->default(true);

            // 📌 علاقة بالمستخدم
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // 📌 عدد مرات الاستخدام
            $table->unsignedInteger('usage_count')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};
