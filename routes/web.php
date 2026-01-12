<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Models\Book;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {

    $books = Book::where('is_verified', true)->get();

    $myBooks = Book::where('user_id', auth()->id())->get();

    return view('dashboard', ['books' => $books, 'myBooks' => $myBooks]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::get('/livros', [BookController::class, 'index'])->name('books.index');

Route::middleware(['auth'])->group(function () {
    Route::get('/enviar-livro', [BookController::class, 'create'])->name('books.create');
    Route::post('/livros', [BookController::class, 'store'])->name('books.store');
});
