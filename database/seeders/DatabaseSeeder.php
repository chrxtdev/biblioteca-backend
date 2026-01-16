<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Cria um usuário administrador padrão para você poder logar
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@biblioteca.com',
            'password' => bcrypt('password'), // A senha será "password"
        ]);

        // Cria 50 livros falsos usando a BookFactory
        // Cada livro também criará um novo usuário associado a ele
        Book::factory(50)->create();
    }
}
