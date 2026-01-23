<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FavoriteController extends Controller
{
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

    public function index(Request $request)
    {
        $user = $request->user();
        $course = $request->input('course');

        // Começa a query com os livros favoritos do usuário
        $favoriteBooksQuery = $user->favoriteBooks();

        // Aplica o filtro de curso, se existir
        if ($course) {
            if ($course === 'Outros') {
                $mainCourses = ['Engenharia Civil', 'Direito', 'Administração', 'Psicologia', 'Serviço Social', 'Fisioterapia', 'Enfermagem'];
                $favoriteBooksQuery->whereNotIn('course', $mainCourses);
            } else {
                $favoriteBooksQuery->where('course', $course);
            }
        }

        $favoriteBooks = $favoriteBooksQuery->with('user')->orderBy('pivot_created_at', 'desc')->get();

        // Agrupar livros favoritos por curso (vitrine)
        $booksByCourse = collect();
        if (!$course) {
            $booksByCourse = $favoriteBooks->groupBy('course');
        }

        $favoriteBookIds = $favoriteBooks->pluck('id')->toArray();
        $readingProgress = $user->readingProgress()->get();

        // **CORREÇÃO:** Busca os livros enviados pelo usuário
        $myBooks = $user->books()->orderBy('created_at', 'desc')->paginate(5, ['*'], 'my_page');

        return view('dashboard', [
            'books' => $favoriteBooks,
            'booksByCourse' => $booksByCourse,
            'newBooks' => collect(), // Não há "novos" na pág de favoritos
            'myBooks' => $myBooks, // Passa os livros enviados
            'search' => '', // Não há busca na pág de favoritos
            'course' => $course,
            'readingProgress' => $readingProgress,
            'favoriteBookIds' => $favoriteBookIds,
        ]);
    }
}
