<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('phone2')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();

            // بيانات الهوية
            $table->string('national_id')->nullable();
            $table->enum('id_type', ['national_id', 'passport', 'iqama', 'commercial_register'])->nullable();

            // بيانات الشركات
            $table->string('company_name')->nullable();
            $table->string('company_registration')->nullable(); // ✅ العمود الجديد
            $table->string('tax_number')->nullable();

            $table->enum('client_type', ['individual', 'company'])->default('individual');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users');

            // فهارس لتحسين الأداء
            $table->index(['name', 'is_active']);
            $table->index(['client_type', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('clients');
    }
};
