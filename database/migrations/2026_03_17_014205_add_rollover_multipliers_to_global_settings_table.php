<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->integer('rollover_deposit_multiplier')->default(1)->after('id');
            $table->integer('rollover_bonus_multiplier')->default(20)->after('rollover_deposit_multiplier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn([
                'rollover_deposit_multiplier',
                'rollover_bonus_multiplier'
            ]);
        });
    }
};
