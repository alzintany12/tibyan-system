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
        Schema::create('hearings', function (Blueprint $table) {
            $table->id();

            // ربط الجلسة بالقضية (يدعم كلا النظامين)
            $table->unsignedBigInteger('case_id')->nullable();
            $table->unsignedBigInteger('legal_case_id')->nullable();

            // تفاصيل الجلسة
            $table->date('hearing_date');
            $table->time('hearing_time')->nullable();

            $table->string('title')->nullable();
            $table->text('description')->nullable();

            // معلومات المحكمة
            $table->string('court_name')->nullable();
            $table->string('court_room')->nullable();
            $table->string('judge_name')->nullable();

            /**
             * ✅ أنواع الجلسات (محدّثة لتتوافق مع Seeder)
             */
            $table->enum('hearing_type', [
                'first',         // الجلسة الأولى
                'follow_up',     // جلسة متابعة
                'final',         // جلسة نهائية
                'appeal',        // استئناف
                'execution',     // تنفيذ
                'mediation',     // وساطة
                'expert',        // خبرة
                'witness',       // سماع شاهد
                'initial',       // افتتاحية
                'evidence',      // جلسة أدلة
                'judgment',      // جلسة حكم
                'pleading',      // جلسة مرافعة
                'deliberation',  // جلسة مداولة
                'announcement',  // جلسة إعلان الحكم
                'hearing',       // عام / افتراضي
                'other'          // ✅ تمت إضافتها لتتوافق مع HearingSeeder
            ])->default('hearing');

            // حالة الجلسة
            $table->enum('status', [
                'scheduled',   // مجدولة
                'completed',   // مكتملة
                'postponed',   // مؤجلة
                'cancelled',   // ملغاة
                'missed'       // لم يحضر
            ])->default('scheduled');

            // نتيجة الجلسة
            $table->text('result')->nullable();

            // تفاصيل إضافية
            $table->text('outcome')->nullable();

            // المستندات والمشاركون
            $table->json('documents_required')->nullable();
            $table->json('documents_submitted')->nullable();
            $table->json('attendees')->nullable();

            // تنبيه التذكير
            $table->boolean('reminder_sent')->default(false);

            // وقت إتمام الجلسة
            $table->timestamp('completed_at')->nullable();

            // تتبع من أنشأ وعدّل السجل
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hearings');
    }
};
