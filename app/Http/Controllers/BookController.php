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
     * Exibe o dashboard com listagem de livros.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $search = $request->input('search');
        $course = $request->input('course');
        $perPage = (int) $request->input('per_page', 24);

        // Determina se deve mostrar layout vitrine ou grid filtrado
        $showShowcase = !$course && !$search;

        return view('dashboard', [
            'books' => $this->bookService->getVerifiedBooks($search, $course, $perPage),
            'booksByCourse' => $showShowcase ? $this->bookService->getBooksByCourse() : collect(),
            'newBooks' => $this->bookService->getNewBooks(),
            'myBooks' => $user->books()->latest()->paginate(5, ['*'], 'my_page'),
            'search' => $search,
            'course' => $course,
            'readingProgress' => $user->readingProgress,
            'favoriteBookIds' => $user->favoriteBooks()->pluck('books.id')->toArray(),
        ]);
    }

    /**
     * Exibe o formulário de criação de livro.
     */
    public function create()
    {
        return view('books.create');
    }

    /**
     * Armazena um novo livro no banco de dados.
     * O processamento do PDF (contagem de páginas) é feito em background via Queue.
     */
    public function store(StoreBookRequest $request)
    {
        $validated = $request->validated();

        // Upload do PDF
        $pdfPath = $request->file('file_path')->store('livros_pdfs', 'public');

        // Upload da capa (opcional)
        $coverPath = $request->hasFile('cover_path')
            ? $request->file('cover_path')->store('livros_capas', 'public')
            : null;

        // Cria o livro com total_pages = null (será processado em background)
        $book = Book::create([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'description' => $validated['description'] ?? null,
            'course' => $validated['course'],
            'file_path' => $pdfPath,
            'cover_path' => $coverPath,
            'user_id' => Auth::id(),
            'is_verified' => false,
            'total_pages' => null, // Será preenchido pelo Job
        ]);

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
