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
        // ✅ التأكد من وجود الحقول في جدول الجلسات
        if (Schema::hasTable('hearings')) {
            Schema::table('hearings', function (Blueprint $table) {
                if (!Schema::hasColumn('hearings', 'postponed_to')) {
                    $table->unsignedBigInteger('postponed_to')->nullable()->after('status');
                }
                if (!Schema::hasColumn('hearings', 'postpone_reason')) {
                    $table->string('postpone_reason')->nullable()->after('postponed_to');
                }

                // ✅ الفهارس لتحسين الأداء
                $this->safeAddIndex($table, 'case_id', 'hearings_case_id_index');
                $this->safeAddIndex($table, 'hearing_date', 'hearings_hearing_date_index');
                $this->safeAddIndex($table, 'status', 'hearings_status_index');
            });
        }

        // ✅ التأكد من وجود الحقول في جدول الفواتير
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'discount_amount')) {
                    $table->decimal('discount_amount', 10, 2)->default(0)->after('tax_amount');
                }

                $this->safeAddIndex($table, 'case_id', 'invoices_case_id_index');
                $this->safeAddIndex($table, 'status', 'invoices_status_index');
                $this->safeAddIndex($table, 'invoice_date', 'invoices_invoice_date_index');
            });
        }

        // ✅ التأكد من وجود الحقول في جدول القضايا
        if (Schema::hasTable('legal_cases')) {
            Schema::table('legal_cases', function (Blueprint $table) {
                if (!Schema::hasColumn('legal_cases', 'fees_pending')) {
                    $table->decimal('fees_pending', 10, 2)->default(0)->after('fees_received');
                }

                $this->safeAddIndex($table, 'status', 'legal_cases_status_index');
                $this->safeAddIndex($table, 'case_type', 'legal_cases_case_type_index');
                $this->safeAddIndex($table, 'client_name', 'legal_cases_client_name_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ✅ حذف الحقول المضافة
        if (Schema::hasTable('hearings')) {
            Schema::table('hearings', function (Blueprint $table) {
                if (Schema::hasColumn('hearings', 'postponed_to')) {
                    $table->dropColumn(['postponed_to', 'postpone_reason']);
                }
            });
        }

        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (Schema::hasColumn('invoices', 'discount_amount')) {
                    $table->dropColumn('discount_amount');
                }
            });
        }

        if (Schema::hasTable('legal_cases')) {
            Schema::table('legal_cases', function (Blueprint $table) {
                if (Schema::hasColumn('legal_cases', 'fees_pending')) {
                    $table->dropColumn('fees_pending');
                }
            });
        }

        // ✅ حذف الفهارس (مع حماية من الأخطاء)
        $this->safeDropIndex('hearings', ['case_id', 'hearing_date', 'status']);
        $this->safeDropIndex('invoices', ['case_id', 'status', 'invoice_date']);
        $this->safeDropIndex('legal_cases', ['status', 'case_type', 'client_name']);
    }

    /**
     * Safely add an index if not exists.
     */
    private function safeAddIndex(Blueprint $table, string $column, string $indexName): void
    {
        try {
            $connection = Schema::getConnection();
            $schemaBuilder = $connection->getSchemaBuilder();
            $tableName = $table->getTable();

            // Laravel ما عنده طريقة مباشرة للتحقق من الفهرس، فنستخدم try-catch
            $schemaBuilder->table($tableName, function (Blueprint $t) use ($column, $indexName) {
                try {
                    $t->index($column, $indexName);
                } catch (\Throwable $e) {
                    // نتجاهل الخطأ إذا كان الفهرس موجود بالفعل
                }
            });
        } catch (\Throwable $e) {
            // نتجاهل أي خطأ
        }
    }

    /**
     * Safely drop indexes by column names.
     */
    private function safeDropIndex(string $table, array $columns): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        Schema::table($table, function (Blueprint $t) use ($columns) {
            foreach ($columns as $col) {
                try {
                    $t->dropIndex([$col]);
                } catch (\Throwable $e) {
                    // تجاهل الخطأ إذا لم يوجد الفهرس
                }
            }
        });
    }
};
