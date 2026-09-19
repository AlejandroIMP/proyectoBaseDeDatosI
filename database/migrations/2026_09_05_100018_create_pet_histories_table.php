<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained('pets')->noActionOnDelete();
            $table->foreignId('pet_status_id')->constrained('pet_statuses')->noActionOnDelete();
            $table->text('notes');
            $table->date('recorded_at');
            $table->foreignId('user_id')->constrained('users')->noActionOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_histories');
    }
};
