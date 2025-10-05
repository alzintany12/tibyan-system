<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('legal_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->string('case_title');
            
            // ربط بالعميل
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->string('client_name'); // إضافة اسم العميل للتسهيل
            $table->string('client_phone')->nullable(); // إضافة رقم هاتف العميل

            // ربط بالمستخدم (المحامي المكلف)
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            // تفاصيل القضية
            $table->string('opposing_party')->nullable();
            $table->string('court_name')->nullable();
            $table->enum('court_type', ['general', 'commercial', 'labor', 'administrative', 'criminal', 'family'])->nullable();
            $table->enum('case_type', [
                'civil', 'criminal', 'commercial', 'family', 
                'real_estate', 'labor', 'administrative', 'other'
            ])->default('civil');
            $table->string('case_category')->nullable();
            $table->enum('status', ['active', 'completed', 'postponed', 'rejected', 'suspended', 'pending'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            
            // التواريخ
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('expected_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->date('next_hearing_date')->nullable();
            $table->time('next_hearing_time')->nullable();
            
            // الوصف والملاحظات
            $table->text('description')->nullable();
            $table->text('case_summary')->nullable(); // إضافة ملخص القضية
            $table->text('notes')->nullable();
            $table->text('result')->nullable();
            
            // الرسوم والأتعاب
            $table->decimal('fee_amount', 10, 2)->nullable();
            $table->enum('fee_type', ['fixed', 'hourly', 'percentage', 'mixed'])->default('fixed');
            $table->decimal('fee_percentage', 5, 2)->nullable();
            $table->decimal('total_fees', 10, 2)->default(0);
            $table->decimal('fees_received', 10, 2)->default(0);
            $table->decimal('fees_pending', 10, 2)->default(0);
            
            // الساعات
            $table->integer('estimated_hours')->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();
            
            // معلومات إضافية
            $table->decimal('case_value', 15, 2)->nullable();
            $table->json('case_documents')->nullable();
            $table->string('opponent_name')->nullable();
            $table->text('opponent_info')->nullable();
            
            // المستخدم الذي أنشأ القضية
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->string('updated_by')->nullable();
            
            // إعدادات أخرى
            $table->boolean('is_archived')->default(false);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();

            // فهارس لتحسين الأداء
            $table->index(['case_number']);
            $table->index(['status', 'priority']);
            $table->index(['next_hearing_date']);
            $table->index(['client_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['status', 'case_type']);
            $table->index('client_name');
            $table->index('start_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('legal_cases');
    }
};