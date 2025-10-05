<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            // تحديث enum للحالة لحل مشكلة البيانات المقطوعة
            $table->enum('status', [
                'draft', 'sent', 'viewed', 'paid', 'overdue', 'cancelled', 'pending'
            ])->default('draft')->change();
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('status', [
                'draft', 'sent', 'paid', 'overdue', 'cancelled'
            ])->default('draft')->change();
        });
    }
};