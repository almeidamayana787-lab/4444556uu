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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('bonus_balance', 15, 2)->default(0.00)->after('balance');
            $table->decimal('rollover_deposit_target', 15, 2)->default(0.00)->after('bonus_balance');
            $table->decimal('rollover_deposit_current', 15, 2)->default(0.00)->after('rollover_deposit_target');
            $table->decimal('rollover_bonus_target', 15, 2)->default(0.00)->after('rollover_deposit_current');
            $table->decimal('rollover_bonus_current', 15, 2)->default(0.00)->after('rollover_bonus_target');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bonus_balance',
                'rollover_deposit_target',
                'rollover_deposit_current',
                'rollover_bonus_target',
                'rollover_bonus_current'
            ]);
        });
    }
};
