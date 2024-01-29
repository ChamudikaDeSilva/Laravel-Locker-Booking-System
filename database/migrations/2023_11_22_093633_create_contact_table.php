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
        Schema::create('contact', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('locker_id');
            $table->unsignedBigInteger('booking_id');
            $table->timestamps();
            $table->string('name');
            $table->string('email');
            $table->text('message');
            $table->integer('rating');
            $table->text('sentiment')->nullable();
            $table->string('action')->default('Nothing');
            $table->string('final_state')->default('Nothing');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('locker_id')->references('id')->on('locker');
            $table->foreign('booking_id')->references('id')->on('booking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact');
    }
};
