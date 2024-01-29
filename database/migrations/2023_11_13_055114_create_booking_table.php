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
        Schema::create('booking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('locker_id');
            $table->timestamps();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('usage')->nullable();
            $table->decimal('unit_amount',8,2)->default(5.00);
            $table->string('key_management')->nullable();
            $table->string('status')->default('Active');
            $table->boolean('reviewed')->default(false);
            $table->foreign('user_id')->references('id')->on('users');




        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};
