<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $books = Book::where('is_verified', true)
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $myBooks = $request->user()->books()
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'my_page');

        return view('dashboard', [
            'books' => $books,
            'myBooks' => $myBooks,
            'search' => $search
        ]);
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(Request $request)
    {
        // ... (seu código de validação e upload continua igual) ...
        $dadosValidados = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course' => 'required|string',
            'file_path' => 'required|file|mimes:pdf|max:10240',
            'cover_path' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('file_path')) {
            $path = $request->file('file_path')->store('livros_pdfs', 'public');
        } else {
            return back()->with('error', 'O arquivo é obrigatório');
        }

        $capaPath = null;
        if ($request->hasFile('cover_path')) {
            $capaPath = $request->file('cover_path')->store('livros_capas', 'public');
        }

        $book = Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'description' => $request->description,
            'course' => $request->course,
            'file_path' => $path,
            'cover_path' => $capaPath,
            'user_id' => auth()->id(),
            'is_verified' => false,
        ]);

        return to_route('dashboard')->with('status', 'livro-enviado');
    }
}
