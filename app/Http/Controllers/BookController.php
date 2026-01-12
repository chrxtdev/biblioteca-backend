<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::where('is_verified', true)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $books->transform(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'description' => $book->description,
                'course' => $book->course,
                'status' => $book->is_verified ? 'Aprovado' : 'Em Análise',
                'file_url' => asset('storage/' . $book->file_path),
                'cover_url' => $book->cover_path ? asset('storage/' . $book->cover_path) : null,
            ];
        });
        return response()
            ->json($books);
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

        if ($request->hasFile('file_path')) {
            $path = $request->file('file_path')->store('livros_pdfs', 'public');
        } else {
            return back()->with('error', 'O arquivo é obrigatório');
        }

        $capaPath = null;
        if ($request
            ->hasFile('cover_path')) {
            $capaPath = $request
                ->file('cover_path')
                ->store('livros_capas', 'public');
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

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Livro enviado com sucesso!',
                'book' => $book,
            ], 201);
        }

        return to_route('dashboard')
            ->with('status', 'livro-enviado');
    }

    public function myBooks(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Não autenticado'], 401);
        }

        $books = Book::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $books->transform(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->title,
                'status' => $book->is_verified ? 'Aprovado' : 'Em Análise',
                'created_at' => $book->created_at,
                'file_url' => asset('storage/' . $book->file_path),
            ];
        });

        return response()->json($books);
    }
}
