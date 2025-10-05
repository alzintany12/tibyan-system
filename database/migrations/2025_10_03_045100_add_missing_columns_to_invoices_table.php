<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {

            // إضافة الحقول فقط إذا لم تكن موجودة
            if (!Schema::hasColumn('invoices', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('tax_amount');
            }

            if (!Schema::hasColumn('invoices', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->default(0)->after('total_amount');
            }

            if (!Schema::hasColumn('invoices', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_date');
            }

            if (!Schema::hasColumn('invoices', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('paid_at');
            }

            if (!Schema::hasColumn('invoices', 'terms')) {
                $table->text('terms')->nullable()->after('notes');
            }
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            // حذف الأعمدة فقط إذا كانت موجودة
            $columns = ['discount_amount', 'paid_amount', 'paid_at', 'sent_at', 'terms'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
