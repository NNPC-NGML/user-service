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
        Schema::create('head_of_units', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id")->comment("The user who is the head of the unit");
            $table->integer("location_id")->comment("the location where this head is ");
            $table->integer("unit_id")->comment("the unit that is being headed");
            $table->integer("status")->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('head_of_units');
    }
};
