<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Unique indexes on the name of each catalog.
     *
     * @var array<string, list<string>>
     */
    private array $uniques = [
        'species' => ['name'],
        'colors' => ['name'],
        'pet_statuses' => ['name'],
        'adoption_statuses' => ['name'],
        'shelters' => ['name'],
        'occupations' => ['name'],
        'treatment_types' => ['name'],
        'appointment_types' => ['name'],
        'breeds' => ['species_id', 'name'],
        'treatments' => ['treatment_type_id', 'name'],
        'people' => ['email'],
    ];

    /**
     * CHECK constraints by table. SQLite cannot add them to an existing table, so they only exist on SQL Server.
     *
     * @var array<string, array<string, string>>
     */
    private array $checks = [
        'adoptions' => [
            'adoptions_approved_after_requested' => 'approved_at >= requested_at',
            'adoptions_delivered_after_approved' => 'delivered_at >= approved_at',
            'adoptions_delivered_after_requested' => 'delivered_at >= requested_at',
        ],
        'people' => [
            'people_income_positive' => 'income >= 0',
            'people_current_pets_positive' => 'current_pets_count >= 0',
        ],
        'pets' => ['pets_weight_positive' => 'weight_kg >= 0'],
        'residences' => ['residences_animal_limit_positive' => 'animal_limit >= 0'],
        'treatments' => ['treatments_cost_positive' => 'cost >= 0'],
        'donations' => ['donations_amount_positive' => 'amount >= 0'],
    ];

    public function up(): void
    {
        // Los cambios automaticos de estado pueden no tener un usuario que los origine.
        Schema::table('pet_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        // Un indice filtrado no puede consultar adoption_statuses: la adopcion guarda si retiene a la mascota.
        Schema::table('adoptions', function (Blueprint $table) {
            $table->boolean('holds_pet')->default(false);
        });

        DB::table('adoptions')
            ->whereIn('adoption_status_id', DB::table('adoption_statuses')->whereIn('name', ['Aprobada', 'Entregada'])->select('id'))
            ->update(['holds_pet' => true]);

        DB::statement('CREATE UNIQUE INDEX adoptions_pet_holding_unique ON adoptions (pet_id) WHERE holds_pet = 1');

        foreach ($this->uniques as $table => $columns) {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->unique($columns));
        }

        // Indice filtrado: el nombre de la vivienda es opcional y SQL Server solo admite un NULL en un unico normal.
        DB::statement('CREATE UNIQUE INDEX residences_name_unique ON residences (name) WHERE name IS NOT NULL');

        if (DB::getDriverName() === 'sqlsrv') {
            foreach ($this->checks as $table => $checks) {
                foreach ($checks as $name => $expression) {
                    DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$name} CHECK ({$expression})");
                }
            }
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlsrv') {
            foreach ($this->checks as $table => $checks) {
                foreach (array_keys($checks) as $name) {
                    DB::statement("ALTER TABLE {$table} DROP CONSTRAINT {$name}");
                }
            }
        }

        Schema::table('residences', fn (Blueprint $table) => $table->dropIndex('residences_name_unique'));

        foreach ($this->uniques as $table => $columns) {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropUnique($columns));
        }

        Schema::table('adoptions', function (Blueprint $table) {
            $table->dropIndex('adoptions_pet_holding_unique');
            $table->dropColumn('holds_pet');
        });

        Schema::table('pet_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
