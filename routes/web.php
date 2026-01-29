<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\FavoriteController;

Route::get('/', function () {
    return redirect()->route('aluno');
});

Route::get('/aluno', [BookController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('aluno');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::get('/livros', [BookController::class, 'index'])->name('books.index');

Route::middleware(['auth'])->group(function () {
    Route::post('/livros', [BookController::class, 'store'])->name('books.store');

    Route::post('/favorites/toggle/{book}', [FavoriteController::class, 'toggleFavorite'])->name('favorites.toggle');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
});

