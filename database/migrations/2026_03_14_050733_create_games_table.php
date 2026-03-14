<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('game_code')->unique();
            $table->string('game_name');
            $table->string('provider_code');
            $table->string('banner_url')->nullable(); // original API banner path
            $table->string('banner_local')->nullable(); // local downloaded path
            $table->boolean('is_popular')->default(false);
            $table->integer('status')->default(1);
            $table->timestamps();

            $table->index('provider_code');
            $table->index('is_popular');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
