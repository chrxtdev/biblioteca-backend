<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Map old specific courses to new broad areas
        $mapping = [
            'Engenharia Civil' => 'Engenharias',
            'Direito' => 'Ciências Humanas e Sociais',
            'Administração' => 'Ciências Humanas e Sociais',
            'Serviço Social' => 'Ciências Humanas e Sociais',
            'Enfermagem' => 'Área da Saúde',
            'Psicologia' => 'Área da Saúde',
            'Fisioterapia' => 'Área da Saúde',
            'Geral' => 'Conteúdos Gerais',
        ];

        foreach ($mapping as $old => $new) {
            DB::table('books')
                ->where('course', $old)
                ->update(['course' => $new]);
        }
        
        // Update any remaining that didn't match specific mapping to 'Geral' or keep as is?
        // Let's set anything else to 'Conteúdos Gerais' to be safe, or 'Tecnologia e TI' if we had data for it.
        // For now, let's assume the mapping covers the known controlled vocabulary.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // It's hard to reverse perfectly without a reliable previous state snapshot,
        // but we can try to map back broadly if needed, or just leave as is since this is a refactor.
        // For this specific task, down method is "best effort" or empty.
    }
};
