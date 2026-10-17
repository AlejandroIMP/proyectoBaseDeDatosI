<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pets') && !Schema::hasColumn('pets', 'photo_url')) {
            Schema::table('pets', function (Blueprint $table) {
                $table->string('photo_url', 500)->nullable()->after('shelter_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pets') && Schema::hasColumn('pets', 'photo_url')) {
            Schema::table('pets', function (Blueprint $table) {
                $table->dropColumn('photo_url');
            });
        }
    }
};

