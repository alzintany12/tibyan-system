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
        // إضافة حقول مفقودة في جدول الجلسات إذا لم تكن موجودة
        Schema::table('hearings', function (Blueprint $table) {
            if (!Schema::hasColumn('hearings', 'reminder_sent')) {
                $table->boolean('reminder_sent')->default(false)->after('status');
            }
            if (!Schema::hasColumn('hearings', 'judge_name')) {
                $table->string('judge_name')->nullable()->after('court_room');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hearings', function (Blueprint $table) {
            if (Schema::hasColumn('hearings', 'reminder_sent')) {
                $table->dropColumn('reminder_sent');
            }
            if (Schema::hasColumn('hearings', 'judge_name')) {
                $table->dropColumn('judge_name');
            }
        });
    }
};