<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('custom_layouts', function (Blueprint $table) {
            $table->string('cor_primaria')->nullable();
            $table->string('cor_secundaria')->nullable();
            $table->string('cor_acento')->nullable();
            $table->string('cor_texto_claro')->nullable();
            $table->string('cor_texto_escuro')->nullable();
            $table->string('cor_fundo_claro')->nullable();
            $table->string('cor_fundo_escuro')->nullable();
            $table->string('cor_borda')->nullable();
            $table->string('cor_botao_primario')->nullable();
            $table->string('cor_botao_primario_hover')->nullable();
            $table->string('cor_botao_secundario')->nullable();
            $table->string('cor_botao_secundario_hover')->nullable();
            $table->string('cor_link')->nullable();
            $table->string('cor_link_hover')->nullable();
            $table->string('cor_sucesso')->nullable();
            $table->string('cor_erro')->nullable();
            $table->string('cor_alerta')->nullable();
            $table->string('cor_informacao')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_layouts', function (Blueprint $table) {
            $table->dropColumn([
                'cor_primaria',
                'cor_secundaria',
                'cor_acento',
                'cor_texto_claro',
                'cor_texto_escuro',
                'cor_fundo_claro',
                'cor_fundo_escuro',
                'cor_borda',
                'cor_botao_primario',
                'cor_botao_primario_hover',
                'cor_botao_secundario',
                'cor_botao_secundario_hover',
                'cor_link',
                'cor_link_hover',
                'cor_sucesso',
                'cor_erro',
                'cor_alerta',
                'cor_informacao'
            ]);
        });
    }
};
