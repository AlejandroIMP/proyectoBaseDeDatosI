<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_treatments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained('pets')->noActionOnDelete();
            $table->foreignId('treatment_id')->constrained('treatments')->noActionOnDelete();
            $table->text('instructions');
            $table->date('treated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_treatments');
    }
};
