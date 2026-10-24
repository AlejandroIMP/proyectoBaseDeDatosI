<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cuenta de adoptante a la que pertenece el registro; las personas que registra el personal no tienen cuenta.
        Schema::table('people', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
        });

        // SQL Server solo admite un NULL en un indice unico normal, por eso ahi se usa un indice filtrado.
        // MySQL, PostgreSQL y SQLite ya permiten varios NULL en un indice unico y no soportan esa sintaxis.
        if (DB::getDriverName() === 'sqlsrv') {
            DB::statement('CREATE UNIQUE INDEX people_user_id_unique ON people (user_id) WHERE user_id IS NOT NULL');
        } else {
            Schema::table('people', function (Blueprint $table) {
                $table->unique('user_id', 'people_user_id_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropIndex('people_user_id_unique');
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
