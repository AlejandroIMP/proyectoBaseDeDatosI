<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->foreignId('species_id')->constrained('species')->noActionOnDelete();
            $table->foreignId('breed_id')->constrained('breeds')->noActionOnDelete();
            $table->char('sex', 1);
            $table->date('birth_date');
            $table->unsignedSmallInteger('estimated_age')->nullable();
            $table->foreignId('color_id')->constrained('colors')->noActionOnDelete();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('pet_status_id')->constrained('pet_statuses')->noActionOnDelete();
            $table->date('registered_at');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('shelter_id')->constrained('shelters')->noActionOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
