<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Define si una mascota en este estado puede iniciar una adopcion (catalogo, solicitudes y aprobacion).
        Schema::table('pet_statuses', function (Blueprint $table) {
            $table->boolean('is_adoptable')->default(false);
        });

        DB::table('pet_statuses')->where('name', 'Disponible')->update(['is_adoptable' => true]);
    }

    public function down(): void
    {
        Schema::table('pet_statuses', function (Blueprint $table) {
            $table->dropColumn('is_adoptable');
        });
    }
};
