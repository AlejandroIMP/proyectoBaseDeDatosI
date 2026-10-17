<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
 
            Schema::table('people', function (Blueprint $table) {
                $table->foreignId('user_id');
                $table->unique('user_id', 'people_user_id_unique');
            });
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropIndex('people_user_id_unique');
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
