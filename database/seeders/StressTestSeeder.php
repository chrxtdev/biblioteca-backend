<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seeder de Stress Test — Simula cenário de alta demanda.
 *
 * Insere 5.000 usuários e 10.000 livros em lotes de 500
 * para evitar estouro de memória. Todos os livros apontam
 * para um único PDF de teste (storage/app/public/test-book.pdf).
 *
 * Uso:
 *   1. Coloque um PDF de ~50MB em: storage/app/public/test-book.pdf
 *   2. Execute: php artisan db:seed --class=StressTestSeeder
 *   3. Para limpar: php artisan db:seed --class=StressTestSeeder (com --force após truncar manualmente)
 */
class StressTestSeeder extends Seeder
{
    // ╔══════════════════════════════════════════════════╗
    // ║  CONFIGURAÇÕES                                   ║
    // ╚══════════════════════════════════════════════════╝

    private const TOTAL_USERS = 5_000;
    private const TOTAL_BOOKS = 10_000;
    private const CHUNK_SIZE = 500;
    private const TEST_PDF_PATH = 'livros/test-book.pdf';

    private array $courses = [
        'Engenharias',
        'Ciências Humanas e Sociais',
        'Área da Saúde',
        'Tecnologia e TI',
        'Conteúdos Gerais',
    ];

    // ╔══════════════════════════════════════════════════╗
    // ║  LISTAS DE DADOS REALISTAS                       ║
    // ╚══════════════════════════════════════════════════╝

    private array $titles = [
        'Introdução à', 'Fundamentos de', 'Manual de', 'Princípios de',
        'Teoria e Prática de', 'Guia Completo de', 'Estudos em',
        'Avanços em', 'Metodologia de', 'Análise de', 'Perspectivas em',
        'Compêndio de', 'Tratado de', 'Elementos de', 'Essenciais de',
    ];

    private array $subjects = [
        'Cálculo Diferencial', 'Resistência dos Materiais', 'Direito Civil',
        'Psicologia Comportamental', 'Anatomia Humana', 'Farmacologia',
        'Algoritmos', 'Banco de Dados', 'Gestão de Projetos',
        'Sociologia', 'Filosofia', 'Economia', 'Estatística',
        'Enfermagem Clínica', 'Fisioterapia Desportiva', 'Serviço Social',
        'Engenharia de Software', 'Redes de Computadores', 'Sistemas Operacionais',
        'Administração Financeira', 'Marketing Digital', 'Contabilidade',
        'Bioquímica', 'Microbiologia', 'Patologia', 'Fisiologia',
        'Estruturas de Concreto', 'Hidráulica', 'Geotecnia',
        'Inteligência Artificial', 'Machine Learning', 'Segurança da Informação',
    ];

    private array $authors = [
        'Carlos Silva', 'Maria Santos', 'João Oliveira', 'Ana Costa',
        'Pedro Rodrigues', 'Fernanda Lima', 'Ricardo Almeida', 'Juliana Ferreira',
        'Roberto Souza', 'Patrícia Barbosa', 'Marcelo Ribeiro', 'Camila Martins',
        'Eduardo Pereira', 'Luciana Araújo', 'Gustavo Nascimento', 'Beatriz Gomes',
        'Alexandre Carvalho', 'Isabela Rocha', 'Fernando Mendes', 'Daniela Moreira',
    ];

    // ╔══════════════════════════════════════════════════╗
    // ║  EXECUÇÃO                                        ║
    // ╚══════════════════════════════════════════════════╝

    public function run(): void
    {
        $this->command->info('🚀 Iniciando Stress Test Seeder...');
        $this->command->newLine();

        $this->seedUsers();
        $this->seedBooks();

        $this->command->newLine();
        $this->command->info('✅ Stress Test Seeder concluído!');
        $this->command->table(
            ['Recurso', 'Quantidade'],
            [
                ['Usuários', number_format(self::TOTAL_USERS, 0, ',', '.')],
                ['Livros', number_format(self::TOTAL_BOOKS, 0, ',', '.')],
            ]
        );
    }

    // ╔══════════════════════════════════════════════════╗
    // ║  SEED DE USUÁRIOS                                ║
    // ╚══════════════════════════════════════════════════╝

    private function seedUsers(): void
    {
        $this->command->info("👤 Inserindo " . number_format(self::TOTAL_USERS, 0, ',', '.') . " usuários...");

        // Senha compartilhada (hash uma vez, reutiliza em todos)
        $hashedPassword = Hash::make('stress123');
        $now = now();
        $bar = $this->command->getOutput()->createProgressBar(self::TOTAL_USERS);

        $chunks = [];
        for ($i = 1; $i <= self::TOTAL_USERS; $i++) {
            $chunks[] = [
                'name' => "Aluno Teste {$i}",
                'email' => "stress_user_{$i}@teste.com",
                'password' => $hashedPassword,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Insere em lotes para não estourar memória
            if (count($chunks) >= self::CHUNK_SIZE) {
                DB::table('users')->insert($chunks);
                $bar->advance(count($chunks));
                $chunks = [];
            }
        }

        // Insere o restante que não completou um lote
        if (!empty($chunks)) {
            DB::table('users')->insert($chunks);
            $bar->advance(count($chunks));
        }

        $bar->finish();
        $this->command->newLine();
    }

    // ╔══════════════════════════════════════════════════╗
    // ║  SEED DE LIVROS                                  ║
    // ╚══════════════════════════════════════════════════╝

    private function seedBooks(): void
    {
        $this->command->info("📚 Inserindo " . number_format(self::TOTAL_BOOKS, 0, ',', '.') . " livros...");

        // Busca IDs dos usuários de teste para associar como autores (FK user_id)
        $userIds = DB::table('users')
            ->where('email', 'like', 'stress_user_%@teste.com')
            ->pluck('id')
            ->toArray();

        if (empty($userIds)) {
            $this->command->error('❌ Nenhum usuário de teste encontrado. Execute seedUsers() primeiro.');
            return;
        }

        $now = now();
        $bar = $this->command->getOutput()->createProgressBar(self::TOTAL_BOOKS);

        $chunks = [];
        for ($i = 1; $i <= self::TOTAL_BOOKS; $i++) {
            $title = $this->titles[array_rand($this->titles)] . ' ' . $this->subjects[array_rand($this->subjects)];
            $author = $this->authors[array_rand($this->authors)];
            $course = $this->courses[array_rand($this->courses)];

            $chunks[] = [
                'title' => $title . ' Vol. ' . rand(1, 20),
                'author' => $author,
                'course' => $course,
                'description' => "Material didático de {$course}. Edição acadêmica para estudos universitários.",
                'file_path' => self::TEST_PDF_PATH,
                'cover_path' => null,
                'is_verified' => true,
                'total_pages' => rand(50, 800),
                'user_id' => $userIds[array_rand($userIds)],
                'created_at' => $now->copy()->subDays(rand(0, 365)),
                'updated_at' => $now,
            ];

            if (count($chunks) >= self::CHUNK_SIZE) {
                DB::table('books')->insert($chunks);
                $bar->advance(count($chunks));
                $chunks = [];
            }
        }

        if (!empty($chunks)) {
            DB::table('books')->insert($chunks);
            $bar->advance(count($chunks));
        }

        $bar->finish();
        $this->command->newLine();
    }
}
