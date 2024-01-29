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
        Schema::create('booking_history', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('user_id1')->nullable();
            $table->unsignedBigInteger('locker_id1');
            $table->unsignedBigInteger('booking_id1');
            $table->date('date1');
            $table->time('start_time1');
            $table->time('end_time1');
            $table->integer('usage1')->nullable();
            $table->decimal('unit_amount1',8,2)->default(5.00);
            $table->string('key_management1')->nullable();
            $table->string('status1')->default('Active');
            $table->foreign('user_id1')->references('id')->on('users');
            $table->foreign('booking_id1')->references('id')->on('booking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_history');
    }
};
