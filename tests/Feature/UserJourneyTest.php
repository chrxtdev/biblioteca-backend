<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserJourneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_user_and_admin_journey()
    {
        Storage::fake('public');

        // 1. Registro do Usuário
        $user = User::factory()->create([
            'email' => 'student@unicentro.edu.br',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user);

        // 2. Upload de Livro
        $file = UploadedFile::fake()->create('livro.pdf', 1000, 'application/pdf');
        $cover = UploadedFile::fake()->image('capa.jpg');

        $response = $this->post('/api/livros', [
            'title' => 'Livro de Teste E2E',
            'author' => 'Autor Teste',
            'course' => 'Engenharias',
            'file_path' => $file,
            'cover_path' => $cover,
        ]);

        $response->assertRedirect('aluno');
        $this->assertDatabaseHas('books', ['title' => 'Livro de Teste E2E']);

        $book = Book::where('title', 'Livro de Teste E2E')->first();
        
        // Livro deve estar não verificado inicialmente
        $this->assertFalse((bool)$book->is_verified);

        // 3. Admin Aprova Livro (Simulação)
        // O modelo User verifica se o email está na config 'app.admin_emails'
        $adminEmail = 'admin@biblioteca.com';
        config(['app.admin_emails' => $adminEmail]);
        
        $admin = User::factory()->create(['email' => $adminEmail]);
        
        // Na prática, o admin faria um update via Filament. Aqui simulamos o resultado.
        $book->is_verified = true;
        $book->save();

        // 4. Usuário vê o livro na lista (agora aprovado)
        $this->get('/api/livros')
             ->assertStatus(200)
             ->assertSee('Livro de Teste E2E');

        // 5. Usuário inicia leitura e registra progresso
        $this->postJson('/api/reading-progress', [
            'book_id' => $book->id,
            'current_page' => 5,
            'total_pages' => 50,
        ])->assertStatus(200);

        $this->assertDatabaseHas('reading_progress', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'current_page' => 5,
        ]);

        // 6. Usuário completa leitura
        $this->postJson('/api/reading-progress/complete', [
            'book_id' => $book->id,
        ])->assertStatus(200);

        $this->assertDatabaseHas('reading_progress', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'is_completed' => true,
        ]);
    }
}
