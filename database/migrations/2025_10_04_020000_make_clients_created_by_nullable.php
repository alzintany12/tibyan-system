<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Note: we use raw SQL here to avoid requiring doctrine/dbal for simple column modification.
     */
    public function up()
    {
        // Make `created_by` nullable so seeders that don't set it won't fail.
        DB::statement('ALTER TABLE `clients` MODIFY `created_by` BIGINT UNSIGNED NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * WARNING: reverting will set the column back to NOT NULL and may fail if there are rows with NULL.
     * Adjust the `down` method to your needs before running `migrate:rollback`.
     */
    public function down()
    {
        DB::statement('ALTER TABLE `clients` MODIFY `created_by` BIGINT UNSIGNED NOT NULL;');
    }
};
