<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Jobs\ProcessBookPdfJob;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function __construct(
        private BookService $bookService
    ) {}

    /**
     * Exibe o dashboard (Blade) ou retorna JSON quando via API.
     *
     * Rotas:
     *   GET /aluno         → View Blade (requer auth)
     *   GET /api/livros    → JSON público (vitrine de livros)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $course = $request->input('course');
        $perPage = (int) $request->input('per_page', 24);

        $books = $this->bookService->getVerifiedBooks($search, $course, $perPage);

        // Responde JSON para chamadas API (Accept: application/json ou rota /api/*)
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'data' => $books->items(),
                'meta' => [
                    'current_page' => $books->currentPage(),
                    'last_page' => $books->lastPage(),
                    'per_page' => $books->perPage(),
                    'total' => $books->total(),
                ],
                'filters' => [
                    'search' => $search,
                    'course' => $course,
                ],
            ]);
        }

        // View Blade para o dashboard web
        $showShowcase = !$course && !$search;

        return view('dashboard', [
            'books' => $books,
            'booksByCourse' => $showShowcase ? $this->bookService->getBooksByCourse() : collect(),
            'newBooks' => $this->bookService->getNewBooks(),
            'search' => $search,
            'course' => $course,
        ]);
    }

    /**
     * Armazena um novo livro no banco de dados.
     * O processamento do PDF (contagem de páginas) é feito em background via Queue.
     */
    public function store(StoreBookRequest $request)
    {
        $validated = $request->validated();

        $book = $this->bookService->createBook(
            $validated,
            $request->file('file_path'),
            $request->file('cover_path')
        );

        // Dispara o job para processar o PDF em background
        ProcessBookPdfJob::dispatch($book);

        return to_route('aluno')->with('status', 'livro-enviado');
    }

    /**
     * Retorna os livros enviados pelo usuário autenticado (API).
     */
    public function myBooks(Request $request)
    {
        $books = $request->user()
            ->books()
            ->latest()
            ->paginate(10);

        return response()->json($books);
    }
}
