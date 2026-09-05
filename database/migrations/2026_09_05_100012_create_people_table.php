<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->enum('document_type', ['DPI', 'PASSPORT']);
            $table->string('document_number', 25)->unique();
            $table->string('email', 150);
            $table->unsignedInteger('current_pets_count')->default(0);
            $table->foreignId('residence_id')->constrained('residences')->noActionOnDelete();
            $table->integer('income');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
