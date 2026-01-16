<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $search = $request->input('search');
        $course = $request->input('course');

        $booksQuery = Book::where('is_verified', true);

        if ($search) {
            $booksQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('author', 'like', "%{$search}%");
            });
        }

        if ($course) {
            if ($course === 'Outros') {
                $mainCourses = ['Engenharia Civil', 'Direito', 'Administração', 'Psicologia', 'Serviço Social', 'Fisioterapia', 'Enfermagem'];
                $booksQuery->whereNotIn('course', $mainCourses);
            } else {
                $booksQuery->where('course', $course);
            }
        }

        $books = $booksQuery->orderBy('created_at', 'desc')->get();

        $newBooks = Book::where('is_verified', true)
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->get();

        $myBooks = $user->books()
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'my_page');

        // Carrega todos os progressos de leitura do usuário
        $readingProgress = $user->readingProgress()->get();

        // Carrega os IDs dos livros favoritados pelo usuário
        $favoriteBookIds = $user->favoriteBooks()->pluck('books.id')->toArray();

        return view('dashboard', [
            'books' => $books,
            'newBooks' => $newBooks,
            'myBooks' => $myBooks,
            'search' => $search,
            'course' => $course,
            'readingProgress' => $readingProgress,
            'favoriteBookIds' => $favoriteBookIds,
        ]);
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(Request $request)
    {
        $dadosValidados = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course' => 'required|string',
            'file_path' => 'required|file|mimes:pdf|max:10240',
            'cover_path' => 'nullable|image|max:5120',
        ]);

        $totalPages = 0;
        if ($request->hasFile('file_path')) {
            $path = $request->file('file_path')->store('livros_pdfs', 'public');

            try {
                $parser = new Parser();
                $pdf = $parser->parseFile(storage_path('app/public/' . $path));
                $totalPages = count($pdf->getPages());
            } catch (\Exception $e) {
                Log::error("Erro ao processar o PDF: " . $e->getMessage());
            }
        } else {
            return back()->with('error', 'O arquivo é obrigatório');
        }

        $capaPath = null;
        if ($request->hasFile('cover_path')) {
            $capaPath = $request->file('cover_path')->store('livros_capas', 'public');
        }

        Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'description' => $request->description,
            'course' => $request->course,
            'file_path' => $path,
            'cover_path' => $capaPath,
            'user_id' => Auth::id(),
            'is_verified' => false,
            'total_pages' => $totalPages,
        ]);

        return to_route('aluno')->with('status', 'livro-enviado');
    }
}
