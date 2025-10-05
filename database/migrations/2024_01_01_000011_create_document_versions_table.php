<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->decimal('version_number', 3, 1);
            $table->string('file_path');
            $table->bigInteger('file_size');
            $table->text('changes_description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
            $table->index(['document_id', 'version_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_versions');
    }
};