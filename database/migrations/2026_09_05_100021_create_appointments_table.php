<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained('pets')->noActionOnDelete();
            $table->date('appointment_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'missed'])->default('pending');
            $table->foreignId('appointment_type_id')->constrained('appointment_types')->noActionOnDelete();
            $table->foreignId('person_id')->constrained('people')->noActionOnDelete();
            $table->foreignId('user_id')->constrained('users')->noActionOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
