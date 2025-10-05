<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->enum('expense_type', [
                'court_fees', 'expert_fees', 'travel', 'documentation',
                'translation', 'copying', 'postage', 'office', 'other'
            ])->default('other');
            $table->enum('category', ['legal', 'administrative', 'operational', 'marketing'])->default('operational');
            $table->date('expense_date');
            $table->unsignedBigInteger('legal_case_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->string('receipt_number')->nullable();
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'credit_card'])->default('cash');
            $table->text('notes')->nullable();
            $table->boolean('is_billable')->default(false);
            $table->boolean('is_reimbursable')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->date('approval_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'reimbursed'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('legal_case_id')->references('id')->on('legal_cases')->onDelete('set null');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
            
            $table->index(['expense_type', 'category']);
            $table->index(['legal_case_id', 'expense_date']);
            $table->index(['status', 'is_billable']);
            $table->index(['expense_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
};