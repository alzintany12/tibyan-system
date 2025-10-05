<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('task_type', [
                'document_review', 'research', 'client_meeting', 'court_appearance',
                'document_preparation', 'follow_up', 'administrative', 'other'
            ])->default('other');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled', 'on_hold'])->default('pending');
            $table->date('due_date')->nullable();
            $table->time('due_time')->nullable();
            $table->decimal('estimated_hours', 6, 2)->nullable();
            $table->decimal('actual_hours', 6, 2)->nullable();
            $table->integer('completion_percentage')->default(0);
            $table->unsignedBigInteger('legal_case_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('assigned_to');
            $table->unsignedBigInteger('created_by');
            $table->timestamp('completed_at')->nullable();
            $table->json('tags')->nullable();
            $table->timestamp('reminder_date')->nullable();
            $table->boolean('is_billable')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('legal_case_id')->references('id')->on('legal_cases')->onDelete('set null');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            
            $table->index(['status', 'priority']);
            $table->index(['assigned_to', 'status']);
            $table->index(['due_date', 'status']);
            $table->index(['legal_case_id', 'status']);
            $table->index(['is_billable', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};