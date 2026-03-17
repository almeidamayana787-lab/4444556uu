<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Só se aplica verdadeiramente ao MySQL/MariaDB
        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE users AUTO_INCREMENT = 100000;');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Sem rollback aplicável por segurança a chaves recém criadas.
    }
};
