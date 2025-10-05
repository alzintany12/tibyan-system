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
        Schema::table('legal_cases', function (Blueprint $table) {
            // إضافة العمود فقط إذا لم يكن موجودًا مسبقًا
            if (!Schema::hasColumn('legal_cases', 'is_active')) {
                $table->boolean('is_active')
                      ->default(true)
                      ->after('id'); // يوضع بعد العمود id
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('legal_cases', function (Blueprint $table) {
            // حذف العمود فقط إذا كان موجودًا
            if (Schema::hasColumn('legal_cases', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
