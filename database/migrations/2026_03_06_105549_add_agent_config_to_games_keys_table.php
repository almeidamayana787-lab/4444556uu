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
        Schema::table('games_keys', function (Blueprint $table) {
            // PlayFiver agent config
            $table->decimal('pf_rtp', 5, 2)->nullable();
            $table->decimal('pf_limit_amount', 15, 2)->nullable();
            $table->integer('pf_limit_hours')->nullable();
            $table->boolean('pf_limit_enable')->default(false);
            $table->boolean('pf_bonus_enable')->default(true);

            // MAX API Games agent config
            $table->decimal('max_rtp', 5, 2)->nullable();
            $table->decimal('max_limit_amount', 15, 2)->nullable();
            $table->integer('max_limit_hours')->nullable();
            $table->boolean('max_limit_enable')->default(false);
            $table->boolean('max_bonus_enable')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games_keys', function (Blueprint $table) {
            $table->dropColumn([
                'pf_rtp',
                'pf_limit_amount',
                'pf_limit_hours',
                'pf_limit_enable',
                'pf_bonus_enable',
                'max_rtp',
                'max_limit_amount',
                'max_limit_hours',
                'max_limit_enable',
                'max_bonus_enable',
            ]);
        });
    }
};
