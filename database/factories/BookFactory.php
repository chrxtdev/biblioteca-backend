<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'author' => fake()->name(),
            'description' => fake()->paragraph(3),
            'course' => fake()->randomElement(\App\Enums\Course::values()),
            'file_path' => 'livros_pdfs/fake_book.pdf', // Caminho falso, o arquivo não existirá
            'cover_path' => null, // Deixamos nulo, a UI já lida com isso
            'is_verified' => true, // Importante para que apareçam na listagem
            'total_pages' => fake()->numberBetween(150, 800),
            'user_id' => User::factory(), // Cria um usuário novo para o livro ou usa um existente
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
