<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adoption_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adoption_id')->constrained('adoptions')->noActionOnDelete();
            $table->date('followed_at');
            $table->text('notes');
            $table->foreignId('user_id')->constrained('users')->noActionOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adoption_follow_ups');
    }
};
