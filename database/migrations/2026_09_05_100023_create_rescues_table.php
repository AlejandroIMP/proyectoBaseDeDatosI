<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rescues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->noActionOnDelete();
            $table->foreignId('pet_id')->constrained('pets')->noActionOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('pet_status_id')->constrained('pet_statuses')->noActionOnDelete();
            $table->date('rescued_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rescues');
    }
};
