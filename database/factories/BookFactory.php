<?php

namespace Database\Factories;

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
            'title' => fake()->sentence(3),
            'author' => fake()->name(),
            'description' => fake()->paragraph(),
            'file_path' => 'livros/dummy.pdf',
            'cover_path' => null,
            'is_verified' => fake()->boolean(70),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
