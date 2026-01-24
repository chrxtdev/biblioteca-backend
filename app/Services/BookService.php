<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookService
{
    /**
     * Cursos principais do sistema.
     */
    private const MAIN_COURSES = [
        'Engenharia Civil',
        'Direito',
        'Administração',
        'Psicologia',
        'Serviço Social',
        'Fisioterapia',
        'Enfermagem',
    ];

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
     *
     * @return Collection
     */
    public function getBooksByCourse(): Collection
    {
        return Book::verified()
            ->latest()
            ->get()
            ->groupBy('course');
    }

    /**
     * Busca livros novos (últimos 7 dias) que o usuário ainda não viu.
     *
     * @return Collection
     */
    public function getNewBooks(): Collection
    {
        $lastSeen = session('last_seen_news');

        $query = Book::verified()
            ->where('created_at', '>=', now()->subDays(7))
            ->latest();

        if ($lastSeen) {
            $latestBook = (clone $query)->first();
            if ($latestBook && $lastSeen >= $latestBook->created_at) {
                return collect(); // Usuário já viu tudo
            }
        }

        return $query->get();
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
        if ($course === 'Outros') {
            return $query->whereNotIn('course', self::MAIN_COURSES);
        }

        return $query->where('course', $course);
    }

    /**
     * Retorna a lista de cursos principais.
     *
     * @return array
     */
    public static function getMainCourses(): array
    {
        return self::MAIN_COURSES;
    }
}
