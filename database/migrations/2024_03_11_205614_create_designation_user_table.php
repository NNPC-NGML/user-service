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
        Schema::create('designation_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('designation_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('designation_id')->references('id')->on('designations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designation_users');
    }
};
