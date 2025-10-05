<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('legal_cases', function (Blueprint $table) {
            // إضافة حقل رقم هوية العميل
            $table->string('client_id_number')->nullable()->after('client_phone');
            
            // جعل client_id اختياري بدلاً من مطلوب
            $table->unsignedBigInteger('client_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('legal_cases', function (Blueprint $table) {
            $table->dropColumn('client_id_number');
            
            // إرجاع client_id إلى حالته الأصلية (مطلوب)
            $table->unsignedBigInteger('client_id')->nullable(false)->change();
        });
    }
};