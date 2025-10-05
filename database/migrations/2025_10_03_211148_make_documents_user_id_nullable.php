<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('documents', 'user_id')) {
            Schema::table('documents', function (Blueprint $table) {
                try { 
                    $table->dropForeign(['user_id']); 
                } catch (\Throwable $e) {
                    // المفتاح قد لا يكون موجود أصلاً
                }
            });

            DB::statement('ALTER TABLE `documents` MODIFY `user_id` BIGINT UNSIGNED NULL;');

            Schema::table('documents', function (Blueprint $table) {
                $table->foreign('user_id')
                      ->references('id')
                      ->on('users')
                      ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('documents', 'user_id')) {
            Schema::table('documents', function (Blueprint $table) {
                try { 
                    $table->dropForeign(['user_id']); 
                } catch (\Throwable $e) {
                    // المفتاح قد لا يكون موجود أصلاً
                }
            });

            DB::statement('ALTER TABLE `documents` MODIFY `user_id` BIGINT UNSIGNED NOT NULL;');

            Schema::table('documents', function (Blueprint $table) {
                $table->foreign('user_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');
            });
        }
    }
};
