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
        Schema::create('addresses_states', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('state')->nullable();
            $table->string('city_id')->nullable();
            $table->string('type')->nullable();
            $table->string('street')->nullable();
            $table->string('house')->nullable();
            $table->string('stage')->nullable();
            $table->string('flat')->nullable();
            $table->string('note')->nullable();
            $table->string('title')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses_states');
    }
};
