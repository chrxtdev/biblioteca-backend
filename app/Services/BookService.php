<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookService
{


    /**
     * Retorna o total de livros verificados (com cache).
     *
     * @return int
     */
    public function countVerifiedBooks(): int
    {
        return \Illuminate\Support\Facades\Cache::remember('total_books_count', 3600, function () {
            return Book::verified()->count();
        });
    }

    /**
     * Valores permitidos para paginação.
     */
    private const ALLOWED_PER_PAGE = [12, 24, 48, 96];

    /**
     * Busca livros verificados com filtros opcionais.
     *
     * @param string|null $search Termo de busca
     * @param string|null $course Filtro de curso
     * @param int $perPage Itens por página
     * @return LengthAwarePaginator
     */
    public function getVerifiedBooks(?string $search, ?string $course, int $perPage = 24): LengthAwarePaginator
    {
        $perPage = in_array($perPage, self::ALLOWED_PER_PAGE) ? $perPage : 24;

        return Book::verified()
            ->when($search, fn($q) => $q->search($search))
            ->when($course, fn($q) => $this->applyCourseFilter($q, $course))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }


    /**
     * Agrupa todos os livros verificados por curso (para layout vitrine).
     * Cache de 60 minutos.
     *
     * @return Collection
     */
    public function getBooksByCourse(): Collection
    {
        return \Illuminate\Support\Facades\Cache::remember('books_by_course', 3600, function () {
            return Book::verified()
                ->latest()
                ->get()
                ->groupBy(function ($book) {
                    // O campo 'course' é um Enum, então acessamos ->value
                    // Se for nulo ou falhar o cast, usamos null (ou string legado se o cast for removido)
                    $course = $book->course instanceof \App\Enums\Course ? $book->course->value : $book->course;
                    $course = trim((string) $course);

                    // Normaliza todas as variações para "Conteúdos Gerais" (Valor do Enum)
                    if (in_array($course, ['Geral', 'Outros', 'Geral/Outros', 'Geral / Outros', 'Conteúdos Gerais'])) {
                        return \App\Enums\Course::Geral->value;
                    }
                    return $course;
                });
        });
    }

    /**
     * Busca livros novos (últimos 7 dias) que o usuário ainda não viu.
     * Cache de 15 minutos (por usuário seria ideal, mas aqui é global por enquanto).
     *
     * @return Collection
     */
    public function getNewBooks(): Collection
    {
        // Cache global de "Quais são os livros novos no sistema?"
        $newBooks = \Illuminate\Support\Facades\Cache::remember('new_books_global', 900, function () {
            return Book::verified()
                ->where('created_at', '>=', now()->subDays(7))
                ->latest()
                ->get();
        });

        $lastSeen = session('last_seen_news');

        if ($lastSeen) {
            $latestBook = $newBooks->first();
            if ($latestBook && $lastSeen >= $latestBook->created_at) {
                return collect(); // Usuário já viu tudo
            }
        }

        return $newBooks;
    }



    /**
     * Aplica filtro de curso à query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $course
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyCourseFilter($query, string $course)
    {
        $escaped = str_replace(['%', '_'], ['\%', '\_'], $course);
        return $query->where('course', 'LIKE', "%{$escaped}%");
    }

    /**
     * Retorna a lista de cursos principais.
     *
     * @return array
     */
    public static function getMainCourses(): array
    {
        // Alternativa estática para compatibilidade, filtrando 'Geral'
        return array_filter(\App\Enums\Course::values(), fn($c) => $c !== \App\Enums\Course::Geral->value);
    }

    /**
     * Limpa o cache de livros.
     */
    public function clearBookCache(): void
    {
        \Illuminate\Support\Facades\Cache::forget('books_by_course');
        \Illuminate\Support\Facades\Cache::forget('new_books_global');
        \Illuminate\Support\Facades\Cache::forget('total_books_count');
    }

    /**
     * Cria um novo livro e faz upload dos arquivos.
     *
     * @param array $data Dados validados
     * @param \Illuminate\Http\UploadedFile $file Arquivo PDF
     * @param \Illuminate\Http\UploadedFile|null $cover Arquivo de capa
     * @return Book
     */
    public function createBook(array $data, $file, $cover = null): Book
    {
        // Upload do PDF
        $pdfPath = $file->store('livros_pdfs', 'public');

        // Upload da capa (Otimizado)
        $coverPath = null;
        if ($cover) {
            $imageName = \Illuminate\Support\Str::random(40) . '.webp';
            $coverPath = 'livros_capas/' . $imageName;
            
            // Caminho absoluto para salvar
            $absolutePath = storage_path('app/public/' . $coverPath);
            
            // Garante que o diretório existe
            if (!file_exists(dirname($absolutePath))) {
                mkdir(dirname($absolutePath), 0755, true);
            }

            // Otimização: Redimensiona (Max 800px width) e Converte para WebP (80% qualidade)
            try {
                // Instancia manual (sem Facade/Wrapper para evitar conflito de rede/versão)
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $image = $manager->read($cover->getRealPath());
                
                // Redimensiona mantendo proporção, apenas se for maior que 800px
                if ($image->width() > 800) {
                    $image->scale(width: 800);
                }
                
                $image->toWebp(quality: 80)->save($absolutePath);
            } catch (\Exception $e) {
                // Fallback: Se der erro na otimização, salva o original
                \Illuminate\Support\Facades\Log::error('Erro ao otimizar imagem: ' . $e->getMessage());
                // Remove o arquivo parcial se existir
                if (file_exists($absolutePath)) {
                    @unlink($absolutePath);
                }
                $coverPath = $cover->store('livros_capas', 'public');
            }
        }

        // Limpa cache
        $this->clearBookCache();
        
        // Cria o livro
        return Book::create([
            'title' => $data['title'],
            'author' => $data['author'],
            'description' => $data['description'] ?? null,
            'course' => $data['course'],
            'file_path' => $pdfPath,
            'cover_path' => $coverPath,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'is_verified' => false,
            'total_pages' => null, 
        ]);
    }
}
