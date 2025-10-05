<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('document_templates', function (Blueprint $table) {
            // إضافة الأعمدة المفقودة
            $table->string('type')->nullable()->after('description');
            $table->json('variables')->nullable()->after('content');
            $table->bigInteger('file_size')->nullable()->after('file_name');
            $table->boolean('is_system_default')->default(false)->after('is_active');
            $table->unsignedBigInteger('created_by')->nullable()->after('user_id');
            
            // إضافة مفتاح خارجي للـ created_by
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            
            // إضافة فهارس للأداء
            $table->index(['type']);
            $table->index(['is_system_default']);
            $table->index(['created_by']);
        });
    }

    public function down()
    {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropIndex(['type']);
            $table->dropIndex(['is_system_default']);
            $table->dropIndex(['created_by']);
            $table->dropColumn(['type', 'variables', 'file_size', 'is_system_default', 'created_by']);
        });
    }
};