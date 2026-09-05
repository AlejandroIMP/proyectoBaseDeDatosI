<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adoptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained('pets')->noActionOnDelete();
            $table->foreignId('person_id')->constrained('people')->noActionOnDelete();
            $table->date('requested_at');
            $table->date('approved_at')->nullable();
            $table->date('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('document', 255)->nullable();
            $table->foreignId('adoption_status_id')->constrained('adoption_statuses')->noActionOnDelete();
            $table->foreignId('user_id')->constrained('users')->noActionOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adoptions');
    }
};
