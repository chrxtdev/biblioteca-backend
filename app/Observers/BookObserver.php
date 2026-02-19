<?php

namespace App\Observers;

use App\Models\Book;

class BookObserver
{
    /**
     * Handle the Book "created" event.
     */
    public function created(Book $book): void
    {
        $this->optimizeCover($book);
    }

    /**
     * Handle the Book "updated" event.
     */
    public function updated(Book $book): void
    {
        if ($book->isDirty('cover_path')) {
            $this->optimizeCover($book);
        }
    }

    /**
     * Otimiza a capa do livro (Admin/Filament).
     * Se já vier otimizado do BookService (.webp), ignora.
     * Se vier do Filament (provavelmente .jpg/.png), converte.
     */
    private function optimizeCover(Book $book): void
    {
        $path = $book->cover_path;

        // Se não tem capa ou já é .webp, ignora
        if (!$path || str_ends_with($path, '.webp')) {
            return;
        }

        $absolutePath = storage_path('app/public/' . $path);
        if (!file_exists($absolutePath)) {
            return;
        }
        
        try {
            // Instancia manual do Intervention Image v3
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($absolutePath);

            // Redimensiona se necessário
            if ($image->width() > 800) {
                $image->scale(width: 800);
            }

            // Define novo nome e caminho
            $newFilename = pathinfo($path, PATHINFO_FILENAME) . '.webp';
            $newRelativePath = 'livros_capas/' . $newFilename;
            $newAbsolutePath = storage_path('app/public/' . $newRelativePath);

            // Salva como WebP
            $image->toWebp(quality: 80)->save($newAbsolutePath);

            // Remove o arquivo original (opcional, mas bom pra economizar espaço)
            if ($absolutePath !== $newAbsolutePath) {
                @unlink($absolutePath);
            }

            // Atualiza o registro no banco com o novo caminho
            // Usamos saveQuietly() para não disparar o evento 'updated' de novo (loop infinito)
            $book->cover_path = $newRelativePath;
            $book->saveQuietly();

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('BookObserver: Erro ao otimizar imagem via Admin: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Book "deleted" event.
     */
    public function deleted(Book $book): void
    {
        //
    }

    /**
     * Handle the Book "restored" event.
     */
    public function restored(Book $book): void
    {
        //
    }

    /**
     * Handle the Book "force deleted" event.
     */
    public function forceDeleted(Book $book): void
    {
        //
    }
}
