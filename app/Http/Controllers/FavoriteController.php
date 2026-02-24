<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Gerencia os favoritos do usuário.
 *
 * Permite ao aluno marcar/desmarcar livros como favoritos
 * e listar sua coleção pessoal com filtros por curso.
 */
class FavoriteController extends Controller
{
    /**
     * Alterna o estado de favorito de um livro para o usuário autenticado.
     *
     * Usa a relação BelongsToMany com toggle() para add/remove em uma única chamada.
     * Retorna o estado atualizado para que o frontend sincronize a UI.
     */
    public function toggleFavorite(Request $request, Book $book): JsonResponse
    {
        $user = $request->user();
        $user->favoriteBooks()->toggle($book->id);
        $isFavorited = $user->favoriteBooks()->where('book_id', $book->id)->exists();

        return response()->json([
            'success' => true,
            'is_favorited' => $isFavorited,
            'message' => $isFavorited ? 'Livro adicionado aos favoritos!' : 'Livro removido dos favoritos.'
        ]);
    }

    /**
     * Lista os livros favoritos do usuário autenticado.
     *
     * Quando nenhum filtro de curso é aplicado, agrupa os livros por curso
     * para exibição no formato vitrine (carrossel por categoria).
     * Também injeta dados auxiliares necessários para o dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $course = $request->input('course');

        $favoriteBooksQuery = $user->favoriteBooks();

        if ($course) {
            if ($course === 'Outros') {
                $favoriteBooksQuery->whereNotIn('course', \App\Enums\Course::values());
            } else {
                $favoriteBooksQuery->where('course', $course);
            }
        }

        $favoriteBooks = $favoriteBooksQuery->with('user')->orderBy('pivot_created_at', 'desc')->get();

        $booksByCourse = !$course ? $favoriteBooks->groupBy('course') : collect();

        return view('dashboard', [
            'books' => $favoriteBooks,
            'booksByCourse' => $booksByCourse,
            'newBooks' => collect(),
            'myBooks' => $user->books()->latest()->paginate(5, ['*'], 'my_page'),
            'search' => '',
            'course' => $course,
            'readingProgress' => $user->readingProgress()->get(),
            'favoriteBookIds' => $favoriteBooks->pluck('id')->toArray(),
        ]);
    }
}
