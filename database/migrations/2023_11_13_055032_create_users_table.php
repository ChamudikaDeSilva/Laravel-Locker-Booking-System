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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id')->default('2');
            $table->text('first_name');
            $table->text('last_name');
            $table->string('faculty');
            $table->string('registration_number')->unique();
            $table->double('balance',8,2)->default(00.00);
            $table->string('email')->unique();
            $table->integer('phone');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_disabled')->default(false);
            $table->rememberToken();
            $table->timestamps();


            $table->foreign('role_id')->references('id')->on('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
