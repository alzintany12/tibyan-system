<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // تعديل الأعمدة الموجودة لتصبح nullable
            if (Schema::hasColumn('documents', 'file_path')) {
                $table->string('file_path')->nullable()->change();
            }
            if (Schema::hasColumn('documents', 'file_size')) {
                $table->bigInteger('file_size')->nullable()->change();
            }
            if (Schema::hasColumn('documents', 'mime_type')) {
                $table->string('mime_type')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // إرجاعها إلى NOT NULL (كما كانت في البداية)
            if (Schema::hasColumn('documents', 'file_path')) {
                $table->string('file_path')->nullable(false)->change();
            }
            if (Schema::hasColumn('documents', 'file_size')) {
                $table->bigInteger('file_size')->nullable(false)->change();
            }
            if (Schema::hasColumn('documents', 'mime_type')) {
                $table->string('mime_type')->nullable(false)->change();
            }
        });
    }
};
