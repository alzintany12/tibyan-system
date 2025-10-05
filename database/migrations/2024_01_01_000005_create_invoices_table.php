<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            
            // رقم الفاتورة
            $table->string('invoice_number')->unique();

            // ربط بالقضية
            $table->unsignedBigInteger('case_id');

            // العميل (اختياري)
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('client_name')->nullable();

            // التواريخ
            $table->date('invoice_date');
            $table->date('due_date');

            // تفاصيل مالية
            $table->text('description');
            $table->decimal('amount', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);

            // حالة الفاتورة
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])
                  ->default('draft');

            // طريقة الدفع
            $table->enum('payment_method', [
                'cash', 'bank_transfer', 'check', 'credit_card', 'other'
            ])->nullable();

            $table->date('payment_date')->nullable();

            // إضافات
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();

            $table->string('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // علاقات
            $table->foreign('case_id')
                  ->references('id')->on('legal_cases')
                  ->onDelete('cascade');

            $table->foreign('client_id')
                  ->references('id')->on('clients')
                  ->nullOnDelete(); // أفضل من cascade لأن العميل ممكن يتمسح
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
