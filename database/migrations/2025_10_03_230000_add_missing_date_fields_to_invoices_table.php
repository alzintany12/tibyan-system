<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'invoice_date')) {
                $table->dateTime('invoice_date')->nullable()->after('client_id');
            }

            if (!Schema::hasColumn('invoices', 'due_date')) {
                $table->dateTime('due_date')->nullable()->after('invoice_date');
            }

            if (!Schema::hasColumn('invoices', 'payment_date')) {
                $table->dateTime('payment_date')->nullable()->after('due_date');
            }
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'invoice_date')) {
                $table->dropColumn('invoice_date');
            }
            if (Schema::hasColumn('invoices', 'due_date')) {
                $table->dropColumn('due_date');
            }
            if (Schema::hasColumn('invoices', 'payment_date')) {
                $table->dropColumn('payment_date');
            }
        });
    }
};
